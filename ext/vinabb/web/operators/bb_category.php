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
* Operator for a set of phpBB resource categories
*/
class bb_category implements bb_category_interface
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\lock\db */
	protected $lock;

	/** @var string */
	protected $table_name;

	/**
	* Constructor
	*
	* @param ContainerInterface					$container	Container object
	* @param \phpbb\db\driver\driver_interface	$db			Database object
	* @param \phpbb\lock\db						$lock		Lock table object
	* @param string								$table_name	Table name
	*/
	public function __construct(ContainerInterface $container, \phpbb\db\driver\driver_interface $db, \phpbb\lock\db $lock, $table_name)
	{
		$this->container = $container;
		$this->db = $db;
		$this->lock = $lock;
		$this->table_name = $table_name;
	}

	/**
	* Get number of categories
	*
	* @param int $bb_type phpBB resource type
	* @return int
	*/
	public function count_cats($bb_type)
	{
		$sql = 'SELECT COUNT(cat_id) AS counter
			FROM ' . $this->table_name . '
			WHERE bb_type = ' . (int) $bb_type;
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
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
			WHERE bb_type = ' . (int) $bb_type . '
			ORDER BY cat_order';
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.bb_category')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a category
	*
	* @param \vinabb\web\entities\bb_category_interface	$entity		BB category entity
	* @param int										$bb_type	phpBB resource type
	* @return \vinabb\web\entities\bb_category_interface
	*/
	public function add_cat(\vinabb\web\entities\bb_category_interface $entity, $bb_type)
	{
		// Insert the entity to the database
		$entity->insert($bb_type);

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Move a category up/down
	*
	* @param int	$bb_type	phpBB resource type
	* @param int	$id			Category ID
	* @param string	$direction	The direction: up|down
	* @return bool True if row was moved, false otherwise
	*/
	public function move_cat($bb_type, $id, $direction = 'up')
	{
		$this->acquire_lock();

		$sql = 'SELECT cat_order
			FROM ' . $this->table_name . '
			WHERE cat_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$order = $this->db->sql_fetchfield('cat_order');
		$this->db->sql_freeresult($result);

		if ($order === false || ($order == 0 && $direction == 'up'))
		{
			$this->lock->release();
			return false;
		}

		$order = (int) $order;
		$order_total = $order * 2 + (($direction == 'up') ? -1 : 1);

		$sql = 'UPDATE ' . $this->table_name . '
			SET cat_order = ' . $order_total . ' - cat_order
			WHERE bb_type = ' . (int) $bb_type . '
				AND ' . $this->db->sql_in_set('cat_order', [$order, ($direction == 'up') ? $order - 1 : $order + 1]);
		$this->db->sql_query($sql);

		$this->lock->release();

		// Return true/false if the entity was moved
		return (bool) $this->db->sql_affectedrows();
	}

	/**
	* Acquires a lock on the item table
	*
	* @return bool True if the lock was acquired, false if it has been acquired previously
	* @throws \RuntimeException If the lock could not be acquired
	*/
	protected function acquire_lock()
	{
		if ($this->lock->owns_lock())
		{
			return false;
		}

		if (!$this->lock->acquire())
		{
			throw new \RuntimeException('BB_CATS_LOCK_FAILED_ACQUIRE');
		}

		return true;
	}

	/**
	* Delete a category
	*
	* @param int $id Category ID
	* @return bool True if row was deleted, false otherwise
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
