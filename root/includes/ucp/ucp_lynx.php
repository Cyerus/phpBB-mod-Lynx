<?php

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* ucp_ts3jabber
* Changing TeamSpeak UID
*/
class ucp_lynx
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $phpbb_root_path, $phpEx;

		$user->add_lang('posting');

		$submit		= (!empty($_POST['submit'])) ? true : false;
		$error = $data = array();
		$s_hidden_fields = '';

		switch ($mode)
		{
			case 'details':
				
				$data = array(
					'lynx_ts3uid' => utf8_normalize_nfc(request_var('lynx_ts3uid', $user->data['lynx_ts3uid'], true))
				);

				add_form_key('ucp_lynx_details');

				if ($submit)
				{
					// Do not check cur_password, it is the old one.
					$check_ary = array(
						'lynx_ts3uid'	=> array('string', true, 0, 20),
					);
					
					$error = validate_data($data, $check_ary);
					
					if (!check_form_key('ucp_lynx_details'))
					{
						$error[] = 'FORM_INVALID';
					}
					
					if (!sizeof($error))
					{
						if($data['lynx_ts3uid'] != $user->data['lynx_ts3uid'])
						{
							add_log('user', $user->data['user_id'], 'LOG_LYNX_UPDATED_TS3UID', $user->data['lynx_ts3uid']);

							// Since the TeamSpeak 3 UID has changed, lets remove all permissions from the old TS UID
							Lynx\TeamSpeak3::setTeamSpeakAccess($user->data['user_id'], $user->data['lynx_ts3uid'], array());
							
							// Add permissions to the new TeamSpeak 3 UID
							$checkTS = Lynx\Main::setUserAccess($user->data['user_id'], $data['lynx_ts3uid']);
							
							if($checkTS)
							{
								$sql_ary = array(
									'lynx_ts3uid'	=> $data['lynx_ts3uid'],
								);

								$sql = 'UPDATE ' . USERS_TABLE . '
									SET ' . $db->sql_build_array('UPDATE', $sql_ary) . '
									WHERE user_id = ' . $user->data['user_id'];
								$db->sql_query($sql);
							}
						}
						else
						{
							Lynx\Main::setUserAccess($user->data['user_id'], $user->data['lynx_ts3uid']);
						}
						
						$message = $user->lang['PROFILE_UPDATED'] . '<br /><br />' . sprintf($user->lang['RETURN_UCP'], '<a href="' . $this->u_action . '">', '</a>');
						
						trigger_error($message);
					}
					
					// Replace "error" strings with their real, localised form
					$error = preg_replace('#^([A-Z_]+)$#e', "(!empty(\$user->lang['\\1'])) ? \$user->lang['\\1'] : '\\1'", $error);
				}

				$template->assign_vars(array(
					'ERROR'			=> (sizeof($error)) ? implode('<br />', $error) : '',
					
					'LYNX_TS3UID'	=> $data['lynx_ts3uid'],
				));

			break;
		}

		$template->assign_vars(array(
			'L_TITLE'	=> $user->lang['UCP_LYNX_' . strtoupper($mode)],

			'S_HIDDEN_FIELDS'	=> $s_hidden_fields,
			'S_UCP_ACTION'		=> $this->u_action)
		);

		// Set desired template
		$this->tpl_name = 'ucp_lynx_' . $mode;
		$this->page_title = 'UCP_LYNX_' . strtoupper($mode);
	}
}

?>