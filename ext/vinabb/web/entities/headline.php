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
* Entity for a single headline
*/
class headline implements headline_interface
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
			'headline_id'		=> 'integer',
			'headline_lang'		=> 'string',
			'headline_name'		=> 'string',
			'headline_desc'		=> 'string',
			'headline_img'		=> 'string',
			'headline_url'		=> 'string',
			'headline_order'	=> 'integer'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Headline ID
	* @return headline_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id = 0)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE headline_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('headline_id');
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
	* @return headline_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
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
			// We love unsigned numbers
			else if ($type != 'string' && $data[$field] < 0)
			{
				throw new \vinabb\web\exceptions\out_of_bounds($field);
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
	* @return headline_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['headline_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('headline_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['headline_id']);

		$sql = 'INSERT INTO ' . $this->table_name . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['headline_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return headline_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['headline_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('headline_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['headline_id' => null]);

		$sql = 'UPDATE ' . $this->table_name . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE headline_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the headline_id
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['headline_id']) ? (int) $this->data['headline_id'] : 0;
	}

	/**
	* Get the headline language
	*
	* @return string
	*/
	public function get_lang()
	{
		return isset($this->data['headline_lang']) ? (string) $this->data['headline_lang'] : '';
	}

	/**
	* Set the headline language
	*
	* @param string					$text	2-letter language ISO code
	* @return headline_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_lang($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['headline_lang', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_lang_iso($text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['headline_lang', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['headline_lang'] = $text;

		return $this;
	}

	/**
	* Get the headline title
	*
	* @return string
	*/
	public function get_name()
	{
		return isset($this->data['headline_name']) ? (string) $this->data['headline_name'] : '';
	}

	/**
	* Set the headline title
	*
	* @param string				$text	Headline title
	* @return headline_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['headline_name', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_HEADLINE_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['headline_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['headline_name'] = $text;

		return $this;
	}

	/**
	* Get the headline description
	*
	* @return string
	*/
	public function get_desc()
	{
		return isset($this->data['headline_desc']) ? (string) $this->data['headline_desc'] : '';
	}

	/**
	* Set the headline description
	*
	* @param string					$text	Headline description
	* @return headline_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_desc($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['headline_desc', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_HEADLINE_DESC) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['headline_desc', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['headline_desc'] = $text;

		return $this;
	}

	/**
	* Get the headline image
	*
	* @return string
	*/
	public function get_img()
	{
		return isset($this->data['headline_img']) ? (string) $this->data['headline_img'] : '';
	}

	/**
	* Set the headline image
	*
	* @param string					$text	Headline image URL
	* @return headline_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_img($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['headline_img', 'EMPTY']);
		}

		// Set the value on our data array
		$this->data['headline_img'] = $text;

		return $this;
	}

	/**
	* Get the headline link
	*
	* @return string
	*/
	public function get_url()
	{
		return isset($this->data['headline_url']) ? (string) htmlspecialchars_decode($this->data['headline_url']) : '';
	}

	/**
	* Set the headline link
	*
	* @param string					$text	Headline URL
	* @return headline_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_url($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['headline_url', 'EMPTY']);
		}

		// Checking for valid URL
		if (filter_var($text, FILTER_VALIDATE_URL) === false)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['headline_url', 'INVALID_URL']);
		}

		// Set the value on our data array
		$this->data['headline_url'] = $text;

		return $this;
	}
}
