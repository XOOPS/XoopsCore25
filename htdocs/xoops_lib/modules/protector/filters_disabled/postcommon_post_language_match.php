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
 * Class protector_postcommon_post_language_match
 *
 * Check post content conformance to the system language. Requires UTF-8 environment with mbstring.
 *
 * This filter compares post data to the characters that define the current system language.
 * If the number of characters that are not normally used in the language exceeds a threshold,
 * the post will be rejected.
 *
 * The threshold can be adjusted in $maximumTolerance.
 *
 * A value of 0.02 (2% non-language characters) can often discriminate between multiple Latin languages,
 * while values approaching 1.0 (100% non-language) indicate totally different alphabets, such as comparing
 * English (Latin) to Russian (Cyrillic.) Some commonalities are always possible with "loanwords," so this
 * number always represents tendency, not absolutes.
 *
 * Certain ranges are common to all languages, whitespace, punctuations, currency symbols, emoji, etc.
 * These are automatically excluded from the analysis.
 *
 * If site requirements are for multiple languages concurrently, a $customRange can be set to to include
 * the requirements of both languages.
 *
 * Ranges are in regular expression format as used in preg_replace()
 *
 * If the language filter detects a mismatch, the post is denied. If the mismatch is more that double (2 times)
 * the configured threshold, the account is deactivated.
 *
 * @category  Protector\Filter
 * @package   Protector
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2016 XOOPS Project (http://xoops.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://xoops.org
 */
class Protector_postcommon_post_language_match extends ProtectorFilterAbstract
{

    /** @var int after this number of posts by the user, skip this filter */
    protected $minPosts = 10;

    /** @var float maximum proportion of off-language characters to accept */
    protected $maximumTolerance = 0.02;

    /** @var string|null custom character range to match, null to use default for current language */
    protected $customRange = null;

    /** @var int do not run analysis if input length is less than this */
    protected $minLength = 15;

    /** @var string[] script names we do NOT want to process */
    protected $skipThese = array('edituser.php', 'register.php', 'search.php', 'user.php', 'lostpass.php');

    // map regex compatible unicode script range to a XOOPS language name
    // http://php.net/manual/en/regexp.reference.unicode.php
    // http://www.regular-expressions.info/unicode.html
    // http://www.localizingjapan.com/blog/2012/01/20/regular-expressions-for-japanese-text/
    protected $scriptCodes = array(
        'arabic'       => '\p{Arabic}',
        'brazilian'    => 'A-Za-zÁáÂâĀãÀàÇçÉéÊêÍíÓóÔôŌõÚú',
        'bulgarian'    => '\p{Cyrillic}',
        'chinese_zh'   => '\p{Han}',
        'croatian'     => 'A-PR-Va-pr-vĆćČčĐđŠšŽž',
        'czech'        => 'A-Za-zÁáČčĎďÉéĚěÍíŇňÓóŘřŠšŤťÚúŮůÝýŽž',
        'danish'       => 'A-Za-zÆØÅæøå',
        'dutch'        => 'A-Za-zĲĳ',
        'english'      => 'A-Za-z',
        'french'       => 'A-Za-zÀàÂâÆæÇçÈèÉéÊêËëÎîÏïÔôŒœÙùÛûÜü',
        'german'       => 'A-Za-zÄäÉéÖöÜüß',
        'greek'        => '\p{Greek}',
        'hebrew'       => '\p{Hebrew}',
        'hungarian'    => '\p{Latin}',
        'italian'      => 'A-IL-VZa-il-vzÀÈÉÌÒÙàèéìòù',
        'japanese'     => '\p{Han}\p{Hiragana}\p{Katakana}',
        'korean'       => '\p{Hangul}',
        'malaysian'    => 'A-Za-z',
        'norwegian'    => 'A-Za-zÆØÅæøå',
        'persian'      => '\p{Arabic}',
        'polish'       => 'A-Za-zĄąĘęÓóĆćŁłŃńŚśŹźŻż',
        'portuguesebr' => 'A-Za-zÁáÂâĀãÀàÇçÉéÊêÍíÓóÔôŌõÚú',
        'portuguese'   => 'A-Za-zÁáÂâĀãÀàÇçÉéÊêÍíÓóÔôŌõÚú',
        'russian'      => '\p{Cyrillic}',
        'schinese'     => '\p{Han}',
        'slovak'       => 'A-Za-zÁáČčĎďÉéÍíĹĺĽľŇňÓóÔôŔŕŠšŤťÚúÝýŽž',
        'slovenian'    => 'A-PR-VZa-pr-vzČčŠšŽž',
        'spanish'      => 'A-Za-zÁáÉéÍíÑñÓóÚúÜü',
        'swedish'      => 'A-Za-zÅåÄäÖö',
        'tchinese'     => '\p{Han}',
        'thai'         => '\p{Thai}',
        'turkish'      => 'A-PR-VYZÇĞİÖŞÜÂÎÛa-pr-vyzçğiöşüâîû',
        'vietnamese'   => 'A-Za-zàÀảẢãÃáÁạẠăĂằẰẳẲẵẴắẮặẶâÂầẦẩẨẫẪấẤậẬđĐèÈẻẺẽẼéÉẹẸêÊềỀểỂễỄếẾệỆìÌỉỈĩĨíÍịỊòÒỏỎõÕóÓọỌôÔồỒổỔỗỖốỐộỘơƠờỜởỞỡỠớỚợỢùÙủỦũŨúÚụỤưƯừỪửỬữỮứỨựỰỳỲỷỶỹỸýÝỵỴ',
    );

    /**
     * stripEmoji - remove pictographic characters, i.e. emoji and dingbats from a string
     *
     * @param string $string UTF-8 encoded string
     *
     * @return string without pictographs
     */
    protected function stripEmoji($string)
    {
        return  preg_replace('/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $string);
    }

    /**
     * Execute the filter
     *
     * @return bool
     */
    public function execute()
    {
        /* @var $xoopsUser XoopsUser */
        global $xoopsUser;

        if (!function_exists('mb_strlen')) {
            return true;
        }

        // we only check POST transactions
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return true;
        }

        // don't process for admin and experienced users
        if (is_object($xoopsUser) && ($xoopsUser->isAdmin() || $this->minPosts < $xoopsUser->posts())) {
            return true;
        }

        $uid = is_object($xoopsUser) ? $xoopsUser->uid() : 0;

        // skip register.php and edituser.php updates (your name is your name)
        if (in_array(basename($_SERVER['SCRIPT_FILENAME']), $this->skipThese)) {
            return true;
        }

        // get all strings from $_POST
        $testString = '';
        foreach ($_POST as $key => $postData) {
            // dare to ignore arrays/objects
            if (!is_string($postData)) {
                continue;
            }
            $testString .= $postData;
        }

        // not big enough to analyse effectively
        if (mb_strlen($testString) < $this->minLength) {
            return true;
        }

        $language = $GLOBALS['xoopsConfig']['language'];
        $range = isset($this->scriptCodes[$language]) ? $this->scriptCodes[$language] : 'p\{Latin}';
        $range = !empty($this->customRange) ? $this->customRange : $range;

        // remove emoji from computations (a smilie cat is universal)
        $testString = $this->stripEmoji($testString);

        $reduced = preg_replace('/[\p{Common}' . $range . ']+/u', '', $testString);

        $remainingLength = (float) mb_strlen($reduced, 'UTF-8');
        $fullLength = (float) mb_strlen($testString, 'UTF-8');
        $percent = ($fullLength > 0) ? $remainingLength / $fullLength : 0.0;

        if ($percent > $this->maximumTolerance) {
            $report = array(
                'score' => $percent,
                'uri' => $_SERVER['REQUEST_URI'],
                'post' => $_POST,
            );
            $this->protector->message = json_encode($report);
            $this->protector->output_log('SPAM Language Map', $uid);
            if ($uid > 0 && $percent > (2.0 * $this->maximumTolerance)) {
                $this->protector->deactivateCurrentUser();
                $this->protector->_should_be_banned_time0 = true;
            } else {
                $this->protector->purgeNoExit();
            }
            // write any message as you like
            echo 'Your post has been denied. '
                . 'If you feel this is in error, please contact the site administrator.';
            exit;
        }

        return true;
    }
}
