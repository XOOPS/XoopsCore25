<?php

declare(strict_types=1);

namespace xoopsclass;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once XOOPS_ROOT_PATH . '/class/class.zipfile.php';

/**
 * Unit tests for Zipfile.
 */
#[CoversClass(\Zipfile::class)]
class ZipfileTest extends TestCase
{
    /** @var \Zipfile */
    private $zip;

    protected function setUp(): void
    {
        $this->zip = new \Zipfile();
    }

    // =========================================================================
    // unix2DosTime
    // =========================================================================

    public function testUnix2DosTimeReturnsIntegerForZero(): void
    {
        $result = $this->zip->unix2DosTime(0);

        $this->assertIsInt($result);
    }

    public function testUnix2DosTimeWithZeroUsesCurrentTime(): void
    {
        // When 0 is passed, the method uses getdate() (current time).
        // We just verify it returns a positive integer (not clamped to 1980).
        $result = $this->zip->unix2DosTime(0);

        $this->assertGreaterThan(0, $result);
    }

    public function testUnix2DosTimeWithKnownTimestamp(): void
    {
        // 2025-01-01 00:00:00 UTC
        $timestamp = mktime(0, 0, 0, 1, 1, 2025);
        $result    = $this->zip->unix2DosTime($timestamp);

        $this->assertIsInt($result);

        // Decode the DOS time back to verify year component
        // Year is stored in bits 25-31 as (year - 1980)
        $yearOffset = ($result >> 25) & 0x7F;
        $this->assertSame(2025 - 1980, $yearOffset);

        // Month is bits 21-24
        $month = ($result >> 21) & 0x0F;
        $this->assertSame(1, $month);

        // Day is bits 16-20
        $day = ($result >> 16) & 0x1F;
        $this->assertSame(1, $day);
    }

    public function testUnix2DosTimeBeforeFeb1980ClampsToJan1980(): void
    {
        // Timestamp before 1980: 1970-06-15
        $timestamp = mktime(0, 0, 0, 6, 15, 1970);
        $result    = $this->zip->unix2DosTime($timestamp);

        // Should be clamped: year=1980, month=1, day=1, 00:00:00
        $yearOffset = ($result >> 25) & 0x7F;
        $month      = ($result >> 21) & 0x0F;
        $day        = ($result >> 16) & 0x1F;
        $hours      = ($result >> 11) & 0x1F;
        $minutes    = ($result >> 5) & 0x3F;

        $this->assertSame(0, $yearOffset, 'Year should be 1980 (offset 0)');
        $this->assertSame(1, $month);
        $this->assertSame(1, $day);
        $this->assertSame(0, $hours);
        $this->assertSame(0, $minutes);
    }

    public function testUnix2DosTimeEpochClampsTo1980(): void
    {
        // Unix epoch (1970-01-01 00:00:01) is before 1980
        $result = $this->zip->unix2DosTime(1);

        $yearOffset = ($result >> 25) & 0x7F;
        $this->assertSame(0, $yearOffset, 'Pre-1980 timestamps should clamp to 1980');
    }

    public function testUnix2DosTimeExactly1980ReturnsValidDosTime(): void
    {
        $timestamp = mktime(0, 0, 0, 1, 1, 1980);
        $result    = $this->zip->unix2DosTime($timestamp);

        $yearOffset = ($result >> 25) & 0x7F;
        $this->assertSame(0, $yearOffset, 'Year 1980 should have offset 0');
    }

    public function testUnix2DosTimeWithTimeComponents(): void
    {
        // 2020-06-15 14:30:10
        $timestamp = mktime(14, 30, 10, 6, 15, 2020);
        $result    = $this->zip->unix2DosTime($timestamp);

        $hours   = ($result >> 11) & 0x1F;
        $minutes = ($result >> 5) & 0x3F;
        // Seconds are stored as seconds/2
        $seconds2 = $result & 0x1F;

        $this->assertSame(14, $hours);
        $this->assertSame(30, $minutes);
        $this->assertSame(5, $seconds2, 'Seconds 10 stored as 10>>1 = 5');
    }

    public function testUnix2DosTimeReturnsDifferentValuesForDifferentDates(): void
    {
        $t1 = mktime(0, 0, 0, 1, 1, 2000);
        $t2 = mktime(0, 0, 0, 6, 15, 2020);

        $this->assertNotSame(
            $this->zip->unix2DosTime($t1),
            $this->zip->unix2DosTime($t2)
        );
    }

    // =========================================================================
    // addFile
    // =========================================================================

    public function testAddFileIncrementsDatasecCount(): void
    {
        $this->assertCount(0, $this->zip->datasec);

        $this->zip->addFile('hello', 'hello.txt');

        $this->assertCount(1, $this->zip->datasec);
    }

    public function testAddFileMultipleCallsGrowDatasec(): void
    {
        $this->zip->addFile('aaa', 'a.txt');
        $this->zip->addFile('bbb', 'b.txt');
        $this->zip->addFile('ccc', 'c.txt');

        $this->assertCount(3, $this->zip->datasec);
        $this->assertCount(3, $this->zip->ctrl_dir);
    }

    public function testAddFileConvertsBackslashesToForwardSlashes(): void
    {
        $this->zip->addFile('data', 'path\\to\\file.txt');

        // The filename appears in the local file header stored in datasec
        $localHeader = $this->zip->datasec[0];

        // The filename "path/to/file.txt" must be present (forward slashes)
        $this->assertNotFalse(
            strpos($localHeader, 'path/to/file.txt'),
            'Backslashes should be converted to forward slashes'
        );
        $this->assertFalse(
            strpos($localHeader, 'path\\to\\file.txt'),
            'No backslashes should remain in the stored filename'
        );
    }

    public function testAddFileLocalHeaderStartsWithPkSignature(): void
    {
        $this->zip->addFile('test', 'test.txt');

        $header = $this->zip->datasec[0];
        $this->assertStringStartsWith("\x50\x4b\x03\x04", $header);
    }

    public function testAddFileCentralDirStartsWithPkSignature(): void
    {
        $this->zip->addFile('test', 'test.txt');

        $cdr = $this->zip->ctrl_dir[0];
        $this->assertStringStartsWith("\x50\x4b\x01\x02", $cdr);
    }

    public function testAddFileWithEmptyData(): void
    {
        $this->zip->addFile('', 'empty.txt');

        $this->assertCount(1, $this->zip->datasec);

        // Empty file should still produce valid ZIP output
        $output = $this->zip->file();
        $this->assertNotEmpty($output);
    }

    public function testAddFileWithTimestamp(): void
    {
        $timestamp = mktime(12, 0, 0, 6, 15, 2020);
        $this->zip->addFile('data', 'dated.txt', $timestamp);

        $this->assertCount(1, $this->zip->datasec);
    }

    // =========================================================================
    // file (ZIP output generation)
    // =========================================================================

    public function testFileReturnsStringStartingWithPkSignature(): void
    {
        $this->zip->addFile('Hello, World!', 'hello.txt');

        $output = $this->zip->file();

        $this->assertIsString($output);
        $this->assertStringStartsWith("\x50\x4b\x03\x04", $output);
    }

    public function testFileOutputIsNonEmptyAfterAddFile(): void
    {
        $this->zip->addFile('content', 'file.txt');

        $output = $this->zip->file();

        $this->assertNotEmpty($output);
    }

    public function testEmptyZipHasProperStructure(): void
    {
        // No files added — should still have end-of-central-directory record
        $output = $this->zip->file();

        $this->assertIsString($output);
        // The EOCD signature should be present
        $this->assertNotFalse(
            strpos($output, "\x50\x4b\x05\x06"),
            'Empty ZIP should contain end-of-central-directory signature'
        );
    }

    public function testZipWithOneFileContainsFilename(): void
    {
        $this->zip->addFile('some data', 'myfile.txt');

        $output = $this->zip->file();

        $this->assertNotFalse(
            strpos($output, 'myfile.txt'),
            'ZIP output should contain the filename'
        );
    }

    public function testZipWithMultipleFilesContainsAllFilenames(): void
    {
        $this->zip->addFile('data1', 'first.txt');
        $this->zip->addFile('data2', 'second.txt');
        $this->zip->addFile('data3', 'third.txt');

        $output = $this->zip->file();

        $this->assertNotFalse(strpos($output, 'first.txt'));
        $this->assertNotFalse(strpos($output, 'second.txt'));
        $this->assertNotFalse(strpos($output, 'third.txt'));
    }

    public function testZipOutputGrowsWithMoreFiles(): void
    {
        $this->zip->addFile('a', 'a.txt');
        $sizeOne = strlen($this->zip->file());

        $this->zip->addFile('b', 'b.txt');
        $sizeTwo = strlen($this->zip->file());

        $this->assertGreaterThan($sizeOne, $sizeTwo);
    }

    public function testZipContainsEndOfCentralDirectorySignature(): void
    {
        $this->zip->addFile('test', 'test.txt');

        $output = $this->zip->file();

        $this->assertNotFalse(
            strpos($output, "\x50\x4b\x05\x06"),
            'ZIP should contain EOCD signature'
        );
    }

    public function testZipContainsCentralDirectorySignature(): void
    {
        $this->zip->addFile('test', 'test.txt');

        $output = $this->zip->file();

        // Central directory file header signature
        $this->assertNotFalse(
            strpos($output, "\x50\x4b\x01\x02"),
            'ZIP should contain central directory header signature'
        );
    }

    public function testNewInstanceHasEmptyDatasec(): void
    {
        $fresh = new \Zipfile();

        $this->assertIsArray($fresh->datasec);
        $this->assertEmpty($fresh->datasec);
    }

    public function testNewInstanceHasEmptyCtrlDir(): void
    {
        $fresh = new \Zipfile();

        $this->assertIsArray($fresh->ctrl_dir);
        $this->assertEmpty($fresh->ctrl_dir);
    }

    public function testNewInstanceOldOffsetIsZero(): void
    {
        $fresh = new \Zipfile();

        $this->assertSame(0, $fresh->old_offset);
    }

    // =========================================================================
    // Data provider for filenames
    // =========================================================================

    /**
     * @return array<string, array{string, string}>
     */
    public static function filenameProvider(): array
    {
        return [
            'simple filename'        => ['hello.txt', 'hello.txt'],
            'path with slashes'      => ['dir/sub/file.txt', 'dir/sub/file.txt'],
            'backslash path'         => ['dir\\sub\\file.txt', 'dir/sub/file.txt'],
            'mixed slashes'          => ['dir\\sub/file.txt', 'dir/sub/file.txt'],
            'unicode filename'       => ['data_file.txt', 'data_file.txt'],
            'filename with spaces'   => ['my file.txt', 'my file.txt'],
        ];
    }

    #[DataProvider('filenameProvider')]
    public function testAddFileStoresCorrectFilename(string $input, string $expected): void
    {
        $this->zip->addFile('test', $input);

        $output = $this->zip->file();

        $this->assertNotFalse(
            strpos($output, $expected),
            "ZIP output should contain normalized filename '{$expected}'"
        );
    }
}
