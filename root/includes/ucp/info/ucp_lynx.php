<?php

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