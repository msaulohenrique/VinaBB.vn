<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Entity for a single phpBB resource item
*/
interface bb_item_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Item ID
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\invalid_argument
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @param int $bb_type phpBB resource type
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert($bb_type);

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
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
	* Get the phpBB resource type
	*
	* @return int
	*/
	public function get_bb_type();

	/**
	* Set the phpBB resource type
	*
	* @param int				$bb_type	phpBB resource type
	* @return bb_item_interface	$this		Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_bb_type($bb_type);

	/**
	* Get the category ID
	*
	* @return int
	*/
	public function get_cat_id();

	/**
	* Set the category ID
	*
	* @param int				$id		Category ID
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_cat_id($id);

	/**
	* Get the author ID
	*
	* @return int
	*/
	public function get_author_id();

	/**
	* Set the author ID
	*
	* @param int				$id		Author ID
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_author_id($id);

	/**
	* Get the item name
	*
	* @return string
	*/
	public function get_name();

	/**
	* Set the item name
	*
	* @param string				$text	Item name
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text);

	/**
	* Get the item varname
	*
	* @return string
	*/
	public function get_varname();
	/**
	* Set the item varname
	*
	* @param string				$text	Item varname
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_varname($text);

	/**
	* Get item description for edit
	*
	* @return string
	*/
	public function get_desc_for_edit();

	/**
	* Get item description for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_desc_for_display($censor = true);

	/**
	* Set item description
	*
	* @param string				$text	Item description
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc($text);

	/**
	* Check if BBCode is enabled on the item description
	*
	* @return bool
	*/
	public function desc_bbcode_enabled();

	/**
	* Enable/Disable BBCode on the item description
	* This should be called before set_desc(); desc_enable_bbcode()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_bbcode($enable);

	/**
	* Check if URLs is enabled on the item description
	*
	* @return bool
	*/
	public function desc_urls_enabled();

	/**
	* Enable/Disable URLs on the item description
	* This should be called before set_desc(); desc_enable_urls()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_urls($enable);

	/**
	* Check if smilies are enabled on the item description
	*
	* @return bool
	*/
	public function desc_smilies_enabled();

	/**
	* Enable/Disable smilies on the item description
	* This should be called before set_desc(); desc_enable_smilies()->set_desc()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_enable_smilies($enable);

	/**
	* Get Vietnamese item description for edit
	*
	* @return string
	*/
	public function get_desc_vi_for_edit();

	/**
	* Get Vietnamese item description for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_desc_vi_for_display($censor = true);

	/**
	* Set Vietnamese item description
	*
	* @param string				$text	Vietnamese item description
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_desc_vi($text);

	/**
	* Check if BBCode is enabled on the Vietnamese item description
	*
	* @return bool
	*/
	public function desc_vi_bbcode_enabled();

	/**
	* Enable/Disable BBCode on the Vietnamese item description
	* This should be called before set_desc_vi(); desc_vi_enable_bbcode()->set_desc_vi()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_vi_enable_bbcode($enable);

	/**
	* Check if URLs is enabled on the Vietnamese item description
	*
	* @return bool
	*/
	public function desc_vi_urls_enabled();

	/**
	* Enable/Disable URLs on the Vietnamese item description
	* This should be called before set_desc_vi(); desc_vi_enable_urls()->set_desc_vi()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_vi_enable_urls($enable);

	/**
	* Check if smilies are enabled on the Vietnamese item description
	*
	* @return bool
	*/
	public function desc_vi_smilies_enabled();

	/**
	* Enable/Disable smilies on the Vietnamese item description
	* This should be called before set_desc_vi(); desc_vi_enable_smilies()->set_desc_vi()
	*
	* @param bool				$enable	true: enable; false: disable
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function desc_vi_enable_smilies($enable);

	/**
	* Get the extension property: Style Changes
	*
	* @return bool
	*/
	public function get_ext_style();

	/**
	* Set the extension property: Style Changes
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_style($value);

	/**
	* Get the extension property: ACP Style Changes
	*
	* @return bool
	*/
	public function get_ext_acp_style();

	/**
	* Set the extension property: ACP Style Changes
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_acp_style($value);

	/**
	* Get the extension property: Language Files
	*
	* @return bool
	*/
	public function get_ext_lang();

	/**
	* Set the extension property: Language Files
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_lang($value);

	/**
	* Get the extension property: Schema Changes
	*
	* @return bool
	*/
	public function get_ext_db_schema();

	/**
	* Set the extension property: Schema Changes
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_db_schema($value);

	/**
	* Get the extension property: Data Changes
	*
	* @return bool
	*/
	public function get_ext_db_data();

	/**
	* Set the extension property: Data Changes
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_db_data($value);

	/**
	* Get the extension property: PHP Events
	*
	* @return bool
	*/
	public function get_ext_event();

	/**
	* Set the extension property: PHP Events
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_event($value);

	/**
	* Get the extension property: Template Events
	*
	* @return bool
	*/
	public function get_ext_event_style();

	/**
	* Set the extension property: Template Events
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_event_style($value);

	/**
	* Get the extension property: ACP Template Events
	*
	* @return bool
	*/
	public function get_ext_event_acp_style();

	/**
	* Set the extension property: ACP Template Events
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_event_acp_style($value);

	/**
	* Get the extension property: ACP Modules
	*
	* @return bool
	*/
	public function get_ext_module_acp();

	/**
	* Set the extension property: ACP Modules
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_module_acp($value);

	/**
	* Get the extension property: MCP Modules
	*
	* @return bool
	*/
	public function get_ext_module_mcp();

	/**
	* Set the extension property: MCP Modules
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_module_mcp($value);

	/**
	* Get the extension property: UCP Modules
	*
	* @return bool
	*/
	public function get_ext_module_ucp();

	/**
	* Set the extension property: UCP Modules
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_module_ucp($value);

	/**
	* Get the extension property: Notifications
	*
	* @return bool
	*/
	public function get_ext_notification();

	/**
	* Set the extension property: Notifications
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_notification($value);

	/**
	* Get the extension property: Cron Tasks
	*
	* @return bool
	*/
	public function get_ext_cron();

	/**
	* Set the extension property: Cron Tasks
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_cron($value);

	/**
	* Get the extension property: Console Commands
	*
	* @return bool
	*/
	public function get_ext_console();

	/**
	* Set the extension property: Console Commands
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_console($value);

	/**
	* Get the extension property: ext.php
	*
	* @return bool
	*/
	public function get_ext_ext_php();

	/**
	* Set the extension property: ext.php
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_ext_ext_php($value);

	/**
	* Get the style property: Number of Presets
	*
	* @return int
	*/
	public function get_style_presets();

	/**
	* Set the style property: Number of Presets
	*
	* @param int				$value	Number of presets
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_presets($value);

	/**
	* Get the style property: All Presets in One Style
	*
	* @return bool
	*/
	public function get_style_presets_aio();

	/**
	* Set the style property: All Presets in One Style
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_presets_aio($value);

	/**
	* Get the style property: Source Files
	*
	* @return bool
	*/
	public function get_style_source();

	/**
	* Set the style property: Source Files
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_source($value);

	/**
	* Get the style property: Responsive Support
	*
	* @return bool
	*/
	public function get_style_responsive();

	/**
	* Set the style property: Responsive Support
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_responsive($value);

	/**
	* Get the style property: Bootstrap Support
	*
	* @return bool
	*/
	public function get_style_bootstrap();

	/**
	* Set the style property: Bootstrap Support
	*
	* @param bool				$value	true: yes; false: no
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_style_bootstrap($value);

	/**
	* Get the language package property: ISO code
	*
	* @return string
	*/
	public function get_lang_iso();

	/**
	* Set the language package property: ISO code
	*
	* @param string				$text	2-letter language ISO code
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function set_lang_iso($text);

	/**
	* Get the tool property: OS Support
	*
	* @return int
	*/
	public function get_tool_os();

	/**
	* Set the tool property: OS Support
	*
	* @param int				$value	OS constant value
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_tool_os($value);

	/**
	* Get the item price
	*
	* @return int
	*/
	public function get_price();

	/**
	* Set the item price
	*
	* @param int				$value	Item price
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_price($value);

	/**
	* Get the item URL
	*
	* @return string
	*/
	public function get_url();

	/**
	* Set the item URL
	*
	* @param string				$text	Item URL
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_url($text);

	/**
	* Get the item GitHub URL
	*
	* @return string
	*/
	public function get_github();

	/**
	* Set the item GitHub URL
	*
	* @param string				$text	Item GitHub URL
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_github($text);

	/**
	* Get item display setting in template
	*
	* @return bool
	*/
	public function get_enable();

	/**
	* Set item display setting in template
	*
	* @param bool				$value	true: enable; false: disable
	* @return bb_item_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable($value);

	/**
	* Get the number of downloads
	*
	* @return int
	*/
	public function get_downloads();

	/**
	* Get the number of likes
	*
	* @return int
	*/
	public function get_likes();

	/**
	* Get the time of adding item
	*
	* @return int
	*/
	public function get_added();

	/**
	* Set the time of adding item
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function set_added();

	/**
	* Get the time of updating item
	*
	* @return int
	*/
	public function get_updated();

	/**
	* Set the time of updating item
	*
	* @return bb_item_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function set_updated();
}
