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
* Operator for a set of attachments
*/
class attachment implements attachment_interface
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
	* Get all attachments
	*
	* @param int $user_id User ID
	* @return array
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function get_attachments($user_id = 0)
	{
		// The entity does not exist
		if (empty($user_id))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('user_id');
		}

		$entities = [];

		$sql = 'SELECT *
			FROM ' . ATTACHMENTS_TABLE . '
			WHERE user_id = ' . (int) $user_id;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.attachment')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add an attachment
	*
	* @param \vinabb\web\entities\attachment_interface $entity Attachment entity
	* @return \vinabb\web\entities\attachment_interface
	*/
	public function add_draft(\vinabb\web\entities\attachment_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete an attachment
	*
	* @param int $id Attachment ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_draft($id)
	{
		$sql = 'DELETE FROM ' . ATTACHMENTS_TABLE . '
			WHERE attach_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
