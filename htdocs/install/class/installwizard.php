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
 * See the enclosed file license.txt for licensing information.
 * If you did not receive this file, get it at http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           DuGris (aka L. JEN) <dugris@frxoops.org>
 */
class XoopsInstallWizard
{
    public $language    = 'english';
    public $pages       = array();
    public $currentPage = 'langselect';
    public $pageIndex   = 0;
    public $configs     = array();

    /**
     * @return bool
     */
    public function xoInit()
    {
        if (@empty($_SERVER['REQUEST_URI'])) {
            $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'];
        }

        // Load the main language file
        $this->initLanguage(!empty($_COOKIE['xo_install_lang']) ? $_COOKIE['xo_install_lang'] : 'english');
        // Setup pages
        include_once './include/page.php';
        $this->pages = $pages;

        // Load default configs
        include_once './include/config.php';
        $this->configs = $configs;
        /*
        // Database type
        $this->db_types  = $db_types;

        // setup config site info
        $this->conf_names  = $conf_names;

        // languages config files
        $this->language_files = $language_files;

        // extension_loaded
        $this->extensions = $extensions;

        // Modules to be installed by default
        $this->modules = $modules;

        // xoops_lib, xoops_data directories
        $this->xoopsPathDefault = $xoopsPathDefault;

        // writable xoops_lib, xoops_data directories
        $this->dataPath = $dataPath;

        // Protector default trust_path
        $this->trust_path = isset($trust_path) ? $trust_path : false;

        // Writable files and directories
        $this->writable = $writable;
        */

        if (!$this->checkAccess()) {
            return false;
        }

        $pagename = preg_replace('~(page_)(.*)~', '$2', basename($_SERVER['PHP_SELF'], '.php'));
        $this->setPage($pagename);

        // Prevent client caching
        header('Cache-Control: no-store, no-cache, must-revalidate', false);
        header('Pragma: no-cache');

        return true;
    }

    /**
     * @return bool
     */
    public function checkAccess()
    {
        if (INSTALL_USER != '' && INSTALL_PASSWORD != '') {
            if (!isset($_SERVER['PHP_AUTH_USER'])) {
                header('WWW-Authenticate: Basic realm="XOOPS Installer"');
                header('HTTP/1.0 401 Unauthorized');
                echo 'You can not access this XOOPS installer.';

                return false;
            }
            if (INSTALL_USER != '' && $_SERVER['PHP_AUTH_USER'] != INSTALL_USER) {
                header('HTTP/1.0 401 Unauthorized');
                echo 'You can not access this XOOPS installer.';

                return false;
            }
            if (INSTALL_PASSWORD != $_SERVER['PHP_AUTH_PW']) {
                header('HTTP/1.0 401 Unauthorized');
                echo 'You can not access this XOOPS installer.';

                return false;
            }
        }

        if (empty($GLOBALS['xoopsOption']['checkadmin'])) {
            return true;
        }

        if (empty($GLOBALS['xoopsUser']) && !empty($_COOKIE['xo_install_user'])) {
            return install_acceptUser($_COOKIE['xo_install_user']);
        }
        if (empty($GLOBALS['xoopsUser'])) {
            redirect_header('../user.php');
        }
        if (!$GLOBALS['xoopsUser']->isAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * @param $file
     */
    public function loadLangFile($file)
    {
        if (file_exists("./language/{$this->language}/{$file}.php")) {
            include_once "./language/{$this->language}/{$file}.php";
        } else {
            include_once "./language/english/$file.php";
        }
    }

    /**
     * @param $language
     */
    public function initLanguage($language)
    {
        $language = preg_replace("/[^a-z0-9_\-]/i", '', $language);
        if (!file_exists("./language/{$language}/install.php")) {
            $language = 'english';
        }
        $this->language = $language;
        $this->loadLangFile('install');
    }

    /**
     * @param $page
     *
     * @return bool|mixed
     */
    public function setPage($page)
    {
        $pages = array_keys($this->pages);
        if ((int)$page && $page >= 0 && $page < count($pages)) {
            $this->pageIndex   = $page;
            $this->currentPage = $pages[$page];
        } elseif (isset($this->pages[$page])) {
            $this->currentPage = $page;
            $this->pageIndex   = array_search($this->currentPage, $pages);
        } else {
            return false;
        }

        if ($this->pageIndex > 0 && !isset($_COOKIE['xo_install_lang'])) {
            header('Location: index.php');
        }

        return $this->pageIndex;
    }

    /**
     * @return string
     */
    public function baseLocation()
    {
        $proto = (@$_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host  = $_SERVER['HTTP_HOST'];
        $base  = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));

        return $proto . '://' . $host . $base;
    }

    /**
     * @param $page
     *
     * @return string
     */
    public function pageURI($page)
    {
        $pages     = array_keys($this->pages);
        $pageIndex = $this->pageIndex;
        if (!(int)$page{0}) {
            if ($page{0} == '+') {
                $pageIndex += substr($page, 1);
            } elseif ($page{0} == '-') {
                $pageIndex -= substr($page, 1);
            } else {
                $pageIndex = (int)array_search($page, $pages);
            }
        }
        if (!isset($pages[$pageIndex])) {
            if (defined('XOOPS_URL')) {
                return XOOPS_URL;
            } else {
                return $this->baseLocation();
            }
        }
        $page = $pages[$pageIndex];

        return $this->baseLocation() . "/page_{$page}.php";
    }

    /**
     * @param        $page
     * @param int    $status
     * @param string $message
     */
    public function redirectToPage($page, $status = 303, $message = 'See other')
    {
        $location = $this->pageURI($page);
        $proto    = !@empty($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
        header("{$proto} {$status} {$message}");
        //header( "Status: $status $message" );
        header("Location: {$location}");
    }

    /**
     * @return string
     */
    public function CreateForm()
    {
        $hidden = '';
        $ret    = '';

        foreach ($this->form as $form) {
            $ret .= '<div class="panel panel-info">';
            $ret .= '<div class="panel-heading">' . $form->getTitle() . '</div>';
            $ret .= '<div class="panel-body">';

            foreach ($form->getElements() as $ele) {
                if (is_object($ele)) {
                    if (!$ele->isHidden()) {
                        if (($caption = $ele->getCaption()) != '') {
                            $name = $ele->getName();
                            $ret .= "<label class='xolabel' for='" . $ele->getName() . "'>" . $caption . '</label>';
                            if (($desc = $ele->getDescription()) != '') {
                                $ret .= "<div class='xoform-help  alert alert-info'>";
                                $ret .= $desc;
                                $ret .= '</div>';
                            }
                        }
                        $ret .= $ele->render() . "\n";
                    } else {
                        $hidden .= $ele->render() . "\n";
                    }
                }
            }
            $ret .= "</div></div>\n" . $hidden . "\n" . $form->renderValidationJS(true);
        }

        return $ret;
    }
}
