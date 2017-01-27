<?php

/**
 * Class MytsSoundcloud
 */
class MytsSoundcloud extends MyTextSanitizerExtension
{
    /**
     * @param $textarea_id
     *
     * @return array
     */
    public function encode($textarea_id)
    {
        $config = parent::loadConfig(__DIR__);

        $code = "<button type='button' class='btn btn-default btn-sm' onclick='xoopsCodeSoundCloud(\"{$textarea_id}\",\""
            . htmlspecialchars(_XOOPS_FORM_ENTER_SOUNDCLOUD_URL, ENT_QUOTES)
            . "\");' onmouseover='style.cursor=\"hand\"' title='" . _XOOPS_FORM_ALT_SOUNDCLOUD
            . "'><span class='fa fa-fw fa-soundcloud' aria-hidden='true'></span></button>";
        $javascript = <<<EOH
            function xoopsCodeSoundCloud(id, enterSoundCloud)
            {
                var selection = xoopsGetSelect(id);
                if (selection.length > 0) {
                    var text = selection;
                } else {
                    var text = prompt(enterSoundCloud, "");
                }

                var domobj = xoopsGetElementById(id);
                if (text.length > 0) {
                xoopsInsertText(domobj, "[soundcloud]"+text+"[/soundcloud]");
                }
                domobj.focus();
            }
EOH;

        return array($code, $javascript);
    }

    /**
     * @param $ts
     */
    public function load($ts)
    {
        $ts->callbackPatterns[] = "/\[soundcloud\](http[s]?:\/\/[^\"'<>]*)(.*)\[\/soundcloud\]/sU";
        $ts->callbacks[]        = __CLASS__ . '::myCallback';
    }

    /**
     * @param $match
     *
     * @return string
     */
    public static function myCallback($match)
    {
        $url    = $match[1] . $match[2];
        $config = parent::loadConfig(__DIR__);
        if (!preg_match("/^http[s]?:\/\/(www\.)?soundcloud\.com\/(.*)/i", $url, $matches)) {
            trigger_error("Not matched: {$url}", E_USER_WARNING);

            return '';
        }

        $code = '<object height="81" width="100%"><param name="movie" ' . 'value="http://player.soundcloud.com/player.swf?url=' . $url . '&amp;g=bb">' . '</param><param name="allowscriptaccess" value="always"></param>' . '<embed allowscriptaccess="always" height="81" ' . 'src="http://player.soundcloud.com/player.swf?url=' . $url . '&amp;g=bb" type="application/x-shockwave-flash" width="100%"></embed></object>' . '<a href="' . $url . '">' . $url . '</a>';

        return $code;
    }

    //   public static function decode($url1, $url2)
    //   {
    //   }
}
