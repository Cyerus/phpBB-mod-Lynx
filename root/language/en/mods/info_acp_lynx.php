<?php

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

// EVEAPI Settings
$lang = array_merge($lang, array(
	'LYNX'								=> 'Lynx',
	'LYNX_EXPLAIN'						=> 'Settings for TeamSpeak 3 and Jabber integration.',
	
	'ACP_CAT_LYNX'						=> 'Lynx modules',
	'LYNX_TEAMSPEAK3'					=> 'TeamSpeak 3',
	'LYNX_JABBER'						=> 'Jabber',
	'LYNXLOG_LOG'						=> 'Log',
	
	'ACP_LYNX_TEAMSPEAK3'				=> 'TeamSpeak 3',
	'ACP_LYNX_TEAMSPEAK3_EXPLAIN'		=> 'Enable integration of this TeamSpeak 3 group when an user is part of this forumgroup.',
	'ACP_LYNX_EJABBERD'					=> 'ejabberd',
	'ACP_LYNX_EJABBERD_EXPLAIN'			=> 'Enable ejabberd access for users part of this forumgroup.',
	'ACP_LYNX_OPENFIRE'					=> 'OpenFire',
	'ACP_LYNX_OPENFIRE_EXPLAIN'			=> 'Set OpenFire group for users part of this forumgroup.',
	
	'LOG_CONFIG_LYNX_TEAMSPEAK3'		=> '<strong>Altered Lynx TeamSpeak 3 settings</strong>',
	'LOG_CONFIG_LYNX_JABBER'			=> '<strong>Altered Lynx Jabber settings</strong>',
	
	'LYNX_TS3UID'						=> 'TeamSpeak 3 UID',
	'LYNX_TS3UID_EXPLAIN'				=> 'Enter your TeamSpeak 3 Unique ID.',
));

$lang = array_merge($lang, array(
	'LYNX_TS_M'							=> 'Enable TeamSpeak 3 management',
	'LYNX_TS_M_EXPLAIN'					=> 'Masterswitch to enable TeamSpeak 3 management (cronjob and special TeamSpeak registration form).<br />Be sure to have this webserver added to the TeamSpeak 3 server whitelist!',
	'LYNX_TS_IP'						=> 'Server IP',
	'LYNX_TS_IP_EXPLAIN'				=> 'IP address of the TeamSpeak server.',
	'LYNX_TS_USERNAME'					=> 'Serverquery username',
	'LYNX_TS_USERNAME_EXPLAIN'			=> 'Username to access the serverquery command prompt.',
	'LYNX_TS_PASSWORD'					=> 'Serverquery password',
	'LYNX_TS_PASSWORD_EXPLAIN'			=> 'Password to access the serverquery command prompt.',
	'LYNX_TS_PORT_SERVER'				=> 'Virtual server port',
	'LYNX_TS_PORT_SERVER_EXPLAIN'		=> 'Port used to connect to the virtual server.<br />Default: 9987',
	'LYNX_TS_PORT_QUERY'				=> 'Serverquery port',
	'LYNX_TS_PORT_QUERY_EXPLAIN'		=> 'Port used to allow remote server commands.<br />Default: 10011',
	'LYNX_TS_NICKNAME'					=> 'Serverquery nickname',
	'LYNX_TS_NICKNAME_EXPLAIN'			=> 'This nickname will be used mask the webserver.',
    
	'LYNX_TS_SPECIAL_GROUPS'			=> 'Special groups',
	'LYNX_TS_SPECIALGROUPS'				=> 'Special TeamSpeak-groups',
	'LYNX_TS_SPECIALGROUPS_EXPLAIN'		=> 'Amount of \'Special\' TeamSpeak-groups shown below.',
	'LYNX_TS_ADMIN'						=> '&#34;Admin&#34;-group immune?',
	'LYNX_TS_ADMIN_EXPLAIN'				=> 'This options enables immunity for the &#34;Admin&#34;-group.',
	'LYNX_TS_ADMIN_TSGROUP'				=> '&#34;Admin&#34;-group',
	'LYNX_TS_ADMIN_TSGROUP_EXPLAIN'		=> 'The TeamSpeak &#34;Admin&#34;-group identified by an integer.',
	'LYNX_TS_SPECIAL'					=> '&#34;Special&#34;-group immune?',
	'LYNX_TS_SPECIAL_EXPLAIN'			=> 'This options enables immunity for this &#34;Special&#34;-group.',
	'LYNX_TS_SPECIAL_TSGROUP'			=> '&#34;Special&#34;-group',
	'LYNX_TS_SPECIAL_TSGROUP_EXPLAIN'	=> 'A TeamSpeak &#34;Special&#34;-group identified by an integer.',
));

$lang = array_merge($lang, array(
	'LYNX_JABBER_M'						=> 'Enable Jabber management',
	'LYNX_JABBER_M_EXPLAIN'				=> 'Masterswitch to enable Jabber management.',	
	
	'LYNX_EJABBERD'						=> 'ejabberd',
	'LYNX_EJABBERD_SWITCH'				=> 'Enable ejabberd management',
	'LYNX_EJABBERD_SWITCH_EXPLAIN'		=> 'Switch to enable ejabberd external authentication using the forums userbase.',
	'LYNX_EJABBERD_CODE'				=> 'Authentication code',
	'LYNX_EJABBERD_CODE_EXPLAIN'		=> 'This code is used to verify incoming requests to avoid spamming and hacking.',
	
	'LYNX_OPENFIRE'						=> 'OpenFire UserService',
	'LYNX_OPENFIRE_SWITCH'				=> 'Enable OpenFire management',
	'LYNX_OPENFIRE_SWITCH_EXPLAIN'		=> 'Switch to enable OpenFire management.',
	'LYNX_OPENFIRE_HOST'				=> 'Host',
	'LYNX_OPENFIRE_HOST_EXPLAIN'		=> 'Host of which the OpenFire server is running on.<br />Example: jabber.example.com',
	'LYNX_OPENFIRE_PORT'				=> 'Port',
	'LYNX_OPENFIRE_PORT_EXPLAIN'		=> 'Port to connect to the OpenFire admin console.<br />Default: 9090',
	'LYNX_OPENFIRE_CODE'				=> 'Shared secret key',
	'LYNX_OPENFIRE_CODE_EXPLAIN'		=> 'This secret key is required to allow for external user management.',
));

?>