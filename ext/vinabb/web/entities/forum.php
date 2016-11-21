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
class forum extends \vinabb\web\entities\abs\forum_desc_rules implements forum_interface
{
	/**
	* Data for this entity
	*
	* @var array
	*	forum_id
	*	parent_id
	*	left_id
	*	right_id
	*	forum_parents
	*	forum_name
	*	forum_name_seo
	*		forum_desc
	*		forum_desc_uid
	*		forum_desc_bitfield
	*		forum_desc_options
	*		forum_rules
	*		forum_rules_uid
	*		forum_rules_bitfield
	*		forum_rules_options
	*	forum_topics_per_page
	*	forum_type
	*	forum_status
	*/
	protected $data;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vinabb\web\entities\helper\helper_interface */
	protected $entity_helper;

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
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = [];

		// All of our fields
		$fields = [
			'forum_id'				=> 'integer',
			'parent_id'				=> 'set_parent_id',
			'left_id'				=> 'integer',
			'right_id'				=> 'integer',
			'forum_parents'			=> 'string',
			'forum_name'			=> 'set_name',
			'forum_name_seo'		=> 'set_name_seo',
			'forum_topics_per_page'	=> 'integer',
			'forum_type'			=> 'set_type',
			'forum_status'			=> 'set_status',

			// We do not pass to set_desc() or set_rules() as generate_text_for_storage() would run twice
			'forum_desc'			=> 'string',
			'forum_desc_uid'		=> 'string',
			'forum_desc_bitfield'	=> 'string',
			'forum_desc_options'	=> 'integer',
			'forum_rules'			=> 'string',
			'forum_rules_uid'		=> 'string',
			'forum_rules_bitfield'	=> 'string',
			'forum_rules_options'	=> 'integer'
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
		$validate_unsigned = ['forum_id', 'parent_id', 'left_id', 'right_id', 'forum_topics_per_page', 'forum_type', 'forum_status', 'forum_desc_options', 'forum_rules_options'];

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
	* Get the forum_id
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['forum_id']) ? (int) $this->data['forum_id'] : 0;
	}

	/**
	* Get the parent_id
	*
	* @return int
	*/
	public function get_parent_id()
	{
		return isset($this->data['parent_id']) ? (int) $this->data['parent_id'] : 0;
	}

	/**
	* Set the parent_id
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
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
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
		$text = strtolower($text);

		// Check invalid characters
		if (!preg_match('#^[a-z0-9-]+$#', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['forum_name_seo', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['forum_name_seo'] = $text;

		return $this;
	}

	/**
	* Get the number of topics per page in this forum
	*
	* @return int
	*/
	public function get_topics_per_page()
	{
		return isset($this->data['forum_topics_per_page']) ? (int) $this->data['forum_topics_per_page'] : 0;
	}

	/**
	* Get the forum type
	*
	* @return int
	*/
	public function get_type()
	{
		return isset($this->data['forum_type']) ? (int) $this->data['forum_type'] : 0;
	}

	/**
	* Set the forum type
	*
	* @param int				$value	Forum type
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_type($value)
	{
		$value = (int) $value;

		if (!in_array($value, [FORUM_CAT, FORUM_POST, FORUM_LINK]))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('forum_type');
		}

		// Set the value on our data array
		$this->data['forum_type'] = $value;

		return $this;
	}

	/**
	* Get the forum status
	*
	* @return int
	*/
	public function get_status()
	{
		return isset($this->data['forum_status']) ? (int) $this->data['forum_status'] : 0;
	}

	/**
	* Set the forum status
	*
	* @param int				$value	Forum status
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_status($value)
	{
		$value = (int) $value;

		if (!in_array($value, [ITEM_UNLOCKED, ITEM_LOCKED, ITEM_MOVED]))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('forum_status');
		}

		// Set the value on our data array
		$this->data['forum_status'] = $value;

		return $this;
	}
}
