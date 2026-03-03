<?php
/**
 * Unit tests for LostPassSecurity
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @package   core
 * @since     2.5.12
 */

declare(strict_types=1);

namespace xoopsclass;

use kernel\KernelTestCase;
use LostPassSecurity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

/**
 * Unit tests for LostPassSecurity class (rate limiting only).
 *
 * Token creation/verification is now handled by XoopsTokenHandler.
 * LostPassSecurity retains rate limiting via XoopsCache and Protector integration.
 *
 * @category  Test
 * @package   core
 * @author    XOOPS Team
 * @copyright (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */
#[CoversClass(LostPassSecurity::class)]
class LostPassSecurityTest extends KernelTestCase
{
    private LostPassSecurity $security;

    protected function setUp(): void
    {
        parent::setUp();
        require_once XOOPS_ROOT_PATH . '/class/LostPassSecurity.php';
        $this->security = new LostPassSecurity();
    }

    /* ========================================================
     * Rate limiting (isRateLimited)
     * ====================================================== */

    #[Test]
    public function testIsRateLimitedReturnsFalseWhenCacheUnavailable(): void
    {
        // With no XoopsCache available, rate limiting should fail-open
        $this->assertFalse($this->security->isRateLimited('127.0.0.1', 'test@example.com'));
    }

    #[Test]
    public function testIsRateLimitedAcceptsEmptyIdentifier(): void
    {
        $this->assertFalse($this->security->isRateLimited('127.0.0.1', ''));
    }

    #[Test]
    public function testIsRateLimitedAcceptsUidIdentifier(): void
    {
        $testIp = sprintf('192.168.%d.%d', 1, 1);
        $this->assertFalse($this->security->isRateLimited($testIp, 'uid:42'));
    }

    /* ========================================================
     * Constructor parameter enforcement
     * ====================================================== */

    #[Test]
    public function testConstructorEnforcesMinimumWindow(): void
    {
        $sec = new LostPassSecurity(window: 10); // below 60 minimum
        $this->assertSame(60, $this->getProtectedProperty($sec, 'window'));
    }

    #[Test]
    public function testConstructorEnforcesMinimumLimits(): void
    {
        $sec = new LostPassSecurity(ipLimit: 0, idLimit: 0);
        $this->assertSame(1, $this->getProtectedProperty($sec, 'ipLimit'));
        $this->assertSame(1, $this->getProtectedProperty($sec, 'idLimit'));
    }

    #[Test]
    public function testConstructorAcceptsCustomValues(): void
    {
        $sec = new LostPassSecurity(window: 1800, ipLimit: 50, idLimit: 10);
        $this->assertSame(1800, $this->getProtectedProperty($sec, 'window'));
        $this->assertSame(50, $this->getProtectedProperty($sec, 'ipLimit'));
        $this->assertSame(10, $this->getProtectedProperty($sec, 'idLimit'));
    }
}
