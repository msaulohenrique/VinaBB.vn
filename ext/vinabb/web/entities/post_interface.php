<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single post
*/
interface post_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Forum ID
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @param array				$data	Data array from the database
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\invalid_argument
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return post_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return post_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the post ID
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the forum ID
	*
	* @return int
	*/
	public function get_forum_id();

	/**
	* Set the forum ID
	*
	* @param int				$id		Forum ID
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_forum_id($id);

	/**
	* Get the topic ID
	*
	* @return int
	*/
	public function get_topic_id();

	/**
	* Set the topic ID
	*
	* @param int				$id		Topic ID
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_topic_id($id);

	/**
	* Get the post icon
	*
	* @return int
	*/
	public function get_icon_id();

	/**
	* Set the post icon
	*
	* @param int				$id		Icon ID
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_icon_id($id);

	/**
	* Get the post subject
	*
	* @return string
	*/
	public function get_subject();

	/**
	* Set the post subject
	*
	* @param string				$text	Post subject
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_subject($text);

	/**
	* Get the SEO post subject
	*
	* @return string
	*/
	public function get_subject_seo();

	/**
	* Set the SEO post subject
	*
	* @param string				$text	SEO post subject
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_subject_seo($text);

	/**
	* Get the poster ID
	*
	* @return int
	*/
	public function get_poster_id();

	/**
	* Set the poster ID
	*
	* @param int				$id		User ID
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_poster_id($id);

	/**
	* Get the poster IP
	*
	* @return string
	*/
	public function get_poster_ip();

	/**
	* Set the poster IP
	*
	* @param string				$text	User IP
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_poster_ip($text);

	/**
	* Get the guest poster username
	*
	* @return string
	*/
	public function get_username();

	/**
	* Set the guest poster username
	*
	* @param string				$text	Username
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_username($text);

	/**
	* Get the post time
	*
	* @return int
	*/
	public function get_time();

	/**
	* Set the post time
	*
	* @return post_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function set_time();

	/**
	* Get the post visibility
	*
	* @return int
	*/
	public function get_visibility();

	/**
	* Set the post visibility
	*
	* @param int				$value	Visibility value
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_visibility($value);

	/**
	* Does the post have attachments?
	*
	* @return bool
	*/
	public function get_attachment();

	/**
	* Set the post have or have not attachments
	*
	* @param bool				$value	true: yes; false: no
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_attachment($value);

	/**
	* Does the post have open reports?
	*
	* @return bool
	*/
	public function get_reported();

	/**
	* Set the post have or have not open reports
	*
	* @param bool				$value	true: yes; false: no
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_reported($value);

	/**
	* Get the post option: Attach signature
	*
	* @return bool
	*/
	public function get_enable_sig();

	/**
	* Set the post option: Attach signature
	*
	* @param bool				$value	true: enable; false: disable
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_sig($value);

	/**
	* Get the post option: Count the number of user posts
	*
	* @return bool
	*/
	public function get_postcount();

	/**
	* Set the post option: Count the number of user posts
	*
	* @param bool				$value	true: yes; false: no
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_postcount($value);

	/**
	* Get the post checksum
	*
	* @return string
	*/
	public function get_checksum();

	/**
	* Set the post checksum
	*
	* @param string				$text	Checksum
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_checksum($text);

	/**
	* Get the time of editing post
	*
	* @return int
	*/
	public function get_edit_time();

	/**
	* Set the time of editing post
	*
	* @param int				$value	UNIX timestamp
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_edit_time($value);

	/**
	* Get the edited reason
	*
	* @return string
	*/
	public function get_edit_reason();

	/**
	* Set the edited reason
	*
	* @param string				$text	Reason
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_edit_reason($text);

	/**
	* Get the user edited post
	*
	* @return int
	*/
	public function get_edit_user();

	/**
	* Set the user edited post
	*
	* @param int				$id		User ID
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_edit_user($id);

	/**
	* Get number of times of editing post
	*
	* @return int
	*/
	public function get_edit_count();

	/**
	* Set number of times of editing post
	*
	* @param int				$value	Number of times
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_edit_count($value);

	/**
	* Is the post locked?
	*
	* @return bool
	*/
	public function get_edit_locked();

	/**
	* Set the post locked or unlocked
	*
	* @param bool				$value	true: locked; false: unlocked
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_edit_locked($value);

	/**
	* Get the time of deleting post
	*
	* @return int
	*/
	public function get_delete_time();

	/**
	* Set the time of deleting post
	*
	* @param int				$value	UNIX timestamp
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_delete_time($value);

	/**
	* Get the deleted reason
	*
	* @return string
	*/
	public function get_delete_reason();

	/**
	* Set the deleted reason
	*
	* @param string				$text	Reason
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_delete_reason($text);

	/**
	* Get the user deleted post
	*
	* @return int
	*/
	public function get_delete_user();

	/**
	* Set the user deleted post
	*
	* @param int				$id		User ID
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_delete_user($id);

	/**
	* Get post content for edit
	*
	* @return string
	*/
	public function get_text_for_edit();

	/**
	* Get post content for display
	*
	* @param bool $censor True to censor the content
	* @return string
	*/
	public function get_text_for_display($censor = true);

	/**
	* Set post content
	*
	* @param string				$text	Post content
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text($text);

	/**
	* Check if BBCode is enabled on the post content
	*
	* @return bool
	*/
	public function text_bbcode_enabled();

	/**
	* Enable/Disable BBCode on the post content
	* This should be called before set_text(); text_enable_bbcode()->set_text()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_bbcode($enable = true);

	/**
	* Check if URLs is enabled on the post content
	*
	* @return bool
	*/
	public function text_urls_enabled();

	/**
	* Enable/Disable URLs on the post content
	* This should be called before set_text(); text_enable_urls()->set_text()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_urls($enable = true);

	/**
	* Check if smilies are enabled on the post content
	*
	* @return bool
	*/
	public function text_smilies_enabled();

	/**
	* Enable/Disable smilies on the post content
	* This should be called before set_text(); text_enable_smilies()->set_text()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return post_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_smilies($enable = true);
}
