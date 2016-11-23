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
class seo implements EventSubscriberInterface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var \vinabb\web\controllers\helper_interface */
	protected $ext_helper;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db
	* @param \vinabb\web\controllers\cache\service_interface $cache
	* @param \vinabb\web\controllers\helper_interface $ext_helper
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \vinabb\web\controllers\cache\service_interface $cache, \vinabb\web\controllers\helper_interface $ext_helper)
	{
		$this->db = $db;
		$this->cache = $cache;
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
			'core.user_add_modify_data'					=> 'user_add_modify_data',
			'core.update_username'						=> 'update_username',
			'core.submit_post_modify_sql_data'			=> 'submit_post_modify_sql_data',
			'core.acp_manage_forums_update_data_before'	=> 'acp_manage_forums_update_data_before'
		];
	}

	/**
	* core.user_add_modify_data
	*
	* @param array $event Data from the PHP event
	*/
	public function user_add_modify_data($event)
	{
		// Add SEO username for newly registered users
		$sql_ary = $event['sql_ary'];
		$sql_ary['username_seo'] = $this->ext_helper->clean_url($sql_ary['username']);
		$event['sql_ary'] = $sql_ary;
	}

	/**
	* core.update_username
	*
	* @param array $event Data from the PHP event
	*/
	public function update_username($event)
	{
		// Update SEO username again when changed
		$username_seo = $this->ext_helper->clean_url($event['new_name']);

		$sql = 'UPDATE ' . USERS_TABLE . "
			SET username_seo = '$username_seo'
			WHERE username = '" . $this->db->sql_escape($event['new_name']) . "'";
		$this->db->sql_query($sql);
	}

	/**
	* core.submit_post_modify_sql_data
	*
	* @param array $event Data from the PHP event
	*/
	public function submit_post_modify_sql_data($event)
	{
		// Adjust topic SEO title based on topic title
		if ($event['post_mode'] == 'post' || $event['post_mode'] == 'edit_topic' || $event['post_mode'] == 'edit_first_post')
		{
			$sql_data = $event['sql_data'];
			$sql_data[TOPICS_TABLE]['sql']['topic_title_seo'] = $this->ext_helper->clean_url($sql_data[TOPICS_TABLE]['sql']['topic_title']);
			$event['sql_data'] = $sql_data;
		}
	}

	/**
	* core.acp_manage_forums_update_data_before
	*
	* @param array $event Data from the PHP event
	*/
	public function acp_manage_forums_update_data_before($event)
	{
		// Adjust the column 'forum_name_seo' based on 'forum_name'
		$forum_data_sql = $event['forum_data_sql'];
		$forum_data_sql['forum_name_seo'] = $this->ext_helper->clean_url($forum_data_sql['forum_name']);

		// If there have more than 2 same forum SEO names, add parent forum SEO name as prefix
		if ($forum_data_sql['parent_id'])
		{
			$forum_data = $this->cache->get_forum_data();

			$sql = 'SELECT forum_id, parent_id, forum_name_seo
				FROM ' . FORUMS_TABLE . '
				WHERE forum_id <> ' . $forum_data_sql['forum_id'] . "
					AND forum_name = '" . $this->db->sql_escape($forum_data_sql['forum_name']) . "'";
			$result = $this->db->sql_query($sql);
			$rows = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			if (sizeof($rows))
			{
				foreach ($rows as $row)
				{
					$sql = 'UPDATE ' . FORUMS_TABLE . "
						SET forum_name_seo = '" . $forum_data[$row['parent_id']]['name_seo'] . constants::REWRITE_URL_FORUM_CAT . $row['forum_name_seo'] . "'
						WHERE forum_id = " . $row['forum_id'];
					$this->sql_query($sql);
				}

				$forum_data_sql['forum_name_seo'] = $forum_data[$forum_data_sql['parent_id']]['name_seo'] . constants::REWRITE_URL_FORUM_CAT . $forum_data_sql['forum_name_seo'];
			}
		}

		$event['forum_data_sql'] = $forum_data_sql;
	}
}
