<?php

declare(strict_types=1);

namespace kernel;

use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use XoopsBlock;
use XoopsBlockHandler;

require_once XOOPS_ROOT_PATH . '/kernel/block.php';

/**
 * Unit tests for XoopsBlock and XoopsBlockHandler.
 */
class XoopsBlockTest extends KernelTestCase
{
    // =========================================================================
    // XoopsBlock — constructor with null
    // =========================================================================

    public function testConstructorWithNullCreatesInstance(): void
    {
        $block = new XoopsBlock();

        $this->assertInstanceOf(XoopsBlock::class, $block);
        $this->assertInstanceOf(\XoopsObject::class, $block);
    }

    public function testConstructorInitializesAllVars(): void
    {
        $block = new XoopsBlock();

        $expectedVars = [
            'bid', 'mid', 'func_num', 'options', 'name', 'title',
            'content', 'side', 'weight', 'visible', 'block_type',
            'c_type', 'isactive', 'dirname', 'func_file', 'show_func',
            'edit_func', 'template', 'bcachetime', 'last_modified',
        ];

        $vars = $block->getVars();
        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
    }

    public function testDefaultValues(): void
    {
        $block = new XoopsBlock();

        $this->assertEquals(0, $block->getVar('mid'));
        $this->assertEquals(0, $block->getVar('func_num'));
        $this->assertEquals(0, $block->getVar('side'));
        $this->assertEquals(0, $block->getVar('weight'));
        $this->assertEquals(0, $block->getVar('visible'));
        $this->assertEquals(0, $block->getVar('bcachetime'));
        $this->assertEquals(0, $block->getVar('last_modified'));
    }

    // =========================================================================
    // XoopsBlock — constructor with array
    // =========================================================================

    public function testConstructorWithArrayAssignsVars(): void
    {
        $data = [
            'bid'        => 10,
            'mid'        => 1,
            'name'       => 'Test Block',
            'title'      => 'Block Title',
            'content'    => 'Block content here',
            'side'       => 0,
            'weight'     => 5,
            'visible'    => 1,
            'block_type' => 'S',
            'c_type'     => 'H',
            'isactive'   => 1,
            'dirname'    => 'system',
        ];

        $block = new XoopsBlock($data);

        $this->assertEquals(10, $block->getVar('bid'));
        $this->assertEquals(1, $block->getVar('mid'));
        $this->assertEquals('Test Block', $block->getVar('name'));
        $this->assertEquals('Block Title', $block->getVar('title'));
        $this->assertEquals('Block content here', $block->getVar('content', 'n'));
        $this->assertEquals(1, $block->getVar('visible'));
        $this->assertEquals('S', $block->getVar('block_type'));
        $this->assertEquals(1, $block->getVar('isactive'));
    }

    // =========================================================================
    // XoopsBlock — isCustom
    // =========================================================================

    public function testIsCustomReturnsTrueForTypeC(): void
    {
        $block = new XoopsBlock();
        $block->setVar('block_type', 'C');

        $this->assertTrue($block->isCustom());
    }

    public function testIsCustomReturnsTrueForTypeE(): void
    {
        $block = new XoopsBlock();
        $block->setVar('block_type', 'E');

        $this->assertTrue($block->isCustom());
    }

    public function testIsCustomReturnsFalseForTypeS(): void
    {
        $block = new XoopsBlock();
        $block->setVar('block_type', 'S');

        $this->assertFalse($block->isCustom());
    }

    public function testIsCustomReturnsFalseForTypeM(): void
    {
        $block = new XoopsBlock();
        $block->setVar('block_type', 'M');

        $this->assertFalse($block->isCustom());
    }

    public function testIsCustomReturnsFalseForTypeD(): void
    {
        $block = new XoopsBlock();
        $block->setVar('block_type', 'D');

        $this->assertFalse($block->isCustom());
    }

    public function testIsCustomReturnsFalseForNull(): void
    {
        $block = new XoopsBlock();

        $this->assertFalse($block->isCustom());
    }

    // =========================================================================
    // XoopsBlock — buildContent
    // =========================================================================

    public function testBuildContentPosition0PrefixesDbContent(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildContent(0, 'new_content', 'db_content');

        $this->assertEquals('db_contentnew_content', $result);
    }

    public function testBuildContentPosition1AppendsDbContent(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildContent(1, 'new_content', 'db_content');

        $this->assertEquals('new_contentdb_content', $result);
    }

    public function testBuildContentPosition0WithEmptyContent(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildContent(0, '', 'db_content');

        $this->assertEquals('db_content', $result);
    }

    public function testBuildContentPosition1WithEmptyContent(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildContent(1, '', 'db_content');

        $this->assertEquals('db_content', $result);
    }

    public function testBuildContentPosition0WithEmptyDbContent(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildContent(0, 'new_content', '');

        $this->assertEquals('new_content', $result);
    }

    public function testBuildContentPosition1WithEmptyDbContent(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildContent(1, 'new_content', '');

        $this->assertEquals('new_content', $result);
    }

    public function testBuildContentWithBothEmpty(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildContent(0, '', '');

        $this->assertEquals('', $result);
    }

    public function testBuildContentInvalidPositionReturnsNull(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildContent(2, 'content', 'db_content');

        $this->assertNull($result);
    }

    // =========================================================================
    // XoopsBlock — buildTitle
    // =========================================================================

    public function testBuildTitleReturnsNewTitleWhenNotEmpty(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildTitle('Original Title', 'New Title');

        $this->assertEquals('New Title', $result);
    }

    public function testBuildTitleReturnsOriginalWhenNewIsEmpty(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildTitle('Original Title', '');

        $this->assertEquals('Original Title', $result);
    }

    public function testBuildTitleReturnsOriginalWhenNewIsOmitted(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildTitle('Original Title');

        $this->assertEquals('Original Title', $result);
    }

    public function testBuildTitleWithEmptyOriginalAndNewTitle(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildTitle('', 'New Title');

        $this->assertEquals('New Title', $result);
    }

    public function testBuildTitleWithBothEmpty(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildTitle('', '');

        $this->assertEquals('', $result);
    }

    // =========================================================================
    // XoopsBlock — accessor methods
    // =========================================================================

    public function testIdAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('bid', 42);

        $this->assertEquals(42, $block->id());
    }

    public function testBidAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('bid', 10);

        $this->assertEquals(10, $block->bid());
    }

    public function testMidAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('mid', 3);

        $this->assertEquals(3, $block->mid());
    }

    public function testFuncNumAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('func_num', 1);

        $this->assertEquals(1, $block->func_num());
    }

    public function testOptionsAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('options', 'opt1|opt2|opt3');

        $this->assertEquals('opt1|opt2|opt3', $block->options());
    }

    public function testNameAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('name', 'Block Name');

        $this->assertEquals('Block Name', $block->name());
    }

    public function testTitleAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('title', 'Block Title');

        $this->assertEquals('Block Title', $block->title());
    }

    public function testContentAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('content', 'Block content');

        $this->assertEquals('Block content', $block->content());
    }

    public function testSideAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('side', 1);

        $this->assertEquals(1, $block->side());
    }

    public function testWeightAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('weight', 5);

        $this->assertEquals(5, $block->weight());
    }

    public function testVisibleAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('visible', 1);

        $this->assertEquals(1, $block->visible());
    }

    public function testBlockTypeAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('block_type', 'S');

        $this->assertEquals('S', $block->block_type());
    }

    public function testCTypeAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('c_type', 'H');

        $this->assertEquals('H', $block->c_type());
    }

    public function testIsactiveAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('isactive', 1);

        $this->assertEquals(1, $block->isactive());
    }

    public function testDirnameAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('dirname', 'system');

        $this->assertEquals('system', $block->dirname());
    }

    public function testFuncFileAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('func_file', 'xoops_block.php');

        $this->assertEquals('xoops_block.php', $block->func_file());
    }

    public function testShowFuncAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('show_func', 'b_system_main_show');

        $this->assertEquals('b_system_main_show', $block->show_func());
    }

    public function testEditFuncAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('edit_func', 'b_system_main_edit');

        $this->assertEquals('b_system_main_edit', $block->edit_func());
    }

    public function testTemplateAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('template', 'system_block_login.tpl');

        $this->assertEquals('system_block_login.tpl', $block->template());
    }

    public function testBcachetimeAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('bcachetime', 3600);

        $this->assertEquals(3600, $block->bcachetime());
    }

    public function testLastModifiedAccessor(): void
    {
        $block = new XoopsBlock();
        $block->setVar('last_modified', 1234567890);

        $this->assertEquals(1234567890, $block->last_modified());
    }

    // =========================================================================
    // XoopsBlockHandler — create
    // =========================================================================

    public function testHandlerCreateReturnsNewBlock(): void
    {
        $db      = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsBlockHandler', $db);

        $block = $handler->create();

        $this->assertInstanceOf(XoopsBlock::class, $block);
        $this->assertTrue($block->isNew());
    }

    public function testHandlerCreateNotNewReturnsFlaggedBlock(): void
    {
        $db      = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsBlockHandler', $db);

        $block = $handler->create(false);

        $this->assertInstanceOf(XoopsBlock::class, $block);
        $this->assertFalse($block->isNew());
    }

    // =========================================================================
    // XoopsBlockHandler — get
    // =========================================================================

    public function testHandlerGetReturnsBlockForValidId(): void
    {
        $db = $this->createMockDatabase();

        $row = [
            'bid'           => 5,
            'mid'           => 0,
            'func_num'      => 0,
            'options'       => '',
            'name'          => 'Test Block',
            'title'         => 'Test Title',
            'content'       => 'Test content',
            'side'          => 0,
            'weight'        => 0,
            'visible'       => 1,
            'block_type'    => 'C',
            'c_type'        => 'H',
            'isactive'      => 1,
            'dirname'       => '',
            'func_file'     => '',
            'show_func'     => '',
            'edit_func'     => '',
            'template'      => '',
            'bcachetime'    => 0,
            'last_modified' => 1234567890,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsBlockHandler', $db);
        $block   = $handler->get(5);

        $this->assertInstanceOf(XoopsBlock::class, $block);
        $this->assertEquals(5, $block->getVar('bid'));
        $this->assertEquals('Test Block', $block->getVar('name'));
        $this->assertEquals('Test Title', $block->getVar('title'));
        $this->assertTrue($block->isCustom());
    }

    public function testHandlerGetReturnsFalseForZeroId(): void
    {
        $db      = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsBlockHandler', $db);

        $result = $handler->get(0);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseForNegativeId(): void
    {
        $db      = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsBlockHandler', $db);

        $result = $handler->get(-1);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsBlockHandler', $db);
        $result  = $handler->get(1);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsBlockHandler — insert
    // =========================================================================

    public function testHandlerInsertReturnsFalseForNonBlockObject(): void
    {
        $db      = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsBlockHandler', $db);

        $fakeObj = new \XoopsObject();
        $result  = $handler->insert($fakeObj);

        $this->assertFalse($result);
    }

    public function testHandlerInsertReturnsTrueForNotDirty(): void
    {
        $db      = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsBlockHandler', $db);

        $block = new XoopsBlock();
        $block->unsetNew();

        $result = $handler->insert($block);

        $this->assertTrue($result);
    }

    public function testHandlerInsertNewBlockSetsId(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(true);
        $db->method('getInsertId')->willReturn(20);

        $handler = $this->createHandler('XoopsBlockHandler', $db);

        $block = new XoopsBlock();
        $block->setNew();
        $block->setVar('name', 'New Block');
        $block->setVar('title', 'New Title');
        $block->setVar('block_type', 'C');
        $block->setVar('c_type', 'H');
        $block->setVar('content', 'Content here');
        $block->setVar('dirname', 'system');

        $result = $handler->insert($block);

        $this->assertTrue($result);
        $this->assertEquals(20, $block->getVar('bid'));
    }

    public function testHandlerInsertReturnsFalseOnExecFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsBlockHandler', $db);

        $block = new XoopsBlock();
        $block->setNew();
        $block->setVar('name', 'Fail Block');

        $result = $handler->insert($block);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsBlockHandler — delete
    // =========================================================================

    public function testHandlerDeleteReturnsFalseForNonBlockObject(): void
    {
        $db      = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsBlockHandler', $db);

        $fakeObj = new \XoopsObject();
        $result  = $handler->delete($fakeObj);

        $this->assertFalse($result);
    }

    public function testHandlerDeleteReturnsTrueOnSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsBlockHandler', $db);

        $block = new XoopsBlock();
        $block->assignVars(['bid' => 5]);

        $result = $handler->delete($block);

        $this->assertTrue($result);
    }

    public function testHandlerDeleteReturnsFalseOnExecFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsBlockHandler', $db);

        $block = new XoopsBlock();
        $block->assignVars(['bid' => 5]);

        $result = $handler->delete($block);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsBlockHandler — getObjects
    // =========================================================================

    public function testHandlerGetObjectsReturnsBlockArray(): void
    {
        $db = $this->createMockDatabase();
        $this->stubMultiRowResult($db, [
            [
                'bid'           => 1,
                'mid'           => 0,
                'func_num'      => 0,
                'options'       => '',
                'name'          => 'Block 1',
                'title'         => 'Title 1',
                'content'       => 'Content 1',
                'side'          => 0,
                'weight'        => 0,
                'visible'       => 1,
                'block_type'    => 'S',
                'c_type'        => 'H',
                'isactive'      => 1,
                'dirname'       => 'system',
                'func_file'     => '',
                'show_func'     => '',
                'edit_func'     => '',
                'template'      => '',
                'bcachetime'    => 0,
                'last_modified' => 1000,
            ],
            [
                'bid'           => 2,
                'mid'           => 0,
                'func_num'      => 0,
                'options'       => '',
                'name'          => 'Block 2',
                'title'         => 'Title 2',
                'content'       => 'Content 2',
                'side'          => 1,
                'weight'        => 5,
                'visible'       => 1,
                'block_type'    => 'C',
                'c_type'        => 'H',
                'isactive'      => 1,
                'dirname'       => '',
                'func_file'     => '',
                'show_func'     => '',
                'edit_func'     => '',
                'template'      => '',
                'bcachetime'    => 0,
                'last_modified' => 2000,
            ],
        ]);

        $handler = $this->createHandler('XoopsBlockHandler', $db);
        $result  = $handler->getObjects();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(XoopsBlock::class, $result[0]);
        $this->assertInstanceOf(XoopsBlock::class, $result[1]);
    }

    public function testHandlerGetObjectsWithIdAsKey(): void
    {
        $db = $this->createMockDatabase();
        $this->stubMultiRowResult($db, [
            [
                'bid'           => 10,
                'mid'           => 0,
                'func_num'      => 0,
                'options'       => '',
                'name'          => 'Keyed Block',
                'title'         => 'Keyed Title',
                'content'       => '',
                'side'          => 0,
                'weight'        => 0,
                'visible'       => 1,
                'block_type'    => 'S',
                'c_type'        => 'H',
                'isactive'      => 1,
                'dirname'       => 'system',
                'func_file'     => '',
                'show_func'     => '',
                'edit_func'     => '',
                'template'      => '',
                'bcachetime'    => 0,
                'last_modified' => 0,
            ],
        ]);

        $handler = $this->createHandler('XoopsBlockHandler', $db);
        $result  = $handler->getObjects(null, true);

        $this->assertArrayHasKey(10, $result);
        $this->assertInstanceOf(XoopsBlock::class, $result[10]);
    }

    public function testHandlerGetObjectsReturnsEmptyOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsBlockHandler', $db);
        $result  = $handler->getObjects();

        $this->assertSame([], $result);
    }

    // =========================================================================
    // XoopsBlockHandler — getList
    // =========================================================================

    public function testHandlerGetListReturnsNameForNonCustom(): void
    {
        $db = $this->createMockDatabase();
        $this->stubMultiRowResult($db, [
            [
                'bid'           => 1,
                'mid'           => 1,
                'func_num'      => 0,
                'options'       => '',
                'name'          => 'System Block',
                'title'         => 'System Title',
                'content'       => '',
                'side'          => 0,
                'weight'        => 0,
                'visible'       => 1,
                'block_type'    => 'S',
                'c_type'        => 'H',
                'isactive'      => 1,
                'dirname'       => 'system',
                'func_file'     => '',
                'show_func'     => '',
                'edit_func'     => '',
                'template'      => '',
                'bcachetime'    => 0,
                'last_modified' => 0,
            ],
        ]);

        $handler = $this->createHandler('XoopsBlockHandler', $db);
        $list    = $handler->getList();

        $this->assertIsArray($list);
        $this->assertArrayHasKey(1, $list);
        // For non-custom (type S), getList returns 'name'
        $this->assertEquals('System Block', $list[1]);
    }

    public function testHandlerGetListReturnsTitleForCustom(): void
    {
        $db = $this->createMockDatabase();
        $this->stubMultiRowResult($db, [
            [
                'bid'           => 2,
                'mid'           => 0,
                'func_num'      => 0,
                'options'       => '',
                'name'          => 'Custom Name',
                'title'         => 'Custom Title',
                'content'       => 'Custom content',
                'side'          => 0,
                'weight'        => 0,
                'visible'       => 1,
                'block_type'    => 'C',
                'c_type'        => 'H',
                'isactive'      => 1,
                'dirname'       => '',
                'func_file'     => '',
                'show_func'     => '',
                'edit_func'     => '',
                'template'      => '',
                'bcachetime'    => 0,
                'last_modified' => 0,
            ],
        ]);

        $handler = $this->createHandler('XoopsBlockHandler', $db);
        $list    = $handler->getList();

        $this->assertIsArray($list);
        $this->assertArrayHasKey(2, $list);
        // For custom (type C), getList returns 'title'
        $this->assertEquals('Custom Title', $list[2]);
    }

    // =========================================================================
    // XoopsBlock — public properties
    // =========================================================================

    public function testPublicPropertiesAreAccessible(): void
    {
        $block = new XoopsBlock();

        $block->bid        = 1;
        $block->name       = 'Test';
        $block->title      = 'Title';
        $block->block_type = 'S';

        $this->assertEquals(1, $block->bid);
        $this->assertEquals('Test', $block->name);
        $this->assertEquals('Title', $block->title);
        $this->assertEquals('S', $block->block_type);
    }

    // =========================================================================
    // XoopsBlock — isCustom with all block types
    // =========================================================================

    #[DataProvider('blockTypeProvider')]
    public function testIsCustomForAllBlockTypes(string $type, bool $expected): void
    {
        $block = new XoopsBlock();
        $block->setVar('block_type', $type);

        $this->assertSame($expected, $block->isCustom());
    }

    /**
     * @return array<array{string, bool}>
     */
    public static function blockTypeProvider(): array
    {
        return [
            'System block'         => ['S', false],
            'Module block'         => ['M', false],
            'Custom block'         => ['C', true],
            'Cloned system block'  => ['D', false],
            'Cloned custom block'  => ['E', true],
        ];
    }

    // =========================================================================
    // XoopsBlock — buildContent position edge cases
    // =========================================================================

    public function testBuildContentWithHtmlContent(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildContent(0, '<p>New</p>', '<div>DB</div>');

        $this->assertEquals('<div>DB</div><p>New</p>', $result);
    }

    public function testBuildContentPosition1WithHtmlContent(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildContent(1, '<p>New</p>', '<div>DB</div>');

        $this->assertEquals('<p>New</p><div>DB</div>', $result);
    }

    // =========================================================================
    // XoopsBlock — buildTitle edge cases
    // =========================================================================

    public function testBuildTitleWithWhitespaceNewTitle(): void
    {
        $block = new XoopsBlock();

        // Non-empty string (contains a space) should replace original
        $result = $block->buildTitle('Original', ' ');

        $this->assertEquals(' ', $result);
    }

    public function testBuildTitleOriginalWithSpecialChars(): void
    {
        $block = new XoopsBlock();

        $result = $block->buildTitle('Title <b>bold</b>', '');

        $this->assertEquals('Title <b>bold</b>', $result);
    }
}
