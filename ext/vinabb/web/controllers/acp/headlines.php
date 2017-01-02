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
* Controller for the headlines_module
*/
class headlines implements headlines_interface
{
	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\extension\manager $ext_manager */
	protected $ext_manager;

	/** @var \phpbb\filesystem\filesystem_interface $filesystem */
	protected $filesystem;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\log\log $log */
	protected $log;

	/** @var \vinabb\web\operators\headline_interface $operator */
	protected $operator;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\files\upload $upload */
	protected $upload;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \vinabb\web\controllers\helper_interface $ext_helper */
	protected $ext_helper;

	/** @var string $u_action */
	protected $u_action;

	/** @var string $headline_lang */
	protected $headline_lang;

	/** @var array $data */
	protected $data;

	/** @var array $errors */
	protected $errors;

	/** @var string $ext_root_path */
	protected $ext_root_path;

	/** @var string $upload_dir_path */
	protected $upload_dir_path;

	/** @var array $lang_data */
	protected $lang_data;

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
	* @param \vinabb\web\operators\headline_interface			$operator		Headline operators
	* @param \phpbb\request\request								$request		Request object
	* @param \phpbb\template\template							$template		Template object
	* @param \phpbb\files\upload								$upload			Upload object
	* @param \phpbb\user										$user			User object
	* @param \vinabb\web\controllers\helper_interface			$ext_helper		Extension helper
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		ContainerInterface $container,
		\phpbb\extension\manager $ext_manager,
		\phpbb\filesystem\filesystem_interface $filesystem,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\vinabb\web\operators\headline_interface $operator,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\files\upload $upload,
		\phpbb\user $user,
		\vinabb\web\controllers\helper_interface $ext_helper
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

		$this->ext_root_path = $this->ext_manager->get_extension_path('vinabb/web', true);
		$this->upload_dir_path = $this->ext_root_path . constants::DIR_HEADLINE_IMAGES;
		$this->lang_data = $this->cache->get_lang_data();
	}

	/**
	* Set form data
	*
	* @param array $data Form data
	*/
	public function set_form_data($data)
	{
		$this->u_action = $data['u_action'];
		$this->headline_lang = $data['headline_lang'];
	}

	/**
	* Language selection
	*/
	public function select_lang()
	{
		if (sizeof($this->lang_data) > 1)
		{
			foreach ($this->lang_data as $lang_iso => $lang_data)
			{
				$this->template->assign_block_vars('lang_options', [
					'VALUE'	=> $lang_iso,
					'NAME'	=> ($lang_data['english_name'] == $lang_data['local_name']) ? $lang_data['english_name'] : $lang_data['english_name'] . ' (' . $lang_data['local_name'] . ')'
				]);
			}

			$this->template->assign_var('U_ACTION', $this->u_action);
		}
		// If there is only one available language, we do not need the selection list
		else
		{
			$this->display_headlines();
		}
	}

	/**
	* Display headlines
	*/
	public function display_headlines()
	{
		// Grab all from database
		$entities = $this->operator->get_headlines($this->headline_lang);

		/** @var \vinabb\web\entities\headline_interface $entity */
		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('headlines', [
				'NAME'	=> $entity->get_name(),
				'DESC'	=> $entity->get_desc(),
				'IMG'	=> $entity->get_img(),
				'URL'	=> $entity->get_url(),

				'U_EDIT'		=> "{$this->u_action}&action=edit&lang={$this->headline_lang}&id={$entity->get_id()}",
				'U_MOVE_DOWN'	=> "{$this->u_action}&action=move_down&lang={$this->headline_lang}&id={$entity->get_id()}&hash=" . generate_link_hash('down' . $entity->get_id()),
				'U_MOVE_UP'		=> "{$this->u_action}&action=move_up&lang={$this->headline_lang}&id={$entity->get_id()}&hash=" . generate_link_hash('up' . $entity->get_id()),
				'U_DELETE'		=> "{$this->u_action}&action=delete&lang={$this->headline_lang}&id={$entity->get_id()}"
			]);
		}

		$this->template->assign_vars([
			'LANG_NAME'	=> $this->lang_data[$this->headline_lang]['local_name'],

			'U_ACTION'	=> "{$this->u_action}&action=add&lang={$this->headline_lang}"
		]);
	}

	/**
	* Add a headline
	*/
	public function add_headline()
	{
		// Initiate an entity
		/** @var \vinabb\web\entities\headline_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.headline');

		// Process the new entity
		$this->add_edit_data($entity);

		$this->template->assign_vars([
			'S_ADD'		=> true,

			'U_ACTION'	=> "{$this->u_action}&action=add&lang={$this->headline_lang}",
			'U_BACK'	=> "{$this->u_action}&lang={$this->headline_lang}"
		]);
	}

	/**
	* Edit a headline
	*
	* @param int $headline_id Headline ID
	*/
	public function edit_headline($headline_id)
	{
		// Initiate and load the entity
		/** @var \vinabb\web\entities\headline_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.headline')->load($headline_id);

		// Process the edited entity
		$this->add_edit_data($entity);

		$this->template->assign_vars([
			'S_EDIT'	=> true,

			'U_ACTION'	=> "{$this->u_action}&action=edit&lang={$this->headline_lang}&id={$headline_id}",
			'U_BACK'	=> "{$this->u_action}&lang={$this->headline_lang}"
		]);
	}

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\headline_interface $entity Headline entity
	*/
	public function add_edit_data(\vinabb\web\entities\headline_interface $entity)
	{
		$submit = $this->request->is_set_post('submit');

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_headlines');

		// Get form data
		$this->request_data();

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_headlines'))
			{
				$this->errors[] = $this->language->lang('FORM_INVALID');
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
			'LANG_NAME'	=> $this->lang_data[$this->headline_lang]['local_name'],
			'ERRORS'	=> sizeof($this->errors) ? implode('<br>', $this->errors) : ''
		]);
	}

	/**
	* Request data from the form
	*/
	protected function request_data()
	{
		$this->data = [
			'headline_name'		=> $this->request->variable('headline_name', '', true),
			'headline_desc'		=> $this->request->variable('headline_desc', '', true),
			'headline_img'		=> $this->request->file('headline_img'),
			'headline_url'		=> $this->request->variable('headline_url', '')
		];
	}

	/**
	* Map the form data fields to setters and set them to the entity
	*
	* @param \vinabb\web\entities\headline_interface $entity Headline entity
	*/
	protected function map_set_data(\vinabb\web\entities\headline_interface $entity)
	{
		$map_fields = [
			'set_lang'	=> $this->headline_lang,
			'set_name'	=> $this->data['headline_name'],
			'set_desc'	=> $this->data['headline_desc'],
			'set_url'	=> $this->data['headline_url'],
			'set_order'	=> $this->headline_lang
		];

		// Set the mapped data in the entity
		foreach ($map_fields as $entity_function => $headline_data)
		{
			try
			{
				// Calling the $entity_function on the entity and passing it $headline_data
				$entity->$entity_function($headline_data);
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
	* @param \vinabb\web\entities\headline_interface $entity Headline entity
	*/
	protected function upload_data(\vinabb\web\entities\headline_interface $entity)
	{
		// If there are not any input errors, then begin to upload file
		if ($this->can_upload() && $this->data['headline_img']['name'] != '' && !sizeof($this->errors))
		{
			// Delete the old file if uploaded a new one
			if ($this->data['headline_img']['name'] != $entity->get_img(true, false))
			{
				$this->filesystem->remove($entity->get_img(true));
			}

			$entity->set_img($this->upload_headline_img('headline_img'));
		}
	}

	/**
	* Insert or update data, then log actions and clear cache if needed
	*
	* @param \vinabb\web\entities\headline_interface $entity Headline entity
	*/
	protected function save_data(\vinabb\web\entities\headline_interface $entity)
	{
		if ($entity->get_id())
		{
			// Save the edited entity to the database
			$entity->save();

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_HEADLINE_EDIT', time(), [$entity->get_name()]);

			$message = 'MESSAGE_HEADLINE_EDIT';
		}
		else
		{
			// Add the new entity to the database
			$entity = $this->operator->add_headline($entity);

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_HEADLINE_ADD', time(), [$entity->get_name()]);

			$message = 'MESSAGE_HEADLINE_ADD';
		}

		$this->cache->clear_headlines($this->headline_lang);

		trigger_error($this->language->lang($message) . adm_back_link($this->u_action));
	}

	/**
	* Output entity data to template variables
	*
	* @param \vinabb\web\entities\headline_interface $entity Headline entity
	*/
	protected function data_to_tpl(\vinabb\web\entities\headline_interface $entity)
	{
		$this->template->assign_vars([
			'HEADLINE_NAME'	=> $entity->get_name(),
			'HEADLINE_DESC'	=> $entity->get_desc(),
			'HEADLINE_IMG'	=> $entity->get_img(),
			'HEADLINE_URL'	=> $entity->get_url()
		]);
	}

	/**
	* Move a headline up/down
	*
	* @param int	$headline_id	Headline ID
	* @param string	$direction		The direction (up|down)
	*/
	public function move_headline($headline_id, $direction)
	{
		// Check the valid link hash
		if (!check_link_hash($this->request->variable('hash', ''), $direction . $headline_id))
		{
			trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		try
		{
			$this->operator->move_headline($this->headline_lang, $headline_id, $direction);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_HEADLINE_MOVE', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->cache->clear_headlines($this->headline_lang);

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(['success' => true]);
		}
	}

	/**
	* Delete a headline
	*
	* @param int $headline_id Headline ID
	*/
	public function delete_headline($headline_id)
	{
		/** @var \vinabb\web\entities\headline_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.headline')->load($headline_id);

		try
		{
			$this->operator->delete_headline($headline_id);
			$this->filesystem->remove($entity->get_img(true));
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_HEADLINE_DELETE', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_HEADLINE_DELETE', time(), [$entity->get_name()]);
		$this->cache->clear_headlines($this->headline_lang);

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send([
				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang('MESSAGE_HEADLINE_DELETE'),
				'REFRESH_DATA'	=> ['time'	=> 3]
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
		return (file_exists($this->upload_dir_path) && $this->filesystem->is_writable($this->upload_dir_path) && (ini_get('file_uploads') || strtolower(ini_get('file_uploads')) == 'on'));
	}

	/**
	* Upload article image
	*
	* @return string Filename, empty if there are errors
	*/
	protected function upload_headline_img($form_name)
	{
		$this->upload->set_error_prefix('ERROR_' . strtoupper($form_name) . '_')
			->set_allowed_extensions(constants::FILE_EXTENSION_IMAGES)
			->set_disallowed_content((isset($this->config['mime_triggers']) ? explode('|', $this->config['mime_triggers']) : false));

		$file = $this->upload->handle_upload('files.types.form', $form_name);

		// Rename file
		$file->clean_filename('avatar', $this->headline_lang . '_' . pathinfo($file->get('uploadname'), PATHINFO_FILENAME));

		// Set new destination
		$destination = $this->ext_helper->remove_trailing_slash($this->upload_dir_path);

		// Move file and overwrite any existing image
		$file->move_file($destination, true);

		// If there was an error during upload, then clean up leftovers
		if (sizeof($file->error))
		{
			$file->remove();
			$this->errors += $file->error;

			return '';
		}

		return $file->get('realname');
	}
}
