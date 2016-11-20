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
* Operator for a set of article comments
*/
class portal_comment implements portal_comment_interface
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string */
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
	* Get number of comments
	*
	* @param int	$article_id	Article ID
	* @param int	$user_id	User ID
	* @return int
	*/
	public function count_comments($article_id = 0, $user_id = 0)
	{
		$sql_where = $article_id ? 'WHERE article_id = ' . (int) $article_id : 'WHERE article_id > 0';
		$sql_where .= $user_id ? ' AND user_id = ' . (int) $user_id : ' AND user_id > 0';

		$sql = 'SELECT COUNT(comment_id) AS counter
			FROM ' . $this->table_name . "
			$sql_where";
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all comments
	*
	* @return array
	*/
	public function get_comments()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . $this->table_name;
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.portal_comment')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Add a comment
	*
	* @param \vinabb\web\entities\portal_comment_interface $entity Comment entity
	* @return \vinabb\web\entities\portal_comment_interface
	*/
	public function add_comment($entity)
	{
		// Insert the entity to the database
		$entity->insert();

		// Get the newly inserted entity ID
		$id = $entity->get_id();

		// Reload the data to return a fresh entity
		return $entity->load($id);
	}

	/**
	* Delete a comment
	*
	* @param int $id Comment ID
	* @return bool True if row was deleted, false otherwise
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function delete_comment($id)
	{
		$sql = 'DELETE FROM ' . $this->table_name . '
			WHERE comment_id = ' . (int) $id;
		$this->db->sql_query($sql);

		// Return true/false if the entity was deleted
		return (bool) $this->db->sql_affectedrows();
	}
}
