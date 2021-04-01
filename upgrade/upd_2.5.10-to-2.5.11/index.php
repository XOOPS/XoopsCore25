<?php

use Xmf\Database\Tables;

/**
 * Upgrade from 2.5.10 to 2.5.11
 *
 * @copyright    (c) 2000-2021 XOOPS Project (https://xoops.org)
 * @license          GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          Upgrade
 * @since            2.5.11
 * @author           XOOPS Team
 */
class Upgrade_2511 extends XoopsUpgrade
{
    /**
     * __construct
     */
    public function __construct()
    {
        parent::__construct(basename(__DIR__));
        $this->tasks = array(
            'bannerintsize',
            'captchadata',
            'qmail',
            'rmindexhtml',
            'textsanitizer',
            'xoopsconfig',
        );
        $this->usedFiles = array();
        $this->pathsToCheck = array(
            XOOPS_ROOT_PATH . '/cache',
            XOOPS_ROOT_PATH . '/class',
            XOOPS_ROOT_PATH . '/Frameworks',
            XOOPS_ROOT_PATH . '/images',
            XOOPS_ROOT_PATH . '/include',
            XOOPS_ROOT_PATH . '/kernel',
            XOOPS_ROOT_PATH . '/language',
            XOOPS_ROOT_PATH . '/media',
            XOOPS_ROOT_PATH . '/modules/pm',
            XOOPS_ROOT_PATH . '/modules/profile',
            XOOPS_ROOT_PATH . '/modules/protector',
            XOOPS_ROOT_PATH . '/modules/system',
            XOOPS_ROOT_PATH . '/templates_c',
            XOOPS_ROOT_PATH . '/themes/default',
            XOOPS_ROOT_PATH . '/themes/xbootstrap',
            XOOPS_ROOT_PATH . '/themes/xswatch',
            XOOPS_ROOT_PATH . '/themes/xswatch4',
            XOOPS_ROOT_PATH . '/uploads',
            XOOPS_VAR_PATH,
            XOOPS_PATH,
        );
    }


    /**
     * Determine if columns are declared mediumint, and if
     * so, queue ddl to alter to int.
     *
     * @param $migrate           \Xmf\Database\Tables
     * @param $bannerTableName   string
     * @param $bannerColumnNames string[] array of columns to check
     *
     * @return integer count of queue items added
     */
    protected function fromMediumToInt(Tables $migrate, $bannerTableName, $bannerColumnNames)
    {
        $migrate->useTable($bannerTableName);
        $count = 0;
        foreach ($bannerColumnNames as $column) {
            $attributes = $migrate->getColumnAttributes($bannerTableName, $column);
            if (0 === strpos(trim($attributes), 'mediumint')) {
                $count++;
                $migrate->alterColumn($bannerTableName, $column, 'int(10) UNSIGNED NOT NULL DEFAULT \'0\'');
            }
        }
        return $count;
    }

    private $bannerTableName = 'banner';
    private $bannerColumnNames = array('impmade', 'clicks');

    /**
     * Increase count columns from mediumint to int
     *
     * @return bool true if patch IS applied, false if NOT applied
     */
    public function check_bannerintsize()
    {
        $migrate = new Tables();
        $count = $this->fromMediumToInt($migrate, $this->bannerTableName, $this->bannerColumnNames);

        return $count==0;
    }

    /**
     * Increase count columns from mediumint to int (Think BIG!)
     *
     * @return bool true if applied, false if failed
     */
    public function apply_bannerintsize()
    {
        $migrate = new \Xmf\Database\Tables();

        $count = $this->fromMediumToInt($migrate, $this->bannerTableName, $this->bannerColumnNames);

        $result = $migrate->executeQueue(true);
        if (false === $result) {
            $this->logs[] = sprintf('Migration of %s table failed. Error: %s - %s' .
                $this->bannerTableName,
                $migrate->getLastErrNo(),
                $migrate->getLastError()
            );
            return false;
        }

        return $count!==0;
    }

    /**
     * Add qmail as valid mailmethod
     *
     * @return bool
     */
    public function check_qmail()
    {
        /* @var XoopsMySQLDatabase $db */
        $db = XoopsDatabaseFactory::getDatabaseConnection();

        $table = $db->prefix('configoption');

        $sql = sprintf(
            'SELECT count(*) FROM `%s` '
            . "WHERE `conf_id` = 64 AND `confop_name` = 'qmail'",
            $db->escape($table)
        );

        /** @var mysqli_result $result */
        $result = $db->query($sql);
        if ($result) {
            $row = $db->fetchRow($result);
            if ($row) {
                $count = $row[0];
                return (0 === (int) $count) ? false : true;
            }
        }
        return false;
    }

    /**
     * Add qmail as valid mailmethod
     *
     * phpMailer has qmail support, similar to but slightly different than sendmail
     * This will allow webmasters to utilize qmail if it is provisioned on server.
     *
     * @return bool
     */
    public function apply_qmail()
    {
        $migrate = new Tables();
        $migrate->useTable('configoption');
        $migrate->insert(
            'configoption',
            array('confop_name' => 'qmail', 'confop_value' => 'qmail', 'conf_id' => 64)
        );
        return $migrate->executeQueue(true);
    }

    /**
     * Do we need to move captcha writable data?
     *
     * @return bool
     */
    public function check_captchadata()
    {
        $captchaConfigFile = XOOPS_VAR_PATH . '/configs/captcha/config.php';
        return file_exists($captchaConfigFile);
    }

    /**
     * Attempt to make the supplied path
     * @param $newPath string
     *
     * @return bool
     */
    private function makeDirectory($newPath)
    {
        if (!mkdir($newPath) && !is_dir($newPath)) {
            $this->logs[] = sprintf('Captcha config directory %s was not created', $newPath);
            return false;
        }
        return true;
    }

    /**
     * Copy file $source to $destination
     *
     * @param $source      string
     * @param $destination string
     *
     * @return bool true if successful, false on error
     */
    private function copyFile($source, $destination)
    {
        if (!file_exists($destination)) { // don't overwrite anything
            $result = copy($source, $destination);
            if (false === $result) {
                $this->logs[] = sprintf('Captcha config file copy %s failed', basename($source));
                return false;
            }
        }
        return true;
    }

    /**
     * Move captcha configs to xoops_data to segregate writable data
     *
     * @return bool
     */
    public function apply_captchadata()
    {
        $returnResult = false;
        $sourcePath = XOOPS_ROOT_PATH . '/class/captcha/';
        $destinationPath = XOOPS_VAR_PATH . '/configs/captcha/';

        if (!file_exists($destinationPath)) {
            $this->makeDirectory($destinationPath);
        }
        $directory = dir($sourcePath);
        if (false === $directory) {
            $this->logs[] = sprintf('Failed to read source %s', $sourcePath);
            return false;
        }
        while (false !== ($entry = $directory->read())) {
            if (false === strpos($entry, '.dist.')
                && strpos($entry, 'config.') === 0 && '.php' === substr($entry, -4)) {
                $src = $sourcePath . $entry;
                $dest = $destinationPath . $entry;
                $status = $this->copyFile($src, $dest);
                if (false === $status) {
                    $returnResult = false;
                }
            }
        }
        $directory->close();

        return $returnResult;
    }

    /**
     * Do we need to create a xoops_data/configs/xoopsconfig.php?
     *
     * @return bool true if patch IS applied, false if NOT applied
     */
    public function check_xoopsconfig()
    {
        $xoopsConfigFile = XOOPS_VAR_PATH . '/configs/xoopsconfig.php';
        return file_exists($xoopsConfigFile);
    }

    /**
     * Create xoops_data/configs/xoopsconfig.php from xoopsconfig.dist.php
     *
     * @return bool true if applied, false if failed
     */
    public function apply_xoopsconfig()
    {
        $source = XOOPS_VAR_PATH . '/configs/xoopsconfig.dist.php';
        $destination = XOOPS_VAR_PATH . '/configs/xoopsconfig.php';
        if (!file_exists($destination)) { // don't overwrite anything
            $result = copy($source, $destination);
            if (false === $result) {
                $this->logs[] = 'xoopsconfig.php file copy failed';
                return false;
            }
        }
        return true;
    }

    /**
     * This is a default list based on extensions as supplied by XOOPS.
     * If possible, we will build a list based on contents of class/textsanitizer/
     * key is file path relative to XOOPS_ROOT_PATH . '/class/textsanitizer/
     * value is file path relative to XOOPS_VAR_PATH . '/configs/textsanitizer/'
     *
     * @var string[]
     */
    protected $textsanitizerConfigFiles = array(
        'config.php' => 'config.php',
        'censor/config.php' => 'config.censor.php',
        'flash/config.php' => 'config.flash.php',
        'image/config.php' => 'config.image.php',
        'mms/config.php' => 'config.mms.php',
        'rtsp/config.php' => 'config.rtsp.php',
        'syntaxhighlight/config.php' => 'config.syntaxhighlight.php',
        'textfilter/config.php' => 'config.textfilter.php',
        'wiki/config.php' => 'config.wiki.php',
        'wmp/config.php' => 'config.wmp.php',
    );

    /**
     * Build a list of config files using the existing textsanitizer/config.php
     * This should prevent some issues with customized systems.
     *
     * @return string[] array of existing ts and extension config files
     *                  each as source name => destination name
     */
    protected function buildListTSConfigs()
    {
        if (file_exists(XOOPS_ROOT_PATH . '/class/textsanitizer/config.php')) {
            $config = include XOOPS_ROOT_PATH . '/class/textsanitizer/config.php';
            if (is_array($config) && array_key_exists('extentions', $config)) {
                $this->textsanitizerConfigFiles = array(
                    'config.php' => 'config.php',
                );
                foreach ($config['extentions'] as $module => $enabled) {
                    $source = "{$module}/config.php";
                    if (file_exists(XOOPS_ROOT_PATH . '/class/textsanitizer/' . $source)) {
                        $destination = "{$module}/config.{$module}.php";
                        $this->textsanitizerConfigFiles[$source] = $destination;
                    }
                }
            }
        }
        return;
    }

    /**
     * Do we need to move any existing files to xoops_data/configs/textsanitizer/ ?
     *
     * @return bool true if patch IS applied, false if NOT applied
     */
    public function check_textsanitizer()
    {
        $this->buildListTSConfigs();
        foreach ($this->textsanitizerConfigFiles as $source => $destination) {
            $src  = XOOPS_ROOT_PATH . '/class/textsanitizer/' . $source;
            $dest = XOOPS_VAR_PATH . '/configs/textsanitizer/' . $destination;
            if (!file_exists($dest) && file_exists($src)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Copy and rename any existing class/textsanitizer/ config files to xoops_data/configs/textsanitizer/
     *
     * @return bool true if applied, false if failed
     */
    public function apply_textsanitizer()
    {
        $this->buildListTSConfigs();
        $return = true;
        foreach ($this->textsanitizerConfigFiles as $source => $destination) {
            $src  = XOOPS_ROOT_PATH . '/class/textsanitizer/' . $source;
            $dest = XOOPS_VAR_PATH . '/configs/textsanitizer/' . $destination;
            if (!file_exists($dest) && file_exists($src)) {
                $result = copy($src, $dest);
                if (false === $result) {
                    $this->logs[] = sprintf('textsanitizer file copy to %s failed', $destination);
                    $return = false;
                }
            }
        }
        return $return;
    }

    /**
     * Attempt to remove index.html files replaced by index.php
     */
    /**
     * List of directories supplied by XOOPS. This is used to try and keep us out
     * of things added to the system locally. (Set in __construct() for php BC.)
     *
     * @var string[]
     */
    private $pathsToCheck;

    /**
     * Do we need to remove any index.html files that were replaced by index.php files?
     *
     * @return bool true if patch IS applied, false if NOT applied
     */
    public function check_rmindexhtml()
    {
        /**
         * If we find an index.html that is writable, we know there is work to do
         *
         * @param string $name file name to check
         *
         * @return bool  true to continue, false to stop scan
         */
        $stopIfFound = function ($name) {
            $ok = is_writable($name);
            return !($ok);
        };

        clearstatcache();

        return $this->dirWalker($stopIfFound);
    }

    /**
     * Unlink any index.html files that have been replaced by index.php files
     *
     * @return bool true if patch applied, false if failed
     */
    public function apply_rmindexhtml()
    {
        /**
         * Do unlink() on file
         * Always return true so we process each writable index.html
         *
         * @param string $name file name to unlink
         *
         * @return true always report true, even if we can't delete -- best effort only
         */
        $unlinkByName = function ($name) {
            if (is_writable($name)) {
                $result = unlink($name);
            }
            return true;
        };


        return $this->dirWalker($unlinkByName);
    }

    /**
     * Walk list of directories in $pathsToCheck
     *
     * @param \Closure $onFound
     *
     * @return bool
     */
    private function dirWalker(\Closure $onFound)
    {
        $check = true;
        foreach ($this->pathsToCheck as $path) {
            $check = $this->checkDirForIndexHtml($path, $onFound);
            if (false === $check) {
                break;
            }
        }
        if (false !== $check) {
            $check = true;
        }
        return $check;
    }

    /**
     * Recursively check for index.html files that have a corresponding index.php file
     * in the supplied path.
     *
     * @param string   $startingPath
     * @param \Closure $onFound
     *
     * @return false|int false if onFound returned false (don't continue) else count of matches
     */
    private function checkDirForIndexHtml($startingPath, \Closure $onFound)
    {
        if (!is_dir($startingPath)) {
            return 0;
        }
        $i = 0;
        $rdi = new \RecursiveDirectoryIterator($startingPath);
        $rii = new \RecursiveIteratorIterator($rdi);
        /** @var \SplFileInfo $fileinfo */
        foreach ($rii as $fileinfo) {
            if ($fileinfo->isFile() && 'index.html' === $fileinfo->getFilename() && 60 > $fileinfo->getSize()) {
                $path = $fileinfo->getPath();
                $testFilename = $path . '/index.php';
                if (file_exists($testFilename)) {
                    $unlinkName = $path . '/' . $fileinfo->getFilename();
                    ++$i;
                    $continue = $onFound($unlinkName);
                    if (false === $continue) {
                        return $continue;
                    }
                }
            }
        }
        return $i;
    }
}

return new Upgrade_2511();
