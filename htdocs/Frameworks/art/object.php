<?php
/**
 * Extended object handlers
 *
 * For backward compatibility
 *
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @since               1.00
 * @version             $Id: object.php 13082 2015-06-06 21:59:41Z beckmi $
 * @package             Frameworks
 * @subpackage          art
 */

//if (!class_exists("ArtObject")):
if (class_exists("ArtObject")) {
    return null;
}

/**
 * Art Object
 *
 * @author              D.J. (phppp)
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 * @package             module::article
 *
 * {@link XoopsObject}
 **/
class ArtObject extends XoopsObject
{
    /**
     * @var string
     */
    public $plugin_path;

    /**
     * Constructor
     *
     */

    public function __construct()
    {
    }
}

/**
 * object handler class.
 * @package             module::article
 *
 * @author              D.J. (phppp)
 * @copyright       (c) 2000-2015 XOOPS Project (www.xoops.org)
 *
 * {@link XoopsPersistableObjectHandler}
 *
 */
class ArtObjectHandler extends XoopsPersistableObjectHandler
{
    public $db;

    /**
     * Constructor
     *
     * @param XoopsMySQLDatabase $db reference to the {@link XoopsDatabase} object
     * @param string             $table
     * @param string             $className
     * @param string             $keyName
     * @param string             $identifierName
     */

    public function __construct(XoopsMySQLDatabase $db, $table = "", $className = "", $keyName = "", $identifierName = '')
    {
        $this->db = $db;
        parent::__construct($db, $table, $className, $keyName, $identifierName);
    }

    /**
     * get MySQL server version
     *
     * @param null $conn
     *
     * @return string
     */
    public function mysql_server_version($conn = null)
    {
        if (null === ($conn)) {
            $conn = $this->db->conn;
        }
        return mysqli_get_server_info($conn);
    }

    /**
     * get MySQL major version
     *
     * @return integer : 3 - 4.1-; 4 - 4.1+; 5 - 5.0+
     */
    public function mysql_major_version()
    {
        $version = $this->mysql_server_version($this->db->conn);
        if (version_compare($version, "5.0.0", "ge")) {
            $mysql_version = 5;
        } elseif (version_compare($version, "4.1.0", "ge")) {
            $mysql_version = 4;
        } else {
            $mysql_version = 3;
        }

        return $mysql_version;
    }

    /**
     * @param XoopsObject|ArtObject $object
     * @param bool                  $force
     *
     * @return mixed
     */
    public function insert(XoopsObject $object, $force = true)
    {
        if (!($object instanceof $this->className)) {
            return false;
        }
        if ($ret = parent::insert($object, $force)) {
            $object->unsetNew();
        }

        return $ret;
    }
}
//endif;
