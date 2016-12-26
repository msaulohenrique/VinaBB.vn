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
	* Set user's Jabber account
	*
	* @param string			$text	Jabber username
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_jabber($text)
	{
		// Set the value on our data array
		$this->data['user_jabber'] = (string) $text;

		return $this;
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
	* Set the user option: Notify when have new replies
	*
	* @param bool			$value	true: yes; false: no
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_notify($value)
	{
		// Set the value on our data array
		$this->data['user_notify'] = (bool) $value;

		return $this;
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
	* Set the user option: Notify when have new PMs
	*
	* @param bool			$value	true: yes; false: no
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_notify_pm($value)
	{
		// Set the value on our data array
		$this->data['user_notify_pm'] = (bool) $value;

		return $this;
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
	* Set the user option: Receive PMs from other users
	*
	* @param bool			$value	true: yes; false: no
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_allow_pm($value)
	{
		// Set the value on our data array
		$this->data['user_allow_pm'] = (bool) $value;

		return $this;
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
	* Set the user option: Show online status
	*
	* @param bool			$value	true: yes; false: no
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_allow_viewonline($value)
	{
		// Set the value on our data array
		$this->data['user_allow_viewonline'] = (bool) $value;

		return $this;
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
	* Set the user option: Receive emails from other users
	*
	* @param bool			$value	true: yes; false: no
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_allow_viewemail($value)
	{
		// Set the value on our data array
		$this->data['user_allow_viewemail'] = (bool) $value;

		return $this;
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
	* Set the user option: Receive emails from administrators
	*
	* @param bool			$value	true: yes; false: no
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_allow_massemail($value)
	{
		// Set the value on our data array
		$this->data['user_allow_massemail'] = (bool) $value;

		return $this;
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
	* Set the user option: Display topics from x days
	*
	* @param int			$value	Number of days
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_topic_show_days($value)
	{
		// Set the value on our data array
		$this->data['user_topic_show_days'] = (int) $value;

		return $this;
	}

	/**
	* Get the user option: Sorting field of topics
	*
	* @return string
	*/
	public function get_topic_sortby_type()
	{
		return isset($this->data['user_topic_sortby_type']) ? (string) $this->data['user_topic_sortby_type'] : 't';
	}

	/**
	* Set the user option: Sorting field of topics
	*
	* @param string			$text	Field key
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_topic_sortby_type($text)
	{
		$text = (string) $text;

		// Checking valid sorting fields: a: author name; t: time; r: replies; s: topic title; v: views
		if (!in_array($text, ['a', 't', 'r', 's', 'v']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('user_topic_sortby_type');
		}

		// Set the value on our data array
		$this->data['user_topic_sortby_type'] = $text;

		return $this;
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
	* Set the user option: Sorting direction of topics
	*
	* @param string			$text	Direction key
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_topic_sortby_dir($text)
	{
		$text = (string) $text;

		// Checking valid sorting direction: a: ascending; d: descending
		if (!in_array($text, ['a', 'd']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('user_topic_sortby_dir');
		}

		// Set the value on our data array
		$this->data['user_topic_sortby_dir'] = $text;

		return $this;
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
	* Set the user option: Display posts from x days
	*
	* @param int			$value	Number of days
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_post_show_days($value)
	{
		// Set the value on our data array
		$this->data['user_post_show_days'] = (int) $value;

		return $this;
	}

	/**
	* Get the user option: Sorting field of posts
	*
	* @return string
	*/
	public function get_post_sortby_type()
	{
		return isset($this->data['user_post_sortby_type']) ? (string) $this->data['user_post_sortby_type'] : 't';
	}

	/**
	* Set the user option: Sorting field of posts
	*
	* @param string			$text	Field key
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_post_sortby_type($text)
	{
		$text = (string) $text;

		// Checking valid sorting fields: a: author name; t: time; s: post subject
		if (!in_array($text, ['a', 't', 's']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('user_post_sortby_type');
		}

		// Set the value on our data array
		$this->data['user_post_sortby_type'] = $text;

		return $this;
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

	/**
	* Set the user option: Sorting direction of posts
	*
	* @param string			$text	Direction key
	* @return user_options	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_post_sortby_dir($text)
	{
		$text = (string) $text;

		// Checking valid sorting direction: a: ascending; d: descending
		if (!in_array($text, ['a', 'd']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('user_post_sortby_dir');
		}

		// Set the value on our data array
		$this->data['user_post_sortby_dir'] = $text;

		return $this;
	}
}
