<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Entity for a single comment
*/
class portal_comment implements portal_comment_interface
{
	/**
	* Data for this entity
	*
	* @var array
	*	comment_id
	*	user_id
	*	article_id
	*	comment_text
	*	comment_text_uid
	*	comment_text_bitfield
	*	comment_text_options
	*	comment_pending
	*	comment_time
	*/
	protected $data;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var string */
	protected $table_name;

	/** @var string */
	protected $article_table_name;

	/**
	* Constructor
	*
	* @param \phpbb\config\config				$config				Config object
	* @param \phpbb\db\driver\driver_interface	$db					Database object
	* @param string								$table_name			Table name
	* @param string								$article_table_name	Table name of articles
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, $table_name, $article_table_name)
	{
		$this->config = $config;
		$this->db = $db;
		$this->table_name = $table_name;
		$this->article_table_name = $article_table_name;
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
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data)
	{
		// Clear out any saved data
		$this->data = [];

		// All of our fields
		$fields = [
			'comment_id'		=> 'integer',
			'user_id'			=> 'set_user_id',
			'article_id'		=> 'set_article_id',
			'comment_pending'	=> 'set_pending',
			'comment_time'		=> 'set_time',

			// We do not pass to set_text() as generate_text_for_storage() would run twice
			'comment_text'			=> 'string',
			'comment_text_uid'		=> 'string',
			'comment_text_bitfield'	=> 'string',
			'comment_text_options'	=> 'integer'
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
		$validate_unsigned = ['comment_id', 'user_id', 'article_id', 'comment_time', 'comment_text_options'];

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
	* Get the comment_id
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
		if ($id > 0)
		{
			$sql = 'SELECT 1
				FROM ' . USERS_TABLE . "
				WHERE user_id = $id";
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row === false)
			{
				throw new \vinabb\web\exceptions\unexpected_value(['user_id', 'NOT_EXISTS']);
			}
		}
		else
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_id', 'FIELD_MISSING']);
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
		if ($id > 0)
		{
			$sql = 'SELECT 1
				FROM ' . $this->article_table_name . "
				WHERE article_id = $id";
			$result = $this->db->sql_query_limit($sql, 1);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row === false)
			{
				throw new \vinabb\web\exceptions\unexpected_value(['article_id', 'NOT_EXISTS']);
			}
		}
		else
		{
			throw new \vinabb\web\exceptions\unexpected_value(['article_id', 'FIELD_MISSING']);
		}

		// Set the value on our data array
		$this->data['article_id'] = $id;

		return $this;
	}

	/**
	* Get comment content for edit
	*
	* @return string
	*/
	public function get_text_for_edit()
	{
		// Use defaults if these haven't been set yet
		$text = isset($this->data['comment_text']) ? $this->data['comment_text'] : '';
		$uid = isset($this->data['comment_text_uid']) ? $this->data['comment_text_uid'] : '';
		$options = isset($this->data['comment_text_options']) ? (int) $this->data['comment_text_options'] : 0;

		$text_data = generate_text_for_edit($text, $uid, $options);

		return $text_data['text'];
	}

	/**
	* Get comment content for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_text_for_display($censor = true)
	{
		// If these haven't been set yet; use defaults
		$text = isset($this->data['comment_text']) ? $this->data['comment_text'] : '';
		$uid = isset($this->data['comment_text_uid']) ? $this->data['comment_text_uid'] : '';
		$bitfield = isset($this->data['comment_text_bitfield']) ? $this->data['comment_text_bitfield'] : '';
		$options = isset($this->data['comment_text_options']) ? (int) $this->data['comment_text_options'] : 0;

		return generate_text_for_display($text, $uid, $bitfield, $options, $censor);
	}

	/**
	* Set comment content
	*
	* @param string						$text	Comment content
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text($text)
	{
		// Override maximum post characters limit
		$this->config['max_post_chars'] = 0;

		// Prepare the text for storage
		$uid = $bitfield = $flags = '';
		generate_text_for_storage($text, $uid, $bitfield, $flags, $this->text_bbcode_enabled(), $this->text_urls_enabled(), $this->text_smilies_enabled());

		// Set the value on our data array
		$this->data['comment_text'] = $text;
		$this->data['comment_text_uid'] = $uid;
		$this->data['comment_text_bitfield'] = $bitfield;
		// Option flags are already set

		return $this;
	}

	/**
	* Check if BBCode is enabled on the comment content
	*
	* @return bool
	*/
	public function text_bbcode_enabled()
	{
		return ($this->data['comment_text_options'] & OPTION_FLAG_BBCODE);
	}

	/**
	* Enable BBCode on the comment content
	* This should be called before set_text(); text_enable_bbcode()->set_text()
	*
	* @return portal_comment_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_bbcode()
	{
		$this->set_text_options(OPTION_FLAG_BBCODE);

		return $this;
	}

	/**
	* Disable BBCode on the comment content
	* This should be called before set_text(); text_disable_bbcode()->set_text()
	*
	* @return portal_comment_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_disable_bbcode()
	{
		$this->set_text_options(OPTION_FLAG_BBCODE, true);

		return $this;
	}

	/**
	* Check if URLs is enabled on the comment content
	*
	* @return bool
	*/
	public function text_urls_enabled()
	{
		return ($this->data['comment_text_options'] & OPTION_FLAG_LINKS);
	}

	/**
	* Enable URLs on the comment content
	* This should be called before set_text(); text_enable_urls()->set_text()
	*
	* @return portal_comment_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_urls()
	{
		$this->set_text_options(OPTION_FLAG_LINKS);

		return $this;
	}

	/**
	* Disable URLs on the comment content
	* This should be called before set_text(); text_disable_urls()->set_text()
	*
	* @return portal_comment_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_disable_urls()
	{
		$this->set_text_options(OPTION_FLAG_LINKS, true);

		return $this;
	}

	/**
	* Check if smilies are enabled on the comment content
	*
	* @return bool
	*/
	public function text_smilies_enabled()
	{
		return ($this->data['comment_text_options'] & OPTION_FLAG_SMILIES);
	}

	/**
	* Enable smilies on the comment content
	* This should be called before set_text(); text_enable_smilies()->set_text()
	*
	* @return portal_comment_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_smilies()
	{
		$this->set_text_options(OPTION_FLAG_SMILIES);

		return $this;
	}

	/**
	* Disable smilies on the comment content
	* This should be called before set_text(); text_disable_smilies()->set_text()
	*
	* @return portal_comment_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_disable_smilies()
	{
		$this->set_text_options(OPTION_FLAG_SMILIES, true);

		return $this;
	}

	/**
	* Set BBCode options for the comment content
	*
	* @param int	$value				Value of the option
	* @param bool	$negate				Negate (Unset) option
	* @param bool	$reparse_content	Reparse the content after setting option
	*/
	protected function set_text_options($value, $negate = false, $reparse_content = true)
	{
		// Set comment_text_options to 0 if it does not yet exist
		$this->data['comment_text_options'] = isset($this->data['comment_text_options']) ? $this->data['comment_text_options'] : 0;

		// If we're setting the option and the option is not already set
		if (!$negate && !($this->data['comment_text_options'] & $value))
		{
			// Add the option to the options
			$this->data['comment_text_options'] += $value;
		}

		// If we're unsetting the option and the option is already set
		if ($negate && $this->data['comment_text_options'] & $value)
		{
			// Subtract the option from the options
			$this->data['comment_text_options'] -= $value;
		}

		// Reparse the content
		if ($reparse_content && $this->data['comment_text'] != '')
		{
			$text = $this->data['comment_text'];

			decode_message($text, $this->data['comment_text_uid']);

			$this->set_text($text);
		}
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
	*/
	public function set_pending($value)
	{
		if (!isset($this->data['comment_pending']))
		{
			$this->data['comment_pending'] = (bool) $value;
		}

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
		$this->data['comment_time'] = !$this->get_time() ? time() : 0;

		return $this;
	}
}
