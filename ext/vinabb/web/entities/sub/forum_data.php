<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for forum_data + forum_options + forum_last_post + forum_prune + forum_desc_rules
*/
class forum_data extends forum_options
{
	/** @var array $data */
	protected $data;

	/**
	* Get the forum type
	*
	* @return int
	*/
	public function get_type()
	{
		return isset($this->data['forum_type']) ? (int) $this->data['forum_type'] : FORUM_CAT;
	}

	/**
	* Set the forum type
	*
	* @param int			$value	Forum type
	* @return forum_data	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_type($value)
	{
		$value = (int) $value;

		if (!in_array($value, [FORUM_CAT, FORUM_POST, FORUM_LINK]))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('forum_type');
		}

		// Set the value on our data array
		$this->data['forum_type'] = $value;

		return $this;
	}

	/**
	* Get the forum status
	*
	* @return int
	*/
	public function get_status()
	{
		return isset($this->data['forum_status']) ? (int) $this->data['forum_status'] : ITEM_UNLOCKED;
	}

	/**
	* Set the forum status
	*
	* @param int			$value	Forum status
	* @return forum_data	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_status($value)
	{
		$value = (int) $value;

		if (!in_array($value, [ITEM_UNLOCKED, ITEM_LOCKED]))
		{
			throw new \vinabb\web\exceptions\out_of_bounds('forum_status');
		}

		// Set the value on our data array
		$this->data['forum_status'] = $value;

		return $this;
	}

	/**
	* Get the number of topics per page in this forum
	*
	* @return int
	*/
	public function get_topics_per_page()
	{
		return isset($this->data['forum_topics_per_page']) ? (int) $this->data['forum_topics_per_page'] : 0;
	}

	/**
	* Set the number of topics per page in this forum
	*
	* @param int			$value	Number of topics per page
	* @return forum_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_topics_per_page($value)
	{
		// Set the value on our data array
		$this->data['forum_topics_per_page'] = (int) $value;

		return $this;
	}

	/**
	* Get the number of approved topics
	*
	* @return int
	*/
	public function get_topics_approved()
	{
		return isset($this->data['forum_topics_approved']) ? (int) $this->data['forum_topics_approved'] : 0;
	}

	/**
	* Set the number of approved topics
	*
	* @param int			$value	Number of approved topics
	* @return forum_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_topics_approved($value)
	{
		// Set the value on our data array
		$this->data['forum_topics_approved'] = (int) $value;

		return $this;
	}

	/**
	* Get the number of disapproved topics
	*
	* @return int
	*/
	public function get_topics_unapproved()
	{
		return isset($this->data['forum_topics_unapproved']) ? (int) $this->data['forum_topics_unapproved'] : 0;
	}

	/**
	* Set he number of disapproved topics
	*
	* @param int			$value	Number of disapproved topics
	* @return forum_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_topics_unapproved($value)
	{
		// Set the value on our data array
		$this->data['forum_topics_unapproved'] = (int) $value;

		return $this;
	}

	/**
	* Get the number of soft-deleted topics
	*
	* @return int
	*/
	public function get_topics_softdeleted()
	{
		return isset($this->data['forum_topics_softdeleted']) ? (int) $this->data['forum_topics_softdeleted'] : 0;
	}

	/**
	* Set the number of soft-deleted topics
	*
	* @param int			$value	Number of soft-deleted topics
	* @return forum_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_topics_softdeleted($value)
	{
		// Set the value on our data array
		$this->data['forum_topics_softdeleted'] = (int) $value;

		return $this;
	}

	/**
	* Get the number of approved posts
	*
	* @return int
	*/
	public function get_posts_approved()
	{
		return isset($this->data['forum_posts_approved']) ? (int) $this->data['forum_posts_approved'] : 0;
	}

	/**
	* Set the number of approved posts
	*
	* @param int			$value	Number of approved posts
	* @return forum_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_posts_approved($value)
	{
		// Set the value on our data array
		$this->data['forum_posts_approved'] = (int) $value;

		return $this;
	}

	/**
	* Get the number of disapproved posts
	*
	* @return int
	*/
	public function get_posts_unapproved()
	{
		return isset($this->data['forum_posts_unapproved']) ? (int) $this->data['forum_posts_unapproved'] : 0;
	}

	/**
	* Set the number of disapproved posts
	*
	* @param int			$value	Number of disapproved posts
	* @return forum_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_posts_unapproved($value)
	{
		// Set the value on our data array
		$this->data['forum_posts_unapproved'] = (int) $value;

		return $this;
	}

	/**
	* Get the number of soft-deleted posts
	*
	* @return int
	*/
	public function get_posts_softdeleted()
	{
		return isset($this->data['forum_posts_softdeleted']) ? (int) $this->data['forum_posts_softdeleted'] : 0;
	}

	/**
	* Set the number of soft-deleted posts
	*
	* @param int			$value	Number of soft-deleted posts
	* @return forum_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_posts_softdeleted($value)
	{
		// Set the value on our data array
		$this->data['forum_posts_softdeleted'] = (int) $value;

		return $this;
	}
}
