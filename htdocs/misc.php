<?php
/**
 * XOOPS misc utilities
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package         core
 * @since           2.0.0
 */

include __DIR__ . '/mainfile.php';

xoops_loadLanguage('misc');

$action = isset($_GET['action']) ? strip_tags(trim($_GET['action'])) : '';
$action = isset($_POST['action']) ? strip_tags(trim($_POST['action'])) : $action;
$type = isset($_GET['type']) ? strip_tags(trim($_GET['type'])) : '';
$type = isset($_POST['type']) ? strip_tags(trim($_POST['type'])) : $type;
if ($action === 'showpopups') {
    xoops_header(false);
    // show javascript close button?
    $closebutton = 1;
    switch ($type) {
        case 'smilies':
            $target = isset($_GET['target']) ? trim($_GET['target']) : '';
            if ($target == '' || !preg_match('/^[0-9a-z_]*$/i', $target)) {
            } else {
                echo "<script type=\"text/javascript\"><!--//
            function doSmilie(addSmilie)
            {
                var textareaDom = window.opener.xoopsGetElementById(\"" . $target . "\");
                xoopsInsertText(textareaDom, addSmilie);
                textareaDom.focus();

//                return;
            }
            //-->
            </script>
            ";
                echo '</head><body>
            <table width="100%" class="outer">
            <tr><th colspan="3">' . _MSC_SMILIES . '</th></tr>
            <tr class="head"><td>' . _MSC_CODE . '</td><td>' . _MSC_EMOTION . '</td><td>' . _IMAGE . '</td></tr>';
                $myts = MyTextSanitizer::getInstance();
                if ($smiles = $myts->getSmileys()) {
                    $rcolor = 'even';
                    foreach ($smiles as $key => $smile) {
                        echo "<tr class='$rcolor'><td>" . $smile['code'] . '</td><td>' . $smile['emotion'] . "</td><td><img onmouseover='style.cursor=\"hand\"' onclick='doSmilie(\" " . $smile['code'] . " \");' src='" . XOOPS_UPLOAD_URL . '/' . $smile['smile_url'] . "' alt='' /></td></tr>";
                        $rcolor = ($rcolor === 'even') ? 'odd' : 'even';
                    }
                } else {
                    echo 'Could not retrieve data from the database.';
                }
                echo '</table>' . _MSC_CLICKASMILIE;
            }
            break;
        case 'avatars':
            ?>
<script language='javascript'>
        <!--//
        function myimage_onclick(counter)
        {
            window.opener.xoopsGetElementById("user_avatar").options[counter].selected = true;
            showAvatar();
            window.opener.xoopsGetElementById("user_avatar").focus();
            window.close();
        }
        function showAvatar()
        {
            window.opener.xoopsGetElementById("avatar").src='<?php
            echo XOOPS_UPLOAD_URL;
            ?>/' + window.opener.xoopsGetElementById("user_avatar").options[window.opener.xoopsGetElementById("user_avatar").selectedIndex].value;
        }
        //-->
        </script>
</head>
<body>
<h4><?php
            echo _MSC_AVAVATARS;
            ?></h4>
<form name='avatars'>
<table width='100%'>
    <tr>
        <?php
        /* @var  $avatar_handler XoopsAvatarHandler */
            $avatar_handler = xoops_getHandler('avatar');
            $avatarslist = $avatar_handler->getList('S');
            $cntavs = 0;
            $counter = isset($_GET['start']) ? (int)$_GET['start'] : 0;
            foreach ($avatarslist as $file => $name) {
                echo '<td><img src="uploads/' . $file . '" alt="' . $name . '" style="padding:10px; vertical-align:top;"  /><br>' . $name . '<br><input name="myimage" type="button" value="' . _SELECT . '" onclick="myimage_onclick(' . $counter . ')" /></td>';
                ++$counter;
                ++$cntavs;
                if ($cntavs > 8) {
                    echo '</tr><tr>';
                    $cntavs = 0;
                }
            }
            echo '</tr></table></form></div>';
            break;
        case 'friend':
            if (!$GLOBALS['xoopsSecurity']->check() || ! isset($_POST['op']) || $_POST['op'] === 'sendform') {
                if ($xoopsUser) {
                    $yname = $xoopsUser->getVar('uname', 'e');
                    $ymail = $xoopsUser->getVar('email', 'e');
                    $fname = '';
                    $fmail = '';
                } else {
                    $yname = '';
                    $ymail = '';
                    $fname = '';
                    $fmail = '';
                }
                printCheckForm();
                echo '</head><body>';
                echo "<div class='errorMsg'>" . implode('<br>', $GLOBALS['xoopsSecurity']->getErrors()) . '</div>';
                echo '
            <form action="' . XOOPS_URL . '/misc.php" method="post" onsubmit="return checkForm();"><table  width="100%" class="outer" cellspacing="1"><tr><th colspan="2">' . _MSC_RECOMMENDSITE . '</th></tr>';
                echo "<tr><td class='head'>
                <input type='hidden' name='op' value='sendsite' />
                <input type='hidden' name='action' value='showpopups' />
                <input type='hidden' name='type' value='friend' />\n";
                echo _MSC_YOURNAMEC . "</td><td class='even'><input type='text' name='yname' value='{$yname}' id='yname' /></td></tr>
                <tr><td class='head'>" . _MSC_YOUREMAILC . "</td><td class='odd'><input type='text' name='ymail' value='{$ymail}' id='ymail' /></td></tr>
                <tr><td class='head'>" . _MSC_FRIENDNAMEC . "</td><td class='even'><input type='text' name='fname' value='{$fname}' id='fname' /></td></tr>
                <tr><td class='head'>" . _MSC_FRIENDEMAILC . "</td><td class='odd'><input type='text' name='fmail' value='{$fmail}' id='fmail' /></td></tr>
                <tr><td class='head'>&nbsp;</td><td class='even'><input type='submit' value='" . _SEND . "' />&nbsp;<input value='" . _CLOSE . "' type='button' onclick='window.close();' />" . $GLOBALS['xoopsSecurity']->getTokenHTML() . "</td></tr>
                </table></form>\n";
                $closebutton = 0;
            } elseif ($_POST['op'] === 'sendsite') {
                $myts = MyTextSanitizer::getInstance();
                if ($xoopsUser) {
                    $ymail = $xoopsUser->getVar('email');
                } else {
                    $ymail = isset($_POST['ymail']) ? $myts->stripSlashesGPC(trim($_POST['ymail'])) : '';
                }
                if (!isset($_POST['yname']) || trim($_POST['yname']) == '' || $ymail == '' || !isset($_POST['fname']) || trim($_POST['fname']) == '' || !isset($_POST['fmail']) || trim($_POST['fmail']) == '') {
                    redirect_header(XOOPS_URL . '/misc.php?action=showpopups&amp;type=friend&amp;op=sendform', 2, _MSC_NEEDINFO);
                }
                $yname = $myts->stripSlashesGPC(trim($_POST['yname']));
                $fname = $myts->stripSlashesGPC(trim($_POST['fname']));
                $fmail = $myts->stripSlashesGPC(trim($_POST['fmail']));
                if (!checkEmail($fmail) || !checkEmail($ymail) || preg_match("/[\\0-\\31]/", $yname)) {
                    $errormessage = _MSC_INVALIDEMAIL1 . '<br>' . _MSC_INVALIDEMAIL2 . '';
                    redirect_header(XOOPS_URL . '/misc.php?action=showpopups&amp;type=friend&amp;op=sendform', 2, $errormessage);
                }
                $xoopsMailer =& xoops_getMailer();
                $xoopsMailer->setTemplate('tellfriend.tpl');
                $xoopsMailer->assign('SITENAME', $xoopsConfig['sitename']);
                $xoopsMailer->assign('ADMINMAIL', $xoopsConfig['adminmail']);
                $xoopsMailer->assign('SITEURL', XOOPS_URL . '/');
                $xoopsMailer->assign('YOUR_NAME', $yname);
                $xoopsMailer->assign('FRIEND_NAME', $fname);
                $xoopsMailer->setToEmails($fmail);
                $xoopsMailer->setFromEmail($ymail);
                $xoopsMailer->setFromName($yname);
                $xoopsMailer->setSubject(sprintf(_MSC_INTSITE, $xoopsConfig['sitename']));
                //openTable();
                if (!$xoopsMailer->send()) {
                    echo $xoopsMailer->getErrors();
                } else {
                    echo '<div><h4>' . _MSC_REFERENCESENT . '</h4></div>';
                }
                //closeTable();
            }
            break;
        case 'online':
            $isadmin = $xoopsUserIsAdmin;
            echo '</head><body>';
            echo '<table style="width:100%;" cellspacing="1" class="outer"><tr><th colspan="3">' . _WHOSONLINE . '</th></tr>';
            $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
            /* @var $online_handler XoopsOnlineHandler  */
            $online_handler = xoops_getHandler('online');
            $online_total = $online_handler->getCount();
            $limit = ($online_total > 20) ? 20 : $online_total;
            $criteria = new CriteriaCompo();
            $criteria->setLimit($limit);
            $criteria->setStart($start);
            $onlines = $online_handler->getAll($criteria);
            $count = count($onlines);
            /* @var $module_handler XoopsModuleHandler  */
            $module_handler = xoops_getHandler('module');
            $modules = $module_handler->getList(new Criteria('isactive', 1));
            for ($i = 0; $i < $count; ++$i) {
                if ($onlines[$i]['online_uid'] == 0) {
                    $onlineUsers[$i]['user'] = '';
                } else {
                    $onlineUsers[$i]['user'] =  new XoopsUser($onlines[$i]['online_uid']);
                }
                $onlineUsers[$i]['ip'] = $onlines[$i]['online_ip'];
                $onlineUsers[$i]['updated'] = $onlines[$i]['online_updated'];
                $onlineUsers[$i]['module'] = ($onlines[$i]['online_module'] > 0) ? $modules[$onlines[$i]['online_module']] : '';
            }
            $class = 'even';
            for ($i = 0; $i < $count; ++$i) {
                $class = ($class === 'odd') ? 'even' : 'odd';
                echo '<tr style="vertical-align:middle; text-align: center;" class="' . $class . '">';
                if (is_object($onlineUsers[$i]['user'])) {
                    $avatar = $onlineUsers[$i]['user']->getVar('user_avatar') ? '<img src="' . XOOPS_UPLOAD_URL . '/' . $onlineUsers[$i]['user']->getVar('user_avatar') . '" alt="" />' : '&nbsp;';
                    echo '<td>' . $avatar . "</td><td><a href=\"javascript:window.opener.location='" . XOOPS_URL . '/userinfo.php?uid=' . $onlineUsers[$i]['user']->getVar('uid') . "';window.close();\">" . $onlineUsers[$i]['user']->getVar('uname') . '</a>';
                } else {
                    echo '<td>&nbsp;</td><td>' . $xoopsConfig['anonymous'];
                }
                if ($isadmin == 1) {
                    echo '<br>(' . $onlineUsers[$i]['ip'] . ')';
                }
                echo '</td><td>' . $onlineUsers[$i]['module'] . '</td></tr>';
            }
            echo '</table><br>';
            if ($online_total > 20) {
                include_once $GLOBALS['xoops']->path('class/pagenav.php');
                $nav = new XoopsPageNav($online_total, 20, $start, 'start', 'action=showpopups&amp;type=online');
                echo '<div style="text-align: right;">' . $nav->renderNav() . '</div>';
            }
            break;
        case 'ssllogin':
            if ($xoopsConfig['use_ssl'] && isset($_POST[$xoopsConfig['sslpost_name']]) && is_object($xoopsUser)) {
                include_once $GLOBALS['xoops']->path('language/' . $xoopsConfig['language'] . '/user.php');
                echo sprintf(_US_LOGGINGU, $xoopsUser->getVar('uname'));
                echo '<div style="text-align:center;"><input class="formButton" value="' . _CLOSE . '" type="button" onclick="window.opener.location.reload();window.close();" /></div>';
                $closebutton = false;
            }
            break;
        default:
            break;
    }
    if ($closebutton) {
        echo '<div style="text-align:center;"><input class="formButton" value="' . _CLOSE . '" type="button" onclick="window.close();" /></div>';
    }
    xoops_footer();
}

function printCheckForm()
{
    ?>
    <script language='javascript' type="text/javascript">
        <!--//
        function checkForm()
        {
            if ( xoopsGetElementById("yname").value == "" ) {
                alert( "<?php
    echo _MSC_ENTERYNAME;
    ?>" );
                xoopsGetElementById("yname").focus();

                return false;
            } elseif ( xoopsGetElementById("fname").value == "" ) {
                alert( "<?php
    echo _MSC_ENTERFNAME;
    ?>" );
                xoopsGetElementById("fname").focus();

                return false;
            } elseif ( xoopsGetElementById("fmail").value =="") {
                alert( "<?php
    echo _MSC_ENTERFMAIL;
    ?>" );
                xoopsGetElementById("fmail").focus();

                return false;
            } else {
                return true;
            }
        }
        //-->
    </script>
    <?php

}
