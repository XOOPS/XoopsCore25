<?php

// _LANGCODE: en
// _CHARSET : UTF-8
// Translator: XOOPS Translation Team

define('_XOOPS_SMARTY4_MIGRATION', 'XOOPS Smarty4 Migration');

define('_XOOPS_SMARTY4_SCANNER_RESULTS', 'Scanner Results');
define('_XOOPS_SMARTY4_SCANNER_RUN', 'Run Scan');
define('_XOOPS_SMARTY4_SCANNER_END', 'Exit Scanner');
define('_XOOPS_SMARTY4_SCANNER_RULE', 'Rule');
define('_XOOPS_SMARTY4_SCANNER_MATCH', 'Match');
define('_XOOPS_SMARTY4_SCANNER_FILE', 'File');
define('_XOOPS_SMARTY4_SCANNER_FIXED', 'Fix Count');
define('_XOOPS_SMARTY4_SCANNER_MANUAL_REVIEW', 'Manual review required');
define('_XOOPS_SMARTY4_SCANNER_NOT_WRITABLE', 'Not Writeable');

define('_XOOPS_SMARTY4_RESCAN_OPTIONS', 'Rescan Options');

define('_XOOPS_SMARTY4_FIX_BUTTON', 'Click the "Yes" checkbox below and then click the Run Scan button to try to automatically fix any issues found.');
define('_XOOPS_SMARTY4_SCANNER_MARK_COMPLETE', 'Mark Complete');

define('_XOOPS_SMARTY4_TEMPLATE_DIR', 'Template Directory (optional)');
define('_XOOPS_SMARTY4_TEMPLATE_EXT', 'Template Extension (optional)');


define(
    '_XOOPS_SMARTY4_SCANNER_OFFER',
    <<<'EOT'
<h3>XOOPS 2.5.12 introduces a significant change: Smarty 4</h3>

<p>Unfortunately, this change may potentially disrupt some older themes. Therefore, before proceeding with the upgrade, please ensure that you follow these steps:

<li>Run preflight.php to check for any outdated themes or module templates.</li>
<li>If any issues are identified, consult this document to understand the necessary modifications before proceeding with the upgrade.</li>
<li>After making the required changes, run preflight.php again.</li>
<li>If there are no more issues, you can begin the upgrade process.</li>
</p>
EOT,
);
