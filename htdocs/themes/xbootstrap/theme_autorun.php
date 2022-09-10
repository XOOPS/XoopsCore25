<?php
xoops_load('XoopsFormRendererBootstrap3');
XoopsFormRenderer::getInstance()->set(new XoopsFormRendererBootstrap3());

/* Check if tinyMce 5 is seleected in system configuration */
$editor = xoops_getModuleOption('blocks_editor', 'system');
if ($editor != 'tinymce5') {
    $editor = xoops_getModuleOption('comments_editor', 'system');
    if ($editor != 'tinymce5') {
        $editor = xoops_getModuleOption('general_editor', 'system');
    }
}
if ($editor == 'tinymce5') {
    $GLOBALS['xoTheme']->addStylesheet( XOOPS_URL . '/class/xoopseditor/tinymce5/tinymce5/jscripts/tiny_mce/plugins/xoopscode/css/prism.css' );
    $GLOBALS['xoTheme']->addScript( XOOPS_URL . '/class/xoopseditor/tinymce5/tinymce5/jscripts/tiny_mce/plugins/xoopscode/js/prism.js' );
}