<?php
declare(strict_types=1);
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
 
/**
 * @copyright    2000-2026 XOOPS Project https://xoops.org/
 * @license      GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author       XOOPS Development Team
 */

namespace modulessystem;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SystemMenusTree;

require_once XOOPS_ROOT_PATH . '/modules/system/class/SystemMenusTree.php';

#[CoversClass(SystemMenusTree::class)]
class SystemMenusTreeTest extends TestCase
{
    private const MAX_DEPTH = 3;

    #[Test]
    public function rejectsSelfParenting(): void
    {
        $items = $this->makeItemArray([
            ['id' => 1, 'pid' => 0, 'cid' => 1],
        ]);
        $result = SystemMenusTree::validateParent(1, 1, 1, $items, self::MAX_DEPTH);
        $this->assertNotTrue($result);
    }

    #[Test]
    public function rejectsCrossCategory(): void
    {
        $items = $this->makeItemArray([
            ['id' => 1, 'pid' => 0, 'cid' => 1],
            ['id' => 2, 'pid' => 0, 'cid' => 2],
        ]);
        $result = SystemMenusTree::validateParent(1, 1, 2, $items, self::MAX_DEPTH);
        $this->assertNotTrue($result);
    }

    #[Test]
    public function rejectsCycle(): void
    {
        $items = $this->makeItemArray([
            ['id' => 1, 'pid' => 0, 'cid' => 1],
            ['id' => 2, 'pid' => 1, 'cid' => 1],
            ['id' => 3, 'pid' => 2, 'cid' => 1],
        ]);
        $result = SystemMenusTree::validateParent(1, 1, 3, $items, self::MAX_DEPTH);
        $this->assertNotTrue($result);
    }

    #[Test]
    public function rejectsExcessiveDepth(): void
    {
        $items = $this->makeItemArray([
            ['id' => 1, 'pid' => 0, 'cid' => 1],
            ['id' => 2, 'pid' => 1, 'cid' => 1],
            ['id' => 3, 'pid' => 2, 'cid' => 1],
            ['id' => 4, 'pid' => 0, 'cid' => 1],
        ]);
        $result = SystemMenusTree::validateParent(4, 1, 3, $items, self::MAX_DEPTH);
        $this->assertNotTrue($result);
    }

    #[Test]
    public function acceptsValidParent(): void
    {
        $items = $this->makeItemArray([
            ['id' => 1, 'pid' => 0, 'cid' => 1],
            ['id' => 2, 'pid' => 0, 'cid' => 1],
        ]);
        $result = SystemMenusTree::validateParent(2, 1, 1, $items, self::MAX_DEPTH);
        $this->assertTrue($result);
    }

    #[Test]
    public function acceptsRootParent(): void
    {
        $items = $this->makeItemArray([
            ['id' => 1, 'pid' => 0, 'cid' => 1],
        ]);
        $result = SystemMenusTree::validateParent(1, 1, 0, $items, self::MAX_DEPTH);
        $this->assertTrue($result);
    }

    #[Test]
    public function computeTreeDepthReturnsCorrectDepth(): void
    {
        $tree = [
            ['id' => 1, 'children' => [
                ['id' => 2, 'children' => [
                    ['id' => 3, 'children' => []],
                ]],
            ]],
        ];
        $this->assertSame(3, SystemMenusTree::computeDepth($tree));
    }

    #[Test]
    public function computeTreeDepthReturnZeroForEmpty(): void
    {
        $this->assertSame(0, SystemMenusTree::computeDepth([]));
    }

    #[Test]
    public function collectDescendantIdsFindsAll(): void
    {
        $items = $this->makeItemArray([
            ['id' => 1, 'pid' => 0, 'cid' => 1],
            ['id' => 2, 'pid' => 1, 'cid' => 1],
            ['id' => 3, 'pid' => 1, 'cid' => 1],
            ['id' => 4, 'pid' => 2, 'cid' => 1],
            ['id' => 5, 'pid' => 0, 'cid' => 1],
        ]);
        $result = SystemMenusTree::collectDescendantIds($items, 1);
        sort($result);
        $this->assertSame([2, 3, 4], $result);
    }

    private function makeItemArray(array $specs): array
    {
        $result = [];
        foreach ($specs as $s) {
            $result[] = [
                'items_id'  => $s['id'],
                'items_pid' => $s['pid'],
                'items_cid' => $s['cid'],
            ];
        }
        return $result;
    }
}
