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
	'ACP_CAT_BB'				=> 'Thư viện phpBB',
	'ACP_CAT_VINABB'			=> 'VinaBB',
	'ACP_CAT_VINABB_SETTINGS'	=> 'Thiết lập',

	'BBCODE_C_HELP'	=> 'Chèn mã: [code]đoạn mã[/code] hay `mã một dòng`',
	'BOARD'			=> 'Diễn đàn',

	'CLOSE'			=> 'Đóng',
	'COMMUNITY'		=> 'Cộng đồng',
	'COORDINATES'	=> 'Tọa độ',

	'EMOTICON_TEXT'	=> array(
		'ALIEN'				=> '!@#$%',
		'ANGEL'				=> 'Sẽ có thiên thần sửa lỗi cho em',
		'ANGRY'				=> 'Phẫn nộ cái tốc độ',
		'ASTONISHED'		=> 'Nhanh nhẩu lộ mật khẩu',
		'AWKWARD'			=> 'Mã nguồn giấu dưới xuồng',
		'BEARD'				=> 'Thiếu dấu ngoặc nhọn {…} rồi',
		'BIG_GRIN'			=> 'Nâng cấp đừng hấp tấp',
		'BIG_SMILE'			=> 'Thấy quảng cáo bấm báo cáo',
		'BIG_SURPRISE'		=> 'Xác nhận đừng ân hận',
		'BLUSHING'			=> 'Mơ mộng gói mở rộng',
		'BLUSHING_WINK'		=> 'Hồn nhiên như lập trình viên',
		'CENSORED'			=> 'Tập tin chớ có xin',
		'COLD_SWEAT'		=> 'Phiên bản xài phát nản',
		'CONFOUNDED'		=> 'Xác thực đầy cơ cực',
		'CONFUSED'			=> 'Thông báo chả hiểu gì ráo',
		'COOL'				=> 'Tin tặc con cá sặc',
		'CRYING'			=> 'Dung lượng phải đi mượn',
		'DEVIL'				=> 'Quỷ quyệt cái trình duyệt',
		'DISAPPOINTED'		=> 'Hoang tàn cái diễn đàn',
		'DIZZY'				=> 'Hệ thống con bò rống',
		'EYEWINK'			=> 'Bất bình cái cấu hình',
		'FEARFUL'			=> 'Nông nổi chìm trong lỗi',
		'FROWN'				=> 'Giao diện như lưới điện',
		'GLOOMY'			=> 'Trang chủ cần giặt giũ',
		'HEE_HEE'			=> 'Dữ liệu ngâm củ kiệu',
		'HUFFING'			=> 'Bảo trì làm cái gì?',
		'IN_LOVE'			=> 'Em yêu phpBB',
		'KISS'				=> 'Tin nhắn nhìn muốn cắn',
		'KISS_BLUSHING'		=> 'Thành viên nói liên miên',
		'KISS_WINK'			=> 'Lùng sục cái thư mục',
		'KISSING'			=> 'Thất tình ta lập trình',
		'LAUGHING'			=> 'Chủ đề phải chỉnh tề',
		'LAUGHING_TEARS'	=> 'Cập nhật như đánh vật',
		'LOL'				=> 'Bài viết phải chi tiết',
		'MAD_DEVIL'			=> 'Chạng vạng như cái mạng',
		'MEOW'				=> 'Mã độc ngu gì vọc',
		'MONEY'				=> 'Thiếu dấu đô la rồi',
		'NO_IDEAS'			=> 'Máy tính cũng câm nính',
		'PACMAN'			=> 'Đánh dấu tuồn mã tấu',
		'RELIEVED'			=> 'Cài đặt đơ cái mặt',
		'SAD'				=> 'Quản trị mặt bí xị',
		'SAD_CRYING'		=> 'Chuyên mục cấm nói tục',
		'SCARED'			=> 'Chức năng coi bộ căng',
		'SCREAMING'			=> 'Đăng nhập run lập cập',
		'SHOCKED'			=> 'Gian nan tải cái trang',
		'SILLY'				=> 'Dòng lệnh nhìn muốn bệnh',
		'SLEEPING'			=> 'Hỗ trợ lâu thấy mợ',
		'SLEEPY'			=> 'Nước miếng làm dơ biến',
		'SMILE'				=> 'Cấp phép phải sao chép',
		'SMILING'			=> 'Viết hàm chớ càm ràm',
		'STRAIGHT'			=> 'Bi kịch mã biên dịch',
		'SURPRISED'			=> 'Chật vật đường bảo mật',
		'TEARS_OF_JOY'		=> 'Phát hành nổ tanh bành',
		'TONGUE'			=> 'Truy vấn hay gây hấn',
		'TONGUE_LICK'		=> 'Tìm kiếm trước khi la liếm',
		'TONGUE_OUT'		=> 'Điều hành viên thích xỏ xiên',
		'TONGUE_WINK'		=> 'Liên kết em đã chết',
		'UPSET'				=> 'Tùy chọn thật nhỏ mọn',
		'WEARY'				=> 'Lận đận cái ghi nhận',
		'WHO_AM_I'			=> '…',
		'WINKING'			=> 'Thiếu dấu chấm phẩy rồi',
		'WORLD_WEARY'		=> 'Cú pháp nhìn muốn ngáp',
		'WORRIED'			=> 'Hốt hoảng cái tài khoản',
		'WTF'				=> 'Biến tìm muốn tắt tiếng',
	),

	'FORUM_WAR'	=> 'Trò chơi',

	'G_MANAGEMENT_TEAM'	=> 'Nhóm quản lý',
	'G_MODERATOR_TEAM'	=> 'Điều hành viên',
	'G_SUPPORT_TEAM'	=> 'Nhóm hỗ trợ',
	'GENDER'			=> 'Giới tính',
	'GENDER_FEMALE'		=> 'Nữ',
	'GENDER_MALE'		=> 'Nam',
	'GITHUB'			=> 'GitHub',
	'GITHUB_PROFILE'	=> 'GitHub',

	'LANG_SWITCH'	=> '%1$s » %2$s',
	'LATITUDE'		=> 'Vĩ độ',
	'LONGITUDE'		=> 'Kinh độ',

	'MAINTENANCE_TEXT'				=> 'Zzz… Người quản trị đã chìm sâu vào giấc ngủ cùng với giấc mơ đẹp về ngày mai tươi sáng của phpBB. Bạn không thể đánh thức tên sống ảo này nhưng mẹ hắn thì có thể. Chúng tôi đang đi tìm bà ấy. Yêu bạn!',
	'MAINTENANCE_TEXT_TIME_END'		=> 'Thời gian kết thúc dự kiến: <strong>%s</strong>.',
	'MAINTENANCE_TEXT_TIME_LONG'	=> 'Zzz… Người quản trị đã chìm sâu vào giấc ngủ cùng với giấc mơ đẹp về ngày mai tươi sáng của phpBB. Bạn không thể đánh thức tên sống ảo này cho đến ngày <strong>%s</strong>. Mong bạn thông cảm cho chúng tôi. Yêu bạn!',
	'MAINTENANCE_TEXT_TIME_SHORT'	=> 'Zzz… Người quản trị đã chìm sâu vào giấc ngủ cùng với giấc mơ đẹp về ngày mai tươi sáng của phpBB. Hãy vui lòng trở lại vào lúc <strong>%s</strong> và tên sống ảo này có thể tỉnh giấc. Yêu bạn!',
	'MAINTENANCE_TEXT_TIMEZONE'		=> 'Múi giờ: %1$s (%2$s).',
	'MAINTENANCE_TITLE'				=> 'Xin lỗi, VinaBB đang bảo trì',
	'MAP'							=> 'Bản đồ',
	'MEMBERS'						=> 'Thành viên',

	'NEWS'		=> 'Tin tức',
	'NO_POST'	=> 'Bài viết không tồn tại.',

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

	'TOTAL_ACP_STYLES'	=> 'Số giao diện',
	'TOTAL_EXTS'		=> 'Số gói mở rộng',
	'TOTAL_LANGS'		=> 'Số gói ngôn ngữ',
	'TOTAL_STYLES'		=> 'Số giao diện',
	'TOTAL_TOOLS'		=> 'Số công cụ',

	'USER_ID'	=> 'ID tài khoản',

	'VINABB'	=> 'VinaBB',
));
