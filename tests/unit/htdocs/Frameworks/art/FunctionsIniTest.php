<?php

declare(strict_types=1);

namespace frameworksart;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FunctionsIniTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            require_once XOOPS_ROOT_PATH . '/Frameworks/art/functions.ini.php';
            self::$loaded = true;
        }
    }

    protected function setUp(): void
    {
        // Ensure xoopsLogger exists for deprecation logging
        if (!isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
        }
    }

    // ---------------------------------------------------------------
    // mod_getDirname tests
    // ---------------------------------------------------------------

    #[Test]
    public function getDirnameExtractsModuleDir(): void
    {
        $path = '/var/www/xoops/modules/publisher/admin/index.php';
        $this->assertSame('publisher', mod_getDirname($path));
    }

    #[Test]
    public function getDirnameHandlesWindowsPaths(): void
    {
        $path = 'C:\\wamp64\\www\\xoops\\modules\\news\\index.php';
        $this->assertSame('news', mod_getDirname($path));
    }

    #[Test]
    public function getDirnameExtractsFirstDirAfterModules(): void
    {
        $path = '/home/user/xoops/modules/system/admin/modulesadmin.php';
        $this->assertSame('system', mod_getDirname($path));
    }

    #[Test]
    public function getDirnameHandlesDoubleBackslashPaths(): void
    {
        $path = 'C:\\\\server\\\\share\\\\xoops\\\\modules\\\\mymod\\\\index.php';
        $this->assertSame('mymod', mod_getDirname($path));
    }

    // ---------------------------------------------------------------
    // mod_constant tests
    // ---------------------------------------------------------------

    #[Test]
    public function modConstantReturnsDefinedConstant(): void
    {
        if (!defined('TEST_FRAMEWORK_CONST')) {
            define('TEST_FRAMEWORK_CONST', 'framework_value');
        }
        $this->assertSame('framework_value', mod_constant('test_framework_const'));
    }

    #[Test]
    public function modConstantReturnsFallbackForUndefined(): void
    {
        // When constant is not defined, returns str_replace('_', ' ', strtolower($name))
        $result = mod_constant('SOME_UNDEFINED_CONSTANT_XYZ');
        $this->assertSame('some undefined constant xyz', $result);
    }

    #[Test]
    public function modConstantUsesVarPrefixUWhenSet(): void
    {
        if (!defined('TESTMOD_MYKEY')) {
            define('TESTMOD_MYKEY', 'prefixed_value');
        }
        $GLOBALS['VAR_PREFIXU'] = 'TESTMOD';
        $result = mod_constant('MYKEY');
        unset($GLOBALS['VAR_PREFIXU']);
        $this->assertSame('prefixed_value', $result);
    }

    #[Test]
    public function modConstantUsesXoopsModuleDirname(): void
    {
        if (!defined('MYMODULE_SETTING')) {
            define('MYMODULE_SETTING', 'module_const_value');
        }
        // Create a mock XoopsModule
        $mockModule = $this->createMockModule('mymodule');
        $oldModule = $GLOBALS['xoopsModule'] ?? null;
        $GLOBALS['xoopsModule'] = $mockModule;
        unset($GLOBALS['VAR_PREFIXU']);

        $result = mod_constant('setting');

        if ($oldModule !== null) {
            $GLOBALS['xoopsModule'] = $oldModule;
        } else {
            unset($GLOBALS['xoopsModule']);
        }
        $this->assertSame('module_const_value', $result);
    }

    // ---------------------------------------------------------------
    // mod_DB_prefix tests
    // ---------------------------------------------------------------

    #[Test]
    public function modDbPrefixReturnsFullPrefixedName(): void
    {
        $GLOBALS['MOD_DB_PREFIX'] = 'publisher';
        $result = mod_DB_prefix('articles');
        // xoopsDB->prefix('publisher_articles') => 'xoops_publisher_articles'
        $this->assertSame('xoops_publisher_articles', $result);
        unset($GLOBALS['MOD_DB_PREFIX']);
    }

    #[Test]
    public function modDbPrefixReturnsRelativeWhenFlagged(): void
    {
        $GLOBALS['MOD_DB_PREFIX'] = 'news';
        $result = mod_DB_prefix('items', true);
        $this->assertSame('news_items', $result);
        unset($GLOBALS['MOD_DB_PREFIX']);
    }

    // ---------------------------------------------------------------
    // mod_isModuleAction tests
    // ---------------------------------------------------------------

    #[Test]
    public function modIsModuleActionReturnsFalseWhenNoModule(): void
    {
        $old = $GLOBALS['xoopsModule'] ?? null;
        unset($GLOBALS['xoopsModule']);
        $result = mod_isModuleAction('system');
        if ($old !== null) {
            $GLOBALS['xoopsModule'] = $old;
        }
        $this->assertFalse($result);
    }

    #[Test]
    public function modIsModuleActionReturnsFalseWhenNotSystem(): void
    {
        $mockModule = $this->createMockModule('publisher');
        $old = $GLOBALS['xoopsModule'] ?? null;
        $GLOBALS['xoopsModule'] = $mockModule;

        $result = mod_isModuleAction('system');

        if ($old !== null) {
            $GLOBALS['xoopsModule'] = $old;
        } else {
            unset($GLOBALS['xoopsModule']);
        }
        $this->assertFalse($result);
    }

    #[Test]
    public function modIsModuleActionReturnsFalseWithEmptyPost(): void
    {
        $mockModule = $this->createMockModule('system');
        $old = $GLOBALS['xoopsModule'] ?? null;
        $GLOBALS['xoopsModule'] = $mockModule;
        $oldPost = $_POST;
        $_POST = [];

        $result = mod_isModuleAction('testmod');

        $_POST = $oldPost;
        if ($old !== null) {
            $GLOBALS['xoopsModule'] = $old;
        } else {
            unset($GLOBALS['xoopsModule']);
        }
        $this->assertFalse($result);
    }

    // ---------------------------------------------------------------
    // mod_message tests
    // ---------------------------------------------------------------

    #[Test]
    public function modMessageReturnsTrueWithDebugEnabled(): void
    {
        $GLOBALS['xoopsModuleConfig'] = ['do_debug' => 1];
        ob_start();
        $result = mod_message('test message');
        $output = ob_get_clean();
        unset($GLOBALS['xoopsModuleConfig']);
        $this->assertTrue($result);
        $this->assertStringContainsString('test message', $output);
    }

    #[Test]
    public function modMessageReturnsTrueWithDebugDisabled(): void
    {
        $GLOBALS['xoopsModuleConfig'] = ['do_debug' => 0];
        ob_start();
        $result = mod_message('test message');
        $output = ob_get_clean();
        unset($GLOBALS['xoopsModuleConfig']);
        $this->assertTrue($result);
        $this->assertSame('', $output);
    }

    #[Test]
    public function modMessageOutputsArrayWithDebug(): void
    {
        $GLOBALS['xoopsModuleConfig'] = ['do_debug' => 1];
        ob_start();
        mod_message(['key' => 'value']);
        $output = ob_get_clean();
        unset($GLOBALS['xoopsModuleConfig']);
        $this->assertStringContainsString('key', $output);
        $this->assertStringContainsString('value', $output);
    }

    // ---------------------------------------------------------------
    // load_objectHandler tests
    // ---------------------------------------------------------------

    #[Test]
    public function loadObjectHandlerReturnsTrueForBaseObject(): void
    {
        // Empty handler loads ArtObject class (already loaded by including object.php)
        $result = load_objectHandler('', 'art');
        $this->assertTrue($result);
    }

    #[Test]
    public function loadObjectReturnsTrueForBaseObject(): void
    {
        $result = load_object();
        $this->assertTrue($result);
    }

    // ---------------------------------------------------------------
    // load_functions tests
    // ---------------------------------------------------------------

    #[Test]
    public function loadFunctionsReturnsTrueForAlreadyLoaded(): void
    {
        // FRAMEWORKS_ART_FUNCTIONS_INI is already defined, so 'ini' group is loaded
        $result = load_functions('ini', 'art');
        $this->assertTrue($result);
    }

    #[Test]
    public function loadFunctionsUsesArtAsDefaultDirname(): void
    {
        // Loading 'cache' should work (file exists)
        load_functions('cache', 'art');
        $this->assertTrue(defined('FRAMEWORKS_ART_FUNCTIONS_CACHE'));
    }

    // ---------------------------------------------------------------
    // FRAMEWORKS_ROOT_PATH constant
    // ---------------------------------------------------------------

    #[Test]
    public function frameworksRootPathIsDefined(): void
    {
        $this->assertTrue(defined('FRAMEWORKS_ROOT_PATH'));
        $this->assertSame(XOOPS_ROOT_PATH . '/Frameworks', FRAMEWORKS_ROOT_PATH);
    }

    // ---------------------------------------------------------------
    // Helper
    // ---------------------------------------------------------------

    private function createMockModule(string $dirname): object
    {
        return new class($dirname) {
            private string $dirname;
            public function __construct(string $dirname) { $this->dirname = $dirname; }
            public function getVar(string $key, string $format = 'e')
            {
                if ($key === 'dirname') return $this->dirname;
                if ($key === 'mid') return 1;
                return '';
            }
        };
    }
}
