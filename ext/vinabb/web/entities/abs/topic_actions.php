<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\abs;

use vinabb\web\includes\constants;

/**
* Abstract entity for topic actions + poll
*/
abstract class topic_actions extends topic_poll
{
	/** @var array */
	protected $data;

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
	* Get the first post ID
	*
	* @return int
	*/
	public function get_first_post_id()
	{
		return isset($this->data['topic_first_post_id']) ? (int) $this->data['topic_first_post_id'] : 0;
	}

	/**
	* Set the first post ID
	*
	* @param int				$id		Post ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_first_post_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if ($id && !$this->entity_helper->check_post_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_first_post_id', 'NOT_EXISTS']);
		}
		else
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_first_post_id', 'EMPTY']);
		}

		// Set the value on our data array
		$this->data['topic_first_post_id'] = $id;

		return $this;
	}

	/**
	* Get the poster username
	*
	* @return string
	*/
	public function get_first_poster_name()
	{
		return isset($this->data['topic_first_poster_name']) ? (string) $this->data['topic_first_poster_name'] : '';
	}

	/**
	* Set the poster username
	*
	* @param string				$text	Username
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_first_poster_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_first_poster_name', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_username($text, $this->get_poster()))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_first_poster_name', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['topic_first_poster_name'] = $text;

		return $this;
	}

	/**
	* Get the poster username color
	*
	* @return string
	*/
	public function get_first_poster_colour()
	{
		return isset($this->data['topic_first_poster_colour']) ? (string) $this->data['topic_first_poster_colour'] : '';
	}

	/**
	* Set the poster username color
	*
	* @param string				$text	6-char HEX code without #
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_first_poster_colour($text)
	{
		$text = (string) $text;

		// Check invalid characters
		if (!preg_match('/([a-f0-9]{3}){1,2}\b/i', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_first_poster_colour', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['topic_first_poster_colour'] = $text;

		return $this;
	}

	/**
	* Get the last post ID
	*
	* @return int
	*/
	public function get_last_post_id()
	{
		return isset($this->data['topic_last_post_id']) ? (int) $this->data['topic_last_post_id'] : 0;
	}

	/**
	* Set the last post ID
	*
	* @param int				$id		Post ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_last_post_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if ($id && !$this->entity_helper->check_post_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_last_post_id', 'NOT_EXISTS']);
		}
		else
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_last_post_id', 'EMPTY']);
		}

		// Set the value on our data array
		$this->data['topic_last_post_id'] = $id;

		return $this;
	}

	/**
	* Get the last poster ID
	*
	* @return int
	*/
	public function get_last_poster_id()
	{
		return isset($this->data['topic_last_poster_id']) ? (int) $this->data['topic_last_poster_id'] : 0;
	}

	/**
	* Set the last poster ID
	*
	* @param int				$id		User ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_last_poster_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if ($id && !$this->entity_helper->check_user_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_last_poster_id', 'NOT_EXISTS']);
		}
		else
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_last_poster_id', 'EMPTY']);
		}

		// Set the value on our data array
		$this->data['topic_last_poster_id'] = $id;

		return $this;
	}

	/**
	* Get the last poster username
	*
	* @return string
	*/
	public function get_last_poster_name()
	{
		return isset($this->data['topic_last_poster_name']) ? (string) $this->data['topic_last_poster_name'] : '';
	}

	/**
	* Set the last poster username
	*
	* @param string				$text	Username
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_last_poster_name($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_last_poster_name', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_username($text, $this->get_last_poster_id()))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_last_poster_name', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['topic_last_poster_name'] = $text;

		return $this;
	}

	/**
	* Get the last poster username color
	*
	* @return string
	*/
	public function get_last_poster_colour()
	{
		return isset($this->data['topic_last_poster_colour']) ? (string) $this->data['topic_last_poster_colour'] : '';
	}

	/**
	* Set the last poster username color
	*
	* @param string				$text	6-char HEX code without #
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_last_poster_colour($text)
	{
		$text = (string) $text;

		// Check invalid characters
		if (!preg_match('/([a-f0-9]{3}){1,2}\b/i', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_last_poster_colour', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['topic_last_poster_colour'] = $text;

		return $this;
	}

	/**
	* Get the last post subject
	*
	* @return string
	*/
	public function get_last_post_subject()
	{
		return isset($this->data['topic_last_post_subject']) ? (string) $this->data['topic_last_post_subject'] : '';
	}

	/**
	* Set the last post subject
	*
	* @param string				$text	Post subject
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_last_post_subject($text)
	{
		$text = (string) $text;

		// Check the max length
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_last_post_subject', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['topic_last_post_subject'] = $text;

		return $this;
	}

	/**
	* Get the topic's last post time
	*
	* @return int
	*/
	public function get_last_post_time()
	{
		return isset($this->data['topic_last_post_time']) ? (int) $this->data['topic_last_post_time'] : 0;
	}

	/**
	* Get the topic's last view time
	*
	* @return int
	*/
	public function get_last_view_time()
	{
		return isset($this->data['topic_last_view_time']) ? (int) $this->data['topic_last_view_time'] : 0;
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
