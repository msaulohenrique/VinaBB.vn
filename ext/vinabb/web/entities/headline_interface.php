<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single headline
*/
interface headline_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int					$id		Headline ID
	* @return headline_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @param array					$data	Data array from the database
	* @return headline_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\invalid_argument
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return headline_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return headline_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the headline ID
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the headline language
	*
	* @return string
	*/
	public function get_lang();

	/**
	* Set the headline language
	*
	* @param string					$text	2-letter language ISO code
	* @return headline_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_lang($text);

	/**
	* Get the headline title
	*
	* @return string
	*/
	public function get_name();

	/**
	* Set the headline title
	*
	* @param string					$text	Headline title
	* @return headline_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text);

	/**
	* Get the headline description
	*
	* @return string
	*/
	public function get_desc();

	/**
	* Set the headline description
	*
	* @param string					$text	Headline description
	* @return headline_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_desc($text);

	/**
	* Get the headline image
	*
	* @return string
	*/
	public function get_img();

	/**
	* Set the headline image
	*
	* @param string					$text	Headline image URL
	* @return headline_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_img($text);

	/**
	* Get the headline link
	*
	* @return string
	*/
	public function get_url();

	/**
	* Set the headline link
	*
	* @param string					$text	Headline URL
	* @return headline_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_url($text);

	/**
	* Get the headline sorting order
	*
	* @return int
	*/
	public function get_order();

	/**
	* Set the headline sorting order
	*
	* @param string $lang 2-letter language ISO code
	* @return headline_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function set_order($lang);
}
