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

class Lynx_Main
{
	public static function setUserAccess($userId, $tsUID, $tsVirtualServer = false)
	{
		global $db;

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
		$tsResult = Lynx_TeamSpeak3::setTeamSpeakAccess($userId, $tsUID, $tsGroups, array(), $tsVirtualServer);
		
		// TODO: ejabberd stuff
		
		// Set the correct OpenFire groups
		Lynx_OpenFire::setOpenFireAccess($userId, $ofGroups, array());
		
		// Update the lynx_cron_last parameter to indicate this user has just been updated
		$sql = 'UPDATE '.USERS_TABLE.'
				SET lynx_cron_last = '.time().'
				WHERE user_id = '.$userId;
		$db->sql_query($sql);
		
		// Return the TeamSpeak 3 result to determine whether or not the new TS UID should be saved.
		return $tsResult;
	}
	
	public static function runCronjob()
	{
		global $db, $config, $user;
		
		// Set tsVirtualServer variable to false
		$tsVirtualServer = false;
		
		// Check if TeamSpeak 3 integration is enabled
		if($config['lynx_ts_masterswitch'])
		{
			// Make a connection to the TeamSpeak 3 Virtual Server to avoid having to reconnect per user
			try
			{
				// Set custom nickname for serverquery client
				$tsNickname = (Lynx_TeamSpeak3::validateMixedalphanumeric($tsNickname) != 1) ? "Cyerus" : $config['lynx_ts_nickname'];
				
				$tsVirtualServer = TeamSpeak3::factory("serverquery://" . $config['lynx_ts_username'] . ":" . $config['lynx_ts_password'] . "@" . $config['lynx_ts_ip'] . ":" . $config['lynx_ts_port_query'] . "/?server_port=" . $config['lynx_ts_port_server'] . "&nickname=" . $tsNickname);
			} 
			catch (TeamSpeak3_Exception $e) 
			{
				Lynx_Log::addToLog($user->data['user_id'], "TeamSpeak 3", $e->getCode(), $e->getMessage());
				return false;
			}
		}
		
		// Update only users that haven't been updated in the last hour
		$hourAgo = time() - 3600;
		
		// Grab all current users who's accounts are active
		$sql = 'SELECT user_id, lynx_ts3uid
                FROM ' . USERS_TABLE . '
                WHERE user_type = 0
				AND lynx_cron_last < '.$hourAgo.'
                ORDER BY username';
        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result))
        {
			self::setUserAccess($row['user_id'], $row['lynx_ts3uid'], $tsVirtualServer);
		}
		$db->sql_freeresult($result);
		
		return true;
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