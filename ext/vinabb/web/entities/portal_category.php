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
* Entity for a single news category
*/
class portal_category implements portal_category_interface
{
	/**
	* Data for this entity
	*
	* @var array
	*	cat_id
	*	parent_id
	*	left_id
	*	right_id
	*	cat_parents
	*	cat_name
	*	cat_name_vi
	*	cat_varname
	*	cat_icon
	*/
	protected $data;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string */
	protected $table_name;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface    $db			Database object
	* @param string                               $table_name	Table name
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $table_name)
	{
		$this->db = $db;
		$this->table_name = $table_name;
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int						$id		Category ID
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE cat_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('cat_id');
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
	* @param array						$data	Data array from the database
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = [];

		// All of our fields
		$fields = [
			'cat_id'		=> 'integer',
			'parent_id'		=> 'integer',
			'left_id'		=> 'integer',
			'right_id'		=> 'integer',
			'cat_parents'	=> 'string',
			'cat_name'		=> 'set_cat_name',
			'cat_name_vi'	=> 'set_cat_name_vi',
			'cat_varname'	=> 'set_cat_varname',
			'cat_icon'		=> 'set_cat_icon'
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
		$validate_unsigned = ['cat_id', 'parent_id', 'left_id', 'right_id'];

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
	* @return portal_category_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['cat_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('cat_id');
		}

		// Resets values required for the nested set system
		$this->data['parent_id'] = 0;
		$this->data['left_id'] = 0;
		$this->data['right_id'] = 0;
		$this->data['cat_parents'] = '';

		// Make extra sure there is no ID set
		unset($this->data['cat_id']);

		$sql = 'INSERT INTO ' . $this->table_name . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['cat_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return portal_category_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['cat_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('cat_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['cat_id' => null]);

		$sql = 'UPDATE ' . $this->table_name . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE cat_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the cat_id
	*
	* @return int cat_id
	*/
	public function get_id()
	{
		return isset($this->data['cat_id']) ? (int) $this->data['cat_id'] : 0;
	}

	/**
	* Get the parent_id
	*
	* @return int parent_id
	*/
	public function get_parent_id()
	{
		return isset($this->data['parent_id']) ? (int) $this->data['parent_id'] : 0;
	}

	/**
	* Get the left_id for the tree
	*
	* @return int left_id
	*/
	public function get_left_id()
	{
		return isset($this->data['left_id']) ? (int) $this->data['left_id'] : 0;
	}

	/**
	* Get the right_id for the tree
	*
	* @return int right_id
	*/
	public function get_right_id()
	{
		return isset($this->data['right_id']) ? (int) $this->data['right_id'] : 0;
	}

	/**
	* Get the category name
	*
	* @return string Category name
	*/
	public function get_name()
	{
		return isset($this->data['cat_name']) ? (string) $this->data['cat_name'] : '';
	}

	/**
	* Set the category name
	*
	* @param string						$name	Category name
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($name)
	{
		$name = (string) $name;

		// This is a required field
		if (empty($name))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_name', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($name, constants::MAX_PORTAL_CAT_NAME) != $name)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['cat_name'] = $name;

		return $this;
	}

	/**
	* Get the Vietnamese category name
	*
	* @return string Vietnamese category name
	*/
	public function get_name_vi()
	{
		return isset($this->data['cat_name_vi']) ? (string) $this->data['cat_name_vi'] : '';
	}

	/**
	* Set the Vietnamese category name
	*
	* @param string						$name	Vietnamese category name
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_vi($name)
	{
		$name = (string) $name;

		// This is a required field
		if (empty($name))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_name_vi', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($name, constants::MAX_PORTAL_CAT_NAME) != $name)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_name_vi', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['cat_name_vi'] = $name;

		return $this;
	}

	/**
	* Get the category varname
	*
	* @return string Category varname
	*/
	public function get_varname()
	{
		return isset($this->data['cat_varname']) ? (string) $this->data['cat_varname'] : '';
	}

	/**
	* Set the category varname
	*
	* @param int						$varname	Category varname
	* @return portal_category_interface	$this		Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_varname($varname)
	{
		$varname = strtolower($varname);

		// This is a required field
		if (empty($varname))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_varname', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($varname, constants::MAX_PORTAL_CAT_VARNAME) != $varname)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_varname', 'TOO_LONG']);
		}

		// Check invalid characters
		if (!preg_match('#^[a-z0-9-]+$#', $varname))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_varname', 'ILLEGAL_CHARACTERS']);
		}

		// This field value must be unique
		if (!$this->get_id() || ($this->get_id() && $this->get_varname() !== '' && $this->get_varname() != $varname))
		{
			$sql = 'SELECT 1
				FROM ' . $this->table_name . "
				WHERE cat_varname = '" . $this->db->sql_escape($varname) . "'
					AND cat_id <> " . $this->get_id();
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row)
			{
				throw new \vinabb\web\exceptions\unexpected_value(['cat_varname', 'NOT_UNIQUE']);
			}
		}

		// Set the value on our data array
		$this->data['cat_varname'] = $varname;

		return $this;
	}

	/**
	* Get the category icon
	*
	* @return string Category icon
	*/
	public function get_icon()
	{
		return isset($this->data['cat_icon']) ? (string) $this->data['cat_icon'] : '';
	}

	/**
	* Set the category icon
	*
	* @param int						$icon	Category icon
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_icon($icon)
	{
		if (!isset($this->data['cat_icon']))
		{
			$this->data['cat_icon'] = (string) $icon;
		}

		return $this;
	}
}
