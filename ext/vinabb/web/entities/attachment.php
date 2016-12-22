<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

use vinabb\web\entities\sub\attachment_file;

/**
* Entity for a single attachment
*/
class attachment extends attachment_file implements attachment_interface
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
			'attach_id'			=> 'integer',
			'poster_id'			=> 'integer',
			'topic_id'			=> 'integer',
			'post_msg_id'		=> 'integer',
			'in_message'		=> 'bool',
			'is_orphan'			=> 'bool',
			'download_count'	=> 'integer',
			'attach_comment'	=> 'string',

			// Entity: vinabb\web\entities\sub\attachment_file
			'physical_filename'	=> 'string',
			'real_filename'		=> 'string',
			'extension'			=> 'string',
			'mimetype'			=> 'string',
			'filesize'			=> 'integer',
			'filetime'			=> 'integer',
			'thumbnail'			=> 'bool'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int					$id		Attachment ID
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . ATTACHMENTS_TABLE . '
			WHERE attach_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('attach_id');
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
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return attachment_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['attach_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('attach_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['attach_id']);

		$sql = 'INSERT INTO ' . ATTACHMENTS_TABLE . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['attach_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return attachment_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['attach_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('attach_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['attach_id' => null]);

		$sql = 'UPDATE ' . ATTACHMENTS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE attach_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the attachment ID
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['attach_id']) ? (int) $this->data['attach_id'] : 0;
	}

	/**
	* Get the poster ID
	*
	* @return int
	*/
	public function get_poster_id()
	{
		return isset($this->data['poster_id']) ? (int) $this->data['poster_id'] : 0;
	}

	/**
	* Set the poster ID
	*
	* @param int					$id		User ID
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_poster_id($id)
	{
		$id = (int) $id;

		// Check existing user
		if ($id && !$this->entity_helper->check_user_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['poster_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['poster_id'] = $id;

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
	* @param int					$id		Topic ID
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
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
	* Get the post or PM ID
	*
	* @return int
	*/
	public function get_post_msg_id()
	{
		return isset($this->data['post_msg_id']) ? (int) $this->data['post_msg_id'] : 0;
	}

	/**
	* Set the post or PM ID
	*
	* @param int					$id		Post or PM ID
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_post_msg_id($id)
	{
		$id = (int) $id;

		// Check existing post or PM
		if ($id && ((!$this->get_in_message() && !$this->entity_helper->check_post_id($id))) || ($this->get_in_message() && !$this->entity_helper->check_pm_id($id)))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['post_msg_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['post_msg_id'] = $id;

		return $this;
	}

	/**
	* The attachment is within a PM?
	*
	* @return bool
	*/
	public function get_in_message()
	{
		return isset($this->data['in_message']) ? (bool) $this->data['in_message'] : false;
	}

	/**
	* The attachment is not assigned to any posts or PMs?
	*
	* @return bool
	*/
	public function get_is_orphan()
	{
		return isset($this->data['is_orphan']) ? (bool) $this->data['is_orphan'] : true;
	}

	/**
	* Get number of downloads
	*
	* @return int
	*/
	public function get_download_count()
	{
		return isset($this->data['download_count']) ? (int) $this->data['download_count'] : 0;
	}

	/**
	* Get the attachment comment
	*
	* @return string
	*/
	public function get_comment()
	{
		return isset($this->data['attach_comment']) ? (string) $this->data['attach_comment'] : '';
	}
}
