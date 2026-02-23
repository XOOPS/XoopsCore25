<?php

declare(strict_types=1);

namespace xoopsfile;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use XoopsFile;
use XoopsFileHandler;
use XoopsFolderHandler;

/**
 * Comprehensive unit tests for XoopsFile factory class.
 *
 * XoopsFile is a factory / service locator that loads and instantiates
 * the XoopsFileHandler and XoopsFolderHandler classes on demand. It provides
 * a singleton getInstance(), a load() method to include class files, and a
 * getHandler() method to create handler instances.
 *
 * Tested API:
 *   - __construct()    Can be called without error
 *   - getInstance()    Returns a singleton XoopsFile instance
 *   - load()           Loads class files for 'file' or 'folder' handlers
 *   - getHandler()     Creates and returns handler instances
 *
 */
#[CoversClass(XoopsFile::class)]
class XoopsFileTest extends TestCase
{
    /**
     * Temporary files created during tests, cleaned up in tearDown().
     *
     * @var string[]
     */
    private array $tempFiles = [];

    /**
     * Temporary directories created during tests, cleaned up in tearDown().
     *
     * @var string[]
     */
    private array $tempDirs = [];

    /**
     * Clean up all temporary files and directories after each test.
     */
    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
        // Remove dirs in reverse order (deepest first)
        foreach (array_reverse($this->tempDirs) as $dir) {
            if (is_dir($dir)) {
                @rmdir($dir);
            }
        }
    }

    // ---------------------------------------------------------------
    //  Constructor tests
    // ---------------------------------------------------------------

    /**
     * The constructor can be called without error and produces an XoopsFile instance.
     */
    public function testConstructorCanBeCalled(): void
    {
        $file = new XoopsFile();
        $this->assertInstanceOf(XoopsFile::class, $file);
    }

    // ---------------------------------------------------------------
    //  getInstance() tests
    // ---------------------------------------------------------------

    /**
     * getInstance() returns an XoopsFile instance.
     */
    public function testGetInstanceReturnsXoopsFileInstance(): void
    {
        $instance = (new XoopsFile())->getInstance();
        $this->assertInstanceOf(XoopsFile::class, $instance);
    }

    /**
     * getInstance() returns the same instance on consecutive calls (singleton).
     */
    public function testGetInstanceReturnsSameSingleton(): void
    {
        $first  = (new XoopsFile())->getInstance();
        $second = (new XoopsFile())->getInstance();
        $this->assertSame($first, $second, 'getInstance() should return the same singleton instance');
    }

    /**
     * getInstance() returns an object that is itself an XoopsFile.
     */
    public function testGetInstanceReturnTypeIsXoopsFile(): void
    {
        $instance = (new XoopsFile())->getInstance();
        $this->assertTrue($instance instanceof XoopsFile);
    }

    // ---------------------------------------------------------------
    //  load() tests
    // ---------------------------------------------------------------

    /**
     * load('file') returns true and makes the XoopsFileHandler class available.
     */
    public function testLoadFileReturnsTrue(): void
    {
        $result = XoopsFile::load('file');
        $this->assertTrue($result, 'load("file") should return true');
    }

    /**
     * After load('file'), the XoopsFileHandler class should exist.
     */
    public function testLoadFileMakesXoopsFileHandlerAvailable(): void
    {
        XoopsFile::load('file');
        $this->assertTrue(
            class_exists('XoopsFileHandler', false),
            'XoopsFileHandler class should be available after load("file")'
        );
    }

    /**
     * load('folder') returns true and makes the XoopsFolderHandler class available.
     */
    public function testLoadFolderReturnsTrue(): void
    {
        $result = XoopsFile::load('folder');
        $this->assertTrue($result, 'load("folder") should return true');
    }

    /**
     * After load('folder'), the XoopsFolderHandler class should exist.
     */
    public function testLoadFolderMakesXoopsFolderHandlerAvailable(): void
    {
        XoopsFile::load('folder');
        $this->assertTrue(
            class_exists('XoopsFolderHandler', false),
            'XoopsFolderHandler class should be available after load("folder")'
        );
    }

    /**
     * load() with no argument defaults to 'file' and returns true.
     */
    public function testLoadDefaultsToFile(): void
    {
        $result = XoopsFile::load();
        $this->assertTrue($result, 'load() with no argument should default to "file" and return true');
    }

    /**
     * Calling load('file') multiple times still returns true (idempotent).
     */
    public function testLoadFileIsIdempotent(): void
    {
        $first  = XoopsFile::load('file');
        $second = XoopsFile::load('file');
        $this->assertTrue($first);
        $this->assertTrue($second);
    }

    /**
     * Calling load('folder') multiple times still returns true (idempotent).
     */
    public function testLoadFolderIsIdempotent(): void
    {
        $first  = XoopsFile::load('folder');
        $second = XoopsFile::load('folder');
        $this->assertTrue($first);
        $this->assertTrue($second);
    }

    // ---------------------------------------------------------------
    //  getHandler() tests
    // ---------------------------------------------------------------

    /**
     * getHandler('file', ...) returns an XoopsFileHandler instance.
     */
    public function testGetHandlerFileReturnsXoopsFileHandler(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'xoops_test_');
        $this->tempFiles[] = $tmpFile;

        $handler = XoopsFile::getHandler('file', $tmpFile);
        $this->assertInstanceOf(XoopsFileHandler::class, $handler);
    }

    /**
     * getHandler('folder', ...) returns an XoopsFolderHandler instance.
     */
    public function testGetHandlerFolderReturnsXoopsFolderHandler(): void
    {
        $tmpDir = sys_get_temp_dir() . '/xoops_test_folder_' . uniqid('', true);
        mkdir($tmpDir, 0755, true);
        $this->tempDirs[] = $tmpDir;

        $handler = XoopsFile::getHandler('folder', $tmpDir);
        $this->assertInstanceOf(XoopsFolderHandler::class, $handler);
    }

    /**
     * getHandler() with no arguments defaults to 'file' handler and returns XoopsFileHandler.
     */
    public function testGetHandlerDefaultReturnsXoopsFileHandler(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'xoops_test_');
        $this->tempFiles[] = $tmpFile;

        // Default $name is 'file', but we still need a valid path
        $handler = XoopsFile::getHandler('file', $tmpFile);
        $this->assertInstanceOf(XoopsFileHandler::class, $handler);
    }

    /**
     * getHandler('file') with create=true creates the file if it does not exist.
     */
    public function testGetHandlerFileWithCreateTrue(): void
    {
        $tmpDir = str_replace('\\', '/', sys_get_temp_dir()) . '/xoops_test_create_' . uniqid('', true);
        mkdir($tmpDir, 0755, true);
        $this->tempDirs[] = $tmpDir;

        $filePath = $tmpDir . '/newfile.txt';
        $this->tempFiles[] = $filePath;

        $handler = XoopsFile::getHandler('file', $filePath, true);
        $this->assertInstanceOf(XoopsFileHandler::class, $handler);
        $this->assertFileExists($filePath);
    }

    /**
     * getHandler('folder') with create=true creates the directory if it does not exist.
     */
    public function testGetHandlerFolderWithCreateTrue(): void
    {
        $tmpDir = sys_get_temp_dir() . '/xoops_test_newfolder_' . uniqid('', true);
        $this->tempDirs[] = $tmpDir;

        $handler = XoopsFile::getHandler('folder', $tmpDir, true);
        $this->assertInstanceOf(XoopsFolderHandler::class, $handler);
        $this->assertDirectoryExists($tmpDir);
    }

    /**
     * getHandler() passes the $path argument correctly to the file handler.
     */
    public function testGetHandlerFilePathIsCorrect(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'xoops_test_');
        $this->tempFiles[] = $tmpFile;

        $handler = XoopsFile::getHandler('file', $tmpFile);
        $this->assertInstanceOf(XoopsFileHandler::class, $handler);
        // The handler's name property should be the basename
        $this->assertSame(basename($tmpFile), $handler->name);
    }

    /**
     * getHandler('folder') passes the $path argument correctly to the folder handler.
     */
    public function testGetHandlerFolderPathIsCorrect(): void
    {
        $tmpDir = str_replace('\\', '/', sys_get_temp_dir()) . '/xoops_test_pathcheck_' . uniqid('', true);
        mkdir($tmpDir, 0755, true);
        $this->tempDirs[] = $tmpDir;

        $handler = XoopsFile::getHandler('folder', $tmpDir);
        $this->assertInstanceOf(XoopsFolderHandler::class, $handler);
        $this->assertSame($tmpDir, $handler->pwd());
    }

    /**
     * getHandler() with an unknown name triggers a warning and returns null.
     */
    public function testGetHandlerUnknownNameReturnsNull(): void
    {
        // Suppress the E_USER_WARNING triggered for unknown class names
        set_error_handler(function ($errno, $errstr) {
            return true;
        });

        try {
            $handler = XoopsFile::getHandler('nonexistent', false, false, null);
            $this->assertNull($handler, 'getHandler() with unknown name should return null');
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Multiple calls to getHandler('file') create separate instances.
     */
    public function testGetHandlerFileCreatesNewInstancesEachCall(): void
    {
        $tmpFile1 = tempnam(sys_get_temp_dir(), 'xoops_test_');
        $tmpFile2 = tempnam(sys_get_temp_dir(), 'xoops_test_');
        $this->tempFiles[] = $tmpFile1;
        $this->tempFiles[] = $tmpFile2;

        $handler1 = XoopsFile::getHandler('file', $tmpFile1);
        $handler2 = XoopsFile::getHandler('file', $tmpFile2);

        $this->assertInstanceOf(XoopsFileHandler::class, $handler1);
        $this->assertInstanceOf(XoopsFileHandler::class, $handler2);
        $this->assertNotSame($handler1, $handler2, 'Each getHandler() call should return a new instance');
    }
}
