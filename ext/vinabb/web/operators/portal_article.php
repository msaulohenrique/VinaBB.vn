<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Operator for a set of articles
*/
class portal_article implements portal_article_interface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vinabb\web\entities\portal_article_interface */
	protected $entity;

	/** @var string */
	protected $table_name;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface				$db			Database object
	* @param \vinabb\web\entities\portal_article_interface	$entity		Article entity
	* @param string											$table_name	Table name
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \vinabb\web\entities\portal_article_interface $entity, $table_name)
	{
		$this->db = $db;
		$this->entity = $entity;
		$this->table_name = $table_name;
	}

	/**
	* Get all articles
	*
	* @return array
	*/
	public function get_articles()
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
	* Add an article
	*
	* @return \vinabb\web\entities\portal_article_interface
	*/
	public function add_article()
	{
		// Insert the entity to the database
		$this->entity->insert();

		// Get the newly inserted entity ID
		$id = $this->entity->get_id();

		// Reload the data to return a fresh entity
		return $this->entity->load($id);
	}

	/**
	* Delete an article
	*
	* @param int $id Article ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_article($id)
	{
		$sql = 'DELETE FROM ' . $this->table_name . '
			WHERE article_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
