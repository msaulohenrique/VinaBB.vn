<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single forum description
*/
interface forum_desc_interface extends forum_interface
{
	/**
	* Get forum description for edit
	*
	* @return string
	*/
	public function get_desc_for_edit();

	/**
	* Get forum description for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_desc_for_display($censor = true);

	/**
	* Set forum description
	*
	* @param string				$text	Forum description
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc($text);

	/**
	* Check if BBCode is enabled on the forum description
	*
	* @return bool
	*/
	public function desc_bbcode_enabled();

	/**
	* Enable/Disable BBCode on the forum description
	* This should be called before set_desc(); desc_enable_bbcode()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_bbcode($enable);

	/**
	* Check if URLs is enabled on the forum description
	*
	* @return bool
	*/
	public function desc_urls_enabled();

	/**
	* Enable/Disable URLs on the forum description
	* This should be called before set_desc(); desc_enable_urls()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_urls($enable);

	/**
	* Check if smilies are enabled on the forum description
	*
	* @return bool
	*/
	public function desc_smilies_enabled();

	/**
	* Enable/Disable smilies on the forum description
	* This should be called before set_desc(); desc_enable_smilies()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_smilies($enable);
}
