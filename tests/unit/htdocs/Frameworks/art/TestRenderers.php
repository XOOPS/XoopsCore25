<?php
/**
 * Test fixture: renderer stubs for mod_loadRenderer() tests.
 *
 * These classes are in the global namespace because mod_loadRenderer()
 * constructs class names like "TestmodWidgetRenderer" without a namespace prefix.
 */

class TestmodWidgetRenderer
{
    private static ?self $inst = null;

    public static function instance(): self
    {
        return self::$inst ??= new self();
    }
}

class DemomodFormRenderer
{
    private static ?self $inst = null;

    public static function instance(): self
    {
        return self::$inst ??= new self();
    }
}
