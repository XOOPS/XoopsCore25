<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    2000-2025 XOOPS Project (https://xoops.org)
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');

/**
 * @return array|bool
 */
function b_system_online_show()
{
    global $xoopsUser, $xoopsModule;
    /** @var XoopsOnlineHandler $online_handler */
    $online_handler = xoops_getHandler('online');
    // set gc probability to 10% for now..
    if (mt_rand(1, 100) < 11) {
        $online_handler->gc(300);
    }
    if (is_object($xoopsUser)) {
        $uid   = $xoopsUser->getVar('uid');
        $uname = $xoopsUser->getVar('uname');
    } else {
        $uid   = 0;
        $uname = '';
    }
    $requestIp = \Xmf\IPAddress::fromRequest()->asReadable();
    $requestIp = (false === $requestIp) ? '0.0.0.0' : $requestIp;
    if (is_object($xoopsModule)) {
        $online_handler->write($uid, $uname, time(), $xoopsModule->getVar('mid'), $requestIp);
    } else {
        $online_handler->write($uid, $uname, time(), 0, $requestIp);
    }
    $onlines = $online_handler->getAll();
    if (!empty($onlines)) {
        $total   = count($onlines);
        $block   = [];
        $guests  = 0;

        $member_links = [];
        for ($i = 0; $i < $total; ++$i) {
            if ($onlines[$i]['online_uid'] > 0) {
                $member_links[] = '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $onlines[$i]['online_uid'] . '" title="' . $onlines[$i]['online_uname'] . '">' . $onlines[$i]['online_uname'] . '</a>';
            } else {
                ++$guests;
            }
        }
        $members = implode(', ', $member_links);
        $block['online_names'] = $members;
        $block['online_total'] = sprintf(_ONLINEPHRASE, $total);

        if (is_object($xoopsModule)) {
            $mytotal = $online_handler->getCount(new Criteria('online_module', $xoopsModule->getVar('mid')));
            $block['online_total'] .= ' (' . sprintf(_ONLINEPHRASEX, $mytotal, $xoopsModule->getVar('name')) . ')';
        }
        $block['lang_members']   = _MEMBERS;
        $block['lang_guests']    = _GUESTS;
        $block['online_names']   = $members;
        $block['online_members'] = $total - $guests;
        $block['online_guests']  = $guests;
        $block['lang_more']      = _MORE;

        return $block;
    } else {
        return false;
    }
}

/**
 * @return array|bool
 */
function b_system_login_show()
{
    global $xoopsUser, $xoopsConfig;
    if (!$xoopsUser) {
        $block                     = [];
        $block['lang_username']    = _USERNAME;
        $block['unamevalue']       = '';
        $block['lang_password']    = _PASSWORD;
        $block['lang_login']       = _LOGIN;
        $block['lang_lostpass']    = _MB_SYSTEM_LPASS;
        $block['lang_registernow'] = _MB_SYSTEM_RNOW;
        //$block['lang_rememberme'] = _MB_SYSTEM_REMEMBERME;
        if ($xoopsConfig['use_ssl'] == 1 && $xoopsConfig['sslloginlink'] != '') {
            $block['sslloginlink'] = "<a href=\"javascript:openWithSelfMain('" . $xoopsConfig['sslloginlink'] . "', 'ssllogin', 300, 200);\">" . _MB_SYSTEM_SECURE . '</a>';
        } elseif ($GLOBALS['xoopsConfig']['usercookie']) {
            $block['lang_rememberme'] = _MB_SYSTEM_REMEMBERME;
        }

        return $block;
    }

    return false;
}

/**
 * @return array
 */
function b_system_main_show()
{
    global $xoopsUser, $xoopsModule;
    $block               = [];
    $block['lang_home']  = _MB_SYSTEM_HOME;
    $block['lang_close'] = _CLOSE;

    /** @var XoopsModuleHandler $module_handler */
    $module_handler      = xoops_getHandler('module');
    $criteria            = new CriteriaCompo(new Criteria('hasmain', 1));
    $criteria->add(new Criteria('isactive', 1));
    $criteria->add(new Criteria('weight', 0, '>'));
    $modules            = $module_handler->getObjects($criteria, true);

    /** @var XoopsGroupPermHandler $moduleperm_handler */
    $moduleperm_handler = xoops_getHandler('groupperm');
    $groups             = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    $read_allowed       = $moduleperm_handler->getItemIds('module_read', $groups);

    $block['modules'] = [];
    foreach (array_keys($modules) as $i) {
        if (in_array($i, $read_allowed)) {
            $moduleDirname = $modules[$i]->getVar('dirname');
            $block['modules'][$i]['name']      = $modules[$i]->getVar('name');
            $block['modules'][$i]['directory'] = $moduleDirname;
            $block['modules'][$i]['icon'] = $module_handler->getByDirname($moduleDirname)->getInfo('icon') ?: 'fa-solid fa-caret-right';

            $sublinks                          = $modules[$i]->subLink();
            if (!empty($xoopsModule) && $i == $xoopsModule->getVar('mid')) {
                $block['modules'][$i]['highlight'] = true;
                $block['nothome']                  = true;
            }

            $block['modules'][$i]['sublinks'] = [];
            if (count($sublinks) > 0 && !empty($xoopsModule) && $i == $xoopsModule->getVar('mid')) {
                foreach ($sublinks as $sublink) {
                    if (hasSublinkPermission($sublink, $groups)) {
                        // Normalize URL for backward compatibility
                        $url = parse_url($sublink['url'], PHP_URL_PATH) ?: $sublink['url'];
                    $block['modules'][$i]['sublinks'][] = [
                            'id'       => !empty($sublink['id']) ? $sublink['id'] : basename($url), // Fallback
                        'name' => $sublink['name'],
                            'url'      => $sublink['url'],
                            'full_url' => XOOPS_URL . '/modules/' . $moduleDirname . '/' . $sublink['url'],
                            'icon'     => !empty($sublink['icon']) ? $sublink['icon'] : 'fa-solid fa-caret-right',
                    ];
                }
                }
            }
        }
    }

    return $block;
}

function hasSublinkPermission($sublink, $groups)
{
    return true; // Placeholder
}

/**
 * @return array
 */
function b_system_search_show()
{
    $block                   = [];
    $block['lang_search']    = _MB_SYSTEM_SEARCH;
    $block['lang_advsearch'] = _MB_SYSTEM_ADVS;

    return $block;
}

/**
 * @return array|bool
 */
function b_system_user_show()
{
    global $xoopsUser;
    if (!is_object($xoopsUser)) {
        return false;
    }
    $block                       = [];
    $block['lang_youraccount']   = _MB_SYSTEM_VACNT;
    $block['lang_editaccount']   = _MB_SYSTEM_EACNT;
    $block['lang_notifications'] = _MB_SYSTEM_NOTIF;
    $block['uid']                = $xoopsUser->getVar('uid');
    $block['lang_logout']        = _MB_SYSTEM_LOUT;
    $criteria                    = new CriteriaCompo(new Criteria('read_msg', 0));
    $criteria->add(new Criteria('to_userid', $xoopsUser->getVar('uid')));

    $pm_handler = xoops_getHandler('privmessage');

    $xoopsPreload = XoopsPreload::getInstance();
    $xoopsPreload->triggerEvent('system.blocks.system_blocks.usershow', [&$pm_handler]);

    $block['user_avatar']    = $xoopsUser->getVar('user_avatar');
    $block['uname']          = $xoopsUser->getVar('uname');
    $block['new_messages']   = $pm_handler->getCount($criteria);
    $block['lang_inbox']     = _MB_SYSTEM_INBOX;
    $block['lang_adminmenu'] = _MB_SYSTEM_ADMENU;

    return $block;
}


function checkPendingContent($module, $table, $condition, $adminlink, $lang_linkname, &$block, $xoopsDB) {
    $module_handler = xoops_getHandler('module');
    if (xoops_isActiveModule($module) && $module_handler->getCount(new Criteria('dirname', $module))) {
        $sql = "SELECT COUNT(*) FROM " . $xoopsDB->prefix($table);
        if ('' !== $condition) {
            $sql .= " WHERE $condition";
        }
        $result = $xoopsDB->query($sql);
        if ($xoopsDB->isResultSet($result)) {
            [$count] = $xoopsDB->fetchRow($result);
            if ((int)$count > 0) {
                $block['modules'][] = [
                    'adminlink' => XOOPS_URL . $adminlink,
                    'pendingnum' => $count, // Use $count directly, no second fetch
                    'lang_linkname' => $lang_linkname,
                ];
            }
        }
    }
}

// this block is deprecated
/**
 * @return array
 */
function b_system_waiting_show()
{
    global $xoopsUser;
    $xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
    /** @var XoopsModuleHandler $module_handler */
    $module_handler = xoops_getHandler('module');
    $block = [];

    // waiting content for news
    checkPendingContent('news', 'news_stories', 'published=0',
        '/modules/news/admin/index.php?op=newarticle', _MB_SYSTEM_SUBMS, $block, $xoopsDB);

    // waiting content for mylinks
    checkPendingContent('mylinks', 'mylinks_links', 'status=0',
        '/modules/mylinks/admin/index.php?op=listNewLinks', _MB_SYSTEM_WLNKS, $block, $xoopsDB);

    checkPendingContent('mylinks', 'mylinks_broken', '',
        '/modules/mylinks/admin/index.php?op=listBrokenLinks', _MB_SYSTEM_BLNK, $block, $xoopsDB);

    checkPendingContent('mylinks', 'mylinks_mod', '',
        '/modules/mylinks/admin/index.php?op=listModReq', _MB_SYSTEM_MLNKS, $block, $xoopsDB);

    // waiting content for mydownloads
    if (xoops_isActiveModule('mydownloads') && $module_handler->getCount(new Criteria('dirname', 'mydownloads'))) {

        checkPendingContent('mydownloads', 'mydownloads_downloads', 'status=0',
            '/modules/mydownloads/admin/index.php?op=listNewDownloads', _MB_SYSTEM_WDLS, $block, $xoopsDB);

        checkPendingContent('mydownloads', 'mydownloads_broken', '',
            '/modules/mydownloads/admin/index.php?op=listBrokenDownloads', _MB_SYSTEM_BFLS, $block, $xoopsDB);

        checkPendingContent('mydownloads', 'mydownloads_mod', '',
            '/modules/mydownloads/admin/index.php?op=listModReq', _MB_SYSTEM_MFLS, $block, $xoopsDB);
    }

    // waiting content for xoops comments
    checkPendingContent('system', 'xoopscomments', 'com_status=1',
        '/modules/system/admin.php?module=0&amp;status=1&fct=comments', _MB_SYSTEM_COMPEND, $block, $xoopsDB);


    // waiting content for TDMDownloads
    checkPendingContent('tdmdownloads', 'tdmdownloads_downloads', 'status=0',
        '/modules/tdmdownloads/admin/downloads.php?op=list&statut_display=0', _MB_SYSTEM_TDMDOWNLOADS, $block, $xoopsDB);


    // waiting content for extgallery
    checkPendingContent('extgallery', 'extgallery_publicphoto', 'photo_approved=0',
        '/modules/extgallery/admin/photo.php#pending-photo', _MB_SYSTEM_EXTGALLERY, $block, $xoopsDB);


    // waiting content for smartsection
    checkPendingContent('smartsection', 'smartsection_items', 'status=1',
        '/modules/smartsection/admin/item.php', _MB_SYSTEM_SMARTSECTION, $block, $xoopsDB);


    if (count($block) > 0) {
        $GLOBALS['xoopsLogger']->addDeprecated(_MB_SYSTEM_WAITING_CONTENT_DEPRECATED);
    }
    return $block;
}

/**
 * @param $options
 *
 * @return array
 */
function b_system_info_show($options)
{
    global $xoopsConfig, $xoopsUser;
    $xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
    $myts    = \MyTextSanitizer::getInstance();
    $block   = [];
    if (!empty($options[3])) {
        $block['showgroups'] = true;
        $sql = 'SELECT u.uid, u.uname, u.email, u.user_viewemail, u.user_avatar, g.name AS groupname FROM ' . $xoopsDB->prefix('groups_users_link') . ' l LEFT JOIN ' . $xoopsDB->prefix('users') . ' u ON l.uid=u.uid LEFT JOIN ' . $xoopsDB->prefix('groups') . " g ON l.groupid=g.groupid WHERE g.group_type='Admin' ORDER BY l.groupid, u.uid";
        $result = $xoopsDB->query($sql);
        if ($xoopsDB->isResultSet($result) && $xoopsDB->getRowsNum($result) > 0) {
            $prev_caption = '';
            $i            = 0;
            while (false !== ($userinfo = $xoopsDB->fetchArray($result))) {
                if ($prev_caption != $userinfo['groupname']) {
                    $prev_caption                = $userinfo['groupname'];
                    $block['groups'][$i]['name'] = $myts->htmlSpecialChars($userinfo['groupname']);
                }
                if (isset($xoopsUser) && is_object($xoopsUser)) {
                    $block['groups'][$i]['users'][] = [
                        'id'      => $userinfo['uid'],
                        'name'    => $myts->htmlSpecialChars($userinfo['uname']),
                        'msglink' => "<a href=\"javascript:openWithSelfMain('" . XOOPS_URL . '/pmlite.php?send2=1&amp;to_userid=' . $userinfo['uid'] . "','pmlite',565,500);\"><img src=\"" . XOOPS_URL . "/images/icons/pm_small.gif\" border=\"0\" width=\"27\" height=\"17\" alt=\"\" /></a>",
                        'avatar'  => XOOPS_UPLOAD_URL . '/' . $userinfo['user_avatar'],
                    ];
                } else {
                    if ($userinfo['user_viewemail']) {
                        $block['groups'][$i]['users'][] = [
                            'id'      => $userinfo['uid'],
                            'name'    => $myts->htmlSpecialChars($userinfo['uname']),
                            'msglink' => '<a href="mailto:' . $userinfo['email'] . '"><img src="' . XOOPS_URL . '/images/icons/em_small.gif" border="0" width="16" height="14" alt="" /></a>',
                            'avatar'  => XOOPS_UPLOAD_URL . '/' . $userinfo['user_avatar'],
                        ];
                    } else {
                        $block['groups'][$i]['users'][] = [
                            'id'      => $userinfo['uid'],
                            'name'    => $myts->htmlSpecialChars($userinfo['uname']),
                            'msglink' => '&nbsp;',
                            'avatar'  => XOOPS_UPLOAD_URL . '/' . $userinfo['user_avatar'],
                        ];
                    }
                }
                ++$i;
            }
        }
    } else {
        $block['showgroups'] = false;
    }
    $block['logourl']       = XOOPS_URL . '/images/' . $options[2];
    $block['recommendlink'] = "<a href=\"javascript:openWithSelfMain('" . XOOPS_URL . '/misc.php?action=showpopups&amp;type=friend&amp;op=sendform&amp;t=' . time() . "','friend'," . $options[0] . ',' . $options[1] . ")\">" . _MB_SYSTEM_RECO . '</a>';

    return $block;
}

/**
 * @param $options
 *
 * @return array
 */
function b_system_newmembers_show($options)
{
    $block    = [];
    $criteria = new CriteriaCompo(new Criteria('level', 0, '>'));
    $limit    = (!empty($options[0])) ? $options[0] : 10;
    $criteria->setOrder('DESC');
    $criteria->setSort('user_regdate');
    $criteria->setLimit($limit);
    /** @var XoopsMemberHandler $member_handler */
    $member_handler = xoops_getHandler('member');
    $newmembers     = $member_handler->getUsers($criteria);
    $count          = count($newmembers);
    for ($i = 0; $i < $count; ++$i) {
        if ($options[1] == 1) {
            $block['users'][$i]['avatar'] = $newmembers[$i]->getVar('user_avatar') !== 'blank.gif' ? XOOPS_UPLOAD_URL . '/' . $newmembers[$i]->getVar('user_avatar') : '';
        } else {
            $block['users'][$i]['avatar'] = '';
        }
        $block['users'][$i]['id']       = $newmembers[$i]->getVar('uid');
        $block['users'][$i]['name']     = $newmembers[$i]->getVar('uname');
        $block['users'][$i]['joindate'] = formatTimestamp($newmembers[$i]->getVar('user_regdate'), 's');
    }

    return $block;
}

/**
 * @param $options
 *
 * @return array
 */
function b_system_topposters_show($options)
{
    $block    = [];
    $criteria = new CriteriaCompo(new Criteria('level', 0, '>'));
    $criteria->add(new Criteria('posts', 0, '>'));
    $limit    = (!empty($options[0])) ? $options[0] : 10;
    $size     = count($options);
    for ($i = 2; $i < $size; ++$i) {
        $criteria->add(new Criteria('rank', $options[$i], '<>'));
    }
    $criteria->setOrder('DESC');
    $criteria->setSort('posts');
    $criteria->setLimit($limit);
    /** @var XoopsMemberHandler $member_handler */
    $member_handler = xoops_getHandler('member');
    $topposters     = $member_handler->getUsers($criteria);
    $count          = count($topposters);
    for ($i = 0; $i < $count; ++$i) {
        $block['users'][$i]['rank'] = $i + 1;
        if ($options[1] == 1) {
            $block['users'][$i]['avatar'] = $topposters[$i]->getVar('user_avatar') !== 'blank.gif' ? XOOPS_UPLOAD_URL . '/' . $topposters[$i]->getVar('user_avatar') : '';
        } else {
            $block['users'][$i]['avatar'] = '';
        }
        $block['users'][$i]['id']    = $topposters[$i]->getVar('uid');
        $block['users'][$i]['name']  = $topposters[$i]->getVar('uname');
        $block['users'][$i]['posts'] = $topposters[$i]->getVar('posts');
    }

    return $block;
}

/**
 * @param $options
 *
 * @return array
 */
function b_system_comments_show($options)
{
    $block = [];
    include_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
    $comment_handler = xoops_getHandler('comment');
    $criteria        = new CriteriaCompo(new Criteria('com_status', XOOPS_COMMENT_ACTIVE));
    $criteria->setLimit((int)$options[0]);
    $criteria->setSort('com_created');
    $criteria->setOrder('DESC');

    // Check modules permissions
    global $xoopsUser;
    $moduleperm_handler = xoops_getHandler('groupperm');
    $gperm_groupid      = is_object($xoopsUser) ? $xoopsUser->getGroups() : [XOOPS_GROUP_ANONYMOUS];
    $criteria1          = new CriteriaCompo(new Criteria('gperm_name', 'module_read', '='));
    $criteria1->add(new Criteria('gperm_groupid', '(' . implode(',', $gperm_groupid) . ')', 'IN'));
    $perms  = $moduleperm_handler->getObjects($criteria1, true);
    $modIds = [];
    foreach ($perms as $item) {
        $modIds[] = $item->getVar('gperm_itemid');
    }
    if (count($modIds) > 0) {
        $modIds = array_unique($modIds);
        $criteria->add(new Criteria('com_modid', '(' . implode(',', $modIds) . ')', 'IN'));
    }
    // Check modules permissions

    $comments       = $comment_handler->getObjects($criteria, true);
    /** @var XoopsMemberHandler $member_handler */
    $member_handler = xoops_getHandler('member');
    /** @var XoopsModuleHandler $module_handler */
    $module_handler = xoops_getHandler('module');
    $modules        = $module_handler->getObjects(new Criteria('hascomments', 1), true);
    $comment_config = [];
    foreach (array_keys($comments) as $i) {
        $mid           = $comments[$i]->getVar('com_modid');
        $com['module'] = '<a href="' . XOOPS_URL . '/modules/' . $modules[$mid]->getVar('dirname') . '/">' . $modules[$mid]->getVar('name') . '</a>';
        if (!isset($comment_config[$mid])) {
            $comment_config[$mid] = $modules[$mid]->getInfo('comments');
        }
        $com['id']    = $i;
        $com['title'] = '<a href="' . XOOPS_URL . '/modules/' . $modules[$mid]->getVar('dirname') . '/' . $comment_config[$mid]['pageName'] . '?' . $comment_config[$mid]['itemName'] . '=' . $comments[$i]->getVar('com_itemid') . '&amp;com_id=' . $i . '&amp;com_rootid=' . $comments[$i]->getVar('com_rootid') . '&amp;' . htmlspecialchars((string) $comments[$i]->getVar('com_exparams'), ENT_QUOTES | ENT_HTML5) . '#comment' . $i . '">' . $comments[$i]->getVar('com_title') . '</a>';
        $com['icon']  = htmlspecialchars((string) $comments[$i]->getVar('com_icon'), ENT_QUOTES | ENT_HTML5);
        $com['icon']  = ($com['icon'] != '') ? $com['icon'] : 'icon1.gif';
        $com['time']  = formatTimestamp($comments[$i]->getVar('com_created'), 'm');
        if ($comments[$i]->getVar('com_uid') > 0) {
            $poster = $member_handler->getUser($comments[$i]->getVar('com_uid'));
            if (is_object($poster)) {
                $com['poster'] = '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $comments[$i]->getVar('com_uid') . '">' . $poster->getVar('uname') . '</a>';
            } else {
                $com['poster'] = $GLOBALS['xoopsConfig']['anonymous'];
            }
        } else {
            $com['poster'] = $GLOBALS['xoopsConfig']['anonymous'];
        }
        $block['comments'][] =& $com;
        unset($com);
    }

    return $block;
}

// RMV-NOTIFY
/**
 * @return array|bool
 */
function b_system_notification_show()
{
    global $xoopsConfig, $xoopsUser, $xoopsModule;
    include_once XOOPS_ROOT_PATH . '/include/notification_functions.php';
    include_once XOOPS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/notification.php';
    // Notification must be enabled, and user must be logged in
    if (empty($xoopsUser) || !notificationEnabled('block')) {
        return false; // do not display block
    }
    $notification_handler = xoops_getHandler('notification');
    // Now build the nested associative array of info to pass
    // to the block template.
    $block      = [];
    $categories =& notificationSubscribableCategoryInfo();
    if (empty($categories)) {
        return false;
    }
    foreach ($categories as $category) {
        $section['name']        = $category['name'];
        $section['title']       = $category['title'];
        $section['description'] = $category['description'];
        $section['itemid']      = $category['item_id'];
        $section['events']      = [];
        $subscribed_events      = $notification_handler->getSubscribedEvents($category['name'], $category['item_id'], $xoopsModule->getVar('mid'), $xoopsUser->getVar('uid'));
        foreach (notificationEvents($category['name'], true) as $event) {
            if (!empty($event['admin_only']) && !$xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
                continue;
            }
            $subscribed                        = in_array($event['name'], $subscribed_events) ? 1 : 0;
            $section['events'][$event['name']] = [
                'name'        => $event['name'],
                'title'       => $event['title'],
                'caption'     => $event['caption'],
                'description' => $event['description'],
                'subscribed'  => $subscribed,
            ];
        }
        $block['categories'][$category['name']] = $section;
    }
    // Additional form data
    $block['target_page'] = 'notification_update.php';
    // FIXME: better or more standardized way to do this?
    $script_url                  = explode('/', (string) $_SERVER['PHP_SELF']);
    $script_name                 = $script_url[count($script_url) - 1];
    $block['redirect_script']    = $script_name;
    $block['submit_button']      = _NOT_UPDATENOW;
    $block['notification_token'] = $GLOBALS['xoopsSecurity']->createToken();

    return $block;
}

/**
 * @param $options
 *
 * @return string
 */
function b_system_comments_edit($options)
{
    $inputtag = "<input type='text' name='options[]' value='" . (int)$options[0] . "' />";
    $form     = sprintf(_MB_SYSTEM_DISPLAYC, $inputtag);

    return $form;
}

/**
 * @param $options
 *
 * @return string
 */
function b_system_topposters_edit($options)
{
    include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
    $inputtag = "<input type='text' name='options[]' value='" . (int)$options[0] . "' />";
    $form     = sprintf(_MB_SYSTEM_DISPLAY, $inputtag);
    $form .= '<br>' . _MB_SYSTEM_DISPLAYA . "&nbsp;<input type='radio' id='options[]' name='options[]' value='1'";
    if ($options[1] == 1) {
        $form .= " checked";
    }
    $form .= ' />&nbsp;' . _YES . "<input type='radio' id='options[]' name='options[]' value='0'";
    if ($options[1] == 0) {
        $form .= " checked";
    }
    $form .= ' />&nbsp;' . _NO . '';
    $form .= '<br>' . _MB_SYSTEM_NODISPGR . "<br><select id='options[]' name='options[]' multiple='multiple'>";
    $ranks = XoopsLists::getUserRankList();
    $size  = count($options);
    foreach ($ranks as $k => $v) {
        $sel = '';
        for ($i = 2; $i < $size; ++$i) {
            if ($k == $options[$i]) {
                $sel = " selected";
            }
        }
        $form .= "<option value='$k'$sel>$v</option>";
    }
    $form .= '</select>';

    return $form;
}

/**
 * @param $options
 *
 * @return string
 */
function b_system_newmembers_edit($options)
{
    $inputtag = "<input type='text' name='options[]' value='" . $options[0] . "' />";
    $form     = sprintf(_MB_SYSTEM_DISPLAY, $inputtag);
    $form .= '<br>' . _MB_SYSTEM_DISPLAYA . "&nbsp;<input type='radio' id='options[]' name='options[]' value='1'";
    if ($options[1] == 1) {
        $form .= " checked";
    }
    $form .= ' />&nbsp;' . _YES . "<input type='radio' id='options[]' name='options[]' value='0'";
    if ($options[1] == 0) {
        $form .= " checked";
    }
    $form .= ' />&nbsp;' . _NO . '';

    return $form;
}

/**
 * @param $options
 *
 * @return string
 */
function b_system_info_edit($options)
{
    $form = _MB_SYSTEM_PWWIDTH . '&nbsp;';
    $form .= "<input type='text' name='options[]' value='" . $options[0] . "' />";
    $form .= '<br>' . _MB_SYSTEM_PWHEIGHT . '&nbsp;';
    $form .= "<input type='text' name='options[]' value='" . $options[1] . "' />";
    $form .= '<br>' . sprintf(_MB_SYSTEM_LOGO, XOOPS_URL . '/images/') . '&nbsp;';
    $form .= "<input type='text' name='options[]' value='" . $options[2] . "' />";
    $chk = '';
    $form .= '<br>' . _MB_SYSTEM_SADMIN . '&nbsp;';
    if ($options[3] == 1) {
        $chk = " checked";
    }
    $form .= "<input type='radio' name='options[3]' value='1'" . $chk . ' />&nbsp;' . _YES . '';
    $chk = '';
    if ($options[3] == 0) {
        $chk = " checked";
    }
    $form .= "&nbsp;<input type='radio' name='options[3]' value='0'" . $chk . ' />' . _NO . '';

    return $form;
}

/**
 * @param $options
 *
 * @return array
 */
function b_system_themes_show($options)
{
    global $xoopsConfig;
    $block = [];

    if (!isset($options[2])) {
        $options[2] = 3; // this was the fixed value pre 2.5.8
    }
    $selectSize = ($options[0] == 1) ? 1 : (int) $options[2];
    $select = new XoopsFormSelect('', 'xoops_theme_select', $xoopsConfig['theme_set'], $selectSize);
    foreach ($xoopsConfig['theme_set_allowed'] as $theme) {
        $select->addOption($theme, $theme);
    }

    if ($options[0] == 1) {
        $themeSelect = '<img vspace="2" id="xoops_theme_img" src="'
            . XOOPS_THEME_URL . '/' . $xoopsConfig['theme_set'] . '/shot.gif" '
            . ' alt="screenshot" width="' . (int)$options[1] . '" />'
            . '<br>';
        $select->setExtra(' onchange="showImgSelected(\'xoops_theme_img\', \'xoops_theme_select\', \'themes\', \'/shot.gif\', '
            .  '\'' . XOOPS_URL . '\');" ');
        $selectTray = new XoopsFormElementTray('');
        $selectTray->addElement($select);
        $selectTray->addElement(new XoopsFormButton('', 'submit', _GO, 'submit'));
        $themeSelect .= '<div class="form-inline">';
        $themeSelect .= $selectTray->render();
        $themeSelect .= '</div>';
    } else {
        $select->setExtra(' onchange="submit();" ');
        $themeSelect = $select->render();
    }

    $block['theme_select'] = $themeSelect . '<br>(' . sprintf(_MB_SYSTEM_NUMTHEME, '<strong>'
            . count($xoopsConfig['theme_set_allowed']) . '</strong>') . ')<br>';

    return $block;
}

/**
 * @param $options
 *
 * @return string
 */
function b_system_themes_edit($options)
{
    $chk  = '';
    $form = _MB_SYSTEM_THSHOW . '&nbsp;';
    if (!isset($options[2])) {
        $options[2] = 3; // this was the fixed value pre 2.5.8
    }
    if ($options[0] == 1) {
        $chk = " checked";
    }
    $form .= "<input type='radio' name='options[0]' value='1'" . $chk . ' />&nbsp;' . _YES;
    $chk = '';
    if ($options[0] == 0) {
        $chk = ' checked';
    }
    $form .= '&nbsp;<input type="radio" name="options[0]" value="0"' . $chk . ' />' . _NO;
    $form .= '<br>' . _MB_SYSTEM_THWIDTH . '&nbsp;';
    $form .= "<input type='text' name='options[1]' value='" . $options[1] . "' />";
    $form .= '<br>' . _MB_SYSTEM_BLOCK_HEIGHT . '&nbsp;';
    $form .= "<input type='text' name='options[2]' value='" . $options[2] . "' />";

    return $form;
}
