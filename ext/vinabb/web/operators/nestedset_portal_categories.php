<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Nestedset class for news categories
*/
class nestedset_portal_categories extends \phpbb\tree\nestedset
{
	/**
	* Construct
	*
	* @param \phpbb\db\driver\driver_interface	$db			Database object
	* @param \phpbb\lock\db						$lock		Lock the table when moving entities around
	* @param string								$table_name	Table name
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\lock\db $lock, $table_name)
	{
		parent::__construct(
			$db,
			$lock,
			$table_name,
			'PORTAL_CATEGORIES_NESTEDSET_',
			'',
			[],
			[
				'item_id'		=> 'cat_id',
				'parent_id'		=> 'parent_id',
				'left_id'		=> 'left_id',
				'right_id'		=> 'right_id',
				'item_parents'	=> 'cat_parents'
			]
		);
	}

	/**
	* Get the category data from the database
	*
	* @param int $parent_id Category to display rules from, 0 for all
	* @return array Array of rules data from the database
	*/
	public function get_cat_data($parent_id)
	{
		return $parent_id ? $this->get_subtree_data($parent_id, true, false) : $this->get_all_tree_data();
	}

	/**
	* Update the tree for an item inserted in the database
	*
	* @param int $item_id The item to be added
	* @return array Array with updated data, if the item was added successfully
	*				Empty array otherwise
	*/
	public function add_to_nestedset($item_id)
	{
		return $this->add_item_to_nestedset($item_id);
	}
}
