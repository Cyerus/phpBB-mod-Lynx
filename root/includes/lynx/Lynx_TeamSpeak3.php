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

class Lynx_TeamSpeak3
{
	/**
	* Sets character's TeamSpeak permissions
	*/
	public static function setTeamSpeakAccess($userId, $tsUID, $tsGroups, $tsGroupsExtra = array(), $tsVirtualServer = false)
	{
		global $config;
		
		// Check if TeamSpeak 3 integration is enabled
		if(!$config['lynx_ts_masterswitch'])
		{
			return false;
		}
		
		// Check if TeamSpeak UID is set
		if(!isset($tsUID) || $tsUID == "")
		{
			return false;
		}

		// Set current TeamSpeak groups variable
		$tsGroupsCurrent = array();

		// Set tsGroups to array (just in case)
		if(!is_array($tsGroups))
		{
			$tsGroups = array();
		}

		// There can be extra groups, combine arrays
		if(!empty($tsGroupsExtra))
		{
			$tsGroups = array_merge($tsGroups, $tsGroupsExtra);

			// Remove duplicates
			if(!empty($tsGroups))
			{
				$tsGroups = array_unique($tsGroups);
			}
		}

		try
		{
			// Check if a connection for the TeamSpeak 3 serverquery already exists, and start one if it doesn't
			if(!$tsVirtualServer)
			{
				// Set custom nickname for serverquery client
				$tsNickname = (self::validateMixedalphanumeric($tsNickname) != 1) ? "Cyerus" : $config['lynx_ts_nickname'];
				
				$tsVirtualServer = TeamSpeak3::factory("serverquery://" . $config['lynx_ts_username'] . ":" . $config['lynx_ts_password'] . "@" . $config['lynx_ts_ip'] . ":" . $config['lynx_ts_port_query'] . "/?server_port=" . $config['lynx_ts_port_server'] . "&nickname=" . $tsNickname);
			}

			// Find the client using the clients TS UID
			$tsClientDbId = $tsVirtualServer->clientFindDb($tsUID, true);
			foreach($tsClientDbId as $tsCurrentDbId)
			{
				// Grab the groups for each db entry found
				$tsGroupsCurrent = self::getTeamSpeakGroupsByDbId($tsVirtualServer, $tsCurrentDbId);

				// Loop each group the user is currently part of
				foreach($tsGroupsCurrent as $currentTSGroup)
				{
					// Skip if this person should stay part of this group
					if(in_array($currentTSGroup, $tsGroups))
					{
						continue;
					}

					// Skip if this person is not part of the group (as we can't delete something that isn't there)
					if(!in_array($currentTSGroup, $tsGroupsCurrent))
					{
						continue;
					}
					
					// Skip if group is set at 0 (meaning TeamSpeak integration disabled) 
					if($currentTSGroup == 0)
					{
						continue;
					}
					
					// Skip if the group is set as the TeamSpeak admin group, as we don't want 'accidents' to happen
					if($config['lynx_ts_admin_switch'] && $currentTSGroup == $config['lynx_ts_admin_tsgroup']) 
					{
						continue;
					}
					 
					// Loop each of the special set groups to determine if this group might be one of them
					$tsSetAsSpecial = false;
					for($i = 1; $i <= $config['lynx_ts_special']; $i++)
					{
						// Determine if this group is set as special
						if($currentTSGroup == $config['lynx_ts_special_'.$i.'_tsgroup'] && $config['lynx_ts_special_'.$i.'_switch'])
						{
							$tsSetAsSpecial = true;
						}
					}

					// Only remove a group if a group is not set as a special group
					if($tsSetAsSpecial)
					{
						continue;
					}
					
					self::groupDelete($userId, $tsVirtualServer, $currentTSGroup, $tsCurrentDbId);
				}

				// Loop each group the user should be part of
				foreach($tsGroups as $currentTSGroup)
				{
					// Check if user is already part of group
					if(in_array($currentTSGroup, $tsGroupsCurrent))
					{
						continue;
					}

					// Skip if group is set at 0 (meaning TeamSpeak integration disabled) 
					if($currentTSGroup == 0) 
					{
						continue;
					}
					
					self::groupAdd($userId, $tsVirtualServer, $currentTSGroup, $tsCurrentDbId);
				}
			}
			
			return true;
		}
		catch(TeamSpeak3_Exception $e)
		{
			Lynx_Log::addToLog($userId, "TeamSpeak 3", $e->getCode(), $e->getMessage());
			return false;
		}
	}
	
	private static function getTeamSpeakGroupsByDbId($tsVirtualServer, $tsUserDbId)
	{
		// Grab the groups for each db entry found
		$tsClientGroups = $tsVirtualServer->clientGetServerGroupsByDbid($tsUserDbId);
		foreach($tsClientGroups as $tsCurrentClientGroup)
		{
			$tsGroupsCurrent[] = $tsCurrentClientGroup["sgid"];
		}
		
		return $tsGroupsCurrent;
	}
	
	private static function groupAdd($userId, $tsVirtualServer, $currentTSGroup, $tsCurrentDbId)
	{
		// Simple try block just in case a group doesn't exist (which shouldn't be caught by the 'global' try block, as that would stop the other group syncs)
		try 
		{
			// Grab groupInfo
			$groupInfo = $tsVirtualServer->serverGroupGetById($currentTSGroup);

			// Skip if a group is NOT set as a 'permanent' group
			if(!$groupInfo->savedb) 
			{
				return false;
			}

			// Actually add the user to this group
			$tsVirtualServer->serverGroupClientAdd($currentTSGroup, $tsCurrentDbId);
			
			return true;
		} 
		catch (TeamSpeak3_Exception $e) 
		{
			Lynx_Log::addToLog($userId, "TeamSpeak 3", $e->getCode(), $e->getMessage());
		}
		
		return false;
	}
	
	private static function groupDelete($userId, $tsVirtualServer, $currentTSGroup, $tsCurrentDbId)
	{
		// Simple try block just in case a group doesn't exist (which shouldn't be caught by the 'global' try block, as that would stop the other group syncs)
		try 
		{
			// Grab groupInfo
			$groupInfo = $tsVirtualServer->serverGroupGetById($currentTSGroup);

			// Skip if a group is NOT set as a 'permanent' group
			if(!$groupInfo->savedb) 
			{
				return false;
			}

			// Actually remove the user from this group
			$tsVirtualServer->serverGroupClientDel($currentTSGroup, $tsCurrentDbId);
			
			return true;
		} 
		catch (TeamSpeak3_Exception $e) 
		{
			Lynx_Log::addToLog($userId, "TeamSpeak 3", $e->getCode(), $e->getMessage());
		}
		
		return false;
	}
	
	public static function updateUID($userId, $newUID, $oldUID, $tsVirtualServer = false)
	{
		global $db, $config;
		
		// Check if TeamSpeak 3 integration is enabled
		if(!$config['lynx_ts_masterswitch'])
		{
			return false;
		}
		
		// Checked if we actually updated the TS UID
		if($newUID != $oldUID)
		{
			try
			{
				// Check if connection to the TeamSpeak 3 serverquery is already made
				if(!$tsVirtualServer)
				{
					// Set custom nickname for serverquery client
					$tsNickname = (self::validateMixedalphanumeric($config['lynx_ts_nickname']) != 1) ? "Cyerus" : $config['lynx_ts_nickname'];

					$tsVirtualServer = TeamSpeak3::factory("serverquery://" . $config['lynx_ts_username'] . ":" . $config['lynx_ts_password'] . "@" . $config['lynx_ts_ip'] . ":" . $config['lynx_ts_port_query'] . "/?server_port=" . $config['lynx_ts_port_server'] . "&nickname=" . $tsNickname);
				}

				// Since the TeamSpeak 3 UID has changed, lets remove all permissions from the old TS UID
				self::setTeamSpeakAccess($userId, $oldUID, array(), array(), $tsVirtualServer);

				// Set checkTS variable to true, so that when function is not called it will be true
				$checkTS = true;

				// Only try to set permissions to new TS UID when new TS UID is not empty
				if(!empty($newUID))
				{
					// Add permissions to the new TeamSpeak 3 UID
					$checkTS = Lynx_Main::setUserAccess($userId, $newUID, $tsVirtualServer);
				}

				// Check if the new TS UID correctly updated, or save the new TS UID when it's empty
				$saveUID = ($checkTS) ? $newUID : "";
				
				$sql_ary = array(
					'lynx_ts3uid'	=> $saveUID,
				);

				$sql = 'UPDATE ' . USERS_TABLE . '
					SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
					WHERE user_id = ' . $userId;
				$db->sql_query($sql);

				return true;
			} 
			catch (TeamSpeak3_Exception $e)
			{
				Lynx_Log::addToLog($userId, "TeamSpeak 3", $e->getCode(), $e->getMessage());
			}
		}
		else
		{
			// Only update permissions if TS UID is not empty
			if(!empty($newUID))
			{
				Lynx_Main::setUserAccess($userId, $oldUID);
			}
		}
		
		return false;
	}
	
		/**
	* Validate string to only consist of letters and numbers
	*
	* @param string $str String to check.
	* @return 1 for found, 0 for not-found, and FALSE on error.
	*/
	public static function validateMixedalphanumeric($str) 
	{
		return preg_match('/^[a-zA-Z0-9]+$/',$str);
	}
}

?>