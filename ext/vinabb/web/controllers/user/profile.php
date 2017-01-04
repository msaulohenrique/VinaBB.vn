<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

use Symfony\Component\DependencyInjection\ContainerInterface;

class profile
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\profilefields\manager */
	protected $profile_fields;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\group\helper */
	protected $group_helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $admin_path;

	/** @var string */
	protected $php_ext;

	/** @var array $profile_data */
	protected $profile_data;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
	* @param ContainerInterface								$container	Container object
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\language\language $language
	* @param \phpbb\profilefields\manager $profile_fields
	* @param \phpbb\request\request $request
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param \phpbb\group\helper $group_helper
	* @param string $root_path
	* @param string $admin_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		ContainerInterface $container,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\phpbb\profilefields\manager $profile_fields,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\phpbb\group\helper $group_helper,
		$root_path,
		$admin_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->container = $container;
		$this->db = $db;
		$this->language = $language;
		$this->profile_fields = $profile_fields;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->group_helper = $group_helper;
		$this->root_path = $root_path;
		$this->admin_path = $admin_path;
		$this->php_ext = $php_ext;
	}

	public function main($username)
	{
		include "{$this->root_path}includes/functions_display.{$this->php_ext}";

		// Language
		$this->language->add_lang('memberlist');

		// Setting a variable to let the style designer know where he is...
		$this->template->assign_var('S_IN_MEMBERLIST', true);

		// Can this user view profiles?
		$this->require_login();

		// Get user...
		$this->get_user_data($username);

		// a_user admins and founder are able to view inactive users and bots to be able to manage them more easily
		// Normal users are able to see at least users having only changed their profile settings but not yet reactivated.
		$this->can_view_all_users();

		$user_id = (int) $this->profile_data['user_id'];

		// Get group memberships
		// Also get visiting user's groups to determine hidden group memberships if necessary.
		$this->display_group_membership();

		$this->display_online_status();

		if ($this->config['load_user_activity'])
		{
			display_user_activity($this->profile_data);
		}

		$this->display_posts();

		// Profile Fields
		$this->display_profile();

		// Custom Profile Fields
		$this->display_custom_profile();

		// Inactive reason/account?
		if ($this->profile_data['user_type'] == USER_INACTIVE)
		{
			$this->display_inactive_reason($this->profile_data['user_inactive_reason']);
		}

		// If the user has m_approve permission or a_user permission, then list then display unapproved posts
		$this->get_posts_in_queue();

		$this->template->assign_vars([
			'L_POSTS_IN_QUEUE'	=> $this->language->lang('NUM_POSTS_IN_QUEUE', $this->profile_data['posts_in_queue']),

			'SIGNATURE'			=> $this->profile_data['user_sig'],
			'POSTS_IN_QUEUE'	=> $this->profile_data['posts_in_queue'],

			'L_SEND_EMAIL_USER'	=> $this->language->lang('SEND_EMAIL_USER', $this->profile_data['username']),

			'U_USER_ADMIN'			=> ($this->auth->acl_get('a_user')) ? append_sid("{$this->admin_path}index.{$this->php_ext}", 'i=users&amp;mode=overview&amp;u=' . $user_id, true, $this->user->session_id) : '',
			'U_USER_BAN'			=> ($this->auth->acl_get('m_ban') && $user_id != $this->user->data['user_id']) ? append_sid("{$this->root_path}mcp.{$this->php_ext}", 'i=ban&amp;mode=user&amp;u=' . $user_id, true, $this->user->session_id) : '',
			'U_MCP_QUEUE'			=> ($this->auth->acl_getf_global('m_approve')) ? append_sid("{$this->root_path}mcp.{$this->php_ext}", 'i=queue', true, $this->user->session_id) : '',
			'U_SWITCH_PERMISSIONS'	=> ($this->auth->acl_get('a_switchperm') && $this->user->data['user_id'] != $user_id) ? append_sid("{$this->root_path}ucp.{$this->php_ext}", "mode=switch_perm&amp;u={$user_id}&amp;hash=" . generate_link_hash('switchperm')) : '',
			'U_EDIT_SELF'			=> ($user_id == $this->user->data['user_id'] && $this->auth->acl_get('u_chgprofileinfo')) ? append_sid("{$this->root_path}ucp.{$this->php_ext}", 'i=ucp_profile&amp;mode=profile_info') : '',
			'U_CANONICAL'			=> generate_board_url() . '/' . $this->helper->route('vinabb_web_user_profile_route', ['username' => $username], true, '')
		]);

		return $this->helper->render('memberlist_view.html', $this->language->lang('VIEWING_PROFILE', $this->profile_data['username']));
	}

	/**
	* Get user data by user ID
	*
	* @param int $user_id User ID
	* @return \Symfony\Component\HttpFoundation\Response
	* @throws \phpbb\exception\http_exception
	*/
	public function id($user_id)
	{
		// Display a profile
		if ($user_id == ANONYMOUS)
		{
			throw new \phpbb\exception\http_exception(404, 'NO_USER');
		}

		try
		{
			/** @var \vinabb\web\entities\user_interface $entity */
			$entity = $this->container->get('vinabb.web.entities.user')->load($user_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			throw new \phpbb\exception\http_exception(404, 'NO_USER');
		}

		return $this->main($entity->get_username());
	}

	/**
	* Requires guests to login to view the user profile
	*/
	protected function require_login()
	{
		if (!$this->auth->acl_gets('u_viewprofile', 'a_user', 'a_useradd', 'a_userdel'))
		{
			if ($this->user->data['user_id'] != ANONYMOUS)
			{
				send_status_line(403, 'Forbidden');
				trigger_error('NO_VIEW_USERS');
			}

			login_box('', $this->language->lang('LOGIN_EXPLAIN_VIEWPROFILE'));
		}
	}

	/**
	* Get user data
	*
	* @param string $username Username
	* @throws \phpbb\exception\http_exception
	*/
	protected function get_user_data($username)
	{
		try
		{
			/** @var \vinabb\web\entities\user_interface $entity */
			$entity = $this->container->get('vinabb.web.entities.user')->load_by_username($username);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			throw new \phpbb\exception\http_exception(404, 'NO_USER');
		}

		$this->profile_data = [
			'user_id'				=> $entity->get_id(),
			'group_id'				=> $entity->get_group_id(),
			'username'				=> $entity->get_username(),
			'user_type'				=> $entity->get_type(),
			'user_birthday'			=> $entity->get_birthday(),
			'user_regdate'			=> $entity->get_regdate(),
			'user_colour'			=> $entity->get_colour(),
			'user_posts'			=> $entity->get_posts(),
			'user_sig'				=> $entity->get_sig_for_display(),
			'user_jabber'			=> $entity->get_jabber(),
			'user_avatar'			=> $entity->get_avatar(),
			'user_avatar_type'		=> $entity->get_avatar_type(),
			'user_avatar_width'		=> $entity->get_avatar_width(),
			'user_avatar_height'	=> $entity->get_avatar_height(),
			'user_allow_viewonline'	=> $entity->get_allow_viewonline()
		];
	}

	/**
	* Only administrators can view all users
	*/
	protected function can_view_all_users()
	{
		if (!$this->auth->acl_get('a_user') && $this->user->data['user_type'] != USER_FOUNDER)
		{
			if ($this->profile_data['user_type'] == USER_IGNORE)
			{
				trigger_error('NO_USER');
			}
			else if ($this->profile_data['user_type'] == USER_INACTIVE && $this->profile_data['user_inactive_reason'] != INACTIVE_PROFILE)
			{
				trigger_error('NO_USER');
			}
		}
	}

	/**
	* Display user's groups
	*/
	protected function display_group_membership()
	{
		$auth_hidden_groups = ($this->profile_data['user_id'] === (int) $this->user->data['user_id'] || $this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'));
		$sql_uid_ary = ($auth_hidden_groups) ? [$this->profile_data['user_id']] : [$this->profile_data['user_id'], (int) $this->user->data['user_id']];

		// Do the SQL thang
		$sql = 'SELECT g.group_id, g.group_name, g.group_type, ug.user_id
			FROM ' . GROUPS_TABLE . ' g, ' . USER_GROUP_TABLE . ' ug
			WHERE ' . $this->db->sql_in_set('ug.user_id', $sql_uid_ary) . '
				AND g.group_id = ug.group_id
				AND ug.user_pending = 0';
		$result = $this->db->sql_query($sql);

		// Divide data into profile data and current user data
		$profile_groups = $user_groups = [];

		while ($row = $this->db->sql_fetchrow($result))
		{
			$row['user_id'] = (int) $row['user_id'];
			$row['group_id'] = (int) $row['group_id'];

			if ($row['user_id'] == $this->profile_data['user_id'])
			{
				$profile_groups[] = $row;
			}
			else
			{
				$user_groups[$row['group_id']] = $row['group_id'];
			}
		}
		$this->db->sql_freeresult($result);

		// Filter out hidden groups and sort groups by name
		$group_data = $group_sort = [];

		foreach ($profile_groups as $row)
		{
			// Skip over hidden groups the user cannot see
			if (!$auth_hidden_groups && $row['group_type'] == GROUP_HIDDEN && !isset($user_groups[$row['group_id']]))
			{
				continue;
			}

			$row['group_name'] = $this->group_helper->get_name($row['group_name']);
			$group_sort[$row['group_id']] = utf8_clean_string($row['group_name']);
			$group_data[$row['group_id']] = $row;
		}

		unset($profile_groups);
		unset($user_groups);
		asort($group_sort);

		$group_options = '';
		foreach ($group_sort as $group_id => $null)
		{
			$row = $group_data[$group_id];
			$group_options .= '<option value="' . $row['group_id'] . '"' . (($row['group_id'] == $this->profile_data['group_id']) ? ' selected' : '') . '>' . $row['group_name'] . '</option>';
		}

		unset($group_data);
		unset($group_sort);

		$this->template->assign_vars([
			'S_PROFILE_ACTION'	=> append_sid("{$this->root_path}memberlist.{$this->php_ext}", 'mode=group'),
			'S_GROUP_OPTIONS'	=> $group_options
		]);
	}

	/**
	* Update user's online status
	*/
	protected function display_online_status()
	{
		if ($this->config['load_onlinetrack'])
		{
			$sql = 'SELECT MAX(session_time) AS session_time, MIN(session_viewonline) AS session_viewonline
				FROM ' . SESSIONS_TABLE . '
				WHERE session_user_id = ' . (int) $this->profile_data['user_id'];
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			$this->profile_data['session_time'] = (isset($row['session_time'])) ? $row['session_time'] : 0;
			$this->profile_data['session_viewonline'] = (isset($row['session_viewonline'])) ? $row['session_viewonline'] : 0;

			unset($row);
		}
	}

	/**
	* Display user posts
	*/
	protected function display_posts()
	{
		// Do the relevant calculations
		$memberdays = max(1, round((time() - $this->profile_data['user_regdate']) / 86400));
		$posts_per_day = $this->profile_data['user_posts'] / $memberdays;
		$percentage = ($this->config['num_posts']) ? min(100, ($this->profile_data['user_posts'] / $this->config['num_posts']) * 100) : 0;

		$this->template->assign_vars([
			'POSTS_DAY'	=> $this->language->lang('POST_DAY', $posts_per_day),
			'POSTS_PCT'	=> $this->language->lang('POST_PCT', $percentage)
		]);
	}

	/**
	* Display profile fields
	*/
	protected function display_profile()
	{
		// Friend or blocked?
		$friend = $foe = false;
		$this->check_friend_foe($friend, $foe);

		// We need to check if the modules 'zebra' ('friends' & 'foes' mode),  'notes' ('user_notes' mode) and  'warn' ('warn_user' mode) are accessible to decide if we can display appropriate links
		$zebra_enabled = $friends_enabled = $foes_enabled = $user_notes_enabled = $warn_user_enabled = false;
		$this->check_loaded_modules($zebra_enabled, $friends_enabled, $foes_enabled, $user_notes_enabled, $warn_user_enabled);

		$this->template->assign_vars(phpbb_show_profile($this->profile_data, $user_notes_enabled, $warn_user_enabled));

		$this->template->assign_vars([
			'S_USER_NOTES'		=> $user_notes_enabled,
			'S_WARN_USER'		=> $warn_user_enabled,
			'S_ZEBRA'			=> ($this->user->data['user_id'] != $this->profile_data['user_id'] && $this->user->data['is_registered'] && $zebra_enabled),
			'U_ADD_FRIEND'		=> (!$friend && !$foe && $friends_enabled) ? $this->helper->route('vinabb_web_ucp_route', ['id' => 'zebra', 'add' => urlencode(htmlspecialchars_decode($this->profile_data['username']))]) : '',
			'U_ADD_FOE'			=> (!$friend && !$foe && $foes_enabled) ? $this->helper->route('vinabb_web_ucp_route', ['id' => 'zebra', 'mode' => 'foes', 'add' => urlencode(htmlspecialchars_decode($this->profile_data['username']))]) : '',
			'U_REMOVE_FRIEND'	=> ($friend && $friends_enabled) ? $this->helper->route('vinabb_web_ucp_route', ['id' => 'zebra', 'mode' => 'friends', 'remove' => 1, 'usernames[]' => $this->profile_data['user_id']]) : '',
			'U_REMOVE_FOE'		=> ($foe && $foes_enabled) ? $this->helper->route('vinabb_web_ucp_route', ['id' => 'zebra', 'mode' => 'foes', 'remove' => 1, 'usernames[]' => $this->profile_data['user_id']]) : ''
		]);
	}

	/**
	* Are we friends?
	*
	* @param bool	$friend	true: is friend; false: normal
	* @param bool	$foe	true: blocked; false: normal
	*/
	protected function check_friend_foe(&$friend, &$foe)
	{
		// What colour is the zebra
		$sql = 'SELECT friend, foe
			FROM ' . ZEBRA_TABLE . '
			WHERE zebra_id = ' . (int) $this->profile_data['user_id'] . '
				AND user_id = ' . (int) $this->user->data['user_id'];
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$friend = (bool) $row['friend'];
		$foe = (bool) $row['foe'];
		$this->db->sql_freeresult($result);
	}

	/**
	* Checking loaded UCP/MCP modules
	*
	* @param bool	$zebra_enabled		true: loaded; false: unloaded
	* @param bool	$friends_enabled	true: loaded; false: unloaded
	* @param bool	$foes_enabled		true: loaded; false: unloaded
	* @param bool	$user_notes_enabled	true: loaded; false: unloaded
	* @param bool	$warn_user_enabled	true: loaded; false: unloaded
	*/
	protected function check_loaded_modules(&$zebra_enabled, &$friends_enabled, &$foes_enabled, &$user_notes_enabled, &$warn_user_enabled)
	{
		// Only check if the user is logged in
		if ($this->user->data['is_registered'])
		{
			if (!class_exists('p_master'))
			{
				include "{$this->root_path}includes/functions_module.{$this->php_ext}";
			}

			$module = new \p_master();
			$module->list_modules('ucp');
			$module->list_modules('mcp');

			$zebra_enabled = $module->loaded('ucp_zebra');
			$friends_enabled = $module->loaded('ucp_zebra', 'friends');
			$foes_enabled = $module->loaded('ucp_zebra', 'foes');
			$user_notes_enabled = $module->loaded('mcp_notes', 'user_notes');
			$warn_user_enabled = $module->loaded('mcp_warn', 'warn_user');

			unset($module);
		}
	}

	/**
	* Display custom profile fields
	*/
	protected function display_custom_profile()
	{
		$profile_fields = [];

		if ($this->config['load_cpf_viewprofile'])
		{
			$profile_fields = $this->profile_fields->grab_profile_fields_data($this->profile_data['user_id']);
			$profile_fields = isset($profile_fields[$this->profile_data['user_id']]) ? $this->profile_fields->generate_profile_fields_template_data($profile_fields[$this->profile_data['user_id']]) : [];
		}

		if (!empty($profile_fields['row']))
		{
			$this->template->assign_var('S_CUSTOM_FIELDS', true);
			$this->template->assign_vars($profile_fields['row']);
		}

		if (!empty($profile_fields['blockrow']))
		{
			foreach ($profile_fields['blockrow'] as $field_data)
			{
				$this->template->assign_block_vars('custom_fields', $field_data);
			}
		}
	}

	/**
	* Get the inactive reason in language string by reason ID
	*
	* @param int $reason Reason ID
	* @return string
	*/
	public function get_inactive_reason($reason)
	{
		$data = [
			INACTIVE_REGISTER	=> $this->language->lang('INACTIVE_REASON_REGISTER'),
			INACTIVE_PROFILE	=> $this->language->lang('INACTIVE_REASON_PROFILE'),
			INACTIVE_MANUAL		=> $this->language->lang('INACTIVE_REASON_MANUAL'),
			INACTIVE_REMIND		=> $this->language->lang('INACTIVE_REASON_REMIND')
		];

		return isset($data[$reason]) ? $data[$reason] : $this->language->lang('INACTIVE_REASON_UNKNOWN');
	}

	/**
	* Output inactive reason to template
	*
	* @param int $reason Reason ID
	*/
	public function display_inactive_reason($reason)
	{
		$this->language->add_lang('acp/common');

		$this->template->assign_vars([
			'S_USER_INACTIVE'		=> true,
			'USER_INACTIVE_REASON'	=> $this->get_inactive_reason($reason)
		]);
	}

	/**
	* Get user posts in queue
	*/
	protected function get_posts_in_queue()
	{
		if ($this->auth->acl_getf_global('m_approve') || $this->auth->acl_get('a_user'))
		{
			$sql = 'SELECT COUNT(post_id) AS posts_in_queue
				FROM ' . POSTS_TABLE . '
				WHERE poster_id = ' . (int) $this->profile_data['user_id'] . '
					AND ' . $this->db->sql_in_set('post_visibility', [ITEM_UNAPPROVED, ITEM_REAPPROVE]);
			$result = $this->db->sql_query($sql);
			$this->profile_data['posts_in_queue'] = (int) $this->db->sql_fetchfield('posts_in_queue');
			$this->db->sql_freeresult($result);
		}
		else
		{
			$this->profile_data['posts_in_queue'] = 0;
		}
	}
}
