<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of articles
*/
interface portal_article_interface
{
	/**
	* Get number of articles
	*
	* @param string	$lang	2-letter language ISO code
	* @param int	$cat_id	Category ID
	* @return int
	*/
	public function count_articles($lang, $cat_id = 0);

	/**
	* Get all articles
	*
	* @return array
	*/
	public function get_articles();

	/**
	* Get articles in range for pagination
	*
	* @param string	$lang			2-letter language ISO code
	* @param int	$cat_id			Category ID
	* @param string	$order_field	Sort by this field
	* @param int	$limit			Number of items
	* @param int	$offset			Position of the start
	* @return array
	*/
	public function list_articles($lang, $cat_id = 0, $order_field = 'item_updated DESC', $limit = 0, $offset = 0);

	/**
	* Get latest items
	*
	* @param int	$bb_type	phpBB resource type
	* @param int	$limit		Number of items
	* @return array
	*/
	public function get_latest_items($bb_type, $limit = 10);

	/**
	* Add an article
	*
	* @param \vinabb\web\entities\portal_article_interface $entity Article entity
	* @return \vinabb\web\entities\portal_article_interface
	*/
	public function add_article($entity);

	/**
	* Delete an article
	*
	* @param int $id Article ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_article($id);
}
