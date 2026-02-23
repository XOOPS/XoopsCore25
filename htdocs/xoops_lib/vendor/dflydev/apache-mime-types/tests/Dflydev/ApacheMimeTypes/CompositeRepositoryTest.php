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
 * Composite Repository Test
 *
 * @author Beau Simensen <beau@dflydev.com>
 */
class CompositeRepositoryTest extends AbstractRepositoryTest
{
    protected function createDefaultRepository()
    {
        return new CompositeRepository(array(
            new PhpRepository,
            new JsonRepository,
            new FlatRepository,
        ));
    }

    protected function createRepository()
    {
        return new CompositeRepository(array(
            new ArrayRepository(array(
                'dflydev/apache-mime-types' => array('dflydevamt'),
                'dflydev/yet-another-mime-type' => array('dflydevyamt'),
            )),
            new ArrayRepository(array(
                'dflydev/apache-mime-types' => array('ddevamt'),
            )),
        ));
    }
}
