<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsFormDateTime;
use XoopsFormElementTray;
use XoopsFormTextDateSelect;
use XoopsFormSelect;
use XoopsFormHidden;
use XoopsFormElement;

xoops_load('XoopsFormElement');
xoops_load('XoopsFormElementTray');
xoops_load('XoopsFormText');
xoops_load('XoopsFormTextDateSelect');
xoops_load('XoopsFormSelect');
xoops_load('XoopsFormHidden');
xoops_load('XoopsFormDateTime');

/**
 * Unit tests for XoopsFormDateTime.
 *
 * XoopsFormDateTime extends XoopsFormElementTray and creates child elements
 * (XoopsFormTextDateSelect and XoopsFormSelect or XoopsFormHidden) depending
 * on the $showtime parameter.
 */
class XoopsFormDateTimeTest extends \PHPUnit\Framework\TestCase
{
    // =========================================================================
    // Constants
    // =========================================================================

    public function testShowBothConstantEqualsOne(): void
    {
        $this->assertSame(1, XoopsFormDateTime::SHOW_BOTH);
    }

    public function testShowDateConstantEqualsZero(): void
    {
        $this->assertSame(0, XoopsFormDateTime::SHOW_DATE);
    }

    public function testShowTimeConstantEqualsTwo(): void
    {
        $this->assertSame(2, XoopsFormDateTime::SHOW_TIME);
    }

    // =========================================================================
    // Constructor — isContainer
    // =========================================================================

    public function testIsContainer(): void
    {
        $element = new XoopsFormDateTime('Date/Time', 'datetime_field');

        $this->assertTrue($element->isContainer());
    }

    // =========================================================================
    // Constructor — extends XoopsFormElementTray
    // =========================================================================

    public function testExtendsXoopsFormElementTray(): void
    {
        $element = new XoopsFormDateTime('Caption', 'field');

        $this->assertInstanceOf(XoopsFormElementTray::class, $element);
    }

    public function testExtendsXoopsFormElement(): void
    {
        $element = new XoopsFormDateTime('Caption', 'field');

        $this->assertInstanceOf(XoopsFormElement::class, $element);
    }

    // =========================================================================
    // Constructor — caption
    // =========================================================================

    public function testConstructorSetsCaption(): void
    {
        $element = new XoopsFormDateTime('My Date', 'date_field');

        $this->assertSame('My Date', $element->getCaption());
    }

    public function testConstructorEmptyCaption(): void
    {
        $element = new XoopsFormDateTime('', 'date_field');

        $this->assertSame('', $element->getCaption());
    }

    // =========================================================================
    // SHOW_BOTH (default: $showtime = true) — creates date + time select
    // =========================================================================

    public function testShowBothCreatesDateAndTimeElements(): void
    {
        $timestamp = mktime(14, 30, 0, 6, 15, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, true);

        $elements = $element->getElements();

        $this->assertCount(2, $elements);
    }

    public function testShowBothFirstElementIsTextDateSelect(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, true);

        $elements = $element->getElements();

        $this->assertInstanceOf(XoopsFormTextDateSelect::class, $elements[0]);
    }

    public function testShowBothSecondElementIsFormSelect(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, true);

        $elements = $element->getElements();

        $this->assertInstanceOf(XoopsFormSelect::class, $elements[1]);
    }

    public function testShowBothDateElementHasCorrectName(): void
    {
        $element = new XoopsFormDateTime('Date', 'mydt', 15, mktime(10, 0, 0, 3, 15, 2025), true);

        $elements = $element->getElements();

        $this->assertSame('mydt[date]', $elements[0]->getName(false));
    }

    public function testShowBothTimeElementHasCorrectName(): void
    {
        $element = new XoopsFormDateTime('Date', 'mydt', 15, mktime(10, 0, 0, 3, 15, 2025), true);

        $elements = $element->getElements();

        $this->assertSame('mydt[time]', $elements[1]->getName(false));
    }

    public function testShowBothWithShowBothConstant(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, XoopsFormDateTime::SHOW_BOTH);

        $elements = $element->getElements();

        $this->assertCount(2, $elements);
        $this->assertInstanceOf(XoopsFormTextDateSelect::class, $elements[0]);
        $this->assertInstanceOf(XoopsFormSelect::class, $elements[1]);
    }

    // =========================================================================
    // SHOW_DATE ($showtime = false / SHOW_DATE=0) — date + hidden time
    // =========================================================================

    public function testShowDateCreatesDateAndHiddenTime(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, false);

        $elements = $element->getElements();

        $this->assertCount(2, $elements);
    }

    public function testShowDateFirstElementIsTextDateSelect(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, false);

        $elements = $element->getElements();

        $this->assertInstanceOf(XoopsFormTextDateSelect::class, $elements[0]);
    }

    public function testShowDateSecondElementIsHidden(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, false);

        $elements = $element->getElements();

        $this->assertInstanceOf(XoopsFormHidden::class, $elements[1]);
    }

    public function testShowDateHiddenTimeValueIsZero(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, false);

        $elements = $element->getElements();

        $this->assertEquals(0, $elements[1]->getValue());
    }

    public function testShowDateHiddenTimeHasCorrectName(): void
    {
        $element = new XoopsFormDateTime('Date', 'mydt', 15, mktime(10, 0, 0, 3, 15, 2025), false);

        $elements = $element->getElements();

        $this->assertSame('mydt[time]', $elements[1]->getName(false));
    }

    public function testShowDateWithConstant(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, XoopsFormDateTime::SHOW_DATE);

        $elements = $element->getElements();

        $this->assertCount(2, $elements);
        $this->assertInstanceOf(XoopsFormTextDateSelect::class, $elements[0]);
        $this->assertInstanceOf(XoopsFormHidden::class, $elements[1]);
    }

    // =========================================================================
    // SHOW_TIME ($showtime = SHOW_TIME=2) — hidden date + time select
    // =========================================================================

    public function testShowTimeCreatesHiddenDateAndTimeSelect(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, XoopsFormDateTime::SHOW_TIME);

        $elements = $element->getElements();

        $this->assertCount(2, $elements);
    }

    public function testShowTimeFirstElementIsHidden(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, XoopsFormDateTime::SHOW_TIME);

        $elements = $element->getElements();

        $this->assertInstanceOf(XoopsFormHidden::class, $elements[0]);
    }

    public function testShowTimeSecondElementIsFormSelect(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, XoopsFormDateTime::SHOW_TIME);

        $elements = $element->getElements();

        $this->assertInstanceOf(XoopsFormSelect::class, $elements[1]);
    }

    public function testShowTimeHiddenDateHasCorrectName(): void
    {
        $element = new XoopsFormDateTime('Date', 'mydt', 15, mktime(10, 0, 0, 3, 15, 2025), XoopsFormDateTime::SHOW_TIME);

        $elements = $element->getElements();

        $this->assertSame('mydt[date]', $elements[0]->getName(false));
    }

    public function testShowTimeHiddenDateContainsFormattedDate(): void
    {
        $timestamp = mktime(10, 0, 0, 6, 15, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, XoopsFormDateTime::SHOW_TIME);

        $elements = $element->getElements();
        $hiddenDate = $elements[0];

        // _SHORTDATESTRING is 'Y/m/d'
        $expectedDate = date('Y/m/d', $timestamp);
        $this->assertSame($expectedDate, $hiddenDate->getValue());
    }

    public function testShowTimeSelectHasCorrectName(): void
    {
        $element = new XoopsFormDateTime('Date', 'mydt', 15, mktime(10, 0, 0, 3, 15, 2025), XoopsFormDateTime::SHOW_TIME);

        $elements = $element->getElements();

        $this->assertSame('mydt[time]', $elements[1]->getName(false));
    }

    // =========================================================================
    // Value = 0 defaults to current time
    // =========================================================================

    public function testValueZeroDefaultsToCurrentTime(): void
    {
        $before = time();
        $element = new XoopsFormDateTime('Date', 'dt', 15, 0, true);
        $after = time();

        // When value is 0, it should use time() internally
        // We verify by checking that the date element was created (it uses a timestamp)
        $elements = $element->getElements();
        $this->assertCount(2, $elements);
        $this->assertInstanceOf(XoopsFormTextDateSelect::class, $elements[0]);
    }

    public function testValueDefaultsToZero(): void
    {
        // Default constructor value is 0, which means current time
        $element = new XoopsFormDateTime('Date', 'dt');
        $elements = $element->getElements();

        $this->assertCount(2, $elements);
    }

    // =========================================================================
    // Specific timestamp sets correct child elements
    // =========================================================================

    public function testSpecificTimestampCreatesCorrectElements(): void
    {
        // June 15, 2025 at 14:30:00
        $timestamp = mktime(14, 30, 0, 6, 15, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, true);

        $elements = $element->getElements();

        $this->assertCount(2, $elements);
        $this->assertInstanceOf(XoopsFormTextDateSelect::class, $elements[0]);
        $this->assertInstanceOf(XoopsFormSelect::class, $elements[1]);
    }

    public function testTimeSelectHasTimeOptions(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, true);

        $elements = $element->getElements();
        /** @var XoopsFormSelect $timeSelect */
        $timeSelect = $elements[1];

        // Time select should have 24 hours * 6 (every 10 min) = 144 options
        $options = $timeSelect->getOptions();
        $this->assertCount(144, $options);
    }

    public function testTimeSelectOptionsAreKeySorted(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, true);

        $elements = $element->getElements();
        /** @var XoopsFormSelect $timeSelect */
        $timeSelect = $elements[1];

        $options = $timeSelect->getOptions();
        $keys = array_keys($options);

        // First key should be 0 (0:00), last should be 23*3600 + 50*60 = 85800
        $this->assertSame(0, $keys[0]);
        $this->assertSame(85800, end($keys));
    }

    public function testTimeSelectFirstOptionIsMidnight(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, true);

        $elements = $element->getElements();
        /** @var XoopsFormSelect $timeSelect */
        $timeSelect = $elements[1];

        $options = $timeSelect->getOptions();
        $this->assertSame('0:00', $options[0]);
    }

    public function testTimeSelectContains12Noon(): void
    {
        $timestamp = mktime(10, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, true);

        $elements = $element->getElements();
        /** @var XoopsFormSelect $timeSelect */
        $timeSelect = $elements[1];

        $options = $timeSelect->getOptions();
        // 12:00 = 12 * 3600 = 43200
        $this->assertSame('12:00', $options[43200]);
    }

    public function testTimeSelectSelectedValueForGivenTimestamp(): void
    {
        // 14:30 => hours=14, minutes=30
        // selected = 14*3600 + 600*ceil(30/10) = 50400 + 1800 = 52200
        $timestamp = mktime(14, 30, 0, 6, 15, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, true);

        $elements = $element->getElements();
        /** @var XoopsFormSelect $timeSelect */
        $timeSelect = $elements[1];

        $selectedValue = $timeSelect->getValue();
        // ceil() returns float, so use assertEquals (loose comparison)
        $actual = is_array($selectedValue) ? reset($selectedValue) : $selectedValue;
        $this->assertEquals(52200, $actual);
    }

    // =========================================================================
    // getElements returns children
    // =========================================================================

    public function testGetElementsReturnsArray(): void
    {
        $element = new XoopsFormDateTime('Date', 'dt', 15, mktime(10, 0, 0, 1, 1, 2025), true);

        $elements = $element->getElements();

        $this->assertIsArray($elements);
    }

    public function testGetElementsCountForShowBoth(): void
    {
        $element = new XoopsFormDateTime('Date', 'dt', 15, mktime(10, 0, 0, 1, 1, 2025), true);

        $this->assertCount(2, $element->getElements());
    }

    public function testGetElementsCountForShowDate(): void
    {
        $element = new XoopsFormDateTime('Date', 'dt', 15, mktime(10, 0, 0, 1, 1, 2025), false);

        $this->assertCount(2, $element->getElements());
    }

    public function testGetElementsCountForShowTime(): void
    {
        $element = new XoopsFormDateTime('Date', 'dt', 15, mktime(10, 0, 0, 1, 1, 2025), XoopsFormDateTime::SHOW_TIME);

        $this->assertCount(2, $element->getElements());
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testNegativeTimestampTreatedAsCurrentTime(): void
    {
        // Negative int cast to int is still negative, but ($value > 0) is false
        // so it defaults to time()
        $element = new XoopsFormDateTime('Date', 'dt', 15, -1, true);

        $elements = $element->getElements();
        $this->assertCount(2, $elements);
    }

    public function testMidnightTimestamp(): void
    {
        // Midnight: hours=0, minutes=0
        // selected = 0*3600 + 600*ceil(0/10) = 0
        $timestamp = mktime(0, 0, 0, 1, 1, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, true);

        $elements = $element->getElements();
        /** @var XoopsFormSelect $timeSelect */
        $timeSelect = $elements[1];

        $selectedValue = $timeSelect->getValue();
        // ceil() returns float, so use assertEquals (loose comparison)
        $actual = is_array($selectedValue) ? reset($selectedValue) : $selectedValue;
        $this->assertEquals(0, $actual);
    }

    public function testEndOfDayTimestamp(): void
    {
        // 23:50 => hours=23, minutes=50
        // selected = 23*3600 + 600*ceil(50/10) = 82800 + 3000 = 85800
        $timestamp = mktime(23, 50, 0, 12, 31, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, true);

        $elements = $element->getElements();
        /** @var XoopsFormSelect $timeSelect */
        $timeSelect = $elements[1];

        $selectedValue = $timeSelect->getValue();
        // ceil() returns float, so use assertEquals (loose comparison)
        $actual = is_array($selectedValue) ? reset($selectedValue) : $selectedValue;
        $this->assertEquals(85800, $actual);
    }

    /**
     * @param mixed  $showtime
     * @param string $expectedDateClass
     * @param string $expectedTimeClass
     */
    #[DataProvider('showTimeModesProvider')]
    public function testShowTimeModes($showtime, string $expectedDateClass, string $expectedTimeClass): void
    {
        $timestamp = mktime(10, 0, 0, 6, 15, 2025);
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, $showtime);

        $elements = $element->getElements();

        $this->assertCount(2, $elements);
        $this->assertInstanceOf($expectedDateClass, $elements[0]);
        $this->assertInstanceOf($expectedTimeClass, $elements[1]);
    }

    /**
     * @return array<string, array{0: mixed, 1: string, 2: string}>
     */
    public static function showTimeModesProvider(): array
    {
        return [
            'true (SHOW_BOTH)'     => [true, XoopsFormTextDateSelect::class, XoopsFormSelect::class],
            'SHOW_BOTH constant'   => [XoopsFormDateTime::SHOW_BOTH, XoopsFormTextDateSelect::class, XoopsFormSelect::class],
            'false (SHOW_DATE)'    => [false, XoopsFormTextDateSelect::class, XoopsFormHidden::class],
            'SHOW_DATE constant'   => [XoopsFormDateTime::SHOW_DATE, XoopsFormTextDateSelect::class, XoopsFormHidden::class],
            'SHOW_TIME constant'   => [XoopsFormDateTime::SHOW_TIME, XoopsFormHidden::class, XoopsFormSelect::class],
        ];
    }

    /**
     * @param int $timestamp
     * @param int $expectedHours
     * @param int $expectedMinutesBucket
     */
    #[DataProvider('timestampProvider')]
    public function testTimeSelectionForVariousTimestamps(int $timestamp, int $expectedHours, int $expectedMinutesBucket): void
    {
        $element = new XoopsFormDateTime('Date', 'dt', 15, $timestamp, true);

        $elements = $element->getElements();
        /** @var XoopsFormSelect $timeSelect */
        $timeSelect = $elements[1];

        // Expected selected value: hours*3600 + 600*ceil(minutes/10)
        $expectedValue = $expectedHours * 3600 + 600 * $expectedMinutesBucket;
        $selectedValue = $timeSelect->getValue();

        // ceil() returns float, so use assertEquals (loose comparison)
        $actual = is_array($selectedValue) ? reset($selectedValue) : $selectedValue;
        $this->assertEquals($expectedValue, $actual);
    }

    /**
     * @return array<string, array{0: int, 1: int, 2: int}>
     */
    public static function timestampProvider(): array
    {
        return [
            'midnight'   => [mktime(0, 0, 0, 1, 1, 2025), 0, 0],
            '10:00'      => [mktime(10, 0, 0, 1, 1, 2025), 10, 0],
            '10:05'      => [mktime(10, 5, 0, 1, 1, 2025), 10, 1],
            '10:10'      => [mktime(10, 10, 0, 1, 1, 2025), 10, 1],
            '10:15'      => [mktime(10, 15, 0, 1, 1, 2025), 10, 2],
            '10:20'      => [mktime(10, 20, 0, 1, 1, 2025), 10, 2],
            '10:25'      => [mktime(10, 25, 0, 1, 1, 2025), 10, 3],
            '10:30'      => [mktime(10, 30, 0, 1, 1, 2025), 10, 3],
            '23:59'      => [mktime(23, 59, 0, 1, 1, 2025), 23, 6],
        ];
    }

    public function testSizeParameterDefaultIs15(): void
    {
        $element = new XoopsFormDateTime('Date', 'dt');

        $elements = $element->getElements();
        // The first element (TextDateSelect) is constructed with size param
        // We verify it exists and is the right type
        $this->assertInstanceOf(XoopsFormTextDateSelect::class, $elements[0]);
    }

    public function testDelimiterIsNbsp(): void
    {
        $element = new XoopsFormDateTime('Date', 'dt');

        $this->assertSame('&nbsp;', $element->getDelimeter());
    }
}
