<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single page
*/
interface page_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Page ID
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the page_id
	*
	* @return int page_id
	*/
	public function get_id();

	/**
	* Get the page title
	*
	* @return string
	*/
	public function get_name();

	/**
	* Set the page title
	*
	* @param string				$text	Page title
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text);

	/**
	* Get the Vietnamese page title
	*
	* @return string
	*/
	public function get_name_vi();

	/**
	* Set the Vietnamese page title
	*
	* @param string				$text	Vietnamese page title
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_vi($text);

	/**
	* Get the page varname
	*
	* @return string
	*/
	public function get_varname();

	/**
	* Set the page varname
	*
	* @param int				$text	Page varname
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_varname($text);

	/**
	* Get the page description
	*
	* @return string
	*/
	public function get_desc();

	/**
	* Set the page description
	*
	* @param string				$text	Page description
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	 */
	public function set_desc($text);

	/**
	* Get the Vietnamese page description
	*
	* @return string
	*/
	public function get_desc_vi();

	/**
	* Set the Vietnamese page description
	*
	* @param string				$text	Vietnamese page description
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	 */
	public function set_desc_vi($text);

	/**
	* Get page content for edit
	*
	* @return string
	*/
	public function get_text_for_edit();

	/**
	* Get page content for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_text_for_display($censor = true);

	/**
	* Set page content
	*
	* @param string				$text	Page content
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text($text);

	/**
	* Check if BBCode is enabled on the page content
	*
	* @return bool
	*/
	public function text_bbcode_enabled();

	/**
	* Enable BBCode on the page content
	* This should be called before set_text(); text_enable_bbcode()->set_text()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_bbcode();

	/**
	* Disable BBCode on the page content
	* This should be called before set_text(); text_disable_bbcode()->set_text()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_disable_bbcode();

	/**
	* Check if URLs is enabled on the page content
	*
	* @return bool
	*/
	public function text_urls_enabled();

	/**
	* Enable URLs on the page content
	* This should be called before set_text(); text_enable_urls()->set_text()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_urls();

	/**
	* Disable URLs on the page content
	* This should be called before set_text(); text_disable_urls()->set_text()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_disable_urls();

	/**
	* Check if smilies are enabled on the page content
	*
	* @return bool
	*/
	public function text_smilies_enabled();

	/**
	* Enable smilies on the page content
	* This should be called before set_text(); text_enable_smilies()->set_text()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_smilies();

	/**
	* Disable smilies on the page content
	* This should be called before set_text(); text_disable_smilies()->set_text()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_disable_smilies();

	/**
	* Get Vietnamese page content for edit
	*
	* @return string
	*/
	public function get_text_vi_for_edit();

	/**
	* Get Vietnamese page content for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_text_vi_for_display($censor = true);

	/**
	* Set Vietnamese page content
	*
	* @param string				$text	Vietnamese page content
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text_vi($text);

	/**
	* Check if BBCode is enabled on the Vietnamese page content
	*
	* @return bool
	*/
	public function text_vi_bbcode_enabled();

	/**
	* Enable BBCode on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_enable_bbcode()->set_text_vi()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_enable_bbcode();

	/**
	* Disable BBCode on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_disable_bbcode()->set_text_vi()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_disable_bbcode();

	/**
	* Check if URLs is enabled on the Vietnamese page content
	*
	* @return bool
	*/
	public function text_vi_urls_enabled();

	/**
	* Enable URLs on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_enable_urls()->set_text_vi()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_enable_urls();

	/**
	* Disable URLs on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_disable_urls()->set_text_vi()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_disable_urls();

	/**
	* Check if smilies are enabled on the Vietnamese page content
	*
	* @return bool
	*/
	public function text_vi_smilies_enabled();

	/**
	* Enable smilies on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_enable_smilies()->set_text_vi()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_enable_smilies();

	/**
	* Disable smilies on the Vietnamese page content
	* This should be called before set_text_vi(); text_vi_disable_smilies()->set_text_vi()
	*
	* @return page_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_vi_disable_smilies();

	/**
	* Get page display setting in template
	*
	* @return bool
	*/
	public function get_enable();

	/**
	* Set page display setting in template
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable($value);

	/**
	* Get page display setting for guests
	*
	* @return bool
	*/
	public function get_enable_guest();

	/**
	* Set page display setting for guests
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_guest($value);

	/**
	* Get page display setting for bots
	*
	* @return bool
	*/
	public function get_enable_bot();

	/**
	* Set page display setting for bots
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_bot($value);

	/**
	* Get page display setting for newly registered users
	*
	* @return bool
	*/
	public function get_enable_new_user();

	/**
	* Set page display setting for newly registered users
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_new_user($value);

	/**
	* Get page display setting for registered users
	*
	* @return bool
	*/
	public function get_enable_user();

	/**
	* Set page display setting for registered users
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_user($value);

	/**
	* Get page display setting for moderators
	*
	* @return bool
	*/
	public function get_enable_mod();

	/**
	* Set page display setting for moderators
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_mod($value);

	/**
	* Get page display setting for global moderators
	*
	* @return bool
	*/
	public function get_enable_global_mod();

	/**
	* Set page display setting for global moderators
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_global_mod($value);

	/**
	* Get page display setting for administrators
	*
	* @return bool
	*/
	public function get_enable_admin();

	/**
	* Set page display setting for administrators
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_admin($value);

	/**
	* Get page display setting for founders
	*
	* @return bool
	*/
	public function get_enable_founder();

	/**
	* Set page display setting for founders
	*
	* @param bool				$value	Config value
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_founder($value);
}
