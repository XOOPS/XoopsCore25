<?php

use Xmf\Database\Tables;

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
            'expandactkey',
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
     * Get current actkey column varchar length, or null if undetectable.
     *
     * @param Tables $migrate
     * @return int|null
     */
    private function getActkeyVarcharLength(Tables $migrate)
    {
        $migrate->useTable('users');
        $attributes = $migrate->getColumnAttributes('users', 'actkey');
        if (is_string($attributes) && preg_match('/varchar\((\d+)\)/i', trim($attributes), $m)) {
            return (int)$m[1];
        }
        return null;
    }

    /**
     * Check if actkey column is already large enough for lostpass tokens.
     * Packed token is ~78 chars; column needs to be at least VARCHAR(100).
     *
     * @return bool true if patch IS applied, false if NOT applied
     */
    public function check_expandactkey()
    {
        $length = $this->getActkeyVarcharLength(new Tables());
        if (null === $length) {
            return true; // undetectable — assume applied to avoid blocking
        }
        return $length >= 100;
    }

    /**
     * Expand actkey column to VARCHAR(100) for secure lostpass tokens.
     *
     * @return bool true if applied, false if failed
     */
    public function apply_expandactkey()
    {
        $migrate = new Tables();
        $length = $this->getActkeyVarcharLength($migrate);

        if (null !== $length && $length < 100) {
            $migrate->alterColumn('users', 'actkey', "VARCHAR(100) NOT NULL DEFAULT ''");
        }

        $result = $migrate->executeQueue(true);
        if (false === $result) {
            $this->logs[] = sprintf(
                'Migration of users.actkey column failed. Error: %s - %s',
                $migrate->getLastErrNo(),
                $migrate->getLastError(),
            );
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
