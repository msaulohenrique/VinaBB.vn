<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

/**
* The team page
*/
class team implements team_interface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \vinabb\web\controllers\helper_interface $ext_helper */
	protected $ext_helper;

	/** @var \phpbb\group\helper */
	protected $group_helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var array */
	protected $rank_data;

	/** @var array */
	protected $forum_data;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \vinabb\web\controllers\cache\service_interface $cache
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\language\language $language
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param \vinabb\web\controllers\helper_interface $ext_helper
	* @param \phpbb\group\helper $group_helper
	* @param string $root_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper,
		\phpbb\group\helper $group_helper,
		$root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;
		$this->group_helper = $group_helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		$this->rank_data = $this->cache->get_ranks();
		$this->forum_data = $this->cache->get_forum_data();
	}

	/**
	* Main method
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main()
	{
		include "{$this->root_path}includes/functions_display.{$this->php_ext}";
		include "{$this->root_path}includes/functions_user.{$this->php_ext}";

		// Language
		$this->language->add_lang('groups');

		$sql = 'SELECT *
			FROM ' . TEAMPAGE_TABLE . '
			ORDER BY teampage_position';
		$result = $this->db->sql_query($sql, 3600);
		$teampage_data = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$sql_ary = [
			'SELECT'	=> 'g.group_id, g.group_name, g.group_rank, g.group_colour, g.group_type, ug.user_id as ug_user_id, t.teampage_id',
			'FROM'		=> [GROUPS_TABLE => 'g'],
			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [TEAMPAGE_TABLE => 't'],
					'ON'	=> 't.group_id = g.group_id'
				],
				[
					'FROM'	=> [USER_GROUP_TABLE => 'ug'],
					'ON'	=> 'ug.group_id = g.group_id AND ug.user_pending = 0 AND ug.user_id = ' . (int) $this->user->data['user_id']
				]
			]
		];

		$result = $this->db->sql_query($this->db->sql_build_query('SELECT', $sql_ary));

		$group_ids = $groups_ary = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($row['group_type'] == GROUP_HIDDEN && !$this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel') && $row['ug_user_id'] != $this->user->data['user_id'])
			{
				$row['group_name'] = $this->language->lang('GROUP_UNDISCLOSED');
				$row['u_group'] = '';
			}
			else
			{
				$row['group_name'] = $this->group_helper->get_name($row['group_name']);
				$row['u_group'] = $this->helper->route('vinabb_web_user_group_route', ['group_id' => $row['group_id']]);
			}

			if ($row['teampage_id'])
			{
				// Only put groups into the array we want to display.
				// We are fetching all groups, to ensure we got all data for default groups.
				$group_ids[] = (int) $row['group_id'];
			}

			$groups_ary[(int) $row['group_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		$sql_ary = [
			'SELECT'	=> 'u.user_id, u.group_id as default_group, u.username, u.username_clean, u.user_colour, u.user_type, u.user_rank, u.user_posts, u.user_allow_pm, u.user_avatar_type, u.user_avatar, u.user_avatar_width, u.user_avatar_height, g.group_id',
			'FROM'		=> [USER_GROUP_TABLE => 'ug'],
			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [USERS_TABLE => 'u'],
					'ON'	=> 'ug.user_id = u.user_id AND ug.user_pending = 0'
				],
				[
					'FROM'	=> [GROUPS_TABLE => 'g'],
					'ON'	=> 'ug.group_id = g.group_id'
				]
			],
			'WHERE'		=> $this->db->sql_in_set('g.group_id', $group_ids, false, true),
			'ORDER_BY'	=> 'u.username_clean'
		];
		$result = $this->db->sql_query($this->db->sql_build_query('SELECT', $sql_ary));

		$user_ary = $user_ids = $group_users = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$row['forums'] = '';
			$row['forums_ary'] = [];
			$user_ary[(int) $row['user_id']] = $row;
			$user_ids[] = (int) $row['user_id'];
			$group_users[(int) $row['group_id']][] = (int) $row['user_id'];
		}
		$this->db->sql_freeresult($result);

		$user_ids = array_unique($user_ids);

		if (!empty($user_ids) && $this->config['teampage_forums'])
		{
			$this->template->assign_var('S_DISPLAY_MODERATOR_FORUMS', true);

			// Get all moderators
			$perm_ary = $this->auth->acl_get_list($user_ids, ['m_'], false);

			foreach ($perm_ary as $forum_id => $forum_ary)
			{
				foreach ($forum_ary as $auth_option => $id_ary)
				{
					foreach ($id_ary as $id)
					{
						if (!$forum_id)
						{
							$user_ary[$id]['forums'] = $this->language->lang('ALL_FORUMS');
						}
						else
						{
							$user_ary[$id]['forums_ary'][] = $forum_id;
						}
					}
				}
			}

			foreach ($user_ary as $user_id => $user_data)
			{
				if (!$user_data['forums'])
				{
					foreach ($user_data['forums_ary'] as $forum_id)
					{
						$user_ary[$user_id]['forums_options'] = true;

						if ($this->auth->acl_get('f_list', $forum_id))
						{
							$user_ary[$user_id]['forums'] .= '<option value="">' . $this->forum_data[$forum_id]['name'] . '</option>';
						}
					}
				}
			}
		}

		foreach ($teampage_data as $team_data)
		{
			// If this team entry has no group, it's a category
			if (!$team_data['group_id'])
			{
				$this->template->assign_block_vars('group', [
					'GROUP_NAME'  => $team_data['teampage_name']
				]);

				continue;
			}

			$group_data = $groups_ary[(int) $team_data['group_id']];
			$group_id = (int) $team_data['group_id'];

			if (!$team_data['teampage_parent'])
			{
				// If the group does not have a parent category, we display the groupname as category
				$this->template->assign_block_vars('group', [
					'GROUP_NAME'		=> $group_data['group_name'],
					'GROUP_RANK_RAW'	=> ($group_data['group_rank']) ? $this->rank_data[$group_data['group_rank']]['title'] : '',
					'GROUP_RANK'		=> ($group_data['group_rank']) ? (($this->language->is_set(['RANK_TITLES', strtoupper($this->rank_data[$group_data['group_rank']]['title'])])) ? $this->language->lang(['RANK_TITLES', strtoupper($this->rank_data[$group_data['group_rank']]['title'])]) : $this->rank_data[$group_data['group_rank']]['title']) : '',
					'GROUP_COLOR'		=> $group_data['group_colour'],
					'U_GROUP'			=> $group_data['u_group']
				]);
			}

			// Display group members
			if (!empty($group_users[$group_id]))
			{
				foreach ($group_users[$group_id] as $user_id)
				{
					if (isset($user_ary[$user_id]))
					{
						$row = $user_ary[$user_id];

						if ($this->config['teampage_memberships'] == 1 && ($group_id != $groups_ary[$row['default_group']]['group_id']) && $groups_ary[$row['default_group']]['teampage_id'])
						{
							// Display users in their primary group, instead of the first group, when it is displayed on the teampage.
							continue;
						}

						$user_rank_data = ($row['user_rank']) ? $this->rank_data[$row['user_rank']] : phpbb_get_user_rank($row, (($row['user_id'] == ANONYMOUS) ? false : $row['user_posts']));

						$template_vars = [
							'USER_ID'			=> $row['user_id'],
							'FORUMS'			=> $row['forums'],
							'FORUM_OPTIONS'		=> isset($row['forums_options']),
							'AVATAR_IMG'		=> ($this->user->optionget('viewavatars')) ? (($row['user_avatar_type'] == 'avatar.driver.gravatar') ? $this->ext_helper->get_gravatar_url($row) : phpbb_get_user_avatar($row)) : '',
							'RANK_TITLE_RAW'	=> $user_rank_data['title'],
							'RANK_TITLE'		=> ($this->language->is_set(['RANK_TITLES', strtoupper($user_rank_data['title'])])) ? $this->language->lang(['RANK_TITLES', strtoupper($user_rank_data['title'])]) : $user_rank_data['title'],

							'GROUP_NAME'	=> $groups_ary[$row['default_group']]['group_name'],
							'GROUP_COLOR'	=> $groups_ary[$row['default_group']]['group_colour'],
							'U_GROUP'		=> $groups_ary[$row['default_group']]['u_group'],

							'S_INACTIVE'	=> $row['user_type'] == USER_INACTIVE,

							'U_PM'	=> ($this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm') && ($row['user_allow_pm'] || $this->auth->acl_gets('a_', 'm_') || $this->auth->acl_getf_global('m_'))) ? $this->helper->route('vinabb_web_ucp_route', ['id' => 'pm', 'mode' => 'compose', 'u' => $row['user_id']]) : '',

							'USERNAME_FULL'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
							'USERNAME'			=> get_username_string('username', $row['user_id'], $row['username'], $row['user_colour']),
							'USER_COLOR'		=> get_username_string('colour', $row['user_id'], $row['username'], $row['user_colour']),
							'U_VIEW_PROFILE'	=> get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour'])
						];

						$this->template->assign_block_vars('group.user', $template_vars);

						if ($this->config['teampage_memberships'] != 2)
						{
							unset($user_ary[$user_id]);
						}
					}
				}
			}
		}

		// Breadcrumb
		$this->ext_helper->set_breadcrumb($this->language->lang('THE_TEAM'));

		return $this->helper->render('memberlist_team.html', $this->language->lang('THE_TEAM'));
	}
}
