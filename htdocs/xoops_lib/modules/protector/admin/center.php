<?php

use Xmf\Request;

//require_once XOOPS_ROOT_PATH.'/include/cp_header.php' ;
include_once __DIR__ . '/admin_header.php'; //mb problem: it shows always the same "Center" tab
xoops_cp_header();
include __DIR__ . '/mymenu.php';
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
require_once dirname(__DIR__) . '/class/gtickets.php';

// Define custom exception classes
class FileOpenException extends RuntimeException {}
class FileLockException extends RuntimeException {}
class FileWriteException extends RuntimeException {}

//dirty trick to get navigation working with system menus
if (isset($_GET['num'])) {
    $_SERVER['REQUEST_URI'] = 'admin/center.php?page=center';
}

$myts = \MyTextSanitizer::getInstance();
$db   = XoopsDatabaseFactory::getDatabaseConnection();

// GET vars
$pos = Request::getInt('pos', 0, 'GET');
$num = Request::getInt('num', 20, 'GET');

// Table Name
$log_table = $db->prefix($mydirname . '_log');

// Protector object
require_once dirname(__DIR__) . '/class/protector.php';
$db        = XoopsDatabaseFactory::getDatabaseConnection();
$protector = Protector::getInstance();
$conf      = $protector->getConf();

//
// transaction stage
//

if (!empty($_POST['action'])) {

    // Ticket check
    if (!$xoopsGTicket->check(true, 'protector_admin')) {
        redirect_header(XOOPS_URL . '/', 3, $xoopsGTicket->getErrors());
    }

    if ($_POST['action'] === 'update_ips') {
        $error_msg = '';

        $lines   = empty($_POST['bad_ips']) ? [] : explode("\n", trim($_POST['bad_ips']));
        $bad_ips = [];
        foreach ($lines as $line) {
            [$bad_ip, $jailed_time] = explode('|', $line, 2) + [1 => '']; // Ensure 2 elements
            $bad_ips[trim($bad_ip)] = empty($jailed_time) ? 0x7fffffff : (int) $jailed_time;
        }
        if (!$protector->write_file_badips($bad_ips)) {
            $error_msg .= _AM_MSG_BADIPSCANTOPEN;
            error_log("[File Write Error] Failed to write bad IPs to file.");
        }

        $group1_ips = empty($_POST['group1_ips']) ? [] : explode("\n", trim($_POST['group1_ips']));
        $group1_ips = array_map('trim', $group1_ips); // Use array_map for trimming

        $filePath = $protector->get_filepath4group1ips();
        try {
        $fp = fopen($filePath, 'w');

        if ($fp === false) {
                throw new FileOpenException("Failed to open file for writing: $filePath (mode: 'w')");
        }

            if (!flock($fp, LOCK_EX)) {
                throw new FileLockException("Failed to acquire lock on file: $filePath");
            }

                $data = serialize(array_unique($group1_ips)) . "\n";
                $bytesWritten = fwrite($fp, $data);

                if ($bytesWritten === false || $bytesWritten != strlen($data)) {
                throw new FileWriteException(
                    "Failed to write data to file: $filePath " .
                    "(bytes written: $bytesWritten, expected: " . strlen($data) . ")"
                );
                }
        } catch (FileOpenException $e) {
            $error_msg .= _AM_MSG_GROUP1IPSCANTOPEN;
            error_log("[File Open Error] " . $e->getMessage());
        } catch (FileLockException $e) {
            $error_msg .= "Failed to acquire lock on file.";
            error_log("[File Lock Error] " . $e->getMessage());
        } catch (FileWriteException $e) {
            $error_msg .= "Failed to write data to file.";
            error_log("[File Write Error] " . $e->getMessage());
        } finally {
            if (isset($fp) && is_resource($fp)) {
                flock($fp, LOCK_UN);
            fclose($fp);
        }
        }

        $redirect_msg = $error_msg ?: _AM_MSG_IPFILESUPDATED;
        redirect_header('center.php?page=center', 2, $redirect_msg);
        exit;
    } elseif ($_POST['action'] === 'delete' && isset($_POST['ids']) && \is_array($_POST['ids'])) {
        // remove selected records
        foreach ($_POST['ids'] as $lid) {
            $lid = (int) $lid;
            $db->query("DELETE FROM $log_table WHERE lid='$lid'");
        }
        redirect_header('center.php?page=center', 2, _AM_MSG_REMOVED);
        exit;
    } elseif ($_POST['action'] === 'banbyip' && isset($_POST['ids']) && \is_array($_POST['ids'])) {
        // remove selected records
        foreach ($_POST['ids'] as $lid) {
            $lid = (int) $lid;
            $sql = "SELECT `ip` FROM $log_table WHERE lid='$lid'";
            $result = $db->query($sql);

            if (!$db->isResultSet($result)) {
                [$ip] = $db->fetchRow($result);
                $protector->register_bad_ips(0, $ip);
            }

            if ($db->isResultSet($result)) {
                $db->freeRecordSet($result);
            }

        }
        redirect_header('center.php?page=center', 2, _AM_MSG_BANNEDIP);
        exit;
    } elseif ($_POST['action'] === 'deleteall') {
        // remove all records
        $db->query("DELETE FROM $log_table");
        redirect_header('center.php?page=center', 2, _AM_MSG_REMOVED);
        exit;
    } elseif ($_POST['action'] === 'compactlog') {
        // compact records (remove duplicated records (ip,type)
        $sql = "SELECT `lid`,`ip`,`type` FROM $log_table ORDER BY lid DESC";
        $result = $db->query($sql);
        if (!$db->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(),
                E_USER_ERROR,
            );
        }
        $buf    = [];
        $ids    = [];
        while (false !== ([$lid, $ip, $type] = $db->fetchRow($result))) {
            if (isset($buf[$ip . $type])) {
                $ids[] = $lid;
            } else {
                $buf[$ip . $type] = true;
            }
        }
        $db->query("DELETE FROM $log_table WHERE lid IN (" . implode(',', $ids) . ')');
        redirect_header('center.php?page=center', 2, _AM_MSG_REMOVED);
        exit;
    }
}

//
// display stage
//

// query for listing
$sql = "SELECT count(lid) FROM $log_table";
$result = $db->query($sql);
if (!$db->isResultSet($result)) {
    throw new \RuntimeException(
        \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(),
        E_USER_ERROR,
    );
}
[$numrows] = $db->fetchRow($result);

$sql = "SELECT l.lid, l.uid, l.ip, l.agent, l.type, l.description, UNIX_TIMESTAMP(l.timestamp), u.uname FROM $log_table l LEFT JOIN " . $db->prefix('users') . " u ON l.uid=u.uid ORDER BY timestamp DESC LIMIT $pos,$num";
$result = $db->query($sql);
if (!$db->isResultSet($result)) {
    throw new \RuntimeException(
        \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(),
        E_USER_ERROR,
    );
}

// Page Navigation
$nav      = new XoopsPageNav($numrows, $num, $pos, 'pos', "page=center&num=$num");
$nav_html = $nav->renderNav(10);

// Number selection
$num_options = '';
$num_array   = [20, 100, 500, 2000];
foreach ($num_array as $n) {
    if ($n == $num) {
        $num_options .= "<option value='$n' selected>$n</option>\n";
    } else {
        $num_options .= "<option value='$n'>$n</option>\n";
    }
}

// begin of Output

// title
echo "<h3 style='text-align:left;'>" . $xoopsModule->name() . "</h3>\n";
echo '<style>td.log_description {width: 60em; display: inline-block; word-wrap: break-word; white-space: pre-line;}</style>';

// configs writable check
if (!is_writable(dirname(__DIR__) . '/configs')) {
    printf("<p style='color:red;font-weight:bold;'>" . _AM_FMT_CONFIGSNOTWRITABLE . "</p>\n", dirname(__DIR__) . '/configs');
}

// bad_ips
$bad_ips = $protector->get_bad_ips(true);
uksort($bad_ips, 'protector_ip_cmp');
$bad_ips4disp = '';
foreach ($bad_ips as $bad_ip => $jailed_time) {
    $line = $jailed_time ? $bad_ip . '|' . $jailed_time : $bad_ip;
    $line = str_replace('|2147483647', '', $line); // remove :0x7fffffff
    $bad_ips4disp .= htmlspecialchars($line, ENT_QUOTES | ENT_HTML5) . "\n";
}

// group1_ips
$group1_ips = $protector->get_group1_ips();
usort($group1_ips, 'protector_ip_cmp');
$group1_ips4disp = htmlspecialchars(implode("\n", $group1_ips), ENT_QUOTES | ENT_HTML5);

// edit configs about IP ban and IPs for group=1
echo "
<form name='ConfigForm' action='' method='POST'>
" . $xoopsGTicket->getTicketHtml(__LINE__, 1800, 'protector_admin') . "
<input type='hidden' name='action' value='update_ips' />
<table width='95%' class='outer' cellpadding='4' cellspacing='1'>
  <tr valign='top' align='left'>
    <td class='head'>
      " . _AM_TH_BADIPS . "
    </td>
    <td class='even'>
      <textarea name='bad_ips' id='bad_ips' style='width:360px;height:60px;' spellcheck='false'>$bad_ips4disp</textarea>
      <br>
      " . htmlspecialchars($protector->get_filepath4badips(), ENT_QUOTES | ENT_HTML5) . "
    </td>
  </tr>
  <tr valign='top' align='left'>
    <td class='head'>
      " . _AM_TH_GROUP1IPS . "
    </td>
    <td class='even'>
      <textarea name='group1_ips' id='group1_ips' style='width:360px;height:60px;' spellcheck='false'>$group1_ips4disp</textarea>
      <br>
      " . htmlspecialchars($protector->get_filepath4group1ips(), ENT_QUOTES | ENT_HTML5) . "
    </td>
  </tr>
  <tr valign='top' align='left'>
    <td class='head'>
    </td>
    <td class='even'>
      <input type='submit' value='" . _GO . "' />
    </td>
  </tr>
</table>
</form>
";

// header of log listing
echo "
<table width='95%' border='0' cellpadding='4' cellspacing='0'><tr><td>
<form action='' method='GET' style='margin-bottom:0;'>
  <table width='95%' border='0' cellpadding='4' cellspacing='0'>
    <tr>
      <td align='left'>
        <select name='num' onchange='submit();'>$num_options</select>
        <input type='submit' value='" . _SUBMIT . "'>
      </td>
      <td align='right'>
        $nav_html
      </td>
    </tr>
  </table>
</form>
<form name='MainForm' action='' method='POST' style='margin-top:0;'>
" . $xoopsGTicket->getTicketHtml(__LINE__, 1800, 'protector_admin') . "
<input type='hidden' name='action' value='' />
<table width='95%' class='outer' cellpadding='4' cellspacing='1'>
  <tr valign='middle'>
    <th width='5'><input type='checkbox' name='dummy' onclick=\"with(document.MainForm){for (i=0;i<length;i++) {if (elements[i].type=='checkbox') {elements[i].checked=this.checked;}}}\" /></th>
    <th>" . _AM_TH_DATETIME . '</th>
    <th>' . _AM_TH_USER . '</th>
    <th>' . _AM_TH_IP . '<br>' . _AM_TH_AGENT . '</th>
    <th>' . _AM_TH_TYPE . '</th>
    <th>' . _AM_TH_DESCRIPTION . '</th>
  </tr>
';

// body of log listing
$oddeven = 'odd';
while (false !== ([$lid, $uid, $ip, $agent, $type, $description, $timestamp, $uname] = $db->fetchRow($result))) {
    $oddeven = ($oddeven === 'odd' ? 'even' : 'odd');
    $style = '';

    $ip = htmlspecialchars($ip, ENT_QUOTES | ENT_HTML5);
    $type = htmlspecialchars($type, ENT_QUOTES | ENT_HTML5);
    if ('{"' == substr($description, 0, 2) && defined('JSON_PRETTY_PRINT')) {
        $temp = json_decode($description);
        if (is_object($temp)) {
            $description = json_encode($temp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $style = ' log_description';
        }
    }
    $description = htmlspecialchars($description, ENT_QUOTES | ENT_HTML5);
    $uname = htmlspecialchars(($uid ? $uname : _GUESTS), ENT_QUOTES | ENT_HTML5);

    // make agents shorter
    if (preg_match('/Chrome\/([0-9.]+)/', $agent, $regs)) {
        $agent_short = 'Chrome ' . $regs[1];
    } elseif (preg_match('/MSIE\s+([0-9.]+)/', $agent, $regs)) {
        $agent_short = 'IE ' . $regs[1];
    } elseif (false !== stripos($agent, 'Gecko')) {
        $agent_short = strrchr($agent, ' ');
    } else {
        $agent_short = substr($agent, 0, strpos($agent, ' '));
    }
    $agent4disp = htmlspecialchars($agent, ENT_QUOTES | ENT_HTML5);
    $agent_desc = $agent == $agent_short ? $agent4disp : htmlspecialchars($agent_short, ENT_QUOTES | ENT_HTML5) . "<img src='../images/dotdotdot.gif' alt='$agent4disp' title='$agent4disp' />";

    echo "
  <tr>
    <td class='$oddeven'><input type='checkbox' name='ids[]' value='$lid' /></td>
    <td class='$oddeven'>" . formatTimestamp($timestamp) . "</td>
    <td class='$oddeven'>$uname</td>
    <td class='$oddeven'>$ip<br>$agent_desc</td>
    <td class='$oddeven'>$type</td>
    <td class='{$oddeven}{$style}'>$description</td>
  </tr>\n";
}

// footer of log listing
echo "
  <tr>
    <td colspan='8' align='left'>" . _AM_LABEL_REMOVE . "<input type='button' value='" . _AM_BUTTON_REMOVE . "' onclick='if (confirm(\"" . _AM_JS_REMOVECONFIRM . "\")) {document.MainForm.action.value=\"delete\"; submit();}' />
    &nbsp " . _AM_LABEL_BAN_BY_IP . "<input type='button' value='" . _AM_BUTTON_BAN_BY_IP . "' onclick='if (confirm(\"" . _AM_JS_BANCONFIRM . "\")) {document.MainForm.action.value=\"banbyip\"; submit();}' /></td>
  </tr>
</table>
<div align='right'>
  $nav_html
</div>
<div style='clear:both;'><br><br></div>
<div align='right'>
" . _AM_LABEL_COMPACTLOG . "<input type='button' value='" . _AM_BUTTON_COMPACTLOG . "' onclick='if (confirm(\"" . _AM_JS_COMPACTLOGCONFIRM . "\")) {document.MainForm.action.value=\"compactlog\"; submit();}' />
&nbsp;
" . _AM_LABEL_REMOVEALL . "<input type='button' value='" . _AM_BUTTON_REMOVEALL . "' onclick='if (confirm(\"" . _AM_JS_REMOVEALLCONFIRM . "\")) {document.MainForm.action.value=\"deleteall\"; submit();}' />
</div>
</form>
</td></tr></table>
";

xoops_cp_footer();

/**
 * Callback used by uksort and usort for ip sorting
 *
 * @param string $a
 * @param string $b
 *
 * @return int
 */
function protector_ip_cmp($a, $b)
{
    // ipv6 below ipv4
    if ((false === strpos($a, ':')) && false !== strpos($b, ':')) {
        return -1;
    }
    // ipv4 above ipv6
    if ((false === strpos($a, '.')) && false !== strpos($b, '.')) {
        return 1;
    }
    // normalize ipv4 before comparing
    if ((is_int(strpos($a, '.'))) && (is_int(strpos($b, '.')))) {
        $a = protector_normalize_ipv4($a);
        $b = protector_normalize_ipv4($b);
    }
    return strcasecmp($a, $b);
}

/**
 * pad all octets in an ipv4 address to 3 digits for sorting
 *
 * @param string $n ipv4 address
 *
 * @return string
 */
function protector_normalize_ipv4($n)
{
    $temp = explode('.', $n);
    $n = '';
    foreach($temp as $k => $v) {
        $t = '00' . $v;
        $n .= substr($t, -3);
        if ($k < 3) {
            $n .= '.';
        }
    }
    return $n;
}
