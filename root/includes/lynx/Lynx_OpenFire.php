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

class OpenFire
{
	/**
	* Sets character's OpenFire permissions
	*/
	public function setOpenFireGroups($userId, $ofGroups, $ofGroupsExtra = array())
	{
		global $db, $config, $phpbb_root_path, $phpEx;

		// Set tsGroups to array (just in case)
		if(!is_array($ofGroups))
		{
			$ofGroups = array();
		}

		// There can be extra groups, combine arrays
		if(!empty($ofGroupsExtra))
		{
			$ofGroups = array_merge($ofGroups, $ofGroupsExtra);

			// Remove duplicates
			if(!empty($ofGroups))
			{
				$ofGroups = array_unique($ofGroups);
			}
		}
		
		// Set new array
		$jabberGroups = array();

		// Loop to remove empty strings (just in case)
		foreach($ofGroups as $currentGroup)
		{
			if(!empty($currentGroup))
			{
				$jabberGroups[] = $currentGroup;
			}
		}

		// Create the OpenFireUserService object.
		$pofus = new OpenFireUserService();

		// Set the required config parameters
		$pofus->secret = $config['lynx_openfire_code'];
		$pofus->host = $config['lynx_openfire_host'];
		$pofus->port = (isset($config['lynx_openfire_port']) && $config['lynx_openfire_port'] > 0) ? (string)$config['lynx_openfire_port'] : "9090";

		// Get characters (forum username)
		$sql = "SELECT username, user_email, user_lang
				FROM " . USERS_TABLE . "
				WHERE user_id = " . $userId;
		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		// Clean username for support with Jabber
		// Changing ' to |
		// and <space> to _
		$ofUsername = str_replace(" ", "_", str_replace("'", "|", $data['username']));

		// Check if we have any groups or not, determines whether or not we should disable the jabber account
		if(!empty($jabberGroups))
		{
			// Now that we have the information we need, let's try to update the jabber groups of the selected user
			$result = $pofus->updateUser($ofUsername, false, false, false, $jabberGroups);

			// Check if we have an initial result
			if($result)
			{
				// We have a result, but check what the actual result type is
				if($result['result'])
				{
					// Command was a success, no further action required
					return true;
				}
				else
				{
					if($result['message'] == 'UserNotFoundException')
					{
						// User doesn't exist yet, time to create the user				
						// Create a random password
						$password = self::getRandomString(8);

						// Create the actual user
						$resultAdd = $pofus->addUser($ofUsername, $password, $data['username'], $data['user_email'], $jabberGroups);

						if($resultAdd && $resultAdd['result'])
						{
							// Email the newly created password to the user
							// Load phpBB's Messenger class
							include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
							$messenger = new messenger(true);

							// Load email text from template
							$messenger->template('lynx_openfire_added', $data['user_lang']);

							// Add recipient
							$messenger->to($data['user_email'], $data['username']);

							// Assign variables using in template
							$messenger->assign_vars(array(
								'USERNAME'	=> htmlspecialchars_encode($ofUsername),
								'PASSWORD'	=> htmlspecialchars_encode($password),
								'HOST'		=> htmlspecialchars_encode($config['lynx_openfire_host'])
							));

							// Send the actual email
							$messenger->send();
							$messenger->save_queue();

							// And we are done
							return true;
						}
						elseif($resultAdd && $resultAdd['result'])
						{
							Lynx\Log::addToLog($userId, "OpenFire", "99", $resultAdd['message']);
						}
					}
					else
					{
						Lynx\Log::addToLog($userId, "OpenFire", "99", $result['message']);
					}
				}
			}
		}
		else
		{
			// No groups found, remove the user from jabber
			$result = $pofus->deleteUser($ofUsername);

			if($result && !$result['result'])
			{
				Lynx\Log::addToLog($userId, "OpenFire", "99", $result['message']);
			}
		}
	}
	
	/*
	* Creates a random string 
	*/
	private function getRandomString($length)
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		return substr(str_shuffle($chars), 0, $length);
	}
}

?>