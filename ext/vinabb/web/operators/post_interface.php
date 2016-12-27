<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of posts
*/
interface post_interface
{
	/**
	* Get number of posts
	*
	* @param int	$forum_id	Forum ID
	* @param int	$topic_id	Topic ID
	* @return int
	*/
	public function count_posts($forum_id = 0, $topic_id = 0);

	/**
	* Get all posts
	*
	* @return array
	*/
	public function get_posts();

	/**
	* Get posts in range for pagination
	*
	* @param int	$forum_id		Forum ID
	* @param int	$topic_id		Topic ID
	* @param string	$order_field	Sort by this field
	* @param int	$limit			Number of items
	* @param int	$offset			Position of the start
	* @return array
	*/
	public function list_posts($forum_id = 0, $topic_id = 0, $order_field = 'post_time DESC', $limit = 0, $offset = 0);

	/**
	* Get latest posts
	*
	* @param int	$forum_id	Forum ID
	* @param int	$topic_id	Topic ID
	* @param int	$limit		Number of items
	* @return array
	*/
	public function get_latest_posts($forum_id = 0, $topic_id = 0, $limit = 10);

	/**
	* Add a post
	*
	* @param \vinabb\web\entities\post_interface $entity Post entity
	* @return \vinabb\web\entities\post_interface
	*/
	public function add_post(\vinabb\web\entities\post_interface $entity);

	/**
	* Delete a post
	*
	* @param int $id Post ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_post($id);
}
