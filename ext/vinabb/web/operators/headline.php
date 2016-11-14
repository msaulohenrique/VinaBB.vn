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
* Operator for a set of headlines
*/
class headline implements headline_interface
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
	* Get all headlines
	*
	* @return array
	*/
	public function get_headlines()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . $this->table_name;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.headline')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a headline
	*
	* @param \vinabb\web\entities\headline_interface $entity Headline entity
	* @return \vinabb\web\entities\headline_interface
	*/
	public function add_headline($entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete a headline
	*
	* @param int $id Headline ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_headline($id)
	{
		$sql = 'DELETE FROM ' . $this->table_name . '
			WHERE headline_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
