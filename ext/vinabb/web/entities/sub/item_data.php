<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for item_data + item_ext + item_style + item_lang_tool + item_desc
*/
class item_data extends item_ext
{
	/** @var array $data */
	protected $data;

	/**
	* Get the item price
	*
	* @return int
	*/
	public function get_price()
	{
		return isset($this->data['item_price']) ? (int) $this->data['item_price'] : 0;
	}

	/**
	* Set the item price
	*
	* @param int			$value	Item price
	* @return item_data	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_price($value)
	{
		$this->data['item_price'] = (int) $value;

		return $this;
	}

	/**
	* Get the item URL
	*
	* @return string
	*/
	public function get_url()
	{
		return isset($this->data['item_url']) ? (string) htmlspecialchars_decode($this->data['item_url']) : '';
	}

	/**
	* Set the item URL
	*
	* @param string		$text	Item URL
	* @return item_data	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_url($text)
	{
		$text = (string) $text;

		// Checking for valid URL
		if ($text != '' && filter_var($text, FILTER_VALIDATE_URL) === false)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_url', 'INVALID_URL']);
		}

		// Set the value on our data array
		$this->data['item_url'] = $text;

		return $this;
	}

	/**
	* Get the item GitHub URL
	*
	* @return string
	*/
	public function get_github()
	{
		return isset($this->data['item_github']) ? (string) htmlspecialchars_decode($this->data['item_github']) : '';
	}

	/**
	* Set the item GitHub URL
	*
	* @param string		$text	Item GitHub URL
	* @return item_data	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_github($text)
	{
		$text = (string) $text;

		// Checking for valid URL
		if ($text != '' && filter_var($text, FILTER_VALIDATE_URL) === false)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['item_github', 'INVALID_URL']);
		}

		// Set the value on our data array
		$this->data['item_github'] = $text;

		return $this;
	}

	/**
	* Get item display setting in template
	*
	* @return bool
	*/
	public function get_enable()
	{
		return isset($this->data['item_enable']) ? (bool) $this->data['item_enable'] : true;
	}

	/**
	* Get the time of adding item
	*
	* @return int
	*/
	public function get_added()
	{
		return isset($this->data['item_added']) ? (int) $this->data['item_added'] : 0;
	}

	/**
	* Set the time of adding item
	*
	* @return item_data $this Object for chaining calls: load()->set()->save()
	*/
	public function set_added()
	{
		if (!isset($this->data['item_added']))
		{
			$this->data['item_added'] = time();
		}

		return $this;
	}

	/**
	* Get the last updated time of item
	*
	* @return int
	*/
	public function get_updated()
	{
		return isset($this->data['item_updated']) ? (int) $this->data['item_updated'] : 0;
	}
}
