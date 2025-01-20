<?php declare(strict_types=1);

/**
 * @see https://www.php-fig.org/psr/psr-4/examples/
 */
spl_autoload_register(
    static function ($class): void {
        // project-specific namespace prefix
        $prefix = 'XoopsModules\\' . \ucfirst(\basename(\dirname(__DIR__)));

        // base directory for the namespace prefix
        $baseDir = \dirname(__DIR__) . '/class/';

        // does the class use the namespace prefix?
        $len = \mb_strlen($prefix);

        if (0 !== strncmp($prefix, $class, $len)) {
            return;
        }

        // get the relative class name
        $relativeClass = mb_substr($class, $len);

        // replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php
        $file = $baseDir . \str_replace('\\', '/', $relativeClass) . '.php';

        // if the file exists, require it
        if (\is_file($file)) {
            require_once $file;
        }
    }
);
