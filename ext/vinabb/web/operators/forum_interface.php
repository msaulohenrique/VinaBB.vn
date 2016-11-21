<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of forums
*/
interface forum_interface
{
	/**
	* Get number of forums
	*
	* @return int
	*/
	public function count_forums();

	/**
	* Get all forums
	*
	* @return array
	*/
	public function get_forums();

	/**
	* Add a forum
	*
	* @param \vinabb\web\entities\forum_interface $entity Forum entity
	* @return \vinabb\web\entities\forum_interface
	*/
	public function add_forum(\vinabb\web\entities\forum_interface $entity);

	/**
	* Delete a forum
	*
	* @param int $id Forum ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_forum($id);
}
