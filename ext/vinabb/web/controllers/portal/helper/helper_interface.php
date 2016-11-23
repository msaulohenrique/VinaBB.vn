<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\portal\helper;

/**
* Interface for the portal helper
*/
interface helper_interface
{
	/**
	* Mark notifications as read
	*/
	public function mark_read_notifications();

	/**
	* Check all of new versions
	*/
	public function check_new_versions();

	/**
	* Get and set latest phpBB versions
	*/
	public function fetch_phpbb_version();

	/**
	* Get and set latest PHP versions
	*/
	public function fetch_php_version();

	/**
	* Get and set latest VinaBB.vn version
	*/
	public function fetch_vinabb_version();

	/**
	* Get all of news categories
	*
	* @param string $block_name Twig loop name
	*/
	public function get_portal_cats($block_name = 'portal_cats');

	/**
	* Get latest articles on index page
	*
	* @param string $block_name Twig loop name
	*/
	public function get_latest_articles($block_name = 'latest_articles');

	/**
	* Get latest phpBB resource items
	*
	* @param string $block_name_prefix	Prefix of Twig loop name
	* @param string $block_name_suffix	Suffix of Twig loop name
	*/
	public function get_latest_bb_items($block_name_prefix = 'bb_new_', $block_name_suffix = 's');

	/**
	* Get latest topics
	*
	* @param string $block_name Twig loop name
	*/
	public function get_latest_topics($block_name = 'latest_topics');

	/**
	* Get latest reply posts (Not the first post in each topic)
	*
	* @param string $block_name Twig loop name
	*/
	public function get_latest_posts($block_name = 'latest_posts');

	/**
	* Get latest members
	*
	* @param string $block_name Twig loop name
	*/
	public function get_latest_users($block_name = 'latest_users');

	/**
	* Generate template variables for latest version blocks
	*/
	public function get_version_tpl();

	/**
	* Generate template variables for the donation block
	*/
	public function get_donate_tpl();

	/**
	* Get birthday list
	*
	* @return array
	*/
	public function get_birthdays();
}
