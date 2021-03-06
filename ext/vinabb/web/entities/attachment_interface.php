<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single attachment
*/
interface attachment_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int					$id		Attachment ID
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @param array					$data	Data array from the database
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\invalid_argument
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return attachment_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return attachment_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the attachment ID
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the poster ID
	*
	* @return int
	*/
	public function get_poster_id();

	/**
	* Set the poster ID
	*
	* @param int					$id		User ID
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_poster_id($id);

	/**
	* Get the topic ID
	*
	* @return int
	*/
	public function get_topic_id();

	/**
	* Set the topic ID
	*
	* @param int					$id		Topic ID
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_topic_id($id);

	/**
	* Get the post or PM ID
	*
	* @return int
	*/
	public function get_post_msg_id();

	/**
	* Set the post or PM ID
	*
	* @param int					$id		Post or PM ID
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_post_msg_id($id);

	/**
	* The attachment is within a PM?
	*
	* @return bool
	*/
	public function get_in_message();

	/**
	* Set the attachment is or is not within a PM
	*
	* @param bool					$value	true: PM; false: post
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_in_message($value);

	/**
	* The attachment is not assigned to any posts or PMs?
	*
	* @return bool
	*/
	public function get_is_orphan();

	/**
	* Set the attachment is or is not orphan
	*
	* @param bool					$value	true: not using; false: in using
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_is_orphan($value);

	/**
	* Get number of downloads
	*
	* @return int
	*/
	public function get_download_count();

	/**
	* Get the attachment comment
	*
	* @return string
	*/
	public function get_comment();

	/**
	* Set the attachment comment
	*
	* @param string					$text	Attachment comment
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_comment($text);

	/**
	* Get attachment's filename on server
	*
	* @return string
	*/
	public function get_physical_filename();

	/**
	* Set attachment's filename on server
	*
	* @param string					$text	Server filename
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_physical_filename($text);

	/**
	* Get attachment's original filename
	*
	* @return string
	*/
	public function get_real_filename();

	/**
	* Set attachment's original filename
	*
	* @param string					$text	Original filename
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_real_filename($text);

	/**
	* Get attachment's file extension
	*
	* @return string
	*/
	public function get_extension();

	/**
	* Set attachment's file extension
	*
	* @param string					$text	File extension
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_extension($text);

	/**
	* Get attachment's MIME type
	*
	* @return string
	*/
	public function get_mimetype();

	/**
	* Set attachment's MIME type
	*
	* @param string					$text	MIME type
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_mimetype($text);

	/**
	* Get attachment's filesize
	*
	* @return int
	*/
	public function get_filesize();

	/**
	* Set attachment's filesize
	*
	* @param int					$value	Filesize
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_filesize($value);

	/**
	* Get attachment's uploaded time
	*
	* @return int
	*/
	public function get_filetime();

	/**
	* Set attachment's uploaded time
	*
	* @param int					$value	Uploaded time
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_filetime($value);

	/**
	* The attachment has a thumbnail?
	*
	* @return bool
	*/
	public function get_thumbnail();

	/**
	* Set attachment's MIME type
	*
	* @param bool					$value	true: yes; false: no
	* @return attachment_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_thumbnail($value);
}
