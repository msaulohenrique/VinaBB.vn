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
	public function __construct(\phpbb\db\driver\driver_interface $db,
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
			'core.user_setup'			=> 'user_setup',
			'core.page_header_after'	=> 'page_header_after',
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
		if (!in_array($this->user->page['page_name'], array("viewforum.{$this->php_ext}", "viewtopic.{$this->php_ext}", "viewonline.{$this->php_ext}", "memberlist.{$this->php_ext}", "app.{$this->php_ext}/help/faq")))
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
}
