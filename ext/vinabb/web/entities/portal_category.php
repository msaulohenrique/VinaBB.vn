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
	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \vinabb\web\entities\helper\helper_interface $entity_helper */
	protected $entity_helper;

	/** @var string $table_name */
	protected $table_name;

	/** @var array $data */
	protected $data;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface				$db				Database object
	* @param \vinabb\web\entities\helper\helper_interface	$entity_helper	Entity helper
	* @param string											$table_name		Table name
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \vinabb\web\entities\helper\helper_interface $entity_helper, $table_name)
	{
		$this->db = $db;
		$this->entity_helper = $entity_helper;
		$this->table_name = $table_name;
	}

	/**
	* Data for this entity
	*
	* @return array
	*/
	protected function prepare_data()
	{
		return [
			'cat_id'		=> 'integer',
			'parent_id'		=> 'integer',
			'left_id'		=> 'integer',
			'right_id'		=> 'integer',
			'cat_parents'	=> 'string',
			'cat_name'		=> 'string',
			'cat_name_vi'	=> 'string',
			'cat_varname'	=> 'string',
			'cat_icon'		=> 'string'
		];
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
	* Get the category ID
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['cat_id']) ? (int) $this->data['cat_id'] : 0;
	}

	/**
	* Get the parent category ID
	*
	* @return int
	*/
	public function get_parent_id()
	{
		return isset($this->data['parent_id']) ? (int) $this->data['parent_id'] : 0;
	}

	/**
	* Set the parent category ID
	*
	* @param int						$id		Parent ID
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_parent_id($id)
	{
		$id = (int) $id;

		// Check existing category
		if ($id && !$this->entity_helper->check_portal_cat_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['parent_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['parent_id'] = $id;

		return $this;
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
	* @param string						$text	Category name
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_name', 'EMPTY']);
		}

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_PORTAL_CAT_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_name', 'TOO_LONG']);
		}

		// This field value must be unique
		if ($this->entity_helper->check_portal_cat_name($text, $this->get_id()))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_name', 'DUPLICATE', $text]);
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
	* @param string						$text	Vietnamese category name
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_vi($text)
	{
		$text = (string) $text;

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_PORTAL_CAT_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_name_vi', 'TOO_LONG']);
		}

		// This field value must be unique
		if ($this->entity_helper->check_portal_cat_name_vi($text, $this->get_id()))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_name', 'DUPLICATE', $text]);
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
	* @param string						$text	Category varname
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_varname($text)
	{
		$text = strtolower($text);

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_varname', 'EMPTY']);
		}

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_PORTAL_CAT_VARNAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_varname', 'TOO_LONG']);
		}

		// Check invalid characters
		if (!preg_match(constants::REGEX_VARNAME, $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_varname', 'INVALID']);
		}

		// This field value must be unique
		if ($this->entity_helper->check_portal_cat_varname($text, $this->get_id()))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_varname', 'DUPLICATE', $text]);
		}

		// Set the value on our data array
		$this->data['cat_varname'] = $text;

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
	* @param string						$text	Category icon
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_icon($text)
	{
		$text = (string) $text;

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_icon', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['cat_icon'] = $text;

		return $this;
	}
}
