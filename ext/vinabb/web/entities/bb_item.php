<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

use vinabb\web\entities\sub\item_data;
use vinabb\web\includes\constants;

/**
* Entity for a single phpBB resource item
*/
class bb_item extends item_data implements bb_item_interface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vinabb\web\entities\helper\helper_interface */
	protected $entity_helper;

	/** @var string */
	protected $table_name;

	/** @var string */
	protected $cat_table_name;

	/** @var string */
	protected $author_table_name;

	/** @var array */
	protected $data;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface				$db					Database object
	* @param \vinabb\web\entities\helper\helper_interface	$entity_helper		Entity helper
	* @param string											$table_name			Table name
	* @param string											$cat_table_name		Table name of categories
	* @param string											$author_table_name	Table name of authors
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \vinabb\web\entities\helper\helper_interface $entity_helper, $table_name, $cat_table_name, $author_table_name)
	{
		$this->db = $db;
		$this->entity_helper = $entity_helper;
		$this->table_name = $table_name;
		$this->cat_table_name = $cat_table_name;
		$this->author_table_name = $author_table_name;
	}

	/**
	* Data for this entity
	*
	* @return array
	*/
	protected function prepare_data()
	{
		return [
			'item_id'		=> 'integer',
			'bb_type'		=> 'integer',
			'cat_id'		=> 'integer',
			'author_id'		=> 'integer',
			'item_name'		=> 'string',
			'item_varname'	=> 'string',

			// Entity: vinabb\web\entities\sub\item_data
			'item_price'	=> 'integer',
			'item_url'		=> 'string',
			'item_github'	=> 'string',
			'item_enable'	=> 'bool',
			'item_added'	=> 'integer',
			'item_updated'	=> 'integer',

			// Entity: vinabb\web\entities\sub\item_ext
			'item_ext_style'		=> 'bool',
			'item_ext_acp_style'	=> 'bool',
			'item_ext_lang'			=> 'bool',
			'item_ext_db_schema'	=> 'bool',
			'item_ext_db_data'		=> 'bool',

			// Entity: vinabb\web\entities\sub\item_style
			'item_style_presets'		=> 'integer',
			'item_style_presets_aio'	=> 'bool',
			'item_style_source'			=> 'bool',
			'item_style_responsive'		=> 'bool',
			'item_style_bootstrap'		=> 'bool',

			// Entity: vinabb\web\entities\sub\item_lang_tool
			'item_lang_iso'	=> 'string',
			'item_tool_os'	=> 'integer',

			// Entity: vinabb\web\entities\sub\item_desc
			'item_desc'				=> 'string',
			'item_desc_uid'			=> 'string',
			'item_desc_bitfield'	=> 'string',
			'item_desc_options'		=> 'integer',
			'item_desc_vi'			=> 'string',
			'item_desc_vi_uid'		=> 'string',
			'item_desc_vi_bitfield'	=> 'string',
			'item_desc_vi_options'	=> 'integer'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Item ID
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE item_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('item_id');
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
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @param int $bb_type phpBB resource type
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert($bb_type)
	{
		// The entity already exists
		if (!empty($this->data['item_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('item_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['item_id']);

		// Add the bb_type to the data array
		$this->data['bb_type'] = (int) $bb_type;

		$sql = 'INSERT INTO ' . $this->table_name . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['item_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['item_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('item_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['item_id' => null]);

		$sql = 'UPDATE ' . $this->table_name . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE item_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the item ID
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['article_id']) ? (int) $this->data['article_id'] : 0;
	}

	/**
	* Get the phpBB resource type
	*
	* @return int
	*/
	public function get_bb_type()
	{
		return isset($this->data['bb_type']) ? (int) $this->data['bb_type'] : 0;
	}

	/**
	* Get the category ID
	*
	* @return int
	*/
	public function get_cat_id()
	{
		return isset($this->data['cat_id']) ? (int) $this->data['cat_id'] : 0;
	}

	/**
	* Set the category ID
	*
	* @param int						$id		Category ID
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_cat_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if (!$id)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_id', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_bb_cat_id($this->get_bb_type(), $id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['cat_id'] = $id;

		return $this;
	}

	/**
	* Get the author ID
	*
	* @return int
	*/
	public function get_author_id()
	{
		return isset($this->data['author_id']) ? (int) $this->data['author_id'] : 0;
	}

	/**
	* Set the author ID
	*
	* @param int						$id		Author ID
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_author_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if (!$id)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_id', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_bb_author_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['author_id'] = $id;

		return $this;
	}

	/**
	* Get the item name
	*
	* @return string
	*/
	public function get_name()
	{
		return isset($this->data['item_name']) ? (string) $this->data['item_name'] : '';
	}

	/**
	* Set the item name
	*
	* @param string						$text	Item name
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_name', 'EMPTY']);
		}

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_BB_ITEM_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['item_name'] = $text;

		return $this;
	}

	/**
	* Get the item varname
	*
	* @return string
	*/
	public function get_varname()
	{
		return isset($this->data['item_varname']) ? (string) $this->data['item_varname'] : '';
	}

	/**
	* Set the item varname
	*
	* @param string				$text	Item varname
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_varname($text)
	{
		$text = strtolower($text);

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_varname', 'EMPTY']);
		}

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_BB_ITEM_VARNAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_varname', 'TOO_LONG']);
		}

		// Check invalid characters
		$match = ($this->get_bb_type() === constants::BB_TYPE_EXT) ? constants::REGEX_VARNAME_EXT : constants::REGEX_VARNAME;

		if (!preg_match($match, $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_varname', 'INVALID']);
		}

		// This field value must be unique
		if ($this->entity_helper->check_bb_item_varname($this->get_bb_type(), $text, $this->get_id()))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_varname', 'DUPLICATE', $text]);
		}

		// Set the value on our data array
		$this->data['item_varname'] = $text;

		return $this;
	}
}
