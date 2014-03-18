<?php

/**
* @ignore
*/
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
define('IN_INSTALL', true);  // Make it able to load from /root/install/ directory.
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/umil_lynx');

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// The name of the mod to be displayed during installation.
$mod_name = 'UMIL_LYNX';

/*
* The name of the config variable which will hold the currently installed version
* UMIL will handle checking, setting, and updating the version itself.
*/
$version_config_name = 'lynx_version';

/*
* The array of versions and actions within each.
* You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
*
* You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
* The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
*/
$versions = array(
	'1.0.0'	=> array(
		'custom'	=> 'umil_lynx_1_0_0',
	),
);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

// <editor-fold defaultstate="collapsed" desc="functions based on version">
/*
 * Functions based on version
 */

function umil_lynx_1_0_0($action, $version)
{
    global $db, $table_prefix, $umil;

    if ($action == 'uninstall')
    {
		/*
		 * Modifications to the Users table (TeamSpeak 3 UID)
		 * ==================================================
		 */

        // Remove old backup table is exists
		if ($umil->table_exists($table_prefix . 'lynx_users_tmp'))
        {
            $umil->table_remove($table_prefix . 'lynx_users_tmp');
        }

        // Create a new backup table
		$umil->table_add($table_prefix . 'lynx_users_tmp', array(
            'COLUMNS'	=> array(
                'user_id'		=> array('UINT:10', 0),
                'lynx_ts3uid'	=> array('VCHAR:28', ''),
            ),
            'PRIMARY_KEY'    => 'user_id',
        ));

		// Insert TeamSpeak 3 UIDs into newly created backup table
		$sql = "INSERT INTO ".$table_prefix."lynx_users_tmp (user_id, lynx_ts3uid) 
        SELECT user_id, lynx_ts3uid
        FROM " . USERS_TABLE;
        $db->sql_query($sql);

        // Remove the TeamSpeak 3 UID column from the Users table
		$umil->table_column_remove(USERS_TABLE, 'lynx_ts3uid');
		
		
		/*
		 * Modifications to the Groups table (TeamSpeak 3, ejabberd and OpenFire)
		 * ======================================================================
		 */
		
		// Remove old backup table is exists
        if ($umil->table_exists($table_prefix . 'lynx_groups_tmp'))
        {
            $umil->table_remove($table_prefix . 'lynx_groups_tmp');
        }

		// Create a new backup table
        $umil->table_add($table_prefix . 'lynx_groups_tmp', array(
            'COLUMNS'	=> array(
                'group_id'				=> array('UINT:8', 0),
                'group_lynx_ts3'		=> array('UINT:8', 0),
                'group_lynx_ejabberd'	=> array('TINT:1', 0),
				'group_lynx_openfire'	=> array('VCHAR:20', ''),
            ),
            'PRIMARY_KEY'	=> 'group_id',
        ));

		// Insert TeamSpeak 3, ejabberd and OpenFire group settings into newly created backup table
		$sql = "INSERT INTO ".$table_prefix."lynx_groups_tmp (group_id, group_lynx_ts3, group_lynx_ejabberd, group_lynx_openfire) 
				SELECT group_id, group_lynx_ts3, group_lynx_ejabberd, group_lynx_openfire
				FROM " . GROUPS_TABLE;
		$db->sql_query($sql);
        
		// Remove the TeamSpeak 3, ejabberd and OpenFire columns from the Groups table
		$umil->table_column_remove(GROUPS_TABLE, 'group_lynx_ts3');
		$umil->table_column_remove(GROUPS_TABLE, 'group_lynx_ejabberd');
		$umil->table_column_remove(GROUPS_TABLE, 'group_lynx_openfire');

        
		/*
		 * Removing the error logging table
		 * ===================================================================
		 */
		
		// Remove old backup table is exists
        if ($umil->table_exists($table_prefix . 'lynx_log'))
        {
            $umil->table_remove($table_prefix . 'lynx_log');
        }
		
		
		/*
		 * Modifications to the ACP (Module management -> TeamSpeak 3 / Jabber)
		 * ====================================================================
		 */

        // Check if module category exists before attempting delete
        if($umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_LYNX'))
        {
            $umil->module_remove('acp', 'ACP_CAT_LYNX', array(
                'module_basename'   => 'lynx',
                'modes'             => array('teamspeak3', 'jabber'),
            ));
			
            $umil->module_remove('acp', 'ACP_CAT_LYNX', array(
                'module_basename'   => 'lynxlog',
                'modes'             => array('log'),
            ));
            
            $umil->module_remove('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_LYNX');
        }
		
		
		/*
		 * Modifications to the UCP (Module management -> TeamSpeak 3 UID)
		 * ===============================================================
		 */
        if($umil->module_exists('ucp', 'UCP_PROFILE', 'UCP_LYNX_DETAILS'))
        {
            $umil->module_remove('ucp', 'UCP_PROFILE', array(
                'module_basename'   => 'lynx',
                'modes'             => array('details'),
            ));
        }
    }

    if ($action == 'install')
    {
		/*
		 * Modifications to the Users table (TeamSpeak 3 UID)
		 * ==================================================
		 */
        
		// Add column to Users table if not exist
		if (!$umil->table_column_exists(USERS_TABLE, 'lynx_ts3uid'))
        {
            $umil->table_column_add(USERS_TABLE, 'lynx_ts3uid', array('VCHAR:28', ''));
        }

        // Fill in TeamSpeak 3 UID if backup table exist
        if($umil->table_exists($table_prefix . 'lynx_users_tmp'))
        {
            $sql = "SELECT user_id, lynx_ts3uid
                    FROM ".$table_prefix."lynx_users_tmp
                    ORDER BY user_id";
            $result = $db->sql_query($sql);

            while ($t_row = $db->sql_fetchrow($result))
            {
				$sql_in = "UPDATE " . USERS_TABLE . "
				SET lynx_ts3uid = \"" . $t_row['lynx_ts3uid'] . "\"
				WHERE user_id = " . $t_row['user_id'];
				$db->sql_query($sql_in);
            }
            $db->sql_freeresult($result);

            // Remove the backup table
			$umil->table_remove($table_prefix . 'lynx_users_tmp');
        }
		
		
		/*
		 * Modifications to the Groups table (TeamSpeak 3, ejabberd and Openfire)
		 * ======================================================================
		 */
		
		// Add TeamSpeak 3 column to Groups table if not exist
		if (!$umil->table_column_exists(GROUPS_TABLE, 'group_lynx_ts3'))
		{
			$umil->table_column_add(GROUPS_TABLE, 'group_lynx_ts3', array('UINT:8', 0));
		}

		// Add ejabberd column to Groups table if not exist
		if (!$umil->table_column_exists(GROUPS_TABLE, 'group_lynx_ejabberd'))
		{
			$umil->table_column_add(GROUPS_TABLE, 'group_lynx_ejabberd', array('TINT:1', 0));
		}

		// Add OpenFire column to Groups table if not exist
		if (!$umil->table_column_exists(GROUPS_TABLE, 'group_lynx_openfire'))
		{
			$umil->table_column_add(GROUPS_TABLE, 'group_lynx_openfire', array('VCHAR:20', ""));
		}

		// Fill in TeamSpeak 3, ejabberd and OpenFire settings if backup table exist
		if($umil->table_exists($table_prefix . 'lynx_groups_tmp'))
		{
			$sql = "SELECT group_id, group_lynx_ts3, group_lynx_ejabberd, group_lynx_openfire
					FROM ".$table_prefix."lynx_groups_tmp
					ORDER BY group_id";
			$result = $db->sql_query($sql);

			while ($g_row = $db->sql_fetchrow($result))
			{
				$sql_in = "UPDATE " . GROUPS_TABLE . "
				SET group_lynx_ts3 = " . $g_row['group_lynx_ts3'] . ", group_lynx_ejabberd = " . $g_row['group_lynx_ejabberd'] . ", group_lynx_openfire = '" . $g_row['group_lynx_openfire'] . "'
				WHERE group_id = " . $g_row['group_id'];
				$db->sql_query($sql_in);
			}
			$db->sql_freeresult($result);

			$umil->table_remove($table_prefix . 'lynx_groups_tmp');
		}

		
		/*
		 * Adding the error logging table
		 * ===================================================================
		 */
		
		// Remove old backup table is exists
        if ($umil->table_exists($table_prefix . 'lynx_log'))
        {
            $umil->table_remove($table_prefix . 'lynx_log');
        }
		
		// Create a new error log table
        $umil->table_add($table_prefix . 'lynx_log', array(
            'COLUMNS'	=> array(
                'log_id'				=> array('UINT', NULL, 'auto_increment'),
				'user_id'				=> array('UINT:8', 0),
				'app_type'				=> array('VCHAR:30', ''),
                'error_id'				=> array('UINT:8', 0),
                'error_text'			=> array('VCHAR:200', ''),
				'error_time'			=> array('TIMESTAMP', 0),
            ),
            'PRIMARY_KEY'	=> 'log_id',
        ));
		
        
		/*
		 * Modifications to the ACP (Module management -> TeamSpeak 3 / Jabber / LynxLog)
		 * ==============================================================================
		 */
		
		// Add the module if the module does not exist
        if(!$umil->module_exists('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_LYNX'))
        {
            $umil->module_add(array(
                // Add a new category named ACP_CAT_LYNX to ACP_CAT_GENERAL
                array('acp', 'ACP_CAT_GENERAL', 'ACP_CAT_LYNX'),

                // Add the settings and features modes from the acp_lynx module to the ACP_CAT_LYNX category using the "automatic" method.
                array('acp', 'ACP_CAT_LYNX', array(
                        'module_basename'       => 'lynx',
                        'modes'                 => array('teamspeak3', 'jabber'),
                    ),
                ),
				
                // Add the settings and features modes from the acp_lynxlog module to the ACP_CAT_LYNX category using the "automatic" method.
                array('acp', 'ACP_CAT_LYNX', array(
                        'module_basename'       => 'lynxlog',
                        'modes'                 => array('log'),
                    ),
                ),
            ));
        }
        
		
		/*
		 * Modifications to the UCP (Module management -> TeamSpeak 3 UID)
		 * ===============================================================
		 */
		
		// Add the module if the module does not exist
        if(!$umil->module_exists('ucp', 'UCP_PROFILE', 'UCP_LYNX_DETAILS'))
        {
            $umil->module_add(array(
                // Add the settings and features modes from the ucp_lynx module to the UCP_PROFILE category using the "automatic" method.
                array('ucp', 'UCP_PROFILE', array(
                        'module_basename'       => 'lynx',
                        'modes'                 => array('details'),
                    ),
                ),
            ));
        }
    }

    $umil->cache_purge();

    return 'UMIL_LYNX_1_0_0';
}
// </editor-fold>

?>
