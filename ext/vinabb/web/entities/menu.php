<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

use vinabb\web\includes\constants;

/**
* Entity for a single menu item
*/
class menu implements menu_interface
{
	/**
	* Data for this entity
	*
	* @var array
	*	menu_id
	*	parent_id
	*	left_id
	*	right_id
	*	menu_parents
	*	menu_name
	*	menu_name_vi
	*	menu_type
	*	menu_icon
	*	menu_data
	*	menu_target
	*	menu_enable_guest
	*	menu_enable_bot
	*	menu_enable_new_user
	*	menu_enable_user
	*	menu_enable_mod
	*	menu_enable_global_mod
	*	menu_enable_admin
	*	menu_enable_founder
	*/
	protected $data;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string */
	protected $table_name;

	/**
	* Constructor
	*
	* @param \phpbb\config\config				$config			Config object
	* @param \phpbb\db\driver\driver_interface	$db				Database object
	* @param string								$table_name		Table name
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, $table_name)
	{
		$this->config = $config;
		$this->db = $db;
		$this->table_name = $table_name;
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Page ID
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id = 0)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE menu_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('menu_id');
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
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = [];

		// All of our fields
		$fields = [
			'menu_id'					=> 'integer',
			'parent_id'					=> 'integer',
			'left_id'					=> 'integer',
			'right_id'					=> 'integer',
			'menu_parents'				=> 'string',
			'menu_name'					=> 'set_name',
			'menu_name_vi'				=> 'set_name_vi',
			'menu_type'					=> 'set_type',
			'menu_icon'					=> 'set_icon',
			'menu_data'					=> 'set_data',
			'menu_target'				=> 'bool',
			'menu_enable_guest'			=> 'set_enable_guest',
			'menu_enable_bot'			=> 'set_enable_bot',
			'menu_enable_new_user'		=> 'set_enable_new_user',
			'menu_enable_user'			=> 'set_enable_user',
			'menu_enable_mod'			=> 'set_enable_mod',
			'menu_enable_global_mod'	=> 'set_enable_global_mod',
			'menu_enable_admin'			=> 'set_enable_admin',
			'menu_enable_founder'		=> 'set_enable_founder'
		];

		// Go through the basic fields and set them to our data array
		foreach ($fields as $field => $type)
		{
			// The data wasn't sent to us
			if (!isset($data[$field]))
			{
				throw new \vinabb\web\exceptions\invalid_argument([$field, 'FIELD_MISSING']);
			}

			// If the type is a method on this class, call it
			if (method_exists($this, $type))
			{
				$this->$type($data[$field]);
			}
			else
			{
				// settype() passes values by reference
				$value = $data[$field];

				// We're using settype() to enforce data types
				settype($value, $type);

				$this->data[$field] = $value;
			}
		}

		// Some fields must be >= 0
		$validate_unsigned = ['menu_id', 'parent_id', 'left_id', 'right_id', 'menu_type'];

		foreach ($validate_unsigned as $field)
		{
			// If the data is less than 0, it's not unsigned and we'll throw an exception
			if ($this->data[$field] < 0)
			{
				throw new \vinabb\web\exceptions\out_of_bounds($field);
			}
		}

		return $this;
	}

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return menu_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['menu_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('menu_id');
		}

		// Resets values required for the nested set system
		$this->data['parent_id'] = 0;
		$this->data['left_id'] = 0;
		$this->data['right_id'] = 0;
		$this->data['menu_parents'] = '';

		// Make extra sure there is no ID set
		unset($this->data['menu_id']);

		$sql = 'INSERT INTO ' . $this->table_name . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['menu_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return menu_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['menu_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('menu_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['menu_id' => null]);

		$sql = 'UPDATE ' . $this->table_name . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE menu_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the menu_id
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['menu_id']) ? (int) $this->data['menu_id'] : 0;
	}

	/**
	* Get the parent_id
	*
	* @return int
	*/
	public function get_parent_id()
	{
		return isset($this->data['parent_id']) ? (int) $this->data['parent_id'] : 0;
	}

	/**
	* Get the left_id for the tree
	*
	* @return int
	*/
	public function get_left_id()
	{
		return isset($this->data['left_id']) ? (int) $this->data['left_id'] : 0;
	}

	/**
	* Get the right_id for the tree
	*
	* @return int
	*/
	public function get_right_id()
	{
		return isset($this->data['right_id']) ? (int) $this->data['right_id'] : 0;
	}

	/**
	* Get the menu title
	*
	* @return string
	*/
	public function get_name()
	{
		return isset($this->data['menu_name']) ? (string) $this->data['menu_name'] : '';
	}

	/**
	* Set the menu title
	*
	* @param string				$text	Menu title
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['menu_name', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_MENU_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['menu_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['menu_name'] = $text;

		return $this;
	}

	/**
	* Get the Vietnamese menu title
	*
	* @return string
	*/
	public function get_name_vi()
	{
		return isset($this->data['menu_name_vi']) ? (string) $this->data['menu_name_vi'] : '';
	}

	/**
	* Set the Vietnamese menu title
	*
	* @param string				$text	Vietnamese menu title
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_vi($text)
	{
		$text = (string) $text;

		// Check the max length
		if (truncate_string($text, constants::MAX_MENU_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['menu_name_vi', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['menu_name_vi'] = $text;

		return $this;
	}

	/**
	* Get menu type
	*
	* @return int
	*/
	public function get_type()
	{
		return isset($this->data['menu_type']) ? (int) $this->data['menu_type'] : 0;
	}

	/**
	* Set menu type
	*
	* @param int				$value	Menu type
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_type($value)
	{
		if (!isset($this->data['menu_type']))
		{
			$this->data['menu_type'] = (int) $value;
		}

		return $this;
	}

	/**
	* Get menu icon
	*
	* @return string
	*/
	public function get_icon()
	{
		return isset($this->data['menu_icon']) ? (string) $this->data['menu_icon'] : '';
	}

	/**
	* Set menu icon
	*
	* @param string				$text	Menu icon
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_icon($text)
	{
		if (!isset($this->data['menu_icon']))
		{
			$this->data['menu_icon'] = (string) $text;
		}

		return $this;
	}

	/**
	* Get menu data
	*
	* @return string
	*/
	public function get_data()
	{
		return isset($this->data['menu_data']) ? (string) $this->data['menu_data'] : '';
	}

	/**
	* Set menu data
	*
	* @param string				$text	Menu data
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_data($text)
	{
		if (!isset($this->data['menu_data']))
		{
			$this->data['menu_data'] = (string) $text;
		}

		return $this;
	}

	/**
	* Get menu open target setting
	*
	* @return bool
	*/
	public function get_target()
	{
		return isset($this->data['menu_target']) ? (bool) $this->data['menu_target'] : false;
	}

	/**
	* Set menu open target setting
	*
	* @param bool				$value	Config value
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_target($value)
	{
		if (!isset($this->data['menu_target']))
		{
			$this->data['menu_target'] = (bool) $value;
		}

		return $this;
	}

	/**
	* Get menu display setting for guests
	*
	* @return bool
	*/
	public function get_enable_guest()
	{
		return isset($this->data['menu_enable_guest']) ? (bool) $this->data['menu_enable_guest'] : true;
	}

	/**
	* Set menu display setting for guests
	*
	* @param bool				$value	Config value
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_guest($value)
	{
		if (!isset($this->data['menu_enable_guest']))
		{
			$this->data['menu_enable_guest'] = (bool) $value;
		}

		return $this;
	}

	/**
	* Get menu display setting for bots
	*
	* @return bool
	*/
	public function get_enable_bot()
	{
		return isset($this->data['menu_enable_bot']) ? (bool) $this->data['menu_enable_bot'] : true;
	}

	/**
	* Set menu display setting for bots
	*
	* @param bool				$value	Config value
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_bot($value)
	{
		if (!isset($this->data['menu_enable_bot']))
		{
			$this->data['menu_enable_bot'] = (bool) $value;
		}

		return $this;
	}

	/**
	* Get menu display setting for newly registered users
	*
	* @return bool
	*/
	public function get_enable_new_user()
	{
		return isset($this->data['menu_enable_new_user']) ? (bool) $this->data['menu_enable_new_user'] : true;
	}

	/**
	* Set menu display setting for newly registered users
	*
	* @param bool				$value	Config value
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_new_user($value)
	{
		if (!isset($this->data['menu_enable_new_user']))
		{
			$this->data['menu_enable_new_user'] = (bool) $value;
		}

		return $this;
	}

	/**
	* Get menu display setting for registered users
	*
	* @return bool
	*/
	public function get_enable_user()
	{
		return isset($this->data['menu_enable_user']) ? (bool) $this->data['menu_enable_user'] : true;
	}

	/**
	* Set menu display setting for registered users
	*
	* @param bool				$value	Config value
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_user($value)
	{
		if (!isset($this->data['menu_enable_user']))
		{
			$this->data['menu_enable_user'] = (bool) $value;
		}

		return $this;
	}

	/**
	* Get menu display setting for moderators
	*
	* @return bool
	*/
	public function get_enable_mod()
	{
		return isset($this->data['menu_enable_mod']) ? (bool) $this->data['menu_enable_mod'] : true;
	}

	/**
	* Set menu display setting for moderators
	*
	* @param bool				$value	Config value
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_mod($value)
	{
		if (!isset($this->data['menu_enable_mod']))
		{
			$this->data['menu_enable_mod'] = (bool) $value;
		}

		return $this;
	}

	/**
	* Get menu display setting for global moderators
	*
	* @return bool
	*/
	public function get_enable_global_mod()
	{
		return isset($this->data['menu_enable_global_mod']) ? (bool) $this->data['menu_enable_global_mod'] : true;
	}

	/**
	* Set menu display setting for global moderators
	*
	* @param bool				$value	Config value
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_global_mod($value)
	{
		if (!isset($this->data['menu_enable_global_mod']))
		{
			$this->data['menu_enable_global_mod'] = (bool) $value;
		}

		return $this;
	}

	/**
	* Get menu display setting for administrators
	*
	* @return bool
	*/
	public function get_enable_admin()
	{
		return isset($this->data['menu_enable_admin']) ? (bool) $this->data['menu_enable_admin'] : true;
	}

	/**
	* Set menu display setting for administrators
	*
	* @param bool				$value	Config value
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_admin($value)
	{
		if (!isset($this->data['menu_enable_admin']))
		{
			$this->data['menu_enable_admin'] = (bool) $value;
		}

		return $this;
	}

	/**
	* Get menu display setting for founders
	*
	* @return bool
	*/
	public function get_enable_founder()
	{
		return isset($this->data['menu_enable_founder']) ? (bool) $this->data['menu_enable_founder'] : true;
	}

	/**
	* Set menu display setting for founders
	*
	* @param bool				$value	Config value
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_founder($value)
	{
		if (!isset($this->data['menu_enable_founder']))
		{
			$this->data['menu_enable_founder'] = (bool) $value;
		}

		return $this;
	}
}
