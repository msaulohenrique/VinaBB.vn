<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp\helper;

/**
* Interface for the task helper
*/
interface setting_tasks_interface
{
	/**
	* Radio options for the config item 'maintenance_mode'
	*
	* @return array
	*/
	public function maintenance_mode_type_data();

	/**
	* Check only founders can set the founder-level maintenance mode
	*
	* @param int $value Input value
	* @return bool
	*/
	public function maintenance_mode_founder($value);

	/**
	* Kill out all normal administrators from the ACP
	* keep only founder-level sessions
	*
	* @param int $value Input value
	*/
	public function maintenance_mode_founder_task($value);

	/**
	* Convert the scheduled maintenance time from 'number of minutes' into 'UNIX timestamp'
	*
	* @param int $value Input value
	*/
	public function maintenance_time_task($value);

	/**
	* Reset the stored newest phpBB version if the branch has changed
	*
	* @param string $value Input value
	*/
	public function reset_phpbb_version_task($value);

	/**
	* Reset the stored newest phpBB legacy version if the branch has changed
	*
	* @param string $value Input value
	*/
	public function reset_phpbb_legacy_version_task($value);

	/**
	* Reset the stored newest phpBB development version if the branch has changed
	*
	* @param string $value Input value
	*/
	public function reset_phpbb_dev_version_task($value);

	/**
	* Reset the stored newest PHP version if the branch has changed
	*
	* @param string $value Input value
	*/
	public function reset_php_version_task($value);

	/**
	* Reset the stored newest PHP legacy version if the branch has changed
	*
	* @param string $value Input value
	*/
	public function reset_php_legacy_version_task($value);

	/**
	* Get default language name
	*
	* @return string
	*/
	public function get_default_lang_name();

	/**
	* Select an extra language to switch
	*
	* @param string $selected_lang 2-letter language ISO code
	* @return string HTML code
	*/
	public function build_lang_list($selected_lang);

	/**
	* Select categories (not postable forums)
	*
	* @param int $selected_forum Selected forum ID
	* @return string HTML code
	*/
	public function build_forum_list($selected_forum);
}
