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
class seo implements EventSubscriberInterface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \vinabb\web\controllers\helper_interface */
	protected $ext_helper;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface					$db				Database object
	* @param \vinabb\web\controllers\cache\service_interface	$cache			Cache service
	* @param \phpbb\config\config								$config			Config object
	* @param \phpbb\language\language							$language		Language object
	* @param \phpbb\request\request								$request		Request object
	* @param \phpbb\template\template							$template		Template object
	* @param \vinabb\web\controllers\helper_interface			$ext_helper		Extension helper
	*/
	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\vinabb\web\controllers\helper_interface $ext_helper
	)
	{
		$this->db = $db;
		$this->cache = $cache;
		$this->config = $config;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
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
			'core.submit_post_modify_sql_data'		=> 'submit_post_modify_sql_data',
			'core.acp_manage_forums_request_data'	=> 'acp_manage_forums_request_data',
			'core.acp_manage_forums_display_form'	=> 'acp_manage_forums_display_form',
			'core.acp_manage_forums_validate_data'	=> 'acp_manage_forums_validate_data'
		];
	}

	/**
	* core.submit_post_modify_sql_data
	*
	* @param array $event Data from the PHP event
	*/
	public function submit_post_modify_sql_data($event)
	{
		// Adjust SEO titles based on the original topic titles, post subjects
		$sql_data = $event['sql_data'];

		if (in_array($event['post_mode'], ['post', 'edit_topic', 'edit', 'edit_first_post', 'edit_last_post', 'reply', 'quote']))
		{
			if (in_array($event['post_mode'], ['post', 'edit_topic', 'edit_first_post']))
			{
				$sql_data[TOPICS_TABLE]['sql']['topic_title_seo'] = $this->ext_helper->clean_url($sql_data[TOPICS_TABLE]['sql']['topic_title']);
			}

			$sql_data[POSTS_TABLE]['sql']['post_subject_seo'] = $this->ext_helper->clean_url($sql_data[POSTS_TABLE]['sql']['post_subject']);
		}

		$event['sql_data'] = $sql_data;
	}

	/**
	* core.acp_manage_forums_request_data
	*
	* @param array $event Data from the PHP event
	*/
	public function acp_manage_forums_request_data($event)
	{
		$forum_data = $event['forum_data'];
		$forum_data['forum_name_seo'] = $this->ext_helper->clean_url($this->request->variable('forum_name_seo', ''));
		$forum_data['forum_lang'] = $this->request->variable('forum_lang', '');
		$event['forum_data'] = $forum_data;
	}

	/**
	* core.acp_manage_forums_display_form
	*
	* @param array $event Data from the PHP event
	*/
	public function acp_manage_forums_display_form($event)
	{
		// Output the current forum SEO name to template variables
		$action = $event['action'];
		$forum_data = $event['forum_data'];
		$template_data = $event['template_data'];
		$template_data['FORUM_NAME_SEO'] = ($action == 'add') ? '' : $forum_data['forum_name_seo'];
		$event['template_data'] = $template_data;

		// Select the forum language
		$this->template->assign_var('LANG_OPTIONS', $this->ext_helper->build_lang_list(($action == 'add') ? $this->config['default_lang'] : $forum_data['forum_lang']));
	}

	/**
	* core.acp_manage_forums_validate_data
	*
	* @param array $event Data from the PHP event
	*/
	public function acp_manage_forums_validate_data($event)
	{
		$forum_data = $event['forum_data'];
		$errors = $event['errors'];

		if ($forum_data['forum_lang'] == '')
		{
			$errors[] = $this->language->lang('ERROR_FORUM_LANG_EMPTY');
		}

		$event['errors'] = $errors;
	}
}
