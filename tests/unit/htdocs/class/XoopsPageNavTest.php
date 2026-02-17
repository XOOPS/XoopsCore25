<?php

declare(strict_types=1);

namespace xoopsclass;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

/**
 * Unit tests for XoopsPageNav.
 *
 * The render methods (renderNav, renderSelect, renderImageNav) delegate to
 * the private displayPageNav() method which depends on Smarty templates and
 * XoopsTpl. We can reliably test:
 *   - Constructor property assignment and type casting
 *   - Constructor extra_arg handling logic
 *   - Constructor URL building from $_SERVER['PHP_SELF'] and start_name
 *   - Early-return paths in render methods (empty string when pagination is unnecessary)
 *
 */
#[CoversClass(\XoopsPageNav::class)]
class XoopsPageNavTest extends TestCase
{
    /**
     * @var string|null Original PHP_SELF value to restore after each test.
     */
    private ?string $originalPhpSelf = null;

    protected function setUp(): void
    {
        // Preserve the original $_SERVER['PHP_SELF'] if it exists
        $this->originalPhpSelf = $_SERVER['PHP_SELF'] ?? null;
        // Set a known default for tests
        $_SERVER['PHP_SELF'] = '/modules/news/index.php';
    }

    protected function tearDown(): void
    {
        // Restore original value
        if ($this->originalPhpSelf !== null) {
            $_SERVER['PHP_SELF'] = $this->originalPhpSelf;
        } else {
            unset($_SERVER['PHP_SELF']);
        }
    }

    // =========================================================================
    // Constructor — property assignment
    // =========================================================================

    public function testConstructorSetsTotal(): void
    {
        $nav = new \XoopsPageNav(100, 10, 0);
        $this->assertSame(100, $nav->total);
    }

    public function testConstructorSetsPerpage(): void
    {
        $nav = new \XoopsPageNav(100, 10, 0);
        $this->assertSame(10, $nav->perpage);
    }

    public function testConstructorSetsCurrent(): void
    {
        $nav = new \XoopsPageNav(100, 10, 20);
        $this->assertSame(20, $nav->current);
    }

    public function testConstructorCastsTotalToInt(): void
    {
        $nav = new \XoopsPageNav('50', 10, 0);
        $this->assertSame(50, $nav->total);
    }

    public function testConstructorCastsPerpageToInt(): void
    {
        $nav = new \XoopsPageNav(100, '15', 0);
        $this->assertSame(15, $nav->perpage);
    }

    public function testConstructorCastsCurrentToInt(): void
    {
        $nav = new \XoopsPageNav(100, 10, '30');
        $this->assertSame(30, $nav->current);
    }

    public function testConstructorWithZeroTotal(): void
    {
        $nav = new \XoopsPageNav(0, 10, 0);
        $this->assertSame(0, $nav->total);
    }

    public function testConstructorWithNegativeValues(): void
    {
        $nav = new \XoopsPageNav(-5, -10, -20);
        $this->assertSame(-5, $nav->total);
        $this->assertSame(-10, $nav->perpage);
        $this->assertSame(-20, $nav->current);
    }

    public function testConstructorWithLargeValues(): void
    {
        $nav = new \XoopsPageNav(1000000, 50, 999950);
        $this->assertSame(1000000, $nav->total);
        $this->assertSame(50, $nav->perpage);
        $this->assertSame(999950, $nav->current);
    }

    // =========================================================================
    // Constructor — extra_arg handling
    // =========================================================================

    public function testConstructorEmptyExtraArgStaysEmpty(): void
    {
        $nav = new \XoopsPageNav(100, 10, 0, 'start', '');
        $this->assertSame('', $nav->extra);
    }

    public function testConstructorExtraArgGetsPrepended(): void
    {
        // The condition: $extra_arg != '' && (substr(-5) !== '&amp;' || substr(-1) !== '&')
        // For 'foo=bar': last 5 != '&amp;' is true => OR short-circuits => prepend
        $nav = new \XoopsPageNav(100, 10, 0, 'start', 'foo=bar');
        $this->assertSame('&amp;foo=bar', $nav->extra);
    }

    public function testConstructorExtraArgEndingWithAmpersandEntity(): void
    {
        // For '&amp;': substr(-5) === '&amp;' is true, so first part is false.
        // But substr(-1) === ';' which !== '&', so second part is true.
        // false || true = true => condition is true => prepend happens
        $nav = new \XoopsPageNav(100, 10, 0, 'start', '&amp;');
        $this->assertSame('&amp;&amp;', $nav->extra);
    }

    public function testConstructorExtraArgEndingWithAmpersand(): void
    {
        // For 'test&': substr(-5) !== '&amp;' is true => OR short-circuits => prepend
        $nav = new \XoopsPageNav(100, 10, 0, 'start', 'test&');
        $this->assertSame('&amp;test&', $nav->extra);
    }

    public function testConstructorExtraArgAlreadyHasAmpPrefix(): void
    {
        // Even if it starts with '&amp;', the condition still prepends
        // because it checks the END of the string, not the beginning
        $nav = new \XoopsPageNav(100, 10, 0, 'start', '&amp;foo=bar');
        $this->assertSame('&amp;&amp;foo=bar', $nav->extra);
    }

    public function testConstructorExtraArgSingleCharacter(): void
    {
        $nav = new \XoopsPageNav(100, 10, 0, 'start', 'x');
        $this->assertSame('&amp;x', $nav->extra);
    }

    public function testConstructorExtraArgWithMultipleParams(): void
    {
        $nav = new \XoopsPageNav(100, 10, 0, 'start', 'cat=5&amp;sort=title');
        $this->assertSame('&amp;cat=5&amp;sort=title', $nav->extra);
    }

    // =========================================================================
    // Constructor — URL building
    // =========================================================================

    public function testConstructorUrlContainsPhpSelf(): void
    {
        $_SERVER['PHP_SELF'] = '/modules/news/index.php';
        $nav = new \XoopsPageNav(100, 10, 0);
        $this->assertStringContainsString('/modules/news/index.php', $nav->url);
    }

    public function testConstructorUrlContainsDefaultStartName(): void
    {
        $nav = new \XoopsPageNav(100, 10, 0);
        $this->assertStringContainsString('?start=', $nav->url);
    }

    public function testConstructorUrlContainsCustomStartName(): void
    {
        $nav = new \XoopsPageNav(100, 10, 0, 'offset');
        $this->assertStringContainsString('?offset=', $nav->url);
    }

    public function testConstructorUrlTrimsStartName(): void
    {
        $nav = new \XoopsPageNav(100, 10, 0, '  page  ');
        $this->assertStringContainsString('?page=', $nav->url);
    }

    public function testConstructorUrlEndsWithEquals(): void
    {
        $nav = new \XoopsPageNav(100, 10, 0, 'start');
        $this->assertStringEndsWith('=', $nav->url);
    }

    public function testConstructorUrlHtmlEncodesPhpSelf(): void
    {
        // htmlspecialchars should encode special characters
        $_SERVER['PHP_SELF'] = '/test.php?a=1&b=2';
        $nav = new \XoopsPageNav(100, 10, 0);
        // The '&' in PHP_SELF should be encoded to '&amp;'
        $this->assertStringContainsString('&amp;', $nav->url);
        $this->assertStringNotContainsString('&b=', $nav->url);
    }

    public function testConstructorUrlWithEmptyPhpSelf(): void
    {
        $_SERVER['PHP_SELF'] = '';
        $nav = new \XoopsPageNav(100, 10, 0, 'start');
        $this->assertSame('?start=', $nav->url);
    }

    public function testConstructorUrlWithRootPhpSelf(): void
    {
        $_SERVER['PHP_SELF'] = '/index.php';
        $nav = new \XoopsPageNav(100, 10, 0, 'start');
        $this->assertSame('/index.php?start=', $nav->url);
    }

    // =========================================================================
    // renderNav — early return paths
    // =========================================================================

    public function testRenderNavReturnsEmptyWhenTotalLessThanPerpage(): void
    {
        $nav = new \XoopsPageNav(5, 10, 0);
        $this->assertSame('', $nav->renderNav());
    }

    public function testRenderNavReturnsEmptyWhenTotalEqualsPerpage(): void
    {
        // renderNav checks: $this->total <= $this->perpage
        $nav = new \XoopsPageNav(10, 10, 0);
        $this->assertSame('', $nav->renderNav());
    }

    public function testRenderNavReturnsEmptyWhenTotalIsZero(): void
    {
        $nav = new \XoopsPageNav(0, 10, 0);
        $this->assertSame('', $nav->renderNav());
    }

    public function testRenderNavReturnsEmptyWhenTotalIsOne(): void
    {
        $nav = new \XoopsPageNav(1, 10, 0);
        $this->assertSame('', $nav->renderNav());
    }

    public function testRenderNavReturnsEmptyWhenPerpageEqualsTotal(): void
    {
        $nav = new \XoopsPageNav(50, 50, 0);
        $this->assertSame('', $nav->renderNav());
    }

    public function testRenderNavReturnsEmptyWhenPerpageIsZeroAndTotalIsZero(): void
    {
        // total(0) <= perpage(0) is true => returns ''
        $nav = new \XoopsPageNav(0, 0, 0);
        $this->assertSame('', $nav->renderNav());
    }

    // =========================================================================
    // renderSelect — early return paths
    // =========================================================================

    public function testRenderSelectReturnsEmptyWhenTotalLessThanPerpage(): void
    {
        // renderSelect checks: $this->total < $this->perpage (strict less than)
        $nav = new \XoopsPageNav(5, 10, 0);
        $this->assertSame('', $nav->renderSelect());
    }

    public function testRenderSelectReturnsEmptyWhenTotalIsZero(): void
    {
        $nav = new \XoopsPageNav(0, 10, 0);
        $this->assertSame('', $nav->renderSelect());
    }

    public function testRenderSelectReturnsEmptyWhenTotalIsOne(): void
    {
        $nav = new \XoopsPageNav(1, 10, 0);
        $this->assertSame('', $nav->renderSelect());
    }

    public function testRenderSelectReturnsEmptyWhenTotalEqualsOne(): void
    {
        // total(1) < perpage(10) => true => returns ''
        $nav = new \XoopsPageNav(1, 10, 0);
        $this->assertSame('', $nav->renderSelect(true));
    }

    // =========================================================================
    // renderImageNav — early return paths
    // =========================================================================

    public function testRenderImageNavReturnsEmptyWhenTotalLessThanPerpage(): void
    {
        // renderImageNav checks: $this->total < $this->perpage (strict less than)
        $nav = new \XoopsPageNav(5, 10, 0);
        $this->assertSame('', $nav->renderImageNav());
    }

    public function testRenderImageNavReturnsEmptyWhenTotalIsZero(): void
    {
        $nav = new \XoopsPageNav(0, 10, 0);
        $this->assertSame('', $nav->renderImageNav());
    }

    public function testRenderImageNavReturnsEmptyWhenTotalIsOne(): void
    {
        $nav = new \XoopsPageNav(1, 10, 0);
        $this->assertSame('', $nav->renderImageNav());
    }

    // =========================================================================
    // renderNav vs renderSelect/renderImageNav — boundary difference
    // =========================================================================

    public function testRenderNavReturnsEmptyAtBoundaryWhereSelectMayNot(): void
    {
        // total == perpage: renderNav returns '' (uses <=), renderSelect uses (<)
        // total(10) == perpage(10): renderNav => total <= perpage => '' (yes)
        //                           renderSelect => total < perpage => false, goes further
        // But then total_pages = ceil(10/10) = 1, which is NOT > 1, so renderSelect also returns ''
        $nav = new \XoopsPageNav(10, 10, 0);
        $this->assertSame('', $nav->renderNav());
        $this->assertSame('', $nav->renderSelect());
        $this->assertSame('', $nav->renderImageNav());
    }

    // =========================================================================
    // Data provider tests — constructor variations
    // =========================================================================

    /**
     * @return array<string, array{int|string, int|string, int|string, int, int, int}>
     */
    public static function provideConstructorValues(): array
    {
        return [
            'all zeros'           => [0, 0, 0, 0, 0, 0],
            'basic values'        => [100, 10, 0, 100, 10, 0],
            'string numbers'      => ['200', '20', '40', 200, 20, 40],
            'large total'         => [99999, 25, 75, 99999, 25, 75],
            'perpage of one'      => [50, 1, 10, 50, 1, 10],
            'current at end'      => [100, 10, 90, 100, 10, 90],
            'negative total'      => [-1, 10, 0, -1, 10, 0],
            'float-like strings'  => ['10', '5', '3', 10, 5, 3],
        ];
    }

    #[DataProvider('provideConstructorValues')]
    public function testConstructorPropertyAssignment(
        $totalInput,
        $perpageInput,
        $currentInput,
        int $expectedTotal,
        int $expectedPerpage,
        int $expectedCurrent
    ): void {
        $nav = new \XoopsPageNav($totalInput, $perpageInput, $currentInput);
        $this->assertSame($expectedTotal, $nav->total);
        $this->assertSame($expectedPerpage, $nav->perpage);
        $this->assertSame($expectedCurrent, $nav->current);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideExtraArgValues(): array
    {
        return [
            'empty string'            => ['', ''],
            'simple param'            => ['cat=5', '&amp;cat=5'],
            'multiple params'         => ['cat=5&amp;sort=asc', '&amp;cat=5&amp;sort=asc'],
            'ends with ampersand'     => ['test&', '&amp;test&'],
            'ends with amp entity'    => ['&amp;', '&amp;&amp;'],
            'single char'             => ['a', '&amp;a'],
            'numeric value'           => ['123', '&amp;123'],
            'equals sign only'        => ['=', '&amp;='],
            'complex query'           => ['a=1&amp;b=2&amp;c=3', '&amp;a=1&amp;b=2&amp;c=3'],
        ];
    }

    #[DataProvider('provideExtraArgValues')]
    public function testConstructorExtraArgHandling(string $input, string $expected): void
    {
        $nav = new \XoopsPageNav(100, 10, 0, 'start', $input);
        $this->assertSame($expected, $nav->extra);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideStartNames(): array
    {
        return [
            'default start'   => ['start', '?start='],
            'offset'          => ['offset', '?offset='],
            'page'            => ['page', '?page='],
            'trimmed spaces'  => ['  begin  ', '?begin='],
            'single char'     => ['p', '?p='],
        ];
    }

    #[DataProvider('provideStartNames')]
    public function testConstructorUrlStartName(string $startName, string $expectedSuffix): void
    {
        $_SERVER['PHP_SELF'] = '';
        $nav = new \XoopsPageNav(100, 10, 0, $startName);
        $this->assertSame($expectedSuffix, $nav->url);
    }

    // =========================================================================
    // renderNav — data provider for early-return cases
    // =========================================================================

    /**
     * @return array<string, array{int, int, int}>
     */
    public static function provideRenderNavEmptyCases(): array
    {
        return [
            'total 0, perpage 10'    => [0, 10, 0],
            'total 1, perpage 10'    => [1, 10, 0],
            'total 5, perpage 10'    => [5, 10, 0],
            'total 10, perpage 10'   => [10, 10, 0],
            'total 9, perpage 10'    => [9, 10, 0],
            'total 10, perpage 100'  => [10, 100, 0],
        ];
    }

    #[DataProvider('provideRenderNavEmptyCases')]
    public function testRenderNavReturnsEmptyString(int $total, int $perpage, int $current): void
    {
        $nav = new \XoopsPageNav($total, $perpage, $current);
        $this->assertSame('', $nav->renderNav());
    }

    /**
     * @return array<string, array{int, int, int}>
     */
    public static function provideRenderSelectEmptyCases(): array
    {
        return [
            'total 0, perpage 10'    => [0, 10, 0],
            'total 1, perpage 10'    => [1, 10, 0],
            'total 5, perpage 10'    => [5, 10, 0],
            'total 9, perpage 10'    => [9, 10, 0],
        ];
    }

    #[DataProvider('provideRenderSelectEmptyCases')]
    public function testRenderSelectReturnsEmptyString(int $total, int $perpage, int $current): void
    {
        $nav = new \XoopsPageNav($total, $perpage, $current);
        $this->assertSame('', $nav->renderSelect());
    }

    /**
     * @return array<string, array{int, int, int}>
     */
    public static function provideRenderImageNavEmptyCases(): array
    {
        return [
            'total 0, perpage 10'    => [0, 10, 0],
            'total 1, perpage 10'    => [1, 10, 0],
            'total 5, perpage 10'    => [5, 10, 0],
            'total 9, perpage 10'    => [9, 10, 0],
        ];
    }

    #[DataProvider('provideRenderImageNavEmptyCases')]
    public function testRenderImageNavReturnsEmptyString(int $total, int $perpage, int $current): void
    {
        $nav = new \XoopsPageNav($total, $perpage, $current);
        $this->assertSame('', $nav->renderImageNav());
    }

    // =========================================================================
    // Internal navigation array building — tested via reflection
    // =========================================================================

    /**
     * Use reflection to invoke renderNav up to the point where it builds
     * the navigation array, by temporarily making displayPageNav accessible
     * and capturing its arguments.
     *
     * Since displayPageNav is private, we use a different approach:
     * We test the internal state indirectly by verifying the URL and extra
     * properties that would be used in navigation building.
     */
    public function testNavigationUrlIncludesExtraArg(): void
    {
        $nav = new \XoopsPageNav(100, 10, 0, 'start', 'cat=5');
        // The URL should be ready for appending page offsets
        $this->assertStringEndsWith('=', $nav->url);
        // The extra should be prepended with &amp;
        $this->assertSame('&amp;cat=5', $nav->extra);
    }

    public function testNavigationUrlFormatForPagination(): void
    {
        $_SERVER['PHP_SELF'] = '/test.php';
        $nav = new \XoopsPageNav(100, 10, 20, 'start', 'sort=asc');
        // URL should look like: /test.php?start=
        $this->assertSame('/test.php?start=', $nav->url);
        // A page link would be: /test.php?start=30&amp;sort=asc
        $expectedLink = $nav->url . '30' . $nav->extra;
        $this->assertSame('/test.php?start=30&amp;sort=asc', $expectedLink);
    }

    // =========================================================================
    // Edge cases: renderNav with perpage of zero
    // =========================================================================

    public function testRenderNavWithPerpageZeroAndPositiveTotal(): void
    {
        // total(10) <= perpage(0) is false, so passes first check.
        // Then: total(10) != 0 is true AND perpage(0) != 0 is false => skips nav building => returns ''
        $nav = new \XoopsPageNav(10, 0, 0);
        $this->assertSame('', $nav->renderNav());
    }

    // =========================================================================
    // Constructor with XSS-like PHP_SELF
    // =========================================================================

    public function testConstructorSanitizesPhpSelfWithXss(): void
    {
        $_SERVER['PHP_SELF'] = '/index.php"><script>alert(1)</script>';
        $nav = new \XoopsPageNav(100, 10, 0);
        // Request::getString strips HTML tags, then htmlspecialchars encodes remaining chars
        $this->assertStringNotContainsString('<script>', $nav->url);
        $this->assertStringNotContainsString('">', $nav->url);
        // The double-quote is encoded as &quot; and > as &gt;
        $this->assertStringContainsString('&quot;&gt;', $nav->url);
    }

    public function testConstructorSanitizesPhpSelfWithQuotes(): void
    {
        $_SERVER['PHP_SELF'] = '/index.php?a="test"';
        $nav = new \XoopsPageNav(100, 10, 0);
        // Double quotes should be encoded
        $this->assertStringNotContainsString('"test"', $nav->url);
        $this->assertStringContainsString('&quot;test&quot;', $nav->url);
    }

    // =========================================================================
    // Multiple instantiations with different parameters
    // =========================================================================

    public function testMultipleInstancesAreIndependent(): void
    {
        $_SERVER['PHP_SELF'] = '/a.php';
        $nav1 = new \XoopsPageNav(100, 10, 0, 'start', 'a=1');

        $_SERVER['PHP_SELF'] = '/b.php';
        $nav2 = new \XoopsPageNav(200, 20, 40, 'offset', 'b=2');

        // Verify they are independent
        $this->assertSame(100, $nav1->total);
        $this->assertSame(200, $nav2->total);
        $this->assertSame(10, $nav1->perpage);
        $this->assertSame(20, $nav2->perpage);
        $this->assertSame(0, $nav1->current);
        $this->assertSame(40, $nav2->current);
        $this->assertStringContainsString('/a.php', $nav1->url);
        $this->assertStringContainsString('/b.php', $nav2->url);
        $this->assertStringContainsString('start=', $nav1->url);
        $this->assertStringContainsString('offset=', $nav2->url);
        $this->assertSame('&amp;a=1', $nav1->extra);
        $this->assertSame('&amp;b=2', $nav2->extra);
    }

    // =========================================================================
    // renderSelect boundary: total == perpage (passes first check but
    // total_pages == 1 so returns '' from second check)
    // =========================================================================

    public function testRenderSelectTotalEqualsPerpage(): void
    {
        // total(10) < perpage(10) is false => passes first check
        // total_pages = ceil(10/10) = 1, which is NOT > 1 => returns ''
        $nav = new \XoopsPageNav(10, 10, 0);
        $this->assertSame('', $nav->renderSelect());
    }

    public function testRenderImageNavTotalEqualsPerpage(): void
    {
        // Same logic as renderSelect: passes first check, but total_pages == 1
        $nav = new \XoopsPageNav(10, 10, 0);
        $this->assertSame('', $nav->renderImageNav());
    }

    // =========================================================================
    // Verify all public properties are accessible
    // =========================================================================

    public function testAllPublicPropertiesExist(): void
    {
        $nav = new \XoopsPageNav(100, 10, 0, 'start', 'cat=5');

        $this->assertObjectHasProperty('total', $nav);
        $this->assertObjectHasProperty('perpage', $nav);
        $this->assertObjectHasProperty('current', $nav);
        $this->assertObjectHasProperty('url', $nav);
        $this->assertObjectHasProperty('extra', $nav);
    }

    // =========================================================================
    // Render methods return type verification
    // =========================================================================

    public function testRenderNavReturnTypeIsString(): void
    {
        $nav = new \XoopsPageNav(5, 10, 0);
        $result = $nav->renderNav();
        $this->assertIsString($result);
    }

    public function testRenderSelectReturnTypeIsString(): void
    {
        $nav = new \XoopsPageNav(5, 10, 0);
        $result = $nav->renderSelect();
        $this->assertIsString($result);
    }

    public function testRenderImageNavReturnTypeIsString(): void
    {
        $nav = new \XoopsPageNav(5, 10, 0);
        $result = $nav->renderImageNav();
        $this->assertIsString($result);
    }

    public function testRenderSelectWithShowButtonReturnTypeIsString(): void
    {
        $nav = new \XoopsPageNav(5, 10, 0);
        $result = $nav->renderSelect(true);
        $this->assertIsString($result);
    }

    public function testRenderNavWithCustomOffsetReturnTypeIsString(): void
    {
        $nav = new \XoopsPageNav(5, 10, 0);
        $result = $nav->renderNav(8);
        $this->assertIsString($result);
    }

    public function testRenderImageNavWithCustomOffsetReturnTypeIsString(): void
    {
        $nav = new \XoopsPageNav(5, 10, 0);
        $result = $nav->renderImageNav(8);
        $this->assertIsString($result);
    }
}
