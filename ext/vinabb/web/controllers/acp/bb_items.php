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
* Controller for the bb_items_module
*/
class bb_items implements bb_items_interface
{
	/** @var \vinabb\web\controllers\acp\bb_item_versions_interface */
	protected $bb_item_version;

	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\log\log $log */
	protected $log;

	/** @var \vinabb\web\operators\bb_item_interface $operator */
	protected $operator;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \vinabb\web\controllers\helper_interface $ext_helper */
	protected $ext_helper;

	/** @var string $root_path */
	protected $root_path;

	/** @var string $admin_path */
	protected $admin_path;

	/** @var string $php_ext */
	protected $php_ext;

	/** @var string $mode */
	protected $mode;

	/** @var string $u_action */
	protected $u_action;

	/** @var array $data */
	protected $data;

	/** @var array $errors Use [] because it will be merged to other arrays */
	protected $errors = [];

	/** @var int $bb_type */
	protected $bb_type;

	/** @var string $lang_key */
	protected $lang_key;

	/** @var array $cat_data */
	protected $cat_data;

	/** @var array $cat_varnames */
	protected $cat_varnames;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\acp\bb_item_versions_interface	$bb_item_version	BB item version ACP controller
	* @param \vinabb\web\controllers\cache\service_interface		$cache				Cache service
	* @param \phpbb\config\config									$config				Config object
	* @param ContainerInterface										$container			Container object
	* @param \phpbb\language\language								$language			Language object
	* @param \phpbb\log\log											$log				Log object
	* @param \vinabb\web\operators\bb_item_interface				$operator			BB item operators
	* @param \phpbb\request\request									$request			Request object
	* @param \phpbb\template\template								$template			Template object
	* @param \phpbb\user											$user				User object
	* @param \vinabb\web\controllers\helper_interface				$ext_helper			Extension helper
	* @param string													$root_path			phpBB root path
	* @param string													$admin_path			ACP root path
	* @param string													$php_ext			PHP file extension
	*/
	public function __construct(
		\vinabb\web\controllers\acp\bb_item_versions_interface $bb_item_version,
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		ContainerInterface $container,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\vinabb\web\operators\bb_item_interface $operator,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\vinabb\web\controllers\helper_interface $ext_helper,
		$root_path,
		$admin_path,
		$php_ext
	)
	{
		$this->bb_item_version = $bb_item_version;
		$this->cache = $cache;
		$this->config = $config;
		$this->container = $container;
		$this->language = $language;
		$this->log = $log;
		$this->operator = $operator;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->ext_helper = $ext_helper;
		$this->root_path = $root_path;
		$this->admin_path = $admin_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Set form data
	*
	* @param array $data Form data
	*/
	public function set_form_data($data)
	{
		$this->mode = $data['mode'];
		$this->u_action = $data['u_action'];
		$this->bb_type = $data['bb_type'];
		$this->lang_key = strtoupper($data['mode']);
		$this->cat_data = $this->cache->get_bb_cats($data['bb_type']);
	}

	/**
	* Display items
	*/
	public function display_items()
	{
		// Grab all from database
		$entities = $this->operator->get_items($this->bb_type);

		/** @var \vinabb\web\entities\bb_item_interface $entity */
		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('items', [
				'CATEGORY'	=> $this->cat_data[$this->get_cat_varname_by_id($entity->get_cat_id())]['name'],
				'NAME'		=> $entity->get_name(),
				'VARNAME'	=> $entity->get_varname(),
				'PRICE'		=> $entity->get_price(),
				'URL'		=> $entity->get_url(),
				'GITHUB'	=> $entity->get_github(),
				'ENABLE'	=> $entity->get_enable(),
				'ADDED'		=> $this->user->format_date($entity->get_added()),

				'U_VERSION'	=> append_sid("index.{$this->php_ext}", "i=-vinabb-web-acp-bb_item_versions_module&mode={$this->mode}&id={$entity->get_id()}"),
				'U_EDIT'	=> "{$this->u_action}&action=edit&id={$entity->get_id()}",
				'U_DELETE'	=> "{$this->u_action}&action=delete&id={$entity->get_id()}"
			]);
		}

		$this->template->assign_vars([
			'PAGE_TITLE_EXPLAIN'	=> $this->language->lang('ACP_BB_' . $this->lang_key . 'S_EXPLAIN'),
			'ADD_ITEM_LANG'			=> $this->language->lang('ADD_BB_' . $this->lang_key),
			'ITEM_NAME_LANG'		=> $this->language->lang($this->lang_key . '_NAME'),

			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Get the category varname from cat ID
	*
	* @param int $cat_id Category ID
	* @return string
	*/
	protected function get_cat_varname_by_id($cat_id)
	{
		if (!isset($this->cat_varnames))
		{
			foreach ($this->cat_data as $varname => $cat_data)
			{
				$this->cat_varnames[$cat_data['id']] = $varname;
			}
		}

		return isset($this->cat_varnames[$cat_id]) ? $this->cat_varnames[$cat_id] : '';
	}

	/**
	* Add an item
	*/
	public function add_item()
	{
		// Initiate an entity
		/** @var \vinabb\web\entities\bb_item_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.bb_item');

		// Process the new entity
		$this->add_edit_data($entity);

		// Build the category selection
		$this->build_cat_options($entity, $this->data['cat_id'], 'add');

		// Build the author selection
		$this->build_author_options($entity, $this->data['author_id'], 'add');

		$this->template->assign_vars([
			'S_ADD'		=> true,
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Edit an item
	*
	* @param int $item_id Item ID
	*/
	public function edit_item($item_id)
	{
		// Initiate and load the entity
		/** @var \vinabb\web\entities\bb_item_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.bb_item')->load($item_id);

		// Process the edited entity
		$this->add_edit_data($entity);

		// Build the category selection
		$this->build_cat_options($entity);

		// Build the author selection
		$this->build_author_options($entity);

		$this->template->assign_vars([
			'S_EDIT'	=> true,
			'U_ACTION'	=> "{$this->u_action}&action=edit&id={$item_id}"
		]);
	}

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\bb_item_interface $entity BB item entity
	*/
	public function add_edit_data(\vinabb\web\entities\bb_item_interface $entity)
	{
		$submit = $this->request->is_set_post('submit');

		// Load posting language file for the BBCode editor
		$this->language->add_lang('posting');

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_bb_items');

		// Get form data
		$this->request_data();

		// Set the parse options to the entity
		$this->set_bbcode_options($entity, $submit);

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_bb_items'))
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
			'MODE'					=> $this->mode,
			'ERRORS'				=> sizeof($this->errors) ? implode('<br>', $this->errors) : '',
			'PAGE_TITLE_EXPLAIN'	=> $this->language->lang('ACP_BB_' . $this->lang_key . 'S_EXPLAIN'),
			'ITEM_DETAILS_LANG'		=> $this->language->lang($this->lang_key . '_DETAILS'),
			'ITEM_NAME_LANG'		=> $this->language->lang($this->lang_key . '_NAME'),
			'ITEM_VARNAME_LANG'		=> $this->language->lang($this->lang_key . '_VARNAME'),
			'AUTHOR_LANG'			=> $this->language->lang(($this->bb_type == constants::BB_TYPE_STYLE || $this->bb_type == constants::BB_TYPE_ACP_STYLE) ? 'DESIGNER' : 'DEVELOPER'),

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
			'cat_id'					=> $this->request->variable('cat_id', 0),
			'author_id'					=> $this->request->variable('author_id', 0),
			'item_name'					=> $this->request->variable('item_name', '', true),
			'item_varname'				=> $this->request->variable('item_varname', ''),
			'item_desc'					=> $this->request->variable('item_desc', '', true),
			'desc_bbcode'				=> $this->request->variable('desc_bbcode', true),
			'desc_urls'					=> $this->request->variable('desc_urls', true),
			'desc_smilies'				=> $this->request->variable('desc_smilies', true),
			'item_desc_vi'				=> $this->request->variable('item_desc_vi', '', true),
			'desc_vi_bbcode'			=> $this->request->variable('desc_vi_bbcode', true),
			'desc_vi_urls'				=> $this->request->variable('desc_vi_urls', true),
			'desc_vi_smilies'			=> $this->request->variable('desc_vi_smilies', true),
			'item_ext_style'			=> $this->request->variable('item_ext_style', false),
			'item_ext_acp_style'		=> $this->request->variable('item_ext_acp_style', false),
			'item_ext_lang'				=> $this->request->variable('item_ext_lang', false),
			'item_ext_db_schema'		=> $this->request->variable('item_ext_db_schema', false),
			'item_ext_db_data'			=> $this->request->variable('item_ext_db_data', false),
			'item_style_presets'		=> $this->request->variable('item_style_presets', 0),
			'item_style_presets_aio'	=> $this->request->variable('item_style_presets_aio', false),
			'item_style_source'			=> $this->request->variable('item_style_source', false),
			'item_style_responsive'		=> $this->request->variable('item_style_responsive', false),
			'item_style_bootstrap'		=> $this->request->variable('item_style_bootstrap', false),
			'item_lang_iso'				=> $this->request->variable('item_lang_iso', ''),
			'item_tool_os'				=> $this->request->variable('item_tool_os', 0),
			'item_price'				=> $this->request->variable('item_price', 0),
			'item_url'					=> $this->request->variable('item_url', ''),
			'item_github'				=> $this->request->variable('item_github', ''),
			'item_enable'				=> $this->request->variable('item_enable', true)
		];
	}

	/**
	* Grab the form data's parsing options and set them to the entity
	*
	* If submit, use data from the form
	* In edit mode, use data stored in the entity
	* In add mode, use default values
	*
	* @param \vinabb\web\entities\bb_item_interface $entity BB item entity
	*/
	protected function set_bbcode_options(\vinabb\web\entities\bb_item_interface $entity, $submit)
	{
		$entity->desc_enable_bbcode($submit ? $this->request->is_set_post('desc_bbcode') : ($entity->get_id() ? $entity->desc_bbcode_enabled() : true));
		$entity->desc_enable_urls($submit ? $this->request->is_set_post('desc_urls') : ($entity->get_id() ? $entity->desc_urls_enabled() : true));
		$entity->desc_enable_smilies($submit ? $this->request->is_set_post('desc_smilies') : ($entity->get_id() ? $entity->desc_smilies_enabled() : true));
		$entity->desc_vi_enable_bbcode($submit ? $this->request->is_set_post('desc_vi_bbcode') : ($entity->get_id() ? $entity->desc_vi_bbcode_enabled() : true));
		$entity->desc_vi_enable_urls($submit ? $this->request->is_set_post('desc_vi_urls') : ($entity->get_id() ? $entity->desc_vi_urls_enabled() : true));
		$entity->desc_vi_enable_smilies($submit ? $this->request->is_set_post('desc_vi_smilies') : ($entity->get_id() ? $entity->desc_vi_smilies_enabled() : true));
	}

	/**
	* Map the form data fields to setters and set them to the entity
	*
	* @param \vinabb\web\entities\bb_item_interface $entity BB item entity
	*/
	protected function map_set_data(\vinabb\web\entities\bb_item_interface $entity)
	{
		$map_fields = [
			'set_bb_type'	=> $this->bb_type,
			'set_cat_id'	=> $this->data['cat_id'],
			'set_author_id'	=> $this->data['author_id'],
			'set_name'		=> $this->data['item_name'],
			'set_varname'	=> $this->data['item_varname'],
			'set_desc'		=> $this->data['item_desc'],
			'set_desc_vi'	=> $this->data['item_desc_vi'],
			'set_price'		=> $this->data['item_price'],
			'set_url'		=> $this->data['item_url'],
			'set_github'	=> $this->data['item_github'],
			'set_enable'	=> $this->data['item_enable'],
			'set_added'		=> null
		];

		// Add extra fields based on the item type
		$map_fields = array_merge($map_fields, $this->get_extra_map_fields($this->mode));

		// Do not change the adding time
		if ($entity->get_id())
		{
			unset($map_fields['set_added']);
		}

		// Set the mapped data in the entity
		foreach ($map_fields as $entity_function => $item_data)
		{
			try
			{
				// Calling the $entity_function on the entity and passing it $item_data
				$entity->$entity_function($item_data);
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
	* Get extra set methods for item properties
	*
	* @param string $bb_mode phpBB resource type mode
	* @return array
	*/
	protected function get_extra_map_fields($bb_mode)
	{
		$bb_mode = ($bb_mode == 'acp_style') ? 'style' : $bb_mode;

		$data = [
			'ext'	=> [
				'set_ext_style'		=> $this->data['item_ext_style'],
				'set_ext_acp_style'	=> $this->data['item_ext_acp_style'],
				'set_ext_lang'		=> $this->data['item_ext_lang'],
				'set_ext_db_schema'	=> $this->data['item_ext_db_schema'],
				'set_ext_db_data'	=> $this->data['item_ext_db_data']
			],
			'style'	=> [
				'set_style_presets'		=> $this->data['item_style_presets'],
				'set_style_presets_aio'	=> $this->data['item_style_presets_aio'],
				'set_style_source'		=> $this->data['item_style_source'],
				'set_style_responsive'	=> $this->data['item_style_responsive'],
				'set_style_bootstrap'	=> $this->data['item_style_bootstrap']
			],
			'lang'	=> ['set_lang_iso'	=> $this->data['item_lang_iso']],
			'tool'	=> ['set_tool_os'	=> $this->data['item_tool_os']]
		];

		return isset($data[$bb_mode]) ? $data[$bb_mode] : [];
	}

	/**
	* Insert or update data, then log actions and clear cache if needed
	*
	* @param \vinabb\web\entities\bb_item_interface $entity BB item entity
	*/
	protected function save_data(\vinabb\web\entities\bb_item_interface $entity)
	{
		if ($entity->get_id())
		{
			// Save the edited entity to the database
			$entity->save();

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, "LOG_BB_{$this->lang_key}_EDIT", time(), [$entity->get_name()]);

			$message = "MESSAGE_BB_{$this->lang_key}_EDIT";
		}
		else
		{
			// Add the new entity to the database
			$entity = $this->operator->add_item($entity, $this->bb_type);

			$this->config->increment('vinabb_web_total_bb_' . $this->mode . 's', 1, false);
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, "LOG_BB_{$this->lang_key}_ADD", time(), [$entity->get_name()]);

			$message = "MESSAGE_BB_{$this->lang_key}_ADD";
		}

		$this->cache->clear_new_bb_items($this->bb_type);

		trigger_error($this->language->lang($message) . adm_back_link($this->u_action));
	}

	/**
	* Output entity data to template variables
	*
	* @param \vinabb\web\entities\bb_item_interface $entity BB item entity
	*/
	protected function data_to_tpl(\vinabb\web\entities\bb_item_interface $entity)
	{
		$this->template->assign_vars([
			'ITEM_NAME'					=> $entity->get_name(),
			'ITEM_VARNAME'				=> $entity->get_varname(),
			'ITEM_DESC'					=> $entity->get_desc_for_edit(),
			'ITEM_DESC_BBCODE'			=> $entity->desc_bbcode_enabled(),
			'ITEM_DESC_URLS'			=> $entity->desc_urls_enabled(),
			'ITEM_DESC_SMILIES'			=> $entity->desc_smilies_enabled(),
			'ITEM_DESC_VI'				=> $entity->get_desc_vi_for_edit(),
			'ITEM_DESC_VI_BBCODE'		=> $entity->desc_vi_bbcode_enabled(),
			'ITEM_DESC_VI_URLS'			=> $entity->desc_vi_urls_enabled(),
			'ITEM_DESC_VI_SMILIES'		=> $entity->desc_vi_smilies_enabled(),
			'ITEM_EXT_STYLE'			=> $entity->get_ext_style(),
			'ITEM_EXT_ACP_STYLE'		=> $entity->get_ext_acp_style(),
			'ITEM_EXT_LANG'				=> $entity->get_ext_lang(),
			'ITEM_EXT_DB_SCHEMA'		=> $entity->get_ext_db_schema(),
			'ITEM_EXT_DB_DATA'			=> $entity->get_ext_db_data(),
			'ITEM_STYLE_PRESETS'		=> $entity->get_style_presets(),
			'ITEM_STYLE_PRESETS_AIO'	=> $entity->get_style_presets_aio(),
			'ITEM_STYLE_SOURCE'			=> $entity->get_style_source(),
			'ITEM_STYLE_RESPONSIVE'		=> $entity->get_style_responsive(),
			'ITEM_STYLE_BOOTSTRAP'		=> $entity->get_style_bootstrap(),
			'ITEM_LANG_ISO'				=> $entity->get_lang_iso(),
			'ITEM_TOOL_OS'				=> $entity->get_tool_os(),
			'ITEM_PRICE'				=> $entity->get_price(),
			'ITEM_URL'					=> $entity->get_url(),
			'ITEM_GITHUB'				=> $entity->get_github(),
			'ITEM_ENABLE'				=> $entity->get_enable(),

			'LANG_OPTIONS'	=> ($this->bb_type == constants::BB_TYPE_LANG) ? $this->ext_helper->build_lang_list($entity->get_lang_iso()) : '',
			'OS_OPTIONS'	=> ($this->bb_type == constants::BB_TYPE_TOOL) ? $this->ext_helper->build_os_list($entity->get_tool_os()) : '',

			// These template variables used for the BBCode editor
			'S_BBCODE_ALLOWED'	=> true,
			'S_SMILIES_ALLOWED'	=> true,
			'S_BBCODE_IMG'		=> true,
			'S_BBCODE_FLASH'	=> true,
			'S_LINKS_ALLOWED'	=> true
		]);
	}

	/**
	* Delete an item
	*
	* @param int $item_id Item ID
	*/
	public function delete_item($item_id)
	{
		/** @var \vinabb\web\entities\bb_item_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.bb_item')->load($item_id);

		try
		{
			$this->operator->delete_item($item_id);

			// Delete all versions from this item
			$this->delete_item_versions($item_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang("ERROR_{$this->lang_key}_DELETE", $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->config->increment('vinabb_web_total_bb_' . $this->mode . 's', -1, false);
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, "LOG_BB_{$this->lang_key}_DELETE", time(), [$entity->get_name()]);
		$this->cache->clear_new_bb_items($this->bb_type);

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send([
				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang("MESSAGE_BB_{$this->lang_key}_DELETE"),
				'REFRESH_DATA'	=> ['time'	=> 3]
			]);
		}
	}

	/**
	* Delete all versions from an item
	*
	* @param int $item_id Item ID
	*/
	protected function delete_item_versions($item_id)
	{
		$versions = $this->container->get('vinabb.web.operators.bb_item_version')->get_versions($item_id);

		/** @var \vinabb\web\entities\bb_item_version_interface $version */
		foreach ($versions as $version)
		{
			$this->bb_item_version->delete_version($version->get_id(), $version->get_phpbb_branch());
		}
	}

	/**
	* Generate options of available categories
	*
	* @param \vinabb\web\entities\bb_item_interface	$entity 	BB item entity
	* @param int									$current_id	Selected category ID
	* @param string									$mode		Add or edit mode?
	*/
	protected function build_cat_options(\vinabb\web\entities\bb_item_interface $entity, $current_id = 0, $mode = 'edit')
	{
		$options = $this->container->get('vinabb.web.operators.bb_category')->get_cats($this->bb_type);
		$current_id = ($mode == 'edit') ? $entity->get_cat_id() : $current_id;

		/** @var \vinabb\web\entities\bb_category_interface $option */
		foreach ($options as $option)
		{
			$this->template->assign_block_vars('cat_options', [
				'ID'		=> $option->get_id(),
				'NAME'		=> $option->get_name(),
				'NAME_VI'	=> $option->get_name_vi(),

				'S_SELECTED'	=> $option->get_id() == $current_id
			]);
		}
	}

	/**
	* Generate options of available categories
	*
	* @param \vinabb\web\entities\bb_item_interface	$entity 	BB item entity
	* @param int									$current_id	Selected author ID
	* @param string									$mode		Add or edit mode?
	*/
	protected function build_author_options(\vinabb\web\entities\bb_item_interface $entity, $current_id = 0, $mode = 'edit')
	{
		$options = $this->container->get('vinabb.web.operators.bb_author')->get_authors();
		$current_id = ($mode == 'edit') ? $entity->get_author_id() : $current_id;

		/** @var \vinabb\web\entities\bb_author_interface $option */
		foreach ($options as $option)
		{
			$this->template->assign_block_vars('author_options', [
				'ID'		=> $option->get_id(),
				'NAME'		=> $option->get_name(),
				'FIRSTNAME'	=> $option->get_firstname(),
				'LASTNAME'	=> $option->get_lastname(),

				'S_SELECTED'	=> $option->get_id() == $current_id
			]);
		}
	}
}
