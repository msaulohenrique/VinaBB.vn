<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* Operator for a set of posts
*/
class post implements post_interface
{
	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/**
	* Constructor
	*
	* @param ContainerInterface					$container	Container object
	* @param \phpbb\db\driver\driver_interface	$db			Database object
	*/
	public function __construct(ContainerInterface $container, \phpbb\db\driver\driver_interface $db)
	{
		$this->container = $container;
		$this->db = $db;
	}

	/**
	* Get number of posts
	*
	* @param int	$forum_id	Forum ID
	* @param int	$topic_id	Topic ID
	* @return int
	*/
	public function count_posts($forum_id = 0, $topic_id = 0)
	{
		$sql_where = $forum_id ? 'WHERE forum_id = ' . (int) $forum_id : '';
		$sql_where .= $forum_id ? ' AND topic_id = ' . (int) $topic_id : '';

		$sql = 'SELECT COUNT(post_id) AS counter
			FROM ' . POSTS_TABLE . "
			$sql_where";
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all posts
	*
	* @return array
	*/
	public function get_posts()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . POSTS_TABLE;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.post')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

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
	public function list_posts($forum_id = 0, $topic_id = 0, $order_field = 'post_time DESC', $limit = 0, $offset = 0)
	{
		$entities = [];
		$sql_where = $forum_id ? 'WHERE forum_id = ' . (int) $forum_id : '';
		$sql_where .= $topic_id ? ' AND topic_id = ' . (int) $topic_id : '';

		$sql = 'SELECT *
			FROM ' . POSTS_TABLE . "
			$sql_where
			ORDER BY $order_field";
		$result = $this->db->sql_query_limit($sql, $limit, $offset);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.post')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Get latest posts
	*
	* @param int	$forum_id	Forum ID
	* @param int	$topic_id	Topic ID
	* @param int	$limit		Number of items
	* @return array
	*/
	public function get_latest_posts($forum_id = 0, $topic_id = 0, $limit = 10)
	{
		return $this->list_posts($forum_id, $topic_id, 'post_time DESC', $limit);
	}

	/**
	* Add a post
	*
	* @param \vinabb\web\entities\post_interface $entity Post entity
	* @return \vinabb\web\entities\post_interface
	*/
	public function add_post(\vinabb\web\entities\post_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete a post
	*
	* @param int $id Post ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_post($id)
	{
		$sql = 'DELETE FROM ' . POSTS_TABLE . '
			WHERE post_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
