<?php

declare(strict_types=1);

namespace kernel;

use ReflectionClass;
use XoopsGuestUser;
use XoopsUser;
use XoopsUserHandler;

// Load dependencies not covered by the bootstrap
require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
require_once XOOPS_ROOT_PATH . '/kernel/user.php';

/**
 * Unit tests for XoopsUser, XoopsGuestUser, and XoopsUserHandler.
 */
class XoopsUserTest extends KernelTestCase
{
    // =========================================================================
    // XoopsUser — constructor
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $user = new XoopsUser();

        $this->assertInstanceOf(XoopsUser::class, $user);
        $this->assertInstanceOf(\XoopsObject::class, $user);
    }

    public function testConstructorInitializesAllVars(): void
    {
        $user = new XoopsUser();

        $expectedVars = [
            'uid', 'name', 'uname', 'email', 'url', 'user_avatar',
            'user_regdate', 'user_icq', 'user_from', 'user_sig',
            'user_viewemail', 'actkey', 'user_aim', 'user_yim',
            'user_msnm', 'pass', 'posts', 'attachsig', 'rank',
            'level', 'theme', 'timezone_offset', 'last_login',
            'umode', 'uorder', 'notify_method', 'notify_mode',
            'user_occ', 'bio', 'user_intrest', 'user_mailok',
        ];

        $vars = $user->getVars();
        foreach ($expectedVars as $varName) {
            $this->assertArrayHasKey($varName, $vars, "Missing var: {$varName}");
        }
    }

    public function testConstructorWithArrayAssignsVars(): void
    {
        $data = [
            'uid'   => 42,
            'uname' => 'testuser',
            'email' => 'test@example.com',
            'level' => 1,
        ];

        $user = new XoopsUser($data);

        $this->assertEquals(42, $user->getVar('uid'));
        $this->assertEquals('testuser', $user->getVar('uname'));
        $this->assertEquals('test@example.com', $user->getVar('email'));
        $this->assertEquals(1, $user->getVar('level'));
    }

    public function testConstructorWithNullDoesNotAssignVars(): void
    {
        $user = new XoopsUser(null);

        // uid is XOBJ_DTYPE_INT with null default; getVar() returns '' for null INT
        $this->assertSame('', $user->getVar('uid'));
    }

    // =========================================================================
    // XoopsUser — default values
    // =========================================================================

    public function testDefaultValuesAreCorrect(): void
    {
        $user = new XoopsUser();

        $this->assertEquals(0, $user->getVar('user_viewemail'));
        $this->assertEquals(0, $user->getVar('attachsig'));
        $this->assertEquals(0, $user->getVar('rank'));
        $this->assertEquals(0, $user->getVar('level'));
        $this->assertEquals('0.0', $user->getVar('timezone_offset'));
        $this->assertEquals(0, $user->getVar('last_login'));
        $this->assertEquals(1, $user->getVar('uorder'));
        $this->assertEquals(1, $user->getVar('user_mailok'));
        $this->assertEquals(XOOPS_NOTIFICATION_METHOD_PM, $user->getVar('notify_method'));
        $this->assertEquals(XOOPS_NOTIFICATION_MODE_SENDALWAYS, $user->getVar('notify_mode'));
    }

    // =========================================================================
    // XoopsUser — isGuest
    // =========================================================================

    public function testIsGuestReturnsFalse(): void
    {
        $user = new XoopsUser();

        $this->assertFalse($user->isGuest());
    }

    // =========================================================================
    // XoopsUser — isActive
    // =========================================================================

    public function testIsActiveReturnsFalseWhenLevelIsZero(): void
    {
        $user = new XoopsUser();
        $user->setVar('level', 0);

        $this->assertFalse($user->isActive());
    }

    public function testIsActiveReturnsTrueWhenLevelIsOne(): void
    {
        $user = new XoopsUser();
        $user->setVar('level', 1);

        $this->assertTrue($user->isActive());
    }

    public function testIsActiveReturnsTrueWhenLevelIsGreaterThanOne(): void
    {
        $user = new XoopsUser();
        $user->setVar('level', 5);

        $this->assertTrue($user->isActive());
    }

    // =========================================================================
    // XoopsUser — setGroups / getGroups
    // =========================================================================

    public function testSetGroupsStoresArray(): void
    {
        $user   = new XoopsUser();
        $groups = [1, 2, 3];
        $user->setGroups($groups);

        $this->assertSame([1, 2, 3], $user->_groups);
    }

    public function testSetGroupsIgnoresNonArray(): void
    {
        $user = new XoopsUser();
        $user->setGroups('not_an_array');

        $this->assertSame([], $user->_groups);
    }

    public function testSetGroupsWithEmptyArray(): void
    {
        $user = new XoopsUser();
        $user->setGroups([1, 2]);
        $user->setGroups([]);

        $this->assertSame([], $user->_groups);
    }

    public function testGetGroupsReturnsSetGroups(): void
    {
        $user   = new XoopsUser();
        $groups = [1, 2, 3];
        $user->setGroups($groups);

        $result = &$user->getGroups();

        $this->assertSame([1, 2, 3], $result);
    }

    // =========================================================================
    // XoopsUser — accessor methods
    // =========================================================================

    public function testUidAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('uid', 42);

        $this->assertEquals(42, $user->uid());
    }

    public function testIdAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('uid', 99);

        $this->assertEquals(99, $user->id());
    }

    public function testNameAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('name', 'John Doe');

        $result = $user->name('n');
        $this->assertEquals('John Doe', $result);
    }

    public function testUnameAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('uname', 'johndoe');

        $result = $user->uname('n');
        $this->assertEquals('johndoe', $result);
    }

    public function testEmailAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('email', 'john@example.com');

        $result = $user->email('n');
        $this->assertEquals('john@example.com', $result);
    }

    public function testUrlAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('url', 'https://example.com');

        $result = $user->url('n');
        $this->assertEquals('https://example.com', $result);
    }

    public function testUserAvatarAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('user_avatar', 'avatar.png');

        $result = $user->user_avatar('n');
        $this->assertEquals('avatar.png', $result);
    }

    public function testUserRegdateAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('user_regdate', 1234567890);

        $this->assertEquals(1234567890, $user->user_regdate());
    }

    public function testUserIcqAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('user_icq', '12345');

        $result = $user->user_icq('n');
        $this->assertEquals('12345', $result);
    }

    public function testUserFromAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('user_from', 'New York');

        $result = $user->user_from('n');
        $this->assertEquals('New York', $result);
    }

    public function testUserSigAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('user_sig', 'My signature');

        $result = $user->user_sig('n');
        $this->assertEquals('My signature', $result);
    }

    public function testUserViewemailAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('user_viewemail', 1);

        $this->assertEquals(1, $user->user_viewemail());
    }

    public function testActkeyAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('actkey', 'abc123');

        $this->assertEquals('abc123', $user->actkey());
    }

    public function testUserAimAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('user_aim', 'myaim');

        $result = $user->user_aim('n');
        $this->assertEquals('myaim', $result);
    }

    public function testUserYimAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('user_yim', 'myyim');

        $result = $user->user_yim('n');
        $this->assertEquals('myyim', $result);
    }

    public function testUserMsnmAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('user_msnm', 'mymsnm');

        $result = $user->user_msnm('n');
        $this->assertEquals('mymsnm', $result);
    }

    public function testPassAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('pass', 'hashed_password');

        $this->assertEquals('hashed_password', $user->pass());
    }

    public function testPostsAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('posts', 100);

        $this->assertEquals(100, $user->posts());
    }

    public function testAttachsigAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('attachsig', 1);

        $this->assertEquals(1, $user->attachsig());
    }

    public function testLevelAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('level', 5);

        $this->assertEquals(5, $user->level());
    }

    public function testThemeAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('theme', 'xbootstrap5');

        $this->assertEquals('xbootstrap5', $user->theme());
    }

    public function testTimezoneAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('timezone_offset', '5.5');

        $this->assertEquals('5.5', $user->timezone());
    }

    public function testUmodeAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('umode', 'flat');

        $this->assertEquals('flat', $user->umode());
    }

    public function testUorderAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('uorder', 0);

        $this->assertEquals(0, $user->uorder());
    }

    public function testNotifyMethodAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('notify_method', XOOPS_NOTIFICATION_METHOD_EMAIL);

        $this->assertEquals(XOOPS_NOTIFICATION_METHOD_EMAIL, $user->notify_method());
    }

    public function testNotifyModeAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('notify_mode', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE);

        $this->assertEquals(XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE, $user->notify_mode());
    }

    public function testUserOccAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('user_occ', 'Developer');

        $result = $user->user_occ('n');
        $this->assertEquals('Developer', $result);
    }

    public function testBioAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('bio', 'A short bio');

        $result = $user->bio('n');
        $this->assertEquals('A short bio', $result);
    }

    public function testUserIntrestAccessor(): void
    {
        $user = new XoopsUser();
        $user->setVar('user_intrest', 'Coding');

        $result = $user->user_intrest('n');
        $this->assertEquals('Coding', $result);
    }

    // =========================================================================
    // XoopsUser — assignVars
    // =========================================================================

    public function testAssignVarsSetsUserData(): void
    {
        $user = new XoopsUser();
        $data = [
            'uid'   => 10,
            'uname' => 'assigned_user',
            'email' => 'assigned@example.com',
            'level' => 1,
            'posts' => 50,
        ];
        $user->assignVars($data);

        $this->assertEquals(10, $user->getVar('uid'));
        $this->assertEquals('assigned_user', $user->getVar('uname'));
        $this->assertEquals('assigned@example.com', $user->getVar('email'));
        $this->assertEquals(1, $user->getVar('level'));
        $this->assertEquals(50, $user->getVar('posts'));
    }

    public function testAssignVarsPartialUpdate(): void
    {
        $user = new XoopsUser();
        $user->assignVars(['uid' => 1, 'uname' => 'first']);
        $user->assignVars(['email' => 'new@example.com']);

        $this->assertEquals(1, $user->getVar('uid'));
        $this->assertEquals('first', $user->getVar('uname'));
        $this->assertEquals('new@example.com', $user->getVar('email'));
    }

    // =========================================================================
    // XoopsUser — _groups property
    // =========================================================================

    public function testGroupsPropertyDefaultsToEmpty(): void
    {
        $user = new XoopsUser();

        $this->assertSame([], $user->_groups);
    }

    // =========================================================================
    // XoopsGuestUser
    // =========================================================================

    public function testGuestUserIsInstanceOfXoopsUser(): void
    {
        $guest = new XoopsGuestUser();

        $this->assertInstanceOf(XoopsUser::class, $guest);
        $this->assertInstanceOf(XoopsGuestUser::class, $guest);
    }

    public function testGuestUserIsGuestReturnsTrue(): void
    {
        $guest = new XoopsGuestUser();

        $this->assertTrue($guest->isGuest());
    }

    public function testGuestUserInheritsUserVars(): void
    {
        $guest = new XoopsGuestUser();

        $vars = $guest->getVars();
        $this->assertArrayHasKey('uid', $vars);
        $this->assertArrayHasKey('uname', $vars);
        $this->assertArrayHasKey('email', $vars);
    }

    public function testGuestUserIsActiveDefaultsFalse(): void
    {
        $guest = new XoopsGuestUser();

        // level defaults to 0, so isActive() should be false
        $this->assertFalse($guest->isActive());
    }

    public function testGuestUserSetAndGetGroups(): void
    {
        $guest = new XoopsGuestUser();
        $guest->setGroups([\XOOPS_GROUP_ANONYMOUS]);

        $result = &$guest->getGroups();
        $this->assertSame([\XOOPS_GROUP_ANONYMOUS], $result);
    }

    // =========================================================================
    // XoopsUserHandler
    // =========================================================================

    public function testHandlerCreateReturnsNewUser(): void
    {
        $db      = $this->createMockDatabase();
        $handler = (new ReflectionClass('XoopsUserHandler'))->newInstanceWithoutConstructor();
        $this->setProtectedProperty($handler, 'db', $db);
        $this->setProtectedProperty($handler, 'className', 'XoopsUser');
        $this->setProtectedProperty($handler, 'keyName', 'uid');
        $this->setProtectedProperty($handler, 'identifierName', 'uname');
        $this->setProtectedProperty($handler, 'table', 'xoops_users');

        $user = $handler->create();

        $this->assertInstanceOf(XoopsUser::class, $user);
        $this->assertTrue($user->isNew());
    }

    public function testHandlerCreateNotNewReturnsFlaggedUser(): void
    {
        $db      = $this->createMockDatabase();
        $handler = (new ReflectionClass('XoopsUserHandler'))->newInstanceWithoutConstructor();
        $this->setProtectedProperty($handler, 'db', $db);
        $this->setProtectedProperty($handler, 'className', 'XoopsUser');
        $this->setProtectedProperty($handler, 'keyName', 'uid');
        $this->setProtectedProperty($handler, 'identifierName', 'uname');
        $this->setProtectedProperty($handler, 'table', 'xoops_users');

        $user = $handler->create(false);

        $this->assertInstanceOf(XoopsUser::class, $user);
        $this->assertFalse($user->isNew());
    }

    public function testHandlerGetReturnsUserForValidId(): void
    {
        $db = $this->createMockDatabase();

        $row = [
            'uid'   => 1,
            'uname' => 'admin',
            'email' => 'admin@example.com',
            'level' => 1,
        ];
        $this->stubSingleRowResult($db, $row);

        $handler = (new ReflectionClass('XoopsUserHandler'))->newInstanceWithoutConstructor();
        $this->setProtectedProperty($handler, 'db', $db);
        $this->setProtectedProperty($handler, 'className', 'XoopsUser');
        $this->setProtectedProperty($handler, 'keyName', 'uid');
        $this->setProtectedProperty($handler, 'identifierName', 'uname');
        $this->setProtectedProperty($handler, 'table', 'xoops_users');

        $user = $handler->get(1);

        $this->assertInstanceOf(XoopsUser::class, $user);
        $this->assertEquals(1, $user->getVar('uid'));
        $this->assertEquals('admin', $user->getVar('uname'));
    }

    public function testHandlerGetReturnsFalseForZeroId(): void
    {
        $db      = $this->createMockDatabase();
        $handler = (new ReflectionClass('XoopsUserHandler'))->newInstanceWithoutConstructor();
        $this->setProtectedProperty($handler, 'db', $db);
        $this->setProtectedProperty($handler, 'className', 'XoopsUser');
        $this->setProtectedProperty($handler, 'keyName', 'uid');
        $this->setProtectedProperty($handler, 'identifierName', 'uname');
        $this->setProtectedProperty($handler, 'table', 'xoops_users');

        $result = $handler->get(0);

        // XoopsPersistableObjectHandler::get() returns a new object for empty id (0),
        // not false — this is different from XoopsObjectHandler subclasses
        $this->assertInstanceOf(XoopsUser::class, $result);
        $this->assertTrue($result->isNew());
    }

    // =========================================================================
    // XoopsUser — isGuest vs isActive combined scenarios
    // =========================================================================

    public function testRegularUserIsNotGuestAndCanBeActive(): void
    {
        $user = new XoopsUser();
        $user->setVar('level', 1);

        $this->assertFalse($user->isGuest());
        $this->assertTrue($user->isActive());
    }

    public function testGuestUserIsGuestAndInactive(): void
    {
        $guest = new XoopsGuestUser();

        $this->assertTrue($guest->isGuest());
        $this->assertFalse($guest->isActive());
    }

    // =========================================================================
    // XoopsUser — edge cases
    // =========================================================================

    public function testSetVarAndGetVarRoundTrip(): void
    {
        $user = new XoopsUser();
        $user->setVar('uname', 'roundtrip_user');
        $user->setVar('email', 'rt@example.com');
        $user->setVar('posts', 999);

        $this->assertEquals('roundtrip_user', $user->getVar('uname', 'n'));
        $this->assertEquals('rt@example.com', $user->getVar('email', 'n'));
        $this->assertEquals(999, $user->getVar('posts'));
    }

    public function testPublicPropertiesAreAccessible(): void
    {
        $user = new XoopsUser();

        // PHP 8.2 dynamic properties fix - these should exist as public properties
        $user->uid   = 1;
        $user->uname = 'test';

        $this->assertEquals(1, $user->uid);
        $this->assertEquals('test', $user->uname);
    }
}
