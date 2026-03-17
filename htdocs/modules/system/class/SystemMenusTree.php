<?php
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

include_once $GLOBALS['xoops']->path('class/tree.php');

/**
 * SystemMenusTree : extension of XoopsObjectTree for menus
 */
class SystemMenusTree extends XoopsObjectTree
{
    /**
     * @access private
     */
    protected $listTree = array();
    protected $cpt;

    /**
     * Constructor
     *
     * @param array  $objectArr Array of {@link XoopsObject}s
     * @param string $myId      field name of object ID
     * @param string $parentId  field name of parent object ID
     * @param string $rootId    field name of root object ID
     */
    public function __construct(&$objectArr, $myId, $parentId, $rootId = null)
    {
        $this->cpt = 0;
        parent::__construct($objectArr, $myId, $parentId, $rootId);
    }

    /**
     * Build a flat tree array from the hierarchical structure
     *
     * @param  string  $fieldName  Name of the member variable for title
     * @param  string  $prefix     String to indent deeper levels
     * @param  int     $itemid     Category ID to filter by (0 = all)
     *
     * @return array   $listTree   Flat tree with level info
     */
    public function makeTree(
        $fieldName,
        $prefix = '-',
        $itemid = 0
    ) {
        $this->addTree($fieldName, $itemid, 0, $prefix);

        return $this->listTree;
    }

    /**
     * Recursively build the tree
     *
     * @param string $fieldName   Name of the field for title
     * @param int    $itemid      Category ID to filter by
     * @param int    $key         Current node key
     * @param string $prefix_orig Prefix string for indentation
     * @param string $prefix_curr Current accumulated prefix
     * @param int    $level       Current depth level
     *
     * @return void
     * @access private
     */
    protected function addTree($fieldName, $itemid, $key, $prefix_orig, $prefix_curr = '', $level = 1)
    {
        if ($key > 0) {
            if (($itemid == $this->tree[$key]['obj']->getVar('items_cid')) || $itemid == 0) {
                $value = $this->tree[$key]['obj']->getVar('items_id');
                $name = $prefix_curr . ' ' . $this->tree[$key]['obj']->getVar($fieldName);
                $prefix_curr .= $prefix_orig;
                $this->listTree[$this->cpt]['name'] = $name;
                $this->listTree[$this->cpt]['id'] = $value;
                $this->listTree[$this->cpt]['level'] = $level;
                $this->listTree[$this->cpt]['obj'] = $this->tree[$key]['obj'];
                $this->cpt++;
                $level++;
            }
        }
        if (isset($this->tree[$key]['child']) && !empty($this->tree[$key]['child'])) {
            foreach ($this->tree[$key]['child'] as $childKey) {
                $this->addTree($fieldName, $itemid, $childKey, $prefix_orig, $prefix_curr, $level);
            }
        }
    }
}
