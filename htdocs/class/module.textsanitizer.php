<?php
/**
 * XOOPS TextSanitizer extension
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2021 XOOPS Project (https://xoops.org)
 * @license             GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @since               2.0.0
 * @author              Kazumi Ono (http://www.myweb.ne.jp/, http://jp.xoops.org/)
 * @author              Goghs Cheng (http://www.eqiao.com, http://www.devbeez.com/)
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */

/**
 * Abstract class for extensions
 *
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 * @copyright       (c) 2000-2021 XOOPS Project (https://xoops.org)
 */
class MyTextSanitizerExtension
{
    public $instance;
    public $myts;
    public $config;
    public $image_path;

    /**
     * Constructor
     *
     * @param MyTextSanitizer $myts
     */
    public function __construct(MyTextSanitizer $myts)
    {
        $this->myts         = $myts;
        $this->image_path = XOOPS_URL . '/images/form';
    }

    /**
     * loadConfig
     *
     * @param  string $path
     * @return string|array
     */
    public static function loadConfig($path = null)
    {
        $myts   = \MyTextSanitizer::getInstance();
        $extensionName = (null === $path) ? '' : basename($path);
        $pathDist = $myts->path_basic;
        $pathConfig = $myts->path_config;

        if ('' !== $extensionName) {
            $configFileName = $pathConfig . '/config.' . $extensionName . '.php';
            $distFileName = $pathDist . '/' . $extensionName . '/config.' . $extensionName . '.dist.php';
        } else {
            $configFileName = $pathConfig . '/config.php';
            $distFileName = $pathDist . '/config.dist.php';
        }
        if (!file_exists($configFileName)) {
            if (false === copy($distFileName, $configFileName)) {
                trigger_error('Could not create textsanitizer config file ' . basename($configFileName));
                return $a = array();
            }
        }
        $configs = include $configFileName;
        return $configs;
    }

    /**
     * Merge Config
     *
     * @param  array $config_default
     * @param  array $config_custom
     * @return array
     */
    public static function mergeConfig($config_default, $config_custom)
    {
        if (is_array($config_custom)) {
            foreach ($config_custom as $key => $val) {
                if (is_array($config_default[$key])) {
                    $config_default[$key] = self::mergeConfig($config_default[$key], $config_custom[$key]);
                } else {
                    $config_default[$key] = $val;
                }
            }
        }

        return $config_default;
    }

    /**
     * encode
     *
     * @param string $textarea_id id attribute of text area
     *
     * @return array
     */
    public function encode($textarea_id)
    {
        return array(array(), array());
    }

    /**
     * decode
     *
     * @param string $url
     * @param string|integer $width
     * @param string|integer $height
     *
     * @return Null
     */
    public static function decode($url, $width, $height)
    {
        return null;
    }
}

/**
 * Class to "clean up" text for various uses
 *
 * <strong>Singleton</strong>
 *
 * @package       kernel
 * @subpackage    core
 * @author        Kazumi Ono <onokazu@xoops.org>
 * @author        Taiwen Jiang <phppp@users.sourceforge.net>
 * @author        Goghs Cheng
 * @copyright (c) 2000-2021 XOOPS Project (https://xoops.org)
 */
class MyTextSanitizer
{
    /**
     *
     * @var array
     */
    public $smileys = array();

    /**
     */
    public $censorConf;

    /**
     *
     * @var string holding reference to text
     */
    public $text         = '';
    public $patterns     = array();
    public $replacements = array();
    public $callbackPatterns = array();
    public $callbacks        = array();

    public $path_basic;
    public $path_config;
    public $path_plugin;

    public $config;

    /**
     * Constructor of this class
     *
     * Gets allowed html tags from admin config settings
     * <br> should not be allowed since nl2br will be used
     * when storing data.
     *
     * @access private
     */

    public function __construct()
    {
        $this->path_basic  = XOOPS_ROOT_PATH . '/class/textsanitizer';
        $this->path_config = XOOPS_VAR_PATH . '/configs/textsanitizer';
        $this->path_plugin = XOOPS_ROOT_PATH . '/Frameworks/textsanitizer';
        $this->config      = $this->loadConfig();
    }

    /**
     * Enter description here...
     *
     * @param  string $name
     * @return array|string
     */
    public function loadConfig($name = null)
    {
        // NB: sending a null name results in an infinite loop
        if (!empty($name)) {
            return MyTextSanitizerExtension::loadConfig($name);
        }

        $configFileName = $this->path_config . '/config.php';
        $distFileName = $this->path_basic . '/config.dist.php';

        if (!file_exists($configFileName)) {
            if (false===copy($distFileName, $configFileName)) {
                trigger_error('Could not create textsanitizer config file ' . basename($configFileName));
                return array();
            }
        }
        return include $configFileName;
    }

    /**
     * Enter description here...
     *
     * @param  array $config_default
     * @param  array $config_custom
     * @return mixed
     */
    public function mergeConfig($config_default, $config_custom)
    {
        if (is_array($config_custom)) {
            foreach ($config_custom as $key => $val) {
                if (isset($config_default[$key]) && \is_array($config_default[$key])) {
                    $config_default[$key] = $this->mergeConfig($config_default[$key], $config_custom[$key]);
                } else {
                    $config_default[$key] = $val;
                }
            }
        }

        return $config_default;
    }

    /**
     * Access the only instance of this class
     *
     * @return MyTextSanitizer
     */
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new MyTextSanitizer();
        }

        return $instance;
    }

    /**
     * Get the smileys
     *
     * @param bool $isAll TRUE for all smileys, FALSE for smileys with display = 1
     *
     * @return array
     */
    public function getSmileys($isAll = true)
    {
        if (count($this->smileys) == 0) {
            /** @var XoopsMySQLDatabase $xoopsDB */
            $xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
            $sql     = 'SELECT * FROM ' . $xoopsDB->prefix('smiles');
            $result  = $xoopsDB->query($sql);
            if ($xoopsDB->isResultSet($result)) {
                while (false !== ($smiles = $xoopsDB->fetchArray($result))) {
                    $this->smileys[] = $smiles;
                }
            }
        }
        if ($isAll) {
            return $this->smileys;
        }

        $smileys = array();
        foreach ($this->smileys as $smile) {
            if (empty($smile['display'])) {
                continue;
            }
            $smileys[] = $smile;
        }

        return $smileys;
    }

    /**
     * Replace emoticons in the message with smiley images
     *
     * @param  string $message
     * @return string
     */
    public function smiley($message)
    {
        $smileys = $this->getSmileys();
        foreach ($smileys as $smile) {
            $message = str_replace($smile['code'], '<img class="imgsmile" src="' . XOOPS_UPLOAD_URL . '/' . htmlspecialchars($smile['smile_url'], ENT_QUOTES) . '" alt="" />', $message);
        }

        return $message;
    }

    /**
     * Callback to process email address match
     *
     * @param array $match array of matched elements
     *
     * @return string
     */
    protected function makeClickableCallbackEmailAddress($match)
    {
        return $match[1] . "<a href=\"mailto:$match[2]@$match[3]\" title=\"$match[2]@$match[3]\">" . $match[2] . '@' . $match[3] . '</a>';
    }

    /**
     * Make links in the text clickable
     * Presently handles email addresses and http, https, ftp and sftp urls
     * (Note: at this time, major browsers no longer directly handle ftp/sftp urls.)
     *
     * @param  string $text
     * @return string
     */
    public function makeClickable($text)
    {
        $pattern = "/(^|[^]_a-z0-9-=\"'\/:\.])([-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+)@((?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?)/i";
        $text = preg_replace_callback($pattern, 'self::makeClickableCallbackEmailAddress', $text);
        //TODO after moving to PHP 7+ as minimum version, let's convert it to this
//        $text = preg_replace_callback($pattern, self::class . '::makeClickableCallbackEmailAddress', $text);


        $pattern = "/(?:\s+|^)(https?:\/\/)([-A-Z0-9.\_*?&:;=#\/\[\]\%@]+)/i";
        $replacement = '<a href="$1$2" target="_blank" rel="external noopener nofollow">$1$2</a>';
        $text = preg_replace($pattern, $replacement, $text);

        $pattern = "%(?:\s+|^)(s?ftp://)([-A-Z0-9./_*?&:;=#\[\]\%@]+)%i";
        $replacement = '<a href="$1$2" target="_blank" rel="external">$1$2</a>';
        $text = preg_replace($pattern, $replacement, $text);

        return $text;
    }

    /**
     * MyTextSanitizer::truncate()
     *
     * @param  mixed $text
     * @return mixed|string
     */
    public function truncate($text)
    {
        $instance = \MyTextSanitizer::getInstance();
        if (empty($text) || empty($instance->config['truncate_length']) || strlen($text) < $instance->config['truncate_length']) {
            return $text;
        }
        $len = floor($instance->config['truncate_length'] / 2);
        $ret = substr($text, 0, $len) . ' ... ' . substr($text, 5 - $len);

        return $ret;
    }

    /**
     * Replace XoopsCodes with their equivalent HTML formatting
     *
     * @param  string   $text
     * @param  bool|int $allowimage Allow images in the text?
     *                              On FALSE, uses links to the images.
     * @return string
     */
    public function &xoopsCodeDecode(&$text, $allowimage = 1)
    {
        $patterns       = array();
        $replacements   = array();
        $patterns[]     = "/\[siteurl=(['\"]?)([^\"'<>]*)\\1](.*)\[\/siteurl\]/sU";
        $replacements[] = '<a href="' . XOOPS_URL . '/\\2" title="">\\3</a>';
        $patterns[]     = "/\[url=(['\"]?)(http[s]?:\/\/[^\"'<>]*)\\1](.*)\[\/url\]/sU";
        $replacements[] = '<a href="\\2" rel="noopener external" title="">\\3</a>';
        $patterns[]     = "/\[url=(['\"]?)(ftp?:\/\/[^\"'<>]*)\\1](.*)\[\/url\]/sU";
        $replacements[] = '<a href="\\2" rel="external" title="">\\3</a>';
        $patterns[]     = "/\[url=(['\"]?)([^'\"<>]*)\\1](.*)\[\/url\]/sU";
        $replacements[] = '<a href="http://\\2" rel="noopener external" title="">\\3</a>';
        $patterns[]     = "/\[color=(['\"]?)([a-zA-Z0-9#]*)\\1](.*)\[\/color\]/sU";
        $replacements[] = '<span style="color: \\2;">\\3</span>';
        $patterns[]     = "/\[size=(['\"]?)([a-zA-Z0-9.#]*)\\1](.*)\[\/size\]/sU";
        $replacements[] = '<span style="font-size: \\2;">\\3</span>';
        $patterns[]     = "/\[font=(['\"]?)([^;<>\*\(\)\"']*)\\1](.*)\[\/font\]/sU";
        $replacements[] = '<span style="font-family: \\2;">\\3</span>';
        $patterns[]     = "/\[email]([^;<>\*\(\)\"']*)\[\/email\]/sU";
        $replacements[] = '<a href="mailto:\\1" title="">\\1</a>';

        $patterns[]     = "/\[b](.*)\[\/b\]/sU";
        $replacements[] = '<strong>\\1</strong>';
        $patterns[]     = "/\[i](.*)\[\/i\]/sU";
        $replacements[] = '<em>\\1</em>';
        $patterns[]     = "/\[u](.*)\[\/u\]/sU";
        $replacements[] = '<span style="text-decoration: underline;">\\1</span>';
        $patterns[]     = "/\[d](.*)\[\/d\]/sU";
        $replacements[] = '<del>\\1</del>';
        $patterns[]     = "/\[center](.*)\[\/center\]/sU";
        $replacements[] = '<div style="text-align: center;">\\1</div>';
        $patterns[]     = "/\[left](.*)\[\/left\]/sU";
        $replacements[] = '<div style="text-align: left;">\\1</div>';
        $patterns[]     = "/\[right](.*)\[\/right\]/sU";
        $replacements[] = '<div style="text-align: right;">\\1</div>';

        $this->text         = $text;
        $this->patterns     = $patterns;
        $this->replacements = $replacements;

        $this->config['allowimage'] = $allowimage;
        $this->executeExtensions();

        $text = preg_replace($this->patterns, $this->replacements, $this->text);
        //-------------------------------------------------------------------------------
        $count = count($this->callbackPatterns);

        for ($i = 0; $i < $count; ++$i) {
            $text = preg_replace_callback($this->callbackPatterns[$i], $this->callbacks[$i], $text);
        }
        //------------------------------------------------------------------------------
        $text = $this->quoteConv($text);

        return $text;
    }

    /**
     * Convert quote tags
     *
     * @param  string $text
     * @return string
     */
    public function quoteConv($text)
    {
        //look for both open and closing tags in the correct order
        $pattern     = "/\[quote](.*)\[\/quote\]/sU";
        $replacement = _QUOTEC . '<div class="xoopsQuote"><blockquote>\\1</blockquote></div>';

        $text = preg_replace($pattern, $replacement, $text, -1, $count);
        //no more matches, return now
        if (!$count) {
            return $text;
        }

        //new matches could have been created, keep doing it until we have no matches
        return $this->quoteConv($text);
    }

    /**
     * A quick solution for filtering XSS scripts
     *
     * @TODO : To be improved
     * @param $text
     * @return mixed
     */
    public function filterXss($text)
    {
        $patterns       = array();
        $replacements   = array();
        $text           = str_replace("\x00", '', $text);
        $c              = "[\x01-\x1f]*";
        $patterns[]     = "/\bj{$c}a{$c}v{$c}a{$c}s{$c}c{$c}r{$c}i{$c}p{$c}t{$c}[\s]*:/si";
        $replacements[] = 'javascript;';
        $patterns[]     = "/\ba{$c}b{$c}o{$c}u{$c}t{$c}[\s]*:/si";
        $replacements[] = 'about;';
        $patterns[]     = "/\bx{$c}s{$c}s{$c}[\s]*:/si";
        $replacements[] = 'xss;';
        $text           = preg_replace($patterns, $replacements, $text);

        return $text;
    }

    /**
     * Convert linebreaks to <br> tags
     *
     * @param  string $text
     * @return string
     */
    public function nl2Br($text)
    {
        return preg_replace('/(\015\012)|(\015)|(\012)/', '<br>', $text);
    }

    /**
     * Add slashes to the text if magic_quotes_gpc is turned off.
     *
     * @param  string $text
     * @return string
     */
    public function addSlashes($text)
    {
        if (!@get_magic_quotes_gpc()) {
            $text = addslashes($text);
        }

        return $text;
    }

    /**
     * Convert special characters to HTML entities
     *
     * @param  string $text    string being converted
     * @param  int|null    $quote_style
     * @param  string $charset character set used in conversion
     * @param  bool   $double_encode
     * @return string
     */
    public function htmlSpecialChars($text, $quote_style = NULL, $charset = null, $double_encode = true)
    {
        if ($quote_style === NULL) {
            $quote_style = ENT_QUOTES;
        }
        $text = (string) $text;
        if (version_compare(phpversion(), '5.2.3', '>=')) {
            $text = htmlspecialchars($text, $quote_style, $charset ?: (defined('_CHARSET') ? _CHARSET : 'UTF-8'), $double_encode);
        } else {
            $text = htmlspecialchars($text, $quote_style);
        }

        return preg_replace(array('/&amp;/i', '/&nbsp;/i'), array('&', '&amp;nbsp;'), $text);
    }

    /**
     * Reverses {@link htmlSpecialChars()}
     *
     * @param  string $text
     * @return string
     */
    public function undoHtmlSpecialChars($text)
    {
        return preg_replace(array('/&gt;/i', '/&lt;/i', '/&quot;/i', '/&#039;/i', '/&amp;nbsp;/i'), array('>', '<', '"', '\'', '&nbsp;'), $text);
    }

    /**
     * Filters textarea form data in DB for display
     *
     * @param  string   $text
     * @param  bool|int $html   allow html?
     * @param  bool|int $smiley allow smileys?
     * @param  bool|int $xcode  allow xoopscode?
     * @param  bool|int $image  allow inline images?
     * @param  bool|int $br     convert linebreaks?
     * @return string
     */
    public function &displayTarea($text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1)
    {
        $text = (string) $text;
        $charset = (defined('_CHARSET') ? _CHARSET : 'UTF-8');
        if (function_exists('mb_convert_encoding')) {
            $text = mb_convert_encoding($text, $charset, mb_detect_encoding($text, mb_detect_order(), true));
        }
        if ($html && $br) {
            $testText = strip_tags($text);
            if (mb_strlen($text) != mb_strlen($testText)) {
                $br = 0;
            }
            unset($testText);
        }
        if ($html != 1) {
            // html not allowed
            $text = $this->htmlSpecialChars($text, ENT_COMPAT, $charset);
        }
        $text = $this->codePreConv($text, $xcode); // Ryuji_edit(2003-11-18)
        if ($smiley != 0) {
            // process smiley
            $text = $this->smiley($text);
        }
        if ($xcode != 0) {
            // decode xcode
            if ($image != 0) {
                // image allowed
                $text =& $this->xoopsCodeDecode($text);
            } else {
                // image not allowed
                $text =& $this->xoopsCodeDecode($text, 0);
            }
        }
        if ($br != 0) {
            $text = $this->nl2Br($text);
        }
        $text = $this->codeConv($text, $xcode);
        $text = $this->makeClickable($text);
        if (!empty($this->config['filterxss_on_display'])) {
            $text = $this->filterXss($text);
        }

        return $text;
    }

    /**
     * Filters textarea form data submitted for preview
     *
     * @param  string   $text
     * @param  bool|int $html   allow html?
     * @param  bool|int $smiley allow smileys?
     * @param  bool|int $xcode  allow xoopscode?
     * @param  bool|int $image  allow inline images?
     * @param  bool|int $br     convert linebreaks?
     * @return string
     */
    public function &previewTarea($text, $html = 0, $smiley = 1, $xcode = 1, $image = 1, $br = 1)
    {
        $text = $this->stripSlashesGPC($text);
        $text =& $this->displayTarea($text, $html, $smiley, $xcode, $image, $br);

        return $text;
    }

    /**
     * Replaces banned words in a string with their replacements
     *
     * @param  string $text
     * @return string
     */
    public function &censorString(&$text)
    {
        $ret = $this->executeExtension('censor', $text);
        if ($ret === false) {
            return $text;
        }

        return $ret;
    }

    /**
     * MyTextSanitizer::codePreConv()
     *
     * @param  mixed $text
     * @param  mixed $xcode
     * @return mixed
     */
    public function codePreConv($text, $xcode = 1)
    {
        if ($xcode != 0) {
            //            $patterns = "/\[code([^\]]*?)\](.*)\[\/code\]/esU";
            //            $replacements = "'[code\\1]'.base64_encode('\\2').'[/code]'";

            $patterns = "/\[code([^\]]*?)\](.*)\[\/code\]/sU";
            $text = preg_replace_callback(
                $patterns,
                function ($matches) {
                    return '[code'. $matches[1] . ']' . base64_encode($matches[2]) . '[/code]';
                },
                $text
            );
        }

        return $text;
    }

    /**
     * @param $match
     *
     * @return string
     */
    public function codeConvCallback($match)
    {
        return '<div class="xoopsCode">' . $this->executeExtension('syntaxhighlight', str_replace('\\\"', '\"', base64_decode($match[2])), $match[1]) . '</div>';
    }

    /**
     * MyTextSanitizer::codeConv()
     *
     * @param  mixed $text
     * @param  mixed $xcode
     * @return mixed
     */
    public function codeConv($text, $xcode = 1)
    {
        if (empty($xcode)) {
            return $text;
        }
        $patterns = "/\[code([^\]]*?)\](.*)\[\/code\]/sU";
        $text1    = preg_replace_callback($patterns, array($this, 'codeConvCallback'), $text);

        return $text1;
    }

    /**
     * MyTextSanitizer::executeExtensions()
     *
     * @return bool
     */
    public function executeExtensions()
    {
        $extensions = array_filter($this->config['extensions']);
        if (empty($extensions)) {
            return true;
        }
        foreach (array_keys($extensions) as $extension) {
            $this->executeExtension($extension);
        }
        return null;
    }

    /**
     * MyTextSanitizer::loadExtension()
     *
     * @param  mixed $name
     * @return MyTextSanitizerExtension|false
     */
    public function loadExtension($name)
    {
        if (file_exists($file = $this->path_basic . '/' . $name . '/' . $name . '.php')) {
            include_once $file;
        } elseif (file_exists($file = $this->path_plugin . '/' . $name . '/' . $name . '.php')) {
            include_once $file;
        } else {
            return false;
        }
        $class = 'Myts' . ucfirst($name);
        if (!class_exists($class)) {
            trigger_error("Extension '{$name}' does not exist", E_USER_WARNING);

            return false;
        }
        return new $class($this);
    }

    /**
     * MyTextSanitizer::executeExtension()
     *
     * @param  mixed $name
     * @return mixed
     */
    public function executeExtension($name)
    {
        $extension = $this->loadExtension($name);
        $args      = array_slice(func_get_args(), 1);
        array_unshift($args, $this);

        return call_user_func_array(array($extension, 'load'), $args);
    }

    /**
     * Filter out possible malicious text
     * kses project at SF could be a good solution to check
     *
     * @param  string $text  text to filter
     * @param  bool   $force force filtering
     * @return string filtered text
     */
    public function textFilter($text, $force = false)
    {
        $ret = $this->executeExtension('textfilter', $text, $force);
        if ($ret === false) {
            return $text;
        }

        return $ret;
    }

    // #################### Deprecated Methods ######################

    /**
     * if magic_quotes_gpc is on, strip back slashes
     *
     * @param  string $text
     * @return string
     * @deprecated as of XOOPS 2.5.11 and will be removed in next XOOPS version
     *
     * This remains here until we officially drop support for PHP 5.3 in next release
     */
    public function stripSlashesGPC($text)
    {
        if (@get_magic_quotes_gpc()) {
            $text = stripslashes($text);
        }

        return $text;
    }

    /**
     * MyTextSanitizer::codeSanitizer()
     *
     * @param  mixed $str
     * @param  mixed $image
     * @return mixed|string
     * @deprecated will be removed in next XOOPS version
     */
    public function codeSanitizer($str, $image = 1)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');
        $str = $this->htmlSpecialChars(str_replace('\"', '"', base64_decode($str)));
        $str =& $this->xoopsCodeDecode($str, $image);

        return $str;
    }

    /**
     * MyTextSanitizer::sanitizeForDisplay()
     *
     * @param  mixed   $text
     * @param  integer $allowhtml
     * @param  integer $smiley
     * @param  mixed   $bbcode
     * @return mixed|string
     * @deprecated will be removed in next XOOPS version
     */
    public function sanitizeForDisplay($text, $allowhtml = 0, $smiley = 1, $bbcode = 1)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');
        if ($allowhtml == 0) {
            $text = $this->htmlSpecialChars($text);
        } else {
            // $config =& $GLOBALS['xoopsConfig'];
            // $allowed = $config['allowed_html'];
            // $text = strip_tags($text, $allowed);
            $text = $this->makeClickable($text);
        }
        if ($smiley == 1) {
            $text = $this->smiley($text);
        }
        if ($bbcode == 1) {
            $text =& $this->xoopsCodeDecode($text);
        }
        $text = $this->nl2Br($text);

        return $text;
    }

    /**
     * MyTextSanitizer::sanitizeForPreview()
     *
     * @param  mixed   $text
     * @param  integer $allowhtml
     * @param  integer $smiley
     * @param  mixed   $bbcode
     * @return mixed|string
     * @deprecated will be removed in next XOOPS version
     */
    public function sanitizeForPreview($text, $allowhtml = 0, $smiley = 1, $bbcode = 1)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');
        $text = $this->oopsStripSlashesGPC($text);
        if ($allowhtml == 0) {
            $text = $this->htmlSpecialChars($text);
        } else {
            // $config =& $GLOBALS['xoopsConfig'];
            // $allowed = $config['allowed_html'];
            // $text = strip_tags($text, $allowed);
            $text = $this->makeClickable($text);
        }
        if ($smiley == 1) {
            $text = $this->smiley($text);
        }
        if ($bbcode == 1) {
            $text =& $this->xoopsCodeDecode($text);
        }
        $text = $this->nl2Br($text);

        return $text;
    }

    /**
     * MyTextSanitizer::makeTboxData4Save()
     *
     * @param  mixed $text
     * @return string
     * @deprecated will be removed in next XOOPS version
     */
    public function makeTboxData4Save($text)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        // $text = $this->undoHtmlSpecialChars($text);
        return $this->addSlashes($text);
    }

    /**
     * MyTextSanitizer::makeTboxData4Show()
     *
     * @param  mixed $text
     * @param  mixed $smiley
     * @return mixed|string
     * @deprecated will be removed in next XOOPS version
     */
    public function makeTboxData4Show($text, $smiley = 0)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');
        $text = $this->htmlSpecialChars($text);

        return $text;
    }

    /**
     * MyTextSanitizer::makeTboxData4Edit()
     *
     * @param  mixed $text
     * @return string
     * @deprecated will be removed in next XOOPS version
     */
    public function makeTboxData4Edit($text)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return $this->htmlSpecialChars($text);
    }

    /**
     * MyTextSanitizer::makeTboxData4Preview()
     *
     * @param  mixed $text
     * @param  mixed $smiley
     * @return mixed|string
     * @deprecated will be removed in next XOOPS version
     */
    public function makeTboxData4Preview($text, $smiley = 0)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');
        $text = $this->stripSlashesGPC($text);
        $text = $this->htmlSpecialChars($text);

        return $text;
    }

    /**
     * MyTextSanitizer::makeTboxData4PreviewInForm()
     *
     * @param  mixed $text
     * @return string
     * @deprecated will be removed in next XOOPS version
     */
    public function makeTboxData4PreviewInForm($text)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');
        $text = $this->stripSlashesGPC($text);

        return $this->htmlSpecialChars($text);
    }

    /**
     * MyTextSanitizer::makeTareaData4Save()
     *
     * @param  mixed $text
     * @return string
     * @deprecated will be removed in next XOOPS version
     */
    public function makeTareaData4Save($text)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return $this->addSlashes($text);
    }

    /**
     * MyTextSanitizer::makeTareaData4Show()
     *
     * @param  mixed   $text
     * @param  integer $html
     * @param  integer $smiley
     * @param  mixed   $xcode
     * @return mixed|string
     * @deprecated will be removed in next XOOPS version
     */
    public function &makeTareaData4Show(&$text, $html = 1, $smiley = 1, $xcode = 1)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');
        $text =& $this->displayTarea($text, $html, $smiley, $xcode);

        return $text;
    }

    /**
     * MyTextSanitizer::makeTareaData4Edit()
     *
     * @param  mixed $text
     * @return string
     * @deprecated will be removed in next XOOPS version
     */
    public function makeTareaData4Edit($text)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return $this->htmlSpecialChars($text);
    }

    /**
     * MyTextSanitizer::makeTareaData4Preview()
     *
     * @param  mixed   $text
     * @param  integer $html
     * @param  integer $smiley
     * @param  mixed   $xcode
     * @return mixed|string
     * @deprecated will be removed in next XOOPS version
     */
    public function &makeTareaData4Preview(&$text, $html = 1, $smiley = 1, $xcode = 1)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');
        $text =& $this->previewTarea($text, $html, $smiley, $xcode);

        return $text;
    }

    /**
     * MyTextSanitizer::makeTareaData4PreviewInForm()
     *
     * @param  mixed $text
     * @return string
     * @deprecated will be removed in next XOOPS version
     */
    public function makeTareaData4PreviewInForm($text)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');
        // if magic_quotes_gpc is on, do stipslashes
        $text = $this->stripSlashesGPC($text);

        return $this->htmlSpecialChars($text);
    }

    /**
     * MyTextSanitizer::makeTareaData4InsideQuotes()
     *
     * @param  mixed $text
     * @return string
     * @deprecated will be removed in next XOOPS version
     */
    public function makeTareaData4InsideQuotes($text)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return $this->htmlSpecialChars($text);
    }

    /**
     * MyTextSanitizer::oopsStripSlashesGPC()
     *
     * @param  mixed $text
     * @return string
     * @deprecated will be removed in next XOOPS version
     */
    public function oopsStripSlashesGPC($text)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return $this->stripSlashesGPC($text);
    }

    /**
     * MyTextSanitizer::oopsStripSlashesRT()
     *
     * @param  mixed $text
     * @return mixed|string
     * @deprecated will be removed in next XOOPS version
     */
    public function oopsStripSlashesRT($text)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');
        if (get_magic_quotes_runtime()) {
            $text = stripslashes($text);
        }

        return $text;
    }

    /**
     * MyTextSanitizer::oopsAddSlashes()
     *
     * @param  mixed $text
     * @return string
     * @deprecated will be removed in next XOOPS version
     */
    public function oopsAddSlashes($text)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return $this->addSlashes($text);
    }

    /**
     * MyTextSanitizer::oopsHtmlSpecialChars()
     *
     * @param  mixed $text
     * @return string
     * @deprecated will be removed in next XOOPS version
     */
    public function oopsHtmlSpecialChars($text)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return $this->htmlSpecialChars($text);
    }

    /**
     * MyTextSanitizer::oopsNl2Br()
     *
     * @param  mixed $text
     * @return string
     * @deprecated will be removed in next XOOPS version
     */
    public function oopsNl2Br($text)
    {
        $GLOBALS['xoopsLogger']->addDeprecated(__METHOD__ . ' is deprecated');

        return $this->nl2Br($text);
    }
}
