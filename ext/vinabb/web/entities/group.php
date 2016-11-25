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
* Entity for a single group
*/
class group extends \vinabb\web\entities\abs\group_profile implements group_interface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vinabb\web\entities\helper\helper_interface */
	protected $entity_helper;

	/** @var array */
	protected $data;

	/**
	* Constructor
	*
	* @param \phpbb\config\config							$config			Config object
	* @param \phpbb\db\driver\driver_interface				$db				Database object
	* @param \vinabb\web\entities\helper\helper_interface	$entity_helper	Entity helper
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \vinabb\web\entities\helper\helper_interface $entity_helper)
	{
		$this->config = $config;
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
			'group_id'				=> 'integer',
			'group_name'			=> 'string',
			'forum_type'			=> 'integer',
			'group_founder_manage'	=> 'bool',
			'group_skip_auth'		=> 'bool',
			'group_display'			=> 'bool',
			'group_receive_pm'		=> 'bool',
			'group_sig_chars'		=> 'integer',
			'group_message_limit'	=> 'integer',
			'group_legend'			=> 'integer',
			'group_max_recipients'	=> 'integer',

			// Entity: vinabb\web\entities\abs\group_profile
			'group_avatar'			=> 'string',
			'group_avatar_type'		=> 'string',
			'group_avatar_width'	=> 'integer',
			'group_avatar_height'	=> 'integer',
			'group_rank'			=> 'integer',
			'group_colour'			=> 'string',

			// Entity: vinabb\web\entities\abs\group_desc
			'group_desc'			=> 'string',
			'group_desc_uid'		=> 'string',
			'group_desc_bitfield'	=> 'string',
			'group_desc_options'	=> 'integer'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Group ID
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . GROUPS_TABLE . '
			WHERE group_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('group_id');
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
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return group_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['group_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('group_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['group_id']);

		$sql = 'INSERT INTO ' . GROUPS_TABLE . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['group_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return group_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['group_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('group_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['group_id' => null]);

		$sql = 'UPDATE ' . GROUPS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE group_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the forum_id
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['group_id']) ? (int) $this->data['group_id'] : 0;
	}

	/**
	* Get the forum name
	*
	* @return string
	*/
	public function get_name()
	{
		return isset($this->data['group_name']) ? (string) $this->data['group_name'] : '';
	}

	/**
	* Set the forum name
	*
	* @param string				$text	Forum name
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['group_name', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['group_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['group_name'] = $text;

		return $this;
	}

	/**
	* Get the forum type
	*
	* @return int
	*/
	public function get_type()
	{
		return isset($this->data['group_type']) ? (int) $this->data['group_type'] : GROUP_CLOSED;
	}

	/**
	* Set the forum type
	*
	* @param int				$value	Group type
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_type($value)
	{
		$value = (int) $value;

		if (!in_array($value, [GROUP_OPEN, GROUP_CLOSED, GROUP_HIDDEN, GROUP_SPECIAL, GROUP_FREE]))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('group_type');
		}

		// Set the value on our data array
		$this->data['group_type'] = $value;

		return $this;
	}

	/**
	* Only founders can manage the group?
	*
	* @return bool
	*/
	public function get_founder_manage()
	{
		return isset($this->data['group_founder_manage']) ? (bool) $this->data['group_founder_manage'] : false;
	}

	/**
	* Exclude group leader from group permissions
	*
	* @return bool
	*/
	public function get_skip_auth()
	{
		return isset($this->data['group_skip_auth']) ? (bool) $this->data['group_skip_auth'] : false;
	}

	/**
	* Display group in the legend?
	*
	* @return bool
	*/
	public function get_display()
	{
		return isset($this->data['group_display']) ? (bool) $this->data['group_display'] : false;
	}

	/**
	* Group can receive PMs?
	*
	* @return bool
	*/
	public function get_receive_pm()
	{
		return isset($this->data['group_receive_pm']) ? (bool) $this->data['group_receive_pm'] : false;
	}

	/**
	* Get the maximum characters in signature
	*
	* @return int
	*/
	public function get_sig_chars()
	{
		return isset($this->data['group_sig_chars']) ? (int) $this->data['group_sig_chars'] : 0;
	}

	/**
	* Get the maximum PMs per folder
	*
	* @return int
	*/
	public function get_message_limit()
	{
		return isset($this->data['group_message_limit']) ? (int) $this->data['group_message_limit'] : 0;
	}

	/**
	* Get the order in group legend
	*
	* @return int
	*/
	public function get_legend()
	{
		return isset($this->data['group_legend']) ? (int) $this->data['group_legend'] : 0;
	}

	/**
	* Get the maximum recipients per PM
	*
	* @return int
	*/
	public function get_max_recipients()
	{
		return isset($this->data['group_max_recipients']) ? (int) $this->data['group_max_recipients'] : 0;
	}
}
