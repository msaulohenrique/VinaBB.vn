<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;

/**
* Add SEO columns for forum/topic/post/user
*/
class seo_schema extends migration
{
	/**
	* Update schema
	*
	* @return array
	*/
	public function update_schema()
	{
		return [
			'add_columns'	=> [
				$this->table_prefix . 'forums'	=> ['forum_name_seo' => ['VCHAR', '']],
				$this->table_prefix . 'topics'	=> ['topic_title_seo' => ['VCHAR', '']],
				$this->table_prefix . 'post'	=> ['post_subject_seo' => ['VCHAR', '']],
				$this->table_prefix . 'users'	=> ['username_seo' => ['VCHAR', '']]
			]
		];
	}

	/**
	* Revert schema
	*
	* @return array
	*/
	public function revert_schema()
	{
		return [
			'drop_columns'	=> [
				$this->table_prefix . 'forums'	=> ['forum_name_seo'],
				$this->table_prefix . 'topics'	=> ['topic_title_seo'],
				$this->table_prefix . 'post'	=> ['post_subject_seo'],
				$this->table_prefix . 'users'	=> ['username_seo']
			]
		];
	}
}
