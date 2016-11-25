<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

use vinabb\web\includes\constants;

/**
* Sub-entity for forum_options + forum_last_post + forum_prune + forum_desc_rules
*/
class forum_options extends forum_last_post
{
	/** @var array */
	protected $data;

	/**
	* Get the forum redirect link
	*
	* @return string
	*/
	public function get_forum_link()
	{
		return isset($this->data['forum_link']) ? (string) $this->data['forum_link'] : '';
	}

	/**
	* Set the forum redirect link
	*
	* @param string			$text	URL
	* @return forum_options	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_forum_link($text)
	{
		$text = (string) $text;

		// Check the max length
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['forum_link', 'TOO_LONG']);
		}

		// Checking for valid URL
		if (filter_var($text, FILTER_VALIDATE_URL) === false)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['forum_link', 'INVALID_URL']);
		}

		// Set the value on our data array
		$this->data['forum_link'] = $text;

		return $this;
	}


	/**
	* Get the forum password
	*
	* @return string
	*/
	public function get_forum_password()
	{
		return isset($this->data['forum_password']) ? (string) $this->data['forum_password'] : '';
	}

	/**
	* Set the forum password
	*
	* @param string			$text	Forum password
	* @return forum_options	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_forum_password($text)
	{
		$text = (string) $text;

		// Check the max length
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['forum_password', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['forum_password'] = $text;

		return $this;
	}

	/**
	* Get the forum style
	*
	* @return int
	*/
	public function get_forum_style()
	{
		return isset($this->data['forum_style']) ? (int) $this->data['forum_style'] : 0;
	}

	/**
	* Get the forum image
	*
	* @return string
	*/
	public function get_forum_image()
	{
		return isset($this->data['forum_image']) ? (string) $this->data['forum_image'] : '';
	}

	/**
	* Set the forum image
	*
	* @param string			$text	Image URL
	* @return forum_options	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_forum_image($text)
	{
		$text = (string) $text;

		// Check the max length
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['forum_image', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['forum_image'] = $text;

		return $this;
	}

	/**
	* Get forum flags
	*
	* @return int
	*/
	public function get_forum_flags()
	{
		return isset($this->data['forum_flags']) ? (int) $this->data['forum_flags'] : 32;
	}

	/**
	* Get forum options
	*
	* @return int
	*/
	public function get_forum_options()
	{
		return isset($this->data['forum_options']) ? (int) $this->data['forum_options'] : 0;
	}

	/**
	* Get the forum setting: Display on the board page
	*
	* @return bool
	*/
	public function get_display_on_index()
	{
		return isset($this->data['display_on_index']) ? (bool) $this->data['display_on_index'] : true;
	}

	/**
	* Get the forum setting: Enable creating search indexes
	*
	* @return bool
	*/
	public function get_enable_indexing()
	{
		return isset($this->data['enable_indexing']) ? (bool) $this->data['enable_indexing'] : true;
	}

	/**
	* Get the forum setting: Enable topic/post icons
	*
	* @return bool
	*/
	public function get_enable_icons()
	{
		return isset($this->data['enable_icons']) ? (bool) $this->data['enable_icons'] : true;
	}

	/**
	* Get the forum setting: Display list of subforums
	*
	* @return bool
	*/
	public function get_display_subforum_list()
	{
		return isset($this->data['display_subforum_list']) ? (bool) $this->data['display_subforum_list'] : true;
	}
}
