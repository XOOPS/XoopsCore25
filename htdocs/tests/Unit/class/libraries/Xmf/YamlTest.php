<?php

declare(strict_types=1);

namespace Xmf\Test;

use PHPUnit\Framework\TestCase;

use Xmf\Yaml;

class YamlTest extends TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown(): void
    {
    }

    public function testDumpAndLoad()
    {
        $inputArray = ['one' => 1, 'two' => [1, 2], 'three' => ''];

        $string = Yaml::dump($inputArray);
        $this->assertNotEmpty($string);
        $this->assertIsString($string);

        $outputArray = Yaml::load((string)$string);
        $this->assertIsArray($outputArray);
        $this->assertSame($inputArray, $outputArray);
    }

    public function testSaveAndRead()
    {
        $tmpfname   = tempnam(sys_get_temp_dir(), 'TEST');
        $inputArray = ['one' => 1, 'two' => [1, 2], 'three' => ''];

        $byteCount = Yaml::save($inputArray, $tmpfname);
        $this->assertNotSame(false, $byteCount);
        $this->assertGreaterThan(0, $byteCount);

        $outputArray = Yaml::read($tmpfname);
        $this->assertIsArray($outputArray);
        $this->assertSame($inputArray, $outputArray);

        unlink($tmpfname);
    }

    public function testDumpAndLoadWrapped()
    {
        $inputArray = ['one' => 1, 'two' => [1, 2], 'three' => ''];

        $string = Yaml::dumpWrapped($inputArray);
        $this->assertNotEmpty($string);
        $this->assertIsString($string);

        $outputArray = Yaml::loadWrapped((string)$string);
        $this->assertIsArray($outputArray);
        $this->assertSame($inputArray, $outputArray);
    }

    public function testDumpAndLoadWrappedStress()
    {
        $inputArray = ['start' => '---', 'end' => '...', 'misc' => 'stuff'];

        $string = Yaml::dumpWrapped($inputArray);
        $this->assertNotEmpty($string);
        $this->assertIsString($string);

        $outputArray = Yaml::loadWrapped((string)$string);
        $this->assertIsArray($outputArray);
        $this->assertSame($inputArray, $outputArray);
    }

    public function testDumpAndLoadWrappedStress2()
    {
        $inputArray = ['start' => '---', 'end' => '...', 'misc' => 'stuff'];

        $string = Yaml::dump($inputArray);
        $this->assertNotEmpty($string);
        $this->assertIsString($string);

        $outputArray = Yaml::loadWrapped((string)$string);
        $this->assertIsArray($outputArray);
        $this->assertSame($inputArray, $outputArray);
    }

    public function testSaveAndReadWrapped()
    {
        $tmpfname   = tempnam(sys_get_temp_dir(), 'TEST');
        $inputArray = ['one' => 1, 'two' => [1, 2], 'three' => ''];

        $byteCount = Yaml::saveWrapped($inputArray, $tmpfname);
        $this->assertNotSame(false, $byteCount);
        $this->assertGreaterThan(0, $byteCount);

        $outputArray = Yaml::readWrapped($tmpfname);
        $this->assertIsArray($outputArray);
        $this->assertSame($inputArray, $outputArray);

        unlink($tmpfname);
    }
}
