<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

/**
* Interface for the portal_categories_module
*/
interface portal_categories_interface
{
	/**
	* Set form action URL
	*
	* @param string $u_action Form action
	*/
	public function set_form_action($u_action);
	/**
	* Display categories
	*
	* @param int $parent_id Parent ID
	*/
	public function display_cats($parent_id = 0);

	/**
	* Add a category
	*
	* @param int $parent_id Parent ID
	*/
	public function add_cat($parent_id = 0);

	/**
	* Edit a category
	*
	* @param int $cat_id Category ID
	*/
	public function edit_cat($cat_id);

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\portal_category_interface $entity Portal category entity
	*/
	public function add_edit_data(\vinabb\web\entities\portal_category_interface $entity);

	/**
	* Move a rule up/down
	*
	* @param int	$cat_id		Category ID
	* @param string	$direction	The direction (up|down)
	* @param int	$amount		The number of places to move
	*/
	public function move_cat($cat_id, $direction, $amount = 1);

	/**
	* Deleta a category
	*
	* @param int $cat_id Category ID
	*/
	public function delete_cat($cat_id);
}
