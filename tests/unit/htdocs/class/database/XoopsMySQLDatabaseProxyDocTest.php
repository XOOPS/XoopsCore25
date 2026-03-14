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
 * Verifies that the class docblock contains all required XOOPS PHPDoc tags
 * including @category, @package, @author, @copyright, @license, and @link.
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
    public function proxyClassDocblockContainsCategoryTag(): void
    {
        $docblock = $this->extractProxyDocblock();
        $this->assertStringContainsString('@category', $docblock,
            'Proxy class docblock must include @category per XOOPS conventions');
    }

    #[Test]
    public function proxyClassDocblockContainsPackageTag(): void
    {
        $docblock = $this->extractProxyDocblock();
        $this->assertStringContainsString('@package', $docblock,
            'Proxy class docblock must include @package per XOOPS conventions');
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
