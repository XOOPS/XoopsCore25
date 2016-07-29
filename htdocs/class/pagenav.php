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
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @since               2.0.0
 * @author              Kazumi Ono (http://www.myweb.ne.jp/, http://jp.xoops.org/)
 */

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
        $this->url = $_SERVER['PHP_SELF'] . '?' . trim($start_name) . '=';
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
            $total_pages = ceil($this->total / $this->perpage);
            if ($total_pages > 1) {
                $ret .= '<div id="xo-pagenav">';
                $prev = $this->current - $this->perpage;
                if ($prev >= 0) {
                    $ret .= '<a class="xo-pagarrow" href="' . $this->url . $prev . $this->extra . '"><u>&laquo;</u></a> ';
                }
                $counter      = 1;
                $current_page = (int)floor(($this->current + $this->perpage) / $this->perpage);
                while ($counter <= $total_pages) {
                    if ($counter == $current_page) {
                        $ret .= '<strong class="xo-pagact" >(' . $counter . ')</strong> ';
                    } elseif (($counter > $current_page - $offset && $counter < $current_page + $offset) || $counter == 1 || $counter == $total_pages) {
                        if ($counter == $total_pages && $current_page < $total_pages - $offset) {
                            $ret .= '... ';
                        }
                        $ret .= '<a class="xo-counterpage" href="' . $this->url . (($counter - 1) * $this->perpage) . $this->extra . '">' . $counter . '</a> ';
                        if ($counter == 1 && $current_page > 1 + $offset) {
                            $ret .= '... ';
                        }
                    }
                    ++$counter;
                }
                $next = $this->current + $this->perpage;
                if ($this->total > $next) {
                    $ret .= '<a class="xo-pagarrow" href="' . $this->url . $next . $this->extra . '"><u>&raquo;</u></a> ';
                }
                $ret .= '</div> ';
            }
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
        if ($this->total < $this->perpage) {
            return null;
        }
        $total_pages = ceil($this->total / $this->perpage);
        $ret         = '';
        if ($total_pages > 1) {
            $ret = '<form name="pagenavform">';
            $ret .= '<select name="pagenavselect" onchange="location=this.options[this.options.selectedIndex].value;">';
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
            $ret .= '</select>';
            if ($showbutton) {
                $ret .= '&nbsp;<input type="submit" value="' . _GO . '" />';
            }
            $ret .= '</form>';
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
        if ($this->total < $this->perpage) {
            return null;
        }
        $total_pages = ceil($this->total / $this->perpage);
        $ret         = '';
        if ($total_pages > 1) {
            $ret  = '<table><tr>';
            $prev = $this->current - $this->perpage;
            if ($prev >= 0) {
                $ret .= '<td class="pagneutral"><a href="' . $this->url . $prev . $this->extra . '">&lt;</a></td><td><img src="' . XOOPS_URL . '/images/blank.gif" width="6" alt="" /></td>';
            } else {
                $ret .= '<td class="pagno"></a></td><td><img src="' . XOOPS_URL . '/images/blank.gif" width="6" alt="" /></td>';
            }
            $counter      = 1;
            $current_page = (int)floor(($this->current + $this->perpage) / $this->perpage);
            while ($counter <= $total_pages) {
                if ($counter == $current_page) {
                    $ret .= '<td class="pagact"><strong>' . $counter . '</strong></td>';
                } elseif (($counter > $current_page - $offset && $counter < $current_page + $offset) || $counter == 1 || $counter == $total_pages) {
                    if ($counter == $total_pages && $current_page < $total_pages - $offset) {
                        $ret .= '<td class="paginact">...</td>';
                    }
                    $ret .= '<td class="paginact"><a href="' . $this->url . (($counter - 1) * $this->perpage) . $this->extra . '">' . $counter . '</a></td>';
                    if ($counter == 1 && $current_page > 1 + $offset) {
                        $ret .= '<td class="paginact">...</td>';
                    }
                }
                ++$counter;
            }
            $next = $this->current + $this->perpage;
            if ($this->total > $next) {
                $ret .= '<td><img src="' . XOOPS_URL . '/images/blank.gif" width="6" alt="" /></td><td class="pagneutral"><a href="' . $this->url . $next . $this->extra . '">&gt;</a></td>';
            } else {
                $ret .= '<td><img src="' . XOOPS_URL . '/images/blank.gif" width="6" alt="" /></td><td class="pagno"></td>';
            }
            $ret .= '</tr></table>';
        }

        return $ret;
    }
}
