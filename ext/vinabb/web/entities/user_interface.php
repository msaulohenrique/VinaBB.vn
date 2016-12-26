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
	* @param int				$id		User ID
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
	* Get the user ID
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
	* Set the user timezone
	*
	* @param string				$text	UNIX timezone
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_timezone($text);

	/**
	* Get the user date format
	*
	* @return string
	*/
	public function get_dateformat();

	/**
	* Set the user date format
	*
	* @param string				$text	PHP date format
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_dateformat($text);

	/**
	* Get the user's posts
	*
	* @return int
	*/
	public function get_posts();

	/**
	* Set the user's posts
	*
	* @param int				$value	Number of posts
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_posts($value);

	/**
	* Get the user's new PMs
	*
	* @return int
	*/
	public function get_new_privmsg();

	/**
	* Set the user new PMs
	*
	* @param int				$value	Number of new PMs
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_new_privmsg($value);

	/**
	* Get the user's unread PMs
	*
	* @return int
	*/
	public function get_unread_privmsg();

	/**
	* Set the user's unread PMs
	*
	* @param int				$value	Number of unread PMs
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_unread_privmsg($value);

	/**
	* Get the user's warnings
	*
	* @return int
	*/
	public function get_warnings();

	/**
	* Set the user's warnings
	*
	* @param int				$value	Number of warnings
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_warnings($value);

	/**
	* Get the user's PM rules
	*
	* @return int
	*/
	public function get_message_rules();

	/**
	* Set the user's PM rules
	*
	* @param int				$value	Number of PM rules
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_message_rules($value);

	/**
	* Get the last page user visited
	*
	* @return string
	*/
	public function get_lastpage();

	/**
	* Set the last page user visited
	*
	* @param string				$text	Last page
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_lastpage($text);

	/**
	* Get the user's joined date
	*
	* @return int
	*/
	public function get_regdate();

	/**
	* Set the user's joined date
	*
	* @param int				$value	UNIX timestamp
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_regdate($value);

	/**
	* Get the last time user visited
	*
	* @return int
	*/
	public function get_lastvisit();

	/**
	* Set the last time user visited
	*
	* @param int				$value	UNIX timestamp
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_lastvisit($value);

	/**
	* Get the last time user marked
	*
	* @return int
	*/
	public function get_lastmark();

	/**
	* Set the last time user marked
	*
	* @param int				$value	UNIX timestamp
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_lastmark($value);

	/**
	* Get the last time user searched
	*
	* @return int
	*/
	public function get_last_search();

	/**
	* Set the last time user searched
	*
	* @param int				$value	UNIX timestamp
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_last_search($value);

	/**
	* Get the last time user posted
	*
	* @return int
	*/
	public function get_lastpost_time();

	/**
	* Set the last time user posted
	*
	* @param int				$value	UNIX timestamp
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_lastpost_time($value);

	/**
	* Get the last time user sent PMs
	*
	* @return int
	*/
	public function get_last_privmsg();

	/**
	* Set the last time user sent PMs
	*
	* @param int				$value	UNIX timestamp
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_last_privmsg($value);

	/**
	* Get the last time user got warnings
	*
	* @return int
	*/
	public function get_last_warning();

	/**
	* Set the last time user got warnings
	*
	* @param int				$value	UNIX timestamp
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_last_warning($value);

	/**
	* Get the last time user changed password
	*
	* @return int
	*/
	public function get_passchg();

	/**
	* Set the last time user changed password
	*
	* @param int				$value	UNIX timestamp
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_passchg($value);

	/**
	* Get the last time user got email sent from board
	*
	* @return int
	*/
	public function get_emailtime();

	/**
	* Set the last time user got email sent from board
	*
	* @param int				$value	UNIX timestamp
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_emailtime($value);

	/**
	* Get the last time user got reminds
	*
	* @return int
	*/
	public function get_reminded_time();

	/**
	* Set the last time user got reminds
	*
	* @param int				$value	UNIX timestamp
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_reminded_time($value);

	/**
	* Get the last time user to be inactived
	*
	* @return int
	*/
	public function get_inactive_time();

	/**
	* Set the last time user to be inactived
	*
	* @param int				$value	UNIX timestamp
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_inactive_time($value);

	/**
	* Get user's Jabber account
	*
	* @return string
	*/
	public function get_jabber();

	/**
	* Set user's Jabber account
	*
	* @param string				$text	Jabber username
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_jabber($text);

	/**
	* Get the user option: Notify when have new replies
	*
	* @return bool
	*/
	public function get_notify();

	/**
	* Set the user option: Notify when have new replies
	*
	* @param bool				$value	true: yes; false: no
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_notify($value);

	/**
	* Get the user option: Notify when have new PMs
	*
	* @return bool
	*/
	public function get_notify_pm();

	/**
	* Set the user option: Notify when have new PMs
	*
	* @param bool				$value	true: yes; false: no
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_notify_pm($value);

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
	* Set the user option: Receive PMs from other users
	*
	* @param bool				$value	true: yes; false: no
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_allow_pm($value);

	/**
	* Get the user option: Show online status
	*
	* @return bool
	*/
	public function get_allow_viewonline();

	/**
	* Set the user option: Show online status
	*
	* @param bool				$value	true: yes; false: no
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_allow_viewonline($value);

	/**
	* Get the user option: Receive emails from other users
	*
	* @return bool
	*/
	public function get_allow_viewemail();

	/**
	* Set the user option: Receive emails from other users
	*
	* @param bool				$value	true: yes; false: no
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_allow_viewemail($value);

	/**
	* Get the user option: Receive emails from administrators
	*
	* @return bool
	*/
	public function get_allow_massemail();

	/**
	* Set the user option: Receive emails from administrators
	*
	* @param bool				$value	true: yes; false: no
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_allow_massemail($value);

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
	* Set the user option: Display topics from x days
	*
	* @param int				$value	Number of days
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_topic_show_days($value);

	/**
	* Get the user option: Sorting field of topics
	*
	* @return string
	*/
	public function get_topic_sortby_type();

	/**
	* Set the user option: Sorting field of topics
	*
	* @param string				$text	Jabber
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_topic_sortby_type($text);

	/**
	* Get the user option: Sorting direction of topics
	*
	* @return string
	*/
	public function get_topic_sortby_dir();

	/**
	* Set the user option: Sorting direction of topics
	*
	* @param string				$text	Direction key
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_topic_sortby_dir($text);

	/**
	* Get the user option: Display posts from x days
	*
	* @return int
	*/
	public function get_post_show_days();

	/**
	* Set the user option: Display posts from x days
	*
	* @param int				$value	Number of days
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_post_show_days($value);

	/**
	* Get the user option: Sorting field of posts
	*
	* @return string
	*/
	public function get_post_sortby_type();

	/**
	* Set the user option: Sorting field of posts
	*
	* @param string				$text	Field key
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_post_sortby_type($text);

	/**
	* Get the user option: Sorting direction of posts
	*
	* @return string
	*/
	public function get_post_sortby_dir();

	/**
	* Set the user option: Sorting direction of posts
	*
	* @param string				$text	Direction key
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_post_sortby_dir($text);

	/**
	* Get user's registered IP
	*
	* @return string
	*/
	public function get_ip();

	/**
	* Set user's registered IP
	*
	* @param string				$text	User IP
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_ip($text);

	/**
	* Get user's email hash
	*
	* @return int
	*/
	public function get_email_hash();

	/**
	* Set user's email hash
	*
	* @param int				$value	Email hash
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_email_hash($value);

	/**
	* Get user's form hash
	*
	* @return string
	*/
	public function get_form_salt();

	/**
	* Set user's form hash
	*
	* @param string				$text	Form hash
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_form_salt($text);

	/**
	* Get user permissions
	*
	* @return string
	*/
	public function get_permissions();

	/**
	* Set user permissions
	*
	* @param string				$text	Permissions
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_permissions($text);

	/**
	* User has switched permissions to who?
	*
	* @return int
	*/
	public function get_perm_from();

	/**
	* Set whom the user has switched permissions to
	*
	* @param int				$id		User ID
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_perm_from($id);

	/**
	* Get user's activation key
	*
	* @return string
	*/
	public function get_actkey();

	/**
	* Set user's activation key
	*
	* @param string				$text	Activation key
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_actkey($text);

	/**
	* Get user's last confirm key
	*
	* @return string
	*/
	public function get_last_confirm_key();

	/**
	* Set user's last confirm key
	*
	* @param string				$text	Confirm key
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_last_confirm_key($text);

	/**
	* Get number of failed logins
	*
	* @return int
	*/
	public function get_login_attempts();

	/**
	* Set number of failed logins
	*
	* @param int				$value	Number of failed logins
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_login_attempts($value);

	/**
	* Get user's new reset password
	*
	* @return string
	*/
	public function get_newpasswd();

	/**
	* Set user's new reset password
	*
	* @param string				$text	New password
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_newpasswd($text);

	/**
	* Get number of reminders
	*
	* @return int
	*/
	public function get_reminded();

	/**
	* Set number of reminders
	*
	* @param int				$value	Email
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_reminded($value);

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
	* Set the user avatar width
	*
	* @param int				$value	Avatar width
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_avatar_width($value);

	/**
	* Get the user avatar height
	*
	* @return int
	*/
	public function get_avatar_height();

	/**
	* Set the user avatar height
	*
	* @param int				$value	Avatar height
	* @return user_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_avatar_height($value);

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
