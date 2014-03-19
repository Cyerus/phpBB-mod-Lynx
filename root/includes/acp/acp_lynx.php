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
class acp_lynx
{
	var $u_action;
	var $new_config = array();

	function main($id, $mode)
	{
		global $db, $user, $auth, $template;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
		global $cache;
		
		$action	= request_var('action', '');
		$submit = (isset($_POST['submit'])) ? true : false;

		$form_key = 'acp_lynx';
		add_form_key($form_key);

		/**
		*	Validation types are:
		*		string, int, bool,
		*		script_path (absolute path in url - beginning with / and no trailing slash),
		*		rpath (relative), rwpath (realtive, writable), path (relative path, but able to escape the root), wpath (writable)
		*/
		switch ($mode)
		{
			case 'teamspeak3':
				$vars = array(
					'legend0'				=> 'GENERAL_SETTINGS',
					'lynx_ts_masterswitch'		=> array('lang' => 'LYNX_TS_M',				'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
					'lynx_ts_ip'				=> array('lang' => 'LYNX_TS_IP',			'validate' => 'string',	'type' => 'text:25:30', 'explain' => true),
					'lynx_ts_username'			=> array('lang' => 'LYNX_TS_USERNAME',		'validate' => 'string',	'type' => 'text:15:20', 'explain' => true),
					'lynx_ts_password'			=> array('lang' => 'LYNX_TS_PASSWORD',		'validate' => 'string',	'type' => 'password:15:20', 'explain' => true),
					'lynx_ts_port_server'		=> array('lang' => 'LYNX_TS_PORT_SERVER',	'validate' => 'int:0',	'type' => 'text:4:5', 'explain' => true),
					'lynx_ts_port_query'		=> array('lang' => 'LYNX_TS_PORT_QUERY',	'validate' => 'int:0',	'type' => 'text:4:5', 'explain' => true),
					'lynx_ts_nickname'			=> array('lang' => 'LYNX_TS_NICKNAME',		'validate' => 'string',	'type' => 'text:15:20', 'explain' => true),

					'legend1'				=> 'LYNX_TS_SPECIAL_GROUPS',
					'lynx_ts_special'			=> array('lang' => 'LYNX_TS_SPECIALGROUPS',	'validate' => 'int:5',	'type' => 'text:4:5', 'explain' => true),
					'lynx_ts_admin_switch'		=> array('lang' => 'LYNX_TS_ADMIN',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
					'lynx_ts_admin_tsgroup'		=> array('lang' => 'LYNX_TS_ADMIN_TSGROUP',	'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
				);

				for($i = 1; $i <= $config['lynx_ts_special']; $i++)
				{
					$vars = array_merge($vars, array(
						'lynx_ts_special_'.$i.'_switch'		=> array('lang' => 'LYNX_TS_SPECIAL',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
						'lynx_ts_special_'.$i.'_tsgroup'	=> array('lang' => 'LYNX_TS_SPECIAL_TSGROUP',	'validate' => 'int:0',	'type' => 'text:10:10', 'explain' => true),
					));
				}

				$vars = array_merge($vars, array(
					'legend2'				=> 'ACP_SUBMIT_CHANGES',
				));

				$display_vars = array(
					'title'	=> 'LYNX_TEAMSPEAK3',
					'vars'	=> $vars
				);

				$vars = array();
			break;
                        
			case 'jabber':
				$vars = array(
					'legend0'				=> 'GENERAL_SETTINGS',
					'lynx_jabber_masterswitch'	=> array('lang' => 'LYNX_JABBER_M',			'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),

					'legend1'				=> 'LYNX_EJABBERD',
					'lynx_ejabberd_switch'		=> array('lang' => 'LYNX_EJABBERD_SWITCH',	'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
					'lynx_ejabberd_code'		=> array('lang' => 'LYNX_EJABBERD_CODE',	'validate' => 'string',	'type' => 'text:13:15', 'explain' => true),

					'legend2'				=> 'LYNX_OPENFIRE',
					'lynx_openfire_switch'		=> array('lang' => 'LYNX_OPENFIRE_SWITCH',	'validate' => 'bool',		'type' => 'radio:yes_no', 'explain' => true),
					'lynx_openfire_host'		=> array('lang' => 'LYNX_OPENFIRE_HOST',	'validate' => 'string',		'type' => 'text:30:35', 'explain' => true),
					'lynx_openfire_port'		=> array('lang' => 'LYNX_OPENFIRE_PORT',	'validate' => 'int:0',		'type' => 'text:10:10', 'explain' => true),
					'lynx_openfire_code'		=> array('lang' => 'LYNX_OPENFIRE_CODE',	'validate' => 'string',		'type' => 'text:30:35', 'explain' => true),
					
					'legend3'				=> 'ACP_SUBMIT_CHANGES',
				);

				$display_vars = array(
					'title'	=> 'LYNX_JABBER',
					'vars'	=> $vars
				);

				$vars = array();
			break;
		
			default:
				trigger_error('NO_MODE', E_USER_ERROR);
			break;
		}

		if (isset($display_vars['lang']))
		{
			$user->add_lang($display_vars['lang']);
		}

		$this->new_config = $config;
		$cfg_array = (isset($_REQUEST['config'])) ? utf8_normalize_nfc(request_var('config', array('' => ''), true)) : $this->new_config;
		$error = array();

		// We validate the complete config if whished
		validate_config_vars($display_vars['vars'], $cfg_array, $error);

		if ($submit && !check_form_key($form_key))
		{
			$error[] = $user->lang['FORM_INVALID'];
		}
		
		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}

		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		foreach ($display_vars['vars'] as $config_name => $null)
		{
			if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
			{
				continue;
			}

			$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

			if ($submit)
			{
				set_config($config_name, $config_value);
			}
		}

		if ($submit)
		{
			add_log('admin', 'LOG_CONFIG_LYNX_' . strtoupper($mode));

			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->tpl_name = 'acp_board';
		$this->page_title = $display_vars['title'];

		$template->assign_vars(array(
			'L_TITLE'			=> $user->lang['LYNX'],
			'L_TITLE_EXPLAIN'	=> $user->lang['LYNX_EXPLAIN'],

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),

			'U_ACTION'			=> $this->u_action)
		);

		// Output relevant page
		foreach ($display_vars['vars'] as $config_key => $vars)
		{
			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
				);

				continue;
			}

			$type = explode(':', $vars['type']);

			$l_explain = '';
			if ($vars['explain'] && isset($vars['lang_explain']))
			{
				$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
			}
			else if ($vars['explain'])
			{
				$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
			}

			$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars);

			if (empty($content))
			{
				continue;
			}

			$template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
				'S_EXPLAIN'		=> $vars['explain'],
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT'		=> $content,
				)
			);

			unset($display_vars['vars'][$config_key]);
		}
	}
}

?>