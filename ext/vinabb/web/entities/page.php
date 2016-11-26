<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

use vinabb\web\entities\sub\page_enable;
use vinabb\web\includes\constants;

/**
* Entity for a single page
*/
class page extends page_enable implements page_interface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vinabb\web\entities\helper\helper_interface */
	protected $entity_helper;

	/** @var string */
	protected $table_name;

	/** @var array */
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
			'page_id'		=> 'integer',
			'page_name'		=> 'string',
			'page_name_vi'	=> 'string',
			'page_varname'	=> 'string',
			'page_desc'		=> 'string',
			'page_desc_vi'	=> 'string',

			// Entity: vinabb\web\entities\sub\page_enable
			'page_enable'				=> 'bool',
			'page_enable_guest'			=> 'bool',
			'page_enable_bot'			=> 'bool',
			'page_enable_new_user'		=> 'bool',
			'page_enable_user'			=> 'bool',
			'page_enable_mod'			=> 'bool',
			'page_enable_global_mod'	=> 'bool',
			'page_enable_admin'			=> 'bool',
			'page_enable_founder'		=> 'bool',

			// Entity: vinabb\web\entities\sub\page_text
			'page_text'				=> 'string',
			'page_text_uid'			=> 'string',
			'page_text_bitfield'	=> 'string',
			'page_text_options'		=> 'integer',
			'page_text_vi'			=> 'string',
			'page_text_vi_uid'		=> 'string',
			'page_text_vi_bitfield'	=> 'string',
			'page_text_vi_options'	=> 'integer'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Page ID
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id = 0)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE page_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('page_id');
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
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['page_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('page_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['page_id']);

		$sql = 'INSERT INTO ' . $this->table_name . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['page_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['page_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('page_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['page_id' => null]);

		$sql = 'UPDATE ' . $this->table_name . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE page_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the page ID
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['page_id']) ? (int) $this->data['page_id'] : 0;
	}

	/**
	* Get the page title
	*
	* @return string
	*/
	public function get_name()
	{
		return isset($this->data['page_name']) ? (string) $this->data['page_name'] : '';
	}

	/**
	* Set the page title
	*
	* @param string				$text	Page title
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_name', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_PAGE_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['page_name'] = $text;

		return $this;
	}

	/**
	* Get the Vietnamese page title
	*
	* @return string
	*/
	public function get_name_vi()
	{
		return isset($this->data['page_name_vi']) ? (string) $this->data['page_name_vi'] : '';
	}

	/**
	* Set the Vietnamese page title
	*
	* @param string				$text	Vietnamese page title
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_vi($text)
	{
		$text = (string) $text;

		// Check the max length
		if (truncate_string($text, constants::MAX_PAGE_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_name_vi', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['page_name_vi'] = $text;

		return $this;
	}

	/**
	* Get the page varname
	*
	* @return string
	*/
	public function get_varname()
	{
		return isset($this->data['page_varname']) ? (string) $this->data['page_varname'] : '';
	}

	/**
	* Set the page varname
	*
	* @param int				$text	Page varname
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_varname($text)
	{
		$text = strtolower($text);

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_varname', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_PAGE_VARNAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_varname', 'TOO_LONG']);
		}

		// Check invalid characters
		if (!preg_match('#^[a-z0-9-]+$#', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_varname', 'INVALID']);
		}

		// This field value must be unique
		if ($this->entity_helper->check_page_varname($text, $this->get_id()))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_varname', 'DUPLICATE', $text]);
		}

		// Set the value on our data array
		$this->data['page_varname'] = $text;

		return $this;
	}

	/**
	* Get the page description
	*
	* @return string
	*/
	public function get_desc()
	{
		return isset($this->data['page_desc']) ? (string) $this->data['page_desc'] : '';
	}

	/**
	* Set the page description
	*
	* @param string				$text	Page description
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc($text)
	{
		$this->data['page_desc'] = (string) $text;

		return $this;
	}

	/**
	* Get the Vietnamese page description
	*
	* @return string
	*/
	public function get_desc_vi()
	{
		return isset($this->data['page_desc_vi']) ? (string) $this->data['page_desc_vi'] : '';
	}

	/**
	* Set the Vietnamese page description
	*
	* @param string				$text	Vietnamese page description
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc_vi($text)
	{
		$this->data['page_desc_vi'] = (string) $text;

		return $this;
	}
}
