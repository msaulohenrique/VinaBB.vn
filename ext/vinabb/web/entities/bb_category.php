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
	* @param array					$data	Data array from the database
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = [];

		// All of our fields
		$fields = [
			'cat_id'		=> 'integer',
			'bb_type'		=> 'integer',
			'cat_name'		=> 'set_name',
			'cat_name_vi'	=> 'set_name_vi',
			'cat_varname'	=> 'set_varname',
			'cat_desc'		=> 'set_desc',
			'cat_desc_vi'	=> 'set_desc_vi',
			'cat_icon'		=> 'set_icon',
			'cat_order'		=> 'integer'
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
		$validate_unsigned = ['cat_id', 'bb_type', 'cat_order'];

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
	* @param int $bb_type phpBB resource type
	* @return bb_category_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert($bb_type)
	{
		// The entity already exists
		if (!empty($this->data['cat_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('cat_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['cat_id']);

		// Add the bb_type to the data array
		$this->data['bb_type'] = (int) $bb_type;

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
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['cat_id']) ? (int) $this->data['cat_id'] : 0;
	}

	/**
	* Get the bb_type
	*
	* @return int
	*/
	public function get_bb_type()
	{
		return isset($this->data['bb_type']) ? (int) $this->data['bb_type'] : 0;
	}

	/**
	* Get the category name
	*
	* @return string
	*/
	public function get_name()
	{
		return isset($this->data['cat_name']) ? (string) $this->data['cat_name'] : '';
	}

	/**
	* Set the category name
	*
	* @param string					$text	Category name
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if (empty($text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_name', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_PORTAL_CAT_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['cat_name'] = $text;

		return $this;
	}

	/**
	* Get the Vietnamese category name
	*
	* @return string
	*/
	public function get_name_vi()
	{
		return isset($this->data['cat_name_vi']) ? (string) $this->data['cat_name_vi'] : '';
	}

	/**
	* Set the Vietnamese category name
	*
	* @param string					$text	Vietnamese category name
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_vi($text)
	{
		$text = (string) $text;

		// Check the max length
		if (truncate_string($text, constants::MAX_PORTAL_CAT_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_name_vi', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['cat_name_vi'] = $text;

		return $this;
	}

	/**
	* Get the category varname
	*
	* @return string
	*/
	public function get_varname()
	{
		return isset($this->data['cat_varname']) ? (string) $this->data['cat_varname'] : '';
	}

	/**
	* Set the category varname
	*
	* @param int					$text	Category varname
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_varname($text)
	{
		$text = strtolower($text);

		// This is a required field
		if (empty($text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_varname', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_BB_CAT_VARNAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_varname', 'TOO_LONG']);
		}

		// Check invalid characters
		if (!preg_match('#^[a-z0-9-]+$#', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_varname', 'ILLEGAL_CHARACTERS']);
		}

		// This field value must be unique
		if (!$this->get_id() || !$this->get_bb_type() || ($this->get_id() && $this->get_bb_type() && $this->get_varname() !== '' && $this->get_varname() != $text))
		{
			$sql = 'SELECT 1
				FROM ' . $this->table_name . "
				WHERE cat_varname = '" . $this->db->sql_escape($text) . "'
					AND bb_type = " . $this->get_bb_type() . '
					AND cat_id <> ' . $this->get_id();
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row)
			{
				throw new \vinabb\web\exceptions\unexpected_value(['cat_varname', 'NOT_UNIQUE']);
			}
		}

		// Set the value on our data array
		$this->data['cat_varname'] = $text;

		return $this;
	}

	/**
	* Get the category description
	*
	* @return string
	*/
	public function get_desc()
	{
		return isset($this->data['cat_desc']) ? (string) $this->data['cat_desc'] : '';
	}

	/**
	* Set the category description
	*
	* @param string					$text	Category description
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_desc($text)
	{
		$text = (string) $text;

		// Check the max length
		if (truncate_string($text, constants::MAX_BB_CAT_DESC) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_desc', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['cat_desc'] = $text;

		return $this;
	}

	/**
	* Get the Vietnamese category description
	*
	* @return string
	*/
	public function get_desc_vi()
	{
		return isset($this->data['cat_desc_vi']) ? (string) $this->data['cat_desc_vi'] : '';
	}

	/**
	* Set the Vietnamese category description
	*
	* @param string					$text	Vietnamese category description
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_desc_vi($text)
	{
		$text = (string) $text;

		// Check the max length
		if (truncate_string($text, constants::MAX_BB_CAT_DESC) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_desc_vi', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['cat_desc_vi'] = $text;

		return $this;
	}

	/**
	* Get the category icon
	*
	* @return string
	*/
	public function get_icon()
	{
		return isset($this->data['cat_icon']) ? (string) $this->data['cat_icon'] : '';
	}

	/**
	* Set the category icon
	*
	* @param int					$text	Category icon
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_icon($text)
	{
		if (!isset($this->data['cat_icon']))
		{
			$this->data['cat_icon'] = (string) $text;
		}

		return $this;
	}
}
