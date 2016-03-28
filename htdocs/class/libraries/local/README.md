# XOOPS_ROOT_PATH/class/libraries/local

This directory is intended for vendor libraries which have been modified for use in XOOPS, such as adding custom configuration, or sub setting to eliminate unneeded files.

Unlike the composer managed vendor directory, any code libraries here will not autoload, but will be in a known and standard location.

Isolating such libraries here will simplify updating these libraries to track upstream updates.
