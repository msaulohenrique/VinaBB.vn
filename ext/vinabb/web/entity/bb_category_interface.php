<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entity;

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
	* @throws \vinabb\web\exception\out_of_bounds
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
	* @throws \vinabb\web\exception\base
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return bb_category_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return bb_category_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\out_of_bounds
	*/
	public function save();

	/**
	* Get the cat_id
	*
	* @return int cat_id
	*/
	public function get_id();

	/**
	* Get the category name
	*
	* @return string Category name
	*/
	public function get_cat_name();

	/**
	* Set the category name
	*
	* @param string					$name	Category name
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\unexpected_value
	*/
	public function set_cat_name($name);

	/**
	* Get the Vietnamese category name
	*
	* @return string Vietnamese category name
	*/
	public function get_cat_name_vi();

	/**
	* Set the Vietnamese category name
	*
	* @param string					$name	Vietnamese category name
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\unexpected_value
	*/
	public function set_cat_name_vi($name);

	/**
	* Get the category varname
	*
	* @return string Category varname
	*/
	public function get_cat_varname();

	/**
	* Set the category varname
	*
	* @param int					$varname	Category varname
	* @return bb_category_interface	$this		Object for chaining calls: load()->set()->save()
	*/
	public function set_cat_varname($varname);

	/**
	* Get the category description
	*
	* @return string Category description
	*/
	public function get_cat_desc();

	/**
	* Set the category description
	*
	* @param string					$desc	Category description
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\unexpected_value
	*/
	public function set_cat_desc($desc);

	/**
	* Get the Vietnamese category description
	*
	* @return string Vietnamese category description
	*/
	public function get_cat_desc_vi();

	/**
	* Set the Vietnamese category description
	*
	* @param string					$desc	Vietnamese category description
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exception\unexpected_value
	*/
	public function set_cat_desc_vi($desc);

	/**
	* Get the category icon
	*
	* @return string Category icon
	*/
	public function get_cat_icon();

	/**
	* Set the category icon
	*
	* @param int					$icon	Category icon
	* @return bb_category_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_cat_icon($icon);
}
