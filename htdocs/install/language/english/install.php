<?php
/**
 * Installer main english strings declaration file
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           dugris <dugris@frxoops.org>
 */
// _LANGCODE: en
// _CHARSET : UTF-8
// Translator: XOOPS Translation Team
define('SHOW_HIDE_HELP', 'Show/hide help text');
// License
//define('LICENSE_NOT_WRITEABLE', 'License file "%s" is NOT writable!');
//define('LICENSE_IS_WRITEABLE', '%s License is writable.');
// Configuration check page
define('SERVER_API', 'Server API');
define('PHP_EXTENSION', '%s extension');
define('CHAR_ENCODING', 'Character encoding');
define('XML_PARSING', 'XML parsing');
define('REQUIREMENTS', 'Requirements');
define('_PHP_VERSION', 'PHP version');
define('RECOMMENDED_SETTINGS', 'Recommended settings');
define('RECOMMENDED_EXTENSIONS', 'Recommended extensions');
define('SETTING_NAME', 'Setting name');
define('RECOMMENDED', 'Recommended');
define('CURRENT', 'Current');
define('RECOMMENDED_EXTENSIONS_MSG', 'These extensions are not required for normal use, but may be necessary to explore
    some specific features (like the multi-language or RSS support). Thus, it is recommended to have them installed.');
define('NONE', 'None');
define('SUCCESS', 'Success');
define('WARNING', 'Warning');
define('FAILED', 'Failed');
// Titles (main and pages)
define('XOOPS_INSTALL_WIZARD', 'XOOPS Installation Wizard');
define('LANGUAGE_SELECTION', 'Language selection');
define('LANGUAGE_SELECTION_TITLE', 'Select your language');        // L128
define('INTRODUCTION', 'Introduction');
define('INTRODUCTION_TITLE', 'Welcome to the XOOPS Installation Wizard');        // L0
define('CONFIGURATION_CHECK', 'Configuration check');
define('CONFIGURATION_CHECK_TITLE', 'Checking your server configuration');
define('PATHS_SETTINGS', 'Paths settings');
define('PATHS_SETTINGS_TITLE', 'Paths settings');
define('DATABASE_CONNECTION', 'Database connection');
define('DATABASE_CONNECTION_TITLE', 'Database connection');
define('DATABASE_CONFIG', 'Database configuration');
define('DATABASE_CONFIG_TITLE', 'Database configuration');
define('CONFIG_SAVE', 'Save Configuration');
define('CONFIG_SAVE_TITLE', 'Saving your system configuration');
define('TABLES_CREATION', 'Tables creation');
define('TABLES_CREATION_TITLE', 'Database tables creation');
define('INITIAL_SETTINGS', 'Initial settings');
define('INITIAL_SETTINGS_TITLE', 'Please enter your initial settings');
define('DATA_INSERTION', 'Data insertion');
define('DATA_INSERTION_TITLE', 'Saving your settings to the database');
define('WELCOME', 'Welcome');
define('WELCOME_TITLE', 'Welcome to your XOOPS site');        // L0
// Settings (labels and help text)
define('XOOPS_PATHS', 'XOOPS Physical paths');
define('XOOPS_URLS', 'Web locations');
define('XOOPS_ROOT_PATH_LABEL', 'XOOPS documents root physical path');
define('XOOPS_ROOT_PATH_HELP', 'Physical path to the XOOPS documents (served) directory WITHOUT trailing slash');
define('XOOPS_LIB_PATH_LABEL', 'XOOPS library directory');
define('XOOPS_LIB_PATH_HELP', 'Physical path to the XOOPS library directory WITHOUT trailing slash, for forward compatibility. Locate the folder out of ' . XOOPS_ROOT_PATH_LABEL . ' to make it secure.');
define('XOOPS_DATA_PATH_LABEL', 'XOOPS data files directory');
define('XOOPS_DATA_PATH_HELP', 'Physical path to the XOOPS data files (writable) directory WITHOUT trailing slash, for forward compatibility. Locate the folder out of ' . XOOPS_ROOT_PATH_LABEL . ' to make it secure.');
define('XOOPS_URL_LABEL', 'Website location (URL)'); // L56
define('XOOPS_URL_HELP', 'Main URL that will be used to access your XOOPS installation'); // L58
define('LEGEND_CONNECTION', 'Server connection');
define('LEGEND_DATABASE', 'Database'); // L51
define('DB_HOST_LABEL', 'Server hostname');    // L27
define('DB_HOST_HELP', 'Hostname of the database server. If you are unsure, <em>localhost</em> works in most cases'); // L67
define('DB_USER_LABEL', 'User name');    // L28
define('DB_USER_HELP', 'Name of the user account that will be used to connect to the database server'); // L65
define('DB_PASS_LABEL', 'Password');    // L52
define('DB_PASS_HELP', 'Password of your database user account'); // L68
define('DB_NAME_LABEL', 'Database name');    // L29
define('DB_NAME_HELP', 'The name of database on the host. The installer will attempt to create the database if not exist'); // L64
define('DB_CHARSET_LABEL', 'Database character set');
define('DB_CHARSET_HELP', 'MySQL includes character set support that enables you to store data using a variety of character sets and perform comparisons according to a variety of collations.');
define('DB_COLLATION_LABEL', 'Database collation');
define('DB_COLLATION_HELP', 'A collation is a set of rules for comparing characters in a character set.');
define('DB_PREFIX_LABEL', 'Table prefix');    // L30
define('DB_PREFIX_HELP', 'This prefix will be added to all new tables created to avoid name conflicts in the database. If you are unsure, just keep the default'); // L63
define('DB_PCONNECT_LABEL', 'Use persistent connection');    // L54
define('DB_PCONNECT_HELP', "Default is 'No'. Leave it blank if you are unsure"); // L69
define('DB_DATABASE_LABEL', 'Database');
define('LEGEND_ADMIN_ACCOUNT', 'Administrator account');
define('ADMIN_LOGIN_LABEL', 'Admin login'); // L37
define('ADMIN_EMAIL_LABEL', 'Admin e-mail'); // L38
define('ADMIN_PASS_LABEL', 'Admin password'); // L39
define('ADMIN_CONFIRMPASS_LABEL', 'Confirm password'); // L74
// Buttons
define('BUTTON_PREVIOUS', 'Previous'); // L42
define('BUTTON_NEXT', 'Continue'); // L47
// Messages
define('XOOPS_FOUND', '%s found');
define('CHECKING_PERMISSIONS', 'Checking file and directory permissions...'); // L82
define('IS_NOT_WRITABLE', '%s is NOT writable.'); // L83
define('IS_WRITABLE', '%s is writable.'); // L84
define('XOOPS_PATH_FOUND', 'Path found.');
//define('READY_CREATE_TABLES', 'No XOOPS tables were detected.<br>The installer is now ready to create the XOOPS system tables.');
define('XOOPS_TABLES_FOUND', 'The XOOPS system tables already exist in your database.'); // L131
define('XOOPS_TABLES_CREATED', 'XOOPS system tables have been created.');
//define('READY_INSERT_DATA', 'The installer is now ready to insert initial data into your database.');
//define('READY_SAVE_MAINFILE', 'The installer is now ready to save the specified settings to <em>mainfile.php</em>.');
define('SAVED_MAINFILE', 'Settings saved');
define('SAVED_MAINFILE_MSG', 'The installer has saved the specified settings to <em>mainfile.php</em> and <em>secure.php</em>.');
define('DATA_ALREADY_INSERTED', 'XOOPS data found in database.');
define('DATA_INSERTED', 'Initial data has been inserted into database.');
// %s is database name
define('DATABASE_CREATED', 'Database %s created!'); // L43
// %s is table name
define('TABLE_NOT_CREATED', 'Unable to create table %s'); // L118
define('TABLE_CREATED', 'Table %s created.'); // L45
define('ROWS_INSERTED', '%d entries inserted to table %s.'); // L119
define('ROWS_FAILED', 'Failed inserting %d entries to table %s.'); // L120
define('TABLE_ALTERED', 'Table %s updated.'); // L133
define('TABLE_NOT_ALTERED', 'Failed updating table %s.'); // L134
define('TABLE_DROPPED', 'Table %s dropped.'); // L163
define('TABLE_NOT_DROPPED', 'Failed deleting table %s.'); // L164
// Error messages
define('ERR_COULD_NOT_ACCESS', 'Could not access the specified folder. Please verify that it exists and is readable by the server.');
define('ERR_NO_XOOPS_FOUND', 'No XOOPS installation could be found in the specified folder.');
define('ERR_INVALID_EMAIL', 'Invalid Email'); // L73
define('ERR_REQUIRED', 'Information is required.'); // L41
define('ERR_PASSWORD_MATCH', 'The two passwords do not match');
define('ERR_NEED_WRITE_ACCESS', 'The server must be given write access to the following files and folders<br>(i.e. <em>chmod 775 directory_name</em> on a UNIX/LINUX server)<br>If they are not available or not created correctly, please create manually and set proper permissions.');
define('ERR_NO_DATABASE', 'Could not create database. Contact the server administrator for details.'); // L31
define('ERR_NO_DBCONNECTION', 'Could not connect to the database server.'); // L106
define('ERR_WRITING_CONSTANT', 'Failed writing constant %s.'); // L122
define('ERR_COPY_MAINFILE', 'Could not copy the distribution file to %s');
define('ERR_WRITE_MAINFILE', 'Could not write into %s. Please check the file permission and try again.');
define('ERR_READ_MAINFILE', 'Could not open %s for reading');
define('ERR_INVALID_DBCHARSET', "The charset '%s' is not supported.");
define('ERR_INVALID_DBCOLLATION', "The collation '%s' is not supported.");
define('ERR_CHARSET_NOT_SET', 'Default character set is not set for XOOPS database.');
define('_INSTALL_CHARSET', 'UTF-8');
define('SUPPORT', 'Support');
define('LOGIN', 'Authentication');
define('LOGIN_TITLE', 'Authentication');
define('USER_LOGIN', 'Administrator Login');
define('USERNAME', 'Username :');
define('PASSWORD', 'Password :');
define('ICONV_CONVERSION', 'Character set conversion');
define('ZLIB_COMPRESSION', 'Zlib Compression');
define('IMAGE_FUNCTIONS', 'Image functions');
define('IMAGE_METAS', 'Image meta data (exif)');
define('FILTER_FUNCTIONS', 'Filter functions');
define('ADMIN_EXIST', 'The administrator account already exists.');
define('CONFIG_SITE', 'Site configuration');
define('CONFIG_SITE_TITLE', 'Site configuration');
define('MODULES', 'Modules installation');
define('MODULES_TITLE', 'Modules installation');
define('THEME', 'Select theme');
define('THEME_TITLE', 'Select the default theme');
define('INSTALLED_MODULES', 'The following modules have been installed.');
define('NO_MODULES_FOUND', 'No modules found.');
define('NO_INSTALLED_MODULES', 'No module installed.');
define('THEME_NO_SCREENSHOT', 'No screenshot found');
define('IS_VALOR', ' => ');
// password message
define('PASSWORD_LABEL', 'Password strength');
define('PASSWORD_DESC', 'Password not entered');
define('PASSWORD_GENERATOR', 'Password generator');
define('PASSWORD_GENERATE', 'Generate');
define('PASSWORD_COPY', 'Copy');
define('PASSWORD_VERY_WEAK', 'Very Weak');
define('PASSWORD_WEAK', 'Weak');
define('PASSWORD_BETTER', 'Better');
define('PASSWORD_MEDIUM', 'Medium');
define('PASSWORD_STRONG', 'Strong');
define('PASSWORD_STRONGEST', 'Strongest');
//2.5.7
define('WRITTEN_LICENSE', 'Wrote XOOPS %s License Key: <strong>%s</strong>');
//2.5.8
define('CHMOD_CHGRP_REPEAT', 'Retry');
define('CHMOD_CHGRP_IGNORE', 'Use Anyway');
define('CHMOD_CHGRP_ERROR', 'Installer may not be able to write the configuration file %1$s.<p>PHP is writing files under user %2$s and group %3$s.<p>The directory %4$s/ has user %5$s and group %6$s');
//2.5.9
define("CURL_HTTP", "Client URL Library (cURL)");
define('XOOPS_COOKIE_DOMAIN_LABEL', 'Cookie Domain for the Website');
define('XOOPS_COOKIE_DOMAIN_HELP', 'Domain to set cookies. May be blank, the full host from the URL (www.example.com), or the registered domain without subdomains (example.com) to share across subdomains (www.example.com and blog.example.com.)');
define('INTL_SUPPORT', 'Internationalization functions');
define('XOOPS_SOURCE_CODE', "XOOPS on GitHub");
define('XOOPS_INSTALLING', 'Installing');
define('XOOPS_ERROR_ENCOUNTERED', 'Error');
define('XOOPS_ERROR_SEE_BELOW', 'See below for messages.');
define('MODULES_AVAILABLE', 'Available Modules');
define('INSTALL_THIS_MODULE', 'Add %s');
//2.5.11
define('ERR_COPY_CONFIG_FILE', 'Could not copy the configuration file %s');
