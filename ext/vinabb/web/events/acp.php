<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* PHP events
*/
class acp implements EventSubscriberInterface
{
	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface					$db			Database object
	* @param \vinabb\web\controllers\cache\service_interface	$cache		Cache service
	* @param \phpbb\config\config								$config		Config object
	* @param \phpbb\template\template							$template	Template object
	* @param \phpbb\user										$user		User object
	*/
	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		\phpbb\template\template $template,
		\phpbb\user $user
	)
	{
		$this->db = $db;
		$this->cache = $cache;
		$this->config = $config;
		$this->template = $template;
		$this->user = $user;
	}

	/**
	* List of phpBB's PHP events to be used
	*
	* @return array
	*/
	static public function getSubscribedEvents()
	{
		return [
			'core.adm_page_header'	=> 'adm_page_header',
			'core.add_log'			=> 'add_log'
		];
	}

	/**
	* core.adm_page_header
	*
	* @param array $event Data from the PHP event
	*/
	public function adm_page_header($event)
	{
		// Add template variables
		$this->template->assign_var('S_FOUNDER', $this->user->data['user_type'] == USER_FOUNDER);
	}

	/**
	* core.add_log
	*
	* @param array $event Data from the PHP event
	*/
	public function add_log($event)
	{
		$data = [
			'LOG_FORUM_DEL_FORUM'					=> 'add_log_forum_del',
			'LOG_FORUM_DEL_FORUMS'					=> 'add_log_forum_del',
			'LOG_FORUM_DEL_MOVE_FORUMS'				=> 'add_log_forum_del',
			'LOG_FORUM_DEL_MOVE_POSTS'				=> 'add_log_forum_del',
			'LOG_FORUM_DEL_MOVE_POSTS_FORUMS'		=> 'add_log_forum_del',
			'LOG_FORUM_DEL_MOVE_POSTS_MOVE_FORUMS'	=> 'add_log_forum_del',
			'LOG_FORUM_DEL_POSTS'					=> 'add_log_forum_del',
			'LOG_FORUM_DEL_POSTS_FORUMS'			=> 'add_log_forum_del',
			'LOG_FORUM_DEL_POSTS_MOVE_FORUMS'		=> 'add_log_forum_del',
			'LOG_FORUM_ADD'							=> 'add_log_forum_add',
			'LOG_FORUM_EDIT'						=> 'add_log_forum_edit',
			'LOG_LANGUAGE_PACK_INSTALLED'			=> 'add_log_lang',
			'LOG_LANGUAGE_PACK_DELETED'				=> 'add_log_lang'
		];

		// Run methods depend on the log operation
		if (isset($data[$event['log_operation']]))
		{
			$this->{$data[$event['log_operation']]}();
		}
	}

	/**
	* Do actions while deleting forums
	*/
	protected function add_log_forum_del()
	{
		// Update forum counter
		$sql = 'SELECT COUNT(forum_id) AS num_forums
			FROM ' . FORUMS_TABLE;
		$result = $this->db->sql_query($sql);
		$num_forums = $this->db->sql_fetchfield('num_forums');
		$this->db->sql_freeresult($result);

		$this->config->set('num_forums', $num_forums, true);

		// Clear forum cache
		$this->cache->clear_forum_data();
	}

	/**
	* Do actions while adding forums
	*/
	protected function add_log_forum_add()
	{
		// Update forum counter
		$this->config->increment('num_forums', 1, true);

		// Clear forum cache
		$this->cache->clear_forum_data();
	}

	/**
	* Do actions while editing forums
	*/
	protected function add_log_forum_edit()
	{
		// Clear forum cache
		$this->cache->clear_forum_data();
	}

	/**
	* Do actions while installing/uninstalling language packages
	*/
	protected function add_log_lang()
	{
		// Clear language data cache
		$this->cache->clear_lang_data();
	}
}
