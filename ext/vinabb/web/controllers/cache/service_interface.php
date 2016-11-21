<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\cache;

/**
* Interface for the extended cache service
*/
interface service_interface
{
	/**
	* Returns the cache driver used by this cache service.
	*
	* @return \phpbb\cache\driver\driver_interface The cache driver
	*/
	public function get_driver();

	/**
	* Replaces the cache driver used by this cache service.
	*
	* @param \phpbb\cache\driver\driver_interface $driver The cache driver
	*/
	public function set_driver(\phpbb\cache\driver\driver_interface $driver);

	/**
	* Get cache from table: _config_text
	*
	* @return array
	*/
	public function get_config_text();

	/**
	* Clear cache from table: _config_text
	*/
	public function clear_config_text();

	/**
	* Get cache from table: _lang
	*
	* @return array
	*/
	public function get_lang_data();

	/**
	* Clear cache from table: _lang
	*/
	public function clear_lang_data();

	/**
	* Get cache from table: _forums
	*
	* @return array
	*/
	public function get_forum_data();

	/**
	* Clear cache from table: _forums
	*/
	public function clear_forum_data();

	/**
	* Get cache from table: _smilies
	*
	* @return array
	*/
	public function get_smilies();

	/**
	* Clear cache from table: _smilies
	*/
	public function clear_smilies();

	/**
	* Get cache from table: _bb_categories
	*
	* @param int $bb_type phpBB resource type
	* @return array
	*/
	public function get_bb_cats($bb_type);

	/**
	* Clear cache from table: _bb_categories
	*
	* @param int $bb_type phpBB resource type
	*/
	public function clear_bb_cats($bb_type);

	/**
	* Get cache from table: _bb_items
	*
	* @param int $bb_type phpBB resource type
	* @return array
	*/
	public function get_new_bb_items($bb_type);

	/**
	* Clear cache from table: _bb_items
	*
	* @param int $bb_type phpBB resource type
	*/
	public function clear_new_bb_items($bb_type);

	/**
	* Get cache from table: _portal_categories
	*
	* @return array
	*/
	public function get_portal_cats();

	/**
	* Clear cache from table: _portal_categories
	*/
	public function clear_portal_cats();

	/**
	* Get cache from table: _portal_articles
	*
	* @param string $lang 2-letter language ISO code
	* @return array
	*/
	public function get_index_articles($lang);

	/**
	* Clear cache from table: _portal_articles
	*
	* @param string $lang 2-letter language ISO code
	*/
	public function clear_index_articles($lang);

	/**
	* Get comment counter for get_index_articles()
	*
	* @param string	$lang	2-letter language ISO code
	* @return array
	*/
	public function get_index_comment_counter($lang);

	/**
	* Clear comment counter for get_index_articles()
	*
	* @param string $lang 2-letter language ISO code
	*/
	public function clear_index_comment_counter($lang);

	/**
	* Get cache from table: _pages
	*
	* @return array
	*/
	public function get_pages();

	/**
	* Clear cache from table: _pages
	*/
	public function clear_pages();

	/**
	* Get cache from table: _menus
	*
	* @return array
	*/
	public function get_menus();

	/**
	* Clear cache from table: _menus
	*/
	public function clear_menus();

	/**
	* Get cache from table: _headlines
	*
	* @param string $lang 2-letter language ISO code
	* @return array
	*/
	public function get_headlines($lang);

	/**
	* Clear cache from table: _headlines
	*
	* @param string $lang 2-letter language ISO code
	*/
	public function clear_headlines($lang);
}
