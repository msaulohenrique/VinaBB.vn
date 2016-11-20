<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single language
*/
interface language_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int					$iso	2-letter language ISO code
	* @return language_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($iso);

	/**
	* Import data for an entity
	*
	* Used when the data is already loaded externally.
	* Any existing data on this entity is over-written.
	* All data is validated and an exception is thrown if any data is invalid.
	*
	* @param array					$data	Data array from the database
	* @return language_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return language_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return language_interface $this Object for chaining calls: load()->set()->save()
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
	* Get the language ISO
	*
	* @return string
	*/
	public function get_iso();

	/**
	* Set the language ISO
	*
	* @param string					$text	Language ISO
	* @return language_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_iso($text);

	/**
	* Get the language directory name
	*
	* @return string
	*/
	public function get_dir();

	/**
	* Set the language directory name
	*
	* @param string					$text	Language directory name
	* @return language_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_dir($text);

	/**
	* Get the English language name
	*
	* @return string
	*/
	public function get_english_name();

	/**
	* Set the English language name
	*
	* @param string					$text	English language name
	* @return language_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_english_name($text);

	/**
	* Get the language's native name
	*
	* @return string
	*/
	public function get_local_name();

	/**
	* Set the language's native name
	*
	* @param string					$text	Language's native name
	* @return language_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_local_name($text);

	/**
	* Get the translator name
	*
	* @return string
	*/
	public function get_author();

	/**
	* Set the translator name
	*
	* @param string					$text	Translator name
	* @return language_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_author($text);
}
