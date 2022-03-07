<?php namespace XoopsModules\Protector;

// Filter Handler class (singleton)

/**
 * Class FilterHandler
 */
class FilterHandler
{
    /**
     * @var \Guardian
     */
    public $protector;
    /**
     * @var string
     */
    public $filters_base = '';

    /**
     * FilterHandler constructor.
     */
    protected function __construct()
    {
        $this->protector = Guardian::getInstance();
        $this->filters_base = dirname(__DIR__) . '/class/Filter/Enabled/';
    }

    /**
     * @return FilterHandler
     */
    public static function getInstance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new FilterHandler();
        }

        return $instance;
    }

    // return: false : execute default action

    /**
     * @param string $type
     *
     * @return int|mixed
     */
    public function execute($type)
    {
        $ret = 0;

        $dh = opendir($this->filters_base);
        while (false !== ($file = readdir($dh))) {
            if (0 === strncmp($file, $type, strlen($type))) {
                $pluginName = substr($file, 0, -4);

                $class = __NAMESPACE__ . '\Filter\Enabled\\' . $pluginName;
                if (!class_exists($class)) {
                    throw new \RuntimeException("Class '$class' not found");
                }
                $pluginObj = new $class();
                $ret        |= $pluginObj->execute();
            }
        }
        closedir($dh);

        return $ret;
    }
}
