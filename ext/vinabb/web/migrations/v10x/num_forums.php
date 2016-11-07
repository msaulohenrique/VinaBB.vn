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
* Add the counter "number of forums"
*/
class num_forums extends migration
{
	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		$sql = 'SELECT COUNT(forum_id) AS num_forums
			FROM ' . FORUMS_TABLE;
		$result = $this->db->sql_query($sql);
		$num_forums = (int) $this->db->sql_fetchfield('num_forums');
		$this->db->sql_freeresult($result);

		return [
			['config.add', ['num_forums', $num_forums, true]]
		];
	}
}
