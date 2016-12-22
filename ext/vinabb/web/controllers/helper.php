<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers;

use Symfony\Component\DependencyInjection\ContainerInterface;
use vinabb\web\includes\constants;

class helper implements helper_interface
{
	/** @var \phpbb\auth\auth $auth */
	protected $auth;

	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \phpbb\file_downloader $file_downloader */
	protected $file_downloader;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \phpbb\controller\helper $helper */
	protected $helper;

	/** @var \phpbb\group\helper $group_helper */
	protected $group_helper;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth									$auth				Authentication object
	* @param \vinabb\web\controllers\cache\service_interface	$cache				Cache service
	* @param \phpbb\config\config								$config				Config object
	* @param ContainerInterface									$container			Service container
	* @param \phpbb\db\driver\driver_interface					$db					Database object
	* @param \phpbb\file_downloader								$file_downloader	File downloader
	* @param \phpbb\language\language							$language			Language object
	* @param \phpbb\template\template							$template			Template object
	* @param \phpbb\user										$user				User object
	* @param \phpbb\controller\helper							$helper				Controller helper
	* @param \phpbb\group\helper								$group_helper		Group helper
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		ContainerInterface $container,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\file_downloader $file_downloader,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\phpbb\group\helper $group_helper
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->container = $container;
		$this->db = $db;
		$this->file_downloader = $file_downloader;
		$this->language = $language;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->group_helper = $group_helper;
	}

	/**
	* List of stable phpBB versions
	*
	* @return array
	*/
	public function get_phpbb_versions()
	{
		return [
			// Rhea
			'3.2'	=> [
				'3.2.0'		=> ['name' => '3.2.0', 'date' => '2017-01-08']
			],
			// Ascraeus
			'3.1'	=> [
				'3.1.10'	=> ['name' => '3.1.10', 'date' => '2016-10-12'],
				'3.1.9'		=> ['name' => '3.1.9', 'date' => '2016-04-16'],
				'3.1.8'		=> ['name' => '3.1.8', 'date' => '2016-02-19'],
				'3.1.7'		=> ['name' => '3.1.7', 'date' => '2015-12-19'],
				'3.1.6'		=> ['name' => '3.1.6', 'date' => '2015-09-05'],
				'3.1.5'		=> ['name' => '3.1.5', 'date' => '2015-06-14'],
				'3.1.4'		=> ['name' => '3.1.4', 'date' => '2015-05-03'],
				'3.1.3'		=> ['name' => '3.1.3', 'date' => '2015-02-02'],
				'3.1.2'		=> ['name' => '3.1.2', 'date' => '2014-11-25'],
				'3.1.1'		=> ['name' => '3.1.1', 'date' => '2014-11-02'],
				'3.1.0'		=> ['name' => '3.1.0', 'date' => '2014-10-28']
			],
			// Olympus
			'3.0'	=> [
				'3.0.14'	=> ['name' => '3.0.14', 'date' => '2015-05-03'],
				'3.0.13'	=> ['name' => '3.0.13', 'date' => '2015-01-27'],
				'3.0.12'	=> ['name' => '3.0.12', 'date' => '2013-09-28'],
				'3.0.11'	=> ['name' => '3.0.11', 'date' => '2012-08-20'],
				'3.0.10'	=> ['name' => '3.0.10', 'date' => '2012-01-03'],
				'3.0.9'		=> ['name' => '3.0.9', 'date' => '2011-07-11'],
				'3.0.8'		=> ['name' => '3.0.8', 'date' => '2010-11-20'],
				'3.0.7'		=> ['name' => '3.0.7', 'date' => '2010-03-01'],
				'3.0.6'		=> ['name' => '3.0.6', 'date' => '2009-11-17'],
				'3.0.5'		=> ['name' => '3.0.5', 'date' => '2009-05-31'],
				'3.0.4'		=> ['name' => '3.0.4', 'date' => '2008-12-13'],
				'3.0.3'		=> ['name' => '3.0.3', 'date' => '2008-11-13'],
				'3.0.2'		=> ['name' => '3.0.2', 'date' => '2008-07-11'],
				'3.0.1'		=> ['name' => '3.0.1', 'date' => '2008-04-08'],
				'3.0.0'		=> ['name' => '3.0.0', 'date' => '2007-12-12']
			]
		];
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
	* Generate the icon selection drop-down
	*
	* @param string $selected_icon Selected icon string value
	* @return string HTML code
	*/
	public function build_icon_list($selected_icon)
	{
		$icon_list = $this->get_icons();
		$icon_options = '<option value=""' . (($selected_icon == '') ? ' selected' : '') . '>' . $this->language->lang('SELECT_ICON') . '</option>';

		foreach ($icon_list as $type => $type_data)
		{
			if ($type != 'data')
			{
				$icon_options .= '<optgroup label="' . $icon_list['data'][$type]['name'] . '">"';

				foreach ($type_data as $icon)
				{
					$icon_value = $icon_list['data'][$type]['prefix'] . $icon;
					$icon_name = ucwords(str_replace($icon_list['data'][$type]['char'], ' ', $icon));
					$icon_options .= '<option value="' . $icon_value . '"' . (($selected_icon == $icon_value) ? ' selected' : '') . '>' . $icon_name . '</option>';
				}

				$icon_options .= '</optgroup>';
			}
		}

		unset($icon_list);

		return $icon_options;
	}

	/**
	* Generate the language selection drop-down
	*
	* @param string $selected_lang 2-letter language ISO code
	* @return string HTML code
	*/
	public function build_lang_list($selected_lang)
	{
		$lang_switch_options = '<option value=""' . (($selected_lang == '') ? ' selected' : '') . '>' . $this->language->lang('SELECT_LANGUAGE') . '</option>';

		foreach ($this->cache->get_lang_data() as $lang_iso => $data)
		{
			$lang_switch_options .= '<option value="' . $lang_iso . '"' . (($selected_lang == $lang_iso) ? ' selected' : '') . '>' . $data['english_name'] . ' (' . $data['local_name'] . ')</option>';
		}

		return $lang_switch_options;
	}

	/**
	* Create clean URLs for SEO
	*
	* @param string $text Input text
	* @return string
	*/
	public function clean_url($text)
	{
		return strtolower(preg_replace(['/[^a-zA-Z0-9-\s\.]/', '/[ -]+/', '/^-|-$/'], ['', '-', ''], html_entity_decode($this->remove_accents($text))));
	}

	/**
	* Remove all accents
	*
	* @param string $text Input text
	* @return string Result text
	*/
	public function remove_accents($text = '')
	{
		$find = ['á', 'Á', 'à', 'À', 'ả', 'Ả', 'ã', 'Ã', 'ạ', 'Ạ', 'ă', 'Ă', 'ắ', 'Ắ', 'ằ', 'Ằ', 'ẳ', 'Ẳ', 'ẵ', 'Ẵ', 'ặ', 'Ặ', 'â', 'Â', 'ấ', 'Ấ', 'ầ', 'Ầ', 'ẩ', 'Ẩ', 'ẫ', 'Ẫ', 'ậ', 'Ậ', 'đ', 'Đ', 'é', 'É', 'è', 'È', 'ẻ', 'Ẻ', 'ẽ', 'Ẽ', 'ẹ', 'Ẹ', 'ê', 'Ê', 'ế', 'Ế', 'ề', 'Ề', 'ể', 'Ể', 'ễ', 'Ễ', 'ệ', 'Ệ', 'í', 'Í', 'ì', 'Ì', 'ỉ', 'Ỉ', 'ĩ', 'Ĩ', 'ị', 'Ị', 'ó', 'Ó', 'ò', 'Ò', 'ỏ', 'Ỏ', 'õ', 'Õ', 'ọ', 'Ọ', 'ô', 'Ô', 'ố', 'Ố', 'ồ', 'Ồ', 'ổ', 'Ổ', 'ỗ', 'Ỗ', 'ộ', 'Ộ', 'ơ', 'Ơ', 'ớ', 'Ớ', 'ờ', 'Ờ', 'ở', 'Ở', 'ỡ', 'Ỡ', 'ợ', 'Ợ', 'ú', 'Ú', 'ù', 'Ù', 'ủ', 'Ủ', 'ũ', 'Ũ', 'ụ', 'Ụ', 'ư', 'Ư', 'ứ', 'Ứ', 'ừ', 'Ừ', 'ử', 'Ử', 'ữ', 'Ữ', 'ự', 'Ự', 'ý', 'Ý', 'ỳ', 'Ỳ', 'ỷ', 'Ỷ', 'ỹ', 'Ỹ', 'ỵ', 'Ỵ'];
		$replace = ['a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'd', 'D', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'y', 'Y', 'y', 'Y', 'y', 'Y', 'y', 'Y', 'y', 'Y'];

		return str_replace($find, $replace, $text);
	}

	/**
	* Convert BB type from string to constant value
	*
	* @param string $bb_type phpBB resource type (ext|style|acp_style|lang|tool)
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
	* Convert BB type from string to URL varnames
	*
	* @param string $bb_type phpBB resource type (ext|style|acp_style|lang|tool)
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
	* Convert BB types from URL varnames to standard varnames
	* Example: For ACP styles, URL varname is 'acp-styles' but standard varname is 'acp_style'
	*
	* @param string $varname phpBB resource type URL varname
	* @return string
	*/
	public function convert_bb_type_varnames($varname)
	{
		switch ($varname)
		{
			case constants::BB_TYPE_VARNAME_EXT:
				return 'ext';

			case constants::BB_TYPE_VARNAME_STYLE:
				return 'style';

			case constants::BB_TYPE_VARNAME_ACP_STYLE:
				return 'acp_style';

			case constants::BB_TYPE_VARNAME_LANG:
				return 'lang';

			case constants::BB_TYPE_VARNAME_TOOL:
				return 'tool';

			default:
				return '';
		}
	}

	/**
	* Get OS name from constants
	*
	* @param int $os_value OS constant value
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
	* Adding items to the breadcrumb
	*
	* @param string	$name	Name of item
	* @param string	$url	URL of item
	*/
	public function set_breadcrumb($name, $url = '')
	{
		$this->template->assign_block_vars('breadcrumb', [
			'NAME'	=> $name,
			'URL'	=> $url
		]);
	}

	/**
	* Fetch content from an URL
	*
	* @param string $url URL
	* @return string Raw value
	*/
	public function fetch_url($url)
	{
		$raw = '';

		// Test URL
		$test = ($url != '') ? get_headers($url) : '';

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
	* Group legend for online users
	*
	* @return string
	*/
	public function get_group_legend()
	{
		$order_legend = ($this->config['legend_sort_groupname']) ? 'group_name' : 'group_legend';

		if ($this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
		{
			$sql = 'SELECT group_id, group_name, group_colour, group_type, group_legend
				FROM ' . GROUPS_TABLE . '
				WHERE group_legend > 0
				ORDER BY ' . $order_legend;
		}
		else
		{
			$sql = 'SELECT g.group_id, g.group_name, g.group_colour, g.group_type, g.group_legend
				FROM ' . GROUPS_TABLE . ' g
				LEFT JOIN ' . USER_GROUP_TABLE . ' ug
					ON (
						g.group_id = ug.group_id
						AND ug.user_id = ' . $this->user->data['user_id'] . '
						AND ug.user_pending = 0
					)
				WHERE g.group_legend > 0
					AND (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . $this->user->data['user_id'] . ')
				ORDER BY g.' . $order_legend;
		}
		$result = $this->db->sql_query($sql);

		$legend = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$colour_text = ($row['group_colour']) ? ' style="color: #' . $row['group_colour'] . '"' : '';
			$group_name = $this->group_helper->get_name($row['group_name']);

			if ($row['group_name'] == 'BOTS' || ($this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile')))
			{
				$legend[] = '<span' . $colour_text . '>' . $group_name . '</span>';
			}
			else
			{
				$legend[] = '<a' . $colour_text . ' href="' . $this->helper->route('vinabb_web_user_group_route', ['group_id' => $row['group_id']]) . '">' . $group_name . '</a>';
			}
		}
		$this->db->sql_freeresult($result);

		return implode($this->language->lang('COMMA_SEPARATOR'), $legend);
	}

	/**
	* Enable SCEditor and load data for the smiley list
	*/
	public function load_sceditor()
	{
		$sceditor_smilies = $sceditor_hidden_smilies = $sceditor_smilies_desc = [];

		foreach ($this->cache->get_smilies() as $smiley_code => $smiley_data)
		{
			if ($smiley_data['display'])
			{
				$sceditor_smilies[$smiley_code] = $smiley_data['url'];
			}
			else
			{
				$sceditor_hidden_smilies[$smiley_code] = $smiley_data['url'];
			}

			$sceditor_smilies_desc[$smiley_code] = $this->language->lang(['EMOTICON_TEXT', strtoupper($smiley_data['emotion'])]);
		}

		// Output
		$this->template->assign_vars([
			'SCEDITOR_SMILIES'			=> json_encode($sceditor_smilies),
			'SCEDITOR_HIDDEN_SMILIES'	=> json_encode($sceditor_hidden_smilies),
			'SCEDITOR_SMILIES_DESC'		=> json_encode($sceditor_smilies_desc),

			'S_WYSIWYG_EDITOR'	=> true
		]);
	}

	/**
	* Remove trailing slash in destination path
	*
	* @param string $destination Destination path
	* @return string
	*/
	public function remove_trailing_slash($destination)
	{
		if (substr($destination, -1, 1) == '/' || substr($destination, -1, 1) == '\\')
		{
			$destination = substr($destination, 0, -1);
		}

		$destination = str_replace(['../', '..\\', './', '.\\'], '', $destination);

		if ($destination && ($destination[0] == '/' || $destination[0] == '\\'))
		{
			$destination = '';
		}

		return $destination;
	}

	/**
	* Call the core function get_username_string() with only $user_id
	*
	* @param int	$user_id	User ID
	* @param string	$mode		Output mode
	* @return string
	*/
	public function get_username_string($user_id, $mode = 'full')
	{
		/** @var \vinabb\web\entities\user_interface $entity */
		$entity = $this->container->get('vinabb.web.entities.user')->load($user_id);

		return ($mode == 'username') ? $entity->get_username() : get_username_string($mode, $user_id, $entity->get_username(), $entity->get_colour());
	}

	/**
	* Build gravatar URL for output on page
	*
	* @param array $row User data or group data that has been cleaned with
	*        \phpbb\avatar\manager::clean_row
	* @return string Gravatar URL
	*/
	public function get_gravatar_url($row)
	{
		$url =  '//secure.gravatar.com/avatar/' . md5(strtolower(trim($row['user_avatar'])));

		if ($row['user_avatar_width'] || $row['user_avatar_height'])
		{
			$url .= '?s=' . max($row['user_avatar_width'], $row['user_avatar_height']);
		}

		return $url;
	}
}
