<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controller;

class user
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\event\dispatcher_interface */
	protected $dispatcher;

	/** @var \phpbb\language\language */
	protected $language;

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
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\event\dispatcher_interface $dispatcher
	* @param \phpbb\language\language $language
	* @param \phpbb\request\request $request
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param \phpbb\group\helper $group_helper
	* @param string $root_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\event\dispatcher_interface $dispatcher,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\phpbb\group\helper $group_helper,
		$root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->dispatcher = $dispatcher;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->group_helper = $group_helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		// Common functions
		include "{$this->root_path}includes/functions_display.{$this->php_ext}";

		// Common language files
		$this->language->add_lang(array('memberlist', 'groups'));

		// Setting a variable to let the style designer know where he is...
		$this->template->assign_var('S_IN_MEMBERLIST', true);
	}

	public function memberlist()
	{

	}

	public function profile()
	{
		// Display a profile
		if ($user_id == ANONYMOUS && !$username)
		{
			trigger_error('NO_USER');
		}

		// Get user...
		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
			WHERE ' . (($username) ? "username_clean = '" . $db->sql_escape(utf8_clean_string($username)) . "'" : "user_id = $user_id");
		$result = $db->sql_query($sql);
		$member = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (!$member)
		{
			trigger_error('NO_USER');
		}

		// a_user admins and founder are able to view inactive users and bots to be able to manage them more easily
		// Normal users are able to see at least users having only changed their profile settings but not yet reactivated.
		if (!$auth->acl_get('a_user') && $user->data['user_type'] != USER_FOUNDER)
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
		$auth_hidden_groups = ($user_id === (int) $user->data['user_id'] || $auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel')) ? true : false;
		$sql_uid_ary = ($auth_hidden_groups) ? array($user_id) : array($user_id, (int) $user->data['user_id']);

		// Do the SQL thang
		$sql = 'SELECT g.group_id, g.group_name, g.group_type, ug.user_id
			FROM ' . GROUPS_TABLE . ' g, ' . USER_GROUP_TABLE . ' ug
			WHERE ' . $db->sql_in_set('ug.user_id', $sql_uid_ary) . '
				AND g.group_id = ug.group_id
				AND ug.user_pending = 0';
		$result = $db->sql_query($sql);

		// Divide data into profile data and current user data
		$profile_groups = $user_groups = array();
		while ($row = $db->sql_fetchrow($result))
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
		$db->sql_freeresult($result);

		// Filter out hidden groups and sort groups by name
		$group_data = $group_sort = array();
		foreach ($profile_groups as $row)
		{
			if (!$auth_hidden_groups && $row['group_type'] == GROUP_HIDDEN && !isset($user_groups[$row['group_id']]))
			{
				// Skip over hidden groups the user cannot see
				continue;
			}

			$row['group_name'] = $group_helper->get_name($row['group_name']);

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
				AND user_id = {$user->data['user_id']}";

		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$foe = ($row['foe']) ? true : false;
		$friend = ($row['friend']) ? true : false;
		$db->sql_freeresult($result);

		if ($config['load_onlinetrack'])
		{
			$sql = 'SELECT MAX(session_time) AS session_time, MIN(session_viewonline) AS session_viewonline
				FROM ' . SESSIONS_TABLE . "
				WHERE session_user_id = $user_id";
			$result = $db->sql_query($sql);
			$row = $db->sql_fetchrow($result);
			$db->sql_freeresult($result);

			$member['session_time'] = (isset($row['session_time'])) ? $row['session_time'] : 0;
			$member['session_viewonline'] = (isset($row['session_viewonline'])) ? $row['session_viewonline'] :	0;
			unset($row);
		}

		if ($config['load_user_activity'])
		{
			display_user_activity($member);
		}

		// Do the relevant calculations
		$memberdays = max(1, round((time() - $member['user_regdate']) / 86400));
		$posts_per_day = $member['user_posts'] / $memberdays;
		$percentage = ($config['num_posts']) ? min(100, ($member['user_posts'] / $config['num_posts']) * 100) : 0;


		if ($member['user_sig'])
		{
			$parse_flags = ($member['user_sig_bbcode_bitfield'] ? OPTION_FLAG_BBCODE : 0) | OPTION_FLAG_SMILIES;
			$member['user_sig'] = generate_text_for_display($member['user_sig'], $member['user_sig_bbcode_uid'], $member['user_sig_bbcode_bitfield'], $parse_flags, true);
		}

		// We need to check if the modules 'zebra' ('friends' & 'foes' mode),  'notes' ('user_notes' mode) and  'warn' ('warn_user' mode) are accessible to decide if we can display appropriate links
		$zebra_enabled = $friends_enabled = $foes_enabled = $user_notes_enabled = $warn_user_enabled = false;

		// Only check if the user is logged in
		if ($user->data['is_registered'])
		{
			if (!class_exists('p_master'))
			{
				include($phpbb_root_path . 'includes/functions_module.' . $phpEx);
			}
			$module = new p_master();

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
		$profile_fields = array();
		if ($config['load_cpf_viewprofile'])
		{
			/* @var $cp \phpbb\profilefields\manager */
			$cp = $phpbb_container->get('profilefields.manager');
			$profile_fields = $cp->grab_profile_fields_data($user_id);
			$profile_fields = (isset($profile_fields[$user_id])) ? $cp->generate_profile_fields_template_data($profile_fields[$user_id]) : array();
		}

		/**
		 * Modify user data before we display the profile
		 *
		 * @event core.memberlist_view_profile
		 * @var	array	member					Array with user's data
		 * @var	bool	user_notes_enabled		Is the mcp user notes module enabled?
		 * @var	bool	warn_user_enabled		Is the mcp warnings module enabled?
		 * @var	bool	zebra_enabled			Is the ucp zebra module enabled?
		 * @var	bool	friends_enabled			Is the ucp friends module enabled?
		 * @var	bool	foes_enabled			Is the ucp foes module enabled?
		 * @var	bool    friend					Is the user friend?
		 * @var	bool	foe						Is the user foe?
		 * @var	array	profile_fields			Array with user's profile field data
		 * @since 3.1.0-a1
		 * @changed 3.1.0-b2 Added friend and foe status
		 * @changed 3.1.0-b3 Added profile fields data
		 */
		$vars = array(
			'member',
			'user_notes_enabled',
			'warn_user_enabled',
			'zebra_enabled',
			'friends_enabled',
			'foes_enabled',
			'friend',
			'foe',
			'profile_fields',
		);
		extract($phpbb_dispatcher->trigger_event('core.memberlist_view_profile', compact($vars)));

		$template->assign_vars(phpbb_show_profile($member, $user_notes_enabled, $warn_user_enabled));

		// If the user has m_approve permission or a_user permission, then list then display unapproved posts
		if ($auth->acl_getf_global('m_approve') || $auth->acl_get('a_user'))
		{
			$sql = 'SELECT COUNT(post_id) as posts_in_queue
				FROM ' . POSTS_TABLE . '
				WHERE poster_id = ' . $user_id . '
					AND ' . $db->sql_in_set('post_visibility', array(ITEM_UNAPPROVED, ITEM_REAPPROVE));
			$result = $db->sql_query($sql);
			$member['posts_in_queue'] = (int) $db->sql_fetchfield('posts_in_queue');
			$db->sql_freeresult($result);
		}
		else
		{
			$member['posts_in_queue'] = 0;
		}

		$template->assign_vars(array(
			'L_POSTS_IN_QUEUE'	=> $user->lang('NUM_POSTS_IN_QUEUE', $member['posts_in_queue']),

			'POSTS_DAY'			=> $user->lang('POST_DAY', $posts_per_day),
			'POSTS_PCT'			=> $user->lang('POST_PCT', $percentage),

			'SIGNATURE'		=> $member['user_sig'],
			'POSTS_IN_QUEUE'=> $member['posts_in_queue'],

			'PM_IMG'		=> $user->img('icon_contact_pm', $user->lang['SEND_PRIVATE_MESSAGE']),
			'L_SEND_EMAIL_USER'	=> $user->lang('SEND_EMAIL_USER', $member['username']),
			'EMAIL_IMG'		=> $user->img('icon_contact_email', $user->lang['EMAIL']),
			'JABBER_IMG'	=> $user->img('icon_contact_jabber', $user->lang['JABBER']),
			'SEARCH_IMG'	=> $user->img('icon_user_search', $user->lang['SEARCH']),

			'S_PROFILE_ACTION'	=> append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=group'),
			'S_GROUP_OPTIONS'	=> $group_options,
			'S_CUSTOM_FIELDS'	=> (isset($profile_fields['row']) && sizeof($profile_fields['row'])) ? true : false,

			'U_USER_ADMIN'			=> ($auth->acl_get('a_user')) ? append_sid("{$phpbb_admin_path}index.$phpEx", 'i=users&amp;mode=overview&amp;u=' . $user_id, true, $user->session_id) : '',
			'U_USER_BAN'			=> ($auth->acl_get('m_ban') && $user_id != $user->data['user_id']) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=ban&amp;mode=user&amp;u=' . $user_id, true, $user->session_id) : '',
			'U_MCP_QUEUE'			=> ($auth->acl_getf_global('m_approve')) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=queue', true, $user->session_id) : '',

			'U_SWITCH_PERMISSIONS'	=> ($auth->acl_get('a_switchperm') && $user->data['user_id'] != $user_id) ? append_sid("{$phpbb_root_path}ucp.$phpEx", "mode=switch_perm&amp;u={$user_id}&amp;hash=" . generate_link_hash('switchperm')) : '',
			'U_EDIT_SELF'			=> ($user_id == $user->data['user_id'] && $auth->acl_get('u_chgprofileinfo')) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=ucp_profile&amp;mode=profile_info') : '',

			'S_USER_NOTES'		=> ($user_notes_enabled) ? true : false,
			'S_WARN_USER'		=> ($warn_user_enabled) ? true : false,
			'S_ZEBRA'			=> ($user->data['user_id'] != $user_id && $user->data['is_registered'] && $zebra_enabled) ? true : false,
			'U_ADD_FRIEND'		=> (!$friend && !$foe && $friends_enabled) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=zebra&amp;add=' . urlencode(htmlspecialchars_decode($member['username']))) : '',
			'U_ADD_FOE'			=> (!$friend && !$foe && $foes_enabled) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=zebra&amp;mode=foes&amp;add=' . urlencode(htmlspecialchars_decode($member['username']))) : '',
			'U_REMOVE_FRIEND'	=> ($friend && $friends_enabled) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=zebra&amp;remove=1&amp;usernames[]=' . $user_id) : '',
			'U_REMOVE_FOE'		=> ($foe && $foes_enabled) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=zebra&amp;remove=1&amp;mode=foes&amp;usernames[]=' . $user_id) : '',

			'U_CANONICAL'	=> generate_board_url() . '/' . append_sid("memberlist.$phpEx", 'mode=viewprofile&amp;u=' . $user_id, true, ''),
		));

		if (!empty($profile_fields['row']))
		{
			$template->assign_vars($profile_fields['row']);
		}

		if (!empty($profile_fields['blockrow']))
		{
			foreach ($profile_fields['blockrow'] as $field_data)
			{
				$template->assign_block_vars('custom_fields', $field_data);
			}
		}

		// Inactive reason/account?
		if ($member['user_type'] == USER_INACTIVE)
		{
			$user->add_lang('acp/common');

			$inactive_reason = $user->lang['INACTIVE_REASON_UNKNOWN'];

			switch ($member['user_inactive_reason'])
			{
				case INACTIVE_REGISTER:
					$inactive_reason = $user->lang['INACTIVE_REASON_REGISTER'];
					break;

				case INACTIVE_PROFILE:
					$inactive_reason = $user->lang['INACTIVE_REASON_PROFILE'];
					break;

				case INACTIVE_MANUAL:
					$inactive_reason = $user->lang['INACTIVE_REASON_MANUAL'];
					break;

				case INACTIVE_REMIND:
					$inactive_reason = $user->lang['INACTIVE_REASON_REMIND'];
					break;
			}

			$template->assign_vars(array(
					'S_USER_INACTIVE'		=> true,
					'USER_INACTIVE_REASON'	=> $inactive_reason)
			);
		}

		// Now generate page title
		$page_title = sprintf($user->lang['VIEWING_PROFILE'], $member['username']);
		$template_html = 'memberlist_view.html';
	}

	public function contact_admin()
	{
		define('SKIP_CHECK_BAN', true);
		define('SKIP_CHECK_DISABLED', true);
	}

	public function email()
	{
		if (!class_exists('messenger'))
		{
			include($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);
		}

		$user_id	= $request->variable('u', 0);
		$topic_id	= $request->variable('t', 0);

		if ($user_id)
		{
			$form_name = 'user';
		}
		else if ($topic_id)
		{
			$form_name = 'topic';
		}
		else if ($mode === 'contactadmin')
		{
			$form_name = 'admin';
		}
		else
		{
			trigger_error('NO_EMAIL');
		}

		/** @var $form \phpbb\message\form */
		$form = $phpbb_container->get('message.form.' . $form_name);

		$form->bind($request);
		$error = $form->check_allow();
		if ($error)
		{
			trigger_error($error);
		}

		if ($request->is_set_post('submit'))
		{
			$messenger = new messenger(false);
			$form->submit($messenger);
		}

		$page_title = $form->get_page_title();
		$template_html = $form->get_template_file();
		$form->render($template);
	}

	public function contact()
	{
		$page_title = $user->lang['IM_USER'];
		$template_html = 'memberlist_im.html';

		if (!$auth->acl_get('u_sendim'))
		{
			trigger_error('NOT_AUTHORISED');
		}

		$presence_img = '';
		switch ($action)
		{
			case 'jabber':
				$lang = 'JABBER';
				$sql_field = 'user_jabber';
				$s_select = (@extension_loaded('xml') && $config['jab_enable']) ? 'S_SEND_JABBER' : 'S_NO_SEND_JABBER';
				$s_action = append_sid("{$phpbb_root_path}memberlist.$phpEx", "mode=contact&amp;action=$action&amp;u=$user_id");
				break;

			default:
				trigger_error('NO_MODE', E_USER_ERROR);
				break;
		}

		// Grab relevant data
		$sql = "SELECT user_id, username, user_email, user_lang, $sql_field
			FROM " . USERS_TABLE . "
			WHERE user_id = $user_id
				AND user_type IN (" . USER_NORMAL . ', ' . USER_FOUNDER . ')';
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (!$row)
		{
			trigger_error('NO_USER');
		}
		else if (empty($row[$sql_field]))
		{
			trigger_error('IM_NO_DATA');
		}

		// Post data grab actions
		switch ($action)
		{
			case 'jabber':
				add_form_key('memberlist_messaging');

				if ($submit && @extension_loaded('xml') && $config['jab_enable'])
				{
					if (check_form_key('memberlist_messaging'))
					{

						include_once($phpbb_root_path . 'includes/functions_messenger.' . $phpEx);

						$subject = sprintf($user->lang['IM_JABBER_SUBJECT'], $user->data['username'], $config['server_name']);
						$message = $request->variable('message', '', true);

						if (empty($message))
						{
							trigger_error('EMPTY_MESSAGE_IM');
						}

						$messenger = new messenger(false);

						$messenger->template('profile_send_im', $row['user_lang']);
						$messenger->subject(htmlspecialchars_decode($subject));

						$messenger->replyto($user->data['user_email']);
						$messenger->set_addresses($row);

						$messenger->assign_vars(array(
								'BOARD_CONTACT'	=> phpbb_get_board_contact($config, $phpEx),
								'FROM_USERNAME'	=> htmlspecialchars_decode($user->data['username']),
								'TO_USERNAME'	=> htmlspecialchars_decode($row['username']),
								'MESSAGE'		=> htmlspecialchars_decode($message))
						);

						$messenger->send(NOTIFY_IM);

						$s_select = 'S_SENT_JABBER';
					}
					else
					{
						trigger_error('FORM_INVALID');
					}
				}
				break;
		}

		// Send vars to the template
		$template->assign_vars(array(
				'IM_CONTACT'	=> $row[$sql_field],
				'A_IM_CONTACT'	=> addslashes($row[$sql_field]),

				'USERNAME'		=> $row['username'],
				'CONTACT_NAME'	=> $row[$sql_field],
				'SITENAME'		=> $config['sitename'],

				'PRESENCE_IMG'		=> $presence_img,

				'L_SEND_IM_EXPLAIN'	=> $user->lang['IM_' . $lang],
				'L_IM_SENT_JABBER'	=> sprintf($user->lang['IM_SENT_JABBER'], $row['username']),

				$s_select			=> true,
				'S_IM_ACTION'		=> $s_action)
		);
	}

	public function team()
	{
		// Display a listing of board admins, moderators
		include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

		$page_title = $user->lang['THE_TEAM'];
		$template_html = 'memberlist_team.html';

		$sql = 'SELECT *
			FROM ' . TEAMPAGE_TABLE . '
			ORDER BY teampage_position ASC';
		$result = $db->sql_query($sql, 3600);
		$teampage_data = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		$sql_ary = array(
			'SELECT'	=> 'g.group_id, g.group_name, g.group_colour, g.group_type, ug.user_id as ug_user_id, t.teampage_id',

			'FROM'		=> array(GROUPS_TABLE => 'g'),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(TEAMPAGE_TABLE => 't'),
					'ON'	=> 't.group_id = g.group_id',
				),
				array(
					'FROM'	=> array(USER_GROUP_TABLE => 'ug'),
					'ON'	=> 'ug.group_id = g.group_id AND ug.user_pending = 0 AND ug.user_id = ' . (int) $user->data['user_id'],
				),
			),
		);

		$result = $db->sql_query($db->sql_build_query('SELECT', $sql_ary));

		$group_ids = $groups_ary = array();
		while ($row = $db->sql_fetchrow($result))
		{
			if ($row['group_type'] == GROUP_HIDDEN && !$auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel') && $row['ug_user_id'] != $user->data['user_id'])
			{
				$row['group_name'] = $user->lang['GROUP_UNDISCLOSED'];
				$row['u_group'] = '';
			}
			else
			{
				$row['group_name'] = $group_helper->get_name($row['group_name']);
				$row['u_group'] = append_sid("{$phpbb_root_path}memberlist.$phpEx", 'mode=group&amp;g=' . $row['group_id']);
			}

			if ($row['teampage_id'])
			{
				// Only put groups into the array we want to display.
				// We are fetching all groups, to ensure we got all data for default groups.
				$group_ids[] = (int) $row['group_id'];
			}
			$groups_ary[(int) $row['group_id']] = $row;
		}
		$db->sql_freeresult($result);

		$sql_ary = array(
			'SELECT'	=> 'u.user_id, u.group_id as default_group, u.username, u.username_clean, u.user_colour, u.user_type, u.user_rank, u.user_posts, u.user_allow_pm, g.group_id',

			'FROM'		=> array(
				USER_GROUP_TABLE => 'ug',
			),

			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(USERS_TABLE => 'u'),
					'ON'	=> 'ug.user_id = u.user_id AND ug.user_pending = 0',
				),
				array(
					'FROM'	=> array(GROUPS_TABLE => 'g'),
					'ON'	=> 'ug.group_id = g.group_id',
				),
			),

			'WHERE'		=> $db->sql_in_set('g.group_id', $group_ids, false, true),

			'ORDER_BY'	=> 'u.username_clean ASC',
		);

		/**
		 * Modify the query used to get the users for the team page
		 *
		 * @event core.memberlist_team_modify_query
		 * @var array	sql_ary			Array containing the query
		 * @var array	group_ids		Array of group ids
		 * @var array	teampage_data	The teampage data
		 * @since 3.1.3-RC1
		 */
		$vars = array(
			'sql_ary',
			'group_ids',
			'teampage_data',
		);
		extract($phpbb_dispatcher->trigger_event('core.memberlist_team_modify_query', compact($vars)));

		$result = $db->sql_query($db->sql_build_query('SELECT', $sql_ary));

		$user_ary = $user_ids = $group_users = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$row['forums'] = '';
			$row['forums_ary'] = array();
			$user_ary[(int) $row['user_id']] = $row;
			$user_ids[] = (int) $row['user_id'];
			$group_users[(int) $row['group_id']][] = (int) $row['user_id'];
		}
		$db->sql_freeresult($result);

		$user_ids = array_unique($user_ids);

		if (!empty($user_ids) && $config['teampage_forums'])
		{
			$template->assign_var('S_DISPLAY_MODERATOR_FORUMS', true);
			// Get all moderators
			$perm_ary = $auth->acl_get_list($user_ids, array('m_'), false);

			foreach ($perm_ary as $forum_id => $forum_ary)
			{
				foreach ($forum_ary as $auth_option => $id_ary)
				{
					foreach ($id_ary as $id)
					{
						if (!$forum_id)
						{
							$user_ary[$id]['forums'] = $user->lang['ALL_FORUMS'];
						}
						else
						{
							$user_ary[$id]['forums_ary'][] = $forum_id;
						}
					}
				}
			}

			$sql = 'SELECT forum_id, forum_name
				FROM ' . FORUMS_TABLE;
			$result = $db->sql_query($sql);

			$forums = array();
			while ($row = $db->sql_fetchrow($result))
			{
				$forums[$row['forum_id']] = $row['forum_name'];
			}
			$db->sql_freeresult($result);

			foreach ($user_ary as $user_id => $user_data)
			{
				if (!$user_data['forums'])
				{
					foreach ($user_data['forums_ary'] as $forum_id)
					{
						$user_ary[$user_id]['forums_options'] = true;
						if (isset($forums[$forum_id]))
						{
							if ($auth->acl_get('f_list', $forum_id))
							{
								$user_ary[$user_id]['forums'] .= '<option value="">' . $forums[$forum_id] . '</option>';
							}
						}
					}
				}
			}
		}

		$parent_team = 0;
		foreach ($teampage_data as $team_data)
		{
			// If this team entry has no group, it's a category
			if (!$team_data['group_id'])
			{
				$template->assign_block_vars('group', array(
					'GROUP_NAME'  => $team_data['teampage_name'],
				));

				$parent_team = (int) $team_data['teampage_id'];
				continue;
			}

			$group_data = $groups_ary[(int) $team_data['group_id']];
			$group_id = (int) $team_data['group_id'];

			if (!$team_data['teampage_parent'])
			{
				// If the group does not have a parent category, we display the groupname as category
				$template->assign_block_vars('group', array(
					'GROUP_NAME'	=> $group_data['group_name'],
					'GROUP_COLOR'	=> $group_data['group_colour'],
					'U_GROUP'		=> $group_data['u_group'],
				));
			}

			// Display group members.
			if (!empty($group_users[$group_id]))
			{
				foreach ($group_users[$group_id] as $user_id)
				{
					if (isset($user_ary[$user_id]))
					{
						$row = $user_ary[$user_id];
						if ($config['teampage_memberships'] == 1 && ($group_id != $groups_ary[$row['default_group']]['group_id']) && $groups_ary[$row['default_group']]['teampage_id'])
						{
							// Display users in their primary group, instead of the first group, when it is displayed on the teampage.
							continue;
						}

						$user_rank_data = phpbb_get_user_rank($row, (($row['user_id'] == ANONYMOUS) ? false : $row['user_posts']));

						$template_vars = array(
							'USER_ID'		=> $row['user_id'],
							'FORUMS'		=> $row['forums'],
							'FORUM_OPTIONS'	=> (isset($row['forums_options'])) ? true : false,
							'RANK_TITLE'	=> $user_rank_data['title'],

							'GROUP_NAME'	=> $groups_ary[$row['default_group']]['group_name'],
							'GROUP_COLOR'	=> $groups_ary[$row['default_group']]['group_colour'],
							'U_GROUP'		=> $groups_ary[$row['default_group']]['u_group'],

							'RANK_IMG'		=> $user_rank_data['img'],
							'RANK_IMG_SRC'	=> $user_rank_data['img_src'],

							'S_INACTIVE'	=> $row['user_type'] == USER_INACTIVE,

							'U_PM'			=> ($config['allow_privmsg'] && $auth->acl_get('u_sendpm') && ($row['user_allow_pm'] || $auth->acl_gets('a_', 'm_') || $auth->acl_getf_global('m_'))) ? append_sid("{$phpbb_root_path}ucp.$phpEx", 'i=pm&amp;mode=compose&amp;u=' . $row['user_id']) : '',

							'USERNAME_FULL'		=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
							'USERNAME'			=> get_username_string('username', $row['user_id'], $row['username'], $row['user_colour']),
							'USER_COLOR'		=> get_username_string('colour', $row['user_id'], $row['username'], $row['user_colour']),
							'U_VIEW_PROFILE'	=> get_username_string('profile', $row['user_id'], $row['username'], $row['user_colour']),
						);

						/**
						 * Modify the template vars for displaying the user in the groups on the teampage
						 *
						 * @event core.memberlist_team_modify_template_vars
						 * @var array	template_vars		Array containing the query
						 * @var array	row					Array containing the action user row
						 * @var array	groups_ary			Array of groups with all users that should be displayed
						 * @since 3.1.3-RC1
						 */
						$vars = array(
							'template_vars',
							'row',
							'groups_ary',
						);
						extract($phpbb_dispatcher->trigger_event('core.memberlist_team_modify_template_vars', compact($vars)));

						$template->assign_block_vars('group.user', $template_vars);

						if ($config['teampage_memberships'] != 2)
						{
							unset($user_ary[$user_id]);
						}
					}
				}
			}
		}

		$template->assign_vars(array(
				'PM_IMG'		=> $user->img('icon_contact_pm', $user->lang['SEND_PRIVATE_MESSAGE']))
		);
	}

	public function group()
	{

	}

	public function search()
	{

	}

	public function livesearch()
	{
		if (!$this->config['allow_live_searches'])
		{
			trigger_error('LIVE_SEARCHES_NOT_ALLOWED');
		}

		$username_chars = $request->variable('username', '', true);

		$sql = 'SELECT username, user_id, user_colour
			FROM ' . USERS_TABLE . '
			WHERE ' . $db->sql_in_set('user_type', $user_types) . '
				AND username_clean ' . $db->sql_like_expression(utf8_clean_string($username_chars) . $db->get_any_char());
		$result = $db->sql_query_limit($sql, 10);
		$user_list = array();

		while ($row = $db->sql_fetchrow($result))
		{
			$user_list[] = array(
				'user_id'		=> (int) $row['user_id'],
				'result'		=> $row['username'],
				'username_full'	=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				'display'		=> get_username_string('no_profile', $row['user_id'], $row['username'], $row['user_colour']),
			);
		}
		$db->sql_freeresult($result);
		$json_response = new \phpbb\json_response();
		$json_response->send(array(
			'keyword' => $username_chars,
			'results' => $user_list,
		));
	}
}
