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
* Operator for a set of forums
*/
class forum implements forum_interface
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
	* Get number of forums
	*
	* @return int
	*/
	public function count_forums()
	{
		$sql = 'SELECT COUNT(forum_id) AS counter
			FROM ' . FORUMS_TABLE;
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all forums
	*
	* @return array
	*/
	public function get_forums()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . FORUMS_TABLE . '
			ORDER BY left_id';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.forum')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a forum
	*
	* @param \vinabb\web\entities\forum_interface $entity Forum entity
	* @return \vinabb\web\entities\forum_interface
	*/
	public function add_forum(\vinabb\web\entities\forum_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete a forum
	*
	* @param int $id Forum ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_forum($id)
	{
		$sql = 'DELETE FROM ' . FORUMS_TABLE . '
			WHERE forum_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
