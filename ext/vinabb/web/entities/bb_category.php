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
* Entity for a single phpBB resource category
*/
class bb_category implements bb_category_interface
{
	/**
	* Data for this entity
	*
	* @var array
	*	cat_id
	*	bb_type
	*	cat_name
	*	cat_name_vi
	*	cat_varname
	*	cat_desc
	*	cat_desc_vi
	*	cat_icon
	*	cat_order
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
	* @param int					$id		Category ID
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\out_of_bounds
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
			throw new \vinabb\web\exception\out_of_bounds('cat_id');
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
	* @param array					$data	Data array from the database
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\base
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = [];

		// All of our fields
		$fields = [
			'cat_id'		=> 'integer',
			'bb_type'		=> 'integer',
			'cat_name'		=> 'set_cat_name',
			'cat_name_vi'	=> 'set_cat_name_vi',
			'cat_varname'	=> 'set_cat_varname',
			'cat_desc'		=> 'set_cat_desc',
			'cat_desc_vi'	=> 'set_cat_desc_vi',
			'cat_order'		=> 'integer',
			'cat_icon'		=> 'set_cat_icon'
		];

		// Go through the basic fields and set them to our data array
		foreach ($fields as $field => $type)
		{
			// The data wasn't sent to us
			if (!isset($data[$field]))
			{
				throw new \vinabb\web\exception\invalid_argument([$field, 'FIELD_MISSING']);
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
		$validate_unsigned = ['cat_id', 'cat_order'];

		foreach ($validate_unsigned as $field)
		{
			// If the data is less than 0, it's not unsigned and we'll throw an exception
			if ($this->data[$field] < 0)
			{
				throw new \vinabb\web\exception\out_of_bounds($field);
			}
		}

		return $this;
	}

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return bb_category_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['cat_id']))
		{
			throw new \vinabb\web\exception\out_of_bounds('cat_id');
		}

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
	* @return bb_category_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['cat_id']))
		{
			throw new \vinabb\web\exception\out_of_bounds('cat_id');
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
	* Get the bb_type
	*
	* @return int bb_type
	*/
	public function get_bb_type()
	{
		return isset($this->data['bb_type']) ? (int) $this->data['bb_type'] : 0;
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
	* @param string					$name	Category name
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\unexpected_value
	*/
	public function set_name($name)
	{
		$name = (string) $name;

		// This is a required field
		if (empty($name))
		{
			throw new \vinabb\web\exception\unexpected_value(['cat_name', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($name, constants::MAX_PORTAL_CAT_NAME) != $name)
		{
			throw new \vinabb\web\exception\unexpected_value(['cat_name', 'TOO_LONG']);
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
	* @param string					$name	Vietnamese category name
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\unexpected_value
	*/
	public function set_name_vi($name)
	{
		$name = (string) $name;

		// This is a required field
		if (empty($name))
		{
			throw new \vinabb\web\exception\unexpected_value(['cat_name_vi', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($name, constants::MAX_PORTAL_CAT_NAME) != $name)
		{
			throw new \vinabb\web\exception\unexpected_value(['cat_name_vi', 'TOO_LONG']);
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
	* @param int					$varname	Category varname
	* @return bb_category_interface	$this		Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\unexpected_value
	*/
	public function set_varname($varname)
	{
		$varname = strtolower($varname);

		// This is a required field
		if (empty($varname))
		{
			throw new \vinabb\web\exception\unexpected_value(['cat_varname', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($varname, constants::MAX_BB_CAT_VARNAME) != $varname)
		{
			throw new \vinabb\web\exception\unexpected_value(['cat_varname', 'TOO_LONG']);
		}

		// Check invalid characters
		if (!preg_match('#^[a-z0-9-]+$#', $varname))
		{
			throw new \vinabb\web\exception\unexpected_value(['cat_varname', 'ILLEGAL_CHARACTERS']);
		}

		// This field value must be unique
		if (!$this->get_id() || !$this->get_bb_type() || ($this->get_id() && $this->get_bb_type() && $this->get_varname() !== '' && $this->get_varname() != $varname))
		{
			$sql = 'SELECT 1
				FROM ' . $this->table_name . "
				WHERE cat_varname = '" . $this->db->sql_escape($varname) . "'
					AND bb_type = " . $this->get_bb_type() . '
					AND cat_id <> ' . $this->get_id();
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row)
			{
				throw new \vinabb\web\exception\unexpected_value(['cat_varname', 'NOT_UNIQUE']);
			}
		}

		// Set the value on our data array
		$this->data['cat_varname'] = $varname;

		return $this;
	}

	/**
	* Get the category description
	*
	* @return string Category description
	*/
	public function get_desc()
	{
		return isset($this->data['cat_desc']) ? (string) $this->data['cat_desc'] : '';
	}

	/**
	* Set the category description
	*
	* @param string					$desc	Category description
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\unexpected_value
	*/
	public function set_desc($desc)
	{
		$desc = (string) $desc;

		// Check the max length
		if (truncate_string($desc, constants::MAX_BB_CAT_DESC) != $desc)
		{
			throw new \vinabb\web\exception\unexpected_value(['cat_desc', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['cat_desc'] = $desc;

		return $this;
	}

	/**
	* Get the Vietnamese category description
	*
	* @return string Vietnamese category description
	*/
	public function get_desc_vi()
	{
		return isset($this->data['cat_desc_vi']) ? (string) $this->data['cat_desc_vi'] : '';
	}

	/**
	* Set the Vietnamese category description
	*
	* @param string					$desc	Vietnamese category description
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\unexpected_value
	*/
	public function set_desc_vi($desc)
	{
		$desc = (string) $desc;

		// Check the max length
		if (truncate_string($desc, constants::MAX_BB_CAT_DESC) != $desc)
		{
			throw new \vinabb\web\exception\unexpected_value(['cat_desc_vi', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['cat_desc_vi'] = $desc;

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
	* @param int					$icon	Category icon
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
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
