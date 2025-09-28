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
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @since               2.0.0
 * @author              Kazumi Ono <webmaster@myweb.ne.jp>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @author              DuGris aka L. Jen <http://www.dugris.info>
 * @author              Kris <kris@frxoops.org>
 */

use Xmf\Request;

$xoopsOption['pagetype'] = 'banners';
include __DIR__ . '/mainfile.php';

/**
 * Function to let your client login to see the stats
 * @return void
 */
function clientlogin()
{
    global $xoopsDB, $xoopsLogger, $xoopsConfig;
    include __DIR__ . '/header.php';
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

/**
 * Function to display the banners stats for each client
 * @return void
 */
function bannerstats()
{
    global $xoopsDB, $xoopsConfig, $xoopsLogger, $myts;
    if ($_SESSION['banner_login'] == '' || $_SESSION['banner_pass'] == '') {
        redirect_header('banners.php', 2, _BANNERS_NO_LOGIN_DATA);
    }
    $sql = sprintf('SELECT cid, name, passwd FROM %s WHERE login=%s', $xoopsDB->prefix('bannerclient'), $xoopsDB->quote($_SESSION['banner_login']));
    $result = $xoopsDB->query($sql);
    if (!$xoopsDB->isResultSet($result)) {
        throw new \RuntimeException(
            \sprintf(_DB_QUERY_ERROR, $sql) . $xoopsDB->error(),
            E_USER_ERROR,
        );
    }
    [$cid, $name, $passwd] = $xoopsDB->fetchRow($result);
    if ($_SESSION['banner_pass'] == $passwd) {
        include $GLOBALS['xoops']->path('header.php');
        $cid = (int) $cid;
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

        $sql = 'SELECT bid, imptotal, impmade, clicks, date FROM ' . $xoopsDB->prefix('banner') . " WHERE cid={$cid}";
        $result = $xoopsDB->query($sql);
        if (!$xoopsDB->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $xoopsDB->error(),
                E_USER_ERROR,
            );
        }
        $i      = 0;
        while (false !== ([$bid, $imptotal, $impmade, $clicks, $date] = $xoopsDB->fetchRow($result))) {
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
              <h4 class='content_title'>" . _BANNERS_FOW_IN . htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES | ENT_HTML5) . '</h4><hr />';

        $sql = 'SELECT bid, imageurl, clickurl, htmlbanner, htmlcode FROM ' . $xoopsDB->prefix('banner') . " WHERE cid={$cid}";
        $result = $xoopsDB->query($sql);
        if (!$xoopsDB->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $xoopsDB->error(),
                E_USER_ERROR,
            );
        }
        while (false !== ([$bid, $imageurl, $clickurl, $htmlbanner, $htmlcode] = $xoopsDB->fetchRow($result))) {
            $numrows = $xoopsDB->getRowsNum($result);
            if ($numrows > 1) {
                echo '<br>';
            }
            if (!empty($htmlbanner) && !empty($htmlcode)) {
                echo $myts->displayTarea($htmlcode);
            } else {
                $extension = strtolower(substr($imageurl, strrpos($imageurl, '.')));
                if ($extension === '.swf') {
                    // Inform user that SWF is unsupported
                    echo "<p>" ._BANNERS_NO_FLASH  ."</p>";
                } elseif (in_array($extension, ['.mp4', '.webm', '.ogg'])) {
                    // Handle actual video files
                    echo "<video width='468' height='60' controls>
                <source src='{$imageurl}' type='video/" . substr($extension, 1) . "'>
                Your browser does not support the video tag.
              </video>";
                } else {
                    // Assume it’s an image otherwise
                    echo "<img src='{$imageurl}' alt='' />";
                }
            }
            echo '<br><strong>' . _BANNERS_ID . $bid . '</strong><br>' . sprintf(_BANNERS_SEND_STATS, 'banners.php?op=EmailStats&amp;cid=' . $cid . '&amp;bid=' . $bid) . '<br>';
            if (!$htmlbanner) {
                $clickurl = htmlspecialchars($clickurl, ENT_QUOTES | ENT_HTML5);
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
        $sql    = 'SELECT bid, impressions, clicks, datestart, dateend FROM ' . $xoopsDB->prefix('bannerfinish') . " WHERE cid={$cid}";
        $result = $xoopsDB->query($sql);
        if ($xoopsDB->isResultSet($result)) {
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
            while (false !== ([$bid, $impressions, $clicks, $datestart, $dateend] = $xoopsDB->fetchRow($result))) {
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

/**
 * Function to let clients email their banner's stats
 * @param int|string $cid
 * @param int|string $bid
 * @return void
 */
function emailStats($cid, $bid)
{
    global $xoopsDB, $xoopsConfig;
    if ($_SESSION['banner_login'] != '' && $_SESSION['banner_pass'] != '') {
        $cid     = (int) $cid;
        $bid     = (int) $bid;
        $sql     = sprintf('SELECT name, email, passwd FROM %s WHERE cid=%u AND login=%s', $xoopsDB->prefix('bannerclient'), $cid, $xoopsDB->quote($_SESSION['banner_login']));
        $result2 = $xoopsDB->query($sql);
        if ($xoopsDB->isResultSet($result2)) {
            [$name, $email, $passwd] = $xoopsDB->fetchRow($result2);
            if ($_SESSION['banner_pass'] == $passwd) {
                if ($email == '') {
                    redirect_header('banners.php', 3, sprintf(_BANNERS_MAIL_ERROR, $name));
                } else {
                    $sql    = 'SELECT bid, imptotal, impmade, clicks, imageurl, clickurl, date FROM ' . $xoopsDB->prefix('banner') . " WHERE bid={$bid} AND cid={$cid}";
                    $result = $xoopsDB->query($sql);
                    if ($xoopsDB->isResultSet($result)) {
                        [$bid, $imptotal, $impmade, $clicks, $imageurl, $clickurl, $date] = $xoopsDB->fetchRow($result);
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
                        $xoopsMailer = xoops_getMailer();
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

/**
 * Function to let clients change their banner's URL
 * @param int|string $cid
 * @param int|string $bid
 * @param string $url
 * @return void
 */
function change_banner_url_by_client($cid, $bid, $url)
{
    global $xoopsDB;
    if ($_SESSION['banner_login'] != '' && $_SESSION['banner_pass'] != '' && $url != '') {
        $cid    = (int) $cid;
        $bid    = (int) $bid;
        $sql    = sprintf('SELECT passwd FROM %s WHERE cid=%u AND login=%s', $xoopsDB->prefix('bannerclient'), $cid, $xoopsDB->quote($_SESSION['banner_login']));
        $result = $xoopsDB->query($sql);
        if ($xoopsDB->isResultSet($result)) {
            [$passwd] = $xoopsDB->fetchRow($result);
            if ($_SESSION['banner_pass'] == $passwd) {
                $sql = sprintf('UPDATE %s SET clickurl=%s WHERE bid=%u AND cid=%u', $xoopsDB->prefix('banner'), $xoopsDB->quote($url), $bid, $cid);
                if ($xoopsDB->query($sql)) {
                    redirect_header('banners.php?op=Ok', 3, _BANNERS_DBUPDATED);
                }
            }
        }
    }
    redirect_header('banners.php', 2);
}

/**
 * @param int|string $bid
 * @return void
 */
function clickbanner($bid)
{
    global $xoopsDB;
    $bid = (int) $bid;
    if ($bid > 0) {
        $sql = 'SELECT clickurl FROM ' . $xoopsDB->prefix('banner') . " WHERE bid={$bid}";
        $result = $xoopsDB->query($sql);
        if (!$xoopsDB->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $xoopsDB->error(),
                E_USER_ERROR,
            );
        }
        [$clickurl] = $xoopsDB->fetchRow($result);
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


$op = '';
$clean_bid = 0;
$clean_cid = 0;
$clean_login = '';
$clean_pass = '';
$clean_url = '';
if (!empty($_POST['op'])) {
    // from $_POST we use keys: op, login, pass, url, pass, bid, cid
    $op = Request::getCmd('op', '', 'POST');

    if (isset($_POST['login'])) {
        $clean_login = Request::getString('login', '', 'POST');
    }

    if (isset($_POST['pass'])) {
        $clean_pass = Request::getString('pass', '', 'POST');
    }

    if (isset($_POST['url'])) {
        $clean_url = Request::getUrl('url', '', 'POST');
    }

    if (isset($_POST['bid'])) {
        $clean_bid = Request::getInt('bid', 0, 'POST');
    }

    if (isset($_POST['cid'])) {
        $clean_cid = Request::getInt('cid', 0, 'POST');
    }
} elseif (!empty($_GET['op'])) {
    // from $_GET we use keys: op, bid, cid
    $op = Request::getCmd('op', '', 'GET');

    if (isset($_GET['bid'])) {
        $clean_bid = Request::getInt('bid', 0, 'GET');
    }

    if (isset($_GET['cid'])) {
        $clean_cid = Request::getInt('cid', 0, 'GET');
    }
}

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
