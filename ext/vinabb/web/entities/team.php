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
* Entity for a single team
*/
class team implements team_interface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vinabb\web\entities\helper\helper_interface */
	protected $entity_helper;

	/** @var array */
	protected $data;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface				$db				Database object
	 * @param \vinabb\web\entities\helper\helper_interface	$entity_helper	Entity helper
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \vinabb\web\entities\helper\helper_interface $entity_helper)
	{
		$this->db = $db;
		$this->entity_helper = $entity_helper;
	}

	/**
	* Data for this entity
	*
	* @return array
	*/
	protected function prepare_data()
	{
		return [
			'teampage_id'		=> 'integer',
			'group_id'			=> 'integer',
			'teampage_name'		=> 'string',
			'teampage_position'	=> 'integer',
			'teampage_parent'	=> 'integer'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Team ID
	* @return team_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . TEAMPAGE_TABLE . '
			WHERE teampage_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('teampage_id');
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
	* @return team_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return team_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['teampage_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('teampage_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['teampage_id']);

		$sql = 'INSERT INTO ' . TEAMPAGE_TABLE . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['teampage_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return team_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['teampage_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('teampage_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['teampage_id' => null]);

		$sql = 'UPDATE ' . TEAMPAGE_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE teampage_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the team ID
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['teampage_id']) ? (int) $this->data['teampage_id'] : 0;
	}

	/**
	* Get the group ID
	*
	* @return int
	*/
	public function get_group_id()
	{
		return isset($this->data['group_id']) ? (int) $this->data['group_id'] : 0;
	}

	/**
	* Set the group ID
	*
	* @param int				$id		Group ID
	* @return team_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_group_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if (!$id)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['group_id', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_group_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['group_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['group_id'] = $id;

		return $this;
	}

	/**
	* Get the team name
	*
	* @return string
	*/
	public function get_name()
	{
		return isset($this->data['teampage_name']) ? (string) $this->data['teampage_name'] : '';
	}

	/**
	* Set the team name
	*
	* @param string				$text	Team name
	* @return team_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['teampage_name', 'EMPTY']);
		}

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['teampage_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['teampage_name'] = $text;

		return $this;
	}

	/**
	* Get the team category
	*
	* @return int
	*/
	public function get_parent()
	{
		return isset($this->data['teampage_parent']) ? (int) $this->data['teampage_parent'] : 0;
	}

	/**
	* Set the team category
	*
	* @param int				$id		Team ID
	* @return team_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_parent($id)
	{
		$id = (int) $id;

		// Check existing team
		if ($id && !$this->entity_helper->check_team_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['teampage_parent', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['teampage_parent'] = $id;

		return $this;
	}
}
