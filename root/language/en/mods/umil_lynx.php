<?php
/**
*
* umil_eveapi [English]
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

// Umil EVEAPI Settings
$lang = array_merge($lang, array(
    'UMIL_EVEAPI'                           => 'EVE API MOD by Cyerus',
    
    'ACP_CAT_EVEAPI'                        => 'EVE API modules',
    'EVEAPI_CORPORATIONS'                   => 'Corporations',
    'EVEAPI_ALLIANCES'                      => 'Alliances',
    'EVEAPI_STANDINGS'                      => 'Standings',
    'EVEAPI_FW'                             => 'Faction Warfare',
    'EVEAPI_TS'                             => 'TeamSpeak 3',
    'EVEAPI_JABBER'                         => 'Jabber',
	'EVEAPI_ACCESSMASK'                     => 'AccessMask',
	
	'UCP_PROFILE_AVATAR'                    => 'UCP - Avatar Settings',
    
    'UMIL_EVEAPI_6_0_0'                     => 'Succesfully (un)installed version 6.0.0',
    'UMIL_EVEAPI_6_0_1'                     => 'Succesfully changed to version 6.0.1',
    'UMIL_EVEAPI_6_0_2'                     => 'Succesfully changed to version 6.0.2',
    'UMIL_EVEAPI_6_0_3'                     => 'Succesfully changed to version 6.0.3',
    'UMIL_EVEAPI_6_1_0'                     => 'Succesfully changed to version 6.1.0',
    'UMIL_EVEAPI_6_1_1'                     => 'Succesfully changed to version 6.1.1',
    'UMIL_EVEAPI_6_1_2'                     => 'Succesfully changed to version 6.1.2',
    'UMIL_EVEAPI_6_2_0'                     => 'Succesfully changed to version 6.2.0',
    'UMIL_EVEAPI_6_2_1'                     => 'Succesfully changed to version 6.2.1',
	'UMIL_EVEAPI_6_2_2'                     => 'Succesfully changed to version 6.2.2',
	'UMIL_EVEAPI_6_2_3'                     => 'Succesfully changed to version 6.2.3',
	'UMIL_EVEAPI_6_2_4'                     => 'Succesfully changed to version 6.2.4',
	'UMIL_EVEAPI_6_3_0'                     => 'Succesfully changed to version 6.3.0',
	'UMIL_EVEAPI_6_3_1'                     => 'Succesfully changed to version 6.3.1',
));

?>
