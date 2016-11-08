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
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\file_downloader */
	protected $file_downloader;

	/** @var string */
	protected $bb_items_table;

	/** @var string */
	protected $portal_articles_table;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\file_downloader $file_downloader
	* @param string $bb_items_table
	* @param string $portal_articles_table
	*/
	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\phpbb\file_downloader $file_downloader,
		$bb_items_table,
		$portal_articles_table
	)
	{
		$this->db = $db;
		$this->file_downloader = $file_downloader;
		$this->bb_items_table = $bb_items_table;
		$this->portal_articles_table = $portal_articles_table;
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

	/**
	* List of font icons
	*
	* @return array
	*/
	public function get_icons()
	{
		return [
			// Our data
			'data'	=> [
				'simple_line'	=> [
					'name'		=> 'Simple Line',
					'prefix'	=> 'icon-',
					'char'		=> '-'
				],
				'font_awesome'	=> [
					'name'		=> 'Font Awesome',
					'prefix'	=> 'fa fa-',
					'char'		=> '-'
				]
			],

			// Simple Line Icons 2.4.1
			'simple_line'			=> ['action-redo', 'action-undo', 'anchor', 'arrow-down', 'arrow-down-circle', 'arrow-left', 'arrow-left-circle', 'arrow-right', 'arrow-right-circle', 'arrow-up', 'arrow-up-circle', 'badge', 'bag', 'ban', 'basket', 'basket-loaded', 'bell', 'book-open', 'briefcase', 'bubble', 'bubbles', 'bulb', 'calculator', 'calendar', 'call-end', 'call-in', 'call-out', 'camera', 'camrecorder', 'chart', 'check', 'chemistry', 'clock', 'close', 'cloud-download', 'cloud-upload', 'compass', 'control-end', 'control-forward', 'control-pause', 'control-play', 'control-rewind', 'control-start', 'credit-card', 'crop', 'cup', 'cursor', 'cursor-move', 'diamond', 'direction', 'directions', 'disc', 'dislike', 'doc', 'docs', 'drawer', 'drop', 'earphones', 'earphones-alt', 'emotsmile', 'energy', 'envelope', 'envelope-letter', 'envelope-open', 'equalizer', 'event', 'exclamation', 'eye', 'eyeglass', 'feed', 'film', 'fire', 'flag', 'folder', 'folder-alt', 'frame', 'game-controller', 'ghost', 'globe', 'globe-alt', 'graduation', 'graph', 'grid', 'handbag', 'heart', 'home', 'hourglass', 'info', 'key', 'layers', 'like', 'link', 'list', 'location-pin', 'lock', 'lock-open', 'login', 'logout', 'loop', 'magic-wand', 'magnet', 'magnifier', 'magnifier-add', 'magnifier-remove', 'map', 'menu', 'microphone', 'minus', 'mouse', 'music-tone', 'music-tone-alt', 'mustache', 'note', 'notebook', 'options', 'options-vertical', 'organization', 'paper-clip', 'paper-plane', 'paypal', 'pencil', 'people', 'phone', 'picture', 'pie-chart', 'pin', 'plane', 'playlist', 'plus', 'power', 'present', 'printer', 'puzzle', 'question', 'refresh', 'reload', 'rocket', 'screen-desktop', 'screen-smartphone', 'screen-tablet', 'settings', 'share', 'share-alt', 'shield', 'shuffle', 'size-actual', 'size-fullscreen', 'social-behance', 'social-dribbble', 'social-dropbox', 'social-facebook', 'social-foursqare', 'social-github', 'social-google', 'social-instagram', 'social-linkedin', 'social-pinterest', 'social-reddit', 'social-skype', 'social-soundcloud', 'social-spotify', 'social-steam', 'social-stumbleupon', 'social-tumblr', 'social-twitter', 'social-vkontakte', 'social-youtube', 'speech', 'speedometer', 'star', 'support', 'symbol-female', 'symbol-male', 'tag', 'target', 'trash', 'trophy', 'umbrella', 'user', 'user-female', 'user-follow', 'user-following', 'user-unfollow', 'vector', 'volume-1', 'volume-2', 'volume-off', 'wallet', 'wrench'],

			// Font Awesome 4.7.0
			'font_awesome'			=> ['500px', 'address-book', 'address-book-o', 'address-card', 'address-card-o', 'adjust', 'adn', 'align-center', 'align-justify', 'align-left', 'align-right', 'amazon', 'ambulance', 'american-sign-language-interpreting', 'anchor', 'android', 'angellist', 'angle-double-down', 'angle-double-left', 'angle-double-right', 'angle-double-up', 'angle-down', 'angle-left', 'angle-right', 'angle-up', 'apple', 'archive', 'area-chart', 'arrow-circle-down', 'arrow-circle-left', 'arrow-circle-o-down', 'arrow-circle-o-left', 'arrow-circle-o-right', 'arrow-circle-o-up', 'arrow-circle-right', 'arrow-circle-up', 'arrow-down', 'arrow-left', 'arrow-right', 'arrow-up', 'arrows', 'arrows-alt', 'arrows-h', 'arrows-v', 'assistive-listening-systems', 'asterisk', 'at', 'audio-description', 'backward', 'balance-scale', 'ban', 'bandcamp', 'bar-chart', 'barcode', 'bars', 'bath', 'battery-empty', 'battery-full', 'battery-half', 'battery-quarter', 'battery-three-quarters', 'bed', 'beer', 'behance', 'behance-square', 'bell', 'bell-o', 'bell-slash', 'bell-slash-o', 'bicycle', 'binoculars', 'birthday-cake', 'bitbucket', 'bitbucket-square', 'black-tie', 'blind', 'bluetooth', 'bluetooth-b', 'bold', 'bolt', 'bomb', 'book', 'bookmark', 'bookmark-o', 'braille', 'briefcase', 'btc', 'bug', 'building', 'building-o', 'bullhorn', 'bullseye', 'bus', 'buysellads', 'calculator', 'calendar', 'calendar-check-o', 'calendar-minus-o', 'calendar-o', 'calendar-plus-o', 'calendar-times-o', 'camera', 'camera-retro', 'car', 'caret-down', 'caret-left', 'caret-right', 'caret-square-o-down', 'caret-square-o-left', 'caret-square-o-right', 'caret-square-o-up', 'caret-up', 'cart-arrow-down', 'cart-plus', 'cc', 'cc-amex', 'cc-diners-club', 'cc-discover', 'cc-jcb', 'cc-mastercard', 'cc-paypal', 'cc-stripe', 'cc-visa', 'certificate', 'chain-broken', 'check', 'check-circle', 'check-circle-o', 'check-square', 'check-square-o', 'chevron-circle-down', 'chevron-circle-left', 'chevron-circle-right', 'chevron-circle-up', 'chevron-down', 'chevron-left', 'chevron-right', 'chevron-up', 'child', 'chrome', 'circle', 'circle-o', 'circle-o-notch', 'circle-thin', 'clipboard', 'clock-o', 'clone', 'cloud', 'cloud-download', 'cloud-upload', 'code', 'code-fork', 'codepen', 'codiepie', 'coffee', 'cog', 'cogs', 'columns', 'comment', 'comment-o', 'commenting', 'commenting-o', 'comments', 'comments-o', 'compass', 'compress', 'connectdevelop', 'contao', 'copyright', 'creative-commons', 'credit-card', 'credit-card-alt', 'crop', 'crosshairs', 'css3', 'cube', 'cubes', 'cutlery', 'dashcube', 'database', 'deaf', 'delicious', 'desktop', 'deviantart', 'diamond', 'digg', 'dot-circle-o', 'download', 'dribbble', 'dropbox', 'drupal', 'edge', 'eercast', 'eject', 'ellipsis-h', 'ellipsis-v', 'empire', 'envelope', 'envelope-o', 'envelope-open', 'envelope-open-o', 'envelope-square', 'envira', 'eraser', 'etsy', 'eur', 'exchange', 'exclamation', 'exclamation-circle', 'exclamation-triangle', 'expand', 'expeditedssl', 'external-link', 'external-link-square', 'eye', 'eye-slash', 'eyedropper', 'facebook', 'facebook-official', 'facebook-square', 'fast-backward', 'fast-forward', 'fax', 'female', 'fighter-jet', 'file', 'file-archive-o', 'file-audio-o', 'file-code-o', 'file-excel-o', 'file-image-o', 'file-o', 'file-pdf-o', 'file-powerpoint-o', 'file-text', 'file-text-o', 'file-video-o', 'file-word-o', 'files-o', 'film', 'filter', 'fire', 'fire-extinguisher', 'firefox', 'first-order', 'flag', 'flag-checkered', 'flag-o', 'flask', 'flickr', 'floppy-o', 'folder', 'folder-o', 'folder-open', 'folder-open-o', 'font', 'font-awesome', 'fonticons', 'fort-awesome', 'forumbee', 'forward', 'foursquare', 'free-code-camp', 'frown-o', 'futbol-o', 'gamepad', 'gavel', 'gbp', 'genderless', 'get-pocket', 'gg', 'gg-circle', 'gift', 'git', 'git-square', 'github', 'github-alt', 'github-square', 'gitlab', 'glass', 'glide', 'glide-g', 'globe', 'google', 'google-plus', 'google-plus-official', 'google-plus-square', 'google-wallet', 'graduation-cap', 'gratipay', 'grav', 'h-square', 'hacker-news', 'hand-lizard-o', 'hand-o-down', 'hand-o-left', 'hand-o-right', 'hand-o-up', 'hand-paper-o', 'hand-peace-o', 'hand-pointer-o', 'hand-rock-o', 'hand-scissors-o', 'hand-spock-o', 'handshake-o', 'hashtag', 'hdd-o', 'header', 'headphones', 'heart', 'heart-o', 'heartbeat', 'history', 'home', 'hospital-o', 'hourglass', 'hourglass-end', 'hourglass-half', 'hourglass-o', 'hourglass-start', 'houzz', 'html5', 'i-cursor', 'id-badge', 'id-card', 'id-card-o', 'ils', 'imdb', 'inbox', 'indent', 'industry', 'info', 'info-circle', 'inr', 'instagram', 'internet-explorer', 'ioxhost', 'italic', 'joomla', 'jpy', 'jsfiddle', 'key', 'keyboard-o', 'krw', 'language', 'laptop', 'lastfm', 'lastfm-square', 'leaf', 'leanpub', 'lemon-o', 'level-down', 'level-up', 'life-ring', 'lightbulb-o', 'line-chart', 'link', 'linkedin', 'linkedin-square', 'linode', 'linux', 'list', 'list-alt', 'list-ol', 'list-ul', 'location-arrow', 'lock', 'long-arrow-down', 'long-arrow-left', 'long-arrow-right', 'long-arrow-up', 'low-vision', 'magic', 'magnet', 'male', 'map', 'map-marker', 'map-o', 'map-pin', 'map-signs', 'mars', 'mars-double', 'mars-stroke', 'mars-stroke-h', 'mars-stroke-v', 'maxcdn', 'meanpath', 'medium', 'medkit', 'meetup', 'meh-o', 'mercury', 'microchip', 'microphone', 'microphone-slash', 'minus', 'minus-circle', 'minus-square', 'minus-square-o', 'mixcloud', 'mobile', 'modx', 'money', 'moon-o', 'motorcycle', 'mouse-pointer', 'music', 'neuter', 'newspaper-o', 'object-group', 'object-ungroup', 'odnoklassniki', 'odnoklassniki-square', 'opencart', 'openid', 'opera', 'optin-monster', 'outdent', 'pagelines', 'paint-brush', 'paper-plane', 'paper-plane-o', 'paperclip', 'paragraph', 'pause', 'pause-circle', 'pause-circle-o', 'paw', 'paypal', 'pencil', 'pencil-square', 'pencil-square-o', 'percent', 'phone', 'phone-square', 'picture-o', 'pie-chart', 'pied-piper', 'pied-piper-alt', 'pied-piper-pp', 'pinterest', 'pinterest-p', 'pinterest-square', 'plane', 'play', 'play-circle', 'play-circle-o', 'plug', 'plus', 'plus-circle', 'plus-square', 'plus-square-o', 'podcast', 'power-off', 'print', 'product-hunt', 'puzzle-piece', 'qq', 'qrcode', 'question', 'question-circle', 'question-circle-o', 'quora', 'quote-left', 'quote-right', 'random', 'ravelry', 'rebel', 'recycle', 'reddit', 'reddit-alien', 'reddit-square', 'refresh', 'registered', 'renren', 'repeat', 'reply', 'reply-all', 'retweet', 'road', 'rocket', 'rss', 'rss-square', 'rub', 'safari', 'scissors', 'scribd', 'search', 'search-minus', 'search-plus', 'sellsy', 'server', 'share', 'share-alt', 'share-alt-square', 'share-square', 'share-square-o', 'shield', 'ship', 'shirtsinbulk', 'shopping-bag', 'shopping-basket', 'shopping-cart', 'shower', 'sign-in', 'sign-language', 'sign-out', 'signal', 'simplybuilt', 'sitemap', 'skyatlas', 'skype', 'slack', 'sliders', 'slideshare', 'smile-o', 'snapchat', 'snapchat-ghost', 'snapchat-square', 'snowflake-o', 'sort', 'sort-alpha-asc', 'sort-alpha-desc', 'sort-amount-asc', 'sort-amount-desc', 'sort-asc', 'sort-desc', 'sort-numeric-asc', 'sort-numeric-desc', 'soundcloud', 'space-shuttle', 'spinner', 'spoon', 'spotify', 'square', 'square-o', 'stack-exchange', 'stack-overflow', 'star', 'star-half', 'star-half-o', 'star-o', 'steam', 'steam-square', 'step-backward', 'step-forward', 'stethoscope', 'sticky-note', 'sticky-note-o', 'stop', 'stop-circle', 'stop-circle-o', 'street-view', 'strikethrough', 'stumbleupon', 'stumbleupon-circle', 'subscript', 'subway', 'suitcase', 'sun-o', 'superpowers', 'superscript', 'table', 'tablet', 'tachometer', 'tag', 'tags', 'tasks', 'taxi', 'telegram', 'television', 'tencent-weibo', 'terminal', 'text-height', 'text-width', 'th', 'th-large', 'th-list', 'themeisle', 'thermometer-empty', 'thermometer-full', 'thermometer-half', 'thermometer-quarter', 'thermometer-three-quarters', 'thumb-tack', 'thumbs-down', 'thumbs-o-down', 'thumbs-o-up', 'thumbs-up', 'ticket', 'times', 'times-circle', 'times-circle-o', 'tint', 'toggle-off', 'toggle-on', 'trademark', 'train', 'transgender', 'transgender-alt', 'trash', 'trash-o', 'tree', 'trello', 'tripadvisor', 'trophy', 'truck', 'try', 'tty', 'tumblr', 'tumblr-square', 'twitch', 'twitter', 'twitter-square', 'umbrella', 'underline', 'undo', 'universal-access', 'university', 'unlock', 'unlock-alt', 'upload', 'usb', 'usd', 'user', 'user-circle', 'user-circle-o', 'user-md', 'user-o', 'user-plus', 'user-secret', 'user-times', 'users', 'venus', 'venus-double', 'venus-mars', 'viacoin', 'viadeo', 'viadeo-square', 'video-camera', 'vimeo', 'vimeo-square', 'vine', 'vk', 'volume-control-phone', 'volume-down', 'volume-off', 'volume-up', 'weibo', 'weixin', 'whatsapp', 'wheelchair', 'wheelchair-alt', 'wifi', 'wikipedia-w', 'window-close', 'window-close-o', 'window-maximize', 'window-minimize', 'window-restore', 'windows', 'wordpress', 'wpbeginner', 'wpexplorer', 'wpforms', 'wrench', 'xing', 'xing-square', 'y-combinator', 'yahoo', 'yelp', 'yoast', 'youtube', 'youtube-play', 'youtube-square']
		];
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
				array('/[^a-zA-Z0-9 -.]/', '/[.]/', '/[ -]+/', '/^-|-$/'),
				array('', '-', '-', ''),
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
	private function ivn_convert_accent($text = '', $mode = 'remove')
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

			case 'style':
				return constants::BB_TYPE_STYLE;

			case 'acp_style':
				return constants::BB_TYPE_ACP_STYLE;

			case 'lang':
				return constants::BB_TYPE_LANG;

			case 'tool':
				return constants::BB_TYPE_TOOL;

			default:
				return 0;
		}
	}

	/**
	* Convert BB type from string to constant value
	*
	* @param $bb_type
	* @return string
	*/
	public function get_bb_type_varnames($bb_type)
	{
		switch ($bb_type)
		{
			case 'ext':
				return constants::BB_TYPE_VARNAME_EXT;

			case 'style':
				return constants::BB_TYPE_VARNAME_STYLE;

			case 'acp_style':
				return constants::BB_TYPE_VARNAME_ACP_STYLE;

			case 'lang':
				return constants::BB_TYPE_VARNAME_LANG;

			case 'tool':
				return constants::BB_TYPE_VARNAME_TOOL;

			default:
				return '';
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
			case constants::OS_WIN:
				return constants::OS_NAME_WIN;

			case constants::OS_MAC:
				return constants::OS_NAME_MAC;

			case constants::OS_LINUX:
				return constants::OS_NAME_LINUX;

			case constants::OS_BSD:
				return constants::OS_NAME_BSD;

			case constants::OS_ANDROID:
				return constants::OS_NAME_ANDROID;

			case constants::OS_IOS:
				return constants::OS_NAME_IOS;

			case constants::OS_WP:
				return constants::OS_NAME_WP;

			case constants::OS_ALL:
			default:
				return '';
		}
	}

	/**
	* Fetch content from an URL
	*
	* @param $url
	* @return string
	*/
	public function fetch_url($url)
	{
		$raw = '';

		// Test URL
		$test = get_headers($url);

		if (strpos($test[0], '200') !== false)
		{
			if (function_exists('curl_version'))
			{
				$curl = curl_init($url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				$raw = curl_exec($curl);
				curl_close($curl);
			}
			else
			{
				$url_parts = parse_url($url);

				try
				{
					$raw = $this->file_downloader->get($url_parts['host'], '', $url_parts['path'], ($url_parts['scheme'] == 'https') ? 443 : 80);
				}
				catch (\phpbb\exception\runtime_exception $e)
				{
					throw new \RuntimeException($this->file_downloader->get_error_string());
				}
			}
		}

		return $raw;
	}

	/**
	* List items with pagination
	*
	* @param int	$bb_type
	* @param int	$cat_id
	* @param array	$items
	* @param int	$item_count
	* @param int	$limit
	* @param int	$offset
	*
	* @return int
	*/
	public function list_bb_items($bb_type, $cat_id = 0, &$items, &$item_count, $limit = 0, $offset = 0)
	{
		$sql_and = $cat_id ? "AND cat_id = $cat_id" : '';

		$sql = 'SELECT COUNT(item_id) AS item_count
			FROM ' . $this->bb_items_table . "
			WHERE bb_type = $bb_type
				$sql_and";
		$result = $this->db->sql_query($sql);
		$item_count = (int) $this->db->sql_fetchfield('item_count');
		$this->db->sql_freeresult($result);

		if ($item_count == 0)
		{
			return 0;
		}

		if ($offset >= $item_count)
		{
			$offset = ($offset - $limit < 0) ? 0 : $offset - $limit;
		}

		$sql = 'SELECT *
			FROM ' . $this->bb_items_table . "
			WHERE bb_type = $bb_type
				$sql_and
			ORDER BY item_name";
		$result = $this->db->sql_query_limit($sql, $limit, $offset);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$items[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $offset;
	}

	/**
	* List articles with pagination
	*
	* @param int	$cat_id
	* @param array	$articles
	* @param int	$article_count
	* @param int	$limit
	* @param int	$offset
	*
	* @return int
	*/
	public function list_articles($cat_id = 0, &$articles, &$article_count, $limit = 0, $offset = 0)
	{
		$sql_where = $cat_id ? "WHERE cat_id = $cat_id" : '';

		$sql = 'SELECT COUNT(article_id) AS article_count
			FROM ' . $this->portal_articles_table . "
			$sql_where";
		$result = $this->db->sql_query($sql);
		$article_count = (int) $this->db->sql_fetchfield('article_count');
		$this->db->sql_freeresult($result);

		if ($article_count == 0)
		{
			return 0;
		}

		if ($offset >= $article_count)
		{
			$offset = ($offset - $limit < 0) ? 0 : $offset - $limit;
		}

		$sql = 'SELECT *
			FROM ' . $this->portal_articles_table . "
			$sql_where
			ORDER BY article_time DESC";
		$result = $this->db->sql_query_limit($sql, $limit, $offset);

		while ($row = $this->db->sql_fetchrow($result))
		{
			$articles[] = $row;
		}
		$this->db->sql_freeresult($result);

		return $offset;
	}
}
