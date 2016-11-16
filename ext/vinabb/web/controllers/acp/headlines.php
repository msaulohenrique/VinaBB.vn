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
* Controller for the headlines_module
*/
class headlines implements headlines_interface
{
	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \vinabb\web\operators\headline_interface */
	protected $operator;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string */
	protected $u_action;

	/** @var array */
	protected $lang_data = [];

	/**
	* Constructor
	*
	* @param \phpbb\cache\service						$cache		Cache service
	* @param ContainerInterface							$container	Container object
	* @param \phpbb\language\language					$language	Language object
	* @param \phpbb\log\log								$log		Log object
	* @param \vinabb\web\operators\headline_interface	$operator	Headline operators
	* @param \phpbb\request\request						$request	Request object
	* @param \phpbb\template\template					$template	Template object
	* @param \phpbb\user								$user		User object
	*/
	public function __construct(
		\phpbb\cache\service $cache,
		ContainerInterface $container,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\vinabb\web\operators\headline_interface $operator,
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

		$this->lang_data = $this->cache->get_lang_data();
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
	* Display headlines
	*/
	public function display_headlines()
	{
		// Grab all from database
		$entities = $this->operator->get_headlines();

		/* @var \vinabb\web\entities\headline_interface $entity */
		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('headlines', [
				'LANG'	=> $this->lang_data[$entity->get_lang()]['local_name'],
				'NAME'	=> $entity->get_name(),
				'DESC'	=> $entity->get_desc(),
				'IMG'	=> $entity->get_img(),
				'URL'	=> $entity->get_url(),

				'U_EDIT'	=> "{$this->u_action}&action=edit&id={$entity->get_id()}",
				'U_DELETE'	=> "{$this->u_action}&action=delete&id={$entity->get_id()}"
			]);
		}

		$this->template->assign_vars([
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Add a headline
	*/
	public function add_headline()
	{
		// Initiate an entity
		/* @var \vinabb\web\entities\headline_interface */
		$entity = $this->container->get('vinabb.web.entities.headline');

		// Process the new entity
		$this->add_edit_data($entity);

		$this->template->assign_vars([
			'S_ADD'		=> true,
			'U_ACTION'	=> "{$this->u_action}&action=add"
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
		/* @var \vinabb\web\entities\headline_interface */
		$entity = $this->container->get('vinabb.web.entities.headline')->load($headline_id);

		// Process the edited entity
		$this->add_edit_data($entity);

		$this->template->assign_vars([
			'S_EDIT'	=> true,
			'U_ACTION'	=> "{$this->u_action}&action=edit&id={$headline_id}"
		]);
	}

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\headline_interface $entity Headline entity
	*/
	public function add_edit_data($entity)
	{
		$headline_id = $entity->get_id();
		$submit = $this->request->is_set_post('submit');
		$errors = [];

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_headlines');

		// Get form data
		$data = [
			'headline_lang'	=> $this->request->variable('headline_lang', ''),
			'headline_name'	=> $this->request->variable('headline_name', '', true),
			'headline_desc'	=> $this->request->variable('headline_desc', '', true),
			'headline_img'	=> $this->request->variable('headline_img', ''),
			'headline_url'	=> $this->request->variable('headline_url', '')
		];

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_headlines'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			// Map the form data fields to setters
			$map_fields = [
				'set_lang'	=> $data['headline_lang'],
				'set_name'	=> $data['headline_name'],
				'set_desc'	=> $data['headline_desc'],
				'set_img'	=> $data['headline_img'],
				'set_url'	=> $data['headline_url']
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
					$errors[] = $e->get_friendly_message($this->language);
				}
			}

			unset($map_fields);

			// Insert or update
			if (!sizeof($errors))
			{
				if ($headline_id)
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

				$this->cache->clear_headlines();

				trigger_error($this->language->lang($message) . adm_back_link($this->u_action));
			}
		}

		$this->template->assign_vars([
			'ERRORS'	=> sizeof($errors) ? implode('<br>', $errors) : '',

			'HEADLINE_NAME'	=> $entity->get_name(),
			'HEADLINE_DESC'	=> $entity->get_desc(),
			'HEADLINE_IMG'	=> $entity->get_img(),
			'HEADLINE_URL'	=> $entity->get_url(),

			'U_BACK'	=> $this->u_action
		]);
	}

	/**
	* Delete a headline
	*
	* @param int $headline_id Headline ID
	*/
	public function delete_headline($headline_id)
	{
		/* @var \vinabb\web\entities\headline_interface */
		$entity = $this->container->get('vinabb.web.entities.headline')->load($headline_id);

		try
		{
			$this->operator->delete_headline($headline_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_HEADLINE_DELETE', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_HEADLINE_DELETE', time(), [$entity->get_name()]);

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
}
