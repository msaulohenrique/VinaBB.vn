<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Operator for a set of phpBB resource categories
*/
class bb_category implements bb_category_interface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vinabb\web\entities\bb_category_interface */
	protected $entity;

	/** @var string */
	protected $table_name;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface			$db			Database object
	* @param \vinabb\web\entities\bb_category_interface	$entity		BB category entity
	* @param string										$table_name	Table name
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \vinabb\web\entities\bb_category_interface $entity, $table_name)
	{
		$this->db = $db;
		$this->entity = $entity;
		$this->table_name = $table_name;
	}

	/**
	* Get all categories
	*
	* @param int $bb_type phpBB resource type
	* @return array
	*/
	public function get_cats($bb_type)
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE bb_type = ' . (int) $bb_type;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->entity->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a category
	*
	* @param int $bb_type phpBB resource type
	* @return \vinabb\web\entities\bb_category_interface
	*/
	public function add_cat($bb_type)
	{
		// Insert the entity to the database
		$this->entity->insert($bb_type);

		// Get the newly inserted entity ID
		$id = $this->entity->get_id();

		// Reload the data to return a fresh entity
		return $this->entity->load($id);
	}

	/**
	* Delete a category
	*
	* @param int $id Category ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_cat($id)
	{
		$sql = 'DELETE FROM ' . $this->table_name . '
			WHERE cat_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
