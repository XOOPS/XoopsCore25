<?php

//  Author: Trabis
//  URL: http://www.xuups.com
//  E-Mail: lusopoemas@gmail.com
//  Plugin version: 1.1
//  Release date: 06-04-2009
//  Usage : just place <{block id=1}> inside any template or theme, replace '1' with the id of the block you want to show
//
//  Other options:
//  display = 'title' -> shows just title
//  display = 'none' -> renders the block but does not display it
//  options = 'enter|block|options' -> overwrites block default options
//  groups = 'enter|allowed|groups' -> overwrites block default group view permissions
//  cache = 3600 -> overwrite cache time(in seconds)
//
//  Examples:
//  <{block id=1 display="title"}>   displays just the block title
//  <{block id=1}>                   displays just the block content
//  <{block id=7 display="none"}>    does not display nothing but executes the block, this can go for online block or to trigger some cron block
//  <{block id=600 groups="0|1" cache=20}>  display block just for this 2 groups and sets a cache of 20 seconds
//  <{block id=600 options="100|100|s_poweredby.gif|0"}> displays block with diferent options

/**
 * @param $params
 * @param $smarty
 *
 * @return mixed
 */
function smarty_function_block($params, &$smarty)
{
    if (!isset($params['id'])) {
        return null;
    }

    $display_title = (isset($params['display']) && $params['display'] === 'title');
    $display_none  = (isset($params['display']) && $params['display'] === 'none');
    $options       = isset($params['options']) ? $params['options'] : false;
    $groups        = isset($params['groups']) ? explode('|', $params['groups']) : false;
    $cache         = isset($params['cache']) ? (int)$params['cache'] : false;

    $block_id = (int)$params['id'];

    static $block_objs;
    if (!isset($block_objs[$block_id])) {
        include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';

        $blockObj = new XoopsBlock($block_id);

        if (!is_object($blockObj)) {
            return null;
        }

        $block_objs[$block_id] = $blockObj;
    } else {
        $blockObj = $block_objs[$block_id];
    }

    $user_groups = $GLOBALS['xoopsUser'] ? $GLOBALS['xoopsUser']->getGroups() : array(XOOPS_GROUP_ANONYMOUS);

    static $allowed_blocks;
    if (!is_array(@$allowed_blocks) || count($allowed_blocks) == 0) {
        $allowed_blocks = XoopsBlock::getAllBlocksByGroup($user_groups, false);
    }

    if ($groups) {
        if (!array_intersect($user_groups, $groups)) {
            return null;
        }
    } else {
        if (!in_array($block_id, $allowed_blocks)) {
            return null;
        }
    }

    if ($options) {
        $blockObj->setVar('options', $options);
    }

    if ($cache) {
        $blockObj->setVar('bcachetime', $cache);
    }

    if ($display_title) {
        return $blockObj->getVar('title');
    }

    $xoopsLogger = XoopsLogger::getInstance();
    $template    =& $GLOBALS['xoopsTpl'];

    $bcachetime = (int)$blockObj->getVar('bcachetime');
    if (empty($bcachetime)) {
        $template->caching = 0;
    } else {
        $template->caching        = 2;
        $template->cache_lifetime = $bcachetime;
    }

    $template->setCompileId($blockObj->getVar('dirname', 'n'));
    $tplName = ($tplName = $blockObj->getVar('template')) ? "db:{$tplName}" : 'db:system_block_dummy.tpl';
    $cacheid = 'blk_' . $block_id;

    if (!$bcachetime || !$template->is_cached($tplName, $cacheid)) {
        $xoopsLogger->addBlock($blockObj->getVar('name'));
        if (!($bresult = $blockObj->buildBlock())) {
            return null;
        }
        if (!$display_none) {
            $template->assign('block', $bresult);
            $template->display($tplName, $cacheid);
        }
    } else {
        $xoopsLogger->addBlock($blockObj->getVar('name'), true, $bcachetime);
        if (!$display_none) {
            $template->display($tplName, $cacheid);
        }
    }
    $template->setCompileId($blockObj->getVar('dirname', 'n'));

    return null;
}
