<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use vinabb\web\includes\constants;

/**
* PHP events
*/
class common implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth $auth */
	protected $auth;

	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \phpbb\controller\helper $helper */
	protected $helper;

	/** @var \vinabb\web\events\helper\helper_interface $event_helper */
	protected $event_helper;

	/** @var \vinabb\web\controllers\helper_interface $ext_helper */
	protected $ext_helper;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth									$auth			Authentication object
	* @param \vinabb\web\controllers\cache\service_interface	$cache			Cache service
	* @param \phpbb\config\config								$config			Config object
	* @param \phpbb\language\language							$language		Language object
	* @param \phpbb\template\template							$template		Template object
	* @param \phpbb\user										$user			User object
	* @param \phpbb\controller\helper							$helper			Controller helper
	* @param \vinabb\web\events\helper\helper_interface			$event_helper	Event helper
	* @param \vinabb\web\controllers\helper_interface			$ext_helper		Extension helper
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\events\helper\helper_interface $event_helper,
		\vinabb\web\controllers\helper_interface $ext_helper
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->language = $language;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->event_helper = $event_helper;
		$this->ext_helper = $ext_helper;
	}

	/**
	* List of phpBB's PHP events to be used
	*
	* @return array
	*/
	static public function getSubscribedEvents()
	{
		return [
			'core.user_setup'							=> 'user_setup',
			'core.page_header_after'					=> 'page_header_after',
			'core.modify_username_string'				=> 'modify_username_string',
			'core.get_avatar_after'						=> 'get_avatar_after',
			'core.login_box_redirect'					=> 'login_box_redirect',
			'core.display_forums_modify_template_vars'	=> 'display_forums_modify_template_vars',
			'core.posting_modify_template_vars'			=> 'posting_modify_template_vars',
			'core.memberlist_prepare_profile_data'		=> 'memberlist_prepare_profile_data',
			'core.ucp_pm_view_messsage'					=> 'ucp_pm_view_messsage',
			'core.obtain_users_online_string_modify'	=> 'obtain_users_online_string_modify'
		];
	}

	/**
	* core.user_setup
	*
	* @param array $event Data from the PHP event
	*/
	public function user_setup($event)
	{
		// Add our common language variables
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'vinabb/web',
			'lang_set' => 'common'
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	* core.page_header_after
	*
	* @param array $event Data from the PHP event
	*/
	public function page_header_after($event)
	{
		// Display the forum list on every page
		$this->event_helper->list_forums();

		// Display phpBB Resource's category list on every page
		$this->event_helper->list_bb_cats();

		// Language switcher for guests
		if ($this->user->data['user_id'] == ANONYMOUS && $this->config['vinabb_web_lang_switch'] != '')
		{
			$this->event_helper->add_lang_switcher();
		}

		// Add template variables
		$this->event_helper->auth_to_template();
		$this->event_helper->config_to_template();
		$this->event_helper->add_common_tpl_vars();
		$this->event_helper->add_new_routes();

		// If Gravatar, only return the attribute src="..."
		if ($this->user->data['user_avatar_type'] == 'avatar.driver.gravatar')
		{
			$this->template->assign_var('CURRENT_USER_AVATAR', $this->get_gravatar_url($this->user->data));
		}

		// Maintenance mode
		$this->event_helper->maintenance_mode();
	}

	/**
	* core.modify_username_string
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_username_string($event)
	{
		// Rewrite all profile URLs with our new route
		if ($event['user_id'])
		{
			if ($event['username'] != '')
			{
				$url = $this->helper->route('vinabb_web_user_profile_route', ['username' => $event['username']]);
			}
			else
			{
				$url = $this->helper->route('vinabb_web_user_profile_id_route', ['user_id' => $event['user_id']]);
			}

			if ($event['mode'] == 'profile')
			{
				$event['username_string'] = $url;
			}
			else if ($event['mode'] == 'full')
			{
				$event['username_string'] = '<a href="' . $url . '" style="color: ' . $event['username_colour'] . ';" class="username-coloured">' . $event['username'] . '</a>';
			}
		}
	}

	/**
	* core.get_avatar_after
	*
	* @param array $event Data from the PHP event
	*/
	public function get_avatar_after($event)
	{
		// Modify phpbb_get_avatar() to return only the attribute src=""
		$avatar_data = $event['avatar_data'];
		$event['html'] = $avatar_data['src'];
	}

	/**
	* core.login_box_redirect
	*
	* @param array $event Data from the PHP event
	*/
	public function login_box_redirect($event)
	{
		// Prevent standard administrators to login successfully if the maintenance mode is enabled with founder level
		if ($this->config['vinabb_web_maintenance_mode'] == constants::MAINTENANCE_MODE_FOUNDER && $this->user->data['user_type'] != USER_FOUNDER)
		{
			$this->user->unset_admin();
		}
	}

	/**
	* core.display_forums_modify_template_vars
	*
	* @param array $event Data from the PHP event
	*/
	public function display_forums_modify_template_vars($event)
	{
		// Add description to the subforum list
		$subforums = $event['subforums_row'];
		$forum_data = $this->cache->get_forum_data();

		foreach ($subforums as $i => $subforum)
		{
			$forum_id = substr(strrchr($subforum['U_SUBFORUM'], '.'), 1);
			$subforums[$i]['FORUM_DESC'] = $forum_data[$forum_id]['desc_raw'];
		}

		$event['subforums_row'] = $subforums;
	}

	/**
	* core.posting_modify_template_vars
	*
	* @param array $event Data from the PHP event
	*/
	public function posting_modify_template_vars($event)
	{
		// Loading SCEditor
		$this->ext_helper->load_sceditor();
	}

	/**
	* core.memberlist_prepare_profile_data
	*
	* @param array $event Data from the PHP event
	*/
	public function memberlist_prepare_profile_data($event)
	{
		$data = $event['data'];
		$template_data = $event['template_data'];

		// Add USER_ID
		$template_data['USER_ID'] = $data['user_id'];

		// If Gravatar, only return the attribute src="..."
		if ($data['user_avatar_type'] == 'avatar.driver.gravatar')
		{
			$template_data['AVATAR_IMG'] = $this->ext_helper->get_gravatar_url($data);
		}

		// Translate the rank title RANK_TITLE with the original value RANK_TITLE_RAW
		$template_data['RANK_TITLE_RAW'] = $template_data['RANK_TITLE'];
		$template_data['RANK_TITLE'] = ($this->language->is_set(['RANK_TITLES', strtoupper($template_data['RANK_TITLE'])])) ? $this->language->lang(['RANK_TITLES', strtoupper($template_data['RANK_TITLE'])]) : $template_data['RANK_TITLE'];

		// Override U_PM_ALT without checking $can_receive_pm
		$template_data['U_PM'] = ($this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm')) ? $this->helper->route('vinabb_web_ucp_route', ['id' => 'pm', 'mode' => 'compose', 'u' => $data['user_id']]) : '';

		// Return new data
		$event['template_data'] = $template_data;
	}

	/**
	* core.ucp_pm_view_messsage
	*
	* @param array $event Data from the PHP event
	*/
	public function ucp_pm_view_messsage($event)
	{
		// Translate the rank title RANK_TITLE with the original value RANK_TITLE_RAW
		$msg_data = $event['msg_data'];
		$msg_data['RANK_TITLE_RAW'] = $msg_data['RANK_TITLE'];
		$msg_data['RANK_TITLE'] = ($this->language->is_set(['RANK_TITLES', strtoupper($msg_data['RANK_TITLE'])])) ? $this->language->lang(['RANK_TITLES', strtoupper($msg_data['RANK_TITLE'])]) : $msg_data['RANK_TITLE'];
		$event['msg_data'] = $msg_data;
	}

	/**
	* core.obtain_users_online_string_sql
	*
	* @param array $event Data from the PHP event
	*/
	public function obtain_users_online_string_modify($event)
	{
		// Get total online users (only number)
		$online_users = $event['online_users'];

		$this->template->assign_var('TOTAL_ONLINE_USERS', $online_users['total_online']);
	}
}
