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
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
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
     * @param string $format
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
     * @param  string $c_type type of content<br>
     *                        Legal value for the type of content<br>
     *                        <ul><li>H : custom HTML block
     *                        <li>P : custom PHP block
     *                        <li>S : use text sanitizater (smilies enabled)
     *                        <li>T : use text sanitizater (smilies disabled)</ul>
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
                    $myts    = MyTextSanitizer::getInstance();
                    $content = str_replace('{X_SITEURL}', XOOPS_URL . '/', $this->getVar('content', 'n'));

                    return $myts->displayTarea($content, 0, 1);
                } else {
                    $myts    = MyTextSanitizer::getInstance();
                    $content = str_replace('{X_SITEURL}', XOOPS_URL . '/', $this->getVar('content', 'n'));

                    return $myts->displayTarea($content, 0, 0);
                }
                break;
            case 'e':
                return $this->getVar('content', 'e');
                break;
            default:
                return $this->getVar('content', 'n');
                break;
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
            $sql = 'SELECT * FROM ' . $this->db->prefix('newblocks') . ' WHERE bid=' . $id;
            if ($result = $this->db->query($sql)) {
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
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
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

    /**#@+
     * @deprecated
     * @param      $moduleid
     * @param bool $asobject
     * @param bool $id_as_key
     * @return bool
     */
    public function getByModule($moduleid, $asobject = true, $id_as_key = false)
    {
        trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

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
     */
    public function getAllByGroupModule($groupid, $module_id = 0, $toponlyblock = false, $visible = null, $orderby = 'i.weight,i.instanceid', $isactive = 1)
    {
        trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @param        $groupid
     * @param string $orderby
     *
     * @return bool
     */
    public function getAdminBlocks($groupid, $orderby = 'i.weight,i.instanceid')
    {
        trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }

    /**
     * @return bool
     */
    public function assignBlocks()
    {
        trigger_error(__CLASS__ . '::' . __FUNCTION__ . ' is deprecated', E_USER_WARNING);

        return false;
    }
    /**#@-*/
}
