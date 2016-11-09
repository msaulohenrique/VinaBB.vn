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
* Entity for a single page
*/
class page implements page_interface
{
	/**
	* Data for this entity
	*
	* @var array
	*	page_id
	*	page_name
	*	page_name_vi
	*	page_varname
	*	page_desc
	*	page_desc_vi
	*	page_text
	*	page_text_uid
	*	page_text_bitfield
	*	page_text_options
	*	page_text_vi
	*	page_text_vi_uid
	*	page_text_vi_bitfield
	*	page_text_vi_options
	*	page_enable
	*/
	protected $data;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string */
	protected $table_name;

	/**
	* Constructor
	*
	* @param \phpbb\config\config				$config			Config object
	* @param \phpbb\db\driver\driver_interface	$db				Database object
	* @param string								$table_name		Table name
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, $table_name)
	{
		$this->config = $config;
		$this->db = $db;
		$this->table_name = $table_name;
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Page ID
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id = 0)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE cat_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('page_id');
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
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = [];

		// All of our fields
		$fields = [
			'page_id'			=> 'integer',
			'page_name'			=> 'set_name',
			'page_name_vi'		=> 'set_name_vi',
			'page_varname'		=> 'set_varname',
			'page_desc'			=> 'set_desc',
			'page_desc_vi'		=> 'set_desc_vi',
			'page_enable'		=> 'bool',

			// We do not pass to set_text() or set_text_vi() as generate_text_for_storage() would run twice
			'page_text'				=> 'string',
			'page_text_uid'			=> 'string',
			'page_text_bitfield'	=> 'string',
			'page_text_options'		=> 'integer',
			'page_text_vi'			=> 'string',
			'page_text_vi_uid'		=> 'string',
			'page_text_vi_bitfield'	=> 'string',
			'page_text_vi_options'	=> 'integer'
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
		$validate_unsigned = ['page_id', 'page_text_options', 'page_text_vi_options'];

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
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['page_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('page_id');
		}

		$sql = 'INSERT INTO ' . $this->table_name . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['page_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['page_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('page_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['page_id' => null]);

		$sql = 'UPDATE ' . $this->table_name . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE page_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the page_id
	*
	* @return int page_id
	*/
	public function get_id()
	{
		return isset($this->data['page_id']) ? (int) $this->data['page_id'] : 0;
	}

	/**
	* Get the page title
	*
	* @return string Page title
	*/
	public function get_name()
	{
		return isset($this->data['page_name']) ? (string) $this->data['page_name'] : '';
	}

	/**
	* Set the page title
	*
	* @param string				$name	Page title
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($name)
	{
		$name = (string) $name;

		// This is a required field
		if (empty($name))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_name', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($name, constants::MAX_PAGE_NAME) != $name)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['page_name'] = $name;

		return $this;
	}

	/**
	* Get the Vietnamese page title
	*
	* @return string Vietnamese page title
	*/
	public function get_name_vi()
	{
		return isset($this->data['page_name']) ? (string) $this->data['page_name'] : '';
	}

	/**
	* Set the Vietnamese page title
	*
	* @param string				$name	Vietnamese page title
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_vi($name)
	{
		$name = (string) $name;

		// This is a required field
		if (empty($name))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_name_vi', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($name, constants::MAX_PAGE_NAME) != $name)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_name_vi', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['page_name_vi'] = $name;

		return $this;
	}

	/**
	* Get the page varname
	*
	* @return string Page varname
	*/
	public function get_varname()
	{
		return isset($this->data['cat_varname']) ? (string) $this->data['cat_varname'] : '';
	}

	/**
	* Set the page varname
	*
	* @param int				$varname	Page varname
	* @return page_interface	$this		Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_varname($varname)
	{
		$varname = strtolower($varname);

		// This is a required field
		if (empty($varname))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_varname', 'FIELD_MISSING']);
		}

		// Check the max length
		if (truncate_string($varname, constants::MAX_PAGE_VARNAME) != $varname)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_varname', 'TOO_LONG']);
		}

		// Check invalid characters
		if (!preg_match('#^[a-z0-9-]+$#', $varname))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_varname', 'ILLEGAL_CHARACTERS']);
		}

		// This field value must be unique
		if (!$this->get_id() || ($this->get_id() && $this->get_varname() !== '' && $this->get_varname() != $varname))
		{
			$sql = 'SELECT 1
				FROM ' . $this->table_name . "
				WHERE page_varname = '" . $this->db->sql_escape($varname) . "'
					AND page_id <> " . $this->get_id();
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row)
			{
				throw new \vinabb\web\exceptions\unexpected_value(['page_varname', 'NOT_UNIQUE']);
			}
		}

		// Set the value on our data array
		$this->data['page_varname'] = $varname;

		return $this;
	}

	/**
	* Get the page description
	*
	* @return string Page description
	*/
	public function get_desc()
	{
		return isset($this->data['cat_desc']) ? (string) $this->data['cat_desc'] : '';
	}

	/**
	* Set the page description
	*
	* @param string				$desc	Page description
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc($desc)
	{
		if (!isset($this->data['cat_desc']))
		{
			$this->data['cat_desc'] = (string) $desc;
		}

		return $this;
	}

	/**
	* Get the Vietnamese page description
	*
	* @return string Vietnamese page description
	*/
	public function get_desc_vi()
	{
		return isset($this->data['cat_desc_vi']) ? (string) $this->data['cat_desc_vi'] : '';
	}

	/**
	* Set the Vietnamese page description
	*
	* @param string				$desc	Vietnamese page description
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc_vi($desc)
	{
		if (!isset($this->data['cat_desc_vi']))
		{
			$this->data['cat_desc_vi'] = (string) $desc;
		}

		return $this;
	}

	/**
	* Get page content for edit
	*
	* @return string
	*/
	public function get_text_for_edit()
	{
		// Use defaults if these haven't been set yet
		$text = isset($this->data['page_text']) ? $this->data['page_text'] : '';
		$uid = isset($this->data['page_text_uid']) ? $this->data['page_text_uid'] : '';
		$options = isset($this->data['page_text_options']) ? (int) $this->data['page_text_options'] : 0;

		$text_data = generate_text_for_edit($text, $uid, $options);

		return $text_data['text'];
	}

	/**
	* Get page content for display
	*
	* @param bool $censor_text True to censor the text
	* @return string
	*/
	public function get_text_for_display($censor_text = true)
	{
		// If these haven't been set yet; use defaults
		$text = isset($this->data['page_text']) ? $this->data['page_text'] : '';
		$uid = isset($this->data['page_text_uid']) ? $this->data['page_text_uid'] : '';
		$bitfield = isset($this->data['page_text_bitfield']) ? $this->data['page_text_bitfield'] : '';
		$options = isset($this->data['page_text_options']) ? (int) $this->data['page_text_options'] : 0;

		return generate_text_for_display($text, $uid, $bitfield, $options, $censor_text);
	}

	/**
	* Set page content
	*
	* @param string				$text	Page content
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text($text)
	{
		// Override maximum post characters limit
		$this->config['max_post_chars'] = 0;

		// Prepare the text for storage
		$uid = $bitfield = $flags = '';
		generate_text_for_storage($text, $uid, $bitfield, $flags, $this->text_bbcode_enabled(), $this->text_urls_enabled(), $this->text_smilies_enabled());

		// Set the value on our data array
		$this->data['page_text'] = $text;
		$this->data['page_text_uid'] = $uid;
		$this->data['page_text_bitfield'] = $bitfield;
		// Option flags are already set

		return $this;
	}

	/**
	* Check if BBCode is enabled on the page content
	*
	* @return bool
	*/
	public function text_bbcode_enabled()
	{
		return ($this->data['page_text_options'] & OPTION_FLAG_BBCODE);
	}

	/**
	* Enable BBCode on the page content
	* This should be called before set_text(); text_enable_bbcode()->set_text()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_bbcode()
	{
		$this->set_text_options(OPTION_FLAG_BBCODE);

		return $this;
	}

	/**
	* Disable BBCode on the page content
	* This should be called before set_text(); text_disable_bbcode()->set_text()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_disable_bbcode()
	{
		$this->set_text_options(OPTION_FLAG_BBCODE, true);

		return $this;
	}

	/**
	* Check if URLs is enabled on the page content
	*
	* @return bool
	*/
	public function text_urls_enabled()
	{
		return ($this->data['page_text_options'] & OPTION_FLAG_LINKS);
	}

	/**
	* Enable URLs on the page content
	* This should be called before set_text(); text_enable_urls()->set_text()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_urls()
	{
		$this->set_text_options(OPTION_FLAG_LINKS);

		return $this;
	}

	/**
	* Disable URLs on the page content
	* This should be called before set_text(); text_disable_urls()->set_text()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_disable_urls()
	{
		$this->set_text_options(OPTION_FLAG_LINKS, true);

		return $this;
	}

	/**
	* Check if smilies are enabled on the page content
	*
	* @return bool
	*/
	public function text_smilies_enabled()
	{
		return ($this->data['page_text_options'] & OPTION_FLAG_SMILIES);
	}

	/**
	* Enable smilies on the page content
	* This should be called before set_text(); text_enable_smilies()->set_text()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_smilies()
	{
		$this->set_text_options(OPTION_FLAG_SMILIES);

		return $this;
	}

	/**
	* Disable smilies on the page content
	* This should be called before set_text(); text_disable_smilies()->set_text()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_disable_smilies()
	{
		$this->set_text_options(OPTION_FLAG_SMILIES, true);

		return $this;
	}

	/**
	* Get Vietnamese page content for edit
	*
	* @return string
	*/
	public function get_text_vi_for_edit()
	{
		// Use defaults if these haven't been set yet
		$text = isset($this->data['page_text_vi']) ? $this->data['page_text_vi'] : '';
		$uid = isset($this->data['page_text_vi_uid']) ? $this->data['page_text_vi_uid'] : '';
		$options = isset($this->data['page_text_vi_options']) ? (int) $this->data['page_text_vi_options'] : 0;

		$text_data = generate_text_for_edit($text, $uid, $options);

		return $text_data['text'];
	}

	/**
	* Get Vietnamese page content for display
	*
	* @param bool $censor_text True to censor the text
	* @return string
	*/
	public function get_text_vi_for_display($censor_text = true)
	{
		// If these haven't been set yet; use defaults
		$text = isset($this->data['page_text_vi']) ? $this->data['page_text_vi'] : '';
		$uid = isset($this->data['page_text_vi_uid']) ? $this->data['page_text_vi_uid'] : '';
		$bitfield = isset($this->data['page_text_vi_bitfield']) ? $this->data['page_text_vi_bitfield'] : '';
		$options = isset($this->data['page_text_vi_options']) ? (int) $this->data['page_text_vi_options'] : 0;

		return generate_text_for_display($text, $uid, $bitfield, $options, $censor_text);
	}

	/**
	* Set Vietnamese page content
	*
	* @param string				$text	Vietnamese page content
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text_vi($text)
	{
		// Override maximum post characters limit
		$this->config['max_post_chars'] = 0;

		// Prepare the text for storage
		$uid = $bitfield = $flags = '';
		generate_text_for_storage($text, $uid, $bitfield, $flags, $this->text_bbcode_enabled(), $this->text_urls_enabled(), $this->text_smilies_enabled());

		// Set the value on our data array
		$this->data['page_text_vi'] = $text;
		$this->data['page_text_vi_uid'] = $uid;
		$this->data['page_text_vi_bitfield'] = $bitfield;
		// Option flags are already set

		return $this;
	}

	/**
	* Check if BBCode is enabled on the Vietnamese page content
	*
	* @return bool
	*/
	public function text_vi_bbcode_enabled()
	{
		return ($this->data['page_text_vi_options'] & OPTION_FLAG_BBCODE);
	}

	/**
	* Enable BBCode on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_enable_bbcode()->set_text_vi()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_enable_bbcode()
	{
		$this->set_text_vi_options(OPTION_FLAG_BBCODE);

		return $this;
	}

	/**
	* Disable BBCode on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_disable_bbcode()->set_text_vi()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_disable_bbcode()
	{
		$this->set_text_vi_options(OPTION_FLAG_BBCODE, true);

		return $this;
	}

	/**
	* Check if URLs is enabled on the Vietnamese page content
	*
	* @return bool
	*/
	public function text_vi_urls_enabled()
	{
		return ($this->data['page_text_vi_options'] & OPTION_FLAG_LINKS);
	}

	/**
	* Enable URLs on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_enable_urls()->set_text_vi()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_enable_urls()
	{
		$this->set_text_vi_options(OPTION_FLAG_LINKS);

		return $this;
	}

	/**
	* Disable URLs on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_disable_urls()->set_text_vi()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_disable_urls()
	{
		$this->set_text_vi_options(OPTION_FLAG_LINKS, true);

		return $this;
	}

	/**
	* Check if smilies are enabled on the Vietnamese page content
	*
	* @return bool
	*/
	public function text_vi_smilies_enabled()
	{
		return ($this->data['page_text_vi_options'] & OPTION_FLAG_SMILIES);
	}

	/**
	* Enable smilies on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_enable_smilies()->set_text_vi()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_enable_smilies()
	{
		$this->set_text_vi_options(OPTION_FLAG_SMILIES);

		return $this;
	}

	/**
	* Disable smilies on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_disable_smilies()->set_text_vi()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_disable_smilies()
	{
		$this->set_text_vi_options(OPTION_FLAG_SMILIES, true);

		return $this;
	}

	/**
	* Get page display setting
	*
	* @return bool
	*/
	public function get_enable()
	{
		return isset($this->data['page_enable']) ? (bool) $this->data['page_enable'] : true;
	}

	/**
	* Set page display setting
	*
	* @param bool				$option	Page display setting
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable($option)
	{
		$option = (bool) $option;

		// Set the value on our data array
		$this->data['page_enable'] = $option;

		return $this;
	}

	/**
	* Set BBCode options for the page content
	*
	* @param int	$option_value		Value of the option
	* @param bool	$negate				Negate (Unset) option
	* @param bool	$reparse_content	Reparse the content after setting option
	*/
	protected function set_text_options($option_value, $negate = false, $reparse_content = true)
	{
		// Set page_text_options to 0 if it does not yet exist
		$this->data['page_text_options'] = isset($this->data['page_text_options']) ? $this->data['page_text_options'] : 0;

		// If we're setting the option and the option is not already set
		if (!$negate && !($this->data['page_text_options'] & $option_value))
		{
			// Add the option to the options
			$this->data['page_text_options'] += $option_value;
		}

		// If we're unsetting the option and the option is already set
		if ($negate && $this->data['page_text_options'] & $option_value)
		{
			// Subtract the option from the options
			$this->data['page_text_options'] -= $option_value;
		}

		// Reparse the content
		if ($reparse_content && !empty($this->data['page_text']))
		{
			$text = $this->data['page_text'];

			decode_message($text, $this->data['page_text_uid']);

			$this->set_text($text);
		}
	}

	/**
	* Set BBCode options for the Vietnamese page content
	*
	* @param int	$option_value		Value of the option
	* @param bool	$negate				Negate (Unset) option
	* @param bool	$reparse_content	Reparse the content after setting option
	*/
	protected function set_text_vi_options($option_value, $negate = false, $reparse_content = true)
	{
		// Set page_text_vi_options to 0 if it does not yet exist
		$this->data['page_text_vi_options'] = isset($this->data['page_text_vi_options']) ? $this->data['page_text_vi_options'] : 0;

		// If we're setting the option and the option is not already set
		if (!$negate && !($this->data['page_text_vi_options'] & $option_value))
		{
			// Add the option to the options
			$this->data['page_text_vi_options'] += $option_value;
		}

		// If we're unsetting the option and the option is already set
		if ($negate && $this->data['page_text_vi_options'] & $option_value)
		{
			// Subtract the option from the options
			$this->data['page_text_vi_options'] -= $option_value;
		}

		// Reparse the content
		if ($reparse_content && !empty($this->data['page_text_vi']))
		{
			$text = $this->data['page_text_vi'];

			decode_message($text, $this->data['page_text_vi_uid']);

			$this->set_text_vi($text);
		}
	}
}
