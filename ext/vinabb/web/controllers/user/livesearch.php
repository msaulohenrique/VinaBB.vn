<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

/**
* Username suggestions in search form
*/
class livesearch implements livesearch_interface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db
	)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
	}

	/**
	* Main method
	*
	* @param string $username Keyword
	*/
	public function main($username)
	{
		if (!$this->config['allow_live_searches'])
		{
			trigger_error('LIVE_SEARCHES_NOT_ALLOWED');
		}

		// User types
		$user_types = [USER_NORMAL, USER_FOUNDER];

		if ($this->auth->acl_get('a_user'))
		{
			$user_types[] = USER_INACTIVE;
		}

		$sql = 'SELECT username, user_id, user_colour
			FROM ' . USERS_TABLE . '
			WHERE ' . $this->db->sql_in_set('user_type', $user_types) . '
				AND username_clean ' . $this->db->sql_like_expression(utf8_clean_string($username) . $this->db->get_any_char());
		$result = $this->db->sql_query_limit($sql, 10);

		$user_list = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$user_list[] = [
				'user_id'		=> (int) $row['user_id'],
				'result'		=> $row['username'],
				'username_full'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'display'		=> get_username_string('no_profile', $row['user_id'], $row['username'], $row['user_colour'])
			];
		}
		$this->db->sql_freeresult($result);

		$json_response = new \phpbb\json_response();
		$json_response->send([
			'keyword' => $username,
			'results' => $user_list
		]);
	}
}
