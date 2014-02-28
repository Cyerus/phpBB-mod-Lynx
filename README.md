phpBB mod - Lynx
================

Copyright (C) 2014 by Cyerus, Jordy Wille, 
All rights reserved.

This mod adds TeamSpeak 3 and Jabber integration to your phpBB forum installation.
It allows for permissions in TeamSpeak 3 and Jabber based on the users forumgroups.

## LICENSE
This mod is licensed under a MIT style license, see LICENSE for further information.

## FEATURES
- Simple no-nonsense integration of TeamSpeak 3 and Jabber
- Lightweight and fast
- UCP module for the users to update their TeamSpeak 3 UID
- Cronjob file for scheduled permission checks

## REQUIREMENTS
- phpBB 3.0.12
- phpBB AutoMOD 1.0.2
- phpBB Umil 1.0.5


## INSTALLATION
Download the latest stable as a zipfile from http://eve-it.org/my-phpbb-mods/lynx/
and install it to your phpBB forum using AutoMOD.

Edit the TeamSpeak 3 and Jabber settings under the new Lynx ACP modules, and add
the requested permissions per group to the ACP Group Management module.

Run the cronjob script manually to make sure the correct permissions are replied,
and add it is a scheduled task once you are statisfied with its results.

## USAGE
The mod should do everything automatically, although it does require the cron script
to be set as a scheduled task.

## PROBLEMS / BUGS
If you find any problems with this mod, please use githubs issue tracker at 
https://github.com/Cyerus/phpBB-mod-Lynx/issues

## LINKS
- [Github](https://github.com/Cyerus/phpBB-mod-Lynx/)

## CONTACT
- Cyerus <cyerus.eve@gmail.com>

## ACKNOWLEDGEMENTS
- Lynx for phpBB is written in [PHP](http://php.net)
- phpBB (http://www.phpbb.com)
- phpBB AutoMOD (https://www.phpbb.com/mods/automod/)
- phpBB Umil (https://www.phpbb.com/mods/umil/)
