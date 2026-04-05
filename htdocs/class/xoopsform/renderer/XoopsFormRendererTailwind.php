<?php
/**
 * XOOPS Kernel Class
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
 * @package             kernel
 * @subpackage          form
 * @since               2.5.12
 * @author              XOOPS Project
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

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
 * @see https://daisyui.com/components/
 */
class XoopsFormRendererTailwind implements XoopsFormRendererInterface
{
    /** @var string Reusable class string for small neutral buttons */
    private const BTN_NEUTRAL_SM = 'btn btn-neutral btn-sm';

    /** @var string Reusable dropdown menu class string */
    private const DROPDOWN_MENU_CLS = 'dropdown-content menu bg-base-100 rounded-box z-50 p-2 shadow max-h-64 overflow-y-auto flex-nowrap';

    /**
     * Escape a value for use inside an HTML attribute or as text content.
     *
     * @param mixed $value value to escape
     *
     * @return string escaped string safe for HTML output
     */
    protected function esc($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Capture a form element's render() output as a string.
     *
     * XoopsFormElement::render() is empty in the base class but all concrete
     * subclasses override it to return a string. Any stray echoes are also
     * captured via output buffering.
     *
     * @param XoopsFormElement $element element to render
     *
     * @return string rendered HTML
     */
    protected function renderElementHtml(XoopsFormElement $element): string
    {
        ob_start();
        $rendered = (string) $element->render();
        $echoed   = (string) ob_get_clean();

        return $rendered . $echoed;
    }

    /**
     * Render an element's extra attribute string.
     *
     * Both XoopsFormElement::getExtra() and XoopsForm::getExtra() return raw
     * HTML that may contain attribute fragments like `onclick="..."`. This is
     * legacy behaviour and cannot be fully safely escaped without breaking
     * existing modules. Sanitize by stripping any '>' or '<' characters to
     * prevent tag injection while preserving the attribute fragment format.
     *
     * Accepts any object exposing getExtra() (XoopsFormElement, XoopsForm,
     * XoopsThemeForm, etc.) — enforced via method_exists rather than a
     * restrictive type hint.
     *
     * @param object $element element or form whose extra attributes to render
     *
     * @return string sanitized extra attribute string (leading space included)
     */
    protected function renderExtra($element): string
    {
        if (!is_object($element) || !method_exists($element, 'getExtra')) {
            return '';
        }
        $extra = (string) $element->getExtra();
        if ($extra === '') {
            return '';
        }
        // Strip tag delimiters so getExtra() cannot introduce new elements
        $extra = str_replace(['<', '>'], '', $extra);

        return ' ' . $extra;
    }

    /**
     * Render support for XoopsFormButton
     *
     * @param XoopsFormButton $element form element
     *
     * @return string rendered form element
     */
    public function renderFormButton(XoopsFormButton $element)
    {
        $name  = $this->esc($element->getName(false));
        $value = $this->esc($element->getValue());
        $type  = $this->esc($element->getType());

        return '<button type="' . $type . '" class="btn btn-neutral"'
            . ' name="' . $name . '" id="' . $name . '"'
            . ' title="' . $value . '" value="' . $value . '"'
            . $this->renderExtra($element) . '>' . $value . '</button>';
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
        $name  = $this->esc($element->getName(false));
        $type  = $this->esc($element->getType());
        $value = $this->esc($element->getValue());

        $ret = '<div class="flex flex-wrap gap-2">';
        if ($element->_showDelete) {
            $ret .= '<button type="submit" class="btn btn-error" name="delete" id="delete"'
                . ' onclick="this.form.elements.op.value=\'delete\'">' . _DELETE . '</button>';
        }
        $ret .= '<input type="button" class="btn btn-error" name="cancel" id="cancel"'
            . ' onClick="history.go(-1);return true;" value="' . $this->esc(_CANCEL) . '">'
            . '<button type="reset" class="btn btn-warning" name="reset" id="reset">' . _RESET . '</button>'
            . '<button type="' . $type . '" class="btn btn-success"'
            . ' name="' . $name . '" id="' . $name . '"'
            . $this->renderExtra($element) . '>' . $value . '</button>'
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
        $elementName    = $element->getName(false);
        $elementId      = $elementName;
        $elementOptions = $element->getOptions();
        if (count($elementOptions) > 1 && substr($elementName, -2, 2) !== '[]') {
            $elementName .= '[]';
            $element->setName($elementName);
        }

        return $this->renderChecked($element, 'checkbox', $elementId, $elementName);
    }

    /**
     * Render a checkbox or radio element in any column layout.
     *
     * Consolidates inline / single column / multi column rendering so each
     * input option is produced in exactly one place. Column layout is chosen
     * by the element's `columns` property: 0 = inline, 1 = single column,
     * anything else = multicolumn grid.
     *
     * @param XoopsFormCheckBox|XoopsFormRadio $element     element being rendered
     * @param string                           $type        'checkbox' or 'radio'
     * @param string                           $elementId   input 'id' attribute of element
     * @param string                           $elementName input 'name' attribute of element
     *
     * @return string rendered group
     */
    protected function renderChecked($element, string $type, string $elementId, string $elementName): string
    {
        $columns = (int) ($element->columns ?? 0);
        switch ($columns) {
            case 0:
                $containerCls = 'flex flex-wrap gap-4';
                $labelCls     = 'label cursor-pointer gap-2';
                break;
            case 1:
                $containerCls = 'flex flex-col gap-2';
                $labelCls     = 'label cursor-pointer justify-start gap-2';
                break;
            default:
                $containerCls = 'grid grid-cols-2 md:grid-cols-3 gap-2';
                $labelCls     = 'label cursor-pointer justify-start gap-2';
                break;
        }

        $ret          = '<div class="' . $containerCls . '">';
        $idSuffix     = 0;
        $elementValue = $element->getValue();
        $extra        = $this->renderExtra($element);
        $delimeter    = $element->getDelimeter();

        foreach ($element->getOptions() as $value => $name) {
            ++$idSuffix;
            $checked = $this->isOptionChecked($value, $elementValue) ? ' checked' : '';
            $inputId = $this->esc($elementId . $idSuffix);
            $ret .= '<label class="' . $labelCls . '">'
                . '<input class="' . $type . ' ' . $type . '-primary" type="' . $type . '"'
                . ' name="' . $this->esc($elementName) . '"'
                . ' id="' . $inputId . '"'
                . ' title="' . $this->esc(strip_tags((string) $name)) . '"'
                . ' value="' . $this->esc($value) . '"'
                . $checked . $extra . '>'
                . '<span class="label-text">' . $this->esc((string) $name) . $delimeter . '</span>'
                . '</label>';
        }
        $ret .= '</div>';

        return $ret;
    }

    /**
     * Determine whether a given option value should be marked as checked.
     *
     * @param mixed $optionValue value of the option being rendered
     * @param mixed $current     current value(s) selected on the element
     *
     * @return bool true when the option should render as checked
     */
    private function isOptionChecked($optionValue, $current): bool
    {
        if (is_array($current)) {
            return in_array((string) $optionValue, array_map('strval', $current), true);
        }

        return (string) $optionValue === (string) $current;
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
        $assets = '';
        if (isset($GLOBALS['xoTheme'])) {
            $GLOBALS['xoTheme']->addScript('include/spectrum.js');
            $GLOBALS['xoTheme']->addStylesheet('include/spectrum.css');
        } else {
            // Prepend assets to the returned HTML instead of echoing directly,
            // so renderers remain side-effect free.
            $assets = '<script type="text/javascript" src="' . XOOPS_URL . '/include/spectrum.js"></script>'
                . '<link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/include/spectrum.css">';
        }
        $name  = $this->esc($element->getName(false));
        $title = $this->esc($element->getTitle(false));
        $value = $this->esc($element->getValue());

        return $assets . '<input class="input input-bordered w-24 h-10 p-1" type="color"'
            . ' name="' . $name . '" id="' . $name . '" title="' . $title . '"'
            . ' size="7" maxlength="7" value="' . $value . '"'
            . $this->renderExtra($element) . '>';
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
        $name  = $this->esc($element->getName(false));
        $title = $this->esc($element->getTitle(false));

        $ret  = $this->renderFormDhtmlTAXoopsCode($element) . "<br>\n";
        $ret .= $this->renderFormDhtmlTATypography($element);
        $ret .= "<br>\n";
        $ret .= '<textarea class="textarea textarea-bordered w-full font-mono"'
            . ' id="' . $name . '" name="' . $name . '" title="' . $title . '"'
            . ' onselect="xoopsSavePosition(\'' . $name . '\');"'
            . ' onclick="xoopsSavePosition(\'' . $name . '\');"'
            . ' onkeyup="xoopsSavePosition(\'' . $name . '\');"'
            . ' cols="' . (int) $element->getCols() . '" rows="' . (int) $element->getRows() . '"'
            . $this->renderExtra($element) . '>' . $this->esc($element->getValue()) . "</textarea>\n";

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
                . "', '" . $name . "','" . XOOPS_URL . "/images', " . (int) $element->doHtml . ", '"
                . $this->esc($GLOBALS['xoopsSecurity']->createToken()) . "')\" title='" . _PREVIEW . "'>" . _PREVIEW . "</button>";

            $ret .= '<br>' . "<div id='" . $name . "_hidden' class='card bg-base-200 mt-2'>"
                . "<div class='card-body p-4'>"
                . "<div class='card-title text-sm'>" . $button . "</div>"
                . "<div id='" . $name . "_hidden_data'>" . _XOOPS_FORM_PREVIEW_CONTENT . '</div>'
                . '</div></div>';
        }
        $javascript_file         = XOOPS_URL . '/include/formdhtmltextarea.js';
        $javascript_file_element = 'include_formdhtmltextarea_js';
        $javascript              = ($element->js ? '<script type="text/javascript">' . $element->js . '</script>' : '');
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
        $textarea_id = $this->esc($element->getName(false));
        $btn         = self::BTN_NEUTRAL_SM;
        $code        = "<div class='flex flex-wrap gap-1'>";
        $code .= "<button type='button' class='{$btn}' onclick='xoopsCodeUrl(\"{$textarea_id}\", \"" . $this->esc(_ENTERURL) . "\", \"" . $this->esc(_ENTERWEBTITLE) . "\");' title='" . $this->esc(_XOOPS_FORM_ALT_URL) . "'><span class='fa-solid fa-link' aria-hidden='true'></span></button>";
        $code .= "<button type='button' class='{$btn}' onclick='xoopsCodeEmail(\"{$textarea_id}\", \"" . $this->esc(_ENTEREMAIL) . "\", \"" . $this->esc(_ENTERWEBTITLE) . "\");' title='" . $this->esc(_XOOPS_FORM_ALT_EMAIL) . "'><span class='fa-solid fa-envelope' aria-hidden='true'></span></button>";
        $code .= "<button type='button' class='{$btn}' onclick='xoopsCodeImg(\"{$textarea_id}\", \"" . $this->esc(_ENTERIMGURL) . "\", \"" . $this->esc(_ENTERIMGPOS) . "\", \"" . $this->esc(_IMGPOSRORL) . "\", \"" . $this->esc(_ERRORIMGPOS) . "\", \"" . $this->esc(_XOOPS_FORM_ALT_ENTERWIDTH) . "\");' title='" . $this->esc(_XOOPS_FORM_ALT_IMG) . "'><span class='fa-solid fa-file-image' aria-hidden='true'></span></button>";
        $code .= "<button type='button' class='{$btn}' onclick='openWithSelfMain(\"" . XOOPS_URL . "/imagemanager.php?target={$textarea_id}\",\"imgmanager\",400,430);' title='" . $this->esc(_XOOPS_FORM_ALT_IMAGE) . "'><span class='fa-solid fa-file-image' aria-hidden='true'></span><small> Manager</small></button>";
        $code .= "<button type='button' class='{$btn}' onclick='openWithSelfMain(\"" . XOOPS_URL . "/misc.php?action=showpopups&amp;type=smilies&amp;target={$textarea_id}\",\"smilies\",300,475);' title='" . $this->esc(_XOOPS_FORM_ALT_SMILEY) . "'><span class='fa-solid fa-face-smile' aria-hidden='true'></span></button>";

        $myts       = \MyTextSanitizer::getInstance();
        $extensions = array_filter($myts->config['extensions']);
        foreach (array_keys($extensions) as $key) {
            $extension = $myts->loadExtension($key);
            $result    = $extension->encode($textarea_id);
            $encode    = $result[0] ?? '';
            $js        = $result[1] ?? '';
            if (empty($encode)) {
                continue;
            }
            // Extensions output Bootstrap classes — remap the common ones to DaisyUI.
            $encode = str_replace(['btn-default', 'btn-secondary'], self::BTN_NEUTRAL_SM, $encode);
            $code .= $encode;
            if (!empty($js)) {
                $element->js .= $js;
            }
        }
        $code .= "<button type='button' class='{$btn}' onclick='xoopsCodeCode(\"{$textarea_id}\", \"" . $this->esc(_ENTERCODE) . "\");' title='" . $this->esc(_XOOPS_FORM_ALT_CODE) . "'><span class='fa-solid fa-code' aria-hidden='true'></span></button>";
        $code .= "<button type='button' class='{$btn}' onclick='xoopsCodeQuote(\"{$textarea_id}\", \"" . $this->esc(_ENTERQUOTE) . "\");' title='" . $this->esc(_XOOPS_FORM_ALT_QUOTE) . "'><span class='fa-solid fa-quote-right' aria-hidden='true'></span></button>";
        $code .= '</div>';

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
        $textarea_id = $this->esc($element->getName(false));
        $hiddentext  = $this->esc($element->_hiddenText);
        $btn         = self::BTN_NEUTRAL_SM;
        $menuCls     = self::DROPDOWN_MENU_CLS;

        $fontarray = !empty($GLOBALS['formtextdhtml_fonts']) ? $GLOBALS['formtextdhtml_fonts'] : [
            'Arial', 'Courier', 'Georgia', 'Helvetica', 'Impact', 'Verdana', 'Haettenschweiler',
        ];

        $colorArray = [
            'Black'  => '000000', 'Blue'   => '38AAFF', 'Brown'  => '987857',
            'Green'  => '79D271', 'Grey'   => '888888', 'Orange' => 'FFA700',
            'Paper'  => 'E0E0E0', 'Purple' => '363E98', 'Red'    => 'FF211E',
            'White'  => 'FEFEFE', 'Yellow' => 'FFD628',
        ];

        $fontStr = "<div class='flex flex-wrap gap-1 mt-2'>";

        // Size dropdown
        $fontStr .= "<div class='dropdown'>"
            . "<div tabindex='0' role='button' class='{$btn}' title='" . _SIZE . "'><span class='fa-solid fa-text-height'></span></div>"
            . "<ul tabindex='0' class='{$menuCls}'>";
        foreach ($GLOBALS['formtextdhtml_sizes'] as $value => $label) {
            $fontStr .= "<li><a href=\"javascript:xoopsSetElementAttribute('size', '" . $this->esc($value) . "', '{$textarea_id}', '{$hiddentext}');\">" . $this->esc($label) . "</a></li>";
        }
        $fontStr .= '</ul></div>';

        // Font dropdown
        $fontStr .= "<div class='dropdown'>"
            . "<div tabindex='0' role='button' class='{$btn}' title='" . _FONT . "'><span class='fa-solid fa-font'></span></div>"
            . "<ul tabindex='0' class='{$menuCls}'>";
        foreach ($fontarray as $font) {
            $fontStr .= "<li><a href=\"javascript:xoopsSetElementAttribute('font', '" . $this->esc($font) . "', '{$textarea_id}', '{$hiddentext}');\">" . $this->esc($font) . '</a></li>';
        }
        $fontStr .= '</ul></div>';

        // Color dropdown
        $fontStr .= "<div class='dropdown'>"
            . "<div tabindex='0' role='button' class='{$btn}' title='" . _COLOR . "'><span class='fa-solid fa-palette'></span></div>"
            . "<ul tabindex='0' class='{$menuCls}'>";
        foreach ($colorArray as $color => $hex) {
            $fontStr .= "<li><a href=\"javascript:xoopsSetElementAttribute('color', '{$hex}', '{$textarea_id}', '{$hiddentext}');\"><span style=\"color:#{$hex};\">" . $this->esc($color) . '</span></a></li>';
        }
        $fontStr .= '</ul></div>';

        // Style buttons
        $styleBtn = 'btn btn-neutral btn-sm join-item';
        $fontStr .= "<div class='join'>";
        $fontStr .= "<button type='button' class='{$styleBtn}' onclick='xoopsMakeBold(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_BOLD . "'><span class='fa-solid fa-bold'></span></button>";
        $fontStr .= "<button type='button' class='{$styleBtn}' onclick='xoopsMakeItalic(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_ITALIC . "'><span class='fa-solid fa-italic'></span></button>";
        $fontStr .= "<button type='button' class='{$styleBtn}' onclick='xoopsMakeUnderline(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_UNDERLINE . "'><span class='fa-solid fa-underline'></span></button>";
        $fontStr .= "<button type='button' class='{$styleBtn}' onclick='xoopsMakeLineThrough(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_LINETHROUGH . "'><span class='fa-solid fa-strikethrough'></span></button>";
        $fontStr .= '</div>';

        // Align buttons
        $fontStr .= "<div class='join'>";
        $fontStr .= "<button type='button' class='{$styleBtn}' onclick='xoopsMakeLeft(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_LEFT . "'><span class='fa-solid fa-align-left'></span></button>";
        $fontStr .= "<button type='button' class='{$styleBtn}' onclick='xoopsMakeCenter(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_CENTER . "'><span class='fa-solid fa-align-center'></span></button>";
        $fontStr .= "<button type='button' class='{$styleBtn}' onclick='xoopsMakeRight(\"{$hiddentext}\", \"{$textarea_id}\");' title='" . _XOOPS_FORM_ALT_RIGHT . "'><span class='fa-solid fa-align-right'></span></button>";
        $fontStr .= '</div>';

        // Length check button — configs is a legacy dynamic property on some
        // editor instances; guard the access to avoid PHP 8.2 dynamic property warnings
        $maxlength = 0;
        if (property_exists($element, 'configs') && is_array($element->configs) && isset($element->configs['maxlength'])) {
            $maxlength = (int) $element->configs['maxlength'];
        }
        $fontStr .= "<button type='button' class='{$btn}' onclick=\"XoopsCheckLength('"
            . $textarea_id . "', '" . $maxlength . "', '"
            . $this->esc(_XOOPS_FORM_ALT_LENGTH) . "', '" . $this->esc(_XOOPS_FORM_ALT_LENGTH_MAX) . "');\" title='"
            . $this->esc(_XOOPS_FORM_ALT_CHECKLENGTH) . "'><span class='fa-solid fa-square-check'></span></button>";
        $fontStr .= '</div>';

        return $fontStr;
    }

    /**
     * Render support for XoopsFormElementTray
     *
     * ORIENTATION_VERTICAL stacks elements top to bottom (space-y-2).
     * ORIENTATION_HORIZONTAL lays them out in a horizontal row (flex-wrap).
     *
     * @param XoopsFormElementTray $element form element
     *
     * @return string rendered form element
     */
    public function renderFormElementTray(XoopsFormElementTray $element)
    {
        $isVertical = (\XoopsFormElementTray::ORIENTATION_VERTICAL === $element->getOrientation());
        $container  = $isVertical
            ? '<div class="space-y-2">'
            : '<div class="flex flex-wrap items-center gap-2">';

        $ret   = $container;
        $count = 0;
        foreach ($element->getElements() as $ele) {
            if ($count > 0 && !$isVertical) {
                $ret .= $element->getDelimeter();
            }
            if (!$isVertical) {
                $ret .= '<span class="inline-flex items-center gap-1">';
            }
            if ($ele->getCaption() != '') {
                $ret .= '<label for="' . $this->esc($ele->getName(false)) . '" class="label-text">'
                    . $this->esc($ele->getCaption())
                    . ($ele->isRequired() ? '<span class="text-error ms-1">*</span>' : '')
                    . '</label>&nbsp;';
            }
            $ret .= $this->renderElementHtml($ele) . NWLINE;
            if (!$isVertical) {
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
        $name  = $this->esc($element->getName(false));
        $title = $this->esc($element->getTitle(false));

        return '<input type="hidden" name="MAX_FILE_SIZE" value="' . (int) $element->getMaxFileSize() . '">'
            . '<input type="file" class="file-input file-input-bordered w-full"'
            . ' name="' . $name . '" id="' . $name . '" title="' . $title . '"'
            . $this->renderExtra($element) . '>'
            . '<input type="hidden" name="xoops_upload_file[]" id="xoops_upload_file[]" value="' . $name . '">';
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
        return '<label class="label label-text" id="' . $this->esc($element->getName(false)) . '">'
            . $this->esc($element->getValue())
            . '</label>';
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
        $name = $this->esc($element->getName(false));

        return '<input class="input input-bordered w-full" type="password"'
            . ' name="' . $name . '" id="' . $name . '"'
            . ' size="' . (int) $element->getSize() . '"'
            . ' maxlength="' . (int) $element->getMaxlength() . '"'
            . ' value="' . $this->esc($element->getValue()) . '"'
            . $this->renderExtra($element)
            . ($element->autoComplete ? '' : ' autocomplete="off"')
            . '>';
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
        $elementName = $element->getName(false);

        return $this->renderChecked($element, 'radio', $elementName, $elementName);
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
        $name    = $this->esc($element->getName(false));
        $title   = $this->esc($element->getTitle(false));
        $value   = $element->getValue();
        $options = $element->getOptions();

        $ret = '<select class="select select-bordered w-full"'
            . ' size="' . (int) $element->getSize() . '"'
            . $this->renderExtra($element);
        if ($element->isMultiple() !== false) {
            $ret .= ' name="' . $name . '[]" id="' . $name . '" title="' . $title . '" multiple="multiple">';
        } else {
            $ret .= ' name="' . $name . '" id="' . $name . '" title="' . $title . '">';
        }
        // XoopsFormSelect::getValue() always returns an array
        $valueStrings = array_map('strval', $value);
        foreach ($options as $optValue => $optName) {
            $selected = in_array((string) $optValue, $valueStrings, true) ? ' selected' : '';
            $ret .= '<option value="' . $this->esc($optValue) . '"' . $selected . '>'
                . $this->esc($optName) . '</option>';
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
        $name = $this->esc($element->getName(false));

        return '<input class="input input-bordered w-full" type="text"'
            . ' name="' . $name . '" id="' . $name . '"'
            . ' title="' . $this->esc($element->getTitle(false)) . '"'
            . ' size="' . (int) $element->getSize() . '"'
            . ' maxlength="' . (int) $element->getMaxlength() . '"'
            . ' value="' . $this->esc($element->getValue()) . '"'
            . $this->renderExtra($element) . '>';
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
        $name = $this->esc($element->getName(false));

        return '<textarea class="textarea textarea-bordered w-full"'
            . ' name="' . $name . '" id="' . $name . '"'
            . ' title="' . $this->esc($element->getTitle(false)) . '"'
            . ' rows="' . (int) $element->getRows() . '"'
            . ' cols="' . (int) $element->getCols() . '"'
            . $this->renderExtra($element) . '>'
            . $this->esc($element->getValue()) . '</textarea>';
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

        $name     = $this->esc($element->getName(false));
        $rawValue = $element->getValue(false);
        // Blank: empty string or zero-valued timestamp → no display, open calendar at "today"
        // Numeric timestamp → format as date
        // Anything else → treat as a literal display string
        if ($rawValue === '' || $rawValue === '0' || $rawValue === 0) {
            $display_value = '';
            $timestamp     = time();
        } elseif (is_numeric($rawValue)) {
            $timestamp     = (int) $rawValue;
            $display_value = date(_SHORTDATESTRING, $timestamp);
        } else {
            $display_value = (string) $rawValue;
            $timestamp     = time();
        }

        $jstime = formatTimestamp($timestamp, 'm/d/Y');
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
            . '<input class="input input-bordered join-item w-full" type="text"'
            . ' name="' . $name . '" id="' . $name . '"'
            . ' size="' . (int) $element->getSize() . '"'
            . ' maxlength="' . (int) $element->getMaxlength() . '"'
            . ' value="' . $this->esc($display_value) . '"'
            . $this->renderExtra($element) . '>'
            . '<button class="btn btn-neutral join-item" type="button"'
            . ' onclick="return showCalendar(\'' . $name . '\');">'
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
        $formName = $this->esc($form->getName(false));

        $ret  = '<div class="card bg-base-100 shadow">';
        $ret .= '<form name="' . $formName . '" id="' . $formName . '"'
            . ' action="' . $this->esc($form->getAction(false)) . '"'
            . ' method="' . $this->esc($form->getMethod()) . '"'
            . ' onsubmit="return xoopsFormValidate_' . $formName . '();"'
            . $this->renderExtra($form)
            . ' class="card-body">'
            . '<h3 class="card-title">' . $this->esc($form->getTitle(false)) . '</h3>';
        $hidden = '';

        foreach ($form->getElements() as $element) {
            if (!is_object($element)) { // see $form->addBreak()
                $ret .= $element;
                continue;
            }
            if ($element->isHidden()) {
                $hidden .= $this->renderElementHtml($element);
                continue;
            }

            $ret .= '<div class="form-control w-full mb-4 grid grid-cols-1 md:grid-cols-12 gap-2 md:items-start">';
            $caption = $element->getCaption();
            if ($caption !== '') {
                $ret .= '<label for="' . $this->esc($element->getName(false)) . '" class="label md:col-span-3 md:justify-end">'
                    . '<span class="label-text">' . $this->esc($caption)
                    . ($element->isRequired() ? '<span class="text-error ms-1">*</span>' : '')
                    . '</span></label>';
            } else {
                $ret .= '<div class="md:col-span-3"></div>';
            }
            $ret .= '<div class="md:col-span-9">';
            $ret .= $this->renderElementHtml($element);
            $desc = $element->getDescription();
            if ($desc !== '') {
                $ret .= '<div class="label"><span class="label-text-alt text-base-content/60">' . $this->esc($desc) . '</span></div>';
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
     * @param XoopsThemeForm $form  form being broken
     * @param string         $extra pre-rendered content for break row
     * @param string         $class class for row
     *
     * @return void
     */
    public function addThemeFormBreak(XoopsThemeForm $form, $extra, $class)
    {
        $class = ($class != '') ? preg_replace('/[^A-Za-z0-9\s_-]/i', '', $class) : '';
        $form->addElement('<div class="divider col-span-full ' . $class . '"><span class="font-semibold">' . $extra . '</span></div>');
    }
}
