<?php

use Xmf\Module\Helper\Cache;
use Xmf\IPAddress;

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.xoStats.php
 * Type:     function
 * Name:     xoStats
 * Purpose:  XOOPS Members Statistics
 * Author:   Lio MJ <liomj83@gmail.com>
 * Examples:
 *  <{xoStats}>
 *  Latest Member : <a href="<{$xoops_url}>/userinfo.php?uid=<{$latestUid}>"><{$latestMemberUname}></a><br>
 *  Total Posts   : <{$totalPosts}><br>
 *  Total Users   : <{$totalUsers}><br>
 *  Total Online  : <{$totalOnline}><br>
 *  New Users Today     : <{$newUsersToday}><br>
 *  New Users Yesterday : <{$newUsersYesterday}><br>
 *
 *  These are the names of all the values that will be assigned for Smarty
 *      $latestMemberName - name of newest member
 *      $latestMemberUname - uname of newest member
 *      $latestUid - uid of newest member
 *      $totalOnline - total members online
 *      $totalPosts - total posts by all members
 *      $newUsersToday - number of members registered today
 *      $newUsersYesterday  - number of members registered yesterday
 *      $totalUsers - total number of members
 */

/**
 * @param array   $params
 * @param \Smarty $smarty Smarty instance
 *
 * @return void
 */
function smarty_function_xoStats($params, &$smarty)
{
    $cache = new Cache('system');
    $stats = $cache->cacheRead('xostats', 'xoStatsRegen', 30);

    foreach ($stats as $k => $v) {
        $smarty->assign($k, $v);
    }
}

function xoStatsRegen()
{
    global $xoopsUser, $xoopsModule;

    $stats = array();

    /** @var \XoopsMemberHandler $memberHandler */
    $memberHandler = xoops_getHandler('member');

    // Getting Total Online Users
    /** @var \XoopsOnlineHandler $onlineHandler */
    $onlineHandler = xoops_getHandler('online');
    // set gc probability to 10% for now..
    if (mt_rand(1, 100) < 11) {
        $onlineHandler->gc(300);
    }
    if (is_object($xoopsUser)) {
        $uid   = $xoopsUser->getVar('uid');
        $uname = $xoopsUser->getVar('uname');
    } else {
        $uid   = 0;
        $uname = '';
    }

    $requestIp = IPAddress::fromRequest()->asReadable();
    $requestIp = (false === $requestIp) ? '0.0.0.0' : $requestIp;
    if (is_object($xoopsModule)) {
        $onlineHandler->write($uid, $uname, time(), $xoopsModule->getVar('mid'), $requestIp);
    } else {
        $onlineHandler->write($uid, $uname, time(), 0, $requestIp);
    }
    $onlines = $onlineHandler->getAll();
    if (empty($onlines)) {
        $stats['totalOnline'] = 0;
    } else {
        $stats['totalOnline'] = count($onlines);
    }

    //Getting Total Registered Users
    $levelCriteria = new \Criteria('level', 0, '>');
    $criteria = new \CriteriaCompo($levelCriteria);
    $criteria24 = new \CriteriaCompo($levelCriteria);
    $criteria24->add(new \Criteria('user_regdate', (mktime(0, 0, 0) - (24 * 3600)), '>='), 'AND');
    $criteria48 = new \CriteriaCompo($levelCriteria);
    $criteria48->add(new \Criteria('user_regdate', (mktime(0, 0, 0) - (48 * 3600)), '>='), 'AND');
    $criteria48->add(new \Criteria('user_regdate', (mktime(0, 0, 0) - (24 * 3600)), '<'), 'AND');
    $stats['totalUsers'] = $memberHandler->getUserCount($levelCriteria);

    //Getting User Registration Statistics
    $stats['newUsersToday'] = $memberHandler->getUserCount($criteria24);
    $stats['newUsersYesterday'] = $memberHandler->getUserCount($criteria48);

    // Getting Last Registered Member
    $criteria->setOrder('DESC');
    $criteria->setSort('user_regdate');
    $criteria->setLimit(1);
    $lastMembers = $memberHandler->getUsers($criteria);
    $stats['latestMemberName'] = $lastMembers[0]->getVar('name');
    $stats['latestMemberUname'] = $lastMembers[0]->getVar('uname');
    $stats['latestUid'] = $lastMembers[0]->getVar('uid');

    //Total Post Count
    $sql = 'SELECT SUM(posts) AS totalposts FROM ' . $GLOBALS['xoopsDB']->prefix('users') . ' WHERE level > 0';
    $result = $GLOBALS['xoopsDB']->query($sql);
    if (!$GLOBALS['xoopsDB']->isResultSet($result)) {
        throw new \RuntimeException(
            \sprintf(_DB_QUERY_ERROR, $sql) . $GLOBALS['xoopsDB']->error(), E_USER_ERROR
        );
    }
    $myrow = $GLOBALS['xoopsDB']->fetchArray($result);
    $stats['totalPosts'] = $myrow['totalposts'];

    return $stats;
}
