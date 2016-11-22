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
* Controller for the menus_module
*/
class menus implements menus_interface
{
	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \vinabb\web\operators\menu_interface */
	protected $operator;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \vinabb\web\controllers\helper_interface */
	protected $ext_helper;

	/** @var string */
	protected $u_action;

	/** @var array */
	protected $errors = [];

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

		/* @var \vinabb\web\entities\menu_interface $entity */
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
		/* @var \vinabb\web\entities\menu_interface $entity */
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
		/* @var \vinabb\web\entities\menu_interface */
		$entity = $this->container->get('vinabb.web.entities.menu');

		// Build the parent dropdown selection
		$this->build_parent_options($entity, $parent_id, 'add');

		// Process the new entity
		$this->add_edit_data($entity);

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
		/* @var \vinabb\web\entities\menu_interface */
		$entity = $this->container->get('vinabb.web.entities.menu')->load($menu_id);

		// Build the parent dropdown selection
		$this->build_parent_options($entity);

		// Process the edited entity
		$this->add_edit_data($entity);

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
		$data = $this->request_data();

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_menus'))
			{
				$this->errors[] = $this->language->lang('FORM_INVALID');
			}

			// Map and set data to the entity
			$this->map_set_data($entity, $data);

			// Insert or update
			if (!sizeof($this->errors))
			{
				$this->save_data($entity, $data['parent_id']);
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
	*
	* @return array
	*/
	protected function request_data()
	{
		return [
			'parent_id'					=> $this->request->variable('parent_id', 0),
			'menu_name'					=> $this->request->variable('menu_name', '', true),
			'menu_name_vi'				=> $this->request->variable('menu_name_vi', '', true),
			'menu_type'					=> $this->request->variable('menu_type', 0),
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
	* @param \vinabb\web\entities\menu_interface	$entity	Menu entity
	* @param array									$data	Form data
	*/
	protected function map_set_data(\vinabb\web\entities\menu_interface $entity, $data)
	{
		$map_fields = [
			'set_parent_id'			=> $data['parent_id'],
			'set_name'				=> $data['menu_name'],
			'set_name_vi'			=> $data['menu_name_vi'],
			'set_type'				=> $data['menu_type'],
			'set_icon'				=> $data['menu_icon'],
			'set_data'				=> $data['menu_data'],
			'set_target'			=> $data['menu_target'],
			'set_enable_guest'		=> $data['menu_enable_guest'],
			'set_enable_bot'		=> $data['menu_enable_bot'],
			'set_enable_new_user'	=> $data['menu_enable_new_user'],
			'set_enable_user'		=> $data['menu_enable_user'],
			'set_enable_mod'		=> $data['menu_enable_mod'],
			'set_enable_global_mod'	=> $data['menu_enable_global_mod'],
			'set_enable_admin'		=> $data['menu_enable_admin'],
			'set_enable_founder'	=> $data['menu_enable_founder']
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
	* @param int									$parent_id	Parent ID
	* @param \vinabb\web\entities\menu_interface	$entity		Menu entity
	*/
	protected function save_data(\vinabb\web\entities\menu_interface $entity, $parent_id)
	{
		if ($entity->get_id())
		{
			// Save the edited entity to the database
			$entity->save();

			// Change the parent
			if ($parent_id != $entity->get_parent_id())
			{
				try
				{
					$this->operator->change_parent($entity->get_id(), $parent_id);
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
			$entity = $this->operator->add_menu($entity, $parent_id);

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MENU_ADD', time(), [$entity->get_name()]);

			$message = 'MESSAGE_MENU_ADD';
		}

		$this->cache->clear_menus();

		trigger_error($this->language->lang($message) . adm_back_link("{$this->u_action}&parent_id={$parent_id}"));
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
		/* @var \vinabb\web\entities\menu_interface */
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
		/* @var \vinabb\web\entities\menu_interface */
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
	protected function build_parent_options($entity, $parent_id = 0, $mode = 'edit')
	{
		$options = $this->operator->get_menus();
		$parent_id = ($mode == 'edit') ? $entity->get_parent_id() : $parent_id;

		$padding = '';
		$padding_store = [];
		$right = 0;

		/* @var \vinabb\web\entities\menu_interface $option */
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
}
