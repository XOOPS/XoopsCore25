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

use ArrayObject;

/**
 * XOOPS Upgrade ScannerOutput
 *
 * Output abstraction for use in ScannerWalker based file processing
 *
 * @category  Xoops\Upgrade
 * @package   Xoops
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2023 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */

abstract class ScannerOutput
{
    /**
     * Add item to report
     *
     * @var mixed $item,...
     * @return void
     */
    abstract public function outputAppend($item);

    /**
     * Return recorded output
     *
     * @returns string
     */
    abstract public function outputFetch();

    /**
     * @param ArrayObject $args specify arguments as keyed parameters
     */
    abstract public function outputIssue(ArrayObject $args);

    /**
     * Initialize output, invoked in ScannerWalker::__construct()
     *
     * @returns void
     */
    abstract public function outputStart();

    /**
     * Finish building output
     */
    abstract public function outputWrapUp();
}
