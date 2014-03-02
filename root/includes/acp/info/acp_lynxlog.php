<?php
/**
*
* @package acp
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class acp_lynxlog_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_lynxlog',
			'title'		=> 'ACP_LYNXLOG',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'log'		=> array('title' => 'LYNXLOG_LOG',	'auth' => 'acl_a_board', 'cat' => array('ACP_BOARD_CONFIGURATION')),
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