<?php
namespace xoopsforms;

use PHPUnit\Framework\TestCase;

/**
 * Tests for XoopsFormRenderer singleton.
 *
 * Source: class/xoopsform/renderer/XoopsFormRenderer.php
 */
class XoopsFormRendererTest extends TestCase
{
    protected function setUp(): void
    {
        // Load required classes
        xoops_load('XoopsFormRenderer');
        xoops_load('XoopsFormRendererInterface');
        xoops_load('XoopsFormRendererLegacy');

        // Reset the singleton's renderer property between tests for isolation.
        $ref = new \ReflectionClass('XoopsFormRenderer');
        $prop = $ref->getProperty('renderer');
        $prop->setAccessible(true);
        $prop->setValue(\XoopsFormRenderer::getInstance(), null);
    }

    /**
     * getInstance() must always return the same object reference.
     */
    public function testGetInstanceReturnsSameObject(): void
    {
        $a = \XoopsFormRenderer::getInstance();
        $b = \XoopsFormRenderer::getInstance();

        $this->assertSame($a, $b, 'getInstance() must return the same singleton instance');
    }

    /**
     * getInstance() must return an instance of XoopsFormRenderer.
     */
    public function testGetInstanceReturnsCorrectType(): void
    {
        $instance = \XoopsFormRenderer::getInstance();

        $this->assertInstanceOf(\XoopsFormRenderer::class, $instance);
    }

    /**
     * get() must return an XoopsFormRendererInterface when no renderer has been set.
     * It defaults to XoopsFormRendererLegacy.
     */
    public function testGetReturnsRendererInterface(): void
    {
        $renderer = \XoopsFormRenderer::getInstance()->get();

        $this->assertInstanceOf(
            \XoopsFormRendererInterface::class,
            $renderer,
            'get() must return an XoopsFormRendererInterface implementation'
        );
    }

    /**
     * get() must return XoopsFormRendererLegacy when no renderer has been explicitly set.
     */
    public function testGetReturnsLegacyRendererByDefault(): void
    {
        $renderer = \XoopsFormRenderer::getInstance()->get();

        $this->assertInstanceOf(
            \XoopsFormRendererLegacy::class,
            $renderer,
            'get() must return XoopsFormRendererLegacy by default'
        );
    }

    /**
     * set() must change the renderer returned by get().
     */
    public function testSetChangesRenderer(): void
    {
        $mock = $this->createMock(\XoopsFormRendererInterface::class);
        $instance = \XoopsFormRenderer::getInstance();

        $instance->set($mock);
        $result = $instance->get();

        $this->assertSame(
            $mock,
            $result,
            'set() must change the renderer returned by get()'
        );
    }

    /**
     * get() after set() must not return the legacy renderer.
     */
    public function testSetOverridesDefaultRenderer(): void
    {
        $mock = $this->createMock(\XoopsFormRendererInterface::class);
        $instance = \XoopsFormRenderer::getInstance();

        $instance->set($mock);

        $this->assertNotInstanceOf(
            \XoopsFormRendererLegacy::class,
            $instance->get(),
            'After set(), get() must not return the default legacy renderer'
        );
    }

    /**
     * Calling get() multiple times without set() must return the same object.
     */
    public function testGetReturnsSameRendererOnRepeatedCalls(): void
    {
        $instance = \XoopsFormRenderer::getInstance();
        $first = $instance->get();
        $second = $instance->get();

        $this->assertSame(
            $first,
            $second,
            'get() must return the same renderer instance on repeated calls'
        );
    }

    /**
     * Cloning the singleton must throw a LogicException.
     */
    public function testCloneThrowsLogicException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\XoopsFormRenderer::NOT_PERMITTED);

        $instance = \XoopsFormRenderer::getInstance();
        clone $instance;
    }

    /**
     * Waking up (unserializing) the singleton must throw a LogicException.
     */
    public function testWakeupThrowsLogicException(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(\XoopsFormRenderer::NOT_PERMITTED);

        $instance = \XoopsFormRenderer::getInstance();
        $instance->__wakeup();
    }

    /**
     * NOT_PERMITTED constant must have the expected value.
     */
    public function testNotPermittedConstantValue(): void
    {
        $this->assertSame(
            'Not supported for Singleton',
            \XoopsFormRenderer::NOT_PERMITTED
        );
    }

    /**
     * The exception message from __clone must match NOT_PERMITTED.
     */
    public function testCloneExceptionMessageMatchesConstant(): void
    {
        $instance = \XoopsFormRenderer::getInstance();
        try {
            clone $instance;
            $this->fail('Expected LogicException was not thrown');
        } catch (\LogicException $e) {
            $this->assertSame(
                \XoopsFormRenderer::NOT_PERMITTED,
                $e->getMessage()
            );
        }
    }

    /**
     * The exception message from __wakeup must match NOT_PERMITTED.
     */
    public function testWakeupExceptionMessageMatchesConstant(): void
    {
        $instance = \XoopsFormRenderer::getInstance();
        try {
            $instance->__wakeup();
            $this->fail('Expected LogicException was not thrown');
        } catch (\LogicException $e) {
            $this->assertSame(
                \XoopsFormRenderer::NOT_PERMITTED,
                $e->getMessage()
            );
        }
    }
}
