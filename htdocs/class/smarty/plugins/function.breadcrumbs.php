<?php 
/** 
* Smarty plugin 
* @package Smarty 
* @subpackage plugins 
*/ 

/** 
* Smarty {breadcrumbs} function plugin 
* 
* Type:      function<br> 
* Name:      breadcrumbs<br> 
* Date:      January 9, 2006 
* Purpose:   Prints pagination based on the parameters<br> 
* Input:<br> 
*         - start      = index for the start item 
*         - perpage    = how many items there are on a page 
*         - total      = total number of items 
*         - output_c   = how to output the current page link 
*         - output     = how to output everything else 
*            - %st%    = start 
*            - %pag%   = page number 
* 
* Examples: 
* <pre> 
* {breadcrumbs start="0" perpage="10" total="125" output_c="%pag%" output="<a href=\"script.php?page_id=4&st=%st%\">%pag%</a>"} 
* </pre> 
* 
* @version    1.0 
* @author     Mihai "avataru" Zaharie <avataru at gmail dot com | www.avataru.net> 
* @param      array 
* @param      Smarty 
* @return     string 
*/ 

function smarty_function_breadcrumbs($params, &$smarty) { 

	$bc_st = $params['start'];                          // show results starting from here 
	$bc_pp = $params['perpage'];                        // show this many results per page 
	$bc_tot = $params['total'];                         // total results we have 
	$bc_pgs = ceil($bc_tot / $bc_pp);                   // total pages we need 
	$bc_cp = floor($bc_st / $bc_pp) + 1;                // the page we are on 

	$bc_tpl_cp = $params['output_c'];                   // replacement template for the current page 
	$bc_tpl = $params['output'];                        // replacement template for any other page 

	$bc_offset = 4;                                     // max offset from current page (default 4) 
	$bc_small = 10;                                     // ignore offset when there are only a few pages (default 10) 

	if ($bc_pgs == "1") return "";                         
	elseif ($bc_pgs <= $bc_small) $bc_offset = $bc_small;       


	for ($n = $bc_cp - $bc_offset; $n <= $bc_cp + $bc_offset; $n++) { 
		$bc_temp = ($n - 1) * $bc_pp; 
		if ($n >= 1 && $n <= $bc_pgs) { 
			if ($n == $bc_cp) $breadcrumbs .= " ". str_replace("%st%", $bc_st, str_replace("%pag%", $n, $bc_tpl_cp)); 
			else $breadcrumbs .= " ". str_replace("%st%", $bc_temp, str_replace("%pag%", $n, $bc_tpl)); 
		} 
	} 
	if ($bc_cp - $bc_offset > 1) { 
		$breadcrumbs = " ... ". $breadcrumbs; 
		if ($bc_cp > 1)   { 
			$bc_temp = ($bc_cp - 2) * $bc_pp; 
			$bc_text = "&"; 
			$breadcrumbs = str_replace("%st%", $bc_temp, str_replace("%pag%", $bc_text, $bc_tpl)) . $breadcrumbs; 
		} 
		$bc_temp = "0"; 
		$bc_text = "&"; 
		$breadcrumbs = str_replace("%st%", $bc_temp, str_replace("%pag%", $bc_text, $bc_tpl)) ." ". $breadcrumbs; 
	} 
	elseif (($bc_cp > 1) && ($bc_pgs > $bc_small)) { 
		$bc_temp = ($bc_cp - 2) * $bc_pp; 
		$bc_text = "&"; 
		$breadcrumbs = str_replace("%st%", $bc_temp, str_replace("%pag%", $bc_text, $bc_tpl)) ." ". $breadcrumbs; 
	} 

	if ($bc_cp + $bc_offset < $bc_pgs) { 
		$breadcrumbs .= " ... "; 
		if ($bc_cp < $bc_pgs) { 
			$bc_temp = ($bc_cp * $bc_pp); 
			$bc_text = "&"; 
			$breadcrumbs .= str_replace("%st%", $bc_temp, str_replace("%pag%", $bc_text, $bc_tpl)); 
		} 
		$bc_temp = ($bc_pgs - 1) * $bc_pp; 
		$bc_text = "&"; 
		$breadcrumbs .= " ". str_replace("%st%", $bc_temp, str_replace("%pag%", $bc_text, $bc_tpl)); 
	} 
	elseif (($bc_cp < $bc_pgs) && ($bc_pgs > $bc_small)) { 
		$bc_temp = ($bc_cp * $bc_pp); 
		$bc_text = "&"; 
		$breadcrumbs .= " ". str_replace("%st%", $bc_temp, str_replace("%pag%", $bc_text, $bc_tpl)); 
	} 

	return $breadcrumbs; 
}

/* vim: set expandtab: */
