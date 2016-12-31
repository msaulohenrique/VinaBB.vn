<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of article comments
*/
interface portal_comment_interface
{
	/**
	* Get number of comments
	*
	* @param int $article_id Article ID
	* @return int
	*/
	public function count_comments($article_id = 0);

	/**
	* Get all comments from an article
	*
	* @param int $article_id Article ID
	* @return array
	*/
	public function get_comments($article_id);

	/**
	* Get all pending comments
	*
	* @return array
	*/
	public function get_pending_comments();

	/**
	* Add a comment
	*
	* @param \vinabb\web\entities\portal_comment_interface $entity Comment entity
	* @return \vinabb\web\entities\portal_comment_interface
	*/
	public function add_comment(\vinabb\web\entities\portal_comment_interface $entity);

	/**
	* Delete a comment
	*
	* @param int $id Comment ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_comment($id);
}
