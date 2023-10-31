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
 * @copyright       (c) 2000-2021 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * A block
 *
 * @author  Kazumi Ono <onokazu@xoops.org>
 *
 * @package kernel
 *
 * @todo reconcile the two XoopsBlock classes.
 * @internal This handler appears to only be loaded by system/class/group.php
 * @internal The other, in class/xoopsblock.php is loaded all over
 */
class XoopsBlock extends XoopsObject
{
    //PHP 8.2 Dynamic properties deprecated
    public $bid;
    public $mid;
    public $func_num;
    public $options;
    public $name;
    //public $position;
    public $title;
    public $content;
    public $side;
    public $weight;
    public $visible;
    public $block_type;
    public $c_type;
    public $isactive;
    public $dirname;
    public $func_file;
    public $show_func;
    public $edit_func;
    public $template;
    public $bcachetime;
    public $last_modified;

    /**
     * constructor
     *
     * @param mixed $id
     **/
    public function __construct($id = null)
    {
        $this->initVar('bid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('mid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('func_num', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('options', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 150);
        //$this->initVar('position', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('title', XOBJ_DTYPE_TXTBOX, null, false, 150);
        $this->initVar('content', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('side', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('visible', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('block_type', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('c_type', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('isactive', XOBJ_DTYPE_INT, null, false);
        $this->initVar('dirname', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('func_file', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('show_func', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('edit_func', XOBJ_DTYPE_TXTBOX, null, false, 50);
        $this->initVar('template', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('bcachetime', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('last_modified', XOBJ_DTYPE_INT, 0, false);

        parent::__construct();

        // for backward compatibility
        if (isset($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                $blkhandler = xoops_getHandler('block');
                $obj        = $blkhandler->get($id);
                foreach (array_keys($obj->getVars()) as $i) {
                    $this->assignVar($i, $obj->getVar($i, 'n'));
                }
            }
        }
    }

    /**
     * Returns Class Base Variable bid
     * @param string $format
     * @return mixed
     */
    public function id($format = 'n')
    {
        return $this->getVar('bid', $format);
    }

    /**
     * Returns Class Base Variable bid
     * @param string $format
     * @return mixed
     */
    public function bid($format = '')
    {
        return $this->getVar('bid', $format);
    }

    /**
     * Returns Class Base Variable mid
     * @param string $format
     * @return mixed
     */
    public function mid($format = '')
    {
        return $this->getVar('mid', $format);
    }

    /**
     * Returns Class Base Variable func_num
     * @param string $format
     * @return mixed
     */
    public function func_num($format = '')
    {
        return $this->getVar('func_num', $format);
    }

    /**
     * Returns Class Base Variable avatar_id
     * @param string $format
     * @return mixed
     */
    public function options($format = '')
    {
        return $this->getVar('options', $format);
    }

    /**
     * Returns Class Base Variable name
     * @param string $format
     * @return mixed
     */
    public function name($format = '')
    {
        return $this->getVar('name', $format);
    }

    /**
     * Returns Class Base Variable title
     * @param string $format
     * @return mixed
     */
    public function title($format = '')
    {
        return $this->getVar('title', $format);
    }

    /**
     * Returns Class Base Variable content
     * @param string $format
     * @return mixed
     */
    public function content($format = '')
    {
        return $this->getVar('content', $format);
    }

    /**
     * Returns Class Base Variable side
     * @param string $format
     * @return mixed
     */
    public function side($format = '')
    {
        return $this->getVar('side', $format);
    }

    /**
     * Returns Class Base Variable weight
     * @param string $format
     * @return mixed
     */
    public function weight($format = '')
    {
        return $this->getVar('weight', $format);
    }

    /**
     * Returns Class Base Variable visible
     * @param string $format
     * @return mixed
     */
    public function visible($format = '')
    {
        return $this->getVar('visible', $format);
    }

    /**
     * Returns Class Base Variable block_type
     *
     * Vaild block_type values are:
     * S - generated by system module
     * M - generated by a non-system module
     * C - Custom block
     * D - cloned system/module block
     * E - cloned custom block, DON'T use it
     *
     * @param string $format
     *
     * @return mixed
     */
    public function block_type($format = '')
    {
        return $this->getVar('block_type', $format);
    }

    /**
     * Returns Class Base Variable c_type
     * @param string $format
     * @return mixed
     */
    public function c_type($format = '')
    {
        return $this->getVar('c_type', $format);
    }

    /**
     * Returns Class Base Variable isactive
     * @param string $format
     * @return mixed
     */
    public function isactive($format = '')
    {
        return $this->getVar('isactive', $format);
    }

    /**
     * Returns Class Base Variable dirname
     * @param string $format
     * @return mixed
     */
    public function dirname($format = '')
    {
        return $this->getVar('dirname', $format);
    }

    /**
     * Returns Class Base Variable func_file
     * @param string $format
     * @return mixed
     */
    public function func_file($format = '')
    {
        return $this->getVar('func_file', $format);
    }

    /**
     * Returns Class Base Variable show_func
     * @param string $format
     * @return mixed
     */
    public function show_func($format = '')
    {
        return $this->getVar('show_func', $format);
    }

    /**
     * Returns Class Base Variable edit_func
     * @param string $format
     * @return mixed
     */
    public function edit_func($format = '')
    {
        return $this->getVar('edit_func', $format);
    }

    /**
     * Returns Class Base Variable template
     * @param string $format
     * @return mixed
     */
    public function template($format = '')
    {
        return $this->getVar('template', $format);
    }

    /**
     * Returns Class Base Variable avatar_id
     * @param string $format
     * @return mixed
     */
    public function bcachetime($format = '')
    {
        return $this->getVar('bcachetime', $format);
    }

    /**
     * Returns Class Base Variable last_modified
     * @param string $format
     * @return mixed
     */
    public function last_modified($format = '')
    {
        return $this->getVar('last_modified', $format);
    }

    /**
     * return the content of the block for output
     *
     * @param  string $format
     * @param  string $c_type type of content
     *                        Valid values for the type of content:
     *                        H : custom HTML block
     *                        P : custom PHP block
     *                        S : use text sanitizer (smilies enabled)
     *                        T : use text sanitizer (smilies disabled)</ul>
     * @return string content for output
     */
    public function getContent($format = 's', $c_type = 'T')
    {
        $format = strtolower($format);
        $c_type = strtoupper($c_type);
        switch ($format) {
            case 's':
                if ($c_type === 'H') {
                    return str_replace('{X_SITEURL}', XOOPS_URL . '/', $this->getVar('content', 'n'));
                } elseif ($c_type === 'P') {
                    ob_start();
                    echo eval($this->getVar('content', 'n'));
                    $content = ob_get_contents();
                    ob_end_clean();

                    return str_replace('{X_SITEURL}', XOOPS_URL . '/', $content);
                } elseif ($c_type === 'S') {
                    $myts    = \MyTextSanitizer::getInstance();
                    $content = str_replace('{X_SITEURL}', XOOPS_URL . '/', $this->getVar('content', 'n'));

                    return $myts->displayTarea($content, 0, 1);
                } else {
                    $myts    = \MyTextSanitizer::getInstance();
                    $content = str_replace('{X_SITEURL}', XOOPS_URL . '/', $this->getVar('content', 'n'));

                    return $myts->displayTarea($content, 0, 0);
                }
            case 'e':
                return $this->getVar('content', 'e');
            default:
                return $this->getVar('content', 'n');
        }
    }

    /**
     * (HTML-) form for setting the options of the block
     *
     * @return string HTML for the form, FALSE if not defined for this block
     */
    public function getOptions()
    {
        if (!$this->isCustom()) {
            $edit_func = $this->getVar('edit_func');
            if (!$edit_func) {
                return false;
            }
            if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $this->getVar('dirname') . '/blocks/' . $this->getVar('func_file'))) {
                if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $this->getVar('dirname') . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/blocks.php')) {
                    include_once XOOPS_ROOT_PATH . '/modules/' . $this->getVar('dirname') . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/blocks.php';
                } elseif (file_exists(XOOPS_ROOT_PATH . '/modules/' . $this->getVar('dirname') . '/language/english/blocks.php')) {
                    include_once XOOPS_ROOT_PATH . '/modules/' . $this->getVar('dirname') . '/language/english/blocks.php';
                }
                include_once XOOPS_ROOT_PATH . '/modules/' . $this->getVar('dirname') . '/blocks/' . $this->getVar('func_file');
                $options   = explode('|', $this->getVar('options'));
                $edit_form = $edit_func($options);
                if (!$edit_form) {
                    return false;
                }

                return $edit_form;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function isCustom()
    {
        return in_array($this->getVar('block_type'), array(
            'C',
            'E'));
    }

    /**
     * These methods are for compatibility with the pre 2.5.11 class/xoopsblock.php
     * class/xoopsblock.php defined its own XoopsBlock class, making it impossible
     * to use with anything that used the handler provided in kernel/block.php
     *
     * In addition to the actual data, the old XoopsBlock contained what should be
     * considered handler logic.
     *
     * It appears that class/xoopsblock.php came first, but a conversion to the kernel
     * handler was never completed.
     *
     * These methods should all be considered deprecated.
     */

    /**
     * Load $id
     *
     * @param int $id
     *
     * @deprecated
     */
    public function load($id)
    {
        $id  = (int)$id;
        /** @var XoopsBlockHandler $blkhandler */
        $blkhandler = xoops_getHandler('block');
        $obj        = $blkhandler->get($id);
        foreach (array_keys($obj->getVars()) as $i) {
            $this->assignVar($i, $obj->getVar($i, 'n'));
        }
    }

    /**
     * Store Block Data to Database
     *
     * @return int|false id of inserted block, or false on failure
     *
     * @deprecated
     */
    public function store()
    {
        /** @var XoopsBlockHandler $blkhandler */
        $blkhandler = xoops_getHandler('block');
        if (false === $blkhandler->insert($this)) {
            return false;
        }
        return (int) $this->bid();
    }

    /**
     * Delete an ID from the database
     *
     * @return bool
     *
     * @deprecated
     */
    public function delete()
    {
        /** @var XoopsBlockHandler $blkhandler */
        $blkhandler = xoops_getHandler('block');
        return $blkhandler->delete($this);
    }

    /**
     * Build Block
     *
     * @return mixed
     *
     * @deprecated
     */
    public function buildBlock()
    {
        global $xoopsConfig, $xoopsOption, $xoTheme;
        $block = array();
        if (!$this->isCustom()) {
            // get block display function
            $show_func = $this->getVar('show_func');
            if (!$show_func) {
                return false;
            }
            if (!file_exists($func_file = $GLOBALS['xoops']->path('modules/' . $this->getVar('dirname') . '/blocks/' . $this->getVar('func_file')))) {
                return false;
            }
            // must get lang files b4 including the file
            // some modules require it for code that is outside the function
            xoops_loadLanguage('blocks', $this->getVar('dirname'));
            include_once $func_file;

            if (function_exists($show_func)) {
                // execute the function
                $options = explode('|', $this->getVar('options'));
                $block   = $show_func($options);
                if (!$block) {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            // it is a custom block, so just return the contents
            $block['content'] = $this->getContent('s', $this->getVar('c_type'));
            if (empty($block['content'])) {
                return false;
            }
        }

        return $block;
    }

    /*
    * Aligns the content of a block
    * If position is 0, content in DB is positioned
    * before the original content
    * If position is 1, content in DB is positioned
    * after the original content
    */
    /**
     * @param        $position
     * @param string $content
     * @param string $contentdb
     *
     * @return string
     *
     * @deprecated
     */
    public function buildContent($position, $content = '', $contentdb = '')
    {
        $ret = null;
        if ($position == 0) {
            $ret = $contentdb . $content;
        } elseif ($position == 1) {
            $ret = $content . $contentdb;
        }

        return $ret;
    }

    /**
     * Enter description here...
     *
     * @param  string $originaltitle
     * @param  string $newtitle
     * @return string title
     *
     * @deprecated
     */
    public function buildTitle($originaltitle, $newtitle = '')
    {
        $ret = $originaltitle;
        if ($newtitle != '') {
            $ret = $newtitle;
        }

        return $ret;
    }

    /**
     * get all the blocks that match the supplied parameters
     * @param int|array $groupid  groupid (can be an array)
     * @param bool   $asobject
     * @param null|string $side     0: sideblock - left
     *                         1: sideblock - right
     *                         2: sideblock - left and right
     *                         3: centerblock - left
     *                         4: centerblock - right
     *                         5: centerblock - center
     *                         6: centerblock - left, right, center
     * @param        $visible  0: not visible 1: visible
     * @param string $orderby  order of the blocks
     * @param int    $isactive
     * @returns array of block objects
     *
     * @deprecated
     */
    public static function getAllBlocksByGroup($groupid, $asobject = true, $side = null, $visible = null, $orderby = 'b.weight,b.bid', $isactive = 1)
    {
        $db  = XoopsDatabaseFactory::getDatabaseConnection();
        $ret = array();
        $sql = 'SELECT b.* ';
        if (!$asobject) {
            $sql = 'SELECT b.bid ';
        }
        $sql .= 'FROM ' . $db->prefix('newblocks') . ' b LEFT JOIN ' . $db->prefix('group_permission') . " l ON l.gperm_itemid=b.bid WHERE gperm_name = 'block_read' AND gperm_modid = 1";
        if (is_array($groupid)) {
            $sql .= ' AND (l.gperm_groupid=' . $groupid[0] . '';
            $size = count($groupid);
            if ($size > 1) {
                for ($i = 1; $i < $size; ++$i) {
                    $sql .= ' OR l.gperm_groupid=' . $groupid[$i] . '';
                }
            }
            $sql .= ')';
        } else {
            $sql .= ' AND l.gperm_groupid=' . $groupid . '';
        }
        $sql .= ' AND b.isactive=' . $isactive;
        if (isset($side)) {
            // get both sides in sidebox? (some themes need this)
            if ($side == XOOPS_SIDEBLOCK_BOTH) {
                $side = '(b.side=0 OR b.side=1)';
            } elseif ($side == XOOPS_CENTERBLOCK_ALL) {
                $side = '(b.side=3 OR b.side=4 OR b.side=5 OR b.side=7 OR b.side=8 OR b.side=9 )';
            } elseif ($side == XOOPS_FOOTERBLOCK_ALL) {
                $side = '(b.side=10 OR b.side=11 OR b.side=12 )';
            } else {
                $side = 'b.side=' . $side;
            }
            $sql .= ' AND ' . $side;
        }
        if (isset($visible)) {
            $sql .= " AND b.visible=$visible";
        }
        $sql .= " ORDER BY $orderby";
        $result = $db->query($sql);
        if (!$db->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(), E_USER_ERROR
            );
        }
        $added  = array();
        while (false !== ($myrow = $db->fetchArray($result))) {
            if (!in_array($myrow['bid'], $added)) {
                if (!$asobject) {
                    $ret[] = $myrow['bid'];
                } else {
                    $ret[] = new XoopsBlock($myrow);
                }
                $added[] = $myrow['bid'];
            }
        }

        return $ret;
    }

    /**
     * XoopsBlock::getAllBlocks()
     *
     * @param  string  $rettype
     * @param  mixed   $side
     * @param  mixed   $visible
     * @param  string  $orderby
     * @param  integer $isactive
     * @return array
     *
     * @deprecated
     */
    public function getAllBlocks($rettype = 'object', $side = null, $visible = null, $orderby = 'side,weight,bid', $isactive = 1)
    {
        $db          = XoopsDatabaseFactory::getDatabaseConnection();
        $ret         = array();
        $where_query = ' WHERE isactive=' . $isactive;
        if (isset($side)) {
            // get both sides in sidebox? (some themes need this)
            if ($side == XOOPS_SIDEBLOCK_BOTH) {
                $side = '(side=0 OR side=1)';
            } elseif ($side == XOOPS_CENTERBLOCK_ALL) {
                $side = '(side=3 OR side=4 OR side=5 OR side=7 OR side=8 OR side=9)';
            } elseif ($side == XOOPS_FOOTERBLOCK_ALL) {
                $side = '(side=10 OR side=11 OR side=12)';
            } else {
                $side = 'side=' . $side;
            }
            $where_query .= ' AND ' . $side;
        }
        if (isset($visible)) {
            $where_query .= ' AND visible=.' . $visible;
        }
        $where_query .= ' ORDER BY ' . $orderby;
        switch ($rettype) {
            case 'object':
                $sql    = 'SELECT * FROM ' . $db->prefix('newblocks') . '' . $where_query;
                $result = $db->query($sql);
                if (!$db->isResultSet($result)) {
                    throw new \RuntimeException(
                        \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(), E_USER_ERROR
                    );
                }
                while (false !== ($myrow = $db->fetchArray($result))) {
                    $ret[] = new XoopsBlock($myrow);
                }
                break;
            case 'list':
                $sql    = 'SELECT * FROM ' . $db->prefix('newblocks') . '' . $where_query;
                $result = $db->query($sql);
                if (!$db->isResultSet($result)) {
                    throw new \RuntimeException(
                        \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(), E_USER_ERROR
                    );
                }
                while (false !== ($myrow = $db->fetchArray($result))) {
                    $block                      = new XoopsBlock($myrow);
                    $title                      = $block->getVar('title');
                    $title                      = empty($title) ? $block->getVar('name') : $title;
                    $ret[$block->getVar('bid')] = $title;
                }
                break;
            case 'id':
                $sql    = 'SELECT bid FROM ' . $db->prefix('newblocks') . '' . $where_query;
                $result = $db->query($sql);
                if (!$db->isResultSet($result)) {
                    throw new \RuntimeException(
                        \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(), E_USER_ERROR
                    );
                }
                while (false !== ($myrow = $db->fetchArray($result))) {
                    $ret[] = $myrow['bid'];
                }
                break;
        }

        return $ret;
    }

    /**
     * XoopsBlock::getByModule()
     *
     * @param  mixed $moduleid
     * @param  mixed $asobject
     * @return array
     */
    public static function getByModule($moduleid, $asobject = true)
    {
        $moduleid = (int)$moduleid;
        $db       = XoopsDatabaseFactory::getDatabaseConnection();
        if ($asobject == true) {
            $sql = $sql = 'SELECT * FROM ' . $db->prefix('newblocks') . ' WHERE mid=' . $moduleid;
        } else {
            $sql = 'SELECT bid FROM ' . $db->prefix('newblocks') . ' WHERE mid=' . $moduleid;
        }
        $result = $db->query($sql);
        if (!$db->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(), E_USER_ERROR
            );
        }
        $ret    = array();
        while (false !== ($myrow = $db->fetchArray($result))) {
            if ($asobject) {
                $ret[] = new XoopsBlock($myrow);
            } else {
                $ret[] = $myrow['bid'];
            }
        }

        return $ret;
    }

    /**
     * XoopsBlock::getAllByGroupModule()
     *
     * @param  mixed   $groupid
     * @param  integer $module_id
     * @param  mixed   $toponlyblock
     * @param  mixed   $visible
     * @param  string  $orderby
     * @param  integer $isactive
     * @return array
     *
     * @deprecated (This also appears, dead, in XoopsBlockHandler)
     */
    public function getAllByGroupModule($groupid, $module_id = 0, $toponlyblock = false, $visible = null, $orderby = 'b.weight, m.block_id', $isactive = 1)
    {
        $isactive = (int)$isactive;
        $db       = XoopsDatabaseFactory::getDatabaseConnection();
        $ret      = array();
        if (isset($groupid)) {
            $sql = 'SELECT DISTINCT gperm_itemid FROM ' . $db->prefix('group_permission') . " WHERE gperm_name = 'block_read' AND gperm_modid = 1";
            if (is_array($groupid)) {
                $sql .= ' AND gperm_groupid IN (' . implode(',', $groupid) . ')';
            } else {
                if ((int)$groupid > 0) {
                    $sql .= ' AND gperm_groupid=' . (int)$groupid;
                }
            }
            $result   = $db->query($sql);
            if (!$db->isResultSet($result)) {
                throw new \RuntimeException(
                    \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(), E_USER_ERROR
                );
            }
            $blockids = array();
            while (false !== ($myrow = $db->fetchArray($result))) {
                $blockids[] = $myrow['gperm_itemid'];
            }
            if (empty($blockids)) {
                return $blockids;
            }
        }
        $sql = 'SELECT b.* FROM ' . $db->prefix('newblocks') . ' b, ' . $db->prefix('block_module_link') . ' m WHERE m.block_id=b.bid';
        $sql .= ' AND b.isactive=' . $isactive;
        if (isset($visible)) {
            $sql .= ' AND b.visible=' . (int)$visible;
        }
        if (!isset($module_id)) {
        } elseif (!empty($module_id)) {
            $sql .= ' AND m.module_id IN (0,' . (int)$module_id;
            if ($toponlyblock) {
                $sql .= ',-1';
            }
            $sql .= ')';
        } else {
            if ($toponlyblock) {
                $sql .= ' AND m.module_id IN (0,-1)';
            } else {
                $sql .= ' AND m.module_id=0';
            }
        }
        if (!empty($blockids)) {
            $sql .= ' AND b.bid IN (' . implode(',', $blockids) . ')';
        }
        $sql .= ' ORDER BY ' . $orderby;
        $result = $db->query($sql);
        if (!$db->isResultSet($result)) {
            throw new \RuntimeException(
                \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(), E_USER_ERROR
            );
        }
        while (false !== ($myrow = $db->fetchArray($result))) {
            $block              = new XoopsBlock($myrow);
            $ret[$myrow['bid']] = &$block;
            unset($block);
        }

        return $ret;
    }

    /**
     * XoopsBlock::getNonGroupedBlocks()
     *
     * @param  integer $module_id
     * @param  mixed   $toponlyblock
     * @param  mixed   $visible
     * @param  string  $orderby
     * @param  integer $isactive
     * @return array
     *
     * @deprecated
     */
    public function getNonGroupedBlocks($module_id = 0, $toponlyblock = false, $visible = null, $orderby = 'b.weight, m.block_id', $isactive = 1)
    {
        $db   = XoopsDatabaseFactory::getDatabaseConnection();
        $ret  = array();
        $bids = array();
        $sql  = 'SELECT DISTINCT(bid) from ' . $db->prefix('newblocks');
        $result = $db->query($sql);
        if ($db->isResultSet($result)) {
            while (false !== ($myrow = $db->fetchArray($result))) {
                $bids[] = $myrow['bid'];
            }
        }

        $sql     = 'SELECT DISTINCT(p.gperm_itemid) from ' . $db->prefix('group_permission') . ' p, ' . $db->prefix('groups') . " g WHERE g.groupid=p.gperm_groupid AND p.gperm_name='block_read'";
        $grouped = array();
        $result  = $db->query($sql);
        if ($db->isResultSet($result)) {
            while (false !== ($myrow = $db->fetchArray($result))) {
                $grouped[] = $myrow['gperm_itemid'];
            }
        }


        $non_grouped = array_diff($bids, $grouped);
        if (!empty($non_grouped)) {
            $sql = 'SELECT b.* FROM ' . $db->prefix('newblocks') . ' b, ' . $db->prefix('block_module_link') . ' m WHERE m.block_id=b.bid';
            $sql .= ' AND b.isactive=' . (int)$isactive;
            if (isset($visible)) {
                $sql .= ' AND b.visible=' . (int)$visible;
            }
            if (!isset($module_id)) {
            } elseif (!empty($module_id)) {
                $sql .= ' AND m.module_id IN (0,' . (int)$module_id;
                if ($toponlyblock) {
                    $sql .= ',-1';
                }
                $sql .= ')';
            } else {
                if ($toponlyblock) {
                    $sql .= ' AND m.module_id IN (0,-1)';
                } else {
                    $sql .= ' AND m.module_id=0';
                }
            }
            $sql .= ' AND b.bid IN (' . implode(',', $non_grouped) . ')';
            $sql .= ' ORDER BY ' . $orderby;
            $result = $db->query($sql);
            if (!$db->isResultSet($result)) {
                throw new \RuntimeException(
                    \sprintf(_DB_QUERY_ERROR, $sql) . $db->error(), E_USER_ERROR
                );
            }
            while (false !== ($myrow = $db->fetchArray($result))) {
                $block              = new XoopsBlock($myrow);
                $ret[$myrow['bid']] =& $block;
                unset($block);
            }
        }

        return $ret;
    }

    /**
     * XoopsBlock::countSimilarBlocks()
     *
     * @param  mixed $moduleId
     * @param  mixed $funcNum
     * @param  mixed $showFunc
     * @return int
     *
     * @deprecated
     */
    public function countSimilarBlocks($moduleId, $funcNum, $showFunc = null)
    {
        $funcNum  = (int)$funcNum;
        $moduleId = (int)$moduleId;
        if ($funcNum < 1 || $moduleId < 1) {
            // invalid query
            return 0;
        }
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        if (isset($showFunc)) {
            // showFunc is set for more strict comparison
            $sql = sprintf('SELECT COUNT(*) FROM %s WHERE mid = %d AND func_num = %d AND show_func = %s', $db->prefix('newblocks'), $moduleId, $funcNum, $db->quoteString(trim($showFunc)));
        } else {
            $sql = sprintf('SELECT COUNT(*) FROM %s WHERE mid = %d AND func_num = %d', $db->prefix('newblocks'), $moduleId, $funcNum);
        }
        $result = $db->query($sql);
        if (!$db->isResultSet($result)) {
            return 0;
        }
        list($count) = $db->fetchRow($result);

        return (int)$count;
    }
}

/**
 * XOOPS block handler class. (Singelton)
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS block class objects.
 *
 * @author              Kazumi Ono <onokazu@xoops.org>
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @package             kernel
 * @subpackage          block
 *
 * @todo Why is this not a XoopsPersistableObjectHandler?
 */
class XoopsBlockHandler extends XoopsObjectHandler
{
    /**
     * create a new block
     *
     * @see XoopsBlock
     * @param  bool $isNew is the new block new??
     * @return XoopsBlock XoopsBlock reference to the new block
     **/
    public function create($isNew = true)
    {
        $block = new XoopsBlock();
        if ($isNew) {
            $block->setNew();
        }

        return $block;
    }

    /**
     * retrieve a specific {@link XoopsBlock}
     *
     * @see XoopsBlock
     * @param  int $id bid of the block to retrieve
     * @return XoopsBlock reference to the block
     **/
    public function get($id)
    {
        $block = false;
        $id    = (int)$id;
        if ($id > 0) {
            $sql    = 'SELECT * FROM ' . $this->db->prefix('newblocks') . ' WHERE bid=' . $id;
            $result = $this->db->query($sql);
            if ($this->db->isResultSet($result)) {
                $numrows = $this->db->getRowsNum($result);
                if ($numrows == 1) {
                    $block = new XoopsBlock();
                    $block->assignVars($this->db->fetchArray($result));
                }
            }
        }

        return $block;
    }

    /**
     * write a new block into the database
     *
     * @param XoopsObject|XoopsBlock $block a XoopsBlock object
     *
     * @return bool true on success, otherwise false
     */
    public function insert(XoopsObject $block)
    {
        $className = 'XoopsBlock';
        if (!($block instanceof $className)) {
            return false;
        }
        if (!$block->isDirty()) {
            return true;
        }
        if (!$block->cleanVars()) {
            return false;
        }

        $bid = $block->getVar('bid', 'n');
        $mid = $block->getVar('mid', 'n');
        $func_num = $block->getVar('func_num', 'n');
        $options = $block->getVar('options', 'n');
        $name = $block->getVar('name', 'n');
        $title = $block->getVar('title', 'n');
        $content = $block->getVar('content', 'n');
        $side = $block->getVar('side', 'n');
        $weight = $block->getVar('weight', 'n');
        $visible = $block->getVar('visible', 'n');
        $c_type = $block->getVar('c_type', 'n');
        $isactive = $block->getVar('isactive', 'n');
        $func_file = $block->getVar('func_file', 'n');
        $show_func = $block->getVar('show_func', 'n');
        $edit_func = $block->getVar('edit_func', 'n');
        $template = $block->getVar('template', 'n');
        $bcachetime = $block->getVar('bcachetime', 'n');
        $block_type = $block->getVar('block_type', 'n');
        $dirname = $block->getVar('dirname', 'n');

        if ($block->isNew()) {
            $bid = $this->db->genId('newblocks_bid_seq');
            $sql = sprintf(
                'INSERT INTO %s (bid, mid, func_num, options, name, title, content, side, weight, visible, block_type,'
                . ' c_type, isactive, dirname, func_file, show_func, edit_func, template, bcachetime, last_modified)'
                . " VALUES (%u, %u, %u, '%s', '%s', '%s', '%s', %u, %u, %u, '%s', '%s', %u, '%s', '%s', '%s', '%s',"
                . " '%s', %u, %u)",
                $this->db->prefix('newblocks'),
                $bid,
                $mid,
                $func_num,
                $options,
                $name,
                $title,
                $content,
                $side,
                $weight,
                $visible,
                $block_type,
                $c_type,
                1,
                $dirname,
                $func_file,
                $show_func,
                $edit_func,
                $template,
                $bcachetime,
                time()
            );
        } else {
            $sql = sprintf(
                "UPDATE %s SET func_num = %u, options = '%s', name = '%s', title = '%s', content = '%s', side = %u,"
                . " weight = %u, visible = %u, c_type = '%s', isactive = %u, func_file = '%s', show_func = '%s',"
                . " edit_func = '%s', template = '%s', bcachetime = %u, last_modified = %u WHERE bid = %u",
                $this->db->prefix('newblocks'),
                $func_num,
                $options,
                $name,
                $title,
                $content,
                $side,
                $weight,
                $visible,
                $c_type,
                $isactive,
                $func_file,
                $show_func,
                $edit_func,
                $template,
                $bcachetime,
                time(),
                $bid
            );
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($bid)) {
            $bid = $this->db->getInsertId();
        }
        $block->assignVar('bid', $bid);

        return true;
    }

    /**
     * delete a block from the database
     *
     * @param XoopsObject|XoopsBlock $block a XoopsBlock object
     *
     * @return bool true on success, otherwise false
     */
    public function delete(XoopsObject $block)
    {
        $className = 'XoopsBlock';
        if (!($block instanceof $className)) {
            return false;
        }
        $id  = $block->getVar('bid');
        $sql = sprintf('DELETE FROM %s WHERE bid = %u', $this->db->prefix('newblocks'), $id);
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE block_id = %u', $this->db->prefix('block_module_link'), $id);
        $this->db->query($sql);

        return true;
    }

    /**
     * retrieve array of {@link XoopsBlock}s meeting certain conditions
     * @param  CriteriaElement|CriteriaCompo $criteria  {@link CriteriaElement} with conditions for the blocks
     * @param  bool   $id_as_key should the blocks' bid be the key for the returned array?
     * @return array  {@link XoopsBlock}s matching the conditions
     **/
    public function getObjects(CriteriaElement $criteria = null, $id_as_key = false)
    {
        $ret   = array();
        $limit = $start = 0;
        $sql   = 'SELECT DISTINCT(b.bid), b.* FROM ' . $this->db->prefix('newblocks') . ' b LEFT JOIN '
            . $this->db->prefix('block_module_link') . ' l ON b.bid=l.block_id';
        if (isset($criteria) && \method_exists($criteria, 'renderWhere')) {
            $sql .= ' ' . $criteria->renderWhere();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$this->db->isResultSet($result)) {
            return $ret;
        }
        /** @var array $myrow */
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $block = new XoopsBlock();
            $block->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $block;
            } else {
                $ret[$myrow['bid']] = &$block;
            }
            unset($block);
        }

        return $ret;
    }

    /**
     * get a list of blocks matchich certain conditions
     *
     * @param  CriteriaElement $criteria conditions to match
     * @return array  array of blocks matching the conditions
     **/
    public function getList(CriteriaElement $criteria = null)
    {
        $blocks = $this->getObjects($criteria, true);
        $ret    = array();
        foreach (array_keys($blocks) as $i) {
            $name    = (!$blocks[$i]->isCustom()) ? $blocks[$i]->getVar('name') : $blocks[$i]->getVar('title');
            $ret[$i] = $name;
        }

        return $ret;
    }

    ##################### Deprecated Methods ######################
    /* These are not deprecated, they are dead and should be removed */
    /**
     * @deprecated
     * @param      $moduleid
     * @param bool $asobject
     * @param bool $id_as_key
     * @return bool
     */
    public function getByModule($moduleid, $asobject = true, $id_as_key = false)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @param        $groupid
     * @param int    $module_id
     * @param bool   $toponlyblock
     * @param null   $visible
     * @param string $orderby
     * @param int    $isactive
     *
     * @return bool
     * @deprecated
     */
    public function getAllByGroupModule($groupid, $module_id = 0, $toponlyblock = false, $visible = null, $orderby = 'i.weight,i.instanceid', $isactive = 1)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @param        $groupid
     * @param string $orderby
     *
     * @return bool
     * @deprecated
     */
    public function getAdminBlocks($groupid, $orderby = 'i.weight,i.instanceid')
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }

    /**
     * @return bool
     * @deprecated
     */
    public function assignBlocks()
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return false;
    }
}
