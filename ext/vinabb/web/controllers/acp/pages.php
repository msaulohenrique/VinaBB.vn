<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* Controller for the pages_module
*/
class pages implements pages_interface
{
	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \vinabb\web\operators\page_interface */
	protected $operator;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $u_action;

	/**
	* Constructor
	*
	* @param \phpbb\cache\service					$cache		Cache service
	* @param ContainerInterface						$container	Container object
	* @param \phpbb\language\language				$language	Language object
	* @param \phpbb\log\log							$log		Log object
	* @param \vinabb\web\operators\page_interface	$operator	Page operators
	* @param \phpbb\request\request					$request	Request object
	* @param \phpbb\template\template				$template	Template object
	* @param \phpbb\user							$user		User object
	* @param string									$root_path	phpBB root path
	* @param string									$php_ext	PHP file extension
	*/
	public function __construct(
		\phpbb\cache\service $cache,
		ContainerInterface $container,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\vinabb\web\operators\page_interface $operator,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		$root_path,
		$php_ext
	)
	{
		$this->cache = $cache;
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
	* Display pages
	*/
	public function display_pages()
	{
		// Grab all from database
		$entities = $this->operator->get_pages();

		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('pages', [
				'NAME'		=> $entity->get_name(),
				'NAME_VI'	=> $entity->get_name_vi(),
				'VARNAME'	=> $entity->get_varname(),
				'ENABLE'	=> $entity->get_enable(),

				'U_EDIT'	=> "{$this->u_action}&action=edit&id=" . $entity->get_id(),
				'U_DELETE'	=> "{$this->u_action}&action=delete&id=" . $entity->get_id()
			]);
		}

		$this->template->assign_vars([
			'U_ACTION'	=> $this->u_action,
			'U_ADD'		=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Add a page
	*/
	public function add_page()
	{
		// Initiate an entity
		/* @var \vinabb\web\entities\page_interface */
		$entity = $this->container->get('vinabb.web.entities.page');

		// Process the new entity
		$this->add_edit_page_data($entity);

		$this->template->assign_vars([
			'S_ADD'		=> true,
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Edit a page
	*
	* @param int $page_id Page ID
	*/
	public function edit_page($page_id)
	{
		// Initiate and load the entity
		/* @var \vinabb\web\entities\page_interface */
		$entity = $this->container->get('vinabb.web.entities.page')->load($page_id);

		// Process the edited entity
		$this->add_edit_page_data($entity);

		$this->template->assign_vars([
			'S_EDIT'	=> true,
			'U_ACTION'	=> "{$this->u_action}&action=edit&id={$page_id}"
		]);
	}

	/**
	* Process page data to be added or edited
	*
	* @param \vinabb\web\entities\page_interface $entity Page entity
	*/
	public function add_edit_page_data($entity)
	{
		$page_id = $entity->get_id();
		$submit = $this->request->is_set_post('submit');
		$errors = [];

		// Load posting language file for the BBCode editor
		$this->language->add_lang('posting');

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_pages');

		// Get form data
		$data = [
			'page_name'					=> $this->request->variable('page_name', '', true),
			'page_name_vi'				=> $this->request->variable('page_name_vi', '', true),
			'page_varname'				=> $this->request->variable('page_varname', ''),
			'page_desc'					=> $this->request->variable('page_desc', '', true),
			'page_desc_vi'				=> $this->request->variable('page_desc_vi', '', true),
			'page_text'					=> $this->request->variable('page_text', '', true),
			'text_bbcode'				=> $this->request->variable('text_bbcode', true),
			'text_urls'					=> $this->request->variable('text_urls', true),
			'text_smilies'				=> $this->request->variable('text_smilies', true),
			'page_text_vi'				=> $this->request->variable('page_text_vi', '', true),
			'text_vi_bbcode'			=> $this->request->variable('text_vi_bbcode', true),
			'text_vi_urls'				=> $this->request->variable('text_vi_urls', true),
			'text_vi_smilies'			=> $this->request->variable('text_vi_smilies', true),
			'page_enable'				=> $this->request->variable('page_enable', true),
			'page_enable_guest'			=> $this->request->variable('page_enable_guest', true),
			'page_enable_bot'			=> $this->request->variable('page_enable_bot', true),
			'page_enable_new_user'		=> $this->request->variable('page_enable_new_user', true),
			'page_enable_user'			=> $this->request->variable('page_enable_user', true),
			'page_enable_mod'			=> $this->request->variable('page_enable_mod', true),
			'page_enable_global_mod'	=> $this->request->variable('page_enable_global_mod', true),
			'page_enable_admin'			=> $this->request->variable('page_enable_admin', true),
			'page_enable_founder'		=> $this->request->variable('page_enable_founder', true)
		];

		/**
		* Grab the form data's parsing options
		*
		*	If submit, use data from the form
		*	In edit mode, use data stored in the entity
		*	In add mode, use default values
		*/
		$text_options = [
			'bbcode'	=> $submit ? $data['text_bbcode'] : ($page_id ? $entity->text_bbcode_enabled() : true),
			'urls'		=> $submit ? $data['text_urls'] : ($page_id ? $entity->text_urls_enabled() : true),
			'smilies'	=> $submit ? $data['text_smilies'] : ($page_id ? $entity->text_smilies_enabled() : true)
		];

		$text_vi_options = [
			'bbcode'	=> $submit ? $data['text_vi_bbcode'] : ($page_id ? $entity->text_vi_bbcode_enabled() : true),
			'urls'		=> $submit ? $data['text_vi_urls'] : ($page_id ? $entity->text_vi_urls_enabled() : true),
			'smilies'	=> $submit ? $data['text_vi_smilies'] : ($page_id ? $entity->text_vi_smilies_enabled() : true)
		];

		// Set the parse options in the entity
		foreach ($text_options as $function => $enabled)
		{
			$entity->{($enabled ? 'text_enable_' : 'text_disable_') . $function}();
		}

		foreach ($text_vi_options as $function => $enabled)
		{
			$entity->{($enabled ? 'text_vi_enable_' : 'text_vi_disable_') . $function}();
		}

		unset($text_options);
		unset($text_vi_options);

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_pages'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			// Map the form data fields to setters
			$map_fields = [
				'set_name'				=> $data['page_name'],
				'set_name_vi'			=> $data['page_name_vi'],
				'set_varname'			=> $data['page_varname'],
				'set_desc'				=> $data['page_desc'],
				'set_desc_vi'			=> $data['page_desc_vi'],
				'set_text'				=> $data['page_text'],
				'set_text_vi'			=> $data['page_text_vi'],
				'set_enable'			=> $data['page_enable'],
				'set_enable_guest'		=> $data['page_enable_guest'],
				'set_enable_bot'		=> $data['page_enable_bot'],
				'set_enable_new_user'	=> $data['page_enable_new_user'],
				'set_enable_user'		=> $data['page_enable_user'],
				'set_enable_mod'		=> $data['page_enable_mod'],
				'set_enable_global_mod'	=> $data['page_enable_global_mod'],
				'set_enable_admin'		=> $data['page_enable_admin'],
				'set_enable_founder'	=> $data['page_enable_founder']
			];

			// Set the mapped data in the entity
			foreach ($map_fields as $entity_function => $page_data)
			{
				try
				{
					// Calling the $entity_function on the entity and passing it $page_data
					$entity->$entity_function($page_data);
				}
				catch (\vinabb\web\exceptions\base $e)
				{
					$errors[] = $e->get_friendly_message($this->language);
				}
			}

			unset($map_fields);

			// Insert or update page
			if (!sizeof($errors))
			{
				if ($page_id)
				{
					// Save the edited entity to the database
					$entity->save();

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PAGE_EDIT', time(), [$entity->get_varname()]);

					$message = 'MESSAGE_PAGE_EDIT';
				}
				else
				{
					// Add the new entity to the database
					$entity = $this->operator->add_page($entity);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PAGE_ADD', time(), [$entity->get_varname()]);

					$message = 'MESSAGE_PAGE_ADD';
				}

				$this->cache->clear_pages();

				trigger_error($this->language->lang($message) . adm_back_link($this->u_action));
			}
		}

		$this->template->assign_vars([
			'ERRORS'	=> sizeof($errors) ? implode('<br>', $errors) : '',

			'PAGE_NAME'					=> $entity->get_name(),
			'PAGE_NAME_VI'				=> $entity->get_name_vi(),
			'PAGE_VARNAME'				=> $entity->get_varname(),
			'PAGE_DESC'					=> $entity->get_desc(),
			'PAGE_DESC_VI'				=> $entity->get_desc_vi(),
			'PAGE_TEXT'					=> $entity->get_text_for_edit(),
			'PAGE_TEXT_VI'				=> $entity->get_text_vi_for_edit(),
			'PAGE_ENABLE'				=> $entity->get_enable(),
			'PAGE_ENABLE_GUEST'			=> $entity->get_enable_guest(),
			'PAGE_ENABLE_BOT'			=> $entity->get_enable_bot(),
			'PAGE_ENABLE_NEW_USER'		=> $entity->get_enable_new_user(),
			'PAGE_ENABLE_USER'			=> $entity->get_enable_user(),
			'PAGE_ENABLE_MOD'			=> $entity->get_enable_mod(),
			'PAGE_ENABLE_GLOBAL_MOD'	=> $entity->get_enable_global_mod(),
			'PAGE_ENABLE_ADMIN'			=> $entity->get_enable_admin(),
			'PAGE_ENABLE_FOUNDER'		=> $entity->get_enable_founder(),

			// These template variables used for the BBCode editor
			'S_BBCODE_ALLOWED'	=> true,
			'S_SMILIES_ALLOWED'	=> true,
			'S_BBCODE_IMG'		=> true,
			'S_BBCODE_FLASH'	=> true,
			'S_LINKS_ALLOWED'	=> true,

			'U_BACK'	=> $this->u_action
		]);

		// Custom BBCode
		include_once "{$this->root_path}includes/functions_display.{$this->php_ext}";
		display_custom_bbcodes();
	}

	/**
	* Deleta a page
	*
	* @param int $page_id Page ID
	*/
	public function delete_page($page_id)
	{
		/* @var \vinabb\web\entities\page_interface */
		$entity = $this->container->get('vinabb.web.entities.page')->load($page_id);

		try
		{
			$this->operator->delete_page($page_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_PAGE_DELETE', $e->get_message()) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PAGE_DELETE', time(), [$entity->get_varname()]);

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send([
				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang('MESSAGE_PAGE_DELETE'),
				'REFRESH_DATA'	=> ['time'	=> 3]
			]);
		}
	}
}
