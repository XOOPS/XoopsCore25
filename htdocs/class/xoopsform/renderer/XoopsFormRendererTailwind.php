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
    /** @var string Reusable class string for small neutral buttons */
    private const BTN_NEUTRAL_SM = 'btn btn-neutral btn-sm';

    /** @var string Reusable dropdown menu class string */
    private const DROPDOWN_MENU_CLS = 'dropdown-content menu bg-base-100 rounded-box z-50 p-2 shadow max-h-64 overflow-y-auto flex-nowrap';

    /** HTML attribute fragments — extracted to satisfy SonarQube duplication rules */
    private const ATTR_NAME       = ' name="';
    private const ATTR_ID         = '" id="';
    private const ATTR_TITLE      = '" title="';
    private const ATTR_TITLE_LEAD = ' title="';
    private const ATTR_TITLE_SQ   = "' title='";
    private const ATTR_ARIA_LABEL = "' aria-label='";
    private const ATTR_VALUE      = ' value="';
    private const ATTR_SIZE       = ' size="';
    private const ATTR_MAXLENGTH  = ' maxlength="';

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
     * Encode a value for use inside a JavaScript string literal in an inline
     * `<script>` block.
     *
     * Uses `json_encode` with `JSON_HEX_*` flags so the result cannot break out
     * of the surrounding `<script>` via `</script>`, cannot escape the string
     * via single/double quotes, and cannot introduce HTML entities via `&`.
     * The returned value INCLUDES its own surrounding quotes — do not wrap it
     * in `"..."` at the call site.
     *
     * @param string $value raw value to encode
     *
     * @return string JSON-encoded string literal including surrounding quotes
     */
    protected function esJs(string $value): string
    {
        try {
            return json_encode(
                $value,
                JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP
                | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
            );
        } catch (\JsonException $e) {
            trigger_error(
                'XoopsFormRendererTailwind::esJs: ' . $e->getMessage(),
                E_USER_WARNING,
            );

            return '""';
        }
    }

    /**
     * Build a safe JavaScript function call expression, with each argument
     * encoded through {@see esJs()}.
     *
     * Returns e.g. `xoopsCodeUrl("textarea_id", "Enter URL", "Enter title")`.
     * The result is ready for embedding in a single-quoted HTML attribute
     * (`onclick='...'` or `href='#' onclick='...; return false;'`) because
     * `esJs()` hex-escapes both quote characters — so neither the inner JS
     * quotes nor any locale-provided quote can break the attribute delimiters.
     *
     * Do NOT build `$fn` from user input — the function name is concatenated
     * verbatim. It is intended to be a hard-coded JavaScript function literal.
     *
     * @param string               $fn   JavaScript function name (safe literal)
     * @param array<int, int|string> $args arguments to encode, in order
     *
     * @return string JavaScript expression of the form `fn("a","b",…)`
     */
    protected function buildJsCall(string $fn, array $args): string
    {
        $encoded = [];
        foreach ($args as $arg) {
            $encoded[] = $this->esJs((string) $arg);
        }

        return $fn . '(' . implode(', ', $encoded) . ')';
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
     *
     * @throws \Throwable if the element's render() method throws
     */
    protected function renderElementHtml(XoopsFormElement $element): string
    {
        $bufferLevel = ob_get_level();
        ob_start();
        try {
            /** @var mixed $rendered */
            $rendered = call_user_func([$element, 'render']);
            $echoed   = (string) ob_get_clean();

            return $echoed . (string) $rendered;
        } finally {
            // Peel any buffer levels that are still above the baseline. Covers
            // two cases:
            //   1. render() threw before ob_get_clean() ran — our buffer is
            //      still open one level above the baseline.
            //   2. render() opened its own buffer and returned normally
            //      without closing it — ob_get_clean() only popped the top
            //      level, leaving our outer buffer stranded.
            // In either case the invariant we want is ob_get_level() ===
            // $bufferLevel after the call completes.
            while (ob_get_level() > $bufferLevel) {
                ob_end_clean();
            }
        }
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
     * LIMITATION: Stripping `<` and `>` will corrupt legitimate attribute
     * values containing those characters (e.g. `onclick="if (x < 5) f()"`).
     * Module developers should pre-encode such content as `&lt;` / `&gt;`
     * in their getExtra() strings before passing them to the renderer.
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
            . self::ATTR_NAME . $name . self::ATTR_ID . $name . '"'
            . self::ATTR_TITLE_LEAD . $value . '" value="' . $value . '"'
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
                . ' onclick="this.form.elements.op.value=\'delete\'">' . $this->esc(_DELETE) . '</button>';
        }
        $ret .= '<input type="button" class="btn btn-error" name="cancel" id="cancel"'
            . ' onClick="history.go(-1);return true;" value="' . $this->esc(_CANCEL) . '">'
            . '<button type="reset" class="btn btn-warning" name="reset" id="reset">' . $this->esc(_RESET) . '</button>'
            . '<button type="' . $type . '" class="btn btn-success"'
            . self::ATTR_NAME . $name . self::ATTR_ID . $name . '"'
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
        $delimiter    = $element->getDelimeter();

        foreach ($element->getOptions() as $value => $name) {
            ++$idSuffix;
            $checked = $this->isOptionChecked($value, $elementValue) ? ' checked' : '';
            $inputId = $this->esc($elementId . $idSuffix);
            $ret .= '<label class="' . $labelCls . '">'
                . '<input class="' . $type . ' ' . $type . '-primary" type="' . $type . '"'
                . self::ATTR_NAME . $this->esc($elementName) . '"'
                . ' id="' . $inputId . '"'
                . self::ATTR_TITLE_LEAD . $this->esc(strip_tags((string) $name)) . '"'
                . self::ATTR_VALUE . $this->esc($value) . '"'
                . $checked . $extra . '>'
                . '<span class="label-text">' . $this->esc((string) $name) . $delimiter . '</span>'
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
        static $fallbackEmitted = false;

        $assets = '';
        if (isset($GLOBALS['xoTheme'])) {
            $GLOBALS['xoTheme']->addScript('include/spectrum.js');
            $GLOBALS['xoTheme']->addStylesheet('include/spectrum.css');
        } elseif (!$fallbackEmitted) {
            // Prepend assets to the returned HTML instead of echoing directly,
            // so renderers remain side-effect free. Emit exactly once per
            // request so forms with multiple color pickers do not duplicate.
            $fallbackEmitted = true;
            $assets          = '<script type="text/javascript" src="' . XOOPS_URL . '/include/spectrum.js"></script>'
                . '<link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/include/spectrum.css">';
        }
        $name  = $this->esc($element->getName(false));
        $title = $this->esc($element->getTitle(false));
        $value = $this->esc($element->getValue());

        return $assets . '<input class="input input-bordered w-24 h-10 p-1" type="color"'
            . self::ATTR_NAME . $name . self::ATTR_ID . $name . self::ATTR_TITLE . $title . '"'
            . ' size="7" maxlength="7" value="' . $value . '"'
            . $this->renderExtra($element) . '>';
    }

    /**
     * Render support for XoopsFormDhtmlTextArea
     *
     * @param XoopsFormDhtmlTextArea $element form element
     *
     * @return string rendered form element
     *
     * @throws \Throwable if nested rendering, file I/O, or token generation throws
     */
    public function renderFormDhtmlTextArea(XoopsFormDhtmlTextArea $element)
    {
        xoops_loadLanguage('formdhtmltextarea');
        $nameRaw = (string) $element->getName(false);
        $name    = $this->esc($nameRaw);
        $title   = $this->esc($element->getTitle(false));

        $savePositionJs = $this->buildJsCall('xoopsSavePosition', [$nameRaw]);

        $ret  = $this->renderFormDhtmlTAXoopsCode($element) . "<br>\n";
        $ret .= $this->renderFormDhtmlTATypography($element);
        $ret .= "<br>\n";
        $ret .= '<textarea class="textarea textarea-bordered w-full font-mono"'
            . ' id="' . $name . '" name="' . $name . self::ATTR_TITLE . $title . '"'
            . " onselect='" . $savePositionJs . "'"
            . " onclick='" . $savePositionJs . "'"
            . " onkeyup='" . $savePositionJs . "'"
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
            $previewOnclick = $this->buildJsCall('form_instantPreview', [
                XOOPS_URL,
                $nameRaw,
                XOOPS_URL . '/images',
                (int) $element->doHtml,
                (string) $GLOBALS['xoopsSecurity']->createToken(),
            ]);
            $button = "<button type='button' class='btn btn-primary btn-sm' onclick='" . $previewOnclick . self::ATTR_TITLE_SQ
                . $this->esc(_PREVIEW) . "'>" . $this->esc(_PREVIEW) . '</button>';

            $ret .= '<br>' . "<div id='" . $name . "_hidden' class='card bg-base-200 mt-2'>"
                . "<div class='card-body p-4'>"
                . "<div class='card-title text-sm'>" . $button . "</div>"
                . "<div id='" . $name . "_hidden_data'>" . $this->esc(_XOOPS_FORM_PREVIEW_CONTENT) . '</div>'
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
     * Render a single editor toolbar button with a JS onclick handler.
     *
     * Extracted from {@see renderFormDhtmlTAXoopsCode()} and
     * {@see renderFormDhtmlTATypography()} to eliminate duplication of the
     * `onclick='...'; title='...'` string fragment.
     *
     * @param string $class        button CSS classes (not escaped — caller supplies literal)
     * @param string $onclickJs    JavaScript expression for the onclick handler
     *                             (will be used inside a single-quoted HTML attribute,
     *                             so the caller must ensure it is already safe for that context)
     * @param string $title        tooltip text (raw — escaped internally for the title attribute)
     * @param string $iconClass    Font Awesome icon class string (e.g. 'fa-solid fa-link')
     * @param string $trailingHtml optional HTML appended after the icon span
     *
     * @return string rendered `<button>` element
     */
    protected function renderEditorButton(string $class, string $onclickJs, string $title, string $iconClass, string $trailingHtml = ''): string
    {
        $escapedTitle = $this->esc($title);

        return "<button type='button' class='" . $class
            . "' onclick='" . $onclickJs
            . self::ATTR_TITLE_SQ . $escapedTitle . self::ATTR_ARIA_LABEL . $escapedTitle . "'>"
            . "<span class='" . $iconClass . "' aria-hidden='true'></span>"
            . $trailingHtml
            . '</button>';
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
        $textareaIdRaw   = (string) $element->getName(false);
        $textareaIdParam = rawurlencode($textareaIdRaw);
        $urlImageMgr     = XOOPS_URL . '/imagemanager.php?target=' . $textareaIdParam;
        $urlSmilies      = XOOPS_URL . '/misc.php?action=showpopups&type=smilies&target=' . $textareaIdParam;
        $btn           = self::BTN_NEUTRAL_SM;

        $code = "<div class='flex flex-wrap gap-1'>";
        $code .= $this->renderEditorButton($btn, $this->buildJsCall('xoopsCodeUrl', [$textareaIdRaw, _ENTERURL, _ENTERWEBTITLE]), _XOOPS_FORM_ALT_URL, 'fa-solid fa-link');
        $code .= $this->renderEditorButton($btn, $this->buildJsCall('xoopsCodeEmail', [$textareaIdRaw, _ENTEREMAIL, _ENTERWEBTITLE]), _XOOPS_FORM_ALT_EMAIL, 'fa-solid fa-envelope');
        $code .= $this->renderEditorButton($btn, $this->buildJsCall('xoopsCodeImg', [$textareaIdRaw, _ENTERIMGURL, _ENTERIMGPOS, _IMGPOSRORL, _ERRORIMGPOS, _XOOPS_FORM_ALT_ENTERWIDTH]), _XOOPS_FORM_ALT_IMG, 'fa-solid fa-file-image');
        $code .= $this->renderEditorButton($btn, $this->buildJsCall('openWithSelfMain', [$urlImageMgr, 'imgmanager', 400, 430]), _XOOPS_FORM_ALT_IMAGE, 'fa-solid fa-file-image', '<small> Manager</small>');
        $code .= $this->renderEditorButton($btn, $this->buildJsCall('openWithSelfMain', [$urlSmilies, 'smilies', 300, 475]), _XOOPS_FORM_ALT_SMILEY, 'fa-solid fa-face-smile');

        $myts       = \MyTextSanitizer::getInstance();
        $extensions = array_filter($myts->config['extensions']);
        foreach (array_keys($extensions) as $key) {
            $extension = $myts->loadExtension($key);
            $result    = $extension->encode($textareaIdRaw);
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
        $code .= $this->renderEditorButton($btn, $this->buildJsCall('xoopsCodeCode', [$textareaIdRaw, _ENTERCODE]), _XOOPS_FORM_ALT_CODE, 'fa-solid fa-code');
        $code .= $this->renderEditorButton($btn, $this->buildJsCall('xoopsCodeQuote', [$textareaIdRaw, _ENTERQUOTE]), _XOOPS_FORM_ALT_QUOTE, 'fa-solid fa-quote-right');
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
        $textareaIdRaw = (string) $element->getName(false);
        $hiddentextRaw = (string) $element->_hiddenText;
        $btn           = self::BTN_NEUTRAL_SM;
        $menuCls       = self::DROPDOWN_MENU_CLS;

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

        // Size dropdown — each link uses href='#' onclick='...; return false;'
        // so the JavaScript runs through a real handler context instead of a
        // `javascript:` URL (which adds URL decoding on top of HTML entity
        // decoding before the JS engine sees it).
        $sizes = $GLOBALS['formtextdhtml_sizes'] ?? [];
        $fontStr .= "<div class='dropdown'>"
            . "<div tabindex='0' role='button' class='{$btn}' title='" . $this->esc(_SIZE) . self::ATTR_ARIA_LABEL . $this->esc(_SIZE) . "'><span class='fa-solid fa-text-height'></span></div>"
            . "<ul tabindex='0' class='{$menuCls}'>";
        foreach ($sizes as $value => $label) {
            $onclick = $this->buildJsCall('xoopsSetElementAttribute', ['size', (string) $value, $textareaIdRaw, $hiddentextRaw]);
            $fontStr .= "<li><a href='#' onclick='" . $onclick . "; return false;'>" . $this->esc((string) $label) . '</a></li>';
        }
        $fontStr .= '</ul></div>';

        // Font dropdown
        $fontStr .= "<div class='dropdown'>"
            . "<div tabindex='0' role='button' class='{$btn}' title='" . $this->esc(_FONT) . self::ATTR_ARIA_LABEL . $this->esc(_FONT) . "'><span class='fa-solid fa-font'></span></div>"
            . "<ul tabindex='0' class='{$menuCls}'>";
        foreach ($fontarray as $font) {
            $onclick = $this->buildJsCall('xoopsSetElementAttribute', ['font', (string) $font, $textareaIdRaw, $hiddentextRaw]);
            $fontStr .= "<li><a href='#' onclick='" . $onclick . "; return false;'>" . $this->esc((string) $font) . '</a></li>';
        }
        $fontStr .= '</ul></div>';

        // Color dropdown
        $fontStr .= "<div class='dropdown'>"
            . "<div tabindex='0' role='button' class='{$btn}' title='" . $this->esc(_COLOR) . self::ATTR_ARIA_LABEL . $this->esc(_COLOR) . "'><span class='fa-solid fa-palette'></span></div>"
            . "<ul tabindex='0' class='{$menuCls}'>";
        foreach ($colorArray as $color => $hex) {
            $onclick = $this->buildJsCall('xoopsSetElementAttribute', ['color', $hex, $textareaIdRaw, $hiddentextRaw]);
            $fontStr .= "<li><a href='#' onclick='" . $onclick . "; return false;'><span style=\"color:#" . $this->esc($hex) . ";\">" . $this->esc($color) . '</span></a></li>';
        }
        $fontStr .= '</ul></div>';

        // Style buttons
        $styleBtn = self::BTN_NEUTRAL_SM . ' join-item';
        $fontStr .= "<div class='join'>";
        $fontStr .= $this->renderEditorButton($styleBtn, $this->buildJsCall('xoopsMakeBold', [$hiddentextRaw, $textareaIdRaw]), _XOOPS_FORM_ALT_BOLD, 'fa-solid fa-bold');
        $fontStr .= $this->renderEditorButton($styleBtn, $this->buildJsCall('xoopsMakeItalic', [$hiddentextRaw, $textareaIdRaw]), _XOOPS_FORM_ALT_ITALIC, 'fa-solid fa-italic');
        $fontStr .= $this->renderEditorButton($styleBtn, $this->buildJsCall('xoopsMakeUnderline', [$hiddentextRaw, $textareaIdRaw]), _XOOPS_FORM_ALT_UNDERLINE, 'fa-solid fa-underline');
        $fontStr .= $this->renderEditorButton($styleBtn, $this->buildJsCall('xoopsMakeLineThrough', [$hiddentextRaw, $textareaIdRaw]), _XOOPS_FORM_ALT_LINETHROUGH, 'fa-solid fa-strikethrough');
        $fontStr .= '</div>';

        // Align buttons
        $fontStr .= "<div class='join'>";
        $fontStr .= $this->renderEditorButton($styleBtn, $this->buildJsCall('xoopsMakeLeft', [$hiddentextRaw, $textareaIdRaw]), _XOOPS_FORM_ALT_LEFT, 'fa-solid fa-align-left');
        $fontStr .= $this->renderEditorButton($styleBtn, $this->buildJsCall('xoopsMakeCenter', [$hiddentextRaw, $textareaIdRaw]), _XOOPS_FORM_ALT_CENTER, 'fa-solid fa-align-center');
        $fontStr .= $this->renderEditorButton($styleBtn, $this->buildJsCall('xoopsMakeRight', [$hiddentextRaw, $textareaIdRaw]), _XOOPS_FORM_ALT_RIGHT, 'fa-solid fa-align-right');
        $fontStr .= '</div>';

        // Length check button — configs is a legacy dynamic property on some
        // editor instances; guard the access to avoid PHP 8.2 dynamic property warnings
        $maxlength = 0;
        if (property_exists($element, 'configs') && is_array($element->configs) && isset($element->configs['maxlength'])) {
            $maxlength = (int) $element->configs['maxlength'];
        }
        $lengthOnclick = $this->buildJsCall('XoopsCheckLength', [$textareaIdRaw, (string) $maxlength, _XOOPS_FORM_ALT_LENGTH, _XOOPS_FORM_ALT_LENGTH_MAX]);
        $checkLengthLabel = $this->esc(_XOOPS_FORM_ALT_CHECKLENGTH);
        $fontStr .= "<button type='button' class='{$btn}' onclick='" . $lengthOnclick . self::ATTR_TITLE_SQ
            . $checkLengthLabel . self::ATTR_ARIA_LABEL . $checkLengthLabel . "'><span class='fa-solid fa-square-check'></span></button>";
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
     *
     * @throws \Throwable if nested element rendering throws
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
            // Hidden elements must not consume delimiter/wrapper slots —
            // emit them bare and skip the visible-element layout path.
            if ($ele->isHidden()) {
                $ret .= $this->renderElementHtml($ele) . NWLINE;
                continue;
            }
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
            ++$count;
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
            . self::ATTR_NAME . $name . self::ATTR_ID . $name . self::ATTR_TITLE . $title . '"'
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
            . self::ATTR_NAME . $name . self::ATTR_ID . $name . '"'
            . self::ATTR_SIZE . (int) $element->getSize() . '"'
            . self::ATTR_MAXLENGTH . (int) $element->getMaxlength() . '"'
            . self::ATTR_VALUE . $this->esc($element->getValue()) . '"'
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
            . self::ATTR_SIZE . (int) $element->getSize() . '"'
            . $this->renderExtra($element);
        if ($element->isMultiple()) {
            $ret .= self::ATTR_NAME . $name . '[]" id="' . $name . self::ATTR_TITLE . $title . '" multiple="multiple">';
        } else {
            $ret .= self::ATTR_NAME . $name . self::ATTR_ID . $name . self::ATTR_TITLE . $title . '">';
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
            . self::ATTR_NAME . $name . self::ATTR_ID . $name . '"'
            . self::ATTR_TITLE_LEAD . $this->esc($element->getTitle(false)) . '"'
            . self::ATTR_SIZE . (int) $element->getSize() . '"'
            . self::ATTR_MAXLENGTH . (int) $element->getMaxlength() . '"'
            . self::ATTR_VALUE . $this->esc($element->getValue()) . '"'
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
            . self::ATTR_NAME . $name . self::ATTR_ID . $name . '"'
            . self::ATTR_TITLE_LEAD . $this->esc($element->getTitle(false)) . '"'
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
     *
     * @throws \Throwable if language file inclusion or formatting throws
     */
    public function renderFormTextDateSelect(XoopsFormTextDateSelect $element)
    {
        static $included        = false;
        static $fallbackEmitted = false;

        include_once $this->resolveCalendarLanguageFile();

        $nameRaw  = (string) $element->getName(false);
        $name     = $this->esc($nameRaw);
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

        $assets = '';
        if (isset($GLOBALS['xoTheme']) && is_object($GLOBALS['xoTheme'])) {
            $GLOBALS['xoTheme']->addScript('include/calendar.js');
            $GLOBALS['xoTheme']->addStylesheet('include/calendar-blue.css');
            if (!$included) {
                $included = true;
                $GLOBALS['xoTheme']->addScript('', '', $this->buildCalendarLocaleJs($jstime));
            }
        } elseif (!$fallbackEmitted) {
            // No theme object is available (e.g., standalone AJAX handler or
            // custom entry point). Emit the calendar assets and init script
            // inline — without this, the `onclick="showCalendar(...)"` handler
            // below references an undefined function. Emit exactly once per
            // request so forms with many date fields do not duplicate the JS.
            $fallbackEmitted = true;
            $inlineJs        = $this->buildCalendarLocaleJs($jstime);
            $assets          = '<script type="text/javascript" src="' . XOOPS_URL . '/include/calendar.js"></script>'
                . '<link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/include/calendar-blue.css">'
                . '<script type="text/javascript">' . $inlineJs . '</script>';
        }

        return $assets . '<div class="join w-full">'
            . '<input class="input input-bordered join-item w-full" type="text"'
            . self::ATTR_NAME . $name . self::ATTR_ID . $name . '"'
            . self::ATTR_SIZE . (int) $element->getSize() . '"'
            . self::ATTR_MAXLENGTH . (int) $element->getMaxlength() . '"'
            . self::ATTR_VALUE . $this->esc($display_value) . '"'
            . $this->renderExtra($element) . '>'
            . "<button class='btn btn-neutral join-item' type='button'"
            . " onclick='return " . $this->buildJsCall('showCalendar', [$nameRaw]) . ";'"
            . " aria-label='" . $this->esc($element->getTitle(false)) . "'>"
            . '<i class="fa-solid fa-calendar" aria-hidden="true"></i></button>'
            . '</div>';
    }

    /**
     * Resolve the calendar language file to `include_once`, with two layers
     * of directory traversal hardening.
     *
     * 1. Allowlist filter — only plain directory names (alphanumerics plus
     *    `_` and `-`) are accepted via `$GLOBALS['xoopsConfig']['language']`.
     *    This blocks `..`, `/`, `\\`, null bytes, and URL-encoded payloads at
     *    the character level before any filesystem call.
     *
     * 2. Realpath boundary check — after confirming the candidate file exists
     *    via `is_file()`, both the candidate and the language base directory
     *    are canonicalized and compared. Any result that does not sit under
     *    `XOOPS_ROOT_PATH/language` is rejected. This closes the residual gap
     *    where a symlink inside `language/` could route outside the tree, and
     *    matches the explicit `realpath()` boundary check called for in the
     *    review feedback.
     *
     * Either layer failing — allowlist, `is_file`, or realpath — sends the
     * caller to the english fallback, which ships with XOOPS core and is
     * guaranteed to exist on a healthy installation.
     *
     * Exposed as its own method so the hardening logic can be unit-tested
     * without triggering the `include_once` and its unguarded `define()`
     * calls inside the language file.
     *
     * @return string absolute path to a `calendar.php` that exists under
     *                `XOOPS_ROOT_PATH/language`
     */
    protected function resolveCalendarLanguageFile(): string
    {
        $baseDir  = XOOPS_ROOT_PATH . '/language';
        $fallback = $baseDir . '/english/calendar.php';

        // Canonicalize the fallback so include_once keys match regardless of
        // whether the caller arrives here via the happy path (which returns a
        // realpath-canonicalized string) or a rejection path. Without this,
        // a request that hits both paths would include calendar.php twice
        // because include_once compares literal path strings.
        $realFallback = realpath($fallback);
        if ($realFallback !== false) {
            $fallback = $realFallback;
        }

        // Layer 1: allowlist filter on the configured language name
        $lang = (string) ($GLOBALS['xoopsConfig']['language'] ?? 'english');
        if (!preg_match('/^[a-z0-9_-]+$/i', $lang)) {
            return $fallback;
        }

        $candidate = $baseDir . '/' . $lang . '/calendar.php';
        if (!is_file($candidate)) {
            return $fallback;
        }

        // Layer 2: realpath boundary check — the candidate must canonicalize
        // to a file under XOOPS_ROOT_PATH/language. Catches symlink escapes
        // that the regex allowlist alone cannot see.
        $realCandidate = realpath($candidate);
        $realBase      = realpath($baseDir);
        if ($realCandidate === false || $realBase === false) {
            return $fallback;
        }
        if (!str_starts_with($realCandidate, $realBase . DIRECTORY_SEPARATOR)) {
            return $fallback;
        }

        return $realCandidate;
    }

    /**
     * Build the inline JavaScript block that defines the legacy Calendar
     * widget globals (`showCalendar`, `Calendar._DN`, `Calendar._MN`,
     * `Calendar._TT[...]`).
     *
     * Extracted from {@see renderFormTextDateSelect()} so the same JS can be
     * registered with `$xoTheme` when a theme is present AND emitted inline as
     * a fallback when it is not. All translatable constants flow through
     * {@see esJs()} so a malicious or malformed locale string cannot escape
     * the containing `<script>` block or break its string literals.
     *
     * @param string $jstime default date to seed the Calendar widget with,
     *                       already formatted as `m/d/Y`
     *
     * @return string JavaScript source suitable for embedding in a
     *                `<script>` tag or passing to `XoopsTheme::addScript()`
     */
    protected function buildCalendarLocaleJs(string $jstime): string
    {
        return '
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
                            var cal = new Calendar(true, ' . $this->esJs($jstime) . ', selected, closeHandler);
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
                    Calendar._DN = new Array(' . $this->esJs(_CAL_SUNDAY) . ', ' . $this->esJs(_CAL_MONDAY) . ', ' . $this->esJs(_CAL_TUESDAY) . ', ' . $this->esJs(_CAL_WEDNESDAY) . ', ' . $this->esJs(_CAL_THURSDAY) . ', ' . $this->esJs(_CAL_FRIDAY) . ', ' . $this->esJs(_CAL_SATURDAY) . ', ' . $this->esJs(_CAL_SUNDAY) . ');
                    Calendar._MN = new Array(' . $this->esJs(_CAL_JANUARY) . ', ' . $this->esJs(_CAL_FEBRUARY) . ', ' . $this->esJs(_CAL_MARCH) . ', ' . $this->esJs(_CAL_APRIL) . ', ' . $this->esJs(_CAL_MAY) . ', ' . $this->esJs(_CAL_JUNE) . ', ' . $this->esJs(_CAL_JULY) . ', ' . $this->esJs(_CAL_AUGUST) . ', ' . $this->esJs(_CAL_SEPTEMBER) . ', ' . $this->esJs(_CAL_OCTOBER) . ', ' . $this->esJs(_CAL_NOVEMBER) . ', ' . $this->esJs(_CAL_DECEMBER) . ');
                    Calendar._TT = {};
                    Calendar._TT["TOGGLE"] = ' . $this->esJs(_CAL_TGL1STD) . ';
                    Calendar._TT["PREV_YEAR"] = ' . $this->esJs(_CAL_PREVYR) . ';
                    Calendar._TT["PREV_MONTH"] = ' . $this->esJs(_CAL_PREVMNTH) . ';
                    Calendar._TT["GO_TODAY"] = ' . $this->esJs(_CAL_GOTODAY) . ';
                    Calendar._TT["NEXT_MONTH"] = ' . $this->esJs(_CAL_NXTMNTH) . ';
                    Calendar._TT["NEXT_YEAR"] = ' . $this->esJs(_CAL_NEXTYR) . ';
                    Calendar._TT["SEL_DATE"] = ' . $this->esJs(_CAL_SELDATE) . ';
                    Calendar._TT["DRAG_TO_MOVE"] = ' . $this->esJs(_CAL_DRAGMOVE) . ';
                    Calendar._TT["PART_TODAY"] = "(" + ' . $this->esJs(_CAL_TODAY) . ' + ")";
                    Calendar._TT["MON_FIRST"] = ' . $this->esJs(_CAL_DISPM1ST) . ';
                    Calendar._TT["SUN_FIRST"] = ' . $this->esJs(_CAL_DISPS1ST) . ';
                    Calendar._TT["CLOSE"] = ' . $this->esJs(_CLOSE) . ';
                    Calendar._TT["TODAY"] = ' . $this->esJs(_CAL_TODAY) . ';
                    Calendar._TT["DEF_DATE_FORMAT"] = ' . $this->esJs(_SHORTDATESTRING) . ';
                    Calendar._TT["TT_DATE_FORMAT"] = ' . $this->esJs(_SHORTDATESTRING) . ';
                    Calendar._TT["WK"] = "";
                ';
    }

    /**
     * Render support for XoopsThemeForm
     *
     * @param XoopsThemeForm $form form to render
     *
     * @return string rendered form
     *
     * @throws \Throwable if nested element rendering throws
     */
    public function renderThemeForm(XoopsThemeForm $form)
    {
        // Use getName() (default encoding) — not getName(false) — so the form
        // name matches the function name that renderValidationJS() generates.
        // This is the convention established by XoopsFormRendererBootstrap4 and
        // XoopsForm::renderValidationJS() itself. XOOPS assumes form names are
        // JS-identifier-safe; this renderer does not attempt to fix that broader
        // core constraint.
        $formName = (string) $form->getName();

        $ret  = '<div class="card bg-base-100 shadow">';
        $ret .= '<form name="' . $formName . self::ATTR_ID . $formName . '"'
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
            $ret .= $this->renderThemeFormField($element);
        }
        if (count($form->getRequired()) > 0) {
            $ret .= NWLINE . '<div class="text-sm text-base-content/60 mt-2"><span class="text-error">*</span> = ' . $this->esc(_REQUIRED) . '</div>' . NWLINE;
        }
        $ret .= $hidden;
        $ret .= '</form></div>';
        $ret .= $form->renderValidationJS(true);

        return $ret;
    }

    /**
     * Render a single visible form field row (label + input + description)
     * for {@see renderThemeForm()}.
     *
     * Extracted from the main loop to keep cognitive complexity in check.
     *
     * @param XoopsFormElement $element form element to render
     *
     * @return string rendered field row HTML
     */
    protected function renderThemeFormField(XoopsFormElement $element): string
    {
        $caption = $element->getCaption();
        if ($caption !== '') {
            $required = $element->isRequired() ? '<span class="text-error ms-1">*</span>' : '';
            $label    = '<label for="' . $this->esc($element->getName(false)) . '" class="label md:col-span-3 md:justify-end">'
                . '<span class="label-text">' . $this->esc($caption) . $required
                . '</span></label>';
        } else {
            $label = '<div class="md:col-span-3"></div>';
        }

        // Render description raw (not escaped) to match Bootstrap4/Legacy
        // renderers. Modules commonly use HTML in descriptions (links, <code>,
        // <em>, etc.). Escaping would break that established contract.
        $desc     = $element->getDescription();
        $descHtml = $desc !== ''
            ? '<div class="label"><span class="label-text-alt text-base-content/60">' . $desc . '</span></div>'
            : '';

        return '<div class="form-control w-full mb-4 grid grid-cols-1 md:grid-cols-12 gap-2 md:items-start">'
            . $label
            . '<div class="md:col-span-9">'
            . $this->renderElementHtml($element)
            . $descHtml
            . '</div>'
            . '</div>';
    }

    /**
     * Support for themed addBreak
     *
     * @param XoopsThemeForm $form  form being broken
     * @param string         $extra pre-rendered HTML for the break row — rendered
     *                              verbatim (not escaped) to match the convention
     *                              established by all Bootstrap renderers; callers
     *                              must ensure content is safe
     * @param string         $class CSS class for the row (sanitized to alphanum/space/dash/underscore)
     *
     * @return void
     */
    public function addThemeFormBreak(XoopsThemeForm $form, $extra, $class)
    {
        $class = ($class != '') ? preg_replace('/[^A-Za-z0-9\s_-]/', '', $class) : '';
        $form->addElement('<div class="divider col-span-full ' . $class . '"><span class="font-semibold">' . $extra . '</span></div>');
    }
}
