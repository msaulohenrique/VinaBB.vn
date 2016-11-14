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
	* Get all items
	*
	* @param int $bb_type phpBB resource type
	* @return array
	*/
	public function get_items($bb_type);

	/**
	* Add a item
	*
	* @param \vinabb\web\entities\bb_item_interface	$entity		BB item entity
	* @param int									$bb_type	phpBB resource type
	* @return \vinabb\web\entities\bb_item_interface
	*/
	public function add_item($entity, $bb_type);

	/**
	* Delete a item
	*
	* @param int $id Item ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_item($id);
}
