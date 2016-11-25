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
	/** @var array */
	protected $data;

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
	* Does the topic have attachments?
	*
	* @return bool
	*/
	public function get_attachment()
	{
		return isset($this->data['topic_attachment']) ? (bool) $this->data['topic_attachment'] : false;
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
	* @param int				$id		Topic ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @param int				$id		User ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @param string				$text	Reason
	* @return topic_last_post	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_delete_reason($text)
	{
		$text = (string) $text;

		// Check the max length
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
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
	* @param int				$id		User ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
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
