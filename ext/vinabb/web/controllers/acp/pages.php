<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\acp;

/**
* Controller for the pages_module
*/
class pages
{
	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \vinabb\web\entities\page_interface */
	protected $entity;

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
	* @param \vinabb\web\entities\page_interface	$entity		Page entity
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
		\vinabb\web\entities\page_interface $entity,
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
		$this->entity = $entity;
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

				'U_EDIT'	=> "{$this->u_action}&action=edit&id=" . $entity->get_id(),
				'U_DELETE'	=> "{$this->u_action}&action=delete&id=" . $entity->get_id()
			]);

			$this->template->assign_vars([
				'U_ACTION'	=> $this->u_action,
				'U_ADD'		=> "{$this->u_action}&action=add"
			]);
		}
	}

	public function add_page()
	{
		$this->add_edit_page_data();

		$this->template->assign_vars([
			'S_ADD'		=> true,
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	public function edit_page($page_id)
	{
		$this->add_edit_page_data($page_id);

		$this->template->assign_vars([
			'S_EDIT'	=> true,
			'U_ACTION'	=> "{$this->u_action}&action=edit&id={$page_id}"
		]);
	}

	public function add_edit_page_data($page_id = 0)
	{
		$page = $page_id ? $this->entity->load($page_id) : $this->entity;
		$errors = [];
		$submit = $this->request->is_set_post('submit');

		// Add form key for form validation checks
		add_form_key('add_edit_page');

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
			'bbcode'	=> $submit ? $data['text_bbcode'] : ($page_id ? $page->text_bbcode_enabled() : true),
			'urls'		=> $submit ? $data['text_urls'] : ($page_id ? $page->text_urls_enabled() : true),
			'smilies'	=> $submit ? $data['text_smilies'] : ($page_id ? $page->text_smilies_enabled() : true)
		];

		$text_vi_options = [
			'bbcode'	=> $submit ? $data['text_vi_bbcode'] : ($page_id ? $page->text_vi_bbcode_enabled() : true),
			'urls'		=> $submit ? $data['text_vi_urls'] : ($page_id ? $page->text_vi_urls_enabled() : true),
			'smilies'	=> $submit ? $data['text_vi_smilies'] : ($page_id ? $page->text_vi_smilies_enabled() : true)
		];

		// Set the parse options in the entity
		foreach ($text_options as $function => $enabled)
		{
			$page->{($enabled ? 'text_enable_' : 'text_disable_') . $function}();
		}

		foreach ($text_vi_options as $function => $enabled)
		{
			$page->{($enabled ? 'text_vi_enable_' : 'text_vi_disable_') . $function}();
		}

		// Purge temporary variable
		unset($text_options);
		unset($text_vi_options);

		if ($submit)
		{
			// Test if the form is valid
			// Use -1 to allow unlimited time to submit form
			if (!check_form_key('add_edit_page', -1))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			// Map the form's page data fields to setters
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

			// Set the mapped page data in the entity
			foreach ($map_fields as $entity_function => $page_data)
			{
				try
				{
					// Calling the $entity_function on the entity and passing it $page_data
					$page->$entity_function($page_data);
				}
				catch (\vinabb\web\exceptions\base $e)
				{
					// Catch exceptions and add them to errors array
					$errors[] = $e->get_message($this->language);
				}
			}

			// Purge temporary variable
			unset($map_fields);

			// Insert or update page
			if (empty($errors))
			{
				if ($page_id)
				{
					// Save the edited page entity to the database
					$page->save();

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PAGE_EDIT', time(), array($page->get_varname()));

					$message = 'MESSAGE_PAGE_EDIT';
				}
				else
				{
					// Add the new page entity to the database
					$page = $this->operator->add_page($page);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PAGE_ADD', time(), array($page->get_varname()));

					$message = 'MESSAGE_PAGE_ADD';
				}

				// Purge the cache
				$this->cache->clear_pages();

				// Show user confirmation of the page and provide link back to the previous screen
				trigger_error($this->language->lang($message) . adm_back_link($this->u_action));
			}
		}

		$this->template->assign_vars([
			'S_ERROR'	=> (bool) sizeof($errors),
			'ERROR_MSG'	=> sizeof($errors) ? implode('<br>', $errors) : '',

			'PAGE_NAME'					=> $page->get_name(),
			'PAGE_NAME_VI'				=> $page->get_name_vi(),
			'PAGE_VARNAME'				=> $page->get_varname(),
			'PAGE_DESC'					=> $page->get_desc(),
			'PAGE_DESC_VI'				=> $page->get_desc_vi(),
			'PAGE_TEXT'					=> $page->get_text_for_edit(),
			'PAGE_TEXT_VI'				=> $page->get_text_vi_for_edit(),
			'PAGE_ENABLE'				=> $page->get_enable(),
			'PAGE_ENABLE_GUEST'			=> $page->get_enable_guest(),
			'PAGE_ENABLE_BOT'			=> $page->get_enable_bot(),
			'PAGE_ENABLE_NEW_USER'		=> $page->get_enable_new_user(),
			'PAGE_ENABLE_USER'			=> $page->get_enable_user(),
			'PAGE_ENABLE_MOD'			=> $page->get_enable_mod(),
			'PAGE_ENABLE_GLOBAL_MOD'	=> $page->get_enable_global_mod(),
			'PAGE_ENABLE_ADMIN'			=> $page->get_enable_admin(),
			'PAGE_ENABLE_FOUNDER'		=> $page->get_enable_founder(),

			'U_BACK'	=> $this->u_action
		]);

		// Build custom BBCode
		include_once $this->root_path . 'includes/functions_display.' . $this->php_ext;
		display_custom_bbcodes();
	}

	public function delete_page($page_id)
	{
		try
		{
			// Delete the page
			$this->operator->delete_page($page_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			// Display an error message if delete failed
			trigger_error($this->language->lang('ACP_PAGES_DELETE_ERRORED') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PAGE_DELETE', time(), [$this->entity->load($page_id)->get_title()]);

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send([
				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang('MESSAGE_PAGE_DELETE'),
				'REFRESH_DATA'	=> [
					'time'	=> 3
				]
			]);
		}
	}
}
