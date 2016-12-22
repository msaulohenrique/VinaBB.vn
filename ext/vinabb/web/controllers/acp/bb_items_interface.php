<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

/**
* Interface for the bb_items_module
*/
interface bb_items_interface
{
	/**
	* Set form data
	*
	* @param array $data Form data
	*/
	public function set_form_data($data);

	/**
	* Display items
	*/
	public function display_items();

	/**
	* Add an item
	*/
	public function add_item();

	/**
	* Edit an item
	*
	* @param int $item_id Item ID
	*/
	public function edit_item($item_id);

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\bb_item_interface $entity BB item entity
	*/
	public function add_edit_data(\vinabb\web\entities\bb_item_interface $entity);

	/**
	* Delete an item
	*
	* @param int $item_id Item ID
	*/
	public function delete_item($item_id);
}
