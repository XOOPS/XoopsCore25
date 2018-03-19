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
 * An Image
 *
 * @package          kernel
 * @author           Kazumi Ono     <onokazu@xoops.org>
 * @copyright    (c) 2000-2016 XOOPS Project - www.xoops.org
 */
class XoopsImage extends XoopsObject
{
    /**
     * Constructor
     **/
    public function __construct()
    {
        parent::__construct();
        $this->initVar('image_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('image_name', XOBJ_DTYPE_OTHER, null, false, 30);
        $this->initVar('image_nicename', XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('image_mimetype', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('image_created', XOBJ_DTYPE_INT, null, false);
        $this->initVar('image_display', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('image_weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('image_body', XOBJ_DTYPE_SOURCE, null, true);
        $this->initVar('imgcat_id', XOBJ_DTYPE_INT, 0, false);
    }

    /**
     * Returns Class Base Variable image_id
     * @param  string $format
     * @return mixed
     */
    public function id($format = 'N')
    {
        return $this->getVar('image_id', $format);
    }

    /**
     * Returns Class Base Variable image_id
     * @param  string $format
     * @return mixed
     */
    public function image_id($format = '')
    {
        return $this->getVar('image_id', $format);
    }

    /**
     * Returns Class Base Variable image_name
     * @param  string $format
     * @return mixed
     */
    public function image_name($format = '')
    {
        return $this->getVar('image_name', $format);
    }

    /**
     * Returns Class Base Variable image_nicename
     * @param  string $format
     * @return mixed
     */
    public function image_nicename($format = '')
    {
        return $this->getVar('image_nicename', $format);
    }

    /**
     * Returns Class Base Variable image_mimetype
     * @param  string $format
     * @return mixed
     */
    public function image_mimetype($format = '')
    {
        return $this->getVar('image_mimetype', $format);
    }

    /**
     * Returns Class Base Variable image_created
     * @param  string $format
     * @return mixed
     */
    public function image_created($format = '')
    {
        return $this->getVar('image_created', $format);
    }

    /**
     * Returns Class Base Variable image_display
     * @param  string $format
     * @return mixed
     */
    public function image_display($format = '')
    {
        return $this->getVar('image_display', $format);
    }

    /**
     * Returns Class Base Variable image_weight
     * @param  string $format
     * @return mixed
     */
    public function image_weight($format = '')
    {
        return $this->getVar('image_weight', $format);
    }

    /**
     * Returns Class Base Variable image_body
     * @param  string $format
     * @return mixed
     */
    public function image_body($format = '')
    {
        return $this->getVar('image_body', $format);
    }

    /**
     * Returns Class Base Variable imgcat_id
     * @param  string $format
     * @return mixed
     */
    public function imgcat_id($format = '')
    {
        return $this->getVar('imgcat_id', $format);
    }
}

/**
 * XOOPS image handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS image class objects.
 *
 * @package          kernel
 *
 * @author           Kazumi Ono     <onokazu@xoops.org>
 * @copyright    (c) 2000-2016 XOOPS Project - www.xoops.org
 */
class XoopsImageHandler extends XoopsObjectHandler
{
    /**
     * Create a new {@link XoopsImage}
     *
     * @param  boolean $isNew Flag the object as "new"
     * @return XoopsImage
     **/
    public function create($isNew = true)
    {
        $image = new XoopsImage();
        if ($isNew) {
            $image->setNew();
        }

        return $image;
    }

    /**
     * Load a {@link XoopsImage} object from the database
     *
     * @param  int     $id ID
     * @param  boolean $getbinary
     * @return XoopsImage {@link XoopsImage}, FALSE on fail
     **/
    public function get($id, $getbinary = true)
    {
        $image = false;
        $id    = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT i.*, b.image_body FROM ' . $this->db->prefix('image') . ' i LEFT JOIN ' . $this->db->prefix('imagebody') . ' b ON b.image_id=i.image_id WHERE i.image_id=' . $id;
            if (!$result = $this->db->query($sql)) {
                return $image;
            }
            $numrows = $this->db->getRowsNum($result);
            if ($numrows == 1) {
                $image = new XoopsImage();
                $image->assignVars($this->db->fetchArray($result));
            }
        }

        return $image;
    }

    /**
     * Write a {@link XoopsImage} object to the database
     *
     * @param  XoopsObject|XoopsImage $image a XoopsImage object
     *
     * @return bool true on success, otherwise false
     **/
    public function insert(XoopsObject $image)
    {
        $className = 'XoopsImage';
        if (!($image instanceof $className)) {
            return false;
        }

        if (!$image->isDirty()) {
            return true;
        }
        if (!$image->cleanVars()) {
            return false;
        }
        foreach ($image->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($image->isNew()) {
            $image_id = $this->db->genId('image_image_id_seq');
            $sql      = sprintf('INSERT INTO %s (image_id, image_name, image_nicename, image_mimetype, image_created, image_display, image_weight, imgcat_id) VALUES (%u, %s, %s, %s, %u, %u, %u, %u)', $this->db->prefix('image'), $image_id, $this->db->quoteString($image_name), $this->db->quoteString($image_nicename), $this->db->quoteString($image_mimetype), time(), $image_display, $image_weight, $imgcat_id);
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            if (empty($image_id)) {
                $image_id = $this->db->getInsertId();
            }
            if (isset($image_body) && $image_body != '') {
                $sql = sprintf('INSERT INTO %s (image_id, image_body) VALUES (%u, %s)', $this->db->prefix('imagebody'), $image_id, $this->db->quoteString($image_body));
                if (!$result = $this->db->query($sql)) {
                    $sql = sprintf('DELETE FROM %s WHERE image_id = %u', $this->db->prefix('image'), $image_id);
                    $this->db->query($sql);

                    return false;
                }
            }
            $image->assignVar('image_id', $image_id);
        } else {
            $sql = sprintf('UPDATE %s SET image_name = %s, image_nicename = %s, image_display = %u, image_weight = %u, imgcat_id = %u WHERE image_id = %u', $this->db->prefix('image'), $this->db->quoteString($image_name), $this->db->quoteString($image_nicename), $image_display, $image_weight, $imgcat_id, $image_id);
            if (!$result = $this->db->query($sql)) {
                return false;
            }
            if (isset($image_body) && $image_body != '') {
                $sql = sprintf('UPDATE %s SET image_body = %s WHERE image_id = %u', $this->db->prefix('imagebody'), $this->db->quoteString($image_body), $image_id);
                if (!$result = $this->db->query($sql)) {
                    $this->db->query(sprintf('DELETE FROM %s WHERE image_id = %u', $this->db->prefix('image'), $image_id));

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Delete an image from the database
     *
     * @param  XoopsObject|XoopsImage $image a XoopsImage object
     *
     * @return bool true on success, otherwise false
     **/
    public function delete(XoopsObject $image)
    {
        $className = 'XoopsImage';
        if (!($image instanceof $className)) {
            return false;
        }

        $id  = $image->getVar('image_id');
        $sql = sprintf('DELETE FROM %s WHERE image_id = %u', $this->db->prefix('image'), $id);
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE image_id = %u', $this->db->prefix('imagebody'), $id);
        $this->db->query($sql);

        return true;
    }

    /**
     * Load {@link XoopsImage}s from the database
     *
     * @param  CriteriaElement|CriteriaCompo $criteria  {@link CriteriaElement}
     * @param  boolean         $id_as_key Use the ID as key into the array
     * @param  boolean         $getbinary
     * @return array           Array of {@link XoopsImage} objects
     **/
    public function getObjects(CriteriaElement $criteria = null, $id_as_key = false, $getbinary = false)
    {
        $ret   = array();
        $limit = $start = 0;
        if ($getbinary) {
            $sql = 'SELECT i.*, b.image_body FROM ' . $this->db->prefix('image') . ' i LEFT JOIN ' . $this->db->prefix('imagebody') . ' b ON b.image_id=i.image_id';
        } else {
            $sql = 'SELECT * FROM ' . $this->db->prefix('image');
        }
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
            $sort = $criteria->getSort() == '' ? 'image_weight' : $criteria->getSort();
            $sql .= ' ORDER BY ' . $sort . ' ' . $criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while (false !== ($myrow = $this->db->fetchArray($result))) {
            $image = new XoopsImage();
            $image->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $image;
            } else {
                $ret[$myrow['image_id']] =& $image;
            }
            unset($image);
        }

        return $ret;
    }

    /**
     * Count some images
     *
     * @param  CriteriaElement|CriteriaCompo $criteria {@link CriteriaElement}
     * @return int
     **/
    public function getCount(CriteriaElement $criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('image');
        if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
            $sql .= ' ' . $criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return 0;
        }
        list($count) = $this->db->fetchRow($result);

        return $count;
    }

    /**
     * Get a list of images
     *
     * @param  int  $imgcat_id
     * @param  bool $image_display
     * @return array Array of {@link XoopsImage} objects
     **/
    public function getList($imgcat_id, $image_display = null)
    {
        $criteria = new CriteriaCompo(new Criteria('imgcat_id', (int)$imgcat_id));
        if (isset($image_display)) {
            $criteria->add(new Criteria('image_display', (int)$image_display));
        }
        $images = $this->getObjects($criteria, false, true);
        $ret    = array();
        foreach (array_keys($images) as $i) {
            $ret[$images[$i]->getVar('image_name')] = $images[$i]->getVar('image_nicename');
        }

        return $ret;
    }
}
