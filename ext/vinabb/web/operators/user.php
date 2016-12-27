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
* Operator for a set of users
*/
class user implements user_interface
{
	/** @var \phpbb\auth\auth $auth */
	protected $auth;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var array $user_types */
	private $user_types;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth					$auth		Authentication object
	* @param ContainerInterface					$container	Container object
	* @param \phpbb\db\driver\driver_interface	$db			Database object
	*/
	public function __construct(\phpbb\auth\auth $auth, ContainerInterface $container, \phpbb\db\driver\driver_interface $db)
	{
		$this->auth = $auth;
		$this->container = $container;
		$this->db = $db;

		$this->user_types = [USER_NORMAL, USER_FOUNDER];

		if ($this->auth->acl_get('a_user'))
		{
			$this->user_types[] = USER_INACTIVE;
		}
	}

	/**
	* Get number of users
	*
	* @return int
	*/
	public function count_users()
	{
		$sql = 'SELECT COUNT(user_id) AS counter
			FROM ' . USERS_TABLE;
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all users
	*
	* @return array
	*/
	public function get_users()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
			WHERE ' . $this->db->sql_in_set('user_type', $this->user_types);
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.user')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Get users in range for pagination
	*
	* @param string	$order_field	Sort by this field
	* @param int	$limit			Number of items
	* @param int	$offset			Position of the start
	* @return array
	*/
	public function list_users($order_field = 'user_regdate', $limit = 0, $offset = 0)
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
			WHERE ' . $this->db->sql_in_set('user_type', $this->user_types) . "
			ORDER BY $order_field";
		$result = $this->db->sql_query_limit($sql, $limit, $offset);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.user')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Get latest users
	*
	* @param int $limit Number of items
	* @return array
	*/
	public function get_latest_users($limit = 10)
	{
		return $this->list_users('user_regdate DESC', $limit);
	}

	/**
	* Add an user
	*
	* @param \vinabb\web\entities\user_interface $entity User entity
	* @return \vinabb\web\entities\user_interface
	*/
	public function add_user(\vinabb\web\entities\user_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete an user
	*
	* @param int $id User ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_user($id)
	{
		$sql = 'DELETE FROM ' . USERS_TABLE . '
			WHERE user_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
