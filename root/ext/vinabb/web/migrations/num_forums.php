<?php
/**
* This file is part of the VinaBB Styles Demo package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations;

use phpbb\db\migration\migration;

class num_forums extends migration
{
	public function update_data()
	{
		$sql = 'SELECT COUNT(forum_id) AS num_forums
			FROM ' . FORUMS_TABLE;
		$result = $this->db->sql_query($sql);
		$num_forums = $this->db->sql_fetchfield('num_forums');
		$this->db->sql_freeresult($result);

		return array(
			// Config
			array('config.add', array('num_forums', $num_forums, true)),
		);
	}
}
