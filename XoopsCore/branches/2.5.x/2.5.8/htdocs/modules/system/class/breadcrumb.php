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
 * @copyright       (c) 2000-2014 XOOPS Project (www.xoops.org)
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      Andricq Nicolas (AKA MusS)
 * @package     system
 * @version     $Id$
 */

class SystemBreadcrumb
{
    /* Variables */
    var $_directory;
    var $_bread = array();
    var $_help;
    var $_tips;

    /**
     * @param $directory
     */
    function __construct( $directory )
    {
        $this->_directory = $directory;
    }

    /**
     * Add link to breadcrumb
     *
     */
    function addLink( $title='', $link='', $home=false )
    {
        $this->_bread[] = array(
            'link'  => $link,
            'title' => $title,
            'home'  => $home
            );
    }

    /**
     * Add Help link
     *
     */
    function addHelp( $link = '')
    {
        $this->_help = $link;
    }

    /**
     * Add Tips
     *
     */
    function addTips($value)
    {
        $this->_tips = $value;
    }

    /**
     * Render System BreadCrumb
     *
     */
    function render()
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
                if ( file_exists( XOOPS_ROOT_PATH . '/modules/system/language/' . $GLOBALS['xoopsConfig']['language'] . '/help/' . $this->_directory . '.html' ) ) {
                    $GLOBALS['xoopsTpl']->assign( 'help_content', XOOPS_ROOT_PATH . '/modules/system/language/' . $GLOBALS['xoopsConfig']['language'] . '/help/' . $this->_directory . '.html' );
                } else {
                    if ( file_exists( XOOPS_ROOT_PATH . '/modules/system/language/english/help/' . $this->_directory . '.html' ) ) {
                        $GLOBALS['xoopsTpl']->assign( 'help_content', XOOPS_ROOT_PATH.'/modules/system/language/english/help/' . $this->_directory . '.html' );
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
