<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of phpBB resource categories
*/
interface bb_category_interface
{
	/**
	* Get number of categories
	*
	* @param int $bb_type phpBB resource type
	* @return int
	*/
	public function count_cats($bb_type);

	/**
	* Get all categories
	*
	* @param int $bb_type phpBB resource type
	* @return array
	*/
	public function get_cats($bb_type);

	/**
	* Add a category
	*
	* @param \vinabb\web\entities\bb_category_interface	$entity		BB category entity
	* @param int										$bb_type	phpBB resource type
	* @return \vinabb\web\entities\bb_category_interface
	*/
	public function add_cat($entity, $bb_type);

	/**
	* Delete a category
	*
	* @param int $id Category ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_cat($id);
}
