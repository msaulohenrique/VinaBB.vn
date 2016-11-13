<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Operator for a set of headlines
*/
class headline implements headline_interface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vinabb\web\entities\headline_interface */
	protected $entity;

	/** @var string */
	protected $table_name;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface			$db			Database object
	* @param \vinabb\web\entities\headline_interface	$entity		Headline entity
	* @param string										$table_name	Table name
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \vinabb\web\entities\headline_interface $entity, $table_name)
	{
		$this->db = $db;
		$this->entity = $entity;
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
			$entities[] = $this->entity->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a headline
	*
	* @return \vinabb\web\entities\headline_interface
	*/
	public function add_headline()
	{
		// Insert the entity to the database
		$this->entity->insert();

		// Get the newly inserted entity ID
		$id = $this->entity->get_id();

		// Reload the data to return a fresh entity
		return $this->entity->load($id);
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
