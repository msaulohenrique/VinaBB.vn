<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\abs;

/**
* Abstract entity for bb_item/item_desc and bb_item/item_desc_vi
*/
abstract class item_desc extends bbcode_content
{
	/** @var array */
	protected $data;

	/** @var bool */
	protected $ignore_max_post_chars = true;

	/** @var string */
	protected $desc_prefix = 'item_desc';

	/** @var string */
	protected $desc_vi_prefix = 'item_desc_vi';

	/**
	* Get item description for edit
	*
	* @return string
	*/
	public function get_desc_for_edit()
	{
		return $this->get_for_edit($this->desc_prefix);
	}

	/**
	* Get item description for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_desc_for_display($censor = true)
	{
		return $this->get_for_display($this->desc_prefix, $censor);
	}

	/**
	* Set item description
	*
	* @param string		$text	Item description
	* @return item_desc	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc($text)
	{
		return $this->set($this->desc_prefix, $text);
	}

	/**
	* Check if BBCode is enabled on the item description
	*
	* @return bool
	*/
	public function desc_bbcode_enabled()
	{
		return $this->bbcode_enabled($this->desc_prefix);
	}

	/**
	* Enable/Disable BBCode on the item description
	* This should be called before set_desc(); desc_enable_bbcode()->set_desc()
	*
	* @param bool		$enable	true: enable; false: disable
	* @return item_desc	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_bbcode($enable)
	{
		return $this->enable_bbcode($this->desc_prefix, $enable);
	}

	/**
	* Check if URLs is enabled on the item description
	*
	* @return bool
	*/
	public function desc_urls_enabled()
	{
		return $this->urls_enabled($this->desc_prefix);
	}

	/**
	* Enable/Disable URLs on the item description
	* This should be called before set_desc(); desc_enable_urls()->set_desc()
	*
	* @param bool		$enable	true: enable; false: disable
	* @return item_desc	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_urls($enable)
	{
		return $this->enable_urls($this->desc_prefix, $enable);
	}

	/**
	* Check if smilies are enabled on the item description
	*
	* @return bool
	*/
	public function desc_smilies_enabled()
	{
		return $this->smilies_enabled($this->desc_prefix);
	}

	/**
	* Enable/Disable smilies on the item description
	* This should be called before set_desc(); desc_enable_smilies()->set_desc()
	*
	* @param bool		$enable	true: enable; false: disable
	* @return item_desc	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_smilies($enable)
	{
		return $this->enable_smilies($this->desc_prefix, $enable);
	}

	/**
	* Get Vietnamese item description for edit
	*
	* @return string
	*/
	public function get_desc_vi_for_edit()
	{
		return $this->get_for_edit($this->desc_vi_prefix);
	}

	/**
	* Get Vietnamese item description for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_desc_vi_for_display($censor = true)
	{
		return $this->get_for_display($this->desc_vi_prefix, $censor);
	}

	/**
	* Set Vietnamese item description
	*
	* @param string		$text	Vietnamese item description
	* @return item_desc	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc_vi($text)
	{
		return $this->set($this->desc_vi_prefix, $text);
	}

	/**
	* Check if BBCode is enabled on the Vietnamese item description
	*
	* @return bool
	*/
	public function desc_vi_bbcode_enabled()
	{
		return $this->bbcode_enabled($this->desc_vi_prefix);
	}

	/**
	* Enable/Disable BBCode on the Vietnamese item description
	* This should be called before set_desc_vi(); text_vi_enable_bbcode()->set_desc_vi()
	*
	* @param bool		$enable	true: enable; false: disable
	* @return item_desc	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_vi_enable_bbcode($enable)
	{
		return $this->enable_bbcode($this->desc_vi_prefix, $enable);
	}

	/**
	* Check if URLs is enabled on the Vietnamese item description
	*
	* @return bool
	*/
	public function desc_vi_urls_enabled()
	{
		return $this->urls_enabled($this->desc_vi_prefix);
	}

	/**
	* Enable/Disable URLs on the Vietnamese item description
	* This should be called before set_desc_vi(); text_vi_enable_urls()->set_desc_vi()
	*
	* @param bool		$enable	true: enable; false: disable
	* @return item_desc	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_vi_enable_urls($enable)
	{
		return $this->enable_urls($this->desc_vi_prefix, $enable);
	}

	/**
	* Check if smilies are enabled on the Vietnamese item description
	*
	* @return bool
	*/
	public function desc_vi_smilies_enabled()
	{
		return $this->smilies_enabled($this->desc_vi_prefix);
	}

	/**
	* Enable/Disable smilies on the Vietnamese item description
	* This should be called before set_desc_vi(); text_vi_enable_smilies()->set_desc_vi()
	*
	* @param bool		$enable	true: enable; false: disable
	* @return item_desc	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_vi_enable_smilies($enable)
	{
		return $this->enable_smilies($this->desc_vi_prefix, $enable);
	}
}
