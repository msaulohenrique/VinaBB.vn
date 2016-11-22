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
* Entity for a single language
*/
class language implements language_interface
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
			'lang_id'			=> 'integer',
			'lang_iso'			=> 'string',
			'lang_dir'			=> 'string',
			'lang_english_name'	=> 'string',
			'lang_local_name'	=> 'string',
			'lang_author'		=> 'string'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param string					$iso	2-letter language ISO code
	* @return language_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($iso)
	{
		$sql = 'SELECT *
			FROM ' . LANG_TABLE . "
			WHERE lang_iso = '" . $this->db->sql_escape($iso);
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('lang_iso');
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
	* @return language_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return language_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['lang_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('lang_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['lang_id']);

		$sql = 'INSERT INTO ' . LANG_TABLE . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['lang_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return language_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['lang_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('lang_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['lang_id' => null]);

		$sql = 'UPDATE ' . LANG_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE lang_id = ' . $this->get_id();
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
		return isset($this->data['lang_id']) ? (int) $this->data['lang_id'] : 0;
	}

	/**
	* Get the language ISO
	*
	* @return string
	*/
	public function get_iso()
	{
		return isset($this->data['lang_iso']) ? (string) $this->data['lang_iso'] : '';
	}

	/**
	* Set the language ISO
	*
	* @param string					$text	Language ISO
	* @return language_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_iso($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['lang_iso', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, 30) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['lang_iso', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['lang_iso'] = $text;

		return $this;
	}

	/**
	* Get the language directory name
	*
	* @return string
	*/
	public function get_dir()
	{
		return isset($this->data['lang_dir']) ? (string) $this->data['lang_dir'] : '';
	}

	/**
	* Set the language directory name
	*
	* @param string					$text	Language directory name
	* @return language_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_dir($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['lang_dir', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, 30) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['lang_dir', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['lang_dir'] = $text;

		return $this;
	}

	/**
	* Get the English language name
	*
	* @return string
	*/
	public function get_english_name()
	{
		return isset($this->data['lang_english_name']) ? (string) $this->data['lang_english_name'] : '';
	}

	/**
	* Set the English language name
	*
	* @param string					$text	English language name
	* @return language_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_english_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['lang_english_name', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, 100) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['lang_english_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['lang_english_name'] = $text;

		return $this;
	}

	/**
	* Get the language's native name
	*
	* @return string
	*/
	public function get_local_name()
	{
		return isset($this->data['lang_local_name']) ? (string) $this->data['lang_local_name'] : '';
	}

	/**
	* Set the language's native name
	*
	* @param string					$text	Language's native name
	* @return language_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_local_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['lang_local_name', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['lang_local_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['lang_local_name'] = $text;

		return $this;
	}

	/**
	* Get the translator name
	*
	* @return string
	*/
	public function get_author()
	{
		return isset($this->data['lang_author']) ? (string) $this->data['lang_author'] : '';
	}

	/**
	* Set the translator name
	*
	* @param string					$text	Translator name
	* @return language_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_author($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['lang_author', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['lang_author', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['lang_author'] = $text;

		return $this;
	}
}
