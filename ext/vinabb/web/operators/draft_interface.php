<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of drafts
*/
interface draft_interface
{
	/**
	* Get all drafts
	*
	* @param int $user_id User ID
	* @return array
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function get_drafts($user_id = 0);

	/**
	* Add a draft
	*
	* @param \vinabb\web\entities\draft_interface $entity Draft entity
	* @return \vinabb\web\entities\draft_interface
	*/
	public function add_draft(\vinabb\web\entities\draft_interface $entity);

	/**
	* Delete a draft
	*
	* @param int $id Draft ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_draft($id);
}
