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
	* Set form action URL
	*
	* @param string $u_action Form action
	*/
	public function set_form_action($u_action);

	/**
	* Set phpBB resource types
	*
	* @param int	$bb_type	phpBB resource type
	* @param string	$mode		Module mode
	*/
	public function set_bb_type($bb_type, $mode);

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
	public function edit_item($item_id)
	{
		// Initiate and load the entity
		/* @var \vinabb\web\entities\bb_item_interface */
		$entity = $this->container->get('vinabb.web.entities.bb_item')->load($item_id);

		// Process the edited entity
		$this->add_edit_data($entity);

		$this->template->assign_vars([
			'S_EDIT'	=> true,
			'U_ACTION'	=> "{$this->u_action}&action=edit&id={$item_id}"
		]);
	}

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\bb_item_interface $entity BB item entity
	*/
	public function add_edit_data($entity);

	/**
	* Delete an item
	*
	* @param int $item_id Item ID
	*/
	public function delete_item($item_id);
}
