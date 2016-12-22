<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for user_reg + user_profile + user_sig
*/
class user_reg extends user_profile
{
	/** @var array $data */
	protected $data;

	/**
	* Get user's registered IP
	*
	* @return string
	*/
	public function get_ip()
	{
		return isset($this->data['user_ip']) ? (string) $this->data['user_ip'] : '';
	}

	/**
	* Get user's email hash
	*
	* @return int
	*/
	public function get_email_hash()
	{
		return isset($this->data['user_email_hash']) ? (int) $this->data['user_email_hash'] : 0;
	}

	/**
	* Get user's form hash
	*
	* @return string
	*/
	public function get_form_salt()
	{
		return isset($this->data['user_form_salt']) ? (string) $this->data['user_form_salt'] : '';
	}

	/**
	* Get user permissions
	*
	* @return string
	*/
	public function get_permissions()
	{
		return isset($this->data['user_permissions']) ? (string) $this->data['user_permissions'] : '';
	}

	/**
	* User has switched permissions to who?
	*
	* @return string
	*/
	public function get_perm_from()
	{
		return isset($this->data['user_perm_from']) ? (string) $this->data['user_perm_from'] : '';
	}

	/**
	* Get user's activation key
	*
	* @return string
	*/
	public function get_actkey()
	{
		return isset($this->data['user_actkey']) ? (string) $this->data['user_actkey'] : '';
	}

	/**
	* Get user's last confirm key
	*
	* @return string
	*/
	public function get_last_confirm_key()
	{
		return isset($this->data['user_last_confirm_key']) ? (string) $this->data['user_last_confirm_key'] : '';
	}

	/**
	* Get number of failed logins
	*
	* @return int
	*/
	public function get_login_attempts()
	{
		return isset($this->data['user_login_attempts']) ? (int) $this->data['user_login_attempts'] : 0;
	}

	/**
	* Get user's new reset password
	*
	* @return string
	*/
	public function get_newpasswd()
	{
		return isset($this->data['user_newpasswd']) ? (string) $this->data['user_newpasswd'] : '';
	}

	/**
	* Get number of reminders
	*
	* @return int
	*/
	public function get_reminded()
	{
		return isset($this->data['user_reminded']) ? (int) $this->data['user_reminded'] : NOTIFY_EMAIL;
	}

	/**
	* Get the inactive reason
	*
	* @return int
	*/
	public function get_inactive_reason()
	{
		return isset($this->data['user_inactive_reason']) ? (int) $this->data['user_inactive_reason'] : 0;
	}

	/**
	* Set the inactive reason
	*
	* @param int		$value	Inactive reason constant value
	* @return user_reg	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_inactive_reason($value)
	{
		$value = (int) $value;

		if (!in_array($value, [0, INACTIVE_REGISTER, INACTIVE_PROFILE, INACTIVE_MANUAL, INACTIVE_REMIND]))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('user_inactive_reason');
		}

		// Set the value on our data array
		$this->data['user_inactive_reason'] = $value;

		return $this;
	}
}
