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
 * @copyright      {@link http://xoops.org/ XOOPS Project}
 * @license        {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package
 * @since
 * @author         XOOPS Development Team
 */

// Check users rights
if (!is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->mid())) {
    exit(_NOPERM);
}
//  Check is active
if (!xoops_getModuleOption('active_banners', 'system')) {
    redirect_header('admin.php', 2, _AM_SYSTEM_NOTACTIVE);
}
// Parameters
$nb_aff = xoops_getModuleOption('banners_pager', 'system');
// Classes
/* @var  $banner_Handler SystemBannerHandler */
$banner_Handler        = xoops_getModuleHandler('banner', 'system');
/* @var  $banner_finish_Handler SystemBannerfinishHandler */
$banner_finish_Handler = xoops_getModuleHandler('bannerfinish', 'system');
/* @var  $banner_client_Handler SystemBannerclientHandler */
$banner_client_Handler = xoops_getModuleHandler('bannerclient', 'system');
// Get Action type
$op = system_CleanVars($_REQUEST, 'op', 'default', 'string');
// Define template
$GLOBALS['xoopsOption']['template_main'] = 'system_banners.tpl';
// Call header
xoops_cp_header();
// Define Stylesheet
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/admin.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/system/css/ui/' . xoops_getModuleOption('jquery_theme', 'system') . '/ui.all.css');
// Define scripts
$xoTheme->addScript('browse.php?Frameworks/jquery/jquery.js');
$xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.ui.js');
$xoTheme->addScript('browse.php?Frameworks/jquery/plugins/jquery.tablesorter.js');
$xoTheme->addScript('modules/system/js/admin.js');
// Define Breadcrumb and tips
$xoBreadCrumb->addLink(_AM_SYSTEM_BANNERS_NAV_MANAGER, system_adminVersion('banners', 'adminpath'));
switch ($op) {
    // Banners
    case 'banner_save': // Save banner
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=banners', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $bid = system_CleanVars($_POST, 'bid', 0, 'int');
        /* @var  $obj SystemBanner */
    if ($bid > 0) {
            $obj = $banner_Handler->get($bid);
        } else {
            $obj = $banner_Handler->create();
            $obj->setVar('date', time());
        }
        $obj->setVars($_POST);
        $verif_htmlbanner = system_CleanVars($_POST, 'htmlbanner', 0, 'int');
        $obj->setVar('htmlbanner', $verif_htmlbanner);

        if ($banner_Handler->insert($obj)) {
            redirect_header('admin.php?fct=banners', 2, _AM_SYSTEM_BANNERS_DBUPDATED);
        }
        xoops_error($obj->getHtmlErrors());
        $form = $obj->getForm(false);
        $form->display();
        break;

    case 'banner_edit': // Edit banner
        $xoBreadCrumb->addLink(_AM_SYSTEM_BANNERS_NAV_EDITBNR);
        $xoBreadCrumb->addHelp(system_adminVersion('banners', 'help') . '#banner_edit');
        $xoBreadCrumb->addTips(_AM_SYSTEM_BANNERS_NAV_TIPS);
        $xoBreadCrumb->render();

        $bid = system_CleanVars($_REQUEST, 'bid', 0, 'int');
        if ($bid > 0) {
            /* @var  $obj SystemBanner */
            $obj  = $banner_Handler->get($bid);
            /* @var  $form XoopsThemeForm */
            $form = $obj->getForm();
            $form->display();
        } else {
            redirect_header('admin.php?fct=banners', 1, _AM_SYSTEM_DBERROR);
        }
        break;

    case 'banner_delete': // Delete banner
        $xoBreadCrumb->addLink(_AM_SYSTEM_BANNERS_NAV_DELETEBNR);
        $xoBreadCrumb->addHelp(system_adminVersion('banners', 'help') . '#banner_delete');
        $xoBreadCrumb->render();

        $bid = system_CleanVars($_REQUEST, 'bid', 0, 'int');
        if ($bid > 0) {
            $obj = $banner_Handler->get($bid);
            if (isset($_POST['ok']) && $_POST['ok'] == 1) {
                if (!$GLOBALS['xoopsSecurity']->check()) {
                    redirect_header('admin.php?fct=banners', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
                }
                if ($banner_Handler->delete($obj)) {
                    redirect_header('admin.php?fct=banners', 3, _AM_SYSTEM_BANNERS_DELEBNR);
                } else {
                    xoops_error($obj->getHtmlErrors());
                }
            } else {
                xoops_confirm(array(
                                  'ok' => 1,
                                  'bid' => $bid,
                                  'op' => 'banner_delete'), 'admin.php?fct=banners', sprintf(_AM_SYSTEM_BANNERS_SUREDELE));
            }
        } else {
            redirect_header('admin.php?fct=banners', 1, _AM_SYSTEM_DBERROR);
        }
        break;

    case 'banner_finish_delete': // Delete finish banner
        $xoBreadCrumb->addLink(_AM_SYSTEM_BANNERS_NAV_DELETEFINISHBNR);
        $xoBreadCrumb->addHelp(system_adminVersion('banners', 'help') . '#banner_finish_delete');
        $xoBreadCrumb->render();

        $bid = system_CleanVars($_REQUEST, 'bid', 0, 'int');
        if ($bid > 0) {
            $obj = $banner_finish_Handler->get($bid);
            if (isset($_POST['ok']) && $_POST['ok'] == 1) {
                if (!$GLOBALS['xoopsSecurity']->check()) {
                    redirect_header('admin.php?fct=banners', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
                }
                if ($banner_finish_Handler->delete($obj)) {
                    redirect_header('admin.php?fct=banners', 3, _AM_SYSTEM_BANNERS_DBUPDATED);
                } else {
                    xoops_error($obj->getHtmlErrors());
                }
            } else {
                xoops_confirm(array(
                                  'ok' => 1,
                                  'bid' => $bid,
                                  'op' => 'banner_finish_delete'), 'admin.php?fct=banners', sprintf(_AM_SYSTEM_BANNERS_SUREDELE));
            }
        } else {
            redirect_header('admin.php?fct=banners', 1, _AM_SYSTEM_DBERROR);
        }
        break;

    // Clients
    case 'banner_client_save': // Save client
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('admin.php?fct=banners', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        $cid = system_CleanVars($_REQUEST, 'cid', 0, 'int');
        /* @var  $obj SystemBanner */
        if ($cid > 0) {
            $obj = $banner_client_Handler->get($cid);
        } else {
            $obj = $banner_client_Handler->create();
        }
        $obj->setVars($_POST);

        if ($banner_client_Handler->insert($obj)) {
            redirect_header('admin.php?fct=banners', 2, _AM_SYSTEM_BANNERS_DBUPDATED);
        }

        xoops_error($obj->getHtmlErrors());
        /* @var  $form XoopsThemeForm */
        $form = $obj->getForm(false);
        $form->display();
        break;

    case 'banner_client_edit': // Edit client
        $xoBreadCrumb->addLink(_AM_SYSTEM_BANNERS_NAV_EDITADVCLI);
        $xoBreadCrumb->addHelp(system_adminVersion('banners', 'help') . '#banner_client_edit');
        $xoBreadCrumb->addTips(_AM_SYSTEM_BANNERS_NAV_TIPS);
        $xoBreadCrumb->render();

        $cid = system_CleanVars($_REQUEST, 'cid', 0, 'int');
        if ($cid > 0) {
            /* @var  $obj SystemBanner */
            $obj  = $banner_client_Handler->get($cid);
            $form = $obj->getForm();
            $xoopsTpl->assign('form', $form->render());
        } else {
            redirect_header('admin.php?fct=banners', 1, _AM_SYSTEM_DBERROR);
        }
        break;

    case 'banner_client_delete': // Delete Client
        $xoBreadCrumb->addLink(_AM_SYSTEM_BANNERS_NAV_DELETECLI);
        $xoBreadCrumb->addHelp(system_adminVersion('banners', 'help') . '#banner_client_delete');
        $xoBreadCrumb->render();

        $cid = system_CleanVars($_REQUEST, 'cid', 0, 'int');
        if ($cid > 0) {
            $obj = $banner_client_Handler->get($cid);
            if (isset($_POST['ok']) && $_POST['ok'] == 1) {
                if (!$GLOBALS['xoopsSecurity']->check()) {
                    redirect_header('admin.php?fct=banners', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
                }
                if ($banner_client_Handler->delete($obj)) {
                    // Delete client banners
                    $banner_Handler->deleteAll(new Criteria('cid', $cid));
                    $banner_finish_Handler->deleteAll(new Criteria('cid', $cid));

                    redirect_header('admin.php?fct=banners', 3, _AM_SYSTEM_BANNERS_DBUPDATED);
                } else {
                    xoops_error($obj->getHtmlErrors());
                }
            } else {
                xoops_confirm(array(
                                  'ok' => 1,
                                  'cid' => $cid,
                                  'op' => 'banner_client_delete'), 'admin.php?fct=banners', _AM_SYSTEM_BANNERS_SUREDELBNR);
            }
        } else {
            redirect_header('admin.php?fct=banners', 1, _AM_SYSTEM_DBERROR);
        }
        break;

    case 'new_banner': // Form: New Banner
        $xoBreadCrumb->addLink(_AM_SYSTEM_BANNERS_NAV_ADDBNR);
        $xoBreadCrumb->addHelp(system_adminVersion('banners', 'help') . '#new_banner');
        $xoBreadCrumb->addTips(_AM_SYSTEM_BANNERS_NAV_TIPS);
        $xoBreadCrumb->render();
        /* @var  $obj SystemBanner */
        $obj  = $banner_Handler->create();
        $form = $obj->getForm();
        $xoopsTpl->assign('form', $form->render());
        break;

    case 'new_client': // Form: New Client
        $xoBreadCrumb->addLink(_AM_SYSTEM_BANNERS_NAV_ADDNWCLI);
        $xoBreadCrumb->addHelp(system_adminVersion('banners', 'help') . '#new_client');
        $xoBreadCrumb->addTips(_AM_SYSTEM_BANNERS_NAV_TIPS);
        $xoBreadCrumb->render();
        /* @var  $obj SystemBanner */
        $obj  = $banner_client_Handler->create();
        $form = $obj->getForm();
        $xoopsTpl->assign('form', $form->render());
        break;

    default:
        $xoBreadCrumb->addHelp(system_adminVersion('banners', 'help'));
        $xoBreadCrumb->addTips(_AM_SYSTEM_BANNERS_NAV_TIPS);
        $xoBreadCrumb->render();

        // Get start pager
        $start  = system_CleanVars($_REQUEST, 'start', 0, 'int');
        $startF = system_CleanVars($_REQUEST, 'startF', 0, 'int');
        $startC = system_CleanVars($_REQUEST, 'startC', 0, 'int');
        // Display Banners
        // Criteria
        $criteria = new CriteriaCompo();
        $criteria->setSort('date');
        $criteria->setOrder('DESC');
        $criteria->setStart($start);
        $criteria->setLimit($nb_aff);

        $banner_count = $banner_Handler->getCount($criteria);
        $banner_arr   = $banner_Handler->getall($criteria);

        $xoopsTpl->assign('banner_count', $banner_count);

        if ($banner_count > 0) {
            foreach (array_keys($banner_arr) as $i) {
                $bid         = $banner_arr[$i]->getVar('bid');
                $imptotal    = $banner_arr[$i]->getVar('imptotal');
                $impmade     = $banner_arr[$i]->getVar('impmade');
                $imageurl    = $banner_arr[$i]->getVar('imageurl');
                $clicks      = $banner_arr[$i]->getVar('clicks');
                $htmlbanner  = $banner_arr[$i]->getVar('htmlbanner');
                $htmlcode    = $banner_arr[$i]->getVar('htmlcode');
                $name_client = $banner_client_Handler->get($banner_arr[$i]->getVar('cid'));
                if (is_object($name_client)) {
                    $name = $name_client->getVar('name');
                }

                if ($impmade == 0) {
                    $percent = 0;
                } else {
                    $percent = substr(100 * $clicks / $impmade, 0, 5);
                }
                if ($imptotal == 0) {
                    $left = '' . _AM_SYSTEM_BANNERS_UNLIMIT . '';
                } else {
                    $left = $imptotal - $impmade;
                }

                //Img
                $img = '';
                if ($htmlbanner) {
                    if ($htmlcode){
                        $img .= html_entity_decode($htmlcode);
                    } else {
                        $img .= ' <iframe src=' . $imageurl . ' border="0" scrolling="no" allowtransparency="true" width="480px" height="60px" style="border:0" alt=""> </iframe>';
                    }
                } else {
                    if (strtolower(substr($imageurl, strrpos($imageurl, '.'))) === '.swf') {
                        $img .= "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/ swflash.cab#version=6,0,40,0\" width=\"468\" height=\"60\">";
                        $img .= "<param name=movie value=\"$imageurl\">";
                        $img .= '<param name=quality value=high>';
                        $img .= "<embed src=\"$imageurl\" quality=high pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\"  type=\"application/x-shockwave-flash\" width=\"468\" height=\"60\">";
                        $img .= '</embed>';
                        $img .= '</object>';
                    } else {
                        $img .= "<img src='" . $imageurl . "' alt='' />";
                    }
                }

                $banner['bid']         = $bid;
                $banner['impmade']     = $impmade;
                $banner['clicks']      = $clicks;
                $banner['left']        = $left;
                $banner['percent']     = $percent;
                $banner['imageurl']    = $img;
                $banner['name']        = $name;
                $banner['edit_delete'] = '<img class="cursorpointer" onclick="display_dialog(' . $bid . ', true, false, \'slide\', \'slide\', 200, 520);" src="images/icons/view.png" alt="' . _AM_SYSTEM_BANNERS_VIEW . '" title="' . _AM_SYSTEM_BANNERS_VIEW . '" /><a href="admin.php?fct=banners&amp;op=banner_edit&amp;bid=' . $bid . '"><img src="./images/icons/edit.png" border="0" alt="' . _AM_SYSTEM_BANNERS_EDIT . '" title="' . _AM_SYSTEM_BANNERS_EDIT . '"></a><a href="admin.php?fct=banners&amp;op=banner_delete&amp;bid=' . $bid . '"><img src="./images/icons/delete.png" border="0" alt="' . _AM_SYSTEM_BANNERS_DELETE . '" title="' . _AM_SYSTEM_BANNERS_DELETE . '"></a>';

                $xoopsTpl->append_by_ref('banner', $banner);
                $xoopsTpl->append_by_ref('popup_banner', $banner);
                unset($banner);
            }
        }
        // Display Page Navigation
        if ($banner_count > $nb_aff) {
            $nav = new XoopsPageNav($banner_count, $nb_aff, $start, 'start', 'fct=banners&amp;startF=' . $startF . '&amp;startC=' . $startC);
            $xoopsTpl->assign('nav_menu_banner', $nav->renderNav(4));
        }
        // Display Finished Banners
        // Criteria
        $criteria = new CriteriaCompo();
        $criteria->setSort('bid');
        $criteria->setOrder('DESC');
        $criteria->setStart($startF);
        $criteria->setLimit($nb_aff);

        $banner_finish_count = $banner_finish_Handler->getCount($criteria);
        $banner_finish_arr   = $banner_finish_Handler->getall($criteria);

        $xoopsTpl->assign('banner_finish_count', $banner_finish_count);

        if ($banner_finish_count > 0) {
            foreach (array_keys($banner_finish_arr) as $i) {
                $bid = $banner_finish_arr[$i]->getVar('bid');
                //$imageurl = $banner_arr[$i]->getVar("imageurl");
                $impressions = $banner_finish_arr[$i]->getVar('impressions');
                $clicks      = $banner_finish_arr[$i]->getVar('clicks');
                if ($impressions != 0) {
                    $percent = substr(100 * $clicks / $impressions, 0, 5);
                }

                $banner_finish['bid']         = $bid;
                $banner_finish['impressions'] = $impressions;
                $banner_finish['clicks']      = $clicks;
                $banner_finish['left']        = $left;
                $banner_finish['percent']     = $percent;
                $banner_finish['datestart']   = formatTimestamp($banner_finish_arr[$i]->getVar('datestart'), 'm');
                $banner_finish['dateend']     = formatTimestamp($banner_finish_arr[$i]->getVar('dateend'), 'm');
                $name_client                  = $banner_client_Handler->get($banner_finish_arr[$i]->getVar('cid'));
                $banner_finish['name']        = $name_client->getVar('name');
                $banner_finish['edit_delete'] = '<img class="cursorpointer" onclick="display_dialog(' . $bid . '000, true, true, \'slide\', \'slide\', 200, 520);" src="images/icons/view.png" alt="' . _AM_SYSTEM_BANNERS_VIEW . '" title="' . _AM_SYSTEM_BANNERS_VIEW . '" /><a href="admin.php?fct=banners&amp;op=banner_finish_delete&amp;bid=' . $bid . '"><img src="./images/icons/delete.png" border="0" alt="' . _AM_SYSTEM_BANNERS_DELETE . '" title="' . _AM_SYSTEM_BANNERS_DELETE . '"></a>';

                $xoopsTpl->append_by_ref('banner_finish', $banner_finish);
                unset($banner_finish);
            }
        }
        // Display Page Navigation
        if ($banner_finish_count > $nb_aff) {
            $nav = new XoopsPageNav($banner_count, $nb_aff, $startF, 'startF', 'fct=banners&amp;start=' . $start . '&amp;startC=' . $startC);
            $xoopsTpl->assign('nav_menu_bannerF', $nav->renderNav(4));
        }
        // Display client
        $criteria = new CriteriaCompo();
        $criteria->setSort('cid');
        $criteria->setOrder('DESC');
        $criteria->setStart($startC);
        $criteria->setLimit($nb_aff);

        $banner_client_count = $banner_client_Handler->getCount($criteria);
        $banner_client_arr   = $banner_client_Handler->getall($criteria);

        $xoopsTpl->assign('banner_client_count', $banner_client_count);

        if ($banner_client_count > 0) {
            foreach (array_keys($banner_client_arr) as $i) {
                $cid = $banner_client_arr[$i]->getVar('cid');

                $criteria = new CriteriaCompo();
                $criteria->add(new Criteria('cid', $cid, '='));
                $banner_active                  = $banner_Handler->getCount($criteria);
                $banner_client['cid']           = $cid;
                $banner_client['banner_active'] = $banner_active;
                $banner_client['name']          = $banner_client_arr[$i]->getVar('name');
                $banner_client['contact']       = $banner_client_arr[$i]->getVar('contact');
                $banner_client['email']         = $banner_client_arr[$i]->getVar('email');
                $banner_client['edit_delete']   = '<a href="admin.php?fct=banners&amp;op=banner_client_edit&amp;cid=' . $cid . '"><img src="./images/icons/edit.png" border="0" alt="' . _AM_SYSTEM_BANNERS_EDIT . '" title="' . _AM_SYSTEM_BANNERS_EDIT . '"></a><a href="admin.php?fct=banners&amp;op=banner_client_delete&amp;cid=' . $cid . '"><img src="./images/icons/delete.png" border="0" alt="' . _AM_SYSTEM_BANNERS_DELETE . '" title="' . _AM_SYSTEM_BANNERS_DELETE . '"></a>';

                $xoopsTpl->append_by_ref('banner_client', $banner_client);
                unset($banner_client);
            }
        }
        // Display Page Navigation
        if ($banner_client_count > $nb_aff) {
            $nav = new XoopsPageNav($banner_count, $nb_aff, $startC, 'startC', 'fct=banners&amp;start=' . $start . '&amp;startF=' . $startF);
            $xoopsTpl->assign('nav_menu_client', $nav->renderNav(4));
        }
        break;
}
xoops_cp_footer();
