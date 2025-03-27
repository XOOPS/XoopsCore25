<?php
//
// _LANGCODE: en
// _CHARSET : UTF-8
// Translator: XOOPS Translation Team

$content = '
<p>
    <abbr title="eXtensible Object-Oriented Portal System">XOOPS</abbr> is an open-source
    Object-Oriented Web publishing system written in PHP. It is an ideal tool for
    developing small to large dynamic community websites, intra company portals, corporate portals, weblogs and much more.
</p>
<p>
    XOOPS is released under the terms of the
    <a href="https://www.gnu.org/licenses/gpl-2.0.html" rel="external">GNU General Public License (GPL)</a>
    version 2 or greater, and is free to use and modify.
    It is free to redistribute as long as you abide by the distribution terms of the GPL.
</p>
<h3>Requirements</h3>
<ul>
    <li>WWW Server (<a href="https://www.apache.org/" rel="external">Apache</a>, <a href="https://www.nginx.com/" rel="external">NGINX</a>, IIS, etc)</li>
    <li><a href="https://www.php.net/" rel="external">PHP</a> 5.6.0 or higher, 7.3+ recommended</li>
    <li><a href="https://www.mysql.com/" rel="external">MySQL</a> 5.5 or higher, 5.7+ recommended </li>
</ul>
<h3>Before you install</h3>
<ol>
    <li>Setup WWW server, PHP and database server properly.</li>
    <li>Prepare a database for your XOOPS site.</li>
    <li>Prepare user account and grant the user the access to the database.</li>
    <li>Make these directories and files writable: %s</li>
    <li>For security considerations, you are strongly advised to move the two directories below out of <a href="https://privacyaustralia.net/phpsec/projects/guide/php-security-guide-databases-and-sql/" rel="external">document root</a> and change the folder names: %s</li>
    <li>Create (if not already present) and make these directories writable: %s</li>
    <li>Turn cookie and JavaScript of your browser on.</li>
</ol>
<h3>Special Notes</h3>
<p>Some specific system software combinations may require some additional configurations to work
 with XOOPS. If any of these topics apply to your environment, please see the full
 <a href="https://xoops.gitbook.io/xoops-install-upgrade/" rel="external">XOOPS
 installation manual</a> for more information.
</p>
<p>MySQL 8.0 is not supported in all PHP versions. Even in the supported versions, issues with the
 PHP <em>mysqlnd</em> library may require the MySQL server&apos;s <em>default-authentication-plugin</em>
 to be set to <em>mysql_native_password</em> to function correctly.
</p>
<p>SELinux enabled systems (such as CentOS and RHEL) may require changes to the security context
 for XOOPS directories in addition to the normal file permissions to make directories writable.
 Consult your system documentation and/or systems administrator.
</p>
';

return $content;

