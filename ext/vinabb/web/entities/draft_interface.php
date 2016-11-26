<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single draft
*/
interface draft_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Draft ID
	* @return draft_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return draft_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\invalid_argument
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return draft_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return draft_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the draft ID
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
	* @return draft_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return draft_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_topic_id($id);

	/**
	* Get the user ID
	*
	* @return int
	*/
	public function get_user_id();

	/**
	* Set the user ID
	*
	* @param int				$id		User ID
	* @return draft_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_user_id($id);

	/**
	* Get the draft subject
	*
	* @return string
	*/
	public function get_subject();

	/**
	* Set the draft subject
	*
	* @param string				$text	Draft subject
	* @return rank_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_subject($text);

	/**
	* Get the draft content
	*
	* @return string
	*/
	public function get_message();

	/**
	* Get the saving time
	*
	* @return int
	*/
	public function get_save_time();
}
