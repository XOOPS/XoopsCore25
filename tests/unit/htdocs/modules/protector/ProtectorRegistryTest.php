<?php

declare(strict_types=1);

namespace modulesprotector;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(\ProtectorRegistry::class)]
class ProtectorRegistryTest extends TestCase
{
    private static bool $loaded = false;

    public static function setUpBeforeClass(): void
    {
        if (!self::$loaded) {
            require_once XOOPS_PATH . '/modules/protector/class/registry.php';
            self::$loaded = true;
        }
    }

    protected function setUp(): void
    {
        // Reset registry state between tests
        $reg = \ProtectorRegistry::getInstance();
        $reg->unsetAll();
    }

    // ---------------------------------------------------------------
    // Singleton
    // ---------------------------------------------------------------

    #[Test]
    public function getInstanceReturnsSameInstance(): void
    {
        $a = \ProtectorRegistry::getInstance();
        $b = \ProtectorRegistry::getInstance();
        $this->assertSame($a, $b);
    }

    #[Test]
    public function getInstanceReturnsProtectorRegistry(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $this->assertInstanceOf(\ProtectorRegistry::class, $reg);
    }

    // ---------------------------------------------------------------
    // setEntry / getEntry
    // ---------------------------------------------------------------

    #[Test]
    public function setEntryReturnsTrueOnSuccess(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $this->assertTrue($reg->setEntry('key1', 'value1'));
    }

    #[Test]
    public function getEntryReturnsSetValue(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('foo', 'bar');
        $this->assertSame('bar', $reg->getEntry('foo'));
    }

    #[Test]
    public function getEntryReturnsNullForMissingKey(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $this->assertNull($reg->getEntry('nonexistent'));
    }

    #[Test]
    public function setEntryStoresIntValue(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('count', 42);
        $this->assertSame(42, $reg->getEntry('count'));
    }

    #[Test]
    public function setEntryStoresArrayValue(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $data = ['a' => 1, 'b' => 2];
        $reg->setEntry('config', $data);
        $this->assertSame($data, $reg->getEntry('config'));
    }

    #[Test]
    public function setEntryStoresObjectValue(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $obj = new \stdClass();
        $obj->name = 'test';
        $reg->setEntry('obj', $obj);
        $this->assertSame($obj, $reg->getEntry('obj'));
    }

    #[Test]
    public function setEntryOverwritesExistingValue(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('key', 'first');
        $reg->setEntry('key', 'second');
        $this->assertSame('second', $reg->getEntry('key'));
    }

    // ---------------------------------------------------------------
    // isEntry
    // ---------------------------------------------------------------

    #[Test]
    public function isEntryReturnsTrueForExistingKey(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('exists', 'yes');
        $this->assertTrue($reg->isEntry('exists'));
    }

    #[Test]
    public function isEntryReturnsFalseForMissingKey(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $this->assertFalse($reg->isEntry('missing'));
    }

    #[Test]
    public function isEntryReturnsFalseForNullValue(): void
    {
        // isEntry checks getEntry() !== null, so null value means "not set"
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('nullval', null);
        $this->assertFalse($reg->isEntry('nullval'));
    }

    // ---------------------------------------------------------------
    // unsetEntry
    // ---------------------------------------------------------------

    #[Test]
    public function unsetEntryRemovesKey(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('temp', 'data');
        $reg->unsetEntry('temp');
        $this->assertNull($reg->getEntry('temp'));
    }

    #[Test]
    public function unsetEntryDoesNotAffectOtherKeys(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('keep', 'yes');
        $reg->setEntry('remove', 'no');
        $reg->unsetEntry('remove');
        $this->assertSame('yes', $reg->getEntry('keep'));
    }

    // ---------------------------------------------------------------
    // lockEntry / unlockEntry / isLocked
    // ---------------------------------------------------------------

    #[Test]
    public function lockEntryReturnsTrue(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $this->assertTrue($reg->lockEntry('mykey'));
    }

    #[Test]
    public function isLockedReturnsTrueForLockedKey(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->lockEntry('locked');
        $this->assertTrue($reg->isLocked('locked'));
    }

    #[Test]
    public function isLockedReturnsFalseForUnlockedKey(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $this->assertFalse($reg->isLocked('notlocked'));
    }

    #[Test]
    public function setEntryReturnsFalseWhenLocked(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('protected', 'original');
        $reg->lockEntry('protected');

        // Should trigger E_USER_WARNING and return false
        $result = @$reg->setEntry('protected', 'changed');
        $this->assertFalse($result);
    }

    #[Test]
    public function lockedEntryPreservesValue(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('protected', 'original');
        $reg->lockEntry('protected');
        @$reg->setEntry('protected', 'changed');
        $this->assertSame('original', $reg->getEntry('protected'));
    }

    #[Test]
    public function setEntryOnLockedKeyTriggersWarning(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->lockEntry('warnkey');
        $warningTriggered = false;
        set_error_handler(function ($errno, $errstr) use (&$warningTriggered) {
            if ($errno === E_USER_WARNING) {
                $warningTriggered = true;
            }
            return true;
        });
        $reg->setEntry('warnkey', 'value');
        restore_error_handler();
        $this->assertTrue($warningTriggered);
    }

    #[Test]
    public function unlockEntryAllowsSetAgain(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('toggle', 'v1');
        $reg->lockEntry('toggle');
        @$reg->setEntry('toggle', 'v2'); // blocked
        $this->assertSame('v1', $reg->getEntry('toggle'));

        $reg->unlockEntry('toggle');
        $reg->setEntry('toggle', 'v3'); // should work now
        $this->assertSame('v3', $reg->getEntry('toggle'));
    }

    #[Test]
    public function unlockEntryMakesIsLockedReturnFalse(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->lockEntry('x');
        $reg->unlockEntry('x');
        $this->assertFalse($reg->isLocked('x'));
    }

    // ---------------------------------------------------------------
    // unsetAll
    // ---------------------------------------------------------------

    #[Test]
    public function unsetAllClearsEntries(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('a', 1);
        $reg->setEntry('b', 2);
        $reg->unsetAll();
        $this->assertNull($reg->getEntry('a'));
        $this->assertNull($reg->getEntry('b'));
    }

    #[Test]
    public function unsetAllClearsLocks(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->lockEntry('locked1');
        $reg->lockEntry('locked2');
        $reg->unsetAll();
        $this->assertFalse($reg->isLocked('locked1'));
        $this->assertFalse($reg->isLocked('locked2'));
    }

    #[Test]
    public function unsetAllResetsEntriesArray(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('item', 'data');
        $reg->unsetAll();
        $this->assertSame([], $reg->_entries);
    }

    #[Test]
    public function unsetAllResetsLocksArray(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->lockEntry('item');
        $reg->unsetAll();
        $this->assertSame([], $reg->_locks);
    }

    // ---------------------------------------------------------------
    // Multiple entries
    // ---------------------------------------------------------------

    #[Test]
    public function multipleEntriesStoredIndependently(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('k1', 'v1');
        $reg->setEntry('k2', 'v2');
        $reg->setEntry('k3', 'v3');
        $this->assertSame('v1', $reg->getEntry('k1'));
        $this->assertSame('v2', $reg->getEntry('k2'));
        $this->assertSame('v3', $reg->getEntry('k3'));
    }

    #[Test]
    public function lockingOneKeyDoesNotAffectOthers(): void
    {
        $reg = \ProtectorRegistry::getInstance();
        $reg->setEntry('a', 1);
        $reg->setEntry('b', 2);
        $reg->lockEntry('a');
        $result = $reg->setEntry('b', 22);
        $this->assertTrue($result);
        $this->assertSame(22, $reg->getEntry('b'));
    }
}
