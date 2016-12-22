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
* Operator for a set of headlines
*/
class headline implements headline_interface
{
	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \phpbb\lock\db $lock */
	protected $lock;

	/** @var string $table_name */
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
	* Get number of headlines
	*
	* @param string $lang 2-letter language ISO code
	* @return int
	*/
	public function count_headlines($lang = '')
	{
		$sql_where = ($lang != '') ? "WHERE headline_lang = '" . $this->db->sql_escape($lang) . "'" : '';

		$sql = 'SELECT COUNT(headline_id) AS counter
			FROM ' . $this->table_name . "
			$sql_where";
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all headlines
	*
	* @param string $lang 2-letter language ISO code
	* @return array
	*/
	public function get_headlines($lang = '')
	{
		$entities = [];
		$sql_where = " WHERE headline_lang = '" . $this->db->sql_escape($lang) . "'";

		$sql = 'SELECT *
			FROM ' . $this->table_name . "
			$sql_where
			ORDER BY headline_order";
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.headline')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a headline
	*
	* @param \vinabb\web\entities\headline_interface $entity Headline entity
	* @return \vinabb\web\entities\headline_interface
	*/
	public function add_headline(\vinabb\web\entities\headline_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Move a headline up/down
	*
	* @param string	$lang		2-letter language ISO code
	* @param int	$id			Headline ID
	* @param string	$direction	The direction: up|down
	* @return bool True if row was moved, false otherwise
	*/
	public function move_headline($lang, $id, $direction = 'up')
	{
		$this->acquire_lock();

		$sql = 'SELECT headline_order
			FROM ' . $this->table_name . '
			WHERE headline_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$order = $this->db->sql_fetchfield('headline_order');
		$this->db->sql_freeresult($result);

		if ($order === false || ($order == 0 && $direction == 'up'))
		{
			$this->lock->release();
			return false;
		}

		$order = (int) $order;
		$order_total = $order * 2 + (($direction == 'up') ? -1 : 1);

		$sql = 'UPDATE ' . $this->table_name . '
			SET headline_order = ' . $order_total . " - headline_order
			WHERE headline_lang = '" . $this->db->sql_escape($lang) . "'
				AND " . $this->db->sql_in_set('headline_order', [$order, ($direction == 'up') ? $order - 1 : $order + 1]);
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
			throw new \RuntimeException('HEADLINES_LOCK_FAILED_ACQUIRE');
		}

		return true;
	}

	/**
	* Delete a headline
	*
	* @param int $id Headline ID
	* @return bool True if row was deleted, false otherwise
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
