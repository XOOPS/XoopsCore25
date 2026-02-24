<?php
/**
 * EasyMDE Markdown Editor for XOOPS
 *
 * A simple, embeddable Markdown editor based on EasyMDE.
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          editor
 * @since               2.5.12
 * @author              XOOPS Development Team
 * @see                 https://github.com/Ionaru/easy-markdown-editor
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

xoops_load('XoopsEditor');

/**
 * Class FormEasyMDE
 */
class FormEasyMDE extends XoopsEditor
{
    public string $width  = '100%';
    public string $height = '400px';

    /**
     * FormEasyMDE::__construct()
     *
     * @param array $configs
     */
    public function __construct(array $configs = [])
    {
        $this->rootPath = '/class/xoopseditor/easymde';
        parent::__construct($configs);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return is_readable(XOOPS_ROOT_PATH . $this->rootPath . '/js/easymde.min.js');
    }

    /**
     * FormEasyMDE::render()
     *
     * @return string
     */
    public function render()
    {
        $name   = $this->getName();
        $value  = $this->getValue();
        $cols   = (int) $this->getCols();
        $rows   = (int) $this->getRows();
        $width  = htmlspecialchars($this->configs['width'] ?? $this->width, ENT_QUOTES, 'UTF-8');
        $height = htmlspecialchars($this->configs['height'] ?? $this->height, ENT_QUOTES, 'UTF-8');

        $htmlName     = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $escapedValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $jsId         = json_encode($name, JSON_THROW_ON_ERROR);

        $editorPath = XOOPS_URL . $this->rootPath;

        $html = '';

        // CSS
        $html .= '<link rel="stylesheet" href="' . $editorPath . '/css/easymde.min.css">' . "\n";

        // Textarea (EasyMDE attaches to this)
        $html .= '<textarea id="' . $htmlName . '" name="' . $htmlName . '" '
               . 'cols="' . $cols . '" rows="' . $rows . '" '
               . 'style="width:' . $width . ';">'
               . $escapedValue
               . '</textarea>' . "\n";

        // JS
        $html .= '<script src="' . $editorPath . '/js/easymde.min.js"></script>' . "\n";

        // Initialize EasyMDE
        $html .= '<script>' . "\n";
        $html .= 'document.addEventListener("DOMContentLoaded", function() {' . "\n";
        $html .= '  if (typeof EasyMDE !== "undefined") {' . "\n";
        $html .= '    new EasyMDE({' . "\n";
        $html .= '      element: document.getElementById(' . $jsId . '),' . "\n";
        $html .= '      spellChecker: false,' . "\n";
        $html .= '      minHeight: ' . json_encode($this->configs['height'] ?? $this->height, JSON_THROW_ON_ERROR) . ',' . "\n";
        $html .= '      autoDownloadFontAwesome: true,' . "\n";
        $html .= '      forceSync: true,' . "\n";
        $html .= '      toolbar: [' . "\n";
        $html .= '        "bold", "italic", "heading", "|",' . "\n";
        $html .= '        "quote", "unordered-list", "ordered-list", "|",' . "\n";
        $html .= '        "link", "image", "table", "horizontal-rule", "|",' . "\n";
        $html .= '        "preview", "side-by-side", "fullscreen", "|",' . "\n";
        $html .= '        "guide"' . "\n";
        $html .= '      ],' . "\n";
        $html .= '      status: ["lines", "words", "cursor"]' . "\n";
        $html .= '    });' . "\n";
        $html .= '  }' . "\n";
        $html .= '});' . "\n";
        $html .= '</script>' . "\n";

        return $html;
    }

    /**
     * FormEasyMDE::renderValidationJS()
     *
     * @return string
     */
    public function renderValidationJS()
    {
        $eltname = $this->getName();
        if ($this->isRequired() && $eltname) {
            $eltcaption = $this->getCaption();
            $eltmsg     = empty($eltcaption)
                ? sprintf(_FORM_ENTER, $eltname)
                : sprintf(_FORM_ENTER, $eltcaption);
            $eltmsg = str_replace('"', '\"', stripslashes($eltmsg));

            return "\nif (document.getElementById('{$eltname}').value == '') "
                 . "{ window.alert(\"{$eltmsg}\"); return false; }";
        }

        return '';
    }
}
