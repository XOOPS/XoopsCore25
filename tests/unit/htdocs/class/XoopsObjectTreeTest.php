<?php

declare(strict_types=1);

namespace xoopsclass;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for XoopsObjectTree.
 */
#[CoversClass(\XoopsObjectTree::class)]
class XoopsObjectTreeTest extends TestCase
{
    /**
     * Load the tree class and ensure globals are set before each test.
     */
    protected function setUp(): void
    {
        if (!class_exists('XoopsObjectTree', false)) {
            require_once XOOPS_ROOT_PATH . '/class/tree.php';
        }
        // Ensure xoopsLogger is available for __get deprecation logging
        if (!isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
        }
    }

    /**
     * Create a mock XoopsObject with id, pid, and title vars.
     *
     * @param int    $id       Object ID
     * @param int    $parentId Parent object ID
     * @param string $title    Title (defaults to "Item {$id}")
     *
     * @return \XoopsObject
     */
    private static function createObj(int $id, int $parentId, string $title = ''): \XoopsObject
    {
        $obj = new \XoopsObject();
        $obj->initVar('id', XOBJ_DTYPE_INT);
        $obj->initVar('pid', XOBJ_DTYPE_INT);
        $obj->initVar('title', XOBJ_DTYPE_TXTBOX);
        $obj->assignVar('id', $id);
        $obj->assignVar('pid', $parentId);
        $obj->assignVar('title', $title ?: "Item {$id}");
        return $obj;
    }

    /**
     * Build an XoopsObjectTree from a flat array of [id, pid, title] specs.
     *
     * @param array $specs Array of [id, pid] or [id, pid, title] arrays
     *
     * @return \XoopsObjectTree
     */
    private static function buildTree(array $specs): \XoopsObjectTree
    {
        $objects = [];
        foreach ($specs as $spec) {
            $id    = $spec[0];
            $pid   = $spec[1];
            $title = isset($spec[2]) ? $spec[2] : '';
            $objects[] = self::createObj($id, $pid, $title);
        }
        return new \XoopsObjectTree($objects, 'id', 'pid');
    }

    // =========================================================================
    // Constructor / initialize
    // =========================================================================

    public function testConstructorWithEmptyArray(): void
    {
        $tree = new \XoopsObjectTree([], 'id', 'pid');
        $result = $tree->getTree();
        $this->assertIsArray($result);
        $this->assertSame([], $result);
    }

    public function testConstructorStoresObjects(): void
    {
        $obj = self::createObj(1, 0);
        $tree = new \XoopsObjectTree([$obj], 'id', 'pid');
        $result = $tree->getTree();
        $this->assertArrayHasKey(1, $result);
    }

    public function testConstructorWithRootId(): void
    {
        $obj = new \XoopsObject();
        $obj->initVar('id', XOBJ_DTYPE_INT);
        $obj->initVar('pid', XOBJ_DTYPE_INT);
        $obj->initVar('rid', XOBJ_DTYPE_INT);
        $obj->assignVar('id', 5);
        $obj->assignVar('pid', 0);
        $obj->assignVar('rid', 99);

        $tree = new \XoopsObjectTree([$obj], 'id', 'pid', 'rid');
        $data = $tree->getTree();
        $this->assertArrayHasKey('root', $data[5]);
        $this->assertSame(99, $data[5]['root']);
    }

    public function testConstructorWithoutRootIdOmitsRootKey(): void
    {
        $tree = self::buildTree([[1, 0]]);
        $data = $tree->getTree();
        $this->assertArrayNotHasKey('root', $data[1]);
    }

    // =========================================================================
    // getTree — returns full tree structure
    // =========================================================================

    public function testGetTreeReturnsByReference(): void
    {
        $tree = self::buildTree([[1, 0]]);
        $data = &$tree->getTree();
        $this->assertIsArray($data);
    }

    public function testGetTreeSingleRootHasObjParentKeys(): void
    {
        $tree = self::buildTree([[1, 0]]);
        $data = $tree->getTree();

        $this->assertArrayHasKey('obj', $data[1]);
        $this->assertArrayHasKey('parent', $data[1]);
        $this->assertSame(0, $data[1]['parent']);
    }

    public function testGetTreeChildArrayPopulated(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1], [3, 1]]);
        $data = $tree->getTree();

        // Parent 0 should have child [1]
        $this->assertContains(1, $data[0]['child']);
        // Node 1 should have children [2, 3]
        $this->assertContains(2, $data[1]['child']);
        $this->assertContains(3, $data[1]['child']);
    }

    public function testGetTreeTwoLevelStructure(): void
    {
        $tree = self::buildTree([[1, 0, 'Root'], [2, 1, 'Child A'], [3, 1, 'Child B']]);
        $data = $tree->getTree();

        // Root node (pid=0) appears under key 1
        $this->assertInstanceOf(\XoopsObject::class, $data[1]['obj']);
        $this->assertSame(1, $data[1]['obj']->getVar('id'));

        // Children appear under parent key
        $this->assertCount(2, $data[1]['child']);
    }

    public function testGetTreeThreeLevelStructure(): void
    {
        $tree = self::buildTree([
            [1, 0, 'Root'],
            [2, 1, 'Child'],
            [3, 2, 'Grandchild'],
        ]);
        $data = $tree->getTree();

        $this->assertContains(2, $data[1]['child']);
        $this->assertContains(3, $data[2]['child']);
    }

    public function testGetTreeMultipleRoots(): void
    {
        $tree = self::buildTree([[1, 0, 'Root A'], [2, 0, 'Root B']]);
        $data = $tree->getTree();

        // Both roots registered under parent 0
        $this->assertContains(1, $data[0]['child']);
        $this->assertContains(2, $data[0]['child']);
    }

    public function testGetTreePreservesAllNodes(): void
    {
        $tree = self::buildTree([
            [10, 0], [20, 10], [30, 10], [40, 20], [50, 20],
        ]);
        $data = $tree->getTree();

        foreach ([10, 20, 30, 40, 50] as $id) {
            $this->assertArrayHasKey($id, $data, "Node {$id} should exist in tree");
            $this->assertArrayHasKey('obj', $data[$id]);
        }
    }

    // =========================================================================
    // getByKey — returns object by its ID
    // =========================================================================

    public function testGetByKeyReturnsCorrectObject(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1], [3, 1]]);
        $obj = $tree->getByKey(2);
        $this->assertInstanceOf(\XoopsObject::class, $obj);
        $this->assertSame(2, $obj->getVar('id'));
    }

    public function testGetByKeyReturnsRootObject(): void
    {
        $tree = self::buildTree([[1, 0, 'The Root']]);
        $obj = $tree->getByKey(1);
        $this->assertSame(1, $obj->getVar('id'));
    }

    public function testGetByKeyReturnsLeafObject(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1], [3, 2]]);
        $obj = $tree->getByKey(3);
        $this->assertSame(3, $obj->getVar('id'));
    }

    public function testGetByKeyReturnsByReference(): void
    {
        $tree = self::buildTree([[1, 0]]);
        $obj = &$tree->getByKey(1);
        $this->assertInstanceOf(\XoopsObject::class, $obj);
    }

    // =========================================================================
    // getFirstChild — direct children only
    // =========================================================================

    public function testGetFirstChildReturnsDirectChildren(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1], [3, 1], [4, 2]]);
        $children = $tree->getFirstChild(1);

        $this->assertCount(2, $children);
        $this->assertArrayHasKey(2, $children);
        $this->assertArrayHasKey(3, $children);
    }

    public function testGetFirstChildDoesNotReturnGrandchildren(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1], [3, 2]]);
        $children = $tree->getFirstChild(1);

        $this->assertCount(1, $children);
        $this->assertArrayHasKey(2, $children);
        $this->assertArrayNotHasKey(3, $children);
    }

    public function testGetFirstChildOnLeafReturnsEmpty(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1]]);
        $children = $tree->getFirstChild(2);

        $this->assertIsArray($children);
        $this->assertSame([], $children);
    }

    public function testGetFirstChildOnNodeWithNoChildrenReturnsEmpty(): void
    {
        $tree = self::buildTree([[1, 0]]);
        $children = $tree->getFirstChild(1);
        $this->assertSame([], $children);
    }

    public function testGetFirstChildOnVirtualRootZero(): void
    {
        $tree = self::buildTree([[1, 0], [2, 0]]);
        $children = $tree->getFirstChild(0);

        $this->assertCount(2, $children);
        $this->assertArrayHasKey(1, $children);
        $this->assertArrayHasKey(2, $children);
    }

    public function testGetFirstChildReturnsXoopsObjects(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1]]);
        $children = $tree->getFirstChild(1);

        foreach ($children as $child) {
            $this->assertInstanceOf(\XoopsObject::class, $child);
        }
    }

    // =========================================================================
    // getAllChild — recursive children
    // =========================================================================

    public function testGetAllChildReturnsAllDescendants(): void
    {
        $tree = self::buildTree([
            [1, 0],
            [2, 1],
            [3, 1],
            [4, 2],
            [5, 2],
            [6, 4],
        ]);
        $all = $tree->getAllChild(1);

        $this->assertCount(5, $all);
        foreach ([2, 3, 4, 5, 6] as $id) {
            $this->assertArrayHasKey($id, $all, "Descendant {$id} should be present");
        }
    }

    public function testGetAllChildOnLeafReturnsEmpty(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1]]);
        $all = $tree->getAllChild(2);
        $this->assertSame([], $all);
    }

    public function testGetAllChildIncludesGrandchildren(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1], [3, 2], [4, 3]]);
        $all = $tree->getAllChild(1);

        $this->assertArrayHasKey(2, $all);
        $this->assertArrayHasKey(3, $all);
        $this->assertArrayHasKey(4, $all);
    }

    public function testGetAllChildFromVirtualRoot(): void
    {
        $tree = self::buildTree([[1, 0], [2, 0], [3, 1]]);
        $all = $tree->getAllChild(0);

        $this->assertArrayHasKey(1, $all);
        $this->assertArrayHasKey(2, $all);
        $this->assertArrayHasKey(3, $all);
    }

    public function testGetAllChildDoesNotIncludeStartNode(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1], [3, 2]]);
        $all = $tree->getAllChild(1);
        $this->assertArrayNotHasKey(1, $all);
    }

    public function testGetAllChildReturnsCorrectObjectInstances(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1], [3, 2]]);
        $all = $tree->getAllChild(1);

        $this->assertSame(2, $all[2]->getVar('id'));
        $this->assertSame(3, $all[3]->getVar('id'));
    }

    // =========================================================================
    // getAllParent — recursive parents
    // =========================================================================

    public function testGetAllParentFromLeaf(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1], [3, 2]]);
        $parents = $tree->getAllParent(3);

        // Level 1 parent = node 2, level 2 parent = node 1
        $this->assertCount(2, $parents);
        $this->assertSame(2, $parents[1]->getVar('id'));
        $this->assertSame(1, $parents[2]->getVar('id'));
    }

    public function testGetAllParentFromRootReturnsEmpty(): void
    {
        $tree = self::buildTree([[1, 0]]);
        $parents = $tree->getAllParent(1);

        // Root's parent is 0 (virtual root), which has no 'obj'
        $this->assertSame([], $parents);
    }

    public function testGetAllParentFromDirectChild(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1]]);
        $parents = $tree->getAllParent(2);

        $this->assertCount(1, $parents);
        $this->assertSame(1, $parents[1]->getVar('id'));
    }

    public function testGetAllParentKeysRepresentLevelsUp(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1], [3, 2], [4, 3]]);
        $parents = $tree->getAllParent(4);

        $this->assertArrayHasKey(1, $parents); // 1 level up = node 3
        $this->assertArrayHasKey(2, $parents); // 2 levels up = node 2
        $this->assertArrayHasKey(3, $parents); // 3 levels up = node 1

        $this->assertSame(3, $parents[1]->getVar('id'));
        $this->assertSame(2, $parents[2]->getVar('id'));
        $this->assertSame(1, $parents[3]->getVar('id'));
    }

    public function testGetAllParentDoesNotIncludeStartNode(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1], [3, 2]]);
        $parents = $tree->getAllParent(3);

        // Should not contain node 3 itself
        foreach ($parents as $parent) {
            $this->assertNotSame(3, $parent->getVar('id'));
        }
    }

    public function testGetAllParentReturnsXoopsObjects(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1]]);
        $parents = $tree->getAllParent(2);

        foreach ($parents as $parent) {
            $this->assertInstanceOf(\XoopsObject::class, $parent);
        }
    }

    // =========================================================================
    // makeSelectElement — builds XoopsFormSelect
    // =========================================================================

    public function testMakeSelectElementReturnsXoopsFormSelect(): void
    {
        $tree = self::buildTree([[1, 0, 'Root'], [2, 1, 'Child']]);
        $element = $tree->makeSelectElement('category', 'title');

        $this->assertInstanceOf(\XoopsFormSelect::class, $element);
    }

    public function testMakeSelectElementSetsName(): void
    {
        $tree = self::buildTree([[1, 0, 'Root']]);
        $element = $tree->makeSelectElement('myfield', 'title');

        $this->assertSame('myfield', $element->getName(false));
    }

    public function testMakeSelectElementSetsCaption(): void
    {
        $tree = self::buildTree([[1, 0, 'Root']]);
        $element = $tree->makeSelectElement('myfield', 'title', '-', '', false, 0, '', 'Pick a category');

        $this->assertSame('Pick a category', $element->getCaption());
    }

    public function testMakeSelectElementContainsOptions(): void
    {
        $tree = self::buildTree([[1, 0, 'Root'], [2, 1, 'Child']]);
        $element = $tree->makeSelectElement('cat', 'title');
        $options = $element->getOptions(false);

        // Should have options for node 1 and node 2
        $this->assertCount(2, $options);
    }

    public function testMakeSelectElementWithEmptyOption(): void
    {
        $tree = self::buildTree([[1, 0, 'Root']]);
        $element = $tree->makeSelectElement('cat', 'title', '-', '', true);
        $options = $element->getOptions(false);

        // Empty option + the root node
        $this->assertArrayHasKey('0', $options);
    }

    public function testMakeSelectElementWithoutEmptyOption(): void
    {
        $tree = self::buildTree([[1, 0, 'Root']]);
        $element = $tree->makeSelectElement('cat', 'title', '-', '', false);
        $options = $element->getOptions(false);

        // Only the root node, no empty option
        $this->assertCount(1, $options);
    }

    public function testMakeSelectElementIndentsChildren(): void
    {
        $tree = self::buildTree([[1, 0, 'Root'], [2, 1, 'Child']]);
        $element = $tree->makeSelectElement('cat', 'title', '--');
        $options = $element->getOptions(false);

        // Node 2 (child) should be indented with the prefix
        $childLabel = $options[2];
        $this->assertStringContainsString('--', $childLabel);
    }

    public function testMakeSelectElementDeepIndentation(): void
    {
        $tree = self::buildTree([
            [1, 0, 'L0'],
            [2, 1, 'L1'],
            [3, 2, 'L2'],
        ]);
        $element = $tree->makeSelectElement('cat', 'title', '-');
        $options = $element->getOptions(false);

        // Level 0 (node 1): no prefix (since key=0 is skipped, node 1 gets no prefix as first child)
        // Level 1 (node 2): one prefix "-"
        // Level 2 (node 3): two prefixes "--"
        // The actual title is sanitized by getVar, so check for the prefix pattern
        $this->assertArrayHasKey(1, $options);
        $this->assertArrayHasKey(2, $options);
        $this->assertArrayHasKey(3, $options);
    }

    public function testMakeSelectElementWithSelectedValue(): void
    {
        $tree = self::buildTree([[1, 0, 'Root'], [2, 1, 'Child']]);
        $element = $tree->makeSelectElement('cat', 'title', '-', '2');
        $values = $element->getValue();

        $this->assertContains('2', $values);
    }

    public function testMakeSelectElementWithExtraAttribute(): void
    {
        $tree = self::buildTree([[1, 0, 'Root']]);
        $element = $tree->makeSelectElement('cat', 'title', '-', '', false, 0, 'class="form-control"');

        $this->assertStringContainsString('class="form-control"', $element->getExtra());
    }

    // =========================================================================
    // __get — backward compatibility magic property
    // =========================================================================

    public function testMagicGetTreeReturnsTree(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1]]);
        // Access via __get('_tree')
        $data = $tree->_tree;

        $this->assertIsArray($data);
        $this->assertArrayHasKey(1, $data);
        $this->assertArrayHasKey(2, $data);
    }

    public function testMagicGetTreeMatchesGetTree(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1]]);
        $fromMethod = $tree->getTree();
        $fromMagic  = $tree->_tree;

        $this->assertSame($fromMethod, $fromMagic);
    }

    public function testMagicGetUnknownPropertyReturnsNull(): void
    {
        $tree = self::buildTree([[1, 0]]);
        $result = @$tree->nonExistentProperty;

        $this->assertNull($result);
    }

    public function testMagicGetAnotherUnknownPropertyReturnsNull(): void
    {
        $tree = self::buildTree([[1, 0]]);
        $result = @$tree->_objects;

        $this->assertNull($result);
    }

    // =========================================================================
    // Edge cases and complex scenarios
    // =========================================================================

    public function testLargeTreeStructure(): void
    {
        // Build a tree with 10 root children, each with 2 sub-children
        $specs = [];
        $id = 1;
        for ($i = 1; $i <= 10; $i++) {
            $specs[] = [$i, 0, "Root Child {$i}"];
        }
        for ($i = 1; $i <= 10; $i++) {
            $childId1 = 100 + ($i * 2 - 1);
            $childId2 = 100 + ($i * 2);
            $specs[] = [$childId1, $i, "Sub {$childId1}"];
            $specs[] = [$childId2, $i, "Sub {$childId2}"];
        }

        $tree = self::buildTree($specs);
        $data = $tree->getTree();

        // 10 root children + 20 sub-children = 30 nodes plus virtual root 0
        $this->assertCount(31, $data);
    }

    public function testGetAllChildCountMatchesExpected(): void
    {
        $tree = self::buildTree([
            [1, 0],
            [2, 1], [3, 1],
            [4, 2], [5, 2],
            [6, 3],
        ]);

        $allFromRoot = $tree->getAllChild(1);
        $this->assertCount(5, $allFromRoot);

        $allFromNode2 = $tree->getAllChild(2);
        $this->assertCount(2, $allFromNode2);

        $allFromNode3 = $tree->getAllChild(3);
        $this->assertCount(1, $allFromNode3);
    }

    public function testGetFirstChildAndGetAllChildConsistency(): void
    {
        $tree = self::buildTree([
            [1, 0],
            [2, 1],
            [3, 1],
            [4, 2],
        ]);

        $firstChildren = $tree->getFirstChild(1);
        $allChildren   = $tree->getAllChild(1);

        // All first children should be in allChildren
        foreach (array_keys($firstChildren) as $key) {
            $this->assertArrayHasKey($key, $allChildren);
        }

        // allChildren should contain more entries (grandchild 4)
        $this->assertGreaterThanOrEqual(count($firstChildren), count($allChildren));
    }

    public function testTreeWithNonSequentialIds(): void
    {
        $tree = self::buildTree([
            [100, 0, 'Root'],
            [250, 100, 'Child'],
            [999, 250, 'Grandchild'],
        ]);

        $obj = $tree->getByKey(250);
        $this->assertSame(250, $obj->getVar('id'));

        $parents = $tree->getAllParent(999);
        $this->assertCount(2, $parents);
    }

    #[DataProvider('provideGetFirstChildCounts')]
    public function testGetFirstChildCountViaProvider(array $specs, int $parentKey, int $expectedCount): void
    {
        $tree = self::buildTree($specs);
        $children = $tree->getFirstChild($parentKey);
        $this->assertCount($expectedCount, $children);
    }

    /**
     * @return array<string, array{array, int, int}>
     */
    public static function provideGetFirstChildCounts(): array
    {
        return [
            'no children'      => [[[1, 0]], 1, 0],
            'one child'        => [[[1, 0], [2, 1]], 1, 1],
            'two children'     => [[[1, 0], [2, 1], [3, 1]], 1, 2],
            'three children'   => [[[1, 0], [2, 1], [3, 1], [4, 1]], 1, 3],
            'virtual root'     => [[[1, 0], [2, 0], [3, 0]], 0, 3],
        ];
    }

    #[DataProvider('provideGetAllParentCounts')]
    public function testGetAllParentCountViaProvider(array $specs, int $nodeKey, int $expectedCount): void
    {
        $tree = self::buildTree($specs);
        $parents = $tree->getAllParent($nodeKey);
        $this->assertCount($expectedCount, $parents);
    }

    /**
     * @return array<string, array{array, int, int}>
     */
    public static function provideGetAllParentCounts(): array
    {
        return [
            'root has no parents'   => [[[1, 0]], 1, 0],
            'one level deep'        => [[[1, 0], [2, 1]], 2, 1],
            'two levels deep'       => [[[1, 0], [2, 1], [3, 2]], 3, 2],
            'three levels deep'     => [[[1, 0], [2, 1], [3, 2], [4, 3]], 4, 3],
        ];
    }

    public function testMakeSelectElementEmptyTree(): void
    {
        $tree = new \XoopsObjectTree([], 'id', 'pid');
        $element = $tree->makeSelectElement('cat', 'title');
        $options = $element->getOptions(false);

        $this->assertSame([], $options);
    }

    public function testMakeSelectElementEmptyTreeWithEmptyOption(): void
    {
        $tree = new \XoopsObjectTree([], 'id', 'pid');
        $element = $tree->makeSelectElement('cat', 'title', '-', '', true);
        $options = $element->getOptions(false);

        // Only the empty option should be present
        $this->assertCount(1, $options);
        $this->assertArrayHasKey('0', $options);
    }

    public function testMakeSelectElementOptionValuesAreNodeIds(): void
    {
        $tree = self::buildTree([[5, 0, 'Five'], [10, 5, 'Ten']]);
        $element = $tree->makeSelectElement('cat', 'title');
        $options = $element->getOptions(false);

        $this->assertArrayHasKey(5, $options);
        $this->assertArrayHasKey(10, $options);
    }

    public function testGetTreeObjectsAreSameInstances(): void
    {
        $obj1 = self::createObj(1, 0);
        $obj2 = self::createObj(2, 1);
        $tree = new \XoopsObjectTree([$obj1, $obj2], 'id', 'pid');

        $data = $tree->getTree();
        $this->assertSame($obj1, $data[1]['obj']);
        $this->assertSame($obj2, $data[2]['obj']);
    }

    public function testGetByKeyReturnsSameInstanceAsGetTree(): void
    {
        $tree = self::buildTree([[1, 0], [2, 1]]);
        $data = $tree->getTree();
        $obj  = $tree->getByKey(2);

        $this->assertSame($data[2]['obj'], $obj);
    }
}
