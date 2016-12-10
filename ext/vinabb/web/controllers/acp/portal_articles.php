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
* Controller for the portal_articles_module
*/
class portal_articles implements portal_articles_interface
{
	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

	/** @var \phpbb\filesystem\filesystem_interface */
	protected $filesystem;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \vinabb\web\operators\portal_article_interface */
	protected $operator;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\files\upload */
	protected $upload;

	/** @var \phpbb\user */
	protected $user;

	/** @var \vinabb\web\controllers\helper_interface */
	protected $ext_helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $u_action;

	/** @var array */
	protected $data;

	/** @var array */
	protected $errors = [];

	/** @var string */
	protected $ext_root_path;

	/** @var array */
	protected $lang_data;

	/** @var array */
	protected $cat_data;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface	$cache			Cache service
	* @param \phpbb\config\config								$config			Config object
	* @param ContainerInterface									$container		Container object
	* @param \phpbb\extension\manager							$ext_manager	Extension manager
	* @param \phpbb\filesystem\filesystem_interface				$filesystem		Filesystem object
	* @param \phpbb\language\language							$language		Language object
	* @param \phpbb\log\log										$log			Log object
	* @param \vinabb\web\operators\portal_article_interface		$operator		Article operators
	* @param \phpbb\request\request								$request		Request object
	* @param \phpbb\template\template							$template		Template object
	* @param \phpbb\files\upload								$upload			Upload object
	* @param \phpbb\user										$user			User object
	* @param \vinabb\web\controllers\helper_interface			$ext_helper		Extension helper
	* @param string												$root_path		phpBB root path
	* @param string												$php_ext		PHP file extension
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		ContainerInterface $container,
		\phpbb\extension\manager $ext_manager,
		\phpbb\filesystem\filesystem_interface $filesystem,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\vinabb\web\operators\portal_article_interface $operator,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\files\upload $upload,
		\phpbb\user $user,
		\vinabb\web\controllers\helper_interface $ext_helper,
		$root_path,
		$php_ext
	)
	{
		$this->cache = $cache;
		$this->config = $config;
		$this->container = $container;
		$this->ext_manager = $ext_manager;
		$this->filesystem = $filesystem;
		$this->language = $language;
		$this->log = $log;
		$this->operator = $operator;
		$this->request = $request;
		$this->template = $template;
		$this->upload = $upload;
		$this->user = $user;
		$this->ext_helper = $ext_helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		$this->ext_root_path = $this->ext_manager->get_extension_path('vinabb/web', true);
		$this->lang_data = $this->cache->get_lang_data();
		$this->cat_data = $this->cache->get_portal_cats();
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
	* Display articles
	*/
	public function display_articles()
	{
		// Grab all from database
		$entities = $this->operator->get_articles();

		/* @var \vinabb\web\entities\portal_article_interface $entity */
		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('articles', [
				'CATEGORY'	=> $this->cat_data[$entity->get_cat_id()][($entity->get_lang() == constants::LANG_VIETNAMESE) ? 'name_vi' : 'name'],
				'NAME'		=> $entity->get_name(),
				'LANG'		=> $this->lang_data[$entity->get_lang()]['local_name'],
				'ENABLE'	=> $entity->get_enable(),
				'VIEWS'		=> $entity->get_views(),

				'U_EDIT'	=> "{$this->u_action}&action=edit&id={$entity->get_id()}",
				'U_DELETE'	=> "{$this->u_action}&action=delete&id={$entity->get_id()}"
			]);
		}

		$this->template->assign_vars([
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Add an article
	*/
	public function add_article()
	{
		// Initiate an entity
		/* @var \vinabb\web\entities\portal_article_interface */
		$entity = $this->container->get('vinabb.web.entities.portal_article');

		// Process the new entity
		$this->add_edit_data($entity);

		// Build the category selection
		$this->build_cat_options($entity, $this->data['cat_id'], 'add');

		$this->template->assign_vars([
			'S_ADD'		=> true,
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Edit an article
	*
	* @param int $article_id Article ID
	*/
	public function edit_article($article_id)
	{
		// Initiate and load the entity
		/* @var \vinabb\web\entities\portal_article_interface */
		$entity = $this->container->get('vinabb.web.entities.portal_article')->load($article_id);

		// Process the edited entity
		$this->add_edit_data($entity);

		// Build the category selection
		$this->build_cat_options($entity);

		$this->template->assign_vars([
			'S_EDIT'	=> true,
			'U_ACTION'	=> "{$this->u_action}&action=edit&id={$article_id}"
		]);
	}

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\portal_article_interface $entity Article entity
	*/
	public function add_edit_data(\vinabb\web\entities\portal_article_interface $entity)
	{
		$submit = $this->request->is_set_post('submit');

		// Load posting language file for the BBCode editor
		$this->language->add_lang('posting');

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_portal_articles');

		// Get form data
		$this->request_data();

		// Set the parse options to the entity
		$this->set_bbcode_options($entity, $submit);

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_portal_articles'))
			{
				$this->errors[] = $this->language->lang('FORM_INVALID');
			}

			// Delete the old article image if uploaded a new one
			if ($this->data['article_img'] != '' && $this->data['article_img'] != $entity->get_img(false, false))
			{
				$this->filesystem->remove($entity->get_img(true));
			}

			// Map and set data to the entity
			$this->map_set_data($entity);

			// Upload files
			$this->upload_data($entity);

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
			'cat_id'			=> $this->request->variable('cat_id', 0),
			'user_id'			=> $this->user->data['user_id'],
			'article_name'		=> $this->request->variable('article_name', '', true),
			'article_lang'		=> $this->request->variable('article_lang', ''),
			'article_img'		=> $this->request->file('article_img'),
			'article_desc'		=> $this->request->variable('article_desc', '', true),
			'article_text'		=> $this->request->variable('article_text', '', true),
			'text_bbcode'		=> $this->request->variable('text_bbcode', true),
			'text_urls'			=> $this->request->variable('text_urls', true),
			'text_smilies'		=> $this->request->variable('text_smilies', true),
			'article_enable'	=> $this->request->variable('article_enable', true),
			'article_time'		=> null
		];
	}

	/**
	* Grab the form data's parsing options and set them to the entity
	*
	* If submit, use data from the form
	* In edit mode, use data stored in the entity
	* In add mode, use default values
	*
	* @param \vinabb\web\entities\portal_article_interface $entity Article entity
	*/
	protected function set_bbcode_options(\vinabb\web\entities\portal_article_interface $entity, $submit)
	{
		$entity->text_enable_bbcode($submit ? $this->request->is_set_post('text_bbcode') : ($entity->get_id() ? $entity->text_bbcode_enabled() : true));
		$entity->text_enable_urls($submit ? $this->request->is_set_post('text_urls') : ($entity->get_id() ? $entity->text_urls_enabled() : true));
		$entity->text_enable_smilies($submit ? $this->request->is_set_post('text_smilies') : ($entity->get_id() ? $entity->text_smilies_enabled() : true));
	}

	/**
	* Map the form data fields to setters and set them to the entity
	*
	* @param \vinabb\web\entities\portal_article_interface $entity Article entity
	*/
	protected function map_set_data(\vinabb\web\entities\portal_article_interface $entity)
	{
		$map_fields = [
			'set_cat_id'	=> $this->data['cat_id'],
			'set_user_id'	=> $this->data['user_id'],
			'set_name'		=> $this->data['article_name'],
			'set_name_seo'	=> $this->ext_helper->clean_url($this->data['article_name']),
			'set_lang'		=> $this->data['article_lang'],
			'set_desc'		=> $this->data['article_desc'],
			'set_text'		=> $this->data['article_text'],
			'set_time'		=> $this->data['article_time']
		];

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
	* Upload files and return their filenames to the form data
	*
	* @param \vinabb\web\entities\portal_article_interface $entity Article entity
	*/
	protected function upload_data(\vinabb\web\entities\portal_article_interface $entity)
	{
		// If there are not any input errors, then begin to upload file
		if ($this->data['article_img']['name'] != '' && !sizeof($this->errors))
		{
			$entity->set_img($this->upload_article_img('article_img'));
		}
	}

	/**
	* Insert or update data, then log actions and clear cache if needed
	*
	* @param \vinabb\web\entities\portal_article_interface $entity Article entity
	*/
	protected function save_data(\vinabb\web\entities\portal_article_interface $entity)
	{
		if ($entity->get_id())
		{
			// Save the edited entity to the database
			$entity->save();

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_ARTICLE_EDIT', time(), [$entity->get_name()]);

			$message = 'MESSAGE_ARTICLE_EDIT';
		}
		else
		{
			// Add the new entity to the database
			$entity = $this->operator->add_article($entity);

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_ARTICLE_ADD', time(), [$entity->get_name()]);

			$message = 'MESSAGE_ARTICLE_ADD';
		}

		$this->cache->clear_index_articles($entity->get_lang());

		trigger_error($this->language->lang($message) . adm_back_link($this->u_action));
	}

	/**
	* Output entity data to template variables
	*
	* @param \vinabb\web\entities\portal_article_interface $entity Article entity
	*/
	protected function data_to_tpl(\vinabb\web\entities\portal_article_interface $entity)
	{
		$this->template->assign_vars([
			'ARTICLE_NAME'			=> $entity->get_name(),
			'ARTICLE_IMG'			=> $entity->get_img(),
			'ARTICLE_DESC'			=> $entity->get_desc(),
			'ARTICLE_TEXT'			=> $entity->get_text_for_edit(),
			'ARTICLE_TEXT_BBCODE'	=> $entity->text_bbcode_enabled(),
			'ARTICLE_TEXT_URLS'		=> $entity->text_urls_enabled(),
			'ARTICLE_TEXT_SMILIES'	=> $entity->text_smilies_enabled(),
			'ARTICLE_ENABLE'		=> $entity->get_enable(),

			'LANG_OPTIONS'	=> $this->ext_helper->build_lang_list($entity->get_lang()),

			// These template variables used for the BBCode editor
			'S_BBCODE_ALLOWED'	=> true,
			'S_SMILIES_ALLOWED'	=> true,
			'S_BBCODE_IMG'		=> true,
			'S_BBCODE_FLASH'	=> true,
			'S_LINKS_ALLOWED'	=> true
		]);
	}

	/**
	* Delete an article
	*
	* @param int $article_id Article ID
	*/
	public function delete_article($article_id)
	{
		/** @var \vinabb\web\entities\portal_article_interface */
		$entity = $this->container->get('vinabb.web.entities.portal_article')->load($article_id);

		try
		{
			$this->operator->delete_article($article_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_ARTICLE_DELETE', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_ARTICLE_DELETE', time(), [$entity->get_name()]);
		$this->cache->clear_index_articles($entity->get_lang());

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send([
				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang('MESSAGE_ARTICLE_DELETE'),
				'REFRESH_DATA'	=> ['time'	=> 3]
			]);
		}
	}

	/**
	* Generate options of available categories
	*
	* @param \vinabb\web\entities\portal_article_interface	$entity	Article entity
	* @param int											$cat_id	Category ID
	* @param string											$mode	Add or edit mode?
	*/
	protected function build_cat_options($entity, $cat_id = 0, $mode = 'edit')
	{
		$options = $this->container->get('vinabb.web.operators.portal_category')->get_cats();
		$cat_id = ($mode == 'edit') ? $entity->get_cat_id() : $cat_id;

		$padding = '';
		$padding_store = [];
		$right = 0;

		/** @var \vinabb\web\entities\portal_category_interface $option */
		foreach ($options as $option)
		{
			if ($option->get_left_id() < $right)
			{
				$padding .= '&nbsp;&nbsp;';
				$padding_store[$option->get_parent_id()] = $padding;
			}
			else if ($option->get_left_id() > $right + 1)
			{
				$padding = isset($padding_store[$option->get_parent_id()]) ? $padding_store[$option->get_parent_id()] : '';
			}

			$right = $option->get_right_id();

			$this->template->assign_block_vars('cat_options', [
				'ID'		=> $option->get_id(),
				'NAME'		=> $padding . $option->get_name(),
				'NAME_VI'	=> $padding . $option->get_name_vi(),

				'S_SELECTED'	=> $option->get_id() == $cat_id
			]);
		}
	}

	/**
	* Check if we are able to upload a file
	*
	* @return bool
	*/
	protected function can_upload()
	{
		return (file_exists($this->ext_root_path . constants::DIR_ARTICLE_IMAGES) && $this->filesystem->is_writable($this->ext_root_path . constants::DIR_ARTICLE_IMAGES) && (ini_get('file_uploads') || strtolower(ini_get('file_uploads')) == 'on'));
	}

	/**
	* Upload article image
	*
	* @return string Filename, empty if there are errors
	*/
	protected function upload_article_img($form_name)
	{
		if (!$this->can_upload())
		{
			return '';
		}

		$this->upload->set_error_prefix('ERROR_' . strtoupper($form_name) . '_')
			->set_allowed_extensions(constants::FILE_EXTENSION_IMAGES)
			->set_disallowed_content((isset($this->config['mime_triggers']) ? explode('|', $this->config['mime_triggers']) : false));

		$file = $this->upload->handle_upload('files.types.form', $form_name);

		// Rename file
		$file->clean_filename('avatar', date('Y-m-d-H-i-s_', time()), $this->user->data['user_id']);

		// If there was an error during upload, then abort operation
		if (sizeof($file->error))
		{
			$file->remove();
			$this->errors = array_merge($this->errors, $file->error);

			return '';
		}

		// Set new destination
		$destination = $this->ext_helper->remove_trailing_slash($this->ext_root_path . constants::DIR_ARTICLE_IMAGES);

		// Move file and overwrite any existing image
		if (!sizeof($this->errors))
		{
			$file->move_file($destination, true);
		}

		// If there was an error during move, then clean up leftovers
		if (sizeof($file->error))
		{
			$this->errors = array_merge($this->errors, $file->error);
		}

		if (sizeof($this->errors))
		{
			$file->remove();

			return '';
		}

		return $file->get('realname');
	}
}
