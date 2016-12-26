<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single topic
*/
interface topic_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int				$id		Topic ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\invalid_argument
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return topic_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return topic_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the topic ID
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the forum ID
	*
	* @return int
	*/
	public function get_forum_id();

	/**
	* Set the forum ID
	*
	* @param int				$id		Forum ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_forum_id($id);

	/**
	* Get the first post ID
	*
	* @return int
	*/
	public function get_first_post_id();

	/**
	* Set the first post ID
	*
	* @param int				$id		Post ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_first_post_id($id);

	/**
	* Get the topic icon
	*
	* @return int
	*/
	public function get_icon_id();

	/**
	* Set the topic icon
	*
	* @param int				$id		Icon ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_icon_id($id);

	/**
	* Get the poster ID
	*
	* @return int
	*/
	public function get_poster();

	/**
	* Set the poster ID
	*
	* @param int				$id		User ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_poster($id);

	/**
	* Get the poster username
	*
	* @return string
	*/
	public function get_first_poster_name();

	/**
	* Set the poster username
	*
	* @param string				$text	Username
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_first_poster_name($text);

	/**
	* Get the poster username color
	*
	* @return string
	*/
	public function get_first_poster_colour();

	/**
	* Set the poster username color
	*
	* @param string				$text	6-char HEX code without #
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_first_poster_colour($text);

	/**
	* Get the topic title
	*
	* @return string
	*/
	public function get_title();

	/**
	* Set the topic title
	*
	* @param string				$text	Topic title
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_title($text);

	/**
	* Get the topic SEO title
	*
	* @return string
	*/
	public function get_title_seo();

	/**
	* Set the topic SEO title
	*
	* @param string				$text	Topic SEO title
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_title_seo($text);

	/**
	* Get the topic type
	*
	* @return int
	*/
	public function get_type();

	/**
	* Set the topic type
	*
	* @param int				$value	Topic type
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_type($value);

	/**
	* Get the topic status
	*
	* @return int
	*/
	public function get_status();

	/**
	* Set the topic status
	*
	* @param int				$value	Topic status
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function set_status($value);

	/**
	* Get the topic views
	*
	* @return int
	*/
	public function get_views();

	/**
	* Get the number of approved posts
	*
	* @return int
	*/
	public function get_posts_approved();

	/**
	* Set the number of approved posts
	*
	* @param int				$value	Number of posts
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @param int				$value	Number of posts
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @param int				$value	Number of posts
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_posts_softdeleted($value);

	/**
	* Get the topic time
	*
	* @return int
	*/
	public function get_time();

	/**
	* Set the topic time
	*
	* @param int				$value	UNIX timestamp
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_time($value);

	/**
	* Get the topic time limit
	*
	* @return int
	*/
	public function get_time_limit();

	/**
	* Set the topic time limit
	*
	* @param int				$value	UNIX timestamp
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_time_limit($value);

	/**
	* Get the topic visibility
	*
	* @return int
	*/
	public function get_visibility();

	/**
	* Set the topic visibility
	*
	* @param int				$value	Visibility value
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_visibility($value);

	/**
	* Does the topic have attachments?
	*
	* @return bool
	*/
	public function get_attachment();

	/**
	* Set the topic have or have not attachments
	*
	* @param bool				$value	true: yes; false: no
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_attachment($value);

	/**
	* Does the topic have open reports?
	*
	* @return bool
	*/
	public function get_reported();

	/**
	* Set the topic have or have not open reports
	*
	* @param bool				$value	true: yes; false: no
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_reported($value);

	/**
	* Get the old topic ID after moving and leaving shadow
	*
	* @return int
	*/
	public function get_moved_id();

	/**
	* Set the old topic ID after moving and leaving shadow
	*
	* @param int				$id		Topic ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_moved_id($id);

	/**
	* Get the time of bumping topic up
	*
	* @return int
	*/
	public function get_bumped();

	/**
	* Set the time of bumping topic up
	*
	* @param int				$value	UNIX timestamp
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_bumped($value);

	/**
	* Get the user bumped topic up
	*
	* @return int
	*/
	public function get_bumper();

	/**
	* Set the user bumped topic up
	*
	* @param int				$id		User ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_bumper($id);

	/**
	* Get the time of deleting topic
	*
	* @return int
	*/
	public function get_delete_time();

	/**
	* Set the time of deleting topic
	*
	* @param int				$value	UNIX timestamp
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_delete_time($value);

	/**
	* Get the deleted reason
	*
	* @return string
	*/
	public function get_delete_reason();

	/**
	* Set the deleted reason
	*
	* @param string				$text	Reason
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_delete_reason($text);

	/**
	* Get the user deleted topic
	*
	* @return int
	*/
	public function get_delete_user();

	/**
	* Set the user deleted topic
	*
	* @param int				$id		User ID
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_delete_user($id);

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
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
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
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_last_post_subject($text);

	/**
	* Get the topic's last post time
	*
	* @return int
	*/
	public function get_last_post_time();

	/**
	* Set the topic's last post time
	*
	* @param int				$value	UNIX timestamp
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_last_post_time($value);

	/**
	* Get the topic's last view time
	*
	* @return int
	*/
	public function get_last_view_time();

	/**
	* Set the topic's last view time
	*
	* @param int				$value	UNIX timestamp
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_last_view_time($value);

	/**
	* Get the poll title
	*
	* @return string
	*/
	public function get_poll_title();

	/**
	* Set the poll title
	*
	* @param string				$text	Poll title
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_poll_title($text);

	/**
	* Get the poll's staring time
	*
	* @return int
	*/
	public function get_poll_start();

	/**
	* Set the poll's staring time
	*
	* @param int				$value	UNIX timestamp
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_poll_start($value);

	/**
	* Get the poll's ending time
	*
	* @return int
	*/
	public function get_poll_length();

	/**
	* Set the poll's ending time
	*
	* @param int				$value	UNIX timestamp
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_poll_length($value);

	/**
	* Get the maximum options of poll
	*
	* @return int
	*/
	public function get_poll_max_options();

	/**
	* Set the maximum options of poll
	*
	* @param int				$value	Number of options
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_poll_max_options($value);

	/**
	* Get the poll's last voting time
	*
	* @return int
	*/
	public function get_poll_last_vote();

	/**
	* Set the poll's last voting time
	*
	* @param int				$value	UNIX timestamp
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_poll_last_vote($value);

	/**
	* Does the poll allow to change voted options?
	*
	* @return bool
	*/
	public function get_poll_vote_change();

	/**
	* Set the poll option: Allow to change voted options?
	*
	* @param int				$value	true: allow; false: disallow
	* @return topic_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_poll_vote_change($value);
}
