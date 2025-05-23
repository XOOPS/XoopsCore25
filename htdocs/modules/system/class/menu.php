<?php
/**
 * Class for tab navigation
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2025 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author              John Neill (AKA Catzwolf)
 * @author              Andricq Nicolas (AKA MusS)
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * Class SystemMenuHandler
 */
class SystemMenuHandler
{
    /**
     *
     * @var string
     */
    public $_menutop  = [];
    public $_menutabs = [];
    public $_obj;
    public $_header;
    public $_subheader;

    /**
     * Constructor
     */
    public function __construct()
    {
        global $xoopsModule;
        $this->_obj = $xoopsModule;
    }

    /**
     * @param $addon
     */
    public function getAddon($addon)
    {
        $this->_obj =& $addon;
    }

    /**
     * @param        $value
     * @param string $name
     */
    public function addMenuTop($value, $name = '')
    {
        if ($name !== '') {
            $this->_menutop[$value] = $name;
        } else {
            $this->_menutop[$value] = $value;
        }
    }

    /**
     * @param      $options
     * @param bool $multi
     */
    public function addMenuTopArray($options, $multi = true)
    {
        if (is_array($options)) {
            if ($multi === true) {
                foreach ($options as $k => $v) {
                    $this->addOptionTop($k, $v);
                }
            } else {
                foreach ($options as $k) {
                    $this->addOptiontop($k, $k);
                }
            }
        }
    }

    /**
     * @param        $value
     * @param string $name
     */
    public function addMenuTabs($value, $name = '')
    {
        if ($name !== '') {
            $this->_menutabs[$value] = $name;
        } else {
            $this->_menutabs[$value] = $value;
        }
    }

    /**
     * @param      $options
     * @param bool $multi
     */
    public function addMenuTabsArray($options, $multi = true)
    {
        if (is_array($options)) {
            if ($multi === true) {
                foreach ($options as $k => $v) {
                    $this->addMenuTabsTop($k, $v);
                }
            } else {
                foreach ($options as $k) {
                    $this->addMenuTabsTop($k, $k);
                }
            }
        }
    }

    /**
     * @param $value
     */
    public function addHeader($value)
    {
        $this->_header = $value;
    }

    /**
     * @param $value
     */
    public function addSubHeader($value)
    {
        $this->_subheader = $value;
    }

    /**
     * @param string $basename
     *
     * @return string
     */
    public function breadcrumb_nav($basename = 'Home')
    {
        global $bc_site, $bc_label;
        $site       = $bc_site;
        $return_str = "<a href=\"/\">$basename</a>";
        $str        = substr(dirname(xoops_getenv('PHP_SELF')), 1);

        $arr = explode('/', $str);
        $num = count($arr);

        if ($num > 1) {
            foreach ($arr as $val) {
                $return_str .= ' &gt; <a href="' . $site . $val . '/">' . $bc_label[$val] . '</a>';
                $site .= $val . '/';
            }
        } elseif ($num == 1) {
            $arr = $str;
            $return_str .= ' &gt; <a href="' . $bc_site . $arr . '/">' . $bc_label[$arr] . '</a>';
        }

        return $return_str;
    }

    /**
     * @param int  $currentoption
     * @param bool $display
     *
     * @return string
     */
    public function render($currentoption = 1, $display = true)
    {
        global $modversion;
        $_dirname = $this->_obj->getVar('dirname');
        $i        = 0;

        /**
         * Select current menu tab, sets id names for menu tabs
         */
        $j=0;
        foreach ($this->_menutabs as $k => $menus) {
            if ($j == $currentoption) {
                $breadcrumb = $menus;
            }
            $menuItems[] = 'modmenu_' . $j++;
        }

        $menuItems[$currentoption] = 'current';
        $menu                      = "<div id='buttontop_mod'>";
        $menu .= "<table style='width: 100%; padding: 0;' cellspacing='0'>\n<tr>";
        $menu .= "<td style='font-size: 10px; text-align: left; color: #2F5376; padding: 0 6px; line-height: 18px;'>";
        foreach ($this->_menutop as $k => $v) {
            $menu .= " <a href=\"$k\">$v</a> |";
        }
        $menu = substr($menu, 0, -1);

        $menu .= '</td>';
        $menu .= "<td style='text-align: right;'><strong>" . $this->_obj->getVar('name') . '</strong> : ' . $breadcrumb . '</td>';
        $menu .= "</tr>\n</table>\n";
        $menu .= "</div>\n";
        $menu .= "<div id='buttonbar_mod'><ul>";
        foreach ($this->_menutabs as $k => $v) {
            $menu .= "<li id='" . $menuItems[$i] . "'><a href='" . XOOPS_URL . '/modules/' . $this->_obj->getVar('dirname') . '/' . $k . "'><span>$v</span></a></li>\n";
            ++$i;
        }
        $menu .= "</ul>\n</div>\n";
        if ($this->_header) {
            $menu .= "<h4 class='admin_header'>";
            if (isset($modversion['name'])) {
                if ($modversion['image'] && $this->_obj->getVar('mid') == 1) {
                    $system_image = XOOPS_URL . '/modules/system/images/system/' . $modversion['image'];
                } else {
                    $system_image = XOOPS_URL . '/modules/' . $_dirname . '/images/' . $modversion['image'];
                }
                $menu .= "<img src='$system_image' align='middle' height='32' width='32' alt='' />";
                $menu .= ' ' . $modversion['name'] . "</h4>\n";
            } else {
                $menu .= ' ' . $this->_header . "</h4>\n";
            }
        }
        if ($this->_subheader) {
            $menu .= "<div class='admin_subheader'>" . $this->_subheader . "</div>\n";
        }
        $menu .= '<div class="clear">&nbsp;</div>';
        unset($this->_obj);
        if ($display === true) {
            echo $menu;
        } else {
            return $menu;
        }

        return null;
    }
}
