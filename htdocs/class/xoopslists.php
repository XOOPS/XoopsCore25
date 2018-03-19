<?php
/**
 * XOOPS listing utilities
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
 */

defined('XOOPS_ROOT_PATH') || exit('Restricted access');

if (!defined('XOOPS_LISTS_INCLUDED')) {
    define('XOOPS_LISTS_INCLUDED', 1);

    /**
     * XoopsLists
     *
     * @author              John Neill <catzwolf@xoops.org>
     * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
     * @package             kernel
     * @subpackage          form
     * @access              public
     */
    class XoopsLists
    {
        /**
         * @return array
         */
        public static function getTimeZoneList()
        {
            xoops_loadLanguage('timezone');

            $time_zone_list = array(
                '-12'  => _TZ_GMTM12,
                '-11'  => _TZ_GMTM11,
                '-10'  => _TZ_GMTM10,
                '-9'   => _TZ_GMTM9,
                '-8'   => _TZ_GMTM8,
                '-7'   => _TZ_GMTM7,
                '-6'   => _TZ_GMTM6,
                '-5'   => _TZ_GMTM5,
                '-4'   => _TZ_GMTM4,
                '-3.5' => _TZ_GMTM35,
                '-3'   => _TZ_GMTM3,
                '-2'   => _TZ_GMTM2,
                '-1'   => _TZ_GMTM1,
                '0'    => _TZ_GMT0,
                '1'    => _TZ_GMTP1,
                '2'    => _TZ_GMTP2,
                '3'    => _TZ_GMTP3,
                '3.5'  => _TZ_GMTP35,
                '4'    => _TZ_GMTP4,
                '4.5'  => _TZ_GMTP45,
                '5'    => _TZ_GMTP5,
                '5.5'  => _TZ_GMTP55,
                '6'    => _TZ_GMTP6,
                '7'    => _TZ_GMTP7,
                '8'    => _TZ_GMTP8,
                '9'    => _TZ_GMTP9,
                '9.5'  => _TZ_GMTP95,
                '10'   => _TZ_GMTP10,
                '11'   => _TZ_GMTP11,
                '12'   => _TZ_GMTP12);

            return $time_zone_list;
        }

        /**
         * gets list of themes folder from themes directory
         */
        public static function getThemesList()
        {
            return XoopsLists::getDirListAsArray(XOOPS_THEME_PATH . '/');
        }

        /**
         * gets a list of module folders from the modules directory
         */
        public static function getModulesList()
        {
            return XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . '/modules/');
        }

        /**
         * gets list of editors folder from xoopseditor directory
         */
        public static function getEditorList()
        {
            return XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . '/class/xoopseditor/');
        }

        /**
         * gets list of name of directories inside a directory
         * @param $dirname
         * @return array
         */
        public static function getDirListAsArray($dirname)
        {
            $ignored = array(
                'cvs',
                '_darcs');
            $list    = array();
            if (substr($dirname, -1) !== '/') {
                $dirname .= '/';
            }
            if ($handle = opendir($dirname)) {
                while ($file = readdir($handle)) {
                    if (substr($file, 0, 1) === '.' || in_array(strtolower($file), $ignored)) {
                        continue;
                    }
                    if (is_dir($dirname . $file)) {
                        $list[$file] = $file;
                    }
                }
                closedir($handle);
                asort($list);
                reset($list);
            }

            return $list;
        }

        /**
         * gets list of all files in a directory
         * @param        $dirname
         * @param string $prefix
         * @return array
         */
        public static function getFileListAsArray($dirname, $prefix = '')
        {
            $filelist = array();
            if (substr($dirname, -1) === '/') {
                $dirname = substr($dirname, 0, -1);
            }
            if (is_dir($dirname) && $handle = opendir($dirname)) {
                while (false !== ($file = readdir($handle))) {
                    if (!preg_match('/^[\.]{1,2}$/', $file) && is_file($dirname . '/' . $file)) {
                        $file            = $prefix . $file;
                        $filelist[$file] = $file;
                    }
                }
                closedir($handle);
                asort($filelist);
                reset($filelist);
            }

            return $filelist;
        }

        /**
         * gets list of image file names in a directory
         * @param        $dirname
         * @param string $prefix
         * @return array
         */
        public static function getImgListAsArray($dirname, $prefix = '')
        {
            $filelist = array();
            if ($handle = opendir($dirname)) {
                while (false !== ($file = readdir($handle))) {
                    if (preg_match('/(\.gif|\.jpg|\.png)$/i', $file)) {
                        $file            = $prefix . $file;
                        $filelist[$file] = $file;
                    }
                }
                closedir($handle);
                asort($filelist);
                reset($filelist);
            }

            return $filelist;
        }

        /**
         * gets list of html file names in a certain directory
         * @param        $dirname
         * @param string $prefix
         * @return array
         */
        public static function getHtmlListAsArray($dirname, $prefix = '')
        {
            $filelist = array();
            if ($handle = opendir($dirname)) {
                while (false !== ($file = readdir($handle))) {
                    if (preg_match('/(\.htm|\.html|\.xhtml|\.tpl)$/i', $file) && !is_dir($file)) {
                        $file            = $prefix . $file;
                        $filelist[$file] = $prefix . $file;
                    }
                }
                closedir($handle);
                asort($filelist);
                reset($filelist);
            }

            return $filelist;
        }

        /**
         * gets list of avatar file names in a certain directory
         *                             if directory is not specified, default directory will be searched
         * @param string $avatar_dir
         * @return array
         */
        public static function getAvatarsList($avatar_dir = '')
        {
            $avatars = array();
            if ($avatar_dir != '') {
                $avatars = XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH . '/images/avatar/' . $avatar_dir . '/', $avatar_dir . '/');
            } else {
                $avatars = XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH . '/images/avatar/');
            }

            return $avatars;
        }

        /**
         * gets list of all avatar image files inside default avatars directory
         */
        public static function getAllAvatarsList()
        {
            $avatars = array();
            $dirlist = array();
            $dirlist = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . '/images/avatar/');
            if (count($dirlist) > 0) {
                foreach ($dirlist as $dir) {
                    $avatars[$dir] = &XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH . '/images/avatar/' . $dir . '/', $dir . '/');
                }
            } else {
                return false;
            }

            return $avatars;
        }

        /**
         * gets list of subject icon image file names in a certain directory
         *                             if directory is not specified, default directory will be searched
         * @param string $sub_dir
         * @return array
         */
        public static function getSubjectsList($sub_dir = '')
        {
            $subjects = array();
            if ($sub_dir != '') {
                $subjects = XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH . '/images/subject/' . $sub_dir, $sub_dir . '/');
            } else {
                $subjects = XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH . '/images/subject/');
            }

            return $subjects;
        }

        /**
         * gets list of language folders inside default language directory
         */
        public static function getLangList()
        {
            $lang_list = array();
            $lang_list = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH . '/language/');

            return $lang_list;
        }

        /**
         * XoopsLists::getCountryList()
         *
         * @return array
         */
        public static function getCountryList()
        {
            xoops_loadLanguage('countries');
            $country_list = array(
                '' => '-',
                'AD' => _COUNTRY_AD,
                'AE' => _COUNTRY_AE,
                'AF' => _COUNTRY_AF,
                'AG' => _COUNTRY_AG,
                'AI' => _COUNTRY_AI,
                'AL' => _COUNTRY_AL,
                'AM' => _COUNTRY_AM,
                'AN' => _COUNTRY_AN,
                'AO' => _COUNTRY_AO,
                'AQ' => _COUNTRY_AQ,
                'AR' => _COUNTRY_AR,
                'AS' => _COUNTRY_AS,
                'AT' => _COUNTRY_AT,
                'AU' => _COUNTRY_AU,
                'AW' => _COUNTRY_AW,
                'AX' => _COUNTRY_AX,
                'AZ' => _COUNTRY_AZ,
                'BA' => _COUNTRY_BA,
                'BB' => _COUNTRY_BB,
                'BD' => _COUNTRY_BD,
                'BE' => _COUNTRY_BE,
                'BF' => _COUNTRY_BF,
                'BG' => _COUNTRY_BG,
                'BH' => _COUNTRY_BH,
                'BI' => _COUNTRY_BI,
                'BJ' => _COUNTRY_BJ,
                'BL' => _COUNTRY_BL,
                'BM' => _COUNTRY_BM,
                'BN' => _COUNTRY_BN,
                'BO' => _COUNTRY_BO,
                'BR' => _COUNTRY_BR,
                'BS' => _COUNTRY_BS,
                'BT' => _COUNTRY_BT,
                'BV' => _COUNTRY_BV,
                'BW' => _COUNTRY_BW,
                'BY' => _COUNTRY_BY,
                'BZ' => _COUNTRY_BZ,
                'CA' => _COUNTRY_CA,
                'CC' => _COUNTRY_CC,
                'CD' => _COUNTRY_CD,
                'CF' => _COUNTRY_CF,
                'CG' => _COUNTRY_CG,
                'CH' => _COUNTRY_CH,
                'CI' => _COUNTRY_CI,
                'CK' => _COUNTRY_CK,
                'CL' => _COUNTRY_CL,
                'CM' => _COUNTRY_CM,
                'CN' => _COUNTRY_CN,
                'CO' => _COUNTRY_CO,
                'CR' => _COUNTRY_CR,
                'CS' => _COUNTRY_CS,    //  Not listed in ISO 3166, former Serbia & Montenegro
                'CU' => _COUNTRY_CU,
                'CV' => _COUNTRY_CV,
                'CX' => _COUNTRY_CX,
                'CY' => _COUNTRY_CY,
                'CZ' => _COUNTRY_CZ,
                'DE' => _COUNTRY_DE,
                'DJ' => _COUNTRY_DJ,
                'DK' => _COUNTRY_DK,
                'DM' => _COUNTRY_DM,
                'DO' => _COUNTRY_DO,
                'DZ' => _COUNTRY_DZ,
                'EC' => _COUNTRY_EC,
                'EE' => _COUNTRY_EE,
                'EG' => _COUNTRY_EG,
                'EH' => _COUNTRY_EH,
                'ER' => _COUNTRY_ER,
                'ES' => _COUNTRY_ES,
                'ET' => _COUNTRY_ET,
                'FI' => _COUNTRY_FI,
                'FJ' => _COUNTRY_FJ,
                'FK' => _COUNTRY_FK,
                'FM' => _COUNTRY_FM,
                'FO' => _COUNTRY_FO,
                'FR' => _COUNTRY_FR,
                'FX' => _COUNTRY_FX,    //  Not listed in ISO 3166
                'GA' => _COUNTRY_GA,
                'GB' => _COUNTRY_GB,
                'GD' => _COUNTRY_GD,
                'GE' => _COUNTRY_GE,
                'GF' => _COUNTRY_GF,
                'GG' => _COUNTRY_GG,
                'GH' => _COUNTRY_GH,
                'GI' => _COUNTRY_GI,
                'GL' => _COUNTRY_GL,
                'GM' => _COUNTRY_GM,
                'GN' => _COUNTRY_GN,
                'GP' => _COUNTRY_GP,
                'GQ' => _COUNTRY_GQ,
                'GR' => _COUNTRY_GR,
                'GS' => _COUNTRY_GS,
                'GT' => _COUNTRY_GT,
                'GU' => _COUNTRY_GU,
                'GW' => _COUNTRY_GW,
                'GY' => _COUNTRY_GY,
                'HK' => _COUNTRY_HK,
                'HM' => _COUNTRY_HM,
                'HN' => _COUNTRY_HN,
                'HR' => _COUNTRY_HR,
                'HT' => _COUNTRY_HT,
                'HU' => _COUNTRY_HU,
                'ID' => _COUNTRY_ID,
                'IE' => _COUNTRY_IE,
                'IL' => _COUNTRY_IL,
                'IM' => _COUNTRY_IM,
                'IN' => _COUNTRY_IN,
                'IO' => _COUNTRY_IO,
                'IQ' => _COUNTRY_IQ,
                'IR' => _COUNTRY_IR,
                'IS' => _COUNTRY_IS,
                'IT' => _COUNTRY_IT,
                'JM' => _COUNTRY_JM,
                'JO' => _COUNTRY_JO,
                'JP' => _COUNTRY_JP,
                'KE' => _COUNTRY_KE,
                'KG' => _COUNTRY_KG,
                'KH' => _COUNTRY_KH,
                'KI' => _COUNTRY_KI,
                'KM' => _COUNTRY_KM,
                'KN' => _COUNTRY_KN,
                'KP' => _COUNTRY_KP,
                'KR' => _COUNTRY_KR,
                'KW' => _COUNTRY_KW,
                'KY' => _COUNTRY_KY,
                'KZ' => _COUNTRY_KZ,
                'LA' => _COUNTRY_LA,
                'LB' => _COUNTRY_LB,
                'LC' => _COUNTRY_LC,
                'LI' => _COUNTRY_LI,
                'LK' => _COUNTRY_LK,
                'LR' => _COUNTRY_LR,
                'LS' => _COUNTRY_LS,
                'LT' => _COUNTRY_LT,
                'LU' => _COUNTRY_LU,
                'LV' => _COUNTRY_LV,
                'LY' => _COUNTRY_LY,
                'MA' => _COUNTRY_MA,
                'MC' => _COUNTRY_MC,
                'MD' => _COUNTRY_MD,
                'ME' => _COUNTRY_ME,
                'MF' => _COUNTRY_MF,
                'MG' => _COUNTRY_MG,
                'MH' => _COUNTRY_MH,
                'MK' => _COUNTRY_MK,
                'ML' => _COUNTRY_ML,
                'MM' => _COUNTRY_MM,
                'MN' => _COUNTRY_MN,
                'MO' => _COUNTRY_MO,
                'MP' => _COUNTRY_MP,
                'MQ' => _COUNTRY_MQ,
                'MR' => _COUNTRY_MR,
                'MS' => _COUNTRY_MS,
                'MT' => _COUNTRY_MT,
                'MU' => _COUNTRY_MU,
                'MV' => _COUNTRY_MV,
                'MW' => _COUNTRY_MW,
                'MX' => _COUNTRY_MX,
                'MY' => _COUNTRY_MY,
                'MZ' => _COUNTRY_MZ,
                'NA' => _COUNTRY_NA,
                'NC' => _COUNTRY_NC,
                'NE' => _COUNTRY_NE,
                'NF' => _COUNTRY_NF,
                'NG' => _COUNTRY_NG,
                'NI' => _COUNTRY_NI,
                'NL' => _COUNTRY_NL,
                'NO' => _COUNTRY_NO,
                'NP' => _COUNTRY_NP,
                'NR' => _COUNTRY_NR,
                'NT' => _COUNTRY_NT,    //  Not listed in ISO 3166
                'NU' => _COUNTRY_NU,
                'NZ' => _COUNTRY_NZ,
                'OM' => _COUNTRY_OM,
                'PA' => _COUNTRY_PA,
                'PE' => _COUNTRY_PE,
                'PF' => _COUNTRY_PF,
                'PG' => _COUNTRY_PG,
                'PH' => _COUNTRY_PH,
                'PK' => _COUNTRY_PK,
                'PL' => _COUNTRY_PL,
                'PM' => _COUNTRY_PM,
                'PN' => _COUNTRY_PN,
                'PR' => _COUNTRY_PR,
                'PS' => _COUNTRY_PS,
                'PT' => _COUNTRY_PT,
                'PW' => _COUNTRY_PW,
                'PY' => _COUNTRY_PY,
                'QA' => _COUNTRY_QA,
                'RE' => _COUNTRY_RE,
                'RO' => _COUNTRY_RO,
                'RS' => _COUNTRY_RS,
                'RU' => _COUNTRY_RU,
                'RW' => _COUNTRY_RW,
                'SA' => _COUNTRY_SA,
                'SB' => _COUNTRY_SB,
                'SC' => _COUNTRY_SC,
                'SD' => _COUNTRY_SD,
                'SE' => _COUNTRY_SE,
                'SG' => _COUNTRY_SG,
                'SH' => _COUNTRY_SH,
                'SI' => _COUNTRY_SI,
                'SJ' => _COUNTRY_SJ,
                'SK' => _COUNTRY_SK,
                'SL' => _COUNTRY_SL,
                'SM' => _COUNTRY_SM,
                'SN' => _COUNTRY_SN,
                'SO' => _COUNTRY_SO,
                'SR' => _COUNTRY_SR,
                'ST' => _COUNTRY_ST,
                'SU' => _COUNTRY_SU,    //  Not listed in ISO 3166
                'SV' => _COUNTRY_SV,
                'SY' => _COUNTRY_SY,
                'SZ' => _COUNTRY_SZ,
                'TC' => _COUNTRY_TC,
                'TD' => _COUNTRY_TD,
                'TF' => _COUNTRY_TF,
                'TG' => _COUNTRY_TG,
                'TH' => _COUNTRY_TH,
                'TJ' => _COUNTRY_TJ,
                'TK' => _COUNTRY_TK,
                'TL' => _COUNTRY_TL,
                'TM' => _COUNTRY_TM,
                'TN' => _COUNTRY_TN,
                'TO' => _COUNTRY_TO,
                'TP' => _COUNTRY_TP,    //  Not listed in ISO 3166, has changed to TL
                'TR' => _COUNTRY_TR,
                'TT' => _COUNTRY_TT,
                'TV' => _COUNTRY_TV,
                'TW' => _COUNTRY_TW,
                'TZ' => _COUNTRY_TZ,
                'UA' => _COUNTRY_UA,
                'UG' => _COUNTRY_UG,
                //"UK" => _COUNTRY_UK,    //  Not listed in ISO 3166
                'UM' => _COUNTRY_UM,
                'US' => _COUNTRY_US,
                'UY' => _COUNTRY_UY,
                'UZ' => _COUNTRY_UZ,
                'VA' => _COUNTRY_VA,
                'VC' => _COUNTRY_VC,
                'VE' => _COUNTRY_VE,
                'VG' => _COUNTRY_VG,
                'VI' => _COUNTRY_VI,
                'VN' => _COUNTRY_VN,
                'VU' => _COUNTRY_VU,
                'WF' => _COUNTRY_WF,
                'WS' => _COUNTRY_WS,
                'YE' => _COUNTRY_YE,
                'YT' => _COUNTRY_YT,
                'YU' => _COUNTRY_YU,    //  Not listed in ISO 3166, former Yugoslavia
                'ZA' => _COUNTRY_ZA,
                'ZM' => _COUNTRY_ZM,
                'ZR' => _COUNTRY_ZR,    //  Not listed in ISO 3166
                'ZW' => _COUNTRY_ZW);
            asort($country_list);
            reset($country_list);

            return $country_list;
        }

        /**
         * XoopsLists::getHtmlList()
         *
         * This Function is no longer being used by the core
         *
         * @return array
         */
        public static function getHtmlList()
        {
            $html_list = array(
                'a'          => '&lt;a&gt;',
                'abbr'       => '&lt;abbr&gt;',
                'acronym'    => '&lt;acronym&gt;',
                'address'    => '&lt;address&gt;',
                'b'          => '&lt;b&gt;',
                'bdo'        => '&lt;bdo&gt;',
                'big'        => '&lt;big&gt;',
                'blockquote' => '&lt;blockquote&gt;',
                'br'         => '&lt;br&gt;',
                'caption'    => '&lt;caption&gt;',
                'cite'       => '&lt;cite&gt;',
                'code'       => '&lt;code&gt;',
                'col'        => '&lt;col&gt;',
                'colgroup'   => '&lt;colgroup&gt;',
                'dd'         => '&lt;dd&gt;',
                'del'        => '&lt;del&gt;',
                'dfn'        => '&lt;dfn&gt;',
                'div'        => '&lt;div&gt;',
                'dl'         => '&lt;dl&gt;',
                'dt'         => '&lt;dt&gt;',
                'em'         => '&lt;em&gt;',
                'font'       => '&lt;font&gt;',
                'h1'         => '&lt;h1&gt;',
                'h2'         => '&lt;h2&gt;',
                'h3'         => '&lt;h3&gt;',
                'h4'         => '&lt;h4&gt;',
                'h5'         => '&lt;h5&gt;',
                'h6'         => '&lt;h6&gt;',
                'hr'         => '&lt;hr&gt;',
                'i'          => '&lt;i&gt;',
                'img'        => '&lt;img&gt;',
                'ins'        => '&lt;ins&gt;',
                'kbd'        => '&lt;kbd&gt;',
                'li'         => '&lt;li&gt;',
                'map'        => '&lt;map&gt;',
                'object'     => '&lt;object&gt;',
                'ol'         => '&lt;ol&gt;',
                'p'          => '&lt;p&gt;',
                'pre'        => '&lt;pre&gt;',
                's'          => '&lt;s&gt;',
                'samp'       => '&lt;samp&gt;',
                'small'      => '&lt;small&gt;',
                'span'       => '&lt;span&gt;',
                'strike'     => '&lt;strike&gt;',
                'strong'     => '&lt;strong&gt;',
                'sub'        => '&lt;sub&gt;',
                'sup'        => '&lt;sup&gt;',
                'table'      => '&lt;table&gt;',
                'tbody'      => '&lt;tbody&gt;',
                'td'         => '&lt;td&gt;',
                'tfoot'      => '&lt;tfoot&gt;',
                'th'         => '&lt;th&gt;',
                'thead'      => '&lt;thead&gt;',
                'tr'         => '&lt;tr&gt;',
                'tt'         => '&lt;tt&gt;',
                'u'          => '&lt;u&gt;',
                'ul'         => '&lt;ul&gt;',
                'var'        => '&lt;var&gt;');
            asort($html_list);
            reset($html_list);

            return $html_list;
        }

        /**
         * XoopsLists::getUserRankList()
         *
         * @return array
         */
        public static function getUserRankList()
        {
            $db     = XoopsDatabaseFactory::getDatabaseConnection();
            $myts   = MyTextSanitizer::getInstance();
            $sql    = sprintf('SELECT rank_id, rank_title FROM ' . $db->prefix('ranks') . ' WHERE rank_special = %u', 1);
            $ret    = array();
            $result = $db->query($sql);
            while (false !== ($myrow = $db->fetchArray($result))) {
                $ret[$myrow['rank_id']] = $myts->htmlspecialchars($myrow['rank_title']);
            }

            return $ret;
        }
    }
}
