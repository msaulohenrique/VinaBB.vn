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
* Operator for a set of phpBB resource authors
*/
class bb_author implements bb_author_interface
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
	* Build SQL WHERE for queries
	*
	* @param string $mode 'group', 'author' or empty for both
	* @return string
	*/
	protected function build_sql_where($mode)
	{
		switch ($mode)
		{
			case 'group':
			return 'WHERE author_is_group = 1';

			case 'author':
			return 'WHERE author_is_group = 0';

			default:
			return '';
		}
	}

	/**
	* Get number of authors
	*
	* @param string $mode 'group', 'author' or empty for both
	* @return int
	*/
	public function count_authors($mode = '')
	{
		$sql_where = $this->build_sql_where($mode);

		$sql = 'SELECT COUNT(author_id) AS counter
			FROM ' . $this->table_name . "
			$sql_where";
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all authors
	*
	* @param string $mode 'group', 'author' or empty for both
	* @return array
	*/
	public function get_authors($mode = '')
	{
		$entities = [];
		$sql_where = $this->build_sql_where($mode);

		$sql = 'SELECT *
			FROM ' . $this->table_name . "
			$sql_where";
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.bb_author')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Get authors in range for pagination
	*
	* @param string	$mode			'group', 'author' or empty for both
	* @param string	$order_field	Sort by this field
	* @param int	$limit			Number of items
	* @param int	$offset			Position of the start
	* @return array
	*/
	public function list_authors($mode = '', $order_field = 'author_name', $limit = 0, $offset = 0)
	{
		$entities = [];
		$sql_where = $this->build_sql_where($mode);

		$sql = 'SELECT *
			FROM ' . $this->table_name . "
			$sql_where
			ORDER BY $order_field";
		$result = $this->db->sql_query_limit($sql, $limit, $offset);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.bb_author')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add an author
	*
	* @param \vinabb\web\entities\bb_author_interface $entity BB Author entity
	* @return \vinabb\web\entities\bb_author_interface
	*/
	public function add_author(\vinabb\web\entities\bb_author_interface $entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Remove authors from a group
	*
	* @param int $id Author group ID
	*/
	public function unset_group($id)
	{
		$sql = 'UPDATE ' . $this->table_name . '
			SET author_group = 0
			WHERE author_group = ' . (int) $id;
		$this->db->sql_query($sql);
	}

	/**
	* Delete an author
	*
	* @param int $id Author ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_author($id)
	{
		$sql = 'DELETE FROM ' . $this->table_name . '
			WHERE author_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
