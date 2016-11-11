<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of menu items
*/
interface menu_interface
{
	/**
	* Get all menus
	*
	* @param int $parent_id Parent ID
	* @return array
	*/
	public function get_menus($parent_id = 0);

	/**
	* Add a menu
	*
	* @param int $parent_id Parent ID
	* @return \vinabb\web\entities\menu_interface
	*/
	public function add_menu($parent_id = 0);

	/**
	* Move a menu up/down
	*
	* @param int	$id			Menu ID
	* @param string	$direction	The direction: up|down
	* @param int	$amount		The number of places to move the entity
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function move($id, $direction = 'up', $amount = 1);

	/**
	* Delete a menu
	*
	* @param int $id Menu ID
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete($id);

	/**
	* Change the parent
	*
	* @param int	$id				Menu ID
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
