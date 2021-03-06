<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

use vinabb\web\entities\sub\article_data;
use vinabb\web\includes\constants;

/**
* Entity for a single article
*/
class portal_article extends article_data implements portal_article_interface
{
	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \phpbb\extension\manager $ext_manager */
	protected $ext_manager;

	/** @var \vinabb\web\entities\helper\helper_interface $entity_helper */
	protected $entity_helper;

	/** @var \phpbb\path_helper $path_helper */
	protected $path_helper;

	/** @var string $table_name */
	protected $table_name;

	/** @var string $cat_table_name */
	protected $cat_table_name;

	/** @var string $ext_root_path */
	protected $ext_root_path;

	/** @var string $ext_web_path */
	protected $ext_web_path;

	/** @var array $data */
	protected $data;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface				$db				Database object
	* @param \phpbb\extension\manager						$ext_manager	Extension manager
	* @param \vinabb\web\entities\helper\helper_interface	$entity_helper	Entity helper
	* @param \phpbb\path_helper								$path_helper	Path helper
	* @param string											$table_name		Table name
	* @param string											$cat_table_name	Table name of categories
	*/
	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\phpbb\extension\manager $ext_manager,
		\vinabb\web\entities\helper\helper_interface $entity_helper,
		\phpbb\path_helper $path_helper,
		$table_name,
		$cat_table_name
	)
	{
		$this->db = $db;
		$this->ext_manager = $ext_manager;
		$this->entity_helper = $entity_helper;
		$this->path_helper = $path_helper;
		$this->table_name = $table_name;
		$this->cat_table_name = $cat_table_name;

		$this->ext_root_path = $this->ext_manager->get_extension_path('vinabb/web', true);
		$this->ext_web_path = $this->path_helper->update_web_root_path($this->ext_root_path);
	}

	/**
	* Data for this entity
	*
	* @return array
	*/
	protected function prepare_data()
	{
		return [
			'article_id'		=> 'integer',
			'cat_id'			=> 'integer',
			'user_id'			=> 'integer',
			'article_name'		=> 'string',
			'article_name_seo'	=> 'string',
			'article_lang'		=> 'string',
			'article_img'		=> 'string',

			// Entity: vinabb\web\entities\sub\article_data
			'article_desc'		=> 'string',
			'article_enable'	=> 'bool',
			'article_views'		=> 'integer',
			'article_time'		=> 'integer',

			// Entity: vinabb\web\entities\sub\article_text
			'article_text'			=> 'string',
			'article_text_uid'		=> 'string',
			'article_text_bitfield'	=> 'string',
			'article_text_options'	=> 'integer'
		];
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
	* Get the article ID
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
		if (!$id)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['cat_id', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_portal_cat_id($id))
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
	public function get_user_id()
	{
		return isset($this->data['user_id']) ? (int) $this->data['user_id'] : 0;
	}

	/**
	* Set the author ID
	*
	* @param int						$id		User ID
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_user_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if (!$id)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_id', 'EMPTY']);

		}
		else if (!$this->entity_helper->check_user_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['user_id'] = $id;

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
		if (utf8_strlen($text) > constants::MAX_PORTAL_ARTICLE_NAME)
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
		$text = (string) $text;

		// Check invalid characters
		if (!preg_match(constants::REGEX_SEO, $text))
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
		else if (!$this->entity_helper->check_lang_iso($text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['article_lang', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['article_lang'] = $text;

		return $this;
	}

	/**
	* Get the article image
	*
	* @param bool	$real_path	True to return the path on filesystem, false to return the web access path
	* @param bool	$full_path	True to return the path + filename, false to return only filename
	* @return string
	*/
	public function get_img($real_path = false, $full_path = true)
	{
		$path = $full_path ? ($real_path ? $this->ext_root_path : $this->ext_web_path) . constants::DIR_ARTICLE_IMAGES : '';

		return !empty($this->data['article_img']) ? (string) $path . $this->data['article_img'] : '';
	}

	/**
	* Set the article image
	*
	* @param string						$text	Article image
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_img($text)
	{
		$text = (string) $text;

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['article_img', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['article_img'] = $text;

		return $this;
	}
}
