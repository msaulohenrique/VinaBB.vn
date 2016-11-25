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
	/** @var array */
	protected $data;

	/**
	* Is the auto-pruning enable?
	*
	* @return bool
	*/
	public function get_enable_prune()
	{
		return isset($this->data['enable_prune']) ? (bool) $this->data['enable_prune'] : false;
	}

	/**
	* Is the auto-pruning shadow topics enable?
	*
	* @return bool
	*/
	public function get_enable_shadow_prune()
	{
		return isset($this->data['enable_shadow_prune']) ? (bool) $this->data['enable_shadow_prune'] : false;
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
	* Get number of days between pruning times
	*
	* @return int
	*/
	public function get_prune_freq()
	{
		return isset($this->data['prune_freq']) ? (int) $this->data['prune_freq'] : 0;
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
	* Get number of age-days since the last view, then the topics will be removed
	*
	* @return int
	*/
	public function get_prune_viewed()
	{
		return isset($this->data['prune_viewed']) ? (int) $this->data['prune_viewed'] : 0;
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
	* Get number of days between pruning times of shadow topics
	*
	* @return int
	*/
	public function get_prune_shadow_freq()
	{
		return isset($this->data['prune_shadow_freq']) ? (int) $this->data['prune_shadow_freq'] : 1;
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
}
