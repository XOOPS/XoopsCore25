<?php

if (defined('FOR_XOOPS_LANG_CHECKER')) {
    $mydirname = 'protector';
}
$constpref = '_MI_' . strtoupper($mydirname);

if (defined('FOR_XOOPS_LANG_CHECKER') || !defined($constpref . '_LOADED')) {
    define($constpref . '_LOADED', 1);

    // The name of this module
    define($constpref . '_NAME', 'Protector');

    // A brief description of this module
    define($constpref . '_DESC', 'This module protects your XOOPS site from various attacks like DoS, SQL Injection, and Variables contamination.');

    // Menu
    define($constpref . '_ADMININDEX', 'Protector Center');
    define($constpref . '_ADVISORY', 'Security Advisory');
    define($constpref . '_PREFIXMANAGER', 'Prefix Manager');
    define($constpref . '_ADMENU_MYBLOCKSADMIN', 'Permissions');

    // Configs
    define($constpref . '_GLOBAL_DISBL', 'Temporary disabled');
    define($constpref . '_GLOBAL_DISBLDSC', 'All protections are disabled in temporary.<br>Don\'t forget turn this off after shooting the trouble');

    define($constpref . '_DEFAULT_LANG', 'Default language');
    define($constpref . '_DEFAULT_LANGDSC', 'Specify the language set to display messages before processing common.php');

    define($constpref . '_RELIABLE_IPS', 'Reliable IPs');
    define($constpref . '_RELIABLE_IPSDSC', 'set IPs you can rely separated with | . ^ matches the head of string, $ matches the tail of string.');

    define($constpref . '_LOG_LEVEL', 'Logging level');
    define($constpref . '_LOG_LEVELDSC', '');

    define($constpref . '_BANIP_TIME0', 'Banned IP suspension time (sec)');

    define($constpref . '_LOGLEVEL0', 'none');
    define($constpref . '_LOGLEVEL15', 'Quiet');
    define($constpref . '_LOGLEVEL63', 'quiet');
    define($constpref . '_LOGLEVEL255', 'full');

    define($constpref . '_HIJACK_TOPBIT', 'Protected IP bits for the session');
    define($constpref . '_HIJACK_TOPBITDSC', 'Anti Session Hi-Jacking:<br>Default 24/56 (netmask for IPV4/IPV6). (All bits are protected)<br>When your IP is not stable, set the IP range by number of the bits.<br>(eg) If your IP can move in the range of 192.168.0.0-192.168.0.255, set 24(bit) here');
    define($constpref . '_HIJACK_DENYGP', 'Groups disallowed IP moving in a session');
    define($constpref . '_HIJACK_DENYGPDSC', 'Anti Session Hi-Jacking:<br>Select groups which is disallowed to move their IP in a session.<br>(I recommend to turn Administrator on.)');
    define($constpref . '_SAN_NULLBYTE', 'Sanitizing null-bytes');
    define($constpref . '_SAN_NULLBYTEDSC', 'The terminating character "\\0" is often used in malicious attacks.<br>a null-byte will be changed to a space.<br>(highly recommended as On)');
    define($constpref . '_DIE_NULLBYTE', 'Exit if null bytes are found');
    define($constpref . '_DIE_NULLBYTEDSC', 'The terminating character "\\0" is often used in malicious attacks.<br>(highly recommended as On)');
    define($constpref . '_DIE_BADEXT', 'Exit if bad files are uploaded');
    define($constpref . '_DIE_BADEXTDSC', 'If someone tries to upload files which have bad extensions like .php , this module exits your XOOPS.<br>If you often attach php files into B-Wiki or PukiWikiMod, turn this off.');
    define($constpref . '_CONTAMI_ACTION', 'Action if a contamination is found');
    define($constpref . '_CONTAMI_ACTIONDS', 'Select the action when someone tries to contaminate system global variables into your XOOPS.<br>(recommended option is blank screen)');
    define($constpref . '_ISOCOM_ACTION', 'Action if an isolated comment-in is found');
    define($constpref . '_ISOCOM_ACTIONDSC', 'Anti SQL Injection:<br>Select the action when an isolated "/*" is found.<br>"Sanitizing" means adding another "*/" in tail.<br>(recommended option is Sanitizing)');
    define($constpref . '_UNION_ACTION', 'Action if a UNION is found');
    define($constpref . '_UNION_ACTIONDSC', 'Anti SQL Injection:<br>Select the action when some syntax like UNION of SQL.<br>"Sanitizing" means changing "union" to "uni-on".<br>(recommended option is Sanitizing)');
    define($constpref . '_ID_INTVAL', 'Force intval to variables like id');
    define($constpref . '_ID_INTVALDSC', 'All requests named "*id" will be treated as integer.<br>This option protects you from some kind of XSS and SQL Injections.<br>Though I recommend to turn this option on, it can cause problems with some modules.');
    define($constpref . '_FILE_DOTDOT', 'Protection from Directory Traversals');
    define($constpref . '_FILE_DOTDOTDSC', 'It eliminates ".." from all requests looks like Directory Traversals');

    define($constpref . '_BF_COUNT', 'Anti Brute Force');
    define($constpref . '_BF_COUNTDSC', "Set the maximum number of times a guest is allowed to try and login within 10 minutes. If the failed attempts to login exceed this, the guest's IP address will be banned.");

    define($constpref . '_BWLIMIT_COUNT', 'Bandwidth limitation');
    define($constpref . '_BWLIMIT_COUNTDSC', 'Specify the max access to mainfile.php during watching time. This value should be 0 for normal environments which have enough CPU bandwidth. The number fewer than 10 will be ignored.');

    define($constpref . '_DOS_SKIPMODS', 'Modules out of DoS/Crawler checker');
    define($constpref . '_DOS_SKIPMODSDSC', 'set the dirnames of the modules separated with |. This option will be useful with chatting module etc.');

    define($constpref . '_DOS_EXPIRE', 'Watch time for high loadings (sec)');
    define($constpref . '_DOS_EXPIREDSC', 'This value specifies the watch time for high-frequent reloading (F5 attack) and high loading crawlers.');

    define($constpref . '_DOS_F5COUNT', 'Bad counts for F5 Attack');
    define($constpref . '_DOS_F5COUNTDSC', 'Preventing from DoS attacks.<br>This value specifies the reloading counts to be considered as a malicious attack.');
    define($constpref . '_DOS_F5ACTION', 'Action against F5 Attack');

    define($constpref . '_DOS_CRCOUNT', 'Bad counts for Crawlers');
    define($constpref . '_DOS_CRCOUNTDSC', 'Preventing from high loading crawlers.<br>This value specifies the access counts to be considered as a bad-manner crawler.');
    define($constpref . '_DOS_CRACTION', 'Action against high loading Crawlers');

    define($constpref . '_DOS_CRSAFE', 'Welcomed User-Agent');
    define($constpref . '_DOS_CRSAFEDSC', 'A perl regex pattern for User-Agent.<br>If it matches, the crawler is never considered as a high loading crawler.<br>eg) /(bingbot|Googlebot|Yahoo! Slurp)/i');

    define($constpref . '_OPT_NONE', 'None (only logging)');
    define($constpref . '_OPT_SAN', 'Sanitizing');
    define($constpref . '_OPT_EXIT', 'Blank Screen');
    define($constpref . '_OPT_BIP', 'Ban the IP (No limit)');
    define($constpref . '_OPT_BIPTIME0', 'Ban the IP (moratorium)');

    define($constpref . '_DOSOPT_NONE', 'None (only logging)');
    define($constpref . '_DOSOPT_SLEEP', 'Sleep');
    define($constpref . '_DOSOPT_EXIT', 'Blank Screen');
    define($constpref . '_DOSOPT_BIP', 'Ban the IP (No limit)');
    define($constpref . '_DOSOPT_BIPTIME0', 'Ban the IP (moratorium)');
    define($constpref . '_DOSOPT_HTA', 'DENY by .htaccess(Experimental)');

    define($constpref . '_BIP_EXCEPT', 'Groups never registered as Bad IP');
    define($constpref . '_BIP_EXCEPTDSC', 'A user who belongs to the group specified here will never be banned.<br>(I recommend to turn Administrator on.)');

    define($constpref . '_DISABLES', 'Disable dangerous features in XOOPS');

    define($constpref . '_DBLAYERTRAP', 'Enable DB Layer trapping anti-SQL-Injection');
    define($constpref . '_DBLAYERTRAPDSC', 'Almost SQL Injection attacks will be canceled by this feature. This feature is required a support from databasefactory. You can check it on Security Advisory page. This setting must be on. Never turn it off casually.');
    define($constpref . '_DBTRAPWOSRV', 'Never checking _SERVER for anti-SQL-Injection');
    define($constpref . '_DBTRAPWOSRVDSC', 'Some servers always enable DB Layer trapping. It causes wrong detections as SQL Injection attack. If you got such errors, turn this option on. You should know this option weakens the security of DB Layer trapping anti-SQL-Injection.');

    define($constpref . '_BIGUMBRELLA', 'enable anti-XSS (BigUmbrella)');
    define($constpref . '_BIGUMBRELLADSC', 'This protects you from almost attacks via XSS vulnerabilities. But it is not 100%');

    define($constpref . '_SPAMURI4U', 'anti-SPAM: URLs for normal users');
    define($constpref . '_SPAMURI4UDSC', 'If this number of URLs are found in POST data from users other than admin, the POST is considered as SPAM. 0 means disabling this feature.');
    define($constpref . '_SPAMURI4G', 'anti-SPAM: URLs for guests');
    define($constpref . '_SPAMURI4GDSC', 'If this number of URLs are found in POST data from guests, the POST is considered as SPAM. 0 means disabling this feature.');

    //3.40b
    define($constpref . '_ADMINHOME', 'Home');
    define($constpref . '_ADMINABOUT', 'About');
    //3.50
    define($constpref . '_STOPFORUMSPAM_ACTION', 'Stop Forum Spam');
    define($constpref . '_STOPFORUMSPAM_ACTIONDSC', 'Checks POST data against spammers registered on www.stopforumspam.com database. Requires php CURL lib.');
    // 3.60
    define($constpref . '_ADMINSTATS', 'Overview');
    define($constpref . '_BANIP_TIME0DSC', 'Suspension time in seconds for automatic IP bans');
}
