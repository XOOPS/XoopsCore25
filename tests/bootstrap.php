<?php
/**
 * PHPUnit bootstrap for XOOPS Core tests
 *
 * Defines constants and loads minimal class files needed for unit testing.
 * Does NOT connect to the database or start a session.
 */

// Prevent double-inclusion
if (defined('XOOPS_TEST_BOOTSTRAP')) {
    return;
}
define('XOOPS_TEST_BOOTSTRAP', true);

// Core path constants
if (!defined('XOOPS_ROOT_PATH')) {
    define('XOOPS_ROOT_PATH', dirname(__DIR__) . '/htdocs');
}
if (!defined('XOOPS_PATH')) {
    define('XOOPS_PATH', XOOPS_ROOT_PATH . '/xoops_lib');
}
if (!defined('XOOPS_VAR_PATH')) {
    define('XOOPS_VAR_PATH', XOOPS_ROOT_PATH . '/xoops_data');
}
if (!defined('XOOPS_URL')) {
    define('XOOPS_URL', 'http://localhost');
}
if (!defined('XOOPS_TRUST_PATH')) {
    define('XOOPS_TRUST_PATH', XOOPS_ROOT_PATH . '/xoops_lib');
}

// Directory separator
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// DB constants
if (!defined('XOOPS_DB_TYPE')) {
    define('XOOPS_DB_TYPE', 'mysql');
}
if (!defined('XOOPS_DB_PREFIX')) {
    define('XOOPS_DB_PREFIX', 'xoops');
}
if (!defined('XOOPS_DB_NAME')) {
    define('XOOPS_DB_NAME', 'xoops_test');
}
if (!defined('XOOPS_DB_HOST')) {
    define('XOOPS_DB_HOST', 'localhost');
}
if (!defined('XOOPS_DB_USER')) {
    define('XOOPS_DB_USER', 'test');
}
if (!defined('XOOPS_DB_PASS')) {
    define('XOOPS_DB_PASS', 'test');
}
if (!defined('XOOPS_DB_PCONNECT')) {
    define('XOOPS_DB_PCONNECT', 0);
}

// Language / system constant stubs
if (!defined('_NONE')) {
    define('_NONE', 'None');
}
if (!defined('_DB_QUERY_ERROR')) {
    define('_DB_QUERY_ERROR', 'DB Query Error: %s');
}
if (!defined('_MSC_ORIGINAL_IMAGE')) {
    define('_MSC_ORIGINAL_IMAGE', 'Original Image');
}
if (!defined('_QUOTEC')) {
    define('_QUOTEC', '"');
}
if (!defined('XOOPS_GROUP_ADMIN')) {
    define('XOOPS_GROUP_ADMIN', 1);
}
if (!defined('XOOPS_GROUP_USERS')) {
    define('XOOPS_GROUP_USERS', 2);
}
if (!defined('XOOPS_GROUP_ANONYMOUS')) {
    define('XOOPS_GROUP_ANONYMOUS', 3);
}
if (!defined('XOOPS_CONF')) {
    define('XOOPS_CONF', 1);
}
if (!defined('XOOPS_CONF_USER')) {
    define('XOOPS_CONF_USER', 2);
}
if (!defined('XOOPS_CONF_METAFOOTER')) {
    define('XOOPS_CONF_METAFOOTER', 3);
}
if (!defined('XOOPS_CONF_CENSOR')) {
    define('XOOPS_CONF_CENSOR', 4);
}
if (!defined('XOOPS_CONF_SEARCH')) {
    define('XOOPS_CONF_SEARCH', 5);
}
if (!defined('XOOPS_CONF_MAILER')) {
    define('XOOPS_CONF_MAILER', 6);
}
if (!defined('XOOPS_CONF_AUTH')) {
    define('XOOPS_CONF_AUTH', 7);
}
$GLOBALS['xoopsUserIsAdmin'] = false;

// Form-related constants
if (!defined('NWLINE')) {
    define('NWLINE', "\n");
}
if (!defined('_FORM_ENTER')) {
    define('_FORM_ENTER', 'Please enter %s');
}
if (!defined('_YES')) {
    define('_YES', 'Yes');
}
if (!defined('_NO')) {
    define('_NO', 'No');
}
if (!defined('_ALL')) {
    define('_ALL', 'All');
}
if (!defined('_SUBMIT')) {
    define('_SUBMIT', 'Submit');
}
if (!defined('_CANCEL')) {
    define('_CANCEL', 'Cancel');
}
if (!defined('_SELECT')) {
    define('_SELECT', 'Select');
}
if (!defined('_STARTSWITH')) {
    define('_STARTSWITH', 'Starts with');
}
if (!defined('_ENDSWITH')) {
    define('_ENDSWITH', 'Ends with');
}
if (!defined('_MATCHES')) {
    define('_MATCHES', 'Matches');
}
if (!defined('_CONTAINS')) {
    define('_CONTAINS', 'Contains');
}
if (!defined('XOOPS_MATCH_START')) {
    define('XOOPS_MATCH_START', 0);
}
if (!defined('XOOPS_MATCH_END')) {
    define('XOOPS_MATCH_END', 1);
}
if (!defined('XOOPS_MATCH_EQUAL')) {
    define('XOOPS_MATCH_EQUAL', 2);
}
if (!defined('XOOPS_MATCH_CONTAIN')) {
    define('XOOPS_MATCH_CONTAIN', 3);
}
if (!defined('_SHORTDATESTRING')) {
    define('_SHORTDATESTRING', 'Y/m/d');
}
if (!defined('_MA_USER_REMOVE')) {
    define('_MA_USER_REMOVE', 'Remove');
}
if (!defined('_MA_USER_MORE')) {
    define('_MA_USER_MORE', 'Find more users');
}

// Renderer constants — used by XoopsFormRendererLegacy
if (!defined('_DELETE')) {
    define('_DELETE', 'Delete');
}
if (!defined('_RESET')) {
    define('_RESET', 'Reset');
}
if (!defined('_PREVIEW')) {
    define('_PREVIEW', 'Preview');
}
if (!defined('_REQUIRED')) {
    define('_REQUIRED', 'Required');
}
if (!defined('_CLOSE')) {
    define('_CLOSE', 'Close');
}
if (!defined('_SIZE')) {
    define('_SIZE', 'Size');
}
if (!defined('_FONT')) {
    define('_FONT', 'Font');
}
if (!defined('_COLOR')) {
    define('_COLOR', 'Color');
}

// DHTML textarea constants
if (!defined('_ENTERURL')) {
    define('_ENTERURL', 'Enter URL');
}
if (!defined('_ENTERWEBTITLE')) {
    define('_ENTERWEBTITLE', 'Enter Web Title');
}
if (!defined('_ENTEREMAIL')) {
    define('_ENTEREMAIL', 'Enter Email');
}
if (!defined('_ENTERIMGURL')) {
    define('_ENTERIMGURL', 'Enter Image URL');
}
if (!defined('_ENTERIMGPOS')) {
    define('_ENTERIMGPOS', 'Enter Image Position');
}
if (!defined('_IMGPOSRORL')) {
    define('_IMGPOSRORL', 'R or L');
}
if (!defined('_ERRORIMGPOS')) {
    define('_ERRORIMGPOS', 'Invalid image position');
}
if (!defined('_ENTERCODE')) {
    define('_ENTERCODE', 'Enter Code');
}
if (!defined('_ENTERQUOTE')) {
    define('_ENTERQUOTE', 'Enter Quote');
}

// Form alt-text constants
if (!defined('_XOOPS_FORM_ALT_LENGTH')) {
    define('_XOOPS_FORM_ALT_LENGTH', 'Length');
}
if (!defined('_XOOPS_FORM_ALT_LENGTH_MAX')) {
    define('_XOOPS_FORM_ALT_LENGTH_MAX', 'Max Length');
}
if (!defined('_XOOPS_FORM_ALT_CHECKLENGTH')) {
    define('_XOOPS_FORM_ALT_CHECKLENGTH', 'Check Length');
}
if (!defined('_XOOPS_FORM_ALT_URL')) {
    define('_XOOPS_FORM_ALT_URL', 'URL');
}
if (!defined('_XOOPS_FORM_ALT_EMAIL')) {
    define('_XOOPS_FORM_ALT_EMAIL', 'Email');
}
if (!defined('_XOOPS_FORM_ALT_ENTERWIDTH')) {
    define('_XOOPS_FORM_ALT_ENTERWIDTH', 'Enter Width');
}
if (!defined('_XOOPS_FORM_ALT_IMG')) {
    define('_XOOPS_FORM_ALT_IMG', 'Image');
}
if (!defined('_XOOPS_FORM_ALT_IMAGE')) {
    define('_XOOPS_FORM_ALT_IMAGE', 'Image');
}
if (!defined('_XOOPS_FORM_ALT_SMILEY')) {
    define('_XOOPS_FORM_ALT_SMILEY', 'Smiley');
}
if (!defined('_XOOPS_FORM_ALT_CODE')) {
    define('_XOOPS_FORM_ALT_CODE', 'Code');
}
if (!defined('_XOOPS_FORM_ALT_QUOTE')) {
    define('_XOOPS_FORM_ALT_QUOTE', 'Quote');
}
if (!defined('_XOOPS_FORM_ALT_BOLD')) {
    define('_XOOPS_FORM_ALT_BOLD', 'Bold');
}
if (!defined('_XOOPS_FORM_ALT_ITALIC')) {
    define('_XOOPS_FORM_ALT_ITALIC', 'Italic');
}
if (!defined('_XOOPS_FORM_ALT_UNDERLINE')) {
    define('_XOOPS_FORM_ALT_UNDERLINE', 'Underline');
}
if (!defined('_XOOPS_FORM_ALT_LINETHROUGH')) {
    define('_XOOPS_FORM_ALT_LINETHROUGH', 'Linethrough');
}
if (!defined('_XOOPS_FORM_ALT_LEFT')) {
    define('_XOOPS_FORM_ALT_LEFT', 'Left');
}
if (!defined('_XOOPS_FORM_ALT_CENTER')) {
    define('_XOOPS_FORM_ALT_CENTER', 'Center');
}
if (!defined('_XOOPS_FORM_ALT_RIGHT')) {
    define('_XOOPS_FORM_ALT_RIGHT', 'Right');
}
if (!defined('_XOOPS_FORM_PREVIEW_CONTENT')) {
    define('_XOOPS_FORM_PREVIEW_CONTENT', 'Preview Content');
}

// Calendar constants
if (!defined('_CAL_SUNDAY')) {
    define('_CAL_SUNDAY', 'Sunday');
}
if (!defined('_CAL_MONDAY')) {
    define('_CAL_MONDAY', 'Monday');
}
if (!defined('_CAL_TUESDAY')) {
    define('_CAL_TUESDAY', 'Tuesday');
}
if (!defined('_CAL_WEDNESDAY')) {
    define('_CAL_WEDNESDAY', 'Wednesday');
}
if (!defined('_CAL_THURSDAY')) {
    define('_CAL_THURSDAY', 'Thursday');
}
if (!defined('_CAL_FRIDAY')) {
    define('_CAL_FRIDAY', 'Friday');
}
if (!defined('_CAL_SATURDAY')) {
    define('_CAL_SATURDAY', 'Saturday');
}
if (!defined('_CAL_JANUARY')) {
    define('_CAL_JANUARY', 'January');
}
if (!defined('_CAL_FEBRUARY')) {
    define('_CAL_FEBRUARY', 'February');
}
if (!defined('_CAL_MARCH')) {
    define('_CAL_MARCH', 'March');
}
if (!defined('_CAL_APRIL')) {
    define('_CAL_APRIL', 'April');
}
if (!defined('_CAL_MAY')) {
    define('_CAL_MAY', 'May');
}
if (!defined('_CAL_JUNE')) {
    define('_CAL_JUNE', 'June');
}
if (!defined('_CAL_JULY')) {
    define('_CAL_JULY', 'July');
}
if (!defined('_CAL_AUGUST')) {
    define('_CAL_AUGUST', 'August');
}
if (!defined('_CAL_SEPTEMBER')) {
    define('_CAL_SEPTEMBER', 'September');
}
if (!defined('_CAL_OCTOBER')) {
    define('_CAL_OCTOBER', 'October');
}
if (!defined('_CAL_NOVEMBER')) {
    define('_CAL_NOVEMBER', 'November');
}
if (!defined('_CAL_DECEMBER')) {
    define('_CAL_DECEMBER', 'December');
}
if (!defined('_CAL_TGL1STD')) {
    define('_CAL_TGL1STD', 'Toggle first day of week');
}
if (!defined('_CAL_PREVYR')) {
    define('_CAL_PREVYR', 'Prev. year');
}
if (!defined('_CAL_PREVMNTH')) {
    define('_CAL_PREVMNTH', 'Prev. month');
}
if (!defined('_CAL_GOTODAY')) {
    define('_CAL_GOTODAY', 'Go Today');
}
if (!defined('_CAL_NXTMNTH')) {
    define('_CAL_NXTMNTH', 'Next month');
}
if (!defined('_CAL_NEXTYR')) {
    define('_CAL_NEXTYR', 'Next year');
}
if (!defined('_CAL_SELDATE')) {
    define('_CAL_SELDATE', 'Select date');
}
if (!defined('_CAL_DRAGMOVE')) {
    define('_CAL_DRAGMOVE', 'Drag to move');
}
if (!defined('_CAL_TODAY')) {
    define('_CAL_TODAY', 'Today');
}
if (!defined('_CAL_DISPM1ST')) {
    define('_CAL_DISPM1ST', 'Display Monday first');
}
if (!defined('_CAL_DISPS1ST')) {
    define('_CAL_DISPS1ST', 'Display Sunday first');
}

// TextSanitizer plugin constants
if (!defined('_XOOPS_FORM_ENTERYOUTUBEURL')) {
    define('_XOOPS_FORM_ENTERYOUTUBEURL', 'Enter YouTube URL');
}

// Multibyte constants
if (!defined('XOOPS_USE_MULTIBYTES')) {
    define('XOOPS_USE_MULTIBYTES', 0);
}
if (!defined('_CHARSET')) {
    define('_CHARSET', 'UTF-8');
}
if (!defined('XOOPS_THEME_PATH')) {
    define('XOOPS_THEME_PATH', XOOPS_ROOT_PATH . '/themes');
}

// XOOPS version + cache path (needed by Frameworks)
if (!defined('XOOPS_VERSION')) {
    define('XOOPS_VERSION', 'XOOPS 2.5.12-RC1');
}
if (!defined('XOOPS_CACHE_PATH')) {
    define('XOOPS_CACHE_PATH', XOOPS_VAR_PATH . '/caches/xoops_cache');
}

// Upload paths (needed by module tests)
if (!defined('XOOPS_UPLOAD_PATH')) {
    define('XOOPS_UPLOAD_PATH', XOOPS_ROOT_PATH . '/uploads');
}
if (!defined('XOOPS_UPLOAD_URL')) {
    define('XOOPS_UPLOAD_URL', XOOPS_URL . '/uploads');
}

// Publisher module constants (avoids loading publisher/include/common.php)
if (!defined('PUBLISHER_CONSTANTS_DEFINED')) {
    define('PUBLISHER_DIRNAME', 'publisher');
    define('PUBLISHER_URL', XOOPS_URL . '/modules/publisher');
    define('PUBLISHER_PATH', XOOPS_ROOT_PATH . '/modules/publisher');
    define('PUBLISHER_ROOT_PATH', XOOPS_ROOT_PATH . '/modules/publisher');
    define('PUBLISHER_IMAGES_URL', PUBLISHER_URL . '/assets/images');
    define('PUBLISHER_ADMIN_URL', PUBLISHER_URL . '/admin');
    define('PUBLISHER_UPLOAD_URL', XOOPS_UPLOAD_URL . '/publisher');
    define('PUBLISHER_UPLOAD_PATH', XOOPS_UPLOAD_PATH . '/publisher');
    define('PUBLISHER_CONSTANTS_DEFINED', 1);
}

// Stub formatTimestamp() for tests — returns formatted date without XoopsLocal
if (!function_exists('formatTimestamp')) {
    function formatTimestamp($time, $format = 'l', $timeoffset = '')
    {
        if ($format === 'l' || $format === 'long') {
            return date('Y/m/d H:i:s', (int)$time);
        }
        if ($format === 's' || $format === 'short') {
            return date('Y/m/d', (int)$time);
        }
        return date($format, (int)$time);
    }
}

/**
 * Stub XoopsSecurity for tests — provides createToken() without sessions.
 */
if (!class_exists('XoopsSecurity')) {
    class XoopsSecurity
    {
        public function createToken($timeout = 0, $name = 'XOOPS_TOKEN')
        {
            return 'test_token_' . $name;
        }
    }
}
if (!isset($GLOBALS['xoopsSecurity'])) {
    $GLOBALS['xoopsSecurity'] = new XoopsSecurity();
}

// PSR-4 autoloader for test namespaces
spl_autoload_register(function ($class) {
    $map = [
        'kernel\\'         => __DIR__ . '/unit/htdocs/kernel/',
        'xoopsforms\\'     => __DIR__ . '/unit/htdocs/class/xoopsforms/',
        'xoopsclass\\'     => __DIR__ . '/unit/htdocs/class/',
        'xoopsauth\\'      => __DIR__ . '/unit/htdocs/class/auth/',
        'xoopsdatabase\\'  => __DIR__ . '/unit/htdocs/class/database/',
        'xoopsutility\\'   => __DIR__ . '/unit/htdocs/class/utility/',
        'xoopsxml\\'       => __DIR__ . '/unit/htdocs/class/xml/',
        'xoopsfile\\'      => __DIR__ . '/unit/htdocs/class/file/',
        'xoopsmodel\\'     => __DIR__ . '/unit/htdocs/class/model/',
        'xoopslogger\\'    => __DIR__ . '/unit/htdocs/class/logger/',
        'xoopscache\\'     => __DIR__ . '/unit/htdocs/class/cache/',
        'frameworksart\\'  => __DIR__ . '/unit/htdocs/Frameworks/art/',
        'frameworksmoduleclasses\\' => __DIR__ . '/unit/htdocs/Frameworks/moduleclasses/',
        'frameworkstextsanitizer\\' => __DIR__ . '/unit/htdocs/Frameworks/textsanitizer/',
        'modulessystem\\'       => __DIR__ . '/unit/htdocs/modules/system/',
        'modulespm\\'           => __DIR__ . '/unit/htdocs/modules/pm/',
        'modulesprofile\\'      => __DIR__ . '/unit/htdocs/modules/profile/',
        'modulesprotector\\'    => __DIR__ . '/unit/htdocs/modules/protector/',
    ];
    foreach ($map as $prefix => $dir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $file = $dir . str_replace('\\', '/', substr($class, $len)) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// Stub xoops_loadLanguage for module tests
if (!function_exists('xoops_loadLanguage')) {
    function xoops_loadLanguage($name, $domain = '', $language = null)
    {
        return false;
    }
}

// Stub xoops_getModuleOption for module tests
if (!function_exists('xoops_getModuleOption')) {
    function xoops_getModuleOption($option, $dirname = '')
    {
        return null;
    }
}

// Stub xoops_getenv for SystemMenuHandler
if (!function_exists('xoops_getenv')) {
    function xoops_getenv($key)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : '';
    }
}

// Auth language constants
if (!defined('_AUTH_MSG_AUTH_METHOD')) {
    define('_AUTH_MSG_AUTH_METHOD', 'using %s authentication method');
}
if (!defined('_AUTH_LDAP_EXTENSION_NOT_LOAD')) {
    define('_AUTH_LDAP_EXTENSION_NOT_LOAD', 'PHP LDAP extension not loaded');
}
if (!defined('_AUTH_LDAP_SERVER_NOT_FOUND')) {
    define('_AUTH_LDAP_SERVER_NOT_FOUND', "Can't connect to the server");
}
if (!defined('_AUTH_LDAP_USER_NOT_FOUND')) {
    define('_AUTH_LDAP_USER_NOT_FOUND', 'Member %s not found in the directory server (%s) in %s');
}
if (!defined('_AUTH_LDAP_CANT_READ_ENTRY')) {
    define('_AUTH_LDAP_CANT_READ_ENTRY', "Can't read entry %s");
}
if (!defined('_AUTH_LDAP_XOOPS_USER_NOTFOUND')) {
    define('_AUTH_LDAP_XOOPS_USER_NOTFOUND', 'Sorry, no corresponding user information has been found in the XOOPS database for connection: %s');
}
if (!defined('_AUTH_LDAP_START_TLS_FAILED')) {
    define('_AUTH_LDAP_START_TLS_FAILED', 'Failed to open a TLS connection');
}
if (!defined('_US_INCORRECTLOGIN')) {
    define('_US_INCORRECTLOGIN', 'Incorrect Login!');
}

// Lostpass language constants
foreach ([
    '_US_PWDMAILED'     => 'If a matching account was found, an email with instructions has been sent.',
    '_US_ENTERPWD'      => 'Please enter a password.',
    '_US_PASSNOTSAME'   => 'The two passwords do not match.',
    '_US_PWDTOOSHORT'   => 'Password must be at least %s characters.',
    '_US_MAILPWDNG'     => 'Failed to update password. Please try again.',
    '_US_NEWPWDREQ'     => 'Password Reset Request at %s',
    '_US_PASSWORD'      => 'Password',
    '_US_VERIFYPASS'    => 'Verify Password',
    '_US_SUBMIT'        => 'Submit',
    '_US_LOSTPASSWORD'  => 'Lost Password',
    '_US_SORRYNOTFOUND' => 'Sorry, no matching user was found.',
    '_US_CONFMAIL'      => 'A confirmation email has been sent.',
] as $constName => $constValue) {
    if (!defined($constName)) {
        define($constName, $constValue);
    }
}
if (!defined('_XO_ER_CLASSNOTFOUND')) {
    define('_XO_ER_CLASSNOTFOUND', 'Class Not Found');
}

// Stub xoops_utf8_encode/decode for auth tests
if (!function_exists('xoops_utf8_encode')) {
    function xoops_utf8_encode($text)
    {
        return (string) $text;
    }
}
if (!function_exists('xoops_utf8_decode')) {
    function xoops_utf8_decode($text)
    {
        return (string) $text;
    }
}

/**
 * Exception thrown when redirect_header() is called in tests.
 */
class RedirectHeaderException extends \RuntimeException
{
    public string $url;
    public int $time;

    public function __construct(string $url, int $time, string $message)
    {
        $this->url  = $url;
        $this->time = $time;
        parent::__construct($message);
    }
}

// Stub redirect_header() for tests — throws exception instead of sending headers
if (!function_exists('redirect_header')) {
    function redirect_header($url, $time = 3, $message = '', $addredirect = true, $allowExternalLink = false)
    {
        throw new RedirectHeaderException((string) $url, (int) $time, (string) $message);
    }
}

// Stub $GLOBALS['xoops'] path helper
if (!isset($GLOBALS['xoops'])) {
    $GLOBALS['xoops'] = new class {
        public function path(string $relPath): string
        {
            return XOOPS_ROOT_PATH . '/' . ltrim($relPath, '/');
        }
    };
}

// Model / write handler constants
if (!defined('_DBTIMESTAMPSTRING')) {
    define('_DBTIMESTAMPSTRING', 'Y-m-d H:i:s');
}
if (!defined('_DBTIMESTRING')) {
    define('_DBTIMESTRING', 'H:i:s');
}
if (!defined('_DBDATESTRING')) {
    define('_DBDATESTRING', 'Y-m-d');
}
if (!defined('_XOBJ_ERR_REQUIRED')) {
    define('_XOBJ_ERR_REQUIRED', '%s is required');
}
if (!defined('_XOBJ_ERR_SHORTERTHAN')) {
    define('_XOBJ_ERR_SHORTERTHAN', '%s must be shorter than %d characters');
}
if (!defined('XOOPS_PROT')) {
    define('XOOPS_PROT', 'http://');
}

// Stub xoops_convert_encode for model write tests
if (!function_exists('xoops_convert_encode')) {
    function xoops_convert_encode($text)
    {
        return (string) $text;
    }
}

// ModuleAdmin language constants
if (!defined('_AM_SYSTEM_HELP')) {
    define('_AM_SYSTEM_HELP', 'Help');
}
if (!defined('_MI_SYSTEM_ADMENU6')) {
    define('_MI_SYSTEM_ADMENU6', 'Preferences');
}
if (!defined('_AM_MODULEADMIN_CONFIG')) {
    define('_AM_MODULEADMIN_CONFIG', 'Configuration Check');
}
if (!defined('_AM_MODULEADMIN_CONFIG_CHMOD')) {
    define('_AM_MODULEADMIN_CONFIG_CHMOD', "The folder '%s' must be with a chmod '%s' (it's now set on %s).");
}
if (!defined('_AM_MODULEADMIN_CONFIG_FOLDERKO')) {
    define('_AM_MODULEADMIN_CONFIG_FOLDERKO', "The folder '%s' does not exist");
}
if (!defined('_AM_MODULEADMIN_CONFIG_FOLDEROK')) {
    define('_AM_MODULEADMIN_CONFIG_FOLDEROK', "The folder '%s' exists");
}
if (!defined('_AM_MODULEADMIN_CONFIG_PHP')) {
    define('_AM_MODULEADMIN_CONFIG_PHP', 'Minimum PHP required: %s (your version is %s)');
}
if (!defined('_AM_MODULEADMIN_CONFIG_XOOPS')) {
    define('_AM_MODULEADMIN_CONFIG_XOOPS', 'Minimum XOOPS required: %s (your version is %s)');
}
if (!defined('_AM_MODULEADMIN_CONFIG_DB')) {
    define('_AM_MODULEADMIN_CONFIG_DB', 'Minimum version required: %s (your version is %s)');
}
if (!defined('_AM_MODULEADMIN_CONFIG_ADMIN')) {
    define('_AM_MODULEADMIN_CONFIG_ADMIN', 'Minimum ModuleAdmin required: %s (your version is %s)');
}
if (!defined('_AM_MODULEADMIN_ABOUT_CHANGELOG')) {
    define('_AM_MODULEADMIN_ABOUT_CHANGELOG', 'Change log');
}
if (!defined('_AM_MODULEADMIN_ABOUT_DESCRIPTION')) {
    define('_AM_MODULEADMIN_ABOUT_DESCRIPTION', 'Description');
}
if (!defined('_AM_MODULEADMIN_ABOUT_MODULEINFO')) {
    define('_AM_MODULEADMIN_ABOUT_MODULEINFO', 'Module Info');
}
if (!defined('_AM_MODULEADMIN_ABOUT_MODULESTATUS')) {
    define('_AM_MODULEADMIN_ABOUT_MODULESTATUS', 'Status');
}
if (!defined('_AM_MODULEADMIN_ABOUT_UPDATEDATE')) {
    define('_AM_MODULEADMIN_ABOUT_UPDATEDATE', 'Updated');
}
if (!defined('_AM_MODULEADMIN_ABOUT_WEBSITE')) {
    define('_AM_MODULEADMIN_ABOUT_WEBSITE', 'Website');
}
if (!defined('_AM_MODULEADMIN_ABOUT_BY')) {
    define('_AM_MODULEADMIN_ABOUT_BY', 'by ');
}
if (!defined('_AM_MODULEADMIN_ABOUT_AMOUNT')) {
    define('_AM_MODULEADMIN_ABOUT_AMOUNT', 'Amount');
}
if (!defined('_AM_MODULEADMIN_ABOUT_AMOUNT_TTL')) {
    define('_AM_MODULEADMIN_ABOUT_AMOUNT_TTL', 'Please enter USD amount');
}
if (!defined('_AM_MODULEADMIN_ABOUT_AMOUNT_CURRENCY')) {
    define('_AM_MODULEADMIN_ABOUT_AMOUNT_CURRENCY', 'USD');
}
if (!defined('_AM_MODULEADMIN_ABOUT_AMOUNT_SUGGESTED')) {
    define('_AM_MODULEADMIN_ABOUT_AMOUNT_SUGGESTED', '25.00');
}
if (!defined('_AM_MODULEADMIN_ABOUT_AMOUNT_PATTERN')) {
    define('_AM_MODULEADMIN_ABOUT_AMOUNT_PATTERN', '\\$?[0-9]+(,[0-9]{3})*(\\.[0-9]{0,2})?$');
}
if (!defined('_AM_MODULEADMIN_ABOUT_DONATE_IMG_ALT')) {
    define('_AM_MODULEADMIN_ABOUT_DONATE_IMG_ALT', 'Donate');
}

// Stub checkEmail() for ModuleAdmin renderAbout
if (!function_exists('checkEmail')) {
    function checkEmail($email, $antispam = false)
    {
        return (false !== filter_var($email, FILTER_VALIDATE_EMAIL));
    }
}

// Load the XOOPS autoloader
require_once XOOPS_ROOT_PATH . '/class/xoopsload.php';

// Load foundational class files in dependency order
require_once XOOPS_ROOT_PATH . '/class/module.textsanitizer.php';
require_once XOOPS_ROOT_PATH . '/class/preload.php';
require_once XOOPS_ROOT_PATH . '/kernel/object.php';
require_once XOOPS_ROOT_PATH . '/class/criteria.php';
require_once XOOPS_ROOT_PATH . '/class/database/database.php';
require_once XOOPS_ROOT_PATH . '/class/database/mysqldatabase.php';
require_once XOOPS_ROOT_PATH . '/class/database/databasefactory.php';
require_once XOOPS_ROOT_PATH . '/kernel/module.php';
require_once XOOPS_ROOT_PATH . '/class/logger/xoopslogger.php';
require_once XOOPS_ROOT_PATH . '/class/model/xoopsmodel.php';
require_once XOOPS_ROOT_PATH . '/class/xml/saxparser.php';
require_once XOOPS_ROOT_PATH . '/class/xml/xmltaghandler.php';
if (file_exists(XOOPS_ROOT_PATH . '/class/file/xoopsfile.php')) {
    require_once XOOPS_ROOT_PATH . '/class/file/xoopsfile.php';
}

/**
 * Lightweight stub DB for Criteria::render() and kernel code in tests.
 * Provides quote/escape/prefix without a real connection.
 */
class XoopsTestStubDatabase extends XoopsMySQLDatabase
{
    public function __construct()
    {
        parent::__construct();
    }

    public function connect($selectdb = true)
    {
        return true;
    }

    public function quote($value)
    {
        return "'" . addslashes((string) $value) . "'";
    }

    public function escape($value)
    {
        return addslashes((string) $value);
    }

    public function prefix($table = '')
    {
        return ($table !== '') ? 'xoops_' . $table : 'xoops';
    }

    public function query(string $sql, ?int $limit = null, ?int $start = null)
    {
        return false;
    }

    public function queryF($sql, $limit = 0, $start = 0)
    {
        return false;
    }

    public function exec(string $sql): bool
    {
        return false;
    }

    public function genId($sequence)
    {
        return 0;
    }

    public function getInsertId()
    {
        return 0;
    }

    public function fetchArray($result)
    {
        return false;
    }

    public function fetchRow($result)
    {
        return false;
    }

    public function getRowsNum($result)
    {
        return 0;
    }

    public function isResultSet($result)
    {
        return false;
    }

    public function freeRecordSet($result)
    {
    }

    public function error()
    {
        return '';
    }

    public function errno()
    {
        return 0;
    }

    public function getServerVersion()
    {
        return '8.0.0';
    }
}

// Set global DB stub
$GLOBALS['xoopsDB'] = new XoopsTestStubDatabase();

// Register a preload event to override database factory class with our stub
class XoopsTestDatabasePreload extends XoopsPreloadItem
{
    public static function eventCoreclassdatabasedatabasefactoryconnection($args)
    {
        $args[0] = 'XoopsTestStubDatabase';
    }
}

// Inject our preload event into the XoopsPreload event registry via reflection
$preloadInstance = XoopsPreload::getInstance();
$ref = new ReflectionClass($preloadInstance);
$prop = $ref->getProperty('_events');
$prop->setAccessible(true);
$events = $prop->getValue($preloadInstance);
$events['coreclassdatabasedatabasefactoryconnection'][] = [
    'class_name' => 'XoopsTestDatabasePreload',
    'method'     => 'eventCoreclassdatabasedatabasefactoryconnection',
];
$prop->setValue($preloadInstance, $events);

/**
 * Stub config handler for tests — returns empty configs so censor/etc. are no-ops.
 */
class XoopsTestStubConfigHandler
{
    public function getConfigsByCat($category)
    {
        return [];
    }

    public function getConfigs($criteria = null, $asObject = true)
    {
        return [];
    }

    public function getConfigCount($criteria = null)
    {
        return 0;
    }

    public function getConfigsByModule($module_id = 0)
    {
        return [];
    }
}

/**
 * Test-compatible xoops_getHandler() — returns stub handlers without real DB.
 *
 * @param string $name
 * @param bool   $optional
 * @return XoopsObjectHandler|object|false
 */
function xoops_getHandler($name, $optional = false)
{
    static $handlers = [];
    $name = strtolower(trim($name));

    if (!isset($handlers[$name])) {
        // Special case: config handler needs its own stub
        if ($name === 'config') {
            $handlers[$name] = new XoopsTestStubConfigHandler();
        } else {
            if (file_exists($hnd_file = XOOPS_ROOT_PATH . '/kernel/' . $name . '.php')) {
                require_once $hnd_file;
            }
            $class = 'Xoops' . ucfirst($name) . 'Handler';
            if (class_exists($class)) {
                $ref = new ReflectionClass($class);
                $handler = $ref->newInstanceWithoutConstructor();
                // Inject the stub DB via reflection
                $current = $ref;
                while ($current) {
                    if ($current->hasProperty('db')) {
                        $prop = $current->getProperty('db');
                        $prop->setAccessible(true);
                        $prop->setValue($handler, $GLOBALS['xoopsDB']);
                        break;
                    }
                    $current = $current->getParentClass();
                }
                $handlers[$name] = $handler;
            }
        }
    }
    if (!isset($handlers[$name])) {
        if (!$optional) {
            trigger_error('Class Xoops' . ucfirst($name) . 'Handler not found (test stub)', E_USER_WARNING);
        }
        return false;
    }

    return $handlers[$name];
}

/**
 * Test-compatible xoops_getModuleHandler()
 */
function xoops_getModuleHandler($name, $module_dir = '', $optional = false)
{
    return false;
}

/**
 * Test-compatible xoops_load()
 */
function xoops_load($name, $type = 'core')
{
    return XoopsLoad::load($name, $type);
}
