<?php
//require_once XOOPS_ROOT_PATH.'/include/cp_header.php' ;
include_once 'admin_header.php'; //mb problem: it shows always the same "Center" tab
require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
require_once dirname(__DIR__) . '/class/gtickets.php';

//dirty trick to get navigation working with system menus
if (isset($_GET['num'])) {
    $_SERVER['REQUEST_URI'] = 'admin/center.php?page=center';
}

$myts = MyTextSanitizer::getInstance();
$db   = XoopsDatabaseFactory::getDatabaseConnection();

// GET vars
$pos = empty($_GET['pos']) ? 0 : (int)$_GET['pos'];
$num = empty($_GET['num']) ? 20 : (int)$_GET['num'];

// Table Name
$log_table = $db->prefix($mydirname . '_log');

// Protector object
require_once dirname(__DIR__) . '/class/protector.php';
$db        = XoopsDatabaseFactory::getDatabaseConnection();
$protector = Protector::getInstance($db->conn);
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

        $lines   = empty($_POST['bad_ips']) ? array() : explode("\n", trim($_POST['bad_ips']));
        $bad_ips = array();
        foreach ($lines as $line) {
            @list($bad_ip, $jailed_time) = explode(':', $line, 2);
            $bad_ips[trim($bad_ip)] = empty($jailed_time) ? 0x7fffffff : (int)$jailed_time;
        }
        if (!$protector->write_file_badips($bad_ips)) {
            $error_msg .= _AM_MSG_BADIPSCANTOPEN;
        }

        $group1_ips = empty($_POST['group1_ips']) ? array() : explode("\n", trim($_POST['group1_ips']));
        foreach (array_keys($group1_ips) as $i) {
            $group1_ips[$i] = trim($group1_ips[$i]);
        }
        $fp = @fopen($protector->get_filepath4group1ips(), 'w');
        if ($fp) {
            @flock($fp, LOCK_EX);
            fwrite($fp, serialize(array_unique($group1_ips)) . "\n");
            @flock($fp, LOCK_UN);
            fclose($fp);
        } else {
            $error_msg .= _AM_MSG_GROUP1IPSCANTOPEN;
        }

        $redirect_msg = $error_msg ? : _AM_MSG_IPFILESUPDATED;
        redirect_header('center.php?page=center', 2, $redirect_msg);
        exit;
    } elseif ($_POST['action'] === 'delete' && isset($_POST['ids']) && is_array($_POST['ids'])) {
        // remove selected records
        foreach ($_POST['ids'] as $lid) {
            $lid = (int)$lid;
            $db->query("DELETE FROM $log_table WHERE lid='$lid'");
        }
        redirect_header('center.php?page=center', 2, _AM_MSG_REMOVED);
        exit;
    } elseif ($_POST['action'] === 'banbyip' && isset($_POST['ids']) && is_array($_POST['ids'])) {
        // remove selected records
        foreach ($_POST['ids'] as $lid) {
            $lid = (int)$lid;
            $result = $db->query("SELECT `ip` FROM $log_table WHERE lid='$lid'");
            if (false !== $result) {
                list($ip) = $db->fetchRow($result);
                $protector->register_bad_ips(0, $ip);
            }
            $db->freeRecordSet($result);
        }
        redirect_header('center.php?page=center', 2, _AM_MSG_BANNEDIP);
        exit;
    } elseif ($_POST['action'] === 'deleteall') {
        // remove all records
        $db->query("DELETE FROM $log_table");
        redirect_header('center.php?page=center', 2, _AM_MSG_REMOVED);
        exit;
    } elseif ($_POST['action'] === 'compactlog') {
        // compactize records (removing duplicated records (ip,type)
        $result = $db->query("SELECT `lid`,`ip`,`type` FROM $log_table ORDER BY lid DESC");
        $buf    = array();
        $ids    = array();
        while (false !== (list($lid, $ip, $type) = $db->fetchRow($result))) {
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
$rs = $db->query("SELECT count(lid) FROM $log_table");
list($numrows) = $db->fetchRow($rs);
$prs = $db->query("SELECT l.lid, l.uid, l.ip, l.agent, l.type, l.description, UNIX_TIMESTAMP(l.timestamp), u.uname FROM $log_table l LEFT JOIN " . $db->prefix('users') . " u ON l.uid=u.uid ORDER BY timestamp DESC LIMIT $pos,$num");

// Page Navigation
$nav      = new XoopsPageNav($numrows, $num, $pos, 'pos', "page=center&num=$num");
$nav_html = $nav->renderNav(10);

// Number selection
$num_options = '';
$num_array   = array(20, 100, 500, 2000);
foreach ($num_array as $n) {
    if ($n == $num) {
        $num_options .= "<option value='$n' selected>$n</option>\n";
    } else {
        $num_options .= "<option value='$n'>$n</option>\n";
    }
}

// beggining of Output
xoops_cp_header();
include __DIR__ . '/mymenu.php';

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
    $line = $jailed_time ? $bad_ip . ':' . $jailed_time : $bad_ip;
    $line = str_replace(':2147483647', '', $line); // remove :0x7fffffff
    $bad_ips4disp .= htmlspecialchars($line, ENT_QUOTES) . "\n";
}

// group1_ips
$group1_ips = $protector->get_group1_ips();
usort($group1_ips, 'protector_ip_cmp');
$group1_ips4disp = htmlspecialchars(implode("\n", $group1_ips), ENT_QUOTES);

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
      <textarea name='bad_ips' id='bad_ips' style='width:200px;height:60px;'>$bad_ips4disp</textarea>
      <br>
      " . htmlspecialchars($protector->get_filepath4badips()) . "
    </td>
  </tr>
  <tr valign='top' align='left'>
    <td class='head'>
      " . _AM_TH_GROUP1IPS . "
    </td>
    <td class='even'>
      <textarea name='group1_ips' id='group1_ips' style='width:200px;height:60px;'>$group1_ips4disp</textarea>
      <br>
      " . htmlspecialchars($protector->get_filepath4group1ips()) . "
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
while (false !== (list($lid, $uid, $ip, $agent, $type, $description, $timestamp, $uname) = $db->fetchRow($prs))) {
    $oddeven = ($oddeven === 'odd' ? 'even' : 'odd');
    $style = '';

    $ip = htmlspecialchars($ip, ENT_QUOTES);
    $type = htmlspecialchars($type, ENT_QUOTES);
    if ('{"' == substr($description, 0, 2)) {
        $temp = json_decode($description);
        if (is_object($temp)) {
            $description = json_encode($temp, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
            $style = ' log_description';
        }
    }
    $description = htmlspecialchars($description, ENT_QUOTES);
    $uname = htmlspecialchars(($uid ? $uname : _GUESTS), ENT_QUOTES);

    // make agents shorter
    if (preg_match('/MSIE\s+([0-9.]+)/', $agent, $regs)) {
        $agent_short = 'IE ' . $regs[1];
    } elseif (false !== stripos($agent, 'Gecko')) {
        $agent_short = strrchr($agent, ' ');
    } else {
        $agent_short = substr($agent, 0, strpos($agent, ' '));
    }
    $agent4disp = htmlspecialchars($agent, ENT_QUOTES);
    $agent_desc = $agent == $agent_short ? $agent4disp : htmlspecialchars($agent_short, ENT_QUOTES) . "<img src='../images/dotdotdot.gif' alt='$agent4disp' title='$agent4disp' />";

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
 * @param $a
 * @param $b
 *
 * @return int
 */
function protector_ip_cmp($a, $b)
{
    $as   = explode('.', $a);
    $aval = @$as[0] * 167777216 + @$as[1] * 65536 + @$as[2] * 256 + @$as[3];
    $bs   = explode('.', $b);
    $bval = @$bs[0] * 167777216 + @$bs[1] * 65536 + @$bs[2] * 256 + @$bs[3];

    return $aval > $bval ? 1 : -1;
}
