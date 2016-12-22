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
* Operator for a set of phpBB resource item versions
*/
class bb_item_version implements bb_item_version_interface
{
	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var string $table_name */
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
	* Get number of versions
	*
	* @param int $id Item ID
	* @return int
	*/
	public function count_versions($id)
	{
		$sql = 'SELECT COUNT(item_id) AS counter
			FROM ' . $this->table_name . '
			WHERE item_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all versions
	*
	* @param int $id Item ID
	* @return array
	*/
	public function get_versions($id)
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE item_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.bb_item_version')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a version
	*
	* @param \vinabb\web\entities\bb_item_version_interface	$entity	BB item version entity
	* @param int											$id		Item version
	* @param string											$branch	phpBB branch
	* @return \vinabb\web\entities\bb_item_version_interface
	*/
	public function add_version(\vinabb\web\entities\bb_item_version_interface $entity, $id, $branch)
	{
		// Insert the entity to the database
		$entity->insert($id, $branch);

		// Reload the data to return a fresh entity
		return $entity->load($id, $branch);
	}

	/**
	* Delete a version
	*
	* @param int	$id		Item version
	* @param string	$branch	phpBB branch
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_version($id, $branch)
	{
		$sql = 'DELETE FROM ' . $this->table_name . '
			WHERE item_id = ' . (int) $id . "
				AND phpbb_branch = '" . $this->db->sql_escape($branch) . "'";
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
