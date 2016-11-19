<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single forum rules
*/
interface forum_rules_interface extends forum_interface
{
	/**
	* Get forum rules for edit
	*
	* @return string
	*/
	public function get_rules_for_edit();

	/**
	* Get forum rules for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_rules_for_display($censor = true);

	/**
	* Set forum rules
	*
	* @param string				$text	Forum rules
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_rules($text);

	/**
	* Check if BBCode is enabled on the forum rules
	*
	* @return bool
	*/
	public function rules_bbcode_enabled();

	/**
	* Enable/Disable BBCode on the forum rules
	* This should be called before set_rules(); rules_enable_bbcode()->set_rules()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function rules_enable_bbcode($enable);

	/**
	* Check if URLs is enabled on the forum rules
	*
	* @return bool
	*/
	public function rules_urls_enabled();

	/**
	* Enable/Disable URLs on the forum rules
	* This should be called before set_rules(); rules_enable_urls()->set_rules()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function rules_enable_urls($enable);

	/**
	* Check if smilies are enabled on the forum rules
	*
	* @return bool
	*/
	public function rules_smilies_enabled();

	/**
	* Enable/Disable smilies on the forum rules
	* This should be called before set_rules(); rules_enable_smilies()->set_rules()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function rules_enable_smilies($enable);
}
