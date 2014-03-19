<?php

/*
The MIT License (MIT)

Copyright (c) 2014 Cyerus

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

namespace Lynx;

class Main
{
	public static function setUserAccess($userId, $tsUID, $tsVirtualServer = false)
	{
		// Get all the groups the user is part of
		$userForumGroups = self::getUserForumGroups($userId);
		
		// Grab all the information about all the groups
		$forumGroupInfo = self::getForumGroupInfo();
		
		// Set default values (just in case)
		$tsGroups = array();
		$ejabberd = false;
		$ofGroups = array();
		
		// Loop the user's groups 
		foreach($userForumGroups as $currentForumGroup)
		{
			// Check if TeamSpeak 3 group is set
			if(isset($forumGroupInfo[$currentForumGroup]['group_lynx_ts3']) && $forumGroupInfo[$currentForumGroup]['group_lynx_ts3'] > 0)
			{
				$tsGroups[] = $forumGroupInfo[$currentForumGroup]['group_lynx_ts3'];
			}
			
			// Check if ejabberd is enabled for this group
			if(isset($forumGroupInfo[$currentForumGroup]['group_lynx_ejabberd']) && $forumGroupInfo[$currentForumGroup]['group_lynx_ejabberd'])
			{
				$ejabberd = true;
			}
			
			// Check if OpenFire group is set
			if(isset($forumGroupInfo[$currentForumGroup]['group_lynx_openfire']) && $forumGroupInfo[$currentForumGroup]['group_lynx_openfire'] != "")
			{
				$ofGroups[] = $forumGroupInfo[$currentForumGroup]['group_lynx_openfire'];
			}
		}

		// Set the correct TeamSpeak 3 groups
		$tsResult = Lynx\TeamSpeak3::setTeamSpeakAccess($userId, $tsUID, $tsGroups, array(), $tsVirtualServer);
		
		// TODO: ejabberd stuff
		
		// Set the correct OpenFire groups
		Lynx\OpenFire::setOpenFireAccess($userId, $ofGroups, array());
		
		return $tsResult;
	}
	
	private static function getForumGroupInfo()
	{
		global $db, $user;
		
		// Grab all the forumgroups and put them into an array to be able to easily request values for EVE related stuffz
		$forumGroupInfo = array();
		$sql = "SELECT group_id, group_name, group_lynx_ts3, group_lynx_ejabberd, group_lynx_openfire
				FROM " . GROUPS_TABLE;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$forumGroupInfo[$row['group_id']]['group_name'] = $row['group_name'];
			$forumGroupInfo[$row['group_id']]['group_lynx_ts3'] = $row['group_lynx_ts3'];
			$forumGroupInfo[$row['group_id']]['group_lynx_ejabberd'] = $row['group_lynx_ejabberd'];
			$forumGroupInfo[$row['group_id']]['group_lynx_openfire'] = $row['group_lynx_openfire'];

			// Check if the group name is actually a language identifier
			// If so, grab the correct language string from the language file instead
			// ( group prefix is G_ )
			if(isset($user->lang["G_" . $row['group_name']]))
			{
				$forumGroupInfo[$row['group_id']]['group_name'] = $user->lang["G_" . $row['group_name']];
			}
		}
		$db->sql_freeresult($result);
		
		return $forumGroupInfo;
	}
	
	private static function getUserForumGroups($userId)
	{
		global $db;
		
		$forumGroups = array();
		$sql = "SELECT group_id
				FROM " . USER_GROUP_TABLE . "
				WHERE user_id = " . $userId;
		$result = $db->sql_query($sql);

		while ($row = $db->sql_fetchrow($result))
		{
			$forumGroups[] = $row['group_id'];
		}
		$db->sql_freeresult($result);
		
		return $forumGroups;
	}
}

?>