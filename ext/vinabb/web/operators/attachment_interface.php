<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of attachments
*/
interface attachment_interface
{
	/**
	* Get all attachments
	*
	* @param int $user_id User ID
	* @return array
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function get_attachments($user_id = 0);

	/**
	* Add an attachment
	*
	* @param \vinabb\web\entities\attachment_interface $entity Attachment entity
	* @return \vinabb\web\entities\attachment_interface
	*/
	public function add_draft(\vinabb\web\entities\attachment_interface $entity);

	/**
	* Delete an attachment
	*
	* @param int $id Attachment ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_draft($id);
}
