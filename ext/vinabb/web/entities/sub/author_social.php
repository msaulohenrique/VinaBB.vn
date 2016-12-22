<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for bb_author/author_social
*/
class author_social
{
	/** @var array $data */
	protected $data;

	/**
	* Get the author's phpBB.com username
	*
	* @return string
	*/
	public function get_phpbb()
	{
		return isset($this->data['author_phpbb']) ? (string) $this->data['author_phpbb'] : '';
	}

	/**
	* Set the author's phpBB.com username
	*
	* @param string			$text	phpBB.com username
	* @return author_social	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_phpbb($text)
	{
		$text = (string) $text;

		// Check invalid characters
		if (!preg_match('#^([\w_]+)?$#', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_phpbb', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['author_phpbb'] = $text;

		return $this;
	}

	/**
	* Get the author's social page: GitHub
	*
	* @return string
	*/
	public function get_github()
	{
		return isset($this->data['author_github']) ? (string) $this->data['author_github'] : '';
	}

	/**
	* Set the author's social page: GitHub
	*
	* @param string			$text	GitHub username
	* @return author_social	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_github($text)
	{
		$text = (string) $text;

		// Check invalid characters
		if (!preg_match('#^([\w_]+)?$#', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_github', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['author_github'] = $text;

		return $this;
	}

	/**
	* Get the author's social page: Facebook
	*
	* @return string
	*/
	public function get_facebook()
	{
		return isset($this->data['author_facebook']) ? (string) $this->data['author_facebook'] : '';
	}

	/**
	* Set the author's social page: Facebook
	*
	* @param string			$text	Facebook username
	* @return author_social	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_facebook($text)
	{
		$text = (string) $text;

		// Check invalid characters
		if (!preg_match('#^([\w.]+)?$#', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_facebook', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['author_facebook'] = $text;

		return $this;
	}

	/**
	* Get the author's social page: Twitter
	*
	* @return string
	*/
	public function get_twitter()
	{
		return isset($this->data['author_twitter']) ? (string) $this->data['author_twitter'] : '';
	}

	/**
	* Set the author's social page: Twitter
	*
	* @param string			$text	Twitter username
	* @return author_social	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_twitter($text)
	{
		$text = (string) $text;

		// Check invalid characters
		if (!preg_match('#^([\w_]+)?$#', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_twitter', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['author_twitter'] = $text;

		return $this;
	}

	/**
	* Get the author's social page: Google+
	*
	* @return string
	*/
	public function get_google_plus()
	{
		return isset($this->data['author_google_plus']) ? (string) $this->data['author_google_plus'] : '';
	}

	/**
	* Set the author's social page: Google+
	*
	* @param string			$text	Google+ username
	* @return author_social	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_google_plus($text)
	{
		$text = (string) $text;

		// Check invalid characters
		if (!preg_match('#^([\w]+)?$#', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_google_plus', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['author_google_plus'] = $text;

		return $this;
	}

	/**
	* Get the author's social page: Skype
	*
	* @return string
	*/
	public function get_skype()
	{
		return isset($this->data['author_skype']) ? (string) $this->data['author_skype'] : '';
	}

	/**
	* Set the author's social page: Skype
	*
	* @param string			$text	Skype username
	* @return author_social	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_skype($text)
	{
		$text = (string) $text;

		// Check invalid characters
		if (!preg_match('#^([a-zA-Z][\w\.,\-_]+)?$#', $text))
		{
			throw new \vinabb\web\exceptions\unexpected_value(['author_skype', 'INVALID']);
		}

		// Set the value on our data array
		$this->data['author_skype'] = $text;

		return $this;
	}
}
