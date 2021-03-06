<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of phpBB resource items
*/
interface bb_item_interface
{
	/**
	* Get number of items
	*
	* @param int	$bb_type	phpBB resource type
	* @param int	$cat_id		Category ID
	* @param int	$author_id	Author ID
	* @return int
	*/
	public function count_items($bb_type, $cat_id = 0, $author_id = 0);

	/**
	* Get item counter data by category
	*
	* @param int $bb_type phpBB resource type
	* @return array
	*/
	public function get_count_data_by_cat($bb_type);

	/**
	* Get item counter data by author
	*
	* @return array
	*/
	public function get_count_data_by_author();

	/**
	* Get all items
	*
	* @param int $bb_type phpBB resource type
	* @return array
	*/
	public function get_items($bb_type);

	/**
	* Get items in range for pagination
	*
	* @param int	$bb_type		phpBB resource type
	* @param int	$cat_id			Category ID
	* @param string	$order_field	Sort by this field
	* @param int	$limit			Number of items
	* @param int	$offset			Position of the start
	* @return array
	*/
	public function list_items($bb_type, $cat_id, $order_field = 'item_updated DESC', $limit = 0, $offset = 0);

	/**
	* Get latest items
	*
	* @param int	$bb_type	phpBB resource type
	* @param int	$limit		Number of items
	* @return array
	*/
	public function get_latest_items($bb_type, $limit = 10);

	/**
	* Add a item
	*
	* @param \vinabb\web\entities\bb_item_interface	$entity		BB item entity
	* @param int									$bb_type	phpBB resource type
	* @return \vinabb\web\entities\bb_item_interface
	*/
	public function add_item(\vinabb\web\entities\bb_item_interface $entity, $bb_type);

	/**
	* Delete a item
	*
	* @param int $id Item ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_item($id);
}
