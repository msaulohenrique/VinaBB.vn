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
* Sub-entity for attachment/attachment_file
*/
class attachment_file
{
	/** @var array $data */
	protected $data;

	/**
	* Get attachment's filename on server
	*
	* @return string
	*/
	public function get_physical_filename()
	{
		return isset($this->data['physical_filename']) ? (string) $this->data['physical_filename'] : '';
	}

	/**
	* Set attachment's filename on server
	*
	* @param string				$text	Server filename
	* @return attachment_file	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_physical_filename($text)
	{
		$text = (string) $text;

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['physical_filename', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['physical_filename'] = $text;

		return $this;
	}

	/**
	* Get attachment's original filename
	*
	* @return string
	*/
	public function get_real_filename()
	{
		return isset($this->data['real_filename']) ? (string) $this->data['real_filename'] : '';
	}

	/**
	* Set attachment's original filename
	*
	* @param string				$text	Original filename
	* @return attachment_file	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_real_filename($text)
	{
		$text = (string) $text;

		// Check the max length
		if (utf8_strlen($text) > constants::MAX_CONFIG_NAME)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['real_filename', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['real_filename'] = $text;

		return $this;
	}

	/**
	* Get attachment's file extension
	*
	* @return string
	*/
	public function get_extension()
	{
		return isset($this->data['extension']) ? (string) $this->data['extension'] : '';
	}

	/**
	* Set attachment's file extension
	*
	* @param string				$text	File extension
	* @return attachment_file	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_extension($text)
	{
		$text = (string) $text;

		// Check the max length
		if (utf8_strlen($text) > 100)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['extension', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['extension'] = $text;

		return $this;
	}

	/**
	* Get attachment's MIME type
	*
	* @return string
	*/
	public function get_mimetype()
	{
		return isset($this->data['mimetype']) ? (string) $this->data['mimetype'] : '';
	}

	/**
	* Set attachment's MIME type
	*
	* @param string				$text	MIME type
	* @return attachment_file	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_mimetype($text)
	{
		$text = (string) $text;

		// Check the max length
		if (utf8_strlen($text) > 100)
		{
			throw new \vinabb\web\exceptions\unexpected_value(['mimetype', 'TOO_LONG']);
		}

		// Set the value on our data array
		$this->data['mimetype'] = $text;

		return $this;
	}

	/**
	* Get attachment's filesize
	*
	* @return int
	*/
	public function get_filesize()
	{
		return isset($this->data['filesize']) ? (int) $this->data['filesize'] : 0;
	}

	/**
	* Get attachment's uploaded time
	*
	* @return int
	*/
	public function get_filetime()
	{
		return isset($this->data['filetime']) ? (int) $this->data['filetime'] : 0;
	}

	/**
	* The attachment has a thumbnail?
	*
	* @return bool
	*/
	public function get_thumbnail()
	{
		return isset($this->data['thumbnail']) ? (bool) $this->data['thumbnail'] : false;
	}
}
