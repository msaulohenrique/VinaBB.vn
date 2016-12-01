<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

use vinabb\web\entities\sub\topic_actions;
use vinabb\web\includes\constants;

/**
* Entity for a single topic
*/
class topic extends topic_actions implements topic_interface
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
			'topic_id'					=> 'integer',
			'forum_id'					=> 'integer',
			'topic_first_post_id'		=> 'integer',
			'icon_id'					=> 'integer',
			'topic_poster'				=> 'integer',
			'topic_first_poster_name'	=> 'string',
			'topic_first_poster_colour'	=> 'string',
			'topic_title'				=> 'string',
			'topic_title_seo'			=> 'string',
			'topic_type'				=> 'integer',
			'topic_status'				=> 'integer',
			'topic_views'				=> 'integer',
			'topic_posts_approved'		=> 'integer',
			'topic_posts_unapproved'	=> 'integer',
			'topic_posts_softdeleted'	=> 'integer',
			'topic_time'				=> 'integer',
			'topic_time_limit'			=> 'integer',

			// Entity: vinabb\web\entities\sub\topic_actions
			'topic_visibility'		=> 'integer',
			'topic_attachment'		=> 'bool',
			'topic_reported'		=> 'bool',
			'topic_moved_id'		=> 'integer',
			'topic_bumped'			=> 'bool',
			'topic_bumper'			=> 'integer',
			'topic_delete_time'		=> 'integer',
			'topic_delete_reason'	=> 'string',
			'topic_delete_user'		=> 'integer',

			// Entity: vinabb\web\entities\sub\topic_last_post
			'topic_last_post_id'		=> 'integer',
			'topic_last_poster_id'		=> 'integer',
			'topic_last_poster_name'	=> 'string',
			'topic_last_poster_colour'	=> 'string',
			'topic_last_post_subject'	=> 'string',
			'topic_last_post_time'		=> 'integer',
			'topic_last_view_time'		=> 'integer',

			// Entity: vinabb\web\entities\sub\topic_poll
			'poll_title'		=> 'string',
			'poll_start'		=> 'integer',
			'poll_length'		=> 'integer',
			'poll_max_options'	=> 'integer',
			'poll_last_vote'	=> 'integer',
			'poll_vote_change'	=> 'bool'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Topic ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . TOPICS_TABLE . '
			WHERE topic_id = ' . (int) $id;
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
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return topic_interface $this Object for chaining calls: load()->set()->save()
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
	* @return topic_interface $this Object for chaining calls: load()->set()->save()
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
	* Get the topic ID
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['topic_id']) ? (int) $this->data['topic_id'] : 0;
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
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
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
	* Get the first post ID
	*
	* @return int
	*/
	public function get_first_post_id()
	{
		return isset($this->data['topic_first_post_id']) ? (int) $this->data['topic_first_post_id'] : 0;
	}

	/**
	* Set the first post ID
	*
	* @param int				$id		Post ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_first_post_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if (!$id)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_first_post_id', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_post_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_first_post_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['topic_first_post_id'] = $id;

		return $this;
	}

	/**
	* Get the topic icon
	*
	* @return int
	*/
	public function get_icon_id()
	{
		return isset($this->data['icon_id']) ? (int) $this->data['icon_id'] : 0;
	}

	/**
	* Set the topic icon
	*
	* @param int				$id		Icon ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_icon_id($id)
	{
		$id = (int) $id;

		// Check existing icon
		if ($id && !$this->entity_helper->check_post_icon_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['icon_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['icon_id'] = $id;

		return $this;
	}

	/**
	* Get the poster ID
	*
	* @return int
	*/
	public function get_poster()
	{
		return isset($this->data['topic_poster']) ? (int) $this->data['topic_poster'] : 0;
	}

	/**
	* Set the poster ID
	*
	* @param int				$id		User ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_poster($id)
	{
		$id = (int) $id;

		// Check existing user
		if ($id && !$this->entity_helper->check_user_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_poster', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['topic_poster'] = $id;

		return $this;
	}

	/**
	* Get the poster username
	*
	* @return string
	*/
	public function get_first_poster_name()
	{
		return isset($this->data['topic_first_poster_name']) ? (string) $this->data['topic_first_poster_name'] : '';
	}

	/**
	* Set the poster username
	*
	* @param string				$text	Username
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_first_poster_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_first_poster_name', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_username($text, $this->get_poster()))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_first_poster_name', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['topic_first_poster_name'] = $text;

		return $this;
	}

	/**
	* Get the poster username color
	*
	* @return string
	*/
	public function get_first_poster_colour()
	{
		return isset($this->data['topic_first_poster_colour']) ? (string) $this->data['topic_first_poster_colour'] : '';
	}

	/**
	* Set the poster username color
	*
	* @param string				$text	6-char HEX code without #
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_first_poster_colour($text)
	{
		$text = (string) $text;

		// Check invalid characters
		if (!preg_match('/([a-f0-9]{3}){1,2}\b/i', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_first_poster_colour', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['topic_first_poster_colour'] = $text;

		return $this;
	}

	/**
	* Get the topic title
	*
	* @return string
	*/
	public function get_title()
	{
		return isset($this->data['topic_title']) ? (string) $this->data['topic_title'] : '';
	}

	/**
	* Set the topic title
	*
	* @param string				$text	Topic title
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_title($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_title', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_title', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['topic_title'] = $text;

		return $this;
	}

	/**
	* Get the topic SEO title
	*
	* @return string
	*/
	public function get_title_seo()
	{
		return isset($this->data['topic_title_seo']) ? (string) $this->data['topic_title_seo'] : '';
	}

	/**
	* Set the topic SEO title
	*
	* @param string				$text	Topic SEO title
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_title_seo($text)
	{
		$text = strtolower($text);

		// Check invalid characters
		if (!preg_match('#^[a-z0-9-]+$#', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_title_seo', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['topic_title_seo'] = $text;

		return $this;
	}

	/**
	* Get the topic type
	*
	* @return int
	*/
	public function get_type()
	{
		return isset($this->data['topic_type']) ? (int) $this->data['topic_type'] : POST_NORMAL;
	}

	/**
	* Set the topic type
	*
	* @param int				$value	Topic type
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_type($value)
	{
		$value = (int) $value;

		if (!in_array($value, [POST_NORMAL, POST_STICKY, POST_ANNOUNCE, POST_GLOBAL]))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('topic_type');
		}

		// Set the value on our data array
		$this->data['topic_type'] = $value;

		return $this;
	}

	/**
	* Get the topic status
	*
	* @return int
	*/
	public function get_status()
	{
		return isset($this->data['topic_status']) ? (int) $this->data['topic_status'] : ITEM_UNLOCKED;
	}

	/**
	* Set the topic status
	*
	* @param int				$value	Topic status
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_status($value)
	{
		$value = (int) $value;

		if (!in_array($value, [ITEM_UNLOCKED, ITEM_LOCKED, ITEM_MOVED]))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('topic_status');
		}

		// Set the value on our data array
		$this->data['topic_status'] = $value;

		return $this;
	}

	/**
	* Get the topic views
	*
	* @return int
	*/
	public function get_views()
	{
		return isset($this->data['topic_views']) ? (int) $this->data['topic_views'] : 0;
	}

	/**
	* Get the number of approved posts
	*
	* @return int
	*/
	public function get_posts_approved()
	{
		return isset($this->data['topic_posts_approved']) ? (int) $this->data['topic_posts_approved'] : 0;
	}

	/**
	* Get the number of disapproved posts
	*
	* @return int
	*/
	public function get_posts_unapproved()
	{
		return isset($this->data['topic_posts_unapproved']) ? (int) $this->data['topic_posts_unapproved'] : 0;
	}

	/**
	* Get the number of soft-deleted posts
	*
	* @return int
	*/
	public function get_posts_softdeleted()
	{
		return isset($this->data['topic_posts_softdeleted']) ? (int) $this->data['topic_posts_softdeleted'] : 0;
	}

	/**
	* Get the topic time
	*
	* @return int
	*/
	public function get_time()
	{
		return isset($this->data['topic_time']) ? (int) $this->data['topic_time'] : 0;
	}

	/**
	* Get the topic time limit
	*
	* @return int
	*/
	public function get_time_limit()
	{
		return isset($this->data['topic_time_limit']) ? (int) $this->data['topic_time_limit'] : 0;
	}
}
