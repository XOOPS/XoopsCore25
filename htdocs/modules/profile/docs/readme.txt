Read Me First
=============

Description
------------
The Profile Module is for managing custom user profile fields.

 Requirements
 _____________________________________________________________________

- PHP version >= 5.3.7
- XOOPS 2.5.7+

Install/uninstall
------------------
No special measures necessary, follow the standard installation process, extract the module folder into the ../modules directory. Install the module through Admin -> System Module -> Modules.

Detailed instructions on installing modules are available in the XOOPS Operations Manual (http://goo.gl/adT2i)

Operating instructions
------------------------
- Configure your preferences for the module (see ‘Preferences’) and optionally the Profile block if you intend to use it (see ‘Blocks’).
- Edit existing Categories or add new ones.
- Edit existing Fields or add new ones. Here you can specify which fields will be visible in which category, and if they will be visible during user registration.
- Define the order of Registration steps.
- And finally, you can set permissions for individual fields - which ones are editable, which ones are searchable.

Detailed instructions on configuring the access rights for user groups are available in the XOOPS Operations Manual (http://goo.gl/adT2i)

Anti-Spam measures
---------------------
To minimize spam registrations, do the following:

a) go to the Protector module in Admin, go to Preferences, and then at the bottom, at this option:

"Stop Forum Spam"
Checks POST data against spammers registered on www.stopforumspam.com database. Requires php CURL lib

set it to "Ban the IP (no limit)"

b) in /class/captcha/config.php, make sure that the mode is set as "text":

return $config = array(
'disabled' => false, // Disable CAPTCHA
'mode' => 'text', // default mode, you can choose 'text', 'image', 'recaptcha'(requires api key)
'name' => 'xoopscaptcha', // captcha name
'skipmember' => true, // Skip CAPTCHA check for members
'maxattempts' => 10, // Maximum attempts for each session
);

c) In the Profile module, go to Admin and in the Basic step located at:

/modules/profile/admin/step.php?id=1

 set the "Save after stop" to "No"

d) In Profile Preferences, set the "Use Captcha after the second Registration step" Option to "Yes" (it is the default)


Tutorial
-----------
None available at the moment.
