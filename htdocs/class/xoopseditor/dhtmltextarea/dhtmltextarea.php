<?php
/**
 * XOOPS dhtmltextarea class
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          editor
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

xoops_load('XoopsEditor');

/**
 * FormDhtmlTextArea
 *
 * @package
 * @author              John
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @access              public
 */
class FormDhtmlTextArea extends XoopsEditor
{
    /**
     * Hidden text
     *
     * @var string
     * @access private
     */
    public $_hiddenText = 'xoopsHiddenText';
    public $renderer;

    /**
     * FormDhtmlTextArea::__construct()
     *
     * @param array $options
     */
    public function __construct($options = [])
    {
        parent::__construct($options);
        $this->rootPath = '/class/xoopseditor/' . basename(__DIR__);
        $hiddenText     = $this->configs['hiddenText'] ?? $this->_hiddenText;
        xoops_load('XoopsFormDhtmlTextArea');
        $this->renderer = new XoopsFormDhtmlTextArea('', $this->getName(), $this->getValue(), $this->getRows(), $this->getCols(), $hiddenText, $this->configs);
    }

    /**
     * FormDhtmlTextArea::render()
     *
     * @return string
     */
    public function render()
    {
        return $this->renderer->render();
    }
}
