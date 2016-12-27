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
* Operator for a set of pages
*/
class page implements page_interface
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
	* @param ContainerInterface						$container	Container object
	* @param \phpbb\db\driver\driver_interface		$db			Database object
	* @param string									$table_name	Table name
	*/
	public function __construct(ContainerInterface $container, \phpbb\db\driver\driver_interface $db, $table_name)
	{
		$this->container = $container;
		$this->db = $db;
		$this->table_name = $table_name;
	}

	/**
	* Get number of pages
	*
	* @return int
	*/
	public function count_pages()
	{
		$sql = 'SELECT COUNT(page_id) AS counter
			FROM ' . $this->table_name;
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all pages
	*
	* @return array
	*/
	public function get_pages()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . $this->table_name;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.page')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a page
	*
	* @param \vinabb\web\entities\page_interface $entity Page entity
	* @return \vinabb\web\entities\page_interface
	*/
	public function add_page(\vinabb\web\entities\page_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete a page
	*
	* @param int $id Page ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_page($id)
	{
		$sql = 'DELETE FROM ' . $this->table_name . '
			WHERE page_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
