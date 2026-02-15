<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsGroupFormCheckBox;

xoops_load('XoopsFormElement');
xoops_load('XoopsFormHidden');
xoops_load('XoopsFormHiddenToken');
xoops_load('XoopsForm');
xoops_load('XoopsFormElementTray');
xoops_load('XoopsFormButton');

// grouppermform.php defines both XoopsGroupPermForm and XoopsGroupFormCheckBox
require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

/**
 * Unit tests for XoopsGroupFormCheckBox.
 *
 * XoopsGroupFormCheckBox extends XoopsFormElement and is used within
 * XoopsGroupPermForm to render per-group checkboxes for permission items.
 */
class XoopsGroupFormCheckBoxTest extends \PHPUnit\Framework\TestCase
{
    // =========================================================================
    // Constructor — property initialization
    // =========================================================================

    public function testConstructorSetsCaption(): void
    {
        $element = new XoopsGroupFormCheckBox('Webmasters', 'perms[perm]', 1);

        $this->assertSame('Webmasters', $element->getCaption());
    }

    public function testConstructorSetsName(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'my_field', 1);

        $this->assertSame('my_field', $element->getName(false));
    }

    public function testConstructorSetsGroupId(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 5);

        $this->assertSame(5, $element->_groupId);
    }

    public function testConstructorSetsGroupIdZero(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 0);

        $this->assertSame(0, $element->_groupId);
    }

    public function testConstructorWithNullValuesLeavesEmptyArray(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1, null);

        $this->assertSame([], $element->_value);
    }

    public function testConstructorWithNoValuesDefaultsToEmptyArray(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);

        $this->assertSame([], $element->_value);
    }

    public function testConstructorWithScalarValue(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1, 42);

        $this->assertSame([42], $element->_value);
    }

    public function testConstructorWithArrayValues(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1, [10, 20, 30]);

        $this->assertSame([10, 20, 30], $element->_value);
    }

    public function testConstructorWithEmptyArrayValues(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1, []);

        $this->assertSame([], $element->_value);
    }

    // =========================================================================
    // Default properties
    // =========================================================================

    public function testDefaultValueIsEmptyArray(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);

        $this->assertSame([], $element->_value);
    }

    public function testDefaultOptionTreeIsEmptyArray(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);

        $this->assertSame([], $element->_optionTree);
    }

    // =========================================================================
    // setValue — scalar
    // =========================================================================

    public function testSetValueWithScalarAppendsToArray(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);
        $element->setValue(5);

        $this->assertSame([5], $element->_value);
    }

    public function testSetValueWithMultipleScalarsAccumulates(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);
        $element->setValue(1);
        $element->setValue(2);
        $element->setValue(3);

        $this->assertSame([1, 2, 3], $element->_value);
    }

    public function testSetValueWithStringScalar(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);
        $element->setValue('hello');

        $this->assertSame(['hello'], $element->_value);
    }

    public function testSetValueWithZero(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);
        $element->setValue(0);

        $this->assertSame([0], $element->_value);
    }

    public function testSetValueWithEmptyString(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);
        $element->setValue('');

        $this->assertSame([''], $element->_value);
    }

    // =========================================================================
    // setValue — array
    // =========================================================================

    public function testSetValueWithSimpleArray(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);
        $element->setValue([10, 20, 30]);

        $this->assertSame([10, 20, 30], $element->_value);
    }

    public function testSetValueWithArrayAppendsToExisting(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);
        $element->setValue(1);
        $element->setValue([2, 3]);

        $this->assertSame([1, 2, 3], $element->_value);
    }

    public function testSetValueWithEmptyArray(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);
        $element->setValue([]);

        $this->assertSame([], $element->_value);
    }

    public function testSetValueWithSingleElementArray(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);
        $element->setValue([42]);

        $this->assertSame([42], $element->_value);
    }

    // =========================================================================
    // setValue — nested array (recursive flattening)
    // =========================================================================

    public function testSetValueWithNestedArray(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);
        $element->setValue([[1, 2], [3, 4]]);

        // Recursive: [1,2] -> 1, 2; [3,4] -> 3, 4
        $this->assertSame([1, 2, 3, 4], $element->_value);
    }

    public function testSetValueWithDeeplyNestedArray(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);
        $element->setValue([[[5]]]);

        // [[5]] -> [5] -> 5
        $this->assertSame([5], $element->_value);
    }

    public function testSetValueWithMixedArrayAndScalar(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);
        $element->setValue([1, [2, 3], 4]);

        $this->assertSame([1, 2, 3, 4], $element->_value);
    }

    // =========================================================================
    // setOptionTree
    // =========================================================================

    public function testSetOptionTreeSetsProperty(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);

        $tree = [
            0 => ['children' => [1, 2]],
            1 => ['id' => 1, 'name' => 'Item 1', 'parent' => 0, 'children' => [], 'allchild' => []],
            2 => ['id' => 2, 'name' => 'Item 2', 'parent' => 0, 'children' => [], 'allchild' => []],
        ];

        $element->setOptionTree($tree);

        $this->assertSame($tree, $element->_optionTree);
    }

    public function testSetOptionTreeByReference(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);

        $tree = [
            0 => ['children' => [1]],
            1 => ['id' => 1, 'name' => 'Item 1', 'parent' => 0],
        ];

        $element->setOptionTree($tree);

        // Modify the original — changes should be reflected due to reference
        $tree[0]['children'][] = 2;
        $tree[2] = ['id' => 2, 'name' => 'Item 2', 'parent' => 0];

        $this->assertContains(2, $element->_optionTree[0]['children']);
        $this->assertArrayHasKey(2, $element->_optionTree);
    }

    public function testSetOptionTreeEmptyArray(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);

        $tree = [];
        $element->setOptionTree($tree);

        $this->assertSame([], $element->_optionTree);
    }

    public function testSetOptionTreeOverwritesPrevious(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);

        $tree1 = [0 => ['children' => [1]]];
        $element->setOptionTree($tree1);

        $tree2 = [0 => ['children' => [99]]];
        $element->setOptionTree($tree2);

        $this->assertContains(99, $element->_optionTree[0]['children']);
    }

    // =========================================================================
    // groupId
    // =========================================================================

    public function testGroupIdStoredCorrectly(): void
    {
        $element = new XoopsGroupFormCheckBox('Admin', 'perms', XOOPS_GROUP_ADMIN);

        $this->assertSame(XOOPS_GROUP_ADMIN, $element->_groupId);
    }

    public function testGroupIdUsers(): void
    {
        $element = new XoopsGroupFormCheckBox('Users', 'perms', XOOPS_GROUP_USERS);

        $this->assertSame(XOOPS_GROUP_USERS, $element->_groupId);
    }

    public function testGroupIdAnonymous(): void
    {
        $element = new XoopsGroupFormCheckBox('Anonymous', 'perms', XOOPS_GROUP_ANONYMOUS);

        $this->assertSame(XOOPS_GROUP_ANONYMOUS, $element->_groupId);
    }

    public function testGroupIdCustomValue(): void
    {
        $element = new XoopsGroupFormCheckBox('Custom', 'perms', 42);

        $this->assertSame(42, $element->_groupId);
    }

    // =========================================================================
    // Inheritance
    // =========================================================================

    public function testExtendsXoopsFormElement(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);

        $this->assertInstanceOf(\XoopsFormElement::class, $element);
    }

    public function testIsNotAContainer(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);

        $this->assertFalse($element->isContainer());
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testEmptyCaption(): void
    {
        $element = new XoopsGroupFormCheckBox('', 'name', 1);

        $this->assertSame('', $element->getCaption());
    }

    public function testEmptyName(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', '', 1);

        $this->assertSame('', $element->getName(false));
    }

    public function testCaptionWithSpecialCharacters(): void
    {
        $element = new XoopsGroupFormCheckBox('Group <Admin> & "Users"', 'name', 1);

        $this->assertSame('Group <Admin> & "Users"', $element->getCaption());
    }

    public function testNameWithBrackets(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'perms[module_read]', 1);

        $this->assertSame('perms[module_read]', $element->getName(false));
    }

    public function testLargeGroupId(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 999999);

        $this->assertSame(999999, $element->_groupId);
    }

    public function testSetValuePreservesOrder(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);
        $element->setValue(3);
        $element->setValue(1);
        $element->setValue(2);

        $this->assertSame([3, 1, 2], $element->_value);
    }

    public function testSetValueAllowsDuplicates(): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);
        $element->setValue(5);
        $element->setValue(5);

        // setValue just appends, so duplicates are allowed
        $this->assertSame([5, 5], $element->_value);
    }

    #[DataProvider('constructorValuesProvider')]
    public function testConstructorWithVariousValues($values, array $expected): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1, $values);

        $this->assertSame($expected, $element->_value);
    }

    /**
     * @return array<string, array{0: mixed, 1: array}>
     */
    public static function constructorValuesProvider(): array
    {
        return [
            'null'             => [null, []],
            'single int'       => [42, [42]],
            'single string'    => ['foo', ['foo']],
            'array of ints'    => [[1, 2, 3], [1, 2, 3]],
            'empty array'      => [[], []],
            'nested array'     => [[1, [2, 3]], [1, 2, 3]],
            'zero'             => [0, [0]],
        ];
    }

    #[DataProvider('setValueDataProvider')]
    public function testSetValueSequentialCalls(array $calls, array $expected): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', 1);

        foreach ($calls as $val) {
            $element->setValue($val);
        }

        $this->assertSame($expected, $element->_value);
    }

    /**
     * @return array<string, array{0: array, 1: array}>
     */
    public static function setValueDataProvider(): array
    {
        return [
            'single scalar'       => [[5], [5]],
            'two scalars'         => [[1, 2], [1, 2]],
            'scalar then array'   => [[1, [2, 3]], [1, 2, 3]],
            'array then scalar'   => [[[1, 2], 3], [1, 2, 3]],
            'empty calls'         => [[], []],
            'mixed types'         => [['a', 1, [2, 'b']], ['a', 1, 2, 'b']],
        ];
    }

    #[DataProvider('groupIdProvider')]
    public function testGroupIdDataDriven(int $groupId): void
    {
        $element = new XoopsGroupFormCheckBox('Caption', 'name', $groupId);

        $this->assertSame($groupId, $element->_groupId);
    }

    /**
     * @return array<string, array{0: int}>
     */
    public static function groupIdProvider(): array
    {
        return [
            'admin'     => [XOOPS_GROUP_ADMIN],
            'users'     => [XOOPS_GROUP_USERS],
            'anonymous' => [XOOPS_GROUP_ANONYMOUS],
            'zero'      => [0],
            'custom'    => [100],
            'large'     => [PHP_INT_MAX],
        ];
    }
}
