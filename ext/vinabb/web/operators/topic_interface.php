<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Interface for a set of topics
*/
interface topic_interface
{
	/**
	* Get number of topics
	*
	* @param int $forum_id Forum ID
	* @return int
	*/
	public function count_topics($forum_id = 0);

	/**
	* Get all topics
	*
	* @return array
	*/
	public function get_topics();

	/**
	* Get topics in range for pagination
	*
	* @param int	$forum_id		Forum ID
	* @param string	$order_field	Sort by this field
	* @param int	$limit			Number of items
	* @param int	$offset			Position of the start
	* @return array
	*/
	public function list_topics($forum_id = 0, $order_field = 'topic_time DESC', $limit = 0, $offset = 0);

	/**
	* Get latest topics
	*
	* @param int	$forum_id	Forum ID
	* @param int	$limit		Number of items
	* @return array
	*/
	public function get_latest_topics($forum_id = 0, $limit = 10);

	/**
	* Add a topic
	*
	* @param \vinabb\web\entities\topic_interface $entity Topic entity
	* @return \vinabb\web\entities\topic_interface
	*/
	public function add_topic(\vinabb\web\entities\topic_interface $entity);

	/**
	* Delete a topic
	*
	* @param int $id Topic ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_topic($id);
}
