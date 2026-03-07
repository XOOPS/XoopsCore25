<?php
/**
 * Extended User Profile
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             profile
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

use Xmf\Request;

include __DIR__ . '/header.php';

$limit_default    = 20;
$op               = Request::getCmd('op', '', 'GET') ?: Request::getCmd('op', 'search', 'POST');
$groups           = $GLOBALS['xoopsUser'] ? $GLOBALS['xoopsUser']->getGroups() : [XOOPS_GROUP_ANONYMOUS];
$searchable_types = [
    'textbox',
    'select',
    'radio',
    'yesno',
    'date',
    'datetime',
    'timezone',
    'language',
];

switch ($op) {
    default:
    case 'search':
        $xoopsOption['cache_group']   = implode('', $groups);
        $GLOBALS['xoopsOption']['template_main'] = 'profile_search.tpl';
        include $GLOBALS['xoops']->path('header.php');
        $xoBreadcrumbs[] = ['title' => _SEARCH];
        $sortby_arr      = [];

        // Dynamic fields
        $profile_handler = xoops_getModuleHandler('profile');
        // Get fields
        $fields = $profile_handler->loadFields();
        // Get ids of fields that can be searched
        /** @var  XoopsGroupPermHandler $gperm_handler */
        $gperm_handler     = xoops_getHandler('groupperm');
        $searchable_fields = $gperm_handler->getItemIds('profile_search', $groups, $GLOBALS['xoopsModule']->getVar('mid'));

        include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');
        $searchform = new XoopsThemeForm('', 'searchform', 'search.php', 'post');

        $name_tray = new XoopsFormElementTray(_US_NICKNAME);
        $name_tray->addElement(new XoopsFormSelectMatchOption('', 'uname_match'));
        $name_tray->addElement(new XoopsFormText('', 'uname', 35, 255));
        $searchform->addElement($name_tray);

        $email_tray = new XoopsFormElementTray(_US_EMAIL);
        $email_tray->addElement(new XoopsFormSelectMatchOption('', 'email_match'));
        $email_tray->addElement(new XoopsFormText('', 'email', 35, 255));
        $searchform->addElement($email_tray);

        // add search groups , only for Webmasters
        if ($GLOBALS['xoopsUser'] && $GLOBALS['xoopsUser']->isAdmin()) {
            $group_tray = new XoopsFormElementTray(_US_GROUPS);
            $group_tray->addElement(new XoopsFormSelectGroup('', 'selgroups', null, false, 5, true));
            $searchform->addElement($group_tray);
        }

        foreach (array_keys($fields) as $i) {
            if (!in_array($fields[$i]->getVar('field_id'), $searchable_fields) || !in_array($fields[$i]->getVar('field_type'), $searchable_types)) {
                continue;
            }
            $sortby_arr[$i] = $fields[$i]->getVar('field_title');
            switch ($fields[$i]->getVar('field_type')) {
                case 'textbox':
                    if ($fields[$i]->getVar('field_valuetype') == XOBJ_DTYPE_INT) {
                        $searchform->addElement(new XoopsFormText(sprintf(_PROFILE_MA_LARGERTHAN, $fields[$i]->getVar('field_title')), $fields[$i]->getVar('field_name') . '_larger', 35, 35));
                        $searchform->addElement(new XoopsFormText(sprintf(_PROFILE_MA_SMALLERTHAN, $fields[$i]->getVar('field_title')), $fields[$i]->getVar('field_name') . '_smaller', 35, 35));
                    } else {
                        $tray = new XoopsFormElementTray($fields[$i]->getVar('field_title'));
                        $tray->addElement(new XoopsFormSelectMatchOption('', $fields[$i]->getVar('field_name') . '_match'));
                        $tray->addElement(new XoopsFormText('', $fields[$i]->getVar('field_name'), 35, $fields[$i]->getVar('field_maxlength')));
                        $searchform->addElement($tray);
                        unset($tray);
                    }
                    break;

                case 'radio':
                case 'select':
                    $options = $fields[$i]->getVar('field_options');
                    $size    = min(count($options), 10);
                    $element = new XoopsFormSelect($fields[$i]->getVar('field_title'), $fields[$i]->getVar('field_name'), null, $size, true);
                    asort($options);
                    $element->addOptionArray($options);
                    $searchform->addElement($element);
                    unset($element);
                    break;

                case 'yesno':
                    $element = new XoopsFormSelect($fields[$i]->getVar('field_title'), $fields[$i]->getVar('field_name'), null, 2, true);
                    $element->addOption(1, _YES);
                    $element->addOption(0, _NO);
                    $searchform->addElement($element);
                    unset($element);
                    break;

                case 'date':
                case 'datetime':
                    $searchform->addElement(new XoopsFormTextDateSelect(sprintf(_PROFILE_MA_LATERTHAN, $fields[$i]->getVar('field_title')), $fields[$i]->getVar('field_name') . '_larger', 15, 1));
                    $searchform->addElement(new XoopsFormTextDateSelect(sprintf(_PROFILE_MA_EARLIERTHAN, $fields[$i]->getVar('field_title')), $fields[$i]->getVar('field_name') . '_smaller', 15, time()));
                    break;

                case 'timezone':
                    $element = new XoopsFormSelect($fields[$i]->getVar('field_title'), $fields[$i]->getVar('field_name'), null, 6, true);
                    include_once $GLOBALS['xoops']->path('class/xoopslists.php');
                    $element->addOptionArray(XoopsLists::getTimeZoneList());
                    $searchform->addElement($element);
                    unset($element);
                    break;

                case 'language':
                    $element = new XoopsFormSelectLang($fields[$i]->getVar('field_title'), $fields[$i]->getVar('field_name'), null, 6);
                    $searchform->addElement($element);
                    unset($element);
                    break;
            }
        }
        asort($sortby_arr);
        $sortby_arr    = array_merge(['' => _NONE, 'uname' => _US_NICKNAME, 'email' => _US_EMAIL], $sortby_arr);
        $sortby_select = new XoopsFormSelect(_PROFILE_MA_SORTBY, 'sortby');
        $sortby_select->addOptionArray($sortby_arr);
        $searchform->addElement($sortby_select);

        $order_select = new XoopsFormRadio(_PROFILE_MA_ORDER, 'order', 0);
        $order_select->addOption(0, _ASCENDING);
        $order_select->addOption(1, _DESCENDING);
        $searchform->addElement($order_select);

        $limit_text = new XoopsFormText(_PROFILE_MA_PERPAGE, 'limit', 15, 10, $limit_default);
        $searchform->addElement($limit_text);
        $searchform->addElement(new XoopsFormHidden('op', 'results'));
        $searchform->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));

        $searchform->assign($GLOBALS['xoopsTpl']);
        $GLOBALS['xoopsTpl']->assign('page_title', _PROFILE_MA_SEARCH);

        //added count user
        /** @var XoopsMemberHandler $member_handler */
        $member_handler = xoops_getHandler('member');
        $acttotal       = $member_handler->getUserCount(new Criteria('level', 0, '>'));
        $total          = sprintf(_PROFILE_MA_ACTUS, "<span style='color:#ff0000;'>{$acttotal}</span>");
        $GLOBALS['xoopsTpl']->assign('total_users', $total);
        break;

    case 'results':
        $GLOBALS['xoopsOption']['template_main'] = 'profile_results.tpl';
        include_once $GLOBALS['xoops']->path('header.php');
        $GLOBALS['xoopsTpl']->assign('page_title', _PROFILE_MA_RESULTS);
        $xoBreadcrumbs[] = [
            'link'  => XOOPS_URL . '/modules/' . $GLOBALS['xoopsModule']->getVar('dirname', 'n') . '/search.php',
            'title' => _SEARCH,
        ];
        $xoBreadcrumbs[] = ['title' => _PROFILE_MA_RESULTS];
        /** @var XoopsMemberHandler $member_handler */
        $member_handler = xoops_getHandler('member');
        // Dynamic fields
        $profile_handler = xoops_getModuleHandler('profile');
        // Get fields
        $fields = $profile_handler->loadFields();
        // Get ids of fields that can be searched
        /** @var  XoopsGroupPermHandler $gperm_handler */
        $gperm_handler     = xoops_getHandler('groupperm');
        $searchable_fields = $gperm_handler->getItemIds('profile_search', $groups, $GLOBALS['xoopsModule']->getVar('mid'));
        $searchvars        = [];
        $search_url        = [];

        $criteria = new CriteriaCompo(new Criteria('level', 0, '>'));

        $uname = Request::getString('uname', '', 'POST');
        $uname_match = Request::getInt('uname_match', 0, 'POST');
        if ($uname !== '') {
            $uname = trim($uname);
            // Basic input validation - only allow alphanumeric characters and underscores
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $uname)) {
                redirect_header(XOOPS_URL . '/', 3, 'Invalid username provided.');
            }

            // Adjust the search pattern based on the match type
            switch ($uname_match) {
                case XOOPS_MATCH_START:
                    $uname .= '%';
                    break;
                case XOOPS_MATCH_END:
                    $uname = '%' . $uname;
                    break;
                case XOOPS_MATCH_CONTAIN:
                    $uname = '%' . $uname . '%';
                    break;
            }

            // Create criteria for the SQL query
            $criteria = new Criteria('uname', $uname, 'LIKE');
            [$clause, $params] = $criteria->render();

            // Prepare and execute the SQL query
            $sql = "SELECT * FROM " . $xoopsDB->prefix('users') . " WHERE " . $clause;
            $stmt = $xoopsDB->prepare($sql);

            foreach ($params as $placeholder => $value) {
                $stmt->bindValue($placeholder, $value);
            }

            $stmt->execute();
            $results = $stmt->fetchAll();

            // Process results
            $search_url = [];
            $searchvars = [];

            if ($results) {
                foreach ($results as $row) {
                    // Populate search URL and search variables based on the results
                    $search_url[] = 'uname=' . urlencode($row['uname']);
                    $search_url[] = 'uname_match=' . urlencode((string) $uname_match);
                    $searchvars[] = 'uname';
                }
            }

            // Further processing or usage of $search_url, $searchvars
            // You might render a page or redirect the user based on these results
        }

        $email = Request::getString('email', '', 'POST');
        $email_match = Request::getInt('email_match', 0, 'POST');
        if ($email !== '') {
            $string = $xoopsDB->escape(trim($email));
            switch ($email_match) {
                case XOOPS_MATCH_START:
                    $string .= '%';
                    break;

                case XOOPS_MATCH_END:
                    $string = '%' . $string;
                    break;

                case XOOPS_MATCH_CONTAIN:
                    $string = '%' . $string . '%';
                    break;
            }
            $searchvars[] = 'email';
            $search_url[] = 'email=' . rawurlencode($email);
            $search_url[] = 'email_match=' . $email_match;
            $criteria->add(new Criteria('email', $string, 'LIKE'));
            $criteria->add(new Criteria('user_viewemail', 1));
        }

        //$search_url = array();
        foreach (array_keys($fields) as $i) {
            //Radio and Select fields
            if (!in_array($fields[$i]->getVar('field_id'), $searchable_fields) || !in_array($fields[$i]->getVar('field_type'), $searchable_types)) {
                continue;
            }
            $fieldname = $fields[$i]->getVar('field_name');
            $fieldValues = Request::getArray($fieldname, [], 'POST');
            if (in_array($fields[$i]->getVar('field_type'), ['select', 'radio', 'timezone'])) {
                if (empty($fieldValues)) {
                    continue;
                }

                //If field value is sent through request and is not an empty value
                switch ($fields[$i]->getVar('field_valuetype')) {
                    case XOBJ_DTYPE_OTHER:
                    case XOBJ_DTYPE_INT:
                        $value        = array_map('intval', $fieldValues);
                        $searchvars[] = $fieldname;
                        $criteria->add(new Criteria($fieldname, '(' . implode(',', $value) . ')', 'IN'));
                        break;

                    case XOBJ_DTYPE_URL:
                    case XOBJ_DTYPE_TXTBOX:
                    case XOBJ_DTYPE_TXTAREA:
                        $value        = array_map([$GLOBALS['xoopsDB'], 'quoteString'], $fieldValues);
                        $searchvars[] = $fieldname;
                        $criteria->add(new Criteria($fieldname, '(' . implode(',', $value) . ')', 'IN'));
                        break;
                }
                foreach ($fieldValues as $value) {
                    $search_url[] = $fieldname . '[]=' . rawurlencode($value);
                }
            } else {
                //Other fields (not radio, not select)
                switch ($fields[$i]->getVar('field_valuetype')) {
                    case XOBJ_DTYPE_OTHER:
                    case XOBJ_DTYPE_INT:
                        $largerVal  = Request::getString($fieldname . '_larger', '', 'POST');
                        $smallerVal = Request::getString($fieldname . '_smaller', '', 'POST');
                        switch ($fields[$i]->getVar('field_type')) {
                            case 'date':
                            case 'datetime':
                                $value = strtotime($largerVal);
                                if (!$value) {
                                    $value = (int) $largerVal;
                                }
                                if ($value > 0) {
                                    $search_url[] = $fieldname . '_larger=' . $value;
                                    $searchvars[] = $fieldname;
                                    $criteria->add(new Criteria($fieldname, $value, '>='));
                                }

                                $value = strtotime($smallerVal);
                                if (!$value) {
                                    $value = (int) $smallerVal;
                                }
                                if ($value > 0) {
                                    $search_url[] = $fieldname . '_smaller=' . $value;
                                    $searchvars[] = $fieldname;
                                    $criteria->add(new Criteria($fieldname, $value + 24 * 3600, '<='));
                                }
                                break;

                            default:
                                $intLarger  = Request::getInt($fieldname . '_larger', 0, 'POST');
                                $intSmaller = Request::getInt($fieldname . '_smaller', 0, 'POST');
                                if ($intLarger !== 0) {
                                    $search_url[] = $fieldname . '_larger=' . $intLarger;
                                    $searchvars[] = $fieldname;
                                    $criteria->add(new Criteria($fieldname, $intLarger, '>='));
                                }

                                if ($intSmaller !== 0) {
                                    $search_url[] = $fieldname . '_smaller=' . $intSmaller;
                                    $searchvars[] = $fieldname;
                                    $criteria->add(new Criteria($fieldname, $intSmaller, '<='));
                                }
                                break;
                        }

                        if (!empty($fieldValues) && $largerVal === '' && $smallerVal === '') {
                            if (!is_array($fieldValues)) {
                                $value        = (int) $fieldValues;
                                $search_url[] = $fieldname . '=' . $value;
                                $criteria->add(new Criteria($fieldname, $value, '='));
                            } else {
                                $value = array_map('intval', $fieldValues);
                                foreach ($value as $thisvalue) {
                                    $search_url[] = $fieldname . '[]=' . $thisvalue;
                                }
                                $criteria->add(new Criteria($fieldname, '(' . implode(',', $value) . ')', 'IN'));
                            }

                            $searchvars[] = $fieldname;
                        }
                        break;

                    case XOBJ_DTYPE_URL:
                    case XOBJ_DTYPE_TXTBOX:
                    case XOBJ_DTYPE_TXTAREA:
                        $textFieldVal = Request::getString($fieldname, '', 'POST');
                        $textFieldMatch = Request::getInt($fieldname . '_match', 0, 'POST');
                        if ($textFieldVal !== '') {
                            $value = $xoopsDB->escape(trim($textFieldVal));
                            switch ($textFieldMatch) {
                                case XOOPS_MATCH_START:
                                    $value .= '%';
                                    break;

                                case XOOPS_MATCH_END:
                                    $value = '%' . $value;
                                    break;

                                case XOOPS_MATCH_CONTAIN:
                                    $value = '%' . $value . '%';
                                    break;
                            }
                            $search_url[] = $fieldname . '=' . rawurlencode($textFieldVal);
                            $search_url[] = $fieldname . '_match=' . $textFieldMatch;
                            $operator     = 'LIKE';
                            $criteria->add(new Criteria($fieldname, $value, $operator));
                            $searchvars[] = $fieldname;
                        }
                        break;
                }
            }
        }

        //        if ($_REQUEST['sortby'] == "name") {
        //            $criteria->setSort("name");
        //        } else if ($_REQUEST['sortby'] == "email") {
        //            $criteria->setSort("email");
        //        } else if ($_REQUEST['sortby'] == "uname") {
        //            $criteria->setSort("uname");
        //        } else if (isset($fields[$_REQUEST['sortby']])) {
        //            $criteria->setSort($fields[$_REQUEST['sortby']]->getVar('field_name'));
        //        }

        // change by zyspec:
        $sortby = 'uname';
        $sortbyInput = Request::getCmd('sortby', '', 'GET') ?: Request::getCmd('sortby', '', 'POST');
        if ($sortbyInput !== '') {
            switch ($sortbyInput) {
                case 'name':
                case 'email':
                case 'uname':
                    $sortby = $sortbyInput;
                    break;
                default:
                    if (isset($fields[$sortbyInput])) {
                        $sortby = $fields[$sortbyInput]->getVar('field_name');
                    }
                    break;
            }
            $criteria->setSort($sortby);
        }

        // add search groups , only for Webmasters
        $searchgroups = [];
        if ($GLOBALS['xoopsUser'] && $GLOBALS['xoopsUser']->isAdmin()) {
            $selgroups = Request::getArray('selgroups', [], 'POST');
            $searchgroups = empty($selgroups) ? [] : array_map('intval', $selgroups);
            foreach ($searchgroups as $group) {
                $search_url[] = 'selgroups[]=' . $group;
            }
        }

        $orderInt = Request::getInt('order', -1, 'GET');
        if ($orderInt < 0) {
            $orderInt = Request::getInt('order', 0, 'POST');
        }
        $order = $orderInt === 0 ? 'ASC' : 'DESC';
        $criteria->setOrder($order);

        $limit = Request::getInt('limit', 0, 'GET') ?: Request::getInt('limit', $limit_default, 'POST');
        $criteria->setLimit($limit);

        $start = Request::getInt('start', 0, 'GET') ?: Request::getInt('start', 0, 'POST');
        $criteria->setStart($start);

        [$users, $profiles, $total_users] = $profile_handler->search($criteria, $searchvars, $searchgroups);

        $total = sprintf(_PROFILE_MA_FOUNDUSER, "<span class='red'>{$total_users}</span>") . ' ';
        $GLOBALS['xoopsTpl']->assign('total_users', $total);

        //Sort information
        foreach (array_keys($users) as $k) {
            $userarray             = [];
            $userarray['output'][] = "<a href='userinfo.php?uid=" . $users[$k]->getVar('uid') . "' title=''>" . $users[$k]->getVar('uname') . '</a>';
            $userarray['output'][] = ($users[$k]->getVar('user_viewemail') == 1 || (is_object($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser']->isAdmin())) ? $users[$k]->getVar('email') : '';

            foreach (array_keys($fields) as $i) {
                if (in_array($fields[$i]->getVar('field_id'), $searchable_fields) && in_array($fields[$i]->getVar('field_type'), $searchable_types) && in_array($fields[$i]->getVar('field_name'), $searchvars)) {
                    $userarray['output'][] = $fields[$i]->getOutputValue($users[$k], $profiles[$k]);
                }
            }
            $GLOBALS['xoopsTpl']->append('users', $userarray);
            unset($userarray);
        }

        //Get captions
        $captions[] = _US_NICKNAME;
        $captions[] = _US_EMAIL;
        foreach (array_keys($fields) as $i) {
            if (in_array($fields[$i]->getVar('field_id'), $searchable_fields) && in_array($fields[$i]->getVar('field_type'), $searchable_types) && in_array($fields[$i]->getVar('field_name'), $searchvars)) {
                $captions[] = $fields[$i]->getVar('field_title');
            }
        }
        $GLOBALS['xoopsTpl']->assign('captions', $captions);

        if ($total_users > $limit) {
            $search_url[] = 'op=results';
            $search_url[] = 'order=' . $order;
            //TODO remove it for final release
            //            $search_url[] = "sortby=" . htmlspecialchars($_REQUEST['sortby']);
            $search_url[] = 'sortby=' . rawurlencode($sortby);
            $search_url[] = 'limit=' . $limit;
            if (isset($search_url)) {
                $args = implode('&amp;', $search_url);
            }

            include_once $GLOBALS['xoops']->path('class/pagenav.php');
            $nav = new XoopsPageNav($total_users, $limit, $start, 'start', $args);
            $GLOBALS['xoopsTpl']->assign('nav', $nav->renderNav(5));
        }
        break;
}
include __DIR__ . '/footer.php';
