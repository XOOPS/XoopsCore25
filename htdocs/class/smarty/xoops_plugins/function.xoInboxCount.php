<?php

/**
 * xoInboxCount lets templates access private message inbox statistics for the current user
 *
 * Example: <{xoInboxCount assign='unread_count' total='inbox_total'}>
 *
 * Both assign and total parameters are optional. If neither is specified the unread count is displayed.
 * - assign = variable name to assign with the current unread message count
 * - total  = variable name to assign with the current inbox total
 *
 * @param $params
 * @param $smarty
 * @return null
 */
function smarty_function_xoInboxCount($params, &$smarty)
{
    global $xoopsUser;

    if (!isset($xoopsUser) || !is_object($xoopsUser)) {
        return null;
    }
    $time = time();
    if (isset($_SESSION['xoops_inbox_count']) && @$_SESSION['xoops_inbox_count_expire'] > $time) {
        $totals['assign'] = (int)$_SESSION['xoops_inbox_count'];
        $totals['total'] = (int)$_SESSION['xoops_inbox_total'];
    } else {
        $pm_handler = xoops_getHandler('privmessage');

        $xoopsPreload = XoopsPreload::getInstance();
        $xoopsPreload->triggerEvent('core.class.smarty.xoops_plugins.xoinboxcount', array($pm_handler));

        $criteria = new CriteriaCompo(new Criteria('to_userid', $xoopsUser->getVar('uid')));
        $totals['total'] = $pm_handler->getCount($criteria);

        $criteria->add(new Criteria('read_msg', 0));
        $totals['assign'] = $pm_handler->getCount($criteria);

        $_SESSION['xoops_inbox_count'] = $totals['assign'];
        $_SESSION['xoops_inbox_total'] = $totals['total'];
        $_SESSION['xoops_inbox_count_expire'] = $time + 60;
    }

    $printCount = true;
    foreach ($totals as $key => $count) {
        if (!empty($params[$key])) {
            $smarty->assign($params[$key], $count);
            $printCount = false;
        }
    }
    if ($printCount) {
        echo $totals['assign'];
    }
}
