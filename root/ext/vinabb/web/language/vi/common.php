<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

/**
* All language files should use UTF-8 as their encoding
* and the files must not contain a BOM.
*/

$lang = array_merge($lang, array(
	'BOARD'	=> 'Diễn đàn',

	'COMMUNITY'	=> 'Cộng đồng',

	'EMOTICON_TEXT'	=> array(
		'ALIEN'				=> '!@#$%',
		'ANGEL'				=> 'Sẽ có thiên thần sửa lỗi cho em',
		'ANGRY'				=> 'Tức hộc máu',
		'ASTONISHED'		=> 'Giết tui đi',
		'AWKWARD'			=> 'Lập trình như thằng thất tình',
		'BEARD'				=> 'Vuốt mặt nể râu',
		'BIG_GRIN'			=> 'Cười cho bớt lười',
		'BIG_SMILE'			=> 'Há miệng mắc quai',
		'BIG_SURPRISE'		=> 'Thật không thể tin nổi',
		'BLUSHING'			=> 'Mắc cỡ',
		'BLUSHING_WINK'		=> 'Hồn nhiên như cô tiên',
		'CENSORED'			=> 'Bí mật quốc gia, lột da không nói',
		'COLD_SWEAT'		=> 'Xấu cần phấn đấu',
		'CONFOUNDED'		=> 'Biến thái',
		'CONFUSED'			=> 'Rối như canh hẹ',
		'COOL'				=> 'Quá đỉnh',
		'CRYING'			=> 'Hu hu',
		'DEVIL'				=> 'Quỷ xài tiền tỷ, ma nhặt lá đa',
		'DISAPPOINTED'		=> 'Thất vọng',
		'DIZZY'				=> 'Ứ chịu đâu',
		'EYEWINK'			=> 'Chém gió',
		'FEARFUL'			=> 'Sợ vãi',
		'FROWN'				=> 'Chán như con gián',
		'GLOOMY'			=> 'Buồn 5 phút',
		'HEE_HEE'			=> 'Ờ ờ',
		'HUFFING'			=> 'Hít vào, thở ra…',
		'IN_LOVE'			=> 'Xinh quá',
		'KISS'				=> 'Một nụ hôn bằng 10 thang thuốc bổ',
		'KISS_BLUSHING'		=> 'Hí hí hun cái coi',
		'KISS_WINK'			=> 'Dê xồm',
		'KISSING'			=> 'Chụt chụt',
		'LAUGHING'			=> 'Xạo chết liền',
		'LAUGHING_TEARS'	=> 'Cười bể bụng',
		'LOL'				=> 'Ha ha',
		'MAD_DEVIL'			=> 'Quỷ tha ma bắt',
		'MEOW'				=> 'Méo méo',
		'MONEY'				=> 'Tôi yêu Việt Nam đồng',
		'NO_IDEAS'			=> 'Miễn bàn',
		'PACMAN'			=> 'Cá cắn câu',
		'RELIEVED'			=> 'Anh hùng bàn phím',
		'SAD'				=> 'Buồn như con chuồn chuồn',
		'SAD_CRYING'		=> 'Nước mắt cá sấu',
		'SCARED'			=> 'Đồ bệnh hoạn',
		'SCREAMING'			=> 'Tha cho em',
		'SHOCKED'			=> 'Chuẩn không cần chỉnh',
		'SILLY'				=> 'Hôi nách',
		'SLEEPING'			=> 'Khò khò',
		'SLEEPY'			=> 'Hỉ mũi chưa sạch',
		'SMILE'				=> 'Hi hi',
		'SMILING'			=> 'Tuyệt vời ông mặt trời',
		'STRAIGHT'			=> 'Bó tay',
		'SURPRISED'			=> 'Ôi má ơi',
		'TEARS_OF_JOY'		=> 'Sặc nước',
		'TONGUE'			=> 'Lêu lêu',
		'TONGUE_LICK'		=> 'Liếm lưỡi 7 lần trước khi nói',
		'TONGUE_OUT'		=> 'Chót lưỡi đầu môi',
		'TONGUE_WINK'		=> 'Anh đây cóc sợ',
		'UPSET'				=> 'Đắng lòng',
		'WEARY'				=> 'Ca này khó',
		'WHO_AM_I'			=> 'Không giỡn đâu',
		'WINKING'			=> 'Hãy đợi đấy!',
		'WORLD_WEARY'		=> 'Chán đời',
		'WORRIED'			=> 'Đừng tỏ ra nguy hiểm',
		'WTF'				=> 'Cậu làm gì thế?',
	),

	'FORUM_WAR'	=> 'Trò chơi',

	'G_MANAGEMENT_TEAM'	=> 'Nhóm quản lý',
	'G_MODERATOR_TEAM'	=> 'Điều hành viên',
	'G_SUPPORT_TEAM'	=> 'Nhóm hỗ trợ',
	'GENDER'			=> 'Gender',
	'GENDER_FEMALE'		=> 'Female',
	'GENDER_MALE'		=> 'Male',
	'GITHUB'			=> 'GitHub',
	'GITHUB_PROFILE'	=> 'GitHub',

	'MAINTENANCE_TEXT'				=> 'Zzz… Máy chủ chúng tôi đã chìm sâu vào giấc ngủ cùng với giấc mơ đẹp về ngày mai tươi sáng của phpBB. Bạn không thể đánh thức tên sống ảo này nhưng mẹ hắn thì có thể. Chúng tôi đang đi tìm bà ấy. Yêu bạn!',
	'MAINTENANCE_TEXT_TIME'			=> 'Zzz… Máy chủ chúng tôi đã chìm sâu vào giấc ngủ cùng với giấc mơ đẹp về ngày mai tươi sáng của phpBB. Hãy vui lòng trở lại vào lúc <strong>%s</strong> và tên sống ảo này có thể tỉnh giấc. Yêu bạn!',
	'MAINTENANCE_TEXT_TIME_LONG'	=> 'Zzz… Máy chủ chúng tôi đã chìm sâu vào giấc ngủ cùng với giấc mơ đẹp về ngày mai tươi sáng của phpBB. Bạn không thể đánh thức tên sống ảo này cho đến ngày <strong>%s</strong>. Mong bạn thông cảm cho chúng tôi. Yêu bạn!',
	'MAINTENANCE_TITLE'				=> 'Xin lỗi, VinaBB đang bảo trì',

	'NEWS'	=> 'Tin tức',

	'RANK_TITLES'	=> array(
		'ADMINISTRATOR'		=> 'Quản trị viên',
		'DEVELOPER'			=> 'Nhóm phát triển',
		'FOUNDER'			=> 'Sáng lập viên',
		'GLOBAL_MODERATOR'	=> 'Điều hành viên chính',
		'MANAGER'			=> 'Nhóm quản lý',
		'MEMBER'			=> 'Thành viên',
		'MODERATOR'			=> 'Điều hành viên',
		'SUPPORTER'			=> 'Nhóm hỗ trợ',
	),
	'RESOURCES'		=> 'Tài nguyên',

	'USER_ID'	=> 'ID tài khoản',

	'VINABB'	=> 'VinaBB',
));
