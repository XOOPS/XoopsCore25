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
        $this->protector    = Guardian::getInstance();
        $this->filters_base = dirname(__DIR__) . '/filters_enabled';
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
        while (($file = readdir($dh)) !== false) {
            if (strncmp($file, $type . '_', strlen($type) + 1) === 0) {
                include_once $this->filters_base . '/' . $file;
                $plugin_name = 'protector_' . substr($file, 0, -4);
                if (function_exists($plugin_name)) {
                    // old way
                    $ret |= call_user_func($plugin_name);
                } elseif (class_exists($plugin_name)) {
                    // newer way
                    $plugin_obj = new $plugin_name(); //old code is -> $plugin_obj =& new $plugin_name() ; //hack by Trabis
                    $ret |= $plugin_obj->execute();
                }
            }
        }
        closedir($dh);

        return $ret;
    }
}
