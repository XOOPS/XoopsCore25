<?php

declare(strict_types=1);

namespace xoopsmodel;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsModelStats;
use XoopsTestStubDatabase;

#[CoversClass(XoopsModelStats::class)]
class XoopsModelStatsTest extends TestCase
{
    private XoopsModelStats $model;
    private object $mockHandler;

    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/model/stats.php';

        $db = new XoopsTestStubDatabase();
        $this->mockHandler = (object) [
            'db' => $db,
            'table' => '`xoops_test`',
            'keyName' => 'id',
        ];
        $this->model = new XoopsModelStats();
        $this->model->handler = $this->mockHandler;
    }

    // ---------------------------------------------------------------
    // getCount tests
    // ---------------------------------------------------------------

    #[Test]
    public function getCountReturnsZeroWhenQueryFails(): void
    {
        $result = $this->model->getCount();
        $this->assertSame(0, $result);
    }

    #[Test]
    public function getCountWithNullCriteria(): void
    {
        $result = $this->model->getCount(null);
        $this->assertSame(0, $result);
    }

    #[Test]
    public function getCountReturnTypeIsIntOrArray(): void
    {
        $result = $this->model->getCount();
        $this->assertTrue(is_int($result) || is_array($result));
    }

    // ---------------------------------------------------------------
    // getCounts tests
    // ---------------------------------------------------------------

    #[Test]
    public function getCountsReturnsEmptyArrayWhenQueryFails(): void
    {
        $result = $this->model->getCounts();
        $this->assertSame([], $result);
    }

    #[Test]
    public function getCountsWithNullCriteria(): void
    {
        $result = $this->model->getCounts(null);
        $this->assertIsArray($result);
    }

    // ---------------------------------------------------------------
    // Type safety
    // ---------------------------------------------------------------

    #[Test]
    public function getCountsReturnsArray(): void
    {
        $this->assertIsArray($this->model->getCounts());
    }
}
