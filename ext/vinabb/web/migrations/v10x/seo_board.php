<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\migrations\v10x;

use phpbb\db\migration\migration;
use vinabb\web\includes\constants;

/**
* Add SEO columns for forum/topic/user
*/
class seo_board extends migration
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
				$this->table_prefix . 'forums'	=> [
					'forum_name_seo'	=> ['VCHAR', '']
				],
				$this->table_prefix . 'topics'	=> [
					'topic_title_seo'	=> ['VCHAR', '']
				],
				$this->table_prefix . 'users'	=> [
					'username_seo'		=> ['VCHAR', '']
				]
			]
		];
	}

	/**
	* Update data
	*
	* @return array
	*/
	public function update_data()
	{
		return [['custom', [[&$this, 'update_seo_columns']]]];
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
				$this->table_prefix . 'users'	=> ['username_seo']
			]
		];
	}

	/**
	* Update SEO column value for current entities
	*/
	public function update_seo_columns()
	{
		$tables_list = [
			$this->table_prefix . 'forums'	=> [
				'id'			=> 'forum_id',
				'column'		=> 'forum_name',
				'seo_column'	=> 'forum_name_seo'
			],
			$this->table_prefix . 'topics'	=> [
				'id'			=> 'topic_id',
				'column'		=> 'topic_title',
				'seo_column'	=> 'topic_title_seo'
			],
			$this->table_prefix . 'users'	=> [
				'id'			=> 'user_id',
				'column'		=> 'username',
				'seo_column'	=> 'username_seo'
			]
		];

		$forum_seo_names = [];
		foreach ($tables_list as $table_name => $data)
		{
			list($column_id, $column, $seo_column) = array_values($data);

			$sql = "SELECT $column_id, $column
				FROM $table_name";
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$clean_name = $this->clean_url($row[$column]);

				$sql = "UPDATE $table_name
					SET $seo_column = '" . $clean_name . "'
					WHERE $column_id = " . $row[$column_id];
				$this->sql_query($sql);

				if ($table_name == $this->table_prefix . 'forums')
				{
					$forum_seo_names[$row[$column_id]] = $clean_name;
				}
			}
			$this->db->sql_freeresult($result);
		}

		// If there have more than 2 same forum SEO names, add parent forum SEO name as prefix
		$duplicate_forum_seo_names = [];

		foreach (array_count_values($forum_seo_names) as $forum_seo_name => $count)
		{
			if ($count > 1)
			{
				$duplicate_forum_seo_names[] = $forum_seo_name;
			}
		}

		if (sizeof($duplicate_forum_seo_names))
		{
			$sql = 'SELECT forum_id, parent_id
				FROM ' . $this->table_prefix . 'forums
				WHERE parent_id <> 0
					AND ' . $this->db->sql_in_set('forum_name_seo', $duplicate_forum_seo_names);
			$result = $this->db->sql_query($sql);

			while ($row = $this->db->sql_fetchrow($result))
			{
				$new_clean_name = $forum_seo_names[$row['parent_id']] . constants::REWRITE_URL_FORUM_CAT . $forum_seo_names[$row['forum_id']];

				$sql = 'UPDATE ' . $this->table_prefix . "forums
					SET forum_name_seo = '" . $new_clean_name . "'
					WHERE forum_id = " . $row['forum_id'];
				$this->sql_query($sql);
			}
			$this->db->sql_freeresult($result);
		}
	}

	/**
	* Create clean URLs from titles. It works with many languages
	*
	* @author hello@weblap.ro
	* @param $text
	*
	* @return mixed
	*/
	protected function clean_url($text)
	{
		return strtolower(
			preg_replace(
				['/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'],
				['', '-', ''],
				$this->ivn_convert_accent($text)
			)
		);
	}

	/**
	* Remove all accents or convert them to something
	* Used for SEO, out-dated broswers...
	*
	* This is part of iVN ToolKit from the phpBB iVN package
	* @author VinaBB <vinabb.vn>
	*
	* @param string	$text	Input text
	* @param string	$mode	Mode:
	*							'remove': Remove all accents and convert special letters into English letters
	*							'remove_keep_alphabet': Remove only accents, keep Vietnamese letters in the alphabet
	*							'ncr_decimal': Convert accents into NCR Decimal
	*							'ascii': Convert accents into ASCII symbols
	*							'ascii_kb': Simple version of 'ascii' mode, only typable standard keycaps
	* @return string		Result text
	*/
	protected function ivn_convert_accent($text = '', $mode = 'remove')
	{
		$ivn_data = [
			'accent_letters'	=> [
				'á'	=> ['Á', 'a', 'A', 'a', 'A', '&#225;', '&#193;', 'a´', 'A´', "a'", "A'", 0, 1],
				'à'	=> ['À', 'a', 'A', 'a', 'A', '&#224;', '&#192;', 'a`', 'A`', "a`", "A`", 0, 2],
				'ả'	=> ['Ả', 'a', 'A', 'a', 'A', '&#7843;', '&#7842;', 'a’', 'A’', "a?", "A?", 0, 3],
				'ã'	=> ['Ã', 'a', 'A', 'a', 'A', '&#227;', '&#195;', 'a˜', 'A˜', "a~", "A~", 0, 4],
				'ạ'	=> ['Ạ', 'a', 'A', 'a', 'A', '&#7841;', '&#7840;', 'a·', 'A·', "a.", "A.", 0, 5],
				'ă'	=> ['Ă', 'a', 'A', 'ă', 'Ă', '&#259;', '&#258;', 'ä', 'Ä', "a+", "A+", 1, 0],
				'ắ'	=> ['Ắ', 'a', 'A', 'ă', 'Ă', '&#7855;', '&#7854;', 'ä´', 'Ä´', "a+'", "A+'", 1, 1],
				'ằ'	=> ['Ằ', 'a', 'A', 'ă', 'Ă', '&#7857;', '&#7856;', 'ä`', 'Ä`', "a+`", "A+`", 1, 2],
				'ẳ'	=> ['Ẳ', 'a', 'A', 'ă', 'Ă', '&#7859;', '&#7858;', 'ä’', 'Ä’', "a+?", "A+?", 1, 3],
				'ẵ'	=> ['Ẵ', 'a', 'A', 'ă', 'Ă', '&#7861;', '&#7860;', 'ä˜', 'Ä˜', "a+~", "A+~", 1, 4],
				'ặ'	=> ['Ặ', 'a', 'A', 'ă', 'Ă', '&#7863;', '&#7862;', 'ä·', 'Ä·', "a+.", "A+.", 1, 5],
				'â'	=> ['Â', 'a', 'A', 'â', 'Â', '&#226;', '&#194;', 'â', 'Â', "a^", "A^", 2, 0],
				'ấ'	=> ['Ấ', 'a', 'A', 'â', 'Â', '&#7845;', '&#7844;', 'â´', 'Â´', "a^'", "A^'", 2, 1],
				'ầ'	=> ['Ầ', 'a', 'A', 'â', 'Â', '&#7847;', '&#7846;', 'â`', 'Â`', "a^`", "A^`", 2, 2],
				'ẩ'	=> ['Ẩ', 'a', 'A', 'â', 'Â', '&#7849;', '&#7848;', 'â’', 'Â’', "a^?", "A^?", 2, 3],
				'ẫ'	=> ['Ẫ', 'a', 'A', 'â', 'Â', '&#7851;', '&#7850;', 'â˜', 'Â˜', "a^~", "A^~", 2, 4],
				'ậ'	=> ['Ậ', 'a', 'A', 'â', 'Â', '&#7853;', '&#7852;', 'â·', 'Â·', "a^.", "A^.", 2, 5],
				'đ'	=> ['Đ', 'd', 'D', 'đ', 'Đ', '&#273;', '&#272;', 'ð', 'Ð', "d+", "+D", 1, 0],
				'é'	=> ['É', 'e', 'E', 'e', 'E', '&#233;', '&#201;', 'e´', 'E´', "e'", "E'", 0, 1],
				'è'	=> ['È', 'e', 'E', 'e', 'E', '&#232;', '&#200;', 'e`', 'E`', "e`", "E`", 0, 2],
				'ẻ'	=> ['Ẻ', 'e', 'E', 'e', 'E', '&#7867;', '&#7866;', 'e’', 'E’', "e?", "E?", 0, 3],
				'ẽ'	=> ['Ẽ', 'e', 'E', 'e', 'E', '&#7869;', '&#7868;', 'e˜', 'E˜', "e~", "E~", 0, 4],
				'ẹ'	=> ['Ẹ', 'e', 'E', 'e', 'E', '&#7865;', '&#7864;', 'e·', 'E·', "e.", "E.", 0, 5],
				'ê'	=> ['Ê', 'e', 'E', 'ê', 'Ê', '&#234;', '&#202;', 'ê', 'Ê', "e^", "E^", 1, 0],
				'ế'	=> ['Ế', 'e', 'E', 'ê', 'Ê', '&#7871;', '&#7870;', 'ê´', 'Ê´', "e^'", "E^'", 1, 1],
				'ề'	=> ['Ề', 'e', 'E', 'ê', 'Ê', '&#7873;', '&#7872;', 'ê`', 'Ê`', "e^`", "E^`", 1, 2],
				'ể'	=> ['Ể', 'e', 'E', 'ê', 'Ê', '&#7875;', '&#7874;', 'ê’', 'Ê’', "e^?", "E^?", 1, 3],
				'ễ'	=> ['Ễ', 'e', 'E', 'ê', 'Ê', '&#7877;', '&#7876;', 'ê˜', 'Ê˜', "e^~", "E^~", 1, 4],
				'ệ'	=> ['Ệ', 'e', 'E', 'ê', 'Ê', '&#7879;', '&#7878;', 'ê·', 'Ê·', "e^.", "E^.", 1, 5],
				'í'	=> ['Í', 'i', 'I', 'i', 'I', '&#237;', '&#205;', 'i´', 'I´', "i'", "I'", 0, 1],
				'ì'	=> ['Ì', 'i', 'I', 'i', 'I', '&#236;', '&#204;', 'i`', 'I`', "i`", "I`", 0, 2],
				'ỉ'	=> ['Ỉ', 'i', 'I', 'i', 'I', '&#7881;', '&#7880;', 'i’', 'I’', "i?", "I?", 0, 3],
				'ĩ'	=> ['Ĩ', 'i', 'I', 'i', 'I', '&#297;', '&#296;', 'i˜', 'I˜', "i~", "I~", 0, 4],
				'ị'	=> ['Ị', 'i', 'I', 'i', 'I', '&#7883;', '&#7882;', 'i·', 'I·', "i.", "I.", 0, 5],
				'ó'	=> ['Ó', 'o', 'O', 'o', 'O', '&#243;', '&#211;', 'o´', 'O´', "o'", "O'", 0, 1],
				'ò'	=> ['Ò', 'o', 'O', 'o', 'O', '&#242;', '&#210;', 'o`', 'O`', "o`", "O`", 0, 2],
				'ỏ'	=> ['Ỏ', 'o', 'O', 'o', 'O', '&#7887;', '&#7886;', 'o’', 'O’', "o?", "O?", 0, 3],
				'õ'	=> ['Õ', 'o', 'O', 'o', 'O', '&#245;', '&#213;', 'o˜', 'O˜', "o~", "O~", 0, 4],
				'ọ'	=> ['Ọ', 'o', 'O', 'o', 'O', '&#7885;', '&#7884;', 'o·', 'O·', "o.", "O.", 0, 5],
				'ô'	=> ['Ô', 'o', 'O', 'ô', 'Ô', '&#244;', '&#212;', 'ô', 'Ô', "o^", "O^", 1, 0],
				'ố'	=> ['Ố', 'o', 'O', 'ô', 'Ô', '&#7889;', '&#7888;', 'ô´', 'Ô´', "o^'", "O^'", 1, 1],
				'ồ'	=> ['Ồ', 'o', 'O', 'ô', 'Ô', '&#7891;', '&#7890;', 'ô`', 'Ô`', "o^`", "O^`", 1, 2],
				'ổ'	=> ['Ổ', 'o', 'O', 'ô', 'Ô', '&#7893;', '&#7892;', 'ô’', 'Ô’', "o^?", "O^?", 1, 3],
				'ỗ'	=> ['Ỗ', 'o', 'O', 'ô', 'Ô', '&#7895;', '&#7894;', 'ô˜', 'Ô˜', "o^~", "O^~", 1, 4],
				'ộ'	=> ['Ộ', 'o', 'O', 'ô', 'Ô', '&#7897;', '&#7896;', 'ô·', 'Ô·', "o^.", "O^.", 1, 5],
				'ơ'	=> ['Ơ', 'o', 'O', 'ơ', 'Ơ', '&#417;', '&#416;', 'ö', 'Ö', "o*", "O*", 2, 0],
				'ớ'	=> ['Ớ', 'o', 'O', 'ơ', 'Ơ', '&#7899;', '&#7898;', 'ö´', 'Ö´', "o*'", "O*'", 2, 1],
				'ờ'	=> ['Ờ', 'o', 'O', 'ơ', 'Ơ', '&#7901;', '&#7900;', 'ö`', 'Ö`', "o*`", "O*`", 2, 2],
				'ở'	=> ['Ở', 'o', 'O', 'ơ', 'Ơ', '&#7903;', '&#7902;', 'ö’', 'Ö’', "o*?", "O*?", 2, 3],
				'ỡ'	=> ['Ỡ', 'o', 'O', 'ơ', 'Ơ', '&#7905;', '&#7904;', 'ö˜', 'Ö˜', "o*~", "O*~", 2, 4],
				'ợ'	=> ['Ợ', 'o', 'O', 'ơ', 'Ơ', '&#7907;', '&#7906;', 'ö·', 'Ö·', "o*.", "O*.", 2, 5],
				'ú'	=> ['Ú', 'u', 'U', 'u', 'U', '&#250;', '&#218;', 'u´', 'U´', "u'", "U'", 0, 1],
				'ù'	=> ['Ù', 'u', 'U', 'u', 'U', '&#249;', '&#217;', 'u`', 'U`', "u`", "U`", 0, 2],
				'ủ'	=> ['Ủ', 'u', 'U', 'u', 'U', '&#7911;', '&#7910;', 'u’', 'U’', "u?", "U?", 0, 3],
				'ũ'	=> ['Ũ', 'u', 'U', 'u', 'U', '&#361;', '&#360;', 'u˜', 'U˜', "u~", "U~", 0, 4],
				'ụ'	=> ['Ụ', 'u', 'U', 'u', 'U', '&#7909;', '&#7908;', 'u·', 'U·', "u.", "U.", 0, 5],
				'ư'	=> ['Ư', 'u', 'U', 'ư', 'Ư', '&#432;', '&#431;', 'ü', 'Ü', "u*", "U*", 1, 0],
				'ứ'	=> ['Ứ', 'u', 'U', 'ư', 'Ư', '&#7913;', '&#7912;', 'ü´', 'Ü´', "u*'", "U*'", 1, 1],
				'ừ'	=> ['Ừ', 'u', 'U', 'ư', 'Ư', '&#7915;', '&#7914;', 'ü`', 'Ü`', "u*`", "U*`", 1, 2],
				'ử'	=> ['Ử', 'u', 'U', 'ư', 'Ư', '&#7917;', '&#7916;', 'ü’', 'Ü’', "u*?", "U*?", 1, 3],
				'ữ'	=> ['Ữ', 'u', 'U', 'ư', 'Ư', '&#7919;', '&#7918;', 'ü˜', 'Ü˜', "u*~", "U*~", 1, 4],
				'ự'	=> ['Ự', 'u', 'U', 'ư', 'Ư', '&#7921;', '&#7920;', 'ü·', 'Ü·', "u*.", "U*.", 1, 5],
				'ý'	=> ['Ý', 'y', 'Y', 'y', 'Y', '&#253;', '&#221;', 'y´', 'Y´', "y'", "Y'", 0, 1],
				'ỳ'	=> ['Ỳ', 'y', 'Y', 'y', 'Y', '&#7923;', '&#7922;', 'y`', 'Y`', "y`", "Y`", 0, 2],
				'ỷ'	=> ['Ỷ', 'y', 'Y', 'y', 'Y', '&#7927;', '&#7926;', 'y’', 'Y’', "y?", "y?", 0, 3],
				'ỹ'	=> ['Ỹ', 'y', 'Y', 'y', 'Y', '&#7929;', '&#7928;', 'y˜', 'Y˜', "y~", "Y~", 0, 4],
				'ỵ'	=> ['Ỵ', 'y', 'Y', 'y', 'Y', '&#7925;', '&#7924;', 'y·', 'Y·', "y.", "Y.", 0, 5]
			]
		];

		if (!empty($text))
		{
			switch ($mode)
			{
				default:
				case 'remove';
					$i_lower = 1;
					$i_upper = 2;
				break;

				case 'remove_keep_alphabet';
					$i_lower = 3;
					$i_upper = 4;
				break;

				case 'ncr_decimal';
					$i_lower = 5;
					$i_upper = 6;
				break;

				case 'ascii';
					$i_lower = 7;
					$i_upper = 8;
				break;

				case 'ascii_kb';
					$i_lower = 9;
					$i_upper = 10;
				break;
			}

			foreach ($ivn_data['accent_letters'] as $key => $data)
			{
				$text = str_replace([$key, $data[0]], [$data[$i_lower], $data[$i_upper]], $text);
			}
		}

		return $text;
	}
}
