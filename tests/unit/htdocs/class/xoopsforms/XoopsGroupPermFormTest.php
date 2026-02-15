<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsGroupPermForm;

xoops_load('XoopsFormElement');
xoops_load('XoopsFormHidden');
xoops_load('XoopsFormHiddenToken');
xoops_load('XoopsForm');
xoops_load('XoopsFormElementTray');
xoops_load('XoopsFormButton');

// grouppermform.php defines both XoopsGroupPermForm and XoopsGroupFormCheckBox
require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

/**
 * Unit tests for XoopsGroupPermForm.
 *
 * Tests focus on addItem() and _loadAllChildItemIds() methods which
 * build and traverse the tree structure. render() is excluded because
 * it requires DB handlers (groupperm, member).
 */
class XoopsGroupPermFormTest extends \PHPUnit\Framework\TestCase
{
    // =========================================================================
    // Constructor — property initialization
    // =========================================================================

    public function testConstructorSetsModId(): void
    {
        $form = new XoopsGroupPermForm('Title', 5, 'module_read', 'Read permission');

        $this->assertSame(5, $form->_modid);
    }

    public function testConstructorCastsModIdToInt(): void
    {
        $form = new XoopsGroupPermForm('Title', '7', 'module_read', 'Read permission');

        $this->assertSame(7, $form->_modid);
    }

    public function testConstructorSetsPermName(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'module_admin', 'Admin permission');

        $this->assertSame('module_admin', $form->_permName);
    }

    public function testConstructorSetsPermDesc(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'module_read', 'Read permission description');

        $this->assertSame('Read permission description', $form->_permDesc);
    }

    public function testConstructorSetsShowAnonymousTrue(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc', '', true);

        $this->assertTrue($form->_showAnonymous);
    }

    public function testConstructorSetsShowAnonymousFalse(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc', '', false);

        $this->assertFalse($form->_showAnonymous);
    }

    public function testConstructorDefaultsShowAnonymousToTrue(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');

        $this->assertTrue($form->_showAnonymous);
    }

    public function testConstructorSetsTitle(): void
    {
        $form = new XoopsGroupPermForm('My Permission Form', 1, 'perm', 'desc');

        $this->assertSame('My Permission Form', $form->getTitle());
    }

    public function testConstructorSetsFormName(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');

        $this->assertSame('groupperm_form', $form->getName(false));
    }

    public function testConstructorSetsActionUrl(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');

        $action = $form->getAction(false);

        $this->assertStringContainsString('modules/system/admin/groupperm.php', $action);
    }

    public function testConstructorSetsMethodPost(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');

        $this->assertSame('post', $form->getMethod());
    }

    public function testConstructorItemTreeStartsEmpty(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');

        $this->assertSame([], $form->_itemTree);
    }

    // =========================================================================
    // Constructor — hidden elements added automatically
    // =========================================================================

    public function testConstructorAddsHiddenModid(): void
    {
        $form = new XoopsGroupPermForm('Title', 5, 'perm', 'desc');

        $elements = $form->getElements();

        // Should have at least a token element and a modid hidden element
        $foundModid = false;
        foreach ($elements as $el) {
            if (is_object($el) && $el instanceof \XoopsFormHidden && $el->getName(false) === 'modid') {
                $foundModid = true;
                $this->assertEquals(5, $el->getValue());
                break;
            }
        }
        $this->assertTrue($foundModid, 'Hidden modid element not found');
    }

    public function testConstructorAddsHiddenToken(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'my_perm', 'desc');

        $elements = $form->getElements();

        $foundToken = false;
        foreach ($elements as $el) {
            if (is_object($el) && $el instanceof \XoopsFormHiddenToken) {
                $foundToken = true;
                break;
            }
        }
        $this->assertTrue($foundToken, 'Hidden token element not found');
    }

    public function testConstructorAddsRedirectUrlWhenProvided(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc', 'http://example.com/redirect');

        $elements = $form->getElements();

        $foundRedirect = false;
        foreach ($elements as $el) {
            if (is_object($el) && $el instanceof \XoopsFormHidden && $el->getName(false) === 'redirect_url') {
                $foundRedirect = true;
                $this->assertSame('http://example.com/redirect', $el->getValue());
                break;
            }
        }
        $this->assertTrue($foundRedirect, 'Hidden redirect_url element not found');
    }

    public function testConstructorDoesNotAddRedirectUrlWhenEmpty(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc', '');

        $elements = $form->getElements();

        foreach ($elements as $el) {
            if (is_object($el) && $el instanceof \XoopsFormHidden && $el->getName(false) === 'redirect_url') {
                $this->fail('Hidden redirect_url element should not exist when URL is empty');
            }
        }
        // If we get here, no redirect_url was found -- correct
        $this->assertTrue(true);
    }

    // =========================================================================
    // addItem — tree structure building
    // =========================================================================

    public function testAddItemSingleTopLevelItem(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Item One');

        $this->assertArrayHasKey(1, $form->_itemTree);
        $this->assertSame('Item One', $form->_itemTree[1]['name']);
        $this->assertSame(1, $form->_itemTree[1]['id']);
        $this->assertSame(0, $form->_itemTree[1]['parent']);
    }

    public function testAddItemRegistersChildUnderParent(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Item One');

        // Parent 0 should have child 1
        $this->assertArrayHasKey(0, $form->_itemTree);
        $this->assertContains(1, $form->_itemTree[0]['children']);
    }

    public function testAddItemMultipleTopLevelItems(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Item One');
        $form->addItem(2, 'Item Two');
        $form->addItem(3, 'Item Three');

        $this->assertCount(3, $form->_itemTree[0]['children']);
        $this->assertContains(1, $form->_itemTree[0]['children']);
        $this->assertContains(2, $form->_itemTree[0]['children']);
        $this->assertContains(3, $form->_itemTree[0]['children']);
    }

    public function testAddItemWithParent(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Parent Item');
        $form->addItem(2, 'Child Item', 1);

        // Item 2 should have parent 1
        $this->assertSame(1, $form->_itemTree[2]['parent']);
        // Item 1 should have child 2
        $this->assertContains(2, $form->_itemTree[1]['children']);
    }

    public function testAddItemMultipleChildrenUnderSameParent(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Parent');
        $form->addItem(10, 'Child A', 1);
        $form->addItem(11, 'Child B', 1);
        $form->addItem(12, 'Child C', 1);

        $this->assertCount(3, $form->_itemTree[1]['children']);
        $this->assertContains(10, $form->_itemTree[1]['children']);
        $this->assertContains(11, $form->_itemTree[1]['children']);
        $this->assertContains(12, $form->_itemTree[1]['children']);
    }

    public function testAddItemNestedHierarchy(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Level 0');
        $form->addItem(2, 'Level 1', 1);
        $form->addItem(3, 'Level 2', 2);
        $form->addItem(4, 'Level 3', 3);

        $this->assertSame(0, $form->_itemTree[1]['parent']);
        $this->assertSame(1, $form->_itemTree[2]['parent']);
        $this->assertSame(2, $form->_itemTree[3]['parent']);
        $this->assertSame(3, $form->_itemTree[4]['parent']);
    }

    public function testAddItemPreservesItemName(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(42, 'Special Item Name');

        $this->assertSame('Special Item Name', $form->_itemTree[42]['name']);
    }

    public function testAddItemPreservesItemId(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(99, 'Item 99');

        $this->assertSame(99, $form->_itemTree[99]['id']);
    }

    public function testAddItemDefaultParentIsZero(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(5, 'Top Level');

        $this->assertSame(0, $form->_itemTree[5]['parent']);
    }

    public function testAddItemWithSpecialCharactersInName(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Item <with> "special" & characters');

        $this->assertSame('Item <with> "special" & characters', $form->_itemTree[1]['name']);
    }

    // =========================================================================
    // _loadAllChildItemIds — recursive child collection
    // =========================================================================

    public function testLoadAllChildItemIdsNoChildren(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Leaf');

        $childIds = [];
        $form->_loadAllChildItemIds(1, $childIds);

        $this->assertSame([], $childIds);
    }

    public function testLoadAllChildItemIdsDirectChildren(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Parent');
        $form->addItem(2, 'Child A', 1);
        $form->addItem(3, 'Child B', 1);

        $childIds = [];
        $form->_loadAllChildItemIds(1, $childIds);

        $this->assertCount(2, $childIds);
        $this->assertContains(2, $childIds);
        $this->assertContains(3, $childIds);
    }

    public function testLoadAllChildItemIdsGrandChildren(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Root');
        $form->addItem(2, 'Child', 1);
        $form->addItem(3, 'Grandchild', 2);

        $childIds = [];
        $form->_loadAllChildItemIds(1, $childIds);

        $this->assertCount(2, $childIds);
        $this->assertContains(2, $childIds);
        $this->assertContains(3, $childIds);
    }

    public function testLoadAllChildItemIdsDeepNesting(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Level 0');
        $form->addItem(2, 'Level 1', 1);
        $form->addItem(3, 'Level 2', 2);
        $form->addItem(4, 'Level 3', 3);
        $form->addItem(5, 'Level 4', 4);

        $childIds = [];
        $form->_loadAllChildItemIds(1, $childIds);

        $this->assertCount(4, $childIds);
        $this->assertContains(2, $childIds);
        $this->assertContains(3, $childIds);
        $this->assertContains(4, $childIds);
        $this->assertContains(5, $childIds);
    }

    public function testLoadAllChildItemIdsCollectsAllDescendants(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');

        // Build a more complex tree:
        //   1
        //   ├── 2
        //   │   ├── 4
        //   │   └── 5
        //   └── 3
        //       └── 6
        //           └── 7
        $form->addItem(1, 'Root');
        $form->addItem(2, 'Branch A', 1);
        $form->addItem(3, 'Branch B', 1);
        $form->addItem(4, 'Leaf A1', 2);
        $form->addItem(5, 'Leaf A2', 2);
        $form->addItem(6, 'Sub B1', 3);
        $form->addItem(7, 'Leaf B1a', 6);

        $childIds = [];
        $form->_loadAllChildItemIds(1, $childIds);

        $this->assertCount(6, $childIds);
        $this->assertContains(2, $childIds);
        $this->assertContains(3, $childIds);
        $this->assertContains(4, $childIds);
        $this->assertContains(5, $childIds);
        $this->assertContains(6, $childIds);
        $this->assertContains(7, $childIds);
    }

    public function testLoadAllChildItemIdsFromMiddleNode(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Root');
        $form->addItem(2, 'Middle', 1);
        $form->addItem(3, 'Leaf A', 2);
        $form->addItem(4, 'Leaf B', 2);
        $form->addItem(5, 'Sibling', 1);

        $childIds = [];
        $form->_loadAllChildItemIds(2, $childIds);

        // Should only contain children of item 2, not item 5
        $this->assertCount(2, $childIds);
        $this->assertContains(3, $childIds);
        $this->assertContains(4, $childIds);
        $this->assertNotContains(5, $childIds);
    }

    public function testLoadAllChildItemIdsFromRootZero(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Item A');
        $form->addItem(2, 'Item B');
        $form->addItem(3, 'Item C');

        $childIds = [];
        $form->_loadAllChildItemIds(0, $childIds);

        // Root 0 has direct children 1, 2, 3 (no grandchildren)
        $this->assertCount(3, $childIds);
        $this->assertContains(1, $childIds);
        $this->assertContains(2, $childIds);
        $this->assertContains(3, $childIds);
    }

    public function testLoadAllChildItemIdsFromNonExistentNode(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Item');

        $childIds = [];
        $form->_loadAllChildItemIds(999, $childIds);

        $this->assertSame([], $childIds);
    }

    public function testLoadAllChildItemIdsDoesNotDuplicateIds(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Root');
        $form->addItem(2, 'Child', 1);

        $childIds = [];
        $form->_loadAllChildItemIds(1, $childIds);

        // Count of 2 appearing should be exactly 1
        $counts = array_count_values($childIds);
        $this->assertSame(1, $counts[2]);
    }

    // =========================================================================
    // Tree structure integrity tests
    // =========================================================================

    public function testTreeStructureParentChildConsistency(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(10, 'Category');
        $form->addItem(20, 'SubCategory', 10);

        // Item 20 says its parent is 10
        $this->assertSame(10, $form->_itemTree[20]['parent']);
        // Item 10 has 20 in its children
        $this->assertContains(20, $form->_itemTree[10]['children']);
    }

    public function testTreeStructureMultipleBranches(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');

        // Two independent branches under root
        $form->addItem(1, 'Branch1');
        $form->addItem(2, 'Branch2');
        $form->addItem(3, 'Leaf1a', 1);
        $form->addItem(4, 'Leaf2a', 2);

        // Root has both branches
        $this->assertContains(1, $form->_itemTree[0]['children']);
        $this->assertContains(2, $form->_itemTree[0]['children']);

        // Each branch has its leaf
        $this->assertContains(3, $form->_itemTree[1]['children']);
        $this->assertContains(4, $form->_itemTree[2]['children']);

        // Branches don't have each other's leaves
        $this->assertNotContains(4, $form->_itemTree[1]['children']);
        $this->assertNotContains(3, $form->_itemTree[2]['children']);
    }

    public function testAddItemOverwritesNameForSameId(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');
        $form->addItem(1, 'Original Name');
        $form->addItem(1, 'Updated Name');

        // The second addItem for the same ID overwrites name and id
        $this->assertSame('Updated Name', $form->_itemTree[1]['name']);
    }

    #[DataProvider('treeDataProvider')]
    public function testLoadAllChildItemIdsWithVariousTrees(array $items, int $nodeId, array $expected): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');

        foreach ($items as $item) {
            if (isset($item[2])) {
                $form->addItem($item[0], $item[1], $item[2]);
            } else {
                $form->addItem($item[0], $item[1]);
            }
        }

        $childIds = [];
        $form->_loadAllChildItemIds($nodeId, $childIds);

        sort($childIds);
        sort($expected);
        $this->assertSame($expected, $childIds);
    }

    /**
     * @return array<string, array{0: array, 1: int, 2: array}>
     */
    public static function treeDataProvider(): array
    {
        return [
            'single leaf' => [
                [[1, 'Leaf']],
                1,
                [],
            ],
            'parent with one child' => [
                [[1, 'Parent'], [2, 'Child', 1]],
                1,
                [2],
            ],
            'three levels' => [
                [[1, 'L0'], [2, 'L1', 1], [3, 'L2', 2]],
                1,
                [2, 3],
            ],
            'wide tree' => [
                [[1, 'Root'], [2, 'A', 1], [3, 'B', 1], [4, 'C', 1]],
                1,
                [2, 3, 4],
            ],
            'from middle of deep tree' => [
                [[1, 'R'], [2, 'M', 1], [3, 'L', 2], [4, 'LL', 3]],
                2,
                [3, 4],
            ],
        ];
    }

    // =========================================================================
    // Extends XoopsForm
    // =========================================================================

    public function testExtendsXoopsForm(): void
    {
        $form = new XoopsGroupPermForm('Title', 1, 'perm', 'desc');

        $this->assertInstanceOf(\XoopsForm::class, $form);
    }
}
