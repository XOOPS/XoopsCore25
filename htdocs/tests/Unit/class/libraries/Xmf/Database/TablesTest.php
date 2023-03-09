<?php

declare(strict_types=1);

namespace Xmf\Test\Database;

use Xmf\Database\Tables;

use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 4) . '/init_new.php';

require_once(XOOPS_TU_ROOT_PATH . '/class/logger/xoopslogger.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/xoopsload.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/preload.php');
require_once(XOOPS_TU_ROOT_PATH . '/class/database/databasefactory.php');

//$xoops       = \Xoops::getInstance();
//$xoopsLogger = $xoops->logger();
//$xoops->events();
//$psr4loader = new \Xoops\Core\Psr4ClassLoader();
//$psr4loader->register();
//$xoops->events()
//      ->triggerEvent('core.include.common.psr4loader', $psr4loader);
//$xoops->events()
//      ->triggerEvent('core.include.common.classmaps');

class TablesTest extends TestCase
{
    /**
     * @var Tables
     */
    protected $object;
    /**
     * @var string
     */
    protected $prefix;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->object = new Tables();
        $this->prefix = \XoopsDatabaseFactory::getDatabaseConnection()->prefix();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    protected function prefix($table)
    {
        return $this->prefix . '_' . $table;
    }

    public function testName()
    {
        $tableName = 'users';
        $actual    = $this->object->useTable($tableName);
        $this->assertTrue($actual);

        $tables = $this->object->dumpTables();
        $this->assertArrayHasKey($tableName, $tables);
        $this->assertEquals($this->prefix($tableName), $tables[$tableName]['name']);
    }

    public function testAddColumn()
    {
        $tableName = 'users';
        $actual    = $this->object->useTable($tableName);
        $this->assertTrue($actual);

        $columnName = 'not_real_column';
        $columnAttr = 'int NOT NULL';

        $this->object->addColumn($tableName, $columnName, $columnAttr);
        $queue    = $this->object->dumpQueue();
        $expected = "ALTER TABLE `{$this->prefix}_{$tableName}` ADD COLUMN `{$columnName}` {$columnAttr}";
        $this->assertEquals($expected, $queue[0]);
    }

    public function testAddPrimaryKey()
    {
        $tableName = 'users';
        $actual    = $this->object->useTable($tableName);
        $this->assertTrue($actual);

        $columnName = 'uid';

        $this->object->addPrimaryKey($tableName, $columnName);
        $queue = $this->object->dumpQueue();
        //var_dump($queue);
        $expected = "ALTER TABLE `{$this->prefix}_{$tableName}` ADD PRIMARY KEY(`{$columnName}`)";
        $this->assertEquals($expected, $queue[0]);
    }

    public function testAddTable()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testUseTable()
    {
        $actual = $this->object->useTable('users');
        $this->assertTrue($actual);

        $actual = $this->object->useTable('system_nosuch_table');
        $this->assertFalse($actual);
    }

    public function testGetColumnAttributes()
    {
        $tableName = 'users';
        $actual    = $this->object->useTable($tableName);
        $this->assertTrue($actual);

        $columnName = 'uid';

        $actual = $this->object->getColumnAttributes($tableName, $columnName);

        $this->assertNotFalse(stristr($actual, 'mediumint(8)'));
        $this->assertNotFalse(stristr($actual, 'unsigned'));
        $this->assertNotFalse(stristr($actual, 'NOT NULL'));
        $this->assertNotFalse(stristr($actual, 'auto_increment'));
    }

    public function testGetTableIndexes()
    {
        $tableName = 'users';
        $this->object->useTable($tableName);
        $actual = $this->object->getTableIndexes($tableName);
        $this->assertIsArray($actual);
        $this->assertArrayHasKey('PRIMARY', $actual);

        $actual = $this->object->getTableIndexes('system_bogus_table_name');
        $this->assertFalse($actual);
    }

    public function testAlterColumn()
    {
        $tableName = 'users';
        $actual    = $this->object->useTable($tableName);
        $this->assertTrue($actual);

        $columnName    = 'pass';
        $newColumnName = 'password';
        $attributes    = 'varchar(255) NOT NULL DEFAULT \'\'';

        $this->object->alterColumn($tableName, $columnName, $attributes, $newColumnName);
        $queue = $this->object->dumpQueue();
        //var_dump($queue);
        $expected = "ALTER TABLE `{$this->prefix}_{$tableName}` CHANGE COLUMN `{$columnName}` `{$newColumnName}` {$attributes} ";
        $this->assertEquals($expected, $queue[0]);
    }

    public function testCopyTable()
    {
        $tableName = 'users';
        $actual    = $this->object->useTable($tableName);
        $this->assertTrue($actual);

        $copyName = 'notsystem_user';
        $actual   = $this->object->useTable($copyName);
        $this->assertFalse($actual);

        $actual = $this->object->copyTable($tableName, $copyName, false);
        $this->assertTrue($actual);

        $tables = $this->object->dumpTables();
        $this->assertEquals($tables[$tableName]['columns'], $tables[$copyName]['columns']);
        $this->assertEquals($tables[$tableName]['keys'], $tables[$copyName]['keys']);
    }

    public function testCreateIndex()
    {
        $tableName = 'users';
        $actual    = $this->object->useTable($tableName);
        $this->assertTrue($actual);

        $columnName = 'actkey';
        $indexName  = 'user_actkey';

        $this->object->addIndex($indexName, $tableName, $columnName);
        $queue = $this->object->dumpQueue();

        $expected = "ALTER TABLE `{$this->prefix}_{$tableName}` ADD INDEX `{$indexName}` (`{$columnName}`)";
        $this->assertEquals($expected, $queue[0]);
    }

    public function testDropColumn()
    {
        $tableName = 'users';
        $actual    = $this->object->useTable($tableName);
        $this->assertTrue($actual);

        $columnName = 'actkey';

        $this->object->dropColumn($tableName, $columnName);
        $queue = $this->object->dumpQueue();

        $expected = "ALTER TABLE `{$this->prefix}_{$tableName}` DROP COLUMN `{$columnName}`";
        $this->assertEquals($expected, $queue[0]);
    }

    public function testDropIndex()
    {
        $tableName = 'users';
        $actual    = $this->object->useTable($tableName);
        $this->assertTrue($actual);

        $indexName = 'blahblah';

        $this->object->dropIndex($indexName, $tableName);
        $queue = $this->object->dumpQueue();

        $expected = "ALTER TABLE `{$this->prefix}_{$tableName}` DROP INDEX `{$indexName}`";
        $this->assertEquals($expected, $queue[0]);
    }

    public function testDropIndexes()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testDropPrimaryKey()
    {
        $tableName = 'users';
        $actual    = $this->object->useTable($tableName);
        $this->assertTrue($actual);

        $this->object->dropPrimaryKey($tableName);
        $queue = $this->object->dumpQueue();

        $expected = "ALTER TABLE `{$this->prefix}_{$tableName}` DROP PRIMARY KEY ";
        $this->assertEquals($expected, $queue[0]);
    }

    public function testDropTable()
    {
        $tableName = 'users';
        $actual    = $this->object->useTable($tableName);
        $this->assertTrue($actual);

        $this->object->dropTable($tableName);
        $queue = $this->object->dumpQueue();

        $expected = "DROP TABLE `{$this->prefix}_{$tableName}` ";
        $this->assertEquals($expected, $queue[0]);
    }

    public function testRenameTable()
    {
        $tableName = 'users';
        $actual    = $this->object->useTable($tableName);
        $this->assertTrue($actual);

        $newName = 'system_abuser';
        $this->object->renameTable($tableName, $newName);
        $queue = $this->object->dumpQueue();

        $expected = "ALTER TABLE `{$this->prefix}_{$tableName}` RENAME TO `{$this->prefix}_{$newName}`";
        $this->assertEquals($expected, $queue[0]);
    }

    public function testSetTableOptions()
    {
        $tableName = 'users';
        $actual    = $this->object->useTable($tableName);
        $this->assertTrue($actual);

        $options = 'ENGINE=MEMORY DEFAULT CHARSET=utf8;';
        $this->object->setTableOptions($tableName, $options);
        $queue = $this->object->dumpQueue();

        $expected = "ALTER TABLE `{$this->prefix}_{$tableName}` {$options} ";
        $this->assertEquals($expected, $queue[0]);
    }

    public function testExecuteQueue()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testDelete()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testInsert()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testUpdate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testTruncate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testRenderTableCreate()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testGetLastError()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testGetLastErrNo()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testDumpTables()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testAddToQueue()
    {
        $this->object->resetQueue();
        $queue = $this->object->dumpQueue();
        $this->assertIsArray($queue);
        $this->assertEmpty($queue);

        $expected = 'SELECT * FROM TEST.DUMMY';
        $this->object->addToQueue($expected);

        $queue = $this->object->dumpQueue();
        $this->assertIsArray($queue);
        $this->assertSame(1, count($queue));
        $this->assertEquals($expected, reset($queue));

        $this->object->resetQueue();
        $queue = $this->object->dumpQueue();
        $this->assertIsArray($queue);
        $this->assertEmpty($queue);
    }
}
