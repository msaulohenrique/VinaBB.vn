<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use vinabb\web\includes\constants;

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
    protected $db;

	/** @var \phpbb\config\config */
    protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\controller\helper */
    protected $helper;

	/** @var \phpbb\template\template */
    protected $template;

	/** @var \phpbb\user */
    protected $user;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
    protected $request;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $phpbb_admin_path;

	/** @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\config\config $config
	* @param \phpbb\config\db_text $config_text
	* @param \phpbb\controller\helper $helper
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\language\language $language
	* @param \phpbb\request\request $request
	* @param string $phpbb_root_path
	* @param string $php_ext
	*/
	public function __construct(\phpbb\auth\auth $auth,
								\phpbb\db\driver\driver_interface $db,
								\phpbb\config\config $config,
								\phpbb\config\db_text $config_text,
								\phpbb\controller\helper $helper,
								\phpbb\template\template $template,
								\phpbb\user $user,
								\phpbb\language\language $language,
								\phpbb\request\request $request,
								$phpbb_root_path,
								$phpbb_admin_path,
								$php_ext)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->config = $config;
		$this->config_text = $config_text;
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
		$this->language = $language;
		$this->request = $request;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->phpbb_admin_path = $phpbb_admin_path;
		$this->php_ext = $php_ext;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'						=> 'user_setup',
			'core.page_header_after'				=> 'page_header_after',
			'core.make_jumpbox_modify_tpl_ary'		=> 'make_jumpbox_modify_tpl_ary',
			'core.memberlist_prepare_profile_data'	=> 'memberlist_prepare_profile_data',
			'core.memberlist_memberrow_before'		=> 'memberlist_memberrow_before',
			'core.viewtopic_modify_post_row'		=> 'viewtopic_modify_post_row',
			'core.ucp_pm_view_messsage'				=> 'ucp_pm_view_messsage',

			'core.adm_page_header'						=> 'adm_page_header',
			'core.add_log'								=> 'add_log',
			'core.acp_manage_forums_update_data_after'	=> 'acp_manage_forums_update_data_after',
		);
	}

	/**
	* core.user_setup
	*
	* @param $event
	*/
	public function user_setup($event)
	{
		// Display the forum list on every page
		if (!in_array($this->user->page['page_name'], array("viewforum.{$this->php_ext}", "viewtopic.{$this->php_ext}", "viewonline.{$this->php_ext}", "memberlist.{$this->php_ext}", "ucp.{$this->php_ext}", "app.{$this->php_ext}/help/faq")))
		{
			make_jumpbox(append_sid("{$this->phpbb_root_path}viewforum.{$this->php_ext}"));
		}

		// Add our common language variables
		$this->language->add_lang('common', 'vinabb/web');
	}

	/**
	* core.page_header_after
	*
	* @param $event
	*/
	public function page_header_after($event)
	{
		// Maintenance mode
		global $msg_title;

		if (($this->config['vinabb_web_maintenance_mode'] == constants::MAINTENANCE_MODE_FOUNDER && $this->user->data['user_type'] != USER_FOUNDER)
			|| ($this->config['vinabb_web_maintenance_mode'] == constants::MAINTENANCE_MODE_ADMIN && !$this->auth->acl_gets('a_'))
			|| ($this->config['vinabb_web_maintenance_mode'] == constants::MAINTENANCE_MODE_MOD && !$this->auth->acl_gets('a_', 'm_') && !$this->auth->acl_getf_global('m_'))
			|| ($this->config['vinabb_web_maintenance_mode'] == constants::MAINTENANCE_MODE_USER && ($this->user->data['user_id'] == ANONYMOUS || $this->user->data['is_bot']))
		)
		{
			// Get current time
			$now = time();
			$in_maintenance_time = ($this->config['vinabb_web_maintenance_time'] > $now) ? true : false;

			// Get data from the config_text table
			$data = $this->config_text->get_array(array(
				'vinabb_web_maintenance_text',
				'vinabb_web_maintenance_text_vi'
			));

			// Get maintenance text with/without the end time
			if (empty($data['vinabb_web_maintenance_text']) || empty($data['vinabb_web_maintenance_text_vi']))
			{
				if ($in_maintenance_time)
				{
					// Short maintenance time: 12 hours
					if (($this->config['vinabb_web_maintenance_time'] - $now) > (12 * 60 * 60))
					{
						$message = $this->language->lang('MAINTENANCE_TEXT_TIME_LONG', $this->user->format_date($this->config['vinabb_web_maintenance_time'], 'd/m/Y H:i'));
					}
					else
					{
						$message = $this->language->lang('MAINTENANCE_TEXT_TIME_SHORT', $this->user->format_date($this->config['vinabb_web_maintenance_time'], 'H:i'));
					}
				}
				else
				{
					$message = $this->language->lang('MAINTENANCE_TEXT');
				}

				$message .= '<br>';
			}
			else
			{
				$message = ($this->user->lang_name == 'vi') ? $data['vinabb_web_maintenance_text_vi'] : $data['vinabb_web_maintenance_text'];
				$message = str_replace("\n", '<br>', $message);

				if ($in_maintenance_time)
				{
					$message .= '<br ><br >' . $this->language->lang('MAINTENANCE_TEXT_TIME_END', $this->user->format_date($this->config['vinabb_web_maintenance_time'], 'd/m/Y H:i'));
				}
			}

			// Get timezone data
			$dt = $this->user->create_datetime();
			$timezone_offset = $this->language->lang(['timezones', 'UTC_OFFSET'], phpbb_format_timezone_offset($dt->getOffset()));
			$timezone_name = $this->user->timezone->getName();

			if ($this->language->is_set(['timezones', $timezone_name]))
			{
				$timezone_name = $this->language->lang(['timezones', $timezone_name]);
			}

			if ($in_maintenance_time)
			{
				$message .= '<br>' . $this->language->lang('MAINTENANCE_TEXT_TIMEZONE', $timezone_offset, $timezone_name);
			}

			// Display the maintenance text
			$msg_title = $this->language->lang('MAINTENANCE_TITLE');
			trigger_error($message, ($this->config['vinabb_web_maintenance_tpl']) ? E_USER_WARNING : E_USER_ERROR);
		}

		// Add template variables
		$this->template->assign_vars(array(
			'CONFIG_TOTAL_USERS'	=> $this->config['num_users'],
			'CONFIG_TOTAL_FORUMS'	=> $this->config['num_forums'],
			'CONFIG_TOTAL_TOPICS'	=> $this->config['num_topics'],
			'CONFIG_TOTAL_POSTS'	=> $this->config['num_posts'],

			'U_LOGIN_ACTION'	=> append_sid("{$this->phpbb_root_path}ucp.{$this->php_ext}", 'mode=login'),
			'U_SEND_PASSWORD'	=> ($this->config['email_enable']) ? append_sid("{$this->phpbb_root_path}ucp.{$this->php_ext}", 'mode=sendpassword') : '',
			'U_MCP'				=> ($this->auth->acl_get('m_') || $this->auth->acl_getf_global('m_')) ? append_sid("{$this->phpbb_root_path}mcp.{$this->php_ext}", 'i=main&amp;mode=front', true, $this->user->session_id) : '',
		));
	}

	/**
	* core.make_jumpbox_modify_tpl_ary
	*
	* @param $event
	*/
	public function make_jumpbox_modify_tpl_ary($event)
	{
		// Add PARENT_ID and HAS_SUBFORUM
		$row = $event['row'];
		$tpl_ary = $event['tpl_ary'];
		$i = isset($tpl_ary[1]) ? 1 : 0;
		$tpl_ary[$i]['PARENT_ID'] = $row['parent_id'];
		$tpl_ary[$i]['HAS_SUBFORUM'] = ($row['left_id'] != $row['right_id'] - 1) ? true : false;
		$event['tpl_ary'] = $tpl_ary;
	}

	/**
	* core.memberlist_prepare_profile_data
	*
	* @param $event
	*/
	public function memberlist_prepare_profile_data($event)
	{
		// Add USER_ID and U_PM_ALT without checking $can_receive_pm
		// Also translate the rank title RANK_TITLE with the original value RANK_TITLE_RAW
		$data = $event['data'];
		$template_data = $event['template_data'];
		$template_data['USER_ID'] = $data['user_id'];
		$template_data['RANK_TITLE_RAW'] = $template_data['RANK_TITLE'];
		$template_data['RANK_TITLE'] = ($this->language->is_set(['RANK_TITLES', strtoupper($template_data['RANK_TITLE'])])) ? $this->language->lang(['RANK_TITLES', strtoupper($template_data['RANK_TITLE'])]) : $template_data['RANK_TITLE'];
		$template_data['U_PM_ALT'] = ($this->config['allow_privmsg'] && $this->auth->acl_get('u_sendpm')) ? append_sid("{$this->phpbb_root_path}ucp.{$this->php_ext}", 'i=pm&amp;mode=compose&amp;u=' . $data['user_id']) : '';
		$event['template_data'] = $template_data;
	}

	/**
	* core.memberlist_memberrow_before
	*
	* @param $event
	*/
	public function memberlist_memberrow_before($event)
	{
		// Enable contact fields on the member list
		$event['use_contact_fields'] = true;
	}

	/**
	* core.viewtopic_modify_post_row
	*
	* @param $event
	*/
	public function viewtopic_modify_post_row($event)
	{
		// Translate the rank title RANK_TITLE with the original value RANK_TITLE_RAW
		$post_row = $event['post_row'];
		$post_row['RANK_TITLE_RAW'] = $post_row['RANK_TITLE'];
		$post_row['RANK_TITLE'] = ($this->language->is_set(['RANK_TITLES', strtoupper($post_row['RANK_TITLE'])])) ? $this->language->lang(['RANK_TITLES', strtoupper($post_row['RANK_TITLE'])]) : $post_row['RANK_TITLE'];
		$event['post_row'] = $post_row;
	}

	/**
	* core.ucp_pm_view_messsage
	*
	* @param $event
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
	* core.adm_page_header
	*
	* @param $event
	*/
	public function adm_page_header($event)
	{
		// Add our ACP common language variables
		$this->language->add_lang('acp_common', 'vinabb/web');

		// Add template variables
		$this->template->assign_vars(array(
			'S_FOUNDER'	=> ($this->user->data['user_type'] == USER_FOUNDER) ? true : false
		));
	}

	/**
	* core.acp_manage_forums_update_data_after
	*
	* @param $event
	*/
	public function acp_manage_forums_update_data_after($event)
	{
		if ($event['is_new_forum'])
		{
			$this->config->increment('num_forums', 1, true);
		}
	}

	/**
	* core.add_log
	*
	* @param $event
	*/
	public function add_log($event)
	{
		if (substr($event['log_operation'], 0, 14) == 'LOG_FORUM_DEL_')
		{
			$sql = 'SELECT COUNT(forum_id) AS num_forums
				FROM ' . FORUMS_TABLE;
			$result = $this->db->sql_query($sql);
			$num_forums = $this->db->sql_fetchfield('num_forums');
			$this->db->sql_freeresult($result);

			$this->config->set('num_forums', $num_forums, true);
		}
	}
}
