<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Entity for a single poll option
*/
class poll_option implements poll_option_interface
{
	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \vinabb\web\entities\helper\helper_interface $entity_helper */
	protected $entity_helper;

	/** @var array $data */
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
			'poll_option_id'	=> 'integer',
			'topic_id'			=> 'integer',
			'poll_option_text'	=> 'string',
			'poll_option_total'	=> 'integer'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int					$id		Poll option ID
	* @return poll_option_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . POLL_OPTIONS_TABLE . '
			WHERE poll_option_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('poll_option_id');
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
	* @return poll_option_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return poll_option_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['poll_option_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('poll_option_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['poll_option_id']);

		$sql = 'INSERT INTO ' . POLL_OPTIONS_TABLE . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['poll_option_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return poll_option_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['poll_option_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('poll_option_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['poll_option_id' => null]);

		$sql = 'UPDATE ' . POLL_OPTIONS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE poll_option_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the draft ID
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['poll_option_id']) ? (int) $this->data['poll_option_id'] : 0;
	}

	/**
	* Get the topic ID
	*
	* @return int
	*/
	public function get_topic_id()
	{
		return isset($this->data['topic_id']) ? (int) $this->data['topic_id'] : 0;
	}

	/**
	* Set the topic ID
	*
	* @param int					$id		Topic ID
	* @return poll_option_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_topic_id($id)
	{
		$id = (int) $id;

		// Check existing topic
		if ($id && !$this->entity_helper->check_topic_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['topic_id'] = $id;

		return $this;
	}

	/**
	* Get the poll option text
	*
	* @return string
	*/
	public function get_text()
	{
		return isset($this->data['poll_option_text']) ? (string) $this->data['poll_option_text'] : '';
	}

	/**
	* Get poll option's votes
	*
	* @return int
	*/
	public function get_total()
	{
		return isset($this->data['poll_option_total']) ? (int) $this->data['poll_option_total'] : 0;
	}
}
