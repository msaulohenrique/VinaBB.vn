<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\abs;

/**
* Abstract entity for portal_comment/comment_text
*/
abstract class comment_text extends bbcode_content
{
	/** @var array */
	protected $data;

	/** @var bool */
	protected $ignore_max_post_chars = false;

	/** @var string */
	protected $prefix = 'comment_text';

	/**
	* Get comment content for edit
	*
	* @return string
	*/
	public function get_text_for_edit()
	{
		return $this->get_for_edit($this->prefix);
	}

	/**
	* Get comment content for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_text_for_display($censor = true)
	{
		return $this->get_for_display($this->prefix, $censor);
	}

	/**
	* Set comment content
	*
	* @param string			$text	Comment content
	* @return comment_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text($text)
	{
		return $this->set($this->prefix, $text);
	}

	/**
	* Check if BBCode is enabled on the comment content
	*
	* @return bool
	*/
	public function text_bbcode_enabled()
	{
		return $this->bbcode_enabled($this->prefix);
	}

	/**
	* Enable/Disable BBCode on the comment content
	* This should be called before set_text(); text_enable_bbcode()->set_text()
	*
	* @param bool			$enable	true: enable; false: disable
	* @return comment_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_bbcode($enable)
	{
		return $this->enable_bbcode($this->prefix, $enable);
	}

	/**
	* Check if URLs is enabled on the comment content
	*
	* @return bool
	*/
	public function text_urls_enabled()
	{
		return $this->urls_enabled($this->prefix);
	}

	/**
	* Enable/Disable URLs on the comment content
	* This should be called before set_text(); text_enable_urls()->set_text()
	*
	* @param bool			$enable	true: enable; false: disable
	* @return comment_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_urls($enable)
	{
		return $this->enable_urls($this->prefix, $enable);
	}

	/**
	* Check if smilies are enabled on the comment content
	*
	* @return bool
	*/
	public function text_smilies_enabled()
	{
		return $this->smilies_enabled($this->prefix);
	}

	/**
	* Enable/Disable smilies on the comment content
	* This should be called before set_text(); text_enable_smilies()->set_text()
	*
	* @param bool			$enable	true: enable; false: disable
	* @return comment_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_smilies($enable)
	{
		return $this->enable_smilies($this->prefix, $enable);
	}
}
