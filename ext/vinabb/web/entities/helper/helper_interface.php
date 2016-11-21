<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\helper;

/**
* Interface for the entity helper
*/
interface helper_interface
{
	/**
	* Check the existing language ISO code
	*
	* @param string $text 2-letter language ISO code
	* @return bool
	*/
	public function check_lang_iso($text);

	/**
	* Check the existing group
	*
	* @param int $id Group ID
	* @return bool
	*/
	public function check_group_id($id);

	/**
	* Check the existing user
	*
	* @param int $id User ID
	* @return bool
	*/
	public function check_user_id($id);

	/**
	* Check the existing username
	*
	* @param string $text	Username
	* @param int	$id		User ID
	* @return bool
	*/
	public function check_username($text, $id = 0);

	/**
	* Check the existing forum
	*
	* @param int $id Forum ID
	* @return bool
	*/
	public function check_forum_id($id);

	/**
	* Check the existing topic
	*
	* @param int $id Topic ID
	* @return bool
	*/
	public function check_topic_id($id);

	/**
	* Check the existing post
	*
	* @param int $id Post ID
	* @return bool
	*/
	public function check_post_id($id);

	/**
	* Check the existing post icon
	*
	* @param int $id Post icon ID
	* @return bool
	*/
	public function check_post_icon_id($id);

	/**
	* Check the existing smiley
	*
	* @param int $id Smiley ID
	* @return bool
	*/
	public function check_smiley_id($id);

	/**
	* Check the existing smiley code
	*
	* @param string $text	Smiley code
	* @param int	$id		Smiley ID
	* @return bool
	*/
	public function check_smiley_code($text, $id = 0);

	/**
	* Check the existing news category
	*
	* @param int $id Category ID
	* @return bool
	*/
	public function check_portal_cat_id($id);

	/**
	* Check the existing news category name
	*
	* @param string $text	Category name
	* @param int	$id		Category ID
	* @return bool
	*/
	public function check_portal_cat_name($text, $id = 0);

	/**
	* Check the existing Vietnamese news category name
	*
	* @param string $text	Vietnamese category name
	* @param int	$id		Category ID
	* @return bool
	*/
	public function check_portal_cat_name_vi($text, $id = 0);

	/**
	* Check the existing news category varname
	*
	* @param string $text	Category varname
	* @param int	$id		Category ID
	* @return bool
	*/
	public function check_portal_cat_varname($text, $id = 0);

	/**
	* Check the existing article
	*
	* @param int $id Article ID
	* @return bool
	*/
	public function check_portal_article_id($id);

	/**
	* Check the existing news category
	*
	* @param int	$bb_type	phpBB resource type
	* @param int	$id			Category ID
	* @return bool
	*/
	public function check_bb_cat_id($bb_type, $id);

	/**
	* Check the existing BB category name
	*
	* @param int	$bb_type	phpBB resource type
	* @param string $text		Category name
	* @param int	$id			Category ID
	* @return bool
	*/
	public function check_bb_cat_name($bb_type, $text, $id = 0);

	/**
	* Check the existing Vietnamese BB category name
	*
	* @param int	$bb_type	phpBB resource type
	* @param string $text		Vietnamese category name
	* @param int	$id			Category ID
	* @return bool
	*/
	public function check_bb_cat_name_vi($bb_type, $text, $id = 0);

	/**
	* Check the existing news category varname
	*
	* @param int	$bb_type	phpBB resource type
	* @param string $text		Category varname
	* @param int	$id			Category ID
	* @return bool
	*/
	public function check_bb_cat_varname($bb_type, $text, $id = 0);

	/**
	* Check the existing BB item varname
	*
	* @param int	$bb_type	phpBB resource type
	* @param string $text		Item varname
	* @param int	$id			Item ID
	* @return bool
	*/
	public function check_bb_item_varname($bb_type, $text, $id = 0);

	/**
	* Check the existing BB author
	*
	* @param int $id BB author ID
	* @return bool
	*/
	public function check_bb_author_id($id);

	/**
	* Check the existing BB author name
	*
	* @param string $text	Author name
	* @param int	$id		Author ID
	* @return bool
	*/
	public function check_bb_author_name($text, $id = 0);

	/**
	* Check the existing page varname
	*
	* @param string $text	Page varname
	* @param int	$id		Page ID
	* @return bool
	*/
	public function check_page_varname($text, $id = 0);
}
