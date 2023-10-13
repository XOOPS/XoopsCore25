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
 * XOOPS Upgrade Smarty3ScannerOutput
 *
 * Used to report Smarty3 issues found in scan
 *
 * @category  Xoops\Upgrade
 * @package   Xoops
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2023 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */
class Smarty3ScannerOutput extends ScannerOutput
{
    /**
     * @var string $content accumulated output
     */
    protected $content = '';

    /**
     * @var ArrayObject $counts
     */
    protected $counts;

    /**
     * Initialize
     */
    public function __construct()
    {
        $this->content = '';
        $this->counts = new ArrayObject(array());
    }

    /**
     * add to count of occurrences of items by $key
     *
     * Keys used are:
     *  The standard rules:
     *  'foreachq'
     *  'includeq'
     *  'noquotes'
     *  'varname'
     *
     *  Also, these attributes
     *  'checked' - a running count of files checked
     *  'notwritable' - count of files with that could be fixed if permissions were corrected
     *
     * @param string $key
     *
     * @return void
     */
    public function addToCount($key)
    {
        $count = 0;
        if ($this->counts->offsetExists($key)) {
            $count = $this->counts->offsetGet($key);
        }
        $this->counts->offsetSet($key, ++$count);
    }

    /**
     * get count of occurrences of items by $key
     *
     * Keys used are:
     *  The standard rules:
     *  'foreachq'
     *  'includeq'
     *  'noquotes'
     *  'varname'
     *
     *  Also, these attributes
     *  'checked' - a running count of files checked
     *  'notwritable' - count of files with that could be fixed if permissions were corrected
     *
     * @param string $key
     *
     * @return int
     */
    protected function getCount($key)
    {
        $count = 0;
        if ($this->counts->offsetExists($key)) {
            $count = $this->counts->offsetGet($key);
        }
        return $count;
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
            . _XOOPS_SMARTY3_SCANNER_RULE . '</th><th>'
            . _XOOPS_SMARTY3_SCANNER_MATCH . '</th><th>'
            . _XOOPS_SMARTY3_SCANNER_FILE . '</th></tr>');
    }

    public function outputWrapUp()
    {
        $this->outputAppend('</table>');

        // build summary table
        $this->outputAppend('<table class="table"><tr><th>' . 'Scan Summary' . '</th><th></th></tr>');
        $this->outputAppend('<tr><td>' . 'Files Checked' . '</td><td>' . (string) $this->getCount('checked') . '</td></tr>');
        $this->outputAppend('<tr class="warning"><td>' . 'Need file permission to fix' . '</td><td>' . (string) $this->getCount('notwritable') . '</td></tr>');
        $this->outputAppend('<tr class="danger"><td>' . 'Need manual review to fix' . '</td><td>' . (string) $this->getCount('varname') . '</td></tr>');
        $this->outputAppend('<tr><td>' . 'Using includeq/foreachq'     . '</td><td>' . (string) ($this->getCount('includeq') + $this->getCount('foreachq')) . '</td></tr>');
        $this->outputAppend('<tr><td>' . 'Missing Quotes'     . '</td><td>' . (string) ($this->getCount('noquotes')) . '</td></tr>');
        $this->outputAppend('<tr><td></td><td></td></tr>');
        $this->outputAppend('</table>');
    }

    /**
     * @param ArrayObject $args should contain these keys: rule, file, match, writable
     */
    public function outputIssue(ArrayObject $args)
    {
        $rule = $args['rule'];
        $file = $args['file'];
        $match = $args['match'];
        $writable = $args['writable'];

        $this->addToCount($rule);

        if (!$writable) {
            $message = _XOOPS_SMARTY3_SCANNER_NOT_WRITABLE;
            $this->addToCount('notwritable');
            $this->outputAppend("<tr class='warning'>"
                . "<td>$rule</td><td>$match</td><td>$file<br>$message</td></tr>");
        } elseif ($rule == 'varname') {
            $message = _XOOPS_SMARTY3_SCANNER_MANUAL_REVIEW            ;
            $this->outputAppend("<tr class='danger'>"
                . "<td>$rule</td><td>$match</td><td>$file<br>$message</td></tr>");
        } else {
            $this->outputAppend("<tr><td>$rule</td><td>$match</td><td>$file</td></tr>");
        }
    }

    /**
     * @param string $rule
     * @param string $file
     * @param string $match
     * @param bool   $writable
     *
     * @returns ArrayObject with keys 'rule', 'file', 'match' and 'writeable'
     */
    public function makeOutputIssue($rule, $file, $match, $writable)
    {
        return new ArrayObject(
            array(
                'rule' => $rule,
                'file' => $file,
                'match'=> $match,
                'writable' => $writable
            )
        );
    }
}
