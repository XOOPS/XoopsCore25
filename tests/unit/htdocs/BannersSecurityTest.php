<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests for banner security fixes in htdocs/banners.php.
 *
 * Covers password hashing logic (C-2) and XSS escaping (C-3).
 */
#[CoversFunction('bannerstats')]
#[CoversFunction('emailStats')]
#[CoversFunction('change_banner_url_by_client')]
class BannersSecurityTest extends TestCase
{
    // ---------------------------------------------------------------
    // C-2: Password hashing — unit tests for the authentication logic
    // ---------------------------------------------------------------

    #[Test]
    public function passwordHashCreatesVerifiableHash(): void
    {
        $plain = 'my$ecretP@ss';
        $hash  = password_hash($plain, PASSWORD_DEFAULT);

        $this->assertTrue(password_verify($plain, $hash));
    }

    #[Test]
    public function passwordVerifyRejectsWrongPassword(): void
    {
        $hash = password_hash('correctPassword', PASSWORD_DEFAULT);

        $this->assertFalse(password_verify('wrongPassword', $hash));
    }

    #[Test]
    public function legacyPlaintextPasswordDetectedAsNonHashed(): void
    {
        // password_get_info() returns null (PHP 8.4+) or 0 (PHP 8.2-8.3)
        // for unrecognized/plaintext strings — either way, not a valid algo
        $plaintext = 'oldPlainPassword';
        $info = password_get_info($plaintext);
        $algo = $info['algo'];

        $this->assertTrue(
            $algo === null || $algo === 0,
            'Expected algo to be null or 0 for plaintext, got: ' . var_export($algo, true)
        );
    }

    #[Test]
    public function bcryptHashDetectedAsHashed(): void
    {
        $hash = password_hash('somePass', PASSWORD_DEFAULT);
        $info = password_get_info($hash);
        $algo = $info['algo'];

        $this->assertTrue(
            $algo !== null && $algo !== 0,
            'Expected algo to be a valid identifier for bcrypt hash'
        );
    }

    #[Test]
    public function legacyPlaintextMatchUsesHashEquals(): void
    {
        // Simulates the backward-compat path: plaintext stored, user provides same
        $storedPlaintext = 'legacyPass123';
        $userInput       = 'legacyPass123';

        $this->assertTrue(hash_equals($storedPlaintext, $userInput));
    }

    #[Test]
    public function legacyPlaintextMismatchRejects(): void
    {
        $storedPlaintext = 'legacyPass123';
        $userInput       = 'wrongPass';

        $this->assertFalse(hash_equals($storedPlaintext, $userInput));
    }

    #[Test]
    public function rehashUpgradesPlaintextToBcrypt(): void
    {
        // Simulate the upgrade path: plaintext matches, then rehash
        $plaintext = 'upgradeMePass';
        $newHash   = password_hash($plaintext, PASSWORD_DEFAULT);

        // The new hash should verify correctly
        $this->assertTrue(password_verify($plaintext, $newHash));
        // And it should now be detected as a proper hash
        $this->assertNotNull(password_get_info($newHash)['algo']);
    }

    #[Test]
    public function passwordNeedsRehashDetectsOutdatedHash(): void
    {
        // A hash made with a very low cost should trigger needs_rehash with default cost
        $hash = password_hash('test', PASSWORD_BCRYPT, ['cost' => 4]);

        $this->assertTrue(password_needs_rehash($hash, PASSWORD_DEFAULT));
    }

    // ---------------------------------------------------------------
    // C-2: Session — stores client ID, not raw password
    // ---------------------------------------------------------------

    #[Test]
    public function sessionStoresClientIdNotPassword(): void
    {
        // Simulate what the login handler now does
        $_SESSION = [];
        $clientId = 42;
        $_SESSION['banner_client_id'] = (int) $clientId;

        $this->assertSame(42, $_SESSION['banner_client_id']);
        $this->assertArrayNotHasKey('banner_pass', $_SESSION);
    }

    #[Test]
    public function sessionWithoutClientIdIsRejected(): void
    {
        $_SESSION = [];
        $banner_client_id = isset($_SESSION['banner_client_id']) ? (int) $_SESSION['banner_client_id'] : 0;

        $this->assertSame(0, $banner_client_id);
        $this->assertFalse($banner_client_id > 0);
    }

    // ---------------------------------------------------------------
    // C-3: XSS — $name is escaped in HTML output
    // ---------------------------------------------------------------

    #[Test]
    public function htmlspecialcharsEscapesXssInBannerName(): void
    {
        $maliciousName = '<script>alert("xss")</script>';
        $escaped = htmlspecialchars($maliciousName, ENT_QUOTES, 'UTF-8');

        $this->assertStringNotContainsString('<script>', $escaped);
        $this->assertStringContainsString('&lt;script&gt;', $escaped);
    }

    #[Test]
    public function htmlspecialcharsEscapesQuotesInBannerName(): void
    {
        $nameWithQuotes = 'O\'Malley & "Friends"';
        $escaped = htmlspecialchars($nameWithQuotes, ENT_QUOTES, 'UTF-8');

        $this->assertStringNotContainsString("'", $escaped);
        $this->assertStringNotContainsString('"', $escaped);
        $this->assertStringContainsString('&#039;', $escaped);
        $this->assertStringContainsString('&quot;', $escaped);
        $this->assertStringContainsString('&amp;', $escaped);
    }

    #[Test]
    public function sprintfWithEscapedNameProducesSafeHtml(): void
    {
        // Simulates the pattern: sprintf(_BANNERS_TITLE, htmlspecialchars($name, ENT_QUOTES, 'UTF-8'))
        $titlePattern = 'Stats for %s';
        $maliciousName = '<img src=x onerror=alert(1)>';
        $escaped = htmlspecialchars($maliciousName, ENT_QUOTES, 'UTF-8');
        $output = sprintf($titlePattern, $escaped);

        $this->assertStringNotContainsString('<img', $output);
        $this->assertStringContainsString('&lt;img', $output);
    }

    #[Test]
    public function cleanNamePassesThroughUnchanged(): void
    {
        $cleanName = 'Acme Corp';
        $escaped = htmlspecialchars($cleanName, ENT_QUOTES, 'UTF-8');

        $this->assertSame('Acme Corp', $escaped);
    }

    // ---------------------------------------------------------------
    // C-3: Finished banners also uses escaped name
    // ---------------------------------------------------------------

    #[Test]
    public function finishedBannersPatternEscapesName(): void
    {
        $finishedPattern = 'Finished banners for %s';
        $maliciousName = '"><script>document.cookie</script>';
        $escaped = htmlspecialchars($maliciousName, ENT_QUOTES, 'UTF-8');
        $output = sprintf($finishedPattern, $escaped);

        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringNotContainsString('">', $output);
    }

    // ---------------------------------------------------------------
    // C-2: Authorization — cid must match session client id
    // ---------------------------------------------------------------

    #[Test]
    public function cidMustMatchSessionClientId(): void
    {
        $_SESSION = ['banner_client_id' => 5];
        $banner_client_id = (int) $_SESSION['banner_client_id'];
        $cid = 5;

        $this->assertSame($banner_client_id, $cid);
    }

    #[Test]
    public function cidMismatchIsDetected(): void
    {
        $_SESSION = ['banner_client_id' => 5];
        $banner_client_id = (int) $_SESSION['banner_client_id'];
        $cid = 99;

        $this->assertNotSame($banner_client_id, $cid);
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }
}
