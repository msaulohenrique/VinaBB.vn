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
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
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
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
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
	* Set the forum style
	*
	* @param string			$id		Style ID
	* @return forum_options	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_forum_style($id)
	{
		$id = (int) $id;

		// Check existing style
		if ($id && !$this->entity_helper->check_style_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['forum_style', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['forum_style'] = $id;

		return $this;
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
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
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
	* Set forum flags
	*
	* @param int			$value	Forum flags
	* @return forum_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_forum_flags($value)
	{
		// Set the value on our data array
		$this->data['forum_flags'] = (int) $value;

		return $this;
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
	* Set forum options
	*
	* @param int			$value	Forum options
	* @return forum_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_forum_options($value)
	{
		// Set the value on our data array
		$this->data['forum_options'] = (int) $value;

		return $this;
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
	* Set the forum setting: Display on the board page
	*
	* @param int			$value	true: enable; false: disable
	* @return forum_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_display_on_index($value)
	{
		// Set the value on our data array
		$this->data['display_on_index'] = (bool) $value;

		return $this;
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
	* Set the forum setting: Enable creating search indexes
	*
	* @param bool			$value	true: enable; false: disable
	* @return forum_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_indexing($value)
	{
		// Set the value on our data array
		$this->data['enable_indexing'] = (bool) $value;

		return $this;
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
	* Set the forum setting: Enable topic/post icons
	*
	* @param bool			$value	true: enable; false: disable
	* @return forum_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_icons($value)
	{
		// Set the value on our data array
		$this->data['enable_icons'] = (bool) $value;

		return $this;
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

	/**
	* Set the forum setting: Display list of subforums
	*
	* @param bool			$value	true: enable; false: disable
	* @return forum_options	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_display_subforum_list($value)
	{
		// Set the value on our data array
		$this->data['display_subforum_list'] = (bool) $value;

		return $this;
	}
}
