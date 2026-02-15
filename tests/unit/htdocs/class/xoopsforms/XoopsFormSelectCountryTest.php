<?php
// Define xoops_loadLanguage() in the GLOBAL namespace as a no-op stub for tests.
// The real function lives in include/functions.php which is not loaded in the test bootstrap.
// XoopsLists::getCountryList() calls xoops_loadLanguage('countries') internally from the
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
     * Tests for XoopsFormSelectCountry.
     *
     * Source: class/xoopsform/formselectcountry.php
     */
    class XoopsFormSelectCountryTest extends TestCase
    {
        protected function setUp(): void
        {
            xoops_load('XoopsFormElement');
            xoops_load('XoopsFormSelect');
            xoops_load('XoopsLists');

            // Pre-load the countries language file so the _COUNTRY_* constants are defined.
            $countriesFile = XOOPS_ROOT_PATH . '/language/english/countries.php';
            if (file_exists($countriesFile)) {
                require_once $countriesFile;
            }

            xoops_load('XoopsFormSelectCountry');
        }

        /**
         * Constructor must add a non-empty list of country options.
         */
        public function testConstructorAddsCountryOptions(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field');
            $options = $element->getOptions();

            $this->assertIsArray($options);
            $this->assertNotEmpty($options, 'Country list should not be empty');
        }

        /**
         * The country list must contain many entries (200+).
         */
        public function testCountryListHasManyEntries(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field');
            $options = $element->getOptions();

            $this->assertGreaterThan(200, count($options), 'Country list should have 200+ entries');
        }

        /**
         * The United States (US) must be present.
         */
        public function testUnitedStatesIsPresent(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field');
            $options = $element->getOptions();

            $this->assertArrayHasKey('US', $options);
        }

        /**
         * The United Kingdom (GB) must be present.
         */
        public function testUnitedKingdomIsPresent(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field');
            $options = $element->getOptions();

            $this->assertArrayHasKey('GB', $options);
        }

        /**
         * France (FR) must be present.
         */
        public function testFranceIsPresent(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field');
            $options = $element->getOptions();

            $this->assertArrayHasKey('FR', $options);
        }

        /**
         * Germany (DE) must be present.
         */
        public function testGermanyIsPresent(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field');
            $options = $element->getOptions();

            $this->assertArrayHasKey('DE', $options);
        }

        /**
         * Japan (JP) must be present.
         */
        public function testJapanIsPresent(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field');
            $options = $element->getOptions();

            $this->assertArrayHasKey('JP', $options);
        }

        /**
         * Canada (CA) must be present.
         */
        public function testCanadaIsPresent(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field');
            $options = $element->getOptions();

            $this->assertArrayHasKey('CA', $options);
        }

        /**
         * The empty key (dash separator) must be present.
         */
        public function testEmptyKeyIsPresent(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field');
            $options = $element->getOptions();

            $this->assertArrayHasKey('', $options);
            $this->assertSame('-', $options['']);
        }

        /**
         * Data provider: known country codes that should be in the list.
         *
         * @return array<string, array{string}>
         */
        public static function knownCountryProvider(): array
        {
            return [
                'United States'  => ['US'],
                'United Kingdom' => ['GB'],
                'France'         => ['FR'],
                'Germany'        => ['DE'],
                'Japan'          => ['JP'],
                'Canada'         => ['CA'],
                'Australia'      => ['AU'],
                'Brazil'         => ['BR'],
                'China'          => ['CN'],
                'India'          => ['IN'],
            ];
        }

        /**
         * Known countries must all be present.
         */
        #[DataProvider('knownCountryProvider')]
        public function testKnownCountriesArePresent(string $code): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field');
            $options = $element->getOptions();

            $this->assertArrayHasKey($code, $options);
        }

        /**
         * Caption must be correctly set.
         */
        public function testCaptionIsSet(): void
        {
            $element = new \XoopsFormSelectCountry('My Country', 'country_field');

            $this->assertSame('My Country', $element->getCaption());
        }

        /**
         * Name must be correctly set.
         */
        public function testNameIsSet(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'my_country');

            $this->assertSame('my_country', $element->getName());
        }

        /**
         * Default size must be 1 (dropdown).
         */
        public function testDefaultSizeIsOne(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field');

            $this->assertSame(1, $element->getSize());
        }

        /**
         * Custom size must be respected.
         */
        public function testCustomSize(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field', null, 5);

            $this->assertSame(5, $element->getSize());
        }

        /**
         * Pre-selected value must be respected.
         */
        public function testPreSelectedValue(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field', 'US');
            $value = $element->getValue();

            $this->assertContains('US', $value);
        }

        /**
         * Null value means no selection.
         */
        public function testNullValueMeansNoSelection(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field', null);
            $value = $element->getValue();

            $this->assertIsArray($value);
            $this->assertEmpty($value);
        }

        /**
         * The element must be an instance of XoopsFormSelect.
         */
        public function testInheritsXoopsFormSelect(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field');

            $this->assertInstanceOf(\XoopsFormSelect::class, $element);
        }

        /**
         * The element must be an instance of XoopsFormElement.
         */
        public function testInheritsXoopsFormElement(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field');

            $this->assertInstanceOf(\XoopsFormElement::class, $element);
        }

        /**
         * All option values (country names) must be strings.
         */
        public function testAllOptionValuesAreStrings(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field');
            $options = $element->getOptions();

            foreach ($options as $code => $name) {
                $this->assertIsString(
                    $name,
                    sprintf('Country name for code "%s" must be a string', $code)
                );
            }
        }

        /**
         * Render must return a non-empty string.
         */
        public function testRenderReturnsString(): void
        {
            xoops_load('XoopsFormRenderer');
            $element = new \XoopsFormSelectCountry('Country', 'country_field');
            $rendered = $element->render();

            $this->assertIsString($rendered);
            $this->assertNotEmpty($rendered);
        }

        /**
         * Multiple pre-selected values (as array) should work.
         */
        public function testMultiplePreSelectedValues(): void
        {
            $element = new \XoopsFormSelectCountry('Country', 'country_field', ['US', 'GB']);
            $value = $element->getValue();

            $this->assertContains('US', $value);
            $this->assertContains('GB', $value);
        }
    }
}
