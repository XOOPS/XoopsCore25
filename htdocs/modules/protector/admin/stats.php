<?php

/**
 * admin/about.php
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 */

include __DIR__ . '/../../../mainfile.php';
$mydirname = basename( dirname(__DIR__) ) ;
$mydirpath = dirname(__DIR__) ;
require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

require XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname.'/admin/admin_header.php';

xoops_cp_header();

function dumpArray($array, $wrap = null)
{
    $firstTime = true;
    $string = '[';
    foreach ($array as $value) {
        $string .= (!$firstTime) ? ', ' : '';
        $firstTime = false;
        $wrap = ($wrap === null) ? ((is_int($value)) ? '' : '\'') : $wrap;
        $string .= $wrap . $value . $wrap;
    }
    $string .= ']';
    return $string;
}

$queryFormat = "SELECT `type`, '%s' as age, COUNT(*) as count FROM `" . $xoopsDB->prefix($mydirname . "_log")
    . "` WHERE `timestamp` > NOW() - INTERVAL %d SECOND GROUP BY `type`, 2 ";

$sql = '';
$sql .= sprintf($queryFormat, 'month', 30*24*60*60);
$sql .= 'UNION ALL ';
$sql .= sprintf($queryFormat, 'week', 7*24*60*60);
$sql .= 'UNION ALL ';
$sql .= sprintf($queryFormat, 'day', 24*60*60);
$sql .= 'UNION ALL ';
$sql .= sprintf($queryFormat, 'hour', 60*60);

$rawStats = array();
$rawStats['']['month'] = 0;
$rawStats['']['week'] = 0;
$rawStats['']['day'] = 0;
$rawStats['']['hour'] = 0;
$result = $xoopsDB->query($sql);
while (false !== ($row = $xoopsDB->fetchArray($result))) {
    $rawStats[$row['type']][$row['age']] = $row['count'];
}
$ages = array('month', 'week', 'day', 'hour');
$stats = [];
foreach ($rawStats as $type => $hits) {
    $stats[$type] = [];
}
ksort($stats);
$keys = array_keys($stats);
foreach ($keys as $type) {
    $count = [];
    foreach ($ages as $age) {
        $count[] = isset($rawStats[$type][$age]) ? (int)$rawStats[$type][$age] : 0;
    }
    $stats[$type] = $count;
}

$height = (count($keys) + 1) * 24;

//
// http://gionkunz.github.io/chartist-js/examples.html#example-bar-horizontal
$script = "new Chartist.Bar('.ct-chart', {\n";
$script .= '  labels: ' . dumpArray(array_keys($stats)) . ",\n";
$script .= '  series: ';
$allSets = [];
for ($i=0; $i<4; ++$i) {
    $newSet = [];
    foreach ($stats as $set) {
        $newSet[] = $set[$i] - (($i<3) ? $set[$i+1] : 0);
    }
    $allSets[] = dumpArray($newSet);
}
$series = dumpArray(array_reverse($allSets), '') . "\n";
$script .= $series;
//Xmf\Debug::dump($stats, $series);

$script .= <<<EOS
}, {
  seriesBarDistance: 10,
  reverseData: true,
  horizontalBars: true,
  stackBars: true,
  height: $height,
  axisY: {
        offset: 120
  },
  axisX: {
    position: 'start',
    labelInterpolationFnc: function(value, index) {
      return Math.round(value);
    }
  }
});
EOS;

$GLOBALS['xoTheme']->addStylesheet('modules/protector/assets/css/chartist.min.css');
$GLOBALS['xoTheme']->addScript('modules/protector/assets/js/chartist.min.js');
$GLOBALS['xoTheme']->addScript('', [], $script);
$styles =<<<EOSS
.ct-series-a .ct-bar { stroke: grey; }
.ct-series-b .ct-bar { stroke: orange; }
.ct-series-c .ct-bar { stroke: yellow; }
.ct-series-d .ct-bar { stroke: red; }
.colorkeys {
  display: inline;
  width: 20px;
  height: 20px;
  margin: 5px;
  border: 1px solid rgba(0, 0, 0, .2);
}

.color-series-a { background: grey; }
.color-series-b { background: orange; }
.color-series-c { background: yellow; }
.color-series-d { background: red; }
EOSS;
$GLOBALS['xoTheme']->addStylesheet('', [], $styles);


$moduleAdmin = \Xmf\Module\Admin::getInstance();

$moduleAdmin->displayNavigation(basename(__FILE__));

echo '<h3>' . _AM_ADMINSTATS_TITLE . '</h3>';
echo '<div class="ct-chart xct-minor-seventh"></div>';
echo '<script>'. $script .'</script>';

echo '<div class="right">'
    . '<div class="colorkeys color-series-a">&nbsp;&nbsp;&nbsp;</div><span>Last Month </span>'
    . '<div class="colorkeys color-series-b">&nbsp;&nbsp;&nbsp;</div><span>Last Week </span>'
    . '<div class="colorkeys color-series-c">&nbsp;&nbsp;&nbsp;</div><span>Last Day </span>'
    . '<div class="colorkeys color-series-d">&nbsp;&nbsp;&nbsp;</div><span>Last Hour</span>'
    . '</div>';

/*

new Chartist.Bar('.ct-chart', {
  labels: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
  series: [
    [5, 4, 3, 7, 5, 15, 8],
    [5, 4, 3, 7, 5, 10, 3],
    [3, 2, 9, 5, 4, 6, 4]
]
}, {
    seriesBarDistance: 10,
  reverseData: true,
  horizontalBars: true,
  axisY: {
        offset: 70
  }
});
*/
xoops_cp_footer();

/*
SELECT `type`, 'month', COUNT(*) FROM `wvgw_protector_log` WHERE `timestamp` < NOW() - 30*24*60*60 GROUP BY `type`, 2
UNION ALL
SELECT `type`, 'week', COUNT(*) FROM `wvgw_protector_log` WHERE `timestamp` < NOW() - 7*24*60*60 GROUP BY `type`, 2
UNION ALL
SELECT `type`, 'day', COUNT(*) FROM `wvgw_protector_log` WHERE `timestamp` < NOW() - 24*60*60 GROUP BY `type`, 2
UNION ALL
SELECT `type`, 'hour', COUNT(*) FROM `wvgw_protector_log` WHERE `timestamp` < NOW() - 60*60 GROUP BY `type`, 2
*/
