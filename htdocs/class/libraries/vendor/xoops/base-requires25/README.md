base-requires25
===============

This package separates the requirements of XOOPS/XoopsCore25 from other requirements that may be added to the main composer.json, such as modules.

To update just the packages in base-requires use:

    composer update xoops/base-requires25 --with-dependencies

To check for, but not install updates:

    composer update xoops/base-requires25 --dry-run --with-dependencies
