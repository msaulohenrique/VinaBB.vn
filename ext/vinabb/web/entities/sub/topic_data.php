<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for topic_data + topic_actions + topic_last_post + topic_poll
*/
class topic_data extends topic_actions
{
	/** @var array */
	protected $data;

	/**
	* Get the topic type
	*
	* @return int
	*/
	public function get_type()
	{
		return isset($this->data['topic_type']) ? (int) $this->data['topic_type'] : POST_NORMAL;
	}

	/**
	* Set the topic type
	*
	* @param int			$value	Topic type
	* @return topic_data	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_type($value)
	{
		$value = (int) $value;

		if (!in_array($value, [POST_NORMAL, POST_STICKY, POST_ANNOUNCE, POST_GLOBAL]))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('topic_type');
		}

		// Set the value on our data array
		$this->data['topic_type'] = $value;

		return $this;
	}

	/**
	* Get the topic status
	*
	* @return int
	*/
	public function get_status()
	{
		return isset($this->data['topic_status']) ? (int) $this->data['topic_status'] : ITEM_UNLOCKED;
	}

	/**
	* Set the topic status
	*
	* @param int			$value	Topic status
	* @return topic_data	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_status($value)
	{
		$value = (int) $value;

		if (!in_array($value, [ITEM_UNLOCKED, ITEM_LOCKED, ITEM_MOVED]))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('topic_status');
		}

		// Set the value on our data array
		$this->data['topic_status'] = $value;

		return $this;
	}

	/**
	* Get the topic views
	*
	* @return int
	*/
	public function get_views()
	{
		return isset($this->data['topic_views']) ? (int) $this->data['topic_views'] : 0;
	}

	/**
	* Get the number of approved posts
	*
	* @return int
	*/
	public function get_posts_approved()
	{
		return isset($this->data['topic_posts_approved']) ? (int) $this->data['topic_posts_approved'] : 0;
	}

	/**
	* Get the number of disapproved posts
	*
	* @return int
	*/
	public function get_posts_unapproved()
	{
		return isset($this->data['topic_posts_unapproved']) ? (int) $this->data['topic_posts_unapproved'] : 0;
	}

	/**
	* Get the number of soft-deleted posts
	*
	* @return int
	*/
	public function get_posts_softdeleted()
	{
		return isset($this->data['topic_posts_softdeleted']) ? (int) $this->data['topic_posts_softdeleted'] : 0;
	}

	/**
	* Get the topic time
	*
	* @return int
	*/
	public function get_time()
	{
		return isset($this->data['topic_time']) ? (int) $this->data['topic_time'] : 0;
	}

	/**
	* Get the topic time limit
	*
	* @return int
	*/
	public function get_time_limit()
	{
		return isset($this->data['topic_time_limit']) ? (int) $this->data['topic_time_limit'] : 0;
	}
}
