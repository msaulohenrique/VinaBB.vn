<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of groups
*/
interface group_interface
{
	/**
	* Get number of groups
	*
	* @return int
	*/
	public function count_groups();

	/**
	* Get all groups
	*
	* @return array
	*/
	public function get_groups();

	/**
	* Add a group
	*
	* @param \vinabb\web\entities\group_interface $entity Group entity
	* @return \vinabb\web\entities\group_interface
	*/
	public function add_group(\vinabb\web\entities\group_interface $entity);

	/**
	* Delete a group
	*
	* @param int $id Group ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_group($id);
}
