<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

/**
* Interface for the bb_item_versions_module
*/
interface bb_item_versions_interface
{
	/**
	* Set form data
	*
	* @param array $data Form data
	*/
	public function set_form_data($data);

	/**
	* Display item versions
	*/
	public function display_versions();

	/**
	* Add an item version
	*/
	public function add_version();

	/**
	* Edit an item version
	*
	* @param int	$item_id		Item ID
	* @param string	$phpbb_branch	phpBB branch
	*/
	public function edit_version($item_id, $phpbb_branch);

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\bb_item_version_interface $entity BB item version entity
	*/
	public function add_edit_data(\vinabb\web\entities\bb_item_version_interface $entity);

	/**
	* Delete an item version
	*
	* @param int	$item_id		Item ID
	* @param string	$phpbb_branch	phpBB branch
	*/
	public function delete_item($item_id, $phpbb_branch);
}
