<?php

use Xmf\Database\Tables;

/**
 * Upgrade from 2.5.11 to 2.5.12
 *
 * @copyright    (c) 2000-2023 XOOPS Project (https://xoops.org)
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
