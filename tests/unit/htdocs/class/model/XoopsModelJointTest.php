<?php

declare(strict_types=1);

namespace xoopsmodel;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsModelJoint;
use XoopsTestStubDatabase;

#[CoversClass(XoopsModelJoint::class)]
class XoopsModelJointTest extends TestCase
{
    private XoopsModelJoint $model;
    private object $mockHandler;

    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/model/joint.php';

        $db = new XoopsTestStubDatabase();
        $this->mockHandler = (object) [
            'db' => $db,
            'table' => 'xoops_articles',
            'table_link' => 'xoops_categories',
            'field_link' => 'cat_id',
            'field_object' => 'article_cat',
            'keyName' => 'article_id',
        ];
        $this->model = new XoopsModelJoint();
        $this->model->handler = $this->mockHandler;
    }

    // ---------------------------------------------------------------
    // validateLinks tests
    // ---------------------------------------------------------------

    #[Test]
    public function validateLinksReturnsTrueWhenLinksSet(): void
    {
        $result = $this->model->validateLinks();
        $this->assertTrue($result);
    }

    #[Test]
    public function validateLinksReturnsNullWhenTableLinkEmpty(): void
    {
        $this->mockHandler->table_link = '';
        $result = @$this->model->validateLinks();
        $this->assertNull($result);
    }

    #[Test]
    public function validateLinksReturnsNullWhenFieldLinkEmpty(): void
    {
        $this->mockHandler->field_link = '';
        $result = @$this->model->validateLinks();
        $this->assertNull($result);
    }

    #[Test]
    public function validateLinksSetsFieldObjectFromFieldLinkWhenEmpty(): void
    {
        $this->mockHandler->field_object = '';
        $this->model->validateLinks();
        $this->assertSame('cat_id', $this->mockHandler->field_object);
    }

    // ---------------------------------------------------------------
    // getByLink tests
    // ---------------------------------------------------------------

    #[Test]
    public function getByLinkReturnsEmptyArrayWhenValidationFails(): void
    {
        $this->mockHandler->table_link = '';
        $result = @$this->model->getByLink();
        $this->assertSame([], $result);
    }

    #[Test]
    public function getByLinkThrowsRuntimeExceptionOnQueryFailure(): void
    {
        // With valid links, the query runs but fails (stub returns false)
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/DB Query Error/');
        $this->model->getByLink();
    }

    // ---------------------------------------------------------------
    // getCountByLink tests
    // ---------------------------------------------------------------

    #[Test]
    public function getCountByLinkReturnsNullWhenValidationFails(): void
    {
        $this->mockHandler->table_link = '';
        $result = @$this->model->getCountByLink();
        $this->assertNull($result);
    }

    #[Test]
    public function getCountByLinkReturnsFalseWhenQueryFails(): void
    {
        $result = $this->model->getCountByLink();
        $this->assertFalse($result);
    }

    // ---------------------------------------------------------------
    // getCountsByLink tests
    // ---------------------------------------------------------------

    #[Test]
    public function getCountsByLinkReturnsNullWhenValidationFails(): void
    {
        $this->mockHandler->table_link = '';
        $result = @$this->model->getCountsByLink();
        $this->assertNull($result);
    }

    #[Test]
    public function getCountsByLinkReturnsFalseWhenQueryFails(): void
    {
        $result = $this->model->getCountsByLink();
        $this->assertFalse($result);
    }

    // ---------------------------------------------------------------
    // updateByLink tests
    // ---------------------------------------------------------------

    #[Test]
    public function updateByLinkReturnsNullWhenValidationFails(): void
    {
        $this->mockHandler->table_link = '';
        $result = @$this->model->updateByLink(['status' => 1]);
        $this->assertNull($result);
    }

    // ---------------------------------------------------------------
    // deleteByLink tests
    // ---------------------------------------------------------------

    #[Test]
    public function deleteByLinkReturnsNullWhenValidationFails(): void
    {
        $this->mockHandler->table_link = '';
        $result = @$this->model->deleteByLink();
        $this->assertNull($result);
    }
}
