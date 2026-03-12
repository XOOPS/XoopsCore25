<?php

declare(strict_types=1);

namespace modulessystem\admin\preferences;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Tests for H-6: Empty multi-select preserves old config value (preferences/main.php).
 *
 * When a multi-select form element is submitted with no selections, the
 * corresponding POST key is absent entirely. Request::getVar() then falls
 * back to the default (old value), silently preserving the previous selection
 * instead of clearing it. The fix detects multi-select form types and
 * explicitly sets an empty array when the POST key is absent.
 */
class PreferencesMultiSelectTest extends TestCase
{
    /**
     * Simulate the fixed config save logic for multi-select detection.
     *
     * @param string $formType     The conf_formtype value
     * @param bool   $postKeyExists Whether the POST key exists
     * @param mixed  $postValue     The POST value (if key exists)
     * @param mixed  $oldValue      The old config value
     * @return mixed The resolved new value
     */
    private function resolveConfigValue(string $formType, bool $postKeyExists, $postValue, $oldValue)
    {
        $multiFormTypes = ['select_multi', 'group_multi', 'user_multi', 'theme_multi'];
        if (in_array($formType, $multiFormTypes, true) && !$postKeyExists) {
            return [];
        }
        // Simulate Request::getVar() fallback
        return $postKeyExists ? $postValue : $oldValue;
    }

    #[Test]
    public function emptyMultiSelectClearsValue(): void
    {
        $result = $this->resolveConfigValue('select_multi', false, null, ['old_val1', 'old_val2']);
        $this->assertSame([], $result,
            'Empty multi-select submission must result in empty array, not old values');
    }

    #[Test]
    public function emptyGroupMultiClearsValue(): void
    {
        $result = $this->resolveConfigValue('group_multi', false, null, [1, 2, 3]);
        $this->assertSame([], $result);
    }

    #[Test]
    public function emptyUserMultiClearsValue(): void
    {
        $result = $this->resolveConfigValue('user_multi', false, null, [5, 10]);
        $this->assertSame([], $result);
    }

    #[Test]
    public function emptyThemeMultiClearsValue(): void
    {
        $result = $this->resolveConfigValue('theme_multi', false, null, ['theme1', 'theme2']);
        $this->assertSame([], $result);
    }

    #[Test]
    public function populatedMultiSelectUsesPostValue(): void
    {
        $result = $this->resolveConfigValue('select_multi', true, ['new_val'], ['old_val']);
        $this->assertSame(['new_val'], $result,
            'Multi-select with selections should use POST value');
    }

    #[Test]
    public function singleSelectFallsBackToOldValue(): void
    {
        // For non-multi types, absent POST key should still fall back to old value
        $result = $this->resolveConfigValue('select', false, null, 'old_value');
        $this->assertSame('old_value', $result,
            'Single select should fall back to old value when POST key is absent');
    }

    #[Test]
    public function textboxFallsBackToOldValue(): void
    {
        $result = $this->resolveConfigValue('textbox', false, null, 'old_text');
        $this->assertSame('old_text', $result);
    }

    #[Test]
    public function yesnoFallsBackToOldValue(): void
    {
        $result = $this->resolveConfigValue('yesno', false, null, '1');
        $this->assertSame('1', $result);
    }

    public static function multiFormTypeProvider(): array
    {
        return [
            'select_multi' => ['select_multi'],
            'group_multi'  => ['group_multi'],
            'user_multi'   => ['user_multi'],
            'theme_multi'  => ['theme_multi'],
        ];
    }

    #[Test]
    #[DataProvider('multiFormTypeProvider')]
    public function allMultiFormTypesHandledCorrectly(string $formType): void
    {
        $result = $this->resolveConfigValue($formType, false, null, ['should_be_cleared']);
        $this->assertSame([], $result,
            "$formType with no POST key must yield empty array");
    }

    #[Test]
    public function sourceFileContainsMultiSelectFix(): void
    {
        $source = file_get_contents(XOOPS_ROOT_PATH . '/modules/system/admin/preferences/main.php');
        $this->assertStringContainsString('select_multi', $source);
        $this->assertStringContainsString('group_multi', $source);
        $this->assertStringContainsString('user_multi', $source);
        $this->assertStringContainsString('theme_multi', $source);
        $this->assertStringContainsString(
            "!Request::hasVar(\$confName, 'POST')",
            $source,
            'Save handler must check for absent POST key on multi-select types'
        );
    }
}
