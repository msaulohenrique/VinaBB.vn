<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

/**
* Interface for a single post icon
*/
interface icon_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Icon ID
	* @return icon_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return icon_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return icon_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return icon_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the icons_id
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the icon image file
	*
	* @return string
	*/
	public function get_url();

	/**
	* Set the icon image file
	*
	* @param string				$text	Icon image file
	* @return icon_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_url($text);

	/**
	* Get the icon width
	*
	* @return int
	*/
	public function get_width();

	/**
	* Set the icon width
	*
	* @param int				$value	Icon width
	* @return icon_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_width($value);

	/**
	* Get the icon height
	*
	* @return int
	*/
	public function get_height();

	/**
	* Set the icon height
	*
	* @param int				$value	Icon height
	* @return icon_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_height($value);

	/**
	* Get the icon hover text
	*
	* @return string
	*/
	public function get_alt();

	/**
	* Set the icon hover text
	*
	* @param string				$text	Icon hover text
	* @return icon_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_alt($text);

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
	* @return icon_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_display_on_posting($value);
}
