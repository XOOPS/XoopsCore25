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
 * If you did not receive this file, get it at http://www.fsf.org/copyleft/gpl.html
 *
 * @copyright    (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license          http://www.fsf.org/copyleft/gpl.html GNU General Public License (GPL)
 * @package          upgrader
 * @since            2.3.0
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @version          $Id: index.php 13082 2015-06-06 21:59:41Z beckmi $
 */
class upgrade_231 extends xoopsUpgrade
{
    var $tasks = array('field');

    function upgrade_231()
    {
        $this->xoopsUpgrade(basename(__DIR__));
    }

    /**
     * Check if field type already fixed for mysql strict mode
     *
     */
    function check_field()
    {
        $fields = array(
            "cache_data"     => "cache_model",
            "htmlcode"       => "banner",
            "extrainfo"      => "bannerclient",
            "com_text"       => "xoopscomments",
            "conf_value"     => "config",
            "description"    => "groups",
            "imgsetimg_body" => "imgsetimg",
            "content"        => "newblocks",
            "msg_text"       => "priv_msgs",
            "sess_data"      => "session",
            "tplset_credits" => "tplset",
            "tpl_source"     => "tplsource",
            "user_sig"       => "users",
            "bio"            => "users",);
        foreach ($fields as $field => $table) {
            $sql = "SHOW COLUMNS FROM `" . $GLOBALS['xoopsDB']->prefix($table) . "` LIKE '{$field}'";
            if (!$result = $GLOBALS['xoopsDB']->queryF($sql)) {
                return false;
            }
            while ($row = $GLOBALS['xoopsDB']->fetchArray($result)) {
                if ($row['Field'] != $field) {
                    continue;
                }
                if (strtoupper($row['Null']) != "YES") {
                    return false;
                }
            }
        }

        return true;
    }

    function apply_field()
    {
        $allowWebChanges                     = $GLOBALS['xoopsDB']->allowWebChanges;
        $GLOBALS['xoopsDB']->allowWebChanges = true;
        $result                              = $GLOBALS['xoopsDB']->queryFromFile(__DIR__ . "/mysql.structure.sql");
        $GLOBALS['xoopsDB']->allowWebChanges = $allowWebChanges;

        return $result;
    }
}

$upg = new upgrade_231();
return $upg;

?>
