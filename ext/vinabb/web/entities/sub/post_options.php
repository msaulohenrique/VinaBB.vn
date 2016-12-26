<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for post_options + post_actions + post_text
*/
class post_options extends post_actions
{
	/** @var array $data */
	protected $data;

	/**
	* Get the post visibility
	*
	* @return int
	*/
	public function get_visibility()
	{
		return isset($this->data['post_visibility']) ? (int) $this->data['post_visibility'] : 0;
	}

	/**
	* Set the post visibility
	*
	* @param int			$value	Visibility value
	* @return post_actions	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_visibility($value)
	{
		// Set the value on our data array
		$this->data['post_visibility'] = (int) $value;

		return $this;
	}

	/**
	* Does the post have attachments?
	*
	* @return bool
	*/
	public function get_attachment()
	{
		return isset($this->data['post_attachment']) ? (bool) $this->data['post_attachment'] : false;
	}

	/**
	* Set the post have or have not attachments
	*
	* @param bool			$value	true: yes; false: no
	* @return post_actions	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_attachment($value)
	{
		// Set the value on our data array
		$this->data['post_attachment'] = (bool) $value;

		return $this;
	}

	/**
	* Does the post have open reports?
	*
	* @return bool
	*/
	public function get_reported()
	{
		return isset($this->data['post_reported']) ? (bool) $this->data['post_reported'] : false;
	}

	/**
	* Set the post have or have not open reports
	*
	* @param bool			$value	true: yes; false: no
	* @return post_actions	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_reported($value)
	{
		// Set the value on our data array
		$this->data['post_reported'] = (bool) $value;

		return $this;
	}


	/**
	* Get the post option: Attach signature
	*
	* @return bool
	*/
	public function get_enable_sig()
	{
		return isset($this->data['enable_sig']) ? (bool) $this->data['enable_sig'] : true;
	}

	/**
	* Set the post option: Attach signature
	*
	* @param bool			$value	true: enable; false: disable
	* @return post_actions	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_sig($value)
	{
		// Set the value on our data array
		$this->data['enable_sig'] = (bool) $value;

		return $this;
	}


	/**
	* Get the post option: Count the number of user posts
	*
	* @return bool
	*/
	public function get_postcount()
	{
		return isset($this->data['post_postcount']) ? (bool) $this->data['post_postcount'] : true;
	}

	/**
	* Set the post option: Count the number of user posts
	*
	* @param bool			$value	true: yes; false: no
	* @return post_actions	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_postcount($value)
	{
		// Set the value on our data array
		$this->data['post_postcount'] = (bool) $value;

		return $this;
	}

	/**
	* Get the post checksum
	*
	* @return string
	*/
	public function get_checksum()
	{
		return isset($this->data['post_checksum']) ? (string) $this->data['post_checksum'] : '';
	}

	/**
	* Set the post checksum
	*
	* @param string			$text	Checksum
	* @return post_actions	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_checksum($text)
	{
		$text = (string) $text;

		// Check the max length
		if (utf8_strlen($text) > 32)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['post_checksum', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['post_checksum'] = $text;

		return $this;
	}
}
