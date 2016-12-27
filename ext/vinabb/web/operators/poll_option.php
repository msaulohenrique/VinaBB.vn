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
* Operator for a set of poll options
*/
class poll_option implements poll_option_interface
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
	* Get all options
	*
	* @param int $topic_id Topic ID
	* @return array
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function get_options($topic_id = 0)
	{
		// The entity does not exist
		if (empty($topic_id))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('topic_id');
		}

		$entities = [];

		$sql = 'SELECT *
			FROM ' . POLL_OPTIONS_TABLE . '
			WHERE topic_id = ' . (int) $topic_id;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.poll_option')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add an option
	*
	* @param \vinabb\web\entities\poll_option_interface $entity Poll option entity
	* @return \vinabb\web\entities\poll_option_interface
	*/
	public function add_option(\vinabb\web\entities\poll_option_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete an option
	*
	* @param int $id Draft ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_option($id)
	{
		$sql = 'DELETE FROM ' . POLL_OPTIONS_TABLE . '
			WHERE poll_option_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
