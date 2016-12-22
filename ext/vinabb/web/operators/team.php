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
* Operator for a set of teams
*/
class team implements team_interface
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
	* Get all teams
	*
	* @return array
	*/
	public function get_teams()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . TEAMPAGE_TABLE . '
			ORDER BY teampage_position';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.team')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a team
	*
	* @param \vinabb\web\entities\team_interface $entity Team entity
	* @return \vinabb\web\entities\team_interface
	*/
	public function add_team(\vinabb\web\entities\team_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete a team
	*
	* @param int $id Team ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_team($id)
	{
		$sql = 'DELETE FROM ' . TEAMPAGE_TABLE . '
			WHERE teampage_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
