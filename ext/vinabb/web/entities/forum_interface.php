<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single forum
*/
interface forum_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Forum ID
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return forum_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return forum_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the forum_id
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the parent_id
	*
	* @return int
	*/
	public function get_parent_id();

	/**
	* Set the parent_id
	*
	* @param int				$id		Parent ID
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_parent_id($id);

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
	* Get the forum name
	*
	* @return string
	*/
	public function get_name();

	/**
	* Set the forum name
	*
	* @param string				$text	Forum name
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text);

	/**
	* Get the forum SEO name
	*
	* @return string
	*/
	public function get_name_seo();

	/**
	* Set the forum SEO name
	*
	* @param string				$text	Forum SEO name
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_seo($text);

	/**
	* Get forum description for edit
	*
	* @return string
	*/
	public function get_desc_for_edit();

	/**
	* Get forum description for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_desc_for_display($censor = true);

	/**
	* Set forum description
	*
	* @param string				$text	Forum description
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc($text);

	/**
	* Check if BBCode is enabled on the forum description
	*
	* @return bool
	*/
	public function desc_bbcode_enabled();

	/**
	* Enable/Disable BBCode on the forum description
	* This should be called before set_desc(); desc_enable_bbcode()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_bbcode($enable);

	/**
	* Check if URLs is enabled on the forum description
	*
	* @return bool
	*/
	public function desc_urls_enabled();

	/**
	* Enable/Disable URLs on the forum description
	* This should be called before set_desc(); desc_enable_urls()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_urls($enable);

	/**
	* Check if smilies are enabled on the forum description
	*
	* @return bool
	*/
	public function desc_smilies_enabled();

	/**
	* Enable/Disable smilies on the forum description
	* This should be called before set_desc(); desc_enable_smilies()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_smilies($enable);

	/**
	* Get forum rules for edit
	*
	* @return string
	*/
	public function get_rules_for_edit();

	/**
	* Get forum rules for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_rules_for_display($censor = true);

	/**
	* Set forum rules
	*
	* @param string				$text	Forum rules
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_rules($text);

	/**
	* Check if BBCode is enabled on the forum rules
	*
	* @return bool
	*/
	public function rules_bbcode_enabled();

	/**
	* Enable/Disable BBCode on the forum rules
	* This should be called before set_rules(); rules_enable_bbcode()->set_rules()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function rules_enable_bbcode($enable);

	/**
	* Check if URLs is enabled on the forum rules
	*
	* @return bool
	*/
	public function rules_urls_enabled();

	/**
	* Enable/Disable URLs on the forum rules
	* This should be called before set_rules(); rules_enable_urls()->set_rules()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function rules_enable_urls($enable);

	/**
	* Check if smilies are enabled on the forum rules
	*
	* @return bool
	*/
	public function rules_smilies_enabled();

	/**
	* Enable/Disable smilies on the forum rules
	* This should be called before set_rules(); rules_enable_smilies()->set_rules()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function rules_enable_smilies($enable);

	/**
	* Get the number of topics per page in this forum
	*
	* @return int
	*/
	public function get_topics_per_page();

	/**
	* Get the forum type
	*
	* @return int
	*/
	public function get_type();

	/**
	* Set the forum type
	*
	* @param int				$value	Forum type
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_type($value);

	/**
	* Get the forum status
	*
	* @return int
	*/
	public function get_status();

	/**
	* Set the forum status
	*
	* @param int				$value	Forum status
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_status($value);
}
