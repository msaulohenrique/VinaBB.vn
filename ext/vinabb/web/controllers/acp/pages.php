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
	/** @var \vinabb\web\controllers\cache\service_interface */
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

	/** @var array */
	protected $data;

	/** @var array */
	protected $errors;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface	$cache		Cache service
	* @param ContainerInterface									$container	Container object
	* @param \phpbb\language\language							$language	Language object
	* @param \phpbb\log\log										$log		Log object
	* @param \vinabb\web\operators\page_interface				$operator	Page operators
	* @param \phpbb\request\request								$request	Request object
	* @param \phpbb\template\template							$template	Template object
	* @param \phpbb\user										$user		User object
	* @param string												$root_path	phpBB root path
	* @param string												$php_ext	PHP file extension
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
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

		/** @var \vinabb\web\entities\page_interface $entity */
		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('pages', [
				'NAME'		=> $entity->get_name(),
				'NAME_VI'	=> $entity->get_name_vi(),
				'VARNAME'	=> $entity->get_varname(),
				'ENABLE'	=> $entity->get_enable(),

				'U_EDIT'	=> "{$this->u_action}&action=edit&id={$entity->get_id()}",
				'U_DELETE'	=> "{$this->u_action}&action=delete&id={$entity->get_id()}"
			]);
		}

		$this->template->assign_vars([
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Add a page
	*/
	public function add_page()
	{
		// Initiate an entity
		/** @var \vinabb\web\entities\page_interface */
		$entity = $this->container->get('vinabb.web.entities.page');

		// Process the new entity
		$this->add_edit_data($entity);

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
		/** @var \vinabb\web\entities\page_interface */
		$entity = $this->container->get('vinabb.web.entities.page')->load($page_id);

		// Process the edited entity
		$this->add_edit_data($entity);

		$this->template->assign_vars([
			'S_EDIT'	=> true,
			'U_ACTION'	=> "{$this->u_action}&action=edit&id={$page_id}"
		]);
	}

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\page_interface $entity Page entity
	*/
	public function add_edit_data(\vinabb\web\entities\page_interface $entity)
	{
		$submit = $this->request->is_set_post('submit');

		// Load posting language file for the BBCode editor
		$this->language->add_lang('posting');

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_pages');

		// Get form data
		$this->request_data();

		// Set the parse options to the entity
		$this->set_bbcode_options($entity, $submit);

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_pages'))
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
	*
	* @return array
	*/
	protected function request_data()
	{
		$this->data = [
			'page_name'					=> $this->request->variable('page_name', '', true),
			'page_name_vi'				=> $this->request->variable('page_name_vi', '', true),
			'page_varname'				=> $this->request->variable('page_varname', ''),
			'page_desc'					=> $this->request->variable('page_desc', '', true),
			'page_desc_vi'				=> $this->request->variable('page_desc_vi', '', true),
			'page_text'					=> $this->request->variable('page_text', '', true),
			'page_text_vi'				=> $this->request->variable('page_text_vi', '', true),
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
	}

	/**
	* Grab the form data's parsing options and set them to the entity
	*
	* If submit, use data from the form
	* In edit mode, use data stored in the entity
	* In add mode, use default values
	*
	* @param \vinabb\web\entities\page_interface $entity Page entity
	*/
	protected function set_bbcode_options(\vinabb\web\entities\page_interface $entity, $submit)
	{
		$entity->text_enable_bbcode($submit ? $this->request->is_set_post('text_bbcode') : ($entity->get_id() ? $entity->text_bbcode_enabled() : true));
		$entity->text_enable_urls($submit ? $this->request->is_set_post('text_urls') : ($entity->get_id() ? $entity->text_urls_enabled() : true));
		$entity->text_enable_smilies($submit ? $this->request->is_set_post('text_smilies') : ($entity->get_id() ? $entity->text_smilies_enabled() : true));
		$entity->text_vi_enable_bbcode($submit ? $this->request->is_set_post('text_vi_bbcode') : ($entity->get_id() ? $entity->text_vi_bbcode_enabled() : true));
		$entity->text_vi_enable_urls($submit ? $this->request->is_set_post('text_vi_urls') : ($entity->get_id() ? $entity->text_vi_urls_enabled() : true));
		$entity->text_vi_enable_smilies($submit ? $this->request->is_set_post('text_vi_smilies') : ($entity->get_id() ? $entity->text_vi_smilies_enabled() : true));
	}

	/**
	* Map the form data fields to setters and set them to the entity
	*
	* @param \vinabb\web\entities\page_interface $entity Page entity
	*/
	protected function map_set_data(\vinabb\web\entities\page_interface $entity)
	{
		$map_fields = [
			'set_name'		=> $this->data['page_name'],
			'set_name_vi'	=> $this->data['page_name_vi'],
			'set_varname'	=> $this->data['page_varname'],
			'set_desc'		=> $this->data['page_desc'],
			'set_desc_vi'	=> $this->data['page_desc_vi'],
			'set_text'		=> $this->data['page_text'],
			'set_text_vi'	=> $this->data['page_text_vi']
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
				$this->errors[] = $e->get_friendly_message($this->language);
			}
		}

		unset($map_fields);
	}

	/**
	* Insert or update data, then log actions and clear cache if needed
	*
	* @param \vinabb\web\entities\page_interface $entity Page entity
	*/
	protected function save_data(\vinabb\web\entities\page_interface $entity)
	{
		if ($entity->get_id())
		{
			// Save the edited entity to the database
			$entity->save();

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PAGE_EDIT', time(), [$entity->get_name()]);

			$message = 'MESSAGE_PAGE_EDIT';
		}
		else
		{
			// Add the new entity to the database
			$entity = $this->operator->add_page($entity);

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PAGE_ADD', time(), [$entity->get_name()]);

			$message = 'MESSAGE_PAGE_ADD';
		}

		$this->cache->clear_pages();

		trigger_error($this->language->lang($message) . adm_back_link($this->u_action));
	}

	/**
	* Output entity data to template variables
	*
	* @param \vinabb\web\entities\page_interface $entity Page entity
	*/
	protected function data_to_tpl(\vinabb\web\entities\page_interface $entity)
	{
		$this->template->assign_vars([
			'PAGE_NAME'					=> $entity->get_name(),
			'PAGE_NAME_VI'				=> $entity->get_name_vi(),
			'PAGE_VARNAME'				=> $entity->get_varname(),
			'PAGE_DESC'					=> $entity->get_desc(),
			'PAGE_DESC_VI'				=> $entity->get_desc_vi(),
			'PAGE_TEXT'					=> $entity->get_text_for_edit(),
			'PAGE_TEXT_BBCODE'			=> $entity->text_bbcode_enabled(),
			'PAGE_TEXT_URLS'			=> $entity->text_urls_enabled(),
			'PAGE_TEXT_SMILIES'			=> $entity->text_smilies_enabled(),
			'PAGE_TEXT_VI'				=> $entity->get_text_vi_for_edit(),
			'PAGE_TEXT_VI_BBCODE'		=> $entity->text_vi_bbcode_enabled(),
			'PAGE_TEXT_VI_URLS'			=> $entity->text_vi_urls_enabled(),
			'PAGE_TEXT_VI_SMILIES'		=> $entity->text_vi_smilies_enabled(),
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
			'S_LINKS_ALLOWED'	=> true
		]);
	}

	/**
	* Delete a page
	*
	* @param int $page_id Page ID
	*/
	public function delete_page($page_id)
	{
		/** @var \vinabb\web\entities\page_interface */
		$entity = $this->container->get('vinabb.web.entities.page')->load($page_id);

		try
		{
			$this->operator->delete_page($page_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_PAGE_DELETE', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PAGE_DELETE', time(), [$entity->get_name()]);
		$this->cache->clear_pages();

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
