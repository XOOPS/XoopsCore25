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
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          utility
 * @since               1.0.0
 * @author              Author: Kazumi Ono (AKA onokazu)
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

include_once XOOPS_ROOT_PATH . '/class/xml/saxparser.php';
include_once XOOPS_ROOT_PATH . '/class/xml/xmltaghandler.php';

/**
 * Class XoopsThemeSetParser
 */
class XoopsThemeSetParser extends SaxParser
{
    public $tempArr       = array();
    public $themeSetData  = array();
    public $imagesData    = array();
    public $templatesData = array();

    /**
     * @param $input
     */
    public function __construct(&$input)
    {
        parent::__construct($input);
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
    public function setThemeSetData($name, &$value)
    {
        $this->themeSetData[$name] = &$value;
    }

    /**
     * @param null $name
     *
     * @return array|bool
     */
    public function &getThemeSetData($name = null)
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
    public function setImagesData(&$imagearr)
    {
        $this->imagesData[] = &$imagearr;
    }

    /**
     * @return array
     */
    public function &getImagesData()
    {
        return $this->imagesData;
    }

    /**
     * @param $tplarr
     */
    public function setTemplatesData(&$tplarr)
    {
        $this->templatesData[] = &$tplarr;
    }

    /**
     * @return array
     */
    public function &getTemplatesData()
    {
        return $this->templatesData;
    }

    /**
     * @param        $name
     * @param        $value
     * @param string $delim
     */
    public function setTempArr($name, &$value, $delim = '')
    {
        if (!isset($this->tempArr[$name])) {
            $this->tempArr[$name] = &$value;
        } else {
            $this->tempArr[$name] .= $delim . $value;
        }
    }

    /**
     * @return array
     */
    public function getTempArr()
    {
        return $this->tempArr;
    }

    public function resetTempArr()
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
    /**
     * ThemeSetDateCreatedHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dateCreated';
    }

    /**
     * @param $parser
     * @param $data
     */
    public function handleCharacterData($parser, &$data)
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
    /**
     * ThemeSetAuthorHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'author';
    }

    /**
     * @param $parser
     * @param $attributes
     */
    public function handleBeginElement($parser, &$attributes)
    {
        $parser->resetTempArr();
    }

    /**
     * @param $parser
     */
    public function handleEndElement($parser)
    {
        $parser->setCreditsData($parser->getTempArr());
    }
}

/**
 * Class ThemeSetDescriptionHandler
 */
class ThemeSetDescriptionHandler extends XmlTagHandler
{
    /**
     * ThemeSetDescriptionHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'description';
    }

    /**
     * @param $parser
     * @param $data
     */
    public function handleCharacterData($parser, &$data)
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
    /**
     * ThemeSetGeneratorHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'generator';
    }

    /**
     * @param $parser
     * @param $data
     */
    public function handleCharacterData($parser, &$data)
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
    /**
     * ThemeSetNameHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'name';
    }

    /**
     * @param $parser
     * @param $data
     */
    public function handleCharacterData($parser, &$data)
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
    /**
     * ThemeSetEmailHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'email';
    }

    /**
     * @param $parser
     * @param $data
     */
    public function handleCharacterData($parser, &$data)
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
    /**
     * ThemeSetLinkHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'link';
    }

    /**
     * @param $parser
     * @param $data
     */
    public function handleCharacterData($parser, &$data)
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
    /**
     * ThemeSetTemplateHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'template';
    }

    /**
     * @param $parser
     * @param $attributes
     */
    public function handleBeginElement($parser, &$attributes)
    {
        $parser->resetTempArr();
        $parser->setTempArr('name', $attributes['name']);
    }

    /**
     * @param $parser
     */
    public function handleEndElement($parser)
    {
        $parser->setTemplatesData($parser->getTempArr());
    }
}

/**
 * Class ThemeSetImageHandler
 */
class ThemeSetImageHandler extends XmlTagHandler
{
    /**
     * ThemeSetImageHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'image';
    }

    /**
     * @param $parser
     * @param $attributes
     */
    public function handleBeginElement($parser, &$attributes)
    {
        $parser->resetTempArr();
        $parser->setTempArr('name', $attributes[0]);
    }

    /**
     * @param $parser
     */
    public function handleEndElement($parser)
    {
        $parser->setImagesData($parser->getTempArr());
    }
}

/**
 * Class ThemeSetModuleHandler
 */
class ThemeSetModuleHandler extends XmlTagHandler
{
    /**
     * ThemeSetModuleHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'module';
    }

    /**
     * @param $parser
     * @param $data
     */
    public function handleCharacterData($parser, &$data)
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
    /**
     * ThemeSetFileTypeHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'fileType';
    }

    /**
     * @param $parser
     * @param $data
     */
    public function handleCharacterData($parser, &$data)
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
    /**
     * ThemeSetTagHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'tag';
    }

    /**
     * @param $parser
     * @param $data
     */
    public function handleCharacterData($parser, &$data)
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
