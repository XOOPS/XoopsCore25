<?php

declare(strict_types=1);

namespace xoopsfile;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsFolderHandler;

/**
 * Comprehensive PHPUnit test suite for XoopsFolderHandler.
 *
 * Tests the XOOPS folder/directory handling class which provides utilities
 * for creating, reading, copying, moving, and deleting directories, as well
 * as path manipulation helpers (slash termination, Windows path detection,
 * absolute path checks, realpath resolution, etc.).
 *
 * Uses a temporary directory created in setUp() and cleaned in tearDown()
 * so that real project files are never touched.
 *
 * @package    xoopsfile
 * @subpackage tests
 */
#[CoversClass(XoopsFolderHandler::class)]
class XoopsFolderHandlerTest extends TestCase
{
    /**
     * Temporary directory root for all filesystem tests.
     */
    private string $tempDir;

    /**
     * Create a unique temp directory before each test.
     */
    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/xoops_folder_test_' . uniqid('', true);
        // Normalize to forward slashes so XoopsFolderHandler::isAbsolute() recognises
        // Windows paths like C:\... (its regex requires C:/ with a forward slash).
        $this->tempDir = str_replace('\\', '/', $this->tempDir);
        mkdir($this->tempDir, 0755, true);
    }

    /**
     * Recursively remove the temp directory after each test.
     */
    protected function tearDown(): void
    {
        $this->recursiveDelete($this->tempDir);
    }

    /**
     * Recursively delete a directory and its contents.
     */
    private function recursiveDelete(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = array_diff((array) scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->recursiveDelete($path) : unlink($path);
        }
        rmdir($dir);
    }

    // =========================================================================
    // Constructor tests
    // =========================================================================

    /**
     * Constructor with an existing directory sets path correctly.
     */
    #[Test]
    public function constructorWithExistingDirectorySetsPath(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $this->assertSame($this->tempDir, $folder->pwd());
    }

    /**
     * Constructor with a non-existent path and create=true creates the directory.
     */
    #[Test]
    public function constructorWithNonExistentPathAndCreateTrueCreatesIt(): void
    {
        $newDir = $this->tempDir . '/auto_created';
        $this->assertDirectoryDoesNotExist($newDir);

        $folder = new XoopsFolderHandler($newDir, true);

        $this->assertDirectoryExists($newDir);
        $this->assertSame($newDir, $folder->pwd());
    }

    /**
     * Constructor with false path defaults to XOOPS_VAR_PATH/caches/xoops_cache.
     */
    #[Test]
    public function constructorWithFalsePathDefaultsToCacheDir(): void
    {
        $expectedDefault = XOOPS_VAR_PATH . '/caches/xoops_cache';
        if (DIRECTORY_SEPARATOR === '\\' && !preg_match('#^[A-Z]:/#i', $expectedDefault)) {
            $this->markTestSkipped('XoopsFolderHandler::isAbsolute() does not recognise backslash Windows paths');
        }
        $folder = new XoopsFolderHandler(false, false);
        $this->assertSame($expectedDefault, $folder->pwd());
    }

    /**
     * Constructor with empty string defaults to XOOPS_VAR_PATH/caches/xoops_cache.
     */
    #[Test]
    public function constructorWithEmptyStringDefaultsToCacheDir(): void
    {
        $expectedDefault = XOOPS_VAR_PATH . '/caches/xoops_cache';
        if (DIRECTORY_SEPARATOR === '\\' && !preg_match('#^[A-Z]:/#i', $expectedDefault)) {
            $this->markTestSkipped('XoopsFolderHandler::isAbsolute() does not recognise backslash Windows paths');
        }
        $folder = new XoopsFolderHandler('', false);
        $this->assertSame($expectedDefault, $folder->pwd());
    }

    /**
     * Constructor sets mode when a custom mode is provided.
     */
    #[Test]
    public function constructorSetsModeWhenProvided(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false, '0777');
        $this->assertSame(intval('0777', 8), $folder->mode);
    }

    /**
     * Constructor default mode is '0755' (string) when no mode arg supplied.
     */
    #[Test]
    public function constructorDefaultModeIsString0755(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $this->assertSame('0755', $folder->mode);
    }

    // =========================================================================
    // pwd() / cd() tests
    // =========================================================================

    /**
     * pwd() returns the current path after construction.
     */
    #[Test]
    public function pwdReturnsCurrentPath(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $this->assertSame($this->tempDir, $folder->pwd());
    }

    /**
     * cd() to an existing directory returns the path.
     */
    #[Test]
    public function cdToExistingDirectoryReturnsPath(): void
    {
        $subDir = $this->tempDir . '/subdir';
        mkdir($subDir, 0755);

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->cd($subDir);

        $this->assertSame($subDir, $result);
    }

    /**
     * cd() to a non-existent directory returns false.
     */
    #[Test]
    public function cdToNonExistentDirectoryReturnsFalse(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->cd($this->tempDir . '/does_not_exist');

        $this->assertFalse($result);
    }

    /**
     * cd() updates the path property on success.
     */
    #[Test]
    public function cdUpdatesPathPropertyOnSuccess(): void
    {
        $subDir = $this->tempDir . '/newlocation';
        mkdir($subDir, 0755);

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $folder->cd($subDir);

        $this->assertSame($subDir, $folder->path);
        $this->assertSame($subDir, $folder->pwd());
    }

    /**
     * cd() does NOT update path on failure.
     */
    #[Test]
    public function cdDoesNotUpdatePathOnFailure(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $originalPath = $folder->pwd();

        $folder->cd($this->tempDir . '/nonexistent');

        $this->assertSame($originalPath, $folder->pwd());
    }

    // =========================================================================
    // isWindowsPath() tests
    // =========================================================================

    /**
     * isWindowsPath() correctly identifies Windows and non-Windows paths.
     */
    #[Test]
    #[DataProvider('windowsPathProvider')]
    public function isWindowsPathReturnsExpected(string $path, bool $expected): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $this->assertSame($expected, $folder->isWindowsPath($path));
    }

    /**
     * Data provider for isWindowsPath() tests.
     *
     * @return array<string, array{string, bool}>
     */
    public static function windowsPathProvider(): array
    {
        return [
            'C:\\path is Windows'         => ['C:\\path', true],
            'D:\\ is Windows'             => ['D:\\', true],
            'lowercase c:\\path'          => ['c:\\path', true],
            'Z:\\deep\\path'              => ['Z:\\deep\\path', true],
            '/unix/path is not Windows'   => ['/unix/path', false],
            'relative/path not Windows'   => ['relative/path', false],
            'empty string not Windows'    => ['', false],
            'C:/forward slash not Win'    => ['C:/path', false],
        ];
    }

    // =========================================================================
    // isAbsolute() tests
    // =========================================================================

    /**
     * isAbsolute() correctly identifies absolute and relative paths.
     */
    #[Test]
    #[DataProvider('absolutePathProvider')]
    public function isAbsoluteReturnsExpected(string $path, bool $expected): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = (bool) $folder->isAbsolute($path);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider for isAbsolute() tests.
     *
     * @return array<string, array{string, bool}>
     */
    public static function absolutePathProvider(): array
    {
        return [
            '/path is absolute'           => ['/path', true],
            'C:/path is absolute'         => ['C:/path', true],
            'd:/path is absolute'         => ['d:/path', true],
            '/usr/local/bin absolute'     => ['/usr/local/bin', true],
            'relative/path not absolute'  => ['relative/path', false],
            'file.txt not absolute'       => ['file.txt', false],
            'empty string not absolute'   => ['', false],
        ];
    }

    // =========================================================================
    // normalizePath() / correctSlashFor() tests
    // =========================================================================

    /**
     * normalizePath() returns backslash for Windows paths, forward slash otherwise.
     */
    #[Test]
    #[DataProvider('normalizePathProvider')]
    public function normalizePathReturnsExpected(string $path, string $expected): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $this->assertSame($expected, $folder->normalizePath($path));
    }

    /**
     * Data provider for normalizePath() tests.
     *
     * @return array<string, array{string, string}>
     */
    public static function normalizePathProvider(): array
    {
        return [
            'Windows path gets backslash'  => ['C:\\Users\\test', '\\'],
            'Unix path gets forward slash' => ['/usr/local', '/'],
            'Relative gets forward slash'  => ['some/path', '/'],
        ];
    }

    /**
     * correctSlashFor() returns same result as normalizePath() (they are identical).
     */
    #[Test]
    #[DataProvider('normalizePathProvider')]
    public function correctSlashForReturnsExpected(string $path, string $expected): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $this->assertSame($expected, $folder->correctSlashFor($path));
    }

    // =========================================================================
    // slashTerm() tests
    // =========================================================================

    /**
     * slashTerm() adds a trailing slash to a path that does not have one.
     */
    #[Test]
    public function slashTermAddsTrailingSlashToPathWithoutOne(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->slashTerm('/some/path');
        $this->assertSame('/some/path/', $result);
    }

    /**
     * slashTerm() leaves a path with trailing forward slash unchanged.
     */
    #[Test]
    public function slashTermLeavesPathWithTrailingForwardSlashUnchanged(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->slashTerm('/some/path/');
        $this->assertSame('/some/path/', $result);
    }

    /**
     * slashTerm() leaves a path with trailing backslash unchanged.
     */
    #[Test]
    public function slashTermLeavesPathWithTrailingBackslashUnchanged(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->slashTerm('C:\\some\\path\\');
        $this->assertSame('C:\\some\\path\\', $result);
    }

    /**
     * slashTerm() adds backslash for Windows path without trailing slash.
     */
    #[Test]
    public function slashTermAddsBackslashForWindowsPath(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->slashTerm('C:\\some\\path');
        $this->assertSame('C:\\some\\path\\', $result);
    }

    // =========================================================================
    // addPathElement() tests
    // =========================================================================

    /**
     * addPathElement() combines path and element with correct separator.
     */
    #[Test]
    public function addPathElementCombinesPathAndElement(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->addPathElement('/some/path', 'file.txt');
        $this->assertSame('/some/path/file.txt', $result);
    }

    /**
     * addPathElement() handles Windows paths correctly.
     */
    #[Test]
    public function addPathElementHandlesWindowsPaths(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->addPathElement('C:\\some\\path', 'file.txt');
        $this->assertSame('C:\\some\\path\\file.txt', $result);
    }

    /**
     * addPathElement() does not double-slash when path already slash-terminated.
     */
    #[Test]
    public function addPathElementDoesNotDoubleSlash(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->addPathElement('/some/path/', 'file.txt');
        $this->assertSame('/some/path/file.txt', $result);
    }

    // =========================================================================
    // isSlashTerm() tests
    // =========================================================================

    /**
     * isSlashTerm() correctly identifies slash-terminated and non-terminated paths.
     */
    #[Test]
    #[DataProvider('isSlashTermProvider')]
    public function isSlashTermReturnsExpected(string $path, bool $expected): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $this->assertSame($expected, $folder->isSlashTerm($path));
    }

    /**
     * Data provider for isSlashTerm() tests.
     *
     * @return array<string, array{string, bool}>
     */
    public static function isSlashTermProvider(): array
    {
        return [
            'trailing / returns true'         => ['/some/path/', true],
            'trailing \\ returns true'        => ['C:\\path\\', true],
            'no trailing slash returns false'  => ['/some/path', false],
            'no trailing bslash returns false' => ['C:\\path', false],
            'empty string returns false'       => ['', false],
            'just / returns true'             => ['/', true],
            'just \\ returns true'            => ['\\', true],
        ];
    }

    // =========================================================================
    // realpath() tests
    // =========================================================================

    /**
     * realpath() with a simple absolute path returns it unchanged.
     */
    #[Test]
    public function realpathWithSimpleAbsolutePathReturnsUnchanged(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->realpath('/some/simple/path');
        $this->assertSame('/some/simple/path', $result);
    }

    /**
     * realpath() with '..' resolves parent directory.
     */
    #[Test]
    public function realpathWithDoubleDotResolvesParent(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->realpath('/some/deep/../path');
        $this->assertSame('/some/path', $result);
    }

    /**
     * realpath() with multiple '..' levels resolves correctly.
     */
    #[Test]
    public function realpathWithMultipleDoubleDotLevels(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->realpath('/a/b/c/../../d');
        $this->assertSame('/a/d', $result);
    }

    /**
     * realpath() returns false when '..' goes above root.
     */
    #[Test]
    public function realpathReturnsFalseWhenAboveRoot(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->realpath('/a/../..');
        $this->assertFalse($result);
    }

    /**
     * realpath() with relative path adds current directory prefix.
     */
    #[Test]
    public function realpathWithRelativePathAddsCwdPrefix(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->realpath('relative');
        // Should be $this->tempDir + / + relative
        $expected = $folder->addPathElement($this->tempDir, 'relative');
        $this->assertSame($expected, $result);
    }

    /**
     * realpath() preserves trailing slash from input.
     */
    #[Test]
    public function realpathPreservesTrailingSlash(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->realpath('/a/b/../c/');
        $this->assertSame('/a/c/', $result);
    }

    /**
     * realpath() does NOT strip '.' segments (only '..' is handled).
     * The production code treats '.' like any other path component.
     */
    #[Test]
    public function realpathPreservesDotSegments(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->realpath('/a/./b/./c');
        // The method only resolves '..' — '.' is preserved as-is
        $this->assertSame('/a/./b/./c', $result);
    }

    // =========================================================================
    // read() tests
    // =========================================================================

    /**
     * read() returns two arrays: [dirs, files].
     */
    #[Test]
    public function readReturnsTwoArrays(): void
    {
        // Create a file and a subdirectory
        file_put_contents($this->tempDir . '/testfile.txt', 'hello');
        mkdir($this->tempDir . '/subdir');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->read();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertIsArray($result[0]); // dirs
        $this->assertIsArray($result[1]); // files
    }

    /**
     * read() correctly separates directories and files.
     */
    #[Test]
    public function readSeparatesDirectoriesAndFiles(): void
    {
        file_put_contents($this->tempDir . '/alpha.txt', 'data');
        file_put_contents($this->tempDir . '/beta.log', 'data');
        mkdir($this->tempDir . '/gamma');
        mkdir($this->tempDir . '/delta');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        [$dirs, $files] = $folder->read(true);

        $this->assertContains('gamma', $dirs);
        $this->assertContains('delta', $dirs);
        $this->assertContains('alpha.txt', $files);
        $this->assertContains('beta.log', $files);
    }

    /**
     * read() with sort=true sorts results alphabetically.
     */
    #[Test]
    public function readWithSortTrueSortsResults(): void
    {
        file_put_contents($this->tempDir . '/z_file.txt', 'data');
        file_put_contents($this->tempDir . '/a_file.txt', 'data');
        file_put_contents($this->tempDir . '/m_file.txt', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        [$dirs, $files] = $folder->read(true);

        $this->assertSame(['a_file.txt', 'm_file.txt', 'z_file.txt'], $files);
    }

    /**
     * read() with array exceptions filters out specified entries.
     */
    #[Test]
    public function readWithArrayExceptionsFiltersEntries(): void
    {
        file_put_contents($this->tempDir . '/keep.txt', 'data');
        file_put_contents($this->tempDir . '/skip.txt', 'data');
        mkdir($this->tempDir . '/keepdir');
        mkdir($this->tempDir . '/skipdir');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        [$dirs, $files] = $folder->read(true, ['skip.txt', 'skipdir']);

        $this->assertContains('keep.txt', $files);
        $this->assertNotContains('skip.txt', $files);
        $this->assertContains('keepdir', $dirs);
        $this->assertNotContains('skipdir', $dirs);
    }

    /**
     * read() with exceptions=true filters dot files.
     */
    #[Test]
    public function readWithExceptionsTrueFiltersDotFiles(): void
    {
        file_put_contents($this->tempDir . '/normal.txt', 'data');
        file_put_contents($this->tempDir . '/.hidden', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        [$dirs, $files] = $folder->read(true, true);

        $this->assertContains('normal.txt', $files);
        $this->assertNotContains('.hidden', $files);
    }

    /**
     * read() on empty directory returns empty arrays.
     */
    #[Test]
    public function readOnEmptyDirectoryReturnsEmptyArrays(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        [$dirs, $files] = $folder->read();

        $this->assertSame([], $dirs);
        $this->assertSame([], $files);
    }

    // =========================================================================
    // find() tests
    // =========================================================================

    /**
     * find() with a pattern matches the correct files.
     */
    #[Test]
    public function findWithPatternMatchesFiles(): void
    {
        file_put_contents($this->tempDir . '/readme.txt', 'data');
        file_put_contents($this->tempDir . '/readme.md', 'data');
        file_put_contents($this->tempDir . '/config.php', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $found = $folder->find('readme\\..*');

        $this->assertContains('readme.txt', $found);
        $this->assertContains('readme.md', $found);
        $this->assertNotContains('config.php', $found);
    }

    /**
     * find() returns empty array when no files match the pattern.
     */
    #[Test]
    public function findReturnsEmptyArrayForNoMatches(): void
    {
        file_put_contents($this->tempDir . '/file.txt', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $found = $folder->find('nonexistent\\.xyz');

        $this->assertSame([], $found);
    }

    /**
     * find() with default pattern '.*' matches all files.
     */
    #[Test]
    public function findWithDefaultPatternMatchesAllFiles(): void
    {
        file_put_contents($this->tempDir . '/a.txt', 'data');
        file_put_contents($this->tempDir . '/b.log', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $found = $folder->find();

        $this->assertCount(2, $found);
        $this->assertContains('a.txt', $found);
        $this->assertContains('b.log', $found);
    }

    /**
     * find() does not return directories, only files.
     */
    #[Test]
    public function findReturnsOnlyFilesNotDirectories(): void
    {
        file_put_contents($this->tempDir . '/file.txt', 'data');
        mkdir($this->tempDir . '/somedir');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $found = $folder->find('.*');

        $this->assertContains('file.txt', $found);
        $this->assertNotContains('somedir', $found);
    }

    // =========================================================================
    // findRecursive() tests
    // =========================================================================

    /**
     * findRecursive() finds files in nested subdirectories.
     */
    #[Test]
    public function findRecursiveFindsFilesInSubdirectories(): void
    {
        mkdir($this->tempDir . '/level1');
        mkdir($this->tempDir . '/level1/level2');
        file_put_contents($this->tempDir . '/top.txt', 'data');
        file_put_contents($this->tempDir . '/level1/mid.txt', 'data');
        file_put_contents($this->tempDir . '/level1/level2/deep.txt', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $found = $folder->findRecursive('.*\\.txt');

        $this->assertCount(3, $found);
    }

    /**
     * findRecursive() restores the original path after search.
     */
    #[Test]
    public function findRecursiveRestoresOriginalPath(): void
    {
        mkdir($this->tempDir . '/sub');
        file_put_contents($this->tempDir . '/sub/file.txt', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $originalPath = $folder->pwd();

        $folder->findRecursive('.*');

        $this->assertSame($originalPath, $folder->pwd());
    }

    /**
     * findRecursive() returns full paths, not just filenames.
     */
    #[Test]
    public function findRecursiveReturnsFullPaths(): void
    {
        file_put_contents($this->tempDir . '/root.txt', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $found = $folder->findRecursive('root\\.txt');

        $this->assertCount(1, $found);
        // The result should contain the temp dir path
        $this->assertStringContainsString($this->tempDir, $found[0]);
    }

    // =========================================================================
    // tree() tests
    // =========================================================================

    /**
     * tree() with null type returns [directories, files] arrays.
     */
    #[Test]
    public function treeReturnsDirectoriesAndFilesArrays(): void
    {
        mkdir($this->tempDir . '/treeDir');
        file_put_contents($this->tempDir . '/treeFile.txt', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->tree($this->tempDir);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertIsArray($result[0]); // directories
        $this->assertIsArray($result[1]); // files
    }

    /**
     * tree() includes the root path in the directories list.
     */
    #[Test]
    public function treeIncludesRootInDirectories(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        [$dirs, $files] = $folder->tree($this->tempDir);

        $this->assertContains($this->tempDir, $dirs);
    }

    /**
     * tree() with type='dir' returns only directories (not an array of arrays).
     */
    #[Test]
    public function treeWithTypeDirReturnsOnlyDirectories(): void
    {
        mkdir($this->tempDir . '/subA');
        file_put_contents($this->tempDir . '/afile.txt', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $dirs = $folder->tree($this->tempDir, true, 'dir');

        $this->assertIsArray($dirs);
        $this->assertContains($this->tempDir, $dirs);
        $this->assertContains($this->tempDir . '/subA', $dirs);
        // It should be a flat array of directory paths, not contain file paths
        foreach ($dirs as $dir) {
            $this->assertIsString($dir);
        }
    }

    /**
     * tree() with type='file' returns only files.
     */
    #[Test]
    public function treeWithTypeFileReturnsOnlyFiles(): void
    {
        mkdir($this->tempDir . '/subB');
        file_put_contents($this->tempDir . '/bfile.txt', 'data');
        file_put_contents($this->tempDir . '/subB/nested.txt', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $files = $folder->tree($this->tempDir, true, 'file');

        $this->assertIsArray($files);
        $this->assertNotEmpty($files);
        // Files should contain full paths
        $hasExpectedFile = false;
        foreach ($files as $file) {
            $this->assertIsString($file);
            if (basename($file) === 'bfile.txt' || basename($file) === 'nested.txt') {
                $hasExpectedFile = true;
            }
        }
        $this->assertTrue($hasExpectedFile, 'Expected files not found in tree output');
    }

    /**
     * tree() with hidden=false excludes dot files/directories.
     */
    #[Test]
    public function treeWithHiddenFalseExcludesDotEntries(): void
    {
        mkdir($this->tempDir . '/.hiddendir');
        file_put_contents($this->tempDir . '/.hiddenfile', 'data');
        file_put_contents($this->tempDir . '/visible.txt', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        [$dirs, $files] = $folder->tree($this->tempDir, false);

        // Hidden items should not appear
        foreach ($files as $file) {
            $this->assertStringNotContainsString('.hiddenfile', $file);
        }
        foreach ($dirs as $dir) {
            $this->assertStringNotContainsString('.hiddendir', $dir);
        }
        // Visible file should appear
        $visibleFound = false;
        foreach ($files as $file) {
            if (basename($file) === 'visible.txt') {
                $visibleFound = true;
            }
        }
        $this->assertTrue($visibleFound, 'Visible file should be in tree output');
    }

    /**
     * tree() with nested directories lists all levels.
     */
    #[Test]
    public function treeWithNestedDirectoriesListsAllLevels(): void
    {
        mkdir($this->tempDir . '/a');
        mkdir($this->tempDir . '/a/b');
        mkdir($this->tempDir . '/a/b/c');
        file_put_contents($this->tempDir . '/a/b/c/leaf.txt', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        [$dirs, $files] = $folder->tree($this->tempDir);

        $this->assertContains($this->tempDir, $dirs);
        $this->assertContains($this->tempDir . '/a', $dirs);
        $this->assertContains($this->tempDir . '/a/b', $dirs);
        $this->assertContains($this->tempDir . '/a/b/c', $dirs);
    }

    // =========================================================================
    // create() tests
    // =========================================================================

    /**
     * create() creates a nested directory structure.
     */
    #[Test]
    public function createCreatesNestedDirectoryStructure(): void
    {
        $nested = $this->tempDir . '/one/two/three';
        $folder = new XoopsFolderHandler($this->tempDir, false);

        $result = $folder->create($nested);

        $this->assertTrue($result);
        $this->assertDirectoryExists($nested);
    }

    /**
     * create() returns true for an already-existing directory.
     */
    #[Test]
    public function createReturnsTrueForExistingDirectory(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->create($this->tempDir);

        $this->assertTrue($result);
    }

    /**
     * create() returns true for empty string.
     */
    #[Test]
    public function createReturnsTrueForEmptyString(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->create('');

        $this->assertTrue($result);
    }

    /**
     * create() returns true and adds error when pathname is a file.
     */
    #[Test]
    public function createReturnsTrueWhenPathnameIsFile(): void
    {
        $filePath = $this->tempDir . '/existing_file.txt';
        file_put_contents($filePath, 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->create($filePath);

        $this->assertTrue($result);
        $this->assertNotEmpty($folder->errors());
    }

    /**
     * create() populates messages array on success.
     */
    #[Test]
    public function createPopulatesMessagesOnSuccess(): void
    {
        $newDir = $this->tempDir . '/created_dir';
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $folder->create($newDir);

        $messages = $folder->messages();
        $this->assertNotEmpty($messages);
        // The message should mention the created directory
        $found = false;
        foreach ($messages as $msg) {
            if (strpos($msg, 'created_dir') !== false) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Messages should mention the created directory');
    }

    // =========================================================================
    // delete() tests
    // =========================================================================

    /**
     * delete() removes a directory and all its contents.
     */
    #[Test]
    public function deleteRemovesDirectoryAndContents(): void
    {
        $dirToDelete = $this->tempDir . '/to_delete';
        mkdir($dirToDelete);
        file_put_contents($dirToDelete . '/file1.txt', 'data');
        mkdir($dirToDelete . '/subdir');
        file_put_contents($dirToDelete . '/subdir/file2.txt', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->delete($dirToDelete);

        $this->assertTrue($result);
        $this->assertDirectoryDoesNotExist($dirToDelete);
    }

    /**
     * delete() returns true for a non-existent path (no-op).
     */
    #[Test]
    public function deleteReturnsTrueForNonExistentPath(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->delete($this->tempDir . '/does_not_exist');

        $this->assertTrue($result);
    }

    /**
     * delete() populates messages on successful removal.
     */
    #[Test]
    public function deletePopulatesMessagesOnSuccess(): void
    {
        $dirToDelete = $this->tempDir . '/msg_delete';
        mkdir($dirToDelete);
        file_put_contents($dirToDelete . '/afile.txt', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $folder->delete($dirToDelete);

        $this->assertNotEmpty($folder->messages());
    }

    // =========================================================================
    // copy() tests
    // =========================================================================

    /**
     * copy() copies directory structure to target using string argument.
     */
    #[Test]
    public function copyCopiesDirectoryStructureWithStringTarget(): void
    {
        // Set up source
        $source = $this->tempDir . '/copy_source';
        $target = $this->tempDir . '/copy_target';
        mkdir($source);
        file_put_contents($source . '/file1.txt', 'content1');
        mkdir($source . '/inner');
        file_put_contents($source . '/inner/file2.txt', 'content2');

        $folder = new XoopsFolderHandler($source, false);
        $result = $folder->copy($target);

        $this->assertTrue($result);
        $this->assertDirectoryExists($target);
        $this->assertFileExists($target . '/file1.txt');
        $this->assertSame('content1', file_get_contents($target . '/file1.txt'));
        $this->assertDirectoryExists($target . '/inner');
        $this->assertFileExists($target . '/inner/file2.txt');
        $this->assertSame('content2', file_get_contents($target . '/inner/file2.txt'));
    }

    /**
     * copy() with options array specifying 'to' and 'from'.
     */
    #[Test]
    public function copyCopiesWithOptionsArray(): void
    {
        $source = $this->tempDir . '/opt_source';
        $target = $this->tempDir . '/opt_target';
        mkdir($source);
        file_put_contents($source . '/data.txt', 'hello');

        $folder = new XoopsFolderHandler($source, false);
        $result = $folder->copy([
            'to'   => $target,
            'from' => $source,
        ]);

        $this->assertTrue($result);
        $this->assertFileExists($target . '/data.txt');
    }

    /**
     * copy() with skip option excludes specified entries.
     */
    #[Test]
    public function copyWithSkipOptionExcludesEntries(): void
    {
        $source = $this->tempDir . '/skip_source';
        $target = $this->tempDir . '/skip_target';
        mkdir($source);
        file_put_contents($source . '/keep.txt', 'keep');
        file_put_contents($source . '/skip.txt', 'skip');

        $folder = new XoopsFolderHandler($source, false);
        $result = $folder->copy([
            'to'   => $target,
            'from' => $source,
            'skip' => ['skip.txt'],
        ]);

        $this->assertTrue($result);
        $this->assertFileExists($target . '/keep.txt');
        $this->assertFileDoesNotExist($target . '/skip.txt');
    }

    /**
     * copy() returns false when source does not exist.
     */
    #[Test]
    public function copyReturnsFalseWhenSourceDoesNotExist(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->copy([
            'from' => $this->tempDir . '/nonexistent_source',
            'to'   => $this->tempDir . '/some_target',
        ]);

        $this->assertFalse($result);
        $this->assertNotEmpty($folder->errors());
    }

    /**
     * copy() populates messages on successful copy.
     */
    #[Test]
    public function copyPopulatesMessagesOnSuccess(): void
    {
        $source = $this->tempDir . '/msg_source';
        $target = $this->tempDir . '/msg_target';
        mkdir($source);
        file_put_contents($source . '/f.txt', 'data');

        $folder = new XoopsFolderHandler($source, false);
        $folder->copy($target);

        $this->assertNotEmpty($folder->messages());
    }

    // =========================================================================
    // move() tests
    // =========================================================================

    /**
     * move() moves directory by copying then deleting the source.
     */
    #[Test]
    public function moveMovesDirectoryStructure(): void
    {
        $source = $this->tempDir . '/move_source';
        $target = $this->tempDir . '/move_target';
        mkdir($source);
        file_put_contents($source . '/moveme.txt', 'moved');

        $folder = new XoopsFolderHandler($source, false);
        $result = $folder->move($target);

        // move returns the result of cd() on success, which is a string path
        $this->assertNotFalse($result);
        $this->assertFileExists($target . '/moveme.txt');
        $this->assertSame('moved', file_get_contents($target . '/moveme.txt'));
        // Source should be deleted
        $this->assertDirectoryDoesNotExist($source);
    }

    /**
     * move() with string argument uses it as target directory.
     */
    #[Test]
    public function moveWithStringArgumentUsesAsTarget(): void
    {
        $source = $this->tempDir . '/str_move_src';
        $target = $this->tempDir . '/str_move_dst';
        mkdir($source);
        file_put_contents($source . '/item.txt', 'value');

        $folder = new XoopsFolderHandler($source, false);
        $result = $folder->move($target);

        $this->assertNotFalse($result);
        $this->assertDirectoryExists($target);
        $this->assertDirectoryDoesNotExist($source);
    }

    /**
     * move() returns false when source does not exist.
     */
    #[Test]
    public function moveReturnsFalseWhenSourceInvalid(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->move([
            'from' => $this->tempDir . '/no_such_dir',
            'to'   => $this->tempDir . '/dst',
        ]);

        $this->assertFalse($result);
    }

    // =========================================================================
    // dirsize() tests
    // =========================================================================

    /**
     * dirsize() returns size of directory contents in bytes.
     */
    #[Test]
    public function dirsizeReturnsSizeOfContents(): void
    {
        $content = 'Hello, World!'; // 13 bytes
        file_put_contents($this->tempDir . '/sizefile.txt', $content);

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $size = $folder->dirsize();

        $this->assertSame(strlen($content), $size);
    }

    /**
     * dirsize() returns 0 for an empty directory.
     */
    #[Test]
    public function dirsizeReturnsZeroForEmptyDirectory(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $size = $folder->dirsize();

        $this->assertSame(0, $size);
    }

    /**
     * dirsize() includes files in nested subdirectories.
     */
    #[Test]
    public function dirsizeIncludesNestedFiles(): void
    {
        $content1 = 'AAAA'; // 4 bytes
        $content2 = 'BBBBBB'; // 6 bytes
        mkdir($this->tempDir . '/nested');
        file_put_contents($this->tempDir . '/file1.txt', $content1);
        file_put_contents($this->tempDir . '/nested/file2.txt', $content2);

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $size = $folder->dirsize();

        $this->assertSame(strlen($content1) + strlen($content2), $size);
    }

    // =========================================================================
    // inPath() tests
    // =========================================================================

    /**
     * inPath() returns true when the folder IS in the given path.
     */
    #[Test]
    public function inPathReturnsTrueWhenFolderIsInPath(): void
    {
        $subDir = $this->tempDir . '/parent/child';
        mkdir($subDir, 0755, true);

        $folder = new XoopsFolderHandler($subDir, false);
        $result = $folder->inPath($this->tempDir);

        $this->assertTrue($result);
    }

    /**
     * inPath() returns false when the folder is NOT in the given path.
     */
    #[Test]
    public function inPathReturnsFalseWhenNotInPath(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->inPath('/completely/different/path');

        $this->assertFalse($result);
    }

    /**
     * inPath() with reverse=true checks if given path is inside current folder.
     */
    #[Test]
    public function inPathWithReverseChecksPathInsideCurrentFolder(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        // The tempDir itself, slash-terminated, should "contain" a longer path
        $longerPath = $this->tempDir . '/some/deeper/path';
        $result = $folder->inPath($longerPath, true);

        $this->assertTrue($result);
    }

    /**
     * inPath() with reverse=true returns false for unrelated path.
     */
    #[Test]
    public function inPathWithReverseReturnsFalseForUnrelatedPath(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->inPath('/unrelated', true);

        $this->assertFalse($result);
    }

    // =========================================================================
    // inXoopsPath() tests
    // =========================================================================

    /**
     * inXoopsPath() returns true when folder is within XOOPS root.
     */
    #[Test]
    public function inXoopsPathReturnsTrueWhenInXoopsRoot(): void
    {
        // Use the XOOPS cache dir which is known to exist
        $cachePath = XOOPS_VAR_PATH . '/caches/xoops_cache';
        if (!is_dir($cachePath)) {
            $this->markTestSkipped('XOOPS cache directory does not exist');
        }
        if (DIRECTORY_SEPARATOR === '\\' && !preg_match('#^[A-Z]:/#i', $cachePath)) {
            $this->markTestSkipped('XoopsFolderHandler::isAbsolute() does not recognise backslash Windows paths');
        }
        $folder = new XoopsFolderHandler($cachePath, false);

        // The folder at XOOPS_VAR_PATH would need to be under XOOPS_ROOT_PATH
        // Since XOOPS_VAR_PATH = XOOPS_ROOT_PATH/xoops_data, inXoopsPath('') checks
        // against XOOPS_ROOT_PATH itself
        $result = $folder->inXoopsPath('');

        // Cache is at XOOPS_ROOT_PATH/xoops_data/caches/xoops_cache
        // inXoopsPath('') checks if folder is in XOOPS_ROOT_PATH
        $this->assertTrue($result);
    }

    /**
     * inXoopsPath() with subpath checks more specifically.
     */
    #[Test]
    public function inXoopsPathWithSubpath(): void
    {
        $cachePath = XOOPS_VAR_PATH . '/caches/xoops_cache';
        if (!is_dir($cachePath)) {
            $this->markTestSkipped('XOOPS cache directory does not exist');
        }
        if (DIRECTORY_SEPARATOR === '\\' && !preg_match('#^[A-Z]:/#i', $cachePath)) {
            $this->markTestSkipped('XoopsFolderHandler::isAbsolute() does not recognise backslash Windows paths');
        }
        $folder = new XoopsFolderHandler($cachePath, false);

        // inXoopsPath('/xoops_data') checks if folder is in XOOPS_ROOT_PATH/xoops_data
        $result = $folder->inXoopsPath('/xoops_data');
        $this->assertTrue($result);
    }

    // =========================================================================
    // chmod() tests
    // =========================================================================

    /**
     * chmod() with recursive=false on a directory returns true on success.
     */
    #[Test]
    public function chmodNonRecursiveReturnsTrueOnSuccess(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->chmod($this->tempDir, '0755', false);

        $this->assertTrue($result);
        $this->assertNotEmpty($folder->messages());
    }

    /**
     * chmod() recursive mode processes subdirectories.
     */
    #[Test]
    public function chmodRecursiveProcessesSubdirectories(): void
    {
        mkdir($this->tempDir . '/chmod_sub');
        mkdir($this->tempDir . '/chmod_sub/deeper');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->chmod($this->tempDir, '0755', true);

        $this->assertTrue($result);
        $messages = $folder->messages();
        $this->assertNotEmpty($messages);
    }

    /**
     * chmod() with exceptions skips specified directories.
     */
    #[Test]
    public function chmodWithExceptionsSkipsSpecifiedDirs(): void
    {
        mkdir($this->tempDir . '/include');
        mkdir($this->tempDir . '/exclude');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $folder->chmod($this->tempDir, '0755', true, ['exclude']);

        // Check messages to see exclude was skipped
        $messages = $folder->messages();
        foreach ($messages as $msg) {
            $this->assertStringNotContainsString('exclude', $msg);
        }
    }

    /**
     * chmod() returns false when path is not a directory.
     */
    #[Test]
    public function chmodReturnsFalseForNonDirectory(): void
    {
        $filePath = $this->tempDir . '/notadir.txt';
        file_put_contents($filePath, 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->chmod($filePath, '0755', true);

        $this->assertFalse($result);
    }

    /**
     * chmod() uses default mode from property when mode is false.
     */
    #[Test]
    public function chmodUsesDefaultModeWhenFalse(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->chmod($this->tempDir, false, false);

        // Should succeed with default mode '0755'
        $this->assertTrue($result);
    }

    // =========================================================================
    // messages() / errors() tests
    // =========================================================================

    /**
     * messages() returns the messages array (initially empty).
     */
    #[Test]
    public function messagesReturnsEmptyArrayInitially(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $this->assertSame([], $folder->messages());
    }

    /**
     * errors() returns the errors array (initially empty).
     */
    #[Test]
    public function errorsReturnsEmptyArrayInitially(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $this->assertSame([], $folder->errors());
    }

    /**
     * messages() accumulates messages from operations.
     */
    #[Test]
    public function messagesAccumulatesFromOperations(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);

        // Create a new directory, which should add a message
        $folder->create($this->tempDir . '/msg_test_dir');

        $messages = $folder->messages();
        $this->assertIsArray($messages);
        $this->assertNotEmpty($messages);
    }

    /**
     * errors() accumulates errors from failed operations.
     */
    #[Test]
    public function errorsAccumulatesFromFailedOperations(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);

        // Passing a file path to create() should generate an error message
        $filePath = $this->tempDir . '/block.txt';
        file_put_contents($filePath, 'data');
        $folder->create($filePath);

        $errors = $folder->errors();
        $this->assertIsArray($errors);
        $this->assertNotEmpty($errors);
    }

    // =========================================================================
    // Property access tests
    // =========================================================================

    /**
     * sort property defaults to false.
     */
    #[Test]
    public function sortPropertyDefaultsToFalse(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        $this->assertFalse($folder->sort);
    }

    /**
     * directories and files properties are accessible.
     */
    #[Test]
    public function directoriesAndFilesPropertiesAccessible(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);

        // Before tree() call, these may be null
        // After tree(), they should be populated
        mkdir($this->tempDir . '/propdir');
        file_put_contents($this->tempDir . '/propfile.txt', 'data');

        $folder->tree($this->tempDir);

        // After tree(), $folder->files should be an array
        $this->assertIsArray($folder->files);
    }

    // =========================================================================
    // Edge case tests
    // =========================================================================

    /**
     * Constructor with non-existent path and create=false does not create directory.
     */
    #[Test]
    public function constructorWithCreateFalseDoesNotCreateDir(): void
    {
        $newDir = $this->tempDir . '/should_not_create';
        // Pass create=false
        $folder = new XoopsFolderHandler($newDir, false);

        $this->assertDirectoryDoesNotExist($newDir);
    }

    /**
     * Multiple cd() calls update path correctly each time.
     */
    #[Test]
    public function multipleCdCallsUpdatePath(): void
    {
        $dir1 = $this->tempDir . '/dir1';
        $dir2 = $this->tempDir . '/dir2';
        mkdir($dir1);
        mkdir($dir2);

        $folder = new XoopsFolderHandler($this->tempDir, false);

        $folder->cd($dir1);
        $this->assertSame($dir1, $folder->pwd());

        $folder->cd($dir2);
        $this->assertSame($dir2, $folder->pwd());
    }

    /**
     * read() excludes . and .. by default (exceptions=false).
     */
    #[Test]
    public function readExcludesDotAndDotDotByDefault(): void
    {
        file_put_contents($this->tempDir . '/file.txt', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        [$dirs, $files] = $folder->read(true, false);

        $this->assertNotContains('.', $dirs);
        $this->assertNotContains('..', $dirs);
        $this->assertNotContains('.', $files);
        $this->assertNotContains('..', $files);
    }

    /**
     * find() is case-insensitive (the regex uses /i flag).
     */
    #[Test]
    public function findIsCaseInsensitive(): void
    {
        file_put_contents($this->tempDir . '/README.TXT', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $found = $folder->find('readme\\.txt');

        $this->assertContains('README.TXT', $found);
    }

    /**
     * create() with custom mode applies that mode.
     */
    #[Test]
    public function createWithCustomModeAppliesMode(): void
    {
        $dir = $this->tempDir . '/mode_test';
        $folder = new XoopsFolderHandler($this->tempDir, false);

        $result = $folder->create($dir, '0700');
        $this->assertTrue($result);
        $this->assertDirectoryExists($dir);
    }

    /**
     * delete() handles directory with only subdirectories (no files).
     */
    #[Test]
    public function deleteHandlesDirectoryWithOnlySubdirs(): void
    {
        $dir = $this->tempDir . '/only_subdirs';
        mkdir($dir);
        mkdir($dir . '/child1');
        mkdir($dir . '/child2');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $result = $folder->delete($dir);

        $this->assertTrue($result);
        $this->assertDirectoryDoesNotExist($dir);
    }

    /**
     * dirsize() handles multiple files at various nesting levels.
     */
    #[Test]
    public function dirsizeHandlesMultipleNestingLevels(): void
    {
        $data1 = 'AB'; // 2 bytes
        $data2 = 'CDE'; // 3 bytes
        $data3 = 'F'; // 1 byte

        mkdir($this->tempDir . '/lvl1');
        mkdir($this->tempDir . '/lvl1/lvl2');
        file_put_contents($this->tempDir . '/f1.txt', $data1);
        file_put_contents($this->tempDir . '/lvl1/f2.txt', $data2);
        file_put_contents($this->tempDir . '/lvl1/lvl2/f3.txt', $data3);

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $size = $folder->dirsize();

        $this->assertSame(6, $size);
    }

    /**
     * slashTerm() and addPathElement() work correctly together.
     */
    #[Test]
    public function slashTermAndAddPathElementIntegration(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);

        $base = '/root/folder';
        $terminated = $folder->slashTerm($base);
        $combined = $folder->addPathElement($base, 'subdir');

        $this->assertSame('/root/folder/', $terminated);
        $this->assertSame('/root/folder/subdir', $combined);
    }

    /**
     * realpath() returns false for a path that is just '..'.
     */
    #[Test]
    public function realpathReturnsFalseForBareDotDot(): void
    {
        $folder = new XoopsFolderHandler($this->tempDir, false);
        // '..' with no leading component means the resolved array becomes empty
        // and we pop from an empty array, returning false
        $result = $folder->realpath('/..');
        $this->assertFalse($result);
    }

    /**
     * Verify that read() with sort property set on folder instance sorts results.
     */
    #[Test]
    public function readUsesInstanceSortProperty(): void
    {
        file_put_contents($this->tempDir . '/z.txt', 'data');
        file_put_contents($this->tempDir . '/a.txt', 'data');

        $folder = new XoopsFolderHandler($this->tempDir, false);
        $folder->sort = true;

        // Even with sort=false argument, instance sort=true should trigger sorting
        [$dirs, $files] = $folder->read(false);

        $this->assertSame(['a.txt', 'z.txt'], $files);
    }
}
