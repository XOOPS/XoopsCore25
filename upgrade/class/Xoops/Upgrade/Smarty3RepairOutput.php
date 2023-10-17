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
 * XOOPS Upgrade Smarty3RepairOutput
 *
 * Used to report Smarty3 issues corrected in migration fixes
 *
 * @category  Xoops\Upgrade
 * @package   Xoops
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2023 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */
class Smarty3RepairOutput extends ScannerOutput
{
    /**
     * @var string $content accumulated output
     */
    protected $content = '';

    /**
     * @var int $issueCounts
     */
    protected $issueCounts;

    /**
     * Initialize
     */
    public function __construct()
    {
        $this->content = '';
        $this->issueCounts = 0;
    }

    /**
     * add to count of fixed issues
     *
     * @param int $count
     *
     * @return void
     */
    public function addToCount($count)
    {
        $this->issueCounts += (int)$count;
    }

    /**
     * Return recorded output
     *
     * @returns string
     */
    public function outputFetch()
    {
        return $this->content;
    }

    /**
     * Add item to report
     *
     * @param string $item
     *
     * @return void
     */
    public function outputAppend($item)
    {
        $this->content .= $item . "\n";
    }

    /**
     * Called to
     */
    public function outputStart()
    {
        $this->outputAppend('<h2>' . _XOOPS_SMARTY3_SCANNER_RESULTS . '</h2>');
        $this->outputAppend('<table class="table"><tr><th>'
            . _XOOPS_SMARTY3_SCANNER_FILE . '</th><th>'
            . _XOOPS_SMARTY3_SCANNER_FIXED . '</th></tr>');
    }

    public function outputWrapUp()
    {
        $this->outputAppend('</table>');
    }

    /**
     * @param ArrayObject $args should contain these keys: filename, count
     */
    public function outputIssue(ArrayObject $args)
    {
        $filename = $args['filename'];
        $count  = (int)$args['count'];
        $this->outputAppend("<tr><td>{$filename}</td><td>{$count}</td></tr>");

        $this->addToCount($count);
    }

    /**
     * @param string $filename
     * @param int $count
     *
     * @returns ArrayObject with keys 'filename', 'count'
     */
    public function makeOutputIssue($filename, $count)
    {
        return new ArrayObject(
            array(
                'filename' => $filename,
                'count' => $count
            )
        );
    }
}
//$output->outputIssue($output->makeOutputIssue($filename, $count));
