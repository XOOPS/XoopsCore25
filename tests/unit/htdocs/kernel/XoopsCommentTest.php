<?php

declare(strict_types=1);

namespace kernel;

require_once XOOPS_ROOT_PATH . '/kernel/comment.php';

use PHPUnit\Framework\Attributes\CoversClass;
use XoopsComment;
use XoopsCommentHandler;
use XoopsObject;
use Criteria;

/**
 * Unit tests for XoopsComment and XoopsCommentHandler.
 */
#[CoversClass(XoopsComment::class)]
#[CoversClass(XoopsCommentHandler::class)]
class XoopsCommentTest extends KernelTestCase
{
    // =========================================================================
    // XoopsComment — constructor / initVar
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $comment = new XoopsComment();

        $this->assertInstanceOf(XoopsComment::class, $comment);
        $this->assertInstanceOf(XoopsObject::class, $comment);
    }

    public function testConstructorInitializesAllVars(): void
    {
        $comment = new XoopsComment();

        $expectedVars = [
            'com_id', 'com_pid', 'com_modid', 'com_icon', 'com_title',
            'com_text', 'com_created', 'com_modified', 'com_uid',
            'com_user', 'com_email', 'com_url',
            'com_ip', 'com_sig', 'com_itemid', 'com_rootid', 'com_status',
            'com_exparams', 'dohtml', 'dosmiley', 'doxcode', 'doimage', 'dobr',
        ];

        $vars = $comment->getVars();
        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
    }

    public function testDefaultValues(): void
    {
        $comment = new XoopsComment();

        $this->assertEquals(0, $comment->getVar('com_pid'));
        $this->assertEquals(0, $comment->getVar('com_created'));
        $this->assertEquals(0, $comment->getVar('com_modified'));
        $this->assertEquals(0, $comment->getVar('com_uid'));
        $this->assertEquals(0, $comment->getVar('com_sig'));
        $this->assertEquals(0, $comment->getVar('com_itemid'));
        $this->assertEquals(0, $comment->getVar('com_rootid'));
        $this->assertEquals(0, $comment->getVar('com_status'));
        $this->assertEquals(0, $comment->getVar('dohtml'));
        $this->assertEquals(0, $comment->getVar('dosmiley'));
        $this->assertEquals(0, $comment->getVar('doxcode'));
        $this->assertEquals(0, $comment->getVar('doimage'));
        $this->assertEquals(0, $comment->getVar('dobr'));
    }

    // =========================================================================
    // XoopsComment — isRoot
    // =========================================================================

    public function testIsRootReturnsTrueWhenIdEqualsRootId(): void
    {
        $comment = new XoopsComment();
        $comment->assignVars(['com_id' => 5, 'com_rootid' => 5]);

        $this->assertTrue($comment->isRoot());
    }

    public function testIsRootReturnsFalseWhenIdDiffersFromRootId(): void
    {
        $comment = new XoopsComment();
        $comment->assignVars(['com_id' => 10, 'com_rootid' => 5]);

        $this->assertFalse($comment->isRoot());
    }

    public function testIsRootReturnsTrueForBothZero(): void
    {
        $comment = new XoopsComment();
        // Default values: com_id=null, com_rootid=0
        // After setting both to 0:
        $comment->assignVars(['com_id' => 0, 'com_rootid' => 0]);

        $this->assertTrue($comment->isRoot());
    }

    // =========================================================================
    // XoopsComment — accessor methods
    // =========================================================================

    public function testIdAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_id', 42);

        $this->assertEquals(42, $comment->id());
    }

    public function testComIdAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_id', 10);

        $this->assertEquals(10, $comment->com_id());
    }

    public function testComPidAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_pid', 5);

        $this->assertEquals(5, $comment->com_pid());
    }

    public function testComModidAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_modid', 3);

        $this->assertEquals(3, $comment->com_modid());
    }

    public function testComIconAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_icon', 'icon.png');

        $this->assertEquals('icon.png', $comment->com_icon());
    }

    public function testComTitleAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_title', 'My Comment');

        $this->assertEquals('My Comment', $comment->com_title());
    }

    public function testComTextAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_text', 'Comment body text');

        $this->assertEquals('Comment body text', $comment->com_text());
    }

    public function testComCreatedAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_created', 1234567890);

        $this->assertEquals(1234567890, $comment->com_created());
    }

    public function testComModifiedAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_modified', 1234567899);

        $this->assertEquals(1234567899, $comment->com_modified());
    }

    public function testComUidAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_uid', 7);

        $this->assertEquals(7, $comment->com_uid());
    }

    public function testComUserAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_user', 'guestuser');

        $this->assertEquals('guestuser', $comment->com_user());
    }

    public function testComEmailAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_email', 'guest@example.com');

        $this->assertEquals('guest@example.com', $comment->com_email());
    }

    public function testComUrlAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_url', 'https://example.com');

        $this->assertEquals('https://example.com', $comment->com_url());
    }

    public function testComIpAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_ip', '192.168.1.1');

        $this->assertEquals('192.168.1.1', $comment->com_ip());
    }

    public function testComSigAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_sig', 1);

        $this->assertEquals(1, $comment->com_sig());
    }

    public function testComItemidAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_itemid', 99);

        $this->assertEquals(99, $comment->com_itemid());
    }

    public function testComRootidAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_rootid', 50);

        $this->assertEquals(50, $comment->com_rootid());
    }

    public function testComStatusAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_status', 2);

        $this->assertEquals(2, $comment->com_status());
    }

    public function testComExparamsAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('com_exparams', 'param1=val1&param2=val2');

        $this->assertEquals('param1=val1&param2=val2', $comment->com_exparams());
    }

    public function testDohtmlAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('dohtml', 1);

        $this->assertEquals(1, $comment->dohtml());
    }

    public function testDosmileyAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('dosmiley', 1);

        $this->assertEquals(1, $comment->dosmiley());
    }

    public function testDoxcodeAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('doxcode', 1);

        $this->assertEquals(1, $comment->doxcode());
    }

    public function testDoimageAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('doimage', 1);

        $this->assertEquals(1, $comment->doimage());
    }

    public function testDobrAccessor(): void
    {
        $comment = new XoopsComment();
        $comment->setVar('dobr', 1);

        $this->assertEquals(1, $comment->dobr());
    }

    // =========================================================================
    // XoopsCommentHandler — create
    // =========================================================================

    public function testHandlerCreateReturnsNewComment(): void
    {
        $db      = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsCommentHandler', $db);

        $comment = $handler->create();

        $this->assertInstanceOf(XoopsComment::class, $comment);
        $this->assertTrue($comment->isNew());
    }

    public function testHandlerCreateNotNewReturnsFlaggedComment(): void
    {
        $db      = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsCommentHandler', $db);

        $comment = $handler->create(false);

        $this->assertInstanceOf(XoopsComment::class, $comment);
        $this->assertFalse($comment->isNew());
    }

    // =========================================================================
    // XoopsCommentHandler — get
    // =========================================================================

    public function testHandlerGetReturnsCommentForValidId(): void
    {
        $db = $this->createMockDatabase();

        $row = [
            'com_id'       => 10,
            'com_pid'      => 0,
            'com_modid'    => 1,
            'com_icon'     => '',
            'com_title'    => 'Test Comment',
            'com_text'     => 'Test body',
            'com_created'  => 1000,
            'com_modified' => 0,
            'com_uid'      => 5,
            'com_user'     => '',
            'com_email'    => '',
            'com_url'      => '',
            'com_ip'       => '127.0.0.1',
            'com_sig'      => 0,
            'com_itemid'   => 1,
            'com_rootid'   => 10,
            'com_status'   => 2,
            'com_exparams' => '',
            'dohtml'       => 0,
            'dosmiley'     => 1,
            'doxcode'      => 1,
            'doimage'      => 1,
            'dobr'         => 1,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = $this->createHandler('XoopsCommentHandler', $db);
        $comment = $handler->get(10);

        $this->assertInstanceOf(XoopsComment::class, $comment);
        $this->assertEquals(10, $comment->getVar('com_id'));
        $this->assertEquals('Test Comment', $comment->getVar('com_title'));
        $this->assertTrue($comment->isRoot());
    }

    public function testHandlerGetReturnsFalseForZeroId(): void
    {
        $db      = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsCommentHandler', $db);

        $result = $handler->get(0);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseForNegativeId(): void
    {
        $db      = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsCommentHandler', $db);

        $result = $handler->get(-1);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsCommentHandler', $db);
        $result  = $handler->get(1);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsCommentHandler — insert
    // =========================================================================

    public function testHandlerInsertReturnsFalseForNonCommentObject(): void
    {
        $db      = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsCommentHandler', $db);

        $fakeObj = new XoopsObject();
        $result  = $handler->insert($fakeObj);

        $this->assertFalse($result);
    }

    public function testHandlerInsertReturnsTrueForNotDirty(): void
    {
        $db      = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsCommentHandler', $db);

        $comment = new XoopsComment();
        $comment->unsetNew();

        $result = $handler->insert($comment);

        $this->assertTrue($result);
    }

    public function testHandlerInsertNewCommentSetsId(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(true);
        $db->method('getInsertId')->willReturn(15);

        /** @var XoopsCommentHandler $handler */
        $handler = $this->createHandler('XoopsCommentHandler', $db);

        $comment = new XoopsComment();
        $comment->setNew();
        // Use setVar to mark fields as dirty (assignVars does not set dirty flag)
        $comment->setVar('com_pid', 0);
        $comment->setVar('com_modid', 1);
        $comment->setVar('com_icon', '');
        $comment->setVar('com_title', 'New Comment');
        $comment->setVar('com_text', 'Comment body');
        $comment->setVar('com_created', 0);
        $comment->setVar('com_modified', 0);
        $comment->setVar('com_uid', 1);
        $comment->setVar('com_user', '');
        $comment->setVar('com_email', '');
        $comment->setVar('com_url', '');
        $comment->setVar('com_ip', '');
        $comment->setVar('com_sig', 0);
        $comment->setVar('com_itemid', 0);
        $comment->setVar('com_rootid', 0);
        $comment->setVar('com_status', 0);
        $comment->setVar('com_exparams', '');
        $comment->setVar('dohtml', 0);
        $comment->setVar('dosmiley', 0);
        $comment->setVar('doxcode', 0);
        $comment->setVar('doimage', 0);
        $comment->setVar('dobr', 0);

        $result = $handler->insert($comment);

        $this->assertTrue($result);
        $this->assertEquals(15, $comment->getVar('com_id'));
    }

    public function testHandlerInsertReturnsFalseOnExecFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(false);

        /** @var XoopsCommentHandler $handler */
        $handler = $this->createHandler('XoopsCommentHandler', $db);

        $comment = new XoopsComment();
        $comment->setNew();
        // Use setVar to mark fields as dirty (assignVars does not set dirty flag)
        $comment->setVar('com_pid', 0);
        $comment->setVar('com_modid', 1);
        $comment->setVar('com_icon', '');
        $comment->setVar('com_title', 'Fail Comment');
        $comment->setVar('com_text', 'Body');
        $comment->setVar('com_created', 0);
        $comment->setVar('com_modified', 0);
        $comment->setVar('com_uid', 1);
        $comment->setVar('com_user', '');
        $comment->setVar('com_email', '');
        $comment->setVar('com_url', '');
        $comment->setVar('com_ip', '');
        $comment->setVar('com_sig', 0);
        $comment->setVar('com_itemid', 0);
        $comment->setVar('com_rootid', 0);
        $comment->setVar('com_status', 0);
        $comment->setVar('com_exparams', '');
        $comment->setVar('dohtml', 0);
        $comment->setVar('dosmiley', 0);
        $comment->setVar('doxcode', 0);
        $comment->setVar('doimage', 0);
        $comment->setVar('dobr', 0);

        $result = $handler->insert($comment);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsCommentHandler — delete
    // =========================================================================

    public function testHandlerDeleteReturnsFalseForNonCommentObject(): void
    {
        $db      = $this->createMockDatabase();
        $handler = $this->createHandler('XoopsCommentHandler', $db);

        $fakeObj = new XoopsObject();
        $result  = $handler->delete($fakeObj);

        $this->assertFalse($result);
    }

    public function testHandlerDeleteReturnsTrueOnSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsCommentHandler', $db);

        $comment = new XoopsComment();
        $comment->assignVars(['com_id' => 5]);

        $result = $handler->delete($comment);

        $this->assertTrue($result);
    }

    public function testHandlerDeleteReturnsFalseOnExecFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsCommentHandler', $db);

        $comment = new XoopsComment();
        $comment->assignVars(['com_id' => 5]);

        $result = $handler->delete($comment);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsCommentHandler — getObjects
    // =========================================================================

    public function testHandlerGetObjectsReturnsCommentArray(): void
    {
        $db = $this->createMockDatabase();
        $this->stubMultiRowResult($db, [
            [
                'com_id'       => 1,
                'com_pid'      => 0,
                'com_modid'    => 1,
                'com_icon'     => '',
                'com_title'    => 'Comment 1',
                'com_text'     => 'Body 1',
                'com_created'  => 1000,
                'com_modified' => 0,
                'com_uid'      => 1,
                'com_user'     => '',
                'com_email'    => '',
                'com_url'      => '',
                'com_ip'       => '127.0.0.1',
                'com_sig'      => 0,
                'com_itemid'   => 1,
                'com_rootid'   => 1,
                'com_status'   => 2,
                'com_exparams' => '',
                'dohtml'       => 0,
                'dosmiley'     => 1,
                'doxcode'      => 1,
                'doimage'      => 1,
                'dobr'         => 1,
            ],
            [
                'com_id'       => 2,
                'com_pid'      => 1,
                'com_modid'    => 1,
                'com_icon'     => '',
                'com_title'    => 'Comment 2',
                'com_text'     => 'Body 2',
                'com_created'  => 2000,
                'com_modified' => 0,
                'com_uid'      => 2,
                'com_user'     => '',
                'com_email'    => '',
                'com_url'      => '',
                'com_ip'       => '127.0.0.1',
                'com_sig'      => 0,
                'com_itemid'   => 1,
                'com_rootid'   => 1,
                'com_status'   => 2,
                'com_exparams' => '',
                'dohtml'       => 0,
                'dosmiley'     => 1,
                'doxcode'      => 1,
                'doimage'      => 1,
                'dobr'         => 1,
            ],
        ]);

        $handler = $this->createHandler('XoopsCommentHandler', $db);
        $result  = $handler->getObjects();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(XoopsComment::class, $result[0]);
    }

    public function testHandlerGetObjectsWithIdAsKey(): void
    {
        $db = $this->createMockDatabase();
        $this->stubMultiRowResult($db, [
            [
                'com_id'       => 10,
                'com_pid'      => 0,
                'com_modid'    => 1,
                'com_icon'     => '',
                'com_title'    => 'Keyed Comment',
                'com_text'     => 'Body',
                'com_created'  => 1000,
                'com_modified' => 0,
                'com_uid'      => 1,
                'com_user'     => '',
                'com_email'    => '',
                'com_url'      => '',
                'com_ip'       => '127.0.0.1',
                'com_sig'      => 0,
                'com_itemid'   => 1,
                'com_rootid'   => 10,
                'com_status'   => 2,
                'com_exparams' => '',
                'dohtml'       => 0,
                'dosmiley'     => 0,
                'doxcode'      => 0,
                'doimage'      => 0,
                'dobr'         => 0,
            ],
        ]);

        $handler = $this->createHandler('XoopsCommentHandler', $db);
        $result  = $handler->getObjects(null, true);

        $this->assertArrayHasKey(10, $result);
        $this->assertInstanceOf(XoopsComment::class, $result[10]);
    }

    public function testHandlerGetObjectsReturnsEmptyOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsCommentHandler', $db);
        $result  = $handler->getObjects();

        $this->assertSame([], $result);
    }

    // =========================================================================
    // XoopsCommentHandler — getCount
    // =========================================================================

    public function testHandlerGetCountReturnsInt(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 15);

        $handler = $this->createHandler('XoopsCommentHandler', $db);
        $count   = $handler->getCount();

        $this->assertSame(15, $count);
    }

    public function testHandlerGetCountReturnsZeroOnFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        $handler = $this->createHandler('XoopsCommentHandler', $db);
        $count   = $handler->getCount();

        $this->assertSame(0, $count);
    }

    public function testHandlerGetCountWithCriteria(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 5);

        $handler  = $this->createHandler('XoopsCommentHandler', $db);
        $criteria = new Criteria('com_modid', 1);
        $count    = $handler->getCount($criteria);

        $this->assertSame(5, $count);
    }

    // =========================================================================
    // XoopsCommentHandler — deleteAll
    // =========================================================================

    public function testHandlerDeleteAllReturnsTrueOnSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsCommentHandler', $db);
        $result  = $handler->deleteAll();

        $this->assertTrue($result);
    }

    public function testHandlerDeleteAllReturnsFalseOnFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsCommentHandler', $db);
        $result  = $handler->deleteAll();

        $this->assertFalse($result);
    }

    public function testHandlerDeleteAllWithCriteria(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler  = $this->createHandler('XoopsCommentHandler', $db);
        $criteria = new Criteria('com_modid', 5);
        $result   = $handler->deleteAll($criteria);

        $this->assertTrue($result);
    }

    // =========================================================================
    // XoopsCommentHandler — getList
    // =========================================================================

    public function testHandlerGetListReturnsIdTitleMap(): void
    {
        $db = $this->createMockDatabase();
        $this->stubMultiRowResult($db, [
            [
                'com_id'       => 1,
                'com_pid'      => 0,
                'com_modid'    => 1,
                'com_icon'     => '',
                'com_title'    => 'First Comment',
                'com_text'     => 'Body',
                'com_created'  => 1000,
                'com_modified' => 0,
                'com_uid'      => 1,
                'com_user'     => '',
                'com_email'    => '',
                'com_url'      => '',
                'com_ip'       => '127.0.0.1',
                'com_sig'      => 0,
                'com_itemid'   => 1,
                'com_rootid'   => 1,
                'com_status'   => 2,
                'com_exparams' => '',
                'dohtml'       => 0,
                'dosmiley'     => 0,
                'doxcode'      => 0,
                'doimage'      => 0,
                'dobr'         => 0,
            ],
            [
                'com_id'       => 2,
                'com_pid'      => 0,
                'com_modid'    => 1,
                'com_icon'     => '',
                'com_title'    => 'Second Comment',
                'com_text'     => 'Body',
                'com_created'  => 2000,
                'com_modified' => 0,
                'com_uid'      => 2,
                'com_user'     => '',
                'com_email'    => '',
                'com_url'      => '',
                'com_ip'       => '127.0.0.1',
                'com_sig'      => 0,
                'com_itemid'   => 1,
                'com_rootid'   => 2,
                'com_status'   => 2,
                'com_exparams' => '',
                'dohtml'       => 0,
                'dosmiley'     => 0,
                'doxcode'      => 0,
                'doimage'      => 0,
                'dobr'         => 0,
            ],
        ]);

        $handler = $this->createHandler('XoopsCommentHandler', $db);
        $list    = $handler->getList();

        $this->assertIsArray($list);
        $this->assertArrayHasKey(1, $list);
        $this->assertArrayHasKey(2, $list);
        $this->assertEquals('First Comment', $list[1]);
        $this->assertEquals('Second Comment', $list[2]);
    }

    // =========================================================================
    // XoopsCommentHandler — getCountByItemId
    // =========================================================================

    public function testHandlerGetCountByItemIdReturnsCount(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 8);

        $handler = $this->createHandler('XoopsCommentHandler', $db);
        $count   = $handler->getCountByItemId(1, 10);

        $this->assertSame(8, $count);
    }

    public function testHandlerGetCountByItemIdWithStatus(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 3);

        $handler = $this->createHandler('XoopsCommentHandler', $db);
        $count   = $handler->getCountByItemId(1, 10, 2);

        $this->assertSame(3, $count);
    }

    // =========================================================================
    // XoopsCommentHandler — deleteByModule
    // =========================================================================

    public function testHandlerDeleteByModuleReturnsTrueOnSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsCommentHandler', $db);
        $result  = $handler->deleteByModule(5);

        $this->assertTrue($result);
    }

    public function testHandlerDeleteByModuleReturnsFalseOnFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(false);

        $handler = $this->createHandler('XoopsCommentHandler', $db);
        $result  = $handler->deleteByModule(5);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsCommentHandler — updateByField
    // =========================================================================

    public function testHandlerUpdateByFieldSetsVarAndInserts(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        $handler = $this->createHandler('XoopsCommentHandler', $db);

        $comment = new XoopsComment();
        $comment->setNew();
        $comment->setVar('com_title', 'Original');
        $comment->setVar('com_text', 'Body');
        $comment->setVar('com_uid', 1);

        // First insert to assign an ID
        $db->method('genId')->willReturn(0);
        $db->method('getInsertId')->willReturn(20);
        $handler->insert($comment);

        // Now update by field
        $result = $handler->updateByField($comment, 'com_status', 2);

        // updateByField calls unsetNew, setVar, insert
        $this->assertFalse($comment->isNew());
        $this->assertEquals(2, $comment->getVar('com_status'));
    }

    // =========================================================================
    // XoopsComment — assignVars
    // =========================================================================

    public function testAssignVarsSetsCommentData(): void
    {
        $comment = new XoopsComment();
        $data    = [
            'com_id'    => 100,
            'com_title' => 'Assigned Comment',
            'com_text'  => 'Assigned body',
            'com_uid'   => 5,
            'com_modid' => 2,
        ];
        $comment->assignVars($data);

        $this->assertEquals(100, $comment->getVar('com_id'));
        $this->assertEquals('Assigned Comment', $comment->getVar('com_title'));
        $this->assertEquals('Assigned body', $comment->getVar('com_text'));
        $this->assertEquals(5, $comment->getVar('com_uid'));
        $this->assertEquals(2, $comment->getVar('com_modid'));
    }

    // =========================================================================
    // XoopsComment — public properties
    // =========================================================================

    public function testPublicPropertiesAreAccessible(): void
    {
        $comment = new XoopsComment();

        $comment->com_id    = 1;
        $comment->com_title = 'Test';
        $comment->com_text  = 'Body';

        $this->assertEquals(1, $comment->com_id);
        $this->assertEquals('Test', $comment->com_title);
        $this->assertEquals('Body', $comment->com_text);
    }

    // =========================================================================
    // XoopsComment — isRoot edge cases
    // =========================================================================

    public function testIsRootWithDifferentNonZeroValues(): void
    {
        $comment = new XoopsComment();
        $comment->assignVars(['com_id' => 100, 'com_rootid' => 1]);

        $this->assertFalse($comment->isRoot());
    }

    public function testIsRootWithSameNonZeroValues(): void
    {
        $comment = new XoopsComment();
        $comment->assignVars(['com_id' => 50, 'com_rootid' => 50]);

        $this->assertTrue($comment->isRoot());
    }

}
