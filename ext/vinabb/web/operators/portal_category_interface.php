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
	* Get the entities
	*
	* @param int $parent_id Parent to display sub-entities from
	* @return array Array of entities
	*/
	public function get_cats($parent_id = 0);

	/**
	* Add an entity
	*
	* @param \vinabb\web\entity\portal_category_interface	$entity		Entity with new data to insert
	* @param int											$parent_id	Parent to display sub-entities from
	* @return portal_category_interface Added entity
	*/
	public function add($entity, $parent_id = 0);

	/**
	* Move the entity up/down
	*
	* @param int	$cat_id		Category ID
	* @param string	$direction	The direction: up|down
	* @param int	$amount		The number of places to move the entity
	* @throws \vinabb\web\exception\out_of_bounds
	*/
	public function move($cat_id, $direction = 'up', $amount = 1);

	/**
	* Delete the entity
	*
	* @param int $cat_id Category ID
	* @throws \vinabb\web\exception\out_of_bounds
	*/
	public function delete($cat_id);

	/**
	* Change the parent
	*
	* @param int	$cat_id			Category ID
	* @param int	$new_parent_id	The new parent ID
	* @throws \vinabb\web\exception\out_of_bounds
	*/
	public function change_parent($cat_id, $new_parent_id);

	/**
	* Get an entity's parent entities (for use in breadcrumbs)
	*
	* @param int $parent_id Parent to display sub-entities from
	* @return array Array of entity data for an entity's parent entities
	*/
	public function get_parents($parent_id);
}
