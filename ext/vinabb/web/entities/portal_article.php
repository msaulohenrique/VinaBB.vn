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
* Entity for a single article
*/
class portal_article implements portal_article_interface
{
	/**
	* Data for this entity
	*
	* @var array
	*	article_id
	*	cat_id
	*	article_name
	*	article_name_seo
	*	article_lang
	*	article_img
	*	article_desc
	*	article_text
	*	article_text_uid
	*	article_text_bitfield
	*	article_text_options
	*	article_enable
	*	article_views
	*	article_time
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

	/**
	* Constructor
	*
	* @param \phpbb\config\config				$config			Config object
	* @param \phpbb\db\driver\driver_interface	$db				Database object
	* @param string								$table_name		Table name
	* @param string								$cat_table_name	Table name of categories
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, $table_name, $cat_table_name)
	{
		$this->config = $config;
		$this->db = $db;
		$this->table_name = $table_name;
		$this->cat_table_name = $cat_table_name;
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int						$id		Article ID
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE article_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('article_id');
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
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = [];

		// All of our fields
		$fields = [
			'article_id'		=> 'integer',
			'cat_id'			=> 'set_cat_id',
			'article_name'		=> 'set_name',
			'article_name_seo'	=> 'set_name_seo',
			'article_lang'		=> 'set_lang',
			'article_img'		=> 'set_img',
			'article_desc'		=> 'set_desc',
			'article_enable'	=> 'bool',
			'article_views'		=> 'integer',
			'article_time'		=> 'integer',

			// We do not pass to set_text() as generate_text_for_storage() would run twice
			'article_text'			=> 'string',
			'article_text_uid'		=> 'string',
			'article_text_bitfield'	=> 'string',
			'article_text_options'	=> 'integer'
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
		$validate_unsigned = ['article_id', 'cat_id', 'article_views', 'article_time', 'article_text_options'];

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
	* @return portal_article_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['article_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('article_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['article_id']);

		$sql = 'INSERT INTO ' . $this->table_name . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['article_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return portal_article_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['article_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('article_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['article_id' => null]);

		$sql = 'UPDATE ' . $this->table_name . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE article_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the article_id
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['article_id']) ? (int) $this->data['article_id'] : 0;
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
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_cat_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if ($id)
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
			throw new \vinabb\web\exceptions\unexpected_value(['cat_id', 'EMPTY']);
		}

		// Set the value on our data array
		$this->data['cat_id'] = $id;

		return $this;
	}

	/**
	* Get the article name
	*
	* @return string
	*/
	public function get_name()
	{
		return isset($this->data['article_name']) ? (string) $this->data['article_name'] : '';
	}

	/**
	* Set the article name
	*
	* @param string						$text	Article name
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['article_name', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_PORTAL_ARTICLE_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['article_name', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['article_name'] = $text;

		return $this;
	}

	/**
	* Get the article SEO name
	*
	* @return string
	*/
	public function get_name_seo()
	{
		return isset($this->data['article_name_seo']) ? (string) $this->data['article_name_seo'] : '';
	}

	/**
	* Set the article SEO name
	*
	* @param string						$text	Article SEO name
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_seo($text)
	{
		$text = strtolower($text);

		// Check invalid characters
		if (!preg_match('#^[a-z0-9-]+$#', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['article_name_seo', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['article_name_seo'] = $text;

		return $this;
	}

	/**
	* Get the article language
	*
	* @return string
	*/
	public function get_lang()
	{
		return isset($this->data['article_lang']) ? (string) $this->data['article_lang'] : '';
	}

	/**
	* Set the article language
	*
	* @param string						$text	2-letter language ISO code
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_lang($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['article_lang', 'EMPTY']);
		}
		else
		{
			$sql = 'SELECT 1
				FROM ' . LANG_TABLE . "
				WHERE lang_iso = '" . $this->db->sql_escape($text) . "'";
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row === false)
			{
				throw new \vinabb\web\exceptions\unexpected_value(['article_lang', 'NOT_EXISTS']);
			}
		}

		// Set the value on our data array
		$this->data['article_lang'] = $text;

		return $this;
	}

	/**
	* Get the article main image
	*
	* @return string
	*/
	public function get_img()
	{
		return isset($this->data['article_img']) ? (string) $this->data['article_img'] : '';
	}

	/**
	* Set the article main image
	*
	* @param string						$text	Article image
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_img($text)
	{
		$this->data['article_img'] = (string) $text;

		return $this;
	}

	/**
	* Get the article description
	*
	* @return string
	*/
	public function get_desc()
	{
		return isset($this->data['article_desc']) ? (string) $this->data['article_desc'] : '';
	}

	/**
	* Set the article description
	*
	* @param string						$text	Article description
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_desc($text)
	{
		$text = strtolower($text);

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['article_desc', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_PORTAL_ARTICLE_DESC) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['article_desc', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['article_desc'] = $text;

		return $this;
	}

	/**
	* Get article content for edit
	*
	* @return string
	*/
	public function get_text_for_edit()
	{
		// Use defaults if these haven't been set yet
		$text = isset($this->data['article_text']) ? $this->data['article_text'] : '';
		$uid = isset($this->data['article_text_uid']) ? $this->data['article_text_uid'] : '';
		$options = isset($this->data['article_text_options']) ? (int) $this->data['article_text_options'] : 0;

		$text_data = generate_text_for_edit($text, $uid, $options);

		return $text_data['text'];
	}

	/**
	* Get article content for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_text_for_display($censor = true)
	{
		// If these haven't been set yet; use defaults
		$text = isset($this->data['article_text']) ? $this->data['article_text'] : '';
		$uid = isset($this->data['article_text_uid']) ? $this->data['article_text_uid'] : '';
		$bitfield = isset($this->data['article_text_bitfield']) ? $this->data['article_text_bitfield'] : '';
		$options = isset($this->data['article_text_options']) ? (int) $this->data['article_text_options'] : 0;

		return generate_text_for_display($text, $uid, $bitfield, $options, $censor);
	}

	/**
	* Set article content
	*
	* @param string						$text	Article content
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text($text)
	{
		// Override maximum post characters limit
		$this->config['max_post_chars'] = 0;

		// Prepare the text for storage
		$uid = $bitfield = $flags = '';
		generate_text_for_storage($text, $uid, $bitfield, $flags, $this->text_bbcode_enabled(), $this->text_urls_enabled(), $this->text_smilies_enabled());

		// Set the value on our data array
		$this->data['article_text'] = $text;
		$this->data['article_text_uid'] = $uid;
		$this->data['article_text_bitfield'] = $bitfield;
		// Option flags are already set

		return $this;
	}

	/**
	* Check if BBCode is enabled on the article content
	*
	* @return bool
	*/
	public function text_bbcode_enabled()
	{
		return (bool) ($this->data['article_text_options'] & OPTION_FLAG_BBCODE);
	}

	/**
	* Enable/Disable BBCode on the article content
	* This should be called before set_text(); text_enable_bbcode()->set_text()
	*
	* @param bool						$enable	true: enable; false: disable
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_bbcode($enable)
	{
		$this->set_text_options(OPTION_FLAG_BBCODE, !$enable);

		return $this;
	}

	/**
	* Check if URLs is enabled on the article content
	*
	* @return bool
	*/
	public function text_urls_enabled()
	{
		return (bool) ($this->data['article_text_options'] & OPTION_FLAG_LINKS);
	}

	/**
	* Enable/Disable URLs on the article content
	* This should be called before set_text(); text_enable_urls()->set_text()
	*
	* @param bool						$enable	true: enable; false: disable
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_urls($enable)
	{
		$this->set_text_options(OPTION_FLAG_LINKS, !$enable);

		return $this;
	}

	/**
	* Check if smilies are enabled on the article content
	*
	* @return bool
	*/
	public function text_smilies_enabled()
	{
		return (bool) ($this->data['article_text_options'] & OPTION_FLAG_SMILIES);
	}

	/**
	* Enable/Disable smilies on the article content
	* This should be called before set_text(); text_enable_smilies()->set_text()
	*
	* @param bool						$enable	true: enable; false: disable
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_smilies($enable)
	{
		$this->set_text_options(OPTION_FLAG_SMILIES, !$enable);

		return $this;
	}

	/**
	* Set BBCode options for the article content
	*
	* @param int	$value				Value of the option
	* @param bool	$negate				Negate (Unset) option
	* @param bool	$reparse_content	Reparse the content after setting option
	*/
	protected function set_text_options($value, $negate = false, $reparse_content = true)
	{
		// Set article_text_options to 0 if it does not yet exist
		$this->data['article_text_options'] = isset($this->data['article_text_options']) ? $this->data['article_text_options'] : 0;

		// If we're setting the option and the option is not already set
		if (!$negate && !($this->data['article_text_options'] & $value))
		{
			// Add the option to the options
			$this->data['article_text_options'] += $value;
		}

		// If we're unsetting the option and the option is already set
		if ($negate && $this->data['article_text_options'] & $value)
		{
			// Subtract the option from the options
			$this->data['article_text_options'] -= $value;
		}

		// Reparse the content
		if ($reparse_content && !empty($this->data['article_text']))
		{
			$text = $this->data['article_text'];

			decode_message($text, $this->data['article_text_uid']);

			$this->set_text($text);
		}
	}

	/**
	* Get article display setting
	*
	* @return bool
	*/
	public function get_enable()
	{
		return isset($this->data['article_enable']) ? (bool) $this->data['article_enable'] : true;
	}

	/**
	* Set article display setting
	*
	* @param bool						$value	Article display setting
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable($value)
	{
		$this->data['article_enable'] = (bool) $value;

		return $this;
	}

	/**
	* Get the article views
	*
	* @return int
	*/
	public function get_views()
	{
		return isset($this->data['article_views']) ? (int) $this->data['article_views'] : 0;
	}

	/**
	* Get the article time
	*
	* @return int
	*/
	public function get_time()
	{
		return isset($this->data['article_time']) ? (int) $this->data['article_time'] : 0;
	}

	/**
	* Set the article time
	*
	* @return portal_article_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function set_time()
	{
		if (!isset($this->data['article_time']))
		{
			$this->data['article_time'] = time();
		}

		return $this;
	}
}
