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
	* Create clean URLs from titles. It works with many languages
	*
	* @author hello@weblap.ro
	* @param string $text Input text
	* @return mixed
	*/
	public function clean_url($text);

	/**
	* Convert BB type from string to constant value
	*
	* @param string $bb_type phpBB resource type (ext|style|acp_style|lang|tool)
	* @return int
	*/
	public function get_bb_type_constants($bb_type);

	/**
	* Convert BB type from string to URL varnames
	*
	* @param string $bb_type phpBB resource type (ext|style|acp_style|lang|tool)
	* @return string
	*/
	public function get_bb_type_varnames($bb_type);

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
	* List phpBB resource items with pagination
	*
	* @param int	$bb_type	phpBB resource type in constant value
	* @param int	$cat_id		Category ID
	* @param array	$items		Array of items
	* @param int	$item_count	Number of items
	* @param int	$limit		Items per page
	* @param int	$offset		Position of the start
	*
	* @return int Position of the start
	*/
	public function list_bb_items($bb_type, $cat_id = 0, &$items, &$item_count, $limit = 0, $offset = 0);

	/**
	* List news articles with pagination
	*
	* @param int	$cat_id			Category ID
	* @param array	$articles		Array of articles
	* @param int	$article_count	Number of articles
	* @param int	$limit			Articles per page
	* @param int	$offset			Position of the start
	*
	* @return int Position of the start
	*/
	public function list_articles($cat_id = 0, &$articles, &$article_count, $limit = 0, $offset = 0);
}
