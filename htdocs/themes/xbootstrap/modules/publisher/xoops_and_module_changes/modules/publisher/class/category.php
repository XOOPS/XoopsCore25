<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 * @copyright           The XUUPS Project http://sourceforge.net/projects/xuups/
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             Publisher
 * @since               1.0
 * @author              trabis <lusopoemas@gmail.com>
 * @author              The SmartFactory <www.smartfactory.ca>
 */
defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

include_once dirname(__DIR__) . '/include/common.php';

/**
 * Class PublisherCategory
 */

/**
 * Class PublisherCategory
 */
class PublisherCategory extends XoopsObject
{
    /**
     * @var PublisherPublisher
     * @access public
     */
    public $publisher;

    /**
     * @var array
     */
    public $_categoryPath = false;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->publisher = PublisherPublisher::getInstance();
        $this->initVar('categoryid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('parentid', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA, null, false, 255);
        $this->initVar('image', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('total', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('weight', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('created', XOBJ_DTYPE_INT, null, false);
        $this->initVar('template', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('header', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('meta_keywords', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('meta_description', XOBJ_DTYPE_TXTAREA, null, false);
        $this->initVar('short_url', XOBJ_DTYPE_TXTBOX, null, false, 255);
        $this->initVar('moderator', XOBJ_DTYPE_INT, null, false, 0);
        //not persistent values
        $this->initVar('itemcount', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('last_itemid', XOBJ_DTYPE_INT);
        $this->initVar('last_title_link', XOBJ_DTYPE_TXTBOX);
        $this->initVar('dohtml', XOBJ_DTYPE_INT, 1, false);
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        $arg = isset($args[0]) ? $args[0] : null;

        return $this->getVar($method, $arg);
    }

    /**
     * @return bool
     */
    public function notLoaded()
    {
        return ($this->getVar('categoryid') == -1);
    }

    /**
     * @return bool
     */
    public function checkPermission()
    {
        global $publisher_isAdmin, $xoopsUser;
        $ret = false;
        if ($publisher_isAdmin) {
            return true;
        }
        if (is_object($xoopsUser) && $xoopsUser->getVar('uid') == $this->moderator()) {
            return true;
        }
        $categoriesGranted = $this->publisher->getHandler('permission')->getGrantedItems('category_read');
        if (in_array($this->categoryid(), $categoriesGranted)) {
            $ret = true;
        }

        return $ret;
    }

    /**
     * @param string $format
     *
     * @return mixed|string
     */
    public function image($format = 's')
    {
        if ($this->getVar('image') != '') {
            return $this->getVar('image', $format);
        } else {
            return 'blank.png';
        }
    }

    /**
     * @param string $format
     *
     * @return mixed
     */
    public function template($format = 'n')
    {
        return $this->getVar('template', $format);
    }

    /**
     * @param bool $withAllLink
     *
     * @return array|bool|string
     */
    public function getCategoryPath($withAllLink = true)
    {
        if (!$this->_categoryPath) {
            if ($withAllLink) {
                if ($this->getCategoryLink() != '') {
                    $ret = '<li>' . $this->getCategoryLink() . '</li>';
                } else {
                    $ret = $this->getCategoryLink();
                }
            } else {
                $ret = $this->name();
            }
            $parentid = $this->parentid();
            if ($parentid != 0) {
                $parentObj = $this->publisher->getHandler('category')->get($parentid);
                if ($parentObj->notLoaded()) {
                    exit;
                }
                $ret = $parentObj->getCategoryPath($withAllLink) . $ret;
            }
            $this->_categoryPath = $ret;
        }

        return $this->_categoryPath;
    }

    /* OLD Function
    public function getCategoryPath($withAllLink = true)
    {
        if (!$this->_categoryPath) {
            if ($withAllLink) {
                $ret = $this->getCategoryLink();
            } else {
                $ret = $this->name();
            }
            $parentid = $this->parentid();
            if ($parentid != 0) {
                $parentObj = $this->publisher->getHandler('category')->get($parentid);
                if ($parentObj->notLoaded()) {
                    exit;
                }
                $ret = $parentObj->getCategoryPath($withAllLink) . " > " . $ret;
            }
            $this->_categoryPath = $ret;
        }
        return $this->_categoryPath;
    }
    */
    /**
     * @return mixed|string
     */
    public function getCategoryPathForMetaTitle()
    {
        $ret      = '';
        $parentid = $this->parentid();
        if ($parentid != 0) {
            $parentObj = $this->publisher->getHandler('category')->get($parentid);
            if ($parentObj->notLoaded()) {
                exit('NOT LOADED');
            }
            $ret = $parentObj->getCategoryPath(false);
            $ret = str_replace(' >', ' -', $ret);
        }

        return $ret;
    }

    /**
     * @return array|null
     */
    public function getGroups_read()
    {
        return $this->publisher->getHandler('permission')->getGrantedGroupsById('category_read', $this->categoryid());
    }

    /**
     * @return array|null
     */
    public function getGroups_submit()
    {
        return $this->publisher->getHandler('permission')->getGrantedGroupsById('item_submit', $this->categoryid());
    }

    /**
     * @return array|null
     */
    public function getGroups_moderation()
    {
        return $this->publisher->getHandler('permission')->getGrantedGroupsById('category_moderation', $this->categoryid());
    }

    /**
     * @return string
     */
    public function getCategoryUrl()
    {
        return publisher_seo_genUrl('category', $this->categoryid(), $this->short_url());
    }

    /**
     * @param bool $class
     *
     * @return string
     */
    public function getCategoryLink($class = false)
    {
        if ($class) {
            return "<a class='$class' href='" . $this->getCategoryUrl() . "'>" . $this->name() . '</a>';
        } else {
            return "<a href='" . $this->getCategoryUrl() . "'>" . $this->name() . '</a>';
        }
    }

    /**
     * @param bool $sendNotifications
     * @param bool $force
     *
     * @return mixed
     */
    public function store($sendNotifications = true, $force = true)
    {
        $ret = $this->publisher->getHandler('category')->insert($this, $force);
        if ($sendNotifications && $ret && $this->isNew()) {
            $this->sendNotifications();
        }
        $this->unsetNew();

        return $ret;
    }

    /**
     * Send notifications
     */
    public function sendNotifications()
    {
        $tags                  = array();
        $tags['MODULE_NAME']   = $this->publisher->getModule()->getVar('name');
        $tags['CATEGORY_NAME'] = $this->name();
        $tags['CATEGORY_URL']  = $this->getCategoryUrl();
        $notification_handler  = xoops_getHandler('notification');
        $notification_handler->triggerEvent('global_item', 0, 'category_created', $tags);
    }

    /**
     * @param array $category
     *
     * @return array
     */
    public function ToArraySimple($category = array())
    {
        $category['categoryid']       = $this->categoryid();
        $category['name']             = $this->name();
        $category['categorylink']     = $this->getCategoryLink();
        $category['categoryurl']      = $this->getCategoryUrl();
        $category['total']            = ($this->getVar('itemcount') > 0) ? $this->getVar('itemcount') : '';
        $category['description']      = $this->description();
        $category['header']           = $this->header();
        $category['meta_keywords']    = $this->meta_keywords();
        $category['meta_description'] = $this->meta_description();
        $category['short_url']        = $this->short_url();
        if ($this->getVar('last_itemid') > 0) {
            $category['last_itemid']     = $this->getVar('last_itemid', 'n');
            $category['last_title_link'] = $this->getVar('last_title_link', 'n');
        }
        if ($this->image() !== 'blank.png') {
            $category['image_path'] = publisher_getImageDir('category', false) . $this->image();
        } else {
            $category['image_path'] = '';
        }
        $category['lang_subcategories'] = sprintf(_CO_PUBLISHER_SUBCATEGORIES_INFO, $this->name());

        return $category;
    }

    /**
     * @param array $category
     *
     * @return array
     */
    public function toArrayTable($category = array())
    {
        $category['categoryid']   = $this->categoryid();
        $category['categorylink'] = $this->getCategoryLink();
        $category['total']        = ($this->getVar('itemcount') > 0) ? $this->getVar('itemcount') : '';
        $category['description']  = $this->description();
        if ($this->getVar('last_itemid') > 0) {
            $category['last_itemid']     = $this->getVar('last_itemid', 'n');
            $category['last_title_link'] = $this->getVar('last_title_link', 'n');
        }
        if ($this->image() !== 'blank.png') {
            $category['image_path'] = publisher_getImageDir('category', false) . $this->image();
        } else {
            $category['image_path'] = '';
        }
        $category['lang_subcategories'] = sprintf(_CO_PUBLISHER_SUBCATEGORIES_INFO, $this->name());

        return $category;
    }

    /**
     *
     */
    public function createMetaTags()
    {
        $publisher_metagen = new PublisherMetagen($this->name(), $this->meta_keywords(), $this->meta_description());
        $publisher_metagen->createMetaTags();
    }

    /**
     * @param int $subCatsCount
     *
     * @return PublisherCategoryForm
     */
    public function getForm($subCatsCount = 4)
    {
        include_once XOOPS_ROOT_PATH . '/modules/publisher/class/form/category.php';
        $form = new PublisherCategoryForm($this, $subCatsCount);

        return $form;
    }
}

/**
 * Categories handler class.
 * This class is responsible for providing data access mechanisms to the data source
 * of Category class objects.
 *
 * @author  marcan <marcan@notrevie.ca>
 * @package Publisher
 */
class PublisherCategoryHandler extends XoopsPersistableObjectHandler
{
    /**
     * @var PublisherPublisher
     * @access public
     */
    public $publisher;

    /**
     * @param null|XoopsDatabase $db
     */
    public function __construct(XoopsDatabase $db)
    {
        $this->publisher = PublisherPublisher::getInstance();
        parent::__construct($db, 'publisher_categories', 'PublisherCategory', 'categoryid', 'name');
    }

    /**
     * retrieve an item
     *
     * @param int $id itemid of the user
     *
     * @return mixed reference to the {@link PublisherCategory} object, FALSE if failed
     */
    public function &get($id)
    {
        static $cats;
        if (isset($cats[$id])) {
            return $cats[$id];
        }
        $obj       =& parent::get($id);
        $cats[$id] = $obj;

        return $obj;
    }

    /**
     * insert a new category in the database
     *
     * @param PublisherCategory $category reference to the {@link PublisherCategory} object
     * @param bool              $force
     *
     * @return bool FALSE if failed, TRUE if already present and unchanged or successful
     */
    public function insert(PublisherCategory $category, $force = false)
    {
        // Auto create meta tags if empty
        if (!$category->meta_keywords() || !$category->meta_description()) {
            $publisher_metagen = new PublisherMetagen($category->name(), $category->getVar('meta_keywords'), $category->getVar('description'));
            if (!$category->meta_keywords()) {
                $category->setVar('meta_keywords', $publisher_metagen->_keywords);
            }
            if (!$category->meta_description()) {
                $category->setVar('meta_description', $publisher_metagen->_description);
            }
        }
        // Auto create short_url if empty
        if (!$category->short_url()) {
            $category->setVar('short_url', PublisherMetagen::generateSeoTitle($category->name('n'), false));
        }
        $ret = parent::insert($category, $force);

        return $ret;
    }

    /**
     * delete a category from the database
     *
     * @param PublisherCategory $category reference to the category to delete
     * @param bool              $force
     *
     * @return bool FALSE if failed.
     */
    public function delete(PublisherCategory $category, $force = false)
    {
        // Deleting this category ITEMs
        $criteria = new Criteria('categoryid', $category->categoryid());
        $this->publisher->getHandler('item')->deleteAll($criteria);
        unset($criteria);
        // Deleting the sub categories
        $subcats =& $this->getCategories(0, 0, $category->categoryid());
        foreach ($subcats as $subcat) {
            $this->delete($subcat);
        }
        if (!parent::delete($category, $force)) {
            $category->setErrors('An error while deleting.');

            return false;
        }
        $module_id = $this->publisher->getModule()->getVar('mid');
        xoops_groupperm_deletebymoditem($module_id, 'category_read', $category->categoryid());
        xoops_groupperm_deletebymoditem($module_id, 'item_submit', $category->categoryid());
        xoops_groupperm_deletebymoditem($module_id, 'category_moderation', $category->categoryid());

        return true;
    }

    /**
     * retrieve categories from the database
     *
     * @param CriteriaElement $criteria  {@link CriteriaElement} conditions to be met
     * @param bool            $id_as_key use the categoryid as key for the array?
     *
     * @return array array of {@link XoopsItem} objects
     */
    public function &getObjects(CriteriaElement $criteria = null, $id_as_key = false)
    {
        $ret        = array();
        $theObjects =& parent::getObjects($criteria, true);
        foreach ($theObjects as $theObject) {
            if (!$id_as_key) {
                $ret[] = $theObject;
            } else {
                $ret[$theObject->categoryid()] = $theObject;
            }
            unset($theObject);
        }

        return $ret;
    }

    /**
     * @param int    $limit
     * @param int    $start
     * @param int    $parentid
     * @param string $sort
     * @param string $order
     * @param bool   $id_as_key
     *
     * @return array
     */
    public function &getCategories($limit = 0, $start = 0, $parentid = 0, $sort = 'weight', $order = 'ASC', $id_as_key = true)
    {
        global $publisher_isAdmin, $xoopsUser;
        $criteria = new CriteriaCompo();
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        if ($parentid != -1) {
            $criteria->add(new Criteria('parentid', $parentid));
        }
        if (!$publisher_isAdmin) {
            $categoriesGranted = $this->publisher->getHandler('permission')->getGrantedItems('category_read');
            if (count($categoriesGranted) > 0) {
                $criteria->add(new Criteria('categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN'));
            } else {
                return array();
            }
            if (is_object($xoopsUser)) {
                $criteria->add(new Criteria('moderator', $xoopsUser->getVar('uid')), 'OR');
            }
        }
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $ret =& $this->getObjects($criteria, $id_as_key);

        return $ret;
    }

    /**
     * @param $category
     * @param $level
     * @param $cat_array
     * @param $cat_result
     */
    public function getSubCatArray($category, $level, $cat_array, $cat_result)
    {
        global $theresult;
        $spaces = '';
        for ($j = 0; $j < $level; ++$j) {
            $spaces .= '--';
        }
        $theresult[$category['categoryid']] = $spaces . $category['name'];
        if (isset($cat_array[$category['categoryid']])) {
            ++$level;
            foreach ($cat_array[$category['categoryid']] as $parentid => $cat) {
                $this->getSubCatArray($cat, $level, $cat_array, $cat_result);
            }
        }
    }

    public function &getCategoriesForSubmit()
    {
        global $publisher_isAdmin, $theresult, $xoopsUser;
        $ret      = array();
        $criteria = new CriteriaCompo();
        $criteria->setSort('name');
        $criteria->setOrder('ASC');
        if (!$publisher_isAdmin) {
            $categoriesGranted = $this->publisher->getHandler('permission')->getGrantedItems('item_submit');
            if (count($categoriesGranted) > 0) {
                $criteria->add(new Criteria('categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN'));
            } else {
                return $ret;
            }
            if (is_object($xoopsUser)) {
                $criteria->add(new Criteria('moderator', $xoopsUser->getVar('uid')), 'OR');
            }
        }
        $categories =& $this->getAll($criteria, array('categoryid', 'parentid', 'name'), false, false);
        if (count($categories) == 0) {
            return $ret;
        }
        $cat_array = array();
        foreach ($categories as $cat) {
            $cat_array[$cat['parentid']][$cat['categoryid']] = $cat;
        }
        // Needs to have permission on at least 1 top level category
        if (!isset($cat_array[0])) {
            return $ret;
        }
        $cat_result = array();
        foreach ($cat_array[0] as $thecat) {
            $level = 0;
            $this->getSubCatArray($thecat, $level, $cat_array, $cat_result);
        }

        return $theresult; //this is a global
    }

    /**
     * @return array
     */
    public function &getCategoriesForSearch()
    {
        global $publisher_isAdmin, $theresult, $xoopsUser;
        $ret      = array();
        $criteria = new CriteriaCompo();
        $criteria->setSort('name');
        $criteria->setOrder('ASC');
        if (!$publisher_isAdmin) {
            $categoriesGranted = $this->publisher->getHandler('permission')->getGrantedItems('category_read');
            if (count($categoriesGranted) > 0) {
                $criteria->add(new Criteria('categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN'));
            } else {
                return $ret;
            }
            if (is_object($xoopsUser)) {
                $criteria->add(new Criteria('moderator', $xoopsUser->getVar('uid')), 'OR');
            }
        }
        $categories =& $this->getAll($criteria, array('categoryid', 'parentid', 'name'), false, false);
        if (count($categories) == 0) {
            return $ret;
        }
        $cat_array = array();
        foreach ($categories as $cat) {
            $cat_array[$cat['parentid']][$cat['categoryid']] = $cat;
        }
        // Needs to have permission on at least 1 top level category
        if (!isset($cat_array[0])) {
            return $ret;
        }
        $cat_result = array();
        foreach ($cat_array[0] as $thecat) {
            $level = 0;
            $this->getSubCatArray($thecat, $level, $cat_array, $cat_result);
        }

        return $theresult; //this is a global
    }

    /**
     * @param int $parentid
     *
     * @return int
     */
    public function getCategoriesCount($parentid = 0)
    {
        global $publisher_isAdmin, $xoopsUser;
        if ($parentid == -1) {
            return $this->getCount();
        }
        $criteria = new CriteriaCompo();
        if (isset($parentid) && ($parentid != -1)) {
            $criteria->add(new criteria('parentid', $parentid));
            if (!$publisher_isAdmin) {
                $categoriesGranted = $this->publisher->getHandler('permission')->getGrantedItems('category_read');
                if (count($categoriesGranted) > 0) {
                    $criteria->add(new Criteria('categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN'));
                } else {
                    return 0;
                }
                if (is_object($xoopsUser)) {
                    $criteria->add(new Criteria('moderator', $xoopsUser->getVar('uid')), 'OR');
                }
            }
        }

        return $this->getCount($criteria);
    }

    /**
     * Get all subcats and put them in an array indexed by parent id
     *
     * @param array $categories
     *
     * @return array
     */
    public function getSubCats($categories)
    {
        global $publisher_isAdmin, $xoopsUser;
        $criteria = new CriteriaCompo(new Criteria('parentid', '(' . implode(',', array_keys($categories)) . ')', 'IN'));
        $ret      = array();
        if (!$publisher_isAdmin) {
            $categoriesGranted = $this->publisher->getHandler('permission')->getGrantedItems('category_read');
            if (count($categoriesGranted) > 0) {
                $criteria->add(new Criteria('categoryid', '(' . implode(',', $categoriesGranted) . ')', 'IN'));
            } else {
                return $ret;
            }

            if (is_object($xoopsUser)) {
                $criteria->add(new Criteria('moderator', $xoopsUser->getVar('uid')), 'OR');
            }
        }
        $criteria->setSort('weight');
        $criteria->setOrder('ASC');
        $subcats =& $this->getObjects($criteria, true);
        foreach ($subcats as $subcat) {
            $ret[$subcat->getVar('parentid')][$subcat->getVar('categoryid')] = $subcat;
        }

        return $ret;
    }

    /**
     * delete categories matching a set of conditions
     *
     * @param CriteriaElement $criteria {@link CriteriaElement}
     *
     * @return bool FALSE if deletion failed
     */
    public function deleteAll(CriteriaElement $criteria = null)
    {
        $categories =& $this->getObjects($criteria);
        foreach ($categories as $category) {
            if (!$this->delete($category)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $cat_id
     *
     * @return mixed
     */
    public function publishedItemsCount($cat_id = 0)
    {
        return $this->itemsCount($cat_id, $status = array(_PUBLISHER_STATUS_PUBLISHED));
    }

    /**
     * @param int    $cat_id
     * @param string $status
     *
     * @return mixed
     */
    public function itemsCount($cat_id = 0, $status = '')
    {
        return $this->publisher->getHandler('item')->getCountsByCat($cat_id, $status);
    }
}
