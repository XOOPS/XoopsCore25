<?php
/**
 * XOOPS banner management
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @since               2.0.0
 * @author              Kazumi Ono <webmaster@myweb.ne.jp>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @author              DuGris aka L. Jen <http://www.dugris.info>
 * @author              Kris <kris@frxoops.org>
 */

$xoopsOption['pagetype'] = 'banners';
include __DIR__ . '/mainfile.php';

/********************************************/
/* Function to let your client login to see */
/* the stats                                */
/********************************************/
function clientlogin()
{
    global $xoopsDB, $xoopsLogger, $xoopsConfig;
    include 'header.php';
    $GLOBALS['xoTheme']->addStylesheet(null, null, '
        #login_window  {
            max-width:                          480px;
            margin:                             1em auto;
            background-color:                   #f8f8f8;
            color:                              inherit;
            border:                             1px solid #000;
        }
        #login_window  h2 {
            margin:                             .5em;
            padding:                            130px 0 0;
            background:                         url( images/password.png) no-repeat center top;
            text-align:                         center;
        }
        .login_form  .credentials {
            margin:                             .5em 1em;
            padding:                            1em;
            background-color:                   #ccc;
            color:                              inherit;
        }
        .login_form  .credentials label {
            display:                            inline-block;
            width:                              33%;
            margin:                             1px;
        }
        .login_form  .credentials input {
            width:                              50%;
            margin:                             1px;
            padding:                            1px;
            border:                             1px solid #000;
        }
        .login_form  .credentials input:focus {
            border:                             1px solid #2266cc;
        }
        .login_form  .actions {
            padding:                            1.5em .5em .5em;
            text-align:                         center;
        }
        .login_info {
            margin:                             .5em 1em;
            text-align:                         center;
        }
        .content_title {
            font-size:                          1.2em;
        }
    ');
    echo "<div id='login_window'>
          <h2 class='content_title'>" . _BANNERS_LOGIN_TITLE . "</h2>
          <form method='post' action='banners.php' class='login_form'>
          <div class='credentials'>
          <label for='login_form-login'>" . _BANNERS_LOGIN_LOGIN . "</label>
          <input type='text' name='login' id='login_form-login' value='' /><br>
          <label for='login_form-password'>" . _BANNERS_LOGIN_PASS . "</label>
          <input type='password' name='pass' id='login_form-password' value='' /><br>
          </div>
          <div class='actions'><input type='hidden' name='op' value='Ok' /><button type='submit'>" . _BANNERS_LOGIN_OK . "</button></div>
          <div class='login_info'>" . _BANNERS_LOGIN_INFO . '</div>' . $GLOBALS['xoopsSecurity']->getTokenHTML('BANNER_LOGIN') . '
          </form></div>';
    include $GLOBALS['xoops']->path('footer.php');
}

/*********************************************/
/* Function to display the banners stats for */
/* each client                               */
/*********************************************/
function bannerstats()
{
    global $xoopsDB, $xoopsConfig, $xoopsLogger, $myts;
    if ($_SESSION['banner_login'] == '' || $_SESSION['banner_pass'] == '') {
        redirect_header('banners.php', 2, _BANNERS_NO_LOGIN_DATA);
    }
    $result = $xoopsDB->query(sprintf('SELECT cid, name, passwd FROM %s WHERE login=%s', $xoopsDB->prefix('bannerclient'), $xoopsDB->quoteString($_SESSION['banner_login'])));
    list($cid, $name, $passwd) = $xoopsDB->fetchRow($result);
    if ($_SESSION['banner_pass'] == $passwd) {
        include $GLOBALS['xoops']->path('header.php');
        $GLOBALS['xoTheme']->addStylesheet(null, null, '
            #bannerstats {}
            #bannerstats td {
                text-align: center;
            }
        ');

        echo "<div id='bannerstats'>
              <h4 class='content_title'>" . sprintf(_BANNERS_TITLE, $name) . "</h4><hr />
              <table summary=''>
              <caption>" . sprintf(_BANNERS_TITLE, $name) . '</caption>
              <thead><tr>
              <td>ID</td>
              <td>' . _BANNERS_IMP_MADE . '</td>
              <td>' . _BANNERS_IMP_TOTAL . '</td>
              <td>' . _BANNERS_IMP_LEFT . '</td>
              <td>' . _BANNERS_CLICKS . '</td>
              <td>' . _BANNERS_PER_CLICKS . '</td>
              <td>' . _BANNERS_FUNCTIONS . "</td></tr></thead>
              <tfoot><tr><td colspan='7'></td></tr></tfoot>";

        $result = $xoopsDB->query('SELECT bid, imptotal, impmade, clicks, date FROM ' . $xoopsDB->prefix('banner') . " WHERE cid={$cid}");
        $i      = 0;
        while (false !== (list($bid, $imptotal, $impmade, $clicks, $date) = $xoopsDB->fetchRow($result))) {
            if ($impmade == 0) {
                $percent = 0;
            } else {
                $percent = substr(100 * $clicks / $impmade, 0, 5);
            }
            if ($imptotal == 0) {
                $left = _BANNERS_UNLIMITED;
            } else {
                $left = $imptotal - $impmade;
            }
            $class = ($i % 2 == 0) ? 'even' : 'odd';
            echo "<tbody><tr class='{$class}'>
                  <td>{$bid}</td>
                  <td>{$impmade}</td>
                  <td>{$imptotal}</td>
                  <td>{$left}</td>
                  <td>{$clicks}</td>
                  <td>{$percent}%</td>
                  <td><a href='banners.php?op=EmailStats&amp;cid={$cid}&amp;bid={$bid}' title='" . _BANNERS_STATS . "'>" . _BANNERS_STATS . '</a></td></tr></tbody>';
            ++$i;
        }
        echo "</table>
              <br><br>
              <h4 class='content_title'>" . _BANNERS_FOW_IN . htmlspecialchars($xoopsConfig['sitename']) . '</h4><hr />';

        $result = $xoopsDB->query('SELECT bid, imageurl, clickurl, htmlbanner, htmlcode FROM ' . $xoopsDB->prefix('banner') . " WHERE cid={$cid}");
        while (false !== (list($bid, $imageurl, $clickurl, $htmlbanner, $htmlcode) = $xoopsDB->fetchRow($result))) {
            $numrows = $xoopsDB->getRowsNum($result);
            if ($numrows > 1) {
                echo '<br>';
            }
            if (!empty($htmlbanner) && !empty($htmlcode)) {
                echo $myts->displayTarea($htmlcode);
            } else {
                if (strtolower(substr($imageurl, strrpos($imageurl, '.'))) === '.swf') {
                    echo "<object type='application/x-shockwave-flash' width='468' height='60' data='{$imageurl}'>";
                    echo "<param name='movie' value='{$imageurl}' />";
                    echo "<param name='quality' value='high' />";
                    echo '</object>';
                } else {
                    echo "<img src='{$imageurl}' alt='' />";
                }
            }
            echo '<br><strong>' . _BANNERS_ID . $bid . '</strong><br>' . sprintf(_BANNERS_SEND_STATS, 'banners.php?op=EmailStats&amp;cid=' . $cid . '&amp;bid=' . $bid) . '<br>';
            if (!$htmlbanner) {
                $clickurl = htmlspecialchars($clickurl, ENT_QUOTES);
                echo sprintf(_BANNERS_POINTS, $clickurl) . "<br>
                <form action='banners.php' method='post'>" . _BANNERS_URL . "
                <input type='text' name='url' size='50' maxlength='200' value='{$clickurl}' />
                <input type='hidden' name='bid' value='{$bid}' />
                <input type='hidden' name='cid' value='{$cid}' />
                <input type='submit' name='op' value='" . _BANNERS_CHANGE . "' />" . $GLOBALS['xoopsSecurity']->getTokenHTML('BANNER_EDIT') . '</form>';
            }
        }

        /* Finnished Banners */
        echo '<br>';
        if ($result = $xoopsDB->query('SELECT bid, impressions, clicks, datestart, dateend FROM ' . $xoopsDB->prefix('bannerfinish') . " WHERE cid={$cid}")) {
            echo "<h4 class='content_title'>" . sprintf(_BANNERS_FINISHED, $name) . "</h4><hr />
                  <table summary=''>
                  <caption>" . sprintf(_BANNERS_FINISHED, $name) . '</caption>
                  <thead><tr>
                  <td>ID</td>
                  <td>' . _BANNERS_IMP_MADE . '</td>
                  <td>' . _BANNERS_CLICKS . '</td>
                  <td>' . _BANNERS_PER_CLICKS . '</td>
                  <td>' . _BANNERS_STARTED . '</td>
                  <td>' . _BANNERS_ENDED . "</td></tr></thead>
                  <tfoot><tr><td colspan='6'></td></tr></tfoot>";

            $i = 0;
            while (false !== (list($bid, $impressions, $clicks, $datestart, $dateend) = $xoopsDB->fetchRow($result))) {
                if ($impressions == 0) {
                    $percent = 0;
                } else {
                    $percent = substr(100 * $clicks / $impressions, 0, 5);
                }
                $class = ($i % 2 == 0) ? 'even' : 'odd';
                echo "<tbody><tr class='{$class}'>
                      <td>{$bid}</td>
                      <td>{$impressions}</td>
                      <td>{$clicks}</td>
                      <td>{$percent}%</td>
                      <td>" . formatTimestamp($datestart) . '</td>
                      <td>' . formatTimestamp($dateend) . '</td></tr></tbody>';
            }
            echo '</table></div>';
        }
        include $GLOBALS['xoops']->path('footer.php');
    } else {
        redirect_header('banners.php', 2);
    }
}

/*********************************************/
/* Function to let the client E-mail his     */
/* banner Stats                              */
/*********************************************/
/**
 * @param $cid
 * @param $bid
 */
function emailStats($cid, $bid)
{
    global $xoopsDB, $xoopsConfig;
    if ($_SESSION['banner_login'] != '' && $_SESSION['banner_pass'] != '') {
        $cid = (int)$cid;
        $bid = (int)$bid;
        if ($result2 = $xoopsDB->query(sprintf('SELECT name, email, passwd FROM %s WHERE cid=%u AND login=%s', $xoopsDB->prefix('bannerclient'), $cid, $xoopsDB->quoteString($_SESSION['banner_login'])))) {
            list($name, $email, $passwd) = $xoopsDB->fetchRow($result2);
            if ($_SESSION['banner_pass'] == $passwd) {
                if ($email == '') {
                    redirect_header('banners.php', 3, sprintf(_BANNERS_MAIL_ERROR, $name));
                } else {
                    if ($result = $xoopsDB->query('SELECT bid, imptotal, impmade, clicks, imageurl, clickurl, date FROM ' . $xoopsDB->prefix('banner') . " WHERE bid={$bid} AND cid={$cid}")) {
                        list($bid, $imptotal, $impmade, $clicks, $imageurl, $clickurl, $date) = $xoopsDB->fetchRow($result);
                        if ($impmade == 0) {
                            $percent = 0;
                        } else {
                            $percent = substr(100 * $clicks / $impmade, 0, 5);
                        }
                        if ($imptotal == 0) {
                            $left     = _BANNERS_UNLIMITED;
                            $imptotal = _BANNERS_UNLIMITED;
                        } else {
                            $left = $imptotal - $impmade;
                        }
                        $fecha       = date('F jS Y, h:iA.');
                        $subject     = sprintf(_BANNERS_MAIL_SUBJECT, $xoopsConfig['sitename']);
                        $message     = sprintf(_BANNERS_MAIL_MESSAGE, $xoopsConfig['sitename'], $name, $bid, $imageurl, $clickurl, $imptotal, $impmade, $left, $clicks, $percent, $fecha);
                        $xoopsMailer = &xoops_getMailer();
                        $xoopsMailer->useMail();
                        $xoopsMailer->setToEmails($email);
                        $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                        $xoopsMailer->setFromName($xoopsConfig['sitename']);
                        $xoopsMailer->setSubject($subject);
                        $xoopsMailer->setBody($message);
                        $xoopsMailer->send();
                        redirect_header('banners.php?op=Ok', 3, _BANNERS_MAIL_OK);
                    }
                }
            }
        }
    }
    redirect_header('banners.php', 2);
}

/*********************************************/
/* Function to let the client to change the  */
/* url for his banner                        */
/*********************************************/
/**
 * @param $cid
 * @param $bid
 * @param $url
 */
function change_banner_url_by_client($cid, $bid, $url)
{
    global $xoopsDB;
    if ($_SESSION['banner_login'] != '' && $_SESSION['banner_pass'] != '' && $url != '') {
        $cid = (int)$cid;
        $bid = (int)$bid;
        $sql = sprintf('SELECT passwd FROM %s WHERE cid=%u AND login=%s', $xoopsDB->prefix('bannerclient'), $cid, $xoopsDB->quoteString($_SESSION['banner_login']));
        if ($result = $xoopsDB->query($sql)) {
            list($passwd) = $xoopsDB->fetchRow($result);
            if ($_SESSION['banner_pass'] == $passwd) {
                $sql = sprintf('UPDATE %s SET clickurl=%s WHERE bid=%u AND cid=%u', $xoopsDB->prefix('banner'), $xoopsDB->quoteString($url), $bid, $cid);
                if ($xoopsDB->query($sql)) {
                    redirect_header('banners.php?op=Ok', 3, _BANNERS_DBUPDATED);
                }
            }
        }
    }
    redirect_header('banners.php', 2);
}

/**
 * @param $bid
 */
function clickbanner($bid)
{
    global $xoopsDB;
    $bid = (int)$bid;
    if ($bid > 0) {
        $bresult = $xoopsDB->query('SELECT clickurl FROM ' . $xoopsDB->prefix('banner') . " WHERE bid={$bid}");
        list($clickurl) = $xoopsDB->fetchRow($bresult);
        if ($clickurl) {
            if ($GLOBALS['xoopsSecurity']->checkReferer()) {
                $xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('banner') . " SET clicks=clicks+1 WHERE bid=$bid");
                header('Location: ' . $clickurl);
            } else {
                //No valid referer found so some javascript error or direct access found
                echo _BANNERS_NO_REFERER;
            }
            exit();
        }
    }
    redirect_header(XOOPS_URL, 3, _BANNERS_NO_ID);
}

XoopsLoad::load('XoopsFilterInput');
$myts = MyTextSanitizer::getInstance();

$op = '';
if (!empty($_POST['op'])) {
    // from $_POST we use keys: op, login, pass, url, pass, bid, cid
    $op = trim(XoopsFilterInput::clean($_POST['op'], 'STRING'));

    $clean_login = '';
    if (isset($_POST['login'])) {
        $clean_login = trim(XoopsFilterInput::clean($myts->stripSlashesGPC($_POST['login']), 'STRING'));
    }

    $clean_pass = '';
    if (isset($_POST['pass'])) {
        $clean_pass = trim(XoopsFilterInput::clean($myts->stripSlashesGPC($_POST['pass']), 'STRING'));
    }

    $clean_url = '';
    if (isset($_POST['url'])) {
        $clean_url = trim(XoopsFilterInput::clean($myts->stripSlashesGPC($_POST['url']), 'WEBURL'));
    }

    $clean_bid = 0;
    if (isset($_POST['bid'])) {
        $clean_bid = XoopsFilterInput::clean($_POST['bid'], 'INT');
    }

    $clean_cid = 0;
    if (isset($_POST['cid'])) {
        $clean_cid = XoopsFilterInput::clean($_POST['cid'], 'INT');
    }
} elseif (!empty($_GET['op'])) {
    // from $_POST we use keys: op, bid, cid
    $op = trim(XoopsFilterInput::clean($_GET['op'], 'STRING'));

    $clean_bid = 0;
    if (isset($_GET['bid'])) {
        $clean_bid = XoopsFilterInput::clean($_GET['bid'], 'INT');
    }

    $clean_cid = 0;
    if (isset($_GET['cid'])) {
        $clean_cid = XoopsFilterInput::clean($_GET['cid'], 'INT');
    }
}

$myts = MyTextSanitizer::getInstance();
switch ($op) {
    case 'click':
        $bid = $clean_bid;
        clickbanner($bid);
        break;
    case 'Ok':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$GLOBALS['xoopsSecurity']->check(true, false, 'BANNER_LOGIN')) {
                redirect_header('banners.php', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
            }

            $_SESSION['banner_login'] = $clean_login;
            $_SESSION['banner_pass']  = $clean_pass;
        }
        bannerstats();
        break;
    case _BANNERS_CHANGE:
        if (!$GLOBALS['xoopsSecurity']->check(true, false, 'BANNER_EDIT')) {
            redirect_header('banners.php', 3, implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $url = $clean_url;
        $bid = $clean_bid;
        $cid = $clean_cid;
        change_banner_url_by_client($cid, $bid, $url);
        break;
    case 'EmailStats':
        $bid = $clean_bid;
        $cid = $clean_cid;
        emailStats($cid, $bid);
        break;
    case 'login':
    default:
        clientlogin();
        break;
}
