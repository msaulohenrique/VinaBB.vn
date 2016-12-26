<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single phpBB resource item version
*/
interface bb_item_version_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int						$id		Item ID
	* @param string						$branch	phpBB branch
	* @return bb_item_version_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id, $branch);

	/**
	* Import data for an entity
	*
	* Used when the data is already loaded externally.
	* Any existing data on this entity is over-written.
	* All data is validated and an exception is thrown if any data is invalid.
	*
	* @param array						$data	Data array from the database
	* @return bb_item_version_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\invalid_argument
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @param int						$id		Item ID
	* @param string						$branch	phpBB branch
	* @return bb_item_version_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert($id, $branch);

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return bb_item_version_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the item ID
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the phpBB branch
	*
	* @return string
	*/
	public function get_phpbb_branch();

	/**
	* Get the phpBB version
	*
	* @return string
	*/
	public function get_phpbb_version();

	/**
	* Set the phpBB version
	*
	* @param string						$text	phpBB version
	* @return bb_item_version_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_phpbb_version($text);

	/**
	* Get the item version
	*
	* @return string
	*/
	public function get_version();

	/**
	* Set the item version
	*
	* @param string						$text	Item version
	* @return bb_item_version_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_version($text);

	/**
	* Get the item's downloadable file
	*
	* @param string	$bb_mode	phpBB resource mode (ext|style|acp_style|lang|tool)
	* @param bool	$real_path	True to return the path on filesystem, false to return the web access path
	* @param bool	$full_path	True to return the path + filename, false to return only filename
	* @return string
	*/
	public function get_file($bb_mode, $real_path = false, $full_path = true);

	/**
	* Set the item's downloadable file
	*
	* @param string						$text	Item filename
	* @return bb_item_version_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_file($text);

	/**
	* Get the number of downloads
	*
	* @return int
	*/
	public function get_downloads();
}
