<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single smiley
*/
interface smiley_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Smiley ID
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return smiley_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return smiley_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the lang_id
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the smiley code
	*
	* @return string
	*/
	public function get_code();

	/**
	* Set the smiley code
	*
	* @param string				$text	Smiley code
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_code($text);

	/**
	* Get the smiley emotion
	*
	* @return string
	*/
	public function get_emotion();

	/**
	* Set the smiley emotion
	*
	* @param string				$text	Smiley emotion
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_emotion($text);

	/**
	* Get the smiley image file
	*
	* @return string
	*/
	public function get_url();

	/**
	* Set the smiley image file
	*
	* @param string				$text	Smiley image file
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_url($text);

	/**
	* Get the smiley width
	*
	* @return int
	*/
	public function get_width();

	/**
	* Set the smiley width
	*
	* @param int				$value	Smiley width
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_width($value);

	/**
	* Get the smiley height
	*
	* @return int
	*/
	public function get_height();

	/**
	* Set the smiley height
	*
	* @param int				$value	Smiley height
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_height($value);

	/**
	* Get display setting on posting page
	*
	* @return bool
	*/
	public function get_display_on_posting();

	/**
	* Set display setting on posting page
	*
	* @param bool				$value	Config value
	* @return smiley_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_display_on_posting($value);
}
