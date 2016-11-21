<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single comment
*/
interface portal_comment_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int						$id		Comment ID
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id);

	/**
	* Import data for an entity
	*
	* Used when the data is already loaded externally.
	* Any existing data on this entity is over-written.
	* All data is validated and an exception is thrown if any data is invalid.
	*
	* @param array						$data	Data array from the database
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return portal_comment_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return portal_comment_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the comment_id
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the user ID
	*
	* @return int
	*/
	public function get_user_id();

	/**
	* Set the user ID
	*
	* @param int						$id		User ID
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_cat_id($id);

	/**
	* Get the article ID
	*
	* @return int
	*/
	public function get_article_id();

	/**
	* Set the article ID
	*
	* @param int						$id		Article ID
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_article_id($id);

	/**
	* Get comment content for edit
	*
	* @return string
	*/
	public function get_text_for_edit();

	/**
	* Get comment content for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_text_for_display($censor = true);

	/**
	* Set comment content
	*
	* @param string						$text	Comment content
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text($text);

	/**
	* Check if BBCode is enabled on the comment content
	*
	* @return bool
	*/
	public function text_bbcode_enabled();

	/**
	* Enable/Disable BBCode on the comment content
	* This should be called before set_text(); text_enable_bbcode()->set_text()
	*
	* @param bool						$enable	true: enable; false: disable
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_bbcode($enable);

	/**
	* Check if URLs is enabled on the comment content
	*
	* @return bool
	*/
	public function text_urls_enabled();

	/**
	* Enable/Disable URLs on the comment content
	* This should be called before set_text(); text_enable_urls()->set_text()
	*
	* @param bool						$enable	true: enable; false: disable
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_urls($enable);

	/**
	* Check if smilies are enabled on the comment content
	*
	* @return bool
	*/
	public function text_smilies_enabled();

	/**
	* Enable/Disable smilies on the comment content
	* This should be called before set_text(); text_enable_smilies()->set_text()
	*
	* @param bool						$enable	true: enable; false: disable
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_smilies($enable);

	/**
	* Get comment pending status
	*
	* @return bool
	*/
	public function get_pending();

	/**
	* Set comment pending status
	*
	* @param bool						$value	Pending status
	* @return portal_comment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_pending($value);

	/**
	* Get the comment time
	*
	* @return int
	*/
	public function get_time();

	/**
	* Set the comment time
	*
	* @return portal_comment_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function set_time();
}
