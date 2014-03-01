<?php
/**
*
* umil_lynx [English]
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

// Umil Lynx Settings
$lang = array_merge($lang, array(
    'UMIL_LYNX'				=> 'Lynx by Cyerus',
    
    'ACP_CAT_LYNX'			=> 'Lynx modules',
    'LYNX_TEAMSPEAK3'		=> 'Lynx » TeamSpeak 3',
    'LYNX_JABBER'			=> 'Lynx » Jabber',
	
	'UCP_LYNX_DETAILS'		=> 'Lynx » Details',
	
	'UMIL_LYNX_1_0_0'		=> 'Succesfully (un)installed version 1.0.0',
));

?>
