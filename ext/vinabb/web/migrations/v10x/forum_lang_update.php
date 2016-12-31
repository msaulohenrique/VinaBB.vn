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
* Update for existing forums with the default language
*/
class forum_lang_update extends migration
{
	/**
	* List of required migrations
	*
	* @return array
	*/
	static public function depends_on()
	{
		return ['\vinabb\web\migrations\v10x\forum_lang'];
	}

	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [['custom', [[&$this, 'update_forum_lang']]]];
	}

	/**
	* Update the column forum_lang for current entities
	*/
	public function update_forum_lang()
	{
		$sql = 'UPDATE ' . $this->table_prefix . "forums
			SET forum_lang = '" . $this->db->sql_escape($this->config['default_lang']) . "'";
		$this->sql_query($sql);
	}
}
