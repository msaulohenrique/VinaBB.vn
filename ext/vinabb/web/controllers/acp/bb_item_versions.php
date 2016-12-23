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
* Controller for the bb_item_versions_module
*/
class bb_item_versions implements bb_item_versions_interface
{
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

	/** @var \vinabb\web\operators\bb_item_version_interface $operator */
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

	/** @var int $item_id */
	protected $item_id;

	/** @var string $item_name */
	protected $item_name;

	/** @var array $data */
	protected $data;

	/** @var array $errors Use [] because it will be merged to other arrays */
	protected $errors = [];

	/** @var string $ext_root_path */
	protected $ext_root_path;

	/** @var string $lang_key */
	protected $lang_key;

	/**
	* Constructor
	*
	* @param \phpbb\config\config								$config			Config object
	* @param ContainerInterface									$container		Container object
	* @param \phpbb\extension\manager							$ext_manager	Extension manager
	* @param \phpbb\filesystem\filesystem_interface				$filesystem		Filesystem object
	* @param \phpbb\language\language							$language		Language object
	* @param \phpbb\log\log										$log			Log object
	* @param \vinabb\web\operators\bb_item_version_interface	$operator		BB item version operators
	* @param \phpbb\request\request								$request		Request object
	* @param \phpbb\template\template							$template		Template object
	* @param \phpbb\files\upload								$upload			Upload object
	* @param \phpbb\user										$user			User object
	* @param \vinabb\web\controllers\helper_interface			$ext_helper		Extension helper
	*/
	public function __construct(
		\phpbb\config\config $config,
		ContainerInterface $container,
		\phpbb\extension\manager $ext_manager,
		\phpbb\filesystem\filesystem_interface $filesystem,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\vinabb\web\operators\bb_item_version_interface $operator,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\files\upload $upload,
		\phpbb\user $user,
		\vinabb\web\controllers\helper_interface $ext_helper
	)
	{
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
	}

	/**
	* Set form data
	*
	* @param array $data Form data
	*/
	public function set_form_data($data)
	{
		$this->u_action = $data['u_action'] . "&id={$data['item_id']}";
		$this->lang_key = strtoupper($data['mode']);
		$this->item_id = $data['item_id'];

		/** @var \vinabb\web\entities\bb_item_interface $item */
		$item = $this->container->get('vinabb.web.entities.bb_item')->load($data['item_id']);
		$this->item_name = $item->get_name();
	}

	/**
	* Display item versions
	*/
	public function display_versions()
	{
		// Grab all from database
		$entities = $this->operator->get_versions($this->item_id);

		/** @var \vinabb\web\entities\bb_item_version_interface $entity */
		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('versions', [
				'PHPBB_VERSION'	=> $entity->get_phpbb_version(),
				'ITEM_VERSION'	=> $entity->get_version(),
				'PRICE'			=> $entity->get_price(),

				'U_EDIT'	=> "{$this->u_action}&action=edit&branch={$entity->get_phpbb_branch()}",
				'U_DELETE'	=> "{$this->u_action}&action=delete&branch={$entity->get_phpbb_branch()}"
			]);
		}

		$this->template->assign_vars([
			'PAGE_TITLE_EXPLAIN'	=> $this->language->lang('ACP_BB_' . $this->lang_key . '_VERSIONS_EXPLAIN'),
			'ITEM_NAME'				=> $this->item_name,
			'ITEM_VERSION_LANG'		=> $this->language->lang($this->lang_key . '_VERSION'),

			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Add an item version
	*/
	public function add_version()
	{
		// Initiate an entity
		/** @var \vinabb\web\entities\bb_item_version_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.bb_item_version');

		// Process the new entity
		$this->add_edit_data($entity);

		// Build the phpBB version selection
		$this->build_phpbb_options($entity, $this->data['phpbb_version'], 'add');

		$this->template->assign_vars([
			'S_ADD'		=> true,
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Edit an item version
	*
	* @param int	$item_id		Item ID
	* @param string	$phpbb_branch	phpBB branch
	*/
	public function edit_version($item_id, $phpbb_branch)
	{
		// Initiate and load the entity
		/** @var \vinabb\web\entities\bb_item_version_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.bb_item_version')->load($item_id, $phpbb_branch);

		// Process the edited entity
		$this->add_edit_data($entity);

		// Build the phpBB version selection
		$this->build_phpbb_options($entity);

		$this->template->assign_vars([
			'S_EDIT'	=> true,
			'U_ACTION'	=> "{$this->u_action}&action=edit&branch={$phpbb_branch}"
		]);
	}

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\bb_item_version_interface $entity BB item version entity
	*/
	public function add_edit_data(\vinabb\web\entities\bb_item_version_interface $entity)
	{
		$submit = $this->request->is_set_post('submit');

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_bb_item_versions');

		// Get form data
		$this->request_data();

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_bb_item_versions'))
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
			'ERRORS'				=> sizeof($this->errors) ? implode('<br>', $this->errors) : '',
			'MAX_VERSION_NUMBER'	=> constants::MAX_VERSION_NUMBER,
			'PAGE_TITLE_EXPLAIN'	=> $this->language->lang('ACP_BB_' . $this->lang_key . '_VERSIONS_EXPLAIN'),
			'ITEM_NAME'				=> $this->item_name,
			'ITEM_VERSION_LANG'		=> $this->language->lang($this->lang_key . '_VERSION'),

			'U_BACK'	=> $this->u_action
		]);
	}

	/**
	* Request data from the form
	*/
	protected function request_data()
	{
		$this->data = [
			'phpbb_version'	=> $this->request->variable('phpbb_version', ''),
			'item_version'	=> $this->request->variable('item_version', ''),
			'item_file'		=> $this->request->file('item_file'),
			'item_price'	=> $this->request->variable('item_price', 0)
		];
	}

	/**
	* Map the form data fields to setters and set them to the entity
	*
	* @param \vinabb\web\entities\bb_item_version_interface $entity BB item version entity
	*/
	protected function map_set_data(\vinabb\web\entities\bb_item_version_interface $entity)
	{
		$map_fields = [
			'set_phpbb_version'	=> $this->data['phpbb_version'],
			'set_version'		=> $this->data['item_version']
		];

		// Set the mapped data in the entity
		foreach ($map_fields as $entity_function => $version_data)
		{
			try
			{
				// Calling the $entity_function on the entity and passing it $version_data
				$entity->$entity_function($version_data);
			}
			catch (\vinabb\web\exceptions\base $e)
			{
				// Replace prefix 'item_' with language key prefix: ERROR_ITEM_ -> ERROR_BB_EXT_
				if (substr($e->get_entity_name(), 0, 5) == 'item_')
				{
					$e->set_entity_name('BB_' . $this->lang_key . substr($e->get_entity_name(), 4));
				}

				$this->errors[] = $e->get_friendly_message($this->language);
			}
		}

		unset($map_fields);
	}

	/**
	* Upload files and return their filenames to the form data
	*
	* @param \vinabb\web\entities\bb_item_version_interface $entity BB item version entity
	*/
	protected function upload_data(\vinabb\web\entities\bb_item_version_interface $entity)
	{
		if ($this->data['item_file']['name'] == '')
		{
			$this->errors[] = $this->language->lang('ERROR_ITEM_FILE_EMPTY');
		}

		// If there are not any input errors, then begin to upload file
		if ($this->can_upload() && $this->data['item_file']['name'] != '' && !sizeof($this->errors))
		{
			// Delete the old file if uploaded a new one
			if ($this->data['item_file']['name'] != '' && $this->data['item_file']['name'] != $entity->get_file(true, false))
			{
				$this->filesystem->remove($entity->get_file(true));
			}

			$entity->set_file($this->upload_item_file('item_file'));
		}
	}

	/**
	* Insert or update data, then log actions and clear cache if needed
	*
	* @param \vinabb\web\entities\bb_item_version_interface $entity BB item version entity
	*/
	protected function save_data(\vinabb\web\entities\bb_item_version_interface $entity)
	{
		if ($entity->get_id())
		{
			// Save the edited entity to the database
			$entity->save();

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, "LOG_BB_{$this->lang_key}_VERSION_EDIT", time(), [$this->item_name . ' ' . $entity->get_version()]);

			$message = 'MESSAGE_VERSION_EDIT';
		}
		else
		{
			// Add the new entity to the database
			$entity = $this->operator->add_version($entity, $this->item_id, $this->data['phpbb_branch']);

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, "LOG_BB_{$this->lang_key}_VERSION_ADD", time(), [$this->item_name . ' ' . $entity->get_version()]);

			$message = 'MESSAGE_VERSION_ADD';
		}

		trigger_error($this->language->lang($message) . adm_back_link($this->u_action));
	}

	/**
	* Output entity data to template variables
	*
	* @param \vinabb\web\entities\bb_item_version_interface $entity BB item version entity
	*/
	protected function data_to_tpl(\vinabb\web\entities\bb_item_version_interface $entity)
	{
		$this->template->assign_vars([
			'PHPBB_VERSION'	=> $entity->get_phpbb_version(),
			'ITEM_VERSION'	=> $entity->get_version(),
			'ITEM_PRICE'	=> $entity->get_price()
		]);
	}

	/**
	* Delete an item version
	*
	* @param int	$item_id		Item ID
	* @param string	$phpbb_branch	phpBB branch
	*/
	public function delete_version($item_id, $phpbb_branch)
	{
		/** @var \vinabb\web\entities\bb_item_version_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.bb_item_version')->load($item_id, $phpbb_branch);

		try
		{
			$this->operator->delete_version($item_id, $phpbb_branch);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang("ERROR_{$this->lang_key}_DELETE", $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, "LOG_BB_{$this->lang_key}_VERSION_DELETE", time(), [$this->item_name . ' ' . $entity->get_version()]);

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send([
				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang('MESSAGE_VERSION_DELETE'),
				'REFRESH_DATA'	=> ['time'	=> 3]
			]);
		}
	}

	/**
	* Generate options of available phpBB versions
	*
	* @param \vinabb\web\entities\bb_item_version_interface	$entity			BB item entity
	* @param string											$phpbb_version	phpBB version
	* @param string											$mode			Add or edit mode?
	*/
	protected function build_phpbb_options(\vinabb\web\entities\bb_item_version_interface $entity, $phpbb_version = '', $mode = 'edit')
	{
		$phpbb_version = ($mode == 'edit') ? $entity->get_phpbb_version() : $phpbb_version;

		foreach ($this->ext_helper->get_phpbb_versions() as $branch => $branch_data)
		{
			foreach ($branch_data as $version => $version_data)
			{
				$this->template->assign_block_vars('phpbb_versions', [
					'ID'		=> $version,
					'NAME'		=> $version_data['name'],

					'S_SELECTED'	=> $version == $phpbb_version
				]);
			}
		}
	}

	/**
	* Check if we are able to upload a file
	*
	* @return bool
	*/
	protected function can_upload()
	{
		return (file_exists($this->ext_root_path . constants::DIR_BB_FILES) && $this->filesystem->is_writable($this->ext_root_path . constants::DIR_BB_FILES) && (ini_get('file_uploads') || strtolower(ini_get('file_uploads')) == 'on'));
	}

	/**
	* Upload item file
	*
	* @return string Filename, empty if there are errors
	*/
	protected function upload_item_file($form_name)
	{
		$this->upload->set_error_prefix('ERROR_' . strtoupper($form_name) . '_')
			->set_allowed_extensions(constants::FILE_EXTENSION_BB_FILES)
			->set_disallowed_content((isset($this->config['mime_triggers']) ? explode('|', $this->config['mime_triggers']) : false));

		$file = $this->upload->handle_upload('files.types.form', $form_name);

		// Rename file
		$file->clean_filename('avatar', str_replace(' ', '', $this->item_name) . '_', $this->data['item_version']);

		// If there was an error during upload, then abort operation
		if (sizeof($file->error))
		{
			$file->remove();
			$this->errors = array_merge($this->errors, $file->error);

			return '';
		}

		// Set new destination
		$destination = $this->ext_helper->remove_trailing_slash($this->ext_root_path . constants::DIR_BB_FILES);

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
