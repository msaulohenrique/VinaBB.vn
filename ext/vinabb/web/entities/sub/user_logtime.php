<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for user_logtime + user_options + user_reg + user_profile + user_sig
*/
class user_logtime extends user_options
{
	/** @var array $data */
	protected $data;

	/**
	* Get the last page user visited
	*
	* @return string
	*/
	public function get_lastpage()
	{
		return isset($this->data['user_lastpage']) ? (string) $this->data['user_lastpage'] : '';
	}

	/**
	* Set the last page user visited
	*
	* @param string			$text	Last page
	* @return user_logtime	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_lastpage($text)
	{
		// Set the value on our data array
		$this->data['user_lastpage'] = (string) $text;

		return $this;
	}

	/**
	* Get the user's joined date
	*
	* @return int
	*/
	public function get_regdate()
	{
		return isset($this->data['user_regdate']) ? (int) $this->data['user_regdate'] : 0;
	}

	/**
	* Set the user's joined date
	*
	* @param int			$value	UNIX timestamp
	* @return user_logtime	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_regdate($value)
	{
		// Set the value on our data array
		$this->data['user_regdate'] = (int) $value;

		return $this;
	}

	/**
	* Get the last time user visited
	*
	* @return int
	*/
	public function get_lastvisit()
	{
		return isset($this->data['user_lastvisit']) ? (int) $this->data['user_lastvisit'] : 0;
	}

	/**
	* Set the last time user visited
	*
	* @param int			$value	UNIX timestamp
	* @return user_logtime	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_lastvisit($value)
	{
		// Set the value on our data array
		$this->data['user_lastvisit'] = (int) $value;

		return $this;
	}

	/**
	* Get the last time user marked
	*
	* @return int
	*/
	public function get_lastmark()
	{
		return isset($this->data['user_lastmark']) ? (int) $this->data['user_lastmark'] : 0;
	}

	/**
	* Set the last time user marked
	*
	* @param int			$value	UNIX timestamp
	* @return user_logtime	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_lastmark($value)
	{
		// Set the value on our data array
		$this->data['user_lastmark'] = (int) $value;

		return $this;
	}

	/**
	* Get the last time user searched
	*
	* @return int
	*/
	public function get_last_search()
	{
		return isset($this->data['user_last_search']) ? (int) $this->data['user_last_search'] : 0;
	}

	/**
	* Set the last time user searched
	*
	* @param int			$value	UNIX timestamp
	* @return user_logtime	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_last_search($value)
	{
		// Set the value on our data array
		$this->data['user_last_search'] = (int) $value;

		return $this;
	}

	/**
	* Get the last time user posted
	*
	* @return int
	*/
	public function get_lastpost_time()
	{
		return isset($this->data['user_lastpost_time']) ? (int) $this->data['user_lastpost_time'] : 0;
	}

	/**
	* Set the last time user posted
	*
	* @param int			$value	UNIX timestamp
	* @return user_logtime	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_lastpost_time($value)
	{
		// Set the value on our data array
		$this->data['user_lastpost_time'] = (int) $value;

		return $this;
	}

	/**
	* Get the last time user sent PMs
	*
	* @return int
	*/
	public function get_last_privmsg()
	{
		return isset($this->data['user_last_privmsg']) ? (int) $this->data['user_last_privmsg'] : 0;
	}

	/**
	* Set the last time user sent PMs
	*
	* @param int			$value	UNIX timestamp
	* @return user_logtime	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_last_privmsg($value)
	{
		// Set the value on our data array
		$this->data['user_last_privmsg'] = (int) $value;

		return $this;
	}

	/**
	* Get the last time user got warnings
	*
	* @return int
	*/
	public function get_last_warning()
	{
		return isset($this->data['user_last_warning']) ? (int) $this->data['user_last_warning'] : 0;
	}

	/**
	* Set the last time user got warnings
	*
	* @param int			$value	UNIX timestamp
	* @return user_logtime	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_last_warning($value)
	{
		// Set the value on our data array
		$this->data['user_last_warning'] = (int) $value;

		return $this;
	}

	/**
	* Get the last time user changed password
	*
	* @return int
	*/
	public function get_passchg()
	{
		return isset($this->data['user_passchg']) ? (int) $this->data['user_passchg'] : 0;
	}

	/**
	* Set the last time user changed password
	*
	* @param int			$value	UNIX timestamp
	* @return user_logtime	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_passchg($value)
	{
		// Set the value on our data array
		$this->data['user_passchg'] = (int) $value;

		return $this;
	}

	/**
	* Get the last time user got email sent from board
	*
	* @return int
	*/
	public function get_emailtime()
	{
		return isset($this->data['user_emailtime']) ? (int) $this->data['user_emailtime'] : 0;
	}

	/**
	* Set the last time user got email sent from board
	*
	* @param int			$value	UNIX timestamp
	* @return user_logtime	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_emailtime($value)
	{
		// Set the value on our data array
		$this->data['user_emailtime'] = (int) $value;

		return $this;
	}

	/**
	* Get the last time user got reminds
	*
	* @return int
	*/
	public function get_reminded_time()
	{
		return isset($this->data['user_reminded_time']) ? (int) $this->data['user_reminded_time'] : 0;
	}

	/**
	* Set the last time user got reminds
	*
	* @param int			$value	UNIX timestamp
	* @return user_logtime	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_reminded_time($value)
	{
		// Set the value on our data array
		$this->data['user_reminded_time'] = (int) $value;

		return $this;
	}

	/**
	* Get the last time user to be inactived
	*
	* @return int
	*/
	public function get_inactive_time()
	{
		return isset($this->data['user_inactive_time']) ? (int) $this->data['user_inactive_time'] : 0;
	}

	/**
	* Set the last time user to be inactived
	*
	* @param int			$value	UNIX timestamp
	* @return user_logtime	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_inactive_time($value)
	{
		// Set the value on our data array
		$this->data['user_inactive_time'] = (int) $value;

		return $this;
	}
}
