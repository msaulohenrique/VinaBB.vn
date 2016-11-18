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
	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \vinabb\web\operators\bb_category_interface */
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

	/** @var int */
	protected $bb_type;

	/**
	* Constructor
	*
	* @param \phpbb\cache\service							$cache		Cache service
	* @param ContainerInterface								$container	Container object
	* @param \phpbb\language\language						$language	Language object
	* @param \phpbb\log\log									$log		Log object
	* @param \vinabb\web\operators\bb_category_interface	$operator	BB author operators
	* @param \phpbb\request\request							$request	Request object
	* @param \phpbb\template\template						$template	Template object
	* @param \phpbb\user									$user		User object
	* @param \vinabb\web\controllers\helper_interface		$ext_helper	Extension helper
	*/
	public function __construct(
		\phpbb\cache\service $cache,
		ContainerInterface $container,
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
	* Set phpBB resource types
	*
	* @param int $bb_type phpBB resource type
	*/
	public function set_bb_type($bb_type)
	{
		$this->bb_type = $bb_type;
	}

	/**
	* Display categories
	*/
	public function display_cats()
	{
		// Grab all from database
		$entities = $this->operator->get_cats($this->bb_type);

		/* @var \vinabb\web\entities\bb_category_interface $entity */
		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('cats', [
				'NAME'		=> $entity->get_name(),
				'NAME_VI'	=> $entity->get_name_vi(),
				'VARNAME'	=> $entity->get_varname(),
				'DESC'		=> $entity->get_desc(),
				'DESC_VI'	=> $entity->get_desc_vi(),
				'ICON'		=> $entity->get_icon(),

				'U_EDIT'	=> "{$this->u_action}&action=edit&id={$entity->get_id()}",
				'U_DELETE'	=> "{$this->u_action}&action=delete&id={$entity->get_id()}"
			]);
		}

		$this->template->assign_vars([
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Add a category
	*/
	public function add_cat()
	{
		// Initiate an entity
		/* @var \vinabb\web\entities\bb_category_interface */
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
		/* @var \vinabb\web\entities\bb_category_interface */
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
	public function add_edit_data($entity)
	{
		$submit = $this->request->is_set_post('submit');
		$errors = [];

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_bb_categories');

		// Get form data
		$data = [
			'cat_name'		=> $this->request->variable('cat_name', '', true),
			'cat_name_vi'	=> $this->request->variable('cat_name_vi', '', true),
			'cat_varname'	=> $this->request->variable('cat_varname', ''),
			'cat_desc'		=> $this->request->variable('cat_desc', '', true),
			'cat_desc_vi'	=> $this->request->variable('cat_desc_vi', '', true),
			'cat_icon'		=> $this->request->variable('cat_icon', '')
		];

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_bb_categories'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			// Map the form data fields to setters
			$map_fields = [
				'set_name'		=> $data['cat_name'],
				'set_name_vi'	=> $data['cat_name_vi'],
				'set_varname'	=> $data['cat_varname'],
				'set_desc'		=> $data['cat_desc'],
				'set_desc_vi'	=> $data['cat_desc_vi'],
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

			// Insert or update
			if (!sizeof($errors))
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
		}

		$this->template->assign_vars([
			'ERRORS'	=> sizeof($errors) ? implode('<br>', $errors) : '',

			'CAT_NAME'		=> $entity->get_name(),
			'CAT_NAME_VI'	=> $entity->get_name_vi(),
			'CAT_VARNAME'	=> $entity->get_varname(),
			'CAT_DESC'		=> $entity->get_desc(),
			'CAT_DESC_VI'	=> $entity->get_desc_vi(),

			'ICON_OPTIONS'	=> $this->ext_helper->build_icon_list($entity->get_icon()),

			'U_BACK'	=> $this->u_action
		]);
	}

	/**
	* Delete a category
	*
	* @param int $cat_id Category ID
	*/
	public function delete_cat($cat_id)
	{
		/* @var \vinabb\web\entities\bb_category_interface */
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
