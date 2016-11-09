<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single news category
*/
interface portal_category_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int						$id		Category ID
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return portal_category_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return portal_category_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the cat_id
	*
	* @return int cat_id
	*/
	public function get_id();

	/**
	* Get the parent_id
	*
	* @return int parent_id
	*/
	public function get_parent_id();

	/**
	* Get the left_id for the tree
	*
	* @return int left_id
	*/
	public function get_left_id();

	/**
	* Get the right_id for the tree
	*
	* @return int right_id
	*/
	public function get_right_id();

	/**
	* Get the category name
	*
	* @return string Category name
	*/
	public function get_name();

	/**
	* Set the category name
	*
	* @param string						$name	Category name
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($name);

	/**
	* Get the Vietnamese category name
	*
	* @return string Vietnamese category name
	*/
	public function get_name_vi();

	/**
	* Set the Vietnamese category name
	*
	* @param string						$name	Vietnamese category name
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_vi($name);

	/**
	* Get the category varname
	*
	* @return string Category varname
	*/
	public function get_varname();

	/**
	* Set the category varname
	*
	* @param int						$varname	Category varname
	* @return portal_category_interface	$this		Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_varname($varname);

	/**
	* Get the category icon
	*
	* @return string Category icon
	*/
	public function get_icon();

	/**
	* Set the category icon
	*
	* @param int						$icon	Category icon
	* @return portal_category_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_icon($icon);
}
