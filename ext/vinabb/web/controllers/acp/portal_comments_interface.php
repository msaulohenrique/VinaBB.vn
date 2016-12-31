<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

/**
* Interface for the portal_comments_module
*/
interface portal_comments_interface
{
	/**
	* Set form action URL
	*
	* @param string $u_action Form action
	*/
	public function set_form_action($u_action);

	/**
	* Display pending comments
	*/
	public function display_pending_comments();

	/**
	* Add a comment
	*/
	public function add_comment();

	/**
	* Edit a comment
	*
	* @param int $comment_id Comment ID
	*/
	public function edit_comment($comment_id);

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\portal_comment_interface $entity Comment entity
	*/
	public function add_edit_data(\vinabb\web\entities\portal_comment_interface $entity);

	/**
	* Delete a comment
	*
	* @param int $comment_id Comment ID
	*/
	public function delete_comment($comment_id);
}
