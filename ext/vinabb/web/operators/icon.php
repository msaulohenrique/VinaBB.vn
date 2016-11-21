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
* Operator for a set of post icons
*/
class icon
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
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
	* Get number of icons
	*
	* @return int
	*/
	public function count_icons()
	{
		$sql = 'SELECT COUNT(icons_id) AS counter
			FROM ' . ICONS_TABLE;
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all icons
	*
	* @return array
	*/
	public function get_icons()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . ICONS_TABLE . '
			ORDER BY icons_order';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.smiley')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add an icon
	*
	* @param \vinabb\web\entities\icon_interface $entity Icon entity
	* @return \vinabb\web\entities\icon_interface
	*/
	public function add_icon($entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete an icon
	*
	* @param int $id Icon ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_icon($id)
	{
		$sql = 'DELETE FROM ' . ICONS_TABLE . '
			WHERE icons_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
