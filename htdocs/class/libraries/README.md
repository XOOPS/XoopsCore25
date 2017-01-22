Composer support in XOOPS 2.5.x

The libraries directory in XOOPS 2.5.8 and above is managed by composer.
Composer is a tool for managing PHP library dependencies. You can learn
more about composer at:
https://getcomposer.org/

You are not required to know about or use composer to use XOOPS, but
power users may wish to take advantage of its capabilities to extend
and customize their systems.

We build the libraries directory for our distribution package using
the composer.json.dist file included in the class/libraries directory.
To customize your composer environment, we recommend copying the
supplied composer.json.dist to composer.json as your starting point.

When XOOPS is updated, the composer.json.dist file may be updated, but
we will NOT update your composer.json file. You will be responsible for
moving any changes forward, but your customized requirements will not
be disturbed.

The actual requirements for XOOPS/XoopsCore25 are kept in a separate
package, XOOPS/base-requires25, so changes to composer.json.dist should
be minimal.
