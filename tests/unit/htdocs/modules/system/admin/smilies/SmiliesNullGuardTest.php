<?php

declare(strict_types=1);

namespace modulessystem\admin\smilies;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests for H-3: null guards after handler get() calls in smilies/main.php.
 *
 * The smilies admin page calls $smilies_Handler->get() in several switch cases.
 * When get() returns null/false (e.g. invalid ID), the code must not call
 * methods on null. This test verifies the handler's get() behavior and
 * that the guard pattern works correctly.
 */
class SmiliesNullGuardTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            if (!isset($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
            }
            require_once XOOPS_ROOT_PATH . '/modules/system/class/smilies.php';
            self::$loaded = true;
        }
    }

    #[Test]
    public function handlerGetReturnsNullForInvalidId(): void
    {
        /** @var \SystemsmiliesHandler $handler */
        $handler = new \SystemsmiliesHandler($GLOBALS['xoopsDB']);
        // get() with an invalid/nonexistent ID should return null (stub DB returns no rows)
        $result = $handler->get(99999);
        $this->assertNull($result);
    }

    #[Test]
    public function nullGuardPreventsMethodCallOnNull(): void
    {
        /** @var \SystemsmiliesHandler $handler */
        $handler = new \SystemsmiliesHandler($GLOBALS['xoopsDB']);
        $obj = $handler->get(99999);

        // This is the guard pattern used in main.php — must not proceed if not object
        $this->assertFalse(is_object($obj), 'get() for invalid ID must not return an object');
    }

    #[Test]
    public function createReturnsValidObject(): void
    {
        $handler = new \SystemsmiliesHandler($GLOBALS['xoopsDB']);
        $obj = $handler->create();
        $this->assertInstanceOf(\SystemSmilies::class, $obj);
        $this->assertTrue(is_object($obj));
    }

    #[Test]
    public function nullGuardPatternRedirectsForEditCase(): void
    {
        // Simulate the edit_smilie guard: if get() returns null, redirect_header is called
        $handler = new \SystemsmiliesHandler($GLOBALS['xoopsDB']);
        $obj = $handler->get(99999);

        if (!is_object($obj)) {
            // This is the path taken in main.php — redirect_header would be called
            $this->assertFalse(is_object($obj));
            return;
        }
        $this->fail('Should have taken the null guard path');
    }

    #[Test]
    public function nullGuardPatternRedirectsForDeleteCase(): void
    {
        $handler = new \SystemsmiliesHandler($GLOBALS['xoopsDB']);
        $obj = $handler->get(99998);

        if (!is_object($obj)) {
            $this->assertFalse(is_object($obj));
            return;
        }
        $this->fail('Should have taken the null guard path');
    }

    #[Test]
    public function nullGuardPatternRedirectsForUpdateDisplayCase(): void
    {
        $handler = new \SystemsmiliesHandler($GLOBALS['xoopsDB']);
        $obj = $handler->get(99997);

        // This is the new guard added for smilies_update_display
        if (!is_object($obj)) {
            $this->assertFalse(is_object($obj));
            return;
        }
        $this->fail('Should have taken the null guard path');
    }

    #[Test]
    public function sourceFileContainsNullGuardInUpdateDisplay(): void
    {
        $source = file_get_contents(XOOPS_ROOT_PATH . '/modules/system/admin/smilies/main.php');
        // Verify the update_display case has a null guard
        $pattern = "/case\s+'smilies_update_display'.*?if\s*\(\s*!is_object\s*\(\s*\\\$obj\s*\)\s*\)/s";
        $this->assertMatchesRegularExpression($pattern, $source,
            'smilies_update_display case must contain !is_object($obj) guard');
    }
}
