<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 *  TinyMCE adapter for XOOPS
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          editor
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

xoops_load('XoopsEditor');

/**
 * Class XoopsFormTinymce
 */
class XoopsFormTinymce extends XoopsEditor
{
    public $config;
    public $language;
    public $width  = '100%';
    public $height = '500px';

    public $editor;

    /**
     * Constructor
     *
     * @param array $configs Editor Options
     */
    public function __construct($configs)
    {
        $current_path = __FILE__;
        if (DIRECTORY_SEPARATOR !== '/') {
            $current_path = str_replace(strpos($current_path, "\\\\", 2) ? "\\\\" : DIRECTORY_SEPARATOR, '/', $current_path);
        }

        $this->rootPath = '/class/xoopseditor/tinymce';
        parent::__construct($configs);
        $this->configs['elements']    = $this->getName();
        $this->configs['language']    = $this->getLanguage();
        $this->configs['rootpath']    = $this->rootPath;
        $this->configs['area_width']  = $this->configs['width'] ?? $this->width;
        $this->configs['area_height'] = $this->configs['height'] ?? $this->height;
        $this->configs['fonts']       = $this->getFonts();

        require_once __DIR__ . '/tinymce.php';
        $this->editor = new TinyMCE($this->configs);
    }

    /**
     * Renders the Javascript function needed for client-side for validation
     *
     * I'VE USED THIS EXAMPLE TO WRITE VALIDATION CODE
     * http://tinymce.moxiecode.com/punbb/viewtopic.php?id=12616
     *
     * @return string
     */
    public function renderValidationJS()
    {
        if ($this->isRequired() && $eltname = $this->getName()) {
            //$eltname = $this->getName();
            $eltcaption = $this->getCaption();
            $eltmsg     = empty($eltcaption) ? sprintf(_FORM_ENTER, $eltname) : sprintf(_FORM_ENTER, $eltcaption);
            $eltmsg     = str_replace('"', '\"', stripslashes($eltmsg));
            $ret        = "\n";
            $ret .= "if ( tinyMCE.get('{$eltname}').getContent() == \"\" || tinyMCE.get('{$eltname}').getContent() == null) ";
            $ret .= "{ window.alert(\"{$eltmsg}\"); tinyMCE.get('{$eltname}').focus(); return false; }";

            return $ret;
        }

        return '';
    }

    /**
     * get language
     *
     * @return string
     */
    public function getLanguage()
    {
        if ($this->language) {
            return $this->language;
        }
        if (defined('_XOOPS_EDITOR_TINYMCE_LANGUAGE')) {
            $this->language = strtolower(constant('_XOOPS_EDITOR_TINYMCE_LANGUAGE'));
        } else {
            $this->language = str_replace('_', '-', strtolower(_LANGCODE));
            if (strtolower(_CHARSET) === 'utf-8') {
                $this->language .= '_utf8';
            }
        }

        return $this->language;
    }

    /**
     * @return mixed
     */
    public function getFonts()
    {
        if (empty($this->config['fonts']) && defined('_XOOPS_EDITOR_TINYMCE_FONTS')) {
            $this->config['fonts'] = constant('_XOOPS_EDITOR_TINYMCE_FONTS');
        }

        return $this->config['fonts'] ?? null;
    }

    /**
     * prepare HTML for output
     *
     * @return string HTML
     */
    public function render()
    {
        $ret = $this->editor->render();
        $ret .= parent::render();

        return $ret;
    }

    /**
     * Check if compatible
     *
     * @return bool
     */
    public function isActive()
    {
        return is_readable(XOOPS_ROOT_PATH . $this->rootPath . '/tinymce.php');
    }
}
