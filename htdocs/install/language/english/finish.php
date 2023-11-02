<?php
//
// _LANGCODE: en
// _CHARSET : UTF-8
// Translator: XOOPS Translation Team

$content .= "<h3>Your site</h3>
<p>You can now access the <a href='../index.php'>home page of your site</a>.</p>
<h3>Support</h3>
<p>Visit <a href='https://xoops.org/' rel='external'>The XOOPS Project</a></p>
<p><strong>ATTENTION :</strong> Your site currently contains the minimum functionality. 
Please visit <a href='https://xoops.org/' rel='external' title='XOOPS Web Application System'>xoops.org</a> 
to learn more about extending XOOPS to present text pages, photo galleries, forums, and more, 
with <em>modules</em> as well as customizing the look of your XOOPS with <em>themes</em>.</p>
";

$content .= "<h3>Security configuration</h3>
<p>The installer will try to configure your site for security considerations. Please double-check to make sure:
<div class='confirmMsg'>
The <em>mainfile.php</em> is readonly.<br>
Remove the folder <em>{$installer_modified}</em> (or <em>install</em> if it was not renamed automatically by the installer)  from your server.
</div>
</p>
";
