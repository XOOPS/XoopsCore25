<?php

declare(strict_types=1);

namespace frameworkstextsanitizer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use MyTextSanitizerExtended;
use MyTextSanitizer;

#[CoversClass(MyTextSanitizerExtended::class)]
class MyTextSanitizerExtendedTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            // Initialize logger before any tests to avoid "risky" handler warnings
            if (!isset($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
            }
            require_once XOOPS_ROOT_PATH . '/Frameworks/textsanitizer/module.textsanitizer.php';
            self::$loaded = true;
        }
    }

    // ---------------------------------------------------------------
    // Inheritance tests
    // ---------------------------------------------------------------

    #[Test]
    public function classExtendsMyTextSanitizer(): void
    {
        $this->assertTrue(is_subclass_of(MyTextSanitizerExtended::class, MyTextSanitizer::class));
    }

    #[Test]
    public function instanceOfMyTextSanitizer(): void
    {
        $ext = MyTextSanitizerExtended::getInstance();
        $this->assertInstanceOf(MyTextSanitizer::class, $ext);
    }

    #[Test]
    public function getInstanceReturnsSingleton(): void
    {
        // getInstance() is inherited from MyTextSanitizer and returns MyTextSanitizer type
        // The singleton pattern means the first call determines the concrete type
        $ext = MyTextSanitizerExtended::getInstance();
        $this->assertInstanceOf(MyTextSanitizer::class, $ext);
    }

    // ---------------------------------------------------------------
    // Inherited method availability
    // ---------------------------------------------------------------

    #[Test]
    public function hasHtmlSpecialCharsMethod(): void
    {
        $this->assertTrue(method_exists(MyTextSanitizerExtended::class, 'htmlSpecialChars'));
    }

    #[Test]
    public function hasStripSlashesGpcMethod(): void
    {
        $this->assertTrue(method_exists(MyTextSanitizerExtended::class, 'stripSlashesGPC'));
    }

    #[Test]
    public function hasMakeClickableMethod(): void
    {
        $this->assertTrue(method_exists(MyTextSanitizerExtended::class, 'makeClickable'));
    }

    // ---------------------------------------------------------------
    // Functional tests via inherited methods
    // ---------------------------------------------------------------

    #[Test]
    public function htmlSpecialCharsEscapesHtml(): void
    {
        $ext = MyTextSanitizerExtended::getInstance();
        $input = '<script>alert("xss")</script>';
        $result = $ext->htmlSpecialChars($input);
        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    #[Test]
    public function stripSlashesGpcReturnsString(): void
    {
        // Ensure logger exists — stripSlashesGPC may log deprecation
        if (!isset($GLOBALS['xoopsLogger'])) {
            $GLOBALS['xoopsLogger'] = \XoopsLogger::getInstance();
        }
        $ext = MyTextSanitizerExtended::getInstance();
        $input = "test string";
        $result = $ext->stripSlashesGPC($input);
        // stripSlashesGPC returns the string (no-op since magic_quotes removed)
        $this->assertIsString($result);
        $this->assertSame($input, $result);
    }
}
