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
 * @license         GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package         core
 * @since           2.0.0
 */

use Xmf\Request;

include __DIR__ . '/mainfile.php';

xoops_loadLanguage('misc');
xoops_loadLanguage('user');

$action = Request::getCmd('action', '');
$type = Request::getCmd('type', '');

if ($action !== 'showpopups') {
    header('HTTP/1.0 404 Not Found');
    exit();
}
    xoops_header(false);
    // show javascript close button?
    $closebutton = 1;
    switch ($type) {
        case 'smilies':
            $target = Request::getString('target', '');
            if ($target !== '' && preg_match('/^[0-9a-z_]*$/i', $target)) {
                $variables = array();
$javaScript = <<<EOSMJS
<script type="text/javascript">
function doSmilie(addSmilie) {
    var textareaDom = window.opener.xoopsGetElementById("{$target}");
    xoopsInsertText(textareaDom, addSmilie);
    textareaDom.focus();
}
</script>
EOSMJS;
                $variables['headContents'] = $javaScript;
                $variables['closeHead'] = true;
                $variables['lang_smiles'] = _MSC_SMILIES;
                $variables['lang_code'] = _MSC_CODE;
                $variables['lang_emotion'] = _MSC_EMOTION;
                $variables['lang_image'] = _IMAGE;
                $variables['lang_clicksmile'] = _MSC_CLICKASMILIE;
                $variables['lang_close'] = _CLOSE;
                $variables['upload_url'] = XOOPS_UPLOAD_URL .'/';
                $myts = \MyTextSanitizer::getInstance();
                if ($smiles = $myts->getSmileys(false)) {
                    $variables['smilies'] = $smiles;
                } else {
                    $variables['smilies'] = array();
                    trigger_error('Could not retrieve smilies from the database.', E_USER_NOTICE);
                }
                xoops_misc_popup_body('db:system_misc_smilies.tpl', $variables);
            }
            break;
        case 'avatars':
            /** @var  XoopsAvatarHandler $avatarHandler */
            $avatarHandler = xoops_getHandler('avatar');
            $avatarsList = $avatarHandler->getList('S', true);

            $upload_url = XOOPS_UPLOAD_URL . '/';
            $javaScript = <<<EOAVJS
<script language='javascript'>
    function myimage_onclick(counter) {
        window.opener.xoopsGetElementById("user_avatar").options[counter].selected = true;
        showAvatar();
        window.opener.xoopsGetElementById("user_avatar").focus();
        window.close();
    }
    function showAvatar() {
        window.opener.xoopsGetElementById("avatar").src="{$upload_url}" + window.opener.xoopsGetElementById("user_avatar").options[window.opener.xoopsGetElementById("user_avatar").selectedIndex].value;
    }
</script>
EOAVJS;
            $variables['headContents'] = $javaScript;
            $variables['closeHead'] = true;
            $variables['lang_avavatars'] = _MSC_AVAVATARS;
            $variables['lang_select'] = _SELECT;
            $variables['lang_close'] = _CLOSE;
            $variables['avatars'] = $avatarsList;
            $variables['upload_url'] = $upload_url;
            xoops_misc_popup_body('db:system_misc_avatars.tpl', $variables);
            break;
        case 'friend':
            include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

            $variables['headContents'] = '';
            $variables['closeHead'] = true;
            $variables['lang_recommend'] = _MSC_RECOMMENDSITE;
            $variables['lang_yourname'] = _MSC_YOURNAMEC;
            $variables['lang_youremail'] = _MSC_YOUREMAILC;
            $variables['lang_friendname'] = _MSC_FRIENDNAMEC;
            $variables['lang_friendemail'] = _MSC_FRIENDEMAILC;
            $variables['lang_send'] = _SEND;
            $variables['lang_close'] = _CLOSE;

            $error = false;
            $errorMessage = '';

            $method = Request::getMethod();
            if ('POST' === $method) {
                $yname = Request::getString('yname', '', 'POST');
                $ymail = Request::getString('ymail', '', 'POST');
                $fname = Request::getString('fname', '', 'POST');
                $fmail = Request::getString('fmail', '', 'POST');

                if (!$GLOBALS['xoopsSecurity']->check()) {
                    $error = true;
                    $temp = $GLOBALS['xoopsSecurity']->getErrors();
                    $errorMessage = (is_array($temp)) ? implode('<br>', $temp) : $temp;
                }
                if (!$error && false === filter_var($ymail, FILTER_VALIDATE_EMAIL)) {
                    $error = true;
                    $errorMessage = _MSC_INVALIDEMAIL1;
                }
                if (!$error && false === filter_var($fmail, FILTER_VALIDATE_EMAIL)) {
                    $error = true;
                    $errorMessage = _MSC_INVALIDEMAIL1;
                }
                if (!$error && '' === $yname) {
                    $error = true;
                    $errorMessage = _MSC_ENTERYNAME;
                }
                if (!$error && '' === $fname) {
                    $error = true;
                    $errorMessage = _MSC_ENTERFNAME;
                }
                if ($error) {
                    $variables['errorMessage'] = $errorMessage;
                }
            }
            if ('POST' === $method && false === $error) {
                // send it
                $xoopsMailer = xoops_getMailer();
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

                if (!$xoopsMailer->send()) {
                    $error = true;
                    $errorMessage = $xoopsMailer->getErrors();
                    $variables['errorMessage'] = $errorMessage;
                } else {
                    $variables['successMessage'] = _MSC_REFERENCESENT;
                }
            } else {
                // build form
                $ynameDefault = '';
                $ymailDefault = '';
                if (is_object($xoopsUser)) {
                    $ynameDefault = $xoopsUser->getVar('uname', 'e');
                    $ymailDefault = $xoopsUser->getVar('email', 'e');
                }
                $yname = Request::getString('yname', $ynameDefault);
                $ymail = Request::getString('ymail', $ymailDefault);
                $fname = Request::getString('fname', '');
                $fmail = Request::getString('fmail', '');
            }
            $form = new XoopsThemeForm(_MSC_RECOMMENDSITE, 'recommendus', XOOPS_URL . '/misc.php', 'post', true);
            $ynameElelment = new XoopsFormText(_MSC_YOURNAMEC, 'yname', 32, 64, $yname);
            $form->addElement($ynameElelment, true);
            $ymailElelment = new XoopsFormText(_MSC_YOUREMAILC, 'ymail', 48, 96, $ymail);
            $form->addElement($ymailElelment, true);
            if ('' !== $ymail && false === filter_var($ymail, FILTER_VALIDATE_EMAIL)) {
                $ynameElelment->setDescription(_MSC_INVALIDEMAIL1);
            }
            $fnameElement = new XoopsFormText(_MSC_FRIENDNAMEC, 'fname', 32, 64, $fname);
            $form->addElement($fnameElement, true);
            $fmailElelment = new XoopsFormText(_MSC_FRIENDEMAILC, 'fmail', 48, 96, $fmail);
            if ('' !== $fmail && false === filter_var($fmail, FILTER_VALIDATE_EMAIL)) {
                $fmailElelment->setDescription(_MSC_INVALIDEMAIL1);
            }
            $form->addElement($fmailElelment, true);
            $form->addElement(new XoopsFormHidden('action', $action));
            $form->addElement(new XoopsFormHidden('type', $type));
            $form->addElement(new XoopsFormButton('', 'submit', _SEND, 'submit'));

            xoops_misc_popup_body('db:system_misc_friend.tpl', $variables, true, true, $form);
            break;
        case 'online':
            include_once $GLOBALS['xoops']->path('class/pagenav.php');

            $isadmin = false;
            $timezone = $xoopsConfig['default_TZ'];
            if (is_object($xoopsUser)) {
                $isadmin = $xoopsUser->isAdmin();
                $timezone = $xoopsUser->timezone();
            }

            $variables['headContents'] = '';
            $variables['closeHead'] = true;
            $variables['isadmin'] = $isadmin;
            $variables['lang_whoisonline'] = _WHOSONLINE;
            $variables['lang_close'] = _CLOSE;
            $variables['lang_avatar'] = _US_AVATAR;
            $variables['anonymous'] = $xoopsConfig['anonymous'];
            $variables['upload_url'] = XOOPS_UPLOAD_URL .'/';

            $start = Request::getInt('start', 0);
            $limit = 20; // how many to make available per page

            /** @var XoopsModuleHandler $module_handler */
            $module_handler = xoops_getHandler('module');
            $modules = $module_handler->getObjects(new Criteria('isactive', 1), true);

            /** @var XoopsOnlineHandler $onlineHandler */
            $onlineHandler = xoops_getHandler('online');
            $onlineTotal = $onlineHandler->getCount();
            $criteria = new CriteriaCompo();
            $criteria->setStart($start);
            $criteria->setLimit($limit);
            $onlines = $onlineHandler->getAll($criteria);

            $onlineUserInfo = array();
            foreach ($onlines as $online) {
                $info = array();
                if (0 == $online['online_uid']) {
                    $info['uid'] = $online['online_uid'];
                    $info['uname'] = $xoopsConfig['anonymous'];;
                    $info['name'] = '';
                    $info['xoopsuser'] = false;
                    $info['avatar'] = 'avatars/blank.gif';
                } else {
                    /** @var XoopsUser $onlineUser */
                    $onlineUser = new XoopsUser($online['online_uid']);
                    $info['xoopsuser'] = $onlineUser;
                    $info['uid'] = $online['online_uid'];
                    $info['uname'] = $online['online_uname'];
                    $info['name'] = $onlineUser->name();
                    $info['avatar'] = $onlineUser->user_avatar();
                }
                $info['updated'] = formatTimestamp($online['online_updated'], 'm', $timezone);
                $info['ip'] = $online['online_ip'];
                $info['mid'] = $online['online_module'];
                if (0 === $online['online_module'] || !isset($modules[$online['online_module']])) {
                    $info['module_name'] = '';
                    $info['dirname'] = '';
                } else {
                    /** @var \XoopsModule $mod */
                    $mod = $modules[$online['online_module']];
                    $info['module_name'] = $mod->name();
                    $info['dirname'] = $mod->dirname();
                }
                $onlineUserInfo[] = $info;
            }
            $variables['onlineUserInfo'] = $onlineUserInfo;

            $nav = new XoopsPageNav($onlineTotal, $limit, $start, 'start', 'action=showpopups&amp;type=online');
            $variables['pageNav'] = $nav->renderNav();

            xoops_misc_popup_body('db:system_misc_online.tpl', $variables, true, true);
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
    $closebutton=false;
    if ($closebutton) {
        echo '<div style="text-align:center;"><input class="formButton" value="' . _CLOSE . '" type="button" onclick="window.close();" /></div>';
    }
    xoops_footer();

/**
 * xoops_misc_popup_body()
 *
 * @param string         $template  smarty template to user
 * @param array          $variables array of variables to assign for template
 * @param bool           $closehead if true, close the head element and open the body
 * @param XoopsForm|null $xoopsForm optioal form
 * @return void  echos rendered template
 */
function xoops_misc_popup_body($template, $variables, $closehead = true, $closebutton = true, $xoopsForm = null)
{
    global $xoopsConfig;

    $themeSet = $xoopsConfig['theme_set'];
    $themePath = XOOPS_THEME_PATH . '/' . $themeSet . '/';
    $themeUrl = XOOPS_THEME_URL . '/' . $themeSet . '/';
    include_once XOOPS_ROOT_PATH . '/class/template.php';
    $headTpl = new \XoopsTpl();
    //$GLOBALS['xoopsHeadTpl'] = $headTpl;  // expose template for use by caller
    $headTpl->assign(array(
        'closeHead'      => (bool) $closehead,
        'closeButton'    => (bool) $closebutton,
        'themeUrl'       => $themeUrl,
        'themePath'      => $themePath,
        'xoops_langcode' => _LANGCODE,
        'xoops_charset'  => _CHARSET,
        'xoops_sitename' => $xoopsConfig['sitename'],
        'xoops_url'      => XOOPS_URL,
    ));

    $headTpl->assign($variables);
    if ($xoopsForm instanceof XoopsForm) {
        $xoopsForm->assign($headTpl);
    }

    $output = $headTpl->fetch($template);
    echo $output;
}
