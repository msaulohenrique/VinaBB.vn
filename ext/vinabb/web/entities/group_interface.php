<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single group
*/
interface group_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Group ID
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\invalid_argument
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return group_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return group_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the group ID
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the group name
	*
	* @return string
	*/
	public function get_name();

	/**
	* Set the group name
	*
	* @param string				$text	Group name
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text);

	/**
	* Get the group type
	*
	* @return int
	*/
	public function get_type();

	/**
	* Set the group type
	*
	* @param int				$value	Group type
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_type($value);

	/**
	* Only founders can manage the group?
	*
	* @return bool
	*/
	public function get_founder_manage();

	/**
	* Exclude group leader from group permissions
	*
	* @return bool
	*/
	public function get_skip_auth();

	/**
	* Display group in the legend?
	*
	* @return bool
	*/
	public function get_display();

	/**
	* Group can receive PMs?
	*
	* @return bool
	*/
	public function get_receive_pm();

	/**
	* Get the maximum characters in signature
	*
	* @return int
	*/
	public function get_sig_chars();

	/**
	* Get the maximum PMs per folder
	*
	* @return int
	*/
	public function get_message_limit();
	/**
	* Get the order in group legend
	*
	* @return int
	*/
	public function get_legend();

	/**
	* Get the maximum recipients per PM
	*
	* @return int
	*/
	public function get_max_recipients();

	/**
	* Get the group avatar image
	*
	* @return string
	*/
	public function get_avatar();

	/**
	* Set the group avatar image
	*
	* @param string				$text	Avatar image URL
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_avatar($text);

	/**
	* Get the group avatar type
	*
	* @return string
	*/
	public function get_avatar_type();

	/**
	* Set the group avatar type
	*
	* @param int				$value	Avatar type
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_avatar_type($value);

	/**
	* Get the group avatar width
	*
	* @return int
	*/
	public function get_avatar_width();

	/**
	* Set the group avatar width
	*
	* @param int				$value	Avatar width
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_avatar_width($value);

	/**
	* Get the group avatar height
	*
	* @return int
	*/
	public function get_avatar_height();

	/**
	* Set he group avatar height
	*
	* @param int				$value	Avatar height
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_avatar_height($value);

	/**
	* Get the group rank
	*
	* @return int
	*/
	public function get_rank();

	/**
	* Set the group rank
	*
	* @param int				$id		Rank ID
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_rank($id);

	/**
	* Get the group username color
	*
	* @return string
	*/
	public function get_colour();

	/**
	* Set the group username color
	*
	* @param string				$text	6-char HEX code without #
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_colour($text);

	/**
	* Get group description for edit
	*
	* @return string
	*/
	public function get_desc_for_edit();

	/**
	* Get group description for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_desc_for_display($censor = true);

	/**
	* Set group description
	*
	* @param string				$text	Group description
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc($text);

	/**
	* Check if BBCode is enabled on the group description
	*
	* @return bool
	*/
	public function desc_bbcode_enabled();

	/**
	* Enable/Disable BBCode on the group description
	* This should be called before set_desc(); desc_enable_bbcode()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_bbcode($enable);

	/**
	* Check if URLs is enabled on the group description
	*
	* @return bool
	*/
	public function desc_urls_enabled();

	/**
	* Enable/Disable URLs on the group description
	* This should be called before set_desc(); desc_enable_urls()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_urls($enable);

	/**
	* Check if smilies are enabled on the group description
	*
	* @return bool
	*/
	public function desc_smilies_enabled();

	/**
	* Enable/Disable smilies on the group description
	* This should be called before set_desc(); desc_enable_smilies()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return group_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_smilies($enable);
}
