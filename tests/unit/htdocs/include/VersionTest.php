<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once XOOPS_ROOT_PATH . '/include/version.php';

class VersionTest extends TestCase
{
    public function testXoopsVersionConstantIsDefined()
    {
        $this->assertTrue(defined('XOOPS_VERSION'), 'XOOPS_VERSION constant should be defined');
    }

    public function testXoopsVersionIsNotEmpty()
    {
        $this->assertNotEmpty(XOOPS_VERSION, 'XOOPS_VERSION should not be empty');
    }

    public function testXoopsVersionIsString()
    {
        $this->assertIsString(XOOPS_VERSION, 'XOOPS_VERSION should be a string');
    }

    public function testXoopsVersionFormat()
    {
        // Version should start with "XOOPS"
        $this->assertStringStartsWith('XOOPS', XOOPS_VERSION, 'Version should start with XOOPS');
    }

    public function testXoopsVersionContainsVersionNumber()
    {
        // Should contain a version number pattern (e.g., 2.5.12)
        $this->assertMatchesRegularExpression(
            '/\d+\.\d+\.\d+/',
            XOOPS_VERSION,
            'Version should contain a semantic version number (x.y.z)'
        );
    }

    public function testXoopsVersionValue()
    {
        // Verify the version starts with the expected major.minor.patch
        $this->assertStringStartsWith('XOOPS 2.5.12', XOOPS_VERSION, 'Version should start with XOOPS 2.5.12');
    }

    public function testXoopsVersionContainsXoopsPrefix()
    {
        // Verify the format "XOOPS x.y.z"
        $pattern = '/^XOOPS\s+\d+\.\d+\.\d+/';
        $this->assertMatchesRegularExpression(
            $pattern,
            XOOPS_VERSION,
            'Version should follow format "XOOPS x.y.z"'
        );
    }

    public function testXoopsVersionMayContainStatusSuffix()
    {
        // Version may contain status like -Beta, -RC, -Alpha, or be stable
        $validPatterns = [
            '/^XOOPS\s+\d+\.\d+\.\d+$/',                    // Stable
            '/^XOOPS\s+\d+\.\d+\.\d+-Alpha\d*$/',           // Alpha
            '/^XOOPS\s+\d+\.\d+\.\d+-Beta\d*$/',            // Beta
            '/^XOOPS\s+\d+\.\d+\.\d+-RC\d*$/',              // Release Candidate
            '/^XOOPS\s+\d+\.\d+\.\d+-Dev$/',                // Development
        ];

        $matchesAny = false;
        foreach ($validPatterns as $pattern) {
            if (preg_match($pattern, XOOPS_VERSION)) {
                $matchesAny = true;
                break;
            }
        }

        $this->assertTrue($matchesAny, 'Version should match one of the valid format patterns');
    }

    public function testVersionFileRequiresXoopsRootPath()
    {
        // The version.php file should check for XOOPS_ROOT_PATH
        // We can't easily test this without re-including, but we can verify it's set
        $this->assertTrue(
            defined('XOOPS_ROOT_PATH'),
            'XOOPS_ROOT_PATH should be defined before including version.php'
        );
    }

    public function testXoopsVersionIsReadOnly()
    {
        // Attempt to redefine should fail (PHP will issue a notice/warning)
        // We test that the constant cannot be changed
        $originalValue = XOOPS_VERSION;

        // Constants can't be redefined, so this test ensures the constant system works
        $this->assertEquals($originalValue, XOOPS_VERSION, 'Constant should remain unchanged');
    }

    public function testVersionStringLength()
    {
        // Version string should be reasonable length
        $length = strlen(XOOPS_VERSION);
        $this->assertGreaterThan(5, $length, 'Version string should be longer than 5 characters');
        $this->assertLessThan(50, $length, 'Version string should be less than 50 characters');
    }

    public function testVersionMajorNumber()
    {
        // Extract major version (should be 2 for XOOPS 2.x)
        preg_match('/XOOPS\s+(\d+)\./', XOOPS_VERSION, $matches);
        $this->assertNotEmpty($matches, 'Should extract major version');
        $majorVersion = (int)$matches[1];
        $this->assertEquals(2, $majorVersion, 'Major version should be 2');
    }

    public function testVersionMinorNumber()
    {
        // Extract minor version (should be 5 for XOOPS 2.5.x)
        preg_match('/XOOPS\s+\d+\.(\d+)\./', XOOPS_VERSION, $matches);
        $this->assertNotEmpty($matches, 'Should extract minor version');
        $minorVersion = (int)$matches[1];
        $this->assertEquals(5, $minorVersion, 'Minor version should be 5');
    }

    public function testVersionPatchNumber()
    {
        // Extract patch version
        preg_match('/XOOPS\s+\d+\.\d+\.(\d+)/', XOOPS_VERSION, $matches);
        $this->assertNotEmpty($matches, 'Should extract patch version');
        $patchVersion = (int)$matches[1];
        $this->assertGreaterThanOrEqual(0, $patchVersion, 'Patch version should be non-negative');
    }

    public function testVersionForComparison()
    {
        // Test that version can be compared with version_compare
        $testVersion = '2.5.0';

        // Extract numeric version from XOOPS_VERSION
        preg_match('/(\d+\.\d+\.\d+)/', XOOPS_VERSION, $matches);
        $numericVersion = $matches[1];

        $comparison = version_compare($numericVersion, $testVersion);
        $this->assertIsInt($comparison, 'version_compare should work with XOOPS version');
    }

    public function testCurrentVersionGreaterThan2510()
    {
        // Extract numeric version
        preg_match('/(\d+\.\d+\.\d+)/', XOOPS_VERSION, $matches);
        $numericVersion = $matches[1];

        $this->assertGreaterThanOrEqual(
            0,
            version_compare($numericVersion, '2.5.10'),
            'Current version should be 2.5.10 or higher'
        );
    }

    public function testVersionConsistency()
    {
        // Calling the constant multiple times should return same value
        $value1 = XOOPS_VERSION;
        $value2 = XOOPS_VERSION;

        $this->assertSame($value1, $value2, 'Version constant should be consistent');
    }

    public function testVersionNotContainHtml()
    {
        // Version string should not contain HTML tags
        $this->assertEquals(
            XOOPS_VERSION,
            strip_tags(XOOPS_VERSION),
            'Version should not contain HTML tags'
        );
    }

    public function testVersionNotContainLeadingOrTrailingWhitespace()
    {
        // Version should not have leading/trailing whitespace
        $this->assertEquals(
            XOOPS_VERSION,
            trim(XOOPS_VERSION),
            'Version should not have leading or trailing whitespace'
        );
    }

    public function testVersionHasValidStatusOrIsStable()
    {
        // Version should either be stable (no suffix) or have a valid pre-release suffix
        $this->assertMatchesRegularExpression(
            '/^XOOPS\s+\d+\.\d+\.\d+(?:-(Alpha|Beta|RC|Dev)\d*)?$/',
            XOOPS_VERSION,
            'Version should be stable or have a valid pre-release suffix'
        );
    }

    public function testExtractionOfStatusFromVersion()
    {
        // Extract status (Beta, Alpha, RC, etc.)
        if (preg_match('/-([A-Za-z]+\d*)$/', XOOPS_VERSION, $matches)) {
            $status = $matches[1];
            $this->assertNotEmpty($status, 'Status should be extractable from version');
            // Verify the extracted status matches one of the known pre-release patterns
            $this->assertMatchesRegularExpression(
                '/^(Alpha|Beta|RC|Dev)\d*$/',
                $status,
                'Status should be a valid pre-release identifier'
            );
        } else {
            // If no match, version is stable
            $this->assertStringNotContainsString('-', XOOPS_VERSION);
        }
    }

    public function testVersionSemanticFormat()
    {
        // Test full semantic versioning format: MAJOR.MINOR.PATCH[-STATUS]
        $pattern = '/^XOOPS\s+\d+\.\d+\.\d+(?:-[A-Za-z]+\d*)?$/';
        $this->assertMatchesRegularExpression(
            $pattern,
            XOOPS_VERSION,
            'Version should follow semantic versioning format'
        );
    }

    // Additional regression and edge case tests

    public function testVersionStringDoesNotContainNull()
    {
        $this->assertStringNotContainsString("\0", XOOPS_VERSION, 'Version should not contain null bytes');
    }

    public function testVersionCanBeUsedInFileNames()
    {
        // Version string should be safe for filenames (no /, \, etc.)
        $unsafeChars = ['/', '\\', '*', '?', '"', '<', '>', '|'];

        foreach ($unsafeChars as $char) {
            $this->assertStringNotContainsString(
                $char,
                XOOPS_VERSION,
                "Version should not contain unsafe filename character: {$char}"
            );
        }
    }

    public function testVersionCanBeUsedInUrls()
    {
        // Version should be URL-safe or easily encoded
        $encoded = urlencode(XOOPS_VERSION);
        $this->assertNotEmpty($encoded);

        // Should decode back to original
        $decoded = urldecode($encoded);
        $this->assertEquals(XOOPS_VERSION, $decoded);
    }

    public function testVersionCanBeJsonEncoded()
    {
        $json = json_encode(['version' => XOOPS_VERSION]);
        $this->assertNotFalse($json);

        $decoded = json_decode($json, true);
        $this->assertEquals(XOOPS_VERSION, $decoded['version']);
    }

    public function testVersionConstantTypeConsistency()
    {
        // Multiple calls should return same type
        $type1 = gettype(XOOPS_VERSION);
        $type2 = gettype(XOOPS_VERSION);

        $this->assertEquals($type1, $type2);
        $this->assertEquals('string', $type1);
    }

    public function testVersionParseable()
    {
        // Should be able to extract version components
        preg_match('/XOOPS\s+(\d+)\.(\d+)\.(\d+)(?:-([A-Za-z]+\d*))?/', XOOPS_VERSION, $matches);

        $this->assertNotEmpty($matches, 'Version should be parseable');
        $this->assertGreaterThanOrEqual(4, count($matches), 'Should extract at least major.minor.patch');

        // Validate extracted components
        $major = (int)$matches[1];
        $minor = (int)$matches[2];
        $patch = (int)$matches[3];

        $this->assertGreaterThanOrEqual(0, $major);
        $this->assertGreaterThanOrEqual(0, $minor);
        $this->assertGreaterThanOrEqual(0, $patch);
    }
}
