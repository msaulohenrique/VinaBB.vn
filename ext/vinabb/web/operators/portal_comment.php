<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

use Symfony\Component\DependencyInjection\ContainerInterface;
use vinabb\web\includes\constants;

/**
* Operator for a set of article comments
*/
class portal_comment implements portal_comment_interface
{
	/** @var \phpbb\auth\auth $auth */
	protected $auth;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var string $table_name */
	protected $table_name;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth					$auth		Authentication object
	* @param ContainerInterface					$container	Container object
	* @param \phpbb\db\driver\driver_interface	$db			Database object
	* @param \phpbb\user						$user		User object
	* @param string								$table_name	Table name
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		ContainerInterface $container,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\user $user,
		$table_name
	)
	{
		$this->auth = $auth;
		$this->container = $container;
		$this->db = $db;
		$this->user = $user;
		$this->table_name = $table_name;
	}

	/**
	* Get number of comments
	*
	* @param int $article_id Article ID
	* @return int
	*/
	public function count_comments($article_id = 0)
	{
		$sql_where = $article_id ? 'WHERE article_id = ' . (int) $article_id : 'WHERE article_id > 0';

		$sql = 'SELECT COUNT(comment_id) AS counter
			FROM ' . $this->table_name . "
			$sql_where";
		$result = $this->db->sql_query($sql);
		$counter = (int) $this->db->sql_fetchfield('counter');
		$this->db->sql_freeresult($result);

		return $counter;
	}

	/**
	* Get all comments from an article
	*
	* @param int $article_id Article ID
	* @return array
	*/
	public function get_comments($article_id)
	{
		$entities = [];
		$sql_or = $this->auth->acl_get('a_') ? '' : 'comment_pending = ' . constants::ARTICLE_COMMENT_MODE_SHOW . ' OR user_id = ' . (int) $this->user->data['user_id'];

		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE article_id = ' . (int) $article_id . "
				$sql_or";
		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$entities[] = $this->container->get('vinabb.web.entities.portal_comment')->import($row);
		}
		$this->db->sql_freeresult($result);

		return $entities;
	}

	/**
	* Get all pending comments
	*
	* @return array
	*/
	public function get_pending_comments()
	{
		$entities = [];

		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE comment_pending <> ' . constants::ARTICLE_COMMENT_MODE_SHOW;
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
	public function add_comment(\vinabb\web\entities\portal_comment_interface $entity)
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
