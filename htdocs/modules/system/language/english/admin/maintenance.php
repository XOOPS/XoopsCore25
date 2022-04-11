<?php
/**
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * _LANGCODE    en
 * _CHARSET     UTF-8
 */
//Nav
define('_AM_SYSTEM_MAINTENANCE_NAV_MANAGER', 'Maintenance');
define('_AM_SYSTEM_MAINTENANCE_NAV_LIST', 'All maintenance');
define('_AM_SYSTEM_MAINTENANCE_NAV_DUMP', 'Dump');
define('_AM_SYSTEM_MAINTENANCE_SESSION', 'Empty the sessions table');
define('_AM_SYSTEM_MAINTENANCE_SESSION_OK', 'Session maintenance : OK');
define('_AM_SYSTEM_MAINTENANCE_SESSION_NOTOK', 'Session maintenance : Error');
define('_AM_SYSTEM_MAINTENANCE_AVATAR', 'Purge unused custom avatars');
define('_AM_SYSTEM_MAINTENANCE_CACHE', 'Clean cache folder');
define('_AM_SYSTEM_MAINTENANCE_CACHE_OK', 'Cache maintenance : OK');
define('_AM_SYSTEM_MAINTENANCE_CACHE_NOTOK', 'Cache maintenance : Error');
define('_AM_SYSTEM_MAINTENANCE_TABLES', 'Tables maintenance');
define('_AM_SYSTEM_MAINTENANCE_TABLES_OK', 'Tables maintenance : OK');
define('_AM_SYSTEM_MAINTENANCE_TABLES_NOTOK', 'Tables maintenance : Error');
define('_AM_SYSTEM_MAINTENANCE_QUERY_DESC', 'Optimize, Check, Repair and Analyze your tables');
define('_AM_SYSTEM_MAINTENANCE_QUERY_OK', 'Maintain database : OK');
define('_AM_SYSTEM_MAINTENANCE_QUERY_NOTOK', 'Maintain database : Error');
define('_AM_SYSTEM_MAINTENANCE_CHOICE1', 'Optimize table(s)');
define('_AM_SYSTEM_MAINTENANCE_CHOICE2', 'Check table(s)');
define('_AM_SYSTEM_MAINTENANCE_CHOICE3', 'Repair table(s)');
define('_AM_SYSTEM_MAINTENANCE_CHOICE4', 'Analyze table(s)');
define('_AM_SYSTEM_MAINTENANCE_TABLES_DESC', 'ANALYZE TABLE analyzes and stores the key distribution for a table. During the analysis, the table is locked with a read lock.<br>
CHECK TABLE checks a table or tables for errors.<br>
OPTIMIZE TABLE to reclaim the unused space and to defragment the data file.<br>
REPAIR TABLE repairs a possibly corrupted table.');
define('_AM_SYSTEM_MAINTENANCE_RESULT', 'Result');
define('_AM_SYSTEM_MAINTENANCE_RESULT_NO_RESULT', 'Not Result');
define('_AM_SYSTEM_MAINTENANCE_RESULT_CACHE', 'Clean Cache task');
define('_AM_SYSTEM_MAINTENANCE_RESULT_SESSION', 'Clean sessions table task');
define('_AM_SYSTEM_MAINTENANCE_RESULT_QUERY', 'Database task');
define('_AM_SYSTEM_MAINTENANCE_RESULT_AVATAR', 'Purge unused avatars task');
define('_AM_SYSTEM_MAINTENANCE_ERROR_MAINTENANCE', 'No choice for maintenance');
define('_AM_SYSTEM_MAINTENANCE_TABLES1', 'Tables');
define('_AM_SYSTEM_MAINTENANCE_TABLES_OPTIMIZE', 'Optimize');
define('_AM_SYSTEM_MAINTENANCE_TABLES_CHECK', 'Check');
define('_AM_SYSTEM_MAINTENANCE_TABLES_REPAIR', 'Repair');
define('_AM_SYSTEM_MAINTENANCE_TABLES_ANALYZE', 'Analyze');
//Dump
define('_AM_SYSTEM_MAINTENANCE_DUMP', 'Dump');
define('_AM_SYSTEM_MAINTENANCE_DUMP_TABLES_OR_MODULES', 'Select tables or modules');
define('_AM_SYSTEM_MAINTENANCE_DUMP_DROP', "Add command DROP TABLE IF EXISTS 'tables' in the dump");
define('_AM_SYSTEM_MAINTENANCE_DUMP_OR', 'OR');
define('_AM_SYSTEM_MAINTENANCE_DUMP_AND', 'AND');
define('_AM_SYSTEM_MAINTENANCE_DUMP_ERROR_TABLES_OR_MODULES', 'You must select the tables or modules');
define('_AM_SYSTEM_MAINTENANCE_DUMP_NO_TABLES', 'No tables');
define('_AM_SYSTEM_MAINTENANCE_DUMP_TABLES', 'Tables');
define('_AM_SYSTEM_MAINTENANCE_DUMP_STRUCTURES', 'Structures');
define('_AM_SYSTEM_MAINTENANCE_DUMP_NB_RECORDS', 'Numbers of records');
define('_AM_SYSTEM_MAINTENANCE_DUMP_FILE_CREATED', 'File created');
define('_AM_SYSTEM_MAINTENANCE_DUMP_RESULT', 'Result');
define('_AM_SYSTEM_MAINTENANCE_DUMP_RECORDS', 'record(s)');
// Tips
define('_AM_SYSTEM_MAINTENANCE_TIPS', '<ul>
<li>You can do a simple maintenance of your XOOPS Installation: clear your cache and session table, and do maintenance of your tables</li>
</ul>');
