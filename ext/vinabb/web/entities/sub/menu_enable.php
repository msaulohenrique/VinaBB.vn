<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for page/menu_enable
*/
class menu_enable
{
	/** @var array */
	protected $data;

	/** @var \phpbb\user */
	protected $user;

	/**
	* Constructor
	*
	* @param \phpbb\user $user
	*/
	public function __construct(\phpbb\user $user)
	{
		$this->user = $user;
	}

	/**
	* Get menu display setting for guests
	*
	* @return bool
	*/
	public function get_enable_guest()
	{
		return isset($this->data['menu_enable_guest']) ? (bool) $this->data['menu_enable_guest'] : true;
	}

	/**
	* Get menu display setting for bots
	*
	* @return bool
	*/
	public function get_enable_bot()
	{
		return isset($this->data['menu_enable_bot']) ? (bool) $this->data['menu_enable_bot'] : true;
	}

	/**
	* Get menu display setting for newly registered users
	*
	* @return bool
	*/
	public function get_enable_new_user()
	{
		return isset($this->data['menu_enable_new_user']) ? (bool) $this->data['menu_enable_new_user'] : true;
	}

	/**
	* Get menu display setting for registered users
	*
	* @return bool
	*/
	public function get_enable_user()
	{
		return isset($this->data['menu_enable_user']) ? (bool) $this->data['menu_enable_user'] : true;
	}

	/**
	* Get menu display setting for moderators
	*
	* @return bool
	*/
	public function get_enable_mod()
	{
		return isset($this->data['menu_enable_mod']) ? (bool) $this->data['menu_enable_mod'] : true;
	}

	/**
	* Get menu display setting for global moderators
	*
	* @return bool
	*/
	public function get_enable_global_mod()
	{
		return isset($this->data['menu_enable_global_mod']) ? (bool) $this->data['menu_enable_global_mod'] : true;
	}

	/**
	* Get menu display setting for administrators
	*
	* @return bool
	*/
	public function get_enable_admin()
	{
		return isset($this->data['menu_enable_admin']) ? (bool) $this->data['menu_enable_admin'] : true;
	}

	/**
	* Get menu display setting for founders
	*
	* @return bool
	*/
	public function get_enable_founder()
	{
		return isset($this->data['menu_enable_founder']) ? (bool) $this->data['menu_enable_founder'] : true;
	}

	/**
	* Set menu display setting for founders
	*
	* @param bool			$value	Config value
	* @return menu_enable	$this	Object for chaining calls: load()->set()->save()
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
		$this->data['menu_enable_founder'] = (bool) $value;

		return $this;
	}
}
