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
	* core.user_setup_after
	*
	* @param $event
	*/
	public function page_header_after($event)
	{
		// Maintenance mode
		if ($this->config['vinabb_web_maintenance_mode'])
		{
			$error_message = '';
			$error_type = ($this->config['vinabb_web_maintenance_tpl']) ? E_USER_WARNING : E_USER_ERROR;

			switch ($this->config['vinabb_web_maintenance_mode'])
			{
				case constants::MAINTENANCE_MODE_SERVER:
					trigger_error($error_message, $error_type);
				break;

				case constants::MAINTENANCE_MODE_FOUNDER:
					if ($this->user->data['user_type'] != USER_FOUNDER)
					{
						trigger_error($error_message, $error_type);
					}
				break;

				case constants::MAINTENANCE_MODE_ADMIN:
					if (!$this->auth->acl_gets('a_'))
					{
						trigger_error($error_message, $error_type);
					}
				break;

				case constants::MAINTENANCE_MODE_MOD:
					if (!$this->auth->acl_gets('a_', 'm_') && !$this->auth->acl_getf_global('m_'))
					{
						trigger_error($error_message, $error_type);
					}
				break;

				case constants::MAINTENANCE_MODE_USER:
					if ($this->user->data['user_id'] == ANONYMOUS || $this->user->data['is_bot'])
					{
						trigger_error($error_message, $error_type);
					}
				break;
			}
		}
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
		// Add USER_ID, the translated rank title RANK_TITLE_LANG and U_PM_ALT without checking $can_receive_pm
		$data = $event['data'];
		$template_data = $event['template_data'];
		$template_data['USER_ID'] = $data['user_id'];
		$template_data['RANK_TITLE_LANG'] = ($this->language->is_set(['RANK_TITLES', strtoupper($template_data['RANK_TITLE'])])) ? $this->language->lang(['RANK_TITLES', strtoupper($template_data['RANK_TITLE'])]) : $template_data['RANK_TITLE'];
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
}
