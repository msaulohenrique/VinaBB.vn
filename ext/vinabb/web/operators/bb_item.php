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
* Operator for a set of phpBB resource items
*/
class bb_item implements bb_item_interface
{
	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var string $table_name */
	protected $table_name;

	/**
	* Constructor
	*
	* @param ContainerInterface					$container	Container object
	* @param \phpbb\db\driver\driver_interface	$db			Database object
	* @param string								$table_name	Table name
	*/
	public function __construct(ContainerInterface $container, \phpbb\db\driver\driver_interface $db, $table_name)
	{
		$this->container = $container;
		$this->db = $db;
		$this->table_name = $table_name;
	}

	/**
	* Get number of items
	*
	* @param int	$bb_type	phpBB resource type
	* @param int	$cat_id		Category ID
	* @param int	$author_id	Author ID
	* @return int
	*/
	public function count_items($bb_type, $cat_id = 0, $author_id = 0)
	{
		$sql_where = $bb_type ? 'WHERE bb_type = ' . (int) $bb_type : 'WHERE bb_type <> 0';
		$sql_where .= $cat_id ? ' AND cat_id = ' . (int) $cat_id : '';
		$sql_where .= $author_id ? ' AND author_id = ' . (int) $author_id : '';

		$sql = 'SELECT COUNT(item_id) AS counter
			FROM ' . $this->table_name . "
			$sql_where";
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get item counter data by category
	*
	* @param int $bb_type phpBB resource type
	* @return array
	*/
	public function get_count_data_by_cat($bb_type)
	{
		$sql = 'SELECT cat_id, COUNT(item_id) AS counter
			FROM ' . $this->table_name . '
			WHERE bb_type = ' . (int) $bb_type . '
			GROUP BY cat_id';
		$result = $this->db->sql_query($sql);

		$counter = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$counter[$row['cat_id']] = $row['counter'];
		}
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get item counter data by author
	*
	* @return array
	*/
	public function get_count_data_by_author()
	{
		$sql = 'SELECT author_id, COUNT(item_id) AS counter
			FROM ' . $this->table_name . '
			GROUP BY author_id';
		$result = $this->db->sql_query($sql);

		$counter = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$counter[$row['author_id']] = $row['counter'];
		}
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all items
	*
	* @param int $bb_type phpBB resource type
	* @return array
	*/
	public function get_items($bb_type)
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE bb_type = ' . (int) $bb_type;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.bb_item')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Get items in range for pagination
	*
	* @param int	$bb_type		phpBB resource type
	* @param int	$cat_id			Category ID
	* @param string	$order_field	Sort by this field
	* @param int	$limit			Number of items
	* @param int	$offset			Position of the start
	* @return array
	*/
	public function list_items($bb_type, $cat_id = 0, $order_field = 'item_updated DESC', $limit = 0, $offset = 0)
	{
		$entities = [];
		$sql_and = $cat_id ? 'AND cat_id = ' . (int) $cat_id : '';

		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE bb_type = ' . (int) $bb_type . "
				$sql_and
			ORDER BY $order_field";
		$result = $this->db->sql_query_limit($sql, $limit, $offset);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.bb_item')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Get latest items
	*
	* @param int	$bb_type	phpBB resource type
	* @param int	$limit		Number of items
	* @return array
	*/
	public function get_latest_items($bb_type, $limit = 10)
	{
		return $this->list_items($bb_type, 0, 'item_updated DESC', $limit);
	}

	/**
	* Add a item
	*
	* @param \vinabb\web\entities\bb_item_interface	$entity		BB item entity
	* @param int									$bb_type	phpBB resource type
	* @return \vinabb\web\entities\bb_item_interface
	*/
	public function add_item(\vinabb\web\entities\bb_item_interface $entity, $bb_type)
	{
		// Insert the entity to the database
		$entity->insert($bb_type);

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete a item
	*
	* @param int $id Item ID
	* @return bool True if row was deleted, false otherwise
	*/
	public function delete_item($id)
	{
		$sql = 'DELETE FROM ' . $this->table_name . '
			WHERE item_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
