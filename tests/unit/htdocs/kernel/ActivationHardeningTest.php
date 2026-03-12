<?php

declare(strict_types=1);

namespace kernel;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use XoopsMemberHandler;
use XoopsUser;
use XoopsUserHandler;
use ReflectionClass;

/**
 * Tests for activation flow security hardening:
 * - CSPRNG token generation (bin2hex(random_bytes(4)))
 * - Timing-safe token comparison (hash_equals)
 * - One-time-use actkey (cleared after activation)
 * - Restricted unserialize in profile forms
 */
#[CoversClass(XoopsMemberHandler::class)]
class ActivationHardeningTest extends TestCase
{
    private string $htdocsPath;

    protected function setUp(): void
    {
        $this->htdocsPath = XOOPS_ROOT_PATH;
    }

    /**
     * Helper: read file contents from htdocs path
     */
    private function readSource(string $relativePath): string
    {
        $fullPath = $this->htdocsPath . '/' . ltrim($relativePath, '/');
        $this->assertFileExists($fullPath, "Source file not found: {$fullPath}");
        $contents = file_get_contents($fullPath);
        $this->assertNotFalse($contents, 'Failed to read source file: ' . basename($fullPath));

        return $contents;
    }

    // ---------------------------------------------------------------
    // C-0a: Token generation uses CSPRNG (bin2hex(random_bytes(...)))
    // ---------------------------------------------------------------

    #[Test]
    public function registerPhpUsesCsprngForActkey(): void
    {
        $source = $this->readSource('register.php');
        $this->assertStringContainsString(
            "bin2hex(random_bytes(4))",
            $source,
            'htdocs/register.php must use bin2hex(random_bytes(4)) for actkey generation'
        );
        $this->assertStringNotContainsString(
            'md5(uniqid(mt_rand',
            $source,
            'htdocs/register.php must not use weak md5(uniqid(mt_rand())) for actkey'
        );
    }

    #[Test]
    public function profileRegisterPhpUsesCsprngForActkey(): void
    {
        $source = $this->readSource('modules/profile/register.php');
        $this->assertStringContainsString(
            "bin2hex(random_bytes(4))",
            $source,
            'profile/register.php must use bin2hex(random_bytes(4)) for actkey generation'
        );
        $this->assertStringNotContainsString(
            'md5(uniqid(mt_rand',
            $source,
            'profile/register.php must not use weak md5(uniqid(mt_rand())) for actkey'
        );
    }

    #[Test]
    public function profileDeactivatePhpUsesCsprngForActkey(): void
    {
        $source = $this->readSource('modules/profile/admin/deactivate.php');
        $this->assertStringContainsString(
            "bin2hex(random_bytes(4))",
            $source,
            'profile/admin/deactivate.php must use bin2hex(random_bytes(4)) for actkey generation'
        );
        $this->assertStringNotContainsString(
            'md5(uniqid(mt_rand',
            $source,
            'profile/admin/deactivate.php must not use weak md5(uniqid(mt_rand())) for actkey'
        );
    }

    #[Test]
    public function protectorUsesCsprngForActkey(): void
    {
        $source = $this->readSource('xoops_lib/modules/protector/class/protector.php');
        $this->assertStringContainsString(
            "bin2hex(random_bytes(4))",
            $source,
            'protector.php must use bin2hex(random_bytes(4)) for actkey generation'
        );
        $this->assertStringNotContainsString(
            'md5(uniqid(mt_rand',
            $source,
            'protector.php must not use weak md5(uniqid(mt_rand())) for actkey'
        );
    }

    #[Test]
    public function generatedActkeyIsEightHexChars(): void
    {
        // Verify the CSPRNG output format: bin2hex(random_bytes(4)) = 8 hex chars
        $actkey = bin2hex(random_bytes(4));
        $this->assertSame(8, strlen($actkey), 'Activation key must be 8 characters');
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}$/',
            $actkey,
            'Activation key must consist of lowercase hex characters'
        );
    }

    // ---------------------------------------------------------------
    // C-0b: Token comparison uses hash_equals (timing-safe)
    // ---------------------------------------------------------------

    #[Test]
    public function registerPhpUsesHashEqualsForActkeyComparison(): void
    {
        $source = $this->readSource('register.php');
        $this->assertStringContainsString(
            'hash_equals(',
            $source,
            'htdocs/register.php must use hash_equals() for actkey comparison'
        );
        // Ensure there is no loose != comparison for actkey
        $this->assertDoesNotMatchRegularExpression(
            '/getVar\s*\(\s*[\'"]actkey[\'"]\s*\)\s*!=\s*\$actkey/',
            $source,
            'htdocs/register.php must not use loose != for actkey comparison'
        );
    }

    #[Test]
    public function profileActivatePhpUsesHashEqualsForActkeyComparison(): void
    {
        $source = $this->readSource('modules/profile/activate.php');
        $this->assertStringContainsString(
            'hash_equals(',
            $source,
            'profile/activate.php must use hash_equals() for actkey comparison'
        );
        $this->assertDoesNotMatchRegularExpression(
            '/getVar\s*\(\s*[\'"]actkey[\'"]\s*\)\s*!=\s*\$actkey/',
            $source,
            'profile/activate.php must not use loose != for actkey comparison'
        );
    }

    // ---------------------------------------------------------------
    // C-0c: One-time-use — actkey cleared after activation
    // ---------------------------------------------------------------

    #[Test]
    public function activateUserClearsActkey(): void
    {
        $source = $this->readSource('kernel/member.php');

        // Find the activateUser method and verify it clears actkey
        $this->assertMatchesRegularExpression(
            '/function\s+activateUser\b.*?setVar\s*\(\s*[\'"]actkey[\'"]\s*,\s*[\'"][\'"]\s*\)/s',
            $source,
            'activateUser() must clear actkey by setting it to empty string'
        );
    }

    #[Test]
    public function activateUserDoesNotGenerateNewToken(): void
    {
        $source = $this->readSource('kernel/member.php');

        // Extract the activateUser method body using brace balancing
        $functionPos = strpos($source, 'function activateUser');
        $this->assertNotFalse($functionPos, 'Could not find activateUser function declaration');

        $openBracePos = strpos($source, '{', (int) $functionPos);
        $this->assertNotFalse($openBracePos, 'Could not find opening brace for activateUser method');

        $length = strlen($source);
        $depth = 0;
        $endBracePos = null;

        for ($i = (int) $openBracePos; $i < $length; $i++) {
            if ($source[$i] === '{') {
                $depth++;
            } elseif ($source[$i] === '}') {
                $depth--;
                if ($depth === 0) {
                    $endBracePos = $i;
                    break;
                }
            }
        }

        $this->assertNotNull($endBracePos, 'Could not find matching closing brace for activateUser method');

        $methodBody = substr($source, (int) $openBracePos + 1, (int) $endBracePos - (int) $openBracePos - 1);

        // It should NOT generate a new token
        $this->assertStringNotContainsString(
            'generateSecureToken',
            $methodBody,
            'activateUser() should not generate a new token; it should clear actkey'
        );
    }

    #[Test]
    public function activateUserSetsActkeyToEmptyViaUnit(): void
    {
        // Load the member handler and dependencies
        require_once XOOPS_ROOT_PATH . '/kernel/user.php';
        require_once XOOPS_ROOT_PATH . '/kernel/group.php';
        require_once XOOPS_ROOT_PATH . '/kernel/member.php';

        $handler = (new ReflectionClass(XoopsMemberHandler::class))
            ->newInstanceWithoutConstructor();

        $ref = new ReflectionClass($handler);

        // Create a user with level=0 and a non-empty actkey
        $user = new XoopsUser();
        $user->setVar('level', 0);
        $user->setVar('actkey', 'abc12345');

        // Create mock user handler and assert insert is called with force=true
        $userHandler = $this->createMock(XoopsUserHandler::class);
        $userHandler->expects($this->once())
            ->method('insert')
            ->with($user, true)
            ->willReturn(true);

        // Inject userHandler via reflection
        $userHandlerProp = $ref->getProperty('userHandler');
        $userHandlerProp->setAccessible(true);
        $userHandlerProp->setValue($handler, $userHandler);

        $result = $handler->activateUser($user);

        $this->assertTrue($result, 'activateUser should return true on success');
        $this->assertSame(1, (int) $user->getVar('level'), 'User level should be set to 1');
        $this->assertSame('', $user->getVar('actkey'), 'actkey must be cleared (empty string) after activation');
    }

    // ---------------------------------------------------------------
    // M-8: unserialize() uses allowed_classes restriction
    // ---------------------------------------------------------------

    #[Test]
    public function profileFormsPhpUsesRestrictedUnserialize(): void
    {
        $source = $this->readSource('modules/profile/include/forms.php');

        // Every unserialize call must include allowed_classes => false
        // Find lines containing unserialize
        $lines = explode("\n", $source);
        $unserializeLines = array_filter($lines, static function ($line) {
            return str_contains($line, 'unserialize(');
        });

        $this->assertNotEmpty($unserializeLines, 'Should find unserialize() calls in profile forms.php');

        foreach ($unserializeLines as $lineNum => $line) {
            $this->assertStringContainsString(
                "'allowed_classes' => false",
                $line,
                'Line ' . ($lineNum + 1) . ": unserialize() must include ['allowed_classes' => false]: " . trim($line)
            );
        }
    }

    #[Test]
    public function profileFormsPhpHasNoUnrestrictedUnserialize(): void
    {
        $source = $this->readSource('modules/profile/include/forms.php');

        // Count unserialize calls vs calls with allowed_classes
        preg_match_all('/\bunserialize\s*\(/', $source, $allCalls);
        preg_match_all('/\bunserialize\s*\([^;]*allowed_classes/', $source, $safeCalls);

        $this->assertSame(
            count($allCalls[0]),
            count($safeCalls[0]),
            'All unserialize() calls must have allowed_classes restriction'
        );
    }
}
