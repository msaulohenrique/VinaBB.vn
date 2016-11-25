<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single user
*/
interface user_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Group ID
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\invalid_argument
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return user_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return user_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the user_id
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the group ID
	*
	* @return int
	*/
	public function get_group_id();

	/**
	* Set the group ID
	*
	* @param int				$id		Group ID
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_group_id($id);

	/**
	* Get the username
	*
	* @return string
	*/
	public function get_username();

	/**
	* Get the clean username for searching
	*
	* @return string
	*/
	public function get_username_clean();

	/**
	* Get the user type
	*
	* @return int
	*/
	public function get_type();

	/**
	* Set the user type
	*
	* @param int				$value	User type
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_type($value);

	/**
	* Get the user email
	*
	* @return string
	*/
	public function get_email();

	/**
	* Get the user's hashed password
	*
	* @return string
	*/
	public function get_password();

	/**
	* Get the user birthday
	*
	* @return string
	*/
	public function get_birthday();

	/**
	* The user has just newly registered?
	*
	* @return bool
	*/
	public function get_new();

	/**
	* Get the user language
	*
	* @return string
	*/
	public function get_lang();

	/**
	* Set the user language
	*
	* @param string				$text	2-letter language ISO code
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_lang($text);

	/**
	* Get the user style
	*
	* @return int
	*/
	public function get_style();

	/**
	* Set the user style
	*
	* @param int				$id		Style ID
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_style($id);

	/**
	* Get the user timezone
	*
	* @return string
	*/
	public function get_timezone();

	/**
	* Get the user date format
	*
	* @return string
	*/
	public function get_dateformat();

	/**
	* Get the user's posts
	*
	* @return int
	*/
	public function get_posts();

	/**
	* Get the user's new PMs
	*
	* @return int
	*/
	public function get_new_privmsg();

	/**
	* Get the user's unread PMs
	*
	* @return int
	*/
	public function get_unread_privmsg();

	/**
	* Get the user's warnings
	*
	* @return int
	*/
	public function get_warnings();

	/**
	* Get the user's PM rules
	*
	* @return int
	*/
	public function get_message_rules();

	/**
	* Get the last page user visited
	*
	* @return string
	*/
	public function get_lastpage();

	/**
	* Get the user's joined date
	*
	* @return int
	*/
	public function get_regdate();

	/**
	* Get the last time user visited
	*
	* @return int
	*/
	public function get_lastvisit();

	/**
	* Get the last time user marked
	*
	* @return int
	*/
	public function get_lastmark();

	/**
	* Get the last time user searched
	*
	* @return int
	*/
	public function get_last_search();

	/**
	* Get the last time user posted
	*
	* @return int
	*/
	public function get_lastpost_time();

	/**
	* Get the last time user sent PMs
	*
	* @return int
	*/
	public function get_last_privmsg();

	/**
	* Get the last time user got warnings
	*
	* @return int
	*/
	public function get_last_warning();

	/**
	* Get the last time user changed password
	*
	* @return int
	*/
	public function get_passchg();

	/**
	* Get the last time user got email sent from board
	*
	* @return int
	*/
	public function get_emailtime();

	/**
	* Get the last time user got reminds
	*
	* @return int
	*/
	public function get_reminded_time();

	/**
	* Get the last time user to be inactived
	*
	* @return int
	*/
	public function get_inactive_time();

	/**
	* Get user's Jabber account
	*
	* @return string
	*/
	public function get_jabber();

	/**
	* Get the user option: Notify when have new replies
	*
	* @return bool
	*/
	public function get_notify();

	/**
	* Get the user option: Notify when have new PMs
	*
	* @return bool
	*/
	public function get_notify_pm();

	/**
	* Get the user option: Notification method
	*
	* @return int
	*/
	public function get_notify_type();

	/**
	* Set the user option: Action if a PM folder is full
	*
	* @param int				$value	Notify type
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_notify_type($value);

	/**
	* Get the user option: Receive PMs from other users
	*
	* @return bool
	*/
	public function get_allow_pm();

	/**
	* Get the user option: Show online status
	*
	* @return bool
	*/
	public function get_allow_viewonline();

	/**
	* Get the user option: Receive emails from other users
	*
	* @return bool
	*/
	public function get_allow_viewemail();

	/**
	* Get the user option: Receive emails from administrators
	*
	* @return bool
	*/
	public function get_allow_massemail();

	/**
	* Get the user option: Action if a PM folder is full
	*
	* @return int
	*/
	public function get_full_folder();

	/**
	* Set the user option: Action if a PM folder is full
	*
	* @param int				$value	Action type
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_full_folder($value);

	/**
	* Get the user option: Display topics from x days
	*
	* @return int
	*/
	public function get_topic_show_days();

	/**
	* Get the user option: Sorting order of topics
	*
	* @return string
	*/
	public function get_topic_sortby_type();

	/**
	* Get the user option: Sorting direction of topics
	*
	* @return string
	*/
	public function get_topic_sortby_dir();

	/**
	* Get the user option: Display posts from x days
	*
	* @return int
	*/
	public function get_post_show_days();

	/**
	* Get the user option: Sorting order of posts
	*
	* @return string
	*/
	public function get_post_sortby_type();

	/**
	* Get the user option: Sorting direction of posts
	*
	* @return string
	*/
	public function get_post_sortby_dir();

	/**
	* Get user's registered IP
	*
	* @return string
	*/
	public function get_ip();

	/**
	* Get user's email hash
	*
	* @return int
	*/
	public function get_email_hash();

	/**
	* Get user's form hash
	*
	* @return string
	*/
	public function get_form_salt();

	/**
	* Get user permissions
	*
	* @return string
	*/
	public function get_permissions();

	/**
	* User has switched permissions to who?
	*
	* @return string
	*/
	public function get_perm_from();

	/**
	* Get user's activation key
	*
	* @return string
	*/
	public function get_actkey();

	/**
	* Get user's last confirm key
	*
	* @return string
	*/
	public function get_last_confirm_key();

	/**
	* Get number of failed logins
	*
	* @return int
	*/
	public function get_login_attempts();

	/**
	* Get user's new reset password
	*
	* @return string
	*/
	public function get_newpasswd();

	/**
	* Get number of reminders
	*
	* @return int
	*/
	public function get_reminded();

	/**
	* Get the inactive reason
	*
	* @return int
	*/
	public function get_inactive_reason();

	/**
	* Set the inactive reason
	*
	* @param int				$value	Inactive reason constant value
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_inactive_reason($value);

	/**
	* Get the user avatar image
	*
	* @return string
	*/
	public function get_avatar();

	/**
	* Set the user avatar image
	*
	* @param string				$text	Avatar image URL
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_avatar($text);

	/**
	* Get the user avatar type
	*
	* @return string
	*/
	public function get_avatar_type();

	/**
	* Set the user avatar type
	*
	* @param int				$value	Avatar type
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_avatar_type($value);

	/**
	* Get the user avatar width
	*
	* @return int
	*/
	public function get_avatar_width();

	/**
	* Get the user avatar height
	*
	* @return int
	*/
	public function get_avatar_height();

	/**
	* Get the user rank
	*
	* @return int
	*/
	public function get_rank();

	/**
	* Set the user rank
	*
	* @param int				$id		Rank ID
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_rank($id);

	/**
	* Get the username color
	*
	* @return string
	*/
	public function get_colour();

	/**
	* Set the username color
	*
	* @param string				$text	6-char HEX code without #
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_colour($text);

	/**
	* Get user signature for edit
	*
	* @return string
	*/
	public function get_sig_for_edit();

	/**
	* Get user signature for display
	*
	* @param bool $censor True to censor the content
	* @return string
	*/
	public function get_sig_for_display($censor = true);
}
