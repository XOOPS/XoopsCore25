<?php

// _LANGCODE: en
// _CHARSET : UTF-8
// Translator: XOOPS Translation Team

define(
    '_XOOPS_UPGRADE_WELCOME',
    <<<'EOT'
<h2>XOOPS Upgrader</h2>

<p>
<em>Upgrade</em> will examine this XOOPS installation and apply any needed patches to make it compatible 
with the new XOOPS code. Patches may include database changes, adding default settings for new
configuration items, file and data updates, and more.
<p>
After each patch, the upgrader will report the status, and wait for your input to continue. At the
end of the upgrade, control will pass to the system module update function.

<div class="alert alert-warning">
Once the upgrade is complete, don't forget to:
<ul class="fa-ul">
 <li><span class="fa-li fa-solid fa-folder-open"></span> delete the upgrade folder</li>
 <li><span class="fa-li fa-solid fa-arrows-rotate"></span> update any modules that have changed</li>
</div>

EOT,
);
