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
* Controller for the menus_module
*/
class menus implements menus_interface
{
	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\log\log $log */
	protected $log;

	/** @var \vinabb\web\operators\menu_interface $operator */
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

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface	$cache		Cache service
	* @param ContainerInterface									$container	Container object
	* @param \phpbb\language\language							$language	Language object
	* @param \phpbb\log\log										$log		Log object
	* @param \vinabb\web\operators\menu_interface				$operator	Menu operators
	* @param \phpbb\request\request								$request	Request object
	* @param \phpbb\template\template							$template	Template object
	* @param \phpbb\user										$user		User object
	* @param \vinabb\web\controllers\helper_interface			$ext_helper	Extension helper
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		ContainerInterface $container,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\vinabb\web\operators\menu_interface $operator,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\vinabb\web\controllers\helper_interface $ext_helper
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
		$this->ext_helper = $ext_helper;
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
	* Display menus
	*
	* @param int $parent_id Parent ID
	*/
	public function display_menus($parent_id = 0)
	{
		// Grab all from database
		$entities = $this->operator->get_menus($parent_id);

		// Initialize a variable to hold the right_id value
		$last_right_id = 0;

		/** @var \vinabb\web\entities\menu_interface $entity */
		foreach ($entities as $entity)
		{
			// The current entity is a child of a previous entity, do not display it
			if ($entity->get_left_id() < $last_right_id)
			{
				continue;
			}

			$this->template->assign_block_vars('menus', [
				'URL'		=> "{$this->u_action}&parent_id={$entity->get_id()}",
				'NAME'		=> $entity->get_name(),
				'NAME_VI'	=> $entity->get_name_vi(),
				'TYPE'		=> $entity->get_type(),
				'ICON'		=> $entity->get_icon(),
				'DATA'		=> $entity->get_data(),
				'TARGET'	=> $entity->get_target(),

				'S_IS_CAT'	=> $entity->get_right_id() - $entity->get_left_id() > 1,

				'U_EDIT'		=> "{$this->u_action}&action=edit&id={$entity->get_id()}",
				'U_MOVE_DOWN'	=> "{$this->u_action}&action=move_down&id={$entity->get_id()}&hash=" . generate_link_hash('down' . $entity->get_id()),
				'U_MOVE_UP'		=> "{$this->u_action}&action=move_up&id={$entity->get_id()}&hash=" . generate_link_hash('up' . $entity->get_id()),
				'U_DELETE'		=> "{$this->u_action}&action=delete&id={$entity->get_id()}"
			]);

			// Store the current right_id value
			$last_right_id = $entity->get_right_id();
		}

		// Prepare rule breadcrumb path navigation
		$entities = $this->operator->get_parents($parent_id);

		// Process each entity for breadcrumb
		/** @var \vinabb\web\entities\menu_interface $entity */
		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('breadcrumb', [
				'NAME'	=> $entity->get_name(),
				'URL'	=> "{$this->u_action}&parent_id={$entity->get_id()}",

				'S_CURRENT'	=> $entity->get_id() == $parent_id
			]);
		}

		$this->template->assign_vars([
			'U_ACTION'	=> "{$this->u_action}&action=add&parent_id={$parent_id}",
			'U_ROOT'	=> "{$this->u_action}&parent_id=0"
		]);
	}

	/**
	* Add a menu
	*
	* @param int $parent_id Parent ID
	*/
	public function add_menu($parent_id = 0)
	{
		// Initiate an entity
		/** @var \vinabb\web\entities\menu_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.menu');

		// Process the new entity
		$this->add_edit_data($entity);

		// Build the parent selection
		$this->build_parent_options($entity, $parent_id, 'add');

		// Build the type selection
		$this->build_type_options($entity, $this->data['menu_type'], 'add');

		// Build the data selection
		$this->build_data_options($entity, (int) $this->data['menu_data'], 'add');

		$this->template->assign_vars([
			'S_ADD'	=> true,

			'U_ACTION'	=> "{$this->u_action}&action=add&parent_id={$parent_id}",
			'U_BACK'	=> "{$this->u_action}&parent_id={$parent_id}"
		]);
	}

	/**
	* Edit a menu
	*
	* @param int $menu_id Menu ID
	*/
	public function edit_menu($menu_id)
	{
		// Initiate and load the entity
		/** @var \vinabb\web\entities\menu_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.menu')->load($menu_id);

		// Process the edited entity
		$this->add_edit_data($entity);

		// Build the parent selection
		$this->build_parent_options($entity);

		// Build the type selection
		$this->build_type_options($entity);

		// Build the data selection
		$this->build_data_options($entity);

		$this->template->assign_vars([
			'S_EDIT'	=> true,
			'S_IS_CAT'	=> ($entity->get_right_id() - $entity->get_left_id()) > 1,

			'U_ACTION'	=> "{$this->u_action}&action=edit&id={$menu_id}",
			'U_BACK'	=> "{$this->u_action}&parent_id={$entity->get_parent_id()}"
		]);
	}

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\menu_interface $entity Menu entity
	*/
	public function add_edit_data(\vinabb\web\entities\menu_interface $entity)
	{
		$submit = $this->request->is_set_post('submit');

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_menus');

		// Get form data
		$this->request_data();

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_menus'))
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
			'ERRORS'	=> sizeof($this->errors) ? implode('<br>', $this->errors) : ''
		]);
	}

	/**
	* Request data from the form
	*/
	protected function request_data()
	{
		$this->data = [
			'menu_type'					=> $this->request->variable('menu_type', 0),
			'parent_id'					=> $this->request->variable('parent_id', 0),
			'menu_name'					=> $this->request->variable('menu_name', '', true),
			'menu_name_vi'				=> $this->request->variable('menu_name_vi', '', true),
			'menu_icon'					=> $this->request->variable('menu_icon', ''),
			'menu_data'					=> $this->request->variable('menu_data', ''),
			'menu_target'				=> $this->request->variable('menu_target', false),
			'menu_enable_guest'			=> $this->request->variable('menu_enable_guest', true),
			'menu_enable_bot'			=> $this->request->variable('menu_enable_bot', true),
			'menu_enable_new_user'		=> $this->request->variable('menu_enable_new_user', true),
			'menu_enable_user'			=> $this->request->variable('menu_enable_user', true),
			'menu_enable_mod'			=> $this->request->variable('menu_enable_mod', true),
			'menu_enable_global_mod'	=> $this->request->variable('menu_enable_global_mod', true),
			'menu_enable_admin'			=> $this->request->variable('menu_enable_admin', true),
			'menu_enable_founder'		=> $this->request->variable('menu_enable_founder', true)
		];
	}

	/**
	* Map the form data fields to setters and set them to the entity
	*
	* @param \vinabb\web\entities\menu_interface $entity Menu entity
	*/
	protected function map_set_data(\vinabb\web\entities\menu_interface $entity)
	{
		$map_fields = [
			'set_type'				=> $this->data['menu_type'],
			'set_parent_id'			=> $this->data['parent_id'],
			'set_name'				=> $this->data['menu_name'],
			'set_name_vi'			=> $this->data['menu_name_vi'],
			'set_icon'				=> $this->data['menu_icon'],
			'set_data'				=> $this->data['menu_data'],
			'set_target'			=> $this->data['menu_target'],
			'set_enable_guest'		=> $this->data['menu_enable_guest'],
			'set_enable_bot'		=> $this->data['menu_enable_bot'],
			'set_enable_new_user'	=> $this->data['menu_enable_new_user'],
			'set_enable_user'		=> $this->data['menu_enable_user'],
			'set_enable_mod'		=> $this->data['menu_enable_mod'],
			'set_enable_global_mod'	=> $this->data['menu_enable_global_mod'],
			'set_enable_admin'		=> $this->data['menu_enable_admin'],
			'set_enable_founder'	=> $this->data['menu_enable_founder']
		];

		// Set the mapped data in the entity
		foreach ($map_fields as $entity_function => $menu_data)
		{
			try
			{
				// Calling the $entity_function on the entity and passing it $menu_data
				$entity->$entity_function($menu_data);
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
	* @param \vinabb\web\entities\menu_interface $entity Menu entity
	*/
	protected function save_data(\vinabb\web\entities\menu_interface $entity)
	{
		if ($entity->get_id())
		{
			// Save the edited entity to the database
			$entity->save();

			// Change the parent
			if ($this->data['parent_id'] != $entity->get_parent_id())
			{
				try
				{
					$this->operator->change_parent($entity->get_id(), $this->data['parent_id']);
				}
				catch (\vinabb\web\exceptions\base $e)
				{
					trigger_error($this->language->lang('ERROR_MENU_CHANGE_PARENT', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
				}
			}

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MENU_EDIT', time(), [$entity->get_name()]);

			$message = 'MESSAGE_MENU_EDIT';
		}
		else
		{
			// Add the new entity to the database
			$entity = $this->operator->add_menu($entity, $this->data['parent_id']);

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MENU_ADD', time(), [$entity->get_name()]);

			$message = 'MESSAGE_MENU_ADD';
		}

		$this->cache->clear_menus();

		trigger_error($this->language->lang($message) . adm_back_link("{$this->u_action}&parent_id={$this->data['parent_id']}"));
	}

	/**
	* Output entity data to template variables
	*
	* @param \vinabb\web\entities\menu_interface $entity Menu entity
	*/
	protected function data_to_tpl(\vinabb\web\entities\menu_interface $entity)
	{
		$this->template->assign_vars([
			'MENU_NAME'					=> $entity->get_name(),
			'MENU_NAME_VI'				=> $entity->get_name_vi(),
			'MENU_TYPE'					=> $entity->get_type(),
			'MENU_DATA'					=> $entity->get_data(),
			'MENU_TARGET'				=> $entity->get_target(),
			'MENU_ENABLE_GUEST'			=> $entity->get_enable_guest(),
			'MENU_ENABLE_BOT'			=> $entity->get_enable_bot(),
			'MENU_ENABLE_NEW_USER'		=> $entity->get_enable_new_user(),
			'MENU_ENABLE_USER'			=> $entity->get_enable_user(),
			'MENU_ENABLE_MOD'			=> $entity->get_enable_mod(),
			'MENU_ENABLE_GLOBAL_MOD'	=> $entity->get_enable_global_mod(),
			'MENU_ENABLE_ADMIN'			=> $entity->get_enable_admin(),
			'MENU_ENABLE_FOUNDER'		=> $entity->get_enable_founder(),

			'MENU_TYPE_URL'		=> constants::MENU_TYPE_URL,
			'MENU_TYPE_ROUTE'	=> constants::MENU_TYPE_ROUTE,
			'MENU_TYPE_PAGE'	=> constants::MENU_TYPE_PAGE,
			'MENU_TYPE_FORUM'	=> constants::MENU_TYPE_FORUM,
			'MENU_TYPE_USER'	=> constants::MENU_TYPE_USER,
			'MENU_TYPE_GROUP'	=> constants::MENU_TYPE_GROUP,
			'MENU_TYPE_BOARD'	=> constants::MENU_TYPE_BOARD,
			'MENU_TYPE_PORTAL'	=> constants::MENU_TYPE_PORTAL,
			'MENU_TYPE_BB'		=> constants::MENU_TYPE_BB,

			'ICON_OPTIONS'	=> $this->ext_helper->build_icon_list($entity->get_icon())
		]);
	}

	/**
	* Move a menu up/down
	*
	* @param int	$menu_id	Menu ID
	* @param string	$direction	The direction (up|down)
	* @param int	$amount		The number of places to move
	*/
	public function move_menu($menu_id, $direction, $amount = 1)
	{
		// Check the valid link hash
		if (!check_link_hash($this->request->variable('hash', ''), $direction . $menu_id))
		{
			trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		try
		{
			$this->operator->move_menu($menu_id, $direction, $amount);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_MENU_MOVE', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->cache->clear_menus();

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(['success' => true]);
		}

		// Initiate and load the entity for no AJAX request
		/** @var \vinabb\web\entities\menu_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.menu')->load($menu_id);

		// Reload the current page
		redirect("{$this->u_action}&parent_id={$entity->get_parent_id()}");
	}

	/**
	* Delete a menu
	*
	* @param int $menu_id Menu ID
	*/
	public function delete_menu($menu_id)
	{
		/** @var \vinabb\web\entities\menu_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.menu')->load($menu_id);

		try
		{
			$this->operator->delete_menu($menu_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_MENU_DELETE', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MENU_DELETE', time(), [$entity->get_name()]);
		$this->cache->clear_menus();

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send([
				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang('MESSAGE_MENU_DELETE'),
				'REFRESH_DATA'	=> ['time'	=> 3]
			]);
		}
	}

	/**
	* Generate options of available parents
	*
	* @param \vinabb\web\entities\menu_interface	$entity		Menu entity
	* @param int									$parent_id	Parent ID
	* @param string									$mode		Add or edit mode?
	*/
	protected function build_parent_options(\vinabb\web\entities\menu_interface $entity, $parent_id = 0, $mode = 'edit')
	{
		$options = $this->operator->get_menus();
		$parent_id = ($mode == 'edit') ? $entity->get_parent_id() : $parent_id;

		$padding = '';
		$padding_store = [];
		$right = 0;

		/** @var \vinabb\web\entities\menu_interface $option */
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

			$this->template->assign_block_vars('parent_options', [
				'ID'		=> $option->get_id(),
				'NAME'		=> $padding . $option->get_name(),
				'NAME_VI'	=> $padding . $option->get_name_vi(),

				'S_DISABLED'	=> $mode == 'edit' && (($option->get_left_id() > $entity->get_left_id()) && ($option->get_right_id() < $entity->get_right_id()) || ($option->get_id() == $entity->get_id())),
				'S_SELECTED'	=> $option->get_id() == $parent_id
			]);
		}
	}

	/**
	* Generate options of available menu types
	*
	* @param \vinabb\web\entities\menu_interface	$entity 		Menu entity
	* @param int									$current_type	Selected menu type
	* @param string									$mode			Add or edit mode?
	*/
	protected function build_type_options(\vinabb\web\entities\menu_interface $entity, $current_type = 0, $mode = 'edit')
	{
		$options = [constants::MENU_TYPE_URL, constants::MENU_TYPE_ROUTE, constants::MENU_TYPE_PAGE, constants::MENU_TYPE_FORUM, constants::MENU_TYPE_USER, constants::MENU_TYPE_GROUP, constants::MENU_TYPE_BOARD, constants::MENU_TYPE_PORTAL, constants::MENU_TYPE_BB];
		$current_type = ($mode == 'edit') ? $entity->get_type() : $current_type;

		foreach ($options as $option)
		{
			$this->template->assign_block_vars('type_options', [
				'ID'		=> $option,
				'NAME'		=> $this->language->lang(['MENU_TYPES', $option]),

				'S_SELECTED'	=> $option == $current_type
			]);
		}
	}

	/**
	* Generate groups of options for each menu type
	*
	* @param \vinabb\web\entities\menu_interface	$entity 	Menu entity
	* @param int									$current_id	Selected item ID
	* @param string									$mode		Add or edit mode?
	*/
	protected function build_data_options(\vinabb\web\entities\menu_interface $entity, $current_id = 0, $mode = 'edit')
	{
		$current_id = ($mode == 'edit') ? (int) $entity->get_data() : $current_id;

		$this->build_page_options($entity, $current_id);
		$this->build_forum_options($entity, $current_id);
		$this->build_group_options($entity, $current_id);
	}

	/**
	* Generate options of available pages
	*
	* @param \vinabb\web\entities\menu_interface	$entity 	Menu entity
	* @param int									$current_id	Selected page ID
	*/
	protected function build_page_options(\vinabb\web\entities\menu_interface $entity, $current_id = 0)
	{
		foreach ($this->cache->get_pages() as $page_id => $page_data)
		{
			$this->template->assign_block_vars('page_options', [
				'ID'		=> $page_id,
				'NAME'		=> $page_data['name'],
				'NAME_VI'	=> $page_data['name_vi'],

				'S_SELECTED'	=> $page_id == $current_id
			]);
		}
	}

	/**
	* Generate options of available forums
	*
	* @param \vinabb\web\entities\menu_interface	$entity 	Menu entity
	* @param int									$current_id	Selected forum ID
	*/
	protected function build_forum_options(\vinabb\web\entities\menu_interface $entity, $current_id = 0)
	{
		foreach ($this->cache->get_forum_data() as $forum_id => $forum_data)
		{
			$this->template->assign_block_vars('page_options', [
				'ID'		=> $forum_id,
				'NAME'		=> $forum_data['name'],

				'S_SELECTED'	=> $forum_id == $current_id
			]);
		}
	}

	/**
	* Generate options of available groups
	*
	* @param \vinabb\web\entities\menu_interface	$entity 	Menu entity
	* @param int									$current_id	Selected group ID
	*/
	protected function build_group_options(\vinabb\web\entities\menu_interface $entity, $current_id = 0)
	{
		foreach ($this->cache->get_groups() as $group_id => $group_data)
		{
			$this->template->assign_block_vars('page_options', [
				'ID'		=> $group_id,
				'NAME'		=> $group_data['name'],

				'S_SELECTED'	=> $group_id == $current_id
			]);
		}
	}
}
