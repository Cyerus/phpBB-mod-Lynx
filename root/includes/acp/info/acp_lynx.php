<?php

/**
* @package module_install
*/
class acp_lynx_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_lynx',
			'title'		=> 'ACP_LYNX',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'teamspeak3'		=> array('title' => 'LYNX_TEAMSPEAK3',	'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION')),
				'jabber'			=> array('title' => 'LYNX_JABBER', 		'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION')),
				'cronforce'			=> array('title' => 'LYNX_CRONFORCE', 	'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>