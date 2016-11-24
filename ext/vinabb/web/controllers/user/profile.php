<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

class profile
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

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

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
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

		// Display a profile
		if ($username == '')
		{
			trigger_error('NO_USER');
		}

		// Get user...
		$sql = 'SELECT *
			FROM ' . USERS_TABLE . "
			WHERE username_clean = '" . $this->db->sql_escape(utf8_clean_string($username)) . "'";
		$result = $this->db->sql_query($sql);
		$member = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($member === false)
		{
			trigger_error('NO_USER');
		}

		// a_user admins and founder are able to view inactive users and bots to be able to manage them more easily
		// Normal users are able to see at least users having only changed their profile settings but not yet reactivated.
		if (!$this->auth->acl_get('a_user') && $this->user->data['user_type'] != USER_FOUNDER)
		{
			if ($member['user_type'] == USER_IGNORE)
			{
				trigger_error('NO_USER');
			}
			else if ($member['user_type'] == USER_INACTIVE && $member['user_inactive_reason'] != INACTIVE_PROFILE)
			{
				trigger_error('NO_USER');
			}
		}

		$user_id = (int) $member['user_id'];

		// Get group memberships
		// Also get visiting user's groups to determine hidden group memberships if necessary.
		$auth_hidden_groups = ($user_id === (int) $this->user->data['user_id'] || $this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel')) ? true : false;
		$sql_uid_ary = ($auth_hidden_groups) ? array($user_id) : array($user_id, (int) $this->user->data['user_id']);

		// Do the SQL thang
		$sql = 'SELECT g.group_id, g.group_name, g.group_type, ug.user_id
			FROM ' . GROUPS_TABLE . ' g, ' . USER_GROUP_TABLE . ' ug
			WHERE ' . $this->db->sql_in_set('ug.user_id', $sql_uid_ary) . '
				AND g.group_id = ug.group_id
				AND ug.user_pending = 0';
		$result = $this->db->sql_query($sql);

		// Divide data into profile data and current user data
		$profile_groups = $user_groups = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$row['user_id'] = (int) $row['user_id'];
			$row['group_id'] = (int) $row['group_id'];

			if ($row['user_id'] == $user_id)
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
		$group_data = $group_sort = array();
		foreach ($profile_groups as $row)
		{
			if (!$auth_hidden_groups && $row['group_type'] == GROUP_HIDDEN && !isset($user_groups[$row['group_id']]))
			{
				// Skip over hidden groups the user cannot see
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

			$group_options .= '<option value="' . $row['group_id'] . '"' . (($row['group_id'] == $member['group_id']) ? ' selected="selected"' : '') . '>' . $row['group_name'] . '</option>';
		}
		unset($group_data);
		unset($group_sort);

		// What colour is the zebra
		$sql = 'SELECT friend, foe
			FROM ' . ZEBRA_TABLE . "
			WHERE zebra_id = $user_id
				AND user_id = {$this->user->data['user_id']}";

		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$foe = ($row['foe']) ? true : false;
		$friend = ($row['friend']) ? true : false;
		$this->db->sql_freeresult($result);

		if ($this->config['load_onlinetrack'])
		{
			$sql = 'SELECT MAX(session_time) AS session_time, MIN(session_viewonline) AS session_viewonline
				FROM ' . SESSIONS_TABLE . "
				WHERE session_user_id = $user_id";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			$member['session_time'] = (isset($row['session_time'])) ? $row['session_time'] : 0;
			$member['session_viewonline'] = (isset($row['session_viewonline'])) ? $row['session_viewonline'] :	0;
			unset($row);
		}

		if ($this->config['load_user_activity'])
		{
			display_user_activity($member);
		}

		// Do the relevant calculations
		$memberdays = max(1, round((time() - $member['user_regdate']) / 86400));
		$posts_per_day = $member['user_posts'] / $memberdays;
		$percentage = ($this->config['num_posts']) ? min(100, ($member['user_posts'] / $this->config['num_posts']) * 100) : 0;

		if ($member['user_sig'])
		{
			$parse_flags = ($member['user_sig_bbcode_bitfield'] ? OPTION_FLAG_BBCODE : 0) | OPTION_FLAG_SMILIES;
			$member['user_sig'] = generate_text_for_display($member['user_sig'], $member['user_sig_bbcode_uid'], $member['user_sig_bbcode_bitfield'], $parse_flags, true);
		}

		// We need to check if the modules 'zebra' ('friends' & 'foes' mode),  'notes' ('user_notes' mode) and  'warn' ('warn_user' mode) are accessible to decide if we can display appropriate links
		$zebra_enabled = $friends_enabled = $foes_enabled = $user_notes_enabled = $warn_user_enabled = false;

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

			$user_notes_enabled = ($module->loaded('mcp_notes', 'user_notes')) ? true : false;
			$warn_user_enabled = ($module->loaded('mcp_warn', 'warn_user')) ? true : false;
			$zebra_enabled = ($module->loaded('ucp_zebra')) ? true : false;
			$friends_enabled = ($module->loaded('ucp_zebra', 'friends')) ? true : false;
			$foes_enabled = ($module->loaded('ucp_zebra', 'foes')) ? true : false;

			unset($module);
		}

		// Custom Profile Fields
		$profile_fields = [];

		if ($this->config['load_cpf_viewprofile'])
		{
			$profile_fields = $this->profile_fields->grab_profile_fields_data($user_id);
			$profile_fields = (isset($profile_fields[$user_id])) ? $this->profile_fields->generate_profile_fields_template_data($profile_fields[$user_id]) : array();
		}

		$this->template->assign_vars(phpbb_show_profile($member, $user_notes_enabled, $warn_user_enabled));

		// If the user has m_approve permission or a_user permission, then list then display unapproved posts
		if ($this->auth->acl_getf_global('m_approve') || $this->auth->acl_get('a_user'))
		{
			$sql = 'SELECT COUNT(post_id) as posts_in_queue
				FROM ' . POSTS_TABLE . '
				WHERE poster_id = ' . $user_id . '
					AND ' . $this->db->sql_in_set('post_visibility', array(ITEM_UNAPPROVED, ITEM_REAPPROVE));
			$result = $this->db->sql_query($sql);
			$member['posts_in_queue'] = (int) $this->db->sql_fetchfield('posts_in_queue');
			$this->db->sql_freeresult($result);
		}
		else
		{
			$member['posts_in_queue'] = 0;
		}

		$this->template->assign_vars([
			'L_POSTS_IN_QUEUE'	=> $this->language->lang('NUM_POSTS_IN_QUEUE', $member['posts_in_queue']),

			'POSTS_DAY'			=> $this->language->lang('POST_DAY', $posts_per_day),
			'POSTS_PCT'			=> $this->language->lang('POST_PCT', $percentage),

			'SIGNATURE'		=> $member['user_sig'],
			'POSTS_IN_QUEUE'=> $member['posts_in_queue'],

			'PM_IMG'		=> $this->user->img('icon_contact_pm', $this->language->lang('SEND_PRIVATE_MESSAGE')),
			'L_SEND_EMAIL_USER'	=> $this->language->lang('SEND_EMAIL_USER', $member['username']),
			'EMAIL_IMG'		=> $this->user->img('icon_contact_email', $this->language->lang('EMAIL')),
			'JABBER_IMG'	=> $this->user->img('icon_contact_jabber', $this->language->lang('JABBER')),
			'SEARCH_IMG'	=> $this->user->img('icon_user_search', $this->language->lang('SEARCH')),

			'S_PROFILE_ACTION'	=> append_sid("{$this->root_path}memberlist.{$this->php_ext}", 'mode=group'),
			'S_GROUP_OPTIONS'	=> $group_options,
			'S_CUSTOM_FIELDS'	=> (isset($profile_fields['row']) && sizeof($profile_fields['row'])) ? true : false,

			'U_USER_ADMIN'			=> ($this->auth->acl_get('a_user')) ? append_sid("{$this->admin_path}index.{$this->php_ext}", 'i=users&amp;mode=overview&amp;u=' . $user_id, true, $this->user->session_id) : '',
			'U_USER_BAN'			=> ($this->auth->acl_get('m_ban') && $user_id != $this->user->data['user_id']) ? append_sid("{$this->root_path}mcp.{$this->php_ext}", 'i=ban&amp;mode=user&amp;u=' . $user_id, true, $this->user->session_id) : '',
			'U_MCP_QUEUE'			=> ($this->auth->acl_getf_global('m_approve')) ? append_sid("{$this->root_path}mcp.{$this->php_ext}", 'i=queue', true, $this->user->session_id) : '',

			'U_SWITCH_PERMISSIONS'	=> ($this->auth->acl_get('a_switchperm') && $this->user->data['user_id'] != $user_id) ? append_sid("{$this->root_path}ucp.{$this->php_ext}", "mode=switch_perm&amp;u={$user_id}&amp;hash=" . generate_link_hash('switchperm')) : '',
			'U_EDIT_SELF'			=> ($user_id == $this->user->data['user_id'] && $this->auth->acl_get('u_chgprofileinfo')) ? append_sid("{$this->root_path}ucp.{$this->php_ext}", 'i=ucp_profile&amp;mode=profile_info') : '',

			'S_USER_NOTES'		=> ($user_notes_enabled) ? true : false,
			'S_WARN_USER'		=> ($warn_user_enabled) ? true : false,
			'S_ZEBRA'			=> ($this->user->data['user_id'] != $user_id && $this->user->data['is_registered'] && $zebra_enabled) ? true : false,
			'U_ADD_FRIEND'		=> (!$friend && !$foe && $friends_enabled) ? $this->helper->route('vinabb_web_ucp_route', ['id' => 'zebra', 'add' => urlencode(htmlspecialchars_decode($member['username']))]) : '',
			'U_ADD_FOE'			=> (!$friend && !$foe && $foes_enabled) ? $this->helper->route('vinabb_web_ucp_route', ['id' => 'zebra', 'mode' => 'foes', 'add' => urlencode(htmlspecialchars_decode($member['username']))]) : '',
			'U_REMOVE_FRIEND'	=> ($friend && $friends_enabled) ? $this->helper->route('vinabb_web_ucp_route', ['id' => 'zebra', 'mode' => 'friends', 'remove' => 1, 'usernames[]' => $user_id]) : '',
			'U_REMOVE_FOE'		=> ($foe && $foes_enabled) ? $this->helper->route('vinabb_web_ucp_route', ['id' => 'zebra', 'mode' => 'foes', 'remove' => 1, 'usernames[]' => $user_id]) : '',

			'U_CANONICAL'	=> generate_board_url() . '/' . $this->helper->route('vinabb_web_user_profile_route', ['username' => $username], true, '')
		]);

		if (!empty($profile_fields['row']))
		{
			$this->template->assign_vars($profile_fields['row']);
		}

		if (!empty($profile_fields['blockrow']))
		{
			foreach ($profile_fields['blockrow'] as $field_data)
			{
				$this->template->assign_block_vars('custom_fields', $field_data);
			}
		}

		// Inactive reason/account?
		if ($member['user_type'] == USER_INACTIVE)
		{
			$this->language->add_lang('acp/common');

			$inactive_reason = $this->language->lang('INACTIVE_REASON_UNKNOWN');

			switch ($member['user_inactive_reason'])
			{
				case INACTIVE_REGISTER:
					$inactive_reason = $this->language->lang('INACTIVE_REASON_REGISTER');
				break;

				case INACTIVE_PROFILE:
					$inactive_reason = $this->language->lang('INACTIVE_REASON_PROFILE');
				break;

				case INACTIVE_MANUAL:
					$inactive_reason = $this->language->lang('INACTIVE_REASON_MANUAL');
				break;

				case INACTIVE_REMIND:
					$inactive_reason = $this->language->lang('INACTIVE_REASON_REMIND');
				break;
			}

			$this->template->assign_vars([
				'S_USER_INACTIVE'		=> true,
				'USER_INACTIVE_REASON'	=> $inactive_reason
			]);
		}

		return $this->helper->render('memberlist_view.html', $this->language->lang('VIEWING_PROFILE', $member['username']));
	}

	public function id($user_id)
	{
		// Display a profile
		if ($user_id == ANONYMOUS)
		{
			trigger_error('NO_USER');
		}

		$sql = 'SELECT username
			FROM ' . USERS_TABLE . "
			WHERE user_id = $user_id";
		$result = $this->db->sql_query($sql);
		$username = (string) $this->db->sql_fetchfield('username');
		$this->db->sql_freeresult($result);

		if ($username === false)
		{
			trigger_error('NO_USER');
		}

		return $this->main($username);
	}
}
