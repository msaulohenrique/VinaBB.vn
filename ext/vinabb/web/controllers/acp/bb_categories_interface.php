<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

/**
* Interface for the bb_categories_module
*/
interface bb_categories_interface
{
	/**
	* Set form data
	*
	* @param array $data Form data
	*/
	public function set_form_data($data);

	/**
	* Display categories
	*/
	public function display_cats();

	/**
	* Add a category
	*/
	public function add_cat();

	/**
	* Edit a category
	*
	* @param int $cat_id Category ID
	*/
	public function edit_cat($cat_id);

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\bb_category_interface $entity BB category entity
	*/
	public function add_edit_data(\vinabb\web\entities\bb_category_interface $entity);

	/**
	* Move a category up/down
	*
	* @param int	$cat_id		Category ID
	* @param string	$direction	The direction (up|down)
	*/
	public function move_cat($cat_id, $direction);

	/**
	* Delete a category
	*
	* @param int $cat_id Category ID
	*/
	public function delete_cat($cat_id);
}
