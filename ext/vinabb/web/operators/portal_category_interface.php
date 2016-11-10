<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of news categories
*/
interface portal_category_interface
{
	/**
	* Get all categories
	*
	* @param int $parent_id Parent ID
	* @return array Array of entities
	*/
	public function get_cats($parent_id = 0);

	/**
	* Add a category
	*
	* @param int $parent_id Parent ID
	* @return portal_category_interface Added entity
	*/
	public function add_cat($parent_id = 0);

	/**
	* Move a category up/down
	*
	* @param int	$id			Category ID
	* @param string	$direction	The direction: up|down
	* @param int	$amount		The number of places to move the entity
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function move($id, $direction = 'up', $amount = 1);

	/**
	* Delete a category
	*
	* @param int $id Category ID
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete($id);

	/**
	* Change the parent
	*
	* @param int	$id				Category ID
	* @param int	$new_parent_id	New parent ID
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function change_parent($id, $new_parent_id);

	/**
	* Get a category's parent categories (for use in breadcrumbs)
	*
	* @param int $parent_id Parent ID
	* @return array
	*/
	public function get_parents($parent_id);
}
