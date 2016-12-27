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
* Operator for a set of censor words
*/
class censor_word implements censor_word_interface
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
	* Get all words
	*
	* @return array
	*/
	public function get_words()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . WORDS_TABLE;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.censor_word')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a word
	*
	* @param \vinabb\web\entities\censor_word_interface $entity Censor word entity
	* @return \vinabb\web\entities\censor_word_interface
	*/
	public function add_word(\vinabb\web\entities\censor_word_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete a word
	*
	* @param int $id Word ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_word($id)
	{
		$sql = 'DELETE FROM ' . WORDS_TABLE . '
			WHERE word_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
