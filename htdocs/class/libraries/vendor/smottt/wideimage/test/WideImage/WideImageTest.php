<?php
    /**
    This file is part of WideImage.

    WideImage is free software; you can redistribute it and/or modify
    it under the terms of the GNU Lesser General Public License as published by
    the Free Software Foundation; either version 2.1 of the License, or
    (at your option) any later version.

    WideImage is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public License
    along with WideImage; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

    * @package Tests
  **/

namespace Test\WideImage;

include_once __DIR__ . '/Mapper/FOO.php';
include_once __DIR__ . '/Mapper/FOO2.php';

use WideImage\Exception\InvalidImageSourceException;
use WideImage\WideImage;
use WideImage\PaletteImage;
use WideImage\TrueColorImage;
use WideImage\Mapper\FOO;
use WideImage\Mapper\FOO2;
use Test\WideImage_TestCase;

/**
 * @package Tests
 */
class WideImageTest extends WideImage_TestCase
{
    protected $_FILES;

    /**
     * @before
     */
    public function doSetUp()
    {
        $this->_FILES = $_FILES;
        $_FILES = [];
    }

    /**
     * @after
     */
    public function doTearDown()
    {
        $_FILES = $this->_FILES;

        if (PHP_OS == 'WINNT') {
            chdir(IMG_PATH . "temp");

            foreach (new \DirectoryIterator(IMG_PATH . "temp") as $file) {
                if (!$file->isDot()) {
                    if ($file->isDir()) {
                        exec("rd /S /Q {$file->getFilename()}\n");
                    } else {
                        unlink($file->getFilename());
                    }
                }
            }
        } else {
            exec("rm -rf " . IMG_PATH . 'temp/*');
        }

        WideImage::unregisterCustomMapper(FOO::class, 'image/foo');
        WideImage::unregisterCustomMapper(FOO2::class, 'image/foo2');
    }

    public function testLoadFromFile()
    {
        $img = WideImage::load(IMG_PATH . '100x100-red-transparent.gif');
        $this->assertTrue($img instanceof PaletteImage);
        $this->assertValidImage($img);
        $this->assertFalse($img->isTrueColor());
        $this->assertEquals(100, $img->getWidth());
        $this->assertEquals(100, $img->getHeight());

        $img = WideImage::load(IMG_PATH . '100x100-rainbow.png');
        $this->assertTrue($img instanceof TrueColorImage);
        $this->assertValidImage($img);
        $this->assertTrue($img->isTrueColor());
        $this->assertEquals(100, $img->getWidth());
        $this->assertEquals(100, $img->getHeight());
    }

    public function testLoadFromString()
    {
        $img = WideImage::load(file_get_contents(IMG_PATH . '100x100-rainbow.png'));
        $this->assertTrue($img instanceof TrueColorImage);
        $this->assertValidImage($img);
        $this->assertTrue($img->isTrueColor());
        $this->assertEquals(100, $img->getWidth());
        $this->assertEquals(100, $img->getHeight());
    }

    public function testLoadFromHandle()
    {
        $handle = imagecreatefrompng(IMG_PATH . '100x100-rainbow.png');
        $img = WideImage::loadFromHandle($handle);
        $this->assertValidImage($img);
        $this->assertTrue($img->isTrueColor());
        $this->assertSame($handle, $img->getHandle());
        $this->assertEquals(100, $img->getWidth());
        $this->assertEquals(100, $img->getHeight());
        unset($img);

        // TODO: fix this for PHP 8
        if (!$handle instanceof \GdImage) {
            $this->assertFalse(WideImage::isValidImageHandle($handle));
        }
    }

    public function testLoadFromUpload()
    {
        copy(IMG_PATH . '100x100-rainbow.png', IMG_PATH . 'temp' . DIRECTORY_SEPARATOR . 'upltmpimg');
        $_FILES = [
            'testupl' => [
                'name' => '100x100-rainbow.png',
                'type' => 'image/png',
                'size' => strlen(file_get_contents(IMG_PATH . '100x100-rainbow.png')),
                'tmp_name' => IMG_PATH . 'temp' . DIRECTORY_SEPARATOR . 'upltmpimg',
                'error' => false,
            ]
        ];

        $img = WideImage::loadFromUpload('testupl');
        $this->assertValidImage($img);
    }

    public function testLoadFromMultipleUploads()
    {
        copy(IMG_PATH . '100x100-rainbow.png', IMG_PATH . 'temp' . DIRECTORY_SEPARATOR . 'upltmpimg1');
        copy(IMG_PATH . 'splat.tga', IMG_PATH . 'temp' . DIRECTORY_SEPARATOR . 'upltmpimg2');
        $_FILES = [
            'testupl' => [
                'name' => ['100x100-rainbow.png', 'splat.tga'],
                'type' => ['image/png', 'image/tga'],
                'size' => [
                        strlen(file_get_contents(IMG_PATH . '100x100-rainbow.png')),
                        strlen(file_get_contents(IMG_PATH . 'splat.tga'))
                    ],
                'tmp_name' => [
                        IMG_PATH . 'temp' . DIRECTORY_SEPARATOR . 'upltmpimg1',
                        IMG_PATH . 'temp' . DIRECTORY_SEPARATOR . 'upltmpimg2'
                    ],
                'error' => [false, false],
            ]
        ];

        $images = WideImage::loadFromUpload('testupl');
        if (method_exists($this, 'assertIsArray')) {
            // PHPUnit ≥ 8.0
            $this->assertIsArray($images);
        } else {
            // PHPUnit < 8.0
            $this->assertInternalType("array", $images);
        }
        $this->assertValidImage($images[0]);
        $this->assertValidImage($images[1]);

        $img = WideImage::loadFromUpload('testupl', 1);
        $this->assertValidImage($img);
    }

    public function testLoadMagicalFromHandle()
    {
        $img = WideImage::load(imagecreatefrompng(IMG_PATH . '100x100-rainbow.png'));
        $this->assertValidImage($img);
    }


    public function testLoadMagicalFromBinaryString()
    {
        $img = WideImage::load(file_get_contents(IMG_PATH . '100x100-rainbow.png'));
        $this->assertValidImage($img);
    }

    public function testLoadMagicalFromFile()
    {
        $img = WideImage::load(IMG_PATH . '100x100-rainbow.png');
        $this->assertValidImage($img);
        copy(IMG_PATH . '100x100-rainbow.png', IMG_PATH . 'temp' . DIRECTORY_SEPARATOR . 'upltmpimg');
        $_FILES = [
            'testupl' => [
                'name' => 'fgnl.bmp',
                'type' => 'image/bmp',
                'size' => strlen(file_get_contents(IMG_PATH . 'fgnl.bmp')),
                'tmp_name' => IMG_PATH . 'temp' . DIRECTORY_SEPARATOR . 'upltmpimg',
                'error' => false,
            ]
        ];
        $img = WideImage::load('testupl');
        $this->assertValidImage($img);
    }

    public function testLoadFromStringWithCustomMapper()
    {
        $img = WideImage::loadFromString(file_get_contents(IMG_PATH . 'splat.tga'));
        $this->assertValidImage($img);
    }

    public function testLoadFromFileWithInvalidExtension()
    {
        $img = WideImage::load(IMG_PATH . 'actually-a-png.jpg');
        $this->assertValidImage($img);
    }

    public function testLoadFromFileWithInvalidExtensionWithCustomMapper()
    {
        if (PHP_OS == 'WINNT') {
            $this->markTestSkipped("For some reason, this test kills PHP my 32-bit Vista + PHP 5.3.1.");
        }

        $img = WideImage::loadFromFile(IMG_PATH . 'fgnl-bmp.jpg');
        $this->assertValidImage($img);
    }

    public function testLoadFromStringEmpty()
    {
        $this->expectException(InvalidImageSourceException::class);
        WideImage::loadFromString('');
    }

    public function testLoadBMPMagicalFromUpload()
    {
        copy(IMG_PATH . 'fgnl.bmp', IMG_PATH . 'temp' . DIRECTORY_SEPARATOR . 'upltmpimg');
        $_FILES = [
            'testupl' => [
                'name' => 'fgnl.bmp',
                'type' => 'image/bmp',
                'size' => strlen(file_get_contents(IMG_PATH . 'fgnl.bmp')),
                'tmp_name' => IMG_PATH . 'temp' . DIRECTORY_SEPARATOR . 'upltmpimg',
                'error' => false,
            ]
        ];
        $img = WideImage::load('testupl');
        $this->assertValidImage($img);
    }

    public function testMapperLoad()
    {
        FOO::$handle = imagecreate(10, 10);
        $filename = IMG_PATH . 'image.foo';
        WideImage::registerCustomMapper(FOO::class, 'image/foo', 'foo');
        $img = WideImage::load($filename);
        $this->assertEquals(FOO::$calls['load'], [$filename]);
        imagedestroy(FOO::$handle);
        unset($img);
    }

    public function testLoadFromFileFallbackToLoadFromString()
    {
        FOO::$handle = imagecreate(10, 10);
        $filename = IMG_PATH . 'image-actually-foo.foo2';
        WideImage::registerCustomMapper(FOO::class, 'image/foo', 'foo');
        WideImage::registerCustomMapper(FOO2::class, 'image/foo2', 'foo2');
        $img = WideImage::load($filename);
        $this->assertEquals(FOO2::$calls['load'], [$filename]);
        $this->assertEquals(FOO::$calls['loadFromString'], [file_get_contents($filename)]);
        imagedestroy(FOO::$handle);
        unset($img);
    }

    public function testMapperSaveToFile()
    {
        $img = WideImage::load(IMG_PATH . 'fgnl.jpg');
        $img->saveToFile('test.foo', '123', 789);
        $this->assertEquals(FOO::$calls['save'], [$img->getHandle(), 'test.foo', '123', 789]);
    }

    public function testMapperAsString()
    {
        $img = WideImage::load(IMG_PATH . 'fgnl.jpg');
        $str = $img->asString('foo', '123', 789);
        $this->assertEquals(FOO::$calls['save'], [$img->getHandle(), null, '123', 789]);
        $this->assertEquals('out', $str);
    }

    public function testInvalidImageFile()
    {
        $this->expectException(InvalidImageSourceException::class);
        WideImage::loadFromFile(IMG_PATH . 'fakeimage.png');
    }

    public function testEmptyString()
    {
        $this->expectException(InvalidImageSourceException::class);
        WideImage::load('');
    }

    public function testInvalidImageStringData()
    {
        $this->expectException(InvalidImageSourceException::class);
        WideImage::loadFromString('asdf');
    }

    public function testInvalidImageHandle()
    {
        $this->expectException(InvalidImageSourceException::class);
        WideImage::loadFromHandle(0);
    }

    public function testInvalidImageUploadField()
    {
        $this->expectException(InvalidImageSourceException::class);
        WideImage::loadFromUpload('xyz');
    }
}
