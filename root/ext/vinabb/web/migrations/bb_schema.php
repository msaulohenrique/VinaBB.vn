<?php
/**
* This file is part of the VinaBB Styles Demo package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations;

use phpbb\db\migration\migration;

class bb_schema extends migration
{
	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'bb_authors' => array(
					'COLUMNS' => array(
						'author_id'				=> array('UINT', NULL, 'auto_increment'),
						'user_id'				=> array('ULINT', 0),
						'author_name'			=> array('VCHAR_UNI:255', ''),
						'author_firstname'		=> array('VCHAR_UNI:255', ''),
						'author_lastname'		=> array('VCHAR_UNI:255', ''),
						'author_is_group'		=> array('BOOL', 0),
						'author_group'			=> array('UINT', 0),
						'author_www'			=> array('VCHAR_UNI', 0),
						'author_email'			=> array('VCHAR_UNI', 0),
						'author_phpbb'			=> array('ULINT', 0),
						'author_github'			=> array('VCHAR:255', ''),
						'author_facebook'		=> array('VCHAR:255', ''),
						'author_twitter'		=> array('VCHAR:255', ''),
						'author_google'			=> array('VCHAR:255', ''),
						'author_google_plus'	=> array('VCHAR:255', ''),
						'author_skype'			=> array('VCHAR:255', ''),
					),
					'PRIMARY_KEY' => 'author_id',
					'KEYS' => array(
						'a_id'	=> array('INDEX', 'author_id'),
					),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'bb_authors',
				$this->table_prefix . 'bb_categories',
				$this->table_prefix . 'bb_items',
				$this->table_prefix . 'bb_phpbb_versions',
			),
		);
	}
}
