<?php

xoops_load('XoopsFormRendererBootstrap5');
XoopsFormRenderer::getInstance()->set(new XoopsFormRendererBootstrap5());

global $xoopsTpl;
$iconOverrides = include __DIR__ . '/config/module_icons.php';
$xoopsTpl->assign('iconOverrides', $iconOverrides);
