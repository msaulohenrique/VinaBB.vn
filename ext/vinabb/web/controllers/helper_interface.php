<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers;

interface helper_interface
{
	/**
	* List of stable phpBB versions
	*
	* @return array
	*/
	public function get_phpbb_versions();

	/**
	* List of font icons
	*
	* @return array
	*/
	public function get_icons();

	/**
	* Generate the icon selection drop-down
	*
	* @param string $selected_icon Selected icon string value
	* @return string HTML code
	*/
	public function build_icon_list($selected_icon);

	/**
	* Generate the language selection drop-down
	*
	* @param string $selected_lang 2-letter language ISO code
	* @return string HTML code
	*/
	public function build_lang_list($selected_lang);

	/**
	* Generate the OS selection drop-down
	*
	* @param int $selected_os OS constant value
	* @return string HTML code
	*/
	public function build_os_list($selected_os);

	/**
	* Create clean URLs from titles. It works with many languages
	*
	* @author hello@weblap.ro
	* @param string $text Input text
	* @return string
	*/
	public function clean_url($text);

	/**
	* Remove all accents
	*
	* @param string $text Input text
	* @return string Result text
	*/
	public function remove_accents($text = '');

	/**
	* Convert BB type from string to constant value
	*
	* @param string $bb_type phpBB resource type (ext|style|acp_style|lang|tool)
	* @return int
	*/
	public function get_bb_type_constants($bb_type);

	/**
	* Convert BB type from constant value to string
	*
	* @param int $bb_type phpBB resource type constant value
	* @return string
	*/
	public function convert_bb_type_constants($bb_type);

	/**
	* Convert BB type from string to URL varnames
	*
	* @param string $bb_mode phpBB resource mode (ext|style|acp_style|lang|tool)
	* @return string
	*/
	public function get_bb_type_varnames($bb_mode);

	/**
	* Convert BB types from URL varnames to mode names
	* Example: For ACP styles, URL varname is 'acp-styles' but standard varname is 'acp_style'
	*
	* @param string $varname phpBB resource type URL varname
	* @return string
	*/
	public function convert_bb_type_varnames($varname);

	/**
	* Get OS name from constants
	*
	* @param int $os_value OS constant value
	*
	* @return string
	*/
	public function get_os_name($os_value);

	/**
	* Adding items to the breadcrumb
	*
	* @param string	$name	Name of item
	* @param string	$url	URL of item
	*/
	public function set_breadcrumb($name, $url = '');

	/**
	* Fetch content from an URL
	*
	* @param string $url URL
	* @return string Raw value
	*/
	public function fetch_url($url);

	/**
	* Group legend for online users
	*
	* @return string
	*/
	public function get_group_legend();

	/**
	* Enable SCEditor and load data for the smiley list
	*/
	public function load_sceditor();

	/**
	* Remove trailing slash in destination path
	*
	* @param string $destination Destination path
	* @return string
	*/
	public function remove_trailing_slash($destination);

	/**
	* Call the core function get_username_string() with only $user_id
	*
	* @param int	$user_id	User ID
	* @param string	$mode		Output mode
	* @return string
	*/
	public function get_username_string($user_id, $mode = 'full');

	/**
	* Build gravatar URL for output on page
	*
	* @param array $row User data or group data that has been cleaned with
	*        \phpbb\avatar\manager::clean_row
	* @return string Gravatar URL
	*/
	public function get_gravatar_url($row);
}
