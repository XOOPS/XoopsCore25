xmf ChangeLog
=============
Oct 14, 2026 v1.2.33
------------------
* security: ProxyCheck/IPAddress::fromRequest() only use the proxy_env header when REMOTE_ADDR is listed in the new $xoopsConfig['proxy_trusted'] (addresses or CIDR ranges, array or comma separated; IPv4-mapped IPv6 peers match IPv4 entries). Entries are read from right to left, skipping trusted proxies, and any entry that is not an IP address stops the search. Sites that set proxy_env must add proxy_trusted; until then the peer address is used (backport of #187)
* ProxyCheck: parse RFC 7239 Forwarded values case-insensitively, respect quoted delimiters, strip node ports, reject elements with zero or several for parameters, and stop at any node that is not an IP address with an optional numeric port (backport of #189)
* Database\Tables: CURRENT_TIMESTAMP defaults of TIMESTAMP/DATETIME columns reported as current_timestamp() or with a precision are no longer quoted and render in the canonical CURRENT_TIMESTAMP form, and the same text in a string column stays a quoted literal (backport of #193); tables with functional index key parts are refused with an error instead of loading a wrong index definition (backport of #194)
* Database\Migrate: getCurrentSchema() leaves out missing tables instead of writing an empty definition; getCurrentSchema(), saveCurrentSchema(), getSynchronizeDDL() and synchronizeSchema() throw RuntimeException when a table cannot be read (backport of #194, #195)
* FilterInput::cleanVar() accepts a null type, which Request::getHeader() passes, without a PHP 8.1+ deprecation
* dev: PHPUnit 9 fixture signatures in the test suite; roave/security-advisories dropped from require-dev and the low-severity php-jwt 6.x advisory (PKSA-y2cr-5h3j-g3ys, fixed only in php-jwt 7 which needs PHP 8) ignored so the dev install resolves on PHP 7.4

Oct 6, 2026 v1.2.32.1
------------------
* security: FilterInput filtering always terminates; a value containing "<>" followed by more text exhausted the memory limit; passes are now capped at 10 (GHSA-8j4x-8r8r-5jgw)
* security: FilterInput name checks are anchored with \z, attribute names must start with a letter, on* handlers are matched case-insensitively, URL schemes are checked entity-decoded, and stripped text keeps no tag opener (GHSA-pf53-59r5-mhjv)

Mar 10, 2025 v1.2.32
------------------
* Update firebase/php-jwt to 6.11.0 with PHP 7.4 compatibility

Nov 27, 2024 v1.2.31
------------------
* updated Debug for Kint changes (mamba)
* added Issues Template (mamba)
* PHP 8.4 Implicitly nullable parameters (mamba)
* Update PhpUnit versions (mamba)
* upgrade Smarty to 4.5.5 

May 30, 2024 v1.2.30
------------------
* upgrade Smarty to 4.5.3


Nov 20, 2023 v1.2.29
------------------
* add Random::generateSecureRandomBytes()
* replace random_bytes() with generateSecureRandomBytes() for PHP 5.6


Oct 30, 2023 v1.2.28
------------------
* Updates to library dependencies
* PHP 8.0 Error Suppression operator issues
* Handle case of no permissionHandler found
* Adds ULID support
* cosmetic and code improvements

Mar 19, 2023 v1.2.27
------------------
* Update to firebase/php-jwt 6.0.0

Apr 16, 2022 v1.2.26
------------------
* Add Xmf\Module\Helper\Permission::getItemIds($gperm_name, $gperm_groupid)
* Use new module version in XoopsCore25
* Fix issues in Xmf\Database\Tables and Xmf\Database\Migrate
* Fix some issues related to new PHP versions

May 7, 2021 v1.2.25
------------------
* add \Xmf\Module\Admin::renderNavigation() method

Mar 25, 2021 v1.2.24
------------------
* Fixes for PHP 5.3 compatibility

Feb 15, 2021 v1.2.23
------------------
* Additional fix in Debug for Kint 3.3

Feb 13, 2021 v1.2.22
------------------
* fixes in Debug for Kint 3.3

Feb 13, 2021 v1.2.21
------------------
* Library updates
* XOOPS standardization
* Minor code cleanups

Aug 18, 2020 v1.2.20
------------------
* \Xmf\Module\Helper\AbstractHelper::serializeForHelperLog() fix logging of a resource type
* Unit test updates for latest version of Webmozart\Assert

Feb 13, 2020 v1.2.19
------------------
* \Xmf\Yaml::read() eliminate PHP warning if specified file does not exist.

Dec 1, 2019 v1.2.18
------------------
* PHP 7.4 ready
* fix error in Database\Table::loadTableFromYamlFile()
* add Uuid::packAsBinary() and Uuid::unpackBinary() methods
* add Module/Helper/GenericHelper::uploadPath() and uploadUrl() methods
* add proxy support in IPAddress::fromRequest();

Mar 27, 2019 v1.2.17
------------------
- Docblock corrections

Nov 29, 2018 v1.2.16
------------------
- Fix database column quoting

Oct 1, 2018 v1.2.15
------------------
- Fix database column quoting for prefix indexes
- Add dirname() method to helper classes
- Changes Request::hasVar() default for $hash to 'default'

Mar 30, 2018 v1.2.14
------------------
- add serialization to non-scalar log data
- improved handling of custom key storage
- add some unit testing
- add roave/security-advisories requirement to catch security issues at build time
- Synchronization with XoopsCore

Nov 12, 2017 v1.2.12
------------------
- updates the supporting Kint library to version 2.2.

Nov 12, 2017 v1.2.11
------------------
- adds support for UUID generation using the Xmf\Uuid class.

Jul 24, 2017 v1.2.10
------------------
- fixes issues in Xmf\Random appearing under PHP 7.1. Xmf\Random will now avoid the mcrypt extension if at all possible, and use the native random_bytes() function in PHP 7+.

May 19, 2017 v1.2.9
------------------
- fixes issues in Xmf\Highlighter and Xmf\Metagen

May 7, 2017 v1.2.8
------------------
- add a missing option in \Xmf\Module\Helper\Permission::checkPermission()

Apr 29, 2017 v1.2.7
------------------
- fixes issue with Xmf\Metagen::generateSeoTitle

Apr 18, 2017 v1.2.6
------------------
- fixes issues with Xmf\Request::MASK_ALLOW_HTML

Apr 3, 2017 v1.2.5
------------------
- updates to kint-php/kint

Mar 6, 2017 v1.2.4
------------------
- adds Xmf\Assert

Mar 3, 2017 v1.2.3
------------------
- synchronizes some minor docblock changes

Feb 25, 2017 v1.2.2
------------------
- corrects issues with Yaml:readWrapped()

Nov 2, 2016 v1.2.0
------------------
- Separates the stop word logic from MetaGen into a new StopWords class
- Deprecates MetaGen::checkStopWords()

Sep 11, 2016 v1.1.4
------------------
- #17 Handle non-ascii text in Metagen::generateKeywords()

Aug 13, 2016 v1.1.3
------------------
- Fix #15 XoopsRequest class not found in StripSlashesRecursive method

Aug 6, 2016 v1.1.2
------------------
- Fix #13 Can't check isUserAdmin on Anonymous

Jul 28, 2016 v1.1.1
------------------
- firebase/php-jwt to 4.0.0
- Bump min PHP to 5.3.9 to allow symfony/yaml 2.8.*

Jul 14, 2016 v1.1.0
------------------
- Add Xmf\Database\Migrate class to provide schema synchronization capabilities for modules.
- Bug fixes in Xmf\Database\Tables including option to disable automatic quoting of values in update() and insert() to support using column functions instead of only scalars

01-Jun-2016 V1.0.2
------------------
- fix issues with file name validation in Xmf\Language::loadFile()
- add method Request::hasVar($name, $hash) to determine if a variable name exists in hash

30-Mar-2016 V1.0.1
------------------
- remove @version from docblock, consistent with XoopsCore25

25-Mar-2016 V1.0.0
------------------
- fix minor typos
- add version to changelog

04-Mar-2016 V1.0.0 RC1
-------------------------
- Preparation for release in XOOPS 2.5.8

09-Feb-2016
-----------
- Convert to library instead of module
- Preparing for 2.5.8 inclusion
- Sync with 2.6 current state

14-Sep-2013
-----------
- 1.0 Alpha for XOOPS 2.5.6
- moved development to https://github.com/geekwright/xmf.git for now
- initial checkout: svn checkout http://svn.code.sf.net/p/xoops/svn/XMF/xmf/trunk/xmf
