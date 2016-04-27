<?php
/**
 * CAPTCHA configurations for Image mode
 *
 * Based on DuGris' SecurityImage
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
 * @package             class
 * @subpackage          CAPTCHA
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

return $config = array(
    'num_chars'       => 6,
    // Maximum characters
    'casesensitive'   => false,
    // Characters in image mode is case-sensitive
    'fontsize_min'    => 10,
    // Minimum font-size
    'fontsize_max'    => 24,
    // Maximum font-size
    'background_type' => 0,
    // Background type in image mode: 0 - bar; 1 - circle; 2 - line; 3 - rectangle; 4 - ellipse; 5 - polygon; 100 - generated from files
    'background_num'  => 50,
    // Number of background images to generate
    'polygon_point'   => 3,
    'skip_characters' => array(
        'o',
        '0',
        'i',
        'l',
        '1')); // characters that should not be used

