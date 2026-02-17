<?php

declare(strict_types=1);

namespace xoopsfile;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsFolderHandler;

#[CoversClass(XoopsFolderHandler::class)]
class XoopsFolderHandlerPathTest extends TestCase
{
    private XoopsFolderHandler $folder;

    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/file/xoopsfile.php';
        require_once XOOPS_ROOT_PATH . '/class/file/folder.php';
        // Use a known existing directory — normalize to forward slashes so
        // XoopsFolderHandler::isAbsolute() recognises Windows paths
        $path = str_replace('\\', '/', XOOPS_ROOT_PATH);
        $this->folder = new XoopsFolderHandler($path, false, false);
    }

    // ---------------------------------------------------------------
    // pwd() tests
    // ---------------------------------------------------------------

    #[Test]
    public function pwdReturnsCurrentPath(): void
    {
        $this->assertIsString($this->folder->pwd());
    }

    // ---------------------------------------------------------------
    // isWindowsPath tests
    // ---------------------------------------------------------------

    #[Test]
    public function isWindowsPathReturnsTrueForWindowsPath(): void
    {
        $this->assertTrue($this->folder->isWindowsPath('C:\\Users\\test'));
    }

    #[Test]
    public function isWindowsPathReturnsTrueForLowercaseDrive(): void
    {
        $this->assertTrue($this->folder->isWindowsPath('d:\\data'));
    }

    #[Test]
    public function isWindowsPathReturnsFalseForUnixPath(): void
    {
        $this->assertFalse($this->folder->isWindowsPath('/usr/local/bin'));
    }

    #[Test]
    public function isWindowsPathReturnsFalseForRelativePath(): void
    {
        $this->assertFalse($this->folder->isWindowsPath('relative/path'));
    }

    #[Test]
    public function isWindowsPathReturnsFalseForEmptyString(): void
    {
        $this->assertFalse($this->folder->isWindowsPath(''));
    }

    // ---------------------------------------------------------------
    // isAbsolute tests
    // ---------------------------------------------------------------

    #[Test]
    public function isAbsoluteReturnsTrueForUnixAbsolutePath(): void
    {
        $this->assertTrue($this->folder->isAbsolute('/usr/local'));
    }

    #[Test]
    public function isAbsoluteReturnsTrueForWindowsAbsolutePath(): void
    {
        $this->assertTrue($this->folder->isAbsolute('C:/Users'));
    }

    #[Test]
    public function isAbsoluteReturnsFalseForRelativePath(): void
    {
        $this->assertFalse($this->folder->isAbsolute('relative/path'));
    }

    #[Test]
    public function isAbsoluteReturnsFalseForEmptyString(): void
    {
        $result = $this->folder->isAbsolute('');
        $this->assertFalse((bool) $result);
    }

    // ---------------------------------------------------------------
    // isSlashTerm tests
    // ---------------------------------------------------------------

    #[Test]
    public function isSlashTermReturnsTrueForForwardSlashTerminated(): void
    {
        $this->assertTrue($this->folder->isSlashTerm('/path/'));
    }

    #[Test]
    public function isSlashTermReturnsTrueForBackslashTerminated(): void
    {
        $this->assertTrue($this->folder->isSlashTerm('C:\\path\\'));
    }

    #[Test]
    public function isSlashTermReturnsFalseForNonSlashTerminated(): void
    {
        $this->assertFalse($this->folder->isSlashTerm('/path'));
    }

    #[Test]
    public function isSlashTermReturnsFalseForEmptyString(): void
    {
        $this->assertFalse($this->folder->isSlashTerm(''));
    }

    // ---------------------------------------------------------------
    // slashTerm tests
    // ---------------------------------------------------------------

    #[Test]
    public function slashTermAddsSlashIfMissing(): void
    {
        $result = $this->folder->slashTerm('/path');
        $this->assertTrue($this->folder->isSlashTerm($result));
    }

    #[Test]
    public function slashTermDoesNotDoubleSlash(): void
    {
        $result = $this->folder->slashTerm('/path/');
        $this->assertSame('/path/', $result);
    }

    // ---------------------------------------------------------------
    // addPathElement tests
    // ---------------------------------------------------------------

    #[Test]
    public function addPathElementCombinesPathAndElement(): void
    {
        $result = $this->folder->addPathElement('/path', 'file.txt');
        $this->assertStringContainsString('file.txt', $result);
    }

    #[Test]
    public function addPathElementAddsSlashBetween(): void
    {
        $result = $this->folder->addPathElement('/path', 'subdir');
        $this->assertStringContainsString('/subdir', $result);
    }

    // ---------------------------------------------------------------
    // correctSlashFor tests
    // ---------------------------------------------------------------

    #[Test]
    public function correctSlashForWindowsReturnsBackslash(): void
    {
        $result = $this->folder->correctSlashFor('C:\\path');
        $this->assertSame('\\', $result);
    }

    #[Test]
    public function correctSlashForUnixReturnsForwardSlash(): void
    {
        $result = $this->folder->correctSlashFor('/unix/path');
        $this->assertSame('/', $result);
    }

    // ---------------------------------------------------------------
    // normalizePath tests
    // ---------------------------------------------------------------

    #[Test]
    public function normalizePathForWindowsReturnsBackslash(): void
    {
        $result = $this->folder->normalizePath('C:\\test');
        $this->assertSame('\\', $result);
    }

    #[Test]
    public function normalizePathForUnixReturnsForwardSlash(): void
    {
        $result = $this->folder->normalizePath('/test');
        $this->assertSame('/', $result);
    }

    // ---------------------------------------------------------------
    // realpath tests
    // ---------------------------------------------------------------

    #[Test]
    public function realpathReturnsAbsolutePathAsIs(): void
    {
        $result = $this->folder->realpath('/absolute/path');
        $this->assertSame('/absolute/path', $result);
    }

    #[Test]
    public function realpathResolvesDoubleDots(): void
    {
        $result = $this->folder->realpath('/a/b/../c');
        $this->assertSame('/a/c', $result);
    }

    #[Test]
    public function realpathReturnsFalseForTooManyDoubleDots(): void
    {
        $result = $this->folder->realpath('/../../impossible');
        // With an absolute path, going above root returns false
        $this->assertFalse($result);
    }

    #[Test]
    public function realpathPreservesTrailingSlash(): void
    {
        $result = $this->folder->realpath('/a/b/../c/');
        $this->assertStringEndsWith('/', $result);
    }

    // ---------------------------------------------------------------
    // cd tests
    // ---------------------------------------------------------------

    #[Test]
    public function cdToExistingDirectoryReturnsPath(): void
    {
        $path = str_replace('\\', '/', XOOPS_ROOT_PATH);
        $result = $this->folder->cd($path);
        $this->assertIsString($result);
    }

    #[Test]
    public function cdToNonExistentDirectoryReturnsFalse(): void
    {
        $result = $this->folder->cd('/this/does/not/exist/at/all');
        $this->assertFalse($result);
    }

    // ---------------------------------------------------------------
    // messages / errors tests
    // ---------------------------------------------------------------

    #[Test]
    public function messagesReturnsArray(): void
    {
        $this->assertIsArray($this->folder->messages());
    }

    #[Test]
    public function errorsReturnsArray(): void
    {
        $this->assertIsArray($this->folder->errors());
    }

    // ---------------------------------------------------------------
    // inPath tests
    // ---------------------------------------------------------------

    #[Test]
    public function inPathReturnsTrueWhenPathMatches(): void
    {
        $path = str_replace('\\', '/', XOOPS_ROOT_PATH);
        $this->folder->cd($path);
        $this->assertTrue($this->folder->inPath($path));
    }

    #[Test]
    public function inPathReturnsFalseWhenPathDoesNotMatch(): void
    {
        $this->folder->cd(XOOPS_ROOT_PATH);
        $this->assertFalse($this->folder->inPath('/completely/different/path'));
    }

    // ---------------------------------------------------------------
    // Property type safety
    // ---------------------------------------------------------------

    #[Test]
    public function pathPropertyIsString(): void
    {
        $this->assertIsString($this->folder->path);
    }

    #[Test]
    public function messagesPropertyIsArray(): void
    {
        $this->assertIsArray($this->folder->messages);
    }

    #[Test]
    public function errorsPropertyIsArray(): void
    {
        $this->assertIsArray($this->folder->errors);
    }
}
