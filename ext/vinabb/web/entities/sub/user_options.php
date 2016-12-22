<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for user_options + user_reg + user_profile + user_sig
*/
class user_options extends user_reg
{
	/** @var array $data */
	protected $data;

	/**
	* Get user's Jabber account
	*
	* @return string
	*/
	public function get_jabber()
	{
		return isset($this->data['user_jabber']) ? (string) $this->data['user_jabber'] : '';
	}

	/**
	* Get the user option: Notify when have new replies
	*
	* @return bool
	*/
	public function get_notify()
	{
		return isset($this->data['user_notify']) ? (bool) $this->data['user_notify'] : false;
	}

	/**
	* Get the user option: Notify when have new PMs
	*
	* @return bool
	*/
	public function get_notify_pm()
	{
		return isset($this->data['user_notify_pm']) ? (bool) $this->data['user_notify_pm'] : true;
	}

	/**
	* Get the user option: Notification method
	*
	* @return int
	*/
	public function get_notify_type()
	{
		return isset($this->data['user_notify_type']) ? (int) $this->data['user_notify_type'] : NOTIFY_EMAIL;
	}

	/**
	* Set the user option: Action if a PM folder is full
	*
	* @param int			$value	Notify type
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_notify_type($value)
	{
		$value = (int) $value;

		if (!in_array($value, [NOTIFY_EMAIL, NOTIFY_IM, NOTIFY_BOTH]))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('user_notify_type');
		}

		// Set the value on our data array
		$this->data['user_notify_type'] = $value;

		return $this;
	}

	/**
	* Get the user option: Receive PMs from other users
	*
	* @return bool
	*/
	public function get_allow_pm()
	{
		return isset($this->data['user_allow_pm']) ? (bool) $this->data['user_allow_pm'] : true;
	}

	/**
	* Get the user option: Show online status
	*
	* @return bool
	*/
	public function get_allow_viewonline()
	{
		return isset($this->data['user_allow_viewonline']) ? (bool) $this->data['user_allow_viewonline'] : true;
	}

	/**
	* Get the user option: Receive emails from other users
	*
	* @return bool
	*/
	public function get_allow_viewemail()
	{
		return isset($this->data['user_allow_viewemail']) ? (bool) $this->data['user_allow_viewemail'] : true;
	}

	/**
	* Get the user option: Receive emails from administrators
	*
	* @return bool
	*/
	public function get_allow_massemail()
	{
		return isset($this->data['user_allow_massemail']) ? (bool) $this->data['user_allow_massemail'] : true;
	}

	/**
	* Get the user option: Action if a PM folder is full
	*
	* @return int
	*/
	public function get_full_folder()
	{
		return isset($this->data['user_full_folder']) ? (int) $this->data['user_full_folder'] : FULL_FOLDER_NONE;
	}

	/**
	* Set the user option: Action if a PM folder is full
	*
	* @param int			$value	Action type
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_full_folder($value)
	{
		$value = (int) $value;

		if (!in_array($value, [FULL_FOLDER_NONE, FULL_FOLDER_DELETE, FULL_FOLDER_HOLD]))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('user_full_folder');
		}

		// Set the value on our data array
		$this->data['user_full_folder'] = $value;

		return $this;
	}

	/**
	* Get the user option: Display topics from x days
	*
	* @return int
	*/
	public function get_topic_show_days()
	{
		return isset($this->data['user_topic_show_days']) ? (int) $this->data['user_topic_show_days'] : 0;
	}

	/**
	* Get the user option: Sorting order of topics
	*
	* @return string
	*/
	public function get_topic_sortby_type()
	{
		return isset($this->data['user_topic_sortby_type']) ? (string) $this->data['user_topic_sortby_type'] : 't';
	}

	/**
	* Get the user option: Sorting direction of topics
	*
	* @return string
	*/
	public function get_topic_sortby_dir()
	{
		return isset($this->data['user_topic_sortby_dir']) ? (string) $this->data['user_topic_sortby_dir'] : 'd';
	}

	/**
	* Get the user option: Display posts from x days
	*
	* @return int
	*/
	public function get_post_show_days()
	{
		return isset($this->data['user_post_show_days']) ? (int) $this->data['user_post_show_days'] : 0;
	}

	/**
	* Get the user option: Sorting order of posts
	*
	* @return string
	*/
	public function get_post_sortby_type()
	{
		return isset($this->data['user_post_sortby_type']) ? (string) $this->data['user_post_sortby_type'] : 't';
	}

	/**
	* Get the user option: Sorting direction of posts
	*
	* @return string
	*/
	public function get_post_sortby_dir()
	{
		return isset($this->data['user_post_sortby_dir']) ? (string) $this->data['user_post_sortby_dir'] : 'a';
	}
}
