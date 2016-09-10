<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controller;

class service extends \phpbb\cache\service
{
	function get_forum_data()
	{
		if (($forum_data = $this->driver->get('_forum_data')) === false)
		{
			$sql = 'SELECT *
				FROM ' . FORUMS_TABLE;
			$result = $this->db->sql_query($sql);

			$forum_data = array();
			while ($row = $this->db->sql_fetchrow($result))
			{
				$forum_data[$row['forum_id']] = array(
					'parent_id'		=> $row['parent_id'],
					'left_id'		=> $row['left_id'],
					'right_id'		=> $row['right_id'],
					'name'			=> $row['forum_name'],
					'desc'			=> $row['forum_desc'],
					'desc_bitfield'	=> $row['forum_desc_bitfield'],
					'desc_options'	=> $row['forum_desc_options'],
					'desc_uid'		=> $row['forum_desc_uid'],
					'type'			=> $row['forum_type'],
					'status'		=> $row['forum_status'],
					'name_seo'		=> $row['forum_name_seo'],
				);
			}
			$this->db->sql_freeresult($result);

			$this->driver->put('_forum_data', $forum_data);
		}

		return $forum_data;
	}

	function clear_forum_data()
	{
		$this->driver->destroy('_forum_data');
	}
}
