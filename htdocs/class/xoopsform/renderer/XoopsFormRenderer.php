<?php
/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Factory to build handlers
 *
 * @category  XoopsForm
 * @package   XoopsFormRenderer
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2017-2020 XOOPS Project (https://xoops.org)
 * @license   GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @link      https://xoops.org
 */
final class XoopsFormRenderer
{
    const NOT_PERMITTED = 'Not supported for Singleton';

    /**
     * @var XoopsFormRenderer|null The reference to *Singleton* instance of this class
     */
    private static $instance;

    /**
     * @var XoopsFormRendererInterface|null The reference to *Singleton* instance of this class
     */
    protected $renderer;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return XoopsFormRenderer the singleton instance.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     *
     * @throws \LogicException
     */
    public function __clone()
    {
        throw new \LogicException(static::NOT_PERMITTED);
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     *
     * @throws \LogicException
     */
    public function __wakeup()
    {
        throw new \LogicException(static::NOT_PERMITTED);
    }

    /**
     * set the renderer
     *
     * @param XoopsFormRendererInterface $renderer instance of renderer
     *
     * @return void
     */
    public function set(XoopsFormRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * get the renderer
     *
     * @return XoopsFormRendererInterface
     */
    public function get()
    {
        // return a default if not set
        if (null === $this->renderer) {
            xoops_load('xoopsformrendererlegacy');
            $this->renderer = new XoopsFormRendererLegacy();
        }

        return $this->renderer;
    }
}
