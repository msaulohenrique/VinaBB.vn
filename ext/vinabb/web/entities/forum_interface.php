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
	* @throws \vinabb\web\exceptions\invalid_argument
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
	* Get the forum ID
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the parent forum ID
	*
	* @return int
	*/
	public function get_parent_id();

	/**
	* Set the parent forum ID
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
	* Get the forum language
	*
	* @return string
	*/
	public function get_lang();

	/**
	* Set the forum language
	*
	* @param string				$text	2-letter language ISO code
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_lang($text);

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

	/**
	* Get the number of topics per page in this forum
	*
	* @return int
	*/
	public function get_topics_per_page();

	/**
	* Set the number of topics per page in this forum
	*
	* @param int				$value	Number of topics per page
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_topics_per_page($value);

	/**
	* Get the number of approved topics
	*
	* @return int
	*/
	public function get_topics_approved();

	/**
	* Set the number of approved topics
	*
	* @param int				$value	Number of approved topics
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_topics_approved($value);

	/**
	* Get the number of disapproved topics
	*
	* @return int
	*/
	public function get_topics_unapproved();

	/**
	* Set he number of disapproved topics
	*
	* @param int				$value	Number of disapproved topics
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_topics_unapproved($value);

	/**
	* Get the number of soft-deleted topics
	*
	* @return int
	*/
	public function get_topics_softdeleted();

	/**
	* Set the number of soft-deleted topics
	*
	* @param int				$value	Number of soft-deleted topics
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_topics_softdeleted($value);

	/**
	* Get the number of approved posts
	*
	* @return int
	*/
	public function get_posts_approved();

	/**
	* Set the number of approved posts
	*
	* @param int				$value	Number of approved posts
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_posts_approved($value);

	/**
	* Get the number of disapproved posts
	*
	* @return int
	*/
	public function get_posts_unapproved();

	/**
	* Set the number of disapproved posts
	*
	* @param int				$value	Number of disapproved posts
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_posts_unapproved($value);

	/**
	* Get the number of soft-deleted posts
	*
	* @return int
	*/
	public function get_posts_softdeleted();

	/**
	* Set the number of soft-deleted posts
	*
	* @param int				$value	Number of soft-deleted posts
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_posts_softdeleted($value);

	/**
	* Get the forum redirect link
	*
	* @return string
	*/
	public function get_link();

	/**
	* Set the forum redirect link
	*
	* @param string				$text	URL
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_link($text);

	/**
	* Get the forum password
	*
	* @return string
	*/
	public function get_password();

	/**
	* Set the forum password
	*
	* @param string				$text	Forum password
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_password($text);

	/**
	* Get the forum style
	*
	* @return int
	*/
	public function get_style();

	/**
	* Set the forum style
	*
	* @param string				$id		Style ID
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_style($id);

	/**
	* Get the forum image
	*
	* @return string
	*/
	public function get_image();

	/**
	* Set the forum image
	*
	* @param string				$text	Image URL
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_image($text);

	/**
	* Get forum flags
	*
	* @return int
	*/
	public function get_flags();

	/**
	* Set forum flags
	*
	* @param int				$value	Forum flags
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_flags($value);

	/**
	* Get forum options
	*
	* @return int
	*/
	public function get_forum_options();

	/**
	* Set forum options
	*
	* @param int				$value	Forum options
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_forum_options($value);

	/**
	* Get the forum setting: Display on the board page
	*
	* @return bool
	*/
	public function get_display_on_index();

	/**
	* Set the forum setting: Display on the board page
	*
	* @param int				$value	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_display_on_index($value);

	/**
	* Get the forum setting: Enable creating search indexes
	*
	* @return bool
	*/
	public function get_enable_indexing();

	/**
	* Set the forum setting: Enable creating search indexes
	*
	* @param bool				$value	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_indexing($value);

	/**
	* Get the forum setting: Enable topic/post icons
	*
	* @return bool
	*/
	public function get_enable_icons();

	/**
	* Set the forum setting: Enable topic/post icons
	*
	* @param bool				$value	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_icons($value);

	/**
	* Get the forum setting: Display list of subforums
	*
	* @return bool
	*/
	public function get_display_subforum_list();

	/**
	* Set the forum setting: Display list of subforums
	*
	* @param bool				$value	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_display_subforum_list($value);

	/**
	* Get the last post ID
	*
	* @return int
	*/
	public function get_last_post_id();

	/**
	* Set the last post ID
	*
	* @param int				$id		Post ID
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_last_post_id($id);

	/**
	* Get the last poster ID
	*
	* @return int
	*/
	public function get_last_poster_id();

	/**
	* Set the last poster ID
	*
	* @param int				$id		User ID
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_last_poster_id($id);

	/**
	* Get the last poster username
	*
	* @return string
	*/
	public function get_last_poster_name();

	/**
	* Set the last poster username
	*
	* @param string				$text	Username
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_last_poster_name($text);

	/**
	* Get the last poster username color
	*
	* @return string
	*/
	public function get_last_poster_colour();

	/**
	* Set the last poster username color
	*
	* @param string				$text	6-char HEX code without #
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_last_poster_colour($text);

	/**
	* Get the last post subject
	*
	* @return string
	*/
	public function get_last_post_subject();

	/**
	* Set the last post subject
	*
	* @param string				$text	Post subject
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_last_post_subject($text);

	/**
	* Get the forum's last post time
	*
	* @return int
	*/
	public function get_last_post_time();

	/**
	* Set the forum's last post time
	*
	* @param int				$value	UNIX timestamp
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_last_post_time($value);

	/**
	* Get the forum setting: Auto-pruning
	*
	* @return bool
	*/
	public function get_enable_prune();

	/**
	* Set the forum setting: Auto-pruning
	*
	* @param bool				$value	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_prune($value);

	/**
	* Get the forum setting: Auto-pruning shadow topics
	*
	* @return bool
	*/
	public function get_enable_shadow_prune();

	/**
	* Set the forum setting: Auto-pruning shadow topics
	*
	* @param bool				$value	true: enable; false: disable
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable_shadow_prune($value);

	/**
	* Get number of age-days after the topics will be removed
	*
	* @return int
	*/
	public function get_prune_days();

	/**
	* Set number of age-days after the topics will be removed
	*
	* @param int				$value	Number of days
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_prune_days($value);

	/**
	* Get number of days between pruning times
	*
	* @return int
	*/
	public function get_prune_freq();

	/**
	* Set number of days between pruning times
	*
	* @param int				$value	Number of days
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_prune_freq($value);

	/**
	* Get the beginning time of next pruning
	*
	* @return int
	*/
	public function get_prune_next();

	/**
	* Set the beginning time of next pruning
	*
	* @param int				$value	UNIX timestamp
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_prune_next($value);

	/**
	* Get number of age-days since the last view, then the topics will be removed
	*
	* @return int
	*/
	public function get_prune_viewed();

	/**
	* Set number of age-days since the last view, then the topics will be removed
	*
	* @param int				$value	Number of days
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_prune_viewed($value);

	/**
	* Get number of age-days after the shadow topics will be removed
	*
	* @return int
	*/
	public function get_prune_shadow_days();

	/**
	* Set number of age-days after the shadow topics will be removed
	*
	* @param int				$value	Number of days
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_prune_shadow_days($value);

	/**
	* Get number of days between pruning times of shadow topics
	*
	* @return int
	*/
	public function get_prune_shadow_freq();

	/**
	* Set number of days between pruning times of shadow topics
	*
	* @param int				$value	Number of days
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_prune_shadow_freq($value);

	/**
	* Get the beginning time of next pruning shadow topics
	*
	* @return int
	*/
	public function get_prune_shadow_next();

	/**
	* Set the beginning time of next pruning shadow topics
	*
	* @param int				$value	UNIX timestamp
	* @return forum_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_prune_shadow_next($value);

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
}
