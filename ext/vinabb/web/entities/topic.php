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
* Entity for a single forum
*/
class topic
{
	/**
	* Data for this entity
	*
	* @var array
	*	topic_id
	*	forum_id
	*	icon_id
	*	topic_poster
	*	topic_title
	*	topic_title_seo
	*	topic_attachment
	*	topic_reported
	*	topic_time
	*	topic_time_limit
	*	topic_views
	*	topic_status
	*	topic_type
	*	topic_first_post_id
	*	topic_first_poster_name
	*	topic_first_poster_colour
	*	topic_last_post_id
	*	topic_last_poster_id
	*	topic_last_poster_name
	*	topic_last_poster_colour
	*	topic_last_post_subject
	*	topic_last_post_time
	*	topic_last_view_time
	*	topic_moved_id
	*	topic_bumped
	*	topic_bumper
	*	poll_title
	*	poll_start
	*	poll_length
	*	poll_max_options
	*	poll_last_vote
	*	poll_vote_change
	*	topic_visibility
	*	topic_delete_time
	*	topic_delete_reason
	*	topic_delete_user
	*	topic_posts_approved
	*	topic_posts_unapproved
	*	topic_posts_softdeleted
	*/
	protected $data;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* Constructor
	*
	* @param \phpbb\config\config				$config		Config object
	* @param \phpbb\db\driver\driver_interface	$db			Database object
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db)
	{
		$this->config = $config;
		$this->db = $db;
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Forum ID
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . TOPICS_TABLE . '
			WHERE forum_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('topic_id');
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
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = [];

		// All of our fields
		$fields = [
			'topic_id'					=> 'integer',
			'forum_id'					=> 'set_forum_id',
			'icon_id'					=> 'set_icon_id',
			'topic_poster'				=> 'set_topic_poster',
			'topic_title'				=> 'set_topic_title',
			'topic_title_seo'			=> 'set_topic_title_seo',
			'topic_attachment'			=> 'bool',
			'topic_reported'			=> 'bool',
			'topic_time'				=> 'integer',
			'topic_time_limit'			=> 'integer',
			'topic_views'				=> 'integer',
			'topic_status'				=> 'set_status',
			'topic_type'				=> 'set_type',
			'topic_first_post_id'		=> 'set_first_post_id',
			'topic_first_poster_name'	=> 'set_first_poster_name',
			'topic_first_poster_colour'	=> 'set_first_poster_colour',
			'topic_last_post_id'		=> 'set_last_post_id',
			'topic_last_poster_id'		=> 'set_last_poster_id',
			'topic_last_poster_name'	=> 'set_last_poster_name',
			'topic_last_poster_colour'	=> 'set_last_poster_colour',
			'topic_last_post_subject'	=> 'string',
			'topic_last_post_time'		=> 'integer',
			'topic_last_view_time'		=> 'integer',
			'topic_moved_id'			=> 'set_moved_id',
			'topic_bumped'				=> 'bool',
			'topic_bumper'				=> 'set_bumper',
			'poll_title'				=> 'string',
			'poll_start'				=> 'integer',
			'poll_length'				=> 'integer',
			'poll_max_options'			=> 'integer',
			'poll_last_vote'			=> 'integer',
			'poll_vote_change'			=> 'bool',
			'topic_visibility'			=> 'integer',
			'topic_delete_time'			=> 'integer',
			'topic_delete_reason'		=> 'string',
			'topic_delete_user'			=> 'set_delete_user',
			'topic_posts_approved'		=> 'integer',
			'topic_posts_unapproved'	=> 'integer',
			'topic_posts_softdeleted'	=> 'integer'
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
		$validate_unsigned = ['topic_id', 'forum_id', 'icon_id', 'topic_poster', 'topic_attachment', 'topic_reported', 'topic_time', 'topic_time_limit', 'topic_views', 'topic_first_post_id', 'topic_last_post_id', 'topic_last_poster_id', 'topic_last_post_time', 'topic_last_view_time', 'topic_moved_id', 'topic_bumped', 'topic_bumper', 'poll_start', 'poll_length', 'poll_last_vote', 'poll_vote_change', 'topic_delete_time', 'topic_delete_user', 'topic_posts_approved', 'topic_posts_unapproved', 'topic_posts_softdeleted'];

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
	* @return forum_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['topic_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('topic_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['topic_id']);

		$sql = 'INSERT INTO ' . TOPICS_TABLE . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['topic_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return forum_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['topic_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('topic_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['topic_id' => null]);

		$sql = 'UPDATE ' . TOPICS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE topic_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the topic_id
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['topic_id']) ? (int) $this->data['topic_id'] : 0;
	}
}
