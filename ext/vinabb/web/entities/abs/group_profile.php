<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\abs;

use vinabb\web\includes\constants;

/**
* Abstract entity for group_profile + group_desc
*/
abstract class group_profile extends group_desc
{
	/** @var array */
	protected $data;

	/** @var \vinabb\web\entities\helper\helper_interface */
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
	* Get the group avatar image
	*
	* @return string
	*/
	public function get_avatar()
	{
		return isset($this->data['group_avatar']) ? (string) $this->data['group_avatar'] : '';
	}

	/**
	* Set the group avatar image
	*
	* @param string			$text	Avatar image URL
	* @return group_profile	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_avatar($text)
	{
		$text = (string) $text;

		// Check the max length
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['group_avatar', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['group_avatar'] = $text;

		return $this;
	}

	/**
	* Get the group avatar type
	*
	* @return string
	*/
	public function get_avatar_type()
	{
		return isset($this->data['group_avatar_type']) ? (string) $this->data['group_avatar_type'] : '';
	}

	/**
	* Set the group avatar type
	*
	* @param int			$value	Avatar type
	* @return group_profile	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_avatar_type($value)
	{
		$value = (int) $value;

		if (!in_array($value, ['avatar_driver_gravatar', 'avatar_driver_local', 'avatar_driver_remote', 'avatar_driver_upload']))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('group_avatar_type');
		}

		// Set the value on our data array
		$this->data['group_avatar_type'] = $value;

		return $this;
	}

	/**
	* Get the group avatar width
	*
	* @return int
	*/
	public function get_avatar_width()
	{
		return isset($this->data['group_avatar_width']) ? (int) $this->data['group_avatar_width'] : 0;
	}

	/**
	* Get the group avatar height
	*
	* @return int
	*/
	public function get_avatar_height()
	{
		return isset($this->data['group_avatar_height']) ? (int) $this->data['group_avatar_height'] : 0;
	}

	/**
	* Get the group rank
	*
	* @return int
	*/
	public function get_rank()
	{
		return isset($this->data['group_rank']) ? (int) $this->data['group_rank'] : 0;
	}

	/**
	* Set the group rank
	*
	* @param int			$id		Rank ID
	* @return group_profile	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_rank($id)
	{
		$id = (int) $id;

		// Check existing rank
		if ($id && !$this->entity_helper->check_rank_id($id))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['group_rank', 'NOT_EXISTS']);
		}

		// Set the value on our data array
		$this->data['group_rank'] = $id;

		return $this;
	}

	/**
	* Get the group username color
	*
	* @return string
	*/
	public function get_colour()
	{
		return isset($this->data['group_colour']) ? (string) $this->data['group_colour'] : '';
	}

	/**
	* Set the group username color
	*
	* @param string			$text	6-char HEX code without #
	* @return group_profile	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_colour($text)
	{
		$text = (string) $text;

		// Check invalid characters
		if (!preg_match('/([a-f0-9]{3}){1,2}\b/i', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['group_colour', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['group_colour'] = $text;

		return $this;
	}
}
