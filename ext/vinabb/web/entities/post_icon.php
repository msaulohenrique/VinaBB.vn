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
* Entity for a single post icon
*/
class post_icon implements post_icon_interface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var array */
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
			'icons_id'				=> 'integer',
			'icons_url'				=> 'string',
			'icons_width'			=> 'integer',
			'icons_height'			=> 'integer',
			'icons_alt'				=> 'string',
			'icons_order'			=> 'integer',
			'display_on_posting'	=> 'bool'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int					$id		Icon ID
	* @return post_icon_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . ICONS_TABLE . '
			WHERE icons_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('icons_id');
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
	* @return post_icon_interface	$this	Object for chaining calls: load()->set()->save()
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
			else if ($type != 'string' && $this->data[$field] < 0)
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
	* @return post_icon_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['icons_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('icons_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['icons_id']);

		$sql = 'INSERT INTO ' . ICONS_TABLE . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['icons_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return post_icon_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['icons_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('icons_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['icons_id' => null]);

		$sql = 'UPDATE ' . ICONS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE icons_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the icons_id
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['icons_id']) ? (int) $this->data['icons_id'] : 0;
	}

	/**
	* Get the icon image file
	*
	* @return string
	*/
	public function get_url()
	{
		return isset($this->data['icons_url']) ? (string) $this->data['icons_url'] : '';
	}

	/**
	* Set the icon image file
	*
	* @param string					$text	Icon image file
	* @return post_icon_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_url($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['icons_url', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['icons_url', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['icons_url'] = $text;

		return $this;
	}

	/**
	* Get the icon width
	*
	* @return int
	*/
	public function get_width()
	{
		return isset($this->data['icons_width']) ? (int) $this->data['icons_width'] : 0;
	}

	/**
	* Get the icon height
	*
	* @return int
	*/
	public function get_height()
	{
		return isset($this->data['icons_height']) ? (int) $this->data['icons_height'] : 0;
	}

	/**
	* Get the icon hover text
	*
	* @return string
	*/
	public function get_alt()
	{
		return isset($this->data['icons_alt']) ? (string) $this->data['icons_alt'] : '';
	}

	/**
	* Set the icon hover text
	*
	* @param string					$text	Icon hover text
	* @return post_icon_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_alt($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['icons_alt', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['icons_alt', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['icons_alt'] = $text;

		return $this;
	}

	/**
	* Get display setting on posting page
	*
	* @return bool
	*/
	public function get_display_on_posting()
	{
		return isset($this->data['display_on_posting']) ? (bool) $this->data['display_on_posting'] : true;
	}
}
