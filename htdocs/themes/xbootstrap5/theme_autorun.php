<?php

xoops_load('XoopsFormRendererBootstrap5');
XoopsFormRenderer::getInstance()->set(new XoopsFormRendererBootstrap5());

global $xoopsTpl, $xoTheme;
if (!empty($xoopsTpl)) {
	$xoopsTpl->addConfigDir(__DIR__);
}

if (null === $xoopsTpl){
	require_once XOOPS_ROOT_PATH . '/class/template.php';
	$xoopsTpl = new \XoopsTpl();
}

$iconOverrides = include __DIR__ . '/config/module_icons.php';

if (null !== $xoopsTpl) {
    $xoopsTpl->assign('iconOverrides', $iconOverrides);
}