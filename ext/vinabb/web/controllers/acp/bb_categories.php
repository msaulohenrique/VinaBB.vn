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
* Controller for the bb_categories_module
*/
class bb_categories
{
	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \vinabb\web\operators\bb_item_interface $item_operator */
	protected $item_operator;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\log\log $log */
	protected $log;

	/** @var \vinabb\web\operators\bb_category_interface $operator */
	protected $operator;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \vinabb\web\controllers\helper_interface $ext_helper */
	protected $ext_helper;

	/** @var string $u_action */
	protected $u_action;

	/** @var array $data */
	protected $data;

	/** @var array $errors */
	protected $errors;

	/** @var int $bb_type */
	protected $bb_type;

	/** @var string $lang_key */
	protected $lang_key;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface	$cache			Cache service
	* @param ContainerInterface									$container		Container object
	* @param \vinabb\web\operators\bb_item_interface			$item_operator	BB item operators
	* @param \phpbb\language\language							$language		Language object
	* @param \phpbb\log\log										$log			Log object
	* @param \vinabb\web\operators\bb_category_interface		$operator		BB category operators
	* @param \phpbb\request\request								$request		Request object
	* @param \phpbb\template\template							$template		Template object
	* @param \phpbb\user										$user			User object
	* @param \vinabb\web\controllers\helper_interface			$ext_helper		Extension helper
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		ContainerInterface $container,
		\vinabb\web\operators\bb_item_interface $item_operator,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\vinabb\web\operators\bb_category_interface $operator,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\vinabb\web\controllers\helper_interface $ext_helper
	)
	{
		$this->cache = $cache;
		$this->container = $container;
		$this->item_operator = $item_operator;
		$this->language = $language;
		$this->log = $log;
		$this->operator = $operator;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->ext_helper = $ext_helper;
	}

	/**
	* Set form data
	*
	* @param array $data Form data
	*/
	public function set_form_data($data)
	{
		$this->u_action = $data['u_action'];
		$this->bb_type = $data['bb_type'];
		$this->lang_key = strtoupper($data['mode']);
	}

	/**
	* Display categories
	*/
	public function display_cats()
	{
		// Item counter
		$item_count = $this->item_operator->get_count_data_by_cat($this->bb_type);

		// Grab all from database
		$entities = $this->operator->get_cats($this->bb_type);

		/** @var \vinabb\web\entities\bb_category_interface $entity */
		foreach ($entities as $entity)
		{
			$items = isset($item_count[$entity->get_id()]) ? $item_count[$entity->get_id()] : 0;

			$this->template->assign_block_vars('cats', [
				'NAME'		=> $entity->get_name(),
				'NAME_VI'	=> $entity->get_name_vi(),
				'VARNAME'	=> $entity->get_varname(),
				'DESC'		=> $entity->get_desc(),
				'DESC_VI'	=> $entity->get_desc_vi(),
				'ICON'		=> $entity->get_icon(),
				'ITEMS'		=> $items,

				'U_EDIT'		=> "{$this->u_action}&action=edit&id={$entity->get_id()}",
				'U_MOVE_DOWN'	=> "{$this->u_action}&action=move_down&id={$entity->get_id()}&hash=" . generate_link_hash('down' . $entity->get_id()),
				'U_MOVE_UP'		=> "{$this->u_action}&action=move_up&id={$entity->get_id()}&hash=" . generate_link_hash('up' . $entity->get_id()),
				'U_DELETE'		=> $items ? '' : "{$this->u_action}&action=delete&id={$entity->get_id()}"
			]);
		}

		$this->template->assign_vars([
			'PAGE_TITLE_EXPLAIN'	=> $this->language->lang('ACP_BB_' . $this->lang_key . '_CATS'),
			'TOTAL_ITEMS_LANG'		=> $this->language->lang('TOTAL_' . $this->lang_key . 'S'),

			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Add a category
	*/
	public function add_cat()
	{
		// Initiate an entity
		/** @var \vinabb\web\entities\bb_category_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.bb_category');

		// Process the new entity
		$this->add_edit_data($entity);

		$this->template->assign_vars([
			'S_ADD'		=> true,
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Edit a category
	*
	* @param int $cat_id Category ID
	*/
	public function edit_cat($cat_id)
	{
		// Initiate and load the entity
		/** @var \vinabb\web\entities\bb_category_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.bb_category')->load($cat_id);

		// Process the edited entity
		$this->add_edit_data($entity);

		$this->template->assign_vars([
			'S_EDIT'	=> true,
			'U_ACTION'	=> "{$this->u_action}&action=edit&id={$cat_id}"
		]);
	}

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\bb_category_interface $entity BB category entity
	*/
	public function add_edit_data(\vinabb\web\entities\bb_category_interface $entity)
	{
		$submit = $this->request->is_set_post('submit');

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_bb_categories');

		// Get form data
		$this->request_data();

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_bb_categories'))
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
			'ERRORS'				=> sizeof($this->errors) ? implode('<br>', $this->errors) : '',
			'PAGE_TITLE_EXPLAIN'	=> $this->language->lang('ACP_BB_' . $this->lang_key . '_CATS'),

			'U_BACK'	=> $this->u_action
		]);
	}

	/**
	* Request data from the form
	*/
	protected function request_data()
	{
		$this->data = [
			'cat_name'		=> $this->request->variable('cat_name', '', true),
			'cat_name_vi'	=> $this->request->variable('cat_name_vi', '', true),
			'cat_varname'	=> $this->request->variable('cat_varname', ''),
			'cat_desc'		=> $this->request->variable('cat_desc', '', true),
			'cat_desc_vi'	=> $this->request->variable('cat_desc_vi', '', true),
			'cat_icon'		=> $this->request->variable('cat_icon', '')
		];
	}

	/**
	* Map the form data fields to setters and set them to the entity
	*
	* @param \vinabb\web\entities\bb_category_interface $entity BB category entity
	*/
	protected function map_set_data(\vinabb\web\entities\bb_category_interface $entity)
	{
		// Map the form data fields to setters
		$map_fields = [
			'set_name'		=> $this->data['cat_name'],
			'set_name_vi'	=> $this->data['cat_name_vi'],
			'set_varname'	=> $this->data['cat_varname'],
			'set_desc'		=> $this->data['cat_desc'],
			'set_desc_vi'	=> $this->data['cat_desc_vi'],
			'set_icon'		=> $this->data['cat_icon']
		];

		// Set the mapped data in the entity
		foreach ($map_fields as $entity_function => $cat_data)
		{
			try
			{
				// Calling the $entity_function on the entity and passing it $cat_data
				$entity->$entity_function($cat_data);
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
	* @param \vinabb\web\entities\bb_category_interface $entity BB category entity
	*/
	protected function save_data(\vinabb\web\entities\bb_category_interface $entity)
	{
		if ($entity->get_id())
		{
			// Save the edited entity to the database
			$entity->save();

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BB_CAT_EDIT', time(), [$entity->get_name()]);

			$message = 'MESSAGE_CAT_EDIT';
		}
		else
		{
			// Add the new entity to the database
			$entity = $this->operator->add_cat($entity, $this->bb_type);

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BB_CAT_ADD', time(), [$entity->get_name()]);

			$message = 'MESSAGE_CAT_ADD';
		}

		$this->cache->clear_bb_cats($this->bb_type);

		trigger_error($this->language->lang($message) . adm_back_link($this->u_action));
	}

	/**
	* Output entity data to template variables
	*
	* @param \vinabb\web\entities\bb_category_interface $entity BB category entity
	*/
	protected function data_to_tpl(\vinabb\web\entities\bb_category_interface $entity)
	{
		$this->template->assign_vars([
			'CAT_NAME'		=> $entity->get_name(),
			'CAT_NAME_VI'	=> $entity->get_name_vi(),
			'CAT_VARNAME'	=> $entity->get_varname(),
			'CAT_DESC'		=> $entity->get_desc(),
			'CAT_DESC_VI'	=> $entity->get_desc_vi(),

			'ICON_OPTIONS'	=> $this->ext_helper->build_icon_list($entity->get_icon())
		]);
	}

	/**
	* Move a category up/down
	*
	* @param int	$cat_id		Category ID
	* @param string	$direction	The direction (up|down)
	*/
	public function move_cat($cat_id, $direction)
	{
		// Check the valid link hash
		if (!check_link_hash($this->request->variable('hash', ''), $direction . $cat_id))
		{
			trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		try
		{
			$this->operator->move_cat($this->bb_type, $cat_id, $direction);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_CAT_MOVE', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->cache->clear_bb_cats($this->bb_type);

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(['success' => true]);
		}
	}

	/**
	* Delete a category
	*
	* @param int $cat_id Category ID
	*/
	public function delete_cat($cat_id)
	{
		// Do not delete if the category has assigned items
		if ($this->item_operator->count_items($this->bb_type, $cat_id) > 0)
		{
			trigger_error($this->language->lang('ERROR_CAT_DELETE_IN_USE') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		/** @var \vinabb\web\entities\bb_category_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.bb_category')->load($cat_id);

		try
		{
			$this->operator->delete_cat($cat_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_CAT_DELETE', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BB_CAT_DELETE', time(), [$entity->get_name()]);
		$this->cache->clear_bb_cats($this->bb_type);

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send([
				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang('MESSAGE_CAT_DELETE'),
				'REFRESH_DATA'	=> ['time'	=> 3]
			]);
		}
	}
}
