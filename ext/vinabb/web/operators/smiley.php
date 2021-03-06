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
* Operator for a set of smilies
*/
class smiley implements smiley_interface
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
	* Get number of smilies
	*
	* @return int
	*/
	public function count_smilies()
	{
		$sql = 'SELECT COUNT(smiley_id) AS counter
			FROM ' . SMILIES_TABLE;
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all smilies
	*
	* @return array
	*/
	public function get_smilies()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . SMILIES_TABLE . '
			ORDER BY smiley_order';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.smiley')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a smiley
	*
	* @param \vinabb\web\entities\smiley_interface $entity Smiley entity
	* @return \vinabb\web\entities\smiley_interface
	*/
	public function add_smiley(\vinabb\web\entities\smiley_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete a smiley
	*
	* @param int $id Smiley ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_smiley($id)
	{
		$sql = 'DELETE FROM ' . SMILIES_TABLE . '
			WHERE smiley_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
