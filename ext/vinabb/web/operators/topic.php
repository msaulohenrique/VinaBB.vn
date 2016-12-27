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
* Operator for a set of topics
*/
class topic implements topic_interface
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
	* Get number of topics
	*
	* @param int $forum_id Forum ID
	* @return int
	*/
	public function count_topics($forum_id = 0)
	{
		$sql_where = $forum_id ? ' WHERE forum_id = ' . (int) $forum_id : '';

		$sql = 'SELECT COUNT(topic_id) AS counter
			FROM ' . TOPICS_TABLE . "
			$sql_where";
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all topics
	*
	* @return array
	*/
	public function get_topics()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . TOPICS_TABLE;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.topic')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Get topics in range for pagination
	*
	* @param int	$forum_id		Forum ID
	* @param string	$order_field	Sort by this field
	* @param int	$limit			Number of items
	* @param int	$offset			Position of the start
	* @return array
	*/
	public function list_topics($forum_id = 0, $order_field = 'topic_time DESC', $limit = 0, $offset = 0)
	{
		$entities = [];
		$sql_where = $forum_id ? 'WHERE forum_id = ' . (int) $forum_id : '';

		$sql = 'SELECT *
			FROM ' . TOPICS_TABLE . "
			$sql_where
			ORDER BY $order_field";
		$result = $this->db->sql_query_limit($sql, $limit, $offset);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.topic')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Get latest topics
	*
	* @param int	$forum_id	Forum ID
	* @param int	$limit		Number of items
	* @return array
	*/
	public function get_latest_topics($forum_id = 0, $limit = 10)
	{
		return $this->list_topics($forum_id, 'topic_time DESC', $limit);
	}

	/**
	* Add a topic
	*
	* @param \vinabb\web\entities\topic_interface $entity Topic entity
	* @return \vinabb\web\entities\topic_interface
	*/
	public function add_topic(\vinabb\web\entities\topic_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete a topic
	*
	* @param int $id Topic ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_topic($id)
	{
		$sql = 'DELETE FROM ' . TOPICS_TABLE . '
			WHERE topic_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
