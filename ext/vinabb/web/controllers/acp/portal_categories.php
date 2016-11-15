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
	/** @var \phpbb\cache\service */
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

	/** @var string */
	protected $u_action;

	/**
	* Constructor
	*
	* @param \phpbb\cache\service								$cache		Cache service
	* @param ContainerInterface									$container	Container object
	* @param \phpbb\language\language							$language	Language object
	* @param \phpbb\log\log										$log		Log object
	* @param \vinabb\web\operators\portal_category_interface	$operator	Page operators
	* @param \phpbb\request\request								$request	Request object
	* @param \phpbb\template\template							$template	Template object
	* @param \phpbb\user										$user		User object
	*/
	public function __construct(
		\phpbb\cache\service $cache,
		ContainerInterface $container,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\vinabb\web\operators\portal_category_interface $operator,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user
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

		foreach ($entities as $entity)
		{
			// The current entity is a child of a previous entity, do not display it
			if ($entity->get_left_id() < $last_right_id)
			{
				continue;
			}

			$this->template->assign_block_vars('cats', [
				'NAME'		=> $entity->get_name(),
				'NAME_VI'	=> $entity->get_name_vi(),
				'VARNAME'	=> $entity->get_varname(),

				'U_CAT'			=> "{$this->u_action}&parent_id=" . $entity->get_id(),
				'U_EDIT'		=> "{$this->u_action}&action=edit&id=" . $entity->get_id(),
				'U_MOVE_DOWN'	=> "{$this->u_action}&action=move_down&id=" . $entity->get_id() . '&hash=' . generate_link_hash('down' . $entity->get_id()),
				'U_MOVE_UP'		=> "{$this->u_action}&action=move_up&id=" . $entity->get_id() . '&hash=' . generate_link_hash('up' . $entity->get_id()),
				'U_DELETE'		=> "{$this->u_action}&action=delete&id=" . $entity->get_id()
			]);

			// Store the current right_id value
			$last_right_id = $entity->get_right_id();
		}

		// Prepare rule breadcrumb path navigation
		$entities = $this->operator->get_parents($parent_id);

		// Process each entity for breadcrumb
		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('breadcrumb', [
				'NAME'	=> $entity->get_name(),
				'URL'	=> "{$this->u_action}&parent_id=" . $entity->get_id(),

				'S_CURRENT'	=> $entity->get_id() == $parent_id
			]);
		}

		$this->template->assign_vars([
			'U_ACTION'	=> $this->u_action,
			'U_ROOT'	=> "{$this->u_action}&parent_id=0",
			'U_ADD'		=> "{$this->u_action}&action=add"
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
		/* @var \vinabb\web\entities\portal_category_interface */
		$entity = $this->container->get('vinabb.web.entities.portal_category');

		// Build the parent dropdown selection
		$this->build_parent_options($entity, $parent_id, 'add');

		// Process the new entity
		$this->add_edit_cat_data($entity);

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
		/* @var \vinabb\web\entities\portal_category_interface */
		$entity = $this->container->get('vinabb.web.entities.portal_category')->load($cat_id);

		// Build the parent dropdown selection
		$this->build_parent_options($entity);

		// Process the edited entity
		$this->add_edit_cat_data($entity);

		$this->template->assign_vars([
			'S_EDIT'	=> true,
			'S_IS_CAT'	=> ($entity->get_right_id() - $entity->get_left_id()) > 1,

			'U_ACTION'	=> "{$this->u_action}&action=edit&id={$cat_id}",
			'U_BACK'	=> "{$this->u_action}&parent_id={$entity->get_parent_id()}"
		]);
	}

	/**
	* Process page data to be added or edited
	*
	* @param \vinabb\web\entities\portal_category_interface $entity Page entity
	*/
	public function add_edit_cat_data($entity)
	{
		$cat_id = $entity->get_id();
		$submit = $this->request->is_set_post('submit');
		$errors = [];

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_portal_categories');

		// Get form data
		$data = [
			'cat_name'		=> $this->request->variable('cat_name', '', true),
			'cat_name_vi'	=> $this->request->variable('cat_name_vi', '', true),
			'cat_varname'	=> $this->request->variable('cat_varname', ''),
			'cat_icon'		=> $this->request->variable('cat_icon', '')
		];

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_portal_categories'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			// Map the form data fields to setters
			$map_fields = [
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
					$errors[] = $e->get_friendly_message($this->language);
				}
			}

			unset($map_fields);

			// Insert or update category
			if (!sizeof($errors))
			{
				if ($cat_id)
				{
					// Save the edited entity to the database
					$entity->save();

					// Change the parent
					if (isset($data['parent_id']) && ($data['parent_id'] != $entity->get_parent_id()))
					{
						try
						{
							$this->operator->change_parent($entity->get_id(), $data['parent_id']);
						}
						catch (\vinabb\web\exceptions\base $e)
						{
							trigger_error($this->language->lang('ERROR_CAT_CHANGE_PARENT', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
						}
					}

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_CAT_EDIT', time(), [$entity->get_varname()]);

					$message = 'MESSAGE_CAT_EDIT';
				}
				else
				{
					// Add the new entity to the database
					$entity = $this->operator->add_cat($entity, $data['parent_id']);

					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_CAT_ADD', time(), [$entity->get_varname()]);

					$message = 'MESSAGE_CAT_ADD';
				}

				$this->cache->clear_portal_cats();

				trigger_error($this->language->lang($message) . adm_back_link("{$this->u_action}&parent_id={$data['parent_id']}"));
			}
		}

		$this->template->assign_vars([
			'ERRORS'	=> sizeof($errors) ? implode('<br>', $errors) : '',

			'CAT_NAME'		=> $entity->get_name(),
			'CAT_NAME_VI'	=> $entity->get_name_vi(),
			'CAT_VARNAME'	=> $entity->get_varname(),
			'CAT_ICON'		=> $entity->get_icon()
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
		catch (\Exception $e)
		{
			trigger_error($this->language->lang($e->getMessage()) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(['success' => true]);
		}

		// Initiate and load the entity for no AJAX request
		/* @var \vinabb\web\entities\portal_category_interface */
		$entity = $this->container->get('vinabb.web.entities.portal_category')->load($cat_id);

		// Reload the current page
		redirect("{$this->u_action}&parent_id={$entity->get_parent_id()}");
	}

	/**
	* Deleta a category
	*
	* @param int $cat_id Category ID
	*/
	public function delete_cat($cat_id)
	{
		/* @var \vinabb\web\entities\portal_category_interface */
		$entity = $this->container->get('vinabb.web.entities.portal_category')->load($cat_id);

		try
		{
			$this->operator->delete_cat($cat_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_CAT_DELETE', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PORTAL_CAT_DELETE', time(), [$entity->get_varname()]);

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
		$menu_items = $this->operator->get_cats();
		$parent_id = ($mode == 'edit') ? $entity->get_parent_id() : $parent_id;

		$padding = '';
		$padding_store = [];
		$right = 0;

		foreach ($menu_items as $menu_item)
		{
			if ($menu_item->get_left_id() < $right)
			{
				$padding .= '&nbsp;&nbsp;';
				$padding_store[$menu_item->get_parent_id()] = $padding;
			}
			else if ($menu_item->get_left_id() > $right + 1)
			{
				$padding = isset($padding_store[$menu_item->get_parent_id()]) ? $padding_store[$menu_item->get_parent_id()] : '';
			}

			$right = $menu_item->get_right_id();

			$this->template->assign_block_vars('parent_options', [
				'ID'		=> $menu_item->get_id(),
				'NAME'		=> $padding . $menu_item->get_name(),
				'NAME_VI'	=> $padding . $menu_item->get_name_vi(),

				'S_DISABLED'	=> $mode == 'edit' && (($menu_item->get_left_id() > $entity->get_left_id()) && ($menu_item->get_right_id() < $entity->get_right_id()) || ($menu_item->get_id() == $entity->get_id())),
				'S_SELECTED'	=> $menu_item->get_id() == $parent_id
			]);
		}
	}
}