<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright    XOOPS Project http://xoops.org/
 * @license      GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package
 * @since
 * @author       XOOPS Development Team, Kazumi Ono (AKA onokazu)
 */

defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');
require_once(XOOPS_ROOT_PATH . '/class/xml/saxparser.php');
require_once(XOOPS_ROOT_PATH . '/class/xml/xmltaghandler.php');

/**
 * Class XoopsXmlRss2Parser
 */
class XoopsXmlRss2Parser extends SaxParser
{
    /**
     * @var array
     */
    public $_tempArr     = array();
    /**
     * @var array
     */
    public $_channelData = array();
    /**
     * @var array
     */
    public $_imageData   = array();
    /**
     * @var array
     */
    public $_items       = array();

    /**
     * @param string $input
     */
    public function __construct($input)
    {
        parent::__construct($input);
        $this->useUtfEncoding();
        $this->addTagHandler(new RssChannelHandler());
        $this->addTagHandler(new RssTitleHandler());
        $this->addTagHandler(new RssLinkHandler());
        $this->addTagHandler(new RssGeneratorHandler());
        $this->addTagHandler(new RssDescriptionHandler());
        $this->addTagHandler(new RssCopyrightHandler());
        $this->addTagHandler(new RssNameHandler());
        $this->addTagHandler(new RssManagingEditorHandler());
        $this->addTagHandler(new RssLanguageHandler());
        $this->addTagHandler(new RssLastBuildDateHandler());
        $this->addTagHandler(new RssWebMasterHandler());
        $this->addTagHandler(new RssImageHandler());
        $this->addTagHandler(new RssUrlHandler());
        $this->addTagHandler(new RssWidthHandler());
        $this->addTagHandler(new RssHeightHandler());
        $this->addTagHandler(new RssItemHandler());
        $this->addTagHandler(new RssCategoryHandler());
        $this->addTagHandler(new RssPubDateHandler());
        $this->addTagHandler(new RssCommentsHandler());
        $this->addTagHandler(new RssSourceHandler());
        $this->addTagHandler(new RssAuthorHandler());
        $this->addTagHandler(new RssGuidHandler());
        $this->addTagHandler(new RssTextInputHandler());
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setChannelData($name, $value)
    {
        if (!isset($this->_channelData[$name])) {
            $this->_channelData[$name] = $value;
        } else {
            $this->_channelData[$name] .= $value;
        }
    }

    /**
     * @param string|null $name
     *
     * @return array|bool
     */
    public function &getChannelData($name = null)
    {
        $false = false;
        if (isset($name)) {
            if (isset($this->_channelData[$name])) {
                return $this->_channelData[$name];
            }

            return $false;
        }

        return $this->_channelData;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setImageData($name, &$value)
    {
        $this->_imageData[$name] =& $value;
    }

    /**
     * @param string|null $name
     *
     * @return array|bool
     */
    public function &getImageData($name = null)
    {
        $false = false;
        if (isset($name)) {
            if (isset($this->_imageData[$name])) {
                return $this->_imageData[$name];
            }
            return $false;
        }

        return $this->_imageData;
    }

    /**
     * @param array $itemarr
     */
    public function setItems(&$itemarr)
    {
        $this->_items[] =& $itemarr;
    }

    /**
     * @return array
     */
    public function &getItems()
    {
        return $this->_items;
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param string $delim
     */
    public function setTempArr($name, &$value, $delim = '')
    {
        if (!isset($this->_tempArr[$name])) {
            $this->_tempArr[$name] =& $value;
        } else {
            $this->_tempArr[$name] .= $delim . $value;
        }
    }

    /**
     * @return array
     */
    public function getTempArr()
    {
        return $this->_tempArr;
    }

    /**
     * @return void
     */
    public function resetTempArr()
    {
        unset($this->_tempArr);
        $this->_tempArr = array();
    }
}

/**
 * Class RssChannelHandler
 */
class RssChannelHandler extends XmlTagHandler
{
    /**
     * RssChannelHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'channel';
    }
}

/**
 * Class RssTitleHandler
 */
class RssTitleHandler extends XmlTagHandler
{
    /**
     * RssTitleHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'title';
    }

    /**
     * @param \XoopsXmlRss2Parser  $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'channel':
                $parser->setChannelData('title', $data);
                break;
            case 'image':
                $parser->setImageData('title', $data);
                break;
            case 'item':
            case 'textInput':
                $parser->setTempArr('title', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class RssLinkHandler
 */
class RssLinkHandler extends XmlTagHandler
{
    /**
     * RssLinkHandler constructor.
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
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'channel':
                $parser->setChannelData('link', $data);
                break;
            case 'image':
                $parser->setImageData('link', $data);
                break;
            case 'item':
            case 'textInput':
                $parser->setTempArr('link', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class RssDescriptionHandler
 */
class RssDescriptionHandler extends XmlTagHandler
{
    /**
     * RssDescriptionHandler constructor.
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
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'channel':
                $parser->setChannelData('description', $data);
                break;
            case 'image':
                $parser->setImageData('description', $data);
                break;
            case 'item':
            case 'textInput':
                $parser->setTempArr('description', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class RssGeneratorHandler
 */
class RssGeneratorHandler extends XmlTagHandler
{
    /**
     * RssGeneratorHandler constructor.
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
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'channel':
                $parser->setChannelData('generator', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class RssCopyrightHandler
 */
class RssCopyrightHandler extends XmlTagHandler
{
    /**
     * RssCopyrightHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'copyright';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'channel':
                $parser->setChannelData('copyright', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class RssNameHandler
 */
class RssNameHandler extends XmlTagHandler
{
    /**
     * RssNameHandler constructor.
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
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'textInput':
                $parser->setTempArr('name', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class RssManagingEditorHandler
 */
class RssManagingEditorHandler extends XmlTagHandler
{
    /**
     * RssManagingEditorHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'managingEditor';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'channel':
                $parser->setChannelData('editor', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class RssLanguageHandler
 */
class RssLanguageHandler extends XmlTagHandler
{
    /**
     * RssLanguageHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'language';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'channel':
                $parser->setChannelData('language', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class RssWebMasterHandler
 */
class RssWebMasterHandler extends XmlTagHandler
{
    /**
     * RssWebMasterHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'webMaster';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'channel':
                $parser->setChannelData('webmaster', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class RssDocsHandler
 */
class RssDocsHandler extends XmlTagHandler
{
    /**
     * RssDocsHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'docs';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'channel':
                $parser->setChannelData('docs', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class RssTtlHandler
 */
class RssTtlHandler extends XmlTagHandler
{
    /**
     * RssTtlHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ttl';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'channel':
                $parser->setChannelData('ttl', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class RssTextInputHandler
 */
class RssTextInputHandler extends XmlTagHandler
{
    /**
     * @return void
     */
    public function RssWebMasterHandler()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'textInput';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param array $attributes
     */
    public function handleBeginElement($parser, &$attributes)
    {
        $parser->resetTempArr();
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     */
    public function handleEndElement($parser)
    {
        $parser->setChannelData('textinput', $parser->getTempArr());
    }
}

/**
 * Class RssLastBuildDateHandler
 */
class RssLastBuildDateHandler extends XmlTagHandler
{
    /**
     * RssLastBuildDateHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'lastBuildDate';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'channel':
                $parser->setChannelData('lastbuilddate', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class RssImageHandler
 */
class RssImageHandler extends XmlTagHandler
{
    /**
     * RssImageHandler constructor.
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
}

/**
 * Class RssUrlHandler
 */
class RssUrlHandler extends XmlTagHandler
{
    /**
     * RssUrlHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'url';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        if ($parser->getParentTag() === 'image') {
            $parser->setImageData('url', $data);
        }
    }
}

/**
 * Class RssWidthHandler
 */
class RssWidthHandler extends XmlTagHandler
{
    /**
     * RssWidthHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'width';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        if ($parser->getParentTag() === 'image') {
            $parser->setImageData('width', $data);
        }
    }
}

/**
 * Class RssHeightHandler
 */
class RssHeightHandler extends XmlTagHandler
{
    /**
     * RssHeightHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'height';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        if ($parser->getParentTag() === 'image') {
            $parser->setImageData('height', $data);
        }
    }
}

/**
 * Class RssItemHandler
 */
class RssItemHandler extends XmlTagHandler
{
    /**
     * RssItemHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'item';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param array $attributes
     */
    public function handleBeginElement($parser, &$attributes)
    {
        $parser->resetTempArr();
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     */
    public function handleEndElement($parser)
    {
        $items = $parser->getTempArr();
        $parser->setItems($items);
    }
}

/**
 * Class RssCategoryHandler
 */
class RssCategoryHandler extends XmlTagHandler
{
    /**
     * RssCategoryHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'category';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'channel':
                $parser->setChannelData('category', $data);
                break;
            case 'item':
                $parser->setTempArr('category', $data, ', ');
                break;
            default:
                break;
        }
    }
}

/**
 * Class RssCommentsHandler
 */
class RssCommentsHandler extends XmlTagHandler
{
    /**
     * RssCommentsHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'comments';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        if ($parser->getParentTag() === 'item') {
            $parser->setTempArr('comments', $data);
        }
    }
}

/**
 * Class RssPubDateHandler
 */
class RssPubDateHandler extends XmlTagHandler
{
    /**
     * RssPubDateHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pubDate';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'channel':
                $parser->setChannelData('pubdate', $data);
                break;
            case 'item':
                $parser->setTempArr('pubdate', $data);
                break;
            default:
                break;
        }
    }
}

/**
 * Class RssGuidHandler
 */
class RssGuidHandler extends XmlTagHandler
{
    /**
     * RssGuidHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'guid';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        if ($parser->getParentTag() === 'item') {
            $parser->setTempArr('guid', $data);
        }
    }
}

/**
 * Class RssAuthorHandler
 */
class RssAuthorHandler extends XmlTagHandler
{
    /**
     * @return void
     */
    public function RssGuidHandler()
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
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        if ($parser->getParentTag() === 'item') {
            $parser->setTempArr('author', $data);
        }
    }
}

/**
 * Class RssSourceHandler
 */
class RssSourceHandler extends XmlTagHandler
{
    /**
     * RssSourceHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'source';
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param array $attributes
     */
    public function handleBeginElement($parser, &$attributes)
    {
        if ($parser->getParentTag() === 'item') {
            $parser->setTempArr('source_url', $attributes['url']);
        }
    }

    /**
     * @param \XoopsXmlRss2Parser $parser
     * @param  string $data
     */
    public function handleCharacterData($parser, &$data)
    {
        if ($parser->getParentTag() === 'item') {
            $parser->setTempArr('source', $data);
        }
    }
}
