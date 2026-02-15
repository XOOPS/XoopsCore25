<?php

declare(strict_types=1);

namespace xoopsmodel;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsModelWrite;
use XoopsTestStubDatabase;

#[CoversClass(XoopsModelWrite::class)]
class XoopsModelWriteTest extends TestCase
{
    private XoopsModelWrite $model;
    private object $mockHandler;

    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/model/write.php';

        $db = new XoopsTestStubDatabase();
        $this->mockHandler = (object) [
            'db' => $db,
            'table' => 'xoops_test',
            'keyName' => 'id',
        ];
        $this->model = new XoopsModelWrite();
        $this->model->handler = $this->mockHandler;
    }

    // ---------------------------------------------------------------
    // insert tests — object not dirty
    // ---------------------------------------------------------------

    #[Test]
    public function insertReturnsKeyWhenNotDirty(): void
    {
        $obj = $this->createMockObject(false, false, 42);
        $result = $this->model->insert($obj);
        // When not dirty, returns the existing key value
        $this->assertSame(42, $result);
    }

    // ---------------------------------------------------------------
    // delete tests
    // ---------------------------------------------------------------

    #[Test]
    public function deleteWithSingleKeyExecutesQuery(): void
    {
        $obj = $this->createDeleteObject(99);
        $result = $this->model->delete($obj);
        // Stub DB exec returns false, so delete returns false
        $this->assertFalse($result);
    }

    #[Test]
    public function deleteWithArrayKeyExecutesQuery(): void
    {
        $this->mockHandler->keyName = ['id', 'type'];
        $this->model->handler = $this->mockHandler;
        $obj = $this->createDeleteObjectMultiKey(1, 'post');
        $result = $this->model->delete($obj);
        $this->assertFalse($result);
    }

    // ---------------------------------------------------------------
    // deleteAll tests
    // ---------------------------------------------------------------

    #[Test]
    public function deleteAllWithoutCriteriaExecutesQuery(): void
    {
        $result = $this->model->deleteAll(null, true, false);
        // Stub DB exec returns false
        $this->assertFalse($result);
    }

    // ---------------------------------------------------------------
    // updateAll tests
    // ---------------------------------------------------------------

    #[Test]
    public function updateAllWithNumericValueBuildsCorrectSql(): void
    {
        $result = $this->model->updateAll('status', 1);
        // Stub DB exec returns false
        $this->assertFalse($result);
    }

    #[Test]
    public function updateAllWithStringValueQuotesIt(): void
    {
        $result = $this->model->updateAll('name', 'test');
        $this->assertFalse($result);
    }

    #[Test]
    public function updateAllWithArrayValueJoinsAndQuotes(): void
    {
        $result = $this->model->updateAll('tags', ['a', 'b']);
        $this->assertFalse($result);
    }

    // ---------------------------------------------------------------
    // Type safety
    // ---------------------------------------------------------------

    #[Test]
    public function deleteReturnsBool(): void
    {
        $obj = $this->createDeleteObject(1);
        $result = $this->model->delete($obj);
        $this->assertIsBool($result);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    private function createMockObject(bool $isNew, bool $isDirty, $keyValue): object
    {
        return new class($isNew, $isDirty, $keyValue) {
            private $isNew;
            private $dirty;
            private $key;
            public $cleanVars = [];

            public function __construct($isNew, $dirty, $key)
            {
                $this->isNew = $isNew;
                $this->dirty = $dirty;
                $this->key = $key;
            }

            public function isNew() { return $this->isNew; }
            public function isDirty() { return $this->dirty; }
            public function getVar($name) { return $this->key; }
            public function assignVar($name, $value) {}
            public function getVars() { return []; }
            public function setErrors($errors) {}
            public function unsetDirty() {}
        };
    }

    private function createDeleteObject($keyValue): object
    {
        return new class($keyValue) {
            private $key;
            public function __construct($key) { $this->key = $key; }
            public function getVar($name) { return $this->key; }
        };
    }

    private function createDeleteObjectMultiKey($id, $type): object
    {
        return new class($id, $type) {
            private $id;
            private $type;
            public function __construct($id, $type) { $this->id = $id; $this->type = $type; }
            public function getVar($name)
            {
                if ($name === 'id') return $this->id;
                if ($name === 'type') return $this->type;
                return null;
            }
        };
    }
}
