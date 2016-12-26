<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single menu item
*/
interface menu_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Page ID
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id = 0);

	/**
	* Import data for an entity
	*
	* Used when the data is already loaded externally.
	* Any existing data on this entity is over-written.
	* All data is validated and an exception is thrown if any data is invalid.
	*
	* @param array				$data	Data array from the database
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\invalid_argument
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return menu_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return menu_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the menu ID
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the parent menu ID
	*
	* @return int
	*/
	public function get_parent_id();

	/**
	* Get the left_id for the tree
	*
	* @return int
	*/
	public function get_left_id();

	/**
	* Get the right_id for the tree
	*
	* @return int
	*/
	public function get_right_id();

	/**
	* Get the menu title
	*
	* @return string
	*/
	public function get_name();

	/**
	* Set the menu title
	*
	* @param string				$text	Menu title
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text);

	/**
	* Get the Vietnamese menu title
	*
	* @return string
	*/
	public function get_name_vi();

	/**
	* Set the Vietnamese menu title
	*
	* @param string				$text	Vietnamese menu title
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_vi($text);

	/**
	* Get menu type
	*
	* @return int
	*/
	public function get_type();

	/**
	* Set menu type
	*
	* @param int				$value	Menu type
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_type($value);

	/**
	* Get menu icon
	*
	* @return string
	*/
	public function get_icon();

	/**
	* Set menu icon
	*
	* @param string				$text	Menu icon
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_icon($text);

	/**
	* Get menu data
	*
	* @return string
	*/
	public function get_data();

	/**
	* Set menu data
	*
	* @param string				$text	Menu data
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_data($text);

	/**
	* Get menu open target setting
	*
	* @return bool
	*/
	public function get_target();

	/**
	* Get menu display setting for guests
	*
	* @return bool
	*/
	public function get_enable_guest();

	/**
	* Set menu display setting for guests
	*
	* @param bool				$value	true: enable; false: disable
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_guest($value);

	/**
	* Get menu display setting for bots
	*
	* @return bool
	*/
	public function get_enable_bot();

	/**
	* Set menu display setting for bots
	*
	* @param bool				$value	true: enable; false: disable
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_bot($value);

	/**
	* Get menu display setting for newly registered users
	*
	* @return bool
	*/
	public function get_enable_new_user();

	/**
	* Set menu display setting for newly registered users
	*
	* @param bool				$value	true: enable; false: disable
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_new_user($value);

	/**
	* Get menu display setting for registered users
	*
	* @return bool
	*/
	public function get_enable_user();

	/**
	* Set menu display setting for registered users
	*
	* @param bool				$value	true: enable; false: disable
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_user($value);

	/**
	* Get menu display setting for moderators
	*
	* @return bool
	*/
	public function get_enable_mod();

	/**
	* Set menu display setting for moderators
	*
	* @param bool				$value	true: enable; false: disable
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_mod($value);

	/**
	* Get menu display setting for global moderators
	*
	* @return bool
	*/
	public function get_enable_global_mod();

	/**
	* Set menu display setting for global moderators
	*
	* @param bool				$value	true: enable; false: disable
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_global_mod($value);

	/**
	* Get menu display setting for administrators
	*
	* @return bool
	*/
	public function get_enable_admin();

	/**
	* Set menu display setting for administrators
	*
	* @param bool				$value	true: enable; false: disable
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_admin($value);

	/**
	* Get menu display setting for founders
	*
	* @return bool
	*/
	public function get_enable_founder();

	/**
	* Set menu display setting for founders
	*
	* @param bool				$value	true: enable; false: disable
	* @return menu_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\not_auth
	*/
	public function set_enable_founder($value);
}
