<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Entity for a single forum description
*/
abstract class forum_desc extends bbcode_content
{
	/**
	* Data for this sub-entity
	*
	* @var array
	*	...
	*		forum_desc
	*		forum_desc_uid
	*		forum_desc_bitfield
	*		forum_desc_options
	*	...
	*/
	public $data;

	/** @var string */
	public $prefix = 'forum_desc';

	/** @var bool */
	public $ignore_max_post_chars = false;

	/**
	* Get forum description for edit
	*
	* @return string
	*/
	public function get_desc_for_edit()
	{
		return $this->get_text_for_edit();
	}

	/**
	* Get forum description for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_desc_for_display($censor = true)
	{
		return $this->get_text_for_display($censor);
	}

	/**
	* Set forum description
	*
	* @param string				$text	Forum description
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc($text)
	{
		return $this->set_text($text);
	}

	/**
	* Check if BBCode is enabled on the forum description
	*
	* @return bool
	*/
	public function desc_bbcode_enabled()
	{
		return $this->bbcode_enabled();
	}

	/**
	* Enable/Disable BBCode on the forum description
	* This should be called before set_desc(); desc_enable_bbcode()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_bbcode($enable)
	{
		return $this->enable_bbcode($enable);
	}

	/**
	* Check if URLs is enabled on the forum description
	*
	* @return bool
	*/
	public function desc_urls_enabled()
	{
		return $this->urls_enabled();
	}

	/**
	* Enable/Disable URLs on the forum description
	* This should be called before set_desc(); desc_enable_urls()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_urls($enable)
	{
		return $this->enable_urls($enable);
	}

	/**
	* Check if smilies are enabled on the forum description
	*
	* @return bool
	*/
	public function desc_smilies_enabled()
	{
		return $this->smilies_enabled();
	}

	/**
	* Enable/Disable smilies on the forum description
	* This should be called before set_desc(); desc_enable_smilies()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_smilies($enable)
	{
		return $this->enable_smilies($enable);
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
		return $this->set_text_options($value, $negate, $reparse);
	}
}
