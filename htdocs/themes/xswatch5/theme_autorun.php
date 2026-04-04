<?php
xoops_load('XoopsFormRendererBootstrap5');
XoopsFormRenderer::getInstance()->set(new XoopsFormRendererBootstrap5());

/** @var XoopsTpl */
global $xoopsTpl;
if(!empty($xoopsTpl)) {
    $xoopsTpl->addConfigDir(__DIR__);
}

/* Check if tinyMce 5 is selected in system configuration */
/** @var XoopsConfigHandler $configHandler */
$configHandler = xoops_getHandler('config');
/** @var XoopsModuleHandler $moduleHandler */
$moduleHandler = xoops_getHandler('module');
$systemModule  = $moduleHandler->getByDirname('system');
$editor        = '';
if (is_object($systemModule)) {
    $systemConfig = $configHandler->getConfigsByCat(0, $systemModule->getVar('mid'));
    foreach (['blocks_editor', 'comments_editor', 'general_editor'] as $key) {
        if (isset($systemConfig[$key]) && $systemConfig[$key] === 'tinymce5') {
            $editor = 'tinymce5';
            break;
        }
    }
}
if ($editor === 'tinymce5' && isset($GLOBALS['xoTheme'])) {
    $GLOBALS['xoTheme']->addStylesheet(XOOPS_URL . '/class/xoopseditor/tinymce5/tinymce5/jscripts/tiny_mce/plugins/xoopscode/css/prism.css');
    $GLOBALS['xoTheme']->addScript(XOOPS_URL . '/class/xoopseditor/tinymce5/tinymce5/jscripts/tiny_mce/plugins/xoopscode/js/prism.js');
}
