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
	* @param int	$article_id	Article ID
	* @param int	$user_id	User ID
	* @return int
	*/
	public function count_comments($article_id = 0, $user_id = 0);

	/**
	* Get all comments
	*
	* @return array
	*/
	public function get_comments();

	/**
	* Add a comment
	*
	* @param \vinabb\web\entities\portal_comment_interface $entity Comment entity
	* @return \vinabb\web\entities\portal_comment_interface
	*/
	public function add_comment($entity);

	/**
	* Delete a comment
	*
	* @param int $id Comment ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_comment($id);
}
