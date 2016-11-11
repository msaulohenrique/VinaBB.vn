<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\operators;

/**
* Nestedset class for menu items
*/
class nestedset_menus extends \phpbb\tree\nestedset
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
			'MENUS_NESTEDSET_',
			'',
			[],
			[
				'item_id'		=> 'menu_id',
				'parent_id'		=> 'parent_id',
				'left_id'		=> 'left_id',
				'right_id'		=> 'right_id',
				'item_parents'	=> 'menu_parents'
			]
		);
	}

	/**
	* Get the menu data from the database
	*
	* @param int $parent_id Parent to display menu items from, 0 for all
	* @return array
	*/
	public function get_menu_data($parent_id)
	{
		return $parent_id ? $this->get_subtree_data($parent_id, true, false) : $this->get_all_tree_data();
	}

	/**
	* Update the tree for an item inserted in the database
	*
	* @param int $item_id The item to be added
	* @return array
	*/
	public function add_to_nestedset($item_id)
	{
		return $this->add_item_to_nestedset($item_id);
	}
}
