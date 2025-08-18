<?php
/**
 *  TinyMCE adapter for XOOPS
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          editor
 * @since               2.3.0
 * @author              Laurent JEN <dugris@frxoops.org>
 */

if (!defined('XOOPS_ROOT_PATH')) {
    throw new \RuntimeException('XOOPS root path not defined');
}

// Xlanguage
if ($GLOBALS['module_handler']->getByDirname('xlanguage') && defined('XLANGUAGE_LANG_TAG')) {
    return true;
}

// Easiest Multi-Language Hack (EMLH)
return defined('EASIESTML_LANGS') && defined('EASIESTML_LANGNAMES');
