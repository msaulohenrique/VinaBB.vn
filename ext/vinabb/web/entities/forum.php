<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

use vinabb\web\entities\sub\forum_data;
use vinabb\web\includes\constants;

/**
* Entity for a single forum
*/
class forum extends forum_data implements forum_interface
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
			'forum_id'			=> 'integer',
			'parent_id'			=> 'integer',
			'left_id'			=> 'integer',
			'right_id'			=> 'integer',
			'forum_parents'		=> 'string',
			'forum_name'		=> 'string',
			'forum_name_seo'	=> 'string',

			// Entity: vinabb\web\entities\sub\forum_data
			'forum_type'				=> 'integer',
			'forum_status'				=> 'integer',
			'forum_topics_per_page'		=> 'integer',
			'forum_topics_approved'		=> 'integer',
			'forum_topics_unapproved'	=> 'integer',
			'forum_topics_softdeleted'	=> 'integer',
			'forum_posts_approved'		=> 'integer',
			'forum_posts_unapproved'	=> 'integer',
			'forum_posts_softdeleted'	=> 'integer',

			// Entity: vinabb\web\entities\sub\forum_options
			'forum_link'			=> 'string',
			'forum_password'		=> 'string',
			'forum_style'			=> 'integer',
			'forum_image'			=> 'string',
			'forum_flags'			=> 'integer',
			'forum_options'			=> 'integer',
			'display_on_index'		=> 'bool',
			'enable_indexing'		=> 'bool',
			'enable_icons'			=> 'bool',
			'display_subforum_list'	=> 'bool',

			// Entity: vinabb\web\entities\sub\forum_last_post
			'forum_last_post_id'		=> 'integer',
			'forum_last_poster_id'		=> 'integer',
			'forum_last_poster_name'	=> 'string',
			'forum_last_poster_colour'	=> 'string',
			'forum_last_post_subject'	=> 'string',
			'forum_last_post_time'		=> 'integer',

			// Entity: vinabb\web\entities\sub\forum_prune
			'enable_prune'			=> 'bool',
			'enable_shadow_prune'	=> 'bool',
			'prune_days'			=> 'integer',
			'prune_freq'			=> 'integer',
			'prune_next'			=> 'integer',
			'prune_viewed'			=> 'integer',
			'prune_shadow_days'		=> 'integer',
			'prune_shadow_freq'		=> 'integer',
			'prune_shadow_next'		=> 'integer',

			// Entity: vinabb\web\entities\sub\forum_desc_rules
			'forum_desc'			=> 'string',
			'forum_desc_uid'		=> 'string',
			'forum_desc_bitfield'	=> 'string',
			'forum_desc_options'	=> 'integer',
			'forum_rules'			=> 'string',
			'forum_rules_uid'		=> 'string',
			'forum_rules_bitfield'	=> 'string',
			'forum_rules_options'	=> 'integer'
		];
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
			FROM ' . FORUMS_TABLE . '
			WHERE forum_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('forum_id');
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
	* @return forum_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['forum_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('forum_id');
		}

		// Resets values required for the nested set system
		$this->data['parent_id'] = 0;
		$this->data['left_id'] = 0;
		$this->data['right_id'] = 0;
		$this->data['forum_parents'] = '';

		// Make extra sure there is no ID set
		unset($this->data['forum_id']);

		$sql = 'INSERT INTO ' . FORUMS_TABLE . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['forum_id'] = (int) $this->db->sql_nextid();

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
		if (empty($this->data['forum_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('forum_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['forum_id' => null]);

		$sql = 'UPDATE ' . FORUMS_TABLE . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE forum_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the forum ID
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['forum_id']) ? (int) $this->data['forum_id'] : 0;
	}

	/**
	* Get the parent forum ID
	*
	* @return int
	*/
	public function get_parent_id()
	{
		return isset($this->data['parent_id']) ? (int) $this->data['parent_id'] : 0;
	}

	/**
	* Set the parent forum ID
	*
	* @param int				$id		Parent ID
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_parent_id($id)
	{
		$id = (int) $id;

		// Check existing forum
		if ($id && !$this->entity_helper->check_forum_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['parent_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['parent_id'] = $id;

		return $this;
	}

	/**
	* Get the left_id for the tree
	*
	* @return int
	*/
	public function get_left_id()
	{
		return isset($this->data['left_id']) ? (int) $this->data['left_id'] : 0;
	}

	/**
	* Get the right_id for the tree
	*
	* @return int
	*/
	public function get_right_id()
	{
		return isset($this->data['right_id']) ? (int) $this->data['right_id'] : 0;
	}

	/**
	* Get the forum name
	*
	* @return string
	*/
	public function get_name()
	{
		return isset($this->data['forum_name']) ? (string) $this->data['forum_name'] : '';
	}

	/**
	* Set the forum name
	*
	* @param string				$text	Forum name
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['forum_name', 'EMPTY']);
		}

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['forum_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['forum_name'] = $text;

		return $this;
	}

	/**
	* Get the forum SEO name
	*
	* @return string
	*/
	public function get_name_seo()
	{
		return isset($this->data['forum_name_seo']) ? (string) $this->data['forum_name_seo'] : '';
	}

	/**
	* Set the forum SEO name
	*
	* @param string				$text	Forum SEO name
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_seo($text)
	{
		$text = (string) $text;

		// Check invalid characters
		if (!preg_match(constants::REGEX_SEO, $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['forum_name_seo', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['forum_name_seo'] = $text;

		return $this;
	}
}
