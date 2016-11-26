<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

use vinabb\web\entities\sub\post_options;
use vinabb\web\includes\constants;

/**
* Entity for a single post
*/
class post extends post_options implements post_interface
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
			'post_id'			=> 'integer',
			'forum_id'			=> 'integer',
			'topic_id'			=> 'integer',
			'icon_id'			=> 'integer',
			'poster_id'			=> 'integer',
			'poster_ip'			=> 'string',
			'post_username'		=> 'string',
			'post_subject'		=> 'string',
			'post_subject_seo'	=> 'string',
			'post_time'			=> 'integer',

			// Entity: vinabb\web\entities\sub\post_options
			'post_visibility'	=> 'integer',
			'post_attachment'	=> 'bool',
			'post_reported'		=> 'bool',
			'enable_sig'		=> 'bool',
			'post_postcount'	=> 'bool',
			'post_checksum'		=> 'string',

			// Entity: vinabb\web\entities\sub\post_actions
			'post_edit_time'		=> 'integer',
			'post_edit_reason'		=> 'string',
			'post_edit_user'		=> 'integer',
			'post_edit_count'		=> 'integer',
			'post_edit_locked'		=> 'bool',
			'post_delete_time'		=> 'integer',
			'post_delete_reason'	=> 'string',
			'post_delete_user'		=> 'integer',

			// Entity: vinabb\web\entities\sub\post_text
			'post_text'			=> 'string',
			'bbcode_uid'		=> 'string',
			'bbcode_bitfield'	=> 'string',
			'enable_bbcode'		=> 'bool',
			'enable_smilies'	=> 'bool',
			'enable_magic_url'	=> 'bool'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Forum ID
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . POSTS_TABLE . '
			WHERE post_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('post_id');
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
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return post_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['post_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('post_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['post_id']);

		$sql = 'INSERT INTO ' . POSTS_TABLE . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['post_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return post_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['post_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('post_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['post_id' => null]);

		$sql = 'UPDATE ' . POSTS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE post_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the post ID
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['post_id']) ? (int) $this->data['post_id'] : 0;
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
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
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
	* Get the post icon
	*
	* @return int
	*/
	public function get_icon_id()
	{
		return isset($this->data['icon_id']) ? (int) $this->data['icon_id'] : 0;
	}

	/**
	* Set the post icon
	*
	* @param int				$id		Icon ID
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
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
	public function get_poster_id()
	{
		return isset($this->data['poster_id']) ? (int) $this->data['poster_id'] : 0;
	}

	/**
	* Set the poster ID
	*
	* @param int				$id		User ID
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
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
	* Get the poster IP
	*
	* @return string
	*/
	public function get_poster_ip()
	{
		return isset($this->data['poster_ip']) ? (string) $this->data['poster_ip'] : '';
	}

	/**
	* Get the poster username
	*
	* @return string
	*/
	public function get_post_username()
	{
		return isset($this->data['post_username']) ? (string) $this->data['post_username'] : '';
	}

	/**
	 * Set the poster username
	 *
	 * @param string			$text	Username
	 * @return post_interface	$this	Object for chaining calls: load()->set()->save()
	 * @throws \vinabb\web\exceptions\unexpected_value
	 */
	public function set_post_username($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['post_username', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_username($text, $this->get_poster_id()))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['post_username', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['post_username'] = $text;

		return $this;
	}

	/**
	* Get the post subject
	*
	* @return string
	*/
	public function get_subject()
	{
		return isset($this->data['post_subject']) ? (string) $this->data['post_subject'] : '';
	}

	/**
	* Set the post subject
	*
	* @param string				$text	Post subject
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_subject($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['post_subject', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['post_subject', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['post_subject'] = $text;

		return $this;
	}

	/**
	* Get the SEO post subject
	*
	* @return string
	*/
	public function get_subject_seo()
	{
		return isset($this->data['post_subject_seo']) ? (string) $this->data['post_subject_seo'] : '';
	}

	/**
	* Set the SEO post subject
	*
	* @param string				$text	SEO post subject
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_subject_seo($text)
	{
		$text = strtolower($text);

		// Check invalid characters
		if (!preg_match('#^[a-z0-9-]+$#', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['post_subject_seo', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['post_subject_seo'] = $text;

		return $this;
	}

	/**
	* Get the post time
	*
	* @return int
	*/
	public function get_time()
	{
		return isset($this->data['post_time']) ? (int) $this->data['post_time'] : 0;
	}
}
