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
* Controller for the portal_categories_module
*/
class portal_categories implements portal_categories_interface
{
	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \vinabb\web\operators\portal_category_interface */
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
	protected $errors;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface	$cache		Cache service
	* @param ContainerInterface									$container	Container object
	* @param \phpbb\language\language							$language	Language object
	* @param \phpbb\log\log										$log		Log object
	* @param \vinabb\web\operators\portal_category_interface	$operator	Portal category operators
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
		\vinabb\web\operators\portal_category_interface $operator,
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
	* Display categories
	*
	* @param int $parent_id Parent ID
	*/
	public function display_cats($parent_id = 0)
	{
		// Grab all from database
		$entities = $this->operator->get_cats($parent_id);

		// Initialize a variable to hold the right_id value
		$last_right_id = 0;

		/** @var \vinabb\web\entities\portal_category_interface $entity */
		foreach ($entities as $entity)
		{
			// The current entity is a child of a previous entity, do not display it
			if ($entity->get_left_id() < $last_right_id)
			{
				continue;
			}

			$this->template->assign_block_vars('cats', [
				'URL'		=> "{$this->u_action}&parent_id={$entity->get_id()}",
				'NAME'		=> $entity->get_name(),
				'NAME_VI'	=> $entity->get_name_vi(),
				'VARNAME'	=> $entity->get_varname(),

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
		/** @var \vinabb\web\entities\portal_category_interface $entity */
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
	* Add a category
	*
	* @param int $parent_id Parent ID
	*/
	public function add_cat($parent_id = 0)
	{
		// Initiate an entity
		/** @var \vinabb\web\entities\portal_category_interface */
		$entity = $this->container->get('vinabb.web.entities.portal_category');

		// Build the parent selection
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
	* Edit a category
	*
	* @param int $cat_id Category ID
	*/
	public function edit_cat($cat_id)
	{
		// Initiate and load the entity
		/** @var \vinabb\web\entities\portal_category_interface */
		$entity = $this->container->get('vinabb.web.entities.portal_category')->load($cat_id);

		// Build the parent selection
		$this->build_parent_options($entity);

		// Process the edited entity
		$this->add_edit_data($entity);

		$this->template->assign_vars([
			'S_EDIT'	=> true,
			'S_IS_CAT'	=> ($entity->get_right_id() - $entity->get_left_id()) > 1,

			'U_ACTION'	=> "{$this->u_action}&action=edit&id={$cat_id}",
			'U_BACK'	=> "{$this->u_action}&parent_id={$entity->get_parent_id()}"
		]);
	}

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\portal_category_interface $entity Portal category entity
	*/
	public function add_edit_data(\vinabb\web\entities\portal_category_interface $entity)
	{
		$submit = $this->request->is_set_post('submit');

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_portal_categories');

		// Get form data
		$data = $this->request_data();

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_portal_categories'))
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
			'parent_id'		=> $this->request->variable('parent_id', 0),
			'cat_name'		=> $this->request->variable('cat_name', '', true),
			'cat_name_vi'	=> $this->request->variable('cat_name_vi', '', true),
			'cat_varname'	=> $this->request->variable('cat_varname', ''),
			'cat_icon'		=> $this->request->variable('cat_icon', '')
		];
	}

	/**
	* Map the form data fields to setters and set them to the entity
	*
	* @param \vinabb\web\entities\portal_category_interface	$entity	Portal category entity
	* @param array											$data	Form data
	*/
	protected function map_set_data(\vinabb\web\entities\portal_category_interface $entity, $data)
	{
		$map_fields = [
			'set_parent_id'	=> $data['parent_id'],
			'set_name'		=> $data['cat_name'],
			'set_name_vi'	=> $data['cat_name_vi'],
			'set_varname'	=> $data['cat_varname'],
			'set_icon'		=> $data['cat_icon']
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
	* @param int											$parent_id	Parent ID
	* @param \vinabb\web\entities\portal_category_interface $entity 	Portal category entity
	*/
	protected function save_data(\vinabb\web\entities\portal_category_interface $entity, $parent_id)
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
					trigger_error($this->language->lang('ERROR_CAT_CHANGE_PARENT', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
				}
			}

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_CAT_EDIT', time(), [$entity->get_name()]);

			$message = 'MESSAGE_CAT_EDIT';
		}
		else
		{
			// Add the new entity to the database
			$entity = $this->operator->add_cat($entity, $parent_id);

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_CAT_ADD', time(), [$entity->get_name()]);

			$message = 'MESSAGE_CAT_ADD';
		}

		$this->cache->clear_portal_cats();

		trigger_error($this->language->lang($message) . adm_back_link("{$this->u_action}&parent_id={$parent_id}"));
	}

	/**
	* Output entity data to template variables
	*
	* @param \vinabb\web\entities\portal_category_interface $entity Portal category entity
	*/
	protected function data_to_tpl(\vinabb\web\entities\portal_category_interface $entity)
	{
		$this->template->assign_vars([
			'CAT_NAME'		=> $entity->get_name(),
			'CAT_NAME_VI'	=> $entity->get_name_vi(),
			'CAT_VARNAME'	=> $entity->get_varname(),

			'ICON_OPTIONS'	=> $this->ext_helper->build_icon_list($entity->get_icon())
		]);
	}

	/**
	* Move a rule up/down
	*
	* @param int	$cat_id		Category ID
	* @param string	$direction	The direction (up|down)
	* @param int	$amount		The number of places to move
	*/
	public function move_cat($cat_id, $direction, $amount = 1)
	{
		// Check the valid link hash
		if (!check_link_hash($this->request->variable('hash', ''), $direction . $cat_id))
		{
			trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		try
		{
			$this->operator->move_cat($cat_id, $direction, $amount);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_CAT_MOVE', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->cache->clear_portal_cats();

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(['success' => true]);
		}

		// Initiate and load the entity for no AJAX request
		/** @var \vinabb\web\entities\portal_category_interface */
		$entity = $this->container->get('vinabb.web.entities.portal_category')->load($cat_id);

		// Reload the current page
		redirect("{$this->u_action}&parent_id={$entity->get_parent_id()}");
	}

	/**
	* Delete a category
	*
	* @param int $cat_id Category ID
	*/
	public function delete_cat($cat_id)
	{
		/** @var \vinabb\web\entities\portal_category_interface */
		$entity = $this->container->get('vinabb.web.entities.portal_category')->load($cat_id);

		try
		{
			$this->operator->delete_cat($cat_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_CAT_DELETE', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_CAT_DELETE', time(), [$entity->get_name()]);
		$this->cache->clear_portal_cats();

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

	/**
	* Generate options of available parents
	*
	* @param \vinabb\web\entities\portal_category_interface	$entity		Portal category entity
	* @param int											$parent_id	Parent ID
	* @param string											$mode		Add or edit mode?
	*/
	protected function build_parent_options($entity, $parent_id = 0, $mode = 'edit')
	{
		$options = $this->operator->get_cats();
		$parent_id = ($mode == 'edit') ? $entity->get_parent_id() : $parent_id;

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
