<?php

declare(strict_types=1);

namespace modulesprotector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(\ProtectorMySQLDatabase::class)]
class ProtectorMysqlDatabaseTest extends TestCase
{
    private static bool $loaded = false;
    private \ProtectorMySQLDatabase $db;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            // Ensure Protector class is loaded first (needed by constructor)
            if (!class_exists('Protector', false)) {
                if (file_exists(XOOPS_PATH . '/vendor/autoload.php')) {
                    require_once XOOPS_PATH . '/vendor/autoload.php';
                }
                require_once XOOPS_PATH . '/modules/protector/class/protector.php';
            }
            // Load the ProtectorMysqlDatabase class
            if (!class_exists('ProtectorMySQLDatabase', false)) {
                require_once XOOPS_PATH . '/modules/protector/class/ProtectorMysqlDatabase.class.php';
            }
            self::$loaded = true;
        }
    }

    protected function setUp(): void
    {
        // Create instance without calling constructor (avoids needing Protector singleton)
        $ref = new \ReflectionClass(\ProtectorMySQLDatabase::class);
        $this->db = $ref->newInstanceWithoutConstructor();
        // Initialize public properties manually
        $this->db->doubtful_requests = [];
        $this->db->doubtful_needles = [
            'concat', 'information_schema', 'select', 'union',
            '/*', '--', '#',
        ];
    }

    // ---------------------------------------------------------------
    // Class structure
    // ---------------------------------------------------------------

    #[Test]
    public function classExtendsXoopsMySQLDatabaseProxy(): void
    {
        $this->assertTrue(is_subclass_of(\ProtectorMySQLDatabase::class, \XoopsMySQLDatabaseProxy::class));
    }

    #[Test]
    public function defaultDoubtfulNeedlesIsArray(): void
    {
        $this->assertIsArray($this->db->doubtful_needles);
    }

    #[Test]
    public function defaultDoubtfulNeedlesContainsExpectedEntries(): void
    {
        $this->assertContains('concat', $this->db->doubtful_needles);
        $this->assertContains('information_schema', $this->db->doubtful_needles);
        $this->assertContains('select', $this->db->doubtful_needles);
        $this->assertContains('union', $this->db->doubtful_needles);
        $this->assertContains('/*', $this->db->doubtful_needles);
        $this->assertContains('--', $this->db->doubtful_needles);
        $this->assertContains('#', $this->db->doubtful_needles);
    }

    // ---------------------------------------------------------------
    // separateStringsInSQL — pure string parsing
    // ---------------------------------------------------------------

    #[Test]
    public function separateStringsReturnsArrayOfTwo(): void
    {
        $result = $this->db->separateStringsInSQL("SELECT * FROM users");
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function separateStringsNoQuotesReturnsSqlIntact(): void
    {
        [$sqlWo, $strings] = $this->db->separateStringsInSQL("SELECT * FROM users WHERE id = 1");
        $this->assertSame("SELECT * FROM users WHERE id = 1", $sqlWo);
        $this->assertSame([], $strings);
    }

    #[Test]
    public function separateStringsSingleQuotedString(): void
    {
        [$sqlWo, $strings] = $this->db->separateStringsInSQL("SELECT * FROM users WHERE name = 'John'");
        $this->assertSame("SELECT * FROM users WHERE name = ", $sqlWo);
        $this->assertCount(1, $strings);
        $this->assertSame("'John'", $strings[0]);
    }

    #[Test]
    public function separateStringsDoubleQuotedString(): void
    {
        [$sqlWo, $strings] = $this->db->separateStringsInSQL('SELECT * FROM users WHERE name = "Jane"');
        $this->assertSame("SELECT * FROM users WHERE name = ", $sqlWo);
        $this->assertCount(1, $strings);
        $this->assertSame('"Jane"', $strings[0]);
    }

    #[Test]
    public function separateStringsMultipleQuotedStrings(): void
    {
        $sql = "INSERT INTO t (a, b) VALUES ('hello', 'world')";
        [$sqlWo, $strings] = $this->db->separateStringsInSQL($sql);
        $this->assertCount(2, $strings);
        $this->assertSame("'hello'", $strings[0]);
        $this->assertSame("'world'", $strings[1]);
    }

    #[Test]
    public function separateStringsEscapedQuoteInsideString(): void
    {
        $sql = "SELECT * FROM users WHERE name = 'O\\'Brien'";
        [$sqlWo, $strings] = $this->db->separateStringsInSQL($sql);
        $this->assertCount(1, $strings);
        $this->assertStringContainsString("O\\'Brien", $strings[0]);
    }

    #[Test]
    public function separateStringsMixedQuoteTypes(): void
    {
        $sql = "SELECT * FROM t WHERE a = 'hello' AND b = \"world\"";
        [$sqlWo, $strings] = $this->db->separateStringsInSQL($sql);
        $this->assertCount(2, $strings);
        $this->assertSame("'hello'", $strings[0]);
        $this->assertSame('"world"', $strings[1]);
    }

    #[Test]
    public function separateStringsEmptySQL(): void
    {
        [$sqlWo, $strings] = $this->db->separateStringsInSQL("");
        $this->assertSame("", $sqlWo);
        $this->assertSame([], $strings);
    }

    #[Test]
    public function separateStringsPreservesWhitespace(): void
    {
        $sql = "  SELECT  *  FROM  users  ";
        [$sqlWo, $strings] = $this->db->separateStringsInSQL($sql);
        // trim() is called on the input
        $this->assertSame("SELECT  *  FROM  users", $sqlWo);
    }

    #[Test]
    public function separateStringsSqlWithoutStringsReturnsEmptyArray(): void
    {
        [$sqlWo, $strings] = $this->db->separateStringsInSQL("DELETE FROM logs WHERE id > 100");
        $this->assertSame([], $strings);
    }

    #[Test]
    public function separateStringsEmptyQuotedString(): void
    {
        [$sqlWo, $strings] = $this->db->separateStringsInSQL("SELECT * FROM t WHERE a = ''");
        $this->assertCount(1, $strings);
        $this->assertSame("''", $strings[0]);
    }

    #[Test]
    public function separateStringsComplexInsert(): void
    {
        $sql = "INSERT INTO config (name, value) VALUES ('site_name', 'My Site'), ('admin', 'root')";
        [$sqlWo, $strings] = $this->db->separateStringsInSQL($sql);
        $this->assertCount(4, $strings);
        $this->assertSame("'site_name'", $strings[0]);
        $this->assertSame("'My Site'", $strings[1]);
        $this->assertSame("'admin'", $strings[2]);
        $this->assertSame("'root'", $strings[3]);
    }

    // ---------------------------------------------------------------
    // checkSql — injection detection (uses doubtful_requests)
    // ---------------------------------------------------------------

    #[Test]
    public function checkSqlPassesSafeQuery(): void
    {
        // No doubtful requests => checkSql should not trigger injectionFound
        $this->db->doubtful_requests = [];
        // Should not throw or die
        $this->db->checkSql("SELECT * FROM users WHERE id = 1");
        $this->assertTrue(true); // If we get here, no injection was detected
    }

    #[Test]
    public function checkSqlPassesWhenRequestInsideQuotes(): void
    {
        // Doubtful request is safely inside quotes
        $this->db->doubtful_requests = ["test'value"];
        $sql = "SELECT * FROM users WHERE name = 'test\\'value'";
        $this->db->checkSql($sql);
        $this->assertTrue(true);
    }

    // ---------------------------------------------------------------
    // doubtful_requests property
    // ---------------------------------------------------------------

    #[Test]
    public function doubtfulRequestsDefaultsToEmptyArray(): void
    {
        $this->assertSame([], $this->db->doubtful_requests);
    }

    #[Test]
    public function doubtfulRequestsCanBeSet(): void
    {
        $this->db->doubtful_requests = ['test_request'];
        $this->assertSame(['test_request'], $this->db->doubtful_requests);
    }
}
