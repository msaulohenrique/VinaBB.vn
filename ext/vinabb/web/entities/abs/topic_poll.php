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
* Abstract entity for a single poll
*/
abstract class topic_poll
{
	/**
	* Data for this abstract entity
	*
	* @var array
	*	...
	*		poll_title
	*		poll_start
	*		poll_length
	*		poll_max_options
	*		poll_last_vote
	*		poll_vote_change
	*	...
	*/
	protected $data;

	/**
	* Get the poll title
	*
	* @return string
	*/
	public function get_poll_title()
	{
		return isset($this->data['poll_title']) ? (string) $this->data['poll_title'] : '';
	}

	/**
	* Set the poll title
	*
	* @param string			$text	Poll title
	* @return topic_poll	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_poll_title($text)
	{
		$text = (string) $text;

		// Check the max length
		if (truncate_string($text, constants::MAX_CONFIG_NAME) != $text)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['poll_title', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['poll_title'] = $text;

		return $this;
	}

	/**
	* Get the poll's staring time
	*
	* @return int
	*/
	public function get_poll_start()
	{
		return isset($this->data['poll_start']) ? (int) $this->data['poll_start'] : 0;
	}

	/**
	* Get the poll's ending time
	*
	* @return int
	*/
	public function get_poll_length()
	{
		return isset($this->data['poll_length']) ? (int) $this->data['poll_length'] : 0;
	}

	/**
	* Get the maximum options of poll
	*
	* @return int
	*/
	public function get_poll_max_options()
	{
		return isset($this->data['poll_max_options']) ? (int) $this->data['poll_max_options'] : 0;
	}

	/**
	* Get the poll's last voting time
	*
	* @return int
	*/
	public function get_poll_last_vote()
	{
		return isset($this->data['poll_last_vote']) ? (int) $this->data['poll_last_vote'] : 0;
	}

	/**
	* Does the poll allow to change voted options?
	*
	* @return bool
	*/
	public function get_poll_vote_change()
	{
		return isset($this->data['poll_vote_change']) ? (bool) $this->data['poll_vote_change'] : false;
	}
}
