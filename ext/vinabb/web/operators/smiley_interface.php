<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Operator for a set of smilies
*/
interface smiley_interface
{
	/**
	* Get all smilies
	*
	* @return array
	*/
	public function get_smilies();

	/**
	* Add a smiley
	*
	* @param \vinabb\web\entities\smiley_interface $entity Smiley entity
	* @return \vinabb\web\entities\smiley_interface
	*/
	public function add_smiley($entity);

	/**
	* Delete a smiley
	*
	* @param int $id Smiley ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_smiley($id);
}
