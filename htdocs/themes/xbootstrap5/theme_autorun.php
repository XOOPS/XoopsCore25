<?php
xoops_load('XoopsFormRendererBootstrap5');
XoopsFormRenderer::getInstance()->set(new XoopsFormRendererBootstrap5());

// Comment on the 3 lines below if you don't want to overload the icons in the module menus and sub-menus.
global $xoopsTpl;
$iconOverrides = include __DIR__ . '/config/module_icons.php';
$xoopsTpl->assign('iconOverrides', $iconOverrides);
