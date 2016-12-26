<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for group_settings + group_profile + group_desc
*/
class group_settings extends group_profile
{
	/** @var array $data */
	protected $data;

	/**
	* Only founders can manage the group?
	*
	* @return bool
	*/
	public function get_founder_manage()
	{
		return isset($this->data['group_founder_manage']) ? (bool) $this->data['group_founder_manage'] : false;
	}

	/**
	* Set only founders can manage the group or not
	*
	* @param bool				$value	true: yes; false: no
	* @return group_settings	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_founder_manage($value)
	{
		// Set the value on our data array
		$this->data['group_founder_manage'] = (bool) $value;

		return $this;
	}

	/**
	* Exclude group leader from group permissions
	*
	* @return bool
	*/
	public function get_skip_auth()
	{
		return isset($this->data['group_skip_auth']) ? (bool) $this->data['group_skip_auth'] : false;
	}

	/**
	* Set exclude group leader from group permissions or not
	*
	* @param bool				$value	true: yes; false: no
	* @return group_settings	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_skip_auth($value)
	{
		// Set the value on our data array
		$this->data['group_skip_auth'] = (bool) $value;

		return $this;
	}

	/**
	* Display group in the legend?
	*
	* @return bool
	*/
	public function get_display()
	{
		return isset($this->data['group_display']) ? (bool) $this->data['group_display'] : false;
	}

	/**
	* Set display group in the legend or not
	*
	* @param bool				$value	true: yes; false: no
	* @return group_settings	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_display($value)
	{
		// Set the value on our data array
		$this->data['group_display'] = (bool) $value;

		return $this;
	}

	/**
	* Group can receive PMs?
	*
	* @return bool
	*/
	public function get_receive_pm()
	{
		return isset($this->data['group_receive_pm']) ? (bool) $this->data['group_receive_pm'] : false;
	}

	/**
	* Set the group can receive PMs or not
	*
	* @param bool				$value	true: yes; false: no
	* @return group_settings	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_receive_pm($value)
	{
		// Set the value on our data array
		$this->data['group_receive_pm'] = (bool) $value;

		return $this;
	}

	/**
	* Get the maximum characters in signature
	*
	* @return int
	*/
	public function get_sig_chars()
	{
		return isset($this->data['group_sig_chars']) ? (int) $this->data['group_sig_chars'] : 0;
	}

	/**
	* Set the maximum characters in signature
	*
	* @param int				Number of characters
	* @return group_settings	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_sig_chars($value)
	{
		// Set the value on our data array
		$this->data['group_sig_chars'] = (int) $value;

		return $this;
	}

	/**
	* Get the maximum PMs per folder
	*
	* @return int
	*/
	public function get_message_limit()
	{
		return isset($this->data['group_message_limit']) ? (int) $this->data['group_message_limit'] : 0;
	}

	/**
	* Set the maximum PMs per folder
	*
	* @param int				Number of PMs
	* @return group_settings	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_message_limit($value)
	{
		// Set the value on our data array
		$this->data['group_message_limit'] = (int) $value;

		return $this;
	}

	/**
	* Get the order in group legend
	*
	* @return int
	*/
	public function get_legend()
	{
		return isset($this->data['group_legend']) ? (int) $this->data['group_legend'] : 0;
	}

	/**
	* Set the order in group legend
	*
	* @param int				Order value
	* @return group_settings	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_legend($value)
	{
		// Set the value on our data array
		$this->data['group_legend'] = (int) $value;

		return $this;
	}

	/**
	* Get the maximum recipients per PM
	*
	* @return int
	*/
	public function get_max_recipients()
	{
		return isset($this->data['group_max_recipients']) ? (int) $this->data['group_max_recipients'] : 0;
	}

	/**
	* Set the maximum recipients per PM
	*
	* @param int				Number of recipients
	* @return group_settings	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_max_recipients($value)
	{
		// Set the value on our data array
		$this->data['group_max_recipients'] = (int) $value;

		return $this;
	}
}
