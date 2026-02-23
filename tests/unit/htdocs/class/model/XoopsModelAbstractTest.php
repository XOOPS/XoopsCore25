<?php

declare(strict_types=1);

namespace xoopsmodel;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsModelAbstract;

#[CoversClass(XoopsModelAbstract::class)]
class XoopsModelAbstractTest extends TestCase
{
    protected function setUp(): void
    {
        require_once XOOPS_ROOT_PATH . '/class/model/xoopsmodel.php';
    }

    // ---------------------------------------------------------------
    // Constructor tests
    // ---------------------------------------------------------------

    #[Test]
    public function constructorWithNoArgs(): void
    {
        $model = new XoopsModelAbstract();
        $this->assertNull($model->handler);
    }

    #[Test]
    public function constructorWithArgsSetsDynamicProperties(): void
    {
        $model = new XoopsModelAbstract(['foo' => 'bar', 'baz' => 42]);
        $this->assertSame('bar', $model->foo);
        $this->assertSame(42, $model->baz);
    }

    // ---------------------------------------------------------------
    // setHandler tests
    // ---------------------------------------------------------------

    #[Test]
    public function setHandlerReturnsFalseForNonObject(): void
    {
        $model = new XoopsModelAbstract();
        $this->assertFalse($model->setHandler(null));
    }

    #[Test]
    public function setHandlerReturnsFalseForWrongObjectType(): void
    {
        $model = new XoopsModelAbstract();
        $this->assertFalse($model->setHandler(new \stdClass()));
    }

    #[Test]
    public function setHandlerReturnsTrueForPersistableObjectHandler(): void
    {
        $model = new XoopsModelAbstract();
        $handler = $this->createMock(\XoopsPersistableObjectHandler::class);
        $this->assertTrue($model->setHandler($handler));
        $this->assertSame($handler, $model->handler);
    }

    #[Test]
    public function setHandlerReturnsFalseForString(): void
    {
        $model = new XoopsModelAbstract();
        $this->assertFalse($model->setHandler('not_a_handler'));
    }

    // ---------------------------------------------------------------
    // setVars tests
    // ---------------------------------------------------------------

    #[Test]
    public function setVarsReturnsTrueAlways(): void
    {
        $model = new XoopsModelAbstract();
        $this->assertTrue($model->setVars(null));
        $this->assertTrue($model->setVars([]));
        $this->assertTrue($model->setVars(['x' => 1]));
    }

    #[Test]
    public function setVarsSetsDynamicProperties(): void
    {
        $model = new XoopsModelAbstract();
        $model->setVars(['alpha' => 'A', 'beta' => 'B']);
        $this->assertSame('A', $model->alpha);
        $this->assertSame('B', $model->beta);
    }

    #[Test]
    public function setVarsWithEmptyArrayDoesNothing(): void
    {
        $model = new XoopsModelAbstract();
        $model->setVars([]);
        // handler should remain as initialized
        $this->assertNull($model->handler);
    }

    #[Test]
    public function setVarsOverwritesExistingProperty(): void
    {
        $model = new XoopsModelAbstract();
        $model->setVars(['test' => 'old']);
        $this->assertSame('old', $model->test);
        $model->setVars(['test' => 'new']);
        $this->assertSame('new', $model->test);
    }

    #[Test]
    public function setVarsWithNullDoesNotThrow(): void
    {
        $model = new XoopsModelAbstract();
        $result = $model->setVars(null);
        $this->assertTrue($result);
    }

    #[Test]
    public function setVarsWithNonArrayDoesNotThrow(): void
    {
        $model = new XoopsModelAbstract();
        $result = $model->setVars('string');
        $this->assertTrue($result);
    }
}
