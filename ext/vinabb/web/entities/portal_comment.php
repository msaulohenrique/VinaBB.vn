<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

use vinabb\web\entities\sub\comment_text;
use vinabb\web\includes\constants;

/**
* Entity for a single comment
*/
class portal_comment extends comment_text implements portal_comment_interface
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vinabb\web\entities\helper\helper_interface */
	protected $entity_helper;

	/** @var string */
	protected $table_name;

	/** @var string */
	protected $article_table_name;

	/** @var array */
	protected $data;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface				$db					Database object
	* @param \vinabb\web\entities\helper\helper_interface	$entity_helper		Entity helper
	* @param string											$table_name			Table name
	* @param string											$article_table_name	Table name of articles
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \vinabb\web\entities\helper\helper_interface $entity_helper, $table_name, $article_table_name)
	{
		$this->db = $db;
		$this->entity_helper = $entity_helper;
		$this->table_name = $table_name;
		$this->article_table_name = $article_table_name;
	}

	/**
	* Data for this entity
	*
	* @return array
	*/
	protected function prepare_data()
	{
		return [
			'comment_id'		=> 'integer',
			'user_id'			=> 'integer',
			'article_id'		=> 'integer',
			'comment_pending'	=> 'integer',
			'comment_time'		=> 'integer',

			// Entity: vinabb\web\entities\sub\comment_text
			'comment_text'			=> 'string',
			'comment_text_uid'		=> 'string',
			'comment_text_bitfield'	=> 'string',
			'comment_text_options'	=> 'integer'
		];
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int						$id		Comment ID
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE comment_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('comment_id');
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
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return portal_comment_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['comment_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('comment_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['comment_id']);

		$sql = 'INSERT INTO ' . $this->table_name . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['comment_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return portal_comment_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['comment_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('comment_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['comment_id' => null]);

		$sql = 'UPDATE ' . $this->table_name . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE comment_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the comment ID
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['comment_id']) ? (int) $this->data['comment_id'] : 0;
	}

	/**
	* Get the user ID
	*
	* @return int
	*/
	public function get_user_id()
	{
		return isset($this->data['user_id']) ? (int) $this->data['user_id'] : 0;
	}

	/**
	* Set the user ID
	*
	* @param int						$id		User ID
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_cat_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if ($id && !$this->entity_helper->check_portal_cat_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_id', 'NOT_EXISTS']);
		}
		else
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_id', 'EMPTY']);
		}

		// Set the value on our data array
		$this->data['user_id'] = $id;

		return $this;
	}

	/**
	* Get the article ID
	*
	* @return int
	*/
	public function get_article_id()
	{
		return isset($this->data['article_id']) ? (int) $this->data['article_id'] : 0;
	}

	/**
	* Set the article ID
	*
	* @param int						$id		Article ID
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_article_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if ($id && !$this->entity_helper->check_portal_article_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['article_id', 'NOT_EXISTS']);
		}
		else
		{
			throw new \vinabb\web\exceptions\unexpected_value(['article_id', 'EMPTY']);
		}

		// Set the value on our data array
		$this->data['article_id'] = $id;

		return $this;
	}

	/**
	* Get comment pending status
	*
	* @return bool
	*/
	public function get_pending()
	{
		return isset($this->data['comment_pending']) ? (bool) $this->data['comment_pending'] : true;
	}

	/**
	* Set comment pending status
	*
	* @param bool						$value	Pending status
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_pending($value)
	{
		$value = (int) $value;

		if (!in_array($value, [constants::ARTICLE_COMMENT_MODE_HIDE, constants::ARTICLE_COMMENT_MODE_SHOW, constants::ARTICLE_COMMENT_MODE_PENDING]))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('comment_pending');
		}

		// Set the value on our data array
		$this->data['comment_pending'] = $value;

		return $this;
	}

	/**
	* Get the comment time
	*
	* @return int
	*/
	public function get_time()
	{
		return isset($this->data['comment_time']) ? (int) $this->data['comment_time'] : 0;
	}

	/**
	* Set the comment time
	*
	* @return portal_comment_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function set_time()
	{
		if (!isset($this->data['comment_time']))
		{
			$this->data['comment_time'] = time();
		}

		return $this;
	}
}
