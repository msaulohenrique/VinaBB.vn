<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\events\helper;

interface helper_interface
{
	/**
	* Display forum list on header
	*/
	public function list_forums();

	/**
	* Generate the category list of all phpBB resourse types
	*/
	public function list_bb_cats();

	/**
	* Language switcher for guests
	*/
	public function add_lang_switcher();

	/**
	* Add checking permissions to template variables
	*/
	public function auth_to_template();

	/**
	* Get value from config items and export to template variables
	*/
	public function config_to_template();

	/**
	* Common template variables
	*/
	public function add_common_tpl_vars();

	/**
	* Add our new links to the header
	*/
	public function add_new_routes();

	/**
	* Maintenance mode by user levels
	*/
	public function maintenance_mode();
}
