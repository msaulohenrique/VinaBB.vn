<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\events;

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
	* Add our new links to the header
	*/
	public function add_new_routes();

	/**
	* Maintenance mode by user levels
	*/
	public function maintenance_mode();

	/**
	* Render MediaEmbed markup tags when displaying text
	*
	* https://github.com/s9e/phpbb-ext-mediaembed
	* @copyright Copyright (c) 2014-2016 The s9e Authors
	*
	* @param $text
	* @return mixed
	*/
	public function render($text);

	/**
	* Insert MediaEmbed markup tags when saving text
	*
	* https://github.com/s9e/phpbb-ext-mediaembed
	* @copyright Copyright (c) 2014-2016 The s9e Authors
	*
	* @param $text
	* @return mixed
	*/
	public function parse($text);

	/**
	* Remove MediaEmbed markup tags when editing text
	*
	* https://github.com/s9e/phpbb-ext-mediaembed
	* @copyright Copyright (c) 2014-2016 The s9e Authors
	*
	* @param $text
	* @return mixed
	*/
	public function unparse($text);
}
