<?php

/**
 * Upgrade from 2.5.11 to 2.5.12
 *
 * @copyright    (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license          GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          Upgrade
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
