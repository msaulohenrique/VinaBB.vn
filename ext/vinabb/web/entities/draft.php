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
* Entity for a single draft
*/
class draft implements draft_interface
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
			'draft_id'		=> 'integer',
			'forum_id'		=> 'integer',
			'topic_id'		=> 'integer',
			'user_id'		=> 'integer',
			'draft_subject'	=> 'string',
			'draft_message'	=> 'string',
			'save_time'		=> 'integer'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Draft ID
	* @return draft_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . DRAFTS_TABLE . '
			WHERE draft_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('draft_id');
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
	* @return draft_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return draft_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['draft_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('draft_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['draft_id']);

		$sql = 'INSERT INTO ' . DRAFTS_TABLE . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['draft_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return draft_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['draft_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('draft_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['draft_id' => null]);

		$sql = 'UPDATE ' . DRAFTS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE draft_id = ' . $this->get_id();
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
		return isset($this->data['draft_id']) ? (int) $this->data['draft_id'] : 0;
	}

	/**
	* Get the forum ID
	*
	* @return int
	*/
	public function get_forum_id()
	{
		return isset($this->data['forum_id']) ? (int) $this->data['forum_id'] : 0;
	}

	/**
	* Set the forum ID
	*
	* @param int				$id		Forum ID
	* @return draft_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_forum_id($id)
	{
		$id = (int) $id;

		// Check existing forum
		if ($id && !$this->entity_helper->check_forum_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['forum_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['forum_id'] = $id;

		return $this;
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
	* @param int				$id		Topic ID
	* @return draft_interface	$this	Object for chaining calls: load()->set()->save()
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
	* Get the user ID
	*
	* @return int
	*/
	public function get_user_id()
	{
		return isset($this->data['user_id']) ? (int) $this->data['user_id'] : 0;
	}

	/**
	* Set the user ID
	*
	* @param int				$id		User ID
	* @return draft_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_user_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if (!$id)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_id', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_user_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['user_id'] = $id;

		return $this;
	}

	/**
	* Get the draft subject
	*
	* @return string
	*/
	public function get_subject()
	{
		return isset($this->data['draft_subject']) ? (string) $this->data['draft_subject'] : '';
	}

	/**
	* Set the draft subject
	*
	* @param string				$text	Draft subject
	* @return draft_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_subject($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['draft_subject', 'EMPTY']);
		}

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['draft_subject', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['draft_subject'] = $text;

		return $this;
	}

	/**
	* Get the draft content
	*
	* @return string
	*/
	public function get_message()
	{
		return isset($this->data['draft_message']) ? (string) $this->data['draft_message'] : '';
	}

	/**
	* Get the saving time
	*
	* @return int
	*/
	public function get_save_time()
	{
		return isset($this->data['save_time']) ? (int) $this->data['save_time'] : 0;
	}
}
