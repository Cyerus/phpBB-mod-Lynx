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
						'lynx_ts3uid'	=> array('string', true, 28, 28),
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

							// As we have more than one TeamSpeak 3 operation below, we need to make a connection to the TS Virtual Server here to avoid "nickname in use" errors
							try
							{
								// Set custom nickname for serverquery client
								$tsNickname = (Lynx_TeamSpeak3::validateMixedalphanumeric($tsNickname) != 1) ? "Cyerus" : $config['lynx_ts_nickname'];

								$tsVirtualServer = TeamSpeak3::factory("serverquery://" . $config['lynx_ts_username'] . ":" . $config['lynx_ts_password'] . "@" . $config['lynx_ts_ip'] . ":" . $config['lynx_ts_port_query'] . "/?server_port=" . $config['lynx_ts_port_server'] . "&nickname=" . $tsNickname);

								// Since the TeamSpeak 3 UID has changed, lets remove all permissions from the old TS UID
								Lynx_TeamSpeak3::setTeamSpeakAccess($user->data['user_id'], $user->data['lynx_ts3uid'], array(), $tsVirtualServer);

								
								// Set checkTS variable to true, so that when function is not called it will be true
								$checkTS = true;
								
								// Only try to set permissions to new TS UID when new TS UID is not empty
								if(!empty($data['lynx_ts3uid']))
								{
									// Add permissions to the new TeamSpeak 3 UID
									$checkTS = Lynx_Main::setUserAccess($user->data['user_id'], $data['lynx_ts3uid'], $tsVirtualServer);
								}
								
								// Check if the new TS UID correctly updated, or save the new TS UID when it's empty
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
							catch (TeamSpeak3_Exception $e)
							{
								Lynx_Log::addToLog($user->data['user_id'], "TeamSpeak 3", $e->getCode(), $e->getMessage());
							}
						}
						elseif(!empty($user->data['lynx_ts3uid']))
						{
							Lynx_Main::setUserAccess($user->data['user_id'], $user->data['lynx_ts3uid']);
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