<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\helper;

/**
* Controller for the entity helper
*/
class helper
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db Database object
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db)
	{
		$this->db = $db;
	}

	/**
	* Check the existing group
	*
	* @param int $id Group ID
	* @return bool
	*/
	public function check_group_id($id)
	{
		return $this->check_id_column(GROUPS_TABLE, 'group_id', $id);
	}

	/**
	* Check the existing user
	*
	* @param int $id User ID
	* @return bool
	*/
	public function check_user_id($id)
	{
		return $this->check_id_column(USERS_TABLE, 'user_id', $id);
	}

	/**
	* Check the existing forum
	*
	* @param int $id Forum ID
	* @return bool
	*/
	public function check_forum_id($id)
	{
		return $this->check_id_column(FORUMS_TABLE, 'forum_id', $id);
	}

	/**
	* Check the existing topic
	*
	* @param int $id Topic ID
	* @return bool
	*/
	public function check_topic_id($id)
	{
		return $this->check_id_column(TOPICS_TABLE, 'topic_id', $id);
	}

	/**
	* Check the existing post
	*
	* @param int $id Post ID
	* @return bool
	*/
	public function check_post_id($id)
	{
		return $this->check_id_column(POSTS_TABLE, 'post_id', $id);
	}

	/**
	* Check the existing ID column
	*
	* @param string	$table	Table name
	* @param string	$column	Column name
	* @param int	$id		ID value
	* @return bool
	*/
	protected function check_id_column($table = USERS_TABLE, $column = 'user_id', $id = 0)
	{
		$sql = 'SELECT 1
			FROM ' . $table . "
			WHERE $column = " . (int) $id;
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return (bool) $row;
	}
}
