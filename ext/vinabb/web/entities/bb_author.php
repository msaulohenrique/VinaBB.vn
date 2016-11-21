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
* Entity for a single author
*/
class bb_author implements bb_author_interface
{
	/**
	* Data for this entity
	*
	* @var array
	*	author_id
	*	user_id
	*	author_name
	*	author_name_seo
	*	author_firstname
	*	author_lastname
	*	author_is_group
	*	author_group
	*	author_www
	*	author_email
	*	author_phpbb
	*	author_github
	*	author_facebook
	*	author_twitter
	*	author_google
	*	author_google_plus
	*	author_skype
	*/
	protected $data;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \vinabb\web\entities\helper\helper_interface */
	protected $entity_helper;

	/** @var string */
	protected $table_name;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface				$db				Database object
	* @param \vinabb\web\entities\helper\helper_interface	$entity_helper	Entity helper
	* @param string											$table_name		Table name
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \vinabb\web\entities\helper\helper_interface $entity_helper, $table_name)
	{
		$this->db = $db;
		$this->entity_helper = $entity_helper;
		$this->table_name = $table_name;
	}

	/**
	* Load the data from the database for an entity
	*
	* @param int					$id		Author ID
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id)
	{
		$sql = 'SELECT *
			FROM ' . $this->table_name . '
			WHERE author_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$this->data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// The entity does not exist
		if ($this->data === false)
		{
			throw new \vinabb\web\exceptions\out_of_bounds('author_id');
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
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = [];

		// All of our fields
		$fields = [
			'author_id'				=> 'integer',
			'user_id'				=> 'set_user_id',
			'author_name'			=> 'set_name',
			'author_name_seo'		=> 'set_name_seo',
			'author_firstname'		=> 'set_firstname',
			'author_lastname'		=> 'set_lastname',
			'author_is_group'		=> 'set_is_group',
			'author_group'			=> 'set_group',
			'author_www'			=> 'set_www',
			'author_email'			=> 'set_email',
			'author_phpbb'			=> 'set_phpbb',
			'author_github'			=> 'set_github',
			'author_facebook'		=> 'set_facebook',
			'author_twitter'		=> 'set_twitter',
			'author_google'			=> 'set_google',
			'author_google_plus'	=> 'set_google_plus',
			'author_skype'			=> 'set_skype'
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
		$validate_unsigned = ['author_id', 'user_id', 'author_is_group', 'author_group', 'author_phpbb'];

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
	* @return bb_author_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert()
	{
		// The entity already exists
		if (!empty($this->data['author_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('author_id');
		}

		// Make extra sure there is no ID set
		unset($this->data['author_id']);

		$sql = 'INSERT INTO ' . $this->table_name . ' ' . $this->db->sql_build_array('INSERT', $this->data);
		$this->db->sql_query($sql);

		// Set the ID using the ID created by the SQL INSERT
		$this->data['author_id'] = (int) $this->db->sql_nextid();

		return $this;
	}

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return bb_author_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save()
	{
		// The entity does not exist
		if (empty($this->data['author_id']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('author_id');
		}

		// Copy the data array, filtering out the ID
		// so we do not attempt to update the row's identity column.
		$sql_array = array_diff_key($this->data, ['author_id' => null]);

		$sql = 'UPDATE ' . $this->table_name . '
			SET ' . $this->db->sql_build_array('UPDATE', $sql_array) . '
			WHERE author_id = ' . $this->get_id();
		$this->db->sql_query($sql);

		return $this;
	}

	/**
	* Get the author_id
	*
	* @return int
	*/
	public function get_id()
	{
		return isset($this->data['author_id']) ? (int) $this->data['author_id'] : 0;
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
	* @param int					$id		User ID
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_user_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if ($id && !$this->entity_helper->check_user_id($id))
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
	* Get the author name
	*
	* @return string
	*/
	public function get_name()
	{
		return isset($this->data['author_name']) ? (string) $this->data['author_name'] : '';
	}

	/**
	* Set the author name
	*
	* @param string					$text	Author name
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_name', 'EMPTY']);
		}

		// Check the max length
		if (truncate_string($text, constants::MAX_BB_AUTHOR_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_name', 'TOO_LONG']);
		}

		// This field value must be unique
		if ($this->get_name() != $text && $this->entity_helper->check_bb_author_name($text, $this->get_id()))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_name', 'DUPLICATE', $text]);
		}

		// Set the value on our data array
		$this->data['author_name'] = $text;

		return $this;
	}

	/**
	* Get the author SEO name
	*
	* @return string
	*/
	public function get_name_seo()
	{
		return isset($this->data['author_name_seo']) ? (string) $this->data['author_name_seo'] : '';
	}

	/**
	* Set the author SEO name
	*
	* @param string					$text	Author SEO name
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_seo($text)
	{
		$text = strtolower($text);

		// Check invalid characters
		if (!preg_match('#^[a-z0-9-]+$#', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_name_seo', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['author_name_seo'] = $text;

		return $this;
	}

	/**
	* Get the author firstname
	*
	* @return string
	*/
	public function get_firstname()
	{
		return isset($this->data['author_firstname']) ? (string) $this->data['author_firstname'] : '';
	}

	/**
	* Set the author firstname
	*
	* @param string					$text	Author firstname
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_firstname($text)
	{
		$this->data['author_firstname'] = (string) $text;

		return $this;
	}

	/**
	* Get the author lastname
	*
	* @return string
	*/
	public function get_lastname()
	{
		return isset($this->data['author_lastname']) ? (string) $this->data['author_lastname'] : '';
	}

	/**
	* Set the author lastname
	*
	* @param string					$text	Author lastname
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_lastname($text)
	{
		$this->data['author_lastname'] = (string) $text;

		return $this;
	}

	/**
	* Check the author is a group?
	*
	* @return bool
	*/
	public function get_is_group()
	{
		return isset($this->data['author_is_group']) ? (bool) $this->data['author_is_group'] : false;
	}

	/**
	* Set the author is a group
	*
	* @param bool					$value	Config value
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_is_group($value)
	{
		$this->data['author_is_group'] = (bool) $value;

		return $this;
	}

	/**
	* Get the author's group
	*
	* @return int
	*/
	public function get_group()
	{
		return isset($this->data['author_group']) ? (int) $this->data['author_group'] : 0;
	}

	/**
	* Set the author's group
	*
	* @param int					$id		Group ID (Also is the author_id)
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_group($id)
	{
		$id = (int) $id;

		// Check existing group
		if ($id && !$this->entity_helper->check_bb_author_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_group', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['author_group'] = $id;

		return $this;
	}

	/**
	* Get the author website
	*
	* @return string
	*/
	public function get_www()
	{
		return isset($this->data['author_www']) ? (string) htmlspecialchars_decode($this->data['author_www']) : '';
	}

	/**
	* Set the author website
	*
	* @param string					$text	Author website
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_www($text)
	{
		$text = strtolower($text);

		// Checking for valid URL
		if (filter_var($text, FILTER_VALIDATE_URL) === false)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_www', 'INVALID_URL']);
		}

		// Set the value on our data array
		$this->data['author_www'] = $text;
	}

	/**
	* Get the author email
	*
	* @return string
	*/
	public function get_email()
	{
		return isset($this->data['author_email']) ? (string) htmlspecialchars_decode($this->data['author_email']) : '';
	}

	/**
	* Set the author email
	*
	* @param string					$text	Author email
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_email($text)
	{
		$text = strtolower($text);

		// Checking for valid email address
		if (filter_var($text, FILTER_VALIDATE_EMAIL) === false)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_email', 'INVALID_EMAIL']);
		}

		// Set the value on our data array
		$this->data['author_email'] = $text;
	}

	/**
	* Get the author's phpBB.com user ID
	*
	* @return int
	*/
	public function get_phpbb()
	{
		return isset($this->data['author_phpbb']) ? (int) $this->data['author_phpbb'] : 0;
	}

	/**
	* Set the author's phpBB.com user ID
	*
	* @param int					$value	phpBB.com user ID
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_phpbb($value)
	{
		$this->data['author_phpbb'] = (int) $value;

		return $this;
	}

	/**
	* Get the author's social page: GitHub
	*
	* @return string
	*/
	public function get_github()
	{
		return isset($this->data['author_github']) ? (string) $this->data['author_github'] : '';
	}

	/**
	* Set the author's social page: GitHub
	*
	* @param string					$text	GitHub username
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_github($text)
	{
		$this->data['author_github'] = (string) $text;

		return $this;
	}

	/**
	* Get the author's social page: Facebook
	*
	* @return string
	*/
	public function get_facebook()
	{
		return isset($this->data['author_facebook']) ? (string) $this->data['author_facebook'] : '';
	}

	/**
	* Set the author's social page: Facebook
	*
	* @param string					$text	Facebook username
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_facebook($text)
	{
		$this->data['author_facebook'] = (string) $text;

		return $this;
	}

	/**
	* Get the author's social page: Twitter
	*
	* @return string
	*/
	public function get_twitter()
	{
		return isset($this->data['author_twitter']) ? (string) $this->data['author_twitter'] : '';
	}

	/**
	* Set the author's social page: Twitter
	*
	* @param string					$text	Twitter username
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_twitter($text)
	{
		$this->data['author_twitter'] = (string) $text;

		return $this;
	}

	/**
	* Get the author's social page: Google (YouTube, Gmail...)
	*
	* @return string
	*/
	public function get_google()
	{
		return isset($this->data['author_google']) ? (string) $this->data['author_google'] : '';
	}

	/**
	* Set the author's social page: Google (YouTube, Gmail...)
	*
	* @param string					$text	Google username
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_google($text)
	{
		$this->data['author_google'] = (string) $text;

		return $this;
	}

	/**
	* Get the author's social page: Google+
	*
	* @return string
	*/
	public function get_google_plus()
	{
		return isset($this->data['author_google_plus']) ? (string) $this->data['author_google_plus'] : '';
	}

	/**
	* Set the author's social page: Google+
	*
	* @param string					$text	Google+ username
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_google_plus($text)
	{
		$this->data['author_google_plus'] = (string) $text;

		return $this;
	}

	/**
	* Get the author's social page: Skype
	*
	* @return string
	*/
	public function get_skype()
	{
		return isset($this->data['author_skype']) ? (string) $this->data['author_skype'] : '';
	}

	/**
	* Set the author's social page: Skype
	*
	* @param string					$text	Skype username
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_skype($text)
	{
		$this->data['author_skype'] = (string) $text;

		return $this;
	}
}
