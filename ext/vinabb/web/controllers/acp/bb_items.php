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
* Controller for the bb_items_module
*/
class bb_items implements bb_items_interface
{
	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \vinabb\web\operators\bb_item_interface */
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

	/** @var int */
	protected $bb_type;

	/** @var string */
	protected $lang_key;

	/** @var array */
	protected $cat_data = [];

	/**
	* Constructor
	*
	* @param \phpbb\cache\service						$cache		Cache service
	* @param ContainerInterface							$container	Container object
	* @param \phpbb\language\language					$language	Language object
	* @param \phpbb\log\log								$log		Log object
	* @param \vinabb\web\operators\bb_item_interface	$operator	BB item operators
	* @param \phpbb\request\request						$request	Request object
	* @param \phpbb\template\template					$template	Template object
	* @param \phpbb\user								$user		User object
	* @param string										$root_path	phpBB root path
	* @param string										$php_ext	PHP file extension
	*/
	public function __construct(
		\phpbb\cache\service $cache,
		ContainerInterface $container,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\vinabb\web\operators\bb_item_interface $operator,
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
	* Set phpBB resource types
	*
	* @param int	$bb_type	phpBB resource type
	* @param string	$mode		Module mode
	*/
	public function set_bb_type($bb_type, $mode)
	{
		$this->bb_type = $bb_type;
		$this->lang_key = strtoupper($mode);
		$this->cat_data = $this->cache->get_bb_cats($bb_type);
	}

	/**
	* Display items
	*/
	public function display_items()
	{
		// Grab all from database
		$entities = $this->operator->get_items($this->bb_type);

		/* @var \vinabb\web\entities\bb_item_interface $entity */
		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('items', [
				'CATEGORY'	=> $this->cat_data[$entity->get_cat_id()]['name'],
				'NAME'		=> $entity->get_name(),
				'VARNAME'	=> $entity->get_varname(),
				'PRICE'		=> $entity->get_price(),
				'URL'		=> $entity->get_url(),
				'GITHUB'	=> $entity->get_github(),
				'ENABLE'	=> $entity->get_enable(),
				'ADDED'		=> $this->user->format_date($entity->get_added()),
				'UPDATED'	=> $this->user->format_date($entity->get_updated()),

				'U_EDIT'	=> "{$this->u_action}&action=edit&id={$entity->get_id()}",
				'U_DELETE'	=> "{$this->u_action}&action=delete&id={$entity->get_id()}"
			]);
		}

		$this->template->assign_vars([
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Add an item
	*/
	public function add_item()
	{
		// Initiate an entity
		/* @var \vinabb\web\entities\bb_item_interface */
		$entity = $this->container->get('vinabb.web.entities.bb_item');

		// Process the new entity
		$this->add_edit_data($entity);

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
		/* @var \vinabb\web\entities\bb_item_interface */
		$entity = $this->container->get('vinabb.web.entities.bb_item')->load($item_id);

		// Process the edited entity
		$this->add_edit_data($entity);

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
	public function add_edit_data($entity)
	{
		$submit = $this->request->is_set_post('submit');
		$errors = [];

		// Load posting language file for the BBCode editor
		$this->language->add_lang('posting');

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_bb_items');

		// Get form data
		$data = [
			'cat_id'			=> $this->request->variable('cat_id', 0),
			'author_id'			=> $this->request->variable('author_id', 0),
			'item_name'			=> $this->request->variable('item_name', '', true),
			'item_varname'		=> $this->request->variable('item_varname', ''),
			'item_desc'			=> $this->request->variable('item_desc', '', true),
			'desc_bbcode'		=> $this->request->variable('desc_bbcode', true),
			'desc_urls'			=> $this->request->variable('desc_urls', true),
			'desc_smilies'		=> $this->request->variable('desc_smilies', true),
			'item_desc_vi'		=> $this->request->variable('item_desc_vi', '', true),
			'desc_vi_bbcode'	=> $this->request->variable('desc_vi_bbcode', true),
			'desc_vi_urls'		=> $this->request->variable('desc_vi_urls', true),
			'desc_vi_smilies'	=> $this->request->variable('desc_vi_smilies', true),
			'item_price'		=> $this->request->variable('item_price', 0),
			'item_url'			=> $this->request->variable('item_url', ''),
			'item_github'		=> $this->request->variable('item_github', ''),
			'item_enable'		=> $this->request->variable('item_enable', true)
		];

		/**
		* Grab the form data's parsing options
		*
		*	If submit, use data from the form
		*	In edit mode, use data stored in the entity
		*	In add mode, use default values
		*/
		$desc_options = [
			'bbcode'	=> $submit ? $data['desc_bbcode'] : ($entity->get_id() ? $entity->desc_bbcode_enabled() : true),
			'urls'		=> $submit ? $data['desc_urls'] : ($entity->get_id() ? $entity->desc_urls_enabled() : true),
			'smilies'	=> $submit ? $data['desc_smilies'] : ($entity->get_id() ? $entity->desc_smilies_enabled() : true)
		];

		$desc_vi_options = [
			'bbcode'	=> $submit ? $data['desc_vi_bbcode'] : ($entity->get_id() ? $entity->desc_vi_bbcode_enabled() : true),
			'urls'		=> $submit ? $data['desc_vi_urls'] : ($entity->get_id() ? $entity->desc_vi_urls_enabled() : true),
			'smilies'	=> $submit ? $data['desc_vi_smilies'] : ($entity->get_id() ? $entity->desc_vi_smilies_enabled() : true)
		];

		// Set the parse options in the entity
		foreach ($desc_options as $function => $enabled)
		{
			$entity->{($enabled ? 'desc_enable_' : 'desc_disable_') . $function}();
		}

		foreach ($desc_vi_options as $function => $enabled)
		{
			$entity->{($enabled ? 'desc_vi_enable_' : 'desc_vi_disable_') . $function}();
		}

		unset($desc_options);
		unset($desc_vi_options);

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_bb_items'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			// Map the form data fields to setters
			$map_fields = [
				'set_cat_id'	=> $data['cat_id'],
				'set_author_id'	=> $data['page_name_vi'],
				'set_name'		=> $data['item_name'],
				'set_varname'	=> $data['item_varname'],
				'set_desc'		=> $data['item_desc'],
				'set_desc_vi'	=> $data['item_desc_vi'],
				'set_price'		=> $data['item_price'],
				'set_url'		=> $data['item_url'],
				'set_github'	=> $data['item_github'],
				'set_enable'	=> $data['item_enable'],
				'set_added'		=> null,
				'set_updated'	=> null
			];

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
					$errors[] = $e->get_friendly_message($this->language);
				}
			}

			unset($map_fields);

			// Insert or update
			if (!sizeof($errors))
			{
				if ($entity->get_id())
				{
					// Save the edited entity to the database
					$entity->save();

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, "LOG_{$this->lang_key}_EDIT", time(), [$entity->get_name()]);

					$message = "MESSAGE_{$this->lang_key}_EDIT";
				}
				else
				{
					// Add the new entity to the database
					$entity = $this->operator->add_item($entity, $this->bb_type);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, "LOG_{$this->lang_key}_ADD", time(), [$entity->get_name()]);

					$message = "MESSAGE_{$this->lang_key}_ADD";
				}

				$this->cache->clear_new_bb_items($this->bb_type);

				trigger_error($this->language->lang($message) . adm_back_link($this->u_action));
			}
		}

		$this->template->assign_vars([
			'ERRORS'	=> sizeof($errors) ? implode('<br>', $errors) : '',

			'ITEM_NAME'		=> $entity->get_name(),
			'ITEM_VARNAME'	=> $entity->get_varname(),
			'ITEM_DESC'		=> $entity->get_desc_for_edit(),
			'ITEM_DESC_VI'	=> $entity->get_desc_vi_for_edit(),
			'ITEM_PRICE'	=> $entity->get_price(),
			'ITEM_URL'		=> $entity->get_url(),
			'ITEM_GITHUB'	=> $entity->get_github(),
			'ITEM_ENABLE'	=> $entity->get_enable(),

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
	* Delete an item
	*
	* @param int $item_id Item ID
	*/
	public function delete_item($item_id)
	{
		/* @var \vinabb\web\entities\bb_item_interface */
		$entity = $this->container->get('vinabb.web.entities.bb_item')->load($item_id);

		try
		{
			$this->operator->delete_item($item_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang("ERROR_{$this->lang_key}_DELETE", $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, "LOG_{$this->lang_key}_DELETE", time(), [$entity->get_name()]);

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send([
				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang("MESSAGE_{$this->lang_key}_DELETE"),
				'REFRESH_DATA'	=> ['time'	=> 3]
			]);
		}
	}
}
