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
	* Get the user's joined date
	*
	* @return int
	*/
	public function get_regdate()
	{
		return isset($this->data['user_regdate']) ? (int) $this->data['user_regdate'] : 0;
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
	* Get the last time user marked
	*
	* @return int
	*/
	public function get_lastmark()
	{
		return isset($this->data['user_lastmark']) ? (int) $this->data['user_lastmark'] : 0;
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
	* Get the last time user posted
	*
	* @return int
	*/
	public function get_lastpost_time()
	{
		return isset($this->data['user_lastpost_time']) ? (int) $this->data['user_lastpost_time'] : 0;
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
	* Get the last time user got warnings
	*
	* @return int
	*/
	public function get_last_warning()
	{
		return isset($this->data['user_last_warning']) ? (int) $this->data['user_last_warning'] : 0;
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
	* Get the last time user got email sent from board
	*
	* @return int
	*/
	public function get_emailtime()
	{
		return isset($this->data['user_emailtime']) ? (int) $this->data['user_emailtime'] : 0;
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
	* Get the last time user to be inactived
	*
	* @return int
	*/
	public function get_inactive_time()
	{
		return isset($this->data['user_inactive_time']) ? (int) $this->data['user_inactive_time'] : 0;
	}
}
