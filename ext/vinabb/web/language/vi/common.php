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
if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

/**
* All language files should use UTF-8 as their encoding
* and the files must not contain a BOM.
*/

$lang = array_merge($lang, [
	'ACP_CAT_BB'				=> 'Thư viện phpBB',
	'ACP_CAT_PORTAL'			=> 'Trang chủ',
	'ACP_CAT_VINABB'			=> 'VinaBB',
	'ACP_CAT_VINABB_SETTINGS'	=> 'Thiết lập',
	'ALL_TIMES'					=> 'Múi giờ %1$s',
	'ANALYTICS'					=> 'Phân tích',
	'AUTHOR_NAME'				=> 'Tên người phát triển',

	'BB'				=> 'Thư viện',
	'BB_ACP_STYLES'		=> 'Giao diện quản trị',
	'BB_AUTHORS'		=> 'Người phát triển',
	'BB_EXTS'			=> 'Gói mở rộng',
	'BB_LANGS'			=> 'Gói ngôn ngữ',
	'BB_LANGS_ALT'		=> 'Ngôn ngữ',
	'BB_STYLES'			=> 'Giao diện',
	'BB_SUBSCRIBERS'	=> 'Người theo dõi',
	'BB_TOOLS'			=> 'Công cụ',
	'BOARD'				=> 'Diễn đàn',

	'CATEGORY'				=> 'Danh mục',
	'CLOSE'					=> 'Đóng',
	'CODE'					=> 'Mã',
	'CODECLIMATE'			=> 'CodeClimate',
	'COMMENT'				=> 'Bình luận',
	'COMMENT_PENDING'		=> 'Bình luận chờ duyệt',
	'COMMENTS'				=> 'Bình luận',
	'COMMUNITY'				=> 'Cộng đồng',
	'CONFIRM_LOGOUT'		=> 'Bạn chắc chắn muốn thoát?',
	'COORDINATES'			=> 'Tọa độ',
	'COPY'					=> 'Chép',
	'COPY_ERROR'			=> 'Bấm Ctrl+C để sao chép',
	'COPY_ERROR_MAC'		=> 'Bấm ⌘+C để sao chép',
	'COPY_EXPLAIN'			=> 'Sao chép vào bộ nhớ',
	'COPY_SUCCESS'			=> 'Đã sao chép!',
	'COUNTER_ACP_STYLE'		=> [
		0	=> '0 giao diện',
		1	=> '%d giao diện',
		2	=> '%d giao diện'
	],
	'COUNTER_ARTICLE'		=> [
		0	=> '0 bài viết',
		1	=> '%d bài viết',
		2	=> '%d bài viết'
	],
	'COUNTER_COMMENT'		=> [
		0	=> '0 bình luận',
		1	=> '%d bình luận',
		2	=> '%d bình luận'
	],
	'COUNTER_EXT'			=> [
		0	=> '0 gói mở rộng',
		1	=> '%d gói mở rộng',
		2	=> '%d gói mở rộng'
	],
	'COUNTER_LANG'			=> [
		0	=> '0 gói ngôn ngữ',
		1	=> '%d gói ngôn ngữ',
		2	=> '%d gói ngôn ngữ'
	],
	'COUNTER_STYLE'			=> [
		0	=> '0 giao diện',
		1	=> '%d giao diện',
		2	=> '%d giao diện'
	],
	'COUNTER_TOOL'			=> [
		0	=> '0 công cụ',
		1	=> '%d công cụ',
		2	=> '%d công cụ'
	],
	'CURRENCY'				=> 'Tiền tệ',
	'CURRENT_FILE'			=> 'Tập tin hiện tại',
	'CURRENT_IMAGE'			=> 'Hình hiện tại',

	'DEBUG'				=> 'Dò lỗi',
	'DEBUG_INFO'		=> 'Thông số đánh giá',
	'DESCRIPTION'		=> 'Giới thiệu',
	'DESIGNER'			=> 'Người thiết kế',
	'DEVELOPER'			=> 'Người phát triển',
	'DONATE'			=> 'Đóng góp',
	'DONATE_BANK'		=> 'Tên ngân hàng',
	'DONATE_BANK_ACC'	=> 'Số tài khoản',
	'DONATE_BANK_SWIFT'	=> 'Mã SWIFT',
	'DONATE_BUTTON'		=> 'Tiếp thêm máu',
	'DONATE_CURRENCY'	=> 'Mã tiền tệ',
	'DONATE_DONE'		=> 'Đã thanh toán',
	'DONATE_OWNER'		=> 'Tên tài khoản',
	'DOWNLOADS'			=> 'Lượt tải về',

	'EMOTICON_TEXT'				=> [
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
		'SAD_CRYING'		=> 'Nước miếng làm dơ biến',
		'SCARED'			=> 'Chức năng coi bộ căng',
		'SCREAMING'			=> 'Đăng nhập run lập cập',
		'SHOCKED'			=> 'Gian nan tải cái trang',
		'SILLY'				=> 'Dòng lệnh nhìn muốn bệnh',
		'SLEEPING'			=> 'Hỗ trợ lâu thấy mợ',
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
		'WEEPING'			=> 'Chuyên mục cấm nói tục',
		'WHO_AM_I'			=> '…',
		'WINKING'			=> 'Thiếu dấu chấm phẩy rồi',
		'WORLD_WEARY'		=> 'Cú pháp nhìn muốn ngáp',
		'WORRIED'			=> 'Hốt hoảng cái tài khoản',
		'WTF'				=> 'Biến tìm muốn tắt tiếng'
	],
	'ENGLISH'					=> 'Tiếng Anh',
	'ERROR_FORUM_LANG_EMPTY'	=> 'Chưa chọn ngôn ngữ.',
	'ERROR_USER_ID_EMPTY'		=> 'Người dùng không xác định.',
	'ERROR_USER_ID_NOT_EXISTS'	=> 'Người dùng không tồn tại.',

	'FIRST_NAME'			=> 'Tên',
	'FOOTER_FLAG_TEXT'		=> 'Cống hiến hết mình vì Tổ Quốc Việt Nam Xã Hội Chủ Nghĩa',
	'FOOTER_FLAG_TITLE'		=> 'Code in Viet Nam',
	'FOOTER_HISTORY_TEXT'	=> '
		<i class="icon-heart padding-5"></i> 17/07/2004: Yêu phpBB từ phiên bản 2.0.10.<br>
		<i class="icon-emotsmile padding-5"></i> 22/10/2006: Cất tiếng cười chào đời.<br>
		<i class="icon-rocket padding-5"></i> 11/06/2007: Chính thức định cư trên Olympus, Sao Hỏa.<br>
		<i class="icon-feed padding-5"></i> 11/06/2009: Mất liên lạc với Trái Đất. [ <a href="http://2007.vinabb.vn/">Phiên bản 2007</a> ]<br>
		<i class="icon-graph padding-5"></i> 28/07/2016: Trôi dạt đến mặt trăng Rhea, Sao Thổ.<br>
		<i class="icon-star padding-5"></i> 12/12/2016: Cuộc hành trình mới lại bắt đầu…
	',
	'FOOTER_HISTORY_TITLE'	=> 'Chuyện tình VinaBB',
	'FOOTER_MANAGER_ROLE'	=> 'Đơn vị chủ quản',
	'FOOTER_MANAGER_TEXT'	=> 'Chúng tôi <strong class="text-danger">chịu trách nhiệm</strong> toàn bộ nội dung có trên <strong class="text-info">VinaBB.vn</strong> trước pháp luật.',
	'FOOTER_RULE_EXPLAIN'	=> 'Chúng tôi không thích viết nội quy dài dòng. Bạn tự hiểu những gì đúng đắn nên làm và chúng tôi tự xử những gì vi phạm.',
	'FOOTER_RULE_TEXT'		=> '
		<li>Không đề cập chính trị, tôn giáo, nội dung đồi trụy.</li>
		<li>Giữ gìn sự trong sáng của Tiếng Việt.</li>
		<li>Không chia sẻ phần mềm vi phạm bản quyền.</li>
		<li>Không rao vặt và không nhận đặt quảng cáo.</li>
		<li>Dù trong túi hết tiền thì diễn đàn phpBB của anh cũng phải ngay ngắn.</li>
	',
	'FOOTER_RULE_TITLE'		=> 'Quan điểm',
	'FORUM_LANG'			=> 'Ngôn ngữ',
	'FORUM_NAME_SEO'		=> 'Tên SEO chuyên mục',

	'G_DEVELOPMENT_TEAM'	=> 'Nhóm phát triển',
	'G_FOUNDERS'			=> 'Sáng lập viên',
	'G_MANAGEMENT_TEAM'		=> 'Nhóm quản lý',
	'G_MODERATOR_TEAM'		=> 'Điều hành viên',
	'G_RELEASE_TEAM'		=> 'Nhóm phát hành',
	'G_SUPPORT_TEAM'		=> 'Nhóm hỗ trợ',
	'GITHUB'				=> 'GitHub',

	'INSIGHT'	=> 'SensioLabs Insight',

	'LABEL_PERCENT'			=> '%s%%',
	'LABEL_YEAR'			=> 'Năm %d',
	'LANG_SWITCH'			=> '%1$s » %2$s',
	'LANGUAGE'				=> 'Ngôn ngữ',
	'LAST_ACTIVE'			=> 'Truy cập lần cuối',
	'LAST_NAME'				=> 'Họ',
	'LATEST_ACP_STYLES'		=> 'Giao diện quản trị mới',
	'LATEST_EXTS'			=> 'Gói mở rộng mới',
	'LATEST_IVN'			=> 'Gói Việt hóa',
	'LATEST_IVN_TITLE'		=> 'Tải về phpBB iVN',
	'LATEST_PHP'			=> 'Phiên bản PHP',
	'LATEST_PHP_TITLE'		=> 'Thông tin PHP',
	'LATEST_PHPBB'			=> 'Phiên bản phpBB',
	'LATEST_PHPBB_TITLE'	=> 'Tải về phpBB',
	'LATEST_POSTS'			=> 'Bài viết mới',
	'LATEST_STYLES'			=> 'Giao diện mới',
	'LATEST_TOOLS'			=> 'Công cụ mới',
	'LATEST_TOPICS'			=> 'Chủ đề mới',
	'LATEST_USERS'			=> 'Thành viên mới',
	'LATEST_VERSION'		=> 'Phiên bản mới nhất',
	'LATEST_VERSIONS'		=> 'Phiên bản mới nhất',
	'LATEST_VERSIONS_INFO'	=> 'Thông tin phiên bản mới nhất',
	'LATEST_VINABB'			=> 'Phiên bản VinaBB',
	'LATEST_VINABB_TITLE'	=> 'Mã nguồn VinaBB.vn',
	'LATITUDE'				=> 'Vĩ độ',
	'LONGITUDE'				=> 'Kinh độ',

	'MAINTENANCE_TEXT'				=> 'Zzz… Người quản trị đã chìm sâu vào giấc ngủ cùng với giấc mơ đẹp về ngày mai tươi sáng của phpBB. Bạn không thể đánh thức tên sống ảo này nhưng mẹ hắn thì có thể. Chúng tôi đang đi tìm bà ấy. Yêu bạn!',
	'MAINTENANCE_TEXT_TIME_END'		=> 'Thời gian kết thúc dự kiến: <strong>%s</strong>.',
	'MAINTENANCE_TEXT_TIME_LONG'	=> 'Zzz… Người quản trị đã chìm sâu vào giấc ngủ cùng với giấc mơ đẹp về ngày mai tươi sáng của phpBB. Bạn không thể đánh thức tên sống ảo này cho đến ngày <strong>%s</strong>. Mong bạn thông cảm cho chúng tôi. Yêu bạn!',
	'MAINTENANCE_TEXT_TIME_SHORT'	=> 'Zzz… Người quản trị đã chìm sâu vào giấc ngủ cùng với giấc mơ đẹp về ngày mai tươi sáng của phpBB. Hãy vui lòng trở lại vào lúc <strong>%s</strong> và tên sống ảo này có thể tỉnh giấc. Yêu bạn!',
	'MAINTENANCE_TEXT_TIMEZONE'		=> 'Múi giờ: %1$s (%2$s).',
	'MAINTENANCE_TITLE'				=> 'Xin lỗi, VinaBB đang bảo trì',
	'MAP'							=> 'Bản đồ',
	'MEMBERS'						=> 'Thành viên',

	'NEW'					=> 'Mới',
	'NEWS'					=> 'Tin tức',
	'NO_BB_ACP_STYLE'		=> 'Giao diện quản trị không tồn tại.',
	'NO_BB_ACP_STYLE_ID'	=> 'Giao diện quản trị không xác định.',
	'NO_BB_ACP_STYLES'		=> 'Không có giao diện quản trị nào.',
	'NO_BB_CAT'				=> 'Danh mục không tồn tại.',
	'NO_BB_CAT_ID'			=> 'Danh mục không xác định.',
	'NO_BB_EXT'				=> 'Gói mở rộng không tồn tại.',
	'NO_BB_EXT_ID'			=> 'Gói mở rộng không xác định.',
	'NO_BB_EXTS'			=> 'Không có gói mở rộng nào.',
	'NO_BB_LANG'			=> 'Gói ngôn ngữ không tồn tại.',
	'NO_BB_LANG_ID'			=> 'Gói ngôn ngữ không xác định.',
	'NO_BB_LANGS'			=> 'Không có gói ngôn ngữ nào.',
	'NO_BB_STYLE'			=> 'Giao diện không tồn tại.',
	'NO_BB_STYLE_ID'		=> 'Giao diện không xác định.',
	'NO_BB_STYLES'			=> 'Không có giao diện nào.',
	'NO_BB_TOOL'			=> 'Công cụ không tồn tại.',
	'NO_BB_TOOL_ID'			=> 'Công cụ không xác định.',
	'NO_BB_TOOLS'			=> 'Không có công cụ nào.',
	'NO_PORTAL_ARTICLE'		=> 'Bài viết không tồn tại.',
	'NO_PORTAL_ARTICLES'	=> 'Không có bài viết nào.',
	'NO_POST'				=> 'Bài viết không tồn tại.',
	'NONE'					=> 'Không',

	'ORIGINAL_URL'	=> 'Liên kết gốc',
	'OS'			=> 'Hệ điều hành',
	'OS_ALL'		=> 'Đa nền tảng',

	'PAGE'						=> 'Trang',
	'PAYPAL'					=> 'PayPal',
	'PHONE'						=> 'Điện thoại',
	'PHP_VERSION_X'				=> 'PHP %s',
	'PHPBB_BRANCH'				=> 'Nhánh phpBB',
	'PHPBB_COM'					=> 'phpBB.com',
	'PHPBB_IVN_EXPLAIN'			=> 'Gói ngôn ngữ tiếng Việt cho phpBB.',
	'PHPBB_IVN_VERSION_X'		=> 'phpBB iVN %s',
	'PHPBB_IVNPLUS_EXPLAIN'		=> 'Gói ngôn ngữ tiếng Việt cho các gói mở rộng của phpBB.',
	'PHPBB_IVNPLUS_VERSION_X'	=> 'phpBB iVN+ %s',
	'PHPBB_VERSION'				=> 'Phiên bản phpBB',
	'PHPBB_VERSION_X'			=> 'phpBB %s',
	'PORTAL_ARTICLE'			=> 'Bài viết',
	'POWERED_BY'				=> 'Sử dụng phần mềm <a href="https://www.phpbb.com/" data-tooltip="true" title="&copy; phpBB Limited">phpBB</a> <i class="fa fa-heart animate-pulse text-danger"></i>',
	'PRICE'						=> 'Giá',
	'PRINT'						=> 'In',

	'RANK_TITLES'	=> [
		'ADMINISTRATOR'		=> 'Quản trị viên',
		'DEVELOPER'			=> 'Nhóm phát triển',
		'FOUNDER'			=> 'Sáng lập viên',
		'GLOBAL_MODERATOR'	=> 'Điều hành viên chính',
		'MANAGER'			=> 'Nhóm quản lý',
		'MEMBER'			=> 'Thành viên',
		'MODERATOR'			=> 'Điều hành viên',
		'RELEASE_MANAGER'	=> 'Nhóm phát hành',
		'SUPPORTER'			=> 'Nhóm hỗ trợ'
	],
	'REAL_NAME'		=> 'Tên thật',
	'RESOURCES'		=> 'Tài nguyên',
	'ROUTE_NAME'	=> 'Tên đường dẫn',

	'SAVE'					=> 'Lưu',
	'SCRUTINIZER'			=> 'Scrutinizer',
	'SELECT_CATEGORY'		=> 'Chọn danh mục',
	'SELECT_GROUP'			=> 'Chọn nhóm',
	'SELECT_ICON'			=> 'Chọn biểu tượng',
	'SELECT_LANGUAGE'		=> 'Chọn ngôn ngữ',
	'SELECT_OS'				=> 'Chọn hệ điều hành',
	'SELECT_PAGE'			=> 'Chọn trang',
	'SELECT_PHPBB_VERSION'	=> 'Chọn phiên bản phpBB',
	'SELECT_VERSION'		=> 'Chọn phiên bản',
	'SHARE'					=> 'Chia sẻ',
	'SHARE_ON'				=> 'Chia sẻ trên %s',
	'SOCIAL_ACCOUNTS'		=> 'Tài khoản mạng xã hội',
	'SOCIAL_LINKS'			=> 'Liên kết mạng xã hội',

	'TAGS'				=> 'Thẻ',
	'TOGGLE'			=> 'Hiện/Ẩn',
	'TOTAL_ACP_STYLES'	=> 'Số giao diện',
	'TOTAL_ARTICLES'	=> 'Số bài viết',
	'TOTAL_BB_ITEMS'	=> 'Số tài nguyên',
	'TOTAL_EXTS'		=> 'Số gói mở rộng',
	'TOTAL_LANGS'		=> 'Số gói ngôn ngữ',
	'TOTAL_STYLES'		=> 'Số giao diện',
	'TOTAL_TOOLS'		=> 'Số công cụ',
	'TRAVIS'			=> 'Travis CI',

	'UNKNOWN'			=> 'Không biết',
	'UPLOAD_NEW_FILE'	=> 'Tải tập tin mới lên',
	'UPLOAD_NEW_IMAGE'	=> 'Tải hình mới lên',
	'USER_ID'			=> 'ID tài khoản',

	'VARNAME'			=> 'Tên biến',
	'VERSION'			=> 'Phiên bản',
	'VIETNAMESE'		=> 'Tiếng Việt',
	'VIEW_DETAILS'		=> 'Xem chi tiết',
	'VINABB'			=> 'VinaBB',
	'VINABB_VERSION'	=> 'Phiên bản VinaBB',
	'VINABB_VERSION_X'	=> 'VinaBB %s',
	'VINABB_VN'			=> 'VinaBB.vn',

	'WEBSITE_FUND'	=> 'Kinh phí hoạt động',
]);
