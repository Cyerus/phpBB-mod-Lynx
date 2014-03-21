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

class Lynx_Ejabberd
{
	/**
	* Sets character's ejabberd permissions
	*/
	public static function setEjabberdAccess($userId, $ejAccess = false, $ejAccessExtra = false)
	{
		global $db, $config, $table_prefix;
		
		// Check if ejabberd integration is enabled
		if(!$config['lynx_jabber_masterswitch'] || !$config['lynx_ejabberd_switch'])
		{
			return false;
		}
		
		// Check if the user is suppose to have access to ejabberd
		if($ejAccess || $ejAccessExtra)
		{
			// Check if user already has access
			$ejabberdAccess = self::getEjabberdAccess($userId);

			// If ejabberd access has not yet been set, do it now.
			if(!$ejabberdAccess)
			{
				$sql = 'INSERT INTO '.$table_prefix.'lynx_ejabberd ' . $db->sql_build_array('INSERT', array(
					'user_id' => $userId
				));
				$db->sql_query($sql);
			}
		}
		else
		{
			// Remove ejabberd access
			$sql = 'DELETE FROM '.$table_prefix.'lynx_ejabberd
					WHERE user_id = ' . $userId;
			$db->sql_query($sql);
		}
	}
	
	private static function getEjabberdAccess($userId)
	{
		global $db, $table_prefix;
		
		$sql = 'SELECT user_id
				FROM '.$table_prefix.'lynx_ejabberd
				WHERE user_id = ' . $userId;
		$result = $db->sql_query($sql);
		$data = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		if(isset($data['user_id']))
		{
			return true;
		}
		
		return false;
	}
}

?>