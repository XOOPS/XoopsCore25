<?php

declare(strict_types=1);

namespace xoopsforms;

use PHPUnit\Framework\Attributes\DataProvider;
use XoopsFormFile;
use XoopsFormElement;

xoops_load('XoopsFormElement');
xoops_load('XoopsFormFile');
xoops_load('XoopsFormRendererInterface');
xoops_load('XoopsFormRendererLegacy');
xoops_load('XoopsFormRenderer');

/**
 * Unit tests for XoopsFormFile.
 */
class XoopsFormFileTest extends \PHPUnit\Framework\TestCase
{
    // =========================================================================
    // Constructor
    // =========================================================================

    public function testConstructorCreatesInstance(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', 2097152);

        $this->assertInstanceOf(XoopsFormFile::class, $element);
        $this->assertInstanceOf(XoopsFormElement::class, $element);
    }

    public function testConstructorSetsCaption(): void
    {
        $element = new XoopsFormFile('Upload File', 'upload', 2097152);

        $this->assertSame('Upload File', $element->getCaption());
    }

    public function testConstructorSetsName(): void
    {
        $element = new XoopsFormFile('Upload', 'my_upload', 2097152);

        $this->assertSame('my_upload', $element->getName(false));
    }

    public function testConstructorSetsMaxFileSize(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', 2097152);

        $this->assertSame(2097152, $element->getMaxFileSize());
    }

    // =========================================================================
    // maxFileSize cast to int
    // =========================================================================

    public function testMaxFileSizeCastToInt(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', '1048576');

        $this->assertSame(1048576, $element->getMaxFileSize());
        $this->assertIsInt($element->getMaxFileSize());
    }

    public function testMaxFileSizeCastFromFloat(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', 1048576.5);

        $this->assertSame(1048576, $element->getMaxFileSize());
        $this->assertIsInt($element->getMaxFileSize());
    }

    public function testMaxFileSizeZero(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', 0);

        $this->assertSame(0, $element->getMaxFileSize());
    }

    public function testMaxFileSizeNegativeCastToInt(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', -1);

        $this->assertSame(-1, $element->getMaxFileSize());
    }

    public function testMaxFileSizeLargeValue(): void
    {
        $size = 104857600; // 100 MB
        $element = new XoopsFormFile('Upload', 'upload', $size);

        $this->assertSame($size, $element->getMaxFileSize());
    }

    // =========================================================================
    // getMaxFileSize
    // =========================================================================

    public function testGetMaxFileSize(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', 5242880);

        $this->assertSame(5242880, $element->getMaxFileSize());
    }

    public function testGetMaxFileSizeSmallFile(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', 1024);

        $this->assertSame(1024, $element->getMaxFileSize());
    }

    // =========================================================================
    // render
    // =========================================================================

    public function testRenderReturnsString(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', 2097152);

        $result = $element->render();

        $this->assertIsString($result);
    }

    public function testRenderNotEmpty(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', 2097152);

        $result = $element->render();

        $this->assertNotEmpty($result);
    }

    // =========================================================================
    // Edge cases
    // =========================================================================

    public function testIsNotHiddenByDefault(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', 2097152);

        $this->assertFalse($element->isHidden());
    }

    public function testIsNotRequiredByDefault(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', 2097152);

        $this->assertFalse($element->isRequired());
    }

    public function testIsNotContainer(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', 2097152);

        $this->assertFalse($element->isContainer());
    }

    public function testCaptionTrimmed(): void
    {
        $element = new XoopsFormFile('  Upload  ', 'upload', 2097152);

        $this->assertSame('Upload', $element->getCaption());
    }

    public function testNameTrimmed(): void
    {
        $element = new XoopsFormFile('Upload', '  upload  ', 2097152);

        $this->assertSame('upload', $element->getName(false));
    }

    /**
     * @param string $caption
     * @param string $name
     * @param int    $maxFileSize
     */
    #[DataProvider('fileFieldDataProvider')]
    public function testConstructorDataDriven(
        string $caption,
        string $name,
        int $maxFileSize
    ): void {
        $element = new XoopsFormFile($caption, $name, $maxFileSize);

        $this->assertSame($caption, $element->getCaption());
        $this->assertSame($name, $element->getName(false));
        $this->assertSame($maxFileSize, $element->getMaxFileSize());
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: int}>
     */
    public static function fileFieldDataProvider(): array
    {
        return [
            'small upload'    => ['Photo', 'photo', 1048576],
            'large upload'    => ['Video', 'video', 104857600],
            'avatar upload'   => ['Avatar', 'avatar', 524288],
            'document upload' => ['Document', 'doc', 10485760],
            'zero size'       => ['File', 'file', 0],
        ];
    }

    public function testMaxFileSizeStringNumericCastToInt(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', '500000');

        $this->assertSame(500000, $element->getMaxFileSize());
    }

    public function testInheritedBaseClassMethods(): void
    {
        $element = new XoopsFormFile('Upload', 'upload', 2097152);

        // Test that base class methods are available and functional
        $element->setClass('form-control');
        $this->assertSame('form-control', $element->getClass());

        $element->setDescription('Upload a file');
        $this->assertSame('Upload a file', $element->getDescription());

        $element->setExtra('accept="image/*"');
        $this->assertStringContainsString('accept="image/*"', $element->getExtra());
    }
}
