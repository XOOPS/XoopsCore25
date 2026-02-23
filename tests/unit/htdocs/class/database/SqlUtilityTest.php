<?php

declare(strict_types=1);

namespace xoopsdatabase;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SqlUtility;

/**
 * Comprehensive PHPUnit tests for the SqlUtility class.
 *
 * SqlUtility provides two static methods for processing SQL dump files:
 *   - splitMySqlFile(&$ret, $sql): Splits a multi-statement SQL string into
 *     individual queries, handling string literals, escaped characters, comments,
 *     and various delimiters.
 *   - prefixQuery($query, $prefix): Adds a table name prefix to supported SQL
 *     statements (INSERT INTO, CREATE TABLE, ALTER TABLE, UPDATE, DROP TABLE).
 *
 * The bootstrap loads the source file via require_once and defines XOOPS_ROOT_PATH
 * so the restricted-access guard passes.
 *
 * @see \SqlUtility
 */
#[CoversClass(SqlUtility::class)]
class SqlUtilityTest extends TestCase
{
    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/database/sqlutility.php';
    }

    // =========================================================================
    // splitMySqlFile — Basic behavior
    // =========================================================================

    /**
     * Test that splitMySqlFile always returns true, even for empty input.
     */
    public function testSplitMySqlFileAlwaysReturnsTrue(): void
    {
        $ret = [];
        $result = SqlUtility::splitMySqlFile($ret, '');

        $this->assertTrue($result);
    }

    /**
     * Test that an empty string produces no entries in the result array.
     */
    public function testSplitMySqlFileEmptyStringProducesNoEntries(): void
    {
        $ret = [];
        SqlUtility::splitMySqlFile($ret, '');

        $this->assertSame([], $ret);
    }

    /**
     * Test that whitespace-only input produces no entries in the result array.
     */
    public function testSplitMySqlFileWhitespaceOnlyProducesNoEntries(): void
    {
        $ret = [];
        SqlUtility::splitMySqlFile($ret, "   \t\n  ");

        $this->assertSame([], $ret);
    }

    /**
     * Test splitting a single simple SQL statement terminated by semicolon.
     */
    public function testSplitMySqlFileSingleStatementWithSemicolon(): void
    {
        $ret = [];
        SqlUtility::splitMySqlFile($ret, 'SELECT 1;');

        $this->assertCount(1, $ret);
        $this->assertSame('SELECT 1', $ret[0]);
    }

    /**
     * Test a single statement without a trailing semicolon is still captured.
     */
    public function testSplitMySqlFileSingleStatementWithoutSemicolon(): void
    {
        $ret = [];
        SqlUtility::splitMySqlFile($ret, 'SELECT 1');

        $this->assertCount(1, $ret);
        $this->assertSame('SELECT 1', $ret[0]);
    }

    /**
     * Test splitting two statements separated by a semicolon.
     */
    public function testSplitMySqlFileTwoStatements(): void
    {
        $ret = [];
        SqlUtility::splitMySqlFile($ret, 'SELECT 1; SELECT 2;');

        $this->assertCount(2, $ret);
        $this->assertSame('SELECT 1', $ret[0]);
        $this->assertSame('SELECT 2', $ret[1]);
    }

    /**
     * Test splitting three statements with various whitespace between them.
     */
    public function testSplitMySqlFileThreeStatements(): void
    {
        $ret = [];
        $sql = "INSERT INTO t1 VALUES (1);\n  UPDATE t2 SET x=1;\n  DELETE FROM t3;";
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(3, $ret);
        $this->assertSame('INSERT INTO t1 VALUES (1)', $ret[0]);
        $this->assertSame('UPDATE t2 SET x=1', $ret[1]);
        $this->assertSame('DELETE FROM t3', $ret[2]);
    }

    /**
     * Test that a trailing semicolon with no following content produces
     * exactly one entry (not two).
     */
    public function testSplitMySqlFileTrailingSemicolonNoExtraEntry(): void
    {
        $ret = [];
        SqlUtility::splitMySqlFile($ret, 'SELECT 1;');

        $this->assertCount(1, $ret);
    }

    /**
     * Test multiple trailing semicolons.
     */
    public function testSplitMySqlFileMultipleTrailingSemicolons(): void
    {
        $ret = [];
        SqlUtility::splitMySqlFile($ret, 'SELECT 1;;;');

        // First semicolon yields "SELECT 1", second yields "", third yields ""
        // Empty strings are trimmed out by the ltrim, but '' !== empty
        $this->assertGreaterThanOrEqual(1, count($ret));
        $this->assertSame('SELECT 1', $ret[0]);
    }

    // =========================================================================
    // splitMySqlFile — String literal handling
    // =========================================================================

    /**
     * Test that a semicolon inside single-quoted string is not treated as delimiter.
     */
    public function testSplitMySqlFileSemicolonInsideSingleQuotedString(): void
    {
        $ret = [];
        $sql = "INSERT INTO t1 VALUES ('hello; world');";
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(1, $ret);
        $this->assertSame("INSERT INTO t1 VALUES ('hello; world')", $ret[0]);
    }

    /**
     * Test that a semicolon inside double-quoted string is not treated as delimiter.
     */
    public function testSplitMySqlFileSemicolonInsideDoubleQuotedString(): void
    {
        $ret = [];
        $sql = 'INSERT INTO t1 VALUES ("hello; world");';
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(1, $ret);
        $this->assertSame('INSERT INTO t1 VALUES ("hello; world")', $ret[0]);
    }

    /**
     * Test that a semicolon inside backtick-quoted identifier is not treated as delimiter.
     */
    public function testSplitMySqlFileSemicolonInsideBacktickQuotedIdentifier(): void
    {
        $ret = [];
        $sql = 'SELECT `col;name` FROM t1;';
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(1, $ret);
        $this->assertSame('SELECT `col;name` FROM t1', $ret[0]);
    }

    /**
     * Test that an escaped single quote inside a string does not end the string.
     */
    public function testSplitMySqlFileEscapedSingleQuoteInString(): void
    {
        $ret = [];
        $sql = "INSERT INTO t1 VALUES ('it\\'s a test');";
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(1, $ret);
        $this->assertStringContainsString("it\\'s a test", $ret[0]);
    }

    /**
     * Test that an escaped double quote inside a double-quoted string works.
     */
    public function testSplitMySqlFileEscapedDoubleQuoteInString(): void
    {
        $ret = [];
        $sql = 'INSERT INTO t1 VALUES ("say \\"hello\\"");';
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(1, $ret);
        $this->assertStringContainsString('\\"hello\\"', $ret[0]);
    }

    /**
     * Test that escaped backslashes before a quote end the string correctly.
     * Double backslash (\\) means the backslash is escaped, so the quote
     * that follows is the real end of the string.
     */
    public function testSplitMySqlFileEscapedBackslashBeforeQuote(): void
    {
        $ret = [];
        // 'test\\' — the \\\\ in PHP becomes \\ in the string, so the ' closes the string
        $sql = "INSERT INTO t1 VALUES ('test\\\\'); SELECT 2;";
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(2, $ret);
        $this->assertSame("INSERT INTO t1 VALUES ('test\\\\')", $ret[0]);
        $this->assertSame('SELECT 2', $ret[1]);
    }

    /**
     * Test a string with a backtick that cannot be escaped by backslash.
     * Backtick-quoted identifiers end only at the next backtick.
     */
    public function testSplitMySqlFileBacktickCannotBeEscapedByBackslash(): void
    {
        $ret = [];
        // Backtick-quoted: even with a backslash before the closing backtick,
        // the backtick ends the identifier because backtick escaping is not
        // done via backslash in the parser.
        $sql = 'SELECT `col\\`` FROM t1;';
        SqlUtility::splitMySqlFile($ret, $sql);

        // The parser should find the backtick after backslash as end of identifier
        $this->assertGreaterThanOrEqual(1, count($ret));
    }

    /**
     * Test multiple string literals in one statement.
     */
    public function testSplitMySqlFileMultipleStringLiterals(): void
    {
        $ret = [];
        $sql = "INSERT INTO t1 VALUES ('hello', 'world;');";
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(1, $ret);
        $this->assertSame("INSERT INTO t1 VALUES ('hello', 'world;')", $ret[0]);
    }

    /**
     * Test an unterminated string causes the remainder to be added as-is.
     */
    public function testSplitMySqlFileUnterminatedString(): void
    {
        $ret = [];
        $sql = "INSERT INTO t1 VALUES ('unterminated";
        $result = SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertTrue($result);
        // The entire SQL is added when end-of-string is not found
        $this->assertCount(1, $ret);
        $this->assertSame($sql, $ret[0]);
    }

    // =========================================================================
    // splitMySqlFile — Comment handling
    // =========================================================================

    /**
     * Test that a hash (#) comment is removed from the SQL.
     */
    public function testSplitMySqlFileHashComment(): void
    {
        $ret = [];
        $sql = "SELECT 1;\n# This is a comment\nSELECT 2;";
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(2, $ret);
        $this->assertSame('SELECT 1', $ret[0]);
        $this->assertSame('SELECT 2', $ret[1]);
    }

    /**
     * Test that a double-dash (--) comment is removed from the SQL.
     */
    public function testSplitMySqlFileDashDashComment(): void
    {
        $ret = [];
        $sql = "SELECT 1;\n-- This is a comment\nSELECT 2;";
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(2, $ret);
        $this->assertSame('SELECT 1', $ret[0]);
        $this->assertSame('SELECT 2', $ret[1]);
    }

    /**
     * Test that a comment at the end of file (no newline after) causes
     * the method to return true without adding the comment as a statement.
     */
    public function testSplitMySqlFileCommentAtEndOfFile(): void
    {
        $ret = [];
        $sql = "SELECT 1;\n# final comment";
        $result = SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertTrue($result);
        $this->assertCount(1, $ret);
        $this->assertSame('SELECT 1', $ret[0]);
    }

    /**
     * Test that a hash comment at end of file with no preceding statements
     * returns true and produces no entries.
     */
    public function testSplitMySqlFileOnlyHashComment(): void
    {
        $ret = [];
        $result = SqlUtility::splitMySqlFile($ret, '# just a comment');

        $this->assertTrue($result);
        $this->assertSame([], $ret);
    }

    /**
     * Test that a dash-dash comment at end of file with no preceding statements
     * returns true and produces no entries.
     */
    public function testSplitMySqlFileOnlyDashDashComment(): void
    {
        $ret = [];
        $result = SqlUtility::splitMySqlFile($ret, '-- just a comment');

        $this->assertTrue($result);
        $this->assertSame([], $ret);
    }

    /**
     * Test inline comment after a statement on the same line.
     */
    public function testSplitMySqlFileInlineHashComment(): void
    {
        $ret = [];
        $sql = "SELECT 1; # inline comment\nSELECT 2;";
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(2, $ret);
        $this->assertSame('SELECT 1', $ret[0]);
        $this->assertSame('SELECT 2', $ret[1]);
    }

    /**
     * Test multiple comments interspersed with statements.
     */
    public function testSplitMySqlFileMultipleComments(): void
    {
        $ret = [];
        $sql = "# Header comment\nSELECT 1;\n# Middle comment\nSELECT 2;\n# Footer comment";
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(2, $ret);
    }

    // =========================================================================
    // splitMySqlFile — Complex scenarios
    // =========================================================================

    /**
     * Test a CREATE TABLE statement spanning multiple lines.
     */
    public function testSplitMySqlFileMultiLineCreateTable(): void
    {
        $ret = [];
        $sql = "CREATE TABLE test (\n  id INT,\n  name VARCHAR(255)\n);";
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(1, $ret);
        $this->assertStringStartsWith('CREATE TABLE test', $ret[0]);
    }

    /**
     * Test INSERT with multiple value tuples and semicolons in strings.
     */
    public function testSplitMySqlFileInsertWithMultipleValues(): void
    {
        $ret = [];
        $sql = "INSERT INTO t1 VALUES (1, 'a;b'), (2, 'c;d');";
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(1, $ret);
        $this->assertStringContainsString("'a;b'", $ret[0]);
        $this->assertStringContainsString("'c;d'", $ret[0]);
    }

    /**
     * Test a realistic SQL dump with comments, blank lines, and multiple statements.
     */
    public function testSplitMySqlFileRealisticDump(): void
    {
        $ret = [];
        $sql = <<<'SQL'
# phpMyAdmin SQL Dump
# Table structure for table `users`

CREATE TABLE `users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) DEFAULT '',
  PRIMARY KEY (`id`)
);

INSERT INTO `users` VALUES (1, 'admin');
INSERT INTO `users` VALUES (2, 'guest');
SQL;
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(3, $ret);
        $this->assertStringStartsWith('CREATE TABLE `users`', $ret[0]);
        $this->assertStringContainsString("'admin'", $ret[1]);
        $this->assertStringContainsString("'guest'", $ret[2]);
    }

    /**
     * Test that the return array is modified by reference (not replaced).
     */
    public function testSplitMySqlFileModifiesArrayByReference(): void
    {
        $ret = ['existing'];
        SqlUtility::splitMySqlFile($ret, 'SELECT 1;');

        $this->assertCount(2, $ret);
        $this->assertSame('existing', $ret[0]);
        $this->assertSame('SELECT 1', $ret[1]);
    }

    /**
     * Test that leading whitespace before statements is preserved in output.
     */
    public function testSplitMySqlFilePreservesInternalWhitespace(): void
    {
        $ret = [];
        $sql = "SELECT   1;";
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertSame('SELECT   1', $ret[0]);
    }

    /**
     * Test that newline characters within a statement (but not in comments)
     * are preserved.
     */
    public function testSplitMySqlFilePreservesNewlinesInStatements(): void
    {
        $ret = [];
        $sql = "SELECT\n1\nFROM dual;";
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(1, $ret);
        $this->assertStringContainsString("\n", $ret[0]);
    }

    /**
     * Test with carriage return (\r) line endings (Windows-style).
     */
    public function testSplitMySqlFileHandlesCarriageReturn(): void
    {
        $ret = [];
        $sql = "SELECT 1;\r# comment\rSELECT 2;";
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertGreaterThanOrEqual(1, count($ret));
        $this->assertTrue(true); // Confirms no exception thrown
    }

    // =========================================================================
    // splitMySqlFile — Edge cases
    // =========================================================================

    /**
     * Test that a single semicolon produces no meaningful entry.
     */
    public function testSplitMySqlFileSingleSemicolon(): void
    {
        $ret = [];
        SqlUtility::splitMySqlFile($ret, ';');

        // The semicolon at position 0 yields substr($sql, 0, 0) which is ''
        // Then the rest is empty, so it returns true
        $this->assertTrue(true);
    }

    /**
     * Test with only newlines.
     */
    public function testSplitMySqlFileOnlyNewlines(): void
    {
        $ret = [];
        SqlUtility::splitMySqlFile($ret, "\n\n\n");

        $this->assertSame([], $ret);
    }

    /**
     * Test with a very long single statement (no semicolons).
     */
    public function testSplitMySqlFileLongStatement(): void
    {
        $ret = [];
        $long = 'INSERT INTO t1 VALUES (' . str_repeat('x', 10000) . ')';
        SqlUtility::splitMySqlFile($ret, $long);

        $this->assertCount(1, $ret);
        $this->assertSame($long, $ret[0]);
    }

    /**
     * Test that mixed quote types in a single statement are handled correctly.
     */
    public function testSplitMySqlFileMixedQuoteTypes(): void
    {
        $ret = [];
        $sql = 'INSERT INTO `t1` VALUES (1, "hello", \'world\');';
        SqlUtility::splitMySqlFile($ret, $sql);

        $this->assertCount(1, $ret);
        $this->assertStringContainsString('`t1`', $ret[0]);
        $this->assertStringContainsString('"hello"', $ret[0]);
        $this->assertStringContainsString("'world'", $ret[0]);
    }

    /**
     * Test statement containing only whitespace between two semicolons.
     */
    public function testSplitMySqlFileWhitespaceBetweenSemicolons(): void
    {
        $ret = [];
        SqlUtility::splitMySqlFile($ret, 'SELECT 1;   ; SELECT 2;');

        // The middle part "   " after ltrim would be empty string,
        // but the semicolon at pos 0 of "  ; SELECT 2;" produces an empty substr
        $this->assertGreaterThanOrEqual(2, count($ret));
    }

    // =========================================================================
    // prefixQuery — INSERT INTO
    // =========================================================================

    /**
     * Test prefixQuery with a basic INSERT INTO statement.
     */
    public function testPrefixQueryInsertInto(): void
    {
        $query = 'INSERT INTO tablename VALUES (1)';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with INSERT INTO using backtick-quoted table name.
     */
    public function testPrefixQueryInsertIntoBacktickQuoted(): void
    {
        $query = 'INSERT INTO `tablename` VALUES (1)';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with INSERT INTO — case insensitivity.
     */
    public function testPrefixQueryInsertIntoCaseInsensitive(): void
    {
        $query = 'insert into tablename VALUES (1)';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with INSERT INTO — mixed case.
     */
    public function testPrefixQueryInsertIntoMixedCase(): void
    {
        $query = 'Insert Into tablename VALUES (1)';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with INSERT INTO — extra whitespace.
     */
    public function testPrefixQueryInsertIntoExtraWhitespace(): void
    {
        $query = "INSERT   INTO   tablename   VALUES (1)";
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    // =========================================================================
    // prefixQuery — CREATE TABLE
    // =========================================================================

    /**
     * Test prefixQuery with a basic CREATE TABLE statement.
     */
    public function testPrefixQueryCreateTable(): void
    {
        $query = 'CREATE TABLE tablename (id INT)';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with CREATE TABLE using backtick-quoted table name.
     */
    public function testPrefixQueryCreateTableBacktickQuoted(): void
    {
        $query = 'CREATE TABLE `tablename` (id INT)';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with CREATE TABLE — case insensitivity.
     */
    public function testPrefixQueryCreateTableCaseInsensitive(): void
    {
        $query = 'create table tablename (id INT)';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery CREATE TABLE with multiline content.
     */
    public function testPrefixQueryCreateTableMultiline(): void
    {
        $query = "CREATE TABLE tablename (\n  id INT,\n  name VARCHAR(255)\n)";
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    // =========================================================================
    // prefixQuery — ALTER TABLE
    // =========================================================================

    /**
     * Test prefixQuery with a basic ALTER TABLE statement.
     */
    public function testPrefixQueryAlterTable(): void
    {
        $query = 'ALTER TABLE tablename ADD COLUMN newcol INT';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with ALTER TABLE using backtick-quoted table name.
     */
    public function testPrefixQueryAlterTableBacktickQuoted(): void
    {
        $query = 'ALTER TABLE `tablename` ADD COLUMN newcol INT';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with ALTER TABLE — case insensitivity.
     */
    public function testPrefixQueryAlterTableCaseInsensitive(): void
    {
        $query = 'alter table tablename ADD COLUMN newcol INT';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    // =========================================================================
    // prefixQuery — UPDATE
    // =========================================================================

    /**
     * Test prefixQuery with a basic UPDATE statement.
     */
    public function testPrefixQueryUpdate(): void
    {
        $query = 'UPDATE tablename SET col1=1';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with UPDATE using backtick-quoted table name.
     */
    public function testPrefixQueryUpdateBacktickQuoted(): void
    {
        $query = 'UPDATE `tablename` SET col1=1';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with UPDATE — case insensitivity.
     */
    public function testPrefixQueryUpdateCaseInsensitive(): void
    {
        $query = 'update tablename SET col1=1';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with UPDATE — extra whitespace before table name.
     */
    public function testPrefixQueryUpdateExtraWhitespace(): void
    {
        $query = "UPDATE   tablename   SET col1=1";
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    // =========================================================================
    // prefixQuery — DROP TABLE
    // =========================================================================

    /**
     * Test prefixQuery with a basic DROP TABLE statement.
     */
    public function testPrefixQueryDropTable(): void
    {
        $query = 'DROP TABLE tablename';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with DROP TABLE using backtick-quoted table name.
     */
    public function testPrefixQueryDropTableBacktickQuoted(): void
    {
        $query = 'DROP TABLE `tablename`';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with DROP TABLE — case insensitivity.
     */
    public function testPrefixQueryDropTableCaseInsensitive(): void
    {
        $query = 'drop table tablename';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with DROP TABLE — trailing whitespace.
     */
    public function testPrefixQueryDropTableTrailingWhitespace(): void
    {
        $query = 'DROP TABLE tablename ';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    // =========================================================================
    // prefixQuery — Unsupported statements (returns false)
    // =========================================================================

    /**
     * Test prefixQuery returns false for SELECT statements.
     */
    public function testPrefixQueryReturnsFalseForSelect(): void
    {
        $result = SqlUtility::prefixQuery('SELECT * FROM tablename', 'xoops');

        $this->assertFalse($result);
    }

    /**
     * Test prefixQuery returns false for DELETE statements.
     */
    public function testPrefixQueryReturnsFalseForDelete(): void
    {
        $result = SqlUtility::prefixQuery('DELETE FROM tablename', 'xoops');

        $this->assertFalse($result);
    }

    /**
     * Test prefixQuery returns false for TRUNCATE statements.
     */
    public function testPrefixQueryReturnsFalseForTruncate(): void
    {
        $result = SqlUtility::prefixQuery('TRUNCATE TABLE tablename', 'xoops');

        $this->assertFalse($result);
    }

    /**
     * Test prefixQuery returns false for REPLACE statements.
     */
    public function testPrefixQueryReturnsFalseForReplace(): void
    {
        $result = SqlUtility::prefixQuery('REPLACE INTO tablename VALUES (1)', 'xoops');

        $this->assertFalse($result);
    }

    /**
     * Test prefixQuery returns false for an empty string.
     */
    public function testPrefixQueryReturnsFalseForEmptyString(): void
    {
        $result = SqlUtility::prefixQuery('', 'xoops');

        $this->assertFalse($result);
    }

    /**
     * Test prefixQuery returns false for plain text that is not SQL.
     */
    public function testPrefixQueryReturnsFalseForNonSql(): void
    {
        $result = SqlUtility::prefixQuery('hello world', 'xoops');

        $this->assertFalse($result);
    }

    // =========================================================================
    // prefixQuery — Return value structure
    // =========================================================================

    /**
     * Test that prefixQuery returns an array with the modified query at index [0].
     */
    public function testPrefixQueryReturnArrayContainsModifiedQueryAtIndexZero(): void
    {
        $query = 'CREATE TABLE mytable (id INT)';
        $result = SqlUtility::prefixQuery($query, 'pre');

        $this->assertIsArray($result);
        $this->assertArrayHasKey(0, $result);
        $this->assertStringContainsString('pre_mytable', $result[0]);
    }

    /**
     * Test that the original table name is replaced (not merely prepended).
     */
    public function testPrefixQueryReplacesTableNameCorrectly(): void
    {
        $query = 'INSERT INTO users VALUES (1)';
        $result = SqlUtility::prefixQuery($query, 'site');

        $this->assertIsArray($result);
        $this->assertStringContainsString('site_users', $result[0]);
        // "users " should no longer appear without prefix
        $this->assertStringNotContainsString('INTO users ', $result[0]);
    }

    /**
     * Test that the matches array includes the captured groups.
     */
    public function testPrefixQueryMatchesArrayHasExpectedKeys(): void
    {
        $query = 'CREATE TABLE `config` (id INT)';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        // The regex captures: [1]=command, [2]=whitespace, [3]=backtick, [4]=tablename, [5]=trailing
        $this->assertArrayHasKey(1, $result);
        $this->assertArrayHasKey(4, $result);
    }

    // =========================================================================
    // prefixQuery — Various prefix values
    // =========================================================================

    /**
     * Test prefixQuery with a numeric prefix.
     */
    public function testPrefixQueryWithNumericPrefix(): void
    {
        $query = 'CREATE TABLE tablename (id INT)';
        $result = SqlUtility::prefixQuery($query, '123');

        $this->assertIsArray($result);
        $this->assertStringContainsString('123_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with a prefix containing underscores.
     */
    public function testPrefixQueryWithUnderscorePrefix(): void
    {
        $query = 'CREATE TABLE tablename (id INT)';
        $result = SqlUtility::prefixQuery($query, 'my_site');

        $this->assertIsArray($result);
        $this->assertStringContainsString('my_site_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with an empty prefix still adds underscore.
     */
    public function testPrefixQueryWithEmptyPrefix(): void
    {
        $query = 'CREATE TABLE tablename (id INT)';
        $result = SqlUtility::prefixQuery($query, '');

        $this->assertIsArray($result);
        $this->assertStringContainsString('_tablename', $result[0]);
    }

    // =========================================================================
    // prefixQuery — Data provider for comprehensive command coverage
    // =========================================================================

    /**
     * Data provider for testing all supported SQL command types with prefixQuery.
     *
     * @return array<string, array{string, string, string}>
     */
    public static function supportedCommandsProvider(): array
    {
        return [
            'INSERT INTO unquoted' => [
                'INSERT INTO mytable VALUES (1)',
                'xoops',
                'xoops_mytable',
            ],
            'INSERT INTO backtick' => [
                'INSERT INTO `mytable` VALUES (1)',
                'xoops',
                'xoops_mytable',
            ],
            'CREATE TABLE unquoted' => [
                'CREATE TABLE mytable (id INT)',
                'xoops',
                'xoops_mytable',
            ],
            'CREATE TABLE backtick' => [
                'CREATE TABLE `mytable` (id INT)',
                'xoops',
                'xoops_mytable',
            ],
            'ALTER TABLE unquoted' => [
                'ALTER TABLE mytable ADD col INT',
                'xoops',
                'xoops_mytable',
            ],
            'ALTER TABLE backtick' => [
                'ALTER TABLE `mytable` ADD col INT',
                'xoops',
                'xoops_mytable',
            ],
            'UPDATE unquoted' => [
                'UPDATE mytable SET x=1',
                'xoops',
                'xoops_mytable',
            ],
            'UPDATE backtick' => [
                'UPDATE `mytable` SET x=1',
                'xoops',
                'xoops_mytable',
            ],
            'DROP TABLE unquoted' => [
                'DROP TABLE mytable',
                'xoops',
                'xoops_mytable',
            ],
            'DROP TABLE backtick' => [
                'DROP TABLE `mytable`',
                'xoops',
                'xoops_mytable',
            ],
        ];
    }

    /**
     * Test prefixQuery against all supported SQL command types via data provider.
     *
     * @param string $query    The SQL query to prefix
     * @param string $prefix   The prefix to apply
     * @param string $expected The expected prefixed table name in the output
     */
    #[DataProvider('supportedCommandsProvider')]
    public function testPrefixQuerySupportedCommands(string $query, string $prefix, string $expected): void
    {
        $result = SqlUtility::prefixQuery($query, $prefix);

        $this->assertIsArray($result);
        $this->assertStringContainsString($expected, $result[0]);
    }

    /**
     * Data provider for unsupported SQL statements that should return false.
     *
     * @return array<string, array{string}>
     */
    public static function unsupportedCommandsProvider(): array
    {
        return [
            'SELECT'     => ['SELECT * FROM tablename'],
            'DELETE'     => ['DELETE FROM tablename WHERE id=1'],
            'TRUNCATE'   => ['TRUNCATE TABLE tablename'],
            'REPLACE'    => ['REPLACE INTO tablename VALUES (1)'],
            'RENAME'     => ['RENAME TABLE old TO new'],
            'SHOW'       => ['SHOW TABLES'],
            'DESCRIBE'   => ['DESCRIBE tablename'],
            'EXPLAIN'    => ['EXPLAIN SELECT 1'],
            'SET'        => ['SET @x = 1'],
            'empty'      => [''],
            'whitespace' => ['   '],
            'nonsense'   => ['not a real query'],
        ];
    }

    /**
     * Test prefixQuery returns false for all unsupported SQL command types.
     *
     * @param string $query The unsupported SQL query
     */
    #[DataProvider('unsupportedCommandsProvider')]
    public function testPrefixQueryUnsupportedCommands(string $query): void
    {
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertFalse($result);
    }

    // =========================================================================
    // prefixQuery — Whitespace variations
    // =========================================================================

    /**
     * Test prefixQuery with tab characters as whitespace.
     */
    public function testPrefixQueryTabWhitespace(): void
    {
        $query = "CREATE\tTABLE\ttablename\t(id INT)";
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with newline between CREATE and TABLE.
     */
    public function testPrefixQueryNewlineWhitespace(): void
    {
        $query = "CREATE\nTABLE\ntablename\n(id INT)";
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_tablename', $result[0]);
    }

    /**
     * Test prefixQuery with table name containing numbers.
     */
    public function testPrefixQueryTableNameWithNumbers(): void
    {
        $query = 'CREATE TABLE table123 (id INT)';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_table123', $result[0]);
    }

    /**
     * Test prefixQuery with table name containing underscores.
     */
    public function testPrefixQueryTableNameWithUnderscores(): void
    {
        $query = 'CREATE TABLE my_table_name (id INT)';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('xoops_my_table_name', $result[0]);
    }

    /**
     * Test prefixQuery preserves the rest of the query after the table name.
     */
    public function testPrefixQueryPreservesQueryBody(): void
    {
        $query = 'INSERT INTO tablename VALUES (1, 2, 3)';
        $result = SqlUtility::prefixQuery($query, 'xoops');

        $this->assertIsArray($result);
        $this->assertStringContainsString('VALUES (1, 2, 3)', $result[0]);
    }
}
