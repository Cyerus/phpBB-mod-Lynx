<?php
/**
*
* ucp [English]
*
* @package language
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

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

// Common language entries
$lang = array_merge($lang, array(
	'UCP_LYNX'					=> 'TeamSpeak 3 and Jabber',
	'UCP_LYNX_DETAILS'			=> 'Edit TeamSpeak 3 settings',
	
	'LOG_LYNX_NEW_TS3UID'		=> 'New TS3UID!',
	'LOG_LYNX_SAME_TS3UID'		=> 'Same TS3UID!',
	
	'LYNX_TS3UID'				=> 'TeamSpeak 3 UID',
	'LYNX_TS3UID_EXPLAIN'		=> 'Enter your TeamSpeak 3 Unique ID.',
));

?>