<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\abs;

/**
* Abstract entity for forum/forum_desc and forum/forum_rules
*/
abstract class forum_desc_rules extends bbcode_content
{
	/**
	* Data for this abstract entity
	*
	* @var array
	*	...
	*		forum_desc
	*		forum_desc_uid
	*		forum_desc_bitfield
	*		forum_desc_options
	*		forum_rules
	*		forum_rules_uid
	*		forum_rules_bitfield
	*		forum_rules_options
	*	...
	*/
	protected $data;

	/** @var bool */
	protected $ignore_max_post_chars = false;

	/** @var string */
	protected $desc_prefix = 'forum_desc';

	/** @var string */
	protected $rules_prefix = 'forum_rules';

	/**
	* Get forum description for edit
	*
	* @return string
	*/
	public function get_desc_for_edit()
	{
		return $this->get_text_for_edit($this->desc_prefix);
	}

	/**
	* Get forum description for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_desc_for_display($censor = true)
	{
		return $this->get_text_for_display($this->desc_prefix, $censor);
	}

	/**
	* Set forum description
	*
	* @param string				$text	Forum description
	* @return forum_desc_rules	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc($text)
	{
		return $this->set_text($this->desc_prefix, $text);
	}

	/**
	* Check if BBCode is enabled on the forum description
	*
	* @return bool
	*/
	public function desc_bbcode_enabled()
	{
		return $this->bbcode_enabled($this->desc_prefix);
	}

	/**
	* Enable/Disable BBCode on the forum description
	* This should be called before set_desc(); desc_enable_bbcode()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_desc_rules	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_bbcode($enable)
	{
		return $this->enable_bbcode($this->desc_prefix, $enable);
	}

	/**
	* Check if URLs is enabled on the forum description
	*
	* @return bool
	*/
	public function desc_urls_enabled()
	{
		return $this->urls_enabled($this->desc_prefix);
	}

	/**
	* Enable/Disable URLs on the forum description
	* This should be called before set_desc(); desc_enable_urls()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_desc_rules	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_urls($enable)
	{
		return $this->enable_urls($this->desc_prefix, $enable);
	}

	/**
	* Check if smilies are enabled on the forum description
	*
	* @return bool
	*/
	public function desc_smilies_enabled()
	{
		return $this->smilies_enabled($this->desc_prefix);
	}

	/**
	* Enable/Disable smilies on the forum description
	* This should be called before set_desc(); desc_enable_smilies()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_desc_rules	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_smilies($enable)
	{
		return $this->enable_smilies($this->desc_prefix, $enable);
	}

	/**
	* Set BBCode options for the forum description
	*
	* @param int	$value		Value of the option
	* @param bool	$negate		Negate (Unset) option
	* @param bool	$reparse	Reparse the content after setting option
	*/
	protected function set_desc_options($value, $negate = false, $reparse = true)
	{
		$this->set_text_options($this->desc_prefix, $value, $negate, $reparse);
	}

	/**
	* Get forum rules for edit
	*
	* @return string
	*/
	public function get_rules_for_edit()
	{
		return $this->get_text_for_edit($this->rules_prefix);
	}

	/**
	* Get forum rules for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_rules_for_display($censor = true)
	{
		return $this->get_text_for_display($this->rules_prefix, $censor);
	}

	/**
	* Set forum rules
	*
	* @param string				$text	Forum rules
	* @return forum_desc_rules	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_rules($text)
	{
		return $this->set_text($this->rules_prefix, $text);
	}

	/**
	* Check if BBCode is enabled on the forum rules
	*
	* @return bool
	*/
	public function rules_bbcode_enabled()
	{
		return $this->bbcode_enabled($this->rules_prefix);
	}

	/**
	* Enable/Disable BBCode on the forum rules
	* This should be called before set_rules(); rules_enable_bbcode()->set_rules()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_desc_rules	$this	Object for chaining calls: load()->set()->save()
	*/
	public function rules_enable_bbcode($enable)
	{
		return $this->enable_bbcode($this->rules_prefix, $enable);
	}

	/**
	* Check if URLs is enabled on the forum rules
	*
	* @return bool
	*/
	public function rules_urls_enabled()
	{
		return $this->urls_enabled($this->rules_prefix);
	}

	/**
	* Enable/Disable URLs on the forum rules
	* This should be called before set_rules(); rules_enable_urls()->set_rules()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_desc_rules	$this	Object for chaining calls: load()->set()->save()
	*/
	public function rules_enable_urls($enable)
	{
		return $this->enable_urls($this->rules_prefix, $enable);
	}

	/**
	* Check if smilies are enabled on the forum rules
	*
	* @return bool
	*/
	public function rules_smilies_enabled()
	{
		return $this->smilies_enabled($this->rules_prefix);
	}

	/**
	* Enable/Disable smilies on the forum rules
	* This should be called before set_rules(); rules_enable_smilies()->set_rules()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_desc_rules	$this	Object for chaining calls: load()->set()->save()
	*/
	public function rules_enable_smilies($enable)
	{
		return $this->enable_smilies($this->rules_prefix, $enable);
	}

	/**
	* Set BBCode options for the forum rules
	*
	* @param int	$value		Value of the option
	* @param bool	$negate		Negate (Unset) option
	* @param bool	$reparse	Reparse the content after setting option
	*/
	protected function set_rules_options($value, $negate = false, $reparse = true)
	{
		$this->set_text_options($this->rules_prefix, $value, $negate, $reparse);
	}
}