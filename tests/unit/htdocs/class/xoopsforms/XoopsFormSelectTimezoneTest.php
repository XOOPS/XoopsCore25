<?php
// Define xoops_loadLanguage() in the GLOBAL namespace as a no-op stub for tests.
// The real function lives in include/functions.php which is not loaded in the test bootstrap.
// XoopsLists::getTimeZoneList() calls xoops_loadLanguage('timezone') internally from the
// global namespace, so this stub must also be in the global namespace.
namespace {
    if (!function_exists('xoops_loadLanguage')) {
        function xoops_loadLanguage($name, $domain = '', $language = null)
        {
            return false;
        }
    }
}

namespace xoopsforms {

    use PHPUnit\Framework\Attributes\DataProvider;
    use PHPUnit\Framework\TestCase;

    /**
     * Tests for XoopsFormSelectTimezone.
     *
     * Source: class/xoopsform/formselecttimezone.php
     */
    class XoopsFormSelectTimezoneTest extends TestCase
    {
        protected function setUp(): void
        {
            xoops_load('XoopsFormElement');
            xoops_load('XoopsFormSelect');
            xoops_load('XoopsLists');

            // Pre-load the timezone language file so the _TZ_* constants are defined.
            $timezoneFile = XOOPS_ROOT_PATH . '/language/english/timezone.php';
            if (file_exists($timezoneFile)) {
                require_once $timezoneFile;
            }

            xoops_load('XoopsFormSelectTimezone');
        }

        /**
         * Constructor must add a non-empty list of timezone options.
         */
        public function testConstructorAddsTimezoneOptions(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field');
            $options = $element->getOptions();

            $this->assertIsArray($options);
            $this->assertNotEmpty($options, 'Timezone list should not be empty');
        }

        /**
         * The timezone list must contain exactly 30 entries (from -12 to +12 with half-hours).
         */
        public function testTimezoneListCountIs30(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field');
            $options = $element->getOptions();

            $this->assertCount(30, $options, 'Timezone list should have exactly 30 entries');
        }

        /**
         * GMT+0 timezone must be present.
         */
        public function testGmtZeroIsPresent(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field');
            $options = $element->getOptions();

            $this->assertArrayHasKey('0', $options);
        }

        /**
         * GMT-12 timezone must be present.
         */
        public function testGmtMinus12IsPresent(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field');
            $options = $element->getOptions();

            $this->assertArrayHasKey('-12', $options);
        }

        /**
         * GMT+12 timezone must be present.
         */
        public function testGmtPlus12IsPresent(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field');
            $options = $element->getOptions();

            $this->assertArrayHasKey('12', $options);
        }

        /**
         * Half-hour timezones must be present.
         */
        public function testHalfHourTimezonesPresent(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field');
            $options = $element->getOptions();

            $this->assertArrayHasKey('-3.5', $options, 'GMT-3:30 should be present');
            $this->assertArrayHasKey('3.5', $options, 'GMT+3:30 should be present');
            $this->assertArrayHasKey('4.5', $options, 'GMT+4:30 should be present');
            $this->assertArrayHasKey('5.5', $options, 'GMT+5:30 should be present');
            $this->assertArrayHasKey('9.5', $options, 'GMT+9:30 should be present');
        }

        /**
         * Data provider: specific timezone keys that should exist.
         *
         * @return array<string, array{string}>
         */
        public static function timezoneKeyProvider(): array
        {
            return [
                'GMT-12'   => ['-12'],
                'GMT-11'   => ['-11'],
                'GMT-10'   => ['-10'],
                'GMT-9'    => ['-9'],
                'GMT-8'    => ['-8'],
                'GMT-7'    => ['-7'],
                'GMT-6'    => ['-6'],
                'GMT-5'    => ['-5'],
                'GMT-4'    => ['-4'],
                'GMT-3.5'  => ['-3.5'],
                'GMT-3'    => ['-3'],
                'GMT-2'    => ['-2'],
                'GMT-1'    => ['-1'],
                'GMT+0'    => ['0'],
                'GMT+1'    => ['1'],
                'GMT+2'    => ['2'],
                'GMT+3'    => ['3'],
                'GMT+3.5'  => ['3.5'],
                'GMT+4'    => ['4'],
                'GMT+4.5'  => ['4.5'],
                'GMT+5'    => ['5'],
                'GMT+5.5'  => ['5.5'],
                'GMT+6'    => ['6'],
                'GMT+7'    => ['7'],
                'GMT+8'    => ['8'],
                'GMT+9'    => ['9'],
                'GMT+9.5'  => ['9.5'],
                'GMT+10'   => ['10'],
                'GMT+11'   => ['11'],
                'GMT+12'   => ['12'],
            ];
        }

        /**
         * Every timezone key from -12 to +12 (including halves) must be present.
         */
        #[DataProvider('timezoneKeyProvider')]
        public function testTimezoneKeyExists(string $key): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field');
            $options = $element->getOptions();

            $this->assertArrayHasKey($key, $options, sprintf('Timezone key "%s" must be present', $key));
        }

        /**
         * Caption must be correctly set.
         */
        public function testCaptionIsSet(): void
        {
            $element = new \XoopsFormSelectTimezone('My Timezone', 'tz_field');

            $this->assertSame('My Timezone', $element->getCaption());
        }

        /**
         * Name must be correctly set.
         */
        public function testNameIsSet(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'my_tz');

            $this->assertSame('my_tz', $element->getName());
        }

        /**
         * Default size must be 1 (dropdown).
         */
        public function testDefaultSizeIsOne(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field');

            $this->assertSame(1, $element->getSize());
        }

        /**
         * Custom size must be respected.
         */
        public function testCustomSize(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field', null, 5);

            $this->assertSame(5, $element->getSize());
        }

        /**
         * Pre-selected value must be respected.
         */
        public function testPreSelectedValue(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field', '0');
            $value = $element->getValue();

            $this->assertContains('0', $value);
        }

        /**
         * Pre-selecting a negative timezone must work.
         */
        public function testPreSelectNegativeTimezone(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field', '-5');
            $value = $element->getValue();

            $this->assertContains('-5', $value);
        }

        /**
         * Null value means no selection.
         */
        public function testNullValueMeansNoSelection(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field', null);
            $value = $element->getValue();

            $this->assertIsArray($value);
            $this->assertEmpty($value);
        }

        /**
         * The element must be an instance of XoopsFormSelect.
         */
        public function testInheritsXoopsFormSelect(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field');

            $this->assertInstanceOf(\XoopsFormSelect::class, $element);
        }

        /**
         * The element must be an instance of XoopsFormElement.
         */
        public function testInheritsXoopsFormElement(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field');

            $this->assertInstanceOf(\XoopsFormElement::class, $element);
        }

        /**
         * All option values (timezone names) must be non-empty strings.
         */
        public function testAllOptionValuesAreNonEmptyStrings(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field');
            $options = $element->getOptions();

            foreach ($options as $key => $name) {
                $this->assertIsString(
                    $name,
                    sprintf('Timezone name for key "%s" must be a string', $key)
                );
                $this->assertNotEmpty(
                    $name,
                    sprintf('Timezone name for key "%s" must not be empty', $key)
                );
            }
        }

        /**
         * Render must return a non-empty string.
         */
        public function testRenderReturnsString(): void
        {
            xoops_load('XoopsFormRenderer');
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field');
            $rendered = $element->render();

            $this->assertIsString($rendered);
            $this->assertNotEmpty($rendered);
        }

        /**
         * GMT+0 timezone label should contain "GMT".
         */
        public function testGmtZeroLabelContainsGmt(): void
        {
            $element = new \XoopsFormSelectTimezone('Timezone', 'tz_field');
            $options = $element->getOptions();

            $this->assertNotFalse(
                strpos($options['0'], 'GMT'),
                'GMT+0 label should contain "GMT"'
            );
        }
    }
}
