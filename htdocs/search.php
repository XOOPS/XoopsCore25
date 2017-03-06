<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * XOOPS global search
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             core
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu)
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @todo                Modularize; Both search algorithms and interface will be redesigned
 */
include __DIR__ . '/mainfile.php';

xoops_loadLanguage('search');
/* @var $config_handler XoopsConfigHandler  */
$config_handler    = xoops_getHandler('config');
$xoopsConfigSearch = $config_handler->getConfigsByCat(XOOPS_CONF_SEARCH);

if ($xoopsConfigSearch['enable_search'] != 1) {
    header('Location: ' . XOOPS_URL . '/index.php');
    exit();
}
$action = 'search';
if (!empty($_GET['action'])) {
    $action = trim(strip_tags($_GET['action']));
} elseif (!empty($_POST['action'])) {
    $action = trim(strip_tags($_POST['action']));
}
$query = '';
if (!empty($_GET['query'])) {
    $query = trim(strip_tags($_GET['query']));
} elseif (!empty($_POST['query'])) {
    $query = trim(strip_tags($_POST['query']));
}
$andor = 'AND';
if (!empty($_GET['andor'])) {
    $andor = trim(strip_tags($_GET['andor']));
} elseif (!empty($_POST['andor'])) {
    $andor = trim(strip_tags($_POST['andor']));
}
$mid = $uid = $start = 0;
if (!empty($_GET['mid'])) {
    $mid = (int)$_GET['mid'];
} elseif (!empty($_POST['mid'])) {
    $mid = (int)$_POST['mid'];
}
if (!empty($_GET['uid'])) {
    $uid = (int)$_GET['uid'];
} elseif (!empty($_POST['uid'])) {
    $uid = (int)$_POST['uid'];
}
if (!empty($_GET['start'])) {
    $start = (int)$_GET['start'];
} elseif (!empty($_POST['start'])) {
    $start = (int)$_POST['start'];
}

$queries = array();

if ($action === 'results') {
    if ($query == '') {
        redirect_header('search.php', 1, _SR_PLZENTER);
    }
} elseif ($action === 'showall') {
    if ($query == '' || empty($mid)) {
        redirect_header('search.php', 1, _SR_PLZENTER);
    }
} elseif ($action === 'showallbyuser') {
    if (empty($mid) || empty($uid)) {
        redirect_header('search.php', 1, _SR_PLZENTER);
    }
}
$GLOBALS['xoopsOption']['template_main'] = 'system_search.tpl';
$groups            = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
/* @var  $gperm_handler XoopsGroupPermHandler */
$gperm_handler     = xoops_getHandler('groupperm');
$available_modules = $gperm_handler->getItemIds('module_read', $groups);
if ($action === 'search') {
    include $GLOBALS['xoops']->path('header.php');
    include $GLOBALS['xoops']->path('include/searchform.php');
	$xoopsTpl->assign('form', $search_form->render());
    include $GLOBALS['xoops']->path('footer.php');
    exit();
}
if ($andor !== 'OR' && $andor !== 'exact' && $andor !== 'AND') {
    $andor = 'AND';
}

$myts = MyTextSanitizer::getInstance();
if ($action !== 'showallbyuser') {
    if ($andor !== 'exact') {
        $ignored_queries = array(); // holds kewords that are shorter than allowed minmum length
        $temp_queries    = preg_split('/[\s,]+/', $query);
        foreach ($temp_queries as $q) {
            $q = trim($q);
            if (strlen($q) >= $xoopsConfigSearch['keyword_min']) {
                $queries[] = $myts->addSlashes($q);
            } else {
                $ignored_queries[] = $myts->addSlashes($q);
            }
        }
        if (count($queries) == 0) {
            redirect_header('search.php', 2, sprintf(_SR_KEYTOOSHORT, $xoopsConfigSearch['keyword_min']));
        }
    } else {
        $query = trim($query);
        if (strlen($query) < $xoopsConfigSearch['keyword_min']) {
            redirect_header('search.php', 2, sprintf(_SR_KEYTOOSHORT, $xoopsConfigSearch['keyword_min']));
        }
        $queries = array($myts->addSlashes($query));
    }
}
switch ($action) {
    case 'results':
        /* @var $module_handler XoopsModuleHandler  */
        $module_handler = xoops_getHandler('module');
        $criteria       = new CriteriaCompo(new Criteria('hassearch', 1));
        $criteria->add(new Criteria('isactive', 1));
        $criteria->add(new Criteria('mid', '(' . implode(',', $available_modules) . ')', 'IN'));
        $modules = $module_handler->getObjects($criteria, true);
        $mids    = isset($_REQUEST['mids']) ? $_REQUEST['mids'] : array();
        if (empty($mids) || !is_array($mids)) {
            unset($mids);
            $mids = array_keys($modules);
        }
        $xoopsOption['xoops_pagetitle'] = _SR_SEARCHRESULTS . ': ' . implode(' ', $queries);
        include $GLOBALS['xoops']->path('header.php');
		$xoopsTpl->assign('results', true);
        $nomatch = true;
		$keywords = '';
		$error_length = '';
		$error_keywords = '';
        if ($andor !== 'exact') {
            foreach ($queries as $q) {
				$keywords .= htmlspecialchars(stripslashes($q)) . ' ';
            }
            if (!empty($ignored_queries)) {
				$error_length = sprintf(_SR_IGNOREDWORDS, $xoopsConfigSearch['keyword_min']);
                foreach ($ignored_queries as $q) {
					$error_keywords .= htmlspecialchars(stripslashes($q)) . ' ';
                }
            }
        } else {
			$keywords .= '"' . htmlspecialchars(stripslashes($queries[0])) . '"';
        }
		$xoopsTpl->assign('keywords', $keywords);
		$xoopsTpl->assign('error_length', $error_length);
		$xoopsTpl->assign('error_keywords', $error_keywords);
		$results_arr = array();
        foreach ($mids as $mid) {
            $mid = (int)$mid;			
            if (in_array($mid, $available_modules)) {
                $module  = $modules[$mid];
                $results = $module->search($queries, $andor, 5, 0);
                $count   = count($results);
                if (is_array($results) && $count > 0) {
                    $nomatch = false;
					$module_name = $module->getVar('name');					
                    for ($i = 0; $i < $count; ++$i) {
                        if (isset($results[$i]['image']) && $results[$i]['image'] != '') {
							$results_arr[$i]['image_link'] = 'modules/' . $module->getVar('dirname') . '/' . $results[$i]['image'];
                        } else {
							$results_arr[$i]['image_link'] = 'images/icons/posticon2.gif';							
                        }
						$results_arr[$i]['image_title'] = $module->getVar('name');
                        if (!preg_match("/^http[s]*:\/\//i", $results[$i]['link'])) {
                            $results[$i]['link'] = 'modules/' . $module->getVar('dirname') . '/' . $results[$i]['link']; 
                        }
						$results_arr[$i]['link'] = $results[$i]['link'];
						$results_arr[$i]['link_title'] = $myts->htmlspecialchars($results[$i]['title']);
						
                        $results[$i]['uid'] = @(int)$results[$i]['uid'];
                        if (!empty($results[$i]['uid'])) {
                            $uname = XoopsUser::getUnameFromId($results[$i]['uid']);
							$results_arr[$i]['uname'] = $uname;
							$results_arr[$i]['uname_link'] = XOOPS_URL . '/userinfo.php?uid=' . $results[$i]['uid'];
                        }
						if (!empty($results[$i]['time'])){
							$results_arr[$i]['time'] = formatTimestamp((int)$results[$i]['time']);
						}
                    }
                    if ($count >= 5) {
                        $search_url = XOOPS_URL . '/search.php?query=' . urlencode(stripslashes(implode(' ', $queries)));
                        $search_url .= "&mid={$mid}&action=showall&andor={$andor}";
						$search_arr['module_show_all'] = htmlspecialchars($search_url);
                    }
					$search_arr['module_name'] = $module_name;
					$search_arr['module_data'] = $results_arr;
					$xoopsTpl->append_by_ref('search', $search_arr);
					unset($results_arr, $search_arr);
                }
            }			
            unset($results, $module, $module_name);
        }
		
        if ($nomatch) {
			$xoopsTpl->assign('nomatch', _SR_NOMATCH);
        }
        include $GLOBALS['xoops']->path('include/searchform.php');
		$xoopsTpl->assign('form', $search_form->render());
        break;

    case 'showall':
    case 'showallbyuser':
        include $GLOBALS['xoops']->path('header.php');
		$xoopsTpl->assign('showallbyuser', true);
    /* @var $module_handler XoopsModuleHandler  */
		$module_handler = xoops_getHandler('module');
        $module         = $module_handler->get($mid);
        $results        = $module->search($queries, $andor, 20, $start, $uid);
        $count          = count($results);
        if (is_array($results) && $count > 0) {
            $next_results = $module->search($queries, $andor, 1, $start + 20, $uid);
            $next_count   = count($next_results);
            $has_next     = false;
            if (is_array($next_results) && $next_count == 1) {
                $has_next = true;
            }
            if ($action === 'showall') {
				$xoopsTpl->assign('showall', true);
				$keywords = '';
                if ($andor !== 'exact') {
                    foreach ($queries as $q) {
						$keywords .= htmlspecialchars(stripslashes($q));
                    }
                } else {
					$keywords .= htmlspecialchars(stripslashes($queries[0]));
                }
				$xoopsTpl->assign('keywords', $keywords);
            }
			$xoopsTpl->assign('showing', sprintf(_SR_SHOWING, $start + 1, $start + $count));
			$xoopsTpl->assign('module_name', $module->getVar('name'));
			$results_arr = array();
            for ($i = 0; $i < $count; ++$i) {
                if (isset($results[$i]['image']) && $results[$i]['image'] != '') {
					$results_arr['image_link'] = 'modules/' . $module->getVar('dirname') . '/' . $results[$i]['image'];
                } else {
					$results_arr['image_link'] = 'images/icons/posticon2.gif';
                }
				$results_arr['image_title'] = $module->getVar('name');
                if (!preg_match("/^http[s]*:\/\//i", $results[$i]['link'])) {
                    $results[$i]['link'] = 'modules/' . $module->getVar('dirname') . '/' . $results[$i]['link'];
                }
				$results_arr['link'] = $results[$i]['link'];
				$results_arr['link_title'] = $myts->htmlspecialchars($results[$i]['title']);
                $results['uid'] = @(int)$results[$i]['uid'];
                if (!empty($results[$i]['uid'])) {
                    $uname = XoopsUser::getUnameFromId($results[$i]['uid']);
					$results_arr['uname'] = $uname;
					$results_arr['uname_link'] = XOOPS_URL . '/userinfo.php?uid=' . $results[$i]['uid'];
                }
				if (!empty($results[$i]['time'])){
					$results_arr['time'] = formatTimestamp((int)$results[$i]['time']);
				}
				$xoopsTpl->append_by_ref('results_arr', $results_arr);
				unset($results_arr);
            }
            $search_url = XOOPS_URL . '/search.php?query=' . urlencode(stripslashes(implode(' ', $queries)));
            $search_url .= "&mid={$mid}&action={$action}&andor={$andor}";
            if ($action === 'showallbyuser') {
                $search_url .= "&uid={$uid}";
            }
            if ($start > 0) {
                $prev = $start - 20;				
                $search_url_prev = $search_url . "&start={$prev}";
				$xoopsTpl->assign('previous', htmlspecialchars($search_url_prev));
            }
            if (false !== $has_next) {
                $next            = $start + 20;
                $search_url_next = $search_url . "&start={$next}";
				$xoopsTpl->assign('next', htmlspecialchars($search_url_next));
            }
        } else {
			$xoopsTpl->assign('nomatch', true);
        }
        include $GLOBALS['xoops']->path('include/searchform.php');
        $search_form->display();
        break;
}
include $GLOBALS['xoops']->path('footer.php');
