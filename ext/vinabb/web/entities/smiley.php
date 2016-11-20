<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Entity for a single smiley
*/
class smiley implements smiley_interface
{
	/**
	* Data for this entity
	*
	* @var array
	*	smiley_id
	*	code
	*	emotion
	*	smiley_url
	*	smiley_width
	*	smiley_height
	*	smiley_order
	*	display_on_posting
	*/
	protected $data;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

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
	* Load the data from the database for an entity
	*
	* @param int				$id		Smiley ID
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . SMILIES_TABLE . '
			WHERE smiley_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('smiley_id');
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
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = [];

		// All of our fields
		$fields = [
			'smiley_id'				=> 'integer',
			'code'					=> 'set_code',
			'emotion'				=> 'set_emotion',
			'smiley_url'			=> 'set_url',
			'smiley_width'			=> 'set_width',
			'smiley_height'			=> 'set_height',
			'smiley_order'			=> 'integer',
			'display_on_posting'	=> 'set_display_on_posting'
		];

		// Go through the basic fields and set them to our data array
		foreach ($fields as $field => $type)
		{
			// The data wasn't sent to us
			if (!isset($data[$field]))
			{
				throw new \vinabb\web\exceptions\invalid_argument([$field, 'EMPTY']);
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
		$validate_unsigned = ['smiley_id', 'smiley_width', 'smiley_height', 'smiley_order'];

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
	* @return smiley_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['smiley_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('smiley_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['smiley_id']);

		$sql = 'INSERT INTO ' . SMILIES_TABLE . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['smiley_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return smiley_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['smiley_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('smiley_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['smiley_id' => null]);

		$sql = 'UPDATE ' . SMILIES_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE smiley_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the lang_id
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['smiley_id']) ? (int) $this->data['smiley_id'] : 0;
	}

	/**
	* Get the smiley code
	*
	* @return string
	*/
	public function get_code()
	{
		return isset($this->data['code']) ? (string) $this->data['code'] : '';
	}

	/**
	* Set the smiley code
	*
	* @param string				$text	Smiley code
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_code($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['code', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, 50) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['code', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['code'] = $text;

		return $this;
	}

	/**
	* Get the smiley emotion
	*
	* @return string
	*/
	public function get_emotion()
	{
		return isset($this->data['emotion']) ? (string) $this->data['emotion'] : '';
	}

	/**
	* Set the smiley emotion
	*
	* @param string				$text	Smiley emotion
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_emotion($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['emotion', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, 50) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['emotion', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['emotion'] = $text;

		return $this;
	}

	/**
	* Get the smiley image file
	*
	* @return string
	*/
	public function get_url()
	{
		return isset($this->data['smiley_url']) ? (string) $this->data['smiley_url'] : '';
	}

	/**
	* Set the smiley image file
	*
	* @param string				$text	Smiley image file
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_url($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['smiley_url', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, 50) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['smiley_url', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['smiley_url'] = $text;

		return $this;
	}

	/**
	* Get the smiley width
	*
	* @return int
	*/
	public function get_width()
	{
		return isset($this->data['smiley_width']) ? (int) $this->data['smiley_width'] : 0;
	}

	/**
	* Set the smiley width
	*
	* @param int				$value	Smiley width
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_width($value)
	{
		$this->data['smiley_width'] = (int) $value;

		return $this;
	}

	/**
	* Get the smiley height
	*
	* @return int
	*/
	public function get_height()
	{
		return isset($this->data['smiley_height']) ? (int) $this->data['smiley_height'] : 0;
	}

	/**
	* Set the smiley height
	*
	* @param int				$value	Smiley height
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_height($value)
	{
		$this->data['smiley_height'] = (int) $value;

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

	/**
	* Set display setting on posting page
	*
	* @param bool				$value	Config value
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_display_on_posting($value)
	{
		$this->data['display_on_posting'] = (bool) $value;

		return $this;
	}
}