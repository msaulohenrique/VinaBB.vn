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
* Operator for a set of languages
*/
class language implements language_interface
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
	* Get number of languages
	*
	* @return int
	*/
	public function count_langs()
	{
		$sql = 'SELECT COUNT(lang_id) AS counter
			FROM ' . LANG_TABLE;
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all languages
	*
	* @return array
	*/
	public function get_langs()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . LANG_TABLE;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.language')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a language
	*
	* @param \vinabb\web\entities\language_interface $entity Language entity
	* @return \vinabb\web\entities\language_interface
	*/
	public function add_lang(\vinabb\web\entities\language_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete a language
	*
	* @param int $id Language ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_lang($id)
	{
		$sql = 'DELETE FROM ' . LANG_TABLE . '
			WHERE lang_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
