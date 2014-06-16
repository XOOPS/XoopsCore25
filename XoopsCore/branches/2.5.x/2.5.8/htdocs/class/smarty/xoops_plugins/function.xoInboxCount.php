<?php

/**
 * @param $params
 * @param $smarty
 */
function smarty_function_xoInboxCount($params, &$smarty)
{
    global $xoopsUser;

    if (!isset($xoopsUser) || !is_object($xoopsUser)) {
        return;
    }
    $time = time();
    if (isset($_SESSION['xoops_inbox_count']) && @$_SESSION['xoops_inbox_count_expire'] > $time) {
        $count = intval($_SESSION['xoops_inbox_count']);
    } else {
        $pm_handler =& xoops_gethandler('privmessage');

        $xoopsPreload =& XoopsPreload::getInstance();
        $xoopsPreload->triggerEvent('core.class.smarty.xoops_plugins.xoinboxcount', array($pm_handler));

        $criteria = new CriteriaCompo(new Criteria('read_msg', 0));
        $criteria->add(new Criteria('to_userid', $xoopsUser->getVar('uid')));
        $count = intval($pm_handler->getCount($criteria));
        $_SESSION['xoops_inbox_count'] = $count;
        $_SESSION['xoops_inbox_count_expire'] = $time + 60;
    }
    if (!@empty( $params['assign'])) {
        $smarty->assign( $params['assign'], $count );
    } else {
        echo $count;
    }
}
