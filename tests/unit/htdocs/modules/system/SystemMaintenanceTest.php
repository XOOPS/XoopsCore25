<?php

declare(strict_types=1);

namespace modulessystem;

use kernel\KernelTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(\SystemMaintenance::class)]
class SystemMaintenanceTest extends KernelTestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            if (!isset($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
            }
            // Define constants needed by maintenance class
            if (!defined('_AM_SYSTEM_MAINTENANCE_TABLES1')) {
                define('_AM_SYSTEM_MAINTENANCE_TABLES1', 'Table');
            }
            if (!defined('_AM_SYSTEM_MAINTENANCE_TABLES_OPTIMIZE')) {
                define('_AM_SYSTEM_MAINTENANCE_TABLES_OPTIMIZE', 'Optimize');
            }
            if (!defined('_AM_SYSTEM_MAINTENANCE_TABLES_CHECK')) {
                define('_AM_SYSTEM_MAINTENANCE_TABLES_CHECK', 'Check');
            }
            if (!defined('_AM_SYSTEM_MAINTENANCE_TABLES_REPAIR')) {
                define('_AM_SYSTEM_MAINTENANCE_TABLES_REPAIR', 'Repair');
            }
            if (!defined('_AM_SYSTEM_MAINTENANCE_TABLES_ANALYZE')) {
                define('_AM_SYSTEM_MAINTENANCE_TABLES_ANALYZE', 'Analyze');
            }
            if (!defined('_AM_SYSTEM_MAINTENANCE_DUMP_TABLES')) {
                define('_AM_SYSTEM_MAINTENANCE_DUMP_TABLES', 'Tables');
            }
            if (!defined('_AM_SYSTEM_MAINTENANCE_DUMP_STRUCTURES')) {
                define('_AM_SYSTEM_MAINTENANCE_DUMP_STRUCTURES', 'Structures');
            }
            if (!defined('_AM_SYSTEM_MAINTENANCE_DUMP_NB_RECORDS')) {
                define('_AM_SYSTEM_MAINTENANCE_DUMP_NB_RECORDS', 'Records');
            }
            if (!defined('_AM_SYSTEM_MAINTENANCE_DUMP_RECORDS')) {
                define('_AM_SYSTEM_MAINTENANCE_DUMP_RECORDS', 'records');
            }
            if (!defined('_AM_SYSTEM_MAINTENANCE_DUMP_FILE_CREATED')) {
                define('_AM_SYSTEM_MAINTENANCE_DUMP_FILE_CREATED', 'File created');
            }
            if (!defined('_AM_SYSTEM_MAINTENANCE_DUMP_RESULT')) {
                define('_AM_SYSTEM_MAINTENANCE_DUMP_RESULT', 'Result');
            }
            if (!defined('_AM_SYSTEM_MAINTENANCE_DUMP_NO_TABLES')) {
                define('_AM_SYSTEM_MAINTENANCE_DUMP_NO_TABLES', 'No tables');
            }
            require_once XOOPS_ROOT_PATH . '/modules/system/class/maintenance.php';
            self::$loaded = true;
        }
    }

    /**
     * Create a SystemMaintenance instance with a mock database injected.
     *
     * @param \XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject $db
     *
     * @return \SystemMaintenance
     */
    private function createMaintenance($db): \SystemMaintenance
    {
        $ref = new \ReflectionClass(\SystemMaintenance::class);
        $obj = $ref->newInstanceWithoutConstructor();
        $this->setProtectedProperty($obj, 'db', $db);
        $this->setProtectedProperty($obj, 'prefix', 'xoops_');

        return $obj;
    }

    /**
     * Stub SHOW TABLES to return a known table list for validation.
     *
     * @param \XoopsMySQLDatabase|\PHPUnit\Framework\MockObject\MockObject $db
     * @param array $tables  Unprefixed table names
     */
    private function stubShowTables($db, array $tables): void
    {
        $rows = [];
        foreach ($tables as $t) {
            $rows[] = ['Tables_in_test' => XOOPS_DB_PREFIX . '_' . $t];
        }
        $rows[] = false; // End of result set

        $db->method('query')->willReturn('mock_result');
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturnOnConsecutiveCalls(...$rows);
    }

    // ---------------------------------------------------------------
    // isValidTable / isValidPrefixedTable tests (via reflection)
    // ---------------------------------------------------------------

    #[Test]
    public function isValidTableReturnsTrueForExistingTable(): void
    {
        $db = $this->createMockDatabase();
        $this->stubShowTables($db, ['users', 'groups', 'session']);
        $maintenance = $this->createMaintenance($db);

        $method = new \ReflectionMethod($maintenance, 'isValidTable');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($maintenance, 'users'));
        $this->assertTrue($method->invoke($maintenance, 'groups'));
        $this->assertTrue($method->invoke($maintenance, 'session'));
    }

    #[Test]
    public function isValidTableReturnsFalseForNonExistentTable(): void
    {
        $db = $this->createMockDatabase();
        $this->stubShowTables($db, ['users', 'groups']);
        $maintenance = $this->createMaintenance($db);

        $method = new \ReflectionMethod($maintenance, 'isValidTable');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($maintenance, 'evil_table'));
    }

    #[Test]
    public function isValidTableReturnsFalseForSqlInjectionPayload(): void
    {
        $db = $this->createMockDatabase();
        $this->stubShowTables($db, ['users', 'groups']);
        $maintenance = $this->createMaintenance($db);

        $method = new \ReflectionMethod($maintenance, 'isValidTable');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($maintenance, "users; DROP TABLE users; --"));
        $this->assertFalse($method->invoke($maintenance, "users' OR '1'='1"));
    }

    #[Test]
    public function isValidPrefixedTableReturnsTrueForValidPrefixedTable(): void
    {
        $db = $this->createMockDatabase();
        $this->stubShowTables($db, ['users', 'groups']);
        $maintenance = $this->createMaintenance($db);

        $method = new \ReflectionMethod($maintenance, 'isValidPrefixedTable');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($maintenance, 'xoops_users'));
    }

    #[Test]
    public function isValidPrefixedTableReturnsFalseForInvalidPrefixedTable(): void
    {
        $db = $this->createMockDatabase();
        $this->stubShowTables($db, ['users', 'groups']);
        $maintenance = $this->createMaintenance($db);

        $method = new \ReflectionMethod($maintenance, 'isValidPrefixedTable');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($maintenance, 'xoops_evil_table'));
    }

    // ---------------------------------------------------------------
    // dump_table_structure validation tests
    // ---------------------------------------------------------------

    #[Test]
    public function dumpTableStructureRejectsInvalidTable(): void
    {
        $db = $this->createMockDatabase();
        $this->stubShowTables($db, ['users', 'groups']);
        $maintenance = $this->createMaintenance($db);

        $ret = ['', ''];
        $result = $maintenance->dump_table_structure($ret, 'xoops_evil_inject', 0, 'odd');

        // Should return unchanged $ret since the table is invalid
        $this->assertSame($ret, $result);
    }

    // ---------------------------------------------------------------
    // dump_table_datas validation tests
    // ---------------------------------------------------------------

    #[Test]
    public function dumpTableDatasRejectsInvalidTable(): void
    {
        $db = $this->createMockDatabase();
        $this->stubShowTables($db, ['users', 'groups']);
        $maintenance = $this->createMaintenance($db);

        $ret = ['', ''];
        $result = $maintenance->dump_table_datas($ret, 'xoops_evil_inject');

        // Should return unchanged $ret since the table is invalid
        $this->assertSame($ret, $result);
    }

    // ---------------------------------------------------------------
    // displayTables tests
    // ---------------------------------------------------------------

    #[Test]
    public function displayTablesReturnsArrayOfTableNames(): void
    {
        $db = $this->createMockDatabase();
        $this->stubShowTables($db, ['users', 'groups']);
        $maintenance = $this->createMaintenance($db);

        $tables = $maintenance->displayTables(true);

        $this->assertIsArray($tables);
        $this->assertArrayHasKey('users', $tables);
        $this->assertArrayHasKey('groups', $tables);
        $this->assertSame('users', $tables['users']);
    }

    #[Test]
    public function displayTablesReturnsStringWhenArrayIsFalse(): void
    {
        $db = $this->createMockDatabase();
        $this->stubShowTables($db, ['users', 'groups']);
        $maintenance = $this->createMaintenance($db);

        $result = $maintenance->displayTables(false);

        $this->assertIsString($result);
        $this->assertStringContainsString('users', $result);
        $this->assertStringContainsString('groups', $result);
    }

    // ---------------------------------------------------------------
    // Table validation caching test
    // ---------------------------------------------------------------

    #[Test]
    public function isValidTableCachesResults(): void
    {
        $db = $this->createMockDatabase();

        // Build the rows that SHOW TABLES would return
        $rows = [
            ['Tables_in_test' => XOOPS_DB_PREFIX . '_users'],
            false, // End of result set
        ];

        // query() must be called exactly once — subsequent isValidTable()
        // calls must use the cached result, not issue another query.
        $db->expects($this->once())->method('query')->willReturn('mock_result');
        $db->method('isResultSet')->willReturn(true);
        $db->method('fetchArray')->willReturnOnConsecutiveCalls(...$rows);

        $maintenance = $this->createMaintenance($db);

        $method = new \ReflectionMethod($maintenance, 'isValidTable');
        $method->setAccessible(true);

        // First call populates cache
        $this->assertTrue($method->invoke($maintenance, 'users'));
        // Second call uses cache (query() should not be called again)
        $this->assertTrue($method->invoke($maintenance, 'users'));
        // Invalid table still false from cache
        $this->assertFalse($method->invoke($maintenance, 'nonexistent'));
    }
}
