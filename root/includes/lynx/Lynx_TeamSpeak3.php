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

class TeamSpeak3
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
					// Skip if this person is not part of the group (as we can't delete something that isn't there)
					if(!in_array($currentTSGroup, $tsGroups))
					{
						continue;
					}
					
					// Skip if group is set at 0 (meaning TeamSpeak integration disabled) 
					if($currentTSGroup == 0)
					{
						continue;
					}
					
					// Skip if the group is set as the TeamSpeak admin group, as we don't want 'accidents' to happen
					if($currentTSGroup == $config['lynx_ts_admin_tsgroup'] && $config['lynx_ts_admin_switch']) 
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
					if(!$tsSetAsSpecial)
					{
						// Simple try block just in case a group doesn't exist (which shouldn't be caught by the 'global' try block, as that would stop the other group syncs)
						try 
						{
							// Grab groupInfo
							$groupInfo = $tsVirtualServer->serverGroupGetById($currentTSGroup);

							// Skip if a group is NOT set as a 'permanent' group
							if(!$groupInfo->savedb) 
							{
								continue;
							}

							// Actually remove the user from this group
							$tsVirtualServer->serverGroupClientDel($currentTSGroup, $tsCurrentDbId);
						} 
						catch (TeamSpeak3_Exception $e) 
						{
							Lynx\Log::addToLog($userId, "TeamSpeak 3", $e->getCode(), $e->getMessage());
						}
					}
				}

				// Loop each group the user should be part of
				foreach($tsGroups as $currentTSGroup)
				{
					// Skip if group is set at 0 (meaning TeamSpeak integration disabled) 
					if($currentTSGroup == 0) 
					{
						continue;
					}
					
					// Simple try block just in case a group doesn't exist (which shouldn't be caught by the 'global' try block, as that would stop the other group syncs)
					try 
					{
						// Grab groupInfo
						$groupInfo = $tsVirtualServer->serverGroupGetById($currentTSGroup);

						// Skip if a group is NOT set as a 'permanent' group
						if(!$groupInfo->savedb) 
						{
							continue;
						}
						
						// Check if user is already part of group
						if(!in_array($currentTSGroup, $tsGroupsCurrent))
						{
							// Actually add the user to this group
							$tsVirtualServer->serverGroupClientAdd($currentTSGroup, $tsCurrentDbId);
						}
					} 
					catch (TeamSpeak3_Exception $e) 
					{
						Lynx\Log::addToLog($userId, "TeamSpeak 3", $e->getCode(), $e->getMessage());
					}
				}
			}
			
			return true;
		}
		catch(TeamSpeak3_Exception $e)
		{
			Lynx\Log::addToLog($userId, "TeamSpeak 3", $e->getCode(), $e->getMessage());
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
	
	/**
	* Validate string to only consist of letters and numbers
	*
	* @param string $str String to check.
	* @return 1 for found, 0 for not-found, and FALSE on error.
	*/
	private static function validateMixedalphanumeric($str) 
	{
		return preg_match('/^[a-zA-Z0-9]+$/',$str);
	}
}

?>