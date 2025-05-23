<?php
/**
 * Language handler
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
 * @package             upgrader
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

/**
 * phpMyAdmin Language Loading File
 */

/**
 * All the supported languages have to be listed in the array below.
 * 1. The key must be the "official" ISO 639 language code and, if required,
 *     the dialect code. It can also contain some information about the
 *     charset (see the Russian case).
 * 2. The first of the values associated to the key is used in a regular
 *     expression to find some keywords corresponding to the language inside two
 *     environment variables.
 *     These values contains:
 *     - the "official" ISO language code and, if required, the dialect code
 *       also ('bu' for Bulgarian, 'fr([-_][[:alpha:]]{2})?' for all French
 *       dialects, 'zh[-_]tw' for Chinese traditional...);
 *     - the '|' character (it means 'OR');
 *     - the full language name.
 * 3. The second values associated to the key is the name of the file to load
 *     without the '.php' extension.
 * 4. The last values associated to the key is the language code as defined by
 *     the RFC1766.
 *
 * Beware that the sorting order (first values associated to keys by
 * alphabetical reverse order in the array) is important: 'zh-tw' (chinese
 * traditional) must be detected before 'zh' (chinese simplified) for
 * example.
 *
 * When there are more than one charset for a language, we put the -utf-8
 * first.
 */
$available_languages = [
    'af'    => ['af|afrikaans', 'afrikaans'],
    'ar'    => ['ar([-_][[:alpha:]]{2})?|arabic', 'arabic'],
    'bg'    => ['bg|bulgarian', 'bulgarian'],
    'ca'    => ['ca|catalan', 'catalan'],
    'cs'    => ['cs|czech', 'czech'],
    'da'    => ['da|danish', 'danish'],
    'de'    => ['de([-_][[:alpha:]]{2})?|german', 'german'],
    'el'    => ['el|greek', 'greek'],
    'en'    => ['en([-_][[:alpha:]]{2})?|english', 'english'],
    'es'    => ['es([-_][[:alpha:]]{2})?|spanish', 'spanish'],
    'et'    => ['et|estonian', 'estonian'],
    'fi'    => ['fi|finnish', 'finnish'],
    'fa'    => ['fa|persian', 'persian'],
    'fr'    => ['fr([-_][[:alpha:]]{2})?|french', 'french'],
    'gl'    => ['gl|galician', 'galician'],
    'he'    => ['he|hebrew', 'hebrew'],
    'hr'    => ['hr|croatian', 'hrvatski'],
    'hu'    => ['hu|hungarian', 'hungarian'],
    'id'    => ['id|indonesian', 'indonesian'],
    'it'    => ['it|italian', 'italian'],
    'ja'    => ['ja|japanese', 'japanese'],
    'ko'    => ['ko|korean', 'koreano'],
    'ka'    => ['ka|georgian', 'georgian'],
    'lt'    => ['lt|lithuanian', 'lithuanian'],
    'lv'    => ['lv|latvian', 'latvian'],
    'ms'    => ['ms|malay', 'malay'],
    'nl'    => ['nl([-_][[:alpha:]]{2})?|nederlands', 'nederlands'],
    'no'    => ['no|norwegian', 'norwegian'],
    'pl'    => ['pl|polish', 'polish'],
    'pt-br' => ['pt[-_]br|brazilian portuguese', 'portuguesebr'],
    'pt'    => ['pt([-_][[:alpha:]]{2})?|portuguese', 'portuguese'],
    'ro'    => ['ro|romanian', 'romanian'],
    'ru'    => ['ru|russian', 'russian'],
    'sk'    => ['sk|slovak', 'slovak'],
    'sq'    => ['sq|albanian', 'albanian'],
    'sr'    => ['sr|serbian', 'serbian'],
    'srp'   => ['srp|serbian montenegrin', 'montenegrin'],
    'sv'    => ['sv|swedish', 'swedish'],
    'tl'    => ['tl|tagalok', 'tagalok'],
    'th'    => ['th|thai', 'thai'],
    'tr'    => ['tr|turkish', 'turkish'],
    'uk'    => ['uk|ukrainian', 'ukrainian'],
    'ur'    => ['ur|urdu', 'urdu'],
    'zh-tw' => ['zh[-_]tw|chinese traditional', 'tchinese'],
    'zh-cn' => ['zh[-_]cn|chinese simplified', 'schinese'],
];

/**
 * Analyzes some PHP environment variables to find the most probable language
 * that should be used
 *
 * @param string  $str     string to analyze
 * @param integer $envType type of the PHP environment variable which value is $str
 * @global        array    the list of available translations
 * @global        string   the retained translation keyword
 * @access   private
 * @return int|string
 */
function xoops_analyzeLanguage($str = '', $envType = '')
{
    global $available_languages;

    foreach ($available_languages as $key => $value) {
        // $envType =  1 for the 'HTTP_ACCEPT_LANGUAGE' environment variable,
        //             2 for the 'HTTP_USER_AGENT' one
        $expr = $value[0];
        if (strpos($expr, '[-_]') === false) {
            $expr = str_replace('|', '([-_][[:alpha:]]{2,3})?|', $expr);
        }
        if (($envType == 1 && preg_match('/^(' . $expr . ')(;q=[0-9]\\.[0-9])?$/i', $str))
            || ($envType == 2 && preg_match('/(\(|\[|;[[:space:]])(' . $expr . ')(;|\]|\))/', $str))) {
            $lang = $key;
            break;
        }
    }

    return $lang;
}

/**
 * @return string
 */
function xoops_detectLanguage()
{
    global $available_languages;

    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $HTTP_ACCEPT_LANGUAGE = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }

    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
    }

    $lang       = '';
    $xoops_lang = '';
    // 1. try to find out user's language by checking its HTTP_ACCEPT_LANGUAGE
    // variable
    if (empty($lang) && !empty($HTTP_ACCEPT_LANGUAGE)) {
        $accepted    = explode(',', $HTTP_ACCEPT_LANGUAGE);
        $acceptedCnt = count($accepted);
        reset($accepted);
        for ($i = 0; $i < $acceptedCnt; ++$i) {
            $lang = xoops_analyzeLanguage($accepted[$i], 1);
            if (strncasecmp($lang, 'en', 2)) {
                break;
            }
        }
    }
    // 2. try to find out user's language by checking its HTTP_USER_AGENT variable
    if (empty($lang) && !empty($HTTP_USER_AGENT)) {
        $lang = xoops_analyzeLanguage($HTTP_USER_AGENT, 2);
    }
    // 3. If we catch a valid language, configure it
    if (!empty($lang)) {
        $xoops_lang = $available_languages[$lang][1];
    }

    return $xoops_lang;
}
