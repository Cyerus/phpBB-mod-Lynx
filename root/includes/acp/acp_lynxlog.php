<?php

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/
class acp_lynxlog
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $table_prefix, $template;
		
		$submit = (isset($_POST['submit'])) ? true : false;

		$form_key = 'acp_lynxlog';
		add_form_key($form_key);

		/**
		*	Validation types are:
		*		string, int, bool,
		*		script_path (absolute path in url - beginning with / and no trailing slash),
		*		rpath (relative), rwpath (realtive, writable), path (relative path, but able to escape the root), wpath (writable)
		*/
		switch ($mode)
		{
			case 'log':
				$sql = "SELECT	".USERS_TABLE.".username AS username,
								".$table_prefix."lynx_log.app_type AS app_type, 
								".$table_prefix."lynx_log.error_id AS error_id, 
								".$table_prefix."lynx_log.error_text AS error_text, 
								".$table_prefix."lynx_log.error_time AS error_time
						FROM ".$table_prefix."lynx_log
						INNER JOIN ".USERS_TABLE."
							ON ".USERS_TABLE.".user_id = ".$table_prefix."lynx_log.user_id
						ORDER BY ".$table_prefix."lynx_log.error_time DESC, ".USERS_TABLE.".username ASC";
				$result = $db->sql_query($sql);

				while ($t_row = $db->sql_fetchrow($result))
				{
					$template->assign_block_vars('lynxlog', array(
						'USERNAME'	=> htmlspecialchars($t_row['username']),
						'APPTYPE'	=> htmlspecialchars($t_row['app_type']),
						'ERRORID'	=> $t_row['error_id'],
						'ERRORTEXT'	=> htmlspecialchars($t_row['error_text']),
						'ERRORTIME'	=> date('d-m-Y H:i:s', $t_row['error_time']),
					));
				}
				$db->sql_freeresult($result);
			break;
                        
			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}
		
		$error = array();

		if ($submit && !check_form_key($form_key))
		{
			$error[] = $user->lang['FORM_INVALID'];
		}
		
		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}

		if ($submit)
		{
			Lynx\Log::clearLog();
			
			add_log('admin', 'LOG_CONFIG_LYNXLOG_' . strtoupper($mode));

			trigger_error($user->lang['LYNXLOG_CLEARED_LOG'] . adm_back_link($this->u_action));
		}

		$this->tpl_name = 'acp_lynxlog';
		$this->page_title = 'LYNXLOG_LOG';

		$template->assign_vars(array(
			'L_TITLE'			=> $user->lang['LYNXLOG'],
			'L_TITLE_EXPLAIN'	=> $user->lang['LYNXLOG_EXPLAIN'],

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),

			'U_ACTION'			=> $this->u_action)
		);

	}
}

?>