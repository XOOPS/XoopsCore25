<?php

declare(strict_types=1);

namespace xoopsdatabase;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsDatabase;
use XoopsTestStubDatabase;

/**
 * Tests for XoopsDatabase (abstract base), XoopsMySQLDatabase (abstract),
 * XoopsMySQLDatabaseSafe, and XoopsMySQLDatabaseProxy using the
 * XoopsTestStubDatabase from bootstrap.
 */
#[CoversClass(XoopsDatabase::class)]
class XoopsDatabaseTest extends TestCase
{
    private XoopsTestStubDatabase $db;

    protected function setUp(): void
    {
        $this->db = new XoopsTestStubDatabase();
    }

    // ---------------------------------------------------------------
    // prefix() tests
    // ---------------------------------------------------------------

    #[Test]
    public function prefixReturnsOnlyPrefixWhenTableIsEmpty(): void
    {
        $this->db->setPrefix('xoops');
        $this->assertSame('xoops', $this->db->prefix());
    }

    #[Test]
    public function prefixReturnsPrefixUnderscoreTable(): void
    {
        $this->db->setPrefix('xoops');
        $this->assertSame('xoops_users', $this->db->prefix('users'));
    }

    #[Test]
    public function prefixWithEmptyTableReturnsBasePrefix(): void
    {
        // Stub always returns 'xoops' for empty table
        $this->assertSame('xoops', $this->db->prefix(''));
    }

    // ---------------------------------------------------------------
    // setPrefix() tests
    // ---------------------------------------------------------------

    #[Test]
    public function setPrefixDoesNotThrow(): void
    {
        // setPrefix is defined on the abstract base class; verify it's callable
        $this->db->setPrefix('site1');
        $this->assertTrue(true);
    }

    // ---------------------------------------------------------------
    // quote() tests
    // ---------------------------------------------------------------

    #[Test]
    public function quoteWrapsInSingleQuotes(): void
    {
        $result = $this->db->quote('hello');
        $this->assertStringStartsWith("'", $result);
        $this->assertStringEndsWith("'", $result);
    }

    #[Test]
    public function quoteEscapesSingleQuotes(): void
    {
        $result = $this->db->quote("O'Reilly");
        $this->assertStringContainsString("\\'", $result);
    }

    #[Test]
    public function quoteEmptyString(): void
    {
        $result = $this->db->quote('');
        $this->assertSame("''", $result);
    }

    #[Test]
    public function quoteWithBackslash(): void
    {
        $result = $this->db->quote('path\\to\\file');
        $this->assertStringContainsString('\\\\', $result);
    }

    // ---------------------------------------------------------------
    // escape() tests
    // ---------------------------------------------------------------

    #[Test]
    public function escapeDoesNotAddSurroundingQuotes(): void
    {
        $result = $this->db->escape('hello');
        $this->assertStringNotContainsString("'", $result);
    }

    #[Test]
    public function escapeEscapesSingleQuotes(): void
    {
        $result = $this->db->escape("it's");
        $this->assertStringContainsString("\\'", $result);
    }

    // ---------------------------------------------------------------
    // queryF / exec / query (stub behavior) tests
    // ---------------------------------------------------------------

    #[Test]
    public function queryReturnsFalse(): void
    {
        $this->assertFalse($this->db->query('SELECT 1'));
    }

    #[Test]
    public function queryFReturnsFalse(): void
    {
        $this->assertFalse($this->db->queryF('SELECT 1'));
    }

    #[Test]
    public function execReturnsFalse(): void
    {
        $this->assertFalse($this->db->exec('INSERT INTO test VALUES (1)'));
    }

    // ---------------------------------------------------------------
    // normalizeLimitStart tests (protected, test via reflection)
    // ---------------------------------------------------------------

    #[Test]
    public function normalizeLimitStartNullLimitReturnsNullNull(): void
    {
        $ref = new \ReflectionMethod($this->db, 'normalizeLimitStart');
        $ref->setAccessible(true);
        $result = $ref->invoke($this->db, null, null);
        $this->assertSame([null, null], $result);
    }

    #[Test]
    public function normalizeLimitStartWithLimitAndNullStartDefaultsToZero(): void
    {
        $ref = new \ReflectionMethod($this->db, 'normalizeLimitStart');
        $ref->setAccessible(true);
        $result = $ref->invoke($this->db, 10, null);
        $this->assertSame([10, 0], $result);
    }

    #[Test]
    public function normalizeLimitStartWithLimitAndStart(): void
    {
        $ref = new \ReflectionMethod($this->db, 'normalizeLimitStart');
        $ref->setAccessible(true);
        $result = $ref->invoke($this->db, 10, 5);
        $this->assertSame([10, 5], $result);
    }

    #[Test]
    public function normalizeLimitStartNegativeLimitBecomesZero(): void
    {
        $ref = new \ReflectionMethod($this->db, 'normalizeLimitStart');
        $ref->setAccessible(true);
        $result = $ref->invoke($this->db, -5, 0);
        $this->assertSame([0, 0], $result);
    }

    #[Test]
    public function normalizeLimitStartNegativeStartBecomesZero(): void
    {
        $ref = new \ReflectionMethod($this->db, 'normalizeLimitStart');
        $ref->setAccessible(true);
        $result = $ref->invoke($this->db, 10, -3);
        $this->assertSame([10, 0], $result);
    }

    #[Test]
    public function normalizeLimitStartZeroLimitIsAllowed(): void
    {
        $ref = new \ReflectionMethod($this->db, 'normalizeLimitStart');
        $ref->setAccessible(true);
        $result = $ref->invoke($this->db, 0, 0);
        $this->assertSame([0, 0], $result);
    }

    // ---------------------------------------------------------------
    // isResultSet tests
    // ---------------------------------------------------------------

    #[Test]
    public function isResultSetReturnsFalseForBoolean(): void
    {
        $this->assertFalse($this->db->isResultSet(false));
    }

    #[Test]
    public function isResultSetReturnsFalseForNull(): void
    {
        $this->assertFalse($this->db->isResultSet(null));
    }

    #[Test]
    public function isResultSetReturnsFalseForString(): void
    {
        $this->assertFalse($this->db->isResultSet('not a result'));
    }

    #[Test]
    public function isResultSetReturnsFalseForInteger(): void
    {
        $this->assertFalse($this->db->isResultSet(42));
    }

    // ---------------------------------------------------------------
    // getInsertId / genId tests
    // ---------------------------------------------------------------

    #[Test]
    public function getInsertIdReturnsZero(): void
    {
        $this->assertSame(0, $this->db->getInsertId());
    }

    #[Test]
    public function genIdReturnsZero(): void
    {
        $this->assertSame(0, $this->db->genId('seq'));
    }

    // ---------------------------------------------------------------
    // fetchArray / fetchRow / getRowsNum tests
    // ---------------------------------------------------------------

    #[Test]
    public function fetchArrayReturnsFalse(): void
    {
        $this->assertFalse($this->db->fetchArray(null));
    }

    #[Test]
    public function fetchRowReturnsFalse(): void
    {
        $this->assertFalse($this->db->fetchRow(null));
    }

    #[Test]
    public function getRowsNumReturnsZero(): void
    {
        $this->assertSame(0, $this->db->getRowsNum(null));
    }

    // ---------------------------------------------------------------
    // setLogger tests
    // ---------------------------------------------------------------

    #[Test]
    public function setLoggerAssignsLogger(): void
    {
        $logger = \XoopsLogger::getInstance();
        $this->db->setLogger($logger);
        $this->assertSame($logger, $this->db->logger);
    }

    // ---------------------------------------------------------------
    // allowWebChanges default
    // ---------------------------------------------------------------

    #[Test]
    public function allowWebChangesDefaultIsFalse(): void
    {
        $this->assertFalse($this->db->allowWebChanges);
    }

    // ---------------------------------------------------------------
    // Type safety tests
    // ---------------------------------------------------------------

    #[Test]
    public function prefixReturnTypeIsAlwaysString(): void
    {
        $this->db->setPrefix('x');
        $this->assertIsString($this->db->prefix());
        $this->assertIsString($this->db->prefix('t'));
    }

    #[Test]
    public function quoteReturnTypeIsString(): void
    {
        $this->assertIsString($this->db->quote('test'));
    }

    #[Test]
    public function escapeReturnTypeIsString(): void
    {
        $this->assertIsString($this->db->escape('test'));
    }
}
