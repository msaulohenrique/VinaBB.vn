<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single author
*/
interface bb_author_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int					$id		Author ID
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\invalid_argument
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return bb_author_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return bb_author_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the author ID
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the user ID
	*
	* @return int
	*/
	public function get_user_id();

	/**
	* Set the user ID
	*
	* @param int					$id		User ID
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_user_id($id);

	/**
	* Get the author name
	*
	* @return string
	*/
	public function get_name();

	/**
	* Set the author name
	*
	* @param string					$text	Author name
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text);

	/**
	* Get the author SEO name
	*
	* @return string
	*/
	public function get_name_seo();

	/**
	* Set the author SEO name
	*
	* @param string					$text	Author SEO name
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_seo($text);

	/**
	* Get the author firstname
	*
	* @return string
	*/
	public function get_firstname();

	/**
	* Set the author firstname
	*
	* @param string					$text	Author firstname
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_firstname($text);

	/**
	* Get the author lastname
	*
	* @return string
	*/
	public function get_lastname();

	/**
	* Set the author lastname
	*
	* @param string					$text	Author lastname
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_lastname($text);

	/**
	* Check the author is a group?
	*
	* @return bool
	*/
	public function get_is_group();

	/**
	* Set the author is a group
	*
	* @param bool					$value	Config value
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_is_group($value);
	/**
	* Get the author's group
	*
	* @return int
	*/
	public function get_group();

	/**
	* Set the author's group
	*
	* @param int					$id		Group ID (Also is the author_id)
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_group($id);

	/**
	* Get the author website
	*
	* @return string
	*/
	public function get_www();

	/**
	* Set the author website
	*
	* @param string					$text	Author website
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_www($text);

	/**
	* Get the author email
	*
	* @return string
	*/
	public function get_email();

	/**
	* Set the author email
	*
	* @param string					$text	Author email
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_email($text);

	/**
	* Get the author's phpBB.com user ID
	*
	* @return int
	*/
	public function get_phpbb();

	/**
	* Set the author's phpBB.com user ID
	*
	* @param int					$value	phpBB.com user ID
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_phpbb($value);

	/**
	* Get the author's social page: GitHub
	*
	* @return string
	*/
	public function get_github();

	/**
	* Set the author's social page: GitHub
	*
	* @param string					$text	GitHub username
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_github($text);

	/**
	* Get the author's social page: Facebook
	*
	* @return string
	*/
	public function get_facebook();

	/**
	* Set the author's social page: Facebook
	*
	* @param string					$text	Facebook username
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_facebook($text);

	/**
	* Get the author's social page: Twitter
	*
	* @return string
	*/
	public function get_twitter();

	/**
	* Set the author's social page: Twitter
	*
	* @param string					$text	Twitter username
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_twitter($text);

	/**
	* Get the author's social page: Google+
	*
	* @return string
	*/
	public function get_google_plus();

	/**
	* Set the author's social page: Google+
	*
	* @param string					$text	Google+ username
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_google_plus($text);

	/**
	* Get the author's social page: Skype
	*
	* @return string
	*/
	public function get_skype();

	/**
	* Set the author's social page: Skype
	*
	* @param string					$text	Skype username
	* @return bb_author_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_skype($text);
}
