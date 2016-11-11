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
	* Display main settings
	*/
	public function display_main_settings();

	/**
	* Save main settings
	*/
	public function set_main_settings();

	/**
	* Display main settings
	*/
	public function display_version_settings();

	/**
	* Save main settings
	*/
	public function set_version_settings();

	/**
	* Display main settings
	*/
	public function display_setup_settings();

	/**
	* Save main settings
	*/
	public function set_setup_settings();
}
