<?php

declare(strict_types=1);

namespace frameworksart;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FunctionsUserTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            require_once XOOPS_ROOT_PATH . '/Frameworks/art/functions.ini.php';
            require_once XOOPS_ROOT_PATH . '/class/userutility.php';
            require_once XOOPS_ROOT_PATH . '/Frameworks/art/functions.user.php';
            self::$loaded = true;
        }
    }

    protected function setUp(): void
    {
        if (!isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
        }
    }

    // ---------------------------------------------------------------
    // mod_getIP tests
    // ---------------------------------------------------------------

    #[Test]
    public function getIpReturnsValue(): void
    {
        $result = @mod_getIP(true);
        // Should return an IP string or empty
        $this->assertTrue(is_string($result) || is_long($result));
    }

    #[Test]
    public function getIpLogsDeprecation(): void
    {
        $logger = \XoopsLogger::getInstance();
        $countBefore = count($logger->deprecated);

        @mod_getIP(false);

        $this->assertGreaterThan($countBefore, count($logger->deprecated));
    }

    #[Test]
    public function getIpDeprecationMentionsFunction(): void
    {
        $logger = \XoopsLogger::getInstance();
        $countBefore = count($logger->deprecated);

        @mod_getIP(true);

        $lastMsg = $logger->deprecated[count($logger->deprecated) - 1];
        $this->assertStringContainsString('mod_getIP', $lastMsg);
        $this->assertStringContainsString('XoopsUserUtility::getIP', $lastMsg);
    }

    // ---------------------------------------------------------------
    // mod_getUnameFromId tests
    // ---------------------------------------------------------------

    #[Test]
    public function getUnameFromIdLogsDeprecation(): void
    {
        $logger = \XoopsLogger::getInstance();
        $countBefore = count($logger->deprecated);

        // XoopsUserUtility::getUnameFromId() with uid 0 calls htmlSpecialChars(null)
        // on PHP 8.1+ which throws TypeError. Catch and verify deprecation was logged first.
        try {
            @mod_getUnameFromId(0);
        } catch (\TypeError $e) {
            // Expected on PHP 8.1+ due to strict typing
        }

        $this->assertGreaterThan($countBefore, count($logger->deprecated));
    }

    #[Test]
    public function getUnameFromIdDeprecationMentionsFunction(): void
    {
        $logger = \XoopsLogger::getInstance();
        $countBefore = count($logger->deprecated);

        try {
            @mod_getUnameFromId(0);
        } catch (\TypeError $e) {
            // Expected
        }

        $lastMsg = $logger->deprecated[count($logger->deprecated) - 1];
        $this->assertStringContainsString('mod_getUnameFromId', $lastMsg);
        $this->assertStringContainsString('XoopsUserUtility::getUnameFromId', $lastMsg);
    }

    // ---------------------------------------------------------------
    // mod_getUnameFromIds tests
    // ---------------------------------------------------------------

    #[Test]
    public function getUnameFromIdsReturnsArray(): void
    {
        $result = @mod_getUnameFromIds([0], false, false);
        $this->assertIsArray($result);
    }

    #[Test]
    public function getUnameFromIdsLogsDeprecation(): void
    {
        $logger = \XoopsLogger::getInstance();
        $countBefore = count($logger->deprecated);

        @mod_getUnameFromIds([0]);

        $this->assertGreaterThan($countBefore, count($logger->deprecated));
    }

    // ---------------------------------------------------------------
    // FRAMEWORKS_ART_FUNCTIONS_USER constant
    // ---------------------------------------------------------------

    #[Test]
    public function frameworksArtFunctionsUserConstantDefined(): void
    {
        $this->assertTrue(defined('FRAMEWORKS_ART_FUNCTIONS_USER'));
    }
}
