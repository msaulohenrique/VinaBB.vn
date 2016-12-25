<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for forum prune + forum_desc_rules
*/
class forum_prune extends forum_desc_rules
{
	/** @var array $data */
	protected $data;

	/**
	* Get the forum setting: Auto-pruning
	*
	* @return bool
	*/
	public function get_enable_prune()
	{
		return isset($this->data['enable_prune']) ? (bool) $this->data['enable_prune'] : false;
	}

	/**
	* Set the forum setting: Auto-pruning
	*
	* @param bool			$value	true: enable; false: disable
	* @return forum_prune	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_prune($value)
	{
		// Set the value on our data array
		$this->data['enable_prune'] = (bool) $value;

		return $this;
	}

	/**
	* Get the forum setting: Auto-pruning shadow topics
	*
	* @return bool
	*/
	public function get_enable_shadow_prune()
	{
		return isset($this->data['enable_shadow_prune']) ? (bool) $this->data['enable_shadow_prune'] : false;
	}

	/**
	* Set the forum setting: Auto-pruning shadow topics
	*
	* @param bool			$value	true: enable; false: disable
	* @return forum_prune	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_shadow_prune($value)
	{
		// Set the value on our data array
		$this->data['enable_shadow_prune'] = (bool) $value;

		return $this;
	}

	/**
	* Get number of age-days after the topics will be removed
	*
	* @return int
	*/
	public function get_prune_days()
	{
		return isset($this->data['prune_days']) ? (int) $this->data['prune_days'] : 0;
	}

	/**
	* Set number of age-days after the topics will be removed
	*
	* @param int			$value	Number of days
	* @return forum_prune	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_prune_days($value)
	{
		// Set the value on our data array
		$this->data['prune_days'] = (int) $value;

		return $this;
	}

	/**
	* Get number of days between pruning times
	*
	* @return int
	*/
	public function get_prune_freq()
	{
		return isset($this->data['prune_freq']) ? (int) $this->data['prune_freq'] : 0;
	}

	/**
	* Set number of days between pruning times
	*
	* @param int			$value	Number of days
	* @return forum_prune	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_prune_freq($value)
	{
		// Set the value on our data array
		$this->data['prune_freq'] = (int) $value;

		return $this;
	}

	/**
	* Get the beginning time of next pruning
	*
	* @return int
	*/
	public function get_prune_next()
	{
		return isset($this->data['prune_next']) ? (int) $this->data['prune_next'] : 0;
	}

	/**
	* Set the beginning time of next pruning
	*
	* @param int			$value	UNIX timestamp
	* @return forum_prune	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_prune_next($value)
	{
		// Set the value on our data array
		$this->data['prune_next'] = (int) $value;

		return $this;
	}

	/**
	* Get number of age-days since the last view, then the topics will be removed
	*
	* @return int
	*/
	public function get_prune_viewed()
	{
		return isset($this->data['prune_viewed']) ? (int) $this->data['prune_viewed'] : 0;
	}

	/**
	* Set number of age-days since the last view, then the topics will be removed
	*
	* @param int			$value	Number of days
	* @return forum_prune	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_prune_viewed($value)
	{
		// Set the value on our data array
		$this->data['prune_viewed'] = (int) $value;

		return $this;
	}

	/**
	* Get number of age-days after the shadow topics will be removed
	*
	* @return int
	*/
	public function get_prune_shadow_days()
	{
		return isset($this->data['prune_shadow_days']) ? (int) $this->data['prune_shadow_days'] : 7;
	}

	/**
	* Set number of age-days after the shadow topics will be removed
	*
	* @param int			$value	Number of days
	* @return forum_prune	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_prune_shadow_days($value)
	{
		// Set the value on our data array
		$this->data['prune_shadow_days'] = (int) $value;

		return $this;
	}

	/**
	* Get number of days between pruning times of shadow topics
	*
	* @return int
	*/
	public function get_prune_shadow_freq()
	{
		return isset($this->data['prune_shadow_freq']) ? (int) $this->data['prune_shadow_freq'] : 1;
	}

	/**
	* Set number of days between pruning times of shadow topics
	*
	* @param int			$value	Number of days
	* @return forum_prune	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_prune_shadow_freq($value)
	{
		// Set the value on our data array
		$this->data['prune_shadow_freq'] = (int) $value;

		return $this;
	}

	/**
	* Get the beginning time of next pruning shadow topics
	*
	* @return int
	*/
	public function get_prune_shadow_next()
	{
		return isset($this->data['prune_shadow_next']) ? (int) $this->data['prune_shadow_next'] : 0;
	}

	/**
	* Set the beginning time of next pruning shadow topics
	*
	* @param int			$value	UNIX timestamp
	* @return forum_prune	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_prune_shadow_next($value)
	{
		// Set the value on our data array
		$this->data['prune_shadow_next'] = (int) $value;

		return $this;
	}
}
