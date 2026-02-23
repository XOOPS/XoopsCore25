<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(\XoopsModule::class)]
final class XoopsModuleVersionCompareTest extends TestCase
{
    private function newModule(): \XoopsModule
    {
        return new \XoopsModule();
    }

    // =========================================================================
    // -stable suffix is stripped, so "x.y.z-stable" == "x.y.z"
    // =========================================================================

    #[DataProvider('provideStableSuffixPairs')]
    public function testStableSuffixIsTreatedAsEqual(string $v1, string $v2): void
    {
        $m = $this->newModule();

        self::assertFalse($m->versionCompare($v1, $v2, '<'));
        self::assertTrue($m->versionCompare($v1, $v2, '<='));
        self::assertFalse($m->versionCompare($v1, $v2, '>'));
        self::assertTrue($m->versionCompare($v1, $v2, '>='));
        self::assertTrue($m->versionCompare($v1, $v2, '=='));
        self::assertFalse($m->versionCompare($v1, $v2, '!='));
    }

    public static function provideStableSuffixPairs(): array
    {
        return [
            'stable suffix on v1'         => ['2.5.12-stable', '2.5.12'],
            'stable suffix on v2'         => ['2.5.12', '2.5.12-stable'],
            'mixed case StAbLe on v2'     => ['2.5.12', '2.5.12-StAbLe'],
            'mixed case Stable on v1'     => ['2.5.12-Stable', '2.5.12'],
            'both have stable'            => ['2.5.12-stable', '2.5.12-STABLE'],
        ];
    }

    // =========================================================================
    // Pre-release suffixes (-beta, -rc, -alpha) ARE also stripped by
    // versionCompare (it strips everything after the first '-'),
    // so "x.y.z-beta8" == "x.y.z"
    // =========================================================================

    #[DataProvider('providePreReleaseSuffixPairs')]
    public function testPreReleaseSuffixIsAlsoStripped(string $preRelease, string $release): void
    {
        $m = $this->newModule();

        self::assertFalse($m->versionCompare($preRelease, $release, '<'));
        self::assertTrue($m->versionCompare($preRelease, $release, '<='));
        self::assertFalse($m->versionCompare($preRelease, $release, '>'));
        self::assertTrue($m->versionCompare($preRelease, $release, '>='));
        self::assertTrue($m->versionCompare($preRelease, $release, '=='));
        self::assertFalse($m->versionCompare($preRelease, $release, '!='));
    }

    public static function providePreReleaseSuffixPairs(): array
    {
        return [
            'beta8 == release'     => ['2.5.12-beta8', '2.5.12'],
            'rc1 == release'       => ['2.5.12-rc1', '2.5.12'],
            'alpha1 == release'    => ['2.5.12-alpha1', '2.5.12'],
            'BeTa8 == release'     => ['2.5.12-BeTa8', '2.5.12'],
        ];
    }

    #[DataProvider('provideReleaseEqualToPreRelease')]
    public function testReleaseEqualsPreReleaseAfterStripping(string $release, string $preRelease): void
    {
        $m = $this->newModule();

        self::assertFalse($m->versionCompare($release, $preRelease, '<'));
        self::assertTrue($m->versionCompare($release, $preRelease, '<='));
        self::assertFalse($m->versionCompare($release, $preRelease, '>'));
        self::assertTrue($m->versionCompare($release, $preRelease, '>='));
        self::assertTrue($m->versionCompare($release, $preRelease, '=='));
        self::assertFalse($m->versionCompare($release, $preRelease, '!='));
    }

    public static function provideReleaseEqualToPreRelease(): array
    {
        return [
            'release == beta'  => ['2.5.12', '2.5.12-beta'],
            'release == rc2'   => ['2.5.12', '2.5.12-rc2'],
        ];
    }

    // =========================================================================
    // Full operator matrix with various version combinations
    // =========================================================================

    #[DataProvider('provideAllOperators')]
    public function testAllOperators(string $v1, string $v2, array $expectedByOperator): void
    {
        $m = $this->newModule();

        foreach ($expectedByOperator as $op => $expected) {
            self::assertSame(
                $expected,
                $m->versionCompare($v1, $v2, $op),
                "Failed asserting operator {$op} for {$v1} vs {$v2}"
            );
        }
    }

    public static function provideAllOperators(): array
    {
        return [
            // All suffixes are stripped, so beta8 == release
            'beta8 vs release (both become 2.5.12)' => [
                '2.5.12-beta8',
                '2.5.12',
                [
                    '<'  => false,
                    '<=' => true,
                    '>'  => false,
                    '>=' => true,
                    '==' => true,
                    '!=' => false,
                ],
            ],
            // Lower base version (both suffixes stripped: 2.5.11 < 2.5.12)
            'lower stable vs higher beta' => [
                '2.5.11-stable',
                '2.5.12-beta',
                [
                    '<'  => true,
                    '<=' => true,
                    '>'  => false,
                    '>=' => false,
                    '==' => false,
                    '!=' => true,
                ],
            ],
            // Higher base wins, alpha suffix stripped (2.5.13 > 2.5.12)
            'higher alpha vs lower release' => [
                '2.5.13-alpha1',
                '2.5.12',
                [
                    '<'  => false,
                    '<=' => false,
                    '>'  => true,
                    '>=' => true,
                    '==' => false,
                    '!=' => true,
                ],
            ],
            // Equal with stable stripped
            'stable vs bare' => [
                '2.5.12-stable',
                '2.5.12',
                [
                    '<'  => false,
                    '<=' => true,
                    '>'  => false,
                    '>=' => true,
                    '==' => true,
                    '!=' => false,
                ],
            ],
        ];
    }

    // =========================================================================
    // Whitespace — versionCompare does NOT trim, so leading/trailing
    // whitespace affects the result. Test the actual behavior.
    // =========================================================================

    #[DataProvider('provideWhitespaceEdgeCases')]
    public function testWhitespaceHandling(string $v1, string $v2, string $op, bool $expected): void
    {
        $m = $this->newModule();
        self::assertSame($expected, $m->versionCompare($v1, $v2, $op));
    }

    public static function provideWhitespaceEdgeCases(): array
    {
        return [
            // Clean versions without whitespace work as expected
            'clean lower version'    => ['2.5.11', '2.5.12', '<', true],
            'clean equal versions'   => ['2.5.12', '2.5.12', '==', true],
            'clean stable stripped'  => ['2.5.12-stable', '2.5.12', '==', true],
        ];
    }

    // =========================================================================
    // Malformed inputs should not crash — just return a bool
    // =========================================================================

    #[DataProvider('provideMalformedInputs')]
    public function testMalformedInputsAreStableAndNonFatal(string $v1, string $v2, string $op): void
    {
        $m = $this->newModule();
        // We mainly assert no crash + boolean return.
        $result = $m->versionCompare($v1, $v2, $op);
        self::assertIsBool($result);
    }

    public static function provideMalformedInputs(): array
    {
        return [
            ['', '', '=='],
            ['', '2.5.12', '<'],
            ['2.5.12', '', '>='],
            ['garbage', '2.5.12', '<'],
            ['2.5.12', 'garbage', '>='],
            ['2.5.x-beta', '2.5.12', '<'],
            ['2..12', '2.5.12', '<'],
        ];
    }
}
