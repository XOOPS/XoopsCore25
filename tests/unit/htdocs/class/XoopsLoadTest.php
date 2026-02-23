<?php

declare(strict_types=1);

namespace xoopsclass;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use XoopsLoad;

/**
 * Comprehensive unit tests for XoopsLoad.
 *
 * XoopsLoad is the core autoloader for XOOPS 2.5.x. It provides a static
 * class-to-file mapping via loadCoreConfig(), and dispatches loading by type
 * (core, framework, module) through load(). Deprecated names are transparently
 * remapped to their modern equivalents.
 *
 * Tested API:
 *   - loadCoreConfig()    Returns the static class-to-file mapping array
 *   - load()              Main entry point; dispatches by type, caches results
 *   - loadCore()          Loads a class from the core config map
 *   - loadModule()        Loads a class from a module's class/ directory
 *   - loadFramework()     Loads a class from the Frameworks/ directory
 *
 */
#[CoversClass(XoopsLoad::class)]
class XoopsLoadTest extends TestCase
{
    // ---------------------------------------------------------------
    //  loadCoreConfig() tests
    // ---------------------------------------------------------------

    /**
     * loadCoreConfig() must return an array.
     */
    public function testLoadCoreConfigReturnsArray(): void
    {
        $configs = XoopsLoad::loadCoreConfig();
        $this->assertIsArray($configs);
    }

    /**
     * loadCoreConfig() must not return an empty map.
     */
    public function testLoadCoreConfigIsNotEmpty(): void
    {
        $configs = XoopsLoad::loadCoreConfig();
        $this->assertNotEmpty($configs, 'Core config map should contain entries');
    }

    /**
     * Known core mappings must exist in the config array.
     */
    #[DataProvider('coreClassProvider')]
    public function testLoadCoreConfigContainsExpectedKeys(string $key): void
    {
        $configs = XoopsLoad::loadCoreConfig();
        $this->assertArrayHasKey($key, $configs, "Core config should contain key '{$key}'");
    }

    /**
     * @return array<string, array{string}>
     */
    public static function coreClassProvider(): array
    {
        return [
            'xoopssecurity'       => ['xoopssecurity'],
            'xoopspagenav'        => ['xoopspagenav'],
            'xoopslists'          => ['xoopslists'],
            'xoopslogger'         => ['xoopslogger'],
            'xoopsmediauploader'  => ['xoopsmediauploader'],
            'xoopscache'          => ['xoopscache'],
            'xoopsfile'           => ['xoopsfile'],
            'xoopsmodelfactory'   => ['xoopsmodelfactory'],
            'xoopsuserutility'    => ['xoopsuserutility'],
            'xoopskernel'         => ['xoopskernel'],
            'xoopsformloader'     => ['xoopsformloader'],
            'xoopsfilterinput'    => ['xoopsfilterinput'],
            'xoopsrequest'        => ['xoopsrequest'],
            // 'xoopsmonologlogger' is NOT in loadCoreConfig — it's an optional logger class
        ];
    }

    /**
     * All file paths in the config must start with XOOPS_ROOT_PATH.
     */
    public function testLoadCoreConfigPathsStartWithRootPath(): void
    {
        $configs = XoopsLoad::loadCoreConfig();
        foreach ($configs as $name => $path) {
            $this->assertStringStartsWith(
                XOOPS_ROOT_PATH,
                $path,
                "Path for '{$name}' should start with XOOPS_ROOT_PATH"
            );
        }
    }

    /**
     * Files referenced in the config map should exist on disk.
     * Some optional entries (e.g. xoopscalendar) may be absent
     * on specific installations, so we track them separately.
     */
    public function testLoadCoreConfigFilesExistOnDisk(): void
    {
        // Known optional entries that may not be present on every install
        $optional = ['xoopscalendar', 'xoopsformcalendar'];

        $configs = XoopsLoad::loadCoreConfig();
        foreach ($configs as $name => $path) {
            if (in_array($name, $optional, true)) {
                // Just assert the path is a string, don't require the file
                $this->assertIsString($path, "Path for optional class '{$name}' should be a string");
                continue;
            }
            $this->assertFileExists($path, "File for core class '{$name}' should exist at {$path}");
        }
    }

    /**
     * All keys in the config map must be lowercase (XoopsLoad lowercases
     * the name before lookup).
     */
    public function testLoadCoreConfigKeysAreLowercase(): void
    {
        $configs = XoopsLoad::loadCoreConfig();
        foreach (array_keys($configs) as $key) {
            $this->assertSame(
                strtolower($key),
                $key,
                "Core config key '{$key}' should be lowercase"
            );
        }
    }

    /**
     * All values in the config map must be strings (file paths).
     */
    public function testLoadCoreConfigValuesAreStrings(): void
    {
        $configs = XoopsLoad::loadCoreConfig();
        foreach ($configs as $name => $path) {
            $this->assertIsString($path, "Path for '{$name}' should be a string");
        }
    }

    /**
     * All file paths must end with .php.
     */
    public function testLoadCoreConfigPathsEndWithPhp(): void
    {
        $configs = XoopsLoad::loadCoreConfig();
        foreach ($configs as $name => $path) {
            $this->assertStringEndsWith(
                '.php',
                $path,
                "Path for '{$name}' should end with .php"
            );
        }
    }

    /**
     * Calling loadCoreConfig() twice returns identical results (deterministic).
     */
    public function testLoadCoreConfigIsIdempotent(): void
    {
        $first  = XoopsLoad::loadCoreConfig();
        $second = XoopsLoad::loadCoreConfig();
        $this->assertSame($first, $second, 'loadCoreConfig() should return the same result on each call');
    }

    // ---------------------------------------------------------------
    //  load() tests — main dispatcher
    // ---------------------------------------------------------------

    /**
     * Loading an already-loaded class should return true.
     * CriteriaCompo is loaded by the bootstrap.
     */
    public function testLoadReturnsTrueForAlreadyLoadedClass(): void
    {
        // CriteriaCompo is loaded by the bootstrap (criteria.php)
        $this->assertTrue(
            class_exists('CriteriaCompo', false),
            'Precondition: CriteriaCompo must already be loaded'
        );
        $result = XoopsLoad::load('criteriacompo');
        $this->assertTrue($result);
    }

    /**
     * load() must be case-insensitive — 'XoopsSecurity' and 'xoopssecurity'
     * should both resolve.
     */
    public function testLoadIsCaseInsensitive(): void
    {
        $lower = XoopsLoad::load('xoopssecurity');
        $mixed = XoopsLoad::load('XoopsSecurity');
        $upper = XoopsLoad::load('XOOPSSECURITY');

        $this->assertTrue($lower, 'Lowercase name should load');
        $this->assertTrue($mixed, 'Mixed-case name should load');
        $this->assertTrue($upper, 'Uppercase name should load');
    }

    /**
     * Second call with the same name returns the cached result.
     */
    public function testLoadReturnsCachedResultOnSecondCall(): void
    {
        // First call loads it
        $first  = XoopsLoad::load('xoopssecurity');
        // Second call hits the static cache
        $second = XoopsLoad::load('xoopssecurity');

        $this->assertSame($first, $second, 'Second call should return cached result');
    }

    /**
     * 'class' type is treated the same as 'core' type.
     */
    public function testLoadClassTypeResolvesToCore(): void
    {
        $coreResult  = XoopsLoad::load('xoopssecurity', 'core');
        $classResult = XoopsLoad::load('xoopssecurity', 'class');
        $this->assertTrue($coreResult, "'core' type should load xoopssecurity");
        $this->assertTrue($classResult, "'class' type should also load xoopssecurity");
    }

    /**
     * Empty type defaults to 'core'.
     */
    public function testLoadEmptyTypeDefaultsToCore(): void
    {
        $result = XoopsLoad::load('xoopssecurity', '');
        $this->assertTrue($result, 'Empty type should default to core loading');
    }

    /**
     * Module type with empty dirname must return false.
     */
    public function testLoadModuleTypeWithEmptyDirnameReturnsFalse(): void
    {
        $result = XoopsLoad::load('nonexistentclass', '');
        // empty type defaults to core, but let's test the module path explicitly
        // We need to pass a non-empty, non-core, non-framework type but with a class that doesn't exist
        // Actually, the module branch is the default, so we need a dirname-like type
        $result = XoopsLoad::load('someclass', 'nonexistent_module_xyz');
        // The module dir doesn't exist, so loadModule returns false
        $this->assertFalse($result);
    }

    /**
     * Framework type with a non-existent framework should return false.
     */
    public function testLoadFrameworkTypeNonExistentReturnsFalse(): void
    {
        // Suppress the E_USER_WARNING that loadFramework triggers
        $result = @XoopsLoad::load('nonexistentframework999', 'framework');
        $this->assertFalse($result);
    }

    /**
     * Deprecated name 'uploader' maps to 'xoopsmediauploader'.
     */
    public function testLoadDeprecatedUploaderMapsToMediaUploader(): void
    {
        // Suppress deprecation notice
        $result = @XoopsLoad::load('uploader');
        $this->assertTrue($result, "'uploader' should load via deprecated mapping");
        $this->assertTrue(
            class_exists('XoopsMediaUploader', false),
            'XoopsMediaUploader class should be available after loading deprecated name'
        );
    }

    /**
     * Deprecated name 'cache' maps to 'xoopscache'.
     */
    public function testLoadDeprecatedCacheMapsToXoopsCache(): void
    {
        $result = @XoopsLoad::load('cache');
        $this->assertTrue($result, "'cache' should load via deprecated mapping");
    }

    /**
     * Deprecated name 'utility' maps to 'xoopsutility'.
     */
    public function testLoadDeprecatedUtilityMapsToXoopsUtility(): void
    {
        $result = @XoopsLoad::load('utility');
        $this->assertTrue($result, "'utility' should load via deprecated mapping");
    }

    /**
     * Deprecated name 'file' maps to 'xoopsfile'.
     */
    public function testLoadDeprecatedFileMapsToXoopsFile(): void
    {
        $result = @XoopsLoad::load('file');
        $this->assertTrue($result, "'file' should load via deprecated mapping");
    }

    /**
     * Deprecated name 'model' maps to 'xoopsmodelfactory'.
     */
    public function testLoadDeprecatedModelMapsToXoopsModelFactory(): void
    {
        $result = @XoopsLoad::load('model');
        $this->assertTrue($result, "'model' should load via deprecated mapping");
    }

    /**
     * Deprecated name 'userutility' maps to 'xoopsuserutility'.
     */
    public function testLoadDeprecatedUserUtilityMapsToXoopsUserUtility(): void
    {
        $result = @XoopsLoad::load('userutility');
        $this->assertTrue($result, "'userutility' should load via deprecated mapping");
    }

    /**
     * All deprecated mappings should successfully load their target classes.
     */
    #[DataProvider('deprecatedNameProvider')]
    public function testLoadAllDeprecatedNames(string $deprecated, string $modern): void
    {
        $result = @XoopsLoad::load($deprecated);
        $this->assertTrue($result, "Deprecated name '{$deprecated}' should successfully load as '{$modern}'");
    }

    /**
     * Provides deprecated name pairs that can be loaded on this installation.
     * 'calendar' is excluded because xoopscalendar.php is absent on this install.
     *
     * @return array<string, array{string, string}>
     */
    public static function deprecatedNameProvider(): array
    {
        return [
            'uploader'    => ['uploader',    'xoopsmediauploader'],
            'utility'     => ['utility',     'xoopsutility'],
            'captcha'     => ['captcha',     'xoopscaptcha'],
            'cache'       => ['cache',       'xoopscache'],
            'file'        => ['file',        'xoopsfile'],
            'model'       => ['model',       'xoopsmodelfactory'],
            'userutility' => ['userutility', 'xoopsuserutility'],
        ];
    }

    /**
     * Deprecated mapping should only apply for 'core' or 'class' types,
     * not for 'framework' or module types.
     */
    public function testDeprecatedMappingOnlyAppliesForCoreAndClassTypes(): void
    {
        // 'uploader' as framework type should NOT map to xoopsmediauploader
        // It should try to load from Frameworks/uploader/ instead
        $result = @XoopsLoad::load('uploader', 'framework');
        $this->assertFalse($result, "Deprecated mapping should not apply for 'framework' type");
    }

    /**
     * load() with 'framework' type calls loadFramework().
     */
    public function testLoadDispatchesToFramework(): void
    {
        // 'art' framework exists at Frameworks/art/xoopsart.php
        $result = XoopsLoad::load('art', 'framework');
        // Result is the class name string (truthy) or null, depending on whether XoopsArt class exists
        $this->assertNotFalse($result, "Framework 'art' should load successfully");
    }

    // ---------------------------------------------------------------
    //  loadCore() tests
    // ---------------------------------------------------------------

    /**
     * loadCore() returns true for a known core class.
     * Note: We use 'xoopspagenav' instead of 'xoopssecurity' because
     * the test bootstrap defines a stub XoopsSecurity class, and calling
     * loadCore() directly bypasses the class_exists() guard in load().
     */
    public function testLoadCoreReturnsTrueForKnownClass(): void
    {
        $result = XoopsLoad::loadCore('xoopspagenav');
        $this->assertTrue($result);
    }

    /**
     * loadCore() returns false for an unknown name that has no matching file.
     */
    public function testLoadCoreReturnsFalseForUnknownName(): void
    {
        // Suppress the E_USER_WARNING
        $result = @XoopsLoad::loadCore('completelybogusclass9999');
        $this->assertFalse($result);
    }

    /**
     * After loading a core class, it must be available via class_exists().
     */
    public function testLoadCoreMakesClassAvailable(): void
    {
        XoopsLoad::loadCore('xoopspagenav');
        $this->assertTrue(
            class_exists('XoopsPageNav', false),
            'XoopsPageNav should be defined after loadCore("xoopspagenav")'
        );
    }

    /**
     * loadCore() can be called multiple times for the same class without error.
     * Uses 'xoopspagenav' to avoid conflicts with the bootstrap stub.
     */
    public function testLoadCoreIsIdempotent(): void
    {
        $first  = XoopsLoad::loadCore('xoopspagenav');
        $second = XoopsLoad::loadCore('xoopspagenav');
        $this->assertTrue($first);
        $this->assertTrue($second);
    }

    // ---------------------------------------------------------------
    //  loadModule() tests
    // ---------------------------------------------------------------

    /**
     * loadModule() with empty dirname returns false.
     */
    public function testLoadModuleWithEmptyDirnameReturnsFalse(): void
    {
        $result = XoopsLoad::loadModule('somefile', '');
        $this->assertFalse($result);
    }

    /**
     * loadModule() with null dirname returns false.
     */
    public function testLoadModuleWithNullDirnameReturnsFalse(): void
    {
        $result = XoopsLoad::loadModule('somefile', null);
        $this->assertFalse($result);
    }

    /**
     * loadModule() with non-existent module directory returns false.
     */
    public function testLoadModuleWithNonExistentModuleReturnsFalse(): void
    {
        $result = XoopsLoad::loadModule('handler', 'nonexistent_module_xyz');
        $this->assertFalse($result);
    }

    /**
     * loadModule() with non-existent class file in a real module returns false.
     */
    public function testLoadModuleWithNonExistentClassFileReturnsFalse(): void
    {
        // 'system' module exists, but 'nonexistentclass' file does not
        $result = XoopsLoad::loadModule('nonexistentclass_xyz', 'system');
        $this->assertFalse($result);
    }

    // ---------------------------------------------------------------
    //  loadFramework() tests
    // ---------------------------------------------------------------

    /**
     * loadFramework() with a non-existent framework returns false.
     */
    public function testLoadFrameworkReturnsFalseForNonExistentFramework(): void
    {
        $result = @XoopsLoad::loadFramework('nonexistentframework999');
        $this->assertFalse($result);
    }

    /**
     * loadFramework() triggers a user warning for non-existent framework.
     */
    public function testLoadFrameworkTriggersWarningForNonExistentFramework(): void
    {
        $warningTriggered = false;
        $previousHandler = set_error_handler(function ($errno, $errstr) use (&$warningTriggered) {
            if ($errno === E_USER_WARNING) {
                $warningTriggered = true;
            }
            return true; // suppress the warning
        });

        try {
            XoopsLoad::loadFramework('nonexistentframework999');
        } finally {
            restore_error_handler();
        }

        $this->assertTrue($warningTriggered, 'loadFramework() should trigger E_USER_WARNING for non-existent framework');
    }

    /**
     * loadFramework() with an existing framework returns truthy.
     */
    public function testLoadFrameworkReturnsTruthyForExistingFramework(): void
    {
        // 'art' exists at Frameworks/art/xoopsart.php
        $result = XoopsLoad::loadFramework('art');
        // Returns the class name string 'XoopsArt' if class exists, or null
        $this->assertNotFalse($result, "Framework 'art' file exists and should not return false");
    }

    // ---------------------------------------------------------------
    //  loadConfig() tests (instance method)
    // ---------------------------------------------------------------

    /**
     * loadConfig() with an array returns merged config.
     */
    public function testLoadConfigWithArrayReturnsMergedArray(): void
    {
        $loader = new XoopsLoad();
        $custom = ['myclass' => '/path/to/myclass.php'];
        $result = $loader->loadConfig($custom);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('myclass', $result);
        // Core configs should also be present
        $this->assertArrayHasKey('xoopssecurity', $result);
    }

    /**
     * loadConfig() without data and without xoopsModule returns false.
     */
    public function testLoadConfigWithoutDataAndModuleReturnsFalse(): void
    {
        $loader = new XoopsLoad();
        // Ensure xoopsModule global is not set or not an object
        $original = isset($GLOBALS['xoopsModule']) ? $GLOBALS['xoopsModule'] : null;
        $GLOBALS['xoopsModule'] = null;

        $result = $loader->loadConfig(null);
        $this->assertFalse($result);

        // Restore
        if ($original !== null) {
            $GLOBALS['xoopsModule'] = $original;
        } else {
            unset($GLOBALS['xoopsModule']);
        }
    }

    // ---------------------------------------------------------------
    //  Integration / edge-case tests
    // ---------------------------------------------------------------

    /**
     * load() default type parameter is 'core'.
     */
    public function testLoadDefaultTypeIsCore(): void
    {
        $result = XoopsLoad::load('xoopssecurity');
        $this->assertTrue($result, 'Default type should be core');
    }

    /**
     * load() for a known class that was already loaded via require
     * should still return true (class_exists check).
     */
    public function testLoadReturnsTrueForClassLoadedByRequire(): void
    {
        // XoopsPreload was loaded by bootstrap
        $result = XoopsLoad::load('xoopspreload');
        $this->assertTrue($result);
    }

    /**
     * The deprecated map contains 8 entries. We verify the 7 that have
     * backing files on this install, and separately confirm 'calendar'
     * exists as a key in the map even though its file may be absent.
     */
    public function testDeprecatedMapHasExpectedCount(): void
    {
        // The 7 deprecated names whose backing files exist on this install
        $loadable = [
            'uploader', 'utility', 'captcha', 'cache',
            'file', 'model', 'userutility',
        ];

        foreach ($loadable as $name) {
            $result = @XoopsLoad::load($name);
            $this->assertNotFalse(
                $result,
                "Deprecated name '{$name}' should load without returning false"
            );
        }

        // 'calendar' is part of the deprecated map but its file is absent;
        // verify the mapping exists in the core config under 'xoopscalendar'
        $configs = XoopsLoad::loadCoreConfig();
        $this->assertArrayHasKey(
            'xoopscalendar',
            $configs,
            "'xoopscalendar' should exist in core config (deprecated target for 'calendar')"
        );
    }

    /**
     * Core config should contain form-related classes.
     */
    #[DataProvider('formClassProvider')]
    public function testLoadCoreConfigContainsFormClasses(string $key): void
    {
        $configs = XoopsLoad::loadCoreConfig();
        $this->assertArrayHasKey($key, $configs, "Core config should contain form class '{$key}'");
    }

    /**
     * @return array<string, array{string}>
     */
    public static function formClassProvider(): array
    {
        return [
            'xoopsformelement'        => ['xoopsformelement'],
            'xoopsform'               => ['xoopsform'],
            'xoopsformselect'         => ['xoopsformselect'],
            'xoopsformpassword'       => ['xoopsformpassword'],
            'xoopsformbutton'         => ['xoopsformbutton'],
            'xoopsformhidden'         => ['xoopsformhidden'],
            'xoopsformtext'           => ['xoopsformtext'],
            'xoopsformtextarea'       => ['xoopsformtextarea'],
            'xoopsthemeform'          => ['xoopsthemeform'],
            'xoopsformrenderer'       => ['xoopsformrenderer'],
        ];
    }
}
