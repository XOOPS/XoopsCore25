<?php

declare(strict_types=1);

namespace xoopsclass;

use CriteriaElement;
use CriteriaCompo;
use Criteria;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Comprehensive tests for the XOOPS Criteria classes:
 *   - CriteriaElement (abstract base)
 *   - CriteriaCompo   (collection of criteria with AND/OR)
 *   - Criteria         (single criterion with operator)
 *
 * Bootstrap already loads criteria.php and provides $GLOBALS['xoopsDB']
 * as a XoopsTestStubDatabase with quote/escape/prefix methods.
 *
 * @see \CriteriaElement
 * @see \CriteriaCompo
 * @see \Criteria
 */
class CriteriaTest extends TestCase
{
    /** @var \XoopsTestStubDatabase */
    private $db;

    protected function setUp(): void
    {
        $this->db = $GLOBALS['xoopsDB'];
        // Reset global default for inner wildcards between tests
        Criteria::setDefaultAllowInnerWildcards(false);
    }

    protected function tearDown(): void
    {
        // Ensure clean state for other tests
        Criteria::setDefaultAllowInnerWildcards(false);
    }

    // =========================================================================
    // CriteriaElement — Constructor defaults
    // =========================================================================

    public function testCriteriaElementConstructorSetsDefaults(): void
    {
        $el = new CriteriaElement();

        $this->assertSame('ASC', $el->order);
        $this->assertSame('', $el->sort);
        $this->assertSame(0, $el->limit);
        $this->assertSame(0, $el->start);
        $this->assertSame('', $el->groupby);
    }

    // =========================================================================
    // CriteriaElement — setSort / getSort
    // =========================================================================

    public function testSetSortAndGetSort(): void
    {
        $el = new CriteriaElement();
        $el->setSort('created');

        $this->assertSame('created', $el->getSort());
    }

    public function testSetSortEmptyString(): void
    {
        $el = new CriteriaElement();
        $el->setSort('name');
        $el->setSort('');

        $this->assertSame('', $el->getSort());
    }

    public function testSetSortMultipleColumns(): void
    {
        $el = new CriteriaElement();
        $el->setSort('uid, name');

        $this->assertSame('uid, name', $el->getSort());
    }

    // =========================================================================
    // CriteriaElement — setOrder / getOrder
    // =========================================================================

    public function testSetOrderDescChangesOrder(): void
    {
        $el = new CriteriaElement();
        $el->setOrder('DESC');

        $this->assertSame('DESC', $el->getOrder());
    }

    public function testSetOrderDescCaseInsensitive(): void
    {
        $el = new CriteriaElement();
        $el->setOrder('desc');

        $this->assertSame('DESC', $el->getOrder());
    }

    public function testSetOrderDescMixedCase(): void
    {
        $el = new CriteriaElement();
        $el->setOrder('DeSc');

        $this->assertSame('DESC', $el->getOrder());
    }

    public function testSetOrderAscKeepsAsc(): void
    {
        $el = new CriteriaElement();
        $el->setOrder('ASC');

        $this->assertSame('ASC', $el->getOrder());
    }

    public function testSetOrderRandomStringKeepsAsc(): void
    {
        $el = new CriteriaElement();
        $el->setOrder('RANDOM');

        $this->assertSame('ASC', $el->getOrder());
    }

    public function testSetOrderEmptyStringKeepsAsc(): void
    {
        $el = new CriteriaElement();
        $el->setOrder('');

        $this->assertSame('ASC', $el->getOrder());
    }

    public function testSetOrderAfterDescDoesNotRevert(): void
    {
        $el = new CriteriaElement();
        $el->setOrder('DESC');
        $el->setOrder('ASC');

        // setOrder only changes to DESC; setting ASC does NOT revert
        $this->assertSame('DESC', $el->getOrder());
    }

    public function testGetOrderDefaultIsAsc(): void
    {
        $el = new CriteriaElement();

        $this->assertSame('ASC', $el->getOrder());
    }

    // =========================================================================
    // CriteriaElement — setLimit / getLimit
    // =========================================================================

    public function testSetLimitAndGetLimit(): void
    {
        $el = new CriteriaElement();
        $el->setLimit(25);

        $this->assertSame(25, $el->getLimit());
    }

    public function testSetLimitCastsStringToInt(): void
    {
        $el = new CriteriaElement();
        $el->setLimit('50');

        $this->assertSame(50, $el->getLimit());
    }

    public function testSetLimitDefaultZero(): void
    {
        $el = new CriteriaElement();
        $el->setLimit();

        $this->assertSame(0, $el->getLimit());
    }

    public function testSetLimitNegativeValue(): void
    {
        $el = new CriteriaElement();
        $el->setLimit(-10);

        $this->assertSame(-10, $el->getLimit());
    }

    // =========================================================================
    // CriteriaElement — setStart / getStart
    // =========================================================================

    public function testSetStartAndGetStart(): void
    {
        $el = new CriteriaElement();
        $el->setStart(100);

        $this->assertSame(100, $el->getStart());
    }

    public function testSetStartCastsStringToInt(): void
    {
        $el = new CriteriaElement();
        $el->setStart('75');

        $this->assertSame(75, $el->getStart());
    }

    public function testSetStartDefaultZero(): void
    {
        $el = new CriteriaElement();
        $el->setStart();

        $this->assertSame(0, $el->getStart());
    }

    // =========================================================================
    // CriteriaElement — setGroupBy / getGroupby
    // =========================================================================

    public function testSetGroupByAndGetGroupby(): void
    {
        $el = new CriteriaElement();
        $el->setGroupBy('category');

        $this->assertSame(' GROUP BY category', $el->getGroupby());
    }

    public function testGetGroupbyReturnsEmptyWhenNotSet(): void
    {
        $el = new CriteriaElement();

        $this->assertSame('', $el->getGroupby());
    }

    public function testGetGroupbyReturnsEmptyAfterClear(): void
    {
        $el = new CriteriaElement();
        $el->setGroupBy('uid');
        $el->setGroupBy('');

        $this->assertSame('', $el->getGroupby());
    }

    public function testSetGroupByMultipleColumns(): void
    {
        $el = new CriteriaElement();
        $el->setGroupBy('category, status');

        $this->assertSame(' GROUP BY category, status', $el->getGroupby());
    }

    // =========================================================================
    // CriteriaElement — render() base class
    // =========================================================================

    public function testCriteriaElementRenderReturnsNullOrEmpty(): void
    {
        $el = new CriteriaElement();
        $result = $el->render();

        // Base class render() has empty body, returns null
        $this->assertEmpty($result);
    }

    // =========================================================================
    // Criteria — Constructor basic assignment
    // =========================================================================

    public function testCriteriaConstructorStoresProperties(): void
    {
        $c = new Criteria('uid', 5, '>', 'u', 'MAX(%s)', true);

        $this->assertSame('uid', $c->column);
        $this->assertSame(5, $c->value);
        $this->assertSame('>', $c->operator);
        $this->assertSame('u', $c->prefix);
        $this->assertSame('MAX(%s)', $c->function);
    }

    public function testCriteriaConstructorDefaults(): void
    {
        $c = new Criteria('status');

        $this->assertSame('status', $c->column);
        $this->assertSame('', $c->value);
        $this->assertSame('=', $c->operator);
        $this->assertSame('', $c->prefix);
        $this->assertSame('', $c->function);
    }

    // =========================================================================
    // Criteria — Inherited CriteriaElement methods
    // =========================================================================

    public function testCriteriaInheritsSetSort(): void
    {
        $c = new Criteria('uid', 5);
        $c->setSort('uid');

        $this->assertSame('uid', $c->getSort());
    }

    public function testCriteriaInheritsSetOrder(): void
    {
        $c = new Criteria('uid', 5);
        $c->setOrder('DESC');

        $this->assertSame('DESC', $c->getOrder());
    }

    public function testCriteriaInheritsSetLimit(): void
    {
        $c = new Criteria('uid', 5);
        $c->setLimit(10);

        $this->assertSame(10, $c->getLimit());
    }

    public function testCriteriaInheritsSetStart(): void
    {
        $c = new Criteria('uid', 5);
        $c->setStart(20);

        $this->assertSame(20, $c->getStart());
    }

    public function testCriteriaInheritsSetGroupBy(): void
    {
        $c = new Criteria('uid', 5);
        $c->setGroupBy('uid');

        $this->assertSame(' GROUP BY uid', $c->getGroupby());
    }

    // =========================================================================
    // Criteria — Simple equality render
    // =========================================================================

    public function testRenderSimpleIntEquality(): void
    {
        $c = new Criteria('uid', 5);

        $this->assertSame('`uid` = 5', $c->render($this->db));
    }

    public function testRenderSimpleStringEquality(): void
    {
        $c = new Criteria('name', 'John');

        $this->assertSame("`name` = 'John'", $c->render($this->db));
    }

    public function testRenderNumericStringTreatedAsInt(): void
    {
        $c = new Criteria('uid', '42');

        $this->assertSame('`uid` = 42', $c->render($this->db));
    }

    public function testRenderNegativeNumericString(): void
    {
        $c = new Criteria('offset', '-10');

        $this->assertSame('`offset` = -10', $c->render($this->db));
    }

    // =========================================================================
    // Criteria — Comparison operators
    // =========================================================================

    public function testRenderNotEqual(): void
    {
        $c = new Criteria('status', 'active', '!=');

        $this->assertSame("`status` != 'active'", $c->render($this->db));
    }

    public function testRenderGreaterThan(): void
    {
        $c = new Criteria('age', 18, '>');

        $this->assertSame('`age` > 18', $c->render($this->db));
    }

    public function testRenderLessThan(): void
    {
        $c = new Criteria('price', 100, '<');

        $this->assertSame('`price` < 100', $c->render($this->db));
    }

    public function testRenderGreaterThanOrEqual(): void
    {
        $c = new Criteria('score', 90, '>=');

        $this->assertSame('`score` >= 90', $c->render($this->db));
    }

    public function testRenderLessThanOrEqual(): void
    {
        $c = new Criteria('weight', 50, '<=');

        $this->assertSame('`weight` <= 50', $c->render($this->db));
    }

    public function testRenderNotEqualDiamond(): void
    {
        $c = new Criteria('status', 'deleted', '<>');

        $this->assertSame("`status` <> 'deleted'", $c->render($this->db));
    }

    // =========================================================================
    // Criteria — LIKE operator
    // =========================================================================

    public function testRenderLikeWithLeadingAndTrailingWildcards(): void
    {
        $c = new Criteria('name', '%John%', 'LIKE');

        $this->assertSame("`name` LIKE '%John%'", $c->render($this->db));
    }

    public function testRenderLikeWithLeadingWildcardOnly(): void
    {
        $c = new Criteria('name', '%Smith', 'LIKE');

        $this->assertSame("`name` LIKE '%Smith'", $c->render($this->db));
    }

    public function testRenderLikeWithTrailingWildcardOnly(): void
    {
        $c = new Criteria('name', 'John%', 'LIKE');

        $this->assertSame("`name` LIKE 'John%'", $c->render($this->db));
    }

    public function testRenderLikeEscapesInnerPercent(): void
    {
        // "100% complete" inside wildcards: inner % should be escaped
        // Code escapes inner % to \%, then addslashes() in quote() doubles backslash to \\%
        $c = new Criteria('title', '%100% done%', 'LIKE');

        $this->assertSame('`title` LIKE \'%100\\\\% done%\'', $c->render($this->db));
    }

    public function testRenderLikeEscapesInnerUnderscore(): void
    {
        // Code escapes inner _ to \_, then addslashes() doubles the backslash to \\_
        $c = new Criteria('code', '%a_b%', 'LIKE');

        $this->assertSame('`code` LIKE \'%a\\\\_b%\'', $c->render($this->db));
    }

    public function testRenderLikeEscapesInnerBackslash(): void
    {
        // Input: %foo\bar% (one backslash in PHP source is \\)
        // Code doubles backslash: foo\\bar, then addslashes() doubles again: foo\\\\bar
        $c = new Criteria('path', '%foo\\bar%', 'LIKE');

        $result = $c->render($this->db);
        $this->assertSame('`path` LIKE \'%foo\\\\\\\\bar%\'', $result);
    }

    public function testRenderLikeAllowInnerWildcardsPreservesInnerPercent(): void
    {
        $c = new Criteria('name', '%Jo%hn%', 'LIKE');
        $c->allowInnerWildcards();

        $this->assertSame("`name` LIKE '%Jo%hn%'", $c->render($this->db));
    }

    public function testRenderLikeAllowInnerWildcardsPreservesInnerUnderscore(): void
    {
        $c = new Criteria('name', '%user_name%', 'LIKE');
        $c->allowInnerWildcards();

        $this->assertSame("`name` LIKE '%user_name%'", $c->render($this->db));
    }

    public function testRenderLikeAllPercentReturnsEmpty(): void
    {
        $c = new Criteria('name', '%%', 'LIKE');

        $this->assertSame('', $c->render($this->db));
    }

    public function testRenderLikeTriplePercentReturnsEmpty(): void
    {
        $c = new Criteria('name', '%%%', 'LIKE');

        $this->assertSame('', $c->render($this->db));
    }

    public function testRenderLikeSinglePercentReturnsEmpty(): void
    {
        $c = new Criteria('name', '%', 'LIKE');

        $this->assertSame('', $c->render($this->db));
    }

    public function testRenderNotLikeAllPercentDoesNotReturnEmpty(): void
    {
        // NOT LIKE with all-% does NOT trigger the empty return (only LIKE does)
        $c = new Criteria('name', '%%', 'NOT LIKE');

        $this->assertSame("`name` NOT LIKE '%%'", $c->render($this->db));
    }

    public function testRenderLikeNoWildcards(): void
    {
        $c = new Criteria('name', 'John', 'LIKE');

        $this->assertSame("`name` LIKE 'John'", $c->render($this->db));
    }

    public function testRenderNotLike(): void
    {
        $c = new Criteria('name', '%Admin%', 'NOT LIKE');

        $this->assertSame("`name` NOT LIKE '%Admin%'", $c->render($this->db));
    }

    // =========================================================================
    // Criteria — setDefaultAllowInnerWildcards (static)
    // =========================================================================

    public function testSetDefaultAllowInnerWildcardsAffectsNewInstances(): void
    {
        Criteria::setDefaultAllowInnerWildcards(true);

        $c = new Criteria('name', '%user_name%', 'LIKE');

        // Inner underscore should NOT be escaped because default allows inner wildcards
        $this->assertSame("`name` LIKE '%user_name%'", $c->render($this->db));
    }

    public function testSetDefaultAllowInnerWildcardsFalseEscapesInner(): void
    {
        Criteria::setDefaultAllowInnerWildcards(false);

        $c = new Criteria('name', '%user_name%', 'LIKE');

        $this->assertSame('`name` LIKE \'%user\\\\_name%\'', $c->render($this->db));
    }

    public function testAllowInnerWildcardsReturnsSelfForChaining(): void
    {
        $c = new Criteria('name', '%test%', 'LIKE');
        $result = $c->allowInnerWildcards();

        $this->assertSame($c, $result);
    }

    public function testAllowInnerWildcardsCanBeTurnedOff(): void
    {
        $c = new Criteria('name', '%user_name%', 'LIKE');
        $c->allowInnerWildcards(true);
        $c->allowInnerWildcards(false);

        // Should escape inner wildcards again
        $this->assertSame('`name` LIKE \'%user\\\\_name%\'', $c->render($this->db));
    }

    // =========================================================================
    // Criteria — IS NULL / IS NOT NULL
    // =========================================================================

    public function testRenderIsNull(): void
    {
        $c = new Criteria('email', '', 'IS NULL');

        $this->assertSame('`email` IS NULL', $c->render($this->db));
    }

    public function testRenderIsNotNull(): void
    {
        $c = new Criteria('email', '', 'IS NOT NULL');

        $this->assertSame('`email` IS NOT NULL', $c->render($this->db));
    }

    public function testRenderIsNullIgnoresValue(): void
    {
        $c = new Criteria('field', 'ignored_value', 'IS NULL');

        $this->assertSame('`field` IS NULL', $c->render($this->db));
    }

    public function testRenderIsNullCaseInsensitive(): void
    {
        $c = new Criteria('field', '', 'is null');

        $this->assertSame('`field` IS NULL', $c->render($this->db));
    }

    // =========================================================================
    // Criteria — IN / NOT IN with array
    // =========================================================================

    public function testRenderInWithIntArray(): void
    {
        $c = new Criteria('uid', [1, 2, 3], 'IN');

        $this->assertSame('`uid` IN (1,2,3)', $c->render($this->db));
    }

    public function testRenderInWithStringArray(): void
    {
        $c = new Criteria('name', ['alice', 'bob'], 'IN');

        $this->assertSame("`name` IN ('alice','bob')", $c->render($this->db));
    }

    public function testRenderInWithMixedArray(): void
    {
        $c = new Criteria('value', [1, 'two', 3], 'IN');

        $this->assertSame("`value` IN (1,'two',3)", $c->render($this->db));
    }

    public function testRenderNotInWithIntArray(): void
    {
        $c = new Criteria('uid', [4, 5, 6], 'NOT IN');

        $this->assertSame('`uid` NOT IN (4,5,6)', $c->render($this->db));
    }

    public function testRenderNotInWithStringArray(): void
    {
        $c = new Criteria('status', ['deleted', 'banned'], 'NOT IN');

        $this->assertSame("`status` NOT IN ('deleted','banned')", $c->render($this->db));
    }

    public function testRenderInWithEmptyArray(): void
    {
        $c = new Criteria('uid', [], 'IN');

        $this->assertSame('`uid` IN ()', $c->render($this->db));
    }

    public function testRenderInWithSingleElementArray(): void
    {
        $c = new Criteria('uid', [42], 'IN');

        $this->assertSame('`uid` IN (42)', $c->render($this->db));
    }

    public function testRenderInWithNumericStringsInArray(): void
    {
        $c = new Criteria('uid', ['1', '2', '3'], 'IN');

        // Numeric strings are cast to int
        $this->assertSame('`uid` IN (1,2,3)', $c->render($this->db));
    }

    // =========================================================================
    // Criteria — IN / NOT IN with legacy string
    // =========================================================================

    public function testRenderInWithSafeLegacyNumericList(): void
    {
        $c = new Criteria('uid', '(1,2,3)', 'IN');

        $this->assertSame('`uid` IN (1,2,3)', $c->render($this->db));
    }

    public function testRenderInWithSafeLegacyQuotedList(): void
    {
        $c = new Criteria('name', "('alice','bob')", 'IN');

        $this->assertSame("`name` IN ('alice','bob')", $c->render($this->db));
    }

    public function testRenderInWithSafeLegacyEmptyParens(): void
    {
        $c = new Criteria('uid', '()', 'IN');

        $this->assertSame('`uid` IN ()', $c->render($this->db));
    }

    public function testRenderInWithUnsafeLegacyStringQuotesSafely(): void
    {
        $c = new Criteria('uid', 'DROP TABLE', 'IN');

        // Unsafe string gets quoted as a single literal
        $this->assertSame("`uid` IN ('DROP TABLE')", $c->render($this->db));
    }

    public function testRenderInWithUnsafeSqlInjection(): void
    {
        $c = new Criteria('uid', "1; DROP TABLE users--", 'IN');

        // Not safe, so it should be wrapped in quote()
        $result = $c->render($this->db);
        $this->assertStringContainsString('IN (', $result);
        $this->assertStringContainsString("'", $result);
    }

    // =========================================================================
    // Criteria — Empty value handling
    // =========================================================================

    public function testRenderEmptyValueReturnsEmptyByDefault(): void
    {
        $c = new Criteria('name', '');

        $this->assertSame('', $c->render($this->db));
    }

    public function testRenderEmptyValueWithAllowEmptyValueTrue(): void
    {
        $c = new Criteria('name', '', '=', '', '', true);

        $result = $c->render($this->db);
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('`name`', $result);
    }

    public function testRenderWhitespaceOnlyValueReturnsEmpty(): void
    {
        $c = new Criteria('name', '   ');

        $this->assertSame('', $c->render($this->db));
    }

    public function testRenderWhitespaceWithAllowEmptyValueRenders(): void
    {
        $c = new Criteria('name', '   ', '=', '', '', true);

        $result = $c->render($this->db);
        $this->assertNotEmpty($result);
    }

    // =========================================================================
    // Criteria — Legacy always-true pattern
    // =========================================================================

    public function testRenderLegacyAlwaysTrueReturnsEmpty(): void
    {
        $c = new Criteria(1, '1', '=');

        $this->assertSame('', $c->render($this->db));
    }

    public function testRenderLegacyAlwaysTrueClearsColumnAndValue(): void
    {
        $c = new Criteria(1, '1', '=');

        $this->assertSame('', $c->column);
        $this->assertSame('', $c->value);
    }

    public function testRenderLegacyAlwaysTrueOnlyMatchesExactPattern(): void
    {
        // (2, '2', '=') should NOT be treated as always-true
        $c = new Criteria(2, '2', '=');

        $result = $c->render($this->db);
        $this->assertNotEmpty($result);
    }

    // =========================================================================
    // Criteria — Prefix
    // =========================================================================

    public function testRenderWithPrefix(): void
    {
        $c = new Criteria('uid', 5, '=', 'u');

        $this->assertSame('u.`uid` = 5', $c->render($this->db));
    }

    public function testRenderWithPrefixAndStringValue(): void
    {
        $c = new Criteria('name', 'admin', '=', 't');

        $this->assertSame("t.`name` = 'admin'", $c->render($this->db));
    }

    // =========================================================================
    // Criteria — Function wrapper
    // =========================================================================

    public function testRenderWithFunction(): void
    {
        $c = new Criteria('name', 'john', '=', '', 'LOWER(%s)');

        $this->assertSame("LOWER(`name`) = 'john'", $c->render($this->db));
    }

    public function testRenderWithFunctionAndPrefix(): void
    {
        $c = new Criteria('name', 'john', '=', 'u', 'LOWER(%s)');

        $this->assertSame("LOWER(u.`name`) = 'john'", $c->render($this->db));
    }

    public function testRenderWithCountFunction(): void
    {
        $c = new Criteria('uid', 5, '>', '', 'COUNT(%s)');

        $this->assertSame('COUNT(`uid`) > 5', $c->render($this->db));
    }

    // =========================================================================
    // Criteria — Column with dot (no backtick)
    // =========================================================================

    public function testRenderColumnWithDotNoBacktick(): void
    {
        $c = new Criteria('u.uid', 5);

        $this->assertSame('u.uid = 5', $c->render($this->db));
    }

    public function testRenderColumnWithDotAndPrefix(): void
    {
        // Prefix is still prepended even with a dot in column
        $c = new Criteria('u.uid', 5, '=', 'alias');

        $this->assertSame('alias.u.uid = 5', $c->render($this->db));
    }

    // =========================================================================
    // Criteria — Column with parentheses (no backtick)
    // =========================================================================

    public function testRenderColumnWithParenthesesNoBacktick(): void
    {
        $c = new Criteria('COUNT(*)', 5, '>');

        $this->assertSame('COUNT(*) > 5', $c->render($this->db));
    }

    public function testRenderColumnWithFunctionCallNoBacktick(): void
    {
        $c = new Criteria('SUM(amount)', 100, '>=');

        $this->assertSame('SUM(amount) >= 100', $c->render($this->db));
    }

    // =========================================================================
    // Criteria — Backtick value for column-to-column
    // =========================================================================

    public function testRenderBacktickValueForColumnToColumn(): void
    {
        $c = new Criteria('uid', '`other_uid`');

        $this->assertSame('`uid` = `other_uid`', $c->render($this->db));
    }

    public function testRenderBacktickValueWithDot(): void
    {
        $c = new Criteria('uid', '`users.uid`');

        $this->assertSame('`uid` = `users.uid`', $c->render($this->db));
    }

    public function testRenderBacktickValueInvalidIdentifierReturnsEmptyBackticks(): void
    {
        $c = new Criteria('uid', '`DROP TABLE`');

        // Space is not allowed in the identifier regex, so it returns ``
        $this->assertSame('`uid` = ``', $c->render($this->db));
    }

    // =========================================================================
    // Criteria — renderWhere
    // =========================================================================

    public function testRenderWhereWithContent(): void
    {
        $c = new Criteria('uid', 5);

        $this->assertSame('WHERE `uid` = 5', $c->renderWhere($this->db));
    }

    public function testRenderWhereEmptyReturnsEmptyString(): void
    {
        $c = new Criteria('name', '');

        $this->assertSame('', $c->renderWhere($this->db));
    }

    public function testRenderWhereWithLegacyAlwaysTrue(): void
    {
        $c = new Criteria(1, '1', '=');

        $this->assertSame('', $c->renderWhere($this->db));
    }

    // =========================================================================
    // Criteria — No DB throws RuntimeException
    // =========================================================================

    public function testRenderWithExplicitDbParameter(): void
    {
        // Verify that passing a DB explicitly works and ignores the global
        $c = new Criteria('uid', 5);

        $result = $c->render($this->db);
        $this->assertSame('`uid` = 5', $result);
    }

    public function testRenderFallsBackToGlobalDb(): void
    {
        // Calling render(null) should use $GLOBALS['xoopsDB'] as fallback
        $c = new Criteria('uid', 10);

        $result = $c->render(null);
        $this->assertSame('`uid` = 10', $result);
    }

    // =========================================================================
    // Criteria — render uses global DB fallback
    // =========================================================================

    public function testRenderUsesGlobalDbWhenNullPassed(): void
    {
        $c = new Criteria('uid', 5);

        // Calling render() without argument should use $GLOBALS['xoopsDB']
        $this->assertSame('`uid` = 5', $c->render());
    }

    // =========================================================================
    // Criteria — renderLdap
    // =========================================================================

    public function testRenderLdapSimpleEquality(): void
    {
        $c = new Criteria('uid', '1000', '=');
        // Ldap bypasses the always-true check because column is already cleared
        // Test with a real column
        $c2 = new Criteria('cn', 'John', '=');

        $this->assertSame('(cn=John)', $c2->renderLdap());
    }

    public function testRenderLdapGreaterThanBecomesGreaterEqual(): void
    {
        $c = new Criteria('uidNumber', '500', '>');

        $result = $c->renderLdap();
        $this->assertSame('(uidNumber>=500)', $result);
    }

    public function testRenderLdapLessThanBecomesLessEqual(): void
    {
        $c = new Criteria('uidNumber', '500', '<');

        $result = $c->renderLdap();
        $this->assertSame('(uidNumber<=500)', $result);
    }

    public function testRenderLdapNotEqual(): void
    {
        $c = new Criteria('status', 'disabled', '!=');

        $result = $c->renderLdap();
        $this->assertSame('(!(status=disabled))', $result);
    }

    public function testRenderLdapNotEqualDiamond(): void
    {
        $c = new Criteria('status', 'disabled', '<>');

        $result = $c->renderLdap();
        $this->assertSame('(!(status=disabled))', $result);
    }

    public function testRenderLdapInOperator(): void
    {
        $c = new Criteria('uid', '(1,2,3)', 'IN');

        $result = $c->renderLdap();
        $this->assertSame('(|(uid=1)(uid=2)(uid=3))', $result);
    }

    // =========================================================================
    // CriteriaCompo — Constructor
    // =========================================================================

    public function testCriteriaCompoConstructorWithNull(): void
    {
        $cc = new CriteriaCompo();

        $this->assertSame([], $cc->criteriaElements);
        $this->assertSame([], $cc->conditions);
    }

    public function testCriteriaCompoConstructorWithSingleElement(): void
    {
        $c = new Criteria('uid', 5);
        $cc = new CriteriaCompo($c);

        $this->assertCount(1, $cc->criteriaElements);
        $this->assertSame(['AND'], $cc->conditions);
    }

    public function testCriteriaCompoConstructorWithCustomCondition(): void
    {
        $c = new Criteria('uid', 5);
        $cc = new CriteriaCompo($c, 'OR');

        $this->assertCount(1, $cc->criteriaElements);
        $this->assertSame(['OR'], $cc->conditions);
    }

    // =========================================================================
    // CriteriaCompo — add()
    // =========================================================================

    public function testAddAppendsElement(): void
    {
        $cc = new CriteriaCompo();
        $c1 = new Criteria('uid', 1);
        $c2 = new Criteria('uid', 2);

        $cc->add($c1);
        $cc->add($c2, 'OR');

        $this->assertCount(2, $cc->criteriaElements);
        $this->assertSame(['AND', 'OR'], $cc->conditions);
    }

    public function testAddReturnsThisForChaining(): void
    {
        $cc = new CriteriaCompo();
        $c = new Criteria('uid', 1);

        $result = &$cc->add($c);

        $this->assertSame($cc, $result);
    }

    public function testAddChainingMultiple(): void
    {
        $cc = new CriteriaCompo();
        $c1 = new Criteria('uid', 1);
        $c2 = new Criteria('name', 'test');

        $cc->add($c1)->add($c2, 'OR');

        $this->assertCount(2, $cc->criteriaElements);
    }

    // =========================================================================
    // CriteriaCompo — render()
    // =========================================================================

    public function testRenderEmptyCollectionReturnsEmpty(): void
    {
        $cc = new CriteriaCompo();

        $this->assertSame('', $cc->render($this->db));
    }

    public function testRenderSingleElement(): void
    {
        $c = new Criteria('uid', 5);
        $cc = new CriteriaCompo($c);

        $this->assertSame('(`uid` = 5)', $cc->render($this->db));
    }

    public function testRenderTwoElementsWithAnd(): void
    {
        $c1 = new Criteria('uid', 5);
        $c2 = new Criteria('status', 'active');

        $cc = new CriteriaCompo($c1);
        $cc->add($c2);

        $this->assertSame("(`uid` = 5 AND `status` = 'active')", $cc->render($this->db));
    }

    public function testRenderTwoElementsWithOr(): void
    {
        $c1 = new Criteria('uid', 5);
        $c2 = new Criteria('uid', 10);

        $cc = new CriteriaCompo($c1);
        $cc->add($c2, 'OR');

        $this->assertSame('(`uid` = 5 OR `uid` = 10)', $cc->render($this->db));
    }

    public function testRenderThreeElementsMixed(): void
    {
        $c1 = new Criteria('uid', 5);
        $c2 = new Criteria('status', 'active');
        $c3 = new Criteria('role', 'admin');

        $cc = new CriteriaCompo($c1);
        $cc->add($c2);
        $cc->add($c3, 'OR');

        $this->assertSame("(`uid` = 5 AND `status` = 'active' OR `role` = 'admin')", $cc->render($this->db));
    }

    public function testRenderSkipsEmptyChildRender(): void
    {
        $c1 = new Criteria('uid', 5);
        $c2 = new Criteria('name', ''); // Empty value, will render as ''
        $c3 = new Criteria('status', 'active');

        $cc = new CriteriaCompo($c1);
        $cc->add($c2);
        $cc->add($c3);

        // c2 renders empty, should be skipped
        $this->assertSame("(`uid` = 5 AND `status` = 'active')", $cc->render($this->db));
    }

    public function testRenderWrapsInParentheses(): void
    {
        $c = new Criteria('uid', 5);
        $cc = new CriteriaCompo($c);

        $result = $cc->render($this->db);
        $this->assertStringStartsWith('(', $result);
        $this->assertStringEndsWith(')', $result);
    }

    public function testRenderPassesDbToChildren(): void
    {
        // String values require DB for quoting — if DB is not passed, it fails
        $c = new Criteria('name', 'test');
        $cc = new CriteriaCompo($c);

        // Should work because render() passes $db to child's render()
        $result = $cc->render($this->db);
        $this->assertSame("(`name` = 'test')", $result);
    }

    public function testRenderAllChildrenEmpty(): void
    {
        // Both children render empty
        $c1 = new Criteria('name', '');
        $c2 = new Criteria('title', '');

        $cc = new CriteriaCompo($c1);
        $cc->add($c2);

        $this->assertSame('', $cc->render($this->db));
    }

    public function testRenderFirstChildEmptySecondHasValue(): void
    {
        // First child renders empty, second has value
        $c1 = new Criteria('name', '');      // renders ''
        $c2 = new Criteria('uid', 5);         // renders '`uid` = 5'

        $cc = new CriteriaCompo($c1);
        $cc->add($c2);

        // First element renders empty string, then second is appended
        // Because first is empty, the join uses the empty-check on renderString
        $result = $cc->render($this->db);
        $this->assertSame('(`uid` = 5)', $result);
    }

    // =========================================================================
    // CriteriaCompo — renderWhere()
    // =========================================================================

    public function testRenderWhereWithElements(): void
    {
        $c = new Criteria('uid', 5);
        $cc = new CriteriaCompo($c);

        $this->assertSame('WHERE (`uid` = 5)', $cc->renderWhere($this->db));
    }

    public function testRenderWhereEmptyReturnsEmptyString2(): void
    {
        $cc = new CriteriaCompo();

        $this->assertSame('', $cc->renderWhere($this->db));
    }

    public function testRenderWhereMultipleConditions(): void
    {
        $c1 = new Criteria('uid', 5);
        $c2 = new Criteria('status', 'active');

        $cc = new CriteriaCompo($c1);
        $cc->add($c2);

        $this->assertSame("WHERE (`uid` = 5 AND `status` = 'active')", $cc->renderWhere($this->db));
    }

    // =========================================================================
    // CriteriaCompo — Nested CriteriaCompo
    // =========================================================================

    public function testNestedCriteriaCompo(): void
    {
        // (uid = 5 AND (status = 'active' OR status = 'pending'))
        $inner = new CriteriaCompo(new Criteria('status', 'active'));
        $inner->add(new Criteria('status', 'pending'), 'OR');

        $outer = new CriteriaCompo(new Criteria('uid', 5));
        $outer->add($inner);

        $expected = "(`uid` = 5 AND (`status` = 'active' OR `status` = 'pending'))";
        $this->assertSame($expected, $outer->render($this->db));
    }

    public function testDeeplyNestedCriteriaCompo(): void
    {
        // ((a = 1 OR b = 2) AND (c = 3 OR d = 4))
        $inner1 = new CriteriaCompo(new Criteria('a', 1));
        $inner1->add(new Criteria('b', 2), 'OR');

        $inner2 = new CriteriaCompo(new Criteria('c', 3));
        $inner2->add(new Criteria('d', 4), 'OR');

        $outer = new CriteriaCompo($inner1);
        $outer->add($inner2);

        $expected = '((`a` = 1 OR `b` = 2) AND (`c` = 3 OR `d` = 4))';
        $this->assertSame($expected, $outer->render($this->db));
    }

    // =========================================================================
    // CriteriaCompo — Inherited methods from CriteriaElement
    // =========================================================================

    public function testCriteriaCompoInheritsSetSort(): void
    {
        $cc = new CriteriaCompo();
        $cc->setSort('name');

        $this->assertSame('name', $cc->getSort());
    }

    public function testCriteriaCompoInheritsSetOrder(): void
    {
        $cc = new CriteriaCompo();
        $cc->setOrder('DESC');

        $this->assertSame('DESC', $cc->getOrder());
    }

    public function testCriteriaCompoInheritsSetLimit(): void
    {
        $cc = new CriteriaCompo();
        $cc->setLimit(50);

        $this->assertSame(50, $cc->getLimit());
    }

    public function testCriteriaCompoInheritsSetStart(): void
    {
        $cc = new CriteriaCompo();
        $cc->setStart(100);

        $this->assertSame(100, $cc->getStart());
    }

    public function testCriteriaCompoInheritsSetGroupBy(): void
    {
        $cc = new CriteriaCompo();
        $cc->setGroupBy('category');

        $this->assertSame(' GROUP BY category', $cc->getGroupby());
    }

    // =========================================================================
    // CriteriaCompo — renderLdap()
    // =========================================================================

    public function testRenderLdapEmptyCollection(): void
    {
        $cc = new CriteriaCompo();

        $this->assertSame('', $cc->renderLdap());
    }

    public function testRenderLdapSingleElement(): void
    {
        $c = new Criteria('cn', 'John', '=');
        $cc = new CriteriaCompo($c);

        $this->assertSame('(cn=John)', $cc->renderLdap());
    }

    public function testRenderLdapTwoElementsWithAnd(): void
    {
        $c1 = new Criteria('cn', 'John', '=');
        $c2 = new Criteria('sn', 'Doe', '=');

        $cc = new CriteriaCompo($c1);
        $cc->add($c2);

        // AND is the default condition
        $this->assertSame('(&(cn=John)(sn=Doe))', $cc->renderLdap());
    }

    public function testRenderLdapTwoElementsWithOr(): void
    {
        $c1 = new Criteria('cn', 'John', '=');
        $c2 = new Criteria('cn', 'Jane', '=');

        $cc = new CriteriaCompo($c1);
        $cc->add($c2, 'OR');

        $this->assertSame('(|(cn=John)(cn=Jane))', $cc->renderLdap());
    }

    public function testRenderLdapThreeElements(): void
    {
        $c1 = new Criteria('cn', 'John', '=');
        $c2 = new Criteria('sn', 'Doe', '=');
        $c3 = new Criteria('mail', 'john@example.com', '=');

        $cc = new CriteriaCompo($c1);
        $cc->add($c2);
        $cc->add($c3);

        // Each additional element wraps with the previous
        $result = $cc->renderLdap();
        $this->assertStringContainsString('(cn=John)', $result);
        $this->assertStringContainsString('(sn=Doe)', $result);
        $this->assertStringContainsString('(mail=john@example.com)', $result);
    }

    public function testRenderLdapNestedCompo(): void
    {
        $inner = new CriteriaCompo(new Criteria('cn', 'John', '='));
        $inner->add(new Criteria('cn', 'Jane', '='), 'OR');

        $outer = new CriteriaCompo(new Criteria('objectClass', 'person', '='));
        $outer->add($inner);

        $result = $outer->renderLdap();
        $this->assertStringContainsString('(objectClass=person)', $result);
        $this->assertStringContainsString('(cn=John)', $result);
        $this->assertStringContainsString('(cn=Jane)', $result);
    }

    // =========================================================================
    // Criteria — Data-driven tests via providers
    // =========================================================================

    /**
     * @param string $column
     * @param mixed  $value
     * @param string $operator
     * @param string $expected
     */
    #[DataProvider('simpleComparisonProvider')]
    public function testRenderSimpleComparisons(string $column, $value, string $operator, string $expected): void
    {
        $c = new Criteria($column, $value, $operator);

        $this->assertSame($expected, $c->render($this->db));
    }

    /**
     * @return array<string, array{0: string, 1: mixed, 2: string, 3: string}>
     */
    public static function simpleComparisonProvider(): array
    {
        return [
            'int equal'            => ['uid', 5, '=', '`uid` = 5'],
            'int greater'          => ['age', 18, '>', '`age` > 18'],
            'int less'             => ['price', 100, '<', '`price` < 100'],
            'int gte'              => ['score', 90, '>=', '`score` >= 90'],
            'int lte'              => ['weight', 50, '<=', '`weight` <= 50'],
            'string equal'         => ['name', 'Alice', '=', "`name` = 'Alice'"],
            'string not equal'     => ['status', 'deleted', '!=', "`status` != 'deleted'"],
            'string diamond ne'    => ['role', 'guest', '<>', "`role` <> 'guest'"],
            'numeric string'       => ['uid', '42', '=', '`uid` = 42'],
            'negative numeric str' => ['balance', '-5', '=', '`balance` = -5'],
            'zero int'             => ['count', 0, '=', '`count` = 0'],
            'zero string'          => ['count', '0', '=', '`count` = 0'],
        ];
    }

    /**
     * @param string $column
     * @param string $pattern
     * @param string $operator
     * @param bool   $allowInner
     * @param string $expected
     */
    #[DataProvider('likePatternProvider')]
    public function testRenderLikePatterns(string $column, string $pattern, string $operator, bool $allowInner, string $expected): void
    {
        $c = new Criteria($column, $pattern, $operator);
        if ($allowInner) {
            $c->allowInnerWildcards();
        }

        $this->assertSame($expected, $c->render($this->db));
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string, 3: bool, 4: string}>
     */
    public static function likePatternProvider(): array
    {
        return [
            'like leading trailing'         => ['name', '%John%', 'LIKE', false, "`name` LIKE '%John%'"],
            'like leading only'             => ['name', '%Smith', 'LIKE', false, "`name` LIKE '%Smith'"],
            'like trailing only'            => ['name', 'John%', 'LIKE', false, "`name` LIKE 'John%'"],
            'like no wildcards'             => ['name', 'John', 'LIKE', false, "`name` LIKE 'John'"],
            'like inner pct escaped'        => ['t', '%a%b%', 'LIKE', false, '`t` LIKE \'%a\\\\%b%\''],
            'like inner pct allowed'        => ['t', '%a%b%', 'LIKE', true, "`t` LIKE '%a%b%'"],
            'like inner underscore escaped' => ['c', '%a_b%', 'LIKE', false, '`c` LIKE \'%a\\\\_b%\''],
            'like inner underscore allowed' => ['c', '%a_b%', 'LIKE', true, "`c` LIKE '%a_b%'"],
            'not like with wildcards'       => ['name', '%Admin%', 'NOT LIKE', false, "`name` NOT LIKE '%Admin%'"],
            'like all pct empty'            => ['name', '%%', 'LIKE', false, ''],
            'like single pct empty'         => ['name', '%', 'LIKE', false, ''],
            'not like all pct NOT empty'    => ['name', '%%', 'NOT LIKE', false, "`name` NOT LIKE '%%'"],
        ];
    }

    /**
     * @param string $column
     * @param string $operator
     * @param string $expected
     */
    #[DataProvider('nullOperatorProvider')]
    public function testRenderNullOperators(string $column, string $operator, string $expected): void
    {
        $c = new Criteria($column, '', $operator);

        $this->assertSame($expected, $c->render($this->db));
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: string}>
     */
    public static function nullOperatorProvider(): array
    {
        return [
            'IS NULL'           => ['email', 'IS NULL', '`email` IS NULL'],
            'IS NOT NULL'       => ['email', 'IS NOT NULL', '`email` IS NOT NULL'],
            'is null lowercase' => ['email', 'is null', '`email` IS NULL'],
            'is not null lower' => ['email', 'is not null', '`email` IS NOT NULL'],
        ];
    }

    /**
     * @param string $column
     * @param array  $values
     * @param string $operator
     * @param string $expected
     */
    #[DataProvider('inArrayProvider')]
    public function testRenderInWithArrays(string $column, array $values, string $operator, string $expected): void
    {
        $c = new Criteria($column, $values, $operator);

        $this->assertSame($expected, $c->render($this->db));
    }

    /**
     * @return array<string, array{0: string, 1: array, 2: string, 3: string}>
     */
    public static function inArrayProvider(): array
    {
        return [
            'int list'       => ['uid', [1, 2, 3], 'IN', '`uid` IN (1,2,3)'],
            'string list'    => ['name', ['a', 'b'], 'IN', "`name` IN ('a','b')"],
            'mixed list'     => ['val', [1, 'x', 3], 'IN', "`val` IN (1,'x',3)"],
            'single int'     => ['uid', [42], 'IN', '`uid` IN (42)'],
            'empty list'     => ['uid', [], 'IN', '`uid` IN ()'],
            'not in ints'    => ['uid', [4, 5], 'NOT IN', '`uid` NOT IN (4,5)'],
            'not in strings' => ['s', ['a', 'b'], 'NOT IN', "`s` NOT IN ('a','b')"],
            'numeric strings'=> ['uid', ['1', '2'], 'IN', '`uid` IN (1,2)'],
            'negative ints'  => ['uid', [-1, -2], 'IN', '`uid` IN (-1,-2)'],
        ];
    }

    /**
     * @param string $column
     * @param bool   $expectBacktick
     */
    #[DataProvider('columnBacktickProvider')]
    public function testColumnBacktickBehavior(string $column, bool $expectBacktick): void
    {
        $c = new Criteria($column, 5);
        $result = $c->render($this->db);

        if ($expectBacktick) {
            $this->assertStringContainsString('`' . $column . '`', $result);
        } else {
            $this->assertStringNotContainsString('`' . $column . '`', $result);
            $this->assertStringContainsString($column, $result);
        }
    }

    /**
     * @return array<string, array{0: string, 1: bool}>
     */
    public static function columnBacktickProvider(): array
    {
        return [
            'simple column'     => ['uid', true],
            'column with dot'   => ['u.uid', false],
            'column with paren' => ['COUNT(*)', false],
            'function call'     => ['MAX(score)', false],
        ];
    }

    /**
     * @param string $input
     * @param string $expected
     */
    #[DataProvider('orderValidationProvider')]
    public function testOrderValidation(string $input, string $expected): void
    {
        $el = new CriteriaElement();
        $el->setOrder($input);

        $this->assertSame($expected, $el->getOrder());
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function orderValidationProvider(): array
    {
        return [
            'DESC uppercase'    => ['DESC', 'DESC'],
            'DESC lowercase'    => ['desc', 'DESC'],
            'DESC mixed'        => ['DeSc', 'DESC'],
            'ASC uppercase'     => ['ASC', 'ASC'],
            'ASC lowercase'     => ['asc', 'ASC'],
            'random string'     => ['RANDOM', 'ASC'],
            'empty string'      => ['', 'ASC'],
            'numeric string'    => ['123', 'ASC'],
            'partial desc'      => ['DES', 'ASC'],
            'desc with space'   => [' DESC', 'ASC'], // leading space -> no match
        ];
    }

    // =========================================================================
    // Criteria — Special characters in values
    // =========================================================================

    public function testRenderValueWithSingleQuote(): void
    {
        $c = new Criteria('name', "O'Brien");

        // The stub DB quote() uses addslashes, so single quote is escaped
        $this->assertSame("`name` = 'O\\'Brien'", $c->render($this->db));
    }

    public function testRenderValueWithBackslash(): void
    {
        $c = new Criteria('path', 'C:\\temp');

        $this->assertSame("`path` = 'C:\\\\temp'", $c->render($this->db));
    }

    public function testRenderValueWithDoubleQuote(): void
    {
        $c = new Criteria('title', 'He said "hello"');

        // addslashes escapes double quotes
        $this->assertSame('`title` = \'He said \\"hello\\"\'', $c->render($this->db));
    }

    // =========================================================================
    // Criteria — Integer detection edge cases
    // =========================================================================

    public function testRenderIntegerValue(): void
    {
        $c = new Criteria('uid', 42);

        $result = $c->render($this->db);
        // Integer values should not be quoted
        $this->assertSame('`uid` = 42', $result);
    }

    public function testRenderFloatValueIsQuoted(): void
    {
        $c = new Criteria('price', 19.99);

        $result = $c->render($this->db);
        // Float values are NOT integers, so they get quoted as strings
        $this->assertStringContainsString("'19.99'", $result);
    }

    public function testRenderLeadingZeroStringIsQuoted(): void
    {
        $c = new Criteria('zipcode', '01234');

        // '01234' matches /^-?\d+$/ so it gets cast to int
        $this->assertSame('`zipcode` = 1234', $c->render($this->db));
    }

    // =========================================================================
    // Criteria — Edge cases for IN with legacy strings
    // =========================================================================

    public function testRenderInWithDoubleQuotedLegacyList(): void
    {
        $c = new Criteria('name', '("alice","bob")', 'IN');

        $this->assertSame('`name` IN ("alice","bob")', $c->render($this->db));
    }

    public function testRenderInWithSpacedNumericList(): void
    {
        $c = new Criteria('uid', '( 1 , 2 , 3 )', 'IN');

        $this->assertSame('`uid` IN ( 1 , 2 , 3 )', $c->render($this->db));
    }

    // =========================================================================
    // CriteriaCompo — Complex real-world scenarios
    // =========================================================================

    public function testComplexQueryBuild(): void
    {
        // Simulate: WHERE (uid > 0 AND status = 'active') AND (name LIKE '%john%' OR email LIKE '%john%')
        $authCompo = new CriteriaCompo(new Criteria('uid', 0, '>'));
        $authCompo->add(new Criteria('status', 'active'));

        $searchCompo = new CriteriaCompo(new Criteria('name', '%john%', 'LIKE'));
        $searchCompo->add(new Criteria('email', '%john%', 'LIKE'), 'OR');

        $main = new CriteriaCompo($authCompo);
        $main->add($searchCompo);

        $expected = "(((`uid` > 0 AND `status` = 'active')) AND ((`name` LIKE '%john%' OR `email` LIKE '%john%')))";
        $result = $main->render($this->db);

        // Verify structure: wrapped in parens, AND between the two sub-compos
        $this->assertStringStartsWith('(', $result);
        $this->assertStringEndsWith(')', $result);
        $this->assertStringContainsString('AND', $result);
        $this->assertStringContainsString('`uid` > 0', $result);
        $this->assertStringContainsString("`status` = 'active'", $result);
        $this->assertStringContainsString("`name` LIKE '%john%'", $result);
        $this->assertStringContainsString("`email` LIKE '%john%'", $result);
    }

    public function testCriteriaCompoWithSortLimitStart(): void
    {
        $cc = new CriteriaCompo(new Criteria('uid', 0, '>'));
        $cc->setSort('uid');
        $cc->setOrder('DESC');
        $cc->setLimit(10);
        $cc->setStart(20);
        $cc->setGroupBy('category');

        $this->assertSame('uid', $cc->getSort());
        $this->assertSame('DESC', $cc->getOrder());
        $this->assertSame(10, $cc->getLimit());
        $this->assertSame(20, $cc->getStart());
        $this->assertSame(' GROUP BY category', $cc->getGroupby());
        $this->assertSame('(`uid` > 0)', $cc->render($this->db));
    }

    // =========================================================================
    // Criteria — Prefix and function combined
    // =========================================================================

    public function testRenderPrefixFunctionAndOperatorCombined(): void
    {
        $c = new Criteria('name', 'john', 'LIKE', 'u', 'LOWER(%s)');

        $this->assertSame("LOWER(u.`name`) LIKE 'john'", $c->render($this->db));
    }

    public function testRenderPrefixWithInOperator(): void
    {
        $c = new Criteria('uid', [1, 2, 3], 'IN', 'u');

        $this->assertSame('u.`uid` IN (1,2,3)', $c->render($this->db));
    }

    public function testRenderPrefixWithIsNull(): void
    {
        $c = new Criteria('email', '', 'IS NULL', 'u');

        $this->assertSame('u.`email` IS NULL', $c->render($this->db));
    }

    public function testRenderFunctionWithIsNull(): void
    {
        $c = new Criteria('email', '', 'IS NULL', '', 'TRIM(%s)');

        $this->assertSame('TRIM(`email`) IS NULL', $c->render($this->db));
    }

    // =========================================================================
    // CriteriaCompo — Multiple add() with same condition
    // =========================================================================

    public function testMultipleOrConditions(): void
    {
        $cc = new CriteriaCompo(new Criteria('uid', 1));
        $cc->add(new Criteria('uid', 2), 'OR');
        $cc->add(new Criteria('uid', 3), 'OR');
        $cc->add(new Criteria('uid', 4), 'OR');

        $this->assertSame('(`uid` = 1 OR `uid` = 2 OR `uid` = 3 OR `uid` = 4)', $cc->render($this->db));
    }

    public function testMultipleAndConditions(): void
    {
        $cc = new CriteriaCompo(new Criteria('a', 1));
        $cc->add(new Criteria('b', 2));
        $cc->add(new Criteria('c', 3));

        $this->assertSame('(`a` = 1 AND `b` = 2 AND `c` = 3)', $cc->render($this->db));
    }

    // =========================================================================
    // CriteriaCompo — renderWhere with various states
    // =========================================================================

    public function testRenderWhereWithSingleCriterion(): void
    {
        $cc = new CriteriaCompo(new Criteria('uid', 5));

        $this->assertSame('WHERE (`uid` = 5)', $cc->renderWhere($this->db));
    }

    public function testRenderWhereAllChildrenEmptyReturnsEmpty(): void
    {
        $cc = new CriteriaCompo(new Criteria('name', ''));
        $cc->add(new Criteria('title', ''));

        $this->assertSame('', $cc->renderWhere($this->db));
    }

    // =========================================================================
    // Criteria — LIKE with backslash in core that also has wildcards
    // =========================================================================

    public function testRenderLikeBackslashAndWildcardsInCore(): void
    {
        // Pattern: %path\to\file% — backslash should be escaped, inner wildcards not
        $c = new Criteria('p', '%C:\\path%', 'LIKE');

        // Backslashes in core are escaped: C:\\path -> C:\\\\path (after addslashes in quote)
        $result = $c->render($this->db);
        $this->assertStringContainsString('LIKE', $result);
        $this->assertStringStartsWith('`p` LIKE ', $result);
    }

    // =========================================================================
    // Criteria — Value types
    // =========================================================================

    public function testRenderBoolTrueValueIsQuotedAsString(): void
    {
        // bool true: is_int(true) = false, is_string(true) = false
        // so it falls to quote((string)true) = quote("1") = '1'
        $c = new Criteria('active', true);

        $result = $c->render($this->db);
        $this->assertSame("`active` = '1'", $c->render($this->db));
    }

    public function testRenderBoolFalseValueEmpty(): void
    {
        // bool false cast to string is "" (empty)
        $c = new Criteria('active', false);

        // Empty string without allowEmptyValue => ''
        $this->assertSame('', $c->render($this->db));
    }

    public function testRenderBoolFalseWithAllowEmpty(): void
    {
        $c = new Criteria('active', false, '=', '', '', true);

        // allowEmptyValue=true, false cast to string is '', then checked as value
        $result = $c->render($this->db);
        $this->assertNotEmpty($result);
    }

    // =========================================================================
    // Criteria — IN with negative numeric values in array
    // =========================================================================

    public function testRenderInWithNegativeIntegers(): void
    {
        $c = new Criteria('offset', [-10, -20, 5], 'IN');

        $this->assertSame('`offset` IN (-10,-20,5)', $c->render($this->db));
    }

    public function testRenderInWithNegativeNumericStrings(): void
    {
        $c = new Criteria('offset', ['-10', '-20'], 'IN');

        $this->assertSame('`offset` IN (-10,-20)', $c->render($this->db));
    }

    // =========================================================================
    // CriteriaCompo — Uses global DB when no DB passed
    // =========================================================================

    public function testCriteriaCompoRenderUsesGlobalDbFallback(): void
    {
        $cc = new CriteriaCompo(new Criteria('uid', 5));

        // render() without argument should use child's render() which falls back to global
        $this->assertSame('(`uid` = 5)', $cc->render());
    }

    public function testCriteriaCompoRenderWhereUsesGlobalDbFallback(): void
    {
        $cc = new CriteriaCompo(new Criteria('uid', 5));

        $this->assertSame('WHERE (`uid` = 5)', $cc->renderWhere());
    }
}
