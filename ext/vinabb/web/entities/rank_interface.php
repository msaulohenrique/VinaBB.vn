<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single rank
*/
interface rank_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Rank ID
	* @return rank_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return rank_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\invalid_argument
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return rank_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return rank_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the rank ID
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the rank title
	*
	* @return string
	*/
	public function get_title();

	/**
	* Set the rank title
	*
	* @param string				$text	Rank title
	* @return rank_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_title($text);

	/**
	* Get the rank's minimum posts
	*
	* @return int
	*/
	public function get_min();

	/**
	* Set the rank's minimum posts
	*
	* @param string				$value	Number of posts
	* @return rank_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_min($value);

	/**
	* The rank is special?
	*
	* @return bool
	*/
	public function get_special();

	/**
	* Set the rank is special or not
	*
	* @param bool				$value	true: yes; false: no
	* @return rank_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_special($value);

	/**
	* Get the rank image
	*
	* @return string
	*/
	public function get_image();

	/**
	* Set the rank image
	*
	* @param string				$text	Rank image file
	* @return rank_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_image($text);
}
