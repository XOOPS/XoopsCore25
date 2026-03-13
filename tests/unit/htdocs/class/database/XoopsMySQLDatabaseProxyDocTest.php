<?php

declare(strict_types=1);

namespace xoopsdatabase;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsMySQLDatabaseProxy;

/**
 * Tests for XoopsMySQLDatabaseProxy PHPDoc metadata (M-7).
 *
 * Verifies that the class docblock contains all required PSR-12 PHPDoc tags
 * and does not contain legacy tags removed under PSR-12 (@package, @subpackage, @category).
 */
#[CoversClass(XoopsMySQLDatabaseProxy::class)]
class XoopsMySQLDatabaseProxyDocTest extends TestCase
{
    private string $source;

    protected function setUp(): void
    {
        $this->source = file_get_contents(
            XOOPS_ROOT_PATH . '/class/database/mysqldatabase.php'
        );
    }

    #[Test]
    public function proxyClassDocblockContainsLicenseTag(): void
    {
        $docblock = $this->extractProxyDocblock();
        $this->assertStringContainsString('@license', $docblock);
    }

    #[Test]
    public function proxyClassDocblockContainsLinkTag(): void
    {
        $docblock = $this->extractProxyDocblock();
        $this->assertStringContainsString('@link', $docblock);
    }

    #[Test]
    public function proxyClassDocblockContainsAuthorTag(): void
    {
        $docblock = $this->extractProxyDocblock();
        $this->assertStringContainsString('@author', $docblock);
    }

    #[Test]
    public function proxyClassDocblockContainsCopyrightTag(): void
    {
        $docblock = $this->extractProxyDocblock();
        $this->assertStringContainsString('@copyright', $docblock);
    }

    #[Test]
    public function proxyClassDocblockDoesNotContainLegacyTags(): void
    {
        $docblock = $this->extractProxyDocblock();
        $this->assertStringNotContainsString('@package', $docblock, '@package is not used in PSR-12');
        $this->assertStringNotContainsString('@subpackage', $docblock, '@subpackage is not used in PSR-12');
        $this->assertStringNotContainsString('@category', $docblock, '@category is not used in PSR-12');
    }

    /**
     * Extract the docblock immediately preceding the XoopsMySQLDatabaseProxy class.
     */
    private function extractProxyDocblock(): string
    {
        // Find the class declaration
        $classPos = strpos($this->source, 'class XoopsMySQLDatabaseProxy');
        $this->assertNotFalse($classPos, 'XoopsMySQLDatabaseProxy class should exist');

        // Look backwards for the docblock
        $before = substr($this->source, 0, $classPos);
        $lastDocEnd = strrpos($before, '*/');
        $this->assertNotFalse($lastDocEnd, 'There should be a docblock before the class');

        $lastDocStart = strrpos(substr($before, 0, $lastDocEnd), '/**');
        $this->assertNotFalse($lastDocStart, 'Docblock should start with /**');

        return substr($before, $lastDocStart, $lastDocEnd - $lastDocStart + 2);
    }
}
