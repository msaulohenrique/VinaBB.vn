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
* Operator for a set of phpBB resource items
*/
class bb_item implements bb_item_interface
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string */
	protected $table_name;

	/**
	* Constructor
	*
	* @param ContainerInterface					$container	Container object
	* @param \phpbb\db\driver\driver_interface	$db			Database object
	* @param string								$table_name	Table name
	*/
	public function __construct(ContainerInterface $container, \phpbb\db\driver\driver_interface $db, $table_name)
	{
		$this->container = $container;
		$this->db = $db;
		$this->table_name = $table_name;
	}

	/**
	* Get all items
	*
	* @param int $bb_type phpBB resource type
	* @return array
	*/
	public function get_items($bb_type)
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE bb_type = ' . (int) $bb_type;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.bb_item')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a item
	*
	* @param \vinabb\web\entities\bb_item_interface	$entity		BB item entity
	* @param int									$bb_type	phpBB resource type
	* @return \vinabb\web\entities\bb_item_interface
	*/
	public function add_item($entity, $bb_type)
	{
		// Insert the entity to the database
		$entity->insert($bb_type);

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete a item
	*
	* @param int $id Item ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_item($id)
	{
		$sql = 'DELETE FROM ' . $this->table_name . '
			WHERE item_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
