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
	* Get user's registered IP
	*
	* @return string
	*/
	public function get_ip()
	{
		return isset($this->data['user_ip']) ? (string) $this->data['user_ip'] : '';
	}

	/**
	* Set user's registered IP
	*
	* @param string		$text	User IP
	* @return user_reg	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_ip($text)
	{
		$text = (string) $text;

		// Checking for valid IP address
		if ($text != '' && filter_var($text, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === false && filter_var($text, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === false)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_ip', 'INVALID_IP']);
		}

		// Set the value on our data array
		$this->data['user_ip'] = $text;

		return $this;
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
	* Set user's email hash
	*
	* @param int		$value	Email hash
	* @return user_reg	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_email_hash($value)
	{
		// Set the value on our data array
		$this->data['user_email_hash'] = (int) $value;

		return $this;
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
	* Set user's form hash
	*
	* @param string		$text	Form hash
	* @return user_reg	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_form_salt($text)
	{
		// Set the value on our data array
		$this->data['user_form_salt'] = (string) $text;

		return $this;
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
	* Set user permissions
	*
	* @param string		$text	Permissions
	* @return user_reg	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_permissions($text)
	{
		// Set the value on our data array
		$this->data['user_permissions'] = (string) $text;

		return $this;
	}

	/**
	* User has switched permissions to who?
	*
	* @return int
	*/
	public function get_perm_from()
	{
		return isset($this->data['user_perm_from']) ? (int) $this->data['user_perm_from'] : 0;
	}

	/**
	* Set whom the user has switched permissions to
	*
	* @param int		$id		User ID
	* @return user_reg	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_perm_from($id)
	{
		$id = (int) $id;

		// Check existing user
		if ($id && !$this->entity_helper->check_user_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_perm_from', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['user_perm_from'] = $id;

		return $this;
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
	* Set user's activation key
	*
	* @param string		$text	Activation key
	* @return user_reg	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_actkey($text)
	{
		// Set the value on our data array
		$this->data['user_actkey'] = (string) $text;

		return $this;
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
	* Set user's last confirm key
	*
	* @param string		$text	Confirm key
	* @return user_reg	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_last_confirm_key($text)
	{
		// Set the value on our data array
		$this->data['user_last_confirm_key'] = (string) $text;

		return $this;
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
	* Set number of failed logins
	*
	* @param int		$value	Number of failed logins
	* @return user_reg	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_login_attempts($value)
	{
		// Set the value on our data array
		$this->data['user_login_attempts'] = (int) $value;

		return $this;
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
	* Set user's new reset password
	*
	* @param string		$text	New password
	* @return user_reg	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_newpasswd($text)
	{
		// Set the value on our data array
		$this->data['user_newpasswd'] = (string) $text;

		return $this;
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
	* Set number of reminders
	*
	* @param int		$value	Email
	* @return user_reg	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_reminded($value)
	{
		// Set the value on our data array
		$this->data['user_reminded'] = (int) $value;

		return $this;
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
