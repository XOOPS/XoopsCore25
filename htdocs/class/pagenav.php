<?php
/**
 * XOOPS page navigation
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2021 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (http://www.myweb.ne.jp/, http://jp.xoops.org/)
 */

use Xmf\Request;

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Class XoopsPageNav
 */
class XoopsPageNav
{
    /**
     * *#@+
     *
     * @access private
     */
    public $total;
    public $perpage;
    public $current;
    public $url;
    public $extra;
    /**
     * *#@-
     */

    /**
     * Constructor
     *
     * @param int    $total_items   Total number of items
     * @param int    $items_perpage Number of items per page
     * @param int    $current_start First item on the current page
     * @param string $start_name    Name for "start" or "offset"
     * @param string $extra_arg     Additional arguments to pass in the URL
     */
    public function __construct($total_items, $items_perpage, $current_start, $start_name = 'start', $extra_arg = '')
    {
        $this->total   = (int)$total_items;
        $this->perpage = (int)$items_perpage;
        $this->current = (int)$current_start;
        $this->extra   = $extra_arg;
        if ($extra_arg != '' && (substr($extra_arg, -5) !== '&amp;' || substr($extra_arg, -1) !== '&')) {
            $this->extra = '&amp;' . $extra_arg;
        }
        $this->url = htmlspecialchars(Request::getString('PHP_SELF', '', 'SERVER'), ENT_QUOTES) . '?' . trim($start_name) . '=';
    }

    /**
     * Create text navigation
     *
     * @param  integer $offset
     * @return string
     */
    public function renderNav($offset = 4)
    {
        $ret = '';
        if ($this->total <= $this->perpage) {
            return $ret;
        }
        if (($this->total != 0) && ($this->perpage != 0)) {
			$navigation = array();
            $total_pages = ceil($this->total / $this->perpage);
            if ($total_pages > 1) {
				$i = 0;
                $prev = $this->current - $this->perpage;
                if ($prev >= 0) {
					$navigation[$i]['url'] = $this->url . $prev . $this->extra;
					$navigation[$i]['value'] = '';
					$navigation[$i]['option'] = 'first';
					++$i;
                }
                $counter      = 1;
                $current_page = (int)floor(($this->current + $this->perpage) / $this->perpage);
                while ($counter <= $total_pages) {					
                    if ($counter == $current_page) {
						$navigation[$i]['url'] = $this->url . $prev . $this->extra;
						$navigation[$i]['value'] = $counter;
						$navigation[$i]['option'] = 'selected';
                    } elseif (($counter > $current_page - $offset && $counter < $current_page + $offset) || $counter == 1 || $counter == $total_pages) {
                        if ($counter == $total_pages && $current_page < $total_pages - $offset) {
							$navigation[$i]['url'] = '';
							$navigation[$i]['value'] = '';
							$navigation[$i]['option'] = 'break';
							++$i;
                        }
						$navigation[$i]['url'] = $this->url . (($counter - 1) * $this->perpage) . $this->extra;
						$navigation[$i]['value'] = $counter;
						$navigation[$i]['option'] = 'show';
						++$i;
                        if ($counter == 1 && $current_page > 1 + $offset) {
							$navigation[$i]['url'] = '';
							$navigation[$i]['value'] = '';
							$navigation[$i]['option'] = 'break';
                        }
                    }
                    ++$counter;
					++$i;
                }
                $next = $this->current + $this->perpage;
                if ($this->total > $next) {
					$navigation[$i]['url'] = $this->url . $next . $this->extra;
					$navigation[$i]['value'] = '';
					$navigation[$i]['option'] = 'last';
                }
            }
			return $this->displayPageNav('Nav', $navigation);
        }
        return $ret;
    }

    /**
     * Create a navigational dropdown list
     *
     * @param  boolean $showbutton Show the "Go" button?
     * @return string
     */
    public function renderSelect($showbutton = false)
    {
        $ret = '';
		if ($this->total < $this->perpage) {
            return $ret;
        }
        $total_pages = ceil($this->total / $this->perpage);
        if ($total_pages > 1) {
            $counter      = 1;
            $current_page = (int)floor(($this->current + $this->perpage) / $this->perpage);
			while ($counter <= $total_pages) {
                if ($counter == $current_page) {
                    $ret .= '<option value="' . $this->url . (($counter - 1) * $this->perpage) . $this->extra . '" selected>' . $counter . '</option>';
                } else {
                    $ret .= '<option value="' . $this->url . (($counter - 1) * $this->perpage) . $this->extra . '">' . $counter . '</option>';
                }
                ++$counter;
            }
            if ($showbutton) {
				$navigation['button'] = true;
            } else {
				$navigation['button'] = false;
			}
			$navigation['select'] = $ret;
			return $this->displayPageNav('Select', $navigation);
        }
        return $ret;
    }

    /**
     * Create navigation with images
     *
     * @param  integer $offset
     * @return string
     */
    public function renderImageNav($offset = 4)
    {
        $ret = '';
		if ($this->total < $this->perpage) {
            return $ret;
        }
        $total_pages = ceil($this->total / $this->perpage);
        if ($total_pages > 1) {
			$i = 0;
            $prev = $this->current - $this->perpage;
            if ($prev >= 0) {
				$navigation[$i]['url'] = $this->url . $prev . $this->extra;
				$navigation[$i]['value'] = '';
				$navigation[$i]['option'] = 'first';
				++$i;
            } else {
				$navigation[$i]['url'] = '';
				$navigation[$i]['value'] = '';
				$navigation[$i]['option'] = 'firstempty';
				++$i;
            }
            $counter      = 1;
            $current_page = (int)floor(($this->current + $this->perpage) / $this->perpage);
            while ($counter <= $total_pages) {
                if ($counter == $current_page) {
					$navigation[$i]['url'] = '';
					$navigation[$i]['value'] = $counter;
					$navigation[$i]['option'] = 'selected';
                } elseif (($counter > $current_page - $offset && $counter < $current_page + $offset) || $counter == 1 || $counter == $total_pages) {
                    if ($counter == $total_pages && $current_page < $total_pages - $offset) {
						$navigation[$i]['url'] = '';
						$navigation[$i]['value'] = '';
						$navigation[$i]['option'] = 'break';
						++$i;
                    }
					$navigation[$i]['url'] = $this->url . (($counter - 1) * $this->perpage) . $this->extra;
					$navigation[$i]['value'] = $counter;
					$navigation[$i]['option'] = 'show';
					++$i;
                    if ($counter == 1 && $current_page > 1 + $offset) {
						$navigation[$i]['url'] = '';
						$navigation[$i]['value'] = '';
						$navigation[$i]['option'] = 'break';
                    }
                }
                ++$counter;
				++$i;
            }
            $next = $this->current + $this->perpage;
            if ($this->total > $next) {
				$navigation[$i]['url'] = $this->url . $next . $this->extra;
				$navigation[$i]['value'] = '';
				$navigation[$i]['option'] = 'last';
				++$i;
            } else {
				$navigation[$i]['url'] = '';
				$navigation[$i]['value'] = '';
				$navigation[$i]['option'] = 'lastempty';
            }
			return $this->displayPageNav('Image', $navigation);
        }
        return $ret;
    }

    /**
     * Display navigation in template
     *
     * @param  string $type
     * @param  array $navigation
     * @return string
     */
	private function displayPageNav($type = 'nav', $navigation = array()){
		if (!isset($GLOBALS['xoTheme']) || !is_object($GLOBALS['xoTheme'])) {
			include_once $GLOBALS['xoops']->path('/class/theme.php');
			$GLOBALS['xoTheme'] = new \xos_opal_Theme();
		}
		require_once $GLOBALS['xoops']->path('/class/template.php');
		$pageNavTpl = new \XoopsTpl();
		$pageNavTpl->assign('pageNavType', $type);
		$pageNavTpl->assign('pageNavigation', $navigation);
		
		return $pageNavTpl->fetch("db:system_pagenav.tpl");
		
	}
}
