<?php

/*
 * This file is a part of dflydev/apache-mime-types.
 *
 * (c) Dragonfly Development Inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dflydev\ApacheMimeTypes;

/**
 * Flat Repository Test
 *
 * @author Beau Simensen <beau@dflydev.com>
 */
abstract class AbstractRepositoryTest extends \PHPUnit_Framework_TestCase
{
    abstract protected function createDefaultRepository();
    abstract protected function createRepository();

    public function testDefaultCss()
    {
        $repository = $this->createDefaultRepository();

        if (null === $repository) {
            return;
        }

        $this->assertEquals('text/css', $repository->findType('css'));

        $extensions = $repository->findExtensions('text/css');
        $this->assertCount(1, $extensions);
        $this->assertEquals('css', $extensions[0]);
    }

    public function testDefaultHtml()
    {
        $repository = $this->createDefaultRepository();

        if (null === $repository) {
            return;
        }

        $this->assertEquals('text/html', $repository->findType('html'));
        $this->assertEquals('text/html', $repository->findType('htm'));

        $extensions = $repository->findExtensions('text/html');
        $this->assertCount(2, $extensions);
        $this->assertEquals('html', $extensions[0]);
        $this->assertEquals('htm', $extensions[1]);
    }

    public function testDefaultMissing()
    {
        $repository = $this->createDefaultRepository();

        if (null === $repository) {
            return;
        }

        $this->assertTrue(is_array($repository->findExtensions('foobar/missing')));
        $this->assertEmpty($repository->findExtensions('foobar/missing'));
        $this->assertNull($repository->findType('shouldnotexist'));
    }

    public function testDefaultDumpTypeToExtensions()
    {
        $repository = $this->createDefaultRepository();

        if (null === $repository) {
            return;
        }

        $dump = $repository->dumpTypeToExtensions();

        $this->assertCount(765, array_keys($dump));
    }

    public function testDefaultDumpExtensionToType()
    {
        $repository = $this->createDefaultRepository();

        if (null === $repository) {
            return;
        }

        $dump = $repository->dumpExtensionToType();

        $this->assertCount(981, array_keys($dump));
    }

    public function testMissing()
    {
        $repository = $this->createRepository();

        if (null === $repository) {
            return;
        }

        $this->assertTrue(is_array($repository->findExtensions('foobar/missing')));
        $this->assertEmpty($repository->findExtensions('foobar/missing'));
        $this->assertNull($repository->findType('shouldnotexist'));
    }

    public function testFabricated()
    {
        $repository = $this->createRepository();

        if (null === $repository) {
            return;
        }

        $extensions = $repository->findExtensions('dflydev/apache-mime-types');
        $this->count(2, $extensions);
        $this->assertEquals('dflydevamt', $extensions[0]);
        $this->assertEquals('ddevamt', $extensions[1]);
    }

    public function testDumpTypeToExtensions()
    {
        $repository = $this->createRepository();

        if (null === $repository) {
            return;
        }

        $dump = $repository->dumpTypeToExtensions();

        $this->assertCount(2, array_keys($dump));
        $this->assertArrayHasKey('dflydev/apache-mime-types', $dump);
        $this->assertArrayHasKey('dflydev/yet-another-mime-type', $dump);
    }

    public function testDumpExtensionToType()
    {
        $repository = $this->createRepository();

        if (null === $repository) {
            return;
        }

        $dump = $repository->dumpExtensionToType();

        $this->assertCount(3, array_keys($dump));
        $this->assertEquals('dflydev/apache-mime-types', $dump['dflydevamt']);
        $this->assertEquals('dflydev/apache-mime-types', $dump['ddevamt']);
        $this->assertEquals('dflydev/yet-another-mime-type', $dump['dflydevyamt']);
    }
}
