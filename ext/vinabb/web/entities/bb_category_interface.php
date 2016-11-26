<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single phpBB resource category
*/
interface bb_category_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int					$id		Category ID
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\invalid_argument
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @param int $bb_type phpBB resource type
	* @return bb_category_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert($bb_type);

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return bb_category_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the category ID
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the phpBB resource type
	*
	* @return int
	*/
	public function get_bb_type();

	/**
	* Get the category name
	*
	* @return string
	*/
	public function get_name();

	/**
	* Set the category name
	*
	* @param string					$text	Category name
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text);

	/**
	* Get the Vietnamese category name
	*
	* @return string
	*/
	public function get_name_vi();

	/**
	* Set the Vietnamese category name
	*
	* @param string					$text	Vietnamese category name
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_vi($text);

	/**
	* Get the category varname
	*
	* @return string
	*/
	public function get_varname();

	/**
	* Set the category varname
	*
	* @param int					$text	Category varname
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_varname($text);

	/**
	* Get the category description
	*
	* @return string
	*/
	public function get_desc();

	/**
	* Set the category description
	*
	* @param string					$text	Category description
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_desc($text);

	/**
	* Get the Vietnamese category description
	*
	* @return string
	*/
	public function get_desc_vi();

	/**
	* Set the Vietnamese category description
	*
	* @param string					$text	Vietnamese category description
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_desc_vi($text);

	/**
	* Get the category icon
	*
	* @return string
	*/
	public function get_icon();

	/**
	* Set the category icon
	*
	* @param int					$text	Category icon
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_icon($text);
}
