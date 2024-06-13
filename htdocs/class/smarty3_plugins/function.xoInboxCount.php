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
 * @param string[] $params
 * @param Smarty   $smarty
 * @return null
 */
function smarty_function_xoInboxCount($params, $smarty)
{
    global $xoopsUser;

    if (!isset($xoopsUser) || !is_object($xoopsUser)) {
        return null;
    }

    // unset cache in pm programs so stale cache won't show inconsistencies
    $freshRead = isset($GLOBALS['xoInboxCountFresh']);
    $pmScripts = ['pmlite', 'readpmsg', 'viewpmsg'];
    if (in_array(basename($_SERVER['SCRIPT_FILENAME'], '.php'), $pmScripts)) {
        if (!$freshRead) {
            unset($_SESSION['xoops_inbox_count'], $_SESSION['xoops_inbox_total'], $_SESSION['xoops_inbox_count_expire']);
            $GLOBALS['xoInboxCountFresh'] = true;
        }
    }

    $time = time();
    if (isset($_SESSION['xoops_inbox_count']) && (isset($_SESSION['xoops_inbox_count_expire']) && $_SESSION['xoops_inbox_count_expire'] > $time)) {
        $totals['assign'] = (int)$_SESSION['xoops_inbox_count'];
        $totals['total'] = (int)$_SESSION['xoops_inbox_total'];
    } else {
        /** @var \XoopsPrivmessageHandler $pm_handler */
        $pm_handler = xoops_getHandler('privmessage');

        $xoopsPreload = XoopsPreload::getInstance();
        $xoopsPreload->triggerEvent('core.class.smarty.xoops_plugins.xoinboxcount', [$pm_handler]);

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
