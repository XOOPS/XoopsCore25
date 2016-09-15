<?php

use Xmf\Request;

/**
 * CAPTCHA configurations for Image mode
 *
 * Based on DuGris' SecurityImage
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             class
 * @subpackage          CAPTCHA
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');

/**
 * Class XoopsCaptcha
 */
class XoopsCaptcha
{
    // static $instance;
    public $active;
    public $handler;
    public $path_basic;
    public $path_plugin;
    public $name;
    public $config  = array();
    public $message = array(); // Logging error messages

    /**
     * construct
     */
    protected function __construct()
    {
        xoops_loadLanguage('captcha');
        // Load static configurations
        $this->path_basic  = XOOPS_ROOT_PATH . '/class/captcha';
        $this->path_plugin = XOOPS_ROOT_PATH . '/Frameworks/captcha';
        $this->config      = $this->loadConfig();
        $this->name        = $this->config['name'];
    }

    /**
     * Get Instance
     *
     * @return XoopsCaptcha Instance
     */
    public static function getInstance()
    {
        static $instance;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * XoopsCaptcha::loadConfig()
     *
     * @param mixed $filename
     *
     * @return array
     */
    public function loadConfig($filename = null)
    {
        $basic_config  = array();
        $plugin_config = array();
        $filename      = empty($filename) ? 'config.php' : 'config.' . $filename . '.php';
        if (file_exists($file = $this->path_basic . '/' . $filename)) {
            $basic_config = include $file;
        }
        if (file_exists($file = $this->path_plugin . '/' . $filename)) {
            $plugin_config = include $file;
        }

        $config = array_merge($basic_config, $plugin_config);
        foreach ($config as $key => $val) {
            $config[$key] = $val;
        }

        return $config;
    }

    /**
     * XoopsCaptcha::isActive()
     *
     * @return bool
     */
    public function isActive()
    {
        if (null !== $this->active) {
            return $this->active;
        }
        if (!empty($this->config['disabled'])) {
            $this->active = false;

            return $this->active;
        }
        if (!empty($this->config['skipmember']) && is_object($GLOBALS['xoopsUser'])) {
            $this->active = false;

            return $this->active;
        }
        if (null === $this->handler) {
            $this->loadHandler();
        }
        $this->active = isset($this->handler);

        return $this->active;
    }

    /**
     * XoopsCaptcha::loadHandler()
     *
     * @param mixed $name
     * @return
     */
    public function loadHandler($name = null)
    {
        $name  = !empty($name) ? $name : (empty($this->config['mode']) ? 'text' : $this->config['mode']);
        $class = 'XoopsCaptcha' . ucfirst($name);
        if (!empty($this->handler) && get_class($this->handler) == $class) {
            return $this->handler;
        }
        $this->handler = null;
        if (file_exists($file = $this->path_basic . '/' . $name . '.php')) {
            require_once $file;
        } else {
            if (file_exists($file = $this->path_plugin . '/' . $name . '.php')) {
                require_once $file;
            }
        }

        if (!class_exists($class)) {
            $class = 'XoopsCaptchaText';
            require_once $this->path_basic . '/text.php';
        }
        $handler = new $class($this);
        if ($handler->isActive()) {
            $this->handler = $handler;
            $this->handler->loadConfig($name);
        }

        return $this->handler;
    }

    /**
     * XoopsCaptcha::setConfigs()
     *
     * @param  mixed $configs
     * @return bool
     */
    public function setConfigs($configs)
    {
        foreach ($configs as $key => $val) {
            $this->setConfig($key, $val);
        }

        return true;
    }

    /**
     * XoopsCaptcha::setConfig()
     *
     * @param  mixed $name
     * @param  mixed $val
     * @return bool
     */
    public function setConfig($name, $val)
    {
        if (isset($this->$name)) {
            $this->$name = $val;
        } else {
            $this->config[$name] = $val;
        }

        return true;
    }

    /**
     * Verify user submission
     */
    /**
     * XoopsCaptcha::verify()
     *
     * @param  mixed $skipMember
     * @param  mixed $name
     * @return bool
     */
    public function verify($skipMember = null, $name = null)
    {
        $sessionName = empty($name) ? $this->name : $name;
        $skipMember  = ($skipMember === null) ? $_SESSION["{$sessionName}_skipmember"] : $skipMember;
        $maxAttempts = $_SESSION["{$sessionName}_maxattempts"];
        $attempt     = $_SESSION["{$sessionName}_attempt"];
        $is_valid    = false;
        // Skip CAPTCHA verification if disabled
        if (!$this->isActive()) {
            $is_valid = true;
            // Skip CAPTCHA for member if set
        } elseif (!empty($skipMember) && is_object($GLOBALS['xoopsUser'])) {
            $is_valid = true;
            // Kill too many attempts
        } elseif (!empty($maxAttempts) && $attempt > $maxAttempts) {
            $this->message[] = _CAPTCHA_TOOMANYATTEMPTS;
            // Verify the code
        } else {
            $is_valid = $this->handler->verify($sessionName);
            $xoopsPreload = XoopsPreload::getInstance();
            $xoopsPreload->triggerEvent('core.behavior.captcha.result', $is_valid);
        }

        if (!$is_valid) {
            // Increase the attempt records on failure
            $_SESSION["{$sessionName}_attempt"]++;
            // Log the error message
            $this->message[] = _CAPTCHA_INVALID_CODE;
        } else {
            // reset attempt records on success
            $_SESSION["{$sessionName}_attempt"] = null;
        }
        $this->destroyGarbage(true);

        return $is_valid;
    }

    /**
     * XoopsCaptcha::getCaption()
     *
     * @return mixed|string
     */
    public function getCaption()
    {
        return defined('_CAPTCHA_CAPTION') ? constant('_CAPTCHA_CAPTION') : '';
    }

    /**
     * XoopsCaptcha::getMessage()
     *
     * @return string
     */
    public function getMessage()
    {
        return implode('<br>', $this->message);
    }

    /**
     * Destroy historical stuff
     * @param bool $clearSession
     * @return bool
     */
    public function destroyGarbage($clearSession = false)
    {
        $this->loadHandler();
        if (is_callable($this->handler, 'destroyGarbage')) {
            $this->handler->destroyGarbage();
        }
        if ($clearSession) {
            $_SESSION[$this->name . '_name']        = null;
            $_SESSION[$this->name . '_skipmember']  = null;
            $_SESSION[$this->name . '_code']        = null;
            $_SESSION[$this->name . '_maxattempts'] = null;
        }

        return true;
    }

    /**
     * XoopsCaptcha::render()
     *
     * @return string
     */
    public function render()
    {
        $_SESSION[$this->name . '_name']       = $this->name;
        $_SESSION[$this->name . '_skipmember'] = $this->config['skipmember'];
        $form                                  = '';
        if (!$this->active || empty($this->config['name'])) {
            return $form;
        }

        $maxAttempts                            = $this->config['maxattempts'];
        $_SESSION[$this->name . '_maxattempts'] = $maxAttempts;
        $attempt                                = isset($_SESSION[$this->name . '_attempt']) ? $_SESSION[$this->name . '_attempt'] : 0;
        $_SESSION[$this->name . '_attempt']     = $attempt;

        // Failure on too many attempts
        if (!empty($maxAttempts) && $attempt > $maxAttempts) {
            $form = _CAPTCHA_TOOMANYATTEMPTS;
            // Load the form element
        } else {
            $form = $this->loadForm();
        }

        return $form;
    }

    /**
     * XoopsCaptcha::renderValidationJS()
     *
     * @return string
     */
    public function renderValidationJS()
    {
        if (!$this->active || empty($this->config['name'])) {
            return '';
        }

        return $this->handler->renderValidationJS();
    }

    /**
     * XoopsCaptcha::setCode()
     *
     * @param  mixed $code
     * @return bool
     */
    public function setCode($code = null)
    {
        $code = ($code === null) ? $this->handler->getCode() : $code;
        if (!empty($code)) {
            $_SESSION[$this->name . '_code'] = $code;

            return true;
        }

        return false;
    }

    /**
     * XoopsCaptcha::loadForm()
     *
     * @return
     */
    public function loadForm()
    {
        $form = $this->handler->render();
        $this->setCode();

        return $form;
    }
}

/**
 * Abstract class for CAPTCHA method
 *
 * Currently there are two types of CAPTCHA forms, text and image
 * The default mode is "text", it can be changed in the priority:
 * 1 If mode is set through XoopsFormCaptcha::setConfig("mode", $mode), take it
 * 2 Elseif mode is set though captcha/config.php, take it
 * 3 Else, take "text"
 */
class XoopsCaptchaMethod
{
    public $handler;
    public $config;
    public $code;

    /**
     * XoopsCaptchaMethod::__construct()
     *
     * @param mixed $handler
     */
    public function __construct($handler = null)
    {
        $this->handler = $handler;
    }

    /**
     * XoopsCaptchaMethod::isActive()
     *
     * @return bool
     */
    public function isActive()
    {
        return true;
    }

    /**
     * XoopsCaptchaMethod::loadConfig()
     *
     * @param  string $name
     * @return void
     */
    public function loadConfig($name = '')
    {
        $this->config = empty($name) ? $this->handler->config : array_merge($this->handler->config, $this->handler->loadConfig($name));
    }

    /**
     * XoopsCaptchaMethod::getCode()
     *
     * @return string
     */
    public function getCode()
    {
        return (string)$this->code;
    }

    /**
     * XoopsCaptchaMethod::render()
     *
     * @return void
     */
    public function render()
    {
    }

    /**
     * @return string
     */
    public function renderValidationJS()
    {
        return '';
    }

    /**
     * XoopsCaptchaMethod::verify()
     *
     * @param  mixed $sessionName
     * @return bool
     */
    public function verify($sessionName = null)
    {
        $is_valid = false;
        if (!empty($_SESSION["{$sessionName}_code"])) {
            $func     = !empty($this->config['casesensitive']) ? 'strcmp' : 'strcasecmp';
//            $is_valid = !$func(trim(@$_POST[$sessionName]), $_SESSION["{$sessionName}_code"]);
            $is_valid = !$func(trim(Request::getString($sessionName, '', 'POST')), $_SESSION["{$sessionName}_code"]);
        }

        return $is_valid;
    }
}
