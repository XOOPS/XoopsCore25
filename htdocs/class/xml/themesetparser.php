<?php
/**
 * XOOPS Utilities
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package         class
 * @subpackage      utility
 * @since           1.0.0
 * @author          Author: Kazumi Ono (AKA onokazu)
 * @version         $Id$
 */
defined('XOOPS_ROOT_PATH') || die('Restricted access');

include_once XOOPS_ROOT_PATH . '/class/xml/saxparser.php';
include_once XOOPS_ROOT_PATH . '/class/xml/xmltaghandler.php';

/**
 * Class XoopsThemeSetParser
 */
class XoopsThemeSetParser extends SaxParser
{
    var $tempArr = array();
    var $themeSetData = array();
    var $imagesData = array();
    var $templatesData = array();

    /**
     * @param $input
     */
    function XoopsThemeSetParser(&$input)
    {
        $this->SaxParser($input);
        $this->addTagHandler(new ThemeSetThemeNameHandler());
        $this->addTagHandler(new ThemeSetDateCreatedHandler());
        $this->addTagHandler(new ThemeSetAuthorHandler());
        $this->addTagHandler(new ThemeSetDescriptionHandler());
        $this->addTagHandler(new ThemeSetGeneratorHandler());
        $this->addTagHandler(new ThemeSetNameHandler());
        $this->addTagHandler(new ThemeSetEmailHandler());
        $this->addTagHandler(new ThemeSetLinkHandler());
        $this->addTagHandler(new ThemeSetTemplateHandler());
        $this->addTagHandler(new ThemeSetImageHandler());
        $this->addTagHandler(new ThemeSetModuleHandler());
        $this->addTagHandler(new ThemeSetFileTypeHandler());
        $this->addTagHandler(new ThemeSetTagHandler());
    }

    /**
     * @param $name
     * @param $value
     */
    function setThemeSetData($name, &$value)
    {
        $this->themeSetData[$name] = & $value;
    }

    /**
     * @param null $name
     *
     * @return array|bool
     */
    function &getThemeSetData($name = null)
    {
        if (isset($name)) {
            if (isset($this->themeSetData[$name])) {
                return $this->themeSetData[$name];
            }

            return false;
        }

        return $this->themeSetData;
    }

    /**
     * @param $imagearr
     */
    function setImagesData(&$imagearr)
    {
        $this->imagesData[] = & $imagearr;
    }

    /**
     * @return array
     */
    function &getImagesData()
    {
        return $this->imagesData;
    }

    /**
     * @param $tplarr
     */
    function setTemplatesData(&$tplarr)
    {
        $this->templatesData[] = & $tplarr;
    }

    /**
     * @return array
     */
    function &getTemplatesData()
    {
        return $this->templatesData;
    }

    /**
     * @param        $name
     * @param        $value
     * @param string $delim
     */
    function setTempArr($name, &$value, $delim = '')
    {
        if (! isset($this->tempArr[$name])) {
            $this->tempArr[$name] = & $value;
        } else {
            $this->tempArr[$name] .= $delim . $value;
        }
    }

    /**
     * @return array
     */
    function getTempArr()
    {
        return $this->tempArr;
    }

    function resetTempArr()
    {
        unset($this->tempArr);
        $this->tempArr = array();
    }
}

/**
 * Class ThemeSetDateCreatedHandler
 */
class ThemeSetDateCreatedHandler extends XmlTagHandler
{

    function ThemeSetDateCreatedHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'dateCreated';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'themeset':
                $parser->setThemeSetData('date', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class ThemeSetAuthorHandler
 */
class ThemeSetAuthorHandler extends XmlTagHandler
{
    function ThemeSetAuthorHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'author';
    }

    /**
     * @param $parser
     * @param $attributes
     */
    function handleBeginElement(&$parser, &$attributes)
    {
        $parser->resetTempArr();
    }

    /**
     * @param $parser
     */
    function handleEndElement(&$parser)
    {
        $parser->setCreditsData($parser->getTempArr());
    }
}

/**
 * Class ThemeSetDescriptionHandler
 */
class ThemeSetDescriptionHandler extends XmlTagHandler
{
    function ThemeSetDescriptionHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'description';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'template':
                $parser->setTempArr('description', $data);
                break;
            case 'image':
                $parser->setTempArr('description', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class ThemeSetGeneratorHandler
 */
class ThemeSetGeneratorHandler extends XmlTagHandler
{
    function ThemeSetGeneratorHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'generator';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'themeset':
                $parser->setThemeSetData('generator', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class ThemeSetNameHandler
 */
class ThemeSetNameHandler extends XmlTagHandler
{
    function ThemeSetNameHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'name';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'themeset':
                $parser->setThemeSetData('name', $data);
                break;
            case 'author':
                $parser->setTempArr('name', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class ThemeSetEmailHandler
 */
class ThemeSetEmailHandler extends XmlTagHandler
{
    function ThemeSetEmailHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'email';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'author':
                $parser->setTempArr('email', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class ThemeSetLinkHandler
 */
class ThemeSetLinkHandler extends XmlTagHandler
{
    function ThemeSetLinkHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'link';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'author':
                $parser->setTempArr('link', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class ThemeSetTemplateHandler
 */
class ThemeSetTemplateHandler extends XmlTagHandler
{
    function ThemeSetTemplateHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'template';
    }

    /**
     * @param $parser
     * @param $attributes
     */
    function handleBeginElement(&$parser, &$attributes)
    {
        $parser->resetTempArr();
        $parser->setTempArr('name', $attributes['name']);
    }

    /**
     * @param $parser
     */
    function handleEndElement(&$parser)
    {
        $parser->setTemplatesData($parser->getTempArr());
    }
}

/**
 * Class ThemeSetImageHandler
 */
class ThemeSetImageHandler extends XmlTagHandler
{
    function ThemeSetImageHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'image';
    }

    /**
     * @param $parser
     * @param $attributes
     */
    function handleBeginElement(&$parser, &$attributes)
    {
        $parser->resetTempArr();
        $parser->setTempArr('name', $attributes[0]);
    }

    /**
     * @param $parser
     */
    function handleEndElement(&$parser)
    {
        $parser->setImagesData($parser->getTempArr());
    }
}

/**
 * Class ThemeSetModuleHandler
 */
class ThemeSetModuleHandler extends XmlTagHandler
{
    function ThemeSetModuleHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'module';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'template':
            case 'image':
                $parser->setTempArr('module', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class ThemeSetFileTypeHandler
 */
class ThemeSetFileTypeHandler extends XmlTagHandler
{
    function ThemeSetFileTypeHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'fileType';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'template':
                $parser->setTempArr('type', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class ThemeSetTagHandler
 */
class ThemeSetTagHandler extends XmlTagHandler
{
    function ThemeSetTagHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'tag';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'image':
                $parser->setTempArr('tag', $data);
                break;
            default:
                break;
        }
    }
}
