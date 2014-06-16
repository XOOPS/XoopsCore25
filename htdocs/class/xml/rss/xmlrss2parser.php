<?php
// $Id$
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");
require_once(XOOPS_ROOT_PATH.'/class/xml/saxparser.php');
require_once(XOOPS_ROOT_PATH.'/class/xml/xmltaghandler.php');

/**
 * Class XoopsXmlRss2Parser
 */
class XoopsXmlRss2Parser extends SaxParser
{
    var $_tempArr = array();
    var $_channelData = array();
    var $_imageData = array();
    var $_items = array();

    /**
     * @param $input
     */
    function XoopsXmlRss2Parser(&$input)
    {
        $this->SaxParser($input);
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
     * @param $name
     * @param $value
     */
    function setChannelData($name, &$value)
    {
        if (!isset($this->_channelData[$name])) {
            $this->_channelData[$name] =& $value;
        } else {
            $this->_channelData[$name] .= $value;
        }
    }

    /**
     * @param null $name
     *
     * @return array|bool
     */
    function &getChannelData($name = null)
    {
        if (isset($name)) {
            if (isset($this->_channelData[$name])) {
                return $this->_channelData[$name];
            }

            return false;
        }

        return $this->_channelData;
    }

    /**
     * @param $name
     * @param $value
     */
    function setImageData($name, &$value)
    {
        $this->_imageData[$name] =& $value;
    }

    /**
     * @param null $name
     *
     * @return array|bool
     */
    function &getImageData($name = null)
    {
        if (isset($name)) {
            if (isset($this->_imageData[$name])) {
                return $this->_imageData[$name];
            }
            $return = false;

            return $return;
        }

        return $this->_imageData;
    }

    /**
     * @param $itemarr
     */
    function setItems(&$itemarr)
    {
        $this->_items[] =& $itemarr;
    }

    /**
     * @return array
     */
    function &getItems()
    {
        return $this->_items;
    }

    /**
     * @param        $name
     * @param        $value
     * @param string $delim
     */
    function setTempArr($name, &$value, $delim = '')
    {
        if (!isset($this->_tempArr[$name])) {
            $this->_tempArr[$name] =& $value;
        } else {
            $this->_tempArr[$name] .= $delim.$value;
        }
    }

    /**
     * @return array
     */
    function getTempArr()
    {
        return $this->_tempArr;
    }

    function resetTempArr()
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

    function RssChannelHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'channel';
    }
}

/**
 * Class RssTitleHandler
 */
class RssTitleHandler extends XmlTagHandler
{

    function RssTitleHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'title';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
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

    function RssLinkHandler()
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

    function RssDescriptionHandler()
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

    function RssGeneratorHandler()
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

    function RssCopyrightHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'copyright';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
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

    function RssNameHandler()
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

    function RssManagingEditorHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'managingEditor';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
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

    function RssLanguageHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'language';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
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

    function RssWebMasterHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'webMaster';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
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

    function RssDocsHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'docs';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
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

    function RssTtlHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'ttl';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
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

    function RssWebMasterHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'textInput';
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
        $parser->setChannelData('textinput', $parser->getTempArr());
    }
}

/**
 * Class RssLastBuildDateHandler
 */
class RssLastBuildDateHandler extends XmlTagHandler
{

    function RssLastBuildDateHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'lastBuildDate';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
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

    function RssImageHandler()
    {
    }

    /**
     * @return string
     */
    function getName()
    {
        return 'image';
    }
}

/**
 * Class RssUrlHandler
 */
class RssUrlHandler extends XmlTagHandler
{

    function RssUrlHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'url';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'image') {
            $parser->setImageData('url', $data);
        }
    }
}

/**
 * Class RssWidthHandler
 */
class RssWidthHandler extends XmlTagHandler
{

    function RssWidthHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'width';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'image') {
            $parser->setImageData('width', $data);
        }
    }
}

/**
 * Class RssHeightHandler
 */
class RssHeightHandler extends XmlTagHandler
{

    function RssHeightHandler()
    {
    }

    /**
     * @return string
     */
    function getName()
    {
        return 'height';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'image') {
            $parser->setImageData('height', $data);
        }
    }
}

/**
 * Class RssItemHandler
 */
class RssItemHandler extends XmlTagHandler
{

    function RssItemHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'item';
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
        $items =& $parser->getTempArr();
        $parser->setItems( $items );
    }
}

/**
 * Class RssCategoryHandler
 */
class RssCategoryHandler extends XmlTagHandler
{

    function RssCategoryHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'category';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
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

    function RssCommentsHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'comments';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'item') {
            $parser->setTempArr('comments', $data);
        }
    }
}

/**
 * Class RssPubDateHandler
 */
class RssPubDateHandler extends XmlTagHandler
{

    function RssPubDateHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'pubDate';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
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

    function RssGuidHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'guid';
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'item') {
            $parser->setTempArr('guid', $data);
        }
    }
}

/**
 * Class RssAuthorHandler
 */
class RssAuthorHandler extends XmlTagHandler
{

    function RssGuidHandler()
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
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'item') {
            $parser->setTempArr('author', $data);
        }
    }
}

/**
 * Class RssSourceHandler
 */
class RssSourceHandler extends XmlTagHandler
{

    function RssSourceHandler()
    {

    }

    /**
     * @return string
     */
    function getName()
    {
        return 'source';
    }

    /**
     * @param $parser
     * @param $attributes
     */
    function handleBeginElement(&$parser, &$attributes)
    {
        if ($parser->getParentTag() == 'item') {
            $parser->setTempArr('source_url', $attributes['url']);
        }
    }

    /**
     * @param $parser
     * @param $data
     */
    function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'item') {
            $parser->setTempArr('source', $data);
        }
    }
}
