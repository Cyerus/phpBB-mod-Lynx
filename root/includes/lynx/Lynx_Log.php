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

class Lynx_Log
{
	public static function addToLog($userId, $appType, $errorId, $errorText)
	{
        global $db, $table_prefix;
		
		$sql = 'INSERT INTO '.$table_prefix.'lynx_log (user_id, app_type, error_id, error_text, error_time)
				VALUES('.$db->sql_escape($userId).', "'.$db->sql_escape($appType).'", '.$db->sql_escape($errorId).', "'.$db->sql_escape($errorText).'", '.time().')';
		$db->sql_query($sql);
		
		self::clearOldEntries();
	}
	
	public static function clearLog()
	{
        global $db, $table_prefix;
		
		$sql = 'DELETE FROM '.$table_prefix.'lynx_log
				WHERE log_id >= 0';
		$db->sql_query($sql);
	}
	
	private static function clearOldEntries()
	{
        global $db, $table_prefix;
		
		$weekAgo = time() - (7 * 24 * 60 * 60);
		
		$sql = 'DELETE FROM '.$table_prefix.'lynx_log
				WHERE error_time < '.$weekAgo;
		$db->sql_query($sql);
	}
}

?>