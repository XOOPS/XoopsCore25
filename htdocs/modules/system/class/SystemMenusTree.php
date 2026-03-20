<?php
/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
 
/**
 * System Menu Tree Utilities
 *
 * @category  System
 * @author    XOOPS Core Team
 * @copyright 2001-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2+ (https://www.gnu.org/licenses/gpl-2.0.html)
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Static utility class for menu tree validation and traversal.
 */
class SystemMenusTree
{
    /**
     * Validate whether $parentId is a valid parent for $itemId.
     *
     * @param int   $itemId     The item being moved/saved
     * @param int   $categoryId The category both must belong to
     * @param int   $parentId   The proposed parent item ID (0 for root)
     * @param array $allItems   Flat array of item records with items_id, items_pid, items_cid
     * @param int   $maxDepth   Maximum allowed tree depth
     *
     * @return bool|string True if valid, or error key string
     */
    public static function validateParent(
        int $itemId,
        int $categoryId,
        int $parentId,
        array $allItems,
        int $maxDepth
    ): bool|string {
        if ($parentId === 0) {
            return true;
        }
        if ($parentId === $itemId) {
            return 'self_parent';
        }

        $parent = null;
        foreach ($allItems as $row) {
            if ((int)$row['items_id'] === $parentId) {
                $parent = $row;
                break;
            }
        }
        if ($parent === null) {
            return 'parent_not_found';
        }
        if ((int)$parent['items_cid'] !== $categoryId) {
            return 'cross_category';
        }

        $descendants = self::collectDescendantIds($allItems, $itemId);
        if (in_array($parentId, $descendants, true)) {
            return 'cycle';
        }

        $depthOfParent = self::depthOfNode($allItems, $parentId);
        $subtreeDepth  = self::subtreeDepth($allItems, $itemId);
        if ($depthOfParent + 1 + $subtreeDepth > $maxDepth) {
            return 'max_depth';
        }

        return true;
    }

    /**
     * Compute the maximum depth of a nested tree array.
     *
     * @param array $tree Nested array with 'children' key
     *
     * @return int Depth (0 for empty)
     */
    public static function computeDepth(array $tree): int
    {
        if (empty($tree)) {
            return 0;
        }
        $max = 0;
        foreach ($tree as $node) {
            $childDepth = self::computeDepth($node['children'] ?? []);
            $max = max($max, 1 + $childDepth);
        }
        return $max;
    }

    /**
     * Collect all descendant item IDs of a given root item using BFS.
     *
     * @param array $allItems Flat array with items_id, items_pid
     * @param int   $rootId   The root item ID
     *
     * @return array<int> Descendant IDs (not including rootId)
     */
    public static function collectDescendantIds(array $allItems, int $rootId): array
    {
        $descendants = [];
        $queue       = [$rootId];

        while (!empty($queue)) {
            $current = array_shift($queue);
            foreach ($allItems as $row) {
                $id  = (int)$row['items_id'];
                $pid = (int)$row['items_pid'];
                if ($pid === $current && $id !== $rootId && !in_array($id, $descendants, true)) {
                    $descendants[] = $id;
                    $queue[]       = $id;
                }
            }
        }

        return $descendants;
    }

    /**
     * Flatten a tree for display (e.g., delete confirmation).
     *
     * @param array  $items    Array of XoopsMenusItems objects
     * @param int    $parentId Starting parent
     * @param int    $level    Current indent level
     * @param string $prefix   Indentation prefix
     *
     * @return array<int, array{id: int, name: string, level: int}>
     */
    public static function flattenForDisplay(
        array $items,
        int $parentId = 0,
        int $level = 0,
        string $prefix = '--'
    ): array {
        $result = [];
        foreach ($items as $item) {
            if ((int)$item->getVar('items_pid') === $parentId) {
                $indent   = str_repeat($prefix, $level);
                $result[] = [
                    'id'    => (int) $item->getVar('items_id'),
                    'name'  => $indent . ' ' . $item->getAdminTitle(),
                    'level' => $level,
                ];
                $result = array_merge(
                    $result,
                    self::flattenForDisplay($items, (int) $item->getVar('items_id'), $level + 1, $prefix)
                );
            }
        }
        return $result;
    }

    /**
     * Compute the depth of a node from root.
     *
     * @param array $allItems Flat item records
     * @param int   $nodeId   Target node ID
     *
     * @return int Depth (root items = 1)
     */
    private static function depthOfNode(array $allItems, int $nodeId): int
    {
        $depth     = 0;
        $currentId = $nodeId;
        $visited   = [];

        while ($currentId !== 0) {
            if (in_array($currentId, $visited, true)) {
                break;
            }
            $visited[] = $currentId;
            $depth++;
            $found = false;
            foreach ($allItems as $row) {
                if ((int)$row['items_id'] === $currentId) {
                    $currentId = (int)$row['items_pid'];
                    $found     = true;
                    break;
                }
            }
            if (!$found) {
                break;
            }
        }

        return $depth;
    }

    /**
     * Compute max depth of subtree below a node (not counting node itself).
     *
     * @param array $allItems Flat item records
     * @param int   $nodeId   Root of subtree
     *
     * @return int
     */
    private static function subtreeDepth(array $allItems, int $nodeId): int
    {
        $children = [];
        foreach ($allItems as $row) {
            if ((int)$row['items_pid'] === $nodeId && (int)$row['items_id'] !== $nodeId) {
                $children[] = (int)$row['items_id'];
            }
        }
        if (empty($children)) {
            return 0;
        }
        $max = 0;
        foreach ($children as $childId) {
            $max = max($max, 1 + self::subtreeDepth($allItems, $childId));
        }
        return $max;
    }
}
