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
* Sub-entity for user_profile + user_sig
*/
class user_profile extends user_sig
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
	* Get the user avatar image
	*
	* @return string
	*/
	public function get_avatar()
	{
		return isset($this->data['user_avatar']) ? (string) $this->data['user_avatar'] : '';
	}

	/**
	* Set the user avatar image
	*
	* @param string			$text	Avatar image URL
	* @return user_profile	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_avatar($text)
	{
		$text = (string) $text;

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_avatar', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['user_avatar'] = $text;

		return $this;
	}

	/**
	* Get the user avatar type
	*
	* @return string
	*/
	public function get_avatar_type()
	{
		return isset($this->data['user_avatar_type']) ? (string) $this->data['user_avatar_type'] : '';
	}

	/**
	* Set the user avatar type
	*
	* @param int			$value	Avatar type
	* @return user_profile	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_avatar_type($value)
	{
		$value = (int) $value;

		if (!in_array($value, ['avatar_driver_gravatar', 'avatar_driver_local', 'avatar_driver_remote', 'avatar_driver_upload']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('user_avatar_type');
		}

		// Set the value on our data array
		$this->data['user_avatar_type'] = $value;

		return $this;
	}

	/**
	* Get the user avatar width
	*
	* @return int
	*/
	public function get_avatar_width()
	{
		return isset($this->data['user_avatar_width']) ? (int) $this->data['user_avatar_width'] : 0;
	}

	/**
	* Get the user avatar height
	*
	* @return int
	*/
	public function get_avatar_height()
	{
		return isset($this->data['user_avatar_height']) ? (int) $this->data['user_avatar_height'] : 0;
	}

	/**
	* Get the user rank
	*
	* @return int
	*/
	public function get_rank()
	{
		return isset($this->data['user_rank']) ? (int) $this->data['user_rank'] : 0;
	}

	/**
	* Set the user rank
	*
	* @param int			$id		Rank ID
	* @return user_profile	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_rank($id)
	{
		$id = (int) $id;

		// Check existing rank
		if ($id && !$this->entity_helper->check_rank_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_rank', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['user_rank'] = $id;

		return $this;
	}

	/**
	* Get the username color
	*
	* @return string
	*/
	public function get_colour()
	{
		return isset($this->data['user_colour']) ? (string) $this->data['user_colour'] : '';
	}

	/**
	* Set the username color
	*
	* @param string			$text	6-char HEX code without #
	* @return user_profile	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_colour($text)
	{
		$text = (string) $text;

		// Check invalid characters
		if (!preg_match('/([a-f0-9]{3}){1,2}\b/i', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['user_colour', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['user_colour'] = $text;

		return $this;
	}
}
