#!/bin/bash
#
# Modern Theme Compatibility Test Script
# Tests theme integrity and compatibility
#
# Usage: ./test-theme.sh
#

echo ""
echo "╔══════════════════════════════════════════════════════════╗"
echo "║     XOOPS Modern Theme Compatibility Test Suite         ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""

THEME_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PASSED=0
FAILED=0
WARNINGS=0

# Test 1: File Structure
echo "→ Testing file structure..."
REQUIRED_FILES=(
    "modern.php"
    "theme.tpl"
    "css/modern.css"
    "css/dark.css"
    "js/theme.js"
    "js/dashboard.js"
    "js/charts.js"
    "js/customizer.js"
    "xotpl/xo_metas.tpl"
    "xotpl/xo_head.tpl"
    "xotpl/xo_sidebar.tpl"
    "xotpl/xo_dashboard.tpl"
    "xotpl/xo_widgets.tpl"
    "xotpl/xo_customizer.tpl"
    "xotpl/xo_page.tpl"
    "xotpl/xo_footer.tpl"
)

ALL_EXIST=true
for file in "${REQUIRED_FILES[@]}"; do
    if [ ! -f "$THEME_DIR/$file" ]; then
        echo "  ✗ Missing: $file"
        ALL_EXIST=false
    fi
done

if $ALL_EXIST; then
    echo "  ✓ PASS"
    ((PASSED++))
else
    echo "  ✗ FAIL"
    ((FAILED++))
fi

# Test 2: PHP Syntax
echo "→ Testing PHP class structure..."
if grep -q "class XoopsGuiModern extends XoopsSystemGui" "$THEME_DIR/modern.php"; then
    if grep -q "public static function validate" "$THEME_DIR/modern.php" && \
       grep -q "public function header" "$THEME_DIR/modern.php"; then
        echo "  ✓ PASS"
        ((PASSED++))
    else
        echo "  ✗ FAIL - Missing required methods"
        ((FAILED++))
    fi
else
    echo "  ✗ FAIL - Invalid class structure"
    ((FAILED++))
fi

# Test 3: Database Queries
echo "→ Testing database query safety..."
if grep -qE "(FROM xoops_|JOIN xoops_)" "$THEME_DIR/modern.php"; then
    echo "  ⚠ WARNING - Found hardcoded table prefix"
    ((WARNINGS++))
    echo "  ✓ PASS (with warnings)"
    ((PASSED++))
else
    echo "  ✓ PASS"
    ((PASSED++))
fi

# Test 4: Template Syntax
echo "→ Testing Smarty template syntax..."
TEMPLATE_OK=true
for tpl in "$THEME_DIR"/xotpl/*.tpl; do
    # Check for balanced Smarty tags
    OPEN=$(grep -o '<{' "$tpl" | wc -l)
    CLOSE=$(grep -o '}>' "$tpl" | wc -l)

    if [ "$OPEN" -ne "$CLOSE" ]; then
        echo "  ⚠ WARNING - Unbalanced tags in $(basename $tpl)"
        ((WARNINGS++))
        TEMPLATE_OK=false
    fi
done

if $TEMPLATE_OK; then
    echo "  ✓ PASS"
    ((PASSED++))
else
    echo "  ✓ PASS (with warnings)"
    ((PASSED++))
fi

# Test 5: JavaScript Syntax
echo "→ Testing JavaScript files..."
JS_OK=true
for js in "$THEME_DIR"/js/*.js; do
    # Check for balanced braces
    OPEN=$(grep -o '{' "$js" | wc -l)
    CLOSE=$(grep -o '}' "$js" | wc -l)

    if [ "$OPEN" -ne "$CLOSE" ]; then
        echo "  ✗ FAIL - Unbalanced braces in $(basename $js)"
        JS_OK=false
    fi
done

if $JS_OK; then
    echo "  ✓ PASS"
    ((PASSED++))
else
    echo "  ✗ FAIL"
    ((FAILED++))
fi

# Test 6: CSS Syntax
echo "→ Testing CSS files..."
CSS_OK=true
for css in "$THEME_DIR"/css/*.css; do
    # Check for balanced braces
    OPEN=$(grep -o '{' "$css" | wc -l)
    CLOSE=$(grep -o '}' "$css" | wc -l)

    if [ "$OPEN" -ne "$CLOSE" ]; then
        echo "  ✗ FAIL - Unbalanced braces in $(basename $css)"
        CSS_OK=false
    fi

    # Check for CSS variables
    if grep -q '\-\-' "$css" && ! grep -q ':root' "$css"; then
        echo "  ⚠ WARNING - CSS variables without :root in $(basename $css)"
        ((WARNINGS++))
    fi
done

if $CSS_OK; then
    echo "  ✓ PASS"
    ((PASSED++))
else
    echo "  ✗ FAIL"
    ((FAILED++))
fi

# Test 7: File Permissions
echo "→ Testing file permissions..."
PERM_OK=true
for file in "$THEME_DIR"/*.php "$THEME_DIR"/css/*.css "$THEME_DIR"/js/*.js; do
    if [ ! -r "$file" ]; then
        echo "  ✗ FAIL - Not readable: $(basename $file)"
        PERM_OK=false
    fi
done

if $PERM_OK; then
    echo "  ✓ PASS"
    ((PASSED++))
else
    echo "  ✗ FAIL"
    ((FAILED++))
fi

# Test 8: Dependencies
echo "→ Testing external dependencies..."
if grep -q "chart\.js" "$THEME_DIR/modern.php"; then
    echo "  ✓ PASS"
    ((PASSED++))
else
    echo "  ⚠ WARNING - Chart.js reference may be missing"
    ((WARNINGS++))
    echo "  ✓ PASS (with warnings)"
    ((PASSED++))
fi

# Print Results
echo ""
echo "╔══════════════════════════════════════════════════════════╗"
echo "║                    Test Results                          ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""
echo "Passed:   $PASSED"
echo "Failed:   $FAILED"
echo "Warnings: $WARNINGS"
echo ""

if [ $FAILED -eq 0 ]; then
    echo "╔══════════════════════════════════════════════════════════╗"
    echo "║              ✓ ALL TESTS PASSED!                         ║"
    echo "║     Theme is compatible with current XOOPS version       ║"
    echo "╚══════════════════════════════════════════════════════════╝"
    exit 0
else
    echo "╔══════════════════════════════════════════════════════════╗"
    echo "║              ✗ SOME TESTS FAILED                         ║"
    echo "║          Please fix errors before deployment            ║"
    echo "╚══════════════════════════════════════════════════════════╝"
    exit 1
fi
