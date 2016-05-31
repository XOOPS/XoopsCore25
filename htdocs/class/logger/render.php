<?php
/**
 * Xoops Logger renderer
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
 * @subpackage          logger
 * @since               2.3.0
 * @author              Skalpa Keo <skalpa@xoops.org>
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 *
 * @todo                Not well written, just keep as it is. Refactored in 3.0
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

$ret = '';
if ($mode === 'popup') {
    $dump    = $this->dump('');
    $content = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
    <meta http-equiv="content-language" content="' . _LANGCODE . '" />
    <meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '" />
    <title>' . $xoopsConfig['sitename'] . ' - ' . _LOGGER_DEBUG . ' </title>
    <meta name="generator" content="XOOPS" />
    <link rel="stylesheet" type="text/css" media="all" href="' . xoops_getcss($xoopsConfig['theme_set']) . '" />
</head>
<body>' . $dump . '
    <div style="text-align:center;">
        <input class="formButton" value="' . _CLOSE . '" type="button" onclick="window.close();" />
    </div>
';
    $ret .= '
<script type="text/javascript">
    debug_window = openWithSelfMain("about:blank", "popup", 680, 450, true);
    debug_window.document.clear();
';
    $lines = preg_split("/(\r\n|\r|\n)( *)/", $content);
    foreach ($lines as $line) {
        $ret .= "\n" . 'debug_window.document.writeln("' . str_replace(array('"', '</'), array('\"', '<\/'), $line) . '");';
    }
    $ret .= '
    debug_window.focus();
    debug_window.document.close();
</script>
';
}

$this->addExtra(_LOGGER_INCLUDED_FILES, sprintf(_LOGGER_FILES, count(get_included_files())));
$memory = 0;

if (function_exists('memory_get_usage')) {
    $memory = memory_get_usage() . ' bytes';
} else {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $out = array();
        exec('tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $out);
        if (isset($out[5])) {
            $memory = sprintf(_LOGGER_MEM_ESTIMATED, substr($out[5], strpos($out[5], ':') + 1));
        }
    }
}
if ($memory) {
    $this->addExtra(_LOGGER_MEM_USAGE, $memory);
}

if (empty($mode)) {
    $views = array('errors', 'deprecated', 'queries', 'blocks', 'extra');
    $ret .= "\n<div id=\"xo-logger-output\">\n<div id='xo-logger-tabs'>\n";
    $ret .= "<a href='javascript:xoSetLoggerView(\"none\")'>" . _LOGGER_NONE . "</a>\n";
    $ret .= "<a href='javascript:xoSetLoggerView(\"\")'>" . _LOGGER_ALL . "</a>\n";
    foreach ($views as $view) {
        $count = count($this->$view);
        $ret .= "<a href='javascript:xoSetLoggerView(\"$view\")'>" . constant('_LOGGER_' . strtoupper($view)) . " ($count)</a>\n";
    }
    $count = count($this->logstart);
    $ret .= "<a href='javascript:xoSetLoggerView(\"timers\")'>" . _LOGGER_TIMERS . "($count)</a>\n";
    $ret .= "</div>\n";
}

if (empty($mode) || $mode === 'errors') {
    $types = array(
        E_USER_NOTICE  => _LOGGER_E_USER_NOTICE,
        E_USER_WARNING => _LOGGER_E_USER_WARNING,
        E_USER_ERROR   => _LOGGER_E_USER_ERROR,
        E_NOTICE       => _LOGGER_E_NOTICE,
        E_WARNING      => _LOGGER_E_WARNING,/*E_STRICT       => _LOGGER_E_STRICT*/);
    $class = 'even';
    $ret .= '<table id="xo-logger-errors" class="outer"><tr><th>' . _LOGGER_ERRORS . '</th></tr>';
    foreach ($this->errors as $error) {
        $ret .= "\n<tr><td class='$class'>";
        $ret .= isset($types[$error['errno']]) ? $types[$error['errno']] : _LOGGER_UNKNOWN;
        $ret .= ': ';
        $ret .= sprintf(_LOGGER_FILELINE, $this->sanitizePath($error['errstr']), $this->sanitizePath($error['errfile']), $error['errline']);
        $ret .= "<br>\n</td></tr>";
        $class = ($class === 'odd') ? 'even' : 'odd';
    }
    $ret .= "\n</table>\n";
}

if (empty($mode) || $mode === 'deprecated') {
    $class = 'even';
    $ret .= '<table id="xo-logger-deprecated" class="outer"><tr><th>' . _LOGGER_DEPRECATED . '</th></tr>';
    foreach ($this->deprecated as $message) {
        $ret .= "\n<tr><td class='$class'>";
        $ret .= $message;
        $ret .= "<br>\n</td></tr>";
        $class = ($class === 'odd') ? 'even' : 'odd';
    }
    $ret .= "\n</table>\n";
}

if (empty($mode) || $mode === 'queries') {
    $class = 'even';
    $ret .= '<table id="xo-logger-queries" class="outer"><tr><th>' . _LOGGER_QUERIES . '</th></tr>';
    $xoopsDB = XoopsDatabaseFactory::getDatabaseConnection();
    $pattern = '/\b' . preg_quote($xoopsDB->prefix()) . '\_/i';

    foreach ($this->queries as $q) {
        $sql        = preg_replace($pattern, '', $q['sql']);
        $query_time = isset($q['query_time']) ? sprintf('%0.6f - ', $q['query_time']) : '';

        if (isset($q['error'])) {
            $ret .= '<tr class="' . $class . '"><td><span style="color:#ff0000;">' . $query_time . htmlentities($sql) . '<br><strong>Error number:</strong> ' . $q['errno'] . '<br><strong>Error message:</strong> ' . $q['error'] . '</span></td></tr>';
        } else {
            $ret .= '<tr class="' . $class . '"><td>' . $query_time . htmlentities($sql) . '</td></tr>';
        }

        $class = ($class === 'odd') ? 'even' : 'odd';
    }
    $ret .= '<tr class="foot"><td>' . _LOGGER_TOTAL . ': <span style="color:#ff0000;">' . count($this->queries) . '</span></td></tr></table>';
}
if (empty($mode) || $mode === 'blocks') {
    $class = 'even';
    $ret .= '<table id="xo-logger-blocks" class="outer"><tr><th colspan="2">' . _LOGGER_BLOCKS . '</th></tr>';
    foreach ($this->blocks as $b) {
        if ($b['cached']) {
            $ret .= '<tr><td class="' . $class . '"><strong>' . $b['name'] . ':</strong> ' . sprintf(_LOGGER_CACHED, (int)$b['cachetime']) . '</td></tr>';
        } else {
            $ret .= '<tr><td class="' . $class . '"><strong>' . $b['name'] . ':</strong> ' . _LOGGER_NOT_CACHED . '</td></tr>';
        }
        $class = ($class === 'odd') ? 'even' : 'odd';
    }
    $ret .= '<tr class="foot"><td>' . _LOGGER_TOTAL . ': <span style="color:#ff0000;">' . count($this->blocks) . '</span></td></tr></table>';
}
if (empty($mode) || $mode === 'extra') {
    $class = 'even';
    $ret .= '<table id="xo-logger-extra" class="outer"><tr><th colspan="2">' . _LOGGER_EXTRA . '</th></tr>';
    foreach ($this->extra as $ex) {
        $ret .= '<tr><td class="' . $class . '"><strong>';
        $ret .= htmlspecialchars($ex['name']) . ':</strong> ' . htmlspecialchars($ex['msg']);
        $ret .= '</td></tr>';
        $class = ($class === 'odd') ? 'even' : 'odd';
    }
    $ret .= '</table>';
}
if (empty($mode) || $mode === 'timers') {
    $class = 'even';
    $ret .= '<table id="xo-logger-timers" class="outer"><tr><th colspan="2">' . _LOGGER_TIMERS . '</th></tr>';
    foreach ($this->logstart as $k => $v) {
        $ret .= '<tr><td class="' . $class . '"><strong>';
        $ret .= sprintf(_LOGGER_TIMETOLOAD, htmlspecialchars($k) . '</strong>', '<span style="color:#ff0000;">' . sprintf('%.03f', $this->dumpTime($k)) . '</span>');
        $ret .= '</td></tr>';
        $class = ($class === 'odd') ? 'even' : 'odd';
    }
    $ret .= '</table>';
}

if (empty($mode)) {
    $ret .= <<<EOT
</div>
<script type="text/javascript">
    function xoLogCreateCookie(name,value,days)
    {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime()+(days*24*60*60*1000));
            var expires = "; expires="+date.toGMTString();
        } else var expires = "";
        document.cookie = name+"="+value+expires+"; path=/";
    }
    function xoLogReadCookie(name)
    {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
        }

        return null;
    }
    function xoLogEraseCookie(name)
    {
        createCookie(name,"",-1);
    }
    function xoSetLoggerView( name )
    {
        var log = document.getElementById( "xo-logger-output" );
        if ( !log ) return null;
        var i, elt;
        for (i=0; i!=log.childNodes.length; i++) {
            elt = log.childNodes[i];
            if ( elt.tagName && elt.tagName.toLowerCase() != 'script' && elt.id != "xo-logger-tabs" ) {
                elt.style.display = ( !name || elt.id == "xo-logger-" + name ) ? "block" : "none";
            }
        }
        xoLogCreateCookie( 'XOLOGGERVIEW', name, 1 );
    }
    xoSetLoggerView( xoLogReadCookie( 'XOLOGGERVIEW' ) );
</script>

EOT;
}
