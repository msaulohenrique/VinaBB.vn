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
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db
	* @param \vinabb\web\controllers\cache\service_interface $cache
	* @param \phpbb\config\config $config
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
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
		$this->template->assign_vars([
			'S_FOUNDER'	=> $this->user->data['user_type'] == USER_FOUNDER
		]);
	}

	/**
	* core.add_log
	*
	* @param array $event Data from the PHP event
	*/
	public function add_log($event)
	{
		if (substr($event['log_operation'], 0, 14) == 'LOG_FORUM_DEL_')
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
		else if ($event['log_operation'] == 'LOG_FORUM_ADD' || $event['log_operation'] == 'LOG_FORUM_EDIT')
		{
			// Update forum counter
			if ($event['log_operation'] == 'LOG_FORUM_ADD')
			{
				$this->config->increment('num_forums', 1, true);
			}

			// Clear forum cache
			$this->cache->clear_forum_data();
		}
		// Clear language data cache
		else if ($event['log_operation'] == 'LOG_LANGUAGE_PACK_INSTALLED' || $event['log_operation'] == 'LOG_LANGUAGE_PACK_DELETED')
		{
			$this->cache->clear_lang_data();
		}
	}
}
