<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;

class portal_schema extends migration
{
	public function update_schema()
	{
		return array(
			'add_tables' => array(
				$this->table_prefix . 'portal_categories' => array(
					'COLUMNS' => array(
						'cat_id'		=> array('UINT', null, 'auto_increment'),
						'cat_name'		=> array('VCHAR_UNI', ''),
						'cat_name_vi'	=> array('VCHAR_UNI', ''),
						'cat_varname'	=> array('VCHAR', ''),
						'cat_icon'		=> array('VCHAR', ''),
						'cat_order'		=> array('USINT', 0),
					),
					'PRIMARY_KEY' => 'cat_id',
				),
				$this->table_prefix . 'portal_articles' => array(
					'COLUMNS' => array(
						'article_id'			=> array('UINT', null, 'auto_increment'),
						'cat_id'				=> array('UINT', 0),
						'article_name'			=> array('VCHAR_UNI', ''),
						'article_name_seo'		=> array('VCHAR', ''),
						'article_lang'			=> array('VCHAR:30', ''),
						'article_img'			=> array('VCHAR', ''),
						'article_desc'			=> array('TEXT_UNI', ''),
						'article_text'			=> array('MTEXT_UNI', ''),
						'article_text_uid'		=> array('VCHAR:8', ''),
						'article_text_bitfield'	=> array('VCHAR:255', ''),
						'article_text_options'	=> array('UINT:11', 0),
						'article_enable'		=> array('BOOL', 1),
						'article_views'			=> array('UINT', 0),
						'article_time'			=> array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY' => 'article_id',
					'KEYS' => array(
						'c_id'	=> array('INDEX', 'cat_id'),
					),
				),
				$this->table_prefix . 'portal_comments' => array(
					'COLUMNS' => array(
						'comment_id'			=> array('UINT', null, 'auto_increment'),
						'user_id'				=> array('ULINT', 0),
						'article_id'			=> array('UINT', 0),
						'comment_rate'			=> array('TINT:1', 0),
						'comment_text'			=> array('TEXT_UNI', ''),
						'comment_text_uid'		=> array('VCHAR:8', ''),
						'comment_text_bitfield'	=> array('VCHAR:255', ''),
						'comment_text_options'	=> array('UINT:11', 0),
						'comment_mode'			=> array('TINT:1', 0),
						'comment_time'			=> array('TIMESTAMP', 0),
					),
					'PRIMARY_KEY' => 'comment_id',
					'KEYS' => array(
						'u_id'	=> array('INDEX', 'user_id'),
						'a_id'	=> array('INDEX', 'article_id'),
					),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables' => array(
				$this->table_prefix . 'portal_categories',
				$this->table_prefix . 'portal_articles',
				$this->table_prefix . 'portal_comments',
			),
		);
	}
}
