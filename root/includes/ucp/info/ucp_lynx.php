<?php
/**
*
* @package ucp
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class ucp_lynx_info
{
	function module()
	{
		return array(
			'filename'	=> 'ucp_lynx',
			'title'		=> 'UCP_LYNX',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'details'	=> array('title' => 'UCP_LYNX_DETAILS', 'auth' => '', 'cat' => array('UCP_LYNX'))
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