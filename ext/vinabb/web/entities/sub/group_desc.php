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
* Sub-entity for group/group_desc
*/
class group_desc extends bbcode_content
{
	/** @var array */
	protected $data;

	/** @var bool */
	protected $ignore_max_post_chars = false;

	/** @var string */
	protected $desc_prefix = 'group_desc';

	/**
	* Get group description for edit
	*
	* @return string
	*/
	public function get_desc_for_edit()
	{
		return $this->get_for_edit($this->desc_prefix);
	}

	/**
	* Get group description for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_desc_for_display($censor = true)
	{
		return $this->get_for_display($this->desc_prefix, $censor);
	}

	/**
	* Set group description
	*
	* @param string			$text	Group description
	* @return group_desc	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc($text)
	{
		return $this->set($this->desc_prefix, $text);
	}

	/**
	* Check if BBCode is enabled on the group description
	*
	* @return bool
	*/
	public function desc_bbcode_enabled()
	{
		return $this->bbcode_enabled($this->desc_prefix);
	}

	/**
	* Enable/Disable BBCode on the group description
	* This should be called before set_desc(); desc_enable_bbcode()->set_desc()
	*
	* @param bool			$enable	true: enable; false: disable
	* @return group_desc	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_bbcode($enable)
	{
		return $this->enable_bbcode($this->desc_prefix, $enable);
	}

	/**
	* Check if URLs is enabled on the group description
	*
	* @return bool
	*/
	public function desc_urls_enabled()
	{
		return $this->urls_enabled($this->desc_prefix);
	}

	/**
	* Enable/Disable URLs on the group description
	* This should be called before set_desc(); desc_enable_urls()->set_desc()
	*
	* @param bool			$enable	true: enable; false: disable
	* @return group_desc	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_urls($enable)
	{
		return $this->enable_urls($this->desc_prefix, $enable);
	}

	/**
	* Check if smilies are enabled on the group description
	*
	* @return bool
	*/
	public function desc_smilies_enabled()
	{
		return $this->smilies_enabled($this->desc_prefix);
	}

	/**
	* Enable/Disable smilies on the group description
	* This should be called before set_desc(); desc_enable_smilies()->set_desc()
	*
	* @param bool			$enable	true: enable; false: disable
	* @return group_desc	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_smilies($enable)
	{
		return $this->enable_smilies($this->desc_prefix, $enable);
	}
}
