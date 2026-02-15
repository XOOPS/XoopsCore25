<?php

declare(strict_types=1);

namespace xoopsdatabase;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsDatabaseFactory;

#[CoversClass(XoopsDatabaseFactory::class)]
class XoopsDatabaseFactoryTest extends TestCase
{
    // ---------------------------------------------------------------
    // Constructor tests
    // ---------------------------------------------------------------

    #[Test]
    public function constructorDoesNotThrow(): void
    {
        $factory = new XoopsDatabaseFactory();
        $this->assertInstanceOf(XoopsDatabaseFactory::class, $factory);
    }

    // ---------------------------------------------------------------
    // getDatabaseConnection tests
    // ---------------------------------------------------------------

    #[Test]
    public function getDatabaseConnectionReturnsObject(): void
    {
        // The bootstrap injects XoopsTestStubDatabase via a preload event
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->assertIsObject($db);
    }

    #[Test]
    public function getDatabaseConnectionReturnsSameInstance(): void
    {
        $db1 = XoopsDatabaseFactory::getDatabaseConnection();
        $db2 = XoopsDatabaseFactory::getDatabaseConnection();
        $this->assertSame($db1, $db2);
    }

    #[Test]
    public function getDatabaseConnectionHasPrefixMethod(): void
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->assertTrue(method_exists($db, 'prefix'));
    }

    #[Test]
    public function getDatabaseConnectionHasQuoteMethod(): void
    {
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->assertTrue(method_exists($db, 'quote'));
    }
}
