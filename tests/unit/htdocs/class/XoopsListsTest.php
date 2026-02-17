<?php

declare(strict_types=1);

namespace xoopsclass;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

// The bootstrap already defines XOOPS_ROOT_PATH and loads xoopsload.php.
// XoopsLists is guarded by XOOPS_LISTS_INCLUDED, so a single require is fine.
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

/**
 * Unit tests for XoopsLists.
 *
 * Each filesystem-dependent test builds disposable directories under
 * sys_get_temp_dir() and tears them down afterwards.
 */
#[CoversClass(\XoopsLists::class)]
class XoopsListsTest extends TestCase
{
    /** @var string Root temp directory created per-test */
    private $tempDir = '';

    // =========================================================================
    // Fixtures
    // =========================================================================

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR
                       . 'xoops_lists_test_' . uniqid('', true);
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDir($this->tempDir);
    }

    /**
     * Recursively remove a directory tree.
     */
    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = array_diff((array) scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            is_dir($path) ? $this->removeDir($path) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * Helper: create subdirectories inside the temp dir.
     *
     * @param string[] $names
     */
    private function createSubDirs(array $names): void
    {
        foreach ($names as $name) {
            mkdir($this->tempDir . DIRECTORY_SEPARATOR . $name, 0777, true);
        }
    }

    /**
     * Helper: create files inside the temp dir.
     *
     * @param string[] $names
     */
    private function createFiles(array $names): void
    {
        foreach ($names as $name) {
            file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . $name, 'test');
        }
    }

    // =========================================================================
    // getDirListAsArray
    // =========================================================================

    public function testGetDirListAsArrayReturnsSubdirectories(): void
    {
        $this->createSubDirs(['alpha', 'beta', 'gamma']);
        $this->createFiles(['somefile.txt']);

        $result = \XoopsLists::getDirListAsArray($this->tempDir);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertContains('alpha', $result);
        $this->assertContains('beta', $result);
        $this->assertContains('gamma', $result);
    }

    public function testGetDirListAsArrayExcludesFiles(): void
    {
        $this->createSubDirs(['subdir']);
        $this->createFiles(['file1.txt', 'file2.php']);

        $result = \XoopsLists::getDirListAsArray($this->tempDir);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('subdir', $result);
    }

    public function testGetDirListAsArraySkipsHiddenDirs(): void
    {
        $this->createSubDirs(['.hidden', 'visible']);

        $result = \XoopsLists::getDirListAsArray($this->tempDir);

        $this->assertCount(1, $result);
        $this->assertContains('visible', $result);
        $this->assertNotContains('.hidden', $result);
    }

    public function testGetDirListAsArraySkipsCvsDirectory(): void
    {
        $this->createSubDirs(['cvs', 'other']);

        $result = \XoopsLists::getDirListAsArray($this->tempDir);

        $this->assertCount(1, $result);
        $this->assertContains('other', $result);
    }

    public function testGetDirListAsArraySkipsCvsDirectoryCaseInsensitive(): void
    {
        $this->createSubDirs(['CVS', 'other']);

        // The source uses strtolower($file) for checking, so CVS should be skipped
        $result = \XoopsLists::getDirListAsArray($this->tempDir);

        $this->assertCount(1, $result);
        $this->assertContains('other', $result);
    }

    public function testGetDirListAsArraySkipsDarcsDirectory(): void
    {
        $this->createSubDirs(['_darcs', 'normal']);

        $result = \XoopsLists::getDirListAsArray($this->tempDir);

        $this->assertCount(1, $result);
        $this->assertContains('normal', $result);
    }

    public function testGetDirListAsArrayReturnsEmptyForEmptyDir(): void
    {
        $result = \XoopsLists::getDirListAsArray($this->tempDir);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetDirListAsArrayResultIsSorted(): void
    {
        $this->createSubDirs(['zebra', 'apple', 'mango']);

        $result = \XoopsLists::getDirListAsArray($this->tempDir);

        $values = array_values($result);
        $this->assertSame(['apple', 'mango', 'zebra'], $values);
    }

    public function testGetDirListAsArrayAppendsSlashIfMissing(): void
    {
        // Pass dirname without trailing slash — method should still work
        $this->createSubDirs(['subA']);

        $withoutSlash = rtrim($this->tempDir, '/\\');
        $result = \XoopsLists::getDirListAsArray($withoutSlash);

        $this->assertCount(1, $result);
        $this->assertContains('subA', $result);
    }

    public function testGetDirListAsArrayWorksWithTrailingSlash(): void
    {
        $this->createSubDirs(['subB']);

        $result = \XoopsLists::getDirListAsArray($this->tempDir . '/');

        $this->assertCount(1, $result);
        $this->assertContains('subB', $result);
    }

    public function testGetDirListAsArrayKeysMatchValues(): void
    {
        $this->createSubDirs(['dirOne', 'dirTwo']);

        $result = \XoopsLists::getDirListAsArray($this->tempDir);

        foreach ($result as $key => $value) {
            $this->assertSame($key, $value, 'Keys and values must be identical');
        }
    }

    // =========================================================================
    // getFileListAsArray
    // =========================================================================

    public function testGetFileListAsArrayReturnsFiles(): void
    {
        $this->createFiles(['readme.txt', 'script.php']);
        $this->createSubDirs(['subdir']);

        $result = \XoopsLists::getFileListAsArray($this->tempDir);

        $this->assertCount(2, $result);
        $this->assertContains('readme.txt', $result);
        $this->assertContains('script.php', $result);
    }

    public function testGetFileListAsArrayExcludesDirectories(): void
    {
        $this->createFiles(['file.txt']);
        $this->createSubDirs(['dirA', 'dirB']);

        $result = \XoopsLists::getFileListAsArray($this->tempDir);

        $this->assertCount(1, $result);
        $this->assertNotContains('dirA', $result);
    }

    public function testGetFileListAsArraySkipsDotEntries(): void
    {
        $this->createFiles(['normal.txt']);

        $result = \XoopsLists::getFileListAsArray($this->tempDir);

        $this->assertNotContains('.', $result);
        $this->assertNotContains('..', $result);
    }

    public function testGetFileListAsArrayWithPrefix(): void
    {
        $this->createFiles(['data.csv']);

        $result = \XoopsLists::getFileListAsArray($this->tempDir, 'uploads/');

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('uploads/data.csv', $result);
        $this->assertSame('uploads/data.csv', $result['uploads/data.csv']);
    }

    public function testGetFileListAsArrayReturnsEmptyForEmptyDir(): void
    {
        $result = \XoopsLists::getFileListAsArray($this->tempDir);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testGetFileListAsArrayResultIsSorted(): void
    {
        $this->createFiles(['z.txt', 'a.txt', 'm.txt']);

        $result = \XoopsLists::getFileListAsArray($this->tempDir);

        $values = array_values($result);
        $this->assertSame(['a.txt', 'm.txt', 'z.txt'], $values);
    }

    public function testGetFileListAsArrayStripsTrailingSlash(): void
    {
        $this->createFiles(['file.dat']);

        $result = \XoopsLists::getFileListAsArray($this->tempDir . '/');

        $this->assertCount(1, $result);
        $this->assertContains('file.dat', $result);
    }

    // =========================================================================
    // getFileListByExtension
    // =========================================================================

    public function testGetFileListByExtensionFiltersCorrectly(): void
    {
        $this->createFiles(['photo.jpg', 'photo.png', 'doc.pdf', 'style.css']);

        $result = \XoopsLists::getFileListByExtension($this->tempDir, ['jpg', 'png']);

        $this->assertCount(2, $result);
        $this->assertContains('photo.jpg', $result);
        $this->assertContains('photo.png', $result);
    }

    public function testGetFileListByExtensionIsCaseInsensitive(): void
    {
        $this->createFiles(['IMAGE.JPG', 'Photo.Png']);

        $result = \XoopsLists::getFileListByExtension($this->tempDir, ['jpg', 'png']);

        $this->assertCount(2, $result);
    }

    public function testGetFileListByExtensionWithPrefix(): void
    {
        $this->createFiles(['icon.gif']);

        $result = \XoopsLists::getFileListByExtension($this->tempDir, ['gif'], 'images/');

        $this->assertArrayHasKey('images/icon.gif', $result);
    }

    public function testGetFileListByExtensionReturnsEmptyOnNoMatch(): void
    {
        $this->createFiles(['readme.txt', 'notes.md']);

        $result = \XoopsLists::getFileListByExtension($this->tempDir, ['jpg', 'png']);

        $this->assertEmpty($result);
    }

    public function testGetFileListByExtensionResultIsSorted(): void
    {
        $this->createFiles(['z.js', 'a.js', 'm.js']);

        $result = \XoopsLists::getFileListByExtension($this->tempDir, ['js']);

        $values = array_values($result);
        $this->assertSame(['a.js', 'm.js', 'z.js'], $values);
    }

    /**
     * @return array<string, array{string[], string[]}>
     */
    public static function extensionCaseProvider(): array
    {
        return [
            'lower extension list'  => [['jpg'], ['photo.JPG']],
            'upper extension list'  => [['JPG'], ['photo.jpg']],
            'mixed extension list'  => [['JpG'], ['photo.jpg']],
        ];
    }

    /**
     * @param string[] $extensions
     * @param string[] $files
     */
    #[DataProvider('extensionCaseProvider')]
    public function testGetFileListByExtensionCaseCombinations(array $extensions, array $files): void
    {
        $this->createFiles($files);

        $result = \XoopsLists::getFileListByExtension($this->tempDir, $extensions);

        $this->assertCount(1, $result, 'Extension matching must be case-insensitive regardless of input case');
    }

    // =========================================================================
    // getImgListAsArray
    // =========================================================================

    public function testGetImgListAsArrayFiltersImageExtensions(): void
    {
        $this->createFiles([
            'photo.gif',
            'photo.jpeg',
            'photo.jpg',
            'photo.png',
            'doc.pdf',
            'style.css',
        ]);

        $result = \XoopsLists::getImgListAsArray($this->tempDir);

        $this->assertCount(4, $result);
        $this->assertContains('photo.gif', $result);
        $this->assertContains('photo.jpeg', $result);
        $this->assertContains('photo.jpg', $result);
        $this->assertContains('photo.png', $result);
    }

    public function testGetImgListAsArrayWithPrefix(): void
    {
        $this->createFiles(['icon.png']);

        $result = \XoopsLists::getImgListAsArray($this->tempDir, 'img/');

        $this->assertArrayHasKey('img/icon.png', $result);
    }

    public function testGetImgListAsArrayExcludesNonImageFiles(): void
    {
        $this->createFiles(['readme.txt', 'script.js', 'style.css']);

        $result = \XoopsLists::getImgListAsArray($this->tempDir);

        $this->assertEmpty($result);
    }

    public function testGetImgListAsArrayCaseInsensitive(): void
    {
        $this->createFiles(['PHOTO.PNG', 'Image.GIF']);

        $result = \XoopsLists::getImgListAsArray($this->tempDir);

        $this->assertCount(2, $result);
    }

    // =========================================================================
    // getHtmlListAsArray
    // =========================================================================

    public function testGetHtmlListAsArrayFiltersHtmlExtensions(): void
    {
        $this->createFiles([
            'page.htm',
            'page.html',
            'page.xhtml',
            'page.tpl',
            'script.js',
            'style.css',
        ]);

        $result = \XoopsLists::getHtmlListAsArray($this->tempDir);

        $this->assertCount(4, $result);
        $this->assertContains('page.htm', $result);
        $this->assertContains('page.html', $result);
        $this->assertContains('page.xhtml', $result);
        $this->assertContains('page.tpl', $result);
    }

    public function testGetHtmlListAsArrayIsCaseInsensitive(): void
    {
        $this->createFiles(['PAGE.HTML', 'Template.TPL']);

        $result = \XoopsLists::getHtmlListAsArray($this->tempDir);

        $this->assertCount(2, $result);
    }

    public function testGetHtmlListAsArrayWithPrefix(): void
    {
        $this->createFiles(['index.html']);

        $result = \XoopsLists::getHtmlListAsArray($this->tempDir, 'tpl/');

        $this->assertArrayHasKey('tpl/index.html', $result);
    }

    public function testGetHtmlListAsArrayReturnsEmptyOnNoMatch(): void
    {
        $this->createFiles(['image.png', 'data.csv']);

        $result = \XoopsLists::getHtmlListAsArray($this->tempDir);

        $this->assertEmpty($result);
    }

    public function testGetHtmlListAsArrayResultIsSorted(): void
    {
        $this->createFiles(['z.html', 'a.html', 'm.html']);

        $result = \XoopsLists::getHtmlListAsArray($this->tempDir);

        $values = array_values($result);
        $this->assertSame(['a.html', 'm.html', 'z.html'], $values);
    }

    // =========================================================================
    // getHtmlList (static HTML tag dictionary)
    // =========================================================================

    public function testGetHtmlListReturnsArray(): void
    {
        $result = \XoopsLists::getHtmlList();

        $this->assertIsArray($result);
    }

    public function testGetHtmlListContainsExpectedTags(): void
    {
        $result = \XoopsLists::getHtmlList();

        $expectedTags = ['a', 'b', 'br', 'div', 'em', 'h1', 'hr', 'img', 'li', 'ol', 'p', 'pre', 'span', 'strong', 'table', 'td', 'tr', 'ul'];

        foreach ($expectedTags as $tag) {
            $this->assertArrayHasKey($tag, $result, "Tag '{$tag}' should exist in HTML list");
        }
    }

    public function testGetHtmlListValuesAreHtmlEntities(): void
    {
        $result = \XoopsLists::getHtmlList();

        foreach ($result as $tag => $display) {
            $expected = '&lt;' . $tag . '&gt;';
            $this->assertSame($expected, $display, "Display value for '{$tag}' should be HTML-entity encoded");
        }
    }

    public function testGetHtmlListIsSorted(): void
    {
        $result = \XoopsLists::getHtmlList();

        $keys = array_keys($result);
        $sorted = $keys;
        sort($sorted);

        // After asort(), values are sorted but keys may differ
        $values = array_values($result);
        $sortedValues = $values;
        sort($sortedValues);
        $this->assertSame($sortedValues, $values, 'HTML list values should be sorted');
    }

    public function testGetHtmlListIsIdempotent(): void
    {
        $first  = \XoopsLists::getHtmlList();
        $second = \XoopsLists::getHtmlList();

        $this->assertSame($first, $second, 'Repeated calls must return the same result');
    }

    public function testGetHtmlListIsNotEmpty(): void
    {
        $result = \XoopsLists::getHtmlList();

        $this->assertNotEmpty($result);
        $this->assertGreaterThan(10, count($result), 'Should contain a reasonable number of HTML tags');
    }

    // =========================================================================
    // Mixed scenario tests
    // =========================================================================

    public function testGetDirListAndFileListAreDisjoint(): void
    {
        $this->createSubDirs(['subdir']);
        $this->createFiles(['file.txt']);

        $dirs  = \XoopsLists::getDirListAsArray($this->tempDir);
        $files = \XoopsLists::getFileListAsArray($this->tempDir);

        $this->assertEmpty(
            array_intersect($dirs, $files),
            'Directories and files should never overlap'
        );
    }

    public function testGetImgListAsArrayReturnsSubsetOfFileList(): void
    {
        $this->createFiles(['photo.jpg', 'readme.txt', 'icon.png']);

        $allFiles = \XoopsLists::getFileListAsArray($this->tempDir);
        $images   = \XoopsLists::getImgListAsArray($this->tempDir);

        foreach ($images as $img) {
            $this->assertContains($img, $allFiles, 'Every image should also appear in the full file list');
        }
    }

    /**
     * Verify that an empty directory returns an empty array from all list methods.
     */
    public function testAllListMethodsReturnEmptyForEmptyDirectory(): void
    {
        $empty = $this->tempDir . DIRECTORY_SEPARATOR . 'emptydir';
        mkdir($empty);

        $this->assertEmpty(\XoopsLists::getDirListAsArray($empty));
        $this->assertEmpty(\XoopsLists::getFileListAsArray($empty));
        $this->assertEmpty(\XoopsLists::getImgListAsArray($empty));
        $this->assertEmpty(\XoopsLists::getHtmlListAsArray($empty));
        $this->assertEmpty(\XoopsLists::getFileListByExtension($empty, ['txt']));
    }
}
