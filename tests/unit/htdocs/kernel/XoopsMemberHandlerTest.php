<?php

declare(strict_types=1);

namespace kernel;

use Criteria;
use CriteriaElement;
use PHPUnit\Framework\TestCase;
use XoopsGroup;
use XoopsGroupHandler;
use XoopsMemberHandler;
use XoopsMembership;
use XoopsMembershipHandler;
use XoopsMySQLDatabase;
use XoopsUser;
use XoopsUserHandler;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * Comprehensive unit tests for XoopsMemberHandler
 *
 * Uses mocked sub-handlers (groupHandler, userHandler, membershipHandler)
 * injected via reflection to isolate XoopsMemberHandler logic.
 */
class XoopsMemberHandlerTest extends TestCase
{
    /** @var XoopsMemberHandler */
    private $handler;
    /** @var XoopsGroupHandler|\PHPUnit\Framework\MockObject\MockObject */
    private $groupHandler;
    /** @var XoopsUserHandler|\PHPUnit\Framework\MockObject\MockObject */
    private $userHandler;
    /** @var XoopsMembershipHandler|\PHPUnit\Framework\MockObject\MockObject */
    private $membershipHandler;
    /** @var XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject */
    private $db;

    protected function setUp(): void
    {
        $this->loadDependencies();

        // Create mock database — use XoopsMySQLDatabase for fetchArray/fetchRow
        $this->db = $this->createMock(XoopsMySQLDatabase::class);
        $this->db->method('prefix')
                 ->willReturnCallback(function ($table) {
                     return 'xoops_' . $table;
                 });

        // Create mock sub-handlers
        $this->groupHandler      = $this->createMock(XoopsGroupHandler::class);
        $this->userHandler       = $this->createMock(XoopsUserHandler::class);
        $this->membershipHandler = $this->createMock(XoopsMembershipHandler::class);

        // Expose the db property on the userHandler mock (used by getUsersByGroupLink)
        $this->userHandler->db = $this->db;

        // Build handler without constructor to avoid real DB calls
        $ref           = new ReflectionClass(XoopsMemberHandler::class);
        $this->handler = $ref->newInstanceWithoutConstructor();

        // Inject mocked sub-handlers
        $this->setProtectedProperty($this->handler, 'groupHandler', $this->groupHandler);
        $this->setProtectedProperty($this->handler, 'userHandler', $this->userHandler);
        $this->setProtectedProperty($this->handler, 'membershipHandler', $this->membershipHandler);
        $this->setProtectedProperty($this->handler, 'membersWorkingList', []);
    }

    // =========================================================================
    // createGroup / createUser
    // =========================================================================

    public function testCreateGroupReturnsGroupObject(): void
    {
        $group = $this->createStubGroup(0, 'New Group');
        $this->groupHandler->expects($this->once())
                           ->method('create')
                           ->willReturn($group);

        $result = $this->handler->createGroup();
        $this->assertInstanceOf(XoopsGroup::class, $result);
    }

    public function testCreateUserReturnsUserObject(): void
    {
        $user = $this->createStubUser(0, 'newuser');
        $this->userHandler->expects($this->once())
                          ->method('create')
                          ->willReturn($user);

        $result = $this->handler->createUser();
        $this->assertInstanceOf(XoopsUser::class, $result);
    }

    // =========================================================================
    // getGroup
    // =========================================================================

    public function testGetGroupDelegatesToGroupHandler(): void
    {
        $group = $this->createStubGroup(1, 'Webmasters');
        $this->groupHandler->expects($this->once())
                           ->method('get')
                           ->with(1)
                           ->willReturn($group);

        $result = $this->handler->getGroup(1);
        $this->assertInstanceOf(XoopsGroup::class, $result);
    }

    public function testGetGroupReturnsfalseForInvalidId(): void
    {
        $this->groupHandler->expects($this->once())
                           ->method('get')
                           ->with(9999)
                           ->willReturn(false);

        $this->assertFalse($this->handler->getGroup(9999));
    }

    // =========================================================================
    // getUser (with caching)
    // =========================================================================

    public function testGetUserReturnsUserObject(): void
    {
        $user = $this->createStubUser(1, 'admin');
        $this->userHandler->expects($this->once())
                          ->method('get')
                          ->with(1)
                          ->willReturn($user);

        $result = $this->handler->getUser(1);
        $this->assertInstanceOf(XoopsUser::class, $result);
    }

    public function testGetUserCachesResult(): void
    {
        $user = $this->createStubUser(1, 'admin');
        // Should only be called ONCE despite two getUser calls
        $this->userHandler->expects($this->once())
                          ->method('get')
                          ->with(1)
                          ->willReturn($user);

        $result1 = $this->handler->getUser(1);
        $result2 = $this->handler->getUser(1);
        $this->assertSame($result1, $result2);
    }

    public function testGetUserCachesSeparateIds(): void
    {
        $user1 = $this->createStubUser(1, 'admin');
        $user2 = $this->createStubUser(2, 'editor');

        $this->userHandler->expects($this->exactly(2))
                          ->method('get')
                          ->willReturnMap([
                              [1, $user1],
                              [2, $user2],
                          ]);

        $this->assertSame($user1, $this->handler->getUser(1));
        $this->assertSame($user2, $this->handler->getUser(2));
    }

    public function testGetUserCastsIdToInt(): void
    {
        $user = $this->createStubUser(5, 'testuser');
        $this->userHandler->expects($this->once())
                          ->method('get')
                          ->with(5)
                          ->willReturn($user);

        // Pass a string — should be cast to int internally
        $result = $this->handler->getUser('5');
        $this->assertInstanceOf(XoopsUser::class, $result);
    }

    public function testGetUserReturnsfalseForNonexistent(): void
    {
        $this->userHandler->expects($this->once())
                          ->method('get')
                          ->with(9999)
                          ->willReturn(false);

        $this->assertFalse($this->handler->getUser(9999));
    }

    public function testGetUserCachesFalseResult(): void
    {
        // A miss should also be cached (false), so handler is called only once
        $this->userHandler->expects($this->once())
                          ->method('get')
                          ->with(404)
                          ->willReturn(false);

        $this->assertFalse($this->handler->getUser(404));
        $this->assertFalse($this->handler->getUser(404));
    }

    // =========================================================================
    // insertGroup / insertUser
    // =========================================================================

    public function testInsertGroupDelegatesToGroupHandler(): void
    {
        $group = $this->createStubGroup(1, 'Editors');
        $this->groupHandler->expects($this->once())
                           ->method('insert')
                           ->with($group)
                           ->willReturn(true);

        $this->assertTrue($this->handler->insertGroup($group));
    }

    public function testInsertGroupReturnsFalseOnFailure(): void
    {
        $group = $this->createStubGroup(1, 'Editors');
        $this->groupHandler->expects($this->once())
                           ->method('insert')
                           ->with($group)
                           ->willReturn(false);

        $this->assertFalse($this->handler->insertGroup($group));
    }

    public function testInsertUserDelegatesToUserHandler(): void
    {
        $user = $this->createStubUser(1, 'admin');
        $this->userHandler->expects($this->once())
                          ->method('insert')
                          ->with($user, false)
                          ->willReturn(true);

        $this->assertTrue($this->handler->insertUser($user));
    }

    public function testInsertUserWithForceFlag(): void
    {
        $user = $this->createStubUser(1, 'admin');
        $this->userHandler->expects($this->once())
                          ->method('insert')
                          ->with($user, true)
                          ->willReturn(true);

        $this->assertTrue($this->handler->insertUser($user, true));
    }

    public function testInsertUserReturnsFalseOnFailure(): void
    {
        $user = $this->createStubUser(1, 'admin');
        $this->userHandler->expects($this->once())
                          ->method('insert')
                          ->willReturn(false);

        $this->assertFalse($this->handler->insertUser($user));
    }

    // =========================================================================
    // deleteGroup
    // =========================================================================

    public function testDeleteGroupRemovesMembershipsAndGroup(): void
    {
        $group = $this->createStubGroup(5, 'OldGroup');

        $this->membershipHandler->expects($this->once())
                                ->method('deleteAll')
                                ->willReturn(true);

        $this->groupHandler->expects($this->once())
                           ->method('delete')
                           ->with($group)
                           ->willReturn(true);

        $this->assertTrue($this->handler->deleteGroup($group));
    }

    public function testDeleteGroupReturnsFalseWhenMembershipDeleteFails(): void
    {
        $group = $this->createStubGroup(5, 'OldGroup');

        $this->membershipHandler->expects($this->once())
                                ->method('deleteAll')
                                ->willReturn(false);

        $this->groupHandler->expects($this->once())
                           ->method('delete')
                           ->willReturn(true);

        $this->assertFalse($this->handler->deleteGroup($group));
    }

    public function testDeleteGroupReturnsFalseWhenGroupDeleteFails(): void
    {
        $group = $this->createStubGroup(5, 'OldGroup');

        $this->membershipHandler->expects($this->once())
                                ->method('deleteAll')
                                ->willReturn(true);

        $this->groupHandler->expects($this->once())
                           ->method('delete')
                           ->with($group)
                           ->willReturn(false);

        $this->assertFalse($this->handler->deleteGroup($group));
    }

    public function testDeleteGroupReturnsFalseWhenBothDeletesFail(): void
    {
        $group = $this->createStubGroup(5, 'OldGroup');

        $this->membershipHandler->method('deleteAll')
                                ->willReturn(false);
        $this->groupHandler->method('delete')
                           ->willReturn(false);

        $this->assertFalse($this->handler->deleteGroup($group));
    }

    // =========================================================================
    // deleteUser
    // =========================================================================

    public function testDeleteUserRemovesMembershipsAndUser(): void
    {
        $user = $this->createStubUser(10, 'olduser');

        $this->membershipHandler->expects($this->once())
                                ->method('deleteAll')
                                ->willReturn(true);

        $this->userHandler->expects($this->once())
                          ->method('delete')
                          ->with($user)
                          ->willReturn(true);

        $this->assertTrue($this->handler->deleteUser($user));
    }

    public function testDeleteUserReturnsFalseWhenMembershipDeleteFails(): void
    {
        $user = $this->createStubUser(10, 'olduser');

        $this->membershipHandler->method('deleteAll')
                                ->willReturn(false);
        $this->userHandler->method('delete')
                          ->willReturn(true);

        $this->assertFalse($this->handler->deleteUser($user));
    }

    public function testDeleteUserReturnsFalseWhenUserDeleteFails(): void
    {
        $user = $this->createStubUser(10, 'olduser');

        $this->membershipHandler->method('deleteAll')
                                ->willReturn(true);
        $this->userHandler->method('delete')
                          ->willReturn(false);

        $this->assertFalse($this->handler->deleteUser($user));
    }

    // =========================================================================
    // getGroups / getUsers
    // =========================================================================

    public function testGetGroupsDelegatesToGroupHandler(): void
    {
        $groups = [$this->createStubGroup(1, 'Admin'), $this->createStubGroup(2, 'Users')];
        $this->groupHandler->expects($this->once())
                           ->method('getObjects')
                           ->with(null, false)
                           ->willReturn($groups);

        $result = $this->handler->getGroups();
        $this->assertCount(2, $result);
    }

    public function testGetGroupsWithCriteria(): void
    {
        $criteria = new Criteria('group_type', 'Admin');
        $this->groupHandler->expects($this->once())
                           ->method('getObjects')
                           ->with($criteria, true)
                           ->willReturn([]);

        $this->assertSame([], $this->handler->getGroups($criteria, true));
    }

    public function testGetUsersReturnsEmptyArrayWhenNone(): void
    {
        $this->userHandler->expects($this->once())
                          ->method('getObjects')
                          ->willReturn([]);

        $this->assertSame([], $this->handler->getUsers());
    }

    public function testGetUsersWithIdAsKey(): void
    {
        $user = $this->createStubUser(7, 'testuser');
        $this->userHandler->expects($this->once())
                          ->method('getObjects')
                          ->with(null, true)
                          ->willReturn([7 => $user]);

        $result = $this->handler->getUsers(null, true);
        $this->assertArrayHasKey(7, $result);
    }

    // =========================================================================
    // getGroupList / getUserList
    // =========================================================================

    public function testGetGroupListReturnsIdNameMap(): void
    {
        $g1 = $this->createStubGroup(1, 'Webmasters');
        $g2 = $this->createStubGroup(2, 'Registered Users');

        $this->groupHandler->expects($this->once())
                           ->method('getObjects')
                           ->with(null, true)
                           ->willReturn([1 => $g1, 2 => $g2]);

        $result = $this->handler->getGroupList();
        $this->assertSame([1 => 'Webmasters', 2 => 'Registered Users'], $result);
    }

    public function testGetGroupListReturnsEmptyArrayWhenNoGroups(): void
    {
        $this->groupHandler->method('getObjects')
                           ->willReturn([]);
        $this->assertSame([], $this->handler->getGroupList());
    }

    public function testGetUserListReturnsIdUnameMap(): void
    {
        $u1 = $this->createStubUser(1, 'admin');
        $u2 = $this->createStubUser(2, 'editor');

        $this->userHandler->expects($this->once())
                          ->method('getObjects')
                          ->with(null, true)
                          ->willReturn([1 => $u1, 2 => $u2]);

        $result = $this->handler->getUserList();
        $this->assertSame([1 => 'admin', 2 => 'editor'], $result);
    }

    public function testGetUserListReturnsEmptyArrayWhenNoUsers(): void
    {
        $this->userHandler->method('getObjects')
                          ->willReturn([]);
        $this->assertSame([], $this->handler->getUserList());
    }

    // =========================================================================
    // addUserToGroup
    // =========================================================================

    public function testAddUserToGroupReturnsMembershipOnSuccess(): void
    {
        $mship = $this->createStubMembership();
        $this->membershipHandler->expects($this->once())
                                ->method('create')
                                ->willReturn($mship);
        $this->membershipHandler->expects($this->once())
                                ->method('insert')
                                ->with($mship)
                                ->willReturn(true);

        $result = $this->handler->addUserToGroup(1, 10);
        $this->assertInstanceOf(XoopsMembership::class, $result);
    }

    public function testAddUserToGroupReturnsFalseOnFailure(): void
    {
        $mship = $this->createStubMembership();
        $this->membershipHandler->method('create')
                                ->willReturn($mship);
        $this->membershipHandler->method('insert')
                                ->willReturn(false);

        $this->assertFalse($this->handler->addUserToGroup(1, 10));
    }

    public function testAddUserToGroupCastsIdsToIntegers(): void
    {
        $mship = $this->createStubMembership();
        $this->membershipHandler->method('create')
                                ->willReturn($mship);
        $this->membershipHandler->method('insert')
                                ->willReturn(true);

        // Pass strings — should still work via (int) cast
        $result = $this->handler->addUserToGroup('3', '99');
        $this->assertInstanceOf(XoopsMembership::class, $result);
    }

    // =========================================================================
    // removeUsersFromGroup
    // =========================================================================

    public function testRemoveUsersFromGroupWithEmptyArrayReturnsTrue(): void
    {
        // No-op success
        $this->membershipHandler->expects($this->never())
                                ->method('deleteAll');
        $this->assertTrue($this->handler->removeUsersFromGroup(1, []));
    }

    public function testRemoveUsersFromGroupDeletesMatchingMemberships(): void
    {
        $this->membershipHandler->expects($this->once())
                                ->method('deleteAll')
                                ->willReturn(true);

        $this->assertTrue($this->handler->removeUsersFromGroup(1, [10, 20, 30]));
    }

    public function testRemoveUsersFromGroupReturnsFalseOnDeleteFailure(): void
    {
        $this->membershipHandler->method('deleteAll')
                                ->willReturn(false);
        $this->assertFalse($this->handler->removeUsersFromGroup(1, [10]));
    }

    public function testRemoveUsersFromGroupFiltersInvalidIds(): void
    {
        // Negative, zero, and non-numeric values should be filtered out
        // After filtering, if all IDs are invalid, it's a no-op → true
        $this->membershipHandler->expects($this->never())
                                ->method('deleteAll');
        $this->assertTrue($this->handler->removeUsersFromGroup(1, [0, -1, 'abc']));
    }

    public function testRemoveUsersFromGroupHandlesLargeBatches(): void
    {
        // Generate > MAX_BATCH_SIZE (1000) user IDs
        $ids = range(1, 1500);

        // Should be called twice: batch of 1000, then batch of 500
        $this->membershipHandler->expects($this->exactly(2))
                                ->method('deleteAll')
                                ->willReturn(true);

        $this->assertTrue($this->handler->removeUsersFromGroup(1, $ids));
    }

    public function testRemoveUsersFromGroupLargeBatchStopsOnFirstFailure(): void
    {
        $ids = range(1, 1500);

        $this->membershipHandler->expects($this->once())
                                ->method('deleteAll')
                                ->willReturn(false);

        $this->assertFalse($this->handler->removeUsersFromGroup(1, $ids));
    }

    // =========================================================================
    // getUsersByGroup
    // =========================================================================

    public function testGetUsersByGroupReturnsUserIds(): void
    {
        $this->membershipHandler->expects($this->once())
                                ->method('getUsersByGroup')
                                ->with(1, 0, 0)
                                ->willReturn([10, 20, 30]);

        $result = $this->handler->getUsersByGroup(1);
        $this->assertSame([10, 20, 30], $result);
    }

    public function testGetUsersByGroupReturnsEmptyArrayWhenNone(): void
    {
        $this->membershipHandler->method('getUsersByGroup')
                                ->willReturn([]);

        $result = $this->handler->getUsersByGroup(1);
        $this->assertSame([], $result);
    }

    public function testGetUsersByGroupAsObjectsReturnUserObjects(): void
    {
        $user1 = $this->createStubUser(10, 'user10');
        $user2 = $this->createStubUser(20, 'user20');

        $this->membershipHandler->method('getUsersByGroup')
                                ->willReturn([10, 20]);
        $this->userHandler->expects($this->once())
                          ->method('getObjects')
                          ->willReturn([10 => $user1, 20 => $user2]);

        $result = $this->handler->getUsersByGroup(1, true);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(XoopsUser::class, $result[0]);
        $this->assertInstanceOf(XoopsUser::class, $result[1]);
    }

    public function testGetUsersByGroupAsObjectsReturnsEmptyIfNoIds(): void
    {
        $this->membershipHandler->method('getUsersByGroup')
                                ->willReturn([]);

        // When user_ids is empty, should NOT call userHandler->getObjects
        $this->userHandler->expects($this->never())
                          ->method('getObjects');

        $result = $this->handler->getUsersByGroup(1, true);
        $this->assertSame([], $result);
    }

    public function testGetUsersByGroupWithLimitAndStart(): void
    {
        $this->membershipHandler->expects($this->once())
                                ->method('getUsersByGroup')
                                ->with(1, 10, 5)
                                ->willReturn([6, 7, 8]);

        $result = $this->handler->getUsersByGroup(1, false, 10, 5);
        $this->assertSame([6, 7, 8], $result);
    }

    public function testGetUsersByGroupPreservesOrderFromMembership(): void
    {
        // Users returned in a specific order from membership table
        $user30 = $this->createStubUser(30, 'user30');
        $user10 = $this->createStubUser(10, 'user10');

        $this->membershipHandler->method('getUsersByGroup')
                                ->willReturn([30, 10]);
        $this->userHandler->method('getObjects')
                          ->willReturn([10 => $user10, 30 => $user30]);

        $result = $this->handler->getUsersByGroup(1, true);
        // Should be in original [30, 10] order, not sorted by key
        $this->assertSame($user30, $result[0]);
        $this->assertSame($user10, $result[1]);
    }

    // =========================================================================
    // getGroupsByUser
    // =========================================================================

    public function testGetGroupsByUserReturnsGroupIds(): void
    {
        $this->membershipHandler->expects($this->once())
                                ->method('getGroupsByUser')
                                ->with(1)
                                ->willReturn([1, 2, 3]);

        $result = $this->handler->getGroupsByUser(1);
        $this->assertSame([1, 2, 3], $result);
    }

    public function testGetGroupsByUserReturnsEmptyArray(): void
    {
        $this->membershipHandler->method('getGroupsByUser')
                                ->willReturn([]);

        $result = $this->handler->getGroupsByUser(999);
        $this->assertSame([], $result);
    }

    public function testGetGroupsByUserAsObjectsReturnsGroupObjects(): void
    {
        $g1 = $this->createStubGroup(1, 'Admin');
        $g2 = $this->createStubGroup(2, 'Users');

        $this->membershipHandler->method('getGroupsByUser')
                                ->willReturn([1, 2]);
        $this->groupHandler->expects($this->once())
                           ->method('getObjects')
                           ->willReturn([1 => $g1, 2 => $g2]);

        $result = $this->handler->getGroupsByUser(5, true);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(XoopsGroup::class, $result[0]);
    }

    public function testGetGroupsByUserAsObjectsReturnsEmptyIfNoGroups(): void
    {
        $this->membershipHandler->method('getGroupsByUser')
                                ->willReturn([]);
        $this->groupHandler->expects($this->never())
                           ->method('getObjects');

        $result = $this->handler->getGroupsByUser(5, true);
        $this->assertSame([], $result);
    }

    // =========================================================================
    // loginUser
    // =========================================================================

    public function testLoginUserWithModernHashReturnsUser(): void
    {
        $password = 'correct_password';
        $hash     = password_hash($password, PASSWORD_DEFAULT);

        $user = $this->createMock(XoopsUser::class);
        $user->method('pass')
             ->willReturn($hash);
        $user->method('getVar')
             ->willReturn(1);

        $this->userHandler->expects($this->once())
                          ->method('getObjects')
                          ->willReturn([$user]);

        $result = $this->handler->loginUser('admin', $password);
        $this->assertSame($user, $result);
    }

    public function testLoginUserWithLegacyMd5HashReturnsUser(): void
    {
        $password = 'legacy_password';
        $hash     = md5($password);

        $user = $this->createMock(XoopsUser::class);
        $user->method('pass')
             ->willReturn($hash);
        $user->method('getVar')
             ->willReturn(1);

        // Use a partial mock so we can stub getColumnCharacterLength
        // (it calls XoopsDatabaseFactory which needs a real DB connection)
        $ref            = new ReflectionClass(XoopsMemberHandler::class);
        $partialHandler = $this->getMockBuilder(XoopsMemberHandler::class)
                               ->disableOriginalConstructor()
                               ->onlyMethods(['getColumnCharacterLength'])
                               ->getMock();
        $partialHandler->method('getColumnCharacterLength')
                       ->willReturn(255);

        $this->setProtectedProperty($partialHandler, 'userHandler', $this->userHandler);
        $this->setProtectedProperty($partialHandler, 'membershipHandler', $this->membershipHandler);
        $this->setProtectedProperty($partialHandler, 'membersWorkingList', []);

        $this->userHandler->method('getObjects')
                          ->willReturn([$user]);
        // With column length 255, it should upgrade the hash
        $user->expects($this->once())
             ->method('setVar')
             ->with('pass', $this->callback(function ($v) { return is_string($v); }));
        $this->userHandler->expects($this->once())
                          ->method('insert')
                          ->with($user);

        $result = $partialHandler->loginUser('legacyuser', $password);
        $this->assertSame($user, $result);
    }

    public function testLoginUserWithWrongPasswordReturnsFalse(): void
    {
        $hash = password_hash('correct_password', PASSWORD_DEFAULT);

        $user = $this->createMock(XoopsUser::class);
        $user->method('pass')
             ->willReturn($hash);

        $this->userHandler->method('getObjects')
                          ->willReturn([$user]);

        $this->assertFalse($this->handler->loginUser('admin', 'wrong_password'));
    }

    public function testLoginUserWithWrongMd5PasswordReturnsFalse(): void
    {
        $hash = md5('correct_password');

        $user = $this->createMock(XoopsUser::class);
        $user->method('pass')
             ->willReturn($hash);

        $this->userHandler->method('getObjects')
                          ->willReturn([$user]);

        $this->assertFalse($this->handler->loginUser('admin', 'wrong_password'));
    }

    public function testLoginUserReturnsFalseWhenNoUsersFound(): void
    {
        $this->userHandler->method('getObjects')
                          ->willReturn([]);
        $this->assertFalse($this->handler->loginUser('nonexistent', 'password'));
    }

    public function testLoginUserReturnsFalseForNullResult(): void
    {
        // getObjects returns falsy value
        $this->userHandler->method('getObjects')
                          ->willReturn(false);
        $this->assertFalse($this->handler->loginUser('ghost', 'password'));
    }

    public function testLoginUserReturnsFalseForDuplicateUsernames(): void
    {
        // Security: if more than 1 user matches, refuse login
        $user1 = $this->createMock(XoopsUser::class);
        $user2 = $this->createMock(XoopsUser::class);

        $this->userHandler->method('getObjects')
                          ->willReturn([$user1, $user2]);

        $this->assertFalse($this->handler->loginUser('dupeuser', 'password'));
    }

    // =========================================================================
    // getUserCount / getUserCountByGroup
    // =========================================================================

    public function testGetUserCountDelegatesToUserHandler(): void
    {
        $this->userHandler->expects($this->once())
                          ->method('getCount')
                          ->with(null)
                          ->willReturn(42);

        $this->assertSame(42, $this->handler->getUserCount());
    }

    public function testGetUserCountWithCriteria(): void
    {
        $criteria = new Criteria('level', 1);
        $this->userHandler->expects($this->once())
                          ->method('getCount')
                          ->with($criteria)
                          ->willReturn(10);

        $this->assertSame(10, $this->handler->getUserCount($criteria));
    }

    public function testGetUserCountByGroupDelegatesToMembershipHandler(): void
    {
        $this->membershipHandler->expects($this->once())
                                ->method('getCount')
                                ->willReturn(15);

        $this->assertSame(15, $this->handler->getUserCountByGroup(1));
    }

    // =========================================================================
    // updateUserByField
    // =========================================================================

    public function testUpdateUserByFieldSetsVarAndInserts(): void
    {
        $user = $this->createMock(XoopsUser::class);
        $user->expects($this->once())
             ->method('setVar')
             ->with('email', 'new@example.com');

        $this->userHandler->expects($this->once())
                          ->method('insert')
                          ->with($user, false)
                          ->willReturn(true);

        $this->assertTrue($this->handler->updateUserByField($user, 'email', 'new@example.com'));
    }

    public function testUpdateUserByFieldReturnsFalseOnInsertFailure(): void
    {
        $user = $this->createMock(XoopsUser::class);
        $this->userHandler->method('insert')
                          ->willReturn(false);

        $this->assertFalse($this->handler->updateUserByField($user, 'email', 'test@test.com'));
    }

    // =========================================================================
    // updateUsersByField
    // =========================================================================

    public function testUpdateUsersByFieldDelegatesToUpdateAll(): void
    {
        $criteria = new Criteria('level', 0);
        $this->userHandler->expects($this->once())
                          ->method('updateAll')
                          ->with('level', 1, $criteria)
                          ->willReturn(true);

        $this->assertTrue($this->handler->updateUsersByField('level', 1, $criteria));
    }

    public function testUpdateUsersByFieldWithNullCriteria(): void
    {
        $this->userHandler->expects($this->once())
                          ->method('updateAll')
                          ->with('theme', 'default', null)
                          ->willReturn(true);

        $this->assertTrue($this->handler->updateUsersByField('theme', 'default'));
    }

    // =========================================================================
    // activateUser
    // =========================================================================

    public function testActivateUserSetsLevelAndInserts(): void
    {
        $user = $this->createMock(XoopsUser::class);
        $user->method('getVar')
             ->with('level')
             ->willReturn(0);

        $setVarCalls = [];
        $user->expects($this->exactly(2))
             ->method('setVar')
             ->willReturnCallback(function ($key, $value) use (&$setVarCalls) {
                 $setVarCalls[] = [$key, $value];
             });

        $this->userHandler->expects($this->once())
                          ->method('insert')
                          ->with($user, true)
                          ->willReturn(true);

        $this->assertTrue($this->handler->activateUser($user));

        $this->assertSame('level', $setVarCalls[0][0]);
        $this->assertSame(1, $setVarCalls[0][1]);
        $this->assertSame('actkey', $setVarCalls[1][0]);
        $this->assertIsString($setVarCalls[1][1]);
        $this->assertGreaterThanOrEqual(8, strlen($setVarCalls[1][1]));
    }

    public function testActivateUserAlreadyActiveReturnsTrue(): void
    {
        $user = $this->createMock(XoopsUser::class);
        $user->method('getVar')
             ->with('level')
             ->willReturn(1);

        // Should NOT attempt to insert
        $this->userHandler->expects($this->never())
                          ->method('insert');

        $this->assertTrue($this->handler->activateUser($user));
    }

    public function testActivateUserReturnsFalseOnInsertFailure(): void
    {
        $user = $this->createMock(XoopsUser::class);
        $user->method('getVar')
             ->with('level')
             ->willReturn(0);

        $this->userHandler->method('insert')
                          ->willReturn(false);

        $this->assertFalse($this->handler->activateUser($user));
    }

    // =========================================================================
    // getUsersByGroupLink
    // =========================================================================

    public function testGetUsersByGroupLinkReturnsUserIds(): void
    {
        $mockResult = $this->createMockResultSet([
            ['uid' => 10],
            ['uid' => 20],
        ]);

        $this->db->expects($this->once())
                 ->method('query')
                 ->willReturn($mockResult);
        $this->db->method('isResultSet')
                 ->with($mockResult)
                 ->willReturn(true);

        $result = $this->handler->getUsersByGroupLink([1]);
        $this->assertSame([10, 20], $result);
    }

    public function testGetUsersByGroupLinkReturnsEmptyOnQueryFailure(): void
    {
        $this->db->method('query')
                 ->willReturn(false);
        $this->db->method('isResultSet')
                 ->willReturn(false);
        // Suppress error logging
        $this->db->method('error')
                 ->willReturn('mock error');
        $this->db->method('errno')
                 ->willReturn(0);

        $result = $this->handler->getUsersByGroupLink([1]);
        $this->assertSame([], $result);
    }

    public function testGetUsersByGroupLinkWithInvalidGroupsReturnsEmpty(): void
    {
        // All IDs are invalid after sanitization
        $result = $this->handler->getUsersByGroupLink([0, -1, 'abc']);
        $this->assertSame([], $result);
    }

    public function testGetUsersByGroupLinkWithEmptyGroupsQueriesAllUsers(): void
    {
        $mockResult = $this->createMockResultSet([
            ['uid' => 1],
        ]);

        $this->db->method('query')
                 ->willReturn($mockResult);
        $this->db->method('isResultSet')
                 ->willReturn(true);

        // Empty groups array → no group filter, queries all users
        $result = $this->handler->getUsersByGroupLink([]);
        $this->assertSame([1], $result);
    }

    public function testGetUsersByGroupLinkAsObjectsReturnsUserObjects(): void
    {
        $rows       = [
            ['uid' => 10, 'uname' => 'user10', 'email' => 'a@b.com'],
            ['uid' => 20, 'uname' => 'user20', 'email' => 'c@d.com'],
        ];
        $mockResult = $this->createMockResultSet($rows);

        $this->db->method('query')
                 ->willReturn($mockResult);
        $this->db->method('isResultSet')
                 ->willReturn(true);

        $result = $this->handler->getUsersByGroupLink([1], null, true);
        $this->assertCount(2, $result);
        $this->assertInstanceOf(XoopsUser::class, $result[0]);
    }

    public function testGetUsersByGroupLinkAsObjectsWithIdAsKey(): void
    {
        $rows       = [
            ['uid' => 10, 'uname' => 'user10'],
        ];
        $mockResult = $this->createMockResultSet($rows);

        $this->db->method('query')
                 ->willReturn($mockResult);
        $this->db->method('isResultSet')
                 ->willReturn(true);

        $result = $this->handler->getUsersByGroupLink([1], null, true, true);
        $this->assertArrayHasKey(10, $result);
        $this->assertInstanceOf(XoopsUser::class, $result[10]);
    }

    // =========================================================================
    // getUserCountByGroupLink
    // =========================================================================

    public function testGetUserCountByGroupLinkReturnsCount(): void
    {
        $mockResult = $this->createMockResultSet([['COUNT(*)' => 5]]);
        $this->db->method('query')
                 ->willReturn($mockResult);
        $this->db->method('isResultSet')
                 ->willReturn(true);
        $this->db->method('fetchRow')
                 ->with($mockResult)
                 ->willReturn([5]);

        $result = $this->handler->getUserCountByGroupLink([1]);
        $this->assertSame(5, $result);
    }

    public function testGetUserCountByGroupLinkReturnsZeroOnFailure(): void
    {
        $this->db->method('query')
                 ->willReturn(false);
        $this->db->method('isResultSet')
                 ->willReturn(false);
        $this->db->method('error')
                 ->willReturn('mock error');
        $this->db->method('errno')
                 ->willReturn(0);

        $this->assertSame(0, $this->handler->getUserCountByGroupLink([1]));
    }

    public function testGetUserCountByGroupLinkWithInvalidGroupsReturnsZero(): void
    {
        $this->assertSame(0, $this->handler->getUserCountByGroupLink([0, -5]));
    }

    public function testGetUserCountByGroupLinkWithEmptyGroupsCountsAll(): void
    {
        $mockResult = $this->createMockResultSet([]);
        $this->db->method('query')
                 ->willReturn($mockResult);
        $this->db->method('isResultSet')
                 ->willReturn(true);
        $this->db->method('fetchRow')
                 ->willReturn([100]);

        $result = $this->handler->getUserCountByGroupLink([]);
        $this->assertSame(100, $result);
    }

    public function testGetUserCountByGroupLinkReturnsZeroWhenNoRows(): void
    {
        $mockResult = $this->createMockResultSet([]);
        $this->db->method('query')
                 ->willReturn($mockResult);
        $this->db->method('isResultSet')
                 ->willReturn(true);
        $this->db->method('fetchRow')
                 ->willReturn(null);

        $this->assertSame(0, $this->handler->getUserCountByGroupLink([1]));
    }

    // =========================================================================
    // getColumnCharacterLength (partial — uses static factory)
    // =========================================================================

    public function testGetColumnCharacterLengthReturnType(): void
    {
        // This method calls XoopsDatabaseFactory::getDatabaseConnection() internally,
        // which we cannot easily mock. Verify it's declared and callable.
        $ref = new ReflectionMethod(XoopsMemberHandler::class, 'getColumnCharacterLength');
        $this->assertTrue($ref->isPublic());
        $this->assertSame(2, $ref->getNumberOfParameters());
    }

    // =========================================================================
    // Type safety tests
    // =========================================================================

    public function testGetUserCastsZeroId(): void
    {
        // id=0 should still call the handler (the caching key is 0)
        $this->userHandler->expects($this->once())
                          ->method('get')
                          ->with(0)
                          ->willReturn(false);

        $this->assertFalse($this->handler->getUser(0));
    }

    public function testGetUserCastsNegativeId(): void
    {
        $this->userHandler->expects($this->once())
                          ->method('get')
                          ->with(-1)
                          ->willReturn(false);

        $this->assertFalse($this->handler->getUser(-1));
    }

    public function testGetUserCountByGroupCastsToInt(): void
    {
        $this->membershipHandler->expects($this->once())
                                ->method('getCount')
                                ->willReturn(3);

        // Pass a string group_id
        $this->assertSame(3, $this->handler->getUserCountByGroup('5'));
    }

    // =========================================================================
    // Private helper: sanitizeIds (tested indirectly)
    // =========================================================================

    public function testRemoveUsersFromGroupSanitizesMixedInput(): void
    {
        // Only valid positive integer IDs should be kept: 1, 5
        // 0, -1, 'abc', null should be filtered
        $this->membershipHandler->expects($this->once())
                                ->method('deleteAll')
                                ->willReturn(true);

        $this->assertTrue($this->handler->removeUsersFromGroup(1, [1, 0, -1, 'abc', null, 5]));
    }

    public function testRemoveUsersFromGroupAcceptsStringDigits(): void
    {
        // '10' is ctype_digit and should pass
        $this->membershipHandler->expects($this->once())
                                ->method('deleteAll')
                                ->willReturn(true);

        $this->assertTrue($this->handler->removeUsersFromGroup(1, ['10', '20']));
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testDeleteGroupAndDeleteUserUseCorrectCriteriaField(): void
    {
        // deleteGroup uses 'groupid', deleteUser uses 'uid'
        $group = $this->createStubGroup(42, 'TestGroup');
        $user  = $this->createStubUser(99, 'testuser');

        $groupCriteriaCaptured = null;
        $userCriteriaCaptured  = null;

        $this->membershipHandler->method('deleteAll')
                                ->willReturnCallback(function ($criteria) use (&$groupCriteriaCaptured, &$userCriteriaCaptured) {
                                    static $callCount = 0;
                                    $callCount++;
                                    if ($callCount === 1) {
                                        $groupCriteriaCaptured = $criteria;
                                    } else {
                                        $userCriteriaCaptured = $criteria;
                                    }
                                    return true;
                                });
        $this->groupHandler->method('delete')
                           ->willReturn(true);
        $this->userHandler->method('delete')
                          ->willReturn(true);

        $this->handler->deleteGroup($group);
        $this->handler->deleteUser($user);

        $this->assertInstanceOf(CriteriaElement::class, $groupCriteriaCaptured);
        $this->assertInstanceOf(CriteriaElement::class, $userCriteriaCaptured);
    }

    public function testGetGroupsByUserPreservesGroupOrder(): void
    {
        $g3 = $this->createStubGroup(3, 'Group3');
        $g1 = $this->createStubGroup(1, 'Group1');

        $this->membershipHandler->method('getGroupsByUser')
                                ->willReturn([3, 1]);
        $this->groupHandler->method('getObjects')
                           ->willReturn([1 => $g1, 3 => $g3]);

        $result = $this->handler->getGroupsByUser(10, true);
        // Should follow the original [3, 1] order
        $this->assertSame($g3, $result[0]);
        $this->assertSame($g1, $result[1]);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function loadDependencies(): void
    {
        // Notification constants (required by user.php)
        if (!defined('XOOPS_NOTIFICATION_METHOD_PM')) {
            require_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
        }

        // Load MySQL database class (provides fetchArray/fetchRow)
        require_once XOOPS_ROOT_PATH . '/class/database/mysqldatabase.php';

        // Load the kernel files that define the classes under test
        require_once XOOPS_ROOT_PATH . '/kernel/user.php';
        require_once XOOPS_ROOT_PATH . '/kernel/group.php';
        require_once XOOPS_ROOT_PATH . '/kernel/member.php';
    }

    private function setProtectedProperty(object $object, string $property, $value): void
    {
        $ref = new ReflectionProperty(get_class($object), $property);
        $ref->setAccessible(true);
        $ref->setValue($object, $value);
    }

    /**
     * Create an XoopsGroup stub with getVar returning expected values.
     */
    private function createStubGroup(int $id, string $name): XoopsGroup
    {
        $group = $this->createMock(XoopsGroup::class);
        $group->method('getVar')
              ->willReturnCallback(function ($key) use ($id, $name) {
                  switch ($key) {
                      case 'groupid':
                          return $id;
                      case 'name':
                          return $name;
                      default:
                          return null;
                  }
              });
        return $group;
    }

    /**
     * Create an XoopsUser stub with getVar returning expected values.
     */
    private function createStubUser(int $id, string $uname): XoopsUser
    {
        $user = $this->createMock(XoopsUser::class);
        $user->method('getVar')
             ->willReturnCallback(function ($key) use ($id, $uname) {
                 switch ($key) {
                     case 'uid':
                         return $id;
                     case 'uname':
                         return $uname;
                     default:
                         return null;
                 }
             });
        return $user;
    }

    /**
     * Create an XoopsMembership stub.
     */
    private function createStubMembership(): XoopsMembership
    {
        return $this->createMock(XoopsMembership::class);
    }

    /**
     * Create a mock that simulates a database result set with fetchArray() iteration.
     *
     * @param array $rows Array of associative arrays representing rows
     * @return object A value to pass to db->isResultSet() and db->fetchArray()
     */
    private function createMockResultSet(array $rows)
    {
        // We return a simple stdClass as the result handle.
        // The db mock's fetchArray method uses willReturnOnConsecutiveCalls.
        $result = new \stdClass();

        $fetchReturns   = $rows;
        $fetchReturns[] = false; // End of result set marker

        $this->db->method('fetchArray')
                 ->willReturnOnConsecutiveCalls(...$fetchReturns);

        return $result;
    }
}
