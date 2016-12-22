<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

use vinabb\web\entities\sub\user_data;

/**
* Entity for a single user
*/
class user extends user_data implements user_interface
{
	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \vinabb\web\entities\helper\helper_interface $entity_helper */
	protected $entity_helper;

	/** @var array $data */
	protected $data;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface				$db				Database object
	* @param \vinabb\web\entities\helper\helper_interface	$entity_helper	Entity helper
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \vinabb\web\entities\helper\helper_interface $entity_helper)
	{
		$this->db = $db;
		$this->entity_helper = $entity_helper;
	}

	/**
	* Data for this entity
	*
	* @return array
	*/
	protected function prepare_data()
	{
		return [
			'user_id'			=> 'integer',
			'group_id'			=> 'integer',
			'username'			=> 'string',
			'username_clean'	=> 'string',
			'user_type'			=> 'integer',
			'user_email'		=> 'string',
			'user_password'		=> 'string',
			'user_birthday'		=> 'string',
			'user_new'			=> 'bool',

			// Entity: vinabb\web\entities\abs\user_data
			'user_lang'				=> 'string',
			'user_style'			=> 'integer',
			'user_timezone'			=> 'string',
			'user_dateformat'		=> 'string',
			'user_posts'			=> 'integer',
			'user_new_privmsg'		=> 'integer',
			'user_unread_privmsg'	=> 'integer',
			'user_warnings'			=> 'integer',
			'user_message_rules'	=> 'integer',

			// Entity: vinabb\web\entities\abs\user_logtime
			'user_lastpage'			=> 'string',
			'user_regdate'			=> 'integer',
			'user_lastvisit'		=> 'integer',
			'user_lastmark'			=> 'integer',
			'user_last_search'		=> 'integer',
			'user_lastpost_time'	=> 'integer',
			'user_last_privmsg'		=> 'integer',
			'user_last_warning'		=> 'integer',
			'user_passchg'			=> 'integer',
			'user_emailtime'		=> 'integer',
			'user_reminded_time'	=> 'integer',
			'user_inactive_time'	=> 'integer',

			// Entity: vinabb\web\entities\abs\user_options
			'user_jabber'				=> 'string',
			'user_notify'				=> 'bool',
			'user_notify_pm'			=> 'bool',
			'user_notify_type'			=> 'integer',
			'user_allow_pm'				=> 'bool',
			'user_allow_viewonline'		=> 'bool',
			'user_allow_viewemail'		=> 'bool',
			'user_allow_massemail'		=> 'bool',
			'user_full_folder'			=> 'integer',
			'user_topic_show_days'		=> 'integer',
			'user_topic_sortby_type'	=> 'string',
			'user_topic_sortby_dir'		=> 'string',
			'user_post_show_days'		=> 'integer',
			'user_post_sortby_type'		=> 'string',
			'user_post_sortby_dir'		=> 'string',

			// Entity: vinabb\web\entities\abs\user_reg
			'user_ip'				=> 'string',
			'user_email_hash'		=> 'integer',
			'user_form_salt'		=> 'string',
			'user_permissions'		=> 'string',
			'user_perm_from'		=> 'string',
			'user_actkey'			=> 'string',
			'user_last_confirm_key'	=> 'string',
			'user_login_attempts'	=> 'integer',
			'user_newpasswd'		=> 'string',
			'user_reminded'			=> 'integer',
			'user_inactive_reason'	=> 'integer',

			// Entity: vinabb\web\entities\abs\user_profile
			'user_avatar'			=> 'string',
			'user_avatar_type'		=> 'string',
			'user_avatar_width'		=> 'integer',
			'user_avatar_height'	=> 'integer',
			'user_rank'				=> 'integer',
			'user_colour'			=> 'string',

			// Entity: vinabb\web\entities\abs\user_sig
			'user_sig'					=> 'string',
			'user_sig_bbcode_uid'		=> 'string',
			'user_sig_bbcode_bitfield'	=> 'string',
			'user_options'				=> 'integer'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		User ID
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . USERS_TABLE . '
			WHERE user_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('user_id');
		}

		return $this;
	}

	/**
	* Import data for an entity
	*
	* Used when the data is already loaded externally.
	* Any existing data on this entity is over-written.
	* All data is validated and an exception is thrown if any data is invalid.
	*
	* @param array				$data	Data array from the database
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\invalid_argument
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = [];

		// Go through the basic fields and set them to our data array
		foreach ($this->prepare_data() as $field => $type)
		{
			// The data wasn't sent to us
			if (!isset($data[$field]))
			{
				throw new \vinabb\web\exceptions\invalid_argument([$field, 'EMPTY']);
			}

			// settype() passes values by reference
			$value = $data[$field];

			// We're using settype() to enforce data types
			settype($value, $type);

			$this->data[$field] = $value;
		}

		return $this;
	}

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return user_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['user_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('user_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['user_id']);

		$sql = 'INSERT INTO ' . USERS_TABLE . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['user_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return user_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['user_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('user_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['user_id' => null]);

		$sql = 'UPDATE ' . USERS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE user_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the user ID
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['user_id']) ? (int) $this->data['user_id'] : 0;
	}

	/**
	* Get the group ID
	*
	* @return int
	*/
	public function get_group_id()
	{
		return isset($this->data['group_id']) ? (int) $this->data['group_id'] : 0;
	}

	/**
	* Set the group ID
	*
	* @param int				$id		Group ID
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_group_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if (!$id)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['group_id', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_group_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['group_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['group_id'] = $id;

		return $this;
	}

	/**
	* Get the username
	*
	* @return string
	*/
	public function get_username()
	{
		return isset($this->data['username']) ? (string) $this->data['username'] : '';
	}

	/**
	* Get the clean username for searching
	*
	* @return string
	*/
	public function get_username_clean()
	{
		return isset($this->data['username_clean']) ? (string) $this->data['username_clean'] : '';
	}

	/**
	* Get the user type
	*
	* @return int
	*/
	public function get_type()
	{
		return isset($this->data['user_type']) ? (int) $this->data['user_type'] : USER_NORMAL;
	}

	/**
	* Set the user type
	*
	* @param int				$value	User type
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_type($value)
	{
		$value = (int) $value;

		if (!in_array($value, [USER_NORMAL, USER_INACTIVE, USER_IGNORE, USER_FOUNDER]))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('user_type');
		}

		// Set the value on our data array
		$this->data['user_type'] = $value;

		return $this;
	}

	/**
	* Get the user email
	*
	* @return string
	*/
	public function get_email()
	{
		return isset($this->data['user_email']) ? (string) $this->data['user_email'] : '';
	}

	/**
	* Get the user's hashed password
	*
	* @return string
	*/
	public function get_password()
	{
		return isset($this->data['user_password']) ? (string) $this->data['user_password'] : '';
	}

	/**
	* Get the user birthday
	*
	* @return string
	*/
	public function get_birthday()
	{
		return isset($this->data['user_birthday']) ? (string) $this->data['user_birthday'] : '';
	}

	/**
	* The user has just newly registered?
	*
	* @return bool
	*/
	public function get_new()
	{
		return isset($this->data['user_new']) ? (bool) $this->data['user_new'] : true;
	}
}
