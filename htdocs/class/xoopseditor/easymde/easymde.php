<?php
/**
 * EasyMDE Markdown Editor for XOOPS
 *
 * A simple, embeddable Markdown editor based on EasyMDE.
 * @see https://github.com/Ionaru/easy-markdown-editor
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license         GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package         xoopseditor
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

xoops_load('XoopsEditor');

class FormEasyMDE extends XoopsEditor
{
    public $width  = '100%';
    public $height = '400px';

    /**
     * @param array $configs
     */
    public function __construct($configs = [])
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
     * @return string
     */
    public function render()
    {
        $name   = $this->getName();
        $value  = $this->getValue();
        $cols   = $this->getCols();
        $rows   = $this->getRows();
        $width  = $this->configs['width'] ?? $this->width;
        $height = $this->configs['height'] ?? $this->height;

        // Escape value for safe embedding in textarea
        $escapedValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        $editorPath = XOOPS_URL . $this->rootPath;

        $html = '';

        // CSS
        $html .= '<link rel="stylesheet" href="' . $editorPath . '/css/easymde.min.css">' . "\n";

        // Textarea (EasyMDE attaches to this)
        $html .= '<textarea id="' . $name . '" name="' . $name . '" '
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
        $html .= '    var easymde_' . $name . ' = new EasyMDE({' . "\n";
        $html .= '      element: document.getElementById("' . $name . '"),' . "\n";
        $html .= '      spellChecker: false,' . "\n";
        $html .= '      minHeight: "' . $height . '",' . "\n";
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
     * @return string
     */
    public function renderValidationJS()
    {
        if ($this->isRequired() && $eltname = $this->getName()) {
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
