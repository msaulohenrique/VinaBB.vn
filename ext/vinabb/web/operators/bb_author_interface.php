<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of phpBB resource authors
*/
interface bb_author_interface
{
	/**
	* Get all authors
	*
	* @return array
	*/
	public function get_authors();

	/**
	* Add an author
	*
	* @param \vinabb\web\entities\bb_author_interface $entity BB Author entity
	* @return \vinabb\web\entities\bb_author_interface
	*/
	public function add_author($entity);

	/**
	* Delete an author
	*
	* @param int $id Author ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_author($id);
}
