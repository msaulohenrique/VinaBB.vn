<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

use vinabb\web\entities\abs\bbcode_content;

/**
* Sub-entity for portal_article/article_text
*/
class article_text extends bbcode_content
{
	/** @var array $data */
	protected $data;

	/** @var bool $ignore_max_post_chars */
	protected $ignore_max_post_chars = true;

	/** @var string $prefix */
	protected $prefix = 'article_text';

	/**
	* Get article content for edit
	*
	* @return string
	*/
	public function get_text_for_edit()
	{
		return $this->get_for_edit($this->prefix);
	}

	/**
	* Get article content for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_text_for_display($censor = true)
	{
		return $this->get_for_display($this->prefix, $censor);
	}

	/**
	* Set article content
	*
	* @param string			$text	Article content
	* @return article_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text($text)
	{
		return $this->set($this->prefix, $text);
	}

	/**
	* Check if BBCode is enabled on the article content
	*
	* @return bool
	*/
	public function text_bbcode_enabled()
	{
		return $this->bbcode_enabled($this->prefix);
	}

	/**
	* Enable/Disable BBCode on the article content
	* This should be called before set_text(); text_enable_bbcode()->set_text()
	*
	* @param bool			$enable	true: enable; false: disable
	* @return article_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_bbcode($enable)
	{
		return $this->enable_bbcode($this->prefix, $enable);
	}

	/**
	* Check if URLs is enabled on the article content
	*
	* @return bool
	*/
	public function text_urls_enabled()
	{
		return $this->urls_enabled($this->prefix);
	}

	/**
	* Enable/Disable URLs on the article content
	* This should be called before set_text(); text_enable_urls()->set_text()
	*
	* @param bool			$enable	true: enable; false: disable
	* @return article_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_urls($enable)
	{
		return $this->enable_urls($this->prefix, $enable);
	}

	/**
	* Check if smilies are enabled on the article content
	*
	* @return bool
	*/
	public function text_smilies_enabled()
	{
		return $this->smilies_enabled($this->prefix);
	}

	/**
	* Enable/Disable smilies on the article content
	* This should be called before set_text(); text_enable_smilies()->set_text()
	*
	* @param bool			$enable	true: enable; false: disable
	* @return article_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_smilies($enable)
	{
		return $this->enable_smilies($this->prefix, $enable);
	}
}
