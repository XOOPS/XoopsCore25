<?php
/**
 * Find XOOPS users
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
 * @package             kernel
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
/* @var  $xoopsUser XoopsUser */

include_once dirname(__DIR__) . '/mainfile.php';

xoops_header(false);

$denied = true;
if (!empty($_REQUEST['token'])) {
    if ($GLOBALS['xoopsSecurity']->validateToken($_REQUEST['token'], false)) {
        $denied = false;
    }
} elseif (is_object($xoopsUser) && $xoopsUser->isAdmin()) {
    $denied = false;
}
if ($denied) {
    xoops_error(_NOPERM);
    exit();
}

$token         = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
$name_form     = 'memberslist';
$name_userid   = 'uid' . (!empty($_REQUEST['multiple']) ? '[]' : '');
$name_username = 'uname' . (!empty($_REQUEST['multiple']) ? '[]' : '');

xoops_loadLanguage('findusers');

/**
 * Enter description here...
 *
 */
class XoopsRank extends XoopsObject
{
    /**
     * Construct
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->initVar('rank_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('rank_title', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('rank_min', XOBJ_DTYPE_INT, 0);
        $this->initVar('rank_max', XOBJ_DTYPE_INT, 0);
        $this->initVar('rank_special', XOBJ_DTYPE_INT, 0);
        $this->initVar('rank_image', XOBJ_DTYPE_TXTBOX, '');
    }
}

/**
 * Xoops Rank Handler
 *
 */
class XoopsRankHandler extends XoopsObjectHandler
{
    /**
     * Constructor
     *
     * @param XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db);
    }

    /**
     * Create Object
     *
     * @param  bool $isNew
     * @return XoopsRank
     */
    public function create($isNew = true)
    {
        $obj = new XoopsRank();
        if ($isNew === true) {
            $obj->setNew();
        }

        return $obj;
    }

    /**
     * Get Object
     *
     * @param  int $id
     * @return object
     */
    public function get($id = 0)
    {
        $object = $this->create(false);
        $sql    = 'SELECT * FROM ' . $this->db->prefix('ranks') . ' WHERE rank_id = ' . $this->db->quoteString($id);
        if (!$result = $this->db->query($sql)) {
            $ret = null;

            return $ret;
        }
        while (false !== ($row = $this->db->fetchArray($result))) {
            $object->assignVars($row);
        }

        return $object;
    }

    /**
     * Get List
     *
     * @param  CriteriaElement $criteria
     * @param  int             $limit
     * @param  int             $start
     * @return array
     */
    public function getList(CriteriaElement $criteria = null, $limit = 0, $start = 0)
    {
        $ret = array();
        if ($criteria == null) {
            $criteria = new CriteriaCompo();
        }

        $sql = 'SELECT rank_id, rank_title FROM ' . $this->db->prefix('ranks');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            if ($criteria->getSort() != '') {
                $sql .= ' ORDER BY ' . $criteria->getSort() . ' ' . $criteria->getOrder();
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        $myts = MyTextSanitizer::getInstance();
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $ret[$myrow['rank_id']] = $myts->htmlSpecialChars($myrow['rank_title']);
        }

        return $ret;
    }
}

/**
 * Xoops Users Extend Class
 *
 */
class XoUser extends XoopsUser
{
    /**
     * Enter Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $unsets = array(
            'actkey',
            'pass',
            'theme',
            'umode',
            'uorder',
            'notify_mode');
        foreach ($unsets as $var) {
            unset($this->vars[$var]);
        }
    }
}

/**
 * XoUser Handler
 *
 */
class XoUserHandler extends XoopsObjectHandler
{
    /**
     * Enter description here...
     *
     * @param XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db);
    }

    /**
     * Create
     *
     * @param  bool $isNew
     * @return XoUser
     */
    public function create($isNew = true)
    {
        $obj = new XoUser();
        if ($isNew === true) {
            $obj->setNew();
        }

        return $obj;
    }

    /**
     * Get Count
     *
     * @param  CriteriaElement $criteria
     * @param  array           $groups
     * @return int
     */
    public function getCount(CriteriaElement $criteria = null, $groups = array())
    {
        if (!is_array($groups)) {
            $groups = array(
                $groups);
        }
        $groups = array_filter($groups);
        if (empty($groups)) {
            $sql = '    SELECT COUNT(DISTINCT u.uid) FROM ' . $this->db->prefix('users') . ' AS u' . '    WHERE 1=1';
        } else {
            $sql = '    SELECT COUNT(DISTINCT u.uid) FROM ' . $this->db->prefix('users') . ' AS u' . '    LEFT JOIN ' . $this->db->prefix('groups_users_link') . ' AS g ON g.uid = u.uid' . '    WHERE g.groupid IN (' . implode(', ', array_map('intval', $groups)) . ')';
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            // Use the direct renderer, assuming no `uid` in criteria
            if ($render = $criteria->render()) {
                $sql .= ' AND ' . $render;
            }
        }
        $result = $this->db->query($sql);
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * GetAll
     *
     * @param  CriteriaElement $criteria
     * @param  array           $groups
     * @return object
     */
    public function getAll(CriteriaElement $criteria = null, $groups = array())
    {
        if (!is_array($groups)) {
            $groups = array(
                $groups);
        }
        $groups = array_filter($groups);
        $limit  = null;
        $start  = null;
        if (empty($groups)) {
            $sql = '    SELECT u.* FROM ' . $this->db->prefix('users') . ' AS u' . '    WHERE 1=1';
        } else {
            $sql = '    SELECT u.* FROM ' . $this->db->prefix('users') . ' AS u' . '    LEFT JOIN ' . $this->db->prefix('groups_users_link') . ' AS g ON g.uid = u.uid' . '    WHERE g.groupid IN (' . implode(', ', array_map('intval', $groups)) . ')';
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            if ($render = $criteria->render()) {
                $sql .= ' AND ' . $render;
            }
            if ($sort = $criteria->getSort()) {
                $sql .= ' ORDER BY ' . $sort . ' ' . $criteria->getOrder();
                $orderSet = true;
            }
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        if (empty($orderSet)) {
            $sql .= ' ORDER BY u.uid ASC';
        }
        $result = $this->db->query($sql, $limit, $start);
        $ret    = array();
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $object = $this->create(false);
            $object->assignVars($myrow);
            $ret[$myrow['uid']] = $object;
            unset($object);
        }

        return $ret;
    }
}

$rank_handler = new XoopsRankHandler($xoopsDB);
$user_handler = new XoUserHandler($xoopsDB);

$items_match = array(
    'uname'     => _MA_USER_UNAME,
    'name'      => _MA_USER_REALNAME,
    'email'     => _MA_USER_EMAIL,
    'user_icq'  => _MA_USER_ICQ,
    'user_aim'  => _MA_USER_AIM,
    'user_yim'  => _MA_USER_YIM,
    'user_msnm' => _MA_USER_MSNM);

$items_range = array(
    'user_regdate' => _MA_USER_RANGE_USER_REGDATE,
    'last_login'   => _MA_USER_RANGE_LAST_LOGIN,
    'posts'        => _MA_USER_RANGE_POSTS);

define('FINDUSERS_MODE_SIMPLE', 0);
define('FINDUSERS_MODE_ADVANCED', 1);
define('FINDUSERS_MODE_QUERY', 2);

$modes = array(
    FINDUSERS_MODE_SIMPLE   => _MA_USER_MODE_SIMPLE,
    FINDUSERS_MODE_ADVANCED => _MA_USER_MODE_ADVANCED,
    FINDUSERS_MODE_QUERY    => _MA_USER_MODE_QUERY);

if (empty($_POST['user_submit'])) {
    include_once $GLOBALS['xoops']->path('class/xoopsformloader.php');

    $form = new XoopsThemeForm(_MA_USER_FINDUS, 'user_findform', 'findusers.php', 'post', true);
    $mode = (int)(@$_REQUEST['mode']);
    if (FINDUSERS_MODE_QUERY == $mode) {
        $form->addElement(new XoopsFormTextArea(_MA_USER_QUERY, 'query', @$_POST['query']));
    } else {
        if (FINDUSERS_MODE_ADVANCED == $mode) {
            foreach ($items_match as $var => $title) {
                $text       = new XoopsFormText('', $var, 30, 100, @$_POST[$var]);
                $match      = new XoopsFormSelectMatchOption('', "{$var}_match", @$_POST["{$var}_match"]);
                $match_tray = new XoopsFormElementTray($title, '&nbsp;');
                $match_tray->addElement($match);
                $match_tray->addElement($text);
                $form->addElement($match_tray);
                unset($text, $match, $match_tray);
            }

            $url_text        = new XoopsFormText(_MA_USER_URLC, 'url', 30, 100, @$_POST['url']);
            $location_text   = new XoopsFormText(_MA_USER_LOCATION, 'user_from', 30, 100, @$_POST['user_from']);
            $occupation_text = new XoopsFormText(_MA_USER_OCCUPATION, 'user_occ', 30, 100, @$_POST['user_occ']);
            $interest_text   = new XoopsFormText(_MA_USER_INTEREST, 'user_intrest', 30, 100, @$_POST['user_intrest']);
            foreach ($items_range as $var => $title) {
                $more       = new XoopsFormText('', "{$var}_more", 10, 5, @$_POST["{$var}_more"]);
                $less       = new XoopsFormText('', "{$var}_less", 10, 5, @$_POST["{$var}_less"]);
                $range_tray = new XoopsFormElementTray($title, '&nbsp;-&nbsp;&nbsp;');
                $range_tray->addElement($less);
                $range_tray->addElement($more);
                $form->addElement($range_tray);
                unset($more, $less, $range_tray);
            }

            $mailok_radio = new XoopsFormRadio(_MA_USER_SHOWMAILOK, 'user_mailok', empty($_POST['user_mailok']) ? 'both' : $_POST['user_mailok']);
            $mailok_radio->addOptionArray(array(
                                              'mailok' => _MA_USER_MAILOK,
                                              'mailng' => _MA_USER_MAILNG,
                                              'both' => _MA_USER_BOTH));
            $avatar_radio = new XoopsFormRadio(_MA_USER_HASAVATAR, 'user_avatar', empty($_POST['user_avatar']) ? 'both' : $_POST['user_avatar']);
            $avatar_radio->addOptionArray(array(
                                              'y' => _YES,
                                              'n' => _NO,
                                              'both' => _MA_USER_BOTH));

            $level_radio = new XoopsFormRadio(_MA_USER_LEVEL, 'level', @$_POST['level']);
            $levels      = array(
                0 => _ALL,
                1 => _MA_USER_LEVEL_ACTIVE,
                2 => _MA_USER_LEVEL_INACTIVE,
                3 => _MA_USER_LEVEL_DISABLED);
            $level_radio->addOptionArray($levels);

            /* @var $member_handler XoopsMemberHandler */
            $member_handler = xoops_getHandler('member');
            $groups         = $member_handler->getGroupList();
            $groups[0]      = _ALL;
            $group_select   = new XoopsFormSelect(_MA_USER_GROUP, 'groups', @$_POST['groups'], 3, true);
            $group_select->addOptionArray($groups);

            $ranks       = $rank_handler->getList();
            $ranks[0]    = _ALL;
            $rank_select = new XoopsFormSelect(_MA_USER_RANK, 'rank', (int)(@$_POST['rank']));
            $rank_select->addOptionArray($ranks);
            $form->addElement($url_text);
            $form->addElement($location_text);
            $form->addElement($occupation_text);
            $form->addElement($interest_text);
            $form->addElement($mailok_radio);
            $form->addElement($avatar_radio);
            $form->addElement($level_radio);
            $form->addElement($group_select);
            $form->addElement($rank_select);
        } else {
            foreach (array(
                         'uname',
                         'email') as $var) {
                $title      = $items_match[$var];
                $text       = new XoopsFormText('', $var, 30, 100, @$_POST[$var]);
                $match      = new XoopsFormSelectMatchOption('', "{$var}_match", @$_POST["{$var}_match"]);
                $match_tray = new XoopsFormElementTray($title, '&nbsp;');
                $match_tray->addElement($match);
                $match_tray->addElement($text);
                $form->addElement($match_tray);
                unset($text, $match, $match_tray);
            }
        }

        $sort_select = new XoopsFormSelect(_MA_USER_SORT, 'user_sort', @$_POST['user_sort']);
        $sort_select->addOptionArray(array(
                                         'uname' => _MA_USER_UNAME,
                                         'last_login' => _MA_USER_LASTLOGIN,
                                         'user_regdate' => _MA_USER_REGDATE,
                                         'posts' => _MA_USER_POSTS));
        $order_select = new XoopsFormSelect(_MA_USER_ORDER, 'user_order', @$_POST['user_order']);
        $order_select->addOptionArray(array(
                                          'ASC' => _MA_USER_ASC,
                                          'DESC' => _MA_USER_DESC));

        $form->addElement($sort_select);
        $form->addElement($order_select);
    }
    $form->addElement(new XoopsFormText(_MA_USER_LIMIT, 'limit', 6, 6, empty($_REQUEST['limit']) ? 50 : (int)$_REQUEST['limit']));
    $form->addElement(new XoopsFormHidden('mode', $mode));
    $form->addElement(new XoopsFormHidden('target', @$_REQUEST['target']));
    $form->addElement(new XoopsFormHidden('multiple', @$_REQUEST['multiple']));
    $form->addElement(new XoopsFormHidden('token', $token));
    $form->addElement(new XoopsFormButton('', 'user_submit', _SUBMIT, 'submit'));

    $acttotal   = $user_handler->getCount(new Criteria('level', 0, '>'));
    $inacttotal = $user_handler->getCount(new Criteria('level', 0, '<='));
    echo '</html><body>';
    echo "<h2 style='text-align:left;'>" . _MA_USER_FINDUS . ' - ' . $modes[$mode] . '</h2>';
    $modes_switch = array();
    foreach ($modes as $_mode => $title) {
        if ($mode == $_mode) {
            continue;
        }
        $modes_switch[] = "<a href='findusers.php?target=" . htmlspecialchars(@$_REQUEST['target'], ENT_QUOTES) . '&amp;multiple=' . htmlspecialchars(@$_REQUEST['multiple'], ENT_QUOTES) . '&amp;token=' . htmlspecialchars($token, ENT_QUOTES) . "&amp;mode={$_mode}'>{$title}</a>";
    }
    echo '<h4>' . implode(' | ', $modes_switch) . '</h4>';
    echo '(' . sprintf(_MA_USER_ACTUS, "<span style='color:#ff0000;'>$acttotal</span>") . ' ' . sprintf(_MA_USER_INACTUS, "<span style='color:#ff0000;'>$inacttotal</span>") . ')';
    $form->display();
} else {
    $myts  = MyTextSanitizer::getInstance();
    $limit = empty($_POST['limit']) ? 50 : (int)$_POST['limit'];
    $start = (int)(@$_POST['start']);
    if (!isset($_POST['query'])) {
        $criteria = new CriteriaCompo();
        foreach (array_keys($items_match) as $var) {
            if (!empty($_POST[$var])) {
                $match = (!empty($_POST["{$var}_match"])) ? (int)$_POST["{$var}_match"] : XOOPS_MATCH_START;
                $value = str_replace('_', "\\\_", $myts->addSlashes(trim($_POST[$var])));
                switch ($match) {
                    case XOOPS_MATCH_START:
                        $criteria->add(new Criteria($var, $value . '%', 'LIKE'));
                        break;
                    case XOOPS_MATCH_END:
                        $criteria->add(new Criteria($var, '%' . $value, 'LIKE'));
                        break;
                    case XOOPS_MATCH_EQUAL:
                        $criteria->add(new Criteria($var, $value));
                        break;
                    case XOOPS_MATCH_CONTAIN:
                        $criteria->add(new Criteria($var, '%' . $value . '%', 'LIKE'));
                        break;
                }
            }
        }
        if (!empty($_POST['url'])) {
            $url = formatURL(trim($_POST['url']));
            $criteria->add(new Criteria('url', $url . '%', 'LIKE'));
        }
        if (!empty($_POST['user_from'])) {
            $criteria->add(new Criteria('user_from', '%' . $myts->addSlashes(trim($_POST['user_from'])) . '%', 'LIKE'));
        }
        if (!empty($_POST['user_intrest'])) {
            $criteria->add(new Criteria('user_intrest', '%' . $myts->addSlashes(trim($_POST['user_intrest'])) . '%', 'LIKE'));
        }
        if (!empty($_POST['user_occ'])) {
            $criteria->add(new Criteria('user_occ', '%' . $myts->addSlashes(trim($_POST['user_occ'])) . '%', 'LIKE'));
        }
        foreach (array(
                     'last_login',
                     'user_regdate') as $var) {
            if (!empty($_POST["{$var}_more"]) && is_numeric($_POST["{$var}_more"])) {
                $time = time() - (60 * 60 * 24 * (int)trim($_POST["{$var}_more"]));
                if ($time > 0) {
                    $criteria->add(new Criteria($var, $time, '<='));
                }
            }
            if (!empty($_POST["{$var}_less"]) && is_numeric($_POST["{$var}_less"])) {
                $time = time() - (60 * 60 * 24 * (int)trim($_POST["{$var}_less"]));
                if ($time > 0) {
                    $criteria->add(new Criteria($var, $time, '>='));
                }
            }
        }
        if (!empty($_POST['posts_more']) && is_numeric($_POST['posts_more'])) {
            $criteria->add(new Criteria('posts', (int)$_POST['posts_more'], '<='));
        }
        if (!empty($_POST['posts_less']) && is_numeric($_POST['posts_less'])) {
            $criteria->add(new Criteria('posts', (int)$_POST['posts_less'], '>='));
        }
        if (!empty($_POST['user_mailok'])) {
            if ($_POST['user_mailok'] === 'mailng') {
                $criteria->add(new Criteria('user_mailok', 0));
            } elseif ($_POST['user_mailok'] === 'mailok') {
                $criteria->add(new Criteria('user_mailok', 1));
            }
        }
        if (!empty($_POST['user_avatar'])) {
            if ($_POST['user_avatar'] === 'y') {
                $criteria->add(new Criteria('user_avatar', "('', 'blank.gif')", 'NOT IN'));
            } elseif ($_POST['user_avatar'] === 'n') {
                $criteria->add(new Criteria('user_avatar', "('', 'blank.gif')", 'IN'));
            }
        }
        if (!empty($_POST['level'])) {
            $level_value = array(
                1 => 1,
                2 => 0,
                3 => -1);
            $level       = isset($level_value[(int)$_POST['level']]) ? $level_value[(int)$_POST['level']] : 1;
            $criteria->add(new Criteria('level', $level));
        }
        if (!empty($_POST['rank'])) {
            $rank_obj = $rank_handler->get($_POST['rank']);
            if ($rank_obj->getVar('rank_special')) {
                $criteria->add(new Criteria('rank', (int)$_POST['rank']));
            } else {
                if ($rank_obj->getVar('rank_min')) {
                    $criteria->add(new Criteria('posts', $rank_obj->getVar('rank_min'), '>='));
                }
                if ($rank_obj->getVar('rank_max')) {
                    $criteria->add(new Criteria('posts', $rank_obj->getVar('rank_max'), '<='));
                }
            }
        }
        $total     = $user_handler->getCount($criteria, @$_POST['groups']);
        $validsort = array(
            'uname',
            'email',
            'last_login',
            'user_regdate',
            'posts');
        $sort      = (!in_array($_POST['user_sort'], $validsort)) ? 'uname' : $_POST['user_sort'];
        $order     = 'ASC';
        if (isset($_POST['user_order']) && $_POST['user_order'] === 'DESC') {
            $order = 'DESC';
        }
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $foundusers = $user_handler->getAll($criteria, @$_POST['groups']);
    } else {
        $query = trim($_POST['query']);
        // Query with alias
        if (preg_match("/select[\s]+.*[\s]+from[\s]+(" . $xoopsDB->prefix('users') . "[\s]+as[\s]+(\S+).*)/i", $query, $matches)) {
            $alias    = $matches[2];
            $subquery = $matches[1];

            // Query without alias
        } elseif (preg_match("/select[\s]+.*[\s]+from[\s]+(" . $xoopsDB->prefix('users') . "\b.*)/i", $query, $matches)) {
            $alias    = '';
            $subquery = $matches[1];

            // Invalid query
        } else {
            $query    = 'SELECT * FROM ' . $xoopsDB->prefix('users');
            $subquery = $xoopsDB->prefix('users');
        }
        $sql_count = 'SELECT COUNT(DISTINCT ' . (empty($alias) ? '' : $alias . '.') . 'uid) FROM ' . $subquery;
        $result    = $xoopsDB->query($sql_count);
        list($total) = $xoopsDB->FetchRow($result);
        $result     = $xoopsDB->query($query, $limit, $start);
        $foundusers = array();
        while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
            $object = $user_handler->create(false);
            $object->assignVars($myrow);
            $foundusers[$myrow['uid']] = $object;
            unset($object);
        }
    }

    echo $js_adduser = '
        <script type="text/javascript">
        var multiple=' . (int)$_REQUEST['multiple'] . ';
        function addusers()
        {
            var sel_str = "";
            var num = 0;
            var mForm = document.forms["' . $name_form . '"];
            for (var i=0;i!=mForm.elements.length;i++) {
                var id=mForm.elements[i];
                if ( ( (multiple > 0 && id.type == "checkbox") || (multiple == 0 && id.type == "radio") ) && (id.checked == true) && ( id.name == "' . $name_userid . '" ) ) {
                    var name = mForm.elements[++i];
                    var len = id.value.length + name.value.length;
                    sel_str += len + ":" + id.value + ":" + name.value;
                    num ++;
                }
            }
            if (num == 0) {
                alert("' . _MA_USER_NOUSERSELECTED . '");
                return false;
            }
            sel_str = num + ":" + sel_str;
            window.opener.addusers(sel_str);
            alert("' . _MA_USER_USERADDED . '");
            if (multiple == 0) {
                window.close();
                window.opener.focus();
            }
            return true;
        }
        </script>
    ';

    echo '</html><body>';
    echo "<a href='findusers.php?target=" . htmlspecialchars(@$_POST['target'], ENT_QUOTES) . '&amp;multiple=' . (int)(@$_POST['multiple']) . '&amp;token=' . htmlspecialchars($token, ENT_QUOTES) . "'>" . _MA_USER_FINDUS . "</a>&nbsp;<span style='font-weight:bold;'>&raquo;</span>&nbsp;" . _MA_USER_RESULTS . '<br><br>';
    if (empty($start) && empty($foundusers)) {
        echo '<h4>' . _MA_USER_NOFOUND, '</h4>';
        $hiddenform = "<form name='findnext' action='findusers.php' method='post'>";
        foreach ($_POST as $k => $v) {
            if ($k === 'XOOPS_TOKEN_REQUEST') {
                // regenerate token value
                $hiddenform .= $GLOBALS['xoopsSecurity']->getTokenHTML() . "\n";
            } else {
                $hiddenform .= "<input type='hidden' name='" . htmlspecialchars($k, ENT_QUOTES) . "' value='" . htmlspecialchars($myts->stripSlashesGPC($v), ENT_QUOTES) . "' />\n";
            }
        }
        if (!isset($_POST['limit'])) {
            $hiddenform .= "<input type='hidden' name='limit' value='{$limit}' />\n";
        }
        if (!isset($_POST['start'])) {
            $hiddenform .= "<input type='hidden' name='start' value='{$start}' />\n";
        }
        $hiddenform .= "<input type='hidden' name='token' value='" . htmlspecialchars($token, ENT_QUOTES) . "' />\n";
        $hiddenform .= '</form>';

        echo '<div>' . $hiddenform;
        echo "<a href='#' onclick='document.findnext.start.value=0;document.findnext.user_submit.value=0;document.findnext.submit();'>" . _MA_USER_SEARCHAGAIN . "</a>\n";
        echo '</div>';
    } elseif ($start < $total) {
        if (!empty($total)) {
            echo sprintf(_MA_USER_USERSFOUND, $total) . '<br>';
        }
        if (!empty($foundusers)) {
            echo "<form action='findusers.php' method='post' name='{$name_form}' id='{$name_form}'>
            <table width='100%' border='0' cellspacing='1' cellpadding='4' class='outer'>
            <tr>
            <th align='center' width='5px'>";
            if (!empty($_POST['multiple'])) {
                echo "<input type='checkbox' name='memberslist_checkall' id='memberslist_checkall' onclick='xoopsCheckAll(\"{$name_form}\", \"memberslist_checkall\");' />";
            }
            echo "</th>
            <th align='center'>" . _MA_USER_UNAME . "</th>
            <th align='center'>" . _MA_USER_REALNAME . "</th>
            <th align='center'>" . _MA_USER_REGDATE . "</th>
            <th align='center'>" . _MA_USER_LASTLOGIN . "</th>
            <th align='center'>" . _MA_USER_POSTS . '</th>
            </tr>';
            $ucount = 0;
            foreach (array_keys($foundusers) as $j) {
                $class = 'odd';
                if ($ucount % 2 == 0) {
                    $class = 'even';
                }
                ++$ucount;
                $fuser_name = $foundusers[$j]->getVar('name') ?: '&nbsp;';
                echo "<tr class='$class'>
                    <td align='center'>";
                if (!empty($_POST['multiple'])) {
                    echo "<input type='checkbox' name='{$name_userid}' id='{$name_userid}' value='" . $foundusers[$j]->getVar('uid') . "' />";
                    echo "<input type='hidden' name='{$name_username}' id='{$name_username}' value='" . $foundusers[$j]->getVar('uname') . "' />";
                } else {
                    echo "<input type='radio' name='{$name_userid}' id='{$name_userid}' value='" . $foundusers[$j]->getVar('uid') . "' />";
                    echo "<input type='hidden' name='{$name_username}' id='{$name_username}' value='" . $foundusers[$j]->getVar('uname') . "' />";
                }
                echo "</td>
                    <td><a href='" . XOOPS_URL . '/userinfo.php?uid=' . $foundusers[$j]->getVar('uid') . "' target='_blank'>" . $foundusers[$j]->getVar('uname') . '</a></td>
                    <td>' . $fuser_name . "</td>
                    <td align='center'>" . ($foundusers[$j]->getVar('user_regdate') ? date('Y-m-d', $foundusers[$j]->getVar('user_regdate')) : '') . "</td>
                    <td align='center'>" . ($foundusers[$j]->getVar('last_login') ? date('Y-m-d H:i', $foundusers[$j]->getVar('last_login')) : '') . "</td>
                    <td align='center'>" . $foundusers[$j]->getVar('posts') . '</td>';
                echo "</tr>\n";
            }
            echo "<tr class='foot'><td colspan='6'>";

            // placeholder for external applications
            if (empty($_POST['target'])) {
                echo "<select name='fct'><option value='users'>" . _DELETE . "</option><option value='mailusers'>" . _MA_USER_SENDMAIL . '</option>';
                echo '</select>&nbsp;';
                echo $GLOBALS['xoopsSecurity']->getTokenHTML() . "<input type='submit' value='" . _SUBMIT . "' />";

                // Add selected users
            } else {
                echo "<input type='button' value='" . _MA_USER_ADD_SELECTED . "' onclick='addusers();' />";
            }
            echo "<input type='hidden' name='token' value='" . htmlspecialchars($token, ENT_QUOTES) . "' />\n";
            echo "</td></tr></table></form>\n";
        }

        $hiddenform = "<form name='findnext' action='findusers.php' method='post'>";
        foreach ($_POST as $k => $v) {
            if ($k === 'XOOPS_TOKEN_REQUEST') {
                // regenerate token value
                $hiddenform .= $GLOBALS['xoopsSecurity']->getTokenHTML() . "\n";
            } else {
                $hiddenform .= "<input type='hidden' name='" . htmlspecialchars($k, ENT_QUOTES) . "' value='" . htmlspecialchars($myts->stripSlashesGPC($v), ENT_QUOTES) . "' />\n";
            }
        }
        if (!isset($_POST['limit'])) {
            $hiddenform .= "<input type='hidden' name='limit' value='" . $limit . "' />\n";
        }
        if (!isset($_POST['start'])) {
            $hiddenform .= "<input type='hidden' name='start' value='" . $start . "' />\n";
        }
        $hiddenform .= "<input type='hidden' name='token' value='" . htmlspecialchars($token, ENT_QUOTES) . "' />\n";
        if (!isset($total) || ($totalpages = ceil($total / $limit)) > 1) {
            $prev = $start - $limit;
            if ($start - $limit >= 0) {
                $hiddenform .= "<a href='#0' onclick='document.findnext.start.value=" . $prev . ";document.findnext.submit();'>" . _MA_USER_PREVIOUS . "</a>&nbsp;\n";
            }
            $counter     = 1;
            $currentpage = ($start + $limit) / $limit;
            if (!isset($total)) {
                while ($counter <= $currentpage) {
                    if ($counter == $currentpage) {
                        $hiddenform .= '<strong>' . $counter . '</strong> ';
                    } elseif (($counter > $currentpage - 4 && $counter < $currentpage + 4) || $counter == 1) {
                        $hiddenform .= "<a href='#" . $counter . "' onclick='document.findnext.start.value=" . ($counter - 1) * $limit . ";document.findnext.submit();'>" . $counter . '</a> ';
                        if ($counter == 1 && $currentpage > 5) {
                            $hiddenform .= '... ';
                        }
                    }
                    ++$counter;
                }
            } else {
                while ($counter <= $totalpages) {
                    if ($counter == $currentpage) {
                        $hiddenform .= '<strong>' . $counter . '</strong> ';
                    } elseif (($counter > $currentpage - 4 && $counter < $currentpage + 4) || $counter == 1 || $counter == $totalpages) {
                        if ($counter == $totalpages && $currentpage < $totalpages - 4) {
                            $hiddenform .= '... ';
                        }
                        $hiddenform .= "<a href='#" . $counter . "' onclick='document.findnext.start.value=" . ($counter - 1) * $limit . ";document.findnext.submit();'>" . $counter . '</a> ';
                        if ($counter == 1 && $currentpage > 5) {
                            $hiddenform .= '... ';
                        }
                    }
                    ++$counter;
                }
            }

            $next = $start + $limit;
            if ((isset($total) && $total > $next) || (!isset($total) && count($foundusers) >= $limit)) {
                $hiddenform .= "&nbsp;<a href='#" . $total . "' onclick='document.findnext.start.value=" . $next . ";document.findnext.submit();'>" . _MA_USER_NEXT . "</a>\n";
            }
        }
        $hiddenform .= '</form>';

        echo '<div>' . $hiddenform;
        if (isset($total)) {
            echo '<br>' . sprintf(_MA_USER_USERSFOUND, $total) . '&nbsp;';
        }
        echo "<a href='#' onclick='document.findnext.start.value=0;document.findnext.user_submit.value=0;document.findnext.submit();'>" . _MA_USER_SEARCHAGAIN . "</a>\n";
        echo '</div>';
    }
}

xoops_footer();
