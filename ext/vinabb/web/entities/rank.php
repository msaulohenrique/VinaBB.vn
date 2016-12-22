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
* Entity for a single rank
*/
class rank implements rank_interface
{
	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var array $data */
	protected $data;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db Database object
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db)
	{
		$this->db = $db;
	}

	/**
	* Data for this entity
	*
	* @return array
	*/
	protected function prepare_data()
	{
		return [
			'rank_id'		=> 'integer',
			'rank_title'	=> 'string',
			'rank_min'		=> 'integer',
			'rank_special'	=> 'bool',
			'rank_image'	=> 'string'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Rank ID
	* @return rank_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . RANKS_TABLE . '
			WHERE rank_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('rank_id');
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
	* @return rank_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return rank_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['rank_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('rank_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['rank_id']);

		$sql = 'INSERT INTO ' . RANKS_TABLE . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['rank_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return rank_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['rank_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('rank_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['rank_id' => null]);

		$sql = 'UPDATE ' . RANKS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE rank_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the rank ID
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['rank_id']) ? (int) $this->data['rank_id'] : 0;
	}

	/**
	* Get the rank title
	*
	* @return string
	*/
	public function get_title()
	{
		return isset($this->data['rank_title']) ? (string) $this->data['rank_title'] : '';
	}

	/**
	* Set the rank title
	*
	* @param string				$text	Rank title
	* @return rank_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_title($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['rank_title', 'EMPTY']);
		}

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['rank_title', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['rank_title'] = $text;

		return $this;
	}

	/**
	* Get the rank's minimum posts
	*
	* @return int
	*/
	public function get_min()
	{
		return isset($this->data['rank_min']) ? (int) $this->data['rank_min'] : 0;
	}

	/**
	* The rank is special?
	*
	* @return bool
	*/
	public function get_special()
	{
		return isset($this->data['rank_special']) ? (bool) $this->data['rank_special'] : false;
	}

	/**
	* Get the rank image
	*
	* @return string
	*/
	public function get_image()
	{
		return isset($this->data['rank_image']) ? (string) $this->data['rank_image'] : '';
	}

	/**
	* Set the rank image
	*
	* @param string				$text	Rank image file
	* @return rank_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_image($text)
	{
		$text = (string) $text;

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['rank_image', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['rank_image'] = $text;

		return $this;
	}
}
