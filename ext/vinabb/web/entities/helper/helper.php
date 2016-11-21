<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\helper;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* Controller for the entity helper
*/
class helper implements helper_interface
{
	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/**
	* Constructor
	*
	* @param ContainerInterface					$container	Container object
	* @param \phpbb\db\driver\driver_interface	$db			Database object
	*/
	public function __construct(ContainerInterface $container, \phpbb\db\driver\driver_interface $db)
	{
		$this->container = $container;
		$this->db = $db;
	}

	/**
	* Check the existing language ISO code
	*
	* @param string $text 2-letter language ISO code
	* @return bool
	*/
	public function check_lang_iso($text)
	{
		return $this->check_column_for_name(LANG_TABLE, 'lang_iso', $text);
	}

	/**
	* Check the existing group
	*
	* @param int $id Group ID
	* @return bool
	*/
	public function check_group_id($id)
	{
		return $this->check_column_for_id(GROUPS_TABLE, 'group_id', $id);
	}

	/**
	* Check the existing user
	*
	* @param int $id User ID
	* @return bool
	*/
	public function check_user_id($id)
	{
		return $this->check_column_for_id(USERS_TABLE, 'user_id', $id);
	}

	/**
	* Check the existing username
	*
	* @param string $text	Username
	* @param int	$id		User ID
	* @return bool
	*/
	public function check_username($text, $id = 0)
	{
		$extra = ['user_id'	=> ['type' => 'int', 'value' => $id]];

		return $this->check_column_for_name(USERS_TABLE, 'username_clean', $text, $extra);
	}

	/**
	* Check the existing forum
	*
	* @param int $id Forum ID
	* @return bool
	*/
	public function check_forum_id($id)
	{
		return $this->check_column_for_id(FORUMS_TABLE, 'forum_id', $id);
	}

	/**
	* Check the existing topic
	*
	* @param int $id Topic ID
	* @return bool
	*/
	public function check_topic_id($id)
	{
		return $this->check_column_for_id(TOPICS_TABLE, 'topic_id', $id);
	}

	/**
	* Check the existing post
	*
	* @param int $id Post ID
	* @return bool
	*/
	public function check_post_id($id)
	{
		return $this->check_column_for_id(POSTS_TABLE, 'post_id', $id);
	}

	/**
	* Check the existing post icon
	*
	* @param int $id Post icon ID
	* @return bool
	*/
	public function check_post_icon_id($id)
	{
		return $this->check_column_for_id(ICONS_TABLE, 'icons_id', $id);
	}

	/**
	* Check the existing smiley
	*
	* @param int $id Smiley ID
	* @return bool
	*/
	public function check_smiley_id($id)
	{
		return $this->check_column_for_id(SMILIES_TABLE, 'smiley_id', $id);
	}

	/**
	* Check the existing smiley code
	*
	* @param string $text	Smiley code
	* @param int	$id		Smiley ID
	* @return bool
	*/
	public function check_smiley_code($text, $id = 0)
	{
		$extra = ['smiley_id'	=> ['type' => 'int', 'value' => $id]];

		return $this->check_column_for_name(SMILIES_TABLE, 'code', $text, $extra);
	}

	/**
	* Check the existing news category
	*
	* @param int $id Category ID
	* @return bool
	*/
	public function check_portal_cat_id($id)
	{
		return $this->check_column_for_id($this->container->getParameter('vinabb.web.tables.portal_categories'), 'cat_id', $id);
	}

	/**
	* Check the existing news category name
	*
	* @param string $text	Category name
	* @param int	$id		Category ID
	* @return bool
	*/
	public function check_portal_cat_name($text, $id = 0)
	{
		$extra = ['cat_id'	=> ['type' => 'int', 'value' => $id]];

		return $this->check_column_for_name($this->container->getParameter('vinabb.web.tables.portal_categories'), 'cat_name', $text, $extra);
	}

	/**
	* Check the existing Vietnamese news category name
	*
	* @param string $text	Vietnamese category name
	* @param int	$id		Category ID
	* @return bool
	*/
	public function check_portal_cat_name_vi($text, $id = 0)
	{
		$extra = ['cat_id'	=> ['type' => 'int', 'value' => $id]];

		return $this->check_column_for_name($this->container->getParameter('vinabb.web.tables.portal_categories'), 'cat_name_vi', $text, $extra);
	}

	/**
	* Check the existing news category varname
	*
	* @param string $text	Category varname
	* @param int	$id		Category ID
	* @return bool
	*/
	public function check_portal_cat_varname($text, $id = 0)
	{
		$extra = ['cat_id'	=> ['type' => 'int', 'value' => $id]];

		return $this->check_column_for_name($this->container->getParameter('vinabb.web.tables.portal_categories'), 'cat_varname', $text, $extra);
	}

	/**
	* Check the existing article
	*
	* @param int $id Article ID
	* @return bool
	*/
	public function check_portal_article_id($id)
	{
		return $this->check_column_for_id($this->container->getParameter('vinabb.web.tables.portal_articles'), 'article_id', $id);
	}

	/**
	* Check the existing BB category
	*
	* @param int	$bb_type	phpBB resource type
	* @param int	$id			Category ID
	* @return bool
	*/
	public function check_bb_cat_id($bb_type, $id)
	{
		$extra = ['bb_type'	=> ['type' => 'int', 'value' => $bb_type]];

		return $this->check_column_for_id($this->container->getParameter('vinabb.web.tables.bb_categories'), 'cat_id', $id, $extra);
	}

	/**
	* Check the existing BB category name
	*
	* @param int	$bb_type	phpBB resource type
	* @param string $text		Category name
	* @param int	$id			Category ID
	* @return bool
	*/
	public function check_bb_cat_name($bb_type, $text, $id = 0)
	{
		$extra = [
			'bb_type'	=> ['type' => 'int', 'value' => $bb_type],
			'cat_id'	=> ['type' => 'int', 'value' => $id]
		];

		return $this->check_column_for_name($this->container->getParameter('vinabb.web.tables.bb_categories'), 'cat_name', $text, $extra);
	}

	/**
	* Check the existing Vietnamese BB category name
	*
	* @param int	$bb_type	phpBB resource type
	* @param string $text		Vietnamese category name
	* @param int	$id			Category ID
	* @return bool
	*/
	public function check_bb_cat_name_vi($bb_type, $text, $id = 0)
	{
		$extra = [
			'bb_type'	=> ['type' => 'int', 'value' => $bb_type],
			'cat_id'	=> ['type' => 'int', 'value' => $id]
		];

		return $this->check_column_for_name($this->container->getParameter('vinabb.web.tables.bb_categories'), 'cat_name_vi', $text, $extra);
	}

	/**
	* Check the existing BB category varname
	*
	* @param int	$bb_type	phpBB resource type
	* @param string $text		Category varname
	* @param int	$id			Category ID
	* @return bool
	*/
	public function check_bb_cat_varname($bb_type, $text, $id = 0)
	{
		$extra = [
			'bb_type'	=> ['type' => 'int', 'value' => $bb_type],
			'cat_id'	=> ['type' => 'int', 'value' => $id]
		];

		return $this->check_column_for_name($this->container->getParameter('vinabb.web.tables.bb_categories'), 'cat_varname', $text, $extra);
	}

	/**
	* Check the existing BB item varname
	*
	* @param int	$bb_type	phpBB resource type
	* @param string $text		Item varname
	* @param int	$id			Item ID
	* @return bool
	*/
	public function check_bb_item_varname($bb_type, $text, $id = 0)
	{
		$extra = [
			'bb_type'	=> ['type' => 'int', 'value' => $bb_type],
			'item_id'	=> ['type' => 'int', 'value' => $id]
		];

		return $this->check_column_for_name($this->container->getParameter('vinabb.web.tables.bb_items'), 'item_varname', $text, $extra);
	}

	/**
	* Check the existing BB author
	*
	* @param int $id BB author ID
	* @return bool
	*/
	public function check_bb_author_id($id)
	{
		return $this->check_column_for_id($this->container->getParameter('vinabb.web.tables.bb_authors'), 'author_id', $id);
	}

	/**
	* Check the existing BB author name
	*
	* @param string $text	Author name
	* @param int	$id		Author ID
	* @return bool
	*/
	public function check_bb_author_name($text, $id = 0)
	{
		$extra = ['author_id'	=> ['type' => 'int', 'value' => $id]];

		return $this->check_column_for_name($this->container->getParameter('vinabb.web.tables.bb_authors'), 'author_name', $text, $extra);
	}

	/**
	* Check the existing page varname
	*
	* @param string $text	Page varname
	* @param int	$id		Page ID
	* @return bool
	*/
	public function check_page_varname($text, $id = 0)
	{
		$extra = ['page_id'	=> ['type' => 'int', 'value' => $id]];

		return $this->check_column_for_name($this->container->getParameter('vinabb.web.tables.pages'), 'page_varname', $text, $extra);
	}

	/**
	* Check the existing ID
	*
	* @param string	$table	Table name
	* @param string	$column	Column name
	* @param int	$id		ID value
	* @param array	$extra	Extra data for query AND...
	* @return bool
	*/
	protected function check_column_for_id($table = USERS_TABLE, $column = 'user_id', $id = 0, $extra = [])
	{
		$sql_and = sizeof($extra) ? $this->build_sql_and($extra) : '';

		$sql = 'SELECT 1
			FROM ' . $table . "
			WHERE $column = " . (int) $id . "
				$sql_and";
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return (bool) $row;
	}

	/**
	* Check the existing name
	*
	* @param string	$table	Table name
	* @param string	$column	Column name
	* @param string	$text	Name value
	* @param array	$extra	Extra data for query AND...
	* @return bool
	*/
	protected function check_column_for_name($table = USERS_TABLE, $column = 'username_clean', $text = '', $extra = [])
	{
		$sql_and = sizeof($extra) ? $this->build_sql_and($extra) : '';

		$sql = 'SELECT 1
			FROM ' . $table . "
			WHERE $column = '" . $this->db->sql_escape($text) . "'
				$sql_and";
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return (bool) $row;
	}

	/**
	* Generate SQL AND query text after "WHERE ..."
	*
	* @param array $data Data array
	* @return string
	*/
	protected function build_sql_and($data)
	{
		$sql_and = '';

		foreach ($data as $field_name => $field_data)
		{
			if ($field_data['type'] == 'int')
			{
				$sql_and .= " AND $field_name = " . (int) $field_data['value'];
			}
			else
			{
				$sql_and .= " AND $field_name = '" . $this->db->sql_escape($field_data['value']) . "'";
			}
		}

		return $sql_and;
	}
}
