<?php

class UpgradeControl
{
    /** @var PatchStatus[] */
    public $upgradeQueue = array();

    /** @var string[] */
    public $needWriteFiles = array();

    /** @var bool  */
    public $needUpgrade = false;

    /**
     * @var array support sites pulled from language files -- support.php
     */
    public $supportSites = array();

    /** @var bool */
    public $needMainfileRewrite = false;

    /** @var string[]  */
    public $mainfileKeys = array();

    /**
     * @var string language being used in the upgrade process
     */
    public $upgradeLanguage;

    /**
     * get a list of directories inside a directory
     *
     * @param string $dirname directory to search
     *
     * @return string[]
     */
    public function getDirList($dirname)
    {
        $dirlist = array();
        if (is_dir($dirname) && $handle = opendir($dirname)) {
            while (false !== ($file = readdir($handle))) {
                if (substr($file, 0, 1) !== '.' && strtolower($file) !== 'cvs') {
                    if (is_dir("{$dirname}/{$file}")) {
                        $dirlist[] = $file;
                    }
                }
            }
            closedir($handle);
            asort($dirlist);
            reset($dirlist);
        }

        return $dirlist;
    }

    /**
     * @return string[]
     */
    public function availableLanguages()
    {
        $languages = $this->getDirList('./language/');
        return $languages;
    }


    public function loadLanguage($domain, $language = null)
    {
        $supports = null;

        $language = (null === $language) ? $this->upgradeLanguage : $language;

        if (file_exists(__DIR__ . "/../language/{$language}/{$domain}.php")) {
            include_once __DIR__ . "/../language/{$language}/{$domain}.php";
        } elseif (file_exists(__DIR__ . "/../language/english/{$domain}.php")) {
            include_once __DIR__ . "/../language/english/{$domain}.php";
        }


        if (null !== $supports) {
            $this->supportSites = array_merge($this->supportSites, $supports);
        }
    }

    /**
     * Determine the language to use.
     *  - Xoops configuration
     *  - stored cookie
     *  - lang parameter passed to script
     * Save the result in a cookie
     *
     * @return string the language to use in the upgrade process
     */
    public function determineLanguage()
    {
        global $xoopsConfig;

        $upgrade_language = null;
        if (isset($xoopsConfig['language'])) {
            $upgrade_language = $xoopsConfig['language'];
        }
        $upgrade_language = !empty($_COOKIE['xo_upgrade_lang']) ? $_COOKIE['xo_upgrade_lang'] : $upgrade_language;
        $upgrade_language = Xmf\Request::getString('lang', $upgrade_language);
        $upgrade_language = (null === $xoopsConfig['language']) ? 'english' : $upgrade_language;
        xoops_setcookie('xo_upgrade_lang', $upgrade_language, null, null, null);

        $this->upgradeLanguage = $upgrade_language;
        $this->loadLanguage('upgrade');

        return $this->upgradeLanguage;
    }

    /**
     * Examine upgrade directories and determine:
     *  - which tasks need to run
     *  - which files need to be writable
     *
     * @return bool true of upgrade is needed
     */
    public function buildUpgradeQueue()
    {
        $dirs = $this->getDirList('.');

        /** @var PatchStatus[] $results */
        $results     = array();
        $files       = array();
        $this->needUpgrade = false;

        foreach ($dirs as $dir) {
            if (strpos($dir, '-to-')) {
                $upgrader = include "{$dir}/index.php";
                if (is_object($upgrader)) {
                    $results[$dir] = $upgrader->isApplied();
                    if (!($results[$dir]->applied)) {
                        $this->needUpgrade = true;
                        if (!empty($results[$dir]->files)) {
                            $files = array_merge($files, $upgrader->usedFiles);
                        }
                    }
                }
            }
        }

        if ($this->needUpgrade && !empty($files)) {
            foreach ($files as $k => $file) {
                $testFile = preg_match('/^([.\/\\\\:])|([a-z]:)/i', $file) ? $file : "../{$file}";
                if (is_writable($testFile) || !file_exists($testFile)) {
                    unset($files[$k]);
                }
            }
        }

        $this->upgradeQueue = $results;
        $this->needWriteFiles = $files;

        return $this->needUpgrade;
    }

    /**
     * Get count of patch sets that need to be applied.
     *
     * @return int count of patch sets to apply
     */
    public function countUpgradeQueue()
    {
        if (empty($this->upgradeQueue)) {
            $this->buildUpgradeQueue();
        }
        $count = 0;
        foreach ($this->upgradeQueue as $patch) {
            $count += ($patch->applied) ? 0 : 1;
        }
        return $count;
    }

    /**
     * @return string next unapplied patch directory
     */
    public function getNextPatch()
    {
        if (empty($this->upgradeQueue)) {
            $this->buildUpgradeQueue();
        }
        $next = false;

        foreach ($this->upgradeQueue as $directory => $patch) {
            if (!$patch->applied) {
                $next =  $directory;
                break;
            }
        }
        return $next;
    }

    /**
     * Return form consisting of a single button.
     *
     * @param string     $action     URL for form action
     * @param array|null $parameters array of parameters
     *
     * @return string
     */
    public function oneButtonContinueForm($action = 'index.php', $parameters = array('action' =>'next'))
    {
        $form  = '<form action="' . $action . '" method="post">';
        $form .= '<button class="btn btn-lg btn-success" type="submit">' . _CONTINUE;
        $form .= '  <span class="fa fa-caret-right"></span></button>';
        foreach ($parameters as $name => $value) {
            $form .= '<input type="hidden" name="' . $name . '" value="' . $value . '">';
        }
        $form .= '</form>';

        return $form;
    }

    public function storeMainfileCheck($needMainfileRewrite, $mainfileKeys)
    {
        $this->needMainfileRewrite = $needMainfileRewrite;

        if ($needMainfileRewrite) {
            $this->mainfileKeys = $mainfileKeys;
        }
    }

}
