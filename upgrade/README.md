XOOPS Upgrader
==============

The XOOPS Upgrader will examine this XOOPS installation and apply any needed
patches to make it compatible with the new XOOPS code. Patches may include
database changes, adding default settings for new configuration items, file
and data updates, and more.

## Quick Guide to XOOPS Upgrades

If you are updating an existing XOOPS system, follow these steps:
 - it is recommended to turn your site off during the upgrade (see preferences > system > general settings)
 - back up your site
 - really, back up your site, including the files and database. Safe beats sorry, every time.
 - copy the files in the distribution **htdocs** directory over your site root directory
 - copy the entire **upgrade** folder to your root directory
 - launch your site in your browser with `/upgrade/` added to the end of your main URL (i.e. http://example.com/upgrade/)
 - follow the on screen instructions, and click the **continue** button when it appears

At the end of the upgrade, you will be directed to the system administration
area, ready to update your system module. Perform the update. When complete,
visit the modules administation area and update any modules as indicated.

Delete the upgrade directory from your root directory. Also, delete the install
directory if it exists. Turn your system back on and explore
