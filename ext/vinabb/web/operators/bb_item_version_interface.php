<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of phpBB resource item versions
*/
interface bb_item_version_interface
{
	/**
	* Get number of versions
	*
	* @param int $id Item ID
	* @return int
	*/
	public function count_versions($id);

	/**
	* Get all versions
	*
	* @param int $id Item ID
	* @return array
	*/
	public function get_versions($id);

	/**
	* Add a version
	*
	* @param \vinabb\web\entities\bb_item_version_interface	$entity	BB item version entity
	* @param int											$id		Item version
	* @param string											$branch	phpBB branch
	* @return \vinabb\web\entities\bb_item_version_interface
	*/
	public function add_version(\vinabb\web\entities\bb_item_version_interface $entity, $id, $branch);

	/**
	* Delete a version
	*
	* @param int	$id		Item version
	* @param string	$branch	phpBB branch
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_version($id, $branch);
}
