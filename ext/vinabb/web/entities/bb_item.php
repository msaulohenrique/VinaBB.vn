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
* Entity for a single phpBB resource item
*/
class bb_item implements bb_item_interface
{
	/**
	* Data for this entity
	*
	* @var array
	*	item_id
	*	bb_type
	*	cat_id
	*	author_id
	*	item_name
	*	item_varname
	*	item_desc
	*	item_desc_uid
	*	item_desc_bitfield
	*	item_desc_options
	*	item_desc_vi
	*	item_desc_vi_uid
	*	item_desc_vi_bitfield
	*	item_desc_vi_options
	*
	*	[bb_type = 1]
	*		item_ext_style
	*		item_ext_acp_style
	*		item_ext_lang
	*		item_ext_db_schema
	*		item_ext_db_data
	*
	*	[bb_type = 2 || 3]
	*		item_style_presets
	*		item_style_presets_aio
	*		item_style_source
	*		item_style_responsive
	*		item_style_bootstrap
	*
	*	[bb_type = 4]
	*		item_lang_iso
	*
	*	[bb_type = 5]
	*		item_tool_os
	*
	*	item_price
	*	item_url
	*	item_github
	*	item_added
	*	item_updated
	*/
	protected $data;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string */
	protected $table_name;

	/** @var string */
	protected $cat_table_name;

	/** @var string */
	protected $author_table_name;

	/**
	* Constructor
	*
	* @param \phpbb\config\config				$config				Config object
	* @param \phpbb\db\driver\driver_interface	$db					Database object
	* @param string								$table_name			Table name
	* @param string								$cat_table_name		Table name of categories
	* @param string								$author_table_name	Table name of authors
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, $table_name, $cat_table_name, $author_table_name)
	{
		$this->config = $config;
		$this->db = $db;
		$this->table_name = $table_name;
		$this->cat_table_name = $cat_table_name;
		$this->author_table_name = $author_table_name;
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
	* @param array						$data	Data array from the database
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = [];

		// All of our fields
		$fields = [
			'item_id'					=> 'integer',
			'bb_type'					=> 'integer',
			'cat_id'					=> 'set_cat_id',
			'author_id'					=> 'set_author_id',
			'item_name'					=> 'set_name',
			'item_varname'				=> 'set_varname',
			'item_ext_style'			=> 'bool',
			'item_ext_acp_style'		=> 'bool',
			'item_ext_lang'				=> 'bool',
			'item_ext_db_schema'		=> 'bool',
			'item_ext_db_data'			=> 'bool',
			'item_style_presets'		=> 'set_style_presets',
			'item_style_presets_aio'	=> 'bool',
			'item_style_source'			=> 'bool',
			'item_style_responsive'		=> 'bool',
			'item_style_bootstrap'		=> 'bool',
			'item_lang_iso'				=> 'set_lang_iso',
			'item_tool_os'				=> 'set_tool_os',
			'item_price'				=> 'set_price',
			'item_url'					=> 'set_url',
			'item_github'				=> 'set_github',
			'item_added'				=> 'integer',
			'item_updated'				=> 'integer',

			// We do not pass to set_desc() or set_desc_vi() as generate_text_for_storage() would run twice
			'item_desc'				=> 'string',
			'item_desc_uid'			=> 'string',
			'item_desc_bitfield'	=> 'string',
			'item_desc_options'		=> 'integer',
			'item_desc_vi'			=> 'string',
			'item_desc_vi_uid'		=> 'string',
			'item_desc_vi_bitfield'	=> 'string',
			'item_desc_vi_options'	=> 'integer'
		];

		// Go through the basic fields and set them to our data array
		foreach ($fields as $field => $type)
		{
			// The data wasn't sent to us
			if (!isset($data[$field]))
			{
				throw new \vinabb\web\exceptions\invalid_argument([$field, 'FIELD_MISSING']);
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
		$validate_unsigned = ['item_id', 'bb_type', 'cat_id', 'author_id', 'item_desc_options', 'item_desc_vi_options', 'item_style_presets', 'item_price'];

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
	* Get the item_id
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['article_id']) ? (int) $this->data['article_id'] : 0;
	}

	/**
	* Get the bb_type
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
		if ($id > 0)
		{
			$sql = 'SELECT 1
				FROM ' . $this->cat_table_name . "
				WHERE cat_id = $id";
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row === false)
			{
				throw new \vinabb\web\exceptions\unexpected_value(['cat_id', 'NOT_EXISTS']);
			}
		}
		else
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_id', 'FIELD_MISSING']);
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
		if ($id > 0)
		{
			$sql = 'SELECT 1
				FROM ' . $this->author_table_name . "
				WHERE author_id = $id";
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row === false)
			{
				throw new \vinabb\web\exceptions\unexpected_value(['author_id', 'NOT_EXISTS']);
			}
		}
		else
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_id', 'FIELD_MISSING']);
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
		if (empty($text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_name', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_BB_ITEM_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['item_name'] = $text;

		return $this;
	}

	/**
	* Get the Vietnamese item name
	*
	* @return string
	*/
	public function get_name_vi()
	{
		return isset($this->data['item_name_vi']) ? (string) $this->data['item_name_vi'] : '';
	}

	/**
	* Set the Vietnamese item name
	*
	* @param string						$text	Vietnamese item name
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_vi($text)
	{
		$text = (string) $text;

		// This is a required field
		if (empty($text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_name_vi', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_BB_ITEM_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_name_vi', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['item_name_vi'] = $text;

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
		if (empty($text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_varname', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_BB_ITEM_VARNAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_varname', 'TOO_LONG']);
		}

		// Check invalid characters
		if (!$this->get_bb_type() || ($this->get_bb_type() === constants::BB_TYPE_EXT && !preg_match('#^([a-z0-9-]+)\.([a-z0-9-]+)$#', $text)) || ($this->get_bb_type() !== constants::BB_TYPE_EXT && !preg_match('#^[a-z0-9-]+$#', $text)))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_varname', 'ILLEGAL_CHARACTERS']);
		}

		// Set the value on our data array
		$this->data['item_varname'] = $text;

		return $this;
	}

	/**
	* Get item description for edit
	*
	* @return string
	*/
	public function get_desc_for_edit()
	{
		// Use defaults if these haven't been set yet
		$text = isset($this->data['item_desc']) ? $this->data['item_desc'] : '';
		$uid = isset($this->data['item_desc_uid']) ? $this->data['item_desc_uid'] : '';
		$options = isset($this->data['item_desc_options']) ? (int) $this->data['item_desc_options'] : 0;

		$text_data = generate_text_for_edit($text, $uid, $options);

		return $text_data['text'];
	}

	/**
	* Get item description for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_desc_for_display($censor = true)
	{
		// If these haven't been set yet; use defaults
		$text = isset($this->data['item_desc']) ? $this->data['item_desc'] : '';
		$uid = isset($this->data['item_desc_uid']) ? $this->data['item_desc_uid'] : '';
		$bitfield = isset($this->data['item_desc_bitfield']) ? $this->data['item_desc_bitfield'] : '';
		$options = isset($this->data['item_desc_options']) ? (int) $this->data['item_desc_options'] : 0;

		return generate_text_for_display($text, $uid, $bitfield, $options, $censor);
	}

	/**
	* Set item description
	*
	* @param string				$text	Item description
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc($text)
	{
		// Override maximum post characters limit
		$this->config['max_post_chars'] = 0;

		// Prepare the text for storage
		$uid = $bitfield = $flags = '';
		generate_text_for_storage($text, $uid, $bitfield, $flags, $this->desc_bbcode_enabled(), $this->desc_urls_enabled(), $this->desc_smilies_enabled());

		// Set the value on our data array
		$this->data['item_desc'] = $text;
		$this->data['item_desc_uid'] = $uid;
		$this->data['item_desc_bitfield'] = $bitfield;
		// Option flags are already set

		return $this;
	}

	/**
	* Check if BBCode is enabled on the item description
	*
	* @return bool
	*/
	public function desc_bbcode_enabled()
	{
		return ($this->data['item_desc_options'] & OPTION_FLAG_BBCODE);
	}

	/**
	* Enable BBCode on the item description
	* This should be called before set_desc(); desc_enable_bbcode()->set_desc()
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_bbcode()
	{
		$this->set_desc_options(OPTION_FLAG_BBCODE);

		return $this;
	}

	/**
	* Disable BBCode on the item description
	* This should be called before set_desc(); desc_disable_bbcode()->set_desc()
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function desc_disable_bbcode()
	{
		$this->set_desc_options(OPTION_FLAG_BBCODE, true);

		return $this;
	}

	/**
	* Check if URLs is enabled on the item description
	*
	* @return bool
	*/
	public function desc_urls_enabled()
	{
		return ($this->data['item_desc_options'] & OPTION_FLAG_LINKS);
	}

	/**
	* Enable URLs on the item description
	* This should be called before set_desc(); desc_enable_urls()->set_desc()
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_urls()
	{
		$this->set_desc_options(OPTION_FLAG_LINKS);

		return $this;
	}

	/**
	* Disable URLs on the item description
	* This should be called before set_desc(); desc_disable_urls()->set_desc()
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function desc_disable_urls()
	{
		$this->set_desc_options(OPTION_FLAG_LINKS, true);

		return $this;
	}

	/**
	* Check if smilies are enabled on the item description
	*
	* @return bool
	*/
	public function desc_smilies_enabled()
	{
		return ($this->data['item_desc_options'] & OPTION_FLAG_SMILIES);
	}

	/**
	* Enable smilies on the item description
	* This should be called before set_desc(); desc_enable_smilies()->set_desc()
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_smilies()
	{
		$this->set_desc_options(OPTION_FLAG_SMILIES);

		return $this;
	}

	/**
	* Disable smilies on the item description
	* This should be called before set_desc(); desc_disable_smilies()->set_desc()
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function desc_disable_smilies()
	{
		$this->set_desc_options(OPTION_FLAG_SMILIES, true);

		return $this;
	}

	/**
	* Set BBCode options for the item description
	*
	* @param int	$value				Value of the option
	* @param bool	$negate				Negate (Unset) option
	* @param bool	$reparse_content	Reparse the content after setting option
	*/
	protected function set_desc_options($value, $negate = false, $reparse_content = true)
	{
		// Set article_text_options to 0 if it does not yet exist
		$this->data['item_desc_options'] = isset($this->data['item_desc_options']) ? $this->data['item_desc_options'] : 0;

		// If we're setting the option and the option is not already set
		if (!$negate && !($this->data['item_desc_options'] & $value))
		{
			// Add the option to the options
			$this->data['item_desc_options'] += $value;
		}

		// If we're unsetting the option and the option is already set
		if ($negate && $this->data['item_desc_options'] & $value)
		{
			// Subtract the option from the options
			$this->data['item_desc_options'] -= $value;
		}

		// Reparse the content
		if ($reparse_content && !empty($this->data['item_desc']))
		{
			$text = $this->data['item_desc'];

			decode_message($text, $this->data['item_desc_uid']);

			$this->set_desc($text);
		}
	}

	/**
	* Get Vietnamese item description for edit
	*
	* @return string
	*/
	public function get_desc_vi_for_edit()
	{
		// Use defaults if these haven't been set yet
		$text = isset($this->data['item_desc_vi']) ? $this->data['item_desc_vi'] : '';
		$uid = isset($this->data['item_desc_vi_uid']) ? $this->data['item_desc_vi_uid'] : '';
		$options = isset($this->data['item_desc_vi_options']) ? (int) $this->data['item_desc_vi_options'] : 0;

		$text_data = generate_text_for_edit($text, $uid, $options);

		return $text_data['text'];
	}

	/**
	* Get Vietnamese item description for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_desc_vi_for_display($censor = true)
	{
		// If these haven't been set yet; use defaults
		$text = isset($this->data['item_desc_vi']) ? $this->data['item_desc_vi'] : '';
		$uid = isset($this->data['item_desc_vi_uid']) ? $this->data['item_desc_vi_uid'] : '';
		$bitfield = isset($this->data['item_desc_vi_bitfield']) ? $this->data['item_desc_vi_bitfield'] : '';
		$options = isset($this->data['item_desc_vi_options']) ? (int) $this->data['item_desc_vi_options'] : 0;

		return generate_text_for_display($text, $uid, $bitfield, $options, $censor);
	}

	/**
	* Set Vietnamese item description
	*
	* @param string				$text	Vietnamese item description
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc_vi($text)
	{
		// Override maximum post characters limit
		$this->config['max_post_chars'] = 0;

		// Prepare the text for storage
		$uid = $bitfield = $flags = '';
		generate_text_for_storage($text, $uid, $bitfield, $flags, $this->desc_vi_bbcode_enabled(), $this->desc_vi_urls_enabled(), $this->desc_vi_smilies_enabled());

		// Set the value on our data array
		$this->data['item_desc_vi'] = $text;
		$this->data['item_desc_vi_uid'] = $uid;
		$this->data['item_desc_vi_bitfield'] = $bitfield;
		// Option flags are already set

		return $this;
	}

	/**
	* Check if BBCode is enabled on the Vietnamese item description
	*
	* @return bool
	*/
	public function desc_vi_bbcode_enabled()
	{
		return ($this->data['item_desc_vi_options'] & OPTION_FLAG_BBCODE);
	}

	/**
	* Enable BBCode on the Vietnamese item description
	* This should be called before set_desc_vi(); desc_vi_enable_bbcode()->set_desc_vi()
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function desc_vi_enable_bbcode()
	{
		$this->set_desc_vi_options(OPTION_FLAG_BBCODE);

		return $this;
	}

	/**
	* Disable BBCode on the Vietnamese item description
	* This should be called before set_desc_vi(); desc_vi_disable_bbcode()->set_desc_vi()
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function desc_vi_disable_bbcode()
	{
		$this->set_desc_vi_options(OPTION_FLAG_BBCODE, true);

		return $this;
	}

	/**
	* Check if URLs is enabled on the Vietnamese item description
	*
	* @return bool
	*/
	public function desc_vi_urls_enabled()
	{
		return ($this->data['item_desc_vi_options'] & OPTION_FLAG_LINKS);
	}

	/**
	* Enable URLs on the Vietnamese item description
	* This should be called before set_desc_vi(); desc_vi_enable_urls()->set_desc_vi()
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function desc_vi_enable_urls()
	{
		$this->set_desc_vi_options(OPTION_FLAG_LINKS);

		return $this;
	}

	/**
	* Disable URLs on the Vietnamese item description
	* This should be called before set_desc_vi(); desc_vi_disable_urls()->set_desc_vi()
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function desc_vi_disable_urls()
	{
		$this->set_desc_vi_options(OPTION_FLAG_LINKS, true);

		return $this;
	}

	/**
	* Check if smilies are enabled on the Vietnamese item description
	*
	* @return bool
	*/
	public function desc_vi_smilies_enabled()
	{
		return ($this->data['item_desc_vi_options'] & OPTION_FLAG_SMILIES);
	}

	/**
	* Enable smilies on the Vietnamese item description
	* This should be called before set_desc_vi(); desc_vi_enable_smilies()->set_desc_vi()
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function desc_vi_enable_smilies()
	{
		$this->set_desc_vi_options(OPTION_FLAG_SMILIES);

		return $this;
	}

	/**
	* Disable smilies on the Vietnamese item description
	* This should be called before set_desc_vi(); desc_vi_disable_smilies()->set_desc_vi()
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function desc_vi_disable_smilies()
	{
		$this->set_desc_vi_options(OPTION_FLAG_SMILIES, true);

		return $this;
	}

	/**
	* Set BBCode options for the Vietnamese item description
	*
	* @param int	$value				Value of the option
	* @param bool	$negate				Negate (Unset) option
	* @param bool	$reparse_content	Reparse the content after setting option
	*/
	protected function set_desc_vi_options($value, $negate = false, $reparse_content = true)
	{
		// Set item_desc_vi_options to 0 if it does not yet exist
		$this->data['item_desc_vi_options'] = isset($this->data['item_desc_vi_options']) ? $this->data['item_desc_vi_options'] : 0;

		// If we're setting the option and the option is not already set
		if (!$negate && !($this->data['item_desc_options'] & $value))
		{
			// Add the option to the options
			$this->data['item_desc_vi_options'] += $value;
		}

		// If we're unsetting the option and the option is already set
		if ($negate && $this->data['item_desc_vi_options'] & $value)
		{
			// Subtract the option from the options
			$this->data['item_desc_vi_options'] -= $value;
		}

		// Reparse the content
		if ($reparse_content && !empty($this->data['item_desc_vi']))
		{
			$text = $this->data['item_desc_vi'];

			decode_message($text, $this->data['item_desc_vi_uid']);

			$this->set_desc_vi($text);
		}
	}

	/**
	* Get the extension property: Style Changes
	*
	* @return bool
	*/
	public function get_ext_style()
	{
		return ($this->get_bb_type() === constants::BB_TYPE_EXT && isset($this->data['item_ext_style'])) ? (bool) $this->data['item_ext_style'] : false;
	}

	/**
	* Set the extension property: Style Changes
	*
	* @param bool				$value	Config value
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_style($value)
	{
		$value = (bool) $value;

		// This is a field only for extensions
		if ($this->get_bb_type() !== constants::BB_TYPE_EXT)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_ext_style'] = $value;

		return $this;
	}

	/**
	* Get the extension property: ACP Style Changes
	*
	* @return bool
	*/
	public function get_ext_acp_style()
	{
		return ($this->get_bb_type() === constants::BB_TYPE_EXT && isset($this->data['item_ext_acp_style'])) ? (bool) $this->data['item_ext_acp_style'] : false;
	}

	/**
	* Set the extension property: ACP Style Changes
	*
	* @param bool				$value	Config value
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_acp_style($value)
	{
		$value = (bool) $value;

		// This is a field only for extensions
		if ($this->get_bb_type() !== constants::BB_TYPE_EXT)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_ext_acp_style'] = $value;

		return $this;
	}

	/**
	* Get the extension property: Language Changes
	*
	* @return bool
	*/
	public function get_ext_lang()
	{
		return ($this->get_bb_type() === constants::BB_TYPE_EXT && isset($this->data['item_ext_lang'])) ? (bool) $this->data['item_ext_lang'] : false;
	}

	/**
	* Set the extension property: Language Changes
	*
	* @param bool				$value	Config value
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_lang($value)
	{
		$value = (bool) $value;

		// This is a field only for extensions
		if ($this->get_bb_type() !== constants::BB_TYPE_EXT)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_ext_lang'] = $value;

		return $this;
	}

	/**
	* Get the extension property: Schema Changes
	*
	* @return bool
	*/
	public function get_ext_db_schema()
	{
		return ($this->get_bb_type() === constants::BB_TYPE_EXT && isset($this->data['item_ext_db_schema'])) ? (bool) $this->data['item_ext_db_schema'] : false;
	}

	/**
	* Set the extension property: Schema Changes
	*
	* @param bool				$value	Config value
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_db_schema($value)
	{
		$value = (bool) $value;

		// This is a field only for extensions
		if ($this->get_bb_type() !== constants::BB_TYPE_EXT)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_ext_db_schema'] = $value;

		return $this;
	}

	/**
	* Get the extension property: Data Changes
	*
	* @return bool
	*/
	public function get_ext_db_data()
	{
		return ($this->get_bb_type() === constants::BB_TYPE_EXT && isset($this->data['item_ext_db_data'])) ? (bool) $this->data['item_ext_db_data'] : false;
	}

	/**
	* Set the extension property: Data Changes
	*
	* @param bool				$value	Config value
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_db_data($value)
	{
		$value = (bool) $value;

		// This is a field only for extensions
		if ($this->get_bb_type() !== constants::BB_TYPE_EXT)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_ext_db_data'] = $value;

		return $this;
	}

	/**
	* Get the style property: Number of Presets
	*
	* @return int
	*/
	public function get_style_presets()
	{
		return (($this->get_bb_type() === constants::BB_TYPE_STYLE || $this->get_bb_type() === constants::BB_TYPE_ACP_STYLE) && isset($this->data['item_style_presets'])) ? (int) $this->data['item_style_presets'] : 0;
	}

	/**
	* Set the style property: Number of Presets
	*
	* @param int				$value	Config value
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_presets($value)
	{
		$value = (int) $value;

		// This is a field only for styles
		if ($this->get_bb_type() !== constants::BB_TYPE_STYLE && $this->get_bb_type() !== constants::BB_TYPE_ACP_STYLE)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_style_presets'] = $value;

		return $this;
	}

	/**
	* Get the style property: All Presets in One Style
	*
	* @return bool
	*/
	public function get_style_presets_aio()
	{
		return (($this->get_bb_type() === constants::BB_TYPE_STYLE || $this->get_bb_type() === constants::BB_TYPE_ACP_STYLE) && isset($this->data['item_style_presets_aio'])) ? (bool) $this->data['item_style_presets_aio'] : false;
	}

	/**
	* Set the style property: All Presets in One Style
	*
	* @param bool				$value	Config value
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_presets_aio($value)
	{
		$value = (bool) $value;

		// This is a field only for styles
		if ($this->get_bb_type() !== constants::BB_TYPE_STYLE && $this->get_bb_type() !== constants::BB_TYPE_ACP_STYLE)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_style_presets_aio'] = $value;

		return $this;
	}

	/**
	* Get the style property: Source Files
	*
	* @return bool
	*/
	public function get_style_source()
	{
		return (($this->get_bb_type() === constants::BB_TYPE_STYLE || $this->get_bb_type() === constants::BB_TYPE_ACP_STYLE) && isset($this->data['item_style_source'])) ? (bool) $this->data['item_style_source'] : false;
	}

	/**
	* Set the style property: Source Files
	*
	* @param bool				$value	Config value
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_source($value)
	{
		$value = (bool) $value;

		// This is a field only for styles
		if ($this->get_bb_type() !== constants::BB_TYPE_STYLE && $this->get_bb_type() !== constants::BB_TYPE_ACP_STYLE)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_style_source'] = $value;

		return $this;
	}

	/**
	* Get the style property: Responsive Support
	*
	* @return bool
	*/
	public function get_style_responsive()
	{
		return (($this->get_bb_type() === constants::BB_TYPE_STYLE || $this->get_bb_type() === constants::BB_TYPE_ACP_STYLE) && isset($this->data['item_style_responsive'])) ? (bool) $this->data['item_style_responsive'] : false;
	}

	/**
	* Set the style property: Responsive Support
	*
	* @param bool				$value	Config value
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_responsive($value)
	{
		$value = (bool) $value;

		// This is a field only for styles
		if ($this->get_bb_type() !== constants::BB_TYPE_STYLE && $this->get_bb_type() !== constants::BB_TYPE_ACP_STYLE)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_style_responsive'] = $value;

		return $this;
	}

	/**
	* Get the style property: Bootstrap Support
	*
	* @return bool
	*/
	public function get_style_bootstrap()
	{
		return (($this->get_bb_type() === constants::BB_TYPE_STYLE || $this->get_bb_type() === constants::BB_TYPE_ACP_STYLE) && isset($this->data['item_style_bootstrap'])) ? (bool) $this->data['item_style_bootstrap'] : false;
	}

	/**
	* Set the style property: Bootstrap Support
	*
	* @param bool				$value	Config value
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_bootstrap($value)
	{
		$value = (bool) $value;

		// This is a field only for styles
		if ($this->get_bb_type() !== constants::BB_TYPE_STYLE && $this->get_bb_type() !== constants::BB_TYPE_ACP_STYLE)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('bb_type');
		}

		// Set the value on our data array
		$this->data['item_style_bootstrap'] = $value;

		return $this;
	}

	/**
	* Get the item price
	*
	* @return int
	*/
	public function get_price()
	{
		return isset($this->data['item_price']) ? (int) $this->data['item_price'] : 0;
	}

	/**
	* Set the item price
	*
	* @param int					$value	Item price
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_price($value)
	{
		if (!isset($this->data['item_price']))
		{
			$this->data['item_price'] = (int) $value;
		}

		return $this;
	}

	/**
	* Get the item URL
	*
	* @return string
	*/
	public function get_url()
	{
		return isset($this->data['item_url']) ? (string) $this->data['item_url'] : '';
	}

	/**
	* Set the item URL
	*
	* @param string				$text	Item URL
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_url($text)
	{
		if (!isset($this->data['item_url']))
		{
			$this->data['item_url'] = (string) $text;
		}

		return $this;
	}

	/**
	* Get the item GitHub URL
	*
	* @return string
	*/
	public function get_github()
	{
		return isset($this->data['item_github']) ? (string) $this->data['item_github'] : '';
	}

	/**
	* Set the item GitHub URL
	*
	* @param string				$text	Item GitHub URL
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_github($text)
	{
		if (!isset($this->data['item_github']))
		{
			$this->data['item_github'] = (string) $text;
		}

		return $this;
	}

	/**
	* Get the time of adding item
	*
	* @return int
	*/
	public function get_added()
	{
		return isset($this->data['item_added']) ? (int) $this->data['item_added'] : 0;
	}

	/**
	* Set the time of adding item
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function set_added()
	{
		// Set the value on our data array
		$this->data['item_added'] = time();

		return $this;
	}

	/**
	* Get the last updated time of item
	*
	* @return int
	*/
	public function get_updated()
	{
		return isset($this->data['item_updated']) ? (int) $this->data['item_updated'] : 0;
	}

	/**
	* Set the last updated time of item
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function set_updated()
	{
		// Set the value on our data array
		$this->data['item_updated'] = time();

		return $this;
	}
}
