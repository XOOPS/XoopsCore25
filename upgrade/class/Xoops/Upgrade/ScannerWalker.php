<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

namespace Xoops\Upgrade;

use Xmf\Assert;
use InvalidArgumentException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use SplFileInfo;
use SplFileObject;

/**
 * XOOPS Upgrade ScannerWalker
 *
 * Scan for files in specified directories with one of the specified file extensions.
 * For each matching file, invoke $this->process->inspectFile()
 *
 * @category  Xoops\Upgrade
 * @package   Xoops
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2023 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */
/**
 * Scan for files with one of the specified file extensions in the specified directories.
 * For each matching file, invoke $this->process->inspectFile()
 */

class ScannerWalker
{
    /**
     * @var string[] $directories
     */
    protected $directories = array();

    /**
     * @var string[] $directories
     */

    protected $extList = array();

    /**
     * @var ScannerProcess
     */
    private $process;

    /**
     * @var ScannerOutput
     */
    private $output;

    /**
     * ScannerWalker
     *
     * @param ScannerProcess $process used to examine or manipulate matching files
     * @param ScannerOutput  $output  handle output from the scanning process
     */
    public function __construct(ScannerProcess $process, ScannerOutput $output)
    {
        $this->output = $output;
        $this->output->outputStart();
        $this->process = $process;
    }

    /**
     * Add a directory to be scanned
     *
     * @param string $directory
     * @return void
     * @throws InvalidArgumentException
     */
    public function addDirectory($directory)
    {
        Assert::stringNotEmpty($directory, 'Directory must be a string value');
        Assert::directory($directory, 'Directory must be a directory');
        $this->directories[] = $directory;
    }

    /**
     * Add a file extension to be scanned
     *
     * @param string $ext
     * @return void
     * @throws InvalidArgumentException
     */
    public function addExtension($ext)
    {
        Assert::stringNotEmpty($ext, 'Extension must be a nonempty string');
        $this->extList[] = $ext;
    }

    /**
     * ScannerWalker::runScan() walks the directories looking for matching extensions.
     * Any matching files will be passed to the ScannerProcess specified for this instance.
     */
    public function runScan()
    {
        foreach ($this->directories as $workingDirectory) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($workingDirectory));
            /** @var SplFileInfo $fileInfo */
            foreach ($iterator as $fileInfo) {
                if ($fileInfo->isDir() || !$fileInfo->isReadable()) {
                    continue;
                }

                $ext = $fileInfo->getExtension();
                if (in_array($ext, $this->extList, true)) {
                    $this->output->addToCount('checked');
                    /** @var SplFileObject $file */
                    $length = $fileInfo->getSize();
                    if ($length > 0) {
                        $this->process->inspectFile($fileInfo);
                    }
                }
            }
        }
        $this->output->outputWrapUp();
    }
}
