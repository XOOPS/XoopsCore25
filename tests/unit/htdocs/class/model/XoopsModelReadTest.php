<?php

declare(strict_types=1);

namespace xoopsmodel;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsModelRead;
use XoopsTestStubDatabase;

#[CoversClass(XoopsModelRead::class)]
class XoopsModelReadTest extends TestCase
{
    private XoopsModelRead $model;
    private $mockHandler;

    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/model/read.php';

        $this->mockHandler = $this->createMockHandler();
        $this->model = new XoopsModelRead();
        // Use reflection since setHandler requires XoopsPersistableObjectHandler
        $this->model->handler = $this->mockHandler;
    }

    private function createMockHandler(): object
    {
        $db = new XoopsTestStubDatabase();
        return (object) [
            'db' => $db,
            'table' => '`xoops_test`',
            'keyName' => 'id',
            'identifierName' => 'name',
        ];
    }

    // ---------------------------------------------------------------
    // getAll tests
    // ---------------------------------------------------------------

    #[Test]
    public function getAllThrowsRuntimeExceptionOnQueryFailure(): void
    {
        // DB stub returns false for query, which triggers RuntimeException
        $this->expectException(\RuntimeException::class);
        $this->model->getAll();
    }

    #[Test]
    public function getAllThrowsWithQueryErrorMessage(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/DB Query Error/');
        $this->model->getAll(null, null, true, true);
    }

    // ---------------------------------------------------------------
    // getIds tests
    // ---------------------------------------------------------------

    #[Test]
    public function getIdsReturnsEmptyArrayWhenQueryFails(): void
    {
        $result = $this->model->getIds();
        $this->assertSame([], $result);
    }

    // ---------------------------------------------------------------
    // getList tests
    // ---------------------------------------------------------------

    #[Test]
    public function getListReturnsEmptyArrayWhenQueryFails(): void
    {
        $result = $this->model->getList();
        $this->assertSame([], $result);
    }

    #[Test]
    public function getListWithLimitAndStart(): void
    {
        $result = $this->model->getList(null, 10, 0);
        $this->assertIsArray($result);
    }

    // ---------------------------------------------------------------
    // Type safety
    // ---------------------------------------------------------------

    #[Test]
    public function getIdsReturnsArray(): void
    {
        $result = $this->model->getIds();
        $this->assertIsArray($result);
    }

    #[Test]
    public function getListReturnsArray(): void
    {
        $result = $this->model->getList();
        $this->assertIsArray($result);
    }
}
