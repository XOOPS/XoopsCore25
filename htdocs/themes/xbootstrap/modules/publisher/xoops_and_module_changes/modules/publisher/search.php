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
 * @copyright           The XUUPS Project http://sourceforge.net/projects/xuups/
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             Publisher
 * @subpackage          Action
 * @since               1.0
 * @author              trabis <lusopoemas@gmail.com>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

include_once __DIR__ . '/header.php';
xoops_loadLanguage('search');

//Checking general permissions
$config_handler    = xoops_getHandler('config');
$xoopsConfigSearch = $config_handler->getConfigsByCat(XOOPS_CONF_SEARCH);
if (empty($xoopsConfigSearch['enable_search'])) {
    redirect_header(PUBLISHER_URL . '/index.php', 2, _NOPERM);
}

$groups        = $xoopsUser ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
$gperm_handler = xoops_getModuleHandler('groupperm', PUBLISHER_DIRNAME);
$module_id     = $publisher->getModule()->mid();

//Checking permissions
if (!$publisher->getConfig('perm_search') || !$gperm_handler->checkRight('global', _PUBLISHER_SEARCH, $groups, $module_id)) {
    redirect_header(PUBLISHER_URL, 2, _NOPERM);
}

$xoopsConfig['module_cache'][$module_id] = 0;
$xoopsOption['template_main']            = 'publisher_search.tpl';
include XOOPS_ROOT_PATH . '/header.php';

$module_info_search = $publisher->getModule()->getInfo('search');
include_once PUBLISHER_ROOT_PATH . '/' . $module_info_search['file'];

$limit    = 10; //$publisher->getConfig('idxcat_perpage');
$uid      = 0;
$queries  = array();
$andor    = PublisherRequest::getString('andor');
$start    = PublisherRequest::getInt('start');
$category = PublisherRequest::getArray('category');
$username = PublisherRequest::getString('uname');
$searchin = PublisherRequest::getArray('searchin');
$sortby   = PublisherRequest::getString('sortby');
$term     = PublisherRequest::getString('term');

if (empty($category) || (is_array($category) && in_array('all', $category))) {
    $category = array();
} else {
    $category = !is_array($category) ? explode(',', $category) : $category;
    $category = array_map('intval', $category);
}

$andor  = in_array(strtoupper($andor), array('OR', 'AND', 'EXACT')) ? strtoupper($andor) : 'OR';
$sortby = in_array(strtolower($sortby), array('itemid', 'datesub', 'title', 'categoryid')) ? strtolower($sortby) : 'itemid';

if (!(empty($_POST['submit']) && empty($term))) {
    $next_search['category'] = implode(',', $category);
    $next_search['andor']    = $andor;
    $next_search['term']     = $term;
    $query                   = trim($term);

    if ($andor !== 'EXACT') {
        $ignored_queries = array(); // holds kewords that are shorter than allowed minmum length
        $temp_queries    = preg_split("/[\s,]+/", $query);
        foreach ($temp_queries as $q) {
            $q = trim($q);
            if (strlen($q) >= $xoopsConfigSearch['keyword_min']) {
                $queries[] = $myts->addSlashes($q);
            } else {
                $ignored_queries[] = $myts->addSlashes($q);
            }
        }
        if (count($queries) == 0) {
            redirect_header(PUBLISHER_URL . '/search.php', 2, sprintf(_SR_KEYTOOSHORT, $xoopsConfigSearch['keyword_min']));
        }
    } else {
        if (strlen($query) < $xoopsConfigSearch['keyword_min']) {
            redirect_header(PUBLISHER_URL . '/search.php', 2, sprintf(_SR_KEYTOOSHORT, $xoopsConfigSearch['keyword_min']));
        }
        $queries = array($myts->addSlashes($query));
    }

    $uname_required       = false;
    $search_username      = trim($username);
    $next_search['uname'] = $search_username;
    if (!empty($search_username)) {
        $uname_required  = true;
        $search_username = $myts->addSlashes($search_username);
        if (!$result = $xoopsDB->query('SELECT uid FROM ' . $xoopsDB->prefix('users') . ' WHERE uname LIKE ' . $xoopsDB->quoteString("%$search_username%"))) {
            redirect_header(PUBLISHER_URL . '/search.php', 1, _CO_PUBLISHER_ERROR);
        }
        $uid = array();
        while ($row = $xoopsDB->fetchArray($result)) {
            $uid[] = $row['uid'];
        }
    } else {
        $uid = 0;
    }

    $next_search['sortby']   = $sortby;
    $next_search['searchin'] = implode('|', $searchin);

    if (!empty($time)) {
        $extra = '';
    } else {
        $extra = '';
    }

    if ($uname_required && (!$uid || count($uid) < 1)) {
        $results = array();
    } else {
        $results = $module_info_search['func']($queries, $andor, $limit, $start, $uid, $category, $sortby, $searchin, $extra);
    }

    if (count($results) < 1) {
        $results[] = array('text' => _SR_NOMATCH);
    }

    $xoopsTpl->assign('results', $results);

    if (count($next_search) > 0) {
        $items = array();
        foreach ($next_search as $para => $val) {
            if (!empty($val)) {
                $items[] = "{$para}={$val}";
            }
        }
        if (count($items) > 0) {
            $paras = implode('&', $items);
        }
        unset($next_search, $items);
    }
    $search_url = PUBLISHER_URL . '/search.php?' . $paras;

    if (count($results)) {
        $next            = $start + $limit;
        $queries         = implode(',', $queries);
        $search_url_next = $search_url . "&start={$next}";
        $search_next     = "<a href=\"" . htmlspecialchars($search_url_next) . "\">" . _SR_NEXT . '</a>';
        $xoopsTpl->assign('search_next', $search_next);
    }
    if ($start > 0) {
        $prev            = $start - $limit;
        $search_url_prev = $search_url . "&start={$prev}";
        $search_prev     = "<a href=\"" . htmlspecialchars($search_url_prev) . "\">" . _SR_PREVIOUS . '</a>';
        $xoopsTpl->assign('search_prev', $search_prev);
    }

    unset($results);
    $search_info = _SR_KEYWORDS . ': ' . $myts->htmlSpecialChars($term);
    if ($uname_required) {
        if ($search_info) {
            $search_info .= '<br />';
        }
        $search_info .= _CO_PUBLISHER_UID . ': ' . $myts->htmlSpecialChars($search_username);
    }
    $xoopsTpl->assign('search_info', $search_info);
}

/* type */
$type_select = "<select name=\"andor\" class=\"form-control\">";
$type_select .= "<option value=\"OR\"";
if ('OR' === $andor) {
    $type_select .= " selected=\"selected\"";
}
$type_select .= '>' . _SR_ANY . '</option>';
$type_select .= "<option value=\"AND\"";
if ('AND' === $andor) {
    $type_select .= " selected=\"selected\"";
}
$type_select .= '>' . _SR_ALL . '</option>';
$type_select .= "<option value=\"EXACT\"";
if ('EXACT' === $andor) {
    $type_select .= " selected=\"selected\"";
}
$type_select .= '>' . _SR_EXACT . '</option>';
$type_select .= '</select>';

/* category */
$categories = $publisher->getHandler('category')->getCategoriesForSearch();

$select_category = "<select name=\"category[]\" size=\"5\" multiple=\"multiple\" class=\"form-control\">";
$select_category .= "<option value=\"all\"";
if (empty($category) || count($category) == 0) {
    $select_category .= "selected=\"selected\"";
}
$select_category .= '>' . _ALL . '</option>';
foreach ($categories as $id => $cat) {
    $select_category .= "<option value=\"" . $id . "\"";
    if (in_array($id, $category)) {
        $select_category .= "selected=\"selected\"";
    }
    $select_category .= '>' . $cat . '</option>';
}
$select_category .= '</select>';

/* scope */
$searchin_select = '';
$searchin_select .= "<label class=\"checkbox-inline\">
<input type=\"checkbox\" name=\"searchin[]\" value=\"title\"";
if (in_array('title', $searchin)) {
    $searchin_select .= ' checked';
}
$searchin_select .= ' />' . _CO_PUBLISHER_TITLE . '</label>';
$searchin_select .= "<label class=\"checkbox-inline\">
<input type=\"checkbox\" name=\"searchin[]\" value=\"subtitle\"";
if (in_array('subtitle', $searchin)) {
    $searchin_select .= ' checked';
}
$searchin_select .= ' />' . _CO_PUBLISHER_SUBTITLE . '</label>';
$searchin_select .= "<label class=\"checkbox-inline\">
<input type=\"checkbox\" name=\"searchin[]\" value=\"summary\"";
if (in_array('summary', $searchin)) {
    $searchin_select .= ' checked';
}
$searchin_select .= ' />' . _CO_PUBLISHER_SUMMARY . '</label>';
$searchin_select .= "<label class=\"checkbox-inline\">
<input type=\"checkbox\" name=\"searchin[]\" value=\"text\"";
if (in_array('body', $searchin)) {
    $searchin_select .= ' checked';
}
$searchin_select .= ' />' . _CO_PUBLISHER_BODY . '</label>';
$searchin_select .= "<label class=\"checkbox-inline\">
<input type=\"checkbox\" name=\"searchin[]\" value=\"keywords\"";
if (in_array('meta_keywords', $searchin)) {
    $searchin_select .= ' checked';
}
$searchin_select .= ' />' . _CO_PUBLISHER_ITEM_META_KEYWORDS . '</label>';
$searchin_select .= "<label class=\"checkbox-inline\">
<input type=\"checkbox\" name=\"searchin[]\" value=\"all\"";
if (in_array('all', $searchin) || empty($searchin)) {
    $searchin_select .= ' checked';
}
$searchin_select .= ' />' . _ALL . '</label>';

/* sortby */
$sortby_select = "<select name=\"sortby\" class=\"form-control\">";
$sortby_select .= "<option value=\"itemid\"";
if ('itemid' === $sortby || empty($sortby)) {
    $sortby_select .= " selected=\"selected\"";
}
$sortby_select .= '>' . _NONE . '</option>';
$sortby_select .= "<option value=\"datesub\"";
if ('datesub' === $sortby) {
    $sortby_select .= " selected=\"selected\"";
}
$sortby_select .= '>' . _CO_PUBLISHER_DATESUB . '</option>';
$sortby_select .= "<option value=\"title\"";
if ('title' === $sortby) {
    $sortby_select .= " selected=\"selected\"";
}
$sortby_select .= '>' . _CO_PUBLISHER_TITLE . '</option>';
$sortby_select .= "<option value=\"categoryid\"";
if ('categoryid' === $sortby) {
    $sortby_select .= " selected=\"selected\"";
}
$sortby_select .= '>' . _CO_PUBLISHER_CATEGORY . '</option>';
$sortby_select .= '</select>';

$xoopsTpl->assign('type_select', $type_select);
$xoopsTpl->assign('searchin_select', $searchin_select);
$xoopsTpl->assign('category_select', $select_category);
$xoopsTpl->assign('sortby_select', $sortby_select);
$xoopsTpl->assign('search_term', $term);
$xoopsTpl->assign('search_user', $username);

$xoopsTpl->assign('modulename', $publisher->getModule()->name());

if ($xoopsConfigSearch['keyword_min'] > 0) {
    $xoopsTpl->assign('search_rule', sprintf(_SR_KEYIGNORE, $xoopsConfigSearch['keyword_min']));
}

include XOOPS_ROOT_PATH . '/footer.php';
