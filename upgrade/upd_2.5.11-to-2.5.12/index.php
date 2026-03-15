<?php

/**
 * Upgrade from 2.5.11 to 2.5.12
 *
 * @copyright    (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license          GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @since            2.5.11
 * @author           XOOPS Team
 */
class Upgrade_2512 extends XoopsUpgrade
{
    public $pathsToCheck = [];

    /**
     * __construct
     */
    public function __construct()
    {
        parent::__construct(basename(__DIR__));
        $this->tasks        = [
            'deletepurifier',
            'deletephpmailer',
            'createtokenstable',
            'widenbannerclientpasswd',
            'addsessioncookieprefs',
        ];
        $this->usedFiles    = [];
        $this->pathsToCheck = [
            XOOPS_ROOT_PATH . '/class/mail/phpmailer',
            XOOPS_TRUST_PATH . '/modules/protector/library',

        ];
    }

    /**
     * Check if the obsolete HTMLPurifier folder is available to delete?
     *
     * @return bool
     */
    public function check_deletepurifier()
    {
        return !is_dir(XOOPS_TRUST_PATH . '/modules/protector/library/');
    }

    /**
     * Delete obsolete HTMLPurifier folder
     *
     * @return bool
     */
    public function apply_deletepurifier()
    {
        // Define the folder to delete
        $folderToDelete = XOOPS_TRUST_PATH . '/modules/protector/library/';
        return self::deleteFolder($folderToDelete);
    }

    /**
     * Check if the obsolete phpmailer folder is available to delete?
     *
     * @return bool
     */
    public function check_deletephpmailer()
    {
        return !is_dir('../class/mail/phpmailer/');
    }

    /**
     * Delete obsolete phpmailer files
     *
     * @return bool
     */
    public function apply_deletephpmailer()
    {
        // Define the folder to delete
        $folderToDelete = '../class/mail/phpmailer/';

        return self::deleteFolder($folderToDelete);
    }

    /**
     * Check if the tokens table already exists.
     *
     * @return bool true if table exists (patch applied)
     */
    public function check_createtokenstable()
    {
        $table  = $GLOBALS['xoopsDB']->prefix('tokens');
        $sql    = "SELECT 1 FROM `information_schema`.`TABLES`"
                . " WHERE `TABLE_SCHEMA` = DATABASE() AND `TABLE_NAME` = " . $GLOBALS['xoopsDB']->quote($table)
                . " LIMIT 1";
        $result = $GLOBALS['xoopsDB']->query($sql);
        if (!$GLOBALS['xoopsDB']->isResultSet($result) || !($result instanceof \mysqli_result)) {
            return false;
        }
        return (bool)$GLOBALS['xoopsDB']->fetchArray($result);
    }

    /**
     * Create the tokens table for generic scoped tokens.
     *
     * @return bool true on success
     */
    public function apply_createtokenstable()
    {
        $table = $GLOBALS['xoopsDB']->prefix('tokens');
        $sql   = "CREATE TABLE IF NOT EXISTS `{$table}` (
            `token_id`   int unsigned        NOT NULL AUTO_INCREMENT,
            `uid`        mediumint unsigned  NOT NULL DEFAULT 0,
            `scope`      varchar(32)         NOT NULL DEFAULT '',
            `hash`       char(64)            NOT NULL DEFAULT '',
            `issued_at`  int unsigned        NOT NULL DEFAULT 0,
            `expires_at` int unsigned        NOT NULL DEFAULT 0,
            `used_at`    int unsigned        NOT NULL DEFAULT 0,
            PRIMARY KEY (`token_id`),
            UNIQUE KEY `uq_uid_scope_hash` (`uid`, `scope`, `hash`),
            KEY `idx_uid_scope_issued` (`uid`, `scope`, `issued_at`),
            KEY `idx_issued_at` (`issued_at`)
        ) ENGINE=InnoDB;";

        $result = $GLOBALS['xoopsDB']->exec($sql);
        if (!$result) {
            $errno = $GLOBALS['xoopsDB']->errno();
            $error = $GLOBALS['xoopsDB']->error();
            $this->logs[] = sprintf('Failed to create tokens table. Error: %s - %s', $errno, $error);
            return false;
        }
        return true;
    }

    /**
     * Check if bannerclient.passwd column is wide enough for password hashes.
     *
     * @return bool true if column is already wide enough
     */
    public function check_widenbannerclientpasswd()
    {
        $table  = $GLOBALS['xoopsDB']->prefix('bannerclient');
        $sql    = "SELECT CHARACTER_MAXIMUM_LENGTH FROM `information_schema`.`COLUMNS`"
                . " WHERE `TABLE_SCHEMA` = DATABASE()"
                . " AND `TABLE_NAME` = " . $GLOBALS['xoopsDB']->quote($table)
                . " AND `COLUMN_NAME` = 'passwd' LIMIT 1";
        $result = $GLOBALS['xoopsDB']->query($sql);
        if (!$GLOBALS['xoopsDB']->isResultSet($result) || !($result instanceof \mysqli_result)) {
            return false;
        }
        $row = $GLOBALS['xoopsDB']->fetchRow($result);
        return $row && (int) $row[0] >= 255;
    }

    /**
     * Widen bannerclient.passwd column to accommodate password hashes.
     *
     * @return bool true on success
     */
    public function apply_widenbannerclientpasswd()
    {
        $table  = $GLOBALS['xoopsDB']->prefix('bannerclient');
        $sql    = "ALTER TABLE `{$table}` MODIFY `passwd` varchar(255) NOT NULL DEFAULT ''";
        $result = $GLOBALS['xoopsDB']->exec($sql);
        if (!$result) {
            $this->logs[] = sprintf(
                'Failed to widen bannerclient.passwd column. Error: %s - %s',
                $GLOBALS['xoopsDB']->errno(),
                $GLOBALS['xoopsDB']->error()
            );
            return false;
        }
        return true;
    }

    /**
     * Check if session cookie preferences already exist (config rows + options).
     *
     * @return bool true if fully present (no action needed)
     */
    public function check_addsessioncookieprefs()
    {
        $db = $GLOBALS['xoopsDB'];

        // Check both core config rows exist (scoped to conf_modid=0, conf_catid=1)
        $sql = 'SELECT COUNT(DISTINCT conf_name) FROM `' . $db->prefix('config')
            . "` WHERE conf_modid = 0 AND conf_catid = 1"
            . " AND `conf_name` IN ('session_cookie_samesite', 'session_cookie_secure')";
        $result = $db->query($sql);
        if (!$db->isResultSet($result) || !($result instanceof \mysqli_result)) {
            return false;
        }
        $row = $db->fetchRow($result);
        if (!$row || (int) $row[0] < 2) {
            return false;
        }

        // Check SameSite options exist (Lax, Strict, None)
        $sql = "SELECT COUNT(*) FROM `" . $db->prefix('configoption') . "` co"
            . " INNER JOIN `" . $db->prefix('config') . "` c ON co.conf_id = c.conf_id"
            . " WHERE c.conf_name = 'session_cookie_samesite' AND c.conf_modid = 0";
        $result = $db->query($sql);
        if (!$db->isResultSet($result) || !($result instanceof \mysqli_result)) {
            return false;
        }
        $row = $db->fetchRow($result);
        return $row && (int) $row[0] >= 3;
    }

    /**
     * Add session cookie SameSite and Secure preferences (idempotent).
     *
     * @return bool true on success
     */
    public function apply_addsessioncookieprefs()
    {
        $db = $GLOBALS['xoopsDB'];
        $configTable = $db->prefix('config');
        $optionTable = $db->prefix('configoption');

        // Insert SameSite preference (skip if exists)
        $sql = "SELECT conf_id FROM `{$configTable}` WHERE conf_name = 'session_cookie_samesite' AND conf_modid = 0";
        $result = $db->query($sql);
        $sameSiteRow = ($db->isResultSet($result) && ($result instanceof \mysqli_result)) ? $db->fetchRow($result) : false;

        if (!$sameSiteRow) {
            if (!$db->exec("INSERT INTO `{$configTable}` (conf_modid, conf_catid, conf_name, conf_title, conf_value, conf_desc, conf_formtype, conf_valuetype, conf_order) VALUES (0, 1, 'session_cookie_samesite', '_MD_AM_SESSSAMESITE', 'Lax', '_MD_AM_SESSSAMESITE_DSC', 'select', 'text', 43)")) {
                $this->logs[] = 'Failed to insert session_cookie_samesite config: ' . $db->error();
                return false;
            }
            // Re-fetch the conf_id
            $result = $db->query($sql);
            $sameSiteRow = ($db->isResultSet($result) && ($result instanceof \mysqli_result)) ? $db->fetchRow($result) : false;
            if (!$sameSiteRow) {
                $this->logs[] = 'Failed to retrieve session_cookie_samesite conf_id after insert';
                return false;
            }
        }

        // Insert Secure preference (skip if exists)
        $sql = "SELECT conf_id FROM `{$configTable}` WHERE conf_name = 'session_cookie_secure' AND conf_modid = 0";
        $result = $db->query($sql);
        $secureRow = ($db->isResultSet($result) && ($result instanceof \mysqli_result)) ? $db->fetchRow($result) : false;

        if (!$secureRow) {
            if (!$db->exec("INSERT INTO `{$configTable}` (conf_modid, conf_catid, conf_name, conf_title, conf_value, conf_desc, conf_formtype, conf_valuetype, conf_order) VALUES (0, 1, 'session_cookie_secure', '_MD_AM_SESSSECURE', '0', '_MD_AM_SESSSECURE_DSC', 'yesno', 'int', 44)")) {
                $this->logs[] = 'Failed to insert session_cookie_secure config: ' . $db->error();
                return false;
            }
        }

        // Add select options for SameSite — delete and recreate to avoid duplicates
        $confId = (int) $sameSiteRow[0];
        $db->exec("DELETE FROM `{$optionTable}` WHERE conf_id = {$confId}");
        foreach (['Lax', 'Strict', 'None'] as $opt) {
            if (!$db->exec("INSERT INTO `{$optionTable}` (confop_name, confop_value, conf_id) VALUES ('{$opt}', '{$opt}', {$confId})")) {
                $this->logs[] = "Failed to insert SameSite option '{$opt}': " . $db->error();
                return false;
            }
        }

        return true;
    }

    private function deleteFolder($folderPath)
    {
        // Open the folder
        $files = array_diff(scandir($folderPath), ['.', '..']);

        foreach ($files as $file) {
            $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
            if (is_dir($filePath)) {
                // Recursively delete subfolders
                self::deleteFolder($filePath);
            } else {
                // Delete file
                unlink($filePath);
            }
        }

        // Remove the folder itself
        return rmdir($folderPath);
    }
}

return new Upgrade_2512();
