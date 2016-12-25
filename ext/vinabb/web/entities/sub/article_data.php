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
* Sub-entity for article_data + article_text
*/
class article_data extends article_text
{
	/** @var array $data */
	protected $data;

	/**
	* Get the article description
	*
	* @return string
	*/
	public function get_desc()
	{
		return isset($this->data['article_desc']) ? (string) $this->data['article_desc'] : '';
	}

	/**
	* Set the article description
	*
	* @param string			$text	Article description
	* @return article_data	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_desc($text)
	{
		$text = (string) $text;

		// This is a required field
		if ($text == '')
		{
			throw new \vinabb\web\exceptions\unexpected_value(['article_desc', 'EMPTY']);
		}

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_PORTAL_ARTICLE_DESC)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['article_desc', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['article_desc'] = $text;

		return $this;
	}

	/**
	* Get article display setting
	*
	* @return bool
	*/
	public function get_enable()
	{
		return isset($this->data['article_enable']) ? (bool) $this->data['article_enable'] : true;
	}

	/**
	* Set article display setting
	*
	* @param bool			$value	true: enable; false: disable
	* @return article_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable($value)
	{
		// Set the value on our data array
		$this->data['article_enable'] = (bool) $value;

		return $this;
	}

	/**
	* Get the article views
	*
	* @return int
	*/
	public function get_views()
	{
		return isset($this->data['article_views']) ? (int) $this->data['article_views'] : 0;
	}

	/**
	* Get the article time
	*
	* @return int
	*/
	public function get_time()
	{
		return isset($this->data['article_time']) ? (int) $this->data['article_time'] : 0;
	}

	/**
	* Set the article time
	*
	* @return article_data $this Object for chaining calls: load()->set()->save()
	*/
	public function set_time()
	{
		// Set the value on our data array
		$this->data['article_time'] = time();

		return $this;
	}
}
