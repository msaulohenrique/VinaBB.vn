<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for page/page_enable + page_text
*/
class page_enable extends page_text
{
	/** @var array $data */
	protected $data;

	/** @var \phpbb\user $user */
	protected $user;

	/**
	* Constructor
	*
	* @param \phpbb\user $user User object
	*/
	public function __construct(\phpbb\user $user)
	{
		$this->user = $user;
	}

	/**
	* Get page display setting in template
	*
	* @return bool
	*/
	public function get_enable()
	{
		return isset($this->data['page_enable']) ? (bool) $this->data['page_enable'] : true;
	}

	/**
	* Get page display setting for guests
	*
	* @return bool
	*/
	public function get_enable_guest()
	{
		return isset($this->data['page_enable_guest']) ? (bool) $this->data['page_enable_guest'] : true;
	}

	/**
	* Get page display setting for bots
	*
	* @return bool
	*/
	public function get_enable_bot()
	{
		return isset($this->data['page_enable_bot']) ? (bool) $this->data['page_enable_bot'] : true;
	}

	/**
	* Get page display setting for newly registered users
	*
	* @return bool
	*/
	public function get_enable_new_user()
	{
		return isset($this->data['page_enable_new_user']) ? (bool) $this->data['page_enable_new_user'] : true;
	}

	/**
	* Get page display setting for registered users
	*
	* @return bool
	*/
	public function get_enable_user()
	{
		return isset($this->data['page_enable_user']) ? (bool) $this->data['page_enable_user'] : true;
	}

	/**
	* Get page display setting for moderators
	*
	* @return bool
	*/
	public function get_enable_mod()
	{
		return isset($this->data['page_enable_mod']) ? (bool) $this->data['page_enable_mod'] : true;
	}

	/**
	* Get page display setting for global moderators
	*
	* @return bool
	*/
	public function get_enable_global_mod()
	{
		return isset($this->data['page_enable_global_mod']) ? (bool) $this->data['page_enable_global_mod'] : true;
	}

	/**
	* Get page display setting for administrators
	*
	* @return bool
	*/
	public function get_enable_admin()
	{
		return isset($this->data['page_enable_admin']) ? (bool) $this->data['page_enable_admin'] : true;
	}

	/**
	* Get page display setting for founders
	*
	* @return bool
	*/
	public function get_enable_founder()
	{
		return isset($this->data['page_enable_founder']) ? (bool) $this->data['page_enable_founder'] : true;
	}

	/**
	* Set page display setting for founders
	*
	* @param bool			$value	Config value
	* @return page_enable	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\not_auth
	*/
	public function set_enable_founder($value)
	{
		// Only founders can set this option
		if ($this->user->data['user_type'] != USER_FOUNDER)
		{
			throw new \vinabb\web\exceptions\not_auth('user_type');
		}

		// Set the value on our data array
		$this->data['page_enable_founder'] = (bool) $value;

		return $this;
	}
}
