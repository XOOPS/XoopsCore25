<?php

declare(strict_types=1);

namespace modulesprotector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(\ProtectorCorePreload::class)]
class ProtectorCorePreloadTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            require_once XOOPS_ROOT_PATH . '/modules/protector/preloads/core.php';
            self::$loaded = true;
        }
    }

    // ---------------------------------------------------------------
    // Class structure
    // ---------------------------------------------------------------

    #[Test]
    public function classExtendsXoopsPreloadItem(): void
    {
        $this->assertTrue(is_subclass_of(\ProtectorCorePreload::class, \XoopsPreloadItem::class));
    }

    #[Test]
    public function hasEventCoreIncludeCommonStartMethod(): void
    {
        $this->assertTrue(method_exists(\ProtectorCorePreload::class, 'eventCoreIncludeCommonStart'));
    }

    #[Test]
    public function hasEventCoreIncludeCommonEndMethod(): void
    {
        $this->assertTrue(method_exists(\ProtectorCorePreload::class, 'eventCoreIncludeCommonEnd'));
    }

    #[Test]
    public function hasEventCoreClassDatabaseMethod(): void
    {
        $this->assertTrue(
            method_exists(\ProtectorCorePreload::class, 'eventCoreClassDatabaseDatabasefactoryConnection')
        );
    }

    // ---------------------------------------------------------------
    // eventCoreClassDatabaseDatabasefactoryConnection
    // ---------------------------------------------------------------

    #[Test]
    public function databaseEventSetsAlternativeClassWhenDefined(): void
    {
        // Define the constant for this test
        if (!defined('XOOPS_DB_ALTERNATIVE')) {
            define('XOOPS_DB_ALTERNATIVE', 'ProtectorMySQLDatabase');
        }

        // Ensure the class exists (loaded by ProtectorMysqlDatabaseTest or here)
        if (!class_exists('ProtectorMySQLDatabase', false)) {
            if (!class_exists('Protector', false)) {
                require_once XOOPS_PATH . '/modules/protector/class/protector.php';
            }
            require_once XOOPS_PATH . '/modules/protector/class/ProtectorMysqlDatabase.class.php';
        }

        // XOOPS event system passes args with references: array(&$class)
        $class = 'XoopsMySQLDatabase';
        $args = [&$class];
        \ProtectorCorePreload::eventCoreClassDatabaseDatabasefactoryConnection($args);

        $this->assertSame('ProtectorMySQLDatabase', $class);
    }

    // ---------------------------------------------------------------
    // Static method signatures
    // ---------------------------------------------------------------

    #[Test]
    public function allEventMethodsAreStatic(): void
    {
        $ref = new \ReflectionClass(\ProtectorCorePreload::class);

        $methods = [
            'eventCoreIncludeCommonStart',
            'eventCoreIncludeCommonEnd',
            'eventCoreClassDatabaseDatabasefactoryConnection',
        ];

        foreach ($methods as $methodName) {
            $method = $ref->getMethod($methodName);
            $this->assertTrue($method->isStatic(), "$methodName should be static");
        }
    }

    #[Test]
    public function allEventMethodsArePublic(): void
    {
        $ref = new \ReflectionClass(\ProtectorCorePreload::class);

        $methods = [
            'eventCoreIncludeCommonStart',
            'eventCoreIncludeCommonEnd',
            'eventCoreClassDatabaseDatabasefactoryConnection',
        ];

        foreach ($methods as $methodName) {
            $method = $ref->getMethod($methodName);
            $this->assertTrue($method->isPublic(), "$methodName should be public");
        }
    }

    #[Test]
    public function eventMethodsAcceptOneParameter(): void
    {
        $ref = new \ReflectionClass(\ProtectorCorePreload::class);

        $methods = [
            'eventCoreIncludeCommonStart',
            'eventCoreIncludeCommonEnd',
            'eventCoreClassDatabaseDatabasefactoryConnection',
        ];

        foreach ($methods as $methodName) {
            $method = $ref->getMethod($methodName);
            $this->assertSame(1, $method->getNumberOfParameters(), "$methodName should accept 1 parameter");
        }
    }
}
