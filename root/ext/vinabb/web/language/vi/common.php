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
		'ANGRY'				=> 'Nông nổi chìm trong lỗi',
		'ASTONISHED'		=> 'Nhanh nhẩu lộ mật khẩu',
		'AWKWARD'			=> 'Mã nguồn giấu dưới xuồng',
		'BEARD'				=> 'Thiếu dấu ngoặc nhọn {…} rồi',
		'BIG_GRIN'			=> 'Cười cho bớt lười',
		'BIG_SMILE'			=> 'Thấy quảng cáo bấm báo cáo',
		'BIG_SURPRISE'		=> 'Thật không thể tin nổi',
		'BLUSHING'			=> 'Mắc cỡ',
		'BLUSHING_WINK'		=> 'Hồn nhiên như lập trình viên',
		'CENSORED'			=> 'Tập tin chớ có xin',
		'COLD_SWEAT'		=> 'Xấu cần phấn đấu',
		'CONFOUNDED'		=> 'Ứ chịu đâu',
		'CONFUSED'			=> 'Cấp phép phải sao chép',
		'COOL'				=> 'Tin tặc con cá sặc',
		'CRYING'			=> 'Hu hu',
		'DEVIL'				=> 'Quỷ quyệt cái trình duyệt',
		'DISAPPOINTED'		=> 'Thất vọng',
		'DIZZY'				=> 'Mơ mộng gói mở rộng',
		'EYEWINK'			=> 'Hãy đợi đấy!',
		'FEARFUL'			=> 'Đăng nhập run lập cập',
		'FROWN'				=> 'Giao diện như lưới điện',
		'GLOOMY'			=> 'Trang chủ như cái hũ',
		'HEE_HEE'			=> 'Dữ liệu ngâm củ kiệu',
		'HUFFING'			=> 'Bảo trì làm cái gì?',
		'IN_LOVE'			=> 'Em yêu phpBB',
		'KISS'				=> 'Tin nhắn nhìn muốn cắn',
		'KISS_BLUSHING'		=> 'Thành viên nói liên miên',
		'KISS_WINK'			=> 'Lùng sục cái thư mục',
		'KISSING'			=> 'Lập trình như thằng thất tình',
		'LAUGHING'			=> 'Xạo xóa tài khoản liền',
		'LAUGHING_TEARS'	=> 'Cập nhật như đánh vật',
		'LOL'				=> 'Ha ha',
		'MAD_DEVIL'			=> 'Mạng nó chạng vạng',
		'MEOW'				=> 'Mèo cũng biết cài phpBB',
		'MONEY'				=> 'Thiếu dấu đô la rồi',
		'NO_IDEAS'			=> 'Máy tính cũng câm nính',
		'PACMAN'			=> 'Chém gió',
		'RELIEVED'			=> 'Cài đặt đơ cái mặt',
		'SAD'				=> 'Quản trị mặt bí xị',
		'SAD_CRYING'		=> 'Phẫn nộ cái tốc độ',
		'SCARED'			=> 'Chức năng coi bộ căng',
		'SCREAMING'			=> 'Đừng hack em mà',
		'SHOCKED'			=> 'Chuẩn không cần chỉnh',
		'SILLY'				=> 'Mã độc ngu gì vọc',
		'SLEEPING'			=> 'Hỗ trợ lâu dễ sợ',
		'SLEEPY'			=> 'Nước miếng làm dơ biến',
		'SMILE'				=> 'Hi hi',
		'SMILING'			=> 'Hàm viết ba xàm',
		'STRAIGHT'			=> 'Bi kịch cái trình biên dịch',
		'SURPRISED'			=> 'Chật vật đường bảo mật',
		'TEARS_OF_JOY'		=> 'Cười ra nước mắt',
		'TONGUE'			=> 'Truy vấn hay gây hấn',
		'TONGUE_LICK'		=> 'Tìm kiếm trước khi la liếm',
		'TONGUE_OUT'		=> 'Điều hành viên thích xỏ xiên',
		'TONGUE_WINK'		=> 'Anh đây cóc sợ',
		'UPSET'				=> 'Thông báo chả hiểu gì ráo',
		'WEARY'				=> 'Tui hận cái ghi nhận',
		'WHO_AM_I'			=> '…',
		'WINKING'			=> 'Thiếu dấu chấm phẩy rồi',
		'WORLD_WEARY'		=> 'Cú pháp nhìn muốn ngáp',
		'WORRIED'			=> 'Quá nhanh quá nguy hiểm',
		'WTF'				=> 'Biến tìm muốn tắt tiếng',
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

	'LANG_SWITCH'	=> '%1$s » %2$s',

	'MAINTENANCE_TEXT'				=> 'Zzz… Người quản trị đã chìm sâu vào giấc ngủ cùng với giấc mơ đẹp về ngày mai tươi sáng của phpBB. Bạn không thể đánh thức tên sống ảo này nhưng mẹ hắn thì có thể. Chúng tôi đang đi tìm bà ấy. Yêu bạn!',
	'MAINTENANCE_TEXT_TIME_END'		=> 'Thời gian kết thúc dự kiến: <strong>%s</strong>.',
	'MAINTENANCE_TEXT_TIME_LONG'	=> 'Zzz… Người quản trị đã chìm sâu vào giấc ngủ cùng với giấc mơ đẹp về ngày mai tươi sáng của phpBB. Bạn không thể đánh thức tên sống ảo này cho đến ngày <strong>%s</strong>. Mong bạn thông cảm cho chúng tôi. Yêu bạn!',
	'MAINTENANCE_TEXT_TIME_SHORT'	=> 'Zzz… Người quản trị đã chìm sâu vào giấc ngủ cùng với giấc mơ đẹp về ngày mai tươi sáng của phpBB. Hãy vui lòng trở lại vào lúc <strong>%s</strong> và tên sống ảo này có thể tỉnh giấc. Yêu bạn!',
	'MAINTENANCE_TEXT_TIMEZONE'		=> 'Múi giờ: %1$s (%2$s).',
	'MAINTENANCE_TITLE'				=> 'Xin lỗi, VinaBB đang bảo trì',
	'MEMBERS'						=> 'Thành viên',

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
