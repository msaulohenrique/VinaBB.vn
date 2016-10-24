<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controller;

use vinabb\web\includes\constants;

class helper
{
	/** @var \phpbb\language\language */
	protected $language;

	/**
	* Constructor
	*
	* @param \phpbb\language\language $language
	*/
	public function __construct(\phpbb\language\language $language)
	{
		$this->language = $language;
	}

	/**
	* Create clean URLs from titles. It works with many languages
	*
	* @author hello@weblap.ro
	* @param $text
	*
	* @return mixed
	*/
	public function clean_url($text)
	{
		return strtolower(
			preg_replace(
				array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'),
				array('', '-', ''),
				$this->ivn_convert_accent($text)
			)
		);
	}

	/**
	* Remove all accents or convert them to something
	* Used for SEO, out-dated broswers...
	*
	* This is part of iVN ToolKit from the phpBB iVN package
	* @author NEDKA Solutions <nedka.vn>
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
	function ivn_convert_accent($text = '', $mode = 'remove')
	{
		$ivn_data = array(
			'accent_letters'	=> array(
				'á'	=> array('Á', 'a', 'A', 'a', 'A', '&#225;', '&#193;', 'a´', 'A´', "a'", "A'", 0, 1),
				'à'	=> array('À', 'a', 'A', 'a', 'A', '&#224;', '&#192;', 'a`', 'A`', "a`", "A`", 0, 2),
				'ả'	=> array('Ả', 'a', 'A', 'a', 'A', '&#7843;', '&#7842;', 'a’', 'A’', "a?", "A?", 0, 3),
				'ã'	=> array('Ã', 'a', 'A', 'a', 'A', '&#227;', '&#195;', 'a˜', 'A˜', "a~", "A~", 0, 4),
				'ạ'	=> array('Ạ', 'a', 'A', 'a', 'A', '&#7841;', '&#7840;', 'a·', 'A·', "a.", "A.", 0, 5),
				'ă'	=> array('Ă', 'a', 'A', 'ă', 'Ă', '&#259;', '&#258;', 'ä', 'Ä', "a+", "A+", 1, 0),
				'ắ'	=> array('Ắ', 'a', 'A', 'ă', 'Ă', '&#7855;', '&#7854;', 'ä´', 'Ä´', "a+'", "A+'", 1, 1),
				'ằ'	=> array('Ằ', 'a', 'A', 'ă', 'Ă', '&#7857;', '&#7856;', 'ä`', 'Ä`', "a+`", "A+`", 1, 2),
				'ẳ'	=> array('Ẳ', 'a', 'A', 'ă', 'Ă', '&#7859;', '&#7858;', 'ä’', 'Ä’', "a+?", "A+?", 1, 3),
				'ẵ'	=> array('Ẵ', 'a', 'A', 'ă', 'Ă', '&#7861;', '&#7860;', 'ä˜', 'Ä˜', "a+~", "A+~", 1, 4),
				'ặ'	=> array('Ặ', 'a', 'A', 'ă', 'Ă', '&#7863;', '&#7862;', 'ä·', 'Ä·', "a+.", "A+.", 1, 5),
				'â'	=> array('Â', 'a', 'A', 'â', 'Â', '&#226;', '&#194;', 'â', 'Â', "a^", "A^", 2, 0),
				'ấ'	=> array('Ấ', 'a', 'A', 'â', 'Â', '&#7845;', '&#7844;', 'â´', 'Â´', "a^'", "A^'", 2, 1),
				'ầ'	=> array('Ầ', 'a', 'A', 'â', 'Â', '&#7847;', '&#7846;', 'â`', 'Â`', "a^`", "A^`", 2, 2),
				'ẩ'	=> array('Ẩ', 'a', 'A', 'â', 'Â', '&#7849;', '&#7848;', 'â’', 'Â’', "a^?", "A^?", 2, 3),
				'ẫ'	=> array('Ẫ', 'a', 'A', 'â', 'Â', '&#7851;', '&#7850;', 'â˜', 'Â˜', "a^~", "A^~", 2, 4),
				'ậ'	=> array('Ậ', 'a', 'A', 'â', 'Â', '&#7853;', '&#7852;', 'â·', 'Â·', "a^.", "A^.", 2, 5),
				'đ'	=> array('Đ', 'd', 'D', 'đ', 'Đ', '&#273;', '&#272;', 'ð', 'Ð', "d+", "+D", 1, 0),
				'é'	=> array('É', 'e', 'E', 'e', 'E', '&#233;', '&#201;', 'e´', 'E´', "e'", "E'", 0, 1),
				'è'	=> array('È', 'e', 'E', 'e', 'E', '&#232;', '&#200;', 'e`', 'E`', "e`", "E`", 0, 2),
				'ẻ'	=> array('Ẻ', 'e', 'E', 'e', 'E', '&#7867;', '&#7866;', 'e’', 'E’', "e?", "E?", 0, 3),
				'ẽ'	=> array('Ẽ', 'e', 'E', 'e', 'E', '&#7869;', '&#7868;', 'e˜', 'E˜', "e~", "E~", 0, 4),
				'ẹ'	=> array('Ẹ', 'e', 'E', 'e', 'E', '&#7865;', '&#7864;', 'e·', 'E·', "e.", "E.", 0, 5),
				'ê'	=> array('Ê', 'e', 'E', 'ê', 'Ê', '&#234;', '&#202;', 'ê', 'Ê', "e^", "E^", 1, 0),
				'ế'	=> array('Ế', 'e', 'E', 'ê', 'Ê', '&#7871;', '&#7870;', 'ê´', 'Ê´', "e^'", "E^'", 1, 1),
				'ề'	=> array('Ề', 'e', 'E', 'ê', 'Ê', '&#7873;', '&#7872;', 'ê`', 'Ê`', "e^`", "E^`", 1, 2),
				'ể'	=> array('Ể', 'e', 'E', 'ê', 'Ê', '&#7875;', '&#7874;', 'ê’', 'Ê’', "e^?", "E^?", 1, 3),
				'ễ'	=> array('Ễ', 'e', 'E', 'ê', 'Ê', '&#7877;', '&#7876;', 'ê˜', 'Ê˜', "e^~", "E^~", 1, 4),
				'ệ'	=> array('Ệ', 'e', 'E', 'ê', 'Ê', '&#7879;', '&#7878;', 'ê·', 'Ê·', "e^.", "E^.", 1, 5),
				'í'	=> array('Í', 'i', 'I', 'i', 'I', '&#237;', '&#205;', 'i´', 'I´', "i'", "I'", 0, 1),
				'ì'	=> array('Ì', 'i', 'I', 'i', 'I', '&#236;', '&#204;', 'i`', 'I`', "i`", "I`", 0, 2),
				'ỉ'	=> array('Ỉ', 'i', 'I', 'i', 'I', '&#7881;', '&#7880;', 'i’', 'I’', "i?", "I?", 0, 3),
				'ĩ'	=> array('Ĩ', 'i', 'I', 'i', 'I', '&#297;', '&#296;', 'i˜', 'I˜', "i~", "I~", 0, 4),
				'ị'	=> array('Ị', 'i', 'I', 'i', 'I', '&#7883;', '&#7882;', 'i·', 'I·', "i.", "I.", 0, 5),
				'ó'	=> array('Ó', 'o', 'O', 'o', 'O', '&#243;', '&#211;', 'o´', 'O´', "o'", "O'", 0, 1),
				'ò'	=> array('Ò', 'o', 'O', 'o', 'O', '&#242;', '&#210;', 'o`', 'O`', "o`", "O`", 0, 2),
				'ỏ'	=> array('Ỏ', 'o', 'O', 'o', 'O', '&#7887;', '&#7886;', 'o’', 'O’', "o?", "O?", 0, 3),
				'õ'	=> array('Õ', 'o', 'O', 'o', 'O', '&#245;', '&#213;', 'o˜', 'O˜', "o~", "O~", 0, 4),
				'ọ'	=> array('Ọ', 'o', 'O', 'o', 'O', '&#7885;', '&#7884;', 'o·', 'O·', "o.", "O.", 0, 5),
				'ô'	=> array('Ô', 'o', 'O', 'ô', 'Ô', '&#244;', '&#212;', 'ô', 'Ô', "o^", "O^", 1, 0),
				'ố'	=> array('Ố', 'o', 'O', 'ô', 'Ô', '&#7889;', '&#7888;', 'ô´', 'Ô´', "o^'", "O^'", 1, 1),
				'ồ'	=> array('Ồ', 'o', 'O', 'ô', 'Ô', '&#7891;', '&#7890;', 'ô`', 'Ô`', "o^`", "O^`", 1, 2),
				'ổ'	=> array('Ổ', 'o', 'O', 'ô', 'Ô', '&#7893;', '&#7892;', 'ô’', 'Ô’', "o^?", "O^?", 1, 3),
				'ỗ'	=> array('Ỗ', 'o', 'O', 'ô', 'Ô', '&#7895;', '&#7894;', 'ô˜', 'Ô˜', "o^~", "O^~", 1, 4),
				'ộ'	=> array('Ộ', 'o', 'O', 'ô', 'Ô', '&#7897;', '&#7896;', 'ô·', 'Ô·', "o^.", "O^.", 1, 5),
				'ơ'	=> array('Ơ', 'o', 'O', 'ơ', 'Ơ', '&#417;', '&#416;', 'ö', 'Ö', "o*", "O*", 2, 0),
				'ớ'	=> array('Ớ', 'o', 'O', 'ơ', 'Ơ', '&#7899;', '&#7898;', 'ö´', 'Ö´', "o*'", "O*'", 2, 1),
				'ờ'	=> array('Ờ', 'o', 'O', 'ơ', 'Ơ', '&#7901;', '&#7900;', 'ö`', 'Ö`', "o*`", "O*`", 2, 2),
				'ở'	=> array('Ở', 'o', 'O', 'ơ', 'Ơ', '&#7903;', '&#7902;', 'ö’', 'Ö’', "o*?", "O*?", 2, 3),
				'ỡ'	=> array('Ỡ', 'o', 'O', 'ơ', 'Ơ', '&#7905;', '&#7904;', 'ö˜', 'Ö˜', "o*~", "O*~", 2, 4),
				'ợ'	=> array('Ợ', 'o', 'O', 'ơ', 'Ơ', '&#7907;', '&#7906;', 'ö·', 'Ö·', "o*.", "O*.", 2, 5),
				'ú'	=> array('Ú', 'u', 'U', 'u', 'U', '&#250;', '&#218;', 'u´', 'U´', "u'", "U'", 0, 1),
				'ù'	=> array('Ù', 'u', 'U', 'u', 'U', '&#249;', '&#217;', 'u`', 'U`', "u`", "U`", 0, 2),
				'ủ'	=> array('Ủ', 'u', 'U', 'u', 'U', '&#7911;', '&#7910;', 'u’', 'U’', "u?", "U?", 0, 3),
				'ũ'	=> array('Ũ', 'u', 'U', 'u', 'U', '&#361;', '&#360;', 'u˜', 'U˜', "u~", "U~", 0, 4),
				'ụ'	=> array('Ụ', 'u', 'U', 'u', 'U', '&#7909;', '&#7908;', 'u·', 'U·', "u.", "U.", 0, 5),
				'ư'	=> array('Ư', 'u', 'U', 'ư', 'Ư', '&#432;', '&#431;', 'ü', 'Ü', "u*", "U*", 1, 0),
				'ứ'	=> array('Ứ', 'u', 'U', 'ư', 'Ư', '&#7913;', '&#7912;', 'ü´', 'Ü´', "u*'", "U*'", 1, 1),
				'ừ'	=> array('Ừ', 'u', 'U', 'ư', 'Ư', '&#7915;', '&#7914;', 'ü`', 'Ü`', "u*`", "U*`", 1, 2),
				'ử'	=> array('Ử', 'u', 'U', 'ư', 'Ư', '&#7917;', '&#7916;', 'ü’', 'Ü’', "u*?", "U*?", 1, 3),
				'ữ'	=> array('Ữ', 'u', 'U', 'ư', 'Ư', '&#7919;', '&#7918;', 'ü˜', 'Ü˜', "u*~", "U*~", 1, 4),
				'ự'	=> array('Ự', 'u', 'U', 'ư', 'Ư', '&#7921;', '&#7920;', 'ü·', 'Ü·', "u*.", "U*.", 1, 5),
				'ý'	=> array('Ý', 'y', 'Y', 'y', 'Y', '&#253;', '&#221;', 'y´', 'Y´', "y'", "Y'", 0, 1),
				'ỳ'	=> array('Ỳ', 'y', 'Y', 'y', 'Y', '&#7923;', '&#7922;', 'y`', 'Y`', "y`", "Y`", 0, 2),
				'ỷ'	=> array('Ỷ', 'y', 'Y', 'y', 'Y', '&#7927;', '&#7926;', 'y’', 'Y’', "y?", "y?", 0, 3),
				'ỹ'	=> array('Ỹ', 'y', 'Y', 'y', 'Y', '&#7929;', '&#7928;', 'y˜', 'Y˜', "y~", "Y~", 0, 4),
				'ỵ'	=> array('Ỵ', 'y', 'Y', 'y', 'Y', '&#7925;', '&#7924;', 'y·', 'Y·', "y.", "Y.", 0, 5),
			),
		);

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
				$text = str_replace(array($key, $data[0]), array($data[$i_lower], $data[$i_upper]), $text);
			}
		}

		return $text;
	}

	/**
	* Convert BB type from string to constant value
	*
	* @param $bb_type
	* @return int
	*/
	public function get_bb_type_constants($bb_type)
	{
		switch ($bb_type)
		{
			case 'ext':
				return constants::BB_TYPE_EXT;
			break;

			case 'style':
				return constants::BB_TYPE_STYLE;
			break;

			case 'acp_style':
				return constants::BB_TYPE_ACP_STYLE;
			break;

			case 'lang':
				return constants::BB_TYPE_LANG;
			break;

			case 'tool':
				return constants::BB_TYPE_TOOL;
			break;

			default:
				return 0;
			break;
		}
	}

	/**
	* Get OS name from constants
	*
	* @param $os_value
	*
	* @return string
	*/
	public function get_os_name($os_value)
	{
		switch ($os_value)
		{
			case constants::OS_ALL:
				return $this->language->lang(['OS_LIST', 'ALL']);
			break;

			case constants::OS_WIN:
				return $this->language->lang(['OS_LIST', 'WIN']);
			break;

			case constants::OS_MAC:
				return $this->language->lang(['OS_LIST', 'MAC']);
			break;

			case constants::OS_LINUX:
				return $this->language->lang(['OS_LIST', 'LINUX']);
			break;

			case constants::OS_BSD:
				return $this->language->lang(['OS_LIST', 'BSD']);
			break;

			case constants::OS_ANDROID:
				return $this->language->lang(['OS_LIST', 'ANDROID']);
			break;

			case constants::OS_IOS:
				return $this->language->lang(['OS_LIST', 'IOS']);
			break;

			case constants::OS_WP:
				return $this->language->lang(['OS_LIST', 'WP']);
			break;

			default:
				return $this->language->lang('UNKNOWN');
			break;
		}
	}

	/**
	* List of stable phpBB versions
	*
	* @return array
	*/
	public function get_phpbb_versions()
	{
		return array(
			// Rhea
			'3.2'	=> array(
				'3.2.0'		=> array('name' => '3.2.0', 'date' => '2016-12-31'),
			),
			// Ascraeus
			'3.1'	=> array(
				'3.1.10'	=> array('name' => '3.1.10', 'date' => '2016-10-12'),
				'3.1.9'		=> array('name' => '3.1.9', 'date' => '2016-04-16'),
				'3.1.8'		=> array('name' => '3.1.8', 'date' => '2016-02-19'),
				'3.1.7'		=> array('name' => '3.1.7', 'date' => '2015-12-19'),
				'3.1.6'		=> array('name' => '3.1.6', 'date' => '2015-09-05'),
				'3.1.5'		=> array('name' => '3.1.5', 'date' => '2015-06-14'),
				'3.1.4'		=> array('name' => '3.1.4', 'date' => '2015-05-03'),
				'3.1.3'		=> array('name' => '3.1.3', 'date' => '2015-02-02'),
				'3.1.2'		=> array('name' => '3.1.2', 'date' => '2014-11-25'),
				'3.1.1'		=> array('name' => '3.1.1', 'date' => '2014-11-02'),
				'3.1.0'		=> array('name' => '3.1.0', 'date' => '2014-10-28'),
			),
			// Olympus
			'3.0'	=> array(
				'3.0.14'	=> array('name' => '3.0.14', 'date' => '2015-05-03'),
				'3.0.13'	=> array('name' => '3.0.13', 'date' => '2015-01-27'),
				'3.0.12'	=> array('name' => '3.0.12', 'date' => '2013-09-28'),
				'3.0.11'	=> array('name' => '3.0.11', 'date' => '2012-08-20'),
				'3.0.10'	=> array('name' => '3.0.10', 'date' => '2012-01-03'),
				'3.0.9'		=> array('name' => '3.0.9', 'date' => '2011-07-11'),
				'3.0.8'		=> array('name' => '3.0.8', 'date' => '2010-11-20'),
				'3.0.7'		=> array('name' => '3.0.7', 'date' => '2010-03-01'),
				'3.0.6'		=> array('name' => '3.0.6', 'date' => '2009-11-17'),
				'3.0.5'		=> array('name' => '3.0.5', 'date' => '2009-05-31'),
				'3.0.4'		=> array('name' => '3.0.4', 'date' => '2008-12-13'),
				'3.0.3'		=> array('name' => '3.0.3', 'date' => '2008-11-13'),
				'3.0.2'		=> array('name' => '3.0.2', 'date' => '2008-07-11'),
				'3.0.1'		=> array('name' => '3.0.1', 'date' => '2008-04-08'),
				'3.0.0'		=> array('name' => '3.0.0', 'date' => '2007-12-12'),
			),
		);
	}
}
