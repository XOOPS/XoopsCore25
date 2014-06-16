<?php
/**
 * $Id: video.php
 * Module: XoopsTube
 */

function xtube_returnsource($returnsource) {
    switch ($returnsource) {
    case 0:
        $returnsource = _AM_XTUBE_YOUTUBE;
        break;
    case 1:
        $returnsource = _AM_XTUBE_METACAFE;
        break;
    case 2:
        $returnsource = _AM_XTUBE_IFILM;
        break;
    case 3:
        $returnsource = _AM_XTUBE_PHOTOBUCKET;
        break;
    case 4:
        $returnsource = _AM_XTUBE_VIDDLER;
        break;
    case 100:
        $returnsource = _AM_XTUBE_GOOGLEVIDEO;
        break;
    case 101:
        $returnsource = _AM_XTUBE_MYSPAVETV;
        break;
    case 102:
        $returnsource = _AM_XTUBE_DAILYMOTION;
        break;
    case 103:
        $returnsource = _AM_XTUBE_BLIPTV;
        break;
    case 104:
        $returnsource = _AM_XTUBE_CLIPFISH;
        break;
    case 105:
        $returnsource = _AM_XTUBE_LIVELEAK;
        break;
    case 106:
        $returnsource = _AM_XTUBE_MAKTOOB;
        break;
    case 107:
        $returnsource = _AM_XTUBE_VEOH;
        break;
    case 108:
        $returnsource = _AM_XTUBE_VIMEO;
        break;
    case 109:
        $returnsource = _AM_XTUBE_MEGAVIDEO;
        break;
    case 200:
        $returnsource = _AM_XTUBE_XOOPSTUBE;
        break;
    }
    return $returnsource;
}

// *******************************************************
// Function for determining source for creating screenshot
// *******************************************************
function xtube_videothumb($vidid, $title, $source, $picurl, $screenshot, $width = '', $height = '') {
    global $xoopsModuleConfig;
    if ($width == '' || $height == '') {
        $width  = $xoopsModuleConfig['shotwidth'];
        $height = $xoopsModuleConfig['shotheight'];
    }
    $thumb = '';
    switch ($source) {
// YouTube
    case 0:
        $thumb
            = '<img src="http://img.youtube.com/vi/' . $vidid . '/default.jpg"  title="' . $title . '" alt="' . $title
            . '" width="' . $width . '" height="' . $height . '" style="padding: 0px; border-style: none;" />';
        break;

// MetaCafe
    case 1:
        list($metaclip) = split('[/]', $vidid);
        $videothumb['metathumb'] = $metaclip;
        $thumb
                                 =
            '<img src="http://www.metacafe.com/thumb/' . $videothumb['metathumb'] . '.jpg" title="' . $title . '" alt="'
                . $title . '" width="' . $width . '" height="' . $height
                . '" style="padding: 0px; border-style: none;" />';
        break;

// iFilm/Spike
    case 2:
        $thumb = '<img src="http://img3.ifilmpro.com/resize/image/stills/films/resize/istd/' . $vidid . '.jpg?width='
            . $width . '"  title="' . $title . '" alt="' . $title . '" style="padding: 0px; border-style: none;" />';
        break;

// Photobucket
    case 3:
        $thumb
            =
            '<img src="http://i153.photobucket.com/albums/' . $vidid . '.jpg" width="' . $width . '" height="' . $height
                . '"  title="' . $title . '" alt="' . $title . '" style="padding: 0px; border-style: none;" />';
        break;

// Photobucket
    case 4:
        $thumb
            = '<img src="http://cdn-thumbs.viddler.com/thumbnail_2_' . $vidid . '.jpg" width="' . $width . '" height="'
            . $height . '"  title="' . $title . '" alt="' . $title . '" style="padding: 0px; border-style: none;" />';
        break;

// Google Video, MySpace TV, DailyMotion, BrightCove, Blip.tv, ClipFish, LiveLeak, Maktoob, Veoh
    case 100:
    case 101:
    case 102:
    case 103:
    case 104:
    case 105:
    case 106:
    case 107:
    case 108:
    case 109:
        $thumb
            = '<img src="' . $picurl . '" width="' . $width . '" height="' . $height . '"  title="' . $title . '" alt="'
            . $title . '" style="padding: 0px; border-style: none;" />';
        break;

// Determine if video source is XoopsTube for thumbnail
    case 200:
        $thumb
            =
            '<img src="' . XOOPS_URL . '/' . $screenshot . '" width="' . $width . '" height="' . $height . '"  title="'
                . $title . '" alt="' . $title . '" style="padding: 0px; border-style: none;" />';
        break;
    }
    return $thumb;
}

// **********************************
// Function for determining publisher
// **********************************
function xtube_videopublisher($vidid, $publisher, $source = 0) {

    switch ($source) {
        // Determine if video source YouTube for publisher
    case 0:
        $publisher
            = '<a href="http://www.youtube.com/profile?user=' . $publisher . '" target="_blank">' . $publisher . '</a>';
        break;

        // Determine if video source MetaCafe for publisher
    case 1:
        $publisher
            = '<a href="http://www.metacafe.com/channels/' . $publisher . '" target="_blank">' . $publisher . '</a>';
        break;

        // Determine if video source iFilm/Spike for publisher
    case 2:
        $publisher = '<a href="http://www.ifilm.com/profile/' . $publisher . '" target="_blank">' . $publisher . '</a>';
        break;

        // Determine if video source Photobucket for publisher
    case 3:
        $string = 'th_';
        list($photobucket) = split($string, $vidid);
        $ppublisher['ppublisher'] = $photobucket;
        $publisher
                                  =
            '<a href="http://s39.photobucket.com/albums/' . $ppublisher['ppublisher'] . '" target="_blank">'
                . $publisher . '</a>';
        break;

        // Determine if video source is Viddler for publisher
    case 4:
        $publisher
            = '<a href="http://www.viddler.com/explore/' . $publisher . '/" target="_blank">' . $publisher . '</a>';
        break;

        // Determine if video source is Google Video for publisher
    case 100:
    case 101:
    case 103:
    case 106:
    case 108:
    case 109:
        $publisher = $publisher;
        break;

        // Determine if video source is DailyMotion for publisher
    case 102:
        $publisher = '<a href="http://www.dailymotion.com/' . $publisher . '" target="_blank">' . $publisher . '</a>';
        break;

        // Determine if video source is ClipFish for publisher
    case 104:
        $publisher = '<a href="http://www.clipfish.de/user/' . $publisher . '" target="_blank">' . $publisher . '</a>';
        break;

        // Determine if video source is LiveLeak for publisher
    case 105:
        $publisher = '<a href="http://www.liveleak.com/user/' . $publisher . '" target="_blank">' . $publisher . '</a>';
        break;

        // Determine if video source is Veoh for publisher
    case 107:
        $publisher = '<a href="http://www.veoh.com/users/' . $publisher . '" target="_blank">' . $publisher . '</a>';
        break;

        // Determine if video source is XoopsTube for publisher
    case 200:
        $publisher = $publisher;
        break;
    }
    return $publisher;
}

// ************************************************
//Function for displaying videoclip (embedded code)
// ************************************************
function xtube_showvideo($vidid, $source, $screenshot, $picurl) {
    global $xoopsModule, $xoopsModuleConfig;
    $showvideo = '';
    $autoplay  = $xoopsModuleConfig['autoplay'];
    if ($xoopsModuleConfig['autoplay']) {
        $autoplay2   = 'yes';
        $autoplay3   = 'true';
        $photobucket = '&ap=1';
        $google      = 'FlashVars="autoPlay=true"';
        $viddler     = 'flashvars="autoplay=t"';
    } else {
        $autoplay2   = 'no';
        $autoplay3   = 'false';
        $photobucket = '';
        $google      = '';
        $viddler     = '';
    }

//	$hquality = '';
//	if ($hq == 1) {
//		$hquality = '&ap=%2526fmt%3D18&';
//	}

    switch ($source) {
// YouTube
    case 0:
        //  $showvideo = '<object width="480" height="295"><param name="movie" value="http://www.youtube.com/v/' . $vidid . '&ap=%2526fmt%3D18&&autoplay=' . $autoplay . '&rel=1&fs=1&color1=0x999999&color2=0x999999&border=0&loop=0"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/' . $vidid . '&ap=%2526fmt%3D18&&autoplay=' . $autoplay . '&rel=1&fs=1&color1=0x999999&color2=0x999999&border=0&loop=0" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="transparent" width="480" height="295"></embed></object>';
        $showvideo = '<embed src="http://www.youtube.com/v/' . $vidid . '&autoplay=' . $autoplay
            . '&fs=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="853" height="480"></embed>';
        break;

// MetaCafe
    case 1:
        $showvideo = '<embed flashVars="playerVars=showStats=no|autoPlay=' . $autoplay2
            . '" src="http://www.metacafe.com/fplayer/' . $vidid
            . '.swf" width="853" height="480" wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash"></embed>';
        break;

// iFilm/Spike
    case 2:
        $showvideo
            =
            '<embed width="853" height="480" src="http://www.spike.com/efp" quality="high" bgcolor="000000" name="efp" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="flvbaseclip='
                . $vidid . '" allowfullscreen="true"> </embed>';
        break;

// Photobucket
    case 3:
        $vidid = str_replace('th_', '', $vidid);
        $showvideo
               =
            '<embed width="853" height="480" type="application/x-shockwave-flash" wmode="transparent" src="http://i51.photobucket.com/player.swf?file=http://vid51.photobucket.com/albums/'
                . $vidid . '.flv' . $photobucket . '"></embed>';
        break;

// Viddler
    case 4:
        $showvideo = '<embed src="http://www.viddler.com/player/' . $vidid
            . '/" width="853" height="480" type="application/x-shockwave-flash" ' . $viddler
            . ' allowScriptAccess="always" allowFullScreen="true" name="viddler_' . $vidid . '" ></embed>';
        break;

// Google Video
    case 100:
        $showvideo
            =
            '<embed style="width:480px; height:295px;" id="VideoPlayback" type="application/x-shockwave-flash" src="http://video.google.com/googleplayer.swf?docId='
                . $vidid . '&hl=en" ' . $google . '> </embed>';
        break;

// MySpace TV
    case 101:
        $showvideo
            = '<embed src="http://mediaservices.myspace.com/services/media/embed.aspx/m=' . $vidid . ',t=1,mt=video,ap='
            . $autoplay
            . '" width="480" height="295" allowFullScreen="true" type="application/x-shockwave-flash"></embed>';
        break;

// DailyMotion
    case 102:
        $showvideo = '<embed src="http://www.dailymotion.com/swf/' . $vidid . '&autoPlay=' . $autoplay
            . '" type="application/x-shockwave-flash" width="480" height="295" allowFullScreen="true" allowScriptAccess="always"></embed>';
        break;

// Blip.tv
    case 103:
        $showvideo = '<embed src="http://blip.tv/play/' . $vidid
            . '" type="application/x-shockwave-flash" width="480" height="295" allowscriptaccess="always" allowfullscreen="true" flashvars="autostart='
            . $autoplay3 . '"></embed>';
        break;

// ClipFish
    case 104:
        $showvideo = '<embed src="http://www.clipfish.de/videoplayer.swf?as=' . $autoplay . '&videoid=' . $vidid
            . '==&r=1&c=0067B3" quality="high" bgcolor="#0067B3" width="464" height="380" name="player" align="middle" allowFullScreen="true" allowScriptAccess="always"  type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>'; // Change c=0067B3 for different player color
        break;

// LiveLeak
    case 105:
        $showvideo = '<embed src="http://www.liveleak.com/e/' . $vidid
            . '" type="application/x-shockwave-flash" flashvars="autostart=' . $autoplay3
            . '" wmode="transparent" width="450" height="370"></embed>';
        break;

// Maktoob
    case 106:
        $showvideo
            =
            '<embed width="448" height="320" align="middle" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" name="flvplayer" bgcolor="#ffffff" devicefont="true" wmode="transparent" quality="high" src="http://clipat.maktoob.com/flvplayerOurJS.swf?file=http://'
                . $vidid . '.flv&enablejs=true&image=' . $picurl
                . '&lightcolor=0x557722&backcolor=0x000000&frontcolor=0xCCCCCC&showfsbutton=true&autostart='
                . $autoplay3
                . '&logo=http://clipat.maktoob.com/language/ar_sa/images/clipat-icon.png&displaywidth=448" />';
        break;

// Veoh
    case 107:
        $showvideo = '<embed src="http://www.veoh.com/veohplayer.swf?permalinkId=' . $vidid
            . '&id=anonymous&player=videodetailsembedded&affiliateId=&videoAutoPlay=' . $autoplay
            . '" allowFullScreen="true" width="480" height="295" bgcolor="#FFFFFF" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"></embed>';
        break;

// Vimeo 
    case 108:
        $showvideo = '<embed src="http://vimeo.com/moogaloop.swf?clip_id=' . $vidid
            . '&server=vimeo.com&show_title=1&show_byline=1&show_portrait=0&color=&fullscreen=1&autoplay=' . $autoplay
            . '" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" quality="best" width="400" height="321"></embed>';
        break;

// Megavideo 
    case 109:
        $showvideo = '<object width="640" height="363"><param name="movie" value="http://www.megavideo.com/v/' . $vidid
            . '"></param><param name="allowFullScreen" value="true"></param><embed src="http://www.megavideo.com/v/'
            . $vidid
            . '" type="application/x-shockwave-flash" allowfullscreen="true" width="640" height="363"></embed></object>';
        break;


// XoopsTube
    case 200:
        $showvideo = '<embed src="' . XOOPS_URL . '/modules/' . $xoopsModule->getvar('dirname')
            . '/include/mediaplayer.swf" width="425" height="350" allowScriptAccess="always" allowFullScreen="true" flashvars="width=425&height=350&file='
            . XOOPS_URL . '/' . $xoopsModuleConfig['videodir'] . '/' . $vidid . '&image=' . XOOPS_URL . '/'
            . $xoopsModuleConfig['videoimgdir'] . '/' . $screenshot . '&autostart=' . $autoplay3 . '"></embed>';
        break;
    }

    return $showvideo;
}

?>