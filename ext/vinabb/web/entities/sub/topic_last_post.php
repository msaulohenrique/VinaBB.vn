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
* Sub-entity for topic_last_post + topic_poll
*/
class topic_last_post extends topic_poll
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
	* @return topic_last_post	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_last_post_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if (!$id)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_last_post_id', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_post_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_last_post_id', 'NOT_EXISTS']);
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
	* @return topic_last_post	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_last_poster_id($id)
	{
		$id = (int) $id;

		// This is a required field
		if (!$id)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_last_poster_id', 'EMPTY']);
		}
		else if (!$this->entity_helper->check_user_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['topic_last_poster_id', 'NOT_EXISTS']);
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
	* @return topic_last_post	$this	Object for chaining calls: load()->set()->save()
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
	* @return topic_last_post	$this	Object for chaining calls: load()->set()->save()
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
	* @return topic_last_post	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_last_post_subject($text)
	{
		$text = (string) $text;

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
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
}
