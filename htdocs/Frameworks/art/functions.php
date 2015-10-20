<?php
/**
 * common functions
 *
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @since               1.00
 * @version             $Id: functions.php 13082 2015-06-06 21:59:41Z beckmi $
 * @package             Frameworks
 * @subpackage          art
 */

if (defined('XOOPS_ART_FUNCTIONS')) {
    return false;
}
define('XOOPS_ART_FUNCTIONS', true);

include_once __DIR__ . "/functions.ini.php";

load_functions("cache");
load_functions("user");
load_functions("locale");
load_functions("admin");

if (!class_exists('ArtObject')) {
    include_once __DIR__ . "/object.php";
}

/**
 * get MySQL server version
 *
 * In some cases mysql_get_client_info is required instead
 *
 * @param null $conn
 *
 * @return     string
 */
function mod_getMysqlVersion($conn = null)
{
    static $mysql_version;
    if (isset($mysql_version)) {
        return $mysql_version;
    }
    if (null !== ($conn)) {
        $version = mysql_get_server_info($conn);
    } else {
        $version = mysql_get_server_info();
    }

    return $mysql_version;
}