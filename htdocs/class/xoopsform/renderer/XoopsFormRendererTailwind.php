<?php
/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Tailwind CSS + DaisyUI form renderer
 *
 * Renders XOOPS form elements using Tailwind CSS utility classes combined with
 * DaisyUI component classes (.btn, .input, .select, .textarea, .checkbox, .radio,
 * .label, .form-control, etc.). Designed to work out of the box with any theme
 * that includes Tailwind CSS + DaisyUI — no theme-specific overrides required.
 *
 * The output uses DaisyUI semantic colors (primary, secondary, success, warning,
 * error, info) and theme-aware base colors (base-100, base-200, base-content)
 * so rendered forms automatically match whichever DaisyUI theme is active.
 *
 * @category  XoopsForm
 * @package   XoopsFormRendererTailwind
 * @author    XOOPS Project
 * @copyright 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 * @see       https://daisyui.com/components/
 */
class XoopsFormRendererTailwind implements XoopsFormRendererInterface
{
    /**
     * Render support for XoopsFormButton
     *
     * @param XoopsFormButton $element form element
     *
     * @return string rendered form element
     */
    public function renderFormButton(XoopsFormButton $element)
    {
        return '<button type="' . $element->getType() . '"'
            . ' class="btn btn-neutral" name="' . $element->getName() . '"'
            . ' id="' . $element->getName() . '" title="' . $element->getValue() . '"'
            . ' value="' . $element->getValue() . '"'
            . $element->getExtra() . '>' . $element->getValue() . '</button>';
    }

    /**
     * Render support for XoopsFormButtonTray
     *
     * @param XoopsFormButtonTray $element form element
     *
     * @return string rendered form element
     */
    public function renderFormButtonTray(XoopsFormButtonTray $element)
    {
        $ret = '<div class="flex flex-wrap gap-2">';
        if ($element->_showDelete) {
            $ret .= '<button type="submit" class="btn btn-error" name="delete" id="delete"'
                . ' onclick="this.form.elements.op.value=\'delete\'">' . _DELETE . '</button>';
        }
        $ret .= '<input type="button" class="btn btn-error" name="cancel" id="cancel"'
            . ' onClick="history.go(-1);return true;" value="' . _CANCEL . '">'
            . '<button type="reset" class="btn btn-warning" name="reset" id="reset">' . _RESET . '</button>'
            . '<button type="' . $element->getType() . '" class="btn btn-success" name="' . $element->getName()
            . '" id="' . $element->getName() . '" ' . $element->getExtra()
            . '>' . $element->getValue() . '</button>'
            . '</div>';

        return $ret;
    }

    /**
     * Render support for XoopsFormCheckBox
     *
     * @param XoopsFormCheckBox $element form element
     *
     * @return string rendered form element
     */
    public function renderFormCheckBox(XoopsFormCheckBox $element)
    {
        $elementName = $element->getName();
        $elementId = $elementName;
        $elementOptions = $element->getOptions();
        if (count($elementOptions) > 1 && substr($elementName, -2, 2) !== '[]') {
            $elementName .= '[]';
            $element->setName($elementName);
        }

        switch ((int) ($element->columns)) {
            case 0:
                return $this->renderCheckedInline($element, 'checkbox', $elementId, $elementName);
            case 1:
                return $this->renderCheckedOneColumn($element, 'checkbox', $elementId, $elementName);
            default:
                return $this->renderCheckedColumnar($element, 'checkbox', $elementId, $elementName);
        }
    }

    /**
     * Render an inline checkbox or radio element
     *
     * @param XoopsFormCheckBox|XoopsFormRadio $element     element being rendered
     * @param string                           $type        'checkbox' or 'radio'
     * @param string                           $elementId   input 'id' attribute of element
     * @param string                           $elementName input 'name' attribute of element
     * @return string
     */
    protected function renderCheckedInline($element, $type, $elementId, $elementName)
    {
        $ret = '<div class="flex flex-wrap gap-4">';
        $idSuffix = 0;
        $elementValue = $element->getValue();
        foreach ($element->getOptions() as $value => $name) {
            ++$idSuffix;
            $ret .= '<label class="label cursor-pointer gap-2">';
            $ret .= "<input class='" . $type . " " . $type . "-primary' type='" . $type . "'"
                . " name='{$elementName}' id='{$elementId}{$idSuffix}' title='"
                . htmlspecialchars(strip_tags($name), ENT_QUOTES | ENT_HTML5) . "' value='"
                . htmlspecialchars($value, ENT_QUOTES | ENT_HTML5) . "'";
            if (is_array($elementValue) ? in_array($value, $elementValue) : $value == $elementValue) {
                $ret .= ' checked';
            }
            $ret .= $element->getExtra() . '>';
            $ret .= '<span class="label-text">' . $name . $element->getDelimeter() . '</span>';
            $ret .= '</label>';
        }
        $ret .= '</div>';

        return $ret;
    }

    /**
     * Render a single column checkbox or radio element
     *
     * @param XoopsFormCheckBox|XoopsFormRadio $element     element being rendered
     * @param string                           $type        'checkbox' or 'radio'
     * @param string                           $elementId   input 'id' attribute of element
     * @param string                           $elementName input 'name' attribute of element
     * @return string
     */
    protected function renderCheckedOneColumn($element, $type, $elementId, $elementName)
    {
        $ret = '<div class="flex flex-col gap-2">';
        $idSuffix = 0;
        $elementValue = $element->getValue();
        foreach ($element->getOptions() as $value => $name) {
            ++$idSuffix;
            $ret .= '<label class="label cursor-pointer justify-start gap-2">';
            $ret .= "<input class='" . $type . " " . $type . "-primary' type='" . $type . "'"
                . " name='{$elementName}' id='{$elementId}{$idSuffix}' title='"
                . htmlspecialchars(strip_tags($name), ENT_QUOTES | ENT_HTML5) . "' value='"
                . htmlspecialchars($value, ENT_QUOTES | ENT_HTML5) . "'";
            if (is_array($elementValue) ? in_array($value, $elementValue) : $value == $elementValue) {
                $ret .= ' checked';
            }
            $ret .= $element->getExtra() . '>';
            $ret .= '<span class="label-text">' . $name . $element->getDelimeter() . '</span>';
            $ret .= '</label>';
        }
        $ret .= '</div>';

        return $ret;
    }

    /**
     * Render a multicolumn checkbox or radio element
     *
     * @param XoopsFormCheckBox|XoopsFormRadio $element     element being rendered
     * @param string                           $type        'checkbox' or 'radio'
     * @param string                           $elementId   input 'id' attribute of element
     * @param string                           $elementName input 'name' attribute of element
     * @return string
     */
    protected function renderCheckedColumnar($element, $type, $elementId, $elementName)
    {
        $ret = '<div class="grid grid-cols-2 md:grid-cols-3 gap-2">';
        $idSuffix = 0;
        $elementValue = $element->getValue();
        foreach ($element->getOptions() as $value => $name) {
            ++$idSuffix;
            $ret .= '<label class="label cursor-pointer justify-start gap-2">';
            $ret .= "<input class='" . $type . " " . $type . "-primary' type='" . $type . "'"
                . " name='{$elementName}' id='{$elementId}{$idSuffix}' title='"
                . htmlspecialchars(strip_tags($name), ENT_QUOTES | ENT_HTML5) . "' value='"
                . htmlspecialchars($value, ENT_QUOTES | ENT_HTML5) . "'";
            if (is_array($elementValue) ? in_array($value, $elementValue) : $value == $elementValue) {
                $ret .= ' checked';
            }
            $ret .= $element->getExtra() . '>';
            $ret .= '<span class="label-text">' . $name . $element->getDelimeter() . '</span>';
            $ret .= '</label>';
        }
        $ret .= '</div>';

        return $ret;
    }

    /**
     * Render support for XoopsFormColorPicker
     *
     * @param XoopsFormColorPicker $element form element
     *
     * @return string rendered form element
     */
    public function renderFormColorPicker(XoopsFormColorPicker $element)
    {
        if (isset($GLOBALS['xoTheme'])) {
            $GLOBALS['xoTheme']->addScript('include/spectrum.js');
            $GLOBALS['xoTheme']->addStylesheet('include/spectrum.css');
        } else {
            echo '<script type="text/javascript" src="' . XOOPS_URL . '/include/spectrum.js"></script>';
            echo '<link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/include/spectrum.css">';
        }
        return '<input class="input input-bordered w-24 h-10 p-1" type="color" name="' . $element->getName()
            . '" title="' . $element->getTitle() . '" id="' . $element->getName()
            . '" size="7" maxlength="7" value="' . $element->getValue() . '"' . $element->getExtra() . '>';
    }

    /**
     * Render support for XoopsFormDhtmlTextArea
     *
     * @param XoopsFormDhtmlTextArea $element form element
     *
     * @return string rendered form element
     */
    public function renderFormDhtmlTextArea(XoopsFormDhtmlTextArea $element)
    {
        xoops_loadLanguage('formdhtmltextarea');
        $ret = '';
        $ret .= $this->renderFormDhtmlTAXoopsCode($element) . "<br>\n";
        $ret .= $this->renderFormDhtmlTATypography($element);
        $ret .= "<br>\n";
        $ret .= "<textarea class='textarea textarea-bordered w-full font-mono' id='" . $element->getName() . "' name='" . $element->getName()
            . "' title='" . $element->getTitle() . "' onselect=\"xoopsSavePosition('" . $element->getName()
            . "');\" onclick=\"xoopsSavePosition('" . $element->getName()
            . "');\" onkeyup=\"xoopsSavePosition('" . $element->getName() . "');\" cols='"
            . $element->getCols() . "' rows='" . $element->getRows() . "'" . $element->getExtra()
            . '>' . $element->getValue() . "</textarea>\n";

        if (empty($element->skipPreview)) {
            if (empty($GLOBALS['xoTheme'])) {
                $element->js .= implode('', file(XOOPS_ROOT_PATH . '/class/textsanitizer/image/image.js'));
            } else {
                $GLOBALS['xoTheme']->addScript(
                    '/class/textsanitizer/image/image.js',
                    ['type' => 'text/javascript'],
                );
            }
            $button = "<button type='button' class='btn btn-primary btn-sm' onclick=\"form_instantPreview('" . XOOPS_URL
                . "', '" . $element->getName() . "','" . XOOPS_URL . "/images', " . (int) $element->doHtml . ", '"
                . $GLOBALS['xoopsSecurity']->createToken() . "')\" title='" . _PREVIEW . "'>" . _PREVIEW . "</button>";

            $ret .= '<br>' . "<div id='" . $element->getName() . "_hidden' class='card bg-base-200 mt-2'>"
                . "<div class='card-body p-4'>"
                . "<div class='card-title text-sm'>" . $button . "</div>"
                . "<div id='" . $element->getName() . "_hidden_data'>" . _XOOPS_FORM_PREVIEW_CONTENT . '</div>'
                . '</div></div>';
        }
        $javascript_file = XOOPS_URL . '/include/formdhtmltextarea.js';
        $javascript_file_element = 'include_formdhtmltextarea_js';
        $javascript = ($element->js ? '<script type="text/javascript">' . $element->js . '</script>' : '');
        $javascript .= <<<EOJS
<script>
    var el = document.getElementById('{$javascript_file_element}');
    if (el === null) {
        var xformtag = document.createElement('script');
        xformtag.id = '{$javascript_file_element}';
        xformtag.type = 'text/javascript';
        xformtag.src = '{$javascript_file}';
        document.body.appendChild(xformtag);
    }
</script>
EOJS;

        return $javascript . $ret;
    }

    /**
     * Render xoopscode buttons for editor, include calling text sanitizer extensions
     *
     * @param XoopsFormDhtmlTextArea $element form element
     *
     * @return string rendered buttons for xoopscode assistance
     */
    protected function renderFormDhtmlTAXoopsCode(XoopsFormDhtmlTextArea $element)
    {
        $textarea_id = $element->getName();
        $code = "<div class='flex flex-wrap gap-1'>";
        $btn = "btn btn-neutral btn-sm";
        $code .= "<button type='button' class='{$btn}' onclick='xoopsCodeUrl(\"{$textarea_id}\", \"" . htmlspecialchars(_ENTERURL, ENT_QUOTES | ENT_HTML5) . "\", \"" . htmlspecialchars(_ENTERWEBTITLE, ENT_QUOTES | ENT_HTML5) . "\");' title='" . _XOOPS_FORM_ALT_URL . "'><span class='fa-solid fa-link' aria-hidden='true'></span></button>";
        $code .= "<button type='button' class='{$btn}' onclick='xoopsCodeEmail(\"{$textarea_id}\", \"" . htmlspecialchars(_ENTEREMAIL, ENT_QUOTES | ENT_HTML5) . "\", \"" . htmlspecialchars(_ENTERWEBTITLE, ENT_QUOTES | ENT_HTML5) . "\");' title='" . _XOOPS_FORM_ALT_EMAIL . "'><span class='fa-solid fa-envelope' aria-hidden='true'></span></button>";
        $code .= "<button type='button' class='{$btn}' onclick='xoopsCodeImg(\"{$textarea_id}\", \"" . htmlspecialchars(_ENTERIMGURL, ENT_QUOTES | ENT_HTML5) . "\", \"" . htmlspecialchars(_ENTERIMGPOS, ENT_QUOTES | ENT_HTML5) . "\", \"" . htmlspecialchars(_IMGPOSRORL, ENT_QUOTES | ENT_HTML5) . "\", \"" . htmlspecialchars(_ERRORIMGPOS, ENT_QUOTES | ENT_HTML5) . "\", \"" . htmlspecialchars(_XOOPS_FORM_ALT_ENTERWIDTH, ENT_QUOTES | ENT_HTML5) . "\");' title='" . _XOOPS_FORM_ALT_IMG . "'><span class='fa-solid fa-file-image' aria-hidden='true'></span></button>";
        $code .= "<button type='button' class='{$btn}' onclick='openWithSelfMain(\"" . XOOPS_URL . "/imagemanager.php?target={$textarea_id}\",\"imgmanager\",400,430);' title='" . _XOOPS_FORM_ALT_IMAGE . "'><span class='fa-solid fa-file-image' aria-hidden='true'></span><small> Manager</small></button>";
        $code .= "<button type='button' class='{$btn}' onclick='openWithSelfMain(\"" . XOOPS_URL . "/misc.php?action=showpopups&amp;type=smilies&amp;target={$textarea_id}\",\"smilies\",300,475);' title='" . _XOOPS_FORM_ALT_SMILEY . "'><span class='fa-solid fa-face-smile' aria-hidden='true'></span></button>";

        $myts = \MyTextSanitizer::getInstance();
        $extensions = array_filter($myts->config['extensions']);
        foreach (array_keys($extensions) as $key) {
            $extension = $myts->loadExtension($key);
            $result = $extension->encode($textarea_id);
            $encode = $result[0] ?? '';
            $js     = $result[1] ?? '';
            if (empty($encode)) {
                continue;
            }
            // Extensions output Bootstrap classes — remap the common ones to DaisyUI.
            $encode = str_replace(['btn-default', 'btn-secondary'], 'btn btn-neutral btn-sm', $encode);

            $code .= $encode;
            if (!empty($js)) {
                $element->js .= $js;
            }
        }
        $code .= "<button type='button' class='{$btn}' onclick='xoopsCodeCode(\"{$textarea_id}\", \"" . htmlspecialchars(_ENTERCODE, ENT_QUOTES | ENT_HTML5) . "\");' title='" . _XOOPS_FORM_ALT_CODE . "'><span class='fa-solid fa-code' aria-hidden='true'></span></button>";
        $code .= "<button type='button' class='{$btn}' onclick='xoopsCodeQuote(\"{$textarea_id}\", \"" . htmlspecialchars(_ENTERQUOTE, ENT_QUOTES | ENT_HTML5) . "\");' title='" . _XOOPS_FORM_ALT_QUOTE . "'><span class='fa-solid fa-quote-right' aria-hidden='true'></span></button>";
        $code .= "</div>";

        $xoopsPreload = XoopsPreload::getInstance();
        $xoopsPreload->triggerEvent('core.class.xoopsform.formdhtmltextarea.codeicon', [&$code]);

        return $code;
    }

    /**
     * Render typography controls for editor (font, size, color)
     *
     * @param XoopsFormDhtmlTextArea $element form element
     *
     * @return string rendered typography controls
     */
    protected function renderFormDhtmlTATypography(XoopsFormDhtmlTextArea $element)
    {
        $textarea_id = $element->getName();
        $hiddentext  = $element->_hiddenText;

        $fontarray = !empty($GLOBALS['formtextdhtml_fonts']) ? $GLOBALS['formtextdhtml_fonts'] : [
            'Arial', 'Courier', 'Georgia', 'Helvetica', 'Impact', 'Verdana', 'Haettenschweiler',
        ];

        $colorArray = [
            'Black'  => '000000', 'Blue'   => '38AAFF', 'Brown'  => '987857',
            'Green'  => '79D271', 'Grey'   => '888888', 'Orange' => 'FFA700',
            'Paper'  => 'E0E0E0', 'Purple' => '363E98', 'Red'    => 'FF211E',
            'White'  => 'FEFEFE', 'Yellow' => 'FFD628',
        ];

        $btn = "btn btn-neutral btn-sm";
        $menuCls = "dropdown-content menu bg-base-100 rounded-box z-50 p-2 shadow max-h-64 overflow-y-auto flex-nowrap";

        $fontStr = "<div class='flex flex-wrap gap-1 mt-2'>";

        // Size dropdown
        $fontStr .= "<div class='dropdown'>"
            . "<div tabindex='0' role='button' class='{$btn}' title='" . _SIZE . "'><span class='fa-solid fa-text-height'></span></div>"
            . "<ul tabindex='0' class='{$menuCls}'>";
        foreach ($GLOBALS['formtextdhtml_sizes'] as $value => $name) {
            $fontStr .= "<li><a href=\"javascript:xoopsSetElementAttribute('size', '{$value}', '{$textarea_id}', '{$hiddentext}');\">{$name}</a></li>";
        }
        $fontStr .= "</ul></div>";

        // Font dropdown
        $fontStr .= "<div class='dropdown'>"
            . "<div tabindex='0' role='button' class='{$btn}' title='" . _FONT . "'><span class='fa-solid fa-font'></span></div>"
            . "<ul tabindex='0' class='{$menuCls}'>";
        foreach ($fontarray as $font) {
            $fontStr .= "<li><a href=\"javascript:xoopsSetElementAttribute('font', '{$font}', '{$textarea_id}', '{$hiddentext}');\">{$font}</a></li>";
        }
        $fontStr .= "</ul></div>";

        // Color dropdown
        $fontStr .= "<div class='dropdown'>"
            . "<div tabindex='0' role='button' class='{$btn}' title='" . _COLOR . "'><span class='fa-solid fa-palette'></span></div>"
            . "<ul tabindex='0' class='{$menuCls}'>";
        foreach ($colorArray as $color => $hex) {
            $fontStr .= "<li><a href=\"javascript:xoopsSetElementAttribute('color', '{$hex}', '{$textarea_id}', '{$hiddentext}');\"><span style=\"color:#{$hex};\">{$color}</span></a></li>";
        }
        $fontStr .= "</ul></div>";

        // Style buttons
        $fontStr .= "<div class='join'>";
        $fontStr .= "<button type='button' class='btn btn-neutral btn-sm join-item' onclick='xoopsMakeBold(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_BOLD . "'><span class='fa-solid fa-bold'></span></button>";
        $fontStr .= "<button type='button' class='btn btn-neutral btn-sm join-item' onclick='xoopsMakeItalic(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_ITALIC . "'><span class='fa-solid fa-italic'></span></button>";
        $fontStr .= "<button type='button' class='btn btn-neutral btn-sm join-item' onclick='xoopsMakeUnderline(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_UNDERLINE . "'><span class='fa-solid fa-underline'></span></button>";
        $fontStr .= "<button type='button' class='btn btn-neutral btn-sm join-item' onclick='xoopsMakeLineThrough(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_LINETHROUGH . "'><span class='fa-solid fa-strikethrough'></span></button>";
        $fontStr .= "</div>";

        // Align buttons
        $fontStr .= "<div class='join'>";
        $fontStr .= "<button type='button' class='btn btn-neutral btn-sm join-item' onclick='xoopsMakeLeft(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_LEFT . "'><span class='fa-solid fa-align-left'></span></button>";
        $fontStr .= "<button type='button' class='btn btn-neutral btn-sm join-item' onclick='xoopsMakeCenter(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_CENTER . "'><span class='fa-solid fa-align-center'></span></button>";
        $fontStr .= "<button type='button' class='btn btn-neutral btn-sm join-item' onclick='xoopsMakeRight(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_RIGHT . "'><span class='fa-solid fa-align-right'></span></button>";
        $fontStr .= "</div>";

        // Length check button
        $maxlength = $element->configs['maxlength'] ?? 0;
        $fontStr .= "<button type='button' class='{$btn}' onclick=\"XoopsCheckLength('"
            . $element->getName() . "', '" . $maxlength . "', '"
            . _XOOPS_FORM_ALT_LENGTH . "', '" . _XOOPS_FORM_ALT_LENGTH_MAX . "');\" title='"
            . _XOOPS_FORM_ALT_CHECKLENGTH . "'><span class='fa-solid fa-square-check'></span></button>";
        $fontStr .= "</div>";

        return $fontStr;
    }

    /**
     * Render support for XoopsFormElementTray
     *
     * @param XoopsFormElementTray $element form element
     *
     * @return string rendered form element
     */
    public function renderFormElementTray(XoopsFormElementTray $element)
    {
        $count = 0;
        $inline = (\XoopsFormElementTray::ORIENTATION_VERTICAL === $element->getOrientation());
        $ret = $inline ? '<div class="flex flex-wrap items-center gap-2">' : '<div class="space-y-2">';
        foreach ($element->getElements() as $ele) {
            if ($count > 0 && !$inline) {
                $ret .= $element->getDelimeter();
            }
            if ($inline) {
                $ret .= '<span class="inline-flex items-center gap-1">';
            }
            if ($ele->getCaption() != '') {
                $ret .= '<label for="' . $ele->getName() . '" class="label-text">'
                    . $ele->getCaption()
                    . ($ele->isRequired() ? '<span class="text-error ms-1">*</span>' : '')
                    . '</label>&nbsp;';
            }
            $ret .= $ele->render() . NWLINE;
            if ($inline) {
                $ret .= '</span>';
            }
            if (!$ele->isHidden()) {
                ++$count;
            }
        }
        $ret .= '</div>';

        return $ret;
    }

    /**
     * Render support for XoopsFormFile
     *
     * @param XoopsFormFile $element form element
     *
     * @return string rendered form element
     */
    public function renderFormFile(XoopsFormFile $element)
    {
        return '<input type="hidden" name="MAX_FILE_SIZE" value="' . $element->getMaxFileSize() . '">'
            . '<input type="file" class="file-input file-input-bordered w-full" name="' . $element->getName()
            . '" id="' . $element->getName()
            . '" title="' . $element->getTitle() . '" ' . $element->getExtra() . '>'
            . '<input type="hidden" name="xoops_upload_file[]" id="xoops_upload_file[]" value="'
            . $element->getName() . '">';
    }

    /**
     * Render support for XoopsFormLabel
     *
     * @param XoopsFormLabel $element form element
     *
     * @return string rendered form element
     */
    public function renderFormLabel(XoopsFormLabel $element)
    {
        return '<label class="label label-text" id="' . $element->getName() . '">' . $element->getValue();
    }

    /**
     * Render support for XoopsFormPassword
     *
     * @param XoopsFormPassword $element form element
     *
     * @return string rendered form element
     */
    public function renderFormPassword(XoopsFormPassword $element)
    {
        return '<input class="input input-bordered w-full" type="password" name="'
            . $element->getName() . '" id="' . $element->getName() . '" size="' . $element->getSize()
            . '" maxlength="' . $element->getMaxlength() . '" value="' . $element->getValue() . '"'
            . $element->getExtra() . ' ' . ($element->autoComplete ? '' : 'autocomplete="off" ') . '/>';
    }

    /**
     * Render support for XoopsFormRadio
     *
     * @param XoopsFormRadio $element form element
     *
     * @return string rendered form element
     */
    public function renderFormRadio(XoopsFormRadio $element)
    {
        $elementName = $element->getName();
        $elementId = $elementName;

        switch ((int) ($element->columns)) {
            case 0:
                return $this->renderCheckedInline($element, 'radio', $elementId, $elementName);
            case 1:
                return $this->renderCheckedOneColumn($element, 'radio', $elementId, $elementName);
            default:
                return $this->renderCheckedColumnar($element, 'radio', $elementId, $elementName);
        }
    }

    /**
     * Render support for XoopsFormSelect
     *
     * @param XoopsFormSelect $element form element
     *
     * @return string rendered form element
     */
    public function renderFormSelect(XoopsFormSelect $element)
    {
        $ele_name    = $element->getName();
        $ele_title   = $element->getTitle();
        $ele_value   = $element->getValue();
        $ele_options = $element->getOptions();
        $ret = '<select class="select select-bordered w-full" size="'
            . $element->getSize() . '"' . $element->getExtra();
        if ($element->isMultiple() != false) {
            $ret .= ' name="' . $ele_name . '[]" id="' . $ele_name . '" title="' . $ele_title
                . '" multiple="multiple">';
        } else {
            $ret .= ' name="' . $ele_name . '" id="' . $ele_name . '" title="' . $ele_title . '">';
        }
        foreach ($ele_options as $value => $name) {
            $ret .= '<option value="' . htmlspecialchars($value, ENT_QUOTES | ENT_HTML5) . '"';
            if (count($ele_value) > 0 && in_array($value, $ele_value)) {
                $ret .= ' selected';
            }
            $ret .= '>' . $name . '</option>';
        }
        $ret .= '</select>';

        return $ret;
    }

    /**
     * Render support for XoopsFormText
     *
     * @param XoopsFormText $element form element
     *
     * @return string rendered form element
     */
    public function renderFormText(XoopsFormText $element)
    {
        return "<input class='input input-bordered w-full' type='text' name='"
            . $element->getName() . "' title='" . $element->getTitle() . "' id='" . $element->getName()
            . "' size='" . $element->getSize() . "' maxlength='" . $element->getMaxlength()
            . "' value='" . $element->getValue() . "'" . $element->getExtra() . '>';
    }

    /**
     * Render support for XoopsFormTextArea
     *
     * @param XoopsFormTextArea $element form element
     *
     * @return string rendered form element
     */
    public function renderFormTextArea(XoopsFormTextArea $element)
    {
        return "<textarea class='textarea textarea-bordered w-full' name='"
            . $element->getName() . "' id='" . $element->getName() . "' title='" . $element->getTitle()
            . "' rows='" . $element->getRows() . "' cols='" . $element->getCols() . "'"
            . $element->getExtra() . '>' . $element->getValue() . '</textarea>';
    }

    /**
     * Render support for XoopsFormTextDateSelect
     *
     * @param XoopsFormTextDateSelect $element form element
     *
     * @return string rendered form element
     */
    public function renderFormTextDateSelect(XoopsFormTextDateSelect $element)
    {
        static $included = false;
        if (file_exists(XOOPS_ROOT_PATH . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/calendar.php')) {
            include_once XOOPS_ROOT_PATH . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/calendar.php';
        } else {
            include_once XOOPS_ROOT_PATH . '/language/english/calendar.php';
        }

        $ele_name  = $element->getName();
        $ele_value = $element->getValue(false);
        if (is_string($ele_value)) {
            $display_value = $ele_value;
            $ele_value     = time();
        } elseif ($ele_value === 0) {
            $display_value = '';
            $ele_value     = time();
        } else {
            $display_value = date(_SHORTDATESTRING, $ele_value);
        }

        $jstime = formatTimestamp($ele_value, 'm/d/Y');
        if (isset($GLOBALS['xoTheme']) && is_object($GLOBALS['xoTheme'])) {
            $GLOBALS['xoTheme']->addScript('include/calendar.js');
            $GLOBALS['xoTheme']->addStylesheet('include/calendar-blue.css');
            if (!$included) {
                $included = true;
                $GLOBALS['xoTheme']->addScript('', '', '
                    var calendar = null;
                    function selected(cal, date) { cal.sel.value = date; }
                    function closeHandler(cal) {
                        cal.hide();
                        Calendar.removeEvent(document, "mousedown", checkCalendar);
                    }
                    function checkCalendar(ev) {
                        var el = Calendar.is_ie ? Calendar.getElement(ev) : Calendar.getTargetElement(ev);
                        for (; el != null; el = el.parentNode)
                            if (el == calendar.element || el.tagName == "A") break;
                        if (el == null) { calendar.callCloseHandler(); Calendar.stopEvent(ev); }
                    }
                    function showCalendar(id) {
                        var el = xoopsGetElementById(id);
                        if (calendar != null) { calendar.hide(); }
                        else {
                            var cal = new Calendar(true, "' . $jstime . '", selected, closeHandler);
                            calendar = cal;
                            cal.setRange(1900, 2100);
                            calendar.create();
                        }
                        calendar.sel = el;
                        calendar.parseDate(el.value);
                        calendar.showAtElement(el);
                        Calendar.addEvent(document, "mousedown", checkCalendar);
                        return false;
                    }
                    Calendar._DN = new Array("' . _CAL_SUNDAY . '", "' . _CAL_MONDAY . '", "' . _CAL_TUESDAY . '", "' . _CAL_WEDNESDAY . '", "' . _CAL_THURSDAY . '", "' . _CAL_FRIDAY . '", "' . _CAL_SATURDAY . '", "' . _CAL_SUNDAY . '");
                    Calendar._MN = new Array("' . _CAL_JANUARY . '", "' . _CAL_FEBRUARY . '", "' . _CAL_MARCH . '", "' . _CAL_APRIL . '", "' . _CAL_MAY . '", "' . _CAL_JUNE . '", "' . _CAL_JULY . '", "' . _CAL_AUGUST . '", "' . _CAL_SEPTEMBER . '", "' . _CAL_OCTOBER . '", "' . _CAL_NOVEMBER . '", "' . _CAL_DECEMBER . '");
                    Calendar._TT = {};
                    Calendar._TT["TOGGLE"] = "' . _CAL_TGL1STD . '";
                    Calendar._TT["PREV_YEAR"] = "' . _CAL_PREVYR . '";
                    Calendar._TT["PREV_MONTH"] = "' . _CAL_PREVMNTH . '";
                    Calendar._TT["GO_TODAY"] = "' . _CAL_GOTODAY . '";
                    Calendar._TT["NEXT_MONTH"] = "' . _CAL_NXTMNTH . '";
                    Calendar._TT["NEXT_YEAR"] = "' . _CAL_NEXTYR . '";
                    Calendar._TT["SEL_DATE"] = "' . _CAL_SELDATE . '";
                    Calendar._TT["DRAG_TO_MOVE"] = "' . _CAL_DRAGMOVE . '";
                    Calendar._TT["PART_TODAY"] = "(' . _CAL_TODAY . ')";
                    Calendar._TT["MON_FIRST"] = "' . _CAL_DISPM1ST . '";
                    Calendar._TT["SUN_FIRST"] = "' . _CAL_DISPS1ST . '";
                    Calendar._TT["CLOSE"] = "' . _CLOSE . '";
                    Calendar._TT["TODAY"] = "' . _CAL_TODAY . '";
                    Calendar._TT["DEF_DATE_FORMAT"] = "' . _SHORTDATESTRING . '";
                    Calendar._TT["TT_DATE_FORMAT"] = "' . _SHORTDATESTRING . '";
                    Calendar._TT["WK"] = "";
                ');
            }
        }
        return '<div class="join w-full">'
            . '<input class="input input-bordered join-item w-full" type="text" name="' . $ele_name . '" id="' . $ele_name
            . '" size="' . $element->getSize() . '" maxlength="' . $element->getMaxlength()
            . '" value="' . $display_value . '"' . $element->getExtra() . '>'
            . '<button class="btn btn-neutral join-item" type="button"'
            . ' onclick="return showCalendar(\'' . $ele_name . '\');">'
            . '<i class="fa-solid fa-calendar" aria-hidden="true"></i></button>'
            . '</div>';
    }

    /**
     * Render support for XoopsThemeForm
     *
     * @param XoopsThemeForm $form form to render
     *
     * @return string rendered form
     */
    public function renderThemeForm(XoopsThemeForm $form)
    {
        $ele_name = $form->getName();

        $ret = '<div class="card bg-base-100 shadow">';
        $ret .= '<form name="' . $ele_name . '" id="' . $ele_name . '" action="'
            . $form->getAction() . '" method="' . $form->getMethod()
            . '" onsubmit="return xoopsFormValidate_' . $ele_name . '();"' . $form->getExtra()
            . ' class="card-body">'
            . '<h3 class="card-title">' . $form->getTitle() . '</h3>';
        $hidden = '';

        foreach ($form->getElements() as $element) {
            if (!is_object($element)) { // see $form->addBreak()
                $ret .= $element;
                continue;
            }
            if ($element->isHidden()) {
                $hidden .= $element->render();
                continue;
            }

            $ret .= '<div class="form-control w-full mb-4 grid grid-cols-1 md:grid-cols-12 gap-2 md:items-start">';
            if (($caption = $element->getCaption()) != '') {
                $ret .= '<label for="' . $element->getName() . '" class="label md:col-span-3 md:justify-end">'
                    . '<span class="label-text">' . $element->getCaption()
                    . ($element->isRequired() ? '<span class="text-error ms-1">*</span>' : '')
                    . '</span></label>';
            } else {
                $ret .= '<div class="md:col-span-3"></div>';
            }
            $ret .= '<div class="md:col-span-9">';
            $ret .= $element->render();
            if (($desc = $element->getDescription()) != '') {
                $ret .= '<div class="label"><span class="label-text-alt text-base-content/60">' . $desc . '</span></div>';
            }
            $ret .= '</div>';
            $ret .= '</div>';
        }
        if (count($form->getRequired()) > 0) {
            $ret .= NWLINE . '<div class="text-sm text-base-content/60 mt-2"><span class="text-error">*</span> = ' . _REQUIRED . '</div>' . NWLINE;
        }
        $ret .= $hidden;
        $ret .= '</form></div>';
        $ret .= $form->renderValidationJS(true);

        return $ret;
    }

    /**
     * Support for themed addBreak
     *
     * @param XoopsThemeForm $form
     * @param string         $extra pre-rendered content for break row
     * @param string         $class class for row
     *
     * @return void
     */
    public function addThemeFormBreak(XoopsThemeForm $form, $extra, $class)
    {
        $class = ($class != '') ? preg_replace('/[^A-Za-z0-9\s\s_-]/i', '', $class) : '';
        $form->addElement('<div class="divider col-span-full ' . $class . '"><span class="font-semibold">' . $extra . '</span></div>');
    }
}
