<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Operator for a set of phpBB resource authors
*/
class bb_author implements bb_author_interface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vinabb\web\entities\bb_author_interface */
	protected $entity;

	/** @var string */
	protected $table_name;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface			$db			Database object
	* @param \vinabb\web\entities\bb_author_interface	$entity		BB author entity
	* @param string										$table_name	Table name
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \vinabb\web\entities\bb_author_interface $entity, $table_name)
	{
		$this->db = $db;
		$this->entity = $entity;
		$this->table_name = $table_name;
	}

	/**
	* Get all authors
	*
	* @return array
	*/
	public function get_authors()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . $this->table_name;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->entity->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add an author
	*
	* @return \vinabb\web\entities\bb_author_interface
	*/
	public function add_author()
	{
		// Insert the entity to the database
		$this->entity->insert();

		// Get the newly inserted entity ID
		$id = $this->entity->get_id();

		// Reload the data to return a fresh entity
		return $this->entity->load($id);
	}

	/**
	* Delete an author
	*
	* @param int $id Author ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_author($id)
	{
		$sql = 'DELETE FROM ' . $this->table_name . '
			WHERE author_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
