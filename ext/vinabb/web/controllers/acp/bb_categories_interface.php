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
	* Set form action URL
	*
	* @param string $u_action Form action
	*/
	public function set_form_action($u_action);

	/**
	* Set phpBB resource types
	*
	* @param int $bb_type phpBB resource type
	*/
	public function set_bb_type($bb_type);

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
	public function add_edit_data($entity);

	/**
	* Delete a category
	*
	* @param int $cat_id Category ID
	*/
	public function delete_cat($cat_id);
}
