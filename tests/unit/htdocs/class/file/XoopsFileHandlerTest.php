<?php

declare(strict_types=1);

namespace xoopsfile;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use XoopsFile;
use XoopsFileHandler;
use XoopsFolderHandler;

/**
 * Comprehensive unit tests for XoopsFileHandler.
 *
 * XoopsFileHandler is a convenience class (derived from CakePHP's File class)
 * for reading, writing, and appending to files. It wraps common PHP filesystem
 * operations behind an OOP interface, with support for file locking, md5
 * checksums, safe filenames, and OS-aware line-ending normalization.
 *
 * Tested API:
 *   - __construct()     Initializes folder reference, name; optionally creates file
 *   - create()          Creates the file with touch()
 *   - open() / close()  Opens/closes file handle via fopen/fclose
 *   - read()            Reads file contents (file_get_contents or fread)
 *   - write()           Writes data to file via fwrite
 *   - append()          Appends data via write('a')
 *   - delete()          Deletes the file via unlink
 *   - info()            Returns pathinfo array
 *   - ext()             Returns file extension
 *   - name()            Returns filename without extension
 *   - safe()            Makes filename safe (replaces non-word chars)
 *   - md5()             Returns md5 checksum
 *   - pwd()             Returns full path
 *   - exists()          Checks if file exists
 *   - perms()           Returns file permissions
 *   - size()            Returns file size
 *   - writable()        Checks if file is writable
 *   - readable()        Checks if file is readable
 *   - executable()      Checks if file is executable
 *   - owner()           Returns file owner id
 *   - group()           Returns file group id
 *   - lastAccess()      Returns last access timestamp
 *   - lastChange()      Returns last modification timestamp
 *   - folder()          Returns the folder handler reference
 *   - prepare()         Normalizes line endings for current OS
 *   - offset()          Gets/sets file pointer position
 *   - __destruct()      Closes the file handle
 *
 */
#[CoversClass(XoopsFileHandler::class)]
class XoopsFileHandlerTest extends TestCase
{
    /**
     * Temporary directory for all test files.
     */
    private string $tempDir;

    /**
     * Path to the primary temporary test file.
     */
    private string $tempFile;

    /**
     * Additional temporary files to clean up.
     *
     * @var string[]
     */
    private array $additionalFiles = [];

    /**
     * Additional temporary directories to clean up.
     *
     * @var string[]
     */
    private array $additionalDirs = [];

    /**
     * Creates a temp directory and a test file with known content before each test.
     */
    protected function setUp(): void
    {
        // Ensure the handler classes are loaded
        XoopsFile::load('file');
        XoopsFile::load('folder');

        $this->tempDir  = sys_get_temp_dir() . '/xoops_file_test_' . uniqid('', true);
        // Normalize to forward slashes so XoopsFolderHandler::isAbsolute() works on Windows
        $this->tempDir  = str_replace('\\', '/', $this->tempDir);
        mkdir($this->tempDir, 0755, true);
        $this->tempFile = $this->tempDir . '/testfile.txt';
        file_put_contents($this->tempFile, 'Hello World');
    }

    /**
     * Recursively removes temp directory and all its contents after each test.
     */
    protected function tearDown(): void
    {
        // Clean additional tracked files
        foreach ($this->additionalFiles as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        // Clean primary temp dir recursively
        $this->removeDirectory($this->tempDir);

        // Clean additional tracked directories
        foreach (array_reverse($this->additionalDirs) as $dir) {
            $this->removeDirectory($dir);
        }
    }

    /**
     * Recursively removes a directory and all its contents.
     */
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = scandir($dir);
        if ($items === false) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }

    // ---------------------------------------------------------------
    //  Constructor tests
    // ---------------------------------------------------------------

    /**
     * Constructor with an existing file sets the name and folder properties.
     */
    public function testConstructorWithExistingFileSetsNameAndFolder(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);

        $this->assertSame('testfile.txt', $handler->name);
        $this->assertInstanceOf(XoopsFolderHandler::class, $handler->folder);
    }

    /**
     * Constructor with a non-existent file and create=true creates the file.
     */
    public function testConstructorWithCreateTrueCreatesFile(): void
    {
        $newFile = $this->tempDir . '/created_by_ctor.txt';
        $this->additionalFiles[] = $newFile;

        $handler = new XoopsFileHandler($newFile, true);

        $this->assertFileExists($newFile);
        $this->assertSame('created_by_ctor.txt', $handler->name);
    }

    /**
     * Constructor with a non-existent file and create=false does not create the file.
     */
    public function testConstructorWithCreateFalseDoesNotCreateFile(): void
    {
        $newFile = $this->tempDir . '/not_created.txt';

        $handler = new XoopsFileHandler($newFile, false);

        $this->assertFileDoesNotExist($newFile);
        $this->assertSame('not_created.txt', $handler->name);
    }

    /**
     * Constructor sets the folder property to a XoopsFolderHandler for the parent directory.
     */
    public function testConstructorSetsFolderToParentDirectory(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);

        $folderPath = $handler->folder->pwd();
        // Normalize directory separators for comparison
        $expected = str_replace('\\', '/', $this->tempDir);
        $actual   = str_replace('\\', '/', $folderPath);
        $this->assertSame($expected, $actual);
    }

    // ---------------------------------------------------------------
    //  read() tests
    // ---------------------------------------------------------------

    /**
     * read() returns file contents as a string.
     */
    public function testReadReturnsFileContents(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);

        $contents = $handler->read();
        $this->assertSame('Hello World', $contents);
    }

    /**
     * read(false) uses file_get_contents internally.
     */
    public function testReadFalseUsesFileGetContents(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);

        $contents = $handler->read(false);
        $this->assertSame('Hello World', $contents);
    }

    /**
     * read(int) reads exactly N bytes with fread.
     */
    public function testReadIntReadsNBytes(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);

        $contents = $handler->read(5, 'rb', true);
        $this->assertSame('Hello', $contents);
    }

    /**
     * read() on an empty file returns an empty string.
     */
    public function testReadEmptyFileReturnsEmptyString(): void
    {
        $emptyFile = $this->tempDir . '/empty.txt';
        file_put_contents($emptyFile, '');

        $handler  = new XoopsFileHandler($emptyFile);
        $contents = $handler->read();

        $this->assertSame('', $contents);
    }

    // ---------------------------------------------------------------
    //  write() tests
    // ---------------------------------------------------------------

    /**
     * write() writes data to the file and returns true.
     */
    public function testWriteWritesDataToFile(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $result  = $handler->write('New Content');

        $this->assertTrue($result);
        $handler->close();

        $this->assertSame('New Content', file_get_contents($this->tempFile));
    }

    /**
     * write() overwrites existing content by default (mode 'w').
     */
    public function testWriteOverwritesExistingContent(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $handler->write('Overwritten');
        $handler->close();

        $this->assertSame('Overwritten', file_get_contents($this->tempFile));
    }

    // ---------------------------------------------------------------
    //  append() tests
    // ---------------------------------------------------------------

    /**
     * append() appends data to the existing file content.
     */
    public function testAppendAppendsDataToFile(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $result  = $handler->append(' Appended');

        $this->assertTrue($result);
        $handler->close();

        $this->assertSame('Hello World Appended', file_get_contents($this->tempFile));
    }

    /**
     * Multiple append calls accumulate content.
     */
    public function testAppendMultipleTimes(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $handler->append(' One');
        $handler->close();

        $handler2 = new XoopsFileHandler($this->tempFile);
        $handler2->append(' Two');
        $handler2->close();

        $this->assertSame('Hello World One Two', file_get_contents($this->tempFile));
    }

    // ---------------------------------------------------------------
    //  open() and close() tests
    // ---------------------------------------------------------------

    /**
     * open() returns true when a file is successfully opened.
     */
    public function testOpenReturnsTrueOnSuccess(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $result  = $handler->open('r');

        $this->assertTrue($result);
        $handler->close();
    }

    /**
     * close() returns true on a previously opened handle.
     */
    public function testCloseReturnsTrueAfterOpen(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $handler->open('r');

        $result = $handler->close();
        $this->assertTrue($result);
    }

    /**
     * close() returns true when called on an already-closed (no handle) handler.
     */
    public function testCloseReturnsTrueWhenNoHandleOpen(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);

        $result = $handler->close();
        $this->assertTrue($result);
    }

    /**
     * open() without force does not re-open an already opened handle.
     */
    public function testOpenWithoutForceDoesNotReopen(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $handler->open('r');
        $handle1 = $handler->handle;

        $handler->open('r', false);
        $handle2 = $handler->handle;

        // Same resource, not re-opened
        $this->assertSame($handle1, $handle2);
        $handler->close();
    }

    // ---------------------------------------------------------------
    //  delete() tests
    // ---------------------------------------------------------------

    /**
     * delete() removes the file and returns true.
     */
    public function testDeleteRemovesFile(): void
    {
        $deleteFile = $this->tempDir . '/to_delete.txt';
        file_put_contents($deleteFile, 'delete me');

        $handler = new XoopsFileHandler($deleteFile);
        $result  = $handler->delete();

        $this->assertTrue($result);
        $this->assertFileDoesNotExist($deleteFile);
    }

    /**
     * exists() returns true for an existing file.
     */
    public function testExistsReturnsTrueForExistingFile(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $this->assertTrue($handler->exists());
    }

    /**
     * exists() returns false after the file has been deleted.
     */
    public function testExistsReturnsFalseAfterDelete(): void
    {
        $deleteFile = $this->tempDir . '/exists_test.txt';
        file_put_contents($deleteFile, 'test');

        $handler = new XoopsFileHandler($deleteFile);
        $handler->delete();

        $this->assertFalse($handler->exists());
    }

    // ---------------------------------------------------------------
    //  pwd() tests
    // ---------------------------------------------------------------

    /**
     * pwd() returns the full path to the file.
     */
    public function testPwdReturnsFullPath(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $pwd     = $handler->pwd();

        // Normalize separators for cross-platform comparison
        $expected = str_replace('\\', '/', $this->tempFile);
        $actual   = str_replace('\\', '/', $pwd);
        $this->assertSame($expected, $actual);
    }

    // ---------------------------------------------------------------
    //  info() tests
    // ---------------------------------------------------------------

    /**
     * info() returns a pathinfo array with all standard keys.
     */
    public function testInfoReturnsPathinfoArray(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $info    = $handler->info();

        $this->assertIsArray($info);
        $this->assertArrayHasKey('dirname', $info);
        $this->assertArrayHasKey('basename', $info);
        $this->assertArrayHasKey('extension', $info);
        $this->assertArrayHasKey('filename', $info);
    }

    /**
     * info() returns correct extension value.
     */
    public function testInfoExtensionIsCorrect(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $info    = $handler->info();

        $this->assertSame('txt', $info['extension']);
    }

    /**
     * info() returns correct basename.
     */
    public function testInfoBasenameIsCorrect(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $info    = $handler->info();

        $this->assertSame('testfile.txt', $info['basename']);
    }

    // ---------------------------------------------------------------
    //  ext() tests
    // ---------------------------------------------------------------

    /**
     * ext() returns the file extension.
     */
    public function testExtReturnsFileExtension(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $this->assertSame('txt', $handler->ext());
    }

    /**
     * ext() returns the correct extension for a .php file.
     */
    public function testExtReturnsPhpExtension(): void
    {
        $phpFile = $this->tempDir . '/script.php';
        file_put_contents($phpFile, '<?php');

        $handler = new XoopsFileHandler($phpFile);
        $this->assertSame('php', $handler->ext());
    }

    // ---------------------------------------------------------------
    //  name() tests
    // ---------------------------------------------------------------

    /**
     * name() returns filename without extension.
     */
    public function testNameReturnsFilenameWithoutExtension(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $this->assertSame('testfile', $handler->name());
    }

    /**
     * name() handles multi-dot filenames correctly.
     */
    public function testNameWithMultipleDots(): void
    {
        $dotFile = $this->tempDir . '/my.config.json';
        file_put_contents($dotFile, '{}');

        $handler = new XoopsFileHandler($dotFile);
        $this->assertSame('my.config', $handler->name());
    }

    // ---------------------------------------------------------------
    //  safe() tests
    // ---------------------------------------------------------------

    /**
     * safe() replaces non-word characters with underscores.
     */
    public function testSafeReplacesUnsafeCharacters(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);

        $result = $handler->safe('file name (copy).txt', '.txt');
        $this->assertSame('file_name_copy_', $result);
    }

    /**
     * safe() with no arguments uses the handler's own name.
     */
    public function testSafeUsesHandlerName(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $result  = $handler->safe();

        // 'testfile.txt' with ext 'txt' => safe('testfile.txt', 'txt')
        // basename('testfile.txt', 'txt') => 'testfile.' => preg_replace => 'testfile_'
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * safe() with a custom name parameter.
     */
    public function testSafeWithCustomNameParameter(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $result  = $handler->safe('my-file_v2.txt', '.txt');

        // 'my-file_v2' after basename removal, then preg_replace /[^\w\.-]+/ => '_'
        $this->assertSame('my-file_v2', $result);
    }

    /**
     * safe() preserves already-safe characters.
     */
    public function testSafePreservesSafeCharacters(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $result  = $handler->safe('safe_file-name.log', '.log');

        $this->assertSame('safe_file-name', $result);
    }

    // ---------------------------------------------------------------
    //  size() tests
    // ---------------------------------------------------------------

    /**
     * size() returns the file size in bytes.
     */
    public function testSizeReturnsFileSizeInBytes(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $size    = $handler->size();

        $this->assertSame(strlen('Hello World'), $size);
    }

    /**
     * size() returns 0 for an empty file.
     */
    public function testSizeReturnsZeroForEmptyFile(): void
    {
        $emptyFile = $this->tempDir . '/empty_size.txt';
        file_put_contents($emptyFile, '');

        $handler = new XoopsFileHandler($emptyFile);
        // clear stat cache so size reflects current state
        clearstatcache(true, $emptyFile);
        $this->assertSame(0, $handler->size());
    }

    // ---------------------------------------------------------------
    //  perms() tests
    // ---------------------------------------------------------------

    /**
     * perms() returns a permission string for an existing file.
     */
    public function testPermsReturnsPermissionString(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $perms   = $handler->perms();

        $this->assertIsString($perms);
        $this->assertSame(4, strlen($perms), 'Permissions should be a 4-character octal string');
    }

    /**
     * perms() returns false for a non-existent file.
     */
    public function testPermsReturnsFalseForNonExistentFile(): void
    {
        $handler = new XoopsFileHandler($this->tempDir . '/nonexistent.txt', false);
        $this->assertFalse($handler->perms());
    }

    // ---------------------------------------------------------------
    //  writable() / readable() / executable() tests
    // ---------------------------------------------------------------

    /**
     * writable() returns true for a writable file.
     */
    public function testWritableReturnsTrueForWritableFile(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $this->assertTrue($handler->writable());
    }

    /**
     * readable() returns true for a readable file.
     */
    public function testReadableReturnsTrueForReadableFile(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $this->assertTrue($handler->readable());
    }

    /**
     * executable() returns a boolean value.
     */
    public function testExecutableReturnsBoolean(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $this->assertIsBool($handler->executable());
    }

    // ---------------------------------------------------------------
    //  owner() and group() tests
    // ---------------------------------------------------------------

    /**
     * owner() returns the file owner as an integer.
     */
    public function testOwnerReturnsInteger(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $owner   = $handler->owner();

        $this->assertIsInt($owner);
    }

    /**
     * owner() returns false for a non-existent file.
     */
    public function testOwnerReturnsFalseForNonExistentFile(): void
    {
        $handler = new XoopsFileHandler($this->tempDir . '/no_owner.txt', false);
        $this->assertFalse($handler->owner());
    }

    /**
     * group() returns the file group as an integer.
     */
    public function testGroupReturnsInteger(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $group   = $handler->group();

        $this->assertIsInt($group);
    }

    /**
     * group() returns false for a non-existent file.
     */
    public function testGroupReturnsFalseForNonExistentFile(): void
    {
        $handler = new XoopsFileHandler($this->tempDir . '/no_group.txt', false);
        $this->assertFalse($handler->group());
    }

    // ---------------------------------------------------------------
    //  lastAccess() and lastChange() tests
    // ---------------------------------------------------------------

    /**
     * lastAccess() returns a timestamp (integer) for an existing file.
     */
    public function testLastAccessReturnsTimestamp(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $ts      = $handler->lastAccess();

        $this->assertIsInt($ts);
        $this->assertGreaterThan(0, $ts);
    }

    /**
     * lastAccess() returns false for a non-existent file.
     */
    public function testLastAccessReturnsFalseForNonExistentFile(): void
    {
        $handler = new XoopsFileHandler($this->tempDir . '/no_access.txt', false);
        $this->assertFalse($handler->lastAccess());
    }

    /**
     * lastChange() returns a timestamp (integer) for an existing file.
     */
    public function testLastChangeReturnsTimestamp(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $ts      = $handler->lastChange();

        $this->assertIsInt($ts);
        $this->assertGreaterThan(0, $ts);
    }

    /**
     * lastChange() returns false for a non-existent file.
     */
    public function testLastChangeReturnsFalseForNonExistentFile(): void
    {
        $handler = new XoopsFileHandler($this->tempDir . '/no_change.txt', false);
        $this->assertFalse($handler->lastChange());
    }

    // ---------------------------------------------------------------
    //  md5() tests
    // ---------------------------------------------------------------

    /**
     * md5(true) forces checksum calculation regardless of file size.
     */
    public function testMd5TrueReturnsChecksumRegardlessOfSize(): void
    {
        $handler  = new XoopsFileHandler($this->tempFile);
        $checksum = $handler->md5(true);

        $this->assertIsString($checksum);
        $this->assertSame(32, strlen($checksum), 'MD5 hash should be 32 hex characters');
        $this->assertSame(md5_file($this->tempFile), $checksum);
    }

    /**
     * md5() returns checksum for small files (below default maxsize).
     */
    public function testMd5ReturnsChecksumForSmallFiles(): void
    {
        $handler  = new XoopsFileHandler($this->tempFile);
        $checksum = $handler->md5();

        $this->assertIsString($checksum);
        $this->assertSame(md5('Hello World'), $checksum);
    }

    /**
     * md5() with a very small maxsize returns false for files larger than maxsize.
     *
     * The maxsize is in MB. We create a file just over the threshold to test.
     * Using maxsize of 0 means threshold is 0 bytes, so any non-empty file fails.
     */
    public function testMd5ReturnsFalseForFilesLargerThanMaxsize(): void
    {
        // Create a file with some content
        $largeFile = $this->tempDir . '/large.bin';
        file_put_contents($largeFile, str_repeat('X', 100));

        $handler = new XoopsFileHandler($largeFile);

        // maxsize=0 => threshold is 0 * 1024 * 1024 = 0 bytes
        // The condition is: $size < ($maxsize * 1024) * 1024
        // With maxsize=0: $size < 0 is always false for non-empty files
        // But $size could be 0 for empty files. Let's use a value that's clearly under.
        // Actually, md5(0) => $size < 0 always false, so returns false.
        $result = $handler->md5(0);

        $this->assertFalse($result);
    }

    /**
     * md5() returns the correct hash value for known content.
     */
    public function testMd5ReturnsCorrectHash(): void
    {
        $knownContent = 'The quick brown fox jumps over the lazy dog';
        $knownFile    = $this->tempDir . '/known_md5.txt';
        file_put_contents($knownFile, $knownContent);

        $handler = new XoopsFileHandler($knownFile);
        $hash    = $handler->md5(true);

        $this->assertSame(md5($knownContent), $hash);
    }

    // ---------------------------------------------------------------
    //  prepare() tests
    // ---------------------------------------------------------------

    /**
     * prepare() normalizes line endings based on the current OS.
     */
    public function testPrepareNormalizesLineEndings(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);

        $input = "line1\nline2\r\nline3\rline4";
        $result = $handler->prepare($input);

        // On Windows (which is the test environment), all line breaks
        // should be converted to \r\n
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            $expected = "line1\r\nline2\r\nline3\r\nline4";
        } else {
            $expected = "line1\nline2\nline3\nline4";
        }

        $this->assertSame($expected, $result);
    }

    /**
     * prepare() preserves content without line breaks.
     */
    public function testPreparePreservesContentWithoutLineBreaks(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $input   = 'No line breaks here';
        $result  = $handler->prepare($input);

        $this->assertSame($input, $result);
    }

    /**
     * prepare() handles empty string.
     */
    public function testPrepareHandlesEmptyString(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $this->assertSame('', $handler->prepare(''));
    }

    // ---------------------------------------------------------------
    //  folder() tests
    // ---------------------------------------------------------------

    /**
     * folder() returns a XoopsFolderHandler instance.
     */
    public function testFolderReturnsXoopsFolderHandlerInstance(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $folder  = $handler->folder();

        $this->assertInstanceOf(XoopsFolderHandler::class, $folder);
    }

    /**
     * folder() returns a reference to the same folder object as the property.
     */
    public function testFolderReturnsSameAsProperty(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $folder  = &$handler->folder();

        $this->assertSame($handler->folder, $folder);
    }

    // ---------------------------------------------------------------
    //  offset() tests
    // ---------------------------------------------------------------

    /**
     * offset(false) returns current file pointer position when handle is open.
     */
    public function testOffsetFalseReturnsCurrentPosition(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $handler->open('r');

        $position = $handler->offset(false);

        $this->assertSame(0, $position);
        $handler->close();
    }

    /**
     * offset(int) seeks to the specified position.
     */
    public function testOffsetIntSeeksToPosition(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $handler->open('r');

        $result = $handler->offset(5);
        $this->assertTrue($result);

        $position = $handler->offset(false);
        $this->assertSame(5, $position);

        $handler->close();
    }

    /**
     * offset(false) returns false when no handle is open.
     */
    public function testOffsetFalseReturnsFalseWithNoHandle(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        // Do not open the file — handle is not a resource
        $result = $handler->offset(false);

        $this->assertFalse($result);
    }

    /**
     * offset() with a position opens the file automatically if not open.
     */
    public function testOffsetWithPositionOpensFileAutomatically(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);

        // File is not opened yet; offset should call open() internally
        $result = $handler->offset(3);
        $this->assertTrue($result);

        $position = $handler->offset(false);
        $this->assertSame(3, $position);

        $handler->close();
    }

    // ---------------------------------------------------------------
    //  create() tests
    // ---------------------------------------------------------------

    /**
     * create() creates a new file via touch() and returns true.
     */
    public function testCreateCreatesNewFile(): void
    {
        $newFile = $this->tempDir . '/touch_created.txt';
        // Initialize handler without creating the file
        $handler = new XoopsFileHandler($newFile, false);

        $result = $handler->create();

        $this->assertTrue($result);
        $this->assertFileExists($newFile);
    }

    /**
     * create() returns false if the file already exists.
     */
    public function testCreateReturnsFalseIfFileAlreadyExists(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);

        $result = $handler->create();
        $this->assertFalse($result);
    }

    // ---------------------------------------------------------------
    //  delete() additional test — returns false for non-existent file
    // ---------------------------------------------------------------

    /**
     * delete() returns false when the file does not exist.
     */
    public function testDeleteReturnsFalseForNonExistentFile(): void
    {
        $handler = new XoopsFileHandler($this->tempDir . '/phantom.txt', false);
        $this->assertFalse($handler->delete());
    }

    // ---------------------------------------------------------------
    //  Integrated read/write cycle tests
    // ---------------------------------------------------------------

    /**
     * A full write-then-read cycle returns the written data.
     */
    public function testWriteThenReadCycle(): void
    {
        $cycleFile = $this->tempDir . '/cycle.txt';
        file_put_contents($cycleFile, '');

        $writer = new XoopsFileHandler($cycleFile);
        $writer->write('Cycle Test Data');
        $writer->close();

        $reader  = new XoopsFileHandler($cycleFile);
        $content = $reader->read();
        $reader->close();

        $this->assertSame('Cycle Test Data', $content);
    }

    /**
     * Write, append, then read returns combined content.
     */
    public function testWriteAppendReadCycle(): void
    {
        $cycleFile = $this->tempDir . '/append_cycle.txt';
        file_put_contents($cycleFile, '');

        $handler = new XoopsFileHandler($cycleFile);
        $handler->write('First');
        $handler->close();

        $handler2 = new XoopsFileHandler($cycleFile);
        $handler2->append(' Second');
        $handler2->close();

        $handler3 = new XoopsFileHandler($cycleFile);
        $content = $handler3->read();
        $handler3->close();

        $this->assertSame('First Second', $content);
    }

    // ---------------------------------------------------------------
    //  size() after write
    // ---------------------------------------------------------------

    /**
     * size() returns updated size after writing new content.
     */
    public function testSizeReflectsWrittenContent(): void
    {
        $sizeFile = $this->tempDir . '/size_write.txt';
        file_put_contents($sizeFile, '');

        $handler = new XoopsFileHandler($sizeFile);
        $handler->write('12345');
        $handler->close();

        clearstatcache(true, $sizeFile);
        $handler2 = new XoopsFileHandler($sizeFile);
        $this->assertSame(5, $handler2->size());
    }

    // ---------------------------------------------------------------
    //  exists() edge cases
    // ---------------------------------------------------------------

    /**
     * exists() returns false for a path that is actually a directory, not a file.
     */
    public function testExistsReturnsFalseForDirectory(): void
    {
        // XoopsFileHandler with a directory path: name won't be set (is_dir check)
        // and exists() checks is_file(), so it should be false.
        // But the constructor skips setting name when is_dir is true.
        // We test the edge case where pwd() would point to a dir.
        $subDir = $this->tempDir . '/subdir';
        mkdir($subDir, 0755, true);

        $handler = new XoopsFileHandler($subDir, false);
        // name is not set because is_dir($path) is true in constructor
        // exists() calls file_exists() && is_file() on pwd()
        // This is an edge case: the handler's pwd() might not be valid for exists()
        $this->assertFalse($handler->exists());
    }

    // ---------------------------------------------------------------
    //  info() caching behavior
    // ---------------------------------------------------------------

    /**
     * info() caches its result — subsequent calls return the same array.
     */
    public function testInfoIsCached(): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $info1   = $handler->info();
        $info2   = $handler->info();

        $this->assertSame($info1, $info2);
    }

    // ---------------------------------------------------------------
    //  Data providers
    // ---------------------------------------------------------------

    /**
     * Provides unsafe filenames and their expected safe versions.
     *
     * @return array<string, array{string, string, string}>
     */
    public static function unsafeFilenameProvider(): array
    {
        return [
            'spaces'          => ['my file.txt', '.txt', 'my_file'],
            'special chars'   => ['file@#$.txt', '.txt', 'file_'],
            'parentheses'     => ['copy (1).txt', '.txt', 'copy_1_'],
            'already safe'    => ['safe_file.txt', '.txt', 'safe_file'],
            'hyphens allowed' => ['my-file.txt', '.txt', 'my-file'],
        ];
    }

    /**
     * Tests safe() with various unsafe filenames via data provider.
     */
    #[DataProvider('unsafeFilenameProvider')]
    public function testSafeWithDataProvider(string $name, string $ext, string $expected): void
    {
        $handler = new XoopsFileHandler($this->tempFile);
        $result  = $handler->safe($name, $ext);

        $this->assertSame($expected, $result);
    }
}
