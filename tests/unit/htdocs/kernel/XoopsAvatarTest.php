<?php

declare(strict_types=1);

namespace kernel;

require_once XOOPS_ROOT_PATH . '/kernel/avatar.php';

use PHPUnit\Framework\Attributes\CoversClass;
use XoopsAvatar;
use XoopsAvatarHandler;
use XoopsObject;
use Criteria;
use CriteriaCompo;

/**
 * Unit tests for XoopsAvatar and XoopsAvatarHandler.
 */
#[CoversClass(XoopsAvatar::class)]
#[CoversClass(XoopsAvatarHandler::class)]
class XoopsAvatarTest extends KernelTestCase
{
    // =========================================================================
    // XoopsAvatar Object -- constructor / initVar
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $avatar = new XoopsAvatar();

        $this->assertInstanceOf(XoopsAvatar::class, $avatar);
        $this->assertInstanceOf(XoopsObject::class, $avatar);
    }

    public function testConstructorInitializesAllVars(): void
    {
        $avatar = new XoopsAvatar();

        $expectedVars = [
            'avatar_id', 'avatar_file', 'avatar_name', 'avatar_mimetype',
            'avatar_created', 'avatar_display', 'avatar_weight', 'avatar_type',
        ];

        $vars = $avatar->getVars();
        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
        $this->assertCount(8, $vars);
    }

    public function testConstructorSetsCorrectDataTypes(): void
    {
        $avatar = new XoopsAvatar();
        $vars = $avatar->getVars();

        $this->assertSame(XOBJ_DTYPE_INT, $vars['avatar_id']['data_type']);
        $this->assertSame(XOBJ_DTYPE_OTHER, $vars['avatar_file']['data_type']);
        $this->assertSame(XOBJ_DTYPE_TXTBOX, $vars['avatar_name']['data_type']);
        $this->assertSame(XOBJ_DTYPE_OTHER, $vars['avatar_mimetype']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['avatar_created']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['avatar_display']['data_type']);
        $this->assertSame(XOBJ_DTYPE_INT, $vars['avatar_weight']['data_type']);
        $this->assertSame(XOBJ_DTYPE_OTHER, $vars['avatar_type']['data_type']);
    }

    public function testDefaultValues(): void
    {
        $avatar = new XoopsAvatar();

        $this->assertEquals(1, $avatar->getVar('avatar_display'));
        $this->assertEquals(0, $avatar->getVar('avatar_weight'));
        $this->assertEquals(0, $avatar->getVar('avatar_type'));
    }

    public function testAvatarNameIsRequired(): void
    {
        $avatar = new XoopsAvatar();
        $vars = $avatar->getVars();

        $this->assertTrue($vars['avatar_name']['required']);
    }

    public function testAvatarNameMaxlength(): void
    {
        $avatar = new XoopsAvatar();
        $vars = $avatar->getVars();

        $this->assertSame(100, $vars['avatar_name']['maxlength']);
    }

    public function testAvatarFileMaxlength(): void
    {
        $avatar = new XoopsAvatar();
        $vars = $avatar->getVars();

        $this->assertSame(30, $vars['avatar_file']['maxlength']);
    }

    // =========================================================================
    // XoopsAvatar -- accessor methods
    // =========================================================================

    public function testIdAccessor(): void
    {
        $avatar = new XoopsAvatar();
        $avatar->setVar('avatar_id', 42);

        $this->assertEquals(42, $avatar->id());
    }

    public function testAvatarIdAccessor(): void
    {
        $avatar = new XoopsAvatar();
        $avatar->setVar('avatar_id', 10);

        $this->assertEquals(10, $avatar->avatar_id());
    }

    public function testAvatarFileAccessor(): void
    {
        $avatar = new XoopsAvatar();
        $avatar->setVar('avatar_file', 'myavatar.png');

        $this->assertEquals('myavatar.png', $avatar->avatar_file());
    }

    public function testAvatarNameAccessor(): void
    {
        $avatar = new XoopsAvatar();
        $avatar->setVar('avatar_name', 'Cool Avatar');

        $this->assertEquals('Cool Avatar', $avatar->avatar_name());
    }

    public function testAvatarMimetypeAccessor(): void
    {
        $avatar = new XoopsAvatar();
        $avatar->setVar('avatar_mimetype', 'image/png');

        $this->assertEquals('image/png', $avatar->avatar_mimetype());
    }

    public function testAvatarCreatedAccessor(): void
    {
        $avatar = new XoopsAvatar();
        $avatar->setVar('avatar_created', 1234567890);

        $this->assertEquals(1234567890, $avatar->avatar_created());
    }

    public function testAvatarDisplayAccessor(): void
    {
        $avatar = new XoopsAvatar();
        $avatar->setVar('avatar_display', 0);

        $this->assertEquals(0, $avatar->avatar_display());
    }

    public function testAvatarWeightAccessor(): void
    {
        $avatar = new XoopsAvatar();
        $avatar->setVar('avatar_weight', 5);

        $this->assertEquals(5, $avatar->avatar_weight());
    }

    public function testAvatarTypeAccessor(): void
    {
        $avatar = new XoopsAvatar();
        $avatar->setVar('avatar_type', 'S');

        $this->assertEquals('S', $avatar->avatar_type());
    }

    // =========================================================================
    // XoopsAvatar -- setUserCount / getUserCount
    // =========================================================================

    public function testSetUserCountCastsToInt(): void
    {
        $avatar = new XoopsAvatar();
        $avatar->setUserCount('15');

        $this->assertSame(15, $avatar->getUserCount());
    }

    public function testSetUserCountWithZero(): void
    {
        $avatar = new XoopsAvatar();
        $avatar->setUserCount(0);

        $this->assertSame(0, $avatar->getUserCount());
    }

    public function testGetUserCountReturnsSetValue(): void
    {
        $avatar = new XoopsAvatar();
        $avatar->setUserCount(42);

        $this->assertSame(42, $avatar->getUserCount());
    }

    public function testGetUserCountDefaultsToNull(): void
    {
        $avatar = new XoopsAvatar();

        $this->assertNull($avatar->getUserCount());
    }

    // =========================================================================
    // XoopsAvatar -- isNew / isDirty
    // =========================================================================

    public function testIsNewFalseOnRawConstruction(): void
    {
        $avatar = new XoopsAvatar();
        $this->assertFalse($avatar->isNew());
    }

    public function testIsDirtyFalseOnRawConstruction(): void
    {
        $avatar = new XoopsAvatar();
        $this->assertFalse($avatar->isDirty());
    }

    public function testSetVarMakesObjectDirty(): void
    {
        $avatar = new XoopsAvatar();
        $avatar->setVar('avatar_name', 'Test');

        $this->assertTrue($avatar->isDirty());
    }

    // =========================================================================
    // XoopsAvatar -- public properties (PHP 8.2 compatibility)
    // =========================================================================

    public function testPublicPropertiesAreAccessible(): void
    {
        $avatar = new XoopsAvatar();

        $avatar->avatar_id   = 1;
        $avatar->avatar_file = 'test.png';
        $avatar->avatar_name = 'Test';

        $this->assertEquals(1, $avatar->avatar_id);
        $this->assertEquals('test.png', $avatar->avatar_file);
        $this->assertEquals('Test', $avatar->avatar_name);
    }

    // =========================================================================
    // XoopsAvatarHandler -- create
    // =========================================================================

    public function testHandlerCreateReturnsNewAvatar(): void
    {
        $db = $this->createMockDatabase();
        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $avatar = $handler->create();

        $this->assertInstanceOf(XoopsAvatar::class, $avatar);
        $this->assertTrue($avatar->isNew());
    }

    public function testHandlerCreateNotNewReturnsFlaggedAvatar(): void
    {
        $db = $this->createMockDatabase();
        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $avatar = $handler->create(false);

        $this->assertInstanceOf(XoopsAvatar::class, $avatar);
        $this->assertFalse($avatar->isNew());
    }

    // =========================================================================
    // XoopsAvatarHandler -- get
    // =========================================================================

    public function testHandlerGetReturnsAvatarForValidId(): void
    {
        $db = $this->createMockDatabase();

        $row = [
            'avatar_id'       => 5,
            'avatar_file'     => 'test.png',
            'avatar_name'     => 'Test Avatar',
            'avatar_mimetype' => 'image/png',
            'avatar_created'  => 1234567890,
            'avatar_display'  => 1,
            'avatar_weight'   => 0,
            'avatar_type'     => 'S',
        ];
        $this->stubSingleRowResult($db, $row);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);
        $avatar  = $handler->get(5);

        $this->assertInstanceOf(XoopsAvatar::class, $avatar);
        $this->assertEquals(5, $avatar->getVar('avatar_id'));
        $this->assertEquals('test.png', $avatar->getVar('avatar_file'));
        $this->assertEquals('Test Avatar', $avatar->getVar('avatar_name'));
    }

    public function testHandlerGetReturnsFalseForZeroId(): void
    {
        $db = $this->createMockDatabase();
        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $result = $handler->get(0);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseForNegativeId(): void
    {
        $db = $this->createMockDatabase();
        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $result = $handler->get(-1);

        $this->assertFalse($result);
    }

    public function testHandlerGetReturnsFalseOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);
        $result  = $handler->get(1);

        $this->assertFalse($result);
    }

    public function testHandlerGetCastsIdToInt(): void
    {
        $db = $this->createMockDatabase();

        $row = [
            'avatar_id'       => 3,
            'avatar_file'     => 'cast.png',
            'avatar_name'     => 'CastTest',
            'avatar_mimetype' => 'image/png',
            'avatar_created'  => 0,
            'avatar_display'  => 1,
            'avatar_weight'   => 0,
            'avatar_type'     => 'S',
        ];
        $this->stubSingleRowResult($db, $row);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);
        $result  = $handler->get('3');

        $this->assertInstanceOf(XoopsAvatar::class, $result);
    }

    // =========================================================================
    // XoopsAvatarHandler -- insert
    // =========================================================================

    public function testHandlerInsertReturnsFalseForNonAvatarObject(): void
    {
        $db = $this->createMockDatabase();
        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $fakeObj = new XoopsObject();
        $result  = $handler->insert($fakeObj);

        $this->assertFalse($result);
    }

    public function testHandlerInsertNewAvatarSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(true);
        $db->method('getInsertId')->willReturn(10);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $avatar = $this->createMockAvatarForInsert(true, [
            'avatar_id'       => 0,
            'avatar_file'     => 'new.png',
            'avatar_name'     => 'New Avatar',
            'avatar_mimetype' => 'image/png',
            'avatar_created'  => 0,
            'avatar_display'  => 1,
            'avatar_weight'   => 0,
            'avatar_type'     => 'S',
        ]);

        $result = $handler->insert($avatar);

        $this->assertTrue($result);
        $this->assertEquals(10, $avatar->getVar('avatar_id'));
    }

    public function testHandlerInsertUpdateExistingAvatarSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $avatar = $this->createMockAvatarForInsert(false, [
            'avatar_id'       => 5,
            'avatar_file'     => 'updated.png',
            'avatar_name'     => 'Updated',
            'avatar_mimetype' => 'image/png',
            'avatar_created'  => 1700000000,
            'avatar_display'  => 1,
            'avatar_weight'   => 2,
            'avatar_type'     => 'S',
        ]);

        $result = $handler->insert($avatar);

        $this->assertTrue($result);
    }

    public function testHandlerInsertReturnsTrueForNotDirty(): void
    {
        $db = $this->createMockDatabase();
        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $avatar = new XoopsAvatar();
        $avatar->unsetNew();

        $result = $handler->insert($avatar);

        $this->assertTrue($result);
    }

    public function testHandlerInsertReturnsFalseOnExecFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('genId')->willReturn(0);
        $db->method('exec')->willReturn(false);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $avatar = $this->createMockAvatarForInsert(true, [
            'avatar_id'       => 0,
            'avatar_file'     => 'fail.png',
            'avatar_name'     => 'Fail Avatar',
            'avatar_mimetype' => 'image/gif',
            'avatar_created'  => 0,
            'avatar_display'  => 1,
            'avatar_weight'   => 0,
            'avatar_type'     => '0',
        ]);

        $result = $handler->insert($avatar);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsAvatarHandler -- delete
    // =========================================================================

    public function testHandlerDeleteReturnsFalseForNonAvatarObject(): void
    {
        $db = $this->createMockDatabase();
        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $fakeObj = new XoopsObject();
        $result  = $handler->delete($fakeObj);

        $this->assertFalse($result);
    }

    public function testHandlerDeleteReturnsTrueOnSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $avatar = new XoopsAvatar();
        $avatar->assignVars(['avatar_id' => 5]);

        $result = $handler->delete($avatar);

        $this->assertTrue($result);
    }

    public function testHandlerDeleteReturnsFalseOnExecFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(false);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $avatar = new XoopsAvatar();
        $avatar->assignVars(['avatar_id' => 5]);

        $result = $handler->delete($avatar);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsAvatarHandler -- getObjects
    // =========================================================================

    public function testHandlerGetObjectsReturnsAvatarArray(): void
    {
        $db = $this->createMockDatabase();
        $this->stubMultiRowResult($db, [
            [
                'avatar_id'       => 1,
                'avatar_file'     => 'a.png',
                'avatar_name'     => 'Avatar A',
                'avatar_mimetype' => 'image/png',
                'avatar_created'  => 1000,
                'avatar_display'  => 1,
                'avatar_weight'   => 0,
                'avatar_type'     => 'S',
                'count'           => 5,
            ],
            [
                'avatar_id'       => 2,
                'avatar_file'     => 'b.png',
                'avatar_name'     => 'Avatar B',
                'avatar_mimetype' => 'image/png',
                'avatar_created'  => 2000,
                'avatar_display'  => 1,
                'avatar_weight'   => 1,
                'avatar_type'     => 'S',
                'count'           => 3,
            ],
        ]);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);
        $result  = $handler->getObjects();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(XoopsAvatar::class, $result[0]);
        $this->assertSame(5, $result[0]->getUserCount());
        $this->assertSame(3, $result[1]->getUserCount());
    }

    public function testHandlerGetObjectsWithIdAsKey(): void
    {
        $db = $this->createMockDatabase();
        $this->stubMultiRowResult($db, [
            [
                'avatar_id'       => 10,
                'avatar_file'     => 'ten.png',
                'avatar_name'     => 'Ten',
                'avatar_mimetype' => 'image/png',
                'avatar_created'  => 1000,
                'avatar_display'  => 1,
                'avatar_weight'   => 0,
                'avatar_type'     => 'S',
                'count'           => 2,
            ],
        ]);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);
        $result  = $handler->getObjects(null, true);

        $this->assertArrayHasKey(10, $result);
        $this->assertInstanceOf(XoopsAvatar::class, $result[10]);
    }

    public function testHandlerGetObjectsWithCriteriaApplied(): void
    {
        $db = $this->createMockDatabase();
        $this->stubMultiRowResult($db, [
            [
                'avatar_id'       => 3,
                'avatar_file'     => 'sys.png',
                'avatar_name'     => 'System',
                'avatar_mimetype' => 'image/png',
                'avatar_created'  => 500,
                'avatar_display'  => 1,
                'avatar_weight'   => 0,
                'avatar_type'     => 'S',
                'count'           => 1,
            ],
        ]);

        /** @var XoopsAvatarHandler $handler */
        $handler  = $this->createHandler('XoopsAvatarHandler', $db);
        $criteria = new Criteria('avatar_type', 'S');
        $result   = $handler->getObjects($criteria);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(XoopsAvatar::class, $result[0]);
    }

    public function testHandlerGetObjectsThrowsOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);
        $db->method('error')->willReturn('test error');

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $this->expectException(\RuntimeException::class);
        $handler->getObjects();
    }

    // =========================================================================
    // XoopsAvatarHandler -- getCount
    // =========================================================================

    public function testHandlerGetCountReturnsInt(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 7);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);
        $count   = $handler->getCount();

        $this->assertSame(7, $count);
    }

    public function testHandlerGetCountReturnsZeroOnFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);
        $count   = $handler->getCount();

        $this->assertSame(0, $count);
    }

    public function testHandlerGetCountWithCriteria(): void
    {
        $db = $this->createMockDatabase();
        $this->stubCountResult($db, 3);

        /** @var XoopsAvatarHandler $handler */
        $handler  = $this->createHandler('XoopsAvatarHandler', $db);
        $criteria = new Criteria('avatar_type', 'S');
        $count    = $handler->getCount($criteria);

        $this->assertSame(3, $count);
    }

    // =========================================================================
    // XoopsAvatarHandler -- addUser
    // =========================================================================

    public function testHandlerAddUserReturnsTrueOnSuccess(): void
    {
        $db = $this->createMockDatabase();
        $db->method('exec')->willReturn(true);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);
        $result  = $handler->addUser(5, 10);

        $this->assertTrue($result);
    }

    public function testHandlerAddUserReturnsFalseForZeroAvatarId(): void
    {
        $db = $this->createMockDatabase();
        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $this->assertFalse($handler->addUser(0, 1));
    }

    public function testHandlerAddUserReturnsFalseForZeroUserId(): void
    {
        $db = $this->createMockDatabase();
        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $this->assertFalse($handler->addUser(1, 0));
    }

    public function testHandlerAddUserReturnsFalseForBothZero(): void
    {
        $db = $this->createMockDatabase();
        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $this->assertFalse($handler->addUser(0, 0));
    }

    public function testHandlerAddUserReturnsFalseForNegativeId(): void
    {
        $db = $this->createMockDatabase();
        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $this->assertFalse($handler->addUser(-1, 5));
    }

    public function testHandlerAddUserReturnsFalseOnInsertFailure(): void
    {
        $db = $this->createMockDatabase();
        // First call (DELETE) succeeds, second call (INSERT) fails
        $db->method('exec')->willReturnOnConsecutiveCalls(true, false);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);
        $result  = $handler->addUser(5, 10);

        $this->assertFalse($result);
    }

    // =========================================================================
    // XoopsAvatarHandler -- getUser
    // =========================================================================

    public function testHandlerGetUserReturnsArrayOfUserIds(): void
    {
        $db = $this->createMockDatabase();
        $this->stubMultiRowResult($db, [
            ['user_id' => 10],
            ['user_id' => 20],
            ['user_id' => 30],
        ]);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $avatar = new XoopsAvatar();
        $avatar->assignVars(['avatar_id' => 5]);

        $result = $handler->getUser($avatar);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
    }

    public function testHandlerGetUserReturnsEmptyArrayOnQueryFailure(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn(false);
        $db->method('isResultSet')->willReturn(false);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $avatar = new XoopsAvatar();
        $avatar->assignVars(['avatar_id' => 5]);

        $result = $handler->getUser($avatar);

        $this->assertSame([], $result);
    }

    public function testHandlerGetUserReturnsEmptyArrayForNoResults(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn('mock_result');
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);

        $avatar = new XoopsAvatar();
        $avatar->assignVars(['avatar_id' => 99]);

        $result = $handler->getUser($avatar);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    // =========================================================================
    // XoopsAvatarHandler -- getList
    // =========================================================================

    public function testHandlerGetListStartsWithBlankGif(): void
    {
        $db = $this->createMockDatabase();
        $this->stubMultiRowResult($db, [
            [
                'avatar_id'       => 1,
                'avatar_file'     => 'smiley.png',
                'avatar_name'     => 'Smiley',
                'avatar_mimetype' => 'image/png',
                'avatar_created'  => 1000,
                'avatar_display'  => 1,
                'avatar_weight'   => 0,
                'avatar_type'     => 'S',
                'count'           => 0,
            ],
        ]);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);
        $list    = $handler->getList();

        $this->assertIsArray($list);
        $this->assertArrayHasKey('blank.gif', $list);
        $this->assertEquals(_NONE, $list['blank.gif']);
        $this->assertArrayHasKey('smiley.png', $list);
        $this->assertEquals('Smiley', $list['smiley.png']);
    }

    public function testHandlerGetListEmptyReturnsOnlyBlankGif(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn('mock_result');
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);
        $list    = $handler->getList('S');

        $this->assertCount(1, $list);
        $this->assertArrayHasKey('blank.gif', $list);
    }

    public function testHandlerGetListWithCustomType(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn('mock_result');
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);
        $list    = $handler->getList('C');

        $this->assertArrayHasKey('blank.gif', $list);
        $this->assertCount(1, $list);
    }

    public function testHandlerGetListWithDisplayFilter(): void
    {
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn('mock_result');
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);
        $list    = $handler->getList(null, true);

        $this->assertArrayHasKey('blank.gif', $list);
    }

    public function testHandlerGetListNonCTypeDefaultsToS(): void
    {
        // Passing anything other than 'C' should default to 'S'
        $db = $this->createMockDatabase();
        $db->method('query')->willReturn('mock_result');
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturn(false);

        /** @var XoopsAvatarHandler $handler */
        $handler = $this->createHandler('XoopsAvatarHandler', $db);
        $list    = $handler->getList('X');

        // Should still return at minimum blank.gif
        $this->assertArrayHasKey('blank.gif', $list);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Create a partial-mock XoopsAvatar for insert testing.
     * Bypasses cleanVars() (which requires MyTextSanitizer) by mocking it.
     *
     * @param bool  $isNew     Whether the avatar should be flagged as new
     * @param array $cleanVars The cleanVars array to set
     * @return XoopsAvatar
     */
    private function createMockAvatarForInsert(bool $isNew, array $cleanVars): XoopsAvatar
    {
        $avatar = $this->getMockBuilder(XoopsAvatar::class)
            ->onlyMethods(['cleanVars'])
            ->getMock();
        $avatar->method('cleanVars')->willReturnCallback(
            function () use ($avatar, $cleanVars) {
                $avatar->cleanVars = $cleanVars;
                return true;
            }
        );

        if ($isNew) {
            $avatar->setNew();
        } else {
            $avatar->unsetNew();
        }
        // setVar to make isDirty() true
        $avatar->setVar('avatar_name', $cleanVars['avatar_name'] ?? 'Test');

        return $avatar;
    }
}
