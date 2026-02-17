<?php

declare(strict_types=1);

namespace frameworksart;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(\xoopsart::class)]
class XoopsArtTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            require_once XOOPS_ROOT_PATH . '/Frameworks/art/functions.ini.php';
            require_once XOOPS_ROOT_PATH . '/Frameworks/art/xoopsart.php';
            self::$loaded = true;
        }
    }

    // ---------------------------------------------------------------
    // Constructor tests
    // ---------------------------------------------------------------

    #[Test]
    public function constructorDoesNotThrow(): void
    {
        $art = new \xoopsart();
        $this->assertInstanceOf(\xoopsart::class, $art);
    }

    // ---------------------------------------------------------------
    // loadFunctions tests
    // ---------------------------------------------------------------

    #[Test]
    public function loadFunctionsReturnsForCacheGroup(): void
    {
        $art = new \xoopsart();
        // 'cache' group loads functions.cache.php — should succeed
        $result = $art->loadFunctions('cache');
        // include_once returns true on success or 1 on already-loaded
        $this->assertNotFalse($result);
    }

    #[Test]
    public function loadFunctionsReturnsForConfigGroup(): void
    {
        $art = new \xoopsart();
        $result = $art->loadFunctions('config');
        $this->assertNotFalse($result);
    }

    #[Test]
    public function loadFunctionsReturnsForUserGroup(): void
    {
        $art = new \xoopsart();
        // 'user' group loads functions.user.php
        $result = $art->loadFunctions('user');
        $this->assertNotFalse($result);
    }

    #[Test]
    public function loadFunctionsReturnsForIniGroup(): void
    {
        $art = new \xoopsart();
        // 'ini' group loads functions.ini.php — already loaded
        $result = $art->loadFunctions('ini');
        $this->assertNotFalse($result);
    }
}
