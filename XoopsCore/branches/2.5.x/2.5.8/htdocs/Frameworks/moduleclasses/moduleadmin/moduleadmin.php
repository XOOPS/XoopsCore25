<?php
/**
 * Frameworks Module Admin
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright   Grégory Mage (Aka Mage)
 * @license     GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author      Grégory Mage (Aka Mage)
 */

class ModuleAdmin
{

    var $_itemButton = array();
    var $_itemInfoBox = array();
    var $_itemInfoBoxLine = array();
    var $_itemConfigBoxLine = array();
    var $_obj = array();

    /**
     * Constructor
     */
    function __construct()
    {
        //global $xoopsModule, $xoTheme;
        global $xoopsModule;
        $this->_obj =& $xoopsModule;
        echo "<style type=\"text/css\" media=\"screen\">@import \"" . XOOPS_URL . "/Frameworks/moduleclasses/moduleadmin/css/admin.css\";</style>";
        //$xoTheme->addStylesheet("Frameworks/moduleclasses/moduleadmin/css/admin.css");
        $this -> loadLanguage();
    }

    /**
     * @return array
     */
    function getInfo()
    {
       $infoArray = array();
        if (!isset($infoArray) or empty($infoArray)) {
            $infoArray = array();
            $infoArray['version'] = $this->getVersion();
            $infoArray['releasedate'] = $this->getReleaseDate();
            $infoArray['methods'] = $this->getClassMethods();
        }

        return $infoArray;
    }

    /**
     * Return the Module Admin class version number
     * return string version
     **/
    function getVersion()
    {
        /**
         * version is rev of this class
         */
        Include_once 'xoops_version.php';
        $version = XOOPS_FRAMEWORKS_MODULEADMIN_VERSION;

        return $version;
    }

    /**
     * Return the Module Admin class release date
     * return string version
     **/
    function getReleaseDate()
    {
        /**
         * version is rev of this class
         */
        Include_once 'xoops_version.php';
        $releasedate = XOOPS_FRAMEWORKS_MODULEADMIN_RELEASEDATE;

        return $releasedate;
    }

    /**
     * Return the available methods for the class
     *
     * @return array methods supported by this class
     */
    function getClassMethods()
    {
        $myMethods = get_class_methods(__CLASS__);

        return $myMethods;
    }

    //******************************************************************************************************************
    // loadLanguage
    //******************************************************************************************************************
    // Loaf the language file.
    //******************************************************************************************************************
    /**
     * @return bool|mixed
     */
    function loadLanguage()
    {
        $language = $GLOBALS['xoopsConfig']['language'];
        if ( !file_exists($fileinc = XOOPS_ROOT_PATH . "/Frameworks/moduleclasses/moduleadmin/language/{$language}/main.php" )) {
            if ( !file_exists($fileinc = XOOPS_ROOT_PATH . "/Frameworks/moduleclasses/moduleadmin/language/english/main.php" )) {
                return false;
            }
        }
        $ret = include_once $fileinc;

        return $ret;
    }
    //******************************************************************************************************************
    // renderMenuIndex
    //******************************************************************************************************************
    // Creating a menu icon in the index
    //******************************************************************************************************************
    /**
     * @return string
     */
    function renderMenuIndex()
    {
        $path = XOOPS_URL . "/modules/" . $this->_obj->getVar('dirname') . "/";
        $pathsystem = XOOPS_URL . "/modules/system/";
        $this->_obj->loadAdminMenu();
        $ret = "<div class=\"rmmenuicon\">\n";
        foreach (array_keys( $this->_obj->adminmenu) as $i) {
            if ($this->_obj->adminmenu[$i]['link'] != 'admin/index.php') {
                $ret .= "<a href=\"../" . $this->_obj->adminmenu[$i]['link'] . "\" title=\"" . (isset($this->_obj->adminmenu[$i]['desc']) ? $this->_obj->adminmenu[$i]['desc'] : '') . "\">";
                $ret .= "<img src=\"" . $path . $this->_obj->adminmenu[$i]['icon']. "\" alt=\"" . $this->_obj->adminmenu[$i]['title'] . "\" />";
                $ret .= "<span>" . $this->_obj->adminmenu[$i]['title'] . "</span>";
                $ret .= "</a>";
            }
        }
        if ($this->_obj->getInfo('help')) {
            if (substr(XOOPS_VERSION, 0, 9) >= 'XOOPS 2.5') {
                $ret .= "<a href=\"" . $pathsystem . "help.php?mid=" . $this->_obj->getVar('mid', 's') . "&amp;" . $this->_obj->getInfo('help') . "\" title=\"" . _AM_SYSTEM_HELP . "\">";
                $ret .= "<img width=\"32px\" src=\"" . XOOPS_URL . "/Frameworks/moduleclasses/icons/32/help.png\" alt=\"" . _AM_SYSTEM_HELP . "\" /> ";
                $ret .= "<span>" . _AM_SYSTEM_HELP . "</span>";
                $ret .= "</a>";
            }
        }
        $ret .= "</div>\n<div style=\"clear: both;\"></div>\n";

        return $ret;
    }
    //******************************************************************************************************************
    // renderButton
    //******************************************************************************************************************
    // Creating button
    //******************************************************************************************************************
    /**
     * @param string $position
     * @param string $delimeter
     *
     * @return string
     */
    function renderButton($position = "right", $delimeter = "&nbsp;")
    {
        $path = XOOPS_URL . "/Frameworks/moduleclasses/icons/32/";
        switch ($position) {
            default:
            case "right":
                $ret = "<div class=\"floatright\">\n";
                break;

            case "left":
                $ret = "<div class=\"floatleft\">\n";
                break;

            case "center":
                $ret = "<div class=\"aligncenter\">\n";
        }
        $ret .= "<div class=\"xo-buttons\">\n";
        foreach (array_keys( $this -> _itemButton) as $i) {
            $ret .= "<a class='ui-corner-all tooltip' href='" . $this -> _itemButton[$i]['link'] . "' title='" . $this -> _itemButton[$i]['title'] . "'>";
            $ret .= "<img src='" . $path . $this -> _itemButton[$i]['icon'] . "' title='" . $this -> _itemButton[$i]['title'] . "' />" . $this -> _itemButton[$i]['title'] . ' ' . $this -> _itemButton[$i]['extra'];
            $ret .= "</a>\n";
            $ret .= $delimeter;
        }
        $ret .= "</div>\n</div>\n";
        $ret .= "<br />&nbsp;<br /><br />";

        return $ret;
    }

    /**
     * @param        $title
     * @param        $link
     * @param string $icon
     * @param string $extra
     *
     * @return bool
     */
    function addItemButton($title, $link, $icon = 'add', $extra = '')
    {
        $ret['title'] = $title;
        $ret['link'] = $link;
        $ret['icon'] = $icon . '.png';
        $ret['extra'] = $extra;
        $this -> _itemButton[] = $ret;

        return true;

    }
    //******************************************************************************************************************
    // addConfigBoxLine
    //******************************************************************************************************************
    // $value: value
    // $type: type of config:   1- "default": Just a line with value.
    //                          2- "folder": check if this is an folder.
    //                          3- "chmod": check if this is the good chmod.
    //                                      For this type ("chmod"), the value is an array: array(path, chmod)
    //******************************************************************************************************************
    /**
     * @param string $value
     * @param string $type
     *
     * @return bool
     */
    function addConfigBoxLine($value = '', $type = 'default')
    {
        $line = "";
        $path = XOOPS_URL . "/Frameworks/moduleclasses/icons/16/";
        switch ($type) {
            default:
            case "default":
                $line .= "<span>" . $value . "</span>";
                break;

            case "folder":
                if (!is_dir($value)) {
                    $line .= "<span style='color : red; font-weight : bold;'>";
                    $line .= "<img src='" . $path . "0.png' >";
                    $line .= sprintf(_AM_MODULEADMIN_CONFIG_FOLDERKO, $value);
                    $line .= "</span>\n";
                } else {
                    $line .= "<span style='color : green;'>";
                    $line .= "<img src='" . $path . "1.png' >";
                    $line .= sprintf(_AM_MODULEADMIN_CONFIG_FOLDEROK, $value);
                    $line .= "</span>\n";
                }
                break;

            case "chmod":
                if (is_dir($value[0])) {
                    if (substr(decoct(fileperms($value[0])),2) != $value[1]) {
                        $line .= "<span style='color : red; font-weight : bold;'>";
                        $line .= "<img src='" . $path . "0.png' >";
                        $line .= sprintf(_AM_MODULEADMIN_CONFIG_CHMOD, $value[0], $value[1], substr(decoct(fileperms($value[0])),2));
                        $line .= "</span>\n";
                    } else {
                        $line .= "<span style='color : green;'>";
                        $line .= "<img src='" . $path . "1.png' >";
                        $line .= sprintf(_AM_MODULEADMIN_CONFIG_CHMOD, $value[0], $value[1], substr(decoct(fileperms($value[0])),2));
                        $line .= "</span>\n";
                    }
                }
                break;
        }
        $this -> _itemConfigBoxLine[] = $line;

        return true;
    }
    //******************************************************************************************************************
    // renderIndex
    //******************************************************************************************************************
    // Creating an index
    //******************************************************************************************************************
    /**
     * @return string
     */
    function renderIndex()
    {
        $ret = "<table>\n<tr>\n";
        $ret .= "<td width=\"40%\">\n";
        $ret .= $this -> renderMenuIndex();
        $ret .= "</td>\n";
        $ret .= "<td width=\"60%\">\n";
        $ret .= $this -> renderInfoBox();
        $ret .= "</td>\n";
        $ret .= "</tr>\n";
        // If you use a config label
        if ($this->_obj->getInfo('min_php') || $this->_obj->getInfo('min_xoops') || !empty($this -> _itemConfigBoxLine)) {
            $ret .= "<tr>\n";
            $ret .= "<td colspan=\"2\">\n";
            $ret .= "<fieldset><legend class=\"label\">";
            $ret .= _AM_MODULEADMIN_CONFIG;
            $ret .= "</legend>\n";

            // php version
            $path = XOOPS_URL . "/Frameworks/moduleclasses/icons/16/";
            if ($this->_obj->getInfo('min_php')) {
                if (version_compare(phpversion(), $this->_obj->getInfo('min_php'), '<')) {
                    $ret .= "<span style='color : red; font-weight : bold;'><img src='" . $path . "0.png' >" . sprintf(_AM_MODULEADMIN_CONFIG_PHP, $this->_obj->getInfo('min_php'), phpversion()) . "</span>\n";
                } else {
                    $ret .= "<span style='color : green;'><img src='" . $path . "1.png' >" . sprintf(_AM_MODULEADMIN_CONFIG_PHP, $this->_obj->getInfo('min_php'), phpversion()) . "</span>\n";
                }
                $ret .= "<br />";
            }

            // Database version
            $path = XOOPS_URL . "/Frameworks/moduleclasses/icons/16/";
            $dbarray=$this->_obj->getInfo('min_db');

            if ($dbarray[XOOPS_DB_TYPE]) {
                // changes from redheadedrod to use connector specific version info
                switch (XOOPS_DB_TYPE) {
                    // server should be the same in both cases
                    case "mysql":
                    case "mysqli":
                        global $xoopsDB;
                        $dbCurrentVersion= $xoopsDB->getServerVersion();
                        break;
                    //case "pdo":
                    //    global $xoopsDB;
                    //    $dbCurrentVersion = $xoopsDB->getAttribute(PDO::ATTR_SERVER_VERSION);
                    //    break;
                    default: // don't really support anything other than mysql
                        $dbCurrentVersion = '0';
                        break;
                }
                $currentVerParts = explode('.', (string) $dbCurrentVersion);
                $iCurrentVerParts = array_map('intval', $currentVerParts);
                $dbRequiredVersion = $dbarray[XOOPS_DB_TYPE];
                $reqVerParts = explode('.', (string) $dbRequiredVersion);
                $iReqVerParts = array_map('intval', $reqVerParts);
                $icount = $j = count($iReqVerParts);
                $reqVer = $curVer = 0;
                for ($i=0; $i<$icount; $i++) {
                    $j--;
                    $reqVer += $iReqVerParts[$i] * pow(10, $j);
                    if (isset($iCurrentVerParts[$i])) {
                        $curVer += $iCurrentVerParts[$i] * pow(10, $j);
                    } else {
                        $curVer = $curVer * pow(10, $j);
                    }
                }
                if ($reqVer > $curVer) {
                    $ret .= "<span style='color : red; font-weight : bold;'><img src='" . $path . "0.png' >" . sprintf(XOOPS_DB_TYPE.' '._AM_MODULEADMIN_CONFIG_DB, $dbRequiredVersion, $dbCurrentVersion) . "</span><br />\n";
                } else {
                    $ret .= "<span style='color : green;'><img src='" . $path . "1.png' >" . sprintf(strtoupper(XOOPS_DB_TYPE).' '._AM_MODULEADMIN_CONFIG_DB, $dbRequiredVersion, $dbCurrentVersion) . "</span><br />\n";
                }
            }

            // xoops version
            if ($this->_obj->getInfo('min_xoops')) {
                if (substr(XOOPS_VERSION, 6, strlen(XOOPS_VERSION)-6) < $this->_obj->getInfo('min_xoops')) {
                    $ret .= "<span style='color : red; font-weight : bold;'><img src='" . $path . "0.png' >" . sprintf(_AM_MODULEADMIN_CONFIG_XOOPS, $this->_obj->getInfo('min_xoops'), substr(XOOPS_VERSION, 6, strlen(XOOPS_VERSION)-6)) . "</span>\n";
                } else {
                    $ret .= "<span style='color : green;'><img src='" . $path . "1.png' >" . sprintf(_AM_MODULEADMIN_CONFIG_XOOPS, $this->_obj->getInfo('min_xoops'), substr(XOOPS_VERSION, 6, strlen(XOOPS_VERSION)-6)) . "</span>\n";
                }
                $ret .= "<br />";
            }

            // ModuleAdmin version
            if ($this->_obj->getInfo('min_admin')) {
                if ($this->getVersion() < $this->_obj->getInfo('min_admin')) {
                    $ret .= "<span style='color : red; font-weight : bold;'><img src='" . $path . "0.png' >" . sprintf(_AM_MODULEADMIN_CONFIG_ADMIN, $this->_obj->getInfo('min_admin'), $this->getVersion()) . "</span>\n";
                } else {
                    $ret .= "<span style='color : green;'><img src='" . $path . "1.png' >" . sprintf(_AM_MODULEADMIN_CONFIG_ADMIN, $this->_obj->getInfo('min_admin'), $this->getVersion()) . "</span>\n";
                }
                $ret .= "<br />";
            }
            if (!empty($this -> _itemConfigBoxLine)) {
                foreach (array_keys( $this -> _itemConfigBoxLine) as $i) {
                    $ret .= $this -> _itemConfigBoxLine[$i];
                    $ret .= "<br />";
                }
            }
            $ret .= "</fieldset>\n";
            $ret .= "</td>\n";
            $ret .= "</tr>\n";
        }
        $ret .= "</table>\n";

        return $ret;
    }
    //******************************************************************************************************************
    // addInfoBox
    //******************************************************************************************************************
    // $title: title of an InfoBox
    //******************************************************************************************************************
    /**
     * @param $title
     *
     * @return bool
     */
    function addInfoBox($title)
    {
        $ret['title'] = $title;
        $this -> _itemInfoBox[] = $ret;

        return true;
    }
    //******************************************************************************************************************
    // addInfoBoxLine
    //******************************************************************************************************************
    // $label: title of InfoBox Line
    // $text:
    // $type: type of config:   1- "default": Just a line with value.
    //                          2- "information": check if this is an folder.
    //                          3- "chmod": check if this is the good chmod.
    //                                      For this type ("chmod"), the value is an array: array(path, chmod)
    //******************************************************************************************************************
    /**
     * @param        $label
     * @param        $text
     * @param string $value
     * @param string $color
     * @param string $type
     *
     * @return bool
     */
    function addInfoBoxLine($label, $text, $value = '', $color = 'inherit', $type = 'default')
    {
        $ret['label'] = $label;
        $line = "";
        switch ($type)
        {
            default:
            case "default":
                $line .= sprintf($text, "<span style='color : " . $color . "; font-weight : bold;'>" . $value . "</span>");
            break;

            case "information":
                $line .= $text;
                break;
        }
        $ret['line'] = $line;
        $this -> _itemInfoBoxLine[] = $ret;

        return true;
    }

    /**
     * @return string
     */
    function renderInfoBox()
    {
        $ret = "";
        foreach (array_keys( $this -> _itemInfoBox) as $i) {
            $ret .= "<fieldset><legend class=\"label\">";
            $ret .= $this -> _itemInfoBox[$i]['title'];
            $ret .= "</legend>\n";
            foreach (array_keys( $this -> _itemInfoBoxLine) as $k) {
                if ($this -> _itemInfoBoxLine[$k]['label'] == $this -> _itemInfoBox[$i]['title']) {
                    $ret .= $this -> _itemInfoBoxLine[$k]['line'];
                    $ret .= "<br />";
                }
            }
            $ret .= "</fieldset>\n";
            $ret .= "<br/>\n";
        }

        return $ret;
    }

    /**
     * @param string $paypal
     * @param bool   $logo_xoops
     *
     * @return string
     */
    function renderAbout($paypal = '', $logo_xoops = true)
    {
        $path = XOOPS_URL . "/Frameworks/moduleclasses/icons/32/";

        $ret = "<table>\n<tr>\n";
        $ret .= "<td width=\"50%\">\n";
        $date = explode('/',$this->_obj->getInfo('release_date'));
        $author = explode(',',$this->_obj->getInfo('author'));
        $nickname = explode(',',$this->_obj->getInfo('nickname'));
        $release_date = formatTimestamp(mktime(0, 0, 0, $date[1], $date[2], $date[0]), 's');
        $module_info = '<div id="about"><label>' . _AM_MODULEADMIN_ABOUT_DESCRIPTION . '</label><text>' . $this->_obj->getInfo("description") . '</text><br />
        <label>' . _AM_MODULEADMIN_ABOUT_UPDATEDATE . '</label><text class="bold">' . formatTimestamp($this->_obj->getVar("last_update"),"m") . '</text><br />
        <label>' . _AM_MODULEADMIN_ABOUT_MODULESTATUS . '</label><text>' . $this->_obj->getInfo("module_status") . '</text><br />
        <label>' . _AM_MODULEADMIN_ABOUT_WEBSITE . '</label><text><a class="tooltip" href="http://' . $this->_obj->getInfo("module_website_url") . '" rel="external" title="'. $this->_obj->getInfo("module_website_name") . ' - ' . $this->_obj->getInfo("module_website_url") . '">
                        ' . $this->_obj->getInfo("module_website_name") . '</a></text></div>';
        $ret .= "<table>\n<tr>\n<td width=\"100px\">\n";
        $ret .= "<img src='" . XOOPS_URL . "/modules/" . $this->_obj->getVar('dirname') . "/" . $this->_obj->getInfo('image') . "' alt='" . $this->_obj->getVar('name') . "' style='float: left; margin-right: 10px;' />\n";
        $ret .= "</td><td>\n";
        $ret .= "<div style='margin-top: 1px; margin-bottom: 4px; font-size: 18px; line-height: 18px; color: #2F5376; font-weight: bold;'>\n";
        $ret .= $this->_obj->getInfo('name') . " " . $this->_obj->getInfo('version') . " " . $this->_obj->getInfo('module_status') . " (" . $release_date . ")\n";
        $ret .= "<br />\n";
        $ret .= "</div>\n";
        $ret .= "<div style='line-height: 16px; font-weight: bold;'>\n";
        $ret .= "by ";
        foreach (array_keys($author) as $i) {
            $ret .= $author[$i];
            if (isset($nickname[$i]) && $nickname[$i] !='') {
                $ret .= " (" . $nickname[$i] . "), ";
            } else {
                $ret .= ", ";
            }
        }
        $ret = substr($ret,0,-2);
        $ret .= "</div>\n";
        $ret .= "<div style='line-height: 16px;'>\n";
                $ret.= "<a href=\"http://" . $this->_obj->getInfo('license_url'). "\" target=\"_blank\" >" . $this->_obj->getInfo('license'). "</a>\n";
        $ret .= "<br />\n";
        $ret .= "<a href=\"http://" . $this->_obj->getInfo('website') . "\" target=\"_blank\" >" . $this->_obj->getInfo('website') . "</a>\n";
        $ret .= "<br />\n";
        $ret .= "<br />\n";
        if ($paypal != '') {
            $ret .= '<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
                     <input type="hidden" name="cmd" value="_s-xclick">
                     <input type="hidden" name="hosted_button_id" value="' . $paypal . '">
                     <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                     <img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
                     </form>';
        }
        $ret .= "</div>\n";
        $ret .= "</td>\n</tr>\n</table>\n";
        $this -> addInfoBox(_AM_MODULEADMIN_ABOUT_MODULEINFO);
        $this -> addInfoBoxLine(_AM_MODULEADMIN_ABOUT_MODULEINFO, $module_info, '', '', 'information');
        $ret .= $this -> renderInfoBox();
        $ret .= "</td>\n";
        $ret .= "<td width=\"50%\">\n";
        $ret .= "<fieldset><legend class=\"label\">\n";
        $ret .= _AM_MODULEADMIN_ABOUT_CHANGELOG;
        $ret .= "</legend><br/>\n";
        $ret .= "<div class=\"txtchangelog\">\n";
        $language = $GLOBALS['xoopsConfig']['language'];
        if ( !is_file( XOOPS_ROOT_PATH . "/modules/" . $this->_obj->getVar("dirname") . "/language/" . $language . "/changelog.txt" ) ) {
            $language = 'english';
        }
        $language = empty($language) ? $GLOBALS['xoopsConfig']['language'] : $language;
        $file = XOOPS_ROOT_PATH. "/modules/" . $this->_obj->getVar("dirname") . "/language/" . $language . "/changelog.txt";
        if ( is_readable( $file ) ) {
            $ret .= utf8_encode(implode("<br />", file( $file ))) . "\n";
        } else {
            $file = XOOPS_ROOT_PATH. "/modules/" . $this->_obj->getVar("dirname") . "/docs/changelog.txt";
            if ( is_readable( $file ) ) {
                $ret .= utf8_encode(implode("<br />", file( $file ))) . "\n";
            }
        }
        $ret .= "</div>\n";
        $ret .= "</fieldset>\n";
        $ret .= "</td>\n";
        $ret .= "</tr>\n";
        $ret .= "</table>\n";
        if ($logo_xoops == true) {
            $ret .= "<div align=\"center\">";
            $ret .= "<a href=\"http://www.xoops.org\" target=\"_blank\"><img src=\"" . $path . "xoopsmicrobutton.gif\" alt=\"XOOPS\" title=\"XOOPS\"></a>";
            $ret .= "</div>";
        }

        return $ret;
    }

    /**
     * @param string $menu
     *
     * @return string
     */
    function addNavigation($menu = '')
    {
        $ret = "";
        $navigation = "";
        $path = XOOPS_URL . "/modules/" . $this->_obj->getVar('dirname') . "/";
        $this->_obj->loadAdminMenu();
        foreach (array_keys( $this->_obj->adminmenu) as $i) {
            if ($this->_obj->adminmenu[$i]['link'] == "admin/" . $menu) {
                $navigation .= $this->_obj->adminmenu[$i]['title'] . " | ";
                $ret = "<div class=\"CPbigTitle\" style=\"background-image: url(" . $path . $this->_obj->adminmenu[$i]['icon'] . "); background-repeat: no-repeat; background-position: left; padding-left: 50px;\">
        <strong>" . $this->_obj->adminmenu[$i]['title'] . "</strong></div><br />";
            } else {
                $navigation .= "<a href = '../" . $this->_obj->adminmenu[$i]['link'] . "'>" . $this->_obj->adminmenu[$i]['title'] . "</a> | ";
            }
        }
        if (substr(XOOPS_VERSION, 0, 9) < 'XOOPS 2.5') {
            $navigation .= "<a href = '../../system/admin.php?fct=preferences&op=showmod&mod=" . $this->_obj->getVar('mid') . "'>" . _MI_SYSTEM_ADMENU6 . "</a>";
            $ret = $navigation . "<br /><br />" . $ret;
        }

        return $ret;
    }
}
