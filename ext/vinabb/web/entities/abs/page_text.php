<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\abs;

/**
* Abstract entity for page/page_text and page/page_text_vi
*/
abstract class page_text extends bbcode_content
{
	/**
	* Data for this abstract entity
	*
	* @var array
	*	...
	*		page_text
	*		page_text_uid
	*		page_text_bitfield
	*		page_text_options
	*		page_text_vi
	*		page_text_vi_uid
	*		page_text_vi_bitfield
	*		page_text_vi_options
	*	...
	*/
	protected $data;

	/** @var bool */
	protected $ignore_max_post_chars = true;

	/** @var string */
	protected $text_prefix = 'page_text';

	/** @var string */
	protected $text_vi_prefix = 'page_text_vi';

	/**
	* Get page content for edit
	*
	* @return string
	*/
	public function get_text_for_edit()
	{
		return parent::get_text_for_edit($this->text_prefix);
	}

	/**
	* Get page content for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_text_for_display($censor = true)
	{
		return parent::get_text_for_display($this->text_prefix, $censor);
	}

	/**
	* Set page content
	*
	* @param string		$text	Page content
	* @return page_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text($text)
	{
		return parent::set_text($this->text_prefix, $text);
	}

	/**
	* Check if BBCode is enabled on the page content
	*
	* @return bool
	*/
	public function text_bbcode_enabled()
	{
		return $this->bbcode_enabled($this->text_prefix);
	}

	/**
	* Enable/Disable BBCode on the page content
	* This should be called before set_text(); text_enable_bbcode()->set_text()
	*
	* @param bool		$enable	true: enable; false: disable
	* @return page_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_bbcode($enable)
	{
		return $this->enable_bbcode($this->text_prefix, $enable);
	}

	/**
	* Check if URLs is enabled on the page content
	*
	* @return bool
	*/
	public function text_urls_enabled()
	{
		return $this->urls_enabled($this->text_prefix);
	}

	/**
	* Enable/Disable URLs on the page content
	* This should be called before set_text(); text_enable_urls()->set_text()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return page_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_urls($enable)
	{
		return $this->enable_urls($this->text_prefix, $enable);
	}

	/**
	* Check if smilies are enabled on the page content
	*
	* @return bool
	*/
	public function text_smilies_enabled()
	{
		return $this->smilies_enabled($this->text_prefix);
	}

	/**
	* Enable/Disable smilies on the page content
	* This should be called before set_text(); text_enable_smilies()->set_text()
	*
	* @param bool		$enable	true: enable; false: disable
	* @return page_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_smilies($enable)
	{
		return $this->enable_smilies($this->text_prefix, $enable);
	}

	/**
	* Set BBCode options for the page content
	*
	* @param int	$value		Value of the option
	* @param bool	$negate		Negate (Unset) option
	* @param bool	$reparse	Reparse the content after setting option
	*/
	protected function set_text_options($value, $negate = false, $reparse = true)
	{
		parent::set_text_options($this->text_prefix, $value, $negate, $reparse);
	}

	/**
	* Get Vietnamese page content for edit
	*
	* @return string
	*/
	public function get_text_vi_for_edit()
	{
		return parent::get_text_for_edit($this->text_vi_prefix);
	}

	/**
	* Get Vietnamese page content for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_text_vi_for_display($censor = true)
	{
		return parent::get_text_for_display($this->text_vi_prefix, $censor);
	}

	/**
	* Set Vietnamese page content
	*
	* @param string		$text	Vietnamese page content
	* @return page_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text_vi($text)
	{
		return parent::set_text($this->text_vi_prefix, $text);
	}

	/**
	* Check if BBCode is enabled on the Vietnamese page content
	*
	* @return bool
	*/
	public function text_vi_bbcode_enabled()
	{
		return $this->bbcode_enabled($this->text_vi_prefix);
	}

	/**
	* Enable/Disable BBCode on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_enable_bbcode()->set_text_vi()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return page_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_enable_bbcode($enable)
	{
		return $this->enable_bbcode($this->text_vi_prefix, $enable);
	}

	/**
	* Check if URLs is enabled on the Vietnamese page content
	*
	* @return bool
	*/
	public function text_vi_urls_enabled()
	{
		return $this->urls_enabled($this->text_vi_prefix);
	}

	/**
	* Enable/Disable URLs on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_enable_urls()->set_text_vi()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return page_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_enable_urls($enable)
	{
		return $this->enable_urls($this->text_vi_prefix, $enable);
	}

	/**
	* Check if smilies are enabled on the Vietnamese page content
	*
	* @return bool
	*/
	public function text_vi_smilies_enabled()
	{
		return $this->smilies_enabled($this->text_vi_prefix);
	}

	/**
	* Enable/Disable smilies on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_enable_smilies()->set_text_vi()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return page_text	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_enable_smilies($enable)
	{
		return $this->enable_smilies($this->text_vi_prefix, $enable);
	}

	/**
	* Set BBCode options for the Vietnamese page content
	*
	* @param int	$value		Value of the option
	* @param bool	$negate		Negate (Unset) option
	* @param bool	$reparse	Reparse the content after setting option
	*/
	protected function set_text_vi_options($value, $negate = false, $reparse = true)
	{
		parent::set_text_options($this->text_vi_prefix, $value, $negate, $reparse);
	}
}