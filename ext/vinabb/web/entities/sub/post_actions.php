<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

use vinabb\web\includes\constants;

/**
* Sub-entity for post_actions + post_text
*/
class post_actions extends post_text
{
	/** @var array $data */
	protected $data;

	/** @var \vinabb\web\entities\helper\helper_interface $entity_helper */
	protected $entity_helper;

	/**
	* Constructor
	*
	* @param \vinabb\web\entities\helper\helper_interface $entity_helper Entity helper
	*/
	public function __construct(\vinabb\web\entities\helper\helper_interface $entity_helper)
	{
		$this->entity_helper = $entity_helper;
	}

	/**
	* Get the time of editing post
	*
	* @return int
	*/
	public function get_edit_time()
	{
		return isset($this->data['post_edit_time']) ? (int) $this->data['post_edit_time'] : 0;
	}

	/**
	* Set the time of editing post
	*
	* @param int			$value	UNIX timestamp
	* @return post_actions	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_edit_time($value)
	{
		// Set the value on our data array
		$this->data['post_edit_time'] = (int) $value;

		return $this;
	}

	/**
	* Get the edited reason
	*
	* @return string
	*/
	public function get_edit_reason()
	{
		return isset($this->data['post_edit_reason']) ? (string) $this->data['post_edit_reason'] : '';
	}

	/**
	* Set the edited reason
	*
	* @param string			$text	Reason
	* @return post_actions	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_edit_reason($text)
	{
		$text = (string) $text;

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['post_edit_reason', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['post_edit_reason'] = $text;

		return $this;
	}

	/**
	* Get the user edited post
	*
	* @return int
	*/
	public function get_edit_user()
	{
		return isset($this->data['post_edit_user']) ? (int) $this->data['post_edit_user'] : 0;
	}

	/**
	* Set the user edited post
	*
	* @param int			$id		User ID
	* @return post_actions	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_edit_user($id)
	{
		$id = (int) $id;

		// Check existing user
		if ($id && !$this->entity_helper->check_user_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['post_edit_user', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['post_edit_user'] = $id;

		return $this;
	}

	/**
	* Get number of times of editing post
	*
	* @return int
	*/
	public function get_edit_count()
	{
		return isset($this->data['post_edit_count']) ? (int) $this->data['post_edit_count'] : 0;
	}

	/**
	* Set number of times of editing post
	*
	* @param int			$value	Number of times
	* @return post_actions	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_edit_count($value)
	{
		// Set the value on our data array
		$this->data['post_edit_count'] = (int) $value;

		return $this;
	}

	/**
	* Is the post locked?
	*
	* @return bool
	*/
	public function get_edit_locked()
	{
		return isset($this->data['post_edit_locked']) ? (bool) $this->data['post_edit_locked'] : false;
	}

	/**
	* Set the post locked or unlocked
	*
	* @param bool			$value	true: locked; false: unlocked
	* @return post_actions	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_edit_locked($value)
	{
		// Set the value on our data array
		$this->data['post_edit_locked'] = (bool) $value;

		return $this;
	}

	/**
	* Get the time of deleting post
	*
	* @return int
	*/
	public function get_delete_time()
	{
		return isset($this->data['post_delete_time']) ? (int) $this->data['post_delete_time'] : 0;
	}

	/**
	* Set the time of deleting post
	*
	* @param int			$value	UNIX timestamp
	* @return post_actions	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_delete_time($value)
	{
		// Set the value on our data array
		$this->data['post_delete_time'] = (int) $value;

		return $this;
	}

	/**
	* Get the deleted reason
	*
	* @return string
	*/
	public function get_delete_reason()
	{
		return isset($this->data['post_delete_reason']) ? (string) $this->data['post_delete_reason'] : '';
	}

	/**
	* Set the deleted reason
	*
	* @param string			$text	Reason
	* @return post_actions	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_delete_reason($text)
	{
		$text = (string) $text;

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['post_delete_reason', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['post_delete_reason'] = $text;

		return $this;
	}

	/**
	* Get the user deleted post
	*
	* @return int
	*/
	public function get_delete_user()
	{
		return isset($this->data['post_delete_user']) ? (int) $this->data['post_delete_user'] : 0;
	}

	/**
	* Set the user deleted post
	*
	* @param int			$id		User ID
	* @return post_actions	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_delete_user($id)
	{
		$id = (int) $id;

		// Check existing user
		if ($id && !$this->entity_helper->check_user_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['post_delete_user', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['post_delete_user'] = $id;

		return $this;
	}
}
