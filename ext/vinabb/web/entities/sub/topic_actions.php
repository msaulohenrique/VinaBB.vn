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
* Sub-entity for topic_actions + topic_last_post + topic_poll
*/
class topic_actions extends topic_last_post
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
	* Get the topic visibility
	*
	* @return int
	*/
	public function get_visibility()
	{
		return isset($this->data['topic_visibility']) ? (int) $this->data['topic_visibility'] : 0;
	}

	/**
	* Set the topic visibility
	*
	* @param int			$value	Visibility value
	* @return topic_actions	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_visibility($value)
	{
		// Set the value on our data array
		$this->data['topic_visibility'] = (int) $value;

		return $this;
	}

	/**
	* Does the topic have attachments?
	*
	* @return bool
	*/
	public function get_attachment()
	{
		return isset($this->data['topic_attachment']) ? (bool) $this->data['topic_attachment'] : false;
	}

	/**
	* Set the topic have or have not attachments
	*
	* @param bool			$value	true: yes; false: no
	* @return topic_actions	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_attachment($value)
	{
		// Set the value on our data array
		$this->data['topic_attachment'] = (bool) $value;

		return $this;
	}

	/**
	* Does the topic have open reports?
	*
	* @return bool
	*/
	public function get_reported()
	{
		return isset($this->data['topic_reported']) ? (bool) $this->data['topic_reported'] : false;
	}

	/**
	* Set the topic have or have not open reports
	*
	* @param bool			$value	true: yes; false: no
	* @return topic_actions	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_reported($value)
	{
		// Set the value on our data array
		$this->data['topic_reported'] = (bool) $value;

		return $this;
	}

	/**
	* Get the old topic ID after moving and leaving shadow
	*
	* @return int
	*/
	public function get_moved_id()
	{
		return isset($this->data['topic_moved_id']) ? (int) $this->data['topic_moved_id'] : 0;
	}

	/**
	* Set the old topic ID after moving and leaving shadow
	*
	* @param int			$id		Topic ID
	* @return topic_actions	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_moved_id($id)
	{
		$id = (int) $id;

		// Check existing topic
		if ($id && !$this->entity_helper->check_topic_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_moved_id', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['topic_moved_id'] = $id;

		return $this;
	}

	/**
	* Get the time of bumping topic up
	*
	* @return int
	*/
	public function get_bumped()
	{
		return isset($this->data['topic_bumped']) ? (int) $this->data['topic_bumped'] : 0;
	}

	/**
	* Set the time of bumping topic up
	*
	* @param int			$value	UNIX timestamp
	* @return topic_actions	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_bumped($value)
	{
		// Set the value on our data array
		$this->data['topic_bumped'] = (int) $value;

		return $this;
	}

	/**
	* Get the user bumped topic up
	*
	* @return int
	*/
	public function get_bumper()
	{
		return isset($this->data['topic_bumper']) ? (int) $this->data['topic_bumper'] : 0;
	}

	/**
	* Set the user bumped topic up
	*
	* @param int			$id		User ID
	* @return topic_actions	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_bumper($id)
	{
		$id = (int) $id;

		// Check existing user
		if ($id && !$this->entity_helper->check_user_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_bumper', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['topic_bumper'] = $id;

		return $this;
	}

	/**
	* Get the time of deleting topic
	*
	* @return int
	*/
	public function get_delete_time()
	{
		return isset($this->data['topic_delete_time']) ? (int) $this->data['topic_delete_time'] : 0;
	}

	/**
	* Set the time of deleting topic
	*
	* @param int			$value	UNIX timestamp
	* @return topic_actions	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_delete_time($value)
	{
		// Set the value on our data array
		$this->data['topic_delete_time'] = (int) $value;

		return $this;
	}

	/**
	* Get the deleted reason
	*
	* @return string
	*/
	public function get_delete_reason()
	{
		return isset($this->data['topic_delete_reason']) ? (string) $this->data['topic_delete_reason'] : '';
	}

	/**
	* Set the deleted reason
	*
	* @param string			$text	Reason
	* @return topic_actions	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_delete_reason($text)
	{
		$text = (string) $text;

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_delete_reason', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['topic_delete_reason'] = $text;

		return $this;
	}

	/**
	* Get the user deleted topic
	*
	* @return int
	*/
	public function get_delete_user()
	{
		return isset($this->data['topic_delete_user']) ? (int) $this->data['topic_delete_user'] : 0;
	}

	/**
	* Set the user deleted topic
	*
	* @param int			$id		User ID
	* @return topic_actions	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_delete_user($id)
	{
		$id = (int) $id;

		// Check existing user
		if ($id && !$this->entity_helper->check_user_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_delete_user', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['topic_delete_user'] = $id;

		return $this;
	}
}
