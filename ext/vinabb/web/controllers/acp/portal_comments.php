<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

use Symfony\Component\DependencyInjection\ContainerInterface;
use vinabb\web\includes\constants;

/**
* Controller for the portal_comments_module
*/
class portal_comments implements portal_comments_interface
{
	/** @var \phpbb\auth\auth $auth */
	protected $auth;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\log\log $log */
	protected $log;

	/** @var \vinabb\web\operators\portal_comment_interface $operator */
	protected $operator;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var string $root_path */
	protected $root_path;

	/** @var string $php_ext */
	protected $php_ext;

	/** @var string $u_action */
	protected $u_action;

	/** @var array $data */
	protected $data;

	/** @var array $errors */
	protected $errors;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth									$auth			Authentication object
	* @param ContainerInterface									$container		Container object
	* @param \phpbb\language\language							$language		Language object
	* @param \phpbb\log\log										$log			Log object
	* @param \vinabb\web\operators\portal_comment_interface		$operator		Comment operators
	* @param \phpbb\request\request								$request		Request object
	* @param \phpbb\template\template							$template		Template object
	* @param \phpbb\user										$user			User object
	* @param string												$root_path		phpBB root path
	* @param string												$php_ext		PHP file extension
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		ContainerInterface $container,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\vinabb\web\operators\portal_comment_interface $operator,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->container = $container;
		$this->language = $language;
		$this->log = $log;
		$this->operator = $operator;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Set form action URL
	*
	* @param string $u_action Form action
	*/
	public function set_form_action($u_action)
	{
		$this->u_action = $u_action;
	}

	/**
	* Display pending comments
	*/
	public function display_pending_comments()
	{
		// Grab all from database
		$entities = $this->operator->get_pending_comments();

		/** @var \vinabb\web\entities\portal_comment_interface $entity */
		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('comments', [
				'TEXT'		=> $entity->get_text_for_display(),
				'STATUS'	=> $this->language->lang(($entity->get_pending() == constants::ARTICLE_COMMENT_MODE_PENDING) ? 'COMMENT_STATUS_PENDING' : 'COMMENT_STATUS_HIDE'),
				'TIME'		=> $this->user->format_date($entity->get_time()),

				'U_EDIT'	=> "{$this->u_action}&action=edit&id={$entity->get_id()}",
				'U_DELETE'	=> "{$this->u_action}&action=delete&id={$entity->get_id()}"
			]);
		}

		$this->template->assign_vars([
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Add a comment
	*/
	public function add_comment()
	{
		// Initiate an entity
		/** @var \vinabb\web\entities\portal_comment_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.portal_comment');

		// Process the new entity
		$this->add_edit_data($entity);

		$this->template->assign_vars([
			'S_ADD'		=> true,
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Edit a comment
	*
	* @param int $comment_id Comment ID
	*/
	public function edit_comment($comment_id)
	{
		// Initiate and load the entity
		/** @var \vinabb\web\entities\portal_comment_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.portal_comment')->load($comment_id);

		// Process the edited entity
		$this->add_edit_data($entity);

		$this->template->assign_vars([
			'S_EDIT'	=> true,
			'U_ACTION'	=> "{$this->u_action}&action=edit&id={$comment_id}"
		]);
	}

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\portal_comment_interface $entity Comment entity
	*/
	public function add_edit_data(\vinabb\web\entities\portal_comment_interface $entity)
	{
		$submit = $this->request->is_set_post('submit');

		// Load posting language file for the BBCode editor
		$this->language->add_lang('posting');

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_portal_comments');

		// Get form data
		$this->request_data();

		// Set the parse options to the entity
		$this->set_bbcode_options($entity, $submit);

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_portal_comments'))
			{
				$this->errors[] = $this->language->lang('FORM_INVALID');
			}

			// Map and set data to the entity
			$this->map_set_data($entity);

			// Insert or update
			if (!sizeof($this->errors))
			{
				$this->save_data($entity);
			}
		}

		// Output
		$this->data_to_tpl($entity);

		$this->template->assign_vars([
			'ERRORS'	=> sizeof($this->errors) ? implode('<br>', $this->errors) : '',
			'U_BACK'	=> $this->u_action
		]);

		// Custom BBCode
		include_once "{$this->root_path}includes/functions_display.{$this->php_ext}";
		display_custom_bbcodes();
	}

	/**
	* Request data from the form
	*/
	protected function request_data()
	{
		$this->data = [
			'article_id'		=> 0,
			'user_id'			=> $this->user->data['user_id'],
			'comment_text'		=> $this->request->variable('comment_text', '', true),
			'text_bbcode'		=> $this->request->variable('text_bbcode', true),
			'text_urls'			=> $this->request->variable('text_urls', true),
			'text_smilies'		=> $this->request->variable('text_smilies', true),
			'comment_pending'	=> $this->auth->acl_get('a_') ? constants::ARTICLE_COMMENT_MODE_SHOW : constants::ARTICLE_COMMENT_MODE_PENDING,
			'comment_time'		=> null
		];
	}

	/**
	* Grab the form data's parsing options and set them to the entity
	*
	* If submit, use data from the form
	* In edit mode, use data stored in the entity
	* In add mode, use default values
	*
	* @param \vinabb\web\entities\portal_comment_interface $entity Comment entity
	*/
	protected function set_bbcode_options(\vinabb\web\entities\portal_comment_interface $entity, $submit)
	{
		$entity->text_enable_bbcode($submit ? $this->request->is_set_post('text_bbcode') : ($entity->get_id() ? $entity->text_bbcode_enabled() : true));
		$entity->text_enable_urls($submit ? $this->request->is_set_post('text_urls') : ($entity->get_id() ? $entity->text_urls_enabled() : true));
		$entity->text_enable_smilies($submit ? $this->request->is_set_post('text_smilies') : ($entity->get_id() ? $entity->text_smilies_enabled() : true));
	}

	/**
	* Map the form data fields to setters and set them to the entity
	*
	* @param \vinabb\web\entities\portal_comment_interface $entity Comment entity
	*/
	protected function map_set_data(\vinabb\web\entities\portal_comment_interface $entity)
	{
		$map_fields = [
			'set_article_id'	=> $this->data['article_id'],
			'set_user_id'		=> $this->data['user_id'],
			'set_text'			=> $this->data['comment_text'],
			'set_pending'		=> $this->data['comment_pending'],
			'set_time'			=> null
		];

		// Change the post time or not?
		if ($entity->get_id())
		{
			unset($map_fields['set_time']);
		}

		// Set the mapped data in the entity
		foreach ($map_fields as $entity_function => $article_data)
		{
			try
			{
				// Calling the $entity_function on the entity and passing it $article_data
				$entity->$entity_function($article_data);
			}
			catch (\vinabb\web\exceptions\base $e)
			{
				$this->errors[] = $e->get_friendly_message($this->language);
			}
		}

		unset($map_fields);
	}

	/**
	* Insert or update data, then log actions and clear cache if needed
	*
	* @param \vinabb\web\entities\portal_comment_interface $entity Comment entity
	*/
	protected function save_data(\vinabb\web\entities\portal_comment_interface $entity)
	{
		if ($entity->get_id())
		{
			// Save the edited entity to the database
			$entity->save();

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_COMMENT_EDIT', time(), [$entity->get_article_id()]);

			$message = 'MESSAGE_COMMENT_EDIT';
		}
		else
		{
			// Add the new entity to the database
			$entity = $this->operator->add_article($entity);

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_COMMENT_ADD', time(), [$entity->get_article_id()]);

			$message = 'MESSAGE_COMMENT_ADD';
		}

		trigger_error($this->language->lang($message) . adm_back_link($this->u_action));
	}

	/**
	* Output entity data to template variables
	*
	* @param \vinabb\web\entities\portal_comment_interface $entity Comment entity
	*/
	protected function data_to_tpl(\vinabb\web\entities\portal_comment_interface $entity)
	{
		$this->template->assign_vars([
			'COMMENT_TEXT'			=> $entity->get_text_for_edit(),
			'COMMENT_TEXT_BBCODE'	=> $entity->text_bbcode_enabled(),
			'COMMENT_TEXT_URLS'		=> $entity->text_urls_enabled(),
			'COMMENT_TEXT_SMILIES'	=> $entity->text_smilies_enabled(),

			// These template variables used for the BBCode editor
			'S_BBCODE_ALLOWED'	=> true,
			'S_SMILIES_ALLOWED'	=> true,
			'S_BBCODE_IMG'		=> true,
			'S_BBCODE_FLASH'	=> true,
			'S_LINKS_ALLOWED'	=> true
		]);
	}

	/**
	* Delete a comment
	*
	* @param int $comment_id Comment ID
	*/
	public function delete_comment($comment_id)
	{
		/** @var \vinabb\web\entities\portal_comment_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.portal_comment')->load($comment_id);

		try
		{
			$this->operator->delete_comment($comment_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_COMMENT_DELETE', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_COMMENT_DELETE', time(), [$entity->get_article_id()]);

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send([
				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang('MESSAGE_COMMENT_DELETE'),
				'REFRESH_DATA'	=> ['time'	=> 3]
			]);
		}
	}
}
