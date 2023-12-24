ff38a2fb Merge pull request #1404 from GregMage/update_maintenance
aa201e32 Merge pull request #1405 from mambax7/feature/delete_double_renderer_property_in_dhtmltextarea
95b71073 delete duplicate $renderer
35b5921f Update maintenance
06624e57 Merge pull request #1402 from mambax7/feature/profile_version
1a42f1d1 version update
39301d69 Merge pull request #1400 from geekwright/replace-1399
e804b27f Fix protector oninstall issue
838f2460 Merge pull request #1396 from luciorota/patch-9
f6f391f8 Merge pull request #1397 from luciorota/patch-10
ac1ee58b Merge pull request #1398 from luciorota/patch-11
222a5374 Merge pull request #1374 from mambax7/feature/add_exception_trace
4d138575 Merge pull request #1394 from mambax7/feature/check_template_variables_are_set
1a73f6fd Update image.php
da22ddd1 Update mimetypes.inc.php
29cda864 Update uploader.php
898e9cf0 Merge pull request #1390 from mambax7/feature/various_updates
39fe9456 Merge pull request #1391 from mambax7/feature/greedy_array_merge
6cac4ed4 Merge pull request #1395 from mambax7/feature/accept_token_for_valid_users_only
9e2116a8 Accept token for valid users only
651a9671 Merge pull request #1389 from luciorota/patch-7
8a26deba Merge pull request #1393 from mambax7/feature/fix_for_xoUserInfo
f055c533 fix for <{xoUserInfo
d140a0a9 checking if template variables are set
d5fa17f0 replace @ for PHP 8
1780b6fb lang_diff.txt
0a992e46 Revert "Only the first call returns the proper/expected result. Subsequent calls will return 'true'."
1fcc5944 Scrutinizer
782c1fe5 fixed wrong quotation mark
39095b8f standardization on addDeprecated()
85431a9a Unreachable statement
035b5c53 Undefined variable
a4c88a51 Undefined variable
82d9b484 Only variables can be passed by reference
7779e29f Only the first call returns the proper/expected result. Subsequent calls will return 'true'.
00c925b0 Array and string offset validity
d9689d26 making sure that it is integer
518ed62f  'array_merge(...)' is used in a loop and is a resources greedy construction.
170f4e73 Reduce repetitive calls  'count(...)' is used in a loop and is a low performing construction.
0b75010f Update image.php
702d33a7 Merge pull request #1323 from mambax7/feature/remove_error_suppression_operator
372862c0 template fixes
bf659478 types, exceptions
eafcf94c Scrutinizer
9aefe0c4 fix silly bugs, Scrutinizer
966a6f6c Adding POST to Request
d8a843b4 Merge branch 'feature/remove_error_suppression_operator' of https://github.com/mambax7/XoopsCore25 into feature/remove_error_suppression_operator
4f2f2a2f Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into feature/remove_error_suppression_operator
297f35cd cosmetics
6ec9802d Merge branch 'master' into feature/remove_error_suppression_operator
a7c8f433 Merge pull request #1388 from mambax7/feature/array_merge_resources_greedy_construction
65a4424a Merge pull request #1274 from mambax7/feature/undefined_array_key_plus_others
9d9880ee Merge pull request #1386 from CHCCD/patch-6
eb57aeb8 isset()
6ab872fc more convert |default:  to !empty() check
7f8c1d8b more convert |default:false to !empty() check
c397e75c convert |default:false to !empty() check
50986b97 isset
52b5747b  'array_merge(...)' is used in a loop and is a resources greedy construction.
8d69637a templates cleanup
1deab331 Merge branch 'master' into feature/remove_error_suppression_operator
15955ea2 Merge pull request #1239 from Andrew-Staves-Activ/notificationhandler_subscribe_success
1739e88b Merge pull request #1384 from mambax7/feature/improve_check_for_renderWhere()
b7d49979 Merge pull request #1387 from mambax7/feature/reverting_footer_and_styles.css
19ea55ce reverting footer.php
a2a8ab7e Revert "Reverting footer.php"
adb9c8d2 Reverting styles.css and footer.php
e9c1f857 Reverting footer.php
a215a768 Update media.js
7010d6f0 Merge pull request #1385 from mambax7/feature/deprecated_utf8_decode()
d3db652e more checks of the return value
bb537082 check for integer
1735da01 check for XOOPS_USE_MULTIBYTES
dc3129fe Merge pull request #1363 from mambax7/feature/deprecated_utf8_decode()
117358aa refactoring  convert_encoding() for PHP 8
7f5510ee Merge pull request #1223 from mambax7/feature/cosmetics_phpdocs
a0173904 better check for renderWhere()
f346a7d6 Fix incompatible return
dc0a9a74 The safe_mode directive was deprecated in PHP 5.3 and removed in PHP 5.4.
1854a07a Scrutinizer fixes
7e6b9646 Scrutinizer fixes
667cb83a Scrutinizer: The variable $var seems to never exist and therefore isset should always be false.
3c767e65 Scrutinizer: It seems like $value can also be of type array and array; however, parameter $value of XoopsFormRadio::__construct() does only seem to accept null|string
7e8b2f8c Scrutinizer It seems like $user->getVar('level') can also be of type array and array; however, parameter $value of XoopsFormRadio::__construct() does only seem to accept null|string, maybe add an additional type check?
dac03b8c The variable $rev_ip does not seem to be defined for all execution paths leading up to this point.
69c69d8b Scrutinizer fix The variable $rev_ip does not seem to be defined for all execution paths leading up to this point.
dbab2b50 Scrutinizer fixes: IssetNode ? $conf['isocom_action'] & 1 : 0 of type integer is incompatible with the type boolean expected by parameter $sanitize of Protector::check_sql_isolatedcommentin() IssetNode ? $conf['union_action'] & 1 : 0 of type integer is incompatible with the type boolean expected by parameter $sanitize of Protector::check_sql_union()
6aca2889 Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into feature/remove_error_suppression_operator
b93d5f46 Merge branch 'feature/remove_error_suppression_operator' of https://github.com/mambax7/XoopsCore25 into feature/remove_error_suppression_operator
1088070b Merge branch 'feature/cosmetics_phpdocs' of https://github.com/mambax7/XoopsCore25 into feature/cosmetics_phpdocs
f2a483d4 Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into feature/cosmetics_phpdocs
c3376d8e updates
07e75ae4 Merge branch 'master' into feature/cosmetics_phpdocs
5b75cbb6 Merge pull request #1342 from mambax7/feature/ENT_QUOTES_ENT_HTML5
50d3d2c2 Merge branch 'master' into feature/cosmetics_phpdocs
0b31083a updates
1dc4b399 delete files
cf22248a updates
91bdd1bb Update system_block_mainmenu.tpl
e6996020 Update system_block_mainmenu.tpl
c69b1cf3 Update system_block_mainmenu.tpl
49381f0e Update system_block_mainmenu.tpl
545fa49e Update xo_page.tpl
5803680c Update xo_metas.tpl
33f59dbb Update xo_page.tpl
fd05933e Update xo_metas.tpl
e39c552c Update system_modules.tpl
0c9b51ba Update system_header.tpl
038c9b8d Update system_header.tpl
0d28dff7 Update system_header.tpl
d6259339 Update gui.php
319ded7d Update xo_metas.tpl
76934725 Update xo_globalnav.tpl
296e1fff Merge branch 'master' into feature/undefined_array_key_plus_others
e6d921df Merge branch 'feature/undefined_array_key_plus_others' of https://github.com/mambax7/XoopsCore25 into feature/undefined_array_key_plus_others
a5b42007 synch with current changes in Core
cc0e54f9 Update compiler.xoAppUrl.php
8e5b91e7 Merge pull request #1383 from mambax7/feature/Move_MAX_FILE_SIZE_in_XoopsFormRendererBootstrap
f313b2e5 moving MAX_FILE_SIZE up see: https://github.com/XOOPS/XoopsCore25/pull/1379
bb33abbc Merge pull request #1379 from CHCCD/patch-3
f1af4e21 Merge pull request #1378 from mambax7/feature/fix_upgrade_autoincrement
6510bc3b Merge pull request #1377 from mambax7/feature/fix_preflight_regex
553387ab Merge pull request #1380 from mambax7/feature/fixhtacces_in_xbootstrap_theme
7f740a77 Scrutinizer fixes
8dee39eb Merge branch 'feature/ENT_QUOTES_ENT_HTML5' of https://github.com/mambax7/XoopsCore25 into feature/ENT_QUOTES_ENT_HTML5
e2b65e9c Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into feature/ENT_QUOTES_ENT_HTML5
4a5f9950 PHP 5.3.9 compatibility
23355694 Merge pull request #1381 from mambax7/feature/Scrutinizer_check_for_empty_array
b5f162f2 Scrutinizer check for empty array make it clear that you intend to check for an array without elements.
855c6a68 Merge pull request #1370 from mambax7/feature/fixes_xoops_website
8080dbe1 Update system_block_mainmenu.tpl
eb78729e Update system_block_mainmenu.tpl
12b2ffb2 Update system_siteclosed.tpl
5c79a1e4 Update system_siteclosed.html
c032e179 Update system_block_mainmenu.tpl
c7ddcf3a Merge branch 'master' into feature/fixes_xoops_website
8a4470e7 Merge pull request #1369 from mambax7/feature/Installation_Fixes
e3ea138a Merge pull request #1371 from mambax7/feature/fix_paths
2151cef9 Merge pull request #1368 from mambax7/feature/Undefined_array_key_in_xoopskernel
a168bb1b Update .htaccess
f4d687bf Merge pull request #1177 from mambax7/feature/Xmf_Request
0cb977af Merge pull request #1345 from mambax7/feature/css_fixes
29a93099 Merge pull request #1341 from mambax7/feature/regex_delimiter
9ef4f5bc Merge pull request #1343 from mambax7/feature/entropy
1bddc020 Merge pull request #1229 from mambax7/feature/Scrutinizer2022-4-17
80a8b51f compatibility for Apache 2.2 and 2.4
388424e7 add admin .tpl templates
e14c68c2 Update XoopsFormRendererBootstrap5.php
bd1d76ee add missing  AUTO_INCREMENT
9cd6c5a9 fix for multiple varname cases is one file
ba21ed41 regex updates for "="
5c7fdbf3 Regex adjustments, PHP8
598b76d5 Merge pull request #1372 from mambax7/feature/fix_SSL_issue
49d766ee Merge pull request #1376 from mambax7/feature/RC-3_Version
0ded4cdb changed to RC3
79178204 Merge pull request #1373 from mambax7/feature/fix_upgrade_system_confirm
fa54d5e4 Merge pull request #1375 from geekwright/preflight
2cd41d5d Smarty 3 Preflight in Upgrade
f4cff84e handleException() adds trace when calling handleError() this will help to trace the origin of the issue
3d591862 delete obsolete Smarty files
3aa1e733 fix for missing system_confirm.tpl
2cf6828a Merge pull request #1361 from mambax7/feature/jQuery_3.7.0
0fb2345e fix empty quotation marks
55293173 XOOPS_URL was hardcoded with "http://"
b161c8d8 Fixing paths in Protector
1b1b8cfb jQuery to 3.7.1  #1361
4415ddb0 fixes
a059955d placeholders in <{xoAppUrl ''}>
0b6ec490 Undefined_array_key_in_xoopskernel #1368
8beca975 standardized check for array if ($fields && \is_array($fields))
b392446f add |default:null to <{foreach
9e50b82f PHP 8.0: @ Error Suppression operator does not silent, fatal errors
ff3bc35d wrong tag
36695569 jQuery 3.7.1
bacf5a63 Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into feature/remove_error_suppression_operator
feec0020 scrutinizer
a9d30a4d cosmetics
5baad9c9 obsolete code
e7232c01 various fixes
999e99e9 Language Constants  already defined
fdf07c11 Merge pull request #1366 from GregMage/issues_1365
d64c7cf6 https://github.com/XOOPS/XoopsCore25/issues/1367
f5c71e36 Fix 1365 and php8
65a63892 consistency in dealing with deprecated in PHP 8.2 utf8_encode() and utf8_decode() something to consider? In modules, I'll be changing everything to mb_convert_encoding()
e28a3397 jQuery to 3.7.0
04e382b1 Merge pull request #1360 from geekwright/upgrade-issues
b360f7cb Merge pull request #1359 from mambax7/feature/smarty3_quotes
b3597598 Fix uncaught exception during upgrade login
c3fcd7c4 Add core language/*/user.php for login
2e9d8616 Update system_siteclosed.tpl
e0387858 Merge pull request #1358 from mambax7/feature/wizard_language
4c65a75b Update system_siteclosed.tpl
9aa9749d missing quotes
8bbe91e5 fix for {$wizard->language}/english/global.php"
d3a3061a Merge pull request #1357 from geekwright/smarty3plugins
4d560712 Adjust sanitization on exception
783e1615 Replace smarty/xoops_plugins with smarty3_plugins
9ae5b698 Merge pull request #1356 from GregMage/Fix_smarty3
4fdda5e5 fix smarty 3
6d07155a Merge pull request #1355 from geekwright/install-lang
c60d9ed8 Incorrect language checking in install tablesfill step
46781591 Merge pull request #1353 from geekwright/xoops2.5.11-RC2
c9e6f0de Release Prep
25c14c93 Merge pull request #1352 from geekwright/fatal-sec
bc0e569b Merge pull request #1351 from geekwright/dir-install-upgrade
d3e1a090 Sanitize fatal exception message
2fc62ef0 Correcting paths in upgrade
9b3019f2 Correcting paths in install
c58efa0d Merge pull request #1350 from geekwright/updatelibs
7fb707e0 Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into feature/cosmetics_phpdocs
4c65b4ce Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into feature/undefined_array_key_plus_others
f53e7162 Update postcommon_register_insert_js_check.php
985564fa Update postcommon_register_insert_js_check.php
b1207a54 Merge branch 'master' into feature/ENT_QUOTES_ENT_HTML5
88eeb3db Merge branch 'master' into feature/Xmf_Request
3e197a00 Update libraries encore
57b56127 Merge pull request #1348 from geekwright/freshen-libraries
5e285338 Merge pull request #1347 from geekwright/fileinc-install
382fd953 Merge pull request #1346 from geekwright/add-makeSet
5676894a Freshen libraries
91328f55 Fix include paths in install
69e372d3 Add makeSet() - function to simplify setting default for a missing variable - also has tab to space conversion for file
fdda595a Merge pull request #1344 from mambax7/feature/Untrusted_files_inclusion
a50d23b0 Fixes to bugs reported by Geekwright
19e9db61 Mismatched parameters
ef831759 more updates
59238ec6 Updates with https://github.com/postcss/ normalize.css updated to 8.0.1
14802a79 CSS and template issues
8fa38b60 Untrusted files inclusion, not guaranteed to load the right file, using __DIR__
56186278 Insufficient Entropy Vulnerability with 'uniqid(...)' usage
46214959 Insufficient entropy for security
1c2bc424 Single quotes handling is not specified
b05b2deb Single quotes handling is not specified
ce3a9f26 Proper preg_quote(...) usage
23a4c92b Merge pull request #1324 from mambax7/feature/empty_check_in_tpl
d3512a4a Merge pull request #1336 from mambax7/feature/PHP_8.2_Dynamic_Properties_deprecated
90164377 Merge pull request #1338 from mambax7/feature/int_return_xoops_getUserTimestamp
4521f4e7 Merge pull request #1339 from mambax7/feature/string_in_rawurlencode
a5f8e4d2 Merge pull request #1331 from ggoffy/master
6a2e155c Merge pull request #1325 from GregMage/compatibility_smarty3
80e75ef4 rawurlencode requires string
8dd14d53 Merge pull request #1337 from mambax7/feature/lang_diff.txt_updates
f627497f xoops_getUserTimestamp has to return integer
8160fcd2 updating lang_diff.txt https://github.com/XOOPS/XoopsCore25/issues/1321
e4c84c22 PHP 8.2: Dynamic Properties are deprecated
f1ae957c Merge pull request #1334 from mambax7/feature/system_module_template_quotations
a7f0f82f Merge pull request #1332 from mambax7/feature/missing_paypal_image_in_renderAbout
bebbb3eb reversing wrong changes
7d987d3b more updates
fa08fc50 added a warning message for obsolete smarty functions.
372a43bb System_module.tpl wrong quotations
64e168c3 https://github.com/XOOPS/XoopsCore25/issues/1330
61bb461b removed xm-modules
bd3cea9a removed temporary theme
843ce4ff fixed smarty default errors for module extcal
bb482454 Merge pull request #1328 from mambax7/feature/issue1327
ce84bf63 https://github.com/XOOPS/XoopsCore25/issues/1327
9c195820 Update jquery.php
5276d582 Merge pull request #1326 from geekwright/xoops2.5.11-RC1
b71ce5a0 2.5.11 RC1 prep
6ed5d06c get_template_vars() becomes getTemplateVars()
e8cf90d3 additional suppression operators
dd81e5f6 template_exists() becomes templateExists()
ea0e6a45 clear_compiled_tpl() becomes clearCompiledTemplate()
42bf50f2 is_cached() becomes isCached()
cc75dd59 clear_cache() becomes clearCache()
68698408 assign_by_ref() becomes assignByRef()
b157c8da using empty check instead of default
d6cd58b1 append_by_ref() becomes appendByRef()
8607991e PHP 8.0: @ Error Suppression operator does not silent fatal errors
18f15c38 Merge pull request #1322 from geekwright/merge_smarty3
0b7f9cdb Update libraries
b73697f8 Update build to fix yaml
154b046d Sync html templates with tpl
9c81982d Correct form action
3937251f Remove debug code
3c8c6142 Undeprecate getByModule
72f63cf2 function name error
d0335b4d smarty 3 compatibility
2fe2f8ec fix installation and update of the modules with smarty3
906a819a Remove XOOPS specific foreachq and includeq compiler plugins
3501d8be Remove Smarty2
6e628dd7 Update xswatch4 to use Smarty3 configuration files
a27a4673 Update XoopsTpl to Smarty3
80395236 Updated templates for Smarty3
cd3c3c17 Update custom plugins for Smarty3
eafcdba6 Add Smarty3
dfef2f3f Quote smarty custom plugin arguments
5f92b434 Convert and cleanup Smarty securityToken plugin
c4822938 Replace XOOPS specific block function plugin with xoBlock
ed417b8e Update to a base-requires with Smarty
c6d7cb68 Remove all Smarty php tags from core
df16df67 Merge pull request #1320 from mambax7/feature/wrong_parenthesis_in_strlen()
30c37dfd fix for a wrong parenthesis
7b2e5679 Merge pull request #1319 from mambax7/feature/Break_in_Install_#1318
6f691904 fix for Break in Install #1318
45798d28 Merge pull request #1317 from GregMage/issue_1315
2516721e fix: https://github.com/XOOPS/XoopsCore25/issues/1315
9c86bf36 Merge pull request #1314 from mambax7/feature/isResultSet_3
c06ec777 reverse isResultSet() changes to XMF
f6781180 Merge branch 'master' into feature/Scrutinizer2022-4-17
972c7a39 Merge pull request #1301 from mambax7/feature/isResultSet_2
f48505bd Merge branch 'master' into feature/isResultSet_2
b0e35c18 Merge pull request #1237 from mambax7/feature/Unsupported_string_offset_operations
d24c0680 Merge pull request #1238 from mambax7/feature/Php82_dynamic_fields
345c4fe6 removing commented code
50fd2465 Merge branch 'master' into feature/Php82_dynamic_fields
e86531db Update visibility.php
4628aa44 Update profile.php
598208f7 Merge pull request #1295 from GregMage/issue_1293
51088d1c Merge pull request #1311 from GregMage/fix_setcookie
398ffa8f upgrade adjustmetns
2e741da1 add _DB_QUERY_ERROR, replace trigger with exception
4618da53 Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into feature/isResultSet_2
21029aae Merge pull request #1313 from mambax7/feature/missing_pm_fields
863d228c remove duplicate
0073ce6e add missing fields in PM
fa6cdfb8 fix: setcookie(): Passing null to parameter #2 ($value) of type string is deprecated
7462f120 Merge pull request #1310 from GregMage/issue_1299
40ae68f5 Fix: https://github.com/XOOPS/XoopsCore25/issues/1299
23be56fe Merge pull request #1309 from GregMage/Fix_help
6d4a496d Fix: Error in help.php
604d4f2d Merge pull request #1308 from GregMage/update_lang_diff
81df4ce8 update docs/lang_diff.txt
17ac76f7 Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into feature/isResultSet_2
aa513854 Merge pull request #1212 from mambax7/feature/typos
43d9e61c Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into feature/typos
011e6f26 fixing conflicts
c211fdb7 Merge pull request #1287 from GregMage/byRef
4c257071 Merge pull request #1288 from mambax7/feature/xoopscaptcha_arrays
c60bd592 Merge pull request #1292 from GregMage/Fix_preg_split
3a64988f Merge pull request #1302 from alain01/master
aeb14a17 Merge pull request #1300 from mambax7/feature/jquery3.6.3
8c049ca9 Merge pull request #1245 from Andrew-Staves-Activ/1243_user_info_buttons
f3fdb1a0 Merge pull request #1244 from Andrew-Staves-Activ/only_notify_activated_users
ba37fff2 Merge pull request #1241 from Andrew-Staves-Activ/pronouns_typos_and_wording
c926d997 Merge pull request #1240 from Andrew-Staves-Activ/notification_constants_in_user
644b1c72 Merge pull request #1306 from GregMage/issue_1304
1566aea5 Fix: https://github.com/XOOPS/XoopsCore25/issues/1304
7a176474 Update system_block_siteinfo.tpl
d3ac9315 Update system_block_siteinfo.tpl
0674f436 Update system_block_siteinfo.tpl
3d23f267 Update system_block_siteinfo.tpl
7113b1ef  update siteinfo bloc and 1 question
4d86798f Scrutinizer
38505f69 Scrutinizer
5a2a8540 fix in template
e732005c Updates
e7dd97f9 $this->db doesn't exist
3f19bd05 The @ operator no longer silences fatal errors
97d9d6a3 template defaults
9f07e1d1 Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into feature/isResultSet_2
46e55db6 jQuery 3.6.3, jQuery UI 1.13.2
a82493a0 Merge pull request #1298 from GregMage/fix_install
67868f7c fix install table modules (version)
5be61f5d Merge pull request #1294 from GregMage/removeTplXModules
0a11213e Merge pull request #1290 from GregMage/cleanUpOldCode
13c7ac7c Merge pull request #1297 from GregMage/warnig_admin
e7b4d1fc Modification: https://github.com/XOOPS/XoopsCore25/pull/1297#issuecomment-1384354195
6122eed3 Added security with the "install" folder. Now XOOPS detects all "install" folders and displays a warning in the administration page.
de543158 fix php8.2 error
bebd854a Removal of templates from XM modules
ff011a4c Set to only send notifications to activated users.
125c8c8e fix "preg_split(): Passing null to parameter #3 ($limit) of type int is deprecated" error in php 8.1
604dd6ec update date
5c07a53b cleaning up old code, this fixes the issue here: https://github.com/XOOPS/XoopsCore25/pull/1289
c369c335 small adjustments to arrays in xoopscaptcha
b4cb440a updates
88b7cc77 fix error by ref.
2c88d537 Merge branch 'master' into feature/Scrutinizer2022-4-17
ff007f89 Update main.php
919dffd1 Update main.php
ce2a7df5 Update image.php
53f48598 Update xoopsemotions.php
05faca4f Merge pull request #1273 from mambax7/feature/Partially-supported_callable_are_deprecated
556618eb Merge pull request #1272 from mambax7/feature/install_utf8mb4
74c21161 Merge pull request #1278 from mambax7/feature/template_defaults
95972382 Merge pull request #1275 from mambax7/feature/myts_name_consistency
9ef73334 Merge branch 'master' into feature/myts_name_consistency
cd8a53cc Update main.php
6d9688d0 Merge pull request #1276 from mambax7/feature/XoopsGroupPermHandler_createPhpDoc
2b8a84c8 Merge pull request #1277 from geekwright/upgrade2511
346df66a Merge pull request #1279 from GregMage/getUnameFromId
763503c9 Merge pull request #1285 from mambax7/feature/fix_getCount()_returns_string
e236696d Merge pull request #1283 from mambax7/feature/newbb_thread_template
87c07a1f Merge pull request #1271 from GregMage/issues_1269
7896cb7f Merge pull request #1282 from geekwright/tplpathcheck
d5d6796d fix in XoopsOnlineHandler
2a1bfbf0 fix for https://github.com/XOOPS/XoopsCore25/issues/1284
8cf7f846 adding divider
30df6df6 Restrict paths to inside XOOPS_THEME_PATH
91fd644e Merge pull request #1281 from mambax7/feature/censorString2
5f08a882 remove "deprecated" for censorString()
ce269cf2 adding the link on the name as in (XoopsUserUtility::getUnameFromId).
0619353b updates
44707a57 template defaults
bffcbdd0 Fix issues in moving captcha configuration files
f3164777 wrong return
ef6ab774 $myts name consistencies, cleanup
5ceb2748 outdated Inline.php for PHP 8.2
3e04ae36 config file doesn't exist.
1e8fdb03 isset()
a454aecf English
bf57b2dc Passing null to parameter #2 ($value) of type string is deprecated
d9afbc16 Undefined array key 1, if separator not found
a534b9d4 check if file_exists() before unlink
6a69b620 isset()
6e0316da PHP 8.2 creation of dynamic property
23e90c95 Undefined array key 1 in \class\xoopsform\renderer\XoopsFormRendererLegacy.php line 223  @list($encode, $js) = $extension->encode($textarea_id);
a6e75481 PHP 8.2 Partially Supported Callables are deprecated
dc0bbe15 change utf8 to utf8mb4 in mySQL 8.0.30 utf8 as an alias for utf8mb3 character set has been removed see: https://dev.mysql.com/doc/refman/8.0/en/charset-unicode-utf8.html
c3a0360f Fix templates php 8
6a0febe3 fix https://github.com/XOOPS/XoopsCore25/issues/1269
77786938 Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into feature/isResultSet_2
631373db Merge pull request #1268 from mambax7/feature/mysqli_result_for_fetchObject()
e322ae20 Merge pull request #1267 from mambax7/feature/HTML_Purifier_4.15.0
f4107ffd Merge branch 'master' into feature/HTML_Purifier_4.15.0
db995651 Merge pull request #1262 from mambax7/feature/installation_directories_order1248b
2794ad2b Merge pull request #1258 from mambax7/feature/PHP_8.2_Dynamic_Properties_are_deprecated_2
a196374f Merge pull request #1255 from mambax7/feature/isResultSet_1
7d14d056 Merge pull request #1253 from mambax7/feature/Php82_Creation_of_dynamic_property
432e4ddb Merge pull request #1252 from mambax7/feature/Protector_Undefined_array_key_0
44d0e67b improvements, logic fixes
790644cc variable name consistency
b8c84bb4 freeRecordSet()
7ba1b3dd getRowsNum()
aaea9a88 fetchRow()
393801fc fetchArray()
2be15e6c Update mysqldatabase.php
4021b1ed mysqli_fetch_object() requires mysqli_result
aec07f98 HTML Purifier 4.15
74713eee Merge pull request #1266 from geekwright/cache-deserialize
13bf6966 Merge pull request #1265 from geekwright/fix-ts-clickable
7301d1f4 Disallow classes in unserialize
4cea0c8d Correct \MyTextSanitizer::makeClickable()
e204264e Merge pull request #1263 from geekwright/rm-xswatch
b00d443e Merge pull request #1257 from mambax7/feature/utf8mb4
22654fc5 Remove xswatch theme
2db7fca5 - change folder sorting in Installation Issue #1248 (alain01/mamba)
f959eb5a Protector isResultSet()
d2f56bb6 fix for accidently deleted line
74db3907 changes requested by Geekwright
05d2ac92 Merge pull request #1053 from ForXoops/tinymce5
2bfe454a Merge pull request #1256 from mambax7/feature/check_$_REQUEST_modules
fb9686a7 Merge pull request #1233 from mambax7/feature/jqueryUI1.13.1
b7cdd4c1 Merge pull request #1254 from mambax7/feature/jQuery_3.6.1_install
ec15ddcf Merge pull request #1250 from geekwright/issue_1247
f4f54f2d Merge pull request #1236 from mambax7/feature/remove_thadmin
05326650 Merge pull request #1251 from mambax7/master
7bb3d715 check isResultSet()
c51899ff check if $_REQUEST['modules'] is set and if it's an array
525f7624 jQuery 3.6.1
f52f979d PHP 8.2: Dynamic Properties are deprecated
ae5e1bfa Index column size too large for utf8mb4_unicode_ci
9ea15ff5 Merge branch 'tinymce5' of https://github.com/ForXoops/XoopsCore25 into tinymce5
850e9ddf Add prism library for XoopsCode plugin
c0ed6de5 Merge branch 'XOOPS:master' into tinymce5
2291f59c Add prism plugin for display code
22922ef5 PHP 8.2: Dynamic Properties are deprecated https://php.watch/versions/8.2/dynamic-properties-deprecated
45bef966 Undefined array key 0 in file protector.php
d1b410e1 update changelog.php
87399104 Add logging
ddeb982d Add phar wrapper check to protector
b71a66a9 Merge pull request #1246 from mambax7/master
a4a3c979 changelog 2.5.11 Beta 2
a138c0e7 Updated user info buttons to use recognised op values.
16bc27fa Removed unused admin system users constants.
77ce1043 Wording tweaks based on mambax7's feedback.
da31a387 Changed to use gender-neutral pronouns and fixed or adjusted some typos/wording in related lines.
c5dbf35c Used notification_constants in XoopsUser rather than hardcoding the values.
75d1a9b6 Made XoopsNotificationHandler::subscribe() return true rather than null. To indicate success.
5f22efe6 Php8.2 dynamic_fields
259b1fad Php8.2 dynamic_fields
4ecc5165 Php8.2 dynamic_fields
f32abcf9 Could provoke a PHP 7 Fatal error ([] operator not supported for strings).
2e7a215b remove /thadmin
0df721e8 jQuery UI 1.13.1
5448af65 Zero Width Unicode Character
054c4766 The expression return bool returns the type true which is incompatible with the documented return type array.
5668cfb2 The expression return $imgcat returns the type false which is incompatible with the documented return type XoopsImageCategory.
629fa5c1 The expression return false returns the type false which is incompatible with the documented return type XoopsObjectHandler.
29d8913c The expression return false returns the type false which is incompatible with the documented return type null.
afb9e0fb The expression return false returns the type false which is incompatible with the documented return type null.
a7d3590d The method getObjects() does not exist on XoopsObjectHandler.
777df663 The method loadConfig() does not exist on XoopsObjectHandler.
dca601e9 The constant _FAILGETIMG was not found.
c5df9143 The method getObjects() does not exist on XoopsObjectHandler
c9bd52d9 The expression return mysqli_affected_rows($this->conn) also could return the type string which is incompatible with the documented return type integer.
5e98a7ef The expression return @mysqli_num_rows($result) also could return the type string which is incompatible with the documented return type integer.
a91af41c The method getByDirname() does not exist on XoopsObjectHandler.
dfc2ef63 Merge pull request #1228 from geekwright/patch_1227
5d01adbb Missing files from 1227
afb16187 Merge pull request #1227 from geekwright/xmf-1-2-26
4eb2eff6 Merge pull request #1226 from mambax7/feature/imagemanage_template_default
158e615b Update to XMF 1.2.26
bc284483 formatting
7e225b30 Smarty defaults
872dcd4c Merge pull request #1224 from geekwright/fix_1179
cb7ad4f8 Merge pull request #1222 from geekwright/issue_1220
32510669 Merge pull request #1225 from mambax7/feature/folder_preg_match()
377e0c29 Unknown: preg_match(): Passing null to parameter #2 ($subject) of type string is deprecated in file /class/file/folder.php line 291 Unknown: preg_match(): Passing null to parameter #2 ($subject) of type string is deprecated in file /class/file/folder.php line 852
23e33405 Merge pull request #1221 from mambax7/feature/configitem_trim($value)
6ff2f189 Merge pull request #1219 from mambax7/feature/mysqli_report_MYSQLI_REPORT_ERROR
3c101bb3 Fix module admin breadcrumb
2c592685 Correction
745775d2 Implement new module version in system module install
94c44048 changes requested by Geekwright
c126a50c trim() expects a string, null given
d4c48874 In PHP 8.1, the default error handling is set to throw an exception It creates Fatal mysqli_sql_exception Unknown database '2511test8' file: \install\page_dbsettings.php : 62
6f365d29 cosmetics: https, single quotes, PhpDocs
106a213d Merge pull request #1217 from geekwright/protector-serial
03aebdb2 Merge pull request #1218 from geekwright/comment_post
6f104dd1 Corrections after scrutinizer scan
42a7429f Fix misplaced paren
e42aab4e Convert comment posting to use Xmf\Request
30fcf919 Review regex
cae9d580 Protector unserialize review
8f479bbe Merge pull request #1216 from geekwright/issue_1213
56bd9675 Patching Snoopy for PHP 8
85f0a8fa Removing deprecated Cookie class
9f998437 Merge pull request #1211 from mambax7/feature/php81_formelement
4c53f742 Typos
11a92327 Passing null to parameter #1 ($string) of type string is deprecated in file /class/xoopsform/formelement.php line 160
cdf69857 Merge pull request #1209 from geekwright/avatarlist
5a288cd8 Merge pull request #1210 from geekwright/some-php8.1-fixes
b4e06185 Null is not string error in Criteria
691c75c5 Null is not string error in XoopsObject
75254025 Null is not string errors in messaging programs
16f9f222 Avatar display in misc.php should respect avatar_display
e3a3e2fd Merge pull request #1206 from geekwright/smarty-other-php8.1
d9aeb1dd Merge pull request #1207 from geekwright/theme-new-version-patches
7339b903 Propagate recent default theme changes to dark and transition
ea02b839 Merge pull request #1201 from mambax7/feature/module_index_help_file
66322821 Merge pull request #1203 from mambax7/feature/implode_$block_options
5d3350aa Merge pull request #1205 from GregMage/version_varchar32
e2670413 varchar(12) to varchar(32) version
717a6748 trim() expects parameter 1 to be string, array given in file /modules/system/admin/modulesadmin/modulesadmin.php line 230
167c214e Clear PHP 8.1 error
71c71c45 Clear PHP 8.1 errors
b381be69 Fix most encountered errors under PHP8.1
e1f80069 Add a 'year' function as Smarty plugin
f58b6610 Add .tpl extension option for module_index help file #482
bdde9429 Update lostpass.php
82f33c99 Merge pull request #1200 from mambax7/feature/scrutinizer3
69fcf759 parameter $string of trim() does only accept string,
e1d804fd strpos() returns int|false
60eb0995 $tpl_id of type string is incompatible with the type integer expected by parameter $id of XoopsObjectHandler::get()
25f9e64d Merge pull request #1126 from GregMage/new-version-system
7be98cc9 Merge pull request #1199 from mambax7/feature/int_id_in_XoopsObjectHandler
6b1492a6 all modules are using 'id", and this is causing issues in PHP8.1: signatures have to be the same
9ee7b05b Merge pull request #1197 from geekwright/issues_898
a3f0dc98 Merge branch 'master' into new-version-system
e32febe4 system semvar
41b3ebc8 Cleanup pass
ebf40022 protector semvar
13d74a5b profile semver
db262636 pm semver
8d1a1923 Mini stats Smarty plugin xoStats
0f82a575 Merge pull request #1195 from mambax7/feature/scrutinizer2
bd255ace The method getCount() does not exist on XoopsObjectHandler (line 182)
92899082 The method getCount() does not exist on XoopsObjectHandler (line 56)
2edfb1f2 typo
7e601013 The condition null === $format is always false.
7ee94e50 xoops_loadLanguage() accepts only strings
eaae8952 add /** @var \XoopsImageHandler $image_handler */
791d48f3 Merge pull request #1194 from geekwright/issue_1111
08e6c9c9 Merge pull request #1193 from geekwright/issue_1091
56e56e1d Null to string issue in xoAppUrl
c0213a0b Make sure \XoopsModule::$adminmenu is an array
a0452af6 Always initialize \XoopsModule::$adminmenu
810cc45f Merge pull request #1192 from geekwright/issue_809
e7584301 Remove reference to map file
ef58ce9c Fix duplicate id errors from com_mode and com_order
79f6886d Merge pull request #1191 from geekwright/issue_811
4e580160 Remove id="not_list[]" from templates
e056d22a Merge pull request #1190 from geekwright/issue_1085
ae82a410 Respect method arguments $start and $limit
6d692304 Use string not object
aab89c10 Cleanup in \XoopsSecurity::checkBadips()
186959a3 Merge pull request #1189 from geekwright/issue_1118
5b31c213 Merge pull request #1165 from mambax7/feature/strict-types-for-initvar
d87ddd91 Passing null to parameter of type string
abd998cb Merge pull request #1188 from geekwright/issue_1099
0b09df93 Add missing \XoopsConfigOptionHandler::getCount() method
fcd029c8 Merge pull request #1187 from geekwright/issue_1092
c57ee0b7 Merge pull request #1186 from geekwright/issue_1115
5f7c9799 Merge pull request #1185 from geekwright/issue_1151
34e2370f XoopsNotification::getByItemId() uses wrong column info
98642054 Fix Passing null to parameter ... of type string errors
00de57df Fix issues with Bad IP list in Protector Center
d3116543 Merge pull request #1120 from GregMage/formcaptcha_Option2
14a6af80 Merge pull request #1183 from geekwright/issue_1105
08525f7d Merge pull request #1182 from geekwright/issue_1101
44caa8d7 Fix return value in deprecated XoopsBlock::store()
15027920 Fix xoops_getHandler() calls flagged by corrected dockblock
12ab1840 Correct docblock for getHandler and getModuleHandler
ba797e16 Merge pull request #1181 from geekwright/sec1
262b1057 Remove unused composer files from phpmailer directory
2d3afbe6 Merge pull request #1179 from geekwright/alt_1133_1
48dc45dc Merge pull request #1180 from XOOPS/revert-1175-feature/admin_default_theme_tablesorter
7e4cae7b Revert "adding sorting to default theme"
0d21d755 Merge pull request #1175 from mambax7/feature/admin_default_theme_tablesorter
042c8dd8 Merge pull request #1176 from geekwright/maint_eol
1422a799 Merge pull request #1178 from geekwright/alt_1133_3
b18879a9 Fix bug issue #1114
12bf3ec5 Fix non-conforming id elements in module menu tabs
756dcc66 Scrutinizer fixes
1df30d1c conversion to Xmf\Request
36700d6d Add trailing end of line
acc35814 Merge pull request #1174 from mambax7/feature/Request
280fdd56 Merge pull request #1172 from geekwright/fix-clickable
212e096c adding sorting to default theme
638189d4 refactor XoopsRequest to Xmf\Request
a3fdef6f Less agressive \MyTextSanitizer::makeClickable()
36b2e2f7 Merge pull request #1168 from mambax7/feature/foreachq2
839540b2 Merge pull request #1171 from geekwright/ggoffy_1050
58e18e45 Add explicit orientation declaration in XoopsFormElementTray
6d470985 Merge branch 'master' of https://github.com/ggoffy/XoopsCore25 into ggoffy_1050
f8b9470e Merge pull request #1169 from geekwright/protector_php53_patch
c64d4bb1 Merge pull request #1170 from mambax7/feature/delete_xcache.php
288c9f20 delete xcache.php
afc7f82e Skip prettified protector log if not supported
e05291a5 replacing includeq and foreachq
3c9efb1c PhpDoc change to mixed
f7d05c7d Merge pull request #1055 from mambax7/feature/add_link_to_image_in_activated_module
4c8b8a84 Merge pull request #1123 from GregMage/add_xoopsWarning
8fef24c4 Merge pull request #1161 from alain01/xswatch4-works
433c6a85 Merge pull request #1164 from mambax7/feature/html_purifier4.14
5272c12b HTML Purifier 4.14.0
7dd92461 fixed type error
7609261e Merge pull request #1162 from alain01/XS4-fix-base-css-alain
e7267f7d xoops.css url "/image/"
0b6d38a1 Update theme.tpl
3c6c243b Merge pull request #1160 from mambax7/master
d4de79cc Merge pull request #12 from DejaDingo/XS4-fix-base-css
e39da1a0 Update xoops.css
649bc688 Merge pull request #1159 from mambax7/feature/next_in_system_search.tpl
c7f19f8a Undefined constant "next" https://github.com/XOOPS/XoopsCore25/issues/1158
1a8374cd Merge pull request #1130 from mambax7/feature/Smarty_v2.6.32
b7ba5bb8 Merge pull request #1153 from geekwright/mamba-1131
1c0390a8 Merge pull request #1152 from mambax7/feature/module_version_in_modname
3abfedb1 Fix doc block
03c2889d Review filter and escape in findusers
b7d65f96 Add version number to module name in toolbar, similar to what we have in the Default theme
5909f87a added transparent images
84afe6a8 Merge pull request #1124 from alain01/bt4-search-v2
a6174bb1 Merge pull request #1127 from alain01/xswatch4-bt4.6.1
a8d11171 Merge pull request #1146 from GregMage/issue_814
b829fb85 Merge pull request #1147 from GregMage/issue_1041
3edc11ef Merge pull request #1135 from mambax7/feature/php81_xbootstrap
1b581484 Merge pull request #1150 from GregMage/update_xmtemplates
8da0061b Merge pull request #1148 from GregMage/issue_1137
f3a42faa Merge pull request #1149 from GregMage/issue_1129
01b96517 Elimination of version_compare to keep the code cleaner.
b40ac5fb Updated XM module templates
9f20eb16 add missing argument
c2590522 Change mcrypt_create_iv() to random_bytes()
519636f9 The index.php file is deleted and created after a cache delete operation
fee9439e removing extra "|"
9e3d3a02 Link for 'This Module Admin' after module install
f47816c2 Copy of branch 'feature/request_findusers' of https://github.com/mambax7/XoopsCore25
2cc4062c Merge pull request #1141 from geekwright/issue_1117
a17c5a2f Merge pull request #1142 from GregMage/issues_810
08887e09 Merge pull request #1145 from GregMage/issue_1144
613f8956 fix: https://github.com/XOOPS/XoopsCore25/issues/1144
73f35b54 Change define in the system module
65cb39e0 Add \XoopsDatabase::isResultSet()
ef22b234 Merge pull request #1125 from GregMage/origin-of-a-block
2dff9fe5 Merge pull request #1140 from geekwright/issue_1138
d1d9ef79 Remove Google Swiffy references
d817230b Update lx_rss.tpl
8761bfbc Update lexikon_rss.tpl
0965e249 https
abbd39e6 PHP8.0 Smarty defaults
495eb24f double quotes
18ca4642 Update SECURITY.md
96677fb0 Create SECURITY.md
ed212c41 request_findusers
a9297baf Smarty v2.6.32
e61ed8de Bootstrap V4.6.0 to 4.6.1 for xSwatch4
c62ab652 Adding the script for updating the database
f670daae The version was not the one registered in the database but that of xoopversion.php. It is not logical. Now the information is from the database.
8df601ae cosmetic
e121e626 Update XMF and moduladmin for new version system for modules
4454d522 new version system for modules
8b64426c add dirname in block title
3b7b1050 bt4 search for xswatch4
09b31721 add warning message in admin
e213c105 Merge pull request #1102 from geekwright/issue_1096
e7a6e41a Fix: https://github.com/XOOPS/XoopsCore25/issues/1119
42c48306 Merge pull request #1107 from geekwright/issue_1106
91ae3478 Add default for xo_sys_breadcrumb
0ece5720 Merge pull request #1104 from GregMage/xoops_result
50f0d693 add admin.css
75080fe8 Display warning if xml extension not available
4be089e0 Merge pull request #1097 from geekwright/issue_1095
0c12d1bf Merge pull request #1100 from alain01/xswatch4-options
450fc03a Update theme.tpl
bd1e93ef dark and light/unique css customisation files
710461d4 Customize css definitions in css/my_xoops.css
24e003fb check Bootstrap grid
5e89ec38 Replace hardcoded english text in protector stats graph
631fa3cc Merge pull request #1094 from alain01/cosmetic-xswatch4
f3e195a4 Cosmetic changes in theme.tpl - xswatch4 theme
b9277954 Merge pull request #1075 from geekwright/issue_1074
fbe95712 Check for correct object type
5e1018c4 Merge pull request #1073 from geekwright/issue_1045
13d1ff30 Merge pull request #1072 from geekwright/issue_1047
8d924b01 Merge pull request #1071 from geekwright/issue_1052
797ef2b3 Ensure variable is defined
84c10d7c Force array for groups XoopsMemberHandler::getUserCountByGroupLink()
d96e4520 Return of ints in XoopsModelStats::getCount() and getCounts()
9c16040c Merge pull request #1070 from geekwright/issue_1066
e17d7cfb Merge pull request #1069 from geekwright/issue_1064
42533d60 Merge pull request #1067 from geekwright/issue_1065
9c7c2be9 Merge pull request #1068 from mambax7/feature/tablesorter_missed_file
6fb1e6d3 Fix incorrect name for XoopsConfigCategory object variable
7630ca75 missed file
67f48609 Fix incorrect variable for GroupMembership object
cab8f326 Ensure $ret is defined
7c4f398e Merge pull request #1062 from mambax7/feature/tablesorter_theme_widgets
eb53522c Merge pull request #1063 from alain01/xswatch4-v46
15f9db55 Missed js file
943d9e6a Boostrap V4.5.2 to 4.6.0
4901aaf9 Tablesorter CSS files
2d2277f4 Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into feature/tablesorter_theme_widgets
40bfdac8 Merge pull request #1058 from mambax7/feature/replace_foreachq
784bf522 Merge pull request #1059 from mambax7/feature/replace_includeq
54526da5 tablesorter widgets and blue theme
a73ef34f replace includeq with include in Smarty
c9e377e0 replace foreachq with foreach in Smarty templates
0d5b0de5 add link to image of an activated module This is to make it consistent with module installation
a209da67 Merge pull request #1054 from mambax7/feature/global
ae7f16ac updated lang_diff.txt
9e0cb98a no message
b2b68997 additional global language variables
f8753e1c Update Mage version to the lastest version 5.8.2 Add Xoops Emoticons plugins Add Xoops Image Manager plugin
19578c4c hack for proper displaying of fom element trays is bootstrap 4 and 5
668b213f Merge pull request #1049 from ggoffy/master
f795249e calendar div needs z-index in order to be shown on highest level
6e5125fa Merge pull request #1048 from ggoffy/master
89d6e193 label content should have also id (for 'label_for')
c87576de Merge pull request #1046 from mambax7/feature/PHP8_templates
7b93a35b adding defaults for PHP8
12fada93 Merge pull request #1043 from geekwright/xswatch4update
2e68609a Merge pull request #1042 from geekwright/issue1040
d24cee49 Merge pull request #1044 from ggoffy/master
794f2dc2 new icon
35c684f7 Dim form inputs
ab433c0c Fix incorrect path names
77e64a91 Numerous display issues, reverting to default rendered form
eff1c3c9 Forced pixel width wreaks havoc in timezone field display
5c09f8bb Misplaced {\if}
ba96069d Merge pull request #1039 from geekwright/bs5-form-loader
b030a2a1 Add xoopsformrendererbootstrap5 to XoopsLoad
217e4f95 Merge pull request #1038 from alain01/bt5
2cd35748 Create XoopsFormRendererBootstrap5.php
f698cc6d Merge pull request #1037 from geekwright/xmf-1-2-25
a2b99341 Update xoops/xmf v1.2.25
896231d6 Merge pull request #1035 from geekwright/more-dark-admin
cc296b84 Update less, css
49cb28fe Remove unused scripts
e27fc81e Override templates
ff35af3e Missing quotes on title attributes
06474622 Add id to xoops news panel
0abd1b4b Add override template for system_users.tpl
64e6351f Remove styleswitch.js
ace8cb0f Lowercase LESS directory
14ea105a Merge pull request #1033 from mambax7/feature/PHP8_newbb_xswatch4
41c605c3 PHP8 newbb template update
14bff2c7 Merge pull request #1032 from geekwright/dark-admin-rework
81388f26 Remove unused artifacts
28723b83 retesting revealed fresh fixes
6405aa3d rebuild
7b61ecdc rebuild
86ca54c6 Merge pull request #1031 from geekwright/dark-admin-update
7f2d1623 Remove dead code from original hack
e3ed8dc9 Tame bright white ui dialogs
36852997 Help panel readability
cefabc82 better antialiased reload icon
231d84e2 dark admin theme update
d3a6a588 Merge pull request #1030 from GregMage/fix_php8
22ea05ac Merge pull request #1029 from geekwright/profile-cleanup
5821bff1 Fix php8
82775041 Handle the timezone field properly
e9e4a16e Merge pull request #1028 from geekwright/php8.1-mysqli
ba03dac9 Do not assign permissions for old messenger fields
873995bb Exit after sending location header
cf1837a4 Explicitly set default mysqli_report(MYSQLI_REPORT_OFF)
ccfa1e85 Merge pull request #1027 from geekwright/avatartpl
07d28d45 Supply default value for variable
3d26d22e Merge pull request #1026 from geekwright/setcookie
a0ccf0dd Tweak php session cookie handling
37dbdb66 Update bootstrap and jquery assets
ad4200ec Set appropriate options for install session cookie PHP 7.3+
c63c41ab Conform name to current interface definition
12dba5d7 Small patches
082960d1 Changes on testing fresh install on PHP 5.3
6350291a Updates for session cookie handling in PHP 7.3+
0e142c4a Replace cookie storage with session
7feab0ed Use xoops_setcookie instead of setcookie
0d762292 Add xoops_setcookie() function
228ab64f Add samesite to javascript document.cookie uses
aac67aaa Merge pull request #1024 from geekwright/upgrade-dirscan
d6b1a077 Skip scan if directory is not present
82782dfd Merge pull request #1023 from alain01/3-Bootstrap4-templates-(from-misc.php)
926217f5 Merge pull request #1022 from GregMage/template_xmdoc-1.2
fd15f129 Update system_misc_avatars.tpl
24ea9669 Bootstrap4 templates from misc.php
90ce7036 update template for xmdoc V1.2 final
bfb8939e Merge pull request #1021 from mambax7/feature/fix_for__XoopsBlock
8ba82a77 fix for function.block.php
10def74b Merge branch 'master' of https://github.com/XOOPS/XoopsCore25
4b94e1d6 reset
cf63f23f Merge pull request #1019 from mambax7/feature/class_xoopsblock.php
11db8681 Update function.block.php
d75239ac Merge pull request #1020 from mambax7/feature/$results_check
77ffcd05 Merge pull request #1018 from mambax7/feature/THEME_FORUM_REPLY
6bbd702f check for $result
3d7fd050 fix path to new XoopsBlock
86b9900e THEME_FORUM_REPLY it will be defined in NewBB for Default theme
797e84bb Merge pull request #1017 from mambax7/feature/$modules_default
cbd8c713 $modules|default:null
03fb2727 Merge pull request #1016 from mambax7/feature/$xoops_language_default
4e8a1a45 default for $xoops_language
00ff7342 Merge pull request #1015 from alain01/some-bt4-templates
2c15ad0e Bootstrap 4 online templates for xswatch4
5f3a003d Merge pull request #1014 from geekwright/dark-form-entry-fields
510e72d1 Merge pull request #1013 from geekwright/smilies-display
572a3c13 Merge pull request #1012 from geekwright/issue1009
453a9dbd Merge pull request #1010 from geekwright/dark-admin
743c425b Some adjustments based on review
0abf380e Darken form entry fields
0d984ab8 Respect smilies table display column
e284c5f4 Add uploads/files directory to writable list
0f013e53 Prototype admin dark mode theme
c4632984 Merge pull request #1008 from geekwright/rework1005
a44c4c71 Delete phantom anonymous entry in online left when user logs in
6ca8e435 Merge pull request #1007 from XOOPS/revert-1005-bug_online
b2658994 Revert "Fix bug with bloc on-line."
6149149a Merge pull request #1006 from geekwright/themeini
df4d8783 Merge pull request #1005 from GregMage/bug_online
a3b1e0de Fix: same IP
8c3185b7 Show theme description in Select Theme install dialog
8994ea58 Fix bug with bloc on-line. If a user comes to the xoops site, he is registered as anonymous. The user logs in and a new entry is added to the database. The problem is that this person appears as anonymous and members at the same time (just look at the ip addresses to see that they are the same). This patch is used to update the status of a user who connects, it is no longer counted as anonymous.
b3b4c350 Merge pull request #1004 from geekwright/cleanvarmagicq
5077667f Merge pull request #1003 from geekwright/onemoredarkmode
044ea623 Remove FILTER_SANITIZE_MAGIC_QUOTES in system_CleanVars()
493a99db New screenshot and thumbnails
78f1313f Add dark mode support to system_siteclosed.tpl override
34da9301 Remove FILTER_SANITIZE_MAGIC_QUOTES in system_CleanVars()
a4f82372 Merge pull request #1001 from geekwright/build-php5-3-safe
ff8d263d Library updates
298e2499 Short array syntax regressions
c691482e Bump minimum recommended PHP to 7.3
29607143 Merge pull request #1000 from alain01/test-xswatch4-dark
f9f68039 Update theme.tpl
1d5f2913 Introduce prefers-color-scheme media queries
af3dbd6b Merge pull request #998 from geekwright/issue996
d581b0d2 Add templates to misc.php
17bb7379 Merge pull request #995 from GregMage/newbb_templates_post_responsive
7c7ef541 now the form is responsive
8eb3283c Merge pull request #994 from GregMage/Deprecated-Waiting-Contents
5d635d04 Block 'Waiting Contents' is deprecated
a640dfc7 Merge pull request #993 from geekwright/byeindexhtml
d2206e93 Remove redundant index.html files during upgrade
9a8a274c Validate directory using index.php instead of index.html
e7cdebc0 Stop check on index.html
667b61a1 Change xoops_write_index_file to use index.php
c39b1bf9 Merge pull request #992 from geekwright/protectorpagenav
814f6ee3 Fix pagenav in protector center
616839ea Merge pull request #988 from geekwright/writecontext
68701c4f Merge pull request #990 from geekwright/performantusers
c39a1424 Merge pull request #991 from geekwright/cpcompileid
45439fe7 Merge pull request #980 from GregMage/Template_pageNav
4af1d025 Cpanel aware smarty compile id's
144dc6a4 Cannot use method return in write context error
f9c2a750 Merge pull request #987 from GregMage/issue986
913a1898 Fix: https://github.com/XOOPS/XoopsCore25/issues/986 Comment text is now required
8f391a54 remove .html
2bbb6b2b add templates admin system_pagenav.tpl
736a646f Improve performance of admin users page
9e0f8558 Merge pull request #985 from geekwright/issue984
d1c082cc Incorrect template specified
45a1ec5c Merge pull request #981 from mambax7/feature/float_version
c0619936 Merge pull request #982 from geekwright/issue974
2e35958d Allow for override of any template in admin themes
03df58e2 * fix for version number as string some modules have it like $modversion['version'] = '2.5.1';
5166503f Fix css
bed6f109 fixe height
f60a9adb fix display image
1392810a add template bt3 for xswatch and xbootstrap
f36605eb add template bt4 for xswatch4
80f2e777 Add template for pageNav
975da5df Merge pull request #979 from GregMage/template_xmcontent
0a09666b Update templates for xmcontent 1.5
350de855 Merge pull request #978 from geekwright/issue977
2a111ffc Add default for $xoops_logdump
df99bb04 Merge pull request #976 from GregMage/templates_xmnews
afb6bcbf Fix https://github.com/GregMage/xmnews/issues/41
484f30a6 Merge pull request #973 from geekwright/install-var-path-err
3f0a64e1 Errors using XOOPS_VAR_PATH early in install
59081af0 Merge pull request #972 from mambax7/feature/jquery3.6.0
f956db39 jQuery 3.6.0
9b3cc35f Merge pull request #970 from geekwright/fix-composer-platform
fbef49fe Fix composer config platform php
38000a7e Merge pull request #968 from geekwright/unify-xoopsblock
a6668924 Unify XoopsBlock definition
f25d094f Merge pull request #964 from mambax7/feature/missing_array_variable
9ac146ae Merge pull request #963 from mambax7/feature/various
cc5b53ab Merge pull request #960 from mambax7/feature/missing_index.php_files
a8b4ccba missing array in 2.5.11 upgrade
58ffa81e Merge branch 'feature/various' of https://github.com/mambax7/XoopsCore25 into feature/various
0d2a2b86 reverse changes
6d34cd70 updates
519436cb Update Zipper.php
c2c56de0 remove index.php files
0399368b Merge pull request #962 from mambax7/feature/add_real_name
8aa86a7a Merge pull request #961 from alain01/testlocal
859be278 typos
1408331d Unnecessary semicolon
b78b90af Dynamic methods invocation via '::'
1db054da Order of modifiers
0424f1d5 Case mismatch in method call or class usage
30813a0a It makes it easier to recognize users by their full name
b30a86a0 Responsive blocks
73d15791 missing index.php files
836fbeb9 Merge pull request #959 from geekwright/issue934_935
164e416f Merge pull request #957 from mambax7/feature/extra_xoops_links
6f0d4f2a Merge pull request #958 from geekwright/fix-dot-html
1d86f87a Change a few remaining index.html to index.php
d3ac6670 Updates from testing and review
0e449128 extra XOOPS Links in Admin (Themes and Modules)
d5c495da Updates from testing
2729a969 Update changelog
897764dc Migrate config files to xoops_data/configs on upgrade
0398595f Install config files from .dist.php files
f0b89658 Look for config files in xoops_data/configs/captcha
3a355334 Look for config files in xoops_data/configs/textsanitizer
1b839c6a Add define for error in installing config files
313e49ed Create .dist.php textsanitizer config file versions
afac34c8 Fix naming of .dist.php files
79415359 De-HTMLize comments
3f0ba3f5 Create xoopsconfig.php.dist
ea058c9b Create .dist versions of config files
e49f1637 PHP 8 fix for issue discovered in testing
d632f906 Follow standard with index.html to index.php changes
0ef18b3a Merge pull request #956 from GregMage/menu_admin
3185ab12 Now the admin menu only displays useful links: display Go to module only if the module has a user part display block only if there are blocks display comments only if there are  comments
f5b70a10 Merge pull request #954 from geekwright/bannerints
ce9cf122 Fix failing updates to banner table
2653be5d Merge pull request #953 from geekwright/debuglevel
81728f4f Fix a few template errors
cf997c5f Wrong default for debugLevel configuration
1be2d540 Merge pull request #952 from geekwright/issue901
4480d2c5 Make $redirect_message available in system_siteclosed.tpl
293e4aa7 Merge pull request #951 from geekwright/issue914
33982f0f Add sort on step_order, fix form
ee340d1f Merge pull request #949 from geekwright/protector_configs
fc590371 Merge pull request #948 from geekwright/user_standardization
380a8d52 Protector config cache for htmlpurifier written to xoops_lib
e07319d4 Standardize user.php functions
3f8cafeb Merge pull request #946 from geekwright/profile_userinfo_tpl
65ac0fc5 Bad form in profile_userinfo.tpl
9a4e5052 Merge pull request #945 from mambax7/feature/scrutinizer
0a0ee556 updated docs/lang_diff.txt
c4a37ad3 reversing PhpDoc in image.php
a053e883 reversing PhpDoc
315c67b3 reverse self()
c6fbb22b reverse [] in list
82d07d9e reverse [] in list
dbb495ba scrutinizer2
450b922d fixes for Scrutinizer
250c54e1 PhpDoc
692dc54a fix for  "array to string conversion error" add a link to the activated module
7bb5bfa1 Merge pull request #943 from geekwright/model-read-error
06f3ee67 Recognize error return from db->query()
1c9bf8a8 Merge pull request #942 from geekwright/fix-backtrace
0588385e Update xmf
94219f26 Merge pull request #941 from geekwright/new-events
1ca26733 Add core.class.template.new event
e4e334bb Add core.include.common.auth.success event
9eba8c47 Merge pull request #940 from geekwright/xmfv1-2-22
ba645d5a Merge pull request #938 from mambax7/feature/float_version_number
2536d8eb Library refresh
2107abb1 Merge pull request #939 from geekwright/rm-phpthumb
07e43299 Remove phpthumb library
362abb1f fix for version number as string some modules have it like $modversion['version'] = '2.5.1';
91ef6cde Merge pull request #937 from mambax7/feature/simplify_read_language
82b833a5 simplify load of modinfo
e26c051a Merge pull request #933 from geekwright/refine-851
06acb7db Merge pull request #932 from geekwright/refine-850
72431071 Merge pull request #931 from geekwright/fileinfo-ext-req
c3c9ac0a Merge pull request #930 from GregMage/php8
a3f1010f Fix unclosed comment
3ae3a3e7 Add {X_SITEURL} and {X_YEAR} to site closed page
ffe56ac3 Require fileinfo PHP extension during install
66225196 fix php8 (Warning: Undefined array key "blocks" in file /xoops_data/caches/smarty_compile/ca10e9d0-system-xbootstrap-default^%%F7^F76^F76DDF41%%db%3Asystem_blocks.tpl.php line 64) With filter: Groups=unassigned
7f5d7a2d Merge pull request #928 from alain01/bad-blank-links
1feb1ed7 Update system_images.html
7bdf3cca Merge pull request #929 from geekwright/replace_927
29444246 Mark deleted PM as read
6c9fbc8c Update system_images.tpl
b79a6e0e Merge pull request #926 from mambax7/feature/newbbPHP8_xbootstrap
3ee0a4c9 newbbPHP8_xbootstrap
b780fb5e Merge pull request #924 from geekwright/dhtmlrender
6da707ea Merge pull request #868 from alain01/master
5ead2481 Fix issues in DHTMLTextArea support
5b312936 Merge pull request #922 from alain01/Disabled-flash
5ae4f17a Update config.php
5767ba4b Merge pull request #920 from GregMage/X_NAME
3c2e0171 update lang
9edbd7e2 add {X_NAME} tag for mailusers
b7632e4d Merge pull request #917 from geekwright/906errata
fd457f4f Diff for 906 update
8f70c78e Merge pull request #916 from GregMage/captcha_profile
d4a19b29 Merge pull request #912 from mambax7/feature/php8_foreach
6e46e663 Merge pull request #908 from GregMage/Message_profil
ec711857 Bug with captcha
7ebefdcc Merge pull request #909 from mambax7/feature/php8_templates
12815521 defaults
0ce50f08 updates
feaad60c lexikon
4898e732 Merge branch 'feature/php8_templates' of https://github.com/mambax7/XoopsCore25 into feature/php8_templates
a9008dfb updates
bbbbb0f0 Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into feature/php8_templates
25162f54 Update htdocs/themes/xbootstrap/modules/system/blocks/system_block_siteinfo.tpl
1a727ab5 Update htdocs/themes/xswatch4/modules/publisher/blocks/publisher_items_spot.tpl
9dc4f79d Update htdocs/themes/xswatch/modules/publisher/publisher_footer.tpl
1117b938 Update htdocs/themes/xswatch4/modules/pm/pm_readpmsg.tpl
8bd7c2f2 Update htdocs/themes/xswatch4/modules/publisher/blocks/publisher_search_block.tpl
0f4adf86 Update htdocs/themes/xswatch4/modules/publisher/publisher_footer.tpl
b92145dc Update htdocs/themes/xswatch4/modules/system/system_comment.tpl
453c1f04 Update htdocs/themes/xswatch4/modules/publisher/publisher_item.tpl
83c48795 Update htdocs/themes/xbootstrap/modules/publisher/blocks/publisher_search_block.tpl
a4fc37db Update htdocs/modules/system/themes/default/xotpl/xo_globalnav.tpl
db8312e9 Update htdocs/modules/system/templates/system_block_dummy.tpl
9264aed3 Update htdocs/modules/system/templates/admin/system_index.tpl
a1cb21d9 Update htdocs/modules/system/templates/admin/system_index.html
3af9f505 Merge pull request #887 from mambax7/feature/https-GnuLicense
22d87dc5 Merge pull request #911 from mambax7/feature/php8_system_blocksadmis_main
872cd4e7 Merge branch 'master' into feature/https-GnuLicense
56156a6c Merge pull request #913 from GregMage/update_xmtemplates_php8
d20b5cd3 php8 xmdoc
79943715 new template
1dfc2d43 php8
721c88d8 oubli
04dbe8df add new templates
0e56a8bd update php8
83822b80 update xmnews templates for php8
67aed492 check if array
028695a3 Update main.php
db0ba634 Countable Error: TypeError: count(): Argument #1 ($var) must be of type Countable|array, string given in file /modules/system/admin/blocksadmin/main.php line 284
48bd9248 add PHP8 defaults in templates
6de37b53 update Lang_diff
a66d4c11 define optimization
ba427de8 add description for register form (profile)
2f1365df add description for register form (xoops)
8f9a2f68 Add alert bt3 and bt4
86090f2f Merge pull request #906 from luciorota/patch-2
a8a0f99e Update preferences.php
323f62dc Merge pull request #905 from GregMage/captcha_text_css
d47a8805 Class css for captcha text
22667e56 Merge pull request #897 from mambax7/feature/xoModuleIcons16_index
3da2d2ed update for using index.php
e32405d9 Merge pull request #896 from geekwright/imgname-truncate
d9761362 Clean up image manager display
eacc9d14 Merge pull request #895 from geekwright/systembreadcrumbs
0e147a52 Fix system breadcrumbs
265c5222 Merge pull request #894 from geekwright/xdataprotector
5b09b77c Restore xoops_data/protector directory
5731cc7b Merge pull request #893 from geekwright/imageman
5dd76d96 Add default for undefined template variables
2e3c6af2 Restore some visual order in admin breadcrumb bar
e681ee5e Quiet lightBox error message
67406d87 PHP 8 issue
117450d4 Merge pull request #892 from geekwright/badconst
f49c584b Malformed constant __MINWIDTHIMAGEERROR
ee1d8ae9 Merge pull request #891 from geekwright/xdata-index
da96c74f Restore missing xoops_data/caches direcories
db41e257 Merge pull request #889 from geekwright/index-html-to-php
d23ce3b6 https
d414aa0a Exclude index.php in preload
b252bd9f replacing index.html with index.php
d6ea6524 Merge pull request #886 from XOOPS/revert-884-feature/index.html_change
6953ae05 Revert "replacing index.html with index.php"
f576b5d7 Merge pull request #885 from mambax7/feature/short_array_push_syntax_on_a_string
9d2eb7cb Merge pull request #884 from mambax7/feature/index.html_change
6ed49f44 short array push syntax on a string https://stackoverflow.com/questions/5879675/problem-with-fatal-error-operator-not-supported-for-strings-in
c9a05f36 replacing index.html with index.php
a5a81e84 Merge pull request #882 from geekwright/smartydebug
9287df91 Add author info
37ade150 Customized Smarty debug.tpl
b7151ae5 Merge pull request #881 from geekwright/bsmap4.5.2
d29a973d Add map file to quiet Chrome error
b84cd68e Merge pull request #880 from geekwright/fixavatarerrors
0a23c38b Merge pull request #879 from geekwright/rmdebug870
b3b63e89 Fix errors in user avatar templates
47b86863 Remove debugging code
dba7f238 Merge pull request #878 from geekwright/bootstrap4.5.2
cff77360 Update xswatch4 to bootswatch 4.5.2
2914d679 Merge pull request #877 from geekwright/usrprofile-php8
4ab3e3d0 Template fixes user and profile for php8
ea9daaab Merge pull request #875 from geekwright/php8-maint
03ca8633 Merge pull request #874 from geekwright/modadmindbg
64f7108e Remove debug info
81d79074 Fix template errors on php8
57638723 Use integer for XoopsFormRadioYN value
23c0422c Merge pull request #871 from geekwright/issue867
081faba7 Merge pull request #872 from geekwright/pm-templates
4dd1f117 Fix errors seen in PHP 8 in pm module templates
706b07fe Magic __wakeup() method must be public
9f0de3f6 Merge pull request #870 from geekwright/xmf1.2.20
1ea3d1d7 Library updates for XMF 1.2.20
6694814b Responsive xswatch4
8edf14e9 Merge pull request #864 from geekwright/issue848
aa0a8d5f Add all selected users in XoopsFormSelectUser
a896d5ed Merge pull request #863 from geekwright/php8fixes2
f0c89a16 Resolve addition errors in system admin templates
d15c09e5 Resolve errors in default admin theme
68a9d507 Merge branch 'php8fixes' of https://github.com/geekwright/XoopsCore25 into php8fixes2
b2d007a9 Merge pull request #862 from mambax7/feature/CaseMismatch
9303d8ec Merge pull request #859 from mambax7/feature/tablesorter-2.31.3
453cf8a0 Merge pull request #858 from mambax7/feature/jQueryForm4.3
a3dae362 Merge pull request #857 from mambax7/feature/phpmailer5.2.28
b3b7d587 Merge pull request #856 from mambax7/feature/changelog20200815
fa361830 Merge pull request #855 from mambax7/feature/HTML_Purifier_4.13.0
dd37f90a Merge pull request #854 from mambax7/feature/phpthumb_1.7.16-202008101454
79149cd4 Merge pull request #853 from mambax7/feature/smarty__StdClass
ceef7e0a Remove __autoload() from PHPMailer
050cc1a9 Resolve error in Frameworks moduleadmin
10aed319 Resolve errors in transition theme templates
62edd346 Resolve errors in system module templates
ecc9571b Resolve errors in install
e54dc1c9 Case mismatch
52c5a197 Update to tablesorter-2.31.3
1783ca1d jQuery Form 4.3
409935ed PHPMailer 5.2.28
fcc0230a Changelog updates
81f5099c HTML Purifier 4.13.0
ec024f63 latest changes from https://github.com/JamesHeinrich/phpThumb
532375e8 see: https://github.com/smarty-php/smarty/pull/544
014c1820 Merge pull request #852 from geekwright/bootswatch-yeti-patch
3947a14b Merge pull request #851 from geekwright/php8patch1
dfbe3509 Merge pull request #850 from geekwright/issue846
17872789 Merge pull request #849 from geekwright/issue847
c9155f76 Upstream patch for dropdown text contrast issue
2a4547c3 Patch a few issues when using PHP 8
545b7f37 Restore/Add copyright meta tag to Bootstrap themes
7efcf128 Add {X_YEAR} macro in 'footer' and 'meta_copyright' on install
efc50e1f Add macro substitutions for {X_SITEURL} and {X_YEAR}
4975ff9d Update help
189be925 Return proper null value for integer columns
643a0983 Merge pull request #845 from luciorota/patch-1
a0aa367e Update user.php
4c7157e3 Merge pull request #843 from GregMage/fix_countsearch
7714d994 Fix: Avertissement: count(): Parameter must be an array or an object that implements Countable dans le fichier /search.php
bd870004 Merge pull request #841 from geekwright/profilefieldopts
2257b155 Merge pull request #842 from geekwright/pm-alain01-liomj
e81dbf65 Merge pull request #839 from geekwright/newbbpagenav
fe394813 Modal Comment Options
4c6d8b5d Match *box icons sizes in pm_viewpmsg.tpl
5535bfea Incorrect responsive behavior in renderThemeForm
9043158b Eliminate smarty <{php}> tag use
aef0612d Add options text editing to profile field edit
546943f2 Template cleanups
b2e7d91e Restore confirm appearance in admin
43ab4569 Comment template tweaks
2e67844e Expose template for use by caller
47d62367 Fix default
2dd10f41 Remove light and dark css sets
06aa0c2d Rename flaty to flatly
8e5ae909 Do not depend on default shortcut icon
f0e4636c Adjust templates which are not wrapped by theme.tpl
e5edc29a Add tpl/xswatchCss.tpl and document use
d7879bc9 Add themePath variable to xoops_header()
7e1e4d4f Smarty plugin xoUserInfo
ef1898ab Avatar cleanup
df3f54e0 Remove smarty php tags
4eca4f53 Undo inadvertent class change
5b76a006 Bootstrap-Bootswatch-4.5.0
80d129ef Implement pm_pmlite.tpl
3c419296 Modify to match existing style
28c6236c Implement Xmf\Request
3e107e41 Restore pm_pmlite.tpl
cfb2327a Merge branch 'bt4-PM-templates-for-xswatch4' of https://github.com/alain01/XoopsCore25 into pm-alain01-liomj
9cd14070 Merge pull request #838 from mambax7/feature/_MEDIUMDATESTRING
6ba0cf4b Template patches for Newbb 5.0 Final
5900598b https://github.com/XOOPS/XoopsCore25/issues/837
58686fc2 Merge pull request #835 from geekwright/jquery3.5.1
a87eb519 New modern templates for PM
e3359ced  Security update jquery 3.5.1
0996b0a1 Merge pull request #834 from GregMage/update_xmmodule
ca9674df Merge pull request #824 from geekwright/phpthumb1.7.15
252b5f69 Merge pull request #829 from geekwright/issue823
f79994b0 Admin user sync
93e14109 xmcontact
e7f0b647 add xmdocmanager
e3c7165d update xmdoc
f03ae664 Merge pull request #821 from liomj/fix-profile-index-template
e577da9a Fix Profile Index Template
da3edd5b Update to official tagged phpthumb 1.7.15
03d803d9 Merge pull request #813 from mambax7/feature/convert_to_English_comments
2438c0e7 Convert to English
3b7e8391 update_xmdocs
1eb4b9b0 update xmnews
df58bdcf Merge pull request #808 from mambax7/feature/phpThumb_1.7.15-202002130926
99efd9f3 Merge pull request #804 from alain01/bt4-cosmectic-for-xmnews
262c96f6 phpThumb 1.7.15-202002130926
3518fc97 some fix
7009cc66 cosmetic bt4 modification
fc47d525 Merge pull request #803 from GregMage/update_xmmodule
b1de04f3 xmdoc remove truncatHtml
8a027220 Update xmsocial
15b610af Last update xmnews
bfc88547 Merge pull request #802 from geekwright/pmtweaks
d7b77d34 Merge pull request #801 from geekwright/bs-3.4.1
bc25032d js security update bootstrap 3.4.1
e297eaeb Match other module templates in xswatch4
df25f1d4 Add meaningful default page
5ae05884 Merge pull request #800 from geekwright/emptymodules
b0220596 Error when no modules installed
0df29ca0 Merge branch 'master' of https://github.com/XOOPS/XoopsCore25
d92e347d Merge pull request #799 from GregMage/update_xm
6d22cd3d Merge pull request #798 from alain01/cloned-xswatch4
b1bfb4fe Merge pull request #790 from mambax7/feature/xswatch_updates
fa5416a2 Merge branch 'master' of https://github.com/XOOPS/XoopsCore25
e15f6433 update xmdoc
31e4a306 update xmcontact
b34f3237 update xmnews
7a14e94e Update style.css
efe5e03f Update README.md
2e41db9a cloning of all xswatch derivatives
0c64c677 Merge pull request #796 from geekwright/issue794
648265a7 Fix docblock \XoopsNotificationHandler::triggerEvent()
5b013797 Merge pull request #795 from mambax7/feature/deprecatedSnoopyMessage
962d7dbe The message as an error was annoying  :)
cf2446ed Revert "Sourcegraph"
d5e08f52 Sourcegraph
807eda11 Merge pull request #792 from geekwright/issue791
6721a7ee Remove unreferenced templates
8dd77f55 extCal updates
08f37c25 Merge pull request #787 from geekwright/woodstock
0597406f Merge pull request #788 from GregMage/update_xm
2ba6928d Suggest XoopsHttpGet in deprecation notice
07625b69 add edit btn for xmcontent
fe2ef2df Allways schow cat xmdoc
03e88242 optimisation xmnews
4fe8000e Add check for curl_init failing
02dc8e61 Deprecate the Snoopy class
f1a594b5 Use XoopsHttpGet to fetch rss feeds
71bb9c84 Add XoopsHttpGet
5629a17d Merge pull request #786 from GregMage/update_tpl_xmnews
ce3f4e32 Update tpl for bt4 and fix error author in block
24d0648d Merge pull request #785 from geekwright/issue782
0275576c Tweak feeds used in XOOPS News in admin.php
74e1e20e Merge pull request #784 from GregMage/error_avatar
70305774 Fix:https://github.com/XOOPS/XoopsCore25/issues/783
8af021d0 Merge pull request #780 from geekwright/issue778
aa40b3bb Merge pull request #779 from alain01/master
5bb7ed83 Use mixed case constants in DHTML editor controls
7aaa640c Add missing "dropdown-item" class on list items
e0f9f691 New clean favicon.ico
92461a00 Merge pull request #777 from geekwright/newbbnotopic
87e6e601 Fix NEWBB_NOTOPIC error
d1a14f87 Merge pull request #776 from GregMage/fix_error_tpl_xmsocial
7c7a6651 fix errors with xmsocial
00d98742 Merge pull request #772 from GregMage/update_tpl_xmmodules
85cedae8 update xmdoc and xmnews
f8bac053 Merge pull request #770 from geekwright/beta1-2.5.11
38115dd6 Version 2.5.11-Beta1 prep
4f1e74c5 Merge pull request #769 from geekwright/updbs3
c502c356 Tweaks to xswatch templates
d6e91233 Tweaks to xbootstrap templates
dc2070ca add xmsocial bt4
163b43b0 Select category is alway display
77c58208 Add options
8d064773 update bt4 (blocs)
c6605aa4 Merge pull request #768 from geekwright/xmf1.2.19
b5a82896 Libraries update
257270cb Merge pull request #767 from GregMage/update_xmnews_tpl
49bb7184 add xmsocial rating for xmnews bt4
222cd43a Merge pull request #766 from geekwright/addxswatch4
c77b0453 Merge xswatch4
51b44d0a mv xswatch to new home
9999b08e Remove unneeded important
eb2f1b82 Dark mode tweaks
2248b5f3 Documentation review
e2c6e236 Tweaking tpl's for awesome xmnews
073da969 Match breadcrumb style
8b9c53f4 Fix btn-default
c8345fdd Publisher table cleanup
783a6501 Notification cleanup
dfa92aa7 PM cleanup
15fce97a Adjust image display
b8ac3c18 Remove unused assets
b714805a Readme and images
eeb5f579 Fix publisher slider for multiple images
ddda01f6 Move cookie consent to separate template
df8e12d3 Add vertical slide option for slider
84ef8e62 Merge branch 'master' of https://github.com/geekwright/xswatch4
977beab3 Merge pull request #21 from GregMage/cosmetic_xmnews
37157bfe Merge branch 'master' into cosmetic_xmnews
2cd1b1a0 Update tag templates
79892d19 Droping news and extgallery templates
1abcc4de Revert to previous version
d84426f0 update
74df20aa Cleanup pagnav and assorted php errors
bea7fc8e Collection templates
ce2d31a5 Disabled link
501032b2 Merge pull request #20 from GregMage/cosmetic_xmnews
d9ad2a7f form-control-sm
09b8145d Modification breadcrumb
75bcba49 Fix image crop in album image
c735f7a1 Fix untranslated prompt
36567ca9 As requested
7a9ad16c Add wggallery
592557e0 Tweak jumbotron
727dc513 Cleanup pass on extcal templates
58400de5 Replace foreachq with foreach
7eb34552 Restore intended configuration for navbar
8fdd4c16 Fix typo
497369dd Publisher template cleanup
bd2e6deb Merge pull request #765 from geekwright/issue764
a512430f Update class docblock
039545df Remove locale specific date format for calendar.js
bd202ebd Button and select cleanup
abb30c64 Adding defines
3ec446b0 Add classes for media object image sizing
328b4c13 Minor layout adjustment
96d9c12c Semantic buttons
4e668d37 Minor layout adjustments
8a0000e6 Remove extra # in bgcolor
ac0e01cc Add event and location templates
b0e4c8f8 Add .nav-item and directly use constants
52fb7aab Add direct navigation to debug settings
ab5311e9 Incorrect Breadcrumbs
c5bbdf6c Handle Activate Banners? = No
3c2af716 Disable slider
eff7a96a Update cookie consent js
eba86e09 Update main theme.tpl
8fda8675 Minor template updates
8c87bc47 Rework slider to use bootstrap v4.4 carousel
2bdd453a Updating tdmdownloads templates
04632545 Merge pull request #763 from geekwright/blocktitledocs
c62bd46f Typo
2bed5a6a Update Help page for blocks admin
84a0ae94 Merge pull request #762 from geekwright/blocktitle
f88a5d7a Allow comment instead of blank title
bfb31f23 Error if nothome is not defined
b776152f Update obituaries templates
dc2967f2 Merge pull request #760 from geekwright/allowfunc
dffba58f Don't backtick a function call
7100ec68 Merge pull request #17 from GregMage/xmcontact
f132d3d6 Merge pull request #18 from GregMage/update_xmcontent
ab5069fd Main contact form
a20d104b Delete old template unrelated to the module
e7214e82 Responsive behavior in subforum list
3c395943 Fix onhashchange behavior
a8cfa68c update index
108e1d62 Add xmcontact bt4
0d21ddf2 Standardize on badge-pill
3be020cb Rework xoopsfaq templates
0f92b478 Merge pull request #759 from geekwright/onejquery
da398fef Do not load jquery.js if already loaded
fef54e8f Adjust margins in footer blocks
8d31c766 Refine inboxAlert
b977571b Converting publisher templates
227eaadb PM Alert experiment
b8e2306f Style jumpbox
84ee226c Update js-cookie to v2.1.2
b4bf96c8 Match default version
b640a145 Fix duplicate element ids
b184b526 Reworking newbb templates
3e129596 Tweak theme select block
81d89a6a Fix stream of error messages
2ae18807 Merge pull request #757 from geekwright/esc20200103
3424a6cd Escaping issue
6b2c4bb9 newbb templates
31980d6a Misaligned Home menu item
9c5cfb53 Tweak responsive layout
d16128b5 Add img-fluid to logo
697b0eec Merge pull request #756 from geekwright/issue755
bee3cf35 Remove dead list type
2e935bf1 Merge pull request #16 from GregMage/update_xmnews
7c0d504b Merge pull request #15 from GregMage/xmdoc
88e4725d fix xmdoc
c8573263 update templates
095d8bd9 xmdoc
234c3600 Merge pull request #754 from geekwright/alt753
56731b51 Fix ProtectorMysqlDatabase::query() return types
ec8f3fd9 Type fixes in XoopsMySQLDatabase
d2a6aa4e Merge pull request #14 from GregMage/xmnews
01d94208 Merge pull request #10 from GregMage/profile_update
f503b473 add xmnews
98ac4645 Merge pull request #752 from mambax7/feature/include_global.php
cd8bf1e6 include global.php in template.php (PHP 7.4 complained about missing _LANGCODE and _CHARSET)
d6094809 Merge pull request #751 from geekwright/sysconfirm
8a5d653b Add system_confirm.tpl to xswatch
4b3b27b5 Match related views with btn-info
74cc6e71 Add system_confirm.tpl
ec232608 Merge pull request #750 from luciorota/master
fabfb95c Merge pull request #13 from GregMage/redirect
a55d050f change redirect with alert-primary boostrap
88e5a7d0 use smarty in xoops_confirm()
89475240 Merge pull request #748 from GregMage/XoopsFormRendererBootstrap4
2593f647 update userform
6df56d32 Merge pull request #747 from geekwright/issue741
8a3a29a5 fix calandar
b7ef3ae7 Merge pull request #7 from GregMage/profile
2756819c Merge pull request #8 from GregMage/xswatch4
7b814e48 fix search
7e59786a Profile bt4
82386f60 Skip nl2Br() if html detected in MyTextSanitizer::displayTarea()
4952743d Add visible message alert to navbar
804cb617 Update to bootstrap v4
95deb8e8 Remove includeq
1ae42d0b Merge pull request #4 from GregMage/modif_bt4
14fbdae9 Merge pull request #3 from GregMage/system
a68bdc98 Merge pull request #2 from GregMage/xmcontent
06785a4b Update visible
4456c88b bootstrap 4
cc853d57 best for responsive
ae11cef9 Merge pull request #1 from GregMage/xmcontent
30afc3b3 essais
159af48e Xmcontent
c191a033 Merge pull request #746 from geekwright/buttoninput
5140a32e Workaround for Bootstrap4 button classes
78b67e03 Add value attribute to rendered button
f1c27a2a Merge pull request #745 from geekwright/rmdebug
2c0cba75 Remove debug statement
c0aeb79e WIP bootstrap v4.4.1 conversion
ad972d49 Merge pull request #744 from geekwright/issue738
e097228d Initial commit
cef591d7 Refresh inbox count cache when using pm programs
5749dcc6 Merge pull request #743 from geekwright/bootstrap4tweaks
49af721f Smooth some rough edges in Bootstrap v4 support
56e63fa5 Merge pull request #740 from geekwright/issue739
77be984a Get description for edit
42f4867e Merge pull request #737 from geekwright/mailtweaks
4b4f1dd5 Merge pull request #736 from geekwright/issues735
28cafedd Add qmail as valid mailmethod
65d07255 Add qmail as valid mailmethod
e4fac1ff XoopsMultiMailer should call parent::__construct()
ba78f7aa Remove broken caching
62350468 Merge pull request #734 from geekwright/issue732
f151a569 Merge pull request #733 from geekwright/issue730
57c4e709 Handle special case of admin themes in checks
89219d8a Get 'sel' vars from GET explicitly
caa3d66d Merge pull request #729 from geekwright/missingtheme
5e18390f Attempt graceful failure on missing theme
28e49665 Merge pull request #728 from geekwright/libupdates20191201
8c1b1705 Library updates
534dd468 Merge pull request #727 from geekwright/proxyenv
0cc31251 Add docs and example of proxy_env
d19bed19 Merge pull request #726 from geekwright/silencemq
d440db4a Silence magic quotes
511cf34d Merge pull request #725 from geekwright/nocurliesphpthumb
94960ed9 Silence deprecation notice
67822a71 Identify XOOPS patches
517b5ce1 patch curly array access in phpthumb
cc4afccd Merge pull request #724 from geekwright/issue723
48d5d842 Simplify tooltips
4965476b Merge pull request #722 from geekwright/fabuttons
b6a8c859 Replace glyphicon based controls with font-awesome
4a28c058 Merge pull request #721 from mambax7/feature/PHPMailer_5.2.27
44a80e1d Merge pull request #720 from GregMage/fix_icon
29c67483 phpmailer to 5.2.27
70ff9243 fix https://github.com/XOOPS/XoopsCore25/issues/719
a0a34c7d Merge pull request #718 from GregMage/system_moduladmin
fee62987 Fix the errors related to this modification https://github.com/XOOPS/XoopsCore25/pull/714
bf199275 Merge pull request #717 from GregMage/New_module_page
558b80c0 Filter input
38d7a088 Merge branch 'module_page' of https://github.com/GregMage/XoopsCore25 into New_module_page
ba5d75d6 Merge pull request #712 from mambax7/feature/HTML_Purifier_4.12
bdeeab9a Merge pull request #714 from GregMage/Array_modulesadmin
e3173278 Add smarty "xoops_modulepage"
7090abcb Fix https://github.com/XOOPS/XoopsCore25/issues/713
f62c4ecf  HTML Purifier 4.12
f218a413 Merge pull request #711 from geekwright/xswatch2newbb5
425aaf88 Move xswatch templates to NewBB V5
07b4bbf2 Merge pull request #709 from geekwright/imgcattooltip
0e6cd99e Incorrect tooltip in image manager category list
a8a51764 Merge pull request #707 from geekwright/issue706
913c679c Reset Typography Controls when focus lost
3b52af19 Merge pull request #705 from GregMage/display_formdate
2a5e17bc Fix https://github.com/XOOPS/XoopsCore25/issues/704
fcea76b9 Merge pull request #702 from geekwright/maint-tweak
bf7f6d55 Merge pull request #701 from geekwright/remove-zeta
7aa91f9e Merge pull request #700 from geekwright/im-tweak
d96bcee7 Merge pull request #703 from geekwright/issue685
443f5b6a Handle negative ini sizes
a69fb1fa Use cleaned input
c1d61839 Remove zetadigme admin theme
4cd43d64 Fix issue with image_display and permissions
4accea57 Merge pull request #699 from geekwright/tooltipescape
e4c54652 Merge pull request #698 from GregMage/Vulnerability
33c92f59 system_CleanVars is deprecated since XOOPS 2.5.11
d7213a83 delete system_CleanVars users
cd3eb7d3 delete system_CleanVars tplsets
92b59b0d delete system_CleanVars preferences
79b14027 delete system_CleanVars modulesadmin
dcace5f8 delete system_CleanVars maintenance
574e5586 delete system_CleanVars mailusers
cc71af28 delete system_CleanVars groups
e8c68864 delete system_CleanVars comments
74f16a46 delete system_CleanVars blocksadmin
30ba7d14 Banners admin Securing forms
3bcca490 Banners admin Securing forms
350c3b40 Fix attribution
ebeb2564 Banners admin Securing forms
32698158 fix $op
4dcfe17d code optimization (image manager)
ac84e4be Remove text style in tooltips
bc556bb2 Escape tooltip output
1dc9e3b2 Vulnerability with image manager (admin) CVE-2019-16683 and CVE-2019-16684
8b0c9642 Merge pull request #697 from fabou78/Fix-#695
3ececd0e Fixed #695 issue with synchronize in admin->users
81e4ce4f Merge pull request #688 from mambax7/feature/2.5.11_version
b4a8a630 Merge pull request #693 from mambax7/feature/theme_missing_apostrophes
f23ea218 Merge pull request #694 from luciorota/patch-2
435f121b removed uneeded ucfirst
456cb5c8 templates missing apostrophes
e9ecc8b8 Merge pull request #692 from geekwright/killcurlies2
90315c95 Simplify code caught in curly bracket array check
be84a089 Fix trying to access array offset on value of type null
8074b083 Remove array access with curly brackets
0b6c040c 2.5.11 Alpha cycle
34206f2f Merge pull request #689 from mambax7/feature/htmlpurifier-4.11.0
b2efb05a Merge pull request #673 from poehoes/master
595ba98d Merge pull request #668 from tad0616/patch-3
24000e80 Merge pull request #667 from tad0616/patch-2
dec93a36 Merge pull request #690 from mambax7/feature/jQuery_3.4.1
505d0302 jQuery 3.4.1
12883904 htmlpurifier-4.11.0
f9dcea7c 2.5.11-Beta1 version
8862ac78 Merge pull request #687 from GregMage/quote_answer_pm
6e792006 Fix https://github.com/XOOPS/XoopsCore25/issues/686
cfc5a028 Merge pull request #684 from geekwright/issue683
84872b0d Add noopener rel attribute to external links in TextSanitizer
847cf285 Merge pull request #681 from geekwright/mysqlansi
9106ef99 Fix upgrade error under MySQL in ANSI mode
b18fc7cc Fix install error under MySQL in ANSI mode
c1cdbad0 Merge pull request #679 from geekwright/issue678
1f50b4b0 Implement Xmf\Request for input
a90864ef Merge pull request #677 from geekwright/mailerrefs
5a30e6b2 Merge pull request #675 from geekwright/deprecate-setFormVars
5093acae Unneeded by reference
df755f15 Unneeded by reference
266cd11b Deprecate \XoopsObject::setFormVars()
997f5053 Update admin.php
aaecebae Merge pull request #672 from geekwright/2.5.10-final
490bff23 Fix date
65a34bb6 Prep for 2.5.10 final
1aafd65b Update xoopsload.php
e76b8ade Create XoopsFormRendererBootstrap4.php
dcaba5ce Merge pull request #666 from geekwright/prep2.5.10rc
aad8a274 Merge pull request #665 from geekwright/xmf-1.2.17
4e921a9a Update changelogs for RC
d529ccbb XMF 1.2.17
70d838d5 Merge pull request #664 from geekwright/fix-mambax7-phpdoc
ae64f96f non-canonical order
32e799ec typo
cee5ac7e parent::loadConfig
e223954b getAll
594a6515 Merge pull request #660 from mambax7/master
6390cbda Merge pull request #662 from mambax7/feature/index_files
753c5481 index.html files
ea88c581 Merge pull request #3 from mambax7/imgbot
3f01dc38 [ImgBot] Optimize images
9fbdccb8 Merge pull request #655 from geekwright/updgitbook
f617b37d Merge pull request #658 from geekwright/issue657
57a7902d ModuleAdmin Footer Link
7e252354 typo
f5c1c3ae Update gitbook links
d300f3eb Merge pull request #654 from geekwright/tweakbeta
e3007540 Changelog and lang_diff updates
496001db Bump Version
b0885875 Update changelog
a9a4e848 Add notes on SELinux and MySQL8
b5a3b2b6 Module version bumps
743e2209 Minor tweaks and corrections
7a9d6050 Merge pull request #653 from GregMage/update_protector_changelog
00050a4e Merge pull request #652 from GregMage/Lang_diff
94039680 Updating the protector changelog
c377ce72 Update lang_diff.txt
4acdcc43 Merge pull request #651 from geekwright/issue532
e2e345ff Gif version of logo.png
d0b823cc Increase resolution
bef3b5ae Merge pull request #642 from geekwright/denylibs
7b810902 Merge pull request #650 from geekwright/issue649
5d4a31b4 Remove unwanted space in links
94d34b4f Merge pull request #648 from GregMage/ProtectorPrefixManager
1ae52987 the prefix setting is now in secure.php
4136e156 Merge pull request #645 from GregMage/moduladmin
e5223b76 Add strtolower() Note PHP Manual:
ebdacf38 Deny direct access to libraries
50a5001f Merge pull request #641 from geekwright/issue640
a381d091 Missing space
6697a65d Merge pull request #639 from geekwright/issue638
84f95175 Workaround for new Criteria(1,1)
342c689f Merge pull request #636 from GregMage/notifyxbootstrap
2def1fc3 Merge pull request #635 from geekwright/issue317-2
48fead6d fix Xbootstrap and xswatch notification display
891e2892 Hide ICQ, AIM, YIM, MSNM fields on new installs
54c1aa5a Merge pull request #629 from geekwright/libupd20190202
30484f72 Merge pull request #630 from geekwright/relprep2510b
021e7009 Release prep for 2.5.10 BETA
00697d0b Merge pull request #624 from mambax7/master
030f6476 Library updates
71133646 Merge pull request #627 from geekwright/issue626
372c539e Remove unneeded mt_srand() calls
918f39a3 check for variable
12772051 Merge pull request #619 from geekwright/minphp71
512832a0 Merge pull request #623 from geekwright/imglist
d847ddd3 Add getFileListByExtension() method
f334bef6 Merge pull request #622 from mambax7/master
e0ed8397 Merge pull request #2 from mambax7/imgbot
68ba7d88 [ImgBot] Optimize images
2f81cc6a Merge pull request #620 from geekwright/jpgjpeg
91b5ac7d Recognize jpeg extension in getImgListAsArray()
cc816d35 Merge pull request #614 from luciorota/patch-1
8b16bcbf Add warning for old PHP versions
695d695a Merge pull request #618 from geekwright/fineupreview
afb277c0 Minor fixes
ced41323 Minor fixes
4600b720 Merge pull request #617 from geekwright/xswatch-notif
eb39f895 Fineuploader review
ceeb1fef Tweak notifications template
b5fb3e2f Merge pull request #616 from mambax7/master
e6a8677f update info
06febcef cycle values="odd,even"
f484a406 Merge pull request #615 from luciorota/patch-2
55f7592c Update uploader.php
177d8c9f Update form.php
8381b527 Merge pull request #613 from mambax7/feature/newbb_template_array_check
4d0375ae NewBB tpl array check
9e8ed3a9 Merge pull request #612 from geekwright/libupd20181130
49c8dc13 Revert iconv requirement
e6e70350 Update class/libraries
9542e62a Merge pull request #609 from geekwright/remove-recaptcha
05e266a6 Merge pull request #608 from geekwright/xswatch20181114
b6027f3e Merge pull request #610 from geekwright/mysql8
e4463c93 Remove recaptcha
ab7f8d44 Ensure default collation is presented by default
9a6cd4cb Tweak comment templates in xswatch
98a658fd Merge pull request #606 from mambax7/feature/phpThumb_1.7.15-201810050741
998341fd Merge pull request #605 from mambax7/master
29b83cd5 Merge pull request #607 from geekwright/tablesorter-2.31.0
97ced2dc Compile less
c931d064 phpThumb 1.7.15-201810050741
c23ebb26 tablesorter 1.31.0
54ac94b4 typo fetchRow
1fd1b079 Add backtick quote to criteria column name
748f0839 Quote reserved word `rank`
4ae6f4b3 Merge pull request #601 from GregMage/admin_review_comment
04ef592a return to the div
36dc8776 Merge branch 'admin_review_comment' of https://github.com/GregMage/XoopsCore25 into admin_review_comment
7ed109f7 Changes css
b8f1d500 Fix https://github.com/XOOPS/XoopsCore25/issues/600 Comment statut
7761f687 Merge pull request #598 from mambax7/feature/htmlSpecialChars
c08735aa Merge pull request #599 from mambax7/feature/ENT_QUOTES
4af7e82e ENT_QUOTES Warning: htmlspecialchars() expects parameter 2 to be int, unknown given in file /class/module.textsanitizer.php line 557
76bf4f4f htmlSpecialChars
4c4f38fd Merge pull request #596 from geekwright/xmf_v1.2.25
e61dadbf Update to XMF v1.2.25
473a796f Merge pull request #595 from geekwright/prep20180918
c0d1d72c Catch up changelogs
91d41773 Merge pull request #593 from luciorota/patch-8
35d55589 Merge pull request #592 from luciorota/patch-9
30031734 Merge pull request #594 from geekwright/scrutinizertest
78de2e71 Add PHP checks
075e0267 Update scrutinizer config
425a3417 Update uploader.php
0d81fb0e Update uploader.php
ceccfc47 Merge pull request #591 from geekwright/issue590
b73d7706 Always redirect to user.php on no permission
b5dddfe7 Merge pull request #589 from geekwright/fineupvprotector
92c31a4c Protector vs. FineUploader Round 2
62fa9c7a Update README.md
9baa4d19 phpstorm
19f93baa Merge pull request #587 from mambax7/feature/CriteriaElement
74f5b56c update name spelling CriteriaElement
d4ef7207 Merge pull request #584 from geekwright/fineupnohead
c6c5455c Merge pull request #586 from geekwright/issue585
e041231f Properly initialize $exportString
502b02fe Sync with .tpl changes
32fc96dc Pass JWT for ajaxfineupload in post, not header
c35b46d9 Merge pull request #583 from geekwright/libupd2510
b82c976c Library Refresh
c7d43b98 Merge pull request #582 from geekwright/install2510
af1253f4 Installer updates for 2.5.10
40c653af Merge pull request #581 from geekwright/upgrade2510
ff707154 Upgrade for 2.5.10
72393c10 Merge pull request #578 from mambax7/feature/phpThumb_Update
3439f18c Merge pull request #577 from mambax7/feature/jQuery_Plugins_Updates
46f44898 Merge pull request #575 from mambax7/feature/HTML_Purifier_4.10.0
3984931d Merge pull request #580 from geekwright/pagenav
97f25cd2 Use Request to access $_SERVER variable
04040271 phpThumb 1.7.15-201802181529
15817139 jQuery & Plugins updates
f4ceb61c HTML Purifier 4.10.0
288ae6a8 Merge pull request #574 from geekwright/truncatehtml
b6c7730e Merge pull request #572 from mambax7/feature/phpmailer5.2.26
9185eaf8 Add Smarty modifier truncateHtml
0303a47e phpmailer 5.2.26
1d092d56 Merge pull request #570 from zyspec/patch-3
1a786e2f Merge pull request #571 from mambax7/feature/version_info_in_toolbar
aeeb8041 module version in Admin toolbar
a4c2991a Merge pull request #569 from mambax7/feature/assignment_in_condition
8b547517 reverting See: https://github.com/XOOPS/XoopsCore25/pull/569#pullrequestreview-104770639
5b3f7b06 Another hard coded http: to use XOOPS_PROT for URLs
3bf6f8f4 Assignment in Condition
d2358d64 Merge pull request #568 from zyspec/patch-2
cd790fd7 Change hard coded http: to use XOOPS_PROT for URLs
01e27930 Merge pull request #567 from geekwright/protectordata
317517f2 Move protector data to xoops_data
a593f887 Merge pull request #566 from mambax7/feature/missing_space
c7f178fa Missing space
652b9070 Merge pull request #562 from geekwright/themeauto
da24c21e Merge pull request #561 from geekwright/issue559
0ba2e77a Merge pull request #563 from geekwright/imgmime
fff5163b Error in error handling
c709efff Expose xoTheme and xoopsTpl for theme_autorun.php
bd14f25f Use standard xoopsUser check
56d52400 Merge pull request #557 from geekwright/fixtrunc
c10f7065 Don't show post comment button unless you can post
ca9f5e48 Merge pull request #558 from zyspec/patch-1
0631d062 Fix uninitialized variable
acbbc373 Fix XOOPS truncate plugin
515afa8c Merge pull request #556 from geekwright/deptrace
81e25ab1 Add mini-trace to addDeprecated()
b43a82df Merge pull request #511 from mambax7/feature/deprecated
e994fe86 Merge pull request #555 from luciorota/patch-6
d316adff Update image.php
4f243224 Merge pull request #554 from geekwright/truncateutf8
d5ab428b UTF-8 ready truncate for Smarty
f21df392 UTF-8 ready truncate for Smarty
038e9c2b Merge pull request #552 from geekwright/issue551
b9dffc23 Merge pull request #553 from geekwright/fixes20171117
5bc42391 Fix purgeCookies()
878f8961 Assign by ref error
a540652c Assignment not comparison
83f620a7 PHP 7.1+ non-well formed number error
f46ea488 Merge pull request #550 from geekwright/protectorstatfix
4bc84985 Visual fixes for protector overview
cf1d0ffd Merge pull request #549 from mambax7/feature/protector_is_set
77a74c82 is_set
ce0b35dd Merge pull request #548 from geekwright/checkassemver
412e924c Use semver rules to check min_xoops
381344ed Use semver rules to check min_xoops
d0d75955 Merge pull request #547 from geekwright/protector3.60
6916b83a update changelog for 3.60
064d1c05 Adding admin/stats.php
70757907 Tweak some diagnostics in stopforumspam calls
9cbf5696 Merge pull request #546 from geekwright/xmf1.2.12
d9f3250e Library Updates
b8ca50ea Merge pull request #543 from geekwright/changelog2510
aebf5c8c Merge pull request #542 from geekwright/issue541
4d99efc8 Add TinyMCE 3.5.12
f62176e1 Merge pull request #544 from geekwright/mambax7-tinymce3.5.12
5d18835a Revert changelog
3e6503c4 Merge branch 'feature/TinyMCE3.5.12' of https://github.com/mambax7/XoopsCore25 into mambax7-tinymce3.5.12
3a515342 Bump version, changelog
657179e7 Merge pull request #540 from mambax7/feature/Smarty_2.6.31
7d32f0a7 Merge pull request #538 from mambax7/feature/array_check_(PHP_7.2)
9a520334 Merge pull request #536 from geekwright/issue535
fb086aa4 Move MAX_FILE_SIZE element before file element
29148408 Smarty 2.6.31
95e6836f array check
ef95ee84 array check (PHP 5.7.2
7ceab4e0 ModuleAdmin stylesheet inclusion
1b0c5f72 Merge pull request #534 from geekwright/tsu
9c8e5ae1 Merge pull request #533 from mambax7/feature/userform_template
c50856b2 updates to *userform.tpl
c16d70a6 Escaping issues
ae193799 Merge pull request #531 from geekwright/rel259
d8e48147 Release Prep XOOPS 2.5.9
ffdd5710 Merge pull request #530 from geekwright/relpreprc3
16de3bc6 Release Prep RC3
d60498f4 Merge pull request #528 from geekwright/randbytes
7e93d8c2 Update Xmf and other libraries
403f9127 Adding paragonie/random_compat
de840ca1 Removing ircmaxell/random-lib and security-lib
6af95d9b Merge pull request #527 from geekwright/iconvreq
41a8d47c Merge pull request #526 from geekwright/issue524
5a788652 Merge pull request #525 from geekwright/issue523
4966f6cc Merge pull request #520 from GregMage/Strong_tag_not_closed_Uninstall
bf746b71 External URL filtering
b92bf9f2 Sanitize Mime-Type
3f05a40a Change iconv requirement
7d05c590 Fix error with strong tag
11d4d09c Merge pull request #519 from geekwright/relprepRC2
6d75b101 Cleanup generated archives
3d7840d8 Changelog and version update
a9acef80 Merge pull request #518 from geekwright/july04fix
604f87a7 Merge pull request #517 from mambax7/feature/html2tpl_in_themes
f964ff11 Merge pull request #513 from ihackcode/tooltips
95ce61b0 Merge pull request #516 from mambax7/feature/purifier4.9.3
8efdb148 Fixing issue in July 04 commits
fd2293d1 replace system templates .html with .tpl
e9dcf52e HTML Purifier is 4.9.3
e162f15a Set "transition" as default gui theme
82eb17d1 Fix vertical alignment of elements inside table
748a184c Add id to help system table
bffe3f44 Updating styles for admin help section
a11fa6ee Merge pull request #515 from geekwright/issue514
23d4cb61 Add missing arguments
f17eed69 added missing tooltip.js
dc425b03 not needed comma
f3de8373 consistency
0059f510 consistency
01fb70c6 Typo
1a4ebaa9 Merge pull request #510 from geekwright/upgradetpl
24f018ed Remove first time redirect
7c204dc6 Handle errors accessing site-closed template
44abfb1c TinyMCE 3.5.12
4d688119 Merge pull request #503 from geekwright/issue502
836afd72 Merge pull request #505 from geekwright/issue504
77a2c1f0 Theme template search order
05e26f15 Provide default value
0a8422ba Merge pull request #501 from geekwright/relprepRC1
18477352 Merge pull request #500 from geekwright/syscookie
d4c1868e RC1 Prep
3e5858dd Deprecate system module Cookie class
0f7a51f5 Merge pull request #497 from geekwright/issue495
f4c6ffc9 Merge pull request #496 from geekwright/issue494
8cc6041f Freshen Links
ca59c897 Fix botched assignment
67db8414 Merge pull request #493 from geekwright/pmfwd
e2078e35 Fix docblock
9d3026e5 Error in email forward
9899beb1 Merge pull request #490 from geekwright/mh20170512
3e14762f Merge pull request #488 from geekwright/xmfissue33-34
865de5a9 Merge pull request #484 from tad0616/master
a3969bfe Merge pull request #479 from mambax7/feature/kernel_object
fd2eb3c5 Merge pull request #481 from mambax7/feature/xbootstrap_xoopstube
b998e81d Merge pull request #489 from geekwright/issue486
2636a7ee Return integer for XOBJ_DTYPE_INT
c6bc0ddf Update to Xmf 1.2.9
f3ebf837 replace .text-muted with .help-block
7ccf6172 Add XoopsFormElementTray name for WCAG 2.0
45aee6da assets/images
0926384a Escape filename
82ec7e1a Only variables should be assigned by reference
bbc44f66 Validate file name
949c3c96 By ref error
4a89160a Add nonce
44517bba Merge pull request #477 from geekwright/fix475
0f40c0f4 Merge pull request #478 from geekwright/aboutlicenselinks
d36c2dc1 Missing file update in XMF
bd941b6f Fix broken license_url in modules
33d09676 Merge pull request #475 from geekwright/xmfv128
230074ed Merge pull request #476 from geekwright/issue474
2f853259 Upstream library updates
2a298f96 Serialize before escaping
2d659f0b Merge pull request #470 from geekwright/issue468
ea42a2c3 Add simple btn classes
76c5d5be Merge pull request #469 from geekwright/issue466
170f28f7 Remove stale links
96ae647d Merge pull request #467 from geekwright/xmfseotitle
a8e565fb Library updates
d3df482c Merge pull request #464 from GregMage/error_by_ref
81fd5b56 Merge pull request #465 from bleekk/master
51d2e624 newbb bootstrap templates
fad89fca error by ref in class userutility.php (add user)
ade4b20b Merge pull request #462 from geekwright/allowhtml
1b03a2d6 Upstream library updates
0ed51476 Merge pull request #461 from geekwright/xbissues
32599bf4 xBootstrap enable headhesive and submenu demo
1653db7a Merge pull request #460 from geekwright/prep2.5.9Beta3
dc4e4bdb Merge pull request #459 from geekwright/issue429
272cb496 By ref errors
d7d52876 Release prep Beta3
83664827 Merge pull request #458 from geekwright/mambax7-xbootstrap
b06f4a0e Minor error noted in testing
4f306f8f sync xswatch with recent bootstrap changes
fb0eb7ff sync xbootstrap with Angelo's version
46925007 Merge pull request #453 from GregMage/Display_date_comment
6e3c2e0d Published and Updated Dates #452
c0fad599 Merge pull request #450 from geekwright/upgradegroups
6384606d Merge pull request #451 from geekwright/composerupd20170403
f5715504 Change to kint-php/kint
f47a609d updates from upstream
a7594fdd Add group defines to rewrite data
2796a8a3 Merge pull request #449 from mambax7/feature/jQuery3.2.1
d8819c06 jQuery 3.2.1
097511af Merge pull request #447 from geekwright/stdusercookie
f0bdf15d Remove unused define
15947c1e Standardize usage of usercookie
cd384e99 Remove store name in cookie for a year
8cce49c4 Merge pull request #446 from geekwright/zyspec-paypal
69230ed2 Updates for PayPal changes
69135f03 Merge pull request #445 from geekwright/cacheclear
e2e500f2 Clear caches on module updates
9fb89a8c Merge pull request #443 from geekwright/fixedgroups
19914a68 Use XOOPS_GROUP_* defines
e962d1d0 Merge pull request #441 from geekwright/issue438
9718d676 Merge pull request #440 from geekwright/issue436
995526c5 Merge pull request #439 from geekwright/issue432
f9ef82fd Include once
9066839a Typo
44dfa56b Guard against external loading
b5337dc5 Merge pull request #435 from geekwright/shareaholickey
29d38b92 Merge pull request #434 from geekwright/comrefresh
f825c91a Typo
1cdc1b68 Update with shareaholic instructions
1c5fd3c9 Clear data-app-id in shareaholic-canvas
f8ae075a Update to current API conventions
6c77b091 Comment bar missing hidden elements
355493d7 Merge pull request #431 from geekwright/issue430
830a0d7b More visibility for added user
4289c73a Merge pull request #428 from geekwright/docreview
c5bdc965 Merge pull request #427 from geekwright/issue426
8ffdfb8f Doc update
d28f1e68 Undefined variable in some paths
8dbb9a07 Merge pull request #424 from geekwright/partialrevert419
84339f4a Restore array types
808b16d1 Merge pull request #423 from geekwright/changelog20170311
1e47f7db Changelog updates
e15913d0 Merge pull request #422 from GregMage/theme_install
85365e64 Merge pull request #420 from trabisdementia/Profile
31ed09db Fix #421
b8679f81 Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into ProfileModule
666a1953 Merge pull request #419 from geekwright/issue410
7a291f99 Handle non-array, set sane defaults
407cf6f0 Merge pull request #418 from trabisdementia/ProfileModule
766d5135 Fix issue #410 for profile module: select fields not showing.
d26b7083 Merge pull request #416 from geekwright/issue415
990a1c26 Normalize paths
0d029100 Merge pull request #414 from geekwright/forselusr2
d23a985d Merge pull request #413 from geekwright/blankavatar
034d2726 Handle no theme case
b6dd3cfb Merge pull request #411 from geekwright/formselusr
606527ee Consistent user_avatar on account creation
f3e9f781 Remove redundant script tags
895ab969 Merge pull request #408 from geekwright/issue397-3
d4a50866 Add user issue
0a1b8b99 Merge pull request #407 from geekwright/xmfassert
061b87d1 Xmf update
d9d4dcfb Merge pull request #406 from GregMage/Search_error_link
46d7e4d4 Fix error link in search.php
1b74b4dc Merge pull request #404 from geekwright/upgrader
38704afe Fix and standardize exception handling
4ed59770 Upgrade and install tweaks
b29ea779 Merge pull request #402 from geekwright/xmfupdate
c0af22d0 Library Updates
6cc61cfb Merge pull request #401 from geekwright/issue399
b187f44e Merge pull request #400 from geekwright/issue397-2
5d0008a8 Match jGrowl to theme in xswatch
50ed7851 Add user button
5a33903a Merge pull request #398 from geekwright/issue397
f73ab712 Add new user button
b9be98d3 Merge pull request #395 from geekwright/issue390p2
9528da05 Cap non-admin multi-file uploads
300435c9 Merge pull request #394 from geekwright/issue390
1ba292ec Permit higher traffic for multi-file uploads
84b757ce Merge pull request #388 from mambax7/master
b82640d6 Merge pull request #393 from GregMage/fix_link
a4e75644 Fix bug with button
667e419d Merge pull request #392 from geekwright/issue391
6d0cfd1f Lock text and code on instantiation
a760c39e missing quotations
b2d050ca Merge pull request #384 from geekwright/issues381-382
1f92b976 Issues 381 & 382
4b36c8db Merge pull request #380 from geekwright/changelog20170224
d445c654 Merge pull request #379 from geekwright/shorttagerror
3019e6e3 Fix short tag error
df887485 Change log and version
d2d4c172 Merge pull request #378 from geekwright/modversions20170224
e2252f8b Bumping module versions
e077ad34 Merge pull request #377 from geekwright/themechange
a0500909 Merge pull request #376 from geekwright/newupgrade
4160cb4a Update themes
72206ae5 Merge pull request #375 from geekwright/newinstall
917fd022 README, remove debug code
8ac87850 Refactor and new style
e0de483b Arg is ignored, but already set
5c560226 Initial cleanup
06e90c36 Merge pull request #372 from geekwright/inboxcount
e4b0a5f4 Merge pull request #373 from geekwright/rmfileman
71217364 Hide links in module install log
25c6185e Add missing .noininstall
6da275f9 typo
30f27eb2 Credits
b139b95e Remove the old filemanager from extras
4ff26fc9 Merge pull request #371 from geekwright/lexikonbs
67b5ac36 smarty xoInboxCount plugin enhancement
dff7a64b Cleanup
4ac685ca Recognize error condition
62365a90 Update bootstrap.min.css
25719026 Mobile tweak
350cedae Cleanup details
10672852 Assets folder
6928554b First pass
2c418595 Add lexikon templates from bleek
7f1c2635 Merge pull request #370 from geekwright/formassign
427d0fe7 Merge pull request #369 from myxoops/master
b63cfcfb Add rendered form to XoopsForm::assign()
21afcc54 username and avatar in userblock
8d29d1c7 Merge pull request #366 from geekwright/tswiki
68d2db22 Merge pull request #368 from geekwright/resetactkey
a3692185 Reset actkey on activation
6cc22178 Update textsanitizer Wiki imagery
db6e75c0 Merge pull request #364 from geekwright/sslcookies
39f227e8 Merge pull request #363 from geekwright/recptchassl
a24922b7 Secure flag on cookies
f77ae987 Use SSL by default for recaptcha
aee80ef4 Merge pull request #362 from cesagonchu/master
794173b6 Merge pull request #361 from GregMage/fix_tinyMCE
e29de905 define corrections into xBootstrap for XoopsTube module
76a7a28d fix error with getimagesize and allow_url_fopen =
53e04c9f fix error by ref
7df5ea0e Merge pull request #359 from GregMage/link_comment
05a3289b update system_comments.html
158d831f Fix error by ref in main.php (comments) and add a link to display comment
5b1f5ac3 Merge pull request #354 from GregMage/Finalization_fineuploads
87d1f42b update imagemanager
2b803410 Merge pull request #353 from geekwright/intlext
57b70d67 change log
59ce3374 Rebuild libraries
d3352e3d Recommend Intl extension
b6d497f6 Merge pull request #352 from geekwright/noaim
ed77d504 Suppress old messenger field in profile
3328d40a Merge pull request #351 from geekwright/php53-20170214
f04dcd09 Merge pull request #344 from geekwright/fineupdebug
e8971db5 method return value in write context
2a4597e7 Merge pull request #345 from geekwright/changelog20170213
aa77d634 Merge pull request #347 from geekwright/cookiedom
21e9b860 Merge pull request #346 from geekwright/pmicon
cd05eb4c Merge pull request #349 from geekwright/rmgeshi
759377ae New Additions
39c39f36 Merge pull request #350 from GregMage/multi_upload_image
ec855013 Merge pull request #348 from GregMage/multi_upload_avatar
7973b702 fix system_images.html
56eca521 Fix template name
03d0de6c add fineupload for images (admin)
25be0feb Text sanitizer cleanup
64ff375e update ajaxfineupload.php
2506691b add fineupload for avatar
fa75c7da add css file for fineupload
7a7ea685 add new templates for fineupload
e0787acd New Additions
b620451e Add cookie domain to install
32faf590 Issue with subject icons
f699d178 Changelog updates
df6aac7d Debug option for fine uploader
054463a0 Merge pull request #341 from geekwright/issueself
242151b1 Merge pull request #342 from GregMage/multiple_images
9e573323 add some language support for the fine-uploader
b43b4599 Merge pull request #2 from geekwright/multiple_images
d60c4188 Revise xBootstrap pm templates
31d0bd45 Turn off retry for bad mime type or extension
c6c1026d Remove unneeded code
485d77f3 Refactor
8f13fd6c Merge pull request #339 from geekwright/freethis
e1405791 Error in class management
70bb4156 Merge pull request #337 from mambax7/feature/Undefined_method
0d61749f Merge pull request #1 from geekwright/mamba337
b3d897f0 Reverting
66ea5565 Change default to use custom session
3e5ae841 Undefined Methods
3016ed45 add check mimetype. does not work
13b5f072 Merge pull request #336 from mambax7/feature/Missing_constants
7b271153 Merge pull request #335 from mambax7/feature/Strict_PHP
40099227 Merge pull request #334 from GregMage/imageadmin_pager
56f6cfae Merge pull request #333 from mambax7/feature/testdata
23910a5e Undefined constant
74571574 Overridden method compatibility
a385a937 Merge pull request #330 from mambax7/feature/Updates
73f27457 change: renderImageNav() to renderNav(4)
2b82f077 Revert "sync"
6bd6d1ab finalise multi images
49c0158c TestData option
0c632301 Change name and mimetype
bfa6485b update endpoint.php
dddc2385 unset folder fine-uploader in uploads
b1d5cc65 change tpl
3d99c860 update fine-uploader
c7efaf63 Naming conventions
ac10cb6f Unnecessary label
d662f3d7 Missing semicolon
532bc425 Access modifiers shall be defined
18559333 Case mismatch
1e63dc09 sync
100be1b7 Merge pull request #1 from geekwright/multiple_images
d5e38f43 Merge pull request #329 from geekwright/miscfix20170208
2e3cb9db Tweaks for issues
7267bdb2 add endpoint
30291d6b add uploads files
6093bd6b Changing the size of the window
1446bc46 html5 style
fb0635c9 typo fix
7d34c24f Merge pull request #328 from geekwright/seluser
54a9b54d Select user buttons
82e787b3 add fine-uploader in imagemanager
3cf8a2f3 add fine-uploader to Framworks
1f0be3f1 Merge pull request #326 from geekwright/protectortweaks
368dedd1 Merge pull request #324 from geekwright/fix20160206
5cfd9f2e Merge pull request #325 from geekwright/issue321
64ebd0a0 Protector tweaks
47bb1350 Add public suffix list support
530ab18f By reference error
1f2aece6 Merge pull request #320 from geekwright/upgrade224
7a4940d7 Merge pull request #250 from mambax7/feature/htmlpurifier480
14e79de6 Merge pull request #323 from geekwright/protver7.1
23bc73ce Add cast for version in XoopsModule
af199cba Error from protector in PHP 7.1
af5183dc Cleanups
44b43fb1 Merge pull request #319 from geekwright/issue317
5bbce9fc Merge pull request #318 from mambax7/master
23ea6505 Profile updates
01d05f6e updates
2472900d Merge pull request #315 from geekwright/themeblk310
c1a57e91 Merge pull request #240 from mambax7/feature/version_remove
91e2c113 Theme and Info block tweaks
5bb3915b Merge pull request #306 from mambax7/feature/header_updates
d64caddc Merge pull request #312 from geekwright/issue308
efa59cb0 Merge pull request #314 from geekwright/issue309
344457ca Supress zero post users
3dfca644 Add bootstrap compatible template
66d5aa86 file header updates
735bc01d Merge pull request #305 from geekwright/subicons
5a722b97 Merge pull request #304 from geekwright/popupheader
773806f6 Add missed templates
733e0f45 Allow 128x128 avatars by default
a8a62447 Visual consistency tweaks
acc6b720 Add system_popup_header.tpl
ae66d8f0 Replacement subject icons
f456d04b Merge pull request #302 from geekwright/basereq
20860e82 Add XOOPS\base-requires25 package
980d8eca Merge pull request #300 from GregMage/trigger_error_tree
f604d851 add a trigger error if you use makeSelBox().
e6788820 Merge pull request #299 from geekwright/hcbuttons
ec0f21f5 Merge pull request #298 from geekwright/issue294
f26ed126 Removing graphic images with english text
2d62a114 Do not include xoops.css on redirect
f1ad41b8 Merge pull request #296 from geekwright/issue292
28bf19ad Merge pull request #297 from geekwright/commenttpl
b885a1c9 Use XoopsFormSelect in XoopsObjectTree
a90b1b8b Add template for comment bar
bda18efd Merge pull request #295 from geekwright/issue290c
d46a7719 Value display in checkbox and radio elements
d0bd6f9c Merge pull request #293 from geekwright/issue290b
e61dcaf4 Allow nested trays to work as expected
0da0cee9 Merge pull request #291 from geekwright/cleanupxfrbs
597c68fd Cleanup pass on bootstrap form renderer
8fab32c4 Merge pull request #289 from geekwright/issue286
2bb28dda Hidden form issue
bd9bf5e0 Merge pull request #288 from geekwright/issue285a
be68d46d Visual issue in ModuleAdmin::renderIndex()
97766cdc Merge pull request #283 from geekwright/issues-257-8-9
f1d620c3 Fix installer notices
061429cb Merge pull request #281 from geekwright/rm-admin-legacy
2413098a Merge pull request #280 from geekwright/issue275
13b55641 Merge pull request #279 from geekwright/issue276
ef95f8c8 Merge pull request #252 from mambax7/feature/xbootstrap_extgallery
de7a51e1 Merge pull request #251 from mambax7/feature/xbootstrap_publisher
7911bad3 Merge pull request #263 from GregMage/New_search_template
cd572c32 Merge pull request #282 from GregMage/fix_bug_XoopsFormRendererLegacy
eb6caa87 Fix bug with checkbox in XoopsFormRendererLegacy.php
1f435694 Remove "legacy" admin theme
17c04f68 Add indication on cloned blocks
f10f8611 Block admin select color
8d9a9bdc Merge pull request #278 from geekwright/GregMage-Bootstrap_compatibility
b786da58 Add themed form rendering to RendererInterface
a26bf28c Typography dhtml editor tweaks for bootstrap
8029cf0b Adapt to XoopsFormRenderer
408dff36 Adapt to XoopsFormRenderer
a455885c Adapt to XoopsFormRenderer
a0cbbb01 Add theme_autorun.php support
8a4701be Introduce XoopsFormRenderer
1922f9df Add Font-Awesome
c51ea6ac Merge pull request #273 from GregMage/error_by_ref
0b681634 Merge pull request #270 from geekwright/phpmailer-5-2-21
a29c259e Merge pull request #265 from GregMage/bug_userinfo
6e100af9 Merge pull request #272 from GregMage/error_admin_banner
d835258d Merge pull request #274 from GregMage/mimetypes
ed108b97 add .csv mimetypes
1694214d omission error by ref
b0487b94 Fix error by ref in tplsets
d7a2f20d Fixed bug banner in admin
01cdb267 Upstream Update
7d28ae0f Merge pull request #269 from geekwright/phpmailer-5-2-20
b55ac7ec Upstream Update
ffc2d2c2 update templates
f716be32 Merge pull request #268 from geekwright/issue256
a11eccdf Merge pull request #267 from geekwright/xmf1-2-0
102067f2 Keep consistent use of $error variable
7c183b00 Upstream Library Updates
d5c6c4cc Merge pull request #262 from geekwright/memexhaust
2b6743ab Merge pull request #266 from geekwright/phpmailer-5-2-19
4e69cd78 Upstream Updates
e332876d error in userinfo.php
a332b42a Add template for search
af818acf Remove glob call in \SystemMaintenance::CleanCache
309d111e Merge branch 'Bootstrap_compatibility' of https://github.com/GregMage/XoopsCore25 into GregMage-Bootstrap_compatibility
053c643d Merge pull request #260 from geekwright/transition-admin-theme
173d1693 New Admin Theme - Transition
61abb1b0 bug in admin
2595d25e Improved code to improve display (formelementtray)
818446ee Clean code
dae9dd8c bootstrap formelementtray.php
4091fc2d bootstrap formtextdateselect
a62434ab bootstrap formtextarea
1f16c4ac bootstrap formtext
acee58c6 bootstrap formselect
6cff2403 bootstrap formradio
b00c8221 bootstrap formpassword
3a760fb8 bootstrap formlabel
58f0f9a6 bootstrap formfile
2ab9ad8d bootstrap formdhtmltextarea
51f307c0 bootstrap formcolorpicker
27d4a54e bootstrap formcheckbox
a641d018 update theme.php
0276fbf3 bootstrap formdhtmltextarea
a9522f83 bootstrap formbutton and formbuttontray
49e045fd Add $GLOBALS['xoopsConfig']['bootstrap']
0a3df4f1 image folder update
f901ce95 image folder update
710bb6c5 HTML Purifier 4.8.0
679cdd26 HTML Purifier 4.8.0
19ab2ec4 Merge pull request #248 from geekwright/upgrade-merge
a215c0cb Fix merge error in upgrade/index.php
bf3d52ec Merge pull request #247 from geekwright/upgrade-session
69a0f14c Session IP address issues in upgrade
2d970dc4 Merge pull request #245 from GregMage/Login_over_IPV6_fails_during_upgrade
2ff86ddf Merge pull request #244 from mambax7/feature/jQuery3.1.1
c7eee2ce Login over IPV6 fails during upgrade
24997bd0 jQuery3.1.1, jQueryUI 1.12.1
c867cefc Merge pull request #243 from geekwright/protectedtree
dbc71ac9 BC Patch for XoopsObjectTree
8e108a5f remove @version
d9404487 typos
33b9d0d7 Merge pull request #237 from geekwright/php71session
f611a872 Handle possible PHP 7.1 session ids
82918953 Merge pull request #236 from geekwright/issue234
dc574baa Merge pull request #235 from GregMage/recaptcha2
5017028a Rework curl code
44bc7106 Change recaptcha_2 to recaptcha2 in comment
c26f5520 Merge pull request #233 from GregMage/Preload
868fd308 Removal array() in the triggerEvent() arguments
a1613692 Add Preload for behavior code
7589c082 Merge pull request #232 from geekwright/mambax7-feature/captcha
87c15613 Merge pull request #229 from geekwright/upgphp53
0c62df40 Revert to Google supplied reCAPTCHA Library for PHP - v1.11
90dabbd6 captcha cosmetics
4f523eef Merge pull request #230 from geekwright/GregMage-reCaptcha_2
f68c0045 Drop test file
3d0d62c9 XoopsCaptcha should have protected __construct()
d0c96dc9 PSR2 update
7d327e11 UTF-8 conversion
0ef73a45 PHP 5.3 regression
52123427 Add trigger_error("recaptcha is outdated , use recaptcha_2 in \class\captcha\config.php", E_USER_WARNING);
b57f4d6a add recaptcha_2 in config.php
f1ac0124 add test_xoopscaptcha.php. It's just for test. To remove before the release of the final version of XOOPS 2.5.9
550f9189 Add recaptcha 2
4405278f replace xoops_load(captcha') with xoops_load('xoopscaptcha');
fd813e9d Merge pull request #223 from geekwright/bannerupdate
851e184f Add xoopsifyIt banner
f499348f Merge pull request #222 from geekwright/spamupdates
99d70f1f Merge pull request #221 from geekwright/themesearch
6d54e086 New Protector Filters
67f5b4f4 Conditional search box in xbootstrap, fix typo
e48ddc67 Add xoops_search assign
d21a153b Merge pull request #218 from geekwright/ref160823
a60cfb89 By reference error
4d4f15e3 Merge pull request #214 from GregMage/XoopsFormDhtmlTextArea_preview
8c0fd75e Merge pull request #217 from GregMage/textsanitizer
fdb9327b Update formdhtmltextarea.js
3497ca4c Update formdhtmltextarea_preview.php
a73b01cf Update readme.txt
82d8f05a Merge pull request #210 from mambax7/feature/type_comparison
6dbffc71 Merge pull request #216 from geekwright/mambax7-feature/Globals_xoopsOption
86c21b0d Conflict resolved merge of branch 'feature/Globals_xoopsOption' of https://github.com/mambax7/XoopsCore25
88c10479 Merge pull request #213 from mambax7/feature/Callable_parameter_usage_violates_definition
8cd1747e Merge pull request #212 from mambax7/feature/reference_Mismatch
139ca100 Fix bug XoopsFormDhtmlTextArea preview with UTF-8
9a836e7f Callable parameter usage violates definition
1e05a1ea reference mismatch
6cd72a5f Type annotation in PhpStorm
5f8281d4 safe type comparison, other cleanup
c36a13f1 Merge pull request #208 from GregMage/SelectTheme
f001abeb Fix bug with theme.html
f2747be7 add $theme_set_allowed = false in XoopsFormSelectTheme
561980b3 Merge pull request #207 from geekwright/instops
3eff001c Install configuration options
a86b32a7 Merge pull request #206 from geekwright/smarty-2.6.30
3faa36e2 Smarty v2.6.30
fff71991 Merge pull request #204 from geekwright/norem2
8f2cb90d Update system_userform.html
1163cf6d Merge pull request #200 from geekwright/phpthump
ffe625be Merge pull request #196 from geekwright/fixinstsup
e9efbdf0 Merge pull request #199 from geekwright/fix146
9ea0fb15 Merge pull request #201 from geekwright/norem
6d175907 Merge pull request #203 from GregMage/XoopsForm_PHP4
2ffcd415 Compatibility in XoopsForm with php4
89b2c745 Turn off remember me by default
68650bda phpThumb to 1.7.14-201608101311
55926a8e Bad links in admin theme localsupport
4579f795 Merge pull request #198 from geekwright/fixrp197
72315b25 Small issue with symlinks
5f43f9ae Shift toward HTML5
efb47501 Add curl to recommended extensions
03f901d1 Merge pull request #197 from GregMage/xoops_page
a2035537 add $xoops_startpage
f78f0980 Use $xoops_page
a76ccb69 Add $xoops_page
c877681b Merge pull request #195 from mambax7/feature/xmlrpc
8c7cbade Localized support site issue
c1eea687 reference mismatch
9b00f499 name update
ac639113 Merge pull request #187 from geekwright/jqui1.12.0
d9285291 Merge pull request #192 from GregMage/mimetypes
c79b40b8 Update mimetypes.inc.php
6127be15 Merge pull request #190 from geekwright/xmf113
d0d501af Update to XMF 1.1.3
62a60d9d Merge pull request #188 from zyspec/feature/xoopsformtextdateselect-improvements
0f0092d8 Fix lightbox image sizing
fa9ba06c jQueryUI theme update
5681038f Adjust avatars display
d9a589ea jQuery UI to 1.12.0
435d7bbd Merge pull request #186 from geekwright/zyspec-feature/compare-type-improvements
e7bb3685 Reset and reapply corrected fix
97a5a17d check for existance of language calendar.php file and if $GLOBALS['xoopsTpl] before it's used
b813d7d8 Squashed commit of the following:
2c9b6a62 Merge pull request #182 from geekwright/xmf112
437ab39f Merge pull request #179 from mambax7/feature/Bootstrap3.7
b99ed5b8 Merge pull request #178 from mambax7/feature/jQuery3.1
2861f3e6 PHP min to 5.3.9
a2560c69 Update XMF to v1.1.2
ab106da5 Merge pull request #181 from mambax7/feature/Version_update_2.5.9_Beta1
0a125e3a updating version to 2.5.9 and the changelog
8b981cdb Merge pull request #177 from geekwright/upgbtn
086e2f65 Bootstrap 3.7
4fd2e378 jQuery 3.1
fab98790 Merge pull request #175 from mambax7/feature/xBobstrab_Publisher
f7082da9 Merge pull request #176 from mambax7/feature/User_Icons
45301821 Highlight Update System Module in upgrade
0c6104fd changing icons for consistency
49adff50 fixes for Publisher: - adding missing page navigation - fixing "bold" in summary
e7b5d2fb Merge pull request #173 from mambax7/feature/HTML5_Syntax_2
f4a345a3 Merge pull request #172 from mambax7/feature/fixes
c98ba02a HTML5 syntax
7027c43d fixes
eada21ae Merge pull request #171 from geekwright/html5form
97696e76 Merge pull request #170 from geekwright/formtime
9843550c Move to HTML5 syntax
b32b4a96 XoopsFormDateTime - time only option
4566785c Merge pull request #169 from geekwright/profile168
6a927e32 Bug in ProfileProfileHandler::get()
1ddf1415 Merge pull request #160 from mambax7/feature/phpThumb
60bfc744 Merge pull request #165 from geekwright/liomj-master
f399bee0 Merge pull request #167 from geekwright/xoopskernel7
9b400709 Merge pull request #166 from geekwright/tree7
1d0bf196 xos_kernel_Xoops2 reference issues
62983630 XoopsObjectTree issues
5dfae96e Tweaks
b29d8bae Merge pull request #164 from geekwright/tsrefs
f5b52f36 Update style.css
08d81fbf Update theme.tpl
34deb010 Add files via upload
89b79481 Update rightBlock.tpl
64643ebc Update leftBlock.tpl
edb32de6 Update style.css
a4ecbb3a Make clean exit
900648af Reference issues
8343f706 Merge pull request #161 from mambax7/feature/typos
467a48ad comparison fix
6956eb72 Merge pull request #159 from mambax7/feature/jquery-1.10.2
694eea86 Merge pull request #158 from mambax7/feature/jqueryForm
face674e Merge pull request #157 from mambax7/feature/owl.carousel
bcccebc0 Merge pull request #156 from mambax7/feature/tablesorter
a63686c7 Merge pull request #155 from mambax7/feature/masonry
35def7d7 Merge pull request #154 from mambax7/feature/jgrowl
79d9e618 phpThumb 1.7.14-201607141354
3357938a typo
98638219 removing jquery-1.10.2
47f6db3e remove jgrowl.map link
167cbb5d owl.carousel 1.3.3
1f98489c tablesorter 2.26.6
6f598bec Masonry 4.1
c55391c4 jQueryForm 3.51
70ef92f3 jgrowl.css
826d41b1 Merge pull request #153 from geekwright/fixes071416
04f239c5 Misc fixes
f8adfe91 XMF to v1.1.0
6a298b31 Merge pull request #151 from liomj/master
7bc7d6b4 Merge pull request #152 from mambax7/feature/jgrowl
de07148f remove old file
7dce8d76 jGrowl update
1d627c09 update system_siteclosed for xbootstrap
b46f2053 Merge pull request #149 from mambax7/feature/utf8-changelog
3ab5c820 Merge pull request #144 from geekwright/helpsmi
69e18845 Merge pull request #145 from geekwright/issue139
e3e95628 https://github.com/XOOPS/XoopsCore25/issues/140
5a573b45 Merge pull request #147 from cesagonchu/master
e28e13a6 Update help.tpl
82e30489 Correct protector connection check
fa665931 Missed files
3a9f4e5c Merge pull request #142 from mambax7/feature/cosmetics
161eade5 Report error on database connection
4b446d25 Merge pull request #143 from GregMage/master
a9546810 Fix Bug #141 for smilies
469b92a1 Little error
d88a530c Fix Bug #141 for userrank
7c7bddb2 Merge branch 'master' of https://github.com/GregMage/XoopsCore25
a4adbe17 improved upload class
ef8f3da0 improved upload class
acf9e961 Fix Bug #141 for avatar
292213bc missing language constants
1b926585 Correct help/module_index in system
974697c6 Merge pull request #138 from geekwright/doc4handler
e8aefa4d Docblock fix for getHandler functions
8325f3a3 Merge pull request #135 from mambax7/feature/xml
0f140780 Reference mismatch, cosmetics, .tpl
a2ef413c Merge pull request #134 from geekwright/fixes061616
3dbf8cdc Fix occasional issues using xoops_loadLanguage('locale')
c974c005 Fix multiple issues in image.php
c872b012 Add a table property to handlers
fad583fd Backport Smarty securityToken plugin
75a7cd8c Merge pull request #133 from ggoffy/master
2954a572 part 3
d2c12643 part 2
349b1afb eliminate notice "Only variables should be assigned by reference...."
36d09044 Merge pull request #128 from mambax7/feature/xml
39910d73 reference mismatch
2f5c1551 Merge pull request #125 from mambax7/feature/preuninstallfix
8e74ac17 changelog update
162a0404 fix for pre-uninstall
cf0d01bc Merge pull request #124 from luciorota/patch-3
bb4d7d58 Merge pull request #123 from mambax7/XOOPS_DB_CHARSET
1817a33a Update makedata.php
5579e738 bug in PHP < 5.5
fec09f72 Merge pull request #122 from geekwright/xmf102
a589846f Update change log
1b637088 Merge pull request #121 from geekwright/fix120
b6de9c3b Update xmf to version 1.0.2
dc147233 Change set charset behavior for mysqli
50e82a00 Merge pull request #119 from mambax7/feature/br_html
df40f7b4 Merge pull request #118 from mambax7/master
2d0f0a10 replace <br /> with <br>
9763f6e9 Icons for Edit, Delete, Download (bleekk)
d6a847fb Merge pull request #117 from geekwright/pubcleanup
0813c797 Doc updates
43eb2fe8 Assign by reference issues
d502dd25 Streamline setup for publisher with xBootstrap
c7f9b875 Merge pull request #116 from geekwright/mailerlocal
3a4e38b5 Merge pull request #113 from mambax7/master
eee40aef PHP4 named constructor shim
c14d6b0f Tabs 1.2.7
59d8d37e Merge pull request #112 from geekwright/finalprep
36b772c4 Release prep
b0290a20 Documentation fix
1d6a8686 Fix issue if $xoopsModule is set to false
ee31aed8 Merge pull request #111 from mambax7/feature/jQuery_1.12.4
f62d9811 Merge pull request #110 from geekwright/mambax7-feature/banner_fix_Bootstrap
41e1c553 jQuery 1.12.4
e97337c7 Tweak to work correctly in 'default' theme
96dedc49 Merge branch 'feature/banner_fix_Bootstrap' of https://github.com/mambax7/XoopsCore25 into mambax7-feature/banner_fix_Bootstrap
f40189aa Merge pull request #103 from mambax7/feature/phpMailer
f1141df7 fix for https://github.com/XOOPS/XoopsCore25/issues/90#issuecomment-216917263
08beffa0 https://github.com/XOOPS/XoopsCore25/issues/102
9688ab7d Merge pull request #95 from geekwright/i94
9cdd7599 Merge pull request #93 from geekwright/i92
b62e392c Merge pull request #100 from geekwright/mambax7-feature/extra_banners
f509c49a changelog update
bb2d5e52 extra banners
4257bb0f Merge pull request #97 from mambax7/feature/HTML5_Banner
c5f97864 Merge pull request #96 from mambax7/master
d031b91b SWF banners to HTML5
a2cf7184 jQuery 1.12.3
cd96eb0c Fix XoopsModelJoint::getCountsByLink keyName_link bug
ab288b85 Fixes issue if id column keyName is common to both tables
73296480 Merge pull request #88 from geekwright/i87
19068448 Restore missing help
caffc2d4 Module update breadcrumb
67c1aeb0 Merge pull request #83 from mambax7/feature/strong__in__Admin_theme
9550fa19 Making  "strong" tag visible in Admin
8096066f Merge pull request #82 from luciorota/master
e540f355 Merge pull request #81 from geekwright/i78
b1040e2e Update main.php
a7e763c9 Added files via upload
a8ffba2d Move jGrowl button back to right corner
a73b4a5b Revert change of .html to .tpl
dfe1fba1 Merge pull request #76 from txmodxoops/timgno
6122ac43 Added codify xoops_session
af59bddc Merge pull request #75 from geekwright/rc3prep
fe439c47 Merge pull request #74 from geekwright/logandload
0f9d40a9 Release prep for RC3
f2286d0c Two small fixes
74ad3667 Merge pull request #73 from geekwright/wideimage
d072d117 Merge pull request #72 from geekwright/publink
ed894407 Add wideimage to library
96fba8de Point module source links to GitHub
2b7a2ee6 Merge pull request #70 from mambax7/master
2989f1e2 Merge branch 'master' of https://github.com/XOOPS/XoopsCore25 into develop
48dde822 updating comments
dbe5091f Merge pull request #69 from geekwright/mbts
a3fc4d91 Make mb_* call conditional on function exists
add69d41 Merge pull request #68 from geekwright/snoopy1.2.5
1a84d64f Update Snoopy to 1.2.5
429a74bd Merge pull request #67 from geekwright/fix66
3dbb03df Missed bad constructor
c8bbcb9f Unacceptable curl options
ea7f9a60 Remove pointless PHP4 style constructors
6564b52d Merge pull request #66 from mambax7/develop
081a9209 phpThumb, jGrowl update
a7e5e84d Merge pull request #65 from geekwright/mambax7-feature/svn_version
de965e0b Sync with XMF 1.0.1
7fa9d79b Revert vendor changes
4c14d162 removing @version
3a16fa03 removing SVN versioning II
1c513089 removing SVN versioning
91c65880 Merge pull request #63 from geekwright/xmf1.0.0
6c74be77 Merge pull request #62 from geekwright/docupdates
722a7681 Merge pull request #61 from geekwright/issue55-2
2153dd99 Update Libraries
3d4906d9 Documentation review and link checks
8d6c4b7f Fix issue when posix functions not available
4d5ceb39 Merge pull request #60 from geekwright/issue55
16933c63 Update installer configuration file save
4ea4c6d6 Merge pull request #59 from geekwright/issue57
59cd1259 Merge pull request #58 from geekwright/issue56
5c8b71b6 Assign by ref issue
2a5f37b4 Change bug and code links in admin
245cf1fe Merge pull request #54 from geekwright/php7tweaks
0f2a8f6f Remove HTTP_RAW_POST_DATA use
8e5be7b3 Fixing constructors in phpthumb
fda14063 Add comment for future reference
d01ca3c7 Merge pull request #53 from mambax7/feature/php7fixes
5e25cb78 PHP7
a2b26889 Merge pull request #52 from mambax7/develop
7444c90f addNavigation(basename(__FILE__))
0d742fea Merge pull request #51 from geekwright/fixes20160322
e816ad0a Small fixes
0ca8743e Merge pull request #50 from mambax7/feature/profile
e283e8da improve security by saving after 2nd registration step
cc751f10 Merge pull request #49 from mambax7/feature/jquery1.12.2
07b96bef Merge pull request #48 from mambax7/feature/methodsparameters
ba187948 Merge pull request #47 from geekwright/profileop
30ab7150 updating jQuery to 1.12.2
2cbbb95e small fixes
3904df56 Make permission op sticky
c776cf2b Merge pull request #46 from geekwright/fix43
7ab98f80 Replace getAll() in ProfileVisibilityHandler
86d2b96e Merge pull request #45 from geekwright/post44
f1fd9cc1 Fix issues noticed in review
fb998bd6 Merge pull request #44 from geekwright/pr40
3f60781a Merging review changes and conflict resolutions for PR #40
537c4df4 Revert some changes which just complicated things
c2bbc9f5 Revert changes to Smarty
f7aa706a Revert changes to XMF
0d3dd309 Merge pull request #42 from geekwright/fix41
153c67a7 Not respecting $force
42a6cbef Unnecessary Double Quotes, unnecessary Parentheses
95fc9bf6 Merge pull request #39 from geekwright/rc2tweaks
729abecb Deprecating some questionable stuff
a99330a8 Deprecating ArtObject and ArtObjectHandler
91d1383f Minor cleanup in installer
ace18e57 Remove mysql v4 checks
f5430bb6 Updated template
39bdd93e Merge pull request #38 from geekwright/relpreprc2
c97c514f Add missing files
7bc5969d Version bump
1b7cbec3 Remove a couple of database setup defaults
c5216ac1 Small fixes
cf47bed0 Merge pull request #37 from geekwright/nginx
7a0e4af0 Merge pull request #36 from geekwright/phprefs
f22e5013 Merge pull request #35 from geekwright/xbfixes
0a012a39 underscoring this issue
98ca1b5d Fix bad constructor call
1731b26a Merge pull request #34 from geekwright/php4shim
2eedfc9e Minor verbage change in installer
d1eefb8a Clean up some stray object assign by reference
806de4eb Add missing scripts
0aa56fd3 Compatibility shim for XoopsObject
b7dc4eff Merge pull request #33 from geekwright/smartypatch
53d99f39 Smarty patch
1eebe33c Merge pull request #32 from geekwright/mysqlireview
2c0fc16e Review of mysqli calls
d6fc8e4f Merge pull request #31 from geekwright/minphp
6091462b Merge pull request #30 from geekwright/fetchfalse
6a8f308f Fix min_php specifications
7f97e9c9 Return false on end of data
9d6e3bb3 Merge pull request #29 from geekwright/relpreprc1
57cd6261 Merge pull request #28 from geekwright/copy2016
08270ade Update change log and version
4eb577c5 Update copyright date
9e18808c Update copyright date
50ea0ec4 Update copyright date
d1a09d1d Update copyright date
3552859c Update copyright date
9a36210c Merge pull request #24 from geekwright/xmfrc1
6c213472 Update to XMF RC2
5e69583c Merge pull request #27 from mambax7/master
4c5ceb4d making CONTRIBUTING.md generic
54b5a558 Merge pull request #26 from mambax7/master
be0a27ed add CONTRIBUTING.md
8c8a1e80 Merge pull request #25 from mambax7/master
455d4b59 add Scrutinizer in README.md
e21afb6d Update to XMF RC1
2b2ff7d3 Merge pull request #23 from geekwright/relprep1
2549f5bc Remove dead code
e8c62bad Error on upgrade check
673d0036 Add needed classes
bdf85305 Release Prep
0f0aa389 Merge pull request #22 from geekwright/chmodminusx
cbd86821 Remove execute permissions - chmod -x all files
96cd4f88 Merge pull request #21 from geekwright/fixes160225
ba468c35 Merge pull request #20 from geekwright/smartyrevert
a3ba9b52 Update Xmf and some fixes
e623149f Restore Smarty to vendor version 2.6.28
4d26d1d4 Merge pull request #19 from geekwright/userutil2
8f78e173 Fix missing paren
64ccbba0 Merge pull request #18 from geekwright/ipv62
274dc711 Fix password verify calls
593c5919 Update password hash in profile module
20cd4850 IPv6 aware changes
9886fce8 Merge pull request #17 from geekwright/ipfixes
d077d810 Small fix
042e09c5 Inheritance issues
4eb3e9d9 Session IPv6
b8064994 Ignore generated keys
3ae590c5 By-reference cleanups
7079a37e IPv6 aware for protector and online handling
e63420c7 Update XMF
95449f6f Patch from zyspec
21de4fd1 static, object references
aef3c70e cosmetics required versions update
876618d3 PHPMailer 5.2.14
d192b882 Cosmetics
da282f49 Merge pull request #16 from geekwright/fix160212
09120054 Fixes for fixes
fead662d More assign by reference issues
7be60a73 Re-enable strict errors
7ec0f22e Cleanup
7e01a364 Review of kernel classes
9ecf2c93 Install rename
2c3010fd Merge pull request #15 from geekwright/restoretemplates
4aad70d5 Fix some type checking issues
9445535c Restore the .html templates in system module
ae4e74cf Merge pull request #14 from geekwright/fixes14
4aafc9af Remove eregi() uses
24bf5201 Merge pull request #13 from geekwright/passwdxmf
425adc91 XMF and other supporting libraries
76569cc9 Password updates
0d93598f cosmetics
118f9800 cosmetics
20e79c5a Merge pull request #12 from geekwright/refs
afe73506 Small fixes
94d73053 Eliminate reference operators
205bcb33 Merge remote-tracking branch 'upstream/master'
e15d8365 better type-safe comparison
a1d39e7a update phpThumb
bccdb354 Cosmetics
b9eb5241 License link update
d179191c better type-safe comparison
3871f87f update phpThumb
db732e35 Cosmetics
d52fe612 Merge pull request #10 from geekwright/upgrade
f97718bc Upgrade Pass
81027f51 version adjustment
8f5677ae cosmetics
a149ac2b Merge pull request #9 from geekwright/nophp4c
6ad3c06c Eliminate PHP 4 style constructor
f2345a69 Merge pull request #8 from geekwright/jp
0d52cfa5 Remove debug code
0258e9ba Add token to users page
23cd0ece cosmetics
4762dc19 Updating changelog.250.txt
5dfcf719 Merge branch 'master' of https://github.com/mambax7/x258
ae69be9c added image sort order in TinyMCE plugin
3c023a45 Merge pull request #7 from geekwright/userform
f8714760 Correct issue with FormSelectUser
80175f95 Merge pull request #6 from geekwright/youtube
64546f53 Youtube handling enhancements suggested by xd9527
3f688dd9 Merge pull request #5 from geekwright/instfix
86e7f972 Merge pull request #4 from geekwright/ignore
7881b9f0 A few fixes to install
34a9d247 Ignore some generated files
5db9dc45 Merge pull request #3 from geekwright/master
0a100079 Line ending issues
71ea4bf6 enabling both theme.tpl and theme.html
4e305a05 PSR CamelCase changes
f88f0a74 changes to enable updates in ModuleInstaller
ca27e84b fix for mailer
a9ea1d95 Bootstrap 3.3.6
4300934a fixing tabs
594eca98 updates
e659ef70 HTML Purifier 4.7.0
1009baf3 HTML Purifier 4.7.0
38ce0bb0 small cleanups
a87142e7 template updates
9daf3db7 reference mismatches
d2f2f624 Sync with SVN 13166
e885ddba Sync with SVN 13166
fd114f09 changed minimum image sizes in Image Manager
bcffc571 Clean-up
74485f75 PHPMailer 5.2.13
2106fea6 name change
1d481a1c fix profile/search (cesag/slider84)
35130352 added static
8e7be42e Invalid argument supplied for foreach
750f425f phpThumb 1.7.14-201507071049
4bd52548 Switch from .html to .tpl for templates
c7f03fcc fix for class name in function definition
1813666f fixed bug in __construct
7b0724d3 .tpl extension for Help
719f6ce5 formatting
d084611e Formatting
09ac246b formatting
3de6e824 add salt
52529363 Merge branch 'master' of https://github.com/mambax7/x258
62f6f91d update
5d3644ac Update README.md
de2a7bba Update README.md
ef6f4f3a Logical operators
3ec3812e Logical operators
6d5aeea2 remove exit()
7cee8f77 Cleanup
8a72e3e0 updates
3e498575 Merge branch 'master' of https://github.com/mambax7/x258
af2c20f6 Initial commit
d16fc9e1 add option to set height of Themes block
86d38946 moved "count($tagsArray)" and "count($attrArray)" outside of FOR loop
d80dc9f0 added .tpl option for module Help files
0e3aba78 adding "strong { font-weight: bold; }" to reset.css
a9e9692c jGrowl 1.4.3
f2b2f0da TCPDF 6.2.11
57a2739f new language define _AM_SYSTEM_USERS_ACTIVATE
957e0962 XOOPS Theme Complexity - Final Release
c5b97071 XOOPS Theme Complexity - Final Release
17f76b5a XOOPS Theme Complexity - Final Release
f5abf3b6 Signed 2.2 Release Candidate
02c1286a cosmetic changes
50664687 adding TDMLinks
c06954f2 Sexting XOOPS 2.5 Theme - Release Edition First Final Release Release
57b4d292 Sexting theme with Full Meta and gradient exporation version 1.2.8
f54b4e47 Signed 2.1.9 Final Major - Puesdo.legal.binding.v2
3371265b Signed 2.1.9 Final Major - Puesdo.legal.binding.v2
dcf6283d Signed 2.1.9 Final Major - Puesdo.legal.binding.v2
6bd54db3 Sexting - First Printcd ../../XoopsThemes/sexting/ Gradients Wowcd ../../XoopsThemes/sexting/
a4574e8c Signed 2.1.9 - The final signed down! Dod oodoo doood oo didoo doocd php-dev/respository/xoops-svn/XoopsModules/signed/
f120ad3e adding release to archives for historical purposes
9d28b8b4 Please Email Ticketer -- Prealpa2
3fe20fd0 Please Email Ticketer -- Prealpa2
7ac16fb1 Please Email Ticketer -- Prealpa2
4a4454dd added missing branch for archive purposes
db76331d fixing name problem
289d7d3a adding 2.5.7.1 to Archives
7c7df561 adding 2.5.7 to Archives
fdee48e6 adding 2.5.7-rc1 to Archives
dbb11fe4 adding 2.5.7-beta1 to Archives
4f974bfe adding 2.5.6 to Archives
8f23c0e9 adding 2.5.6-rc1 to Archives
76cb9447 adding 2.5.5-rc to Archives
0afbb024 adding 2.5.5 to Archives
2b60ebd8 adding 2.5.5-beta to Archives
8bd2d07c adding 2.5.4 to Archives
6eb52c62 adding 2.5.4-beta to Archives
e5789a0d adding 2.5.3 to Archives
f35a68af adding 2.5.2 to Archives
f1cba836 adding 2.5.2-rc to Archives
09230c9a adding 2.5.1 to Archives
f820d71f adding 2.5.1-rc to Archives
91868a11 adding 2.5.1a to Archives
ecbb19c9 adding 2.5.0 to Archives
96b531e7 adding 2.5.0-rc2 to Archives
96b9d8d8 adding 2.5.0-rc to Archives
530334b7 adding 2.5.0-beta to Archives
4080f0a4 adding 2.4.5-rc3 to Archives
1fe7e81c adding 2.4.5-rc2 to Archives
9585a8ef adding 2.4.5 to Archives
ae369c23 adding 2.4.0-beta1 to Archives
f236f4e4 adding 2.4.0 to Archives
f10fc72c adding 2.3.3 to Archives
1f964e88 
ee909b04 adding 2.3.0 to Archives
c8500d5c Fixed small bugs Refactoring Updated
7c77994c forking luciorota's version
bd646ab7 Intial planning for Email Ticketter with secondary raise to MantisBT for second level support and faults!
91c03b58 added possibility to include full icon URL in module's menu.php
7a48e4db Replacing floatval() with (float)
62f65e64 Replacing intval() with (int)
e1d7d0cd Merged modifies (With Goffy)
481d676c Fix Refacoring More Files Updated
f3f73b1d Added morefiles.php and more for extra custom files Updated
a9a2fc47 Fix added more files
2708dc19 Added template list Added more fields in database Refactoring Fixed seceral bugs Updated existing files and renamed
37cee9bf Pre-release (dev-in-progress) for new signed 2.07 -- trunk
a5d489ff cleaning for new signed 2.07 -- trunk
059ec0d2 Updating XOOPS copyright to 2015
122e0533 Update: XOOPS links point to http://xoops.org
05c56fbe Added jquery ui tabs
e9f26171 Fix
a7ae1c6d Fixed conflict autoload.php
fa3b550b Addeds & fix
4e47fb9c Added alpha 3
3ae57610 Alpha 3
cced7ba7 Fixed display toggle in modules, table & fields list Added table_index filed to insert code in user/index.php Needed a fresh installation
0961bcf5 Refactoring Added & deleted superflues code
ec7a7677 Major changes in lists and language
951d2728 Fix missing value type sql/SqlFile.php XML Rss
d76251ae 1.02 RC-1 for XOOPS 2.5.7.1 (see changelog.txt)
b3a6f760 1.02 RC-1 for XOOPS 2.5.7.1 (see changelog.txt)
f63a218f - Fix url logo - Fix language groups - Fix array file notifications
0e3d7357 Fixed field parent & logos/tdmxoops_logo.gif
ef239486 Fixed bugs!
7c72ab9d Updating jQuery to 1.11.3, UI to 1.11.4
6c7d0385 Added settings.php for customized module informazions
424ad7be Adding module translations (from Žirafka)
a6c8c46b Adding 2.5.7.1 from Zirafka
361e8ef7 Jquery ui tab fixed Added and updated
8beda7e0 Fixings and updates
e92b23df Added format date strings
e2cc524b Fixings and updates
6568f06e Deleted unnecessaries functions in classes
ef0385be Minor adjustments to classes Added to other files on the user side
df9f9e04 Updated In list you see name of category or topic
c17d055c Added field table_solename in tables to distinguish the singular name from the plural to the singular name.
0e401d12 Autoloader for all classes Now is not needed to add link in common.php file to the new classes
2f38a1f2 Updated Fixed minor bugs
1137445c Updated Fixed bugs Database new use Engine=InnoDB Deleted field_numb: This field can be called from the table_nbfield in tables Modified includes with __DIR__
16d0ffeb Updated Fixed bugs
b749ad8b Merged to alpha 2
5922062b Now you can order tables from the list
6400a236 Updated Fixed bugs
8f868aa7 Added Chechbox all controll
fb75a0b6 fixed
26118dff Fixed table_order
167b322a Sortable tables for order menu and icons in admin index.php
3ba4a7c3 Fixed issues to creaction folders & files
d8839892 Changed elements index, added more elements
451022e2 Updated Fixed bugs for field main Work in progress
17048891 Updated Fixed bugs
035cecfa Personalized checkbox and radio button form
ecf5293d Updated
3cba8629 Moving Adminbar to the top
8c7acb18 Rename from /spanishutf8 to /spanish
f5afd06d Fixed path for avatars list
32a8c5c5 Updated changed extension .html to .tpl of all admin themes
cbed59e5 Updated changed extension .html to .tpl of all admin themes
19611276 2.5.7.1 files (culex)
1eb84fa1 branch for 2.5.7.1
fb713398 revert changes
65fbfd35 files for 2.5.7.1 (culex)
57147fe9 
ddc22349 
13c7dcf1 
5c3958a1 Updated Fixed bugs default admin theme
b5f00a2e Updated Fixed bugs
f8058812 Updated Fixed bugs default admin theme
ab9a2ee9 XOOPS 2.5.7.1: Russian translate added
fc3dbb9e Updated Fixed bugs Fixed responsive for mobiles
e4837185 Updated for adaptive assets folder
8b45cae7 Updated Fixed bugs
3330cad6 Updated
98823ff1 Updated profile module with .tpl extension templates Captcha and Recaptcha Modificed admin protector for bootstrap Deleted .html templates files
d527d453 Added CPanel bootnewage theme for xoops 2.5.8 based on bootstrap alpha version Added variables in class/theme.php
0aabc5d2 Added more jquery ui themes
bf514b30 
f619b17f Added string path of theme
29bd7159 Updated Fixed bugs
a82eb769 Updated Fixed bugs
6abd2e17 Added Breadcrumbs class & smarty plugin
8164de83 New admin theme bootstrap responsive ( dashboard )
5b5ed6ed Updated & added assets folder in default admin theme Modificed class/theme.php, added assets folder for admin system themes
c22fb451 * require XOOPS 2.5.7+ & PHP 5.3.7+ * renamed all template extensions from .html to .tpl * added XOOPS & PHP version checks on install/update * moved install/uninstall/upgrade functions to ./include/action.module.php * moved images and css directories to ./assets * added loading stylesheet in block * added XoopspartnersConstants class for constant definitions * added block config to be able to trim title length * added _MI_XPARTNERS_INCADMIN define, missing in previous BETA * refactored /admin/main.php from seperate functions to switch statement * general code cleanup for PSRx
01cc0a04 Adding changes from Zyspec
36fa948f * minor PSRx naming convention updates * minor XOOPS variable name updates for consistency * minor template CSS changes
f2f9a575 * change to use XOOPS jquery js from Frameworks * cleaned up skins directories to use common structure (/css, /images) * fixed bug in rendering menucss in block * added tw_red skin, inadvertently removed in previous release * added mid KEY for SQL to improve performance for common queries * normalized icons16 & icons32 definitions similar to other XOOPS modules * more code cleanup
a91f406b Updated form elements to bootstrap 3
b4167ce7 Updated to bootstrap profile and pm modules
1dbe178b 1 31_ALPHA6_zyspec
0cc11597 Setting up branch for Zyspec
62b24b21 Setting up branch for Zyspec
9ebddbb6 1.11 RC3
70273424 1.40 RC1
4e1d0c4b Adding SmartFAQ
bce0aa1b fixed bug in install, uninstall, update move all .tpl to templates folder
6619a7bf 2.33 RC 1
9e93c5e4 2.38 RC 1
d05f6dfc 4.33 RC8
433ed6ed 1.02 RC1
244c1a34 Minified xoops css
d71d02b0 Adding hack for SystemInfo link (pre-Alpha, proof of concept)
ee0505fc Pagination with bootstrap 3.3.2 in ul tag and not in div tag
8f43eead Updated
0d922e69 update PHPMailer update html2text fixed bug in text decoding
4d2f7dc6 code cleanup in templates/wfdownloads_singlefile.tpl update Magnific Plugin move Magnific Plugin from php script to template
1d39fc31 Updated cpanel theme Added and modfied more files
1322596a Adaptive cpanel theme in admin profile module
aace484c Updated Added calendar icon for datepcker
f8825d61 Added minified css xnewage.min.css file Modifieds
95faaa60 Updated Added and modifed more files
f24bcb37 New cpanel theme xnewage in beta tester Modified system module and core files
a102c359 fix "setExtra" in moduleadmin.php, Admin buttons can now use "onClick" assignments (mamba)
e5ae6352 added: sort downloads by weight
1278f986 changed method of loading XoopsObjectTree in category blocks
8ef520ad create 2 category blocks
243db0af Updated jquery ui libraries to version 1.10.4 Minimized For compatibility to bootstrap 3.3.1
536412e3 Updating changelog
7e8dcea3 reverting Timgno's jQuery updates
6cc891ef Updated jquery ui libraries to version 1.10.4 Minimized
f4eeb13b Updated jquery libraries to version 1.11.2 For developement and compressed
6bd908f9 branch copy of current trunk to create categories block
9c5125de Added side footer blocks template files for customization
ecc3e09b Addes assets folder path
787f02e1 xoosp 2.5.7.1 compatible change directory structure improved subscr form standardize code code cleanup
beb5ea92 xoops 2.5.7.1 compatible change directory structure code cleanup fixed bug when using XoopsFormSelectUser form element
a3529d18 changed directory structure xoops 2.5.7.1 compatible fixed bug in class/error.php
2de33ed5 new branch
bf27d30d new branch
ebdc68f0 Xortify XOOPS 2.5 - Client 5.00
7553dc37 * minor code cleanup * utilize XoopsFilterInput in ContactContact->contactSendMail() * add missing define(s) _MD_CONTACT_MSG_NOVALIDEMAIL2, _MD_CONTACT_MSG_EMAILMATCHFAILURE * removed deprecated methods from Contact class (getInsertId, contactCleanVars) * moved XoopsRequest loading to ./admin/admin_header.php * changed defines from _MD_CONTACT_MES_xxx to _MD_CONTACT_MSG_xxx for consistency
1924ca16 * fixed syntax error in ./ajax.php (extra apostrophy) * minor code cleanup for PSRx naming nomenclature
f8aaaf37 removed unnecessary exit() statement after redirect_header() calls
9df8dc3f patch: from Xoops 2.5.6 XoopsFormSelectUser is not compatible with some old modules like xNews
584d8d90 Branching Zyspec's modules
952c1b4f -create copy of trunk for new branch
dbed904d - added breadcrumb display to top of index page - improved index.tpl rendering - changed to allow HTML in quote entry (XoopsRequest::getText) in admin - moved some hard coded language strings from templates to language files - fixed typo in block text length calculation - fixed bug where 'item_tag' wasn't correctly assigned to template in /class/quotes.php - fixed bug in install script PHP version checking - fixed bug where language file wasn't being loaded in install script
d2a02d3f 
68600251 * Improved module update script to add some error reporting on failure(s) * Fixed bug in subdirectory removal script on update * added KEY(s) to dB table to improve search performance * changed ContactContactHandler->infoProcessing() to use XoopsRequest * sort departments alphabetically when displayed on contact form * Replaced "Reply" text in admin (./admin/contact.php) with icons * Changed "Submitter" information in admin to be able to email from contact page * Improved ContactContact->contactSendMail() method to improve anti-spam score * Minor source code formatting cleanup
b45c6de0 I think this should be included in the folder xoops_lib / Frameworks from xoops 2.5.8 to be lighter on the many issues that we have in the folder themes.
074e47e5 Update to PHPMailer 5.2.9, remove addrFormat override in XoopsMultiMailer
812637ee Added field item file template for sortable field
62e8a767 Updated  - Fixed bugs
751f6614 Added jquery ui libraries in default admin theme system
a2e5cbbd Code cleanup, adding XoopsRequest in couple of places
3b3c9f23 Adding changes from Zyspec
2fe3effa update for cumulus block
85437998 placed in incorrect directory
d2558c2c relocate to proper branches directory
d1f2cf79 * fixed bug where editing menu item put item in 1st menu * fixed reference to JS files since moved to /assets * fixed/improve menu HTML rendering so it can validate * moved ./skins/clean/template.tpl to ./skins/clean/templates subdirectory * moved ./skins/breadcrumb/template.tpl to ./skins/breadcrumb/templates subdirectory * updated from dirname(__FILE__) to PHP 5.3 __DIR__ * updated jquery to v1.11.2 * updated hoverIntent to v1.81 * updated supersubs to v3.0 * updated superfish to v1.7.5 * general code cleanup
fc34be40 newmylinks with tdmcreate v.1.37
f127e244 branches/mylinks timgno v.3.01
55b0a302 branches/timgno xoopstube v.1.12
dfde2814 xBootstrap compatibility
3a275643 Signed 1.15 - Minor Changes to Major Final
71dcac72 Signed 1.15 - Minor Changes to Major Final
7b1c0d2b Signed 1.15 - Minor Changes to Major Final
831fdee4 Fixing a bug (geekwright)
f1baebbb removing wrong "/"
e901da57 Xortify 4.99.1 for XOOPS 2.5 -- Minor Fixes, new ban window!
0a600cf9 database final refactorisation - final model 1.1 SQL92
ae19f958 Final Copy of Extreme Super Closest XOOPS 2.5 Theme!
78d393ae Final Copy of Extreme Super Closest XOOPS 2.5 Theme!
b6bfeb7f Final Copy of Extreme Super Closest XOOPS 2.5 Theme!
43c7aacd Final Copy of Extreme Super Closest XOOPS 2.5 Theme!
fb8f122c Final Copy of Extreme Super Closest XOOPS 2.5 Theme!
07835147 Commenting out breadcrumb.php, since it was deleted
a216cd19 fixing wrong directory
0284fd04 Cosmetic changes
dba7daab Add loading XoopsRequest
49b4e1c6 fixed small error
06a23897 use xoops_load
1a453d82 Xortify.com & Sexting XOOPS Themes for 2.5
b5a9f199 
978cba58 
eb6c2f34 - include 3 files missing from previous commit
e7c54cff - sanitized various ids passed into method/functions to reduce security vulnerability - added module preference setting to remove admin impressions and clicks from banner count - added module preference setting to set maximum file upload size - added ability to change banner status from Admin banner edit page - added banner and image directory removal on uninstall - added additional banner categories for industry standard size banners - moved images, css, & js to assets subdirectory - moved mimetypes.inc.php from ./class to ./include directory where it belongs - converted dirname(__FILE__) to __DIR__ - removed files for permissions & block management (functionality previously removed), uses XOOPS standard methods - removed/commented english language defines that are no longer used - removed RwbannerAbout class as it's no longer used. Module now uses XOOPS standard method to display module info - fixed bug if installed in directory other than default preload could not determine if module is active - re-arranged admin code and tabs to make operations more intuitive - updated Admin help to reflect operation & usage changes - minor code & comment cleanup to improve performance & maintainability
11431188 - include file missing from previous commit
63e7a909 - updated admin footer to be similar to other modules - changed to use exit() instead of die() for consistency - use XoopsRequest instead of custom function
88ce299d improved subscr filter in admin side
6ddce2c8 Fixed PHP version compare on installation
b1932d90 Fixed PHP version check on install
644b1686 min req xoops 2.5.7.1 use XoopsRequest class
34a0c456 o Upgraded min Xoops version to Xoops 2.5.7.1. o Upgraded to Xoops 2.5.7 coding standard. o Upgraded to Xoops 2.5.7 directory structure. ! Fix strtotime bug ! Fix php 5.3+ bugs and remove some deprecates
9cc1a84d remove old work
115fa971 administrator can run the cron.php script from administrative tasks page update help files adding new features
0b1cd346 Beta 4 (see changelog)
df2c1741 fixed huge amount of small bugs standardize code templatize admin side improved admin size options
e39bda29 Identity API for Session Handling
f61ff1f5 Subjected API for Conversion to XoopsAPI -- Xoops 2.x
cf3fc2b1 Subjected API for Conversion to XoopsAPI -- Xoops 2.x
60846776 + minor code cleanup   * moved some hardcoded language strings to language files   * file formatting (tabs-> 4 spaces)   * improved PSRx coding compliance - cannot completely comply without     breaking plugins distributed with other modules   * replaced dirname(__FILE__) with __DIR__ + eliminated /Frameworks/art/functions.admin.php alternative since should be present in all versions of XOOPS >= 2.5.7 + utilize XoopsRequest for input vars + normalized Frameworks icons directory to be similar to other XOOPS modules + deprecated XoopsFormTag (modules should not create classes beginning with 'Xoops'). Created class TagFormTag to replace it. + merged Cumulus module for flash cloud block - Cumulus module no longer needed + fixed bug in display limit in .list.tag.php for both list and cloud + removed support for MySQL < 4.1.0 + added TagConstant class to improve code maintainability + improved HTML rendering (admin side) + modified module defines naming to standardize with other XOOPS modules + added randomquote ./plugin
c0dc4c11 Comitting(ed):: Self Signed Version 1.14 ~ MINOR FINAL RELEASE
1c7486cf Comitting(ed):: Self Signed Version 1.14 ~ MINOR FINAL RELEASE
13a1a4fd Self Signed Version 1.14 ~ MINOR FINAL RELEASE
72a77702 Fixing Trunk
9a0ba188 Fixing Trunk
03c86748 Fixing Trunk
da39b943 Fixing Trunk
5c87682b include files missing in previous commit for v2.1 Beta 1
bb0a237c - Require PHP 5.3 & XOOPS 2.5.7+ - Significant security improvements for all vars passed from forms - Replaced cleanVarsRandomquote with XoopsRequest - removed need for ./include/config.php - fixed typo in ./docs/licencs file name to license.txt - General code (& comment) cleanup - added module search capability - added support for XOOPS Tag module - added date quote was created to database - removed "day" block - can be accomplished setting quote cache
046b4454 Updated  - Fixed bugs
fd894b88 Added folders for horoscope module in next time
98c3e256 Added folders for budget module in next time
94d37e0e Added folders for estimate module in next time
e5244298 Added folders for invoices module in next time
7cfbf14f ! fixed some database fields length + support multiple test emails * templatize some admin side page + use DateTime object instead of strtotime() function + added xnewsletter_randomLipsum() function (in_progress)
1a4e4b0e Xoops 2.5.7.1+ compatibility removed request.php
9867819e Added new folder version 1.91 Alpha 1
4ec3f87d Added new classes file for autoload fixed bugs
764a22bf To Team Core
990a7074 Adding 2.5.7.1 to /releases
a472edef Adding 2.5.7.1 patch
c10bb23d Creating base for 2.5.7.1
c02a0836 - update to latest mamba work - fixed: use DateTime object instead of strtotime function (luciorota)
1b93c689 Use native XoopsRequest
2912b067 - commit files missing in previous commit
969a8190 - Added ability to require visitors to enter email 2X on form - Added features page to Admin help - Added date select to Admin log form - Added ability to include original message body in Admin Reply - Added mail body formatter to limit line lengths per RFC2822 - Added confirm when pruning contacts in Admin panel - Added Waiting module support - Added Stats block - Added ContactFormValidatedInput class using HTML5 types - Restored capability for Admin to enter the thank you message - Removed ./icons folder/files - admin uses XOOPS module icons - Removed ./docs/readme.html - no longer used and out of date - Fixed bug: Contact->contactPlatform() always returned "Web" - Fixed bug: Contact->contactType() always returned "Contact" - Fixed bug: Contact->infoProcessing always used $_POST - Fixed bug: Admin Prune date parser - Fixed bug: remove slashes from email subject & body - Fixed HTML table missing row tags in Admin - Changed clicking contact URL in Admin to open in _blank window - Changed ./admin/tools.php to prune.php to reflect function - Changed update script to remove "old" directories (css, images, js) - Changed retrieving configs, eliminating deprecated XOOPS functions - Updated documentation files (changelog.txt, readme.txt) - Minor code cleanup
508c6ed6 Signed 1.13 (Trunk) - Final Minor Release Versioning
00556b62 Signed 1.13 - Final Minor Release Versioning
a1251df5 fixes for Footer blocks, incl. Default theme
6b8e9381 Updated System Foter Blocks
b90c69ae Added classes for autoLoader Fixed bugs Commented creation image file blank.gif in include/install.php for module repository
aba285d1 fix for mismatched parameters in -moz-linear-gradient(
46f868a0 Alpha 5 (work in progress)
2d1809cc Code Cleanup
9dce9446 replacing dirname(__FILE__), replacing WfdownloadsRequest with XoopsRequest
5a32f175 final release of XOOPS Signed 1.12
c5f9eca9 final release of XOOPS Signed 1.12
5c73e6a8 Fix #1298
ca857f23 first English translation with Google
98282d49 copying French files to English
84f4fa50 fixing table names (missing underscore)
d806e28b adjusting folder structure
a9054e16 adjusting folder structure
0f742127 adding branch
5ac03d0b Adding Vide by Black_beard
5f11baac Fix for #1297
bc61e5ab Fix for #1296
64539ce1 Added spinner.icon (timgno)
fa801fbe Cosmetic changes/updates (mamba)
117b88a7 Adding Footer blocks (http://xoops.org/modules/newbb/viewtopic.php?topic_id=76816), updating xBootstrap
d406b2f2 Add X-Frame-Options header to prevent clickjacking in modern browsers. By default set to 'sameorigin', but can be overridden by setting desired value in 'xFrameOptions' key in xoopsconfig. Resolves #1295
cd4c6c3b 
597d0d5c 
ae0d2d60 branch to update to Xoops 2.5.7. standards
fe44d8db Some code cleanup for private messages
be0e4a40 Remove extra parenthesis introduced in last change
ed0e995d updating for XOOPS 2.5.7 (work in progress)
0a2d891f Adding extension to the readme file
6bdff7f7 Adding gnavi module
5de033df Adding eGuide module
4b608372 updates for XOOPS 2.5.7 (work in progress)
9723cdff Temporarily re-enable calling update from xoops_version until update block routine can be re-factored.
ef7ebb02 minor updates
0d97abd3 adding missing "/" (zyspec)
6a571e0e Adding Zyspec's 0.98 RC1
cb9e9a54 
a6467b6f Adding branch for Zyspec
1cec6ab1 RC1 + delete multiple reports
3b874782 rename error.php to index.php for compatibility in case of module upgrade
a0711f9f added import from fmContent (zyspec)
35eb3c4f moving files to /publisher folder
1469343d Adding /publisher folder
37e7f469 - creating fmcontent import functionality
672af529 - incorrectly created folder for branch
1e2a01d4 - creating fmcontent import functionality
2bcf801f Prelimiary still in developer too you Harold!
665d4768 add new files standardize module fixed bug when reporting eror
f5796a8f remove unused files
61a8caba standardize
26dffacc removing shadow box around icons for new topics/forums
8b9a7cc0 XOOPS Rooms Chatting IRC Style Chat all in PHP
b63f4c81 Cosmetic changes
ddef5a9c Update Releases from labs.coop ~ Chronolabs Cooperative
299262de code clean-up for consistency
b820309f Merging eForms into xForms 2.0 Alpha 1
a7efd905 fixed bug when updating from xNewsletter to xnewsletter on linux/unix servers fixed bug in subscr form (goffy)
d25c22a4 branch for Bosco
dc3902cc branch for Bosco
23150534 Removing extra "the" (thanks Cesag!)
38efafc8 code formatting, updated changelog
4f4238ce redesigned archives section to have months horizontally, added count of articles
cf59a3c0 started conversion to PSR-2 coding
2af190a1 updated makepdf.php file for the latest TCPDF
4c648f93 replaced PublisherRequest with calls to XoopsRequest
90fd4766 replaced $i++ with ++$i for consistency/performance
a491e095 Upgrade from montuy337513 (alias black_beard)
e57fa764 Adding /themes/xbootstrap/modules with renamed templates to .tpl
b11722f6 replaced "dirname(__FILE__)" with "__DIR__"
b63b585c use screenshots field instead of screenshot, screentshot1, ... IN PROGRESS
8a926c20 convert to Unix file format 'cause this is the standard for Xoops files standardize var names minor bug fixes add method to protocol handler to create a protocol using only 1 line of code (IN PROGRESS) fixed db bug
6288fa9c fixed bug in var names, standardize var names fixed bug in db uses screenshots filed and improved update function (task: add possibility to have all the screenshots you wish IN PROGRESS) changed some configuration default values
51b99b5b reverting couple of $i++ conversions
450d050d converting $i++ to ++$i for improved performance
802b662d $GLOBALS['...] instead of global $... IN PROGRESS
53e5f44c WIN to UNIX file format use $GLOBALS[...] instead of global $... improve protocol system (in progress)
75cd7b0e fixing a bug (wrong name of Criteria)
4450e027 
e9328509 remove 'cause will branch from mamba work
808a8c2c this is my latest version, start from here
8e3e0994 Updating some changes from Luciorota
48464e65 Clean-up of assets, PSR-2, __DIR__, English updates, centering icons in Actions (work in progress)
81289b1e Adding changes by Luciorota
8a57a107 fixed bugs code standardization
a3026cf5 fixed bug
dec67140 standardize code fixed bug in module update procedure
13462189 standardize code use absolute path/url where possible
2bb63145 allowing for "https" URL in YouTube links in Textsanitizer
991d00a9 Fixing English typo
78d62e1c replacing relative links with absolute links
ced652f4 fixing links to module after its installation
a9657598 fixing Help issues
37e1556f added extgallery 1.11 (by Skyppyno)
f6ed0450 standardize: use include_once dirname(__FILE__)
65eba108 replaced dirname(__FILE__) with __DIR__
3c4b3a6e Beta 3, work in progress. See changelog.txt
d3b3a133 deleting TCPDF library from /Frameworks
67c01b6d adding TDMDownloads
c40d165f Commented out unneeded lines
e0de1023 commit my latest work
2f9042dd delete wfdownloads from my branch
126a4736 module folder in lowercase added some features, listed in changelog.txt IMO module is ready for beta testing...
82607d7e 
c372fcb1 Adding new version (work in progress)
27ff2359 Deleting old version
deef7b69 branch for Cesag
2e918e32 Adding branch for Cesag
baf4ae7e making function getTimeFormatDesc() static
2096736c suppression des fichiers .db inutiles et qui prennent de la place
bc4ed682 Suppression du dossier upload qui n'est pas utile dans cette version puisque créé automatiquement.
3edd72ff ========================================================= 2014-08-08 1.10 Final =========================================================
95fa73a7 removing unneeded % sign
91c7dd4f updates, work in progress
89a80a54 Upgrade to version RC9 (cesag)
4562bf8f Updated links to .tpl files (cesag)
d2f2dfb6 renamed forgotten .html Smarty templates to .tpl (cesag)
48dba46c reverting to revision 1250 (cesag)
3c3c100c renamed forgotten .html Smarty templates to .tpl (cesag)
96baa021 renamed .htaccess to _htaccess to avoid an error in the installation (cesag)
f5ac8448 deleting non english language files (cesag)
9fdaa4fb Adding Cesag branch
c16687df 
07281553 
7b5c13dc Updating copyrights, adding couple of "isset" checks
00045852 Adding couple of "isset" checks
3f90fe91 Upgrading to TinyMCE 4.1.4
8bfad6a1 MyPoints 1.02 Beta 1 English corrections (cesag).
72c82fcc deleting other obsolete bookmarks (cesag)
fbb49c21 Adding define for Traffic Rank, Web Archive and Google SiteSearch (cesag)
2dc8432f replaced dirname(__FILE__) with __DIR__
aae02ed0 fixed bugs: wrong url for images folder, renamed html links to tpl links, replacing (!result) with (!$result), deleting obsolete bookmarks (cesag)
0ba73404  - Control selectbox item 1 is done by javascript instead of using $action, we use two single quotes as string
63139e59  - Poured code @goffy since the control is done by javascript instead of using 0, we use two single quotes as string
400c8727  - A little bug
b3756672  - Fixed bugs (goffy/timgno)
5716a60b  - Updated  - Default language is english
b44331ba  - Updated
8836e221 1.02 Beta 1: - added tab for bulk Updates and patches for Core files, required for "bulk Update" to work, located in /extras
665f3d79 Updating changelog
77f34801 Adding language constants for Prefix Manager
65302ead XoopsRequest::getArray($name, $default = array(), $hash = 'default') If $name is not an array but it should be, method converts $name in an array. This change is important when a select box contain only 1 option (eg: XoopsFormCheckBox)
64f56152 
7f8e9f36 fix folder error, it is possible that I am becoming stupid?
e4574de8 code clean up
f2032e55 
67c9023d RC2
2f096ebf Updating phpThumb to latest version
7cc7752d Sync with latest changes to geekwright/xmf on GitHub. Include vendor directory in prep for release.
e496d335 Couple more corrections
4d8202f2 Converting to UTF-8 without BOM
a101bda4 Used for system and others modules
bd65bfcb improving makepdf with language options (rgriffith)
748d7e00 adding code to .html template entries from the tpl table
d5d04e21 adding code to .html template entries from the tpl table
b48f3d09 removing unneeded % sign
4d700fd7  - Updated  - added defines class file for languages  - Still work to do
b4cb2385 Fixing typo (Timgno) fix for Update setting Template import date to 0 (timgno/mamba)
31bc5211 Fixing typo (Timgno)
4f4825b3 Fixing typo (Timgno)
aa3b317b  - Fixed bugs  - Updated  - Still work to do
2fc56fbb - Fix issues when protector 'id_forceintval' preference is enabled, that manifests as an error when deleting private messages.
da39a5c1 fix for Update setting Template import date to 0
89c7dea5 - updating some copyright notices
3c7b1905 FIX: repeated code - removed
9fa1021c  - Fixed important bugs  - Updated  - Still work to do
dce08b8b  - Reorganized for modules without tables, but always accessible in admin
a9a618bf Adding RandomQuote update
db9630e9 FIX: Missing translation definitions FIX: UTF-8-BOM file formats.
b0bf9e57 FIX: UTF-8-BOM file formats.
b218043e Adding missing files
ab25b372 FIX: Missing files
0e02e8e5 After renamed main folder, updated revision
8d4fdef8  - Renamed directory name from TDMCreate to tdmcreate
ff063f96 
ab123130  - Released 1.91 alpha 1
ed4cbf94  - Fixed bugs  - Updated  - Still work to do
fa45fac3  - Updated  - Still work to do
44b338be  - Updated  - Still work to do
b3bad16c  - Fixed bugs  - Updated  - Still work to do
6dbebbd9 
458cb32c Fix issues when protector 'id_forceintval' preference is enabled, that manifests as an error when deleting private messages.
de7b7937  - Fixed bugs  - Updated  - Still work to do
2dd77ddd remove echo (still remained from testing period)
f4b04f4f corrected check of right for reading newsletters for the list of newsletters in block
e308deb9  - Renamed directory name from TDMCreate to tdmcreate  - Fix bug
af62332e 
27995932  - Renamed directory name from TDMCreate to tdmcreate  - Fix bug
b780bcfe  - Fixed bug for constant _AM_SYSTEM_MODULES_UNINSTALL
364263e8  - Updated  - Clear unnecessary code  - Still work to do
2f262a26  - Fixed bugs  - Updated  - Added mod_blocks option  - Still work to do
c99df48c corrected check of right for reading newsletters
12731a21 corrected check of right for reading newsletters
92cf594f corrected check of right for reading newsletters
a37cc449  - Fixed bugs  - Updated  - Still work to do
1d68bdb5  - Fixed many issues for templates & admin/menu
26d31c73  - Fixed several bugs  - Still work to do
d9bac032  - Fixed bugs  - Updated  - Solved issue for directory images  - Added field element ImageList  - Refactoring variables to PSR system  - Still work to do
1803ee53  - Updated  - Still work to do
baf0ef11  - Fixed bugs  - Updated  - Still work to do
ffe1f0cd  - Fixed several bugs  - Still work to do
eccb77be  - Fixed bugs  - Updated  - Solved issue for upload file image in modules.php and tables.php - admin  - Still work to do
4144bf0f Added frameworks/moduleclasses/icons/ 32 & 16 folder default.png
701a46a9 normalizing copyright notice
d303f568 Added mod_dirname field in modules to avoid conflicts with the names of the modules and to have more freedom of choice for the names of the modules directory
86c15745 replaced "dirname(__FILE__)" with "__DIR__" (part 2)
35c9bdcd replaced "dirname(__FILE__)" with "__DIR__"
9f6eb44f Added italian language for default theme
2eedabeb  - Fixed bugs for field_name empty  - Still work to do
9b3c0f26  - Fixed bugs  - Still work to do
2bc57e73  - Fixed bugs  - Updated  - Still work to do
d141ce03  - Fixed bugs  - Updated  - Renamed tdmcreate_slogo.png to logo.png  - Personal logo copied by architecture.php file in uploads/tdmcreate/images/repository  - Still work to do
af302d98  - Fixed bugs  - Updated  - Still work to do
013ad373 fix for array issue in pm/viempmsg.php (Roby73/mamba)
b41bcb21 Definitions with a single quote and their description with double quote Updated and added missing definitions
58926d3a 
b0a73f27 
cc9daedf 
ed11fcda 
f6455db7  - Fixed bugs  - Still work to do
7ecd224a  - Fixed bugs  - Still work to do
ad1f53ae  - Fixed bugs  - Updated  - Still work to do
d14375d6  - Fixed bugs  - Still work to do
a40e4e8a  - Fixed bugs  - Still work to do
4475dd42  - solved issues for modules without tables  - fixed bugs  - still work to do
aeb3ba88 fixed disappearing link when edited
f40fef3e 
5e22e016 
b28331b0 removed prefix 'mod_' from mysql files
b519f1ee If there are any errors, try to comment out lines 80 and 82 of the file class/files/abstract.php
181e3db9 fixed bug to create a new logo because moving images folder and js folder in assets
3ee735f2  Renamed templates files
a8ef5629  - Is't necessary to uninstall and then reinstall  - The templates have changed names  - Fixed bugs
431b8647 Pleace: You can test alpha 1 ???
48ac8943 Updating docu for the correct version of tablesorter, synch with 2.5.7 changes
9715478a Updating docu for the correct version of tablesorter
99c34e9b  - Fixed bugs  - Updated  - Work in progress
00068342  - Fixed bugs  - Updated  - Work in progress
4a92cc27  - Fixed bugs  - Updated  - Work in progress
26069384 Fixing couple of bugs related to blocks, code cosmetics
9f61ff91 updated to XOOPS 2.5.7 version of jQuery UI Tabs (noo-b/mamba)
fa2eebce adding clean-up routine for old directories/files
ac641149 Adding Publisher 1.0 to SVN Release Archive
8bdb938e - updated to XOOPS 2.5.7 version of jQuery UI Tabs (noo-b/mamba)
05e1bcdc  - Fixed bugs  - Updated  - Work in progress
c4cb8a09  - Fixed bugs  - Updated  - Work in progress
5c795906  - Fixed bugs  - Updated  - Work in progress
8a60a085  - Updated  - Now we can edit and update fields  - Work in progress
4563c223 Adding 2.5.7 translation for /install/language/english/install.php
b933148c Fix path specified for include
dc0edcf7  - Updated  - Work in progress
cfc4215b For use on servers where write to root is not possible.
643dc714 Fix path specified for include
78b1cd61  - Fixed bugs  - Updated  - Deleted files and Cleared code unused or duplicated  - Work in progress
326644d1  - Updated  - Moved templates files in the appropriate subdirectory  - Work in progress
69fee515  - Updated  - Work in progress
cb65adea German Update for 2.5.7
b69338ce Setting up 2.5.8
ef1b8d5b Setting up XOOPS 2.5.8 branch, just in case, we need it...
