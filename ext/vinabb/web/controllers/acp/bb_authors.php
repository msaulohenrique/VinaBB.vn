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
* Controller for the bb_authors_module
*/
class bb_authors implements bb_authors_interface
{
	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\log\log $log */
	protected $log;

	/** @var \vinabb\web\operators\bb_author_interface $operator */
	protected $operator;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

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
	* @param \vinabb\web\operators\bb_author_interface			$operator	BB author operators
	* @param \phpbb\request\request								$request	Request object
	* @param \phpbb\template\template							$template	Template object
	* @param \phpbb\user										$user		User object
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		ContainerInterface $container,
		\phpbb\language\language $language,
		\phpbb\log\log $log,
		\vinabb\web\operators\bb_author_interface $operator,
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
	* Display authors
	*/
	public function display_authors()
	{
		// Grab all from database
		$entities = $this->operator->get_authors();

		/** @var \vinabb\web\entities\bb_author_interface $entity */
		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('authors', [
				'NAME'		=> $entity->get_name(),
				'FIRSTNAME'	=> $entity->get_firstname(),
				'LASTNAME'	=> $entity->get_lastname(),

				'U_EDIT'	=> "{$this->u_action}&action=edit&id={$entity->get_id()}",
				'U_DELETE'	=> "{$this->u_action}&action=delete&id={$entity->get_id()}"
			]);
		}

		$this->template->assign_vars([
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Add an author
	*/
	public function add_author()
	{
		// Initiate an entity
		/** @var \vinabb\web\entities\bb_author_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.bb_author');

		// Process the new entity
		$this->add_edit_data($entity);

		$this->template->assign_vars([
			'S_ADD'		=> true,
			'U_ACTION'	=> "{$this->u_action}&action=add"
		]);
	}

	/**
	* Edit an author
	*
	* @param int $author_id Author ID
	*/
	public function edit_author($author_id)
	{
		// Initiate and load the entity
		/** @var \vinabb\web\entities\bb_author_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.bb_author')->load($author_id);

		// Process the edited entity
		$this->add_edit_data($entity);

		$this->template->assign_vars([
			'S_EDIT'	=> true,
			'U_ACTION'	=> "{$this->u_action}&action=edit&id={$author_id}"
		]);
	}

	/**
	* Process data to be added or edited
	*
	* @param \vinabb\web\entities\bb_author_interface $entity BB author entity
	*/
	public function add_edit_data(\vinabb\web\entities\bb_author_interface $entity)
	{
		$submit = $this->request->is_set_post('submit');

		// Create a form key for preventing CSRF attacks
		add_form_key('acp_bb_authors');

		// Get form data
		$this->request_data();

		if ($submit)
		{
			// Test if the submitted form is valid
			if (!check_form_key('acp_bb_authors'))
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
			'ERRORS'	=> sizeof($this->errors) ? implode('<br>', $this->errors) : '',
			'U_BACK'	=> $this->u_action
		]);
	}

	/**
	* Request data from the form
	*/
	protected function request_data()
	{
		$this->data = [
			'user_id'				=> $this->request->variable('user_id', 0),
			'author_name'			=> $this->request->variable('author_name', '', true),
			'author_firstname'		=> $this->request->variable('author_firstname', '', true),
			'author_lastname'		=> $this->request->variable('author_lastname', '', true),
			'author_www'			=> $this->request->variable('author_www', ''),
			'author_email'			=> $this->request->variable('author_email', ''),
			'author_phpbb'			=> $this->request->variable('author_phpbb', 0),
			'author_github'			=> $this->request->variable('author_github', ''),
			'author_facebook'		=> $this->request->variable('author_facebook', ''),
			'author_twitter'		=> $this->request->variable('author_twitter', ''),
			'author_google_plus'	=> $this->request->variable('author_google_plus', ''),
			'author_skype'			=> $this->request->variable('author_skype', '')
		];
	}

	/**
	* Map the form data fields to setters and set them to the entity
	*
	* @param \vinabb\web\entities\bb_author_interface $entity BB author entity
	*/
	protected function map_set_data(\vinabb\web\entities\bb_author_interface $entity)
	{
		$map_fields = [
			'set_user_id'		=> $this->data['user_id'],
			'set_name'			=> $this->data['author_name'],
			'set_name_seo'		=> $this->data['author_name'],
			'set_firstname'		=> $this->data['author_firstname'],
			'set_lastname'		=> $this->data['author_lastname'],
			'set_www'			=> $this->data['author_www'],
			'set_email'			=> $this->data['author_email'],
			'set_github'		=> $this->data['author_github'],
			'set_facebook'		=> $this->data['author_facebook'],
			'set_twitter'		=> $this->data['author_twitter'],
			'set_google_plus'	=> $this->data['author_google_plus'],
			'set_skype'			=> $this->data['author_skype']
		];

		// Set the mapped data in the entity
		foreach ($map_fields as $entity_function => $author_data)
		{
			try
			{
				// Calling the $entity_function on the entity and passing it $author_data
				$entity->$entity_function($author_data);
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
	* @param \vinabb\web\entities\bb_author_interface $entity BB author entity
	*/
	protected function save_data(\vinabb\web\entities\bb_author_interface $entity)
	{
		if ($entity->get_id())
		{
			// Save the edited entity to the database
			$entity->save();

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BB_AUTHOR_EDIT', time(), [$entity->get_name()]);

			$message = 'MESSAGE_AUTHOR_EDIT';
		}
		else
		{
			// Add the new entity to the database
			$entity = $this->operator->add_author($entity);

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BB_AUTHOR_ADD', time(), [$entity->get_name()]);

			$message = 'MESSAGE_AUTHOR_ADD';
		}

		trigger_error($this->language->lang($message) . adm_back_link($this->u_action));
	}

	/**
	* Output entity data to template variables
	*
	* @param \vinabb\web\entities\bb_author_interface $entity BB author entity
	*/
	protected function data_to_tpl(\vinabb\web\entities\bb_author_interface $entity)
	{
		$this->template->assign_vars([
			'AUTHOR_NAME'			=> $entity->get_name(),
			'AUTHOR_FIRSTNAME'		=> $entity->get_firstname(),
			'AUTHOR_LASTNAME'		=> $entity->get_lastname(),
			'AUTHOR_WWW'			=> $entity->get_www(),
			'AUTHOR_EMAIL'			=> $entity->get_email(),
			'AUTHOR_PHPBB'			=> $entity->get_phpbb(),
			'AUTHOR_GITHUB'			=> $entity->get_github(),
			'AUTHOR_FACEBOOK'		=> $entity->get_facebook(),
			'AUTHOR_TWITTER'		=> $entity->get_twitter(),
			'AUTHOR_GOOGLE_PLUS'	=> $entity->get_google_plus(),
			'AUTHOR_SKYPE'			=> $entity->get_skype()
		]);
	}

	/**
	* Delete an author
	*
	* @param int $author_id Author ID
	*/
	public function delete_author($author_id)
	{
		/** @var \vinabb\web\entities\bb_author_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.bb_author')->load($author_id);

		try
		{
			$this->operator->delete_author($author_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			trigger_error($this->language->lang('ERROR_AUTHOR_DELETE', $e->get_message($this->language)) . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_BB_AUTHOR_DELETE', time(), [$entity->get_name()]);

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send([
				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang('MESSAGE_AUTHOR_DELETE'),
				'REFRESH_DATA'	=> ['time'	=> 3]
			]);
		}
	}
}
