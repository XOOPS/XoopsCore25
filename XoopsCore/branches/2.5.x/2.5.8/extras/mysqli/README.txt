MySQLi driver for XOOPS 2.5.x

The mysqldatabase.php file in this directory is a drop in replacement for the
standard MySQL driver found in htdocs/class/database/mysqldatabase.php

If you would like to use the PHP MySQLi extension rather than the now deprecated
MySQL extension, you can copy this file to the class/database directory to overwrite
the standard driver. No other changes are required.

There may be direct calls to mysql_* functions that do not go through the driver.
Those calls will not be changed by installing this updated driver.
