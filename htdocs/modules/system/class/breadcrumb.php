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
 * BreadCrumb Class
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author              Andricq Nicolas (AKA MusS)
 * @package             system
 */
class SystemBreadcrumb
{
    /* Variables */
    public $_directory;
    public $_bread = array();
    public $_help;
    public $_tips;

    /**
     * @param $directory
     */
    public function __construct($directory)
    {
        $this->_directory = $directory;
    }

    /**
     * Add link to breadcrumb
     * @param string $title
     * @param string $link
     * @param bool   $home
     */
    public function addLink($title = '', $link = '', $home = false)
    {
        $this->_bread[] = array(
            'link'  => $link,
            'title' => $title,
            'home'  => $home);
    }

    /**
     * Add Help link
     * @param string $link
     */
    public function addHelp($link = '')
    {
        $this->_help = $link;
    }

    /**
     * Add Tips
     * @param $value
     */
    public function addTips($value)
    {
        $this->_tips = $value;
    }

    /**
     * Render System BreadCrumb
     *
     */
    public function render()
    {
        if (isset($GLOBALS['xoopsTpl'])) {
            $GLOBALS['xoopsTpl']->assign('xo_sys_breadcrumb', $this->_bread);
            $GLOBALS['xoopsTpl']->assign('xo_sys_help', $this->_help);
            if ($this->_tips) {
                if (xoops_getModuleOption('usetips', 'system')) {
                    $GLOBALS['xoopsTpl']->assign('xo_sys_tips', $this->_tips);
                }
            }
            // Call template
            if (file_exists(XOOPS_ROOT_PATH . '/modules/system/language/' . $GLOBALS['xoopsConfig']['language'] . '/help/' . $this->_directory . '.html')) {
                $GLOBALS['xoopsTpl']->assign('help_content', XOOPS_ROOT_PATH . '/modules/system/language/' . $GLOBALS['xoopsConfig']['language'] . '/help/' . $this->_directory . '.html');
            } else {
                if (file_exists(XOOPS_ROOT_PATH . '/modules/system/language/english/help/' . $this->_directory . '.html')) {
                    $GLOBALS['xoopsTpl']->assign('help_content', XOOPS_ROOT_PATH . '/modules/system/language/english/help/' . $this->_directory . '.html');
                } else {
                    $GLOBALS['xoopsTpl']->assign('load_error', 1);
                }
            }
        } else {
            $out = $menu = '<style type="text/css" media="screen">@import ' . XOOPS_URL . '/modules/system/css/menu.css;</style>';
            $out .= '<ul id="xo-breadcrumb">';
            foreach ($this->_bread as $menu) {
                if ($menu['home']) {
                    $out .= '<li><a href="' . $menu['link'] . '" title="' . $menu['title'] . '"><img src="images/home.png" alt="' . $menu['title'] . '" class="home" /></a></li>';
                } else {
                    if ($menu['link'] != '') {
                        $out .= '<li><a href="' . $menu['link'] . '" title="' . $menu['title'] . '">' . $menu['title'] . '</a></li>';
                    } else {
                        $out .= '<li>' . $menu['title'] . '</li>';
                    }
                }
            }
            $out .= '</ul>';
            if ($this->_tips) {
                $out .= '<div class="tips">' . $this->_tips . '</div>';
            }
            echo $out;
        }
    }
}
