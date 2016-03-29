<?php
/**
 * XOOPS feed creator
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
 * @since               2.0.0
 */

include __DIR__ . '/mainfile.php';

$GLOBALS['xoopsLogger']->activated = false;
if (function_exists('mb_http_output')) {
    mb_http_output('pass');
}
header('Content-Type:text/xml; charset=utf-8');

include_once $GLOBALS['xoops']->path('class/template.php');
$tpl                 = new XoopsTpl();
$tpl->caching        = 2;
$tpl->cache_lifetime = 3600;
if (!$tpl->is_cached('db:system_rss.tpl')) {
    xoops_load('XoopsLocal');
    $tpl->assign('channel_title', XoopsLocal::convert_encoding(htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES)));
    $tpl->assign('channel_link', XOOPS_URL . '/');
    $tpl->assign('channel_desc', XoopsLocal::convert_encoding(htmlspecialchars($xoopsConfig['slogan'], ENT_QUOTES)));
    $tpl->assign('channel_lastbuild', formatTimestamp(time(), 'rss'));
    $tpl->assign('channel_webmaster', checkEmail($xoopsConfig['adminmail'], true));
    $tpl->assign('channel_editor', checkEmail($xoopsConfig['adminmail'], true));
    $tpl->assign('channel_category', 'News');
    $tpl->assign('channel_generator', 'XOOPS');
    $tpl->assign('channel_language', _LANGCODE);
    $tpl->assign('image_url', XOOPS_URL . '/images/logo.png');
    $dimension = getimagesize(XOOPS_ROOT_PATH . '/images/logo.png');
    if (empty($dimension[0])) {
        $width = 88;
    } else {
        $width = ($dimension[0] > 144) ? 144 : $dimension[0];
    }
    if (empty($dimension[1])) {
        $height = 31;
    } else {
        $height = ($dimension[1] > 400) ? 400 : $dimension[1];
    }
    $tpl->assign('image_width', $width);
    $tpl->assign('image_height', $height);
    if (file_exists($fileinc = $GLOBALS['xoops']->path('modules/news/class/class.newsstory.php'))) {
        include $fileinc;
        $sarray = NewsStory::getAllPublished(10, 0, true);
    }
    if (!empty($sarray) && is_array($sarray)) {
        foreach ($sarray as $story) {
            $tpl->append('items', array(
                'title'       => XoopsLocal::convert_encoding(htmlspecialchars($story->title(), ENT_QUOTES)),
                'link'        => XOOPS_URL . '/modules/news/article.php?storyid=' . $story->storyid(),
                'guid'        => XOOPS_URL . '/modules/news/article.php?storyid=' . $story->storyid(),
                'pubdate'     => formatTimestamp($story->published(), 'rss'),
                'description' => XoopsLocal::convert_encoding(htmlspecialchars($story->hometext(), ENT_QUOTES))));
        }
    }
}
$tpl->display('db:system_rss.tpl');
