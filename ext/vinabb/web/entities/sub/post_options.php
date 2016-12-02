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
	/** @var array */
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
	* Does the post have attachments?
	*
	* @return bool
	*/
	public function get_attachment()
	{
		return isset($this->data['post_attachment']) ? (int) $this->data['post_attachment'] : false;
	}

	/**
	* Does the post have open reports?
	*
	* @return bool
	*/
	public function get_reported()
	{
		return isset($this->data['post_reported']) ? (int) $this->data['post_reported'] : false;
	}

	/**
	* Get the post option: Attach signature
	*
	* @return bool
	*/
	public function get_enable_sig()
	{
		return isset($this->data['enable_sig']) ? (int) $this->data['enable_sig'] : true;
	}

	/**
	* Get the post option: Count the number of user posts
	*
	* @return bool
	*/
	public function get_postcount()
	{
		return isset($this->data['post_postcount']) ? (int) $this->data['post_postcount'] : true;
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
