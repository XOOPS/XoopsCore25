<?php
/**
 * XOOPS Form Class Elements
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          form
 * @since               2.0.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

xoops_load('XoopsFormTextArea');

/**
 * XOOPS Form Editor
 *
 */
class XoopsFormEditor extends XoopsFormTextArea
{
    public $editor;

    /**
     * Constructor
     *
     * @param string $caption   Caption
     * @param string $name      Name for textarea field
     * @param array  $configs   configures: editor - editor identifier; name - textarea field name; width, height - dimensions for textarea; value - text content
     * @param bool   $nohtml    use non-WYSIWYG editor onfailure
     * @param string $OnFailure editor to be used if current one failed
     *
     */
    public function __construct($caption, $name, $configs = null, $nohtml = false, $OnFailure = '')
    {
        // Backward compatibility: $name -> editor name; $configs['name'] -> textarea field name
        if (!isset($configs['editor'])) {
            $configs['editor'] = $name;
            $name              = $configs['name'];
            // New: $name -> textarea field name; $configs['editor'] -> editor name; $configs['name'] -> textarea field name
        } else {
            $configs['name'] = $name;
        }
        parent::__construct($caption, $name);
        xoops_load('XoopsEditorHandler');
        $editor_handler = XoopsEditorHandler::getInstance();
        $this->editor   = $editor_handler->get($configs['editor'], $configs, $nohtml, $OnFailure);
    }

    /**
     * renderValidationJS
     * TEMPORARY SOLUTION to 'override' original renderValidationJS method
     * with custom XoopsEditor's renderValidationJS method
     */
    public function renderValidationJS()
    {
        if (is_object($this->editor) && $this->isRequired()) {
            if (method_exists($this->editor, 'renderValidationJS')) {
                $this->editor->setName($this->getName());
                $this->editor->setCaption($this->getCaption());
                $this->editor->_required = $this->isRequired();
                $ret                     = $this->editor->renderValidationJS();

                return $ret;
            } else {
                parent::renderValidationJS();
            }
        }

        return false;
    }

    /**
     * XoopsFormEditor::render()
     *
     * @return \sting
     */
    public function render()
    {
        if (is_object($this->editor)) {
            return $this->editor->render();
        }
        return null;
    }
}
