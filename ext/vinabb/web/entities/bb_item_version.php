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
* Entity for a single phpBB resource item version
*/
class bb_item_version implements bb_item_version_interface
{
	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \phpbb\extension\manager $ext_manager */
	protected $ext_manager;

	/** @var \phpbb\path_helper $path_helper */
	protected $path_helper;

	/** @var string $table_name */
	protected $table_name;

	/** @var string $ext_root_path */
	protected $ext_root_path;

	/** @var string $ext_web_path */
	protected $ext_web_path;

	/** @var array $data */
	protected $data;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface	$db				Database object
	* @param \phpbb\extension\manager			$ext_manager	Extension manager
	* @param \phpbb\path_helper					$path_helper	Path helper
	* @param string								$table_name		Table name
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\extension\manager $ext_manager, \phpbb\path_helper $path_helper, $table_name)
	{
		$this->db = $db;
		$this->ext_manager = $ext_manager;
		$this->path_helper = $path_helper;
		$this->table_name = $table_name;

		$this->ext_root_path = $this->ext_manager->get_extension_path('vinabb/web', true);
		$this->ext_web_path = $this->path_helper->update_web_root_path($this->ext_root_path);
	}

	/**
	* Data for this entity
	*
	* @return array
	*/
	protected function prepare_data()
	{
		return [
			'item_id'			=> 'integer',
			'phpbb_branch'		=> 'string',
			'phpbb_version'		=> 'string',
			'item_version'		=> 'string',
			'item_file'			=> 'string',
			'item_price'		=> 'integer',
			'item_downloads'	=> 'integer'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int						$id		Item ID
	* @param string						$branch	phpBB branch
	* @return bb_item_version_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id, $branch)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE item_id = ' . (int) $id . "
				AND phpbb_branch = '" . $this->db->sql_escape($branch) . "'";
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('item_id/phpbb_branch');
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
	* @return bb_item_version_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @param int						$id		Item ID
	* @param string						$branch	phpBB branch
	* @return bb_item_version_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert($id, $branch)
	{
		// The entity already exists
		if (!empty($this->data['item_id']) || $this->data['phpbb_branch'] != '')
		{
			throw new \vinabb\web\exceptions\out_of_bounds('item_id/phpbb_branch');
		}

		// Add the item ID to the data array
		$this->data['item_id'] = (int) $id;

		// Add the phpBB branch to the data array
		$this->data['phpbb_branch'] = (string) $branch;

		$sql = 'INSERT INTO ' . $this->table_name . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return bb_item_version_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['item_id']) || $this->data['phpbb_branch'] == '')
		{
			throw new \vinabb\web\exceptions\out_of_bounds('item_id/phpbb_branch');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['item_id' => null, 'phpbb_branch' => null]);

		$sql = 'UPDATE ' . $this->table_name . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE item_id = ' . $this->get_id() . "
				AND phpbb_branch = '" .  $this->db->sql_escape($this->get_phpbb_branch()) . "'";
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the item ID
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['item_id']) ? (int) $this->data['item_id'] : 0;
	}

	/**
	* Get the phpBB branch
	*
	* @return string
	*/
	public function get_phpbb_branch()
	{
		return isset($this->data['phpbb_branch']) ? (string) $this->data['phpbb_branch'] : '';
	}

	/**
	* Get the phpBB version
	*
	* @return string
	*/
	public function get_phpbb_version()
	{
		return isset($this->data['phpbb_version']) ? (string) $this->data['phpbb_version'] : '';
	}

	/**
	* Set the phpBB version
	*
	* @param string						$text	phpBB version
	* @return bb_item_version_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_phpbb_version($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['phpbb_version', 'EMPTY']);
		}

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['phpbb_version', 'TOO_LONG']);
		}

		// Check invalid version numbers
		if (!preg_match(constants::REGEX_VERSION, $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['phpbb_version', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['phpbb_version'] = $text;

		return $this;
	}

	/**
	* Get the item version
	*
	* @return string
	*/
	public function get_version()
	{
		return isset($this->data['item_version']) ? (string) $this->data['item_version'] : '';
	}

	/**
	* Set the item version
	*
	* @param string						$text	Item version
	* @return bb_item_version_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_version($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_version', 'EMPTY']);
		}

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_version', 'TOO_LONG']);
		}

		// Check invalid version numbers
		if (!preg_match(constants::REGEX_VERSION, $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_version', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['item_version'] = $text;

		return $this;
	}

	/**
	* Get the item's downloadable file
	*
	* @param bool	$real_path	True to return the path on filesystem, false to return the web access path
	* @param bool	$full_path	True to return the path + filename, false to return only filename
	* @return string
	*/
	public function get_file($real_path = false, $full_path = true)
	{
		$path = $full_path ? ($real_path ? $this->ext_root_path : $this->ext_web_path) . constants::DIR_BB_FILES : '';

		return !empty($this->data['item_file']) ? (string) $path . $this->data['item_file'] : '';
	}

	/**
	* Set the item's downloadable file
	*
	* @param string						$text	Item filename
	* @return bb_item_version_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_file($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_file', 'EMPTY']);
		}

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_file', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['item_file'] = $text;

		return $this;
	}

	/**
	* Get the item price
	*
	* @return int
	*/
	public function get_price()
	{
		return isset($this->data['item_price']) ? (int) $this->data['item_price'] : 0;
	}

	/**
	* Get the number of downloads
	*
	* @return int
	*/
	public function get_downloads()
	{
		return isset($this->data['item_downloads']) ? (int) $this->data['item_downloads'] : 0;
	}
}
