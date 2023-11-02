<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * Upgrader from 2.3.0 to 2.3.1
 *
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          upgrader
 * @since            2.3.0
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 */
class Upgrade_231 extends XoopsUpgrade
{
    /**
     * Upgrade_231 constructor.
     */
    public function __construct()
    {
        parent::__construct(basename(__DIR__));
        $this->tasks = array('field');
    }

    /**
     * Check if field type already fixed for mysql strict mode
     *
     */
    public function check_field()
    {
        $fields = array(
            'cache_data' => 'cache_model',
            'htmlcode' => 'banner',
            'extrainfo' => 'bannerclient',
            'com_text' => 'xoopscomments',
            'conf_value' => 'config',
            'description' => 'groups',
            'imgsetimg_body' => 'imgsetimg',
            'content' => 'newblocks',
            'msg_text' => 'priv_msgs',
            'sess_data' => 'session',
            'tplset_credits' => 'tplset',
            'tpl_source' => 'tplsource',
            'user_sig' => 'users',
            'bio' => 'users');
        foreach ($fields as $field => $table) {
            $sql = 'SHOW COLUMNS FROM `' . $GLOBALS['xoopsDB']->prefix($table) . "` LIKE '{$field}'";
            $result = $GLOBALS['xoopsDB']->queryF($sql);
            if (!$GLOBALS['xoopsDB']->isResultSet($result)) {
                return false;
            }
            while (false !== ($row = $GLOBALS['xoopsDB']->fetchArray($result))) {
                if ($row['Field'] != $field) {
                    continue;
                }
                if (strtoupper($row['Null']) !== 'YES') {
                    return false;
                }
            }
        }

        return true;
    }

    public function apply_field()
    {
        $allowWebChanges                     = $GLOBALS['xoopsDB']->allowWebChanges;
        $GLOBALS['xoopsDB']->allowWebChanges = true;
        $result                              = $GLOBALS['xoopsDB']->queryFromFile(__DIR__ . '/mysql.structure.sql');
        $GLOBALS['xoopsDB']->allowWebChanges = $allowWebChanges;

        return $result;
    }
}

$upg = new Upgrade_231();
return $upg;
