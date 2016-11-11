<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

/**
* Interface for the settings_module
*/
interface settings_interface
{
	/**
	* Set form action URL
	*
	* @param string $u_action Form action
	*/
	public function set_form_action($u_action);

	/**
	* Display main settings
	*/
	public function display_main_settings();

	/**
	* List of main setting items
	*
	* @return array
	*/
	public function list_main_settings();

	/**
	* Display main settings
	*/
	public function display_version_settings();

	/**
	* List of version setting items
	*
	* @return array
	*/
	public function list_version_settings();

	/**
	* Display main settings
	*/
	public function display_setup_settings();

	/**
	* List of setup setting items
	*
	* @return array
	*/
	public function list_setup_settings();
}
