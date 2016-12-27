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
* Operator for a set of groups
*/
class group
{
	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/**
	* Constructor
	*
	* @param \phpbb\config\config				$config		Config object
	* @param ContainerInterface					$container	Container object
	* @param \phpbb\db\driver\driver_interface	$db			Database object
	*/
	public function __construct(\phpbb\config\config $config, ContainerInterface $container, \phpbb\db\driver\driver_interface $db)
	{
		$this->config = $config;
		$this->container = $container;
		$this->db = $db;
	}

	/**
	* Get number of groups
	*
	* @return int
	*/
	public function count_groups()
	{
		$sql = 'SELECT COUNT(group_id) AS counter
			FROM ' . GROUPS_TABLE;
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all groups
	*
	* @return array
	*/
	public function get_groups()
	{
		$entities = [];
		$order_legend = ($this->config['legend_sort_groupname']) ? 'group_name' : 'group_legend';

		$sql = 'SELECT *
			FROM ' . GROUPS_TABLE . "
			ORDER BY $order_legend";
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.group')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a group
	*
	* @param \vinabb\web\entities\group_interface $entity Group entity
	* @return \vinabb\web\entities\group_interface
	*/
	public function add_group(\vinabb\web\entities\group_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete a group
	*
	* @param int $id Group ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_group($id)
	{
		$sql = 'DELETE FROM ' . GROUPS_TABLE . '
			WHERE group_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
