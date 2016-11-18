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
	*	page_enable_guest
	*	page_enable_bot
	*	page_enable_new_user
	*	page_enable_user
	*	page_enable_mod
	*	page_enable_global_mod
	*	page_enable_admin
	*	page_enable_founder
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
			WHERE page_id = ' . (int) $id;
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
			'page_id'					=> 'integer',
			'page_name'					=> 'set_name',
			'page_name_vi'				=> 'set_name_vi',
			'page_varname'				=> 'set_varname',
			'page_desc'					=> 'set_desc',
			'page_desc_vi'				=> 'set_desc_vi',
			'page_enable'				=> 'set_enable',
			'page_enable_guest'			=> 'set_enable_guest',
			'page_enable_bot'			=> 'set_enable_bot',
			'page_enable_new_user'		=> 'set_enable_new_user',
			'page_enable_user'			=> 'set_enable_user',
			'page_enable_mod'			=> 'set_enable_mod',
			'page_enable_global_mod'	=> 'set_enable_global_mod',
			'page_enable_admin'			=> 'set_enable_admin',
			'page_enable_founder'		=> 'set_enable_founder',

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

		// Make extra sure there is no ID set
		unset($this->data['page_id']);

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
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['page_id']) ? (int) $this->data['page_id'] : 0;
	}

	/**
	* Get the page title
	*
	* @return string
	*/
	public function get_name()
	{
		return isset($this->data['page_name']) ? (string) $this->data['page_name'] : '';
	}

	/**
	* Set the page title
	*
	* @param string				$text	Page title
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_name', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_PAGE_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['page_name'] = $text;

		return $this;
	}

	/**
	* Get the Vietnamese page title
	*
	* @return string
	*/
	public function get_name_vi()
	{
		return isset($this->data['page_name_vi']) ? (string) $this->data['page_name_vi'] : '';
	}

	/**
	* Set the Vietnamese page title
	*
	* @param string				$text	Vietnamese page title
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_vi($text)
	{
		$text = (string) $text;

		// Check the max length
		if (truncate_string($text, constants::MAX_PAGE_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_name_vi', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['page_name_vi'] = $text;

		return $this;
	}

	/**
	* Get the page varname
	*
	* @return string
	*/
	public function get_varname()
	{
		return isset($this->data['page_varname']) ? (string) $this->data['page_varname'] : '';
	}

	/**
	* Set the page varname
	*
	* @param int				$text	Page varname
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_varname($text)
	{
		$text = strtolower($text);

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_varname', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_PAGE_VARNAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_varname', 'TOO_LONG']);
		}

		// Check invalid characters
		if (!preg_match('#^[a-z0-9-]+$#', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['page_varname', 'INVALID']);
		}

		// This field value must be unique
		if (!$this->get_id() || ($this->get_id() && $this->get_varname() !== '' && $this->get_varname() != $text))
		{
			$sql = 'SELECT 1
				FROM ' . $this->table_name . "
				WHERE page_varname = '" . $this->db->sql_escape($text) . "'
					AND page_id <> " . $this->get_id();
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row)
			{
				throw new \vinabb\web\exceptions\unexpected_value(['page_varname', 'DUPLICATE', $text]);
			}
		}

		// Set the value on our data array
		$this->data['page_varname'] = $text;

		return $this;
	}

	/**
	* Get the page description
	*
	* @return string
	*/
	public function get_desc()
	{
		return isset($this->data['page_desc']) ? (string) $this->data['page_desc'] : '';
	}

	/**
	* Set the page description
	*
	* @param string				$text	Page description
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc($text)
	{
		$this->data['page_desc'] = (string) $text;

		return $this;
	}

	/**
	* Get the Vietnamese page description
	*
	* @return string
	*/
	public function get_desc_vi()
	{
		return isset($this->data['page_desc_vi']) ? (string) $this->data['page_desc_vi'] : '';
	}

	/**
	* Set the Vietnamese page description
	*
	* @param string				$text	Vietnamese page description
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc_vi($text)
	{
		$this->data['page_desc_vi'] = (string) $text;

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
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_text_for_display($censor = true)
	{
		// If these haven't been set yet; use defaults
		$text = isset($this->data['page_text']) ? $this->data['page_text'] : '';
		$uid = isset($this->data['page_text_uid']) ? $this->data['page_text_uid'] : '';
		$bitfield = isset($this->data['page_text_bitfield']) ? $this->data['page_text_bitfield'] : '';
		$options = isset($this->data['page_text_options']) ? (int) $this->data['page_text_options'] : 0;

		return generate_text_for_display($text, $uid, $bitfield, $options, $censor);
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
		return (bool) ($this->data['page_text_options'] & OPTION_FLAG_BBCODE);
	}

	/**
	* Enable/Disable BBCode on the page content
	* This should be called before set_text(); text_enable_bbcode()->set_text()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_bbcode($enable)
	{
		$this->set_text_options(OPTION_FLAG_BBCODE, !$enable);

		return $this;
	}

	/**
	* Check if URLs is enabled on the page content
	*
	* @return bool
	*/
	public function text_urls_enabled()
	{
		return (bool) ($this->data['page_text_options'] & OPTION_FLAG_LINKS);
	}

	/**
	* Enable/Disable URLs on the page content
	* This should be called before set_text(); text_enable_urls()->set_text()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_urls($enable)
	{
		$this->set_text_options(OPTION_FLAG_LINKS, !$enable);

		return $this;
	}

	/**
	* Check if smilies are enabled on the page content
	*
	* @return bool
	*/
	public function text_smilies_enabled()
	{
		return (bool) ($this->data['page_text_options'] & OPTION_FLAG_SMILIES);
	}

	/**
	* Enable/Disable smilies on the page content
	* This should be called before set_text(); text_enable_smilies()->set_text()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_smilies($enable)
	{
		$this->set_text_options(OPTION_FLAG_SMILIES, !$enable);

		return $this;
	}

	/**
	* Set BBCode options for the page content
	*
	* @param int	$value				Value of the option
	* @param bool	$negate				Negate (Unset) option
	* @param bool	$reparse_content	Reparse the content after setting option
	*/
	protected function set_text_options($value, $negate = false, $reparse_content = true)
	{
		// Set page_text_options to 0 if it does not yet exist
		$this->data['page_text_options'] = isset($this->data['page_text_options']) ? $this->data['page_text_options'] : 0;

		// If we're setting the option and the option is not already set
		if (!$negate && !($this->data['page_text_options'] & $value))
		{
			// Add the option to the options
			$this->data['page_text_options'] += $value;
		}

		// If we're unsetting the option and the option is already set
		if ($negate && $this->data['page_text_options'] & $value)
		{
			// Subtract the option from the options
			$this->data['page_text_options'] -= $value;
		}

		// Reparse the content
		if ($reparse_content && isset($this->data['page_text']) && $this->data['page_text'] != '')
		{
			$text = $this->data['page_text'];

			decode_message($text, $this->data['page_text_uid']);

			$this->set_text($text);
		}
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
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_text_vi_for_display($censor = true)
	{
		// If these haven't been set yet; use defaults
		$text = isset($this->data['page_text_vi']) ? $this->data['page_text_vi'] : '';
		$uid = isset($this->data['page_text_vi_uid']) ? $this->data['page_text_vi_uid'] : '';
		$bitfield = isset($this->data['page_text_vi_bitfield']) ? $this->data['page_text_vi_bitfield'] : '';
		$options = isset($this->data['page_text_vi_options']) ? (int) $this->data['page_text_vi_options'] : 0;

		return generate_text_for_display($text, $uid, $bitfield, $options, $censor);
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
		return (bool) ($this->data['page_text_vi_options'] & OPTION_FLAG_BBCODE);
	}

	/**
	* Enable/Disable BBCode on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_enable_bbcode()->set_text_vi()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_enable_bbcode($enable)
	{
		$this->set_text_vi_options(OPTION_FLAG_BBCODE, !$enable);

		return $this;
	}

	/**
	* Check if URLs is enabled on the Vietnamese page content
	*
	* @return bool
	*/
	public function text_vi_urls_enabled()
	{
		return (bool) ($this->data['page_text_vi_options'] & OPTION_FLAG_LINKS);
	}

	/**
	* Enable/Disable URLs on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_enable_urls()->set_text_vi()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_enable_urls($enable)
	{
		$this->set_text_vi_options(OPTION_FLAG_LINKS, !$enable);

		return $this;
	}

	/**
	* Check if smilies are enabled on the Vietnamese page content
	*
	* @return bool
	*/
	public function text_vi_smilies_enabled()
	{
		return (bool) ($this->data['page_text_vi_options'] & OPTION_FLAG_SMILIES);
	}

	/**
	* Enable/Disable smilies on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_enable_smilies()->set_text_vi()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_enable_smilies($enable)
	{
		$this->set_text_vi_options(OPTION_FLAG_SMILIES, !$enable);

		return $this;
	}

	/**
	* Set BBCode options for the Vietnamese page content
	*
	* @param int	$value				Value of the option
	* @param bool	$negate				Negate (Unset) option
	* @param bool	$reparse_content	Reparse the content after setting option
	*/
	protected function set_text_vi_options($value, $negate = false, $reparse_content = true)
	{
		// Set page_text_vi_options to 0 if it does not yet exist
		$this->data['page_text_vi_options'] = isset($this->data['page_text_vi_options']) ? $this->data['page_text_vi_options'] : 0;

		// If we're setting the option and the option is not already set
		if (!$negate && !($this->data['page_text_vi_options'] & $value))
		{
			// Add the option to the options
			$this->data['page_text_vi_options'] += $value;
		}

		// If we're unsetting the option and the option is already set
		if ($negate && $this->data['page_text_vi_options'] & $value)
		{
			// Subtract the option from the options
			$this->data['page_text_vi_options'] -= $value;
		}

		// Reparse the content
		if ($reparse_content && isset($this->data['page_text_vi']) && $this->data['page_text_vi'] != '')
		{
			$text = $this->data['page_text_vi'];

			decode_message($text, $this->data['page_text_vi_uid']);

			$this->set_text_vi($text);
		}
	}

	/**
	* Get page display setting in template
	*
	* @return bool
	*/
	public function get_enable()
	{
		return isset($this->data['page_enable']) ? (bool) $this->data['page_enable'] : true;
	}

	/**
	* Set page display setting in template
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable($value)
	{
		$this->data['page_enable'] = (bool) $value;

		return $this;
	}

	/**
	* Get page display setting for guests
	*
	* @return bool
	*/
	public function get_enable_guest()
	{
		return isset($this->data['page_enable_guest']) ? (bool) $this->data['page_enable_guest'] : true;
	}

	/**
	* Set page display setting for guests
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_guest($value)
	{
		$this->data['page_enable_guest'] = (bool) $value;

		return $this;
	}

	/**
	* Get page display setting for bots
	*
	* @return bool
	*/
	public function get_enable_bot()
	{
		return isset($this->data['page_enable_bot']) ? (bool) $this->data['page_enable_bot'] : true;
	}

	/**
	* Set page display setting for bots
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_bot($value)
	{
		$this->data['page_enable_bot'] = (bool) $value;

		return $this;
	}

	/**
	* Get page display setting for newly registered users
	*
	* @return bool
	*/
	public function get_enable_new_user()
	{
		return isset($this->data['page_enable_new_user']) ? (bool) $this->data['page_enable_new_user'] : true;
	}

	/**
	* Set page display setting for newly registered users
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_new_user($value)
	{
		$this->data['page_enable_new_user'] = (bool) $value;

		return $this;
	}

	/**
	* Get page display setting for registered users
	*
	* @return bool
	*/
	public function get_enable_user()
	{
		return isset($this->data['page_enable_user']) ? (bool) $this->data['page_enable_user'] : true;
	}

	/**
	* Set page display setting for registered users
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_user($value)
	{
		$this->data['page_enable_user'] = (bool) $value;

		return $this;
	}

	/**
	* Get page display setting for moderators
	*
	* @return bool
	*/
	public function get_enable_mod()
	{
		return isset($this->data['page_enable_mod']) ? (bool) $this->data['page_enable_mod'] : true;
	}

	/**
	* Set page display setting for moderators
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_mod($value)
	{
		$this->data['page_enable_mod'] = (bool) $value;

		return $this;
	}

	/**
	* Get page display setting for global moderators
	*
	* @return bool
	*/
	public function get_enable_global_mod()
	{
		return isset($this->data['page_enable_global_mod']) ? (bool) $this->data['page_enable_global_mod'] : true;
	}

	/**
	* Set page display setting for global moderators
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_global_mod($value)
	{
		$this->data['page_enable_global_mod'] = (bool) $value;

		return $this;
	}

	/**
	* Get page display setting for administrators
	*
	* @return bool
	*/
	public function get_enable_admin()
	{
		return isset($this->data['page_enable_admin']) ? (bool) $this->data['page_enable_admin'] : true;
	}

	/**
	* Set page display setting for administrators
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_admin($value)
	{
		$this->data['page_enable_admin'] = (bool) $value;

		return $this;
	}

	/**
	* Get page display setting for founders
	*
	* @return bool
	*/
	public function get_enable_founder()
	{
		return isset($this->data['page_enable_founder']) ? (bool) $this->data['page_enable_founder'] : true;
	}

	/**
	* Set page display setting for founders
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_founder($value)
	{
		$this->data['page_enable_founder'] = (bool) $value;

		return $this;
	}
}
