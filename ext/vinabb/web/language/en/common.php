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
	'ACP_CAT_BB'				=> 'phpBB Resource',
	'ACP_CAT_PORTAL'			=> 'Portal',
	'ACP_CAT_VINABB'			=> 'VinaBB',
	'ACP_CAT_VINABB_SETTINGS'	=> 'Settings',
	'ACP_STYLE_NAME'			=> 'Style name',
	'ACP_STYLE_VERSION'			=> 'Style version',

	'BB_ACP_STYLES'	=> 'ACP styles',
	'BB_EXTS'		=> 'Extensions',
	'BB_LANGS'		=> 'Language packages',
	'BB_STYLES'		=> 'Styles',
	'BB_TOOLS'		=> 'Tools',
	'BBCODE_C_HELP'	=> 'Insert code: [code]block code[/code] or `inline code`',
	'BOARD'			=> 'Board',

	'CATEGORY'		=> 'Category',
	'CLOSE'			=> 'Close',
	'COMMUNITY'		=> 'Community',
	'COORDINATES'	=> 'Coordinates',
	'CURRENCY'		=> 'Currency',

	'DEBUG'				=> 'Debug',
	'DEBUG_INFO'		=> 'Debug information',
	'DONATE'			=> 'Donate',
	'DONATE_BANK'		=> 'Bank name',
	'DONATE_BANK_ACC'	=> 'Bank account',
	'DONATE_BANK_SWIFT'	=> 'SWIFT code',
	'DONATE_BUTTON'		=> 'Donate',
	'DONATE_CURRENCY'	=> 'Currency code',
	'DONATE_DONE'		=> 'Paid',
	'DONATE_OWNER'		=> 'Account owner',

	'EMOTICON_TEXT'	=> array(
		'ALIEN'				=> 'Alien',
		'ANGEL'				=> 'Angel',
		'ANGRY'				=> 'Angry',
		'ASTONISHED'		=> 'Astonished',
		'AWKWARD'			=> 'Awkward',
		'BEARD'				=> 'Beard',
		'BIG_GRIN'			=> 'Big grin',
		'BIG_SMILE'			=> 'Big smile',
		'BIG_SURPRISE'		=> 'Big surprise',
		'BLUSHING'			=> 'Blushing',
		'BLUSHING_WINK'		=> 'Blushing wink',
		'CENSORED'			=> 'Censored',
		'COLD_SWEAT'		=> 'Cold sweat',
		'CONFOUNDED'		=> 'Confounded',
		'CONFUSED'			=> 'Confused',
		'COOL'				=> 'Cool',
		'CRYING'			=> 'Crying',
		'DEVIL'				=> 'Devil',
		'DISAPPOINTED'		=> 'Disappointed',
		'DIZZY'				=> 'Dizzy',
		'EYEWINK'			=> 'Eyewink',
		'FEARFUL'			=> 'Fearful',
		'FROWN'				=> 'Frown',
		'GLOOMY'			=> 'Gloomy',
		'HEE_HEE'			=> 'Hee hee',
		'HUFFING'			=> 'Huffing',
		'IN_LOVE'			=> 'In love',
		'KISS'				=> 'Kiss',
		'KISS_BLUSHING'		=> 'Kiss blushing',
		'KISS_WINK'			=> 'Kiss wink',
		'KISSING'			=> 'Kissing',
		'LAUGHING'			=> 'Laughing',
		'LAUGHING_TEARS'	=> 'Laughing tears',
		'LOL'				=> 'LOL',
		'MAD_DEVIL'			=> 'Mad devil',
		'MEOW'				=> 'Meow',
		'MONEY'				=> 'Money',
		'NO_IDEAS'			=> 'No ideas',
		'PACMAN'			=> 'Pacman',
		'RELIEVED'			=> 'Relieved',
		'SAD'				=> 'Sad',
		'SAD_CRYING'		=> 'Sad crying',
		'SCARED'			=> 'Scared',
		'SCREAMING'			=> 'Screaming',
		'SHOCKED'			=> 'Shocked',
		'SILLY'				=> 'Silly',
		'SLEEPING'			=> 'Sleeping',
		'SLEEPY'			=> 'Sleepy',
		'SMILE'				=> 'Smile',
		'SMILING'			=> 'Smiling',
		'STRAIGHT'			=> 'Straight',
		'SURPRISED'			=> 'Surprised',
		'TEARS_OF_JOY'		=> 'Tears of joy',
		'TONGUE'			=> 'Tongue',
		'TONGUE_LICK'		=> 'Tongue lick',
		'TONGUE_OUT'		=> 'Tongue out',
		'TONGUE_WINK'		=> 'Tongue wink',
		'UPSET'				=> 'Upset',
		'WEARY'				=> 'Weary',
		'WHO_AM_I'			=> 'Who am I?',
		'WINKING'			=> 'Winking',
		'WORLD_WEARY'		=> 'World weary',
		'WORRIED'			=> 'Worried',
		'WTF'				=> 'WTF',
	),
	'ENGLISH'		=> 'English',
	'EXT_DETAILS'	=> 'Extension information',
	'EXT_NAME'		=> 'Extension name',
	'EXT_VERSION'	=> 'Extension version',

	'FOOTER_FLAG_TEXT'		=> 'VinaBB - The Vietnamese phpBB Community since 2006',
	'FOOTER_FLAG_TITLE'		=> 'Code in Viet Nam',
	'FOOTER_HISTORY_TEXT'	=> '
		<i class="icon-heart padding-5"></i> 17 Jul 2004: Found phpBB 2.0.10 on the Internet.<br>
		<i class="icon-emotsmile padding-5"></i> 22 Oct 2006: Born with a smile.<br>
		<i class="icon-rocket padding-5"></i> 11 Jun 2007: Set data on Olympus, Mars.<br>
		<i class="icon-feed padding-5"></i> 11 Jun 2009: Lost connection to Earth. [ <a href="http://2007.vinabb.vn/">Time Machine</a> ]<br>
		<i class="icon-graph padding-5"></i> 28 Jul 2016: Set new base on the Rhea moon, Saturn.<br>
		<i class="icon-star padding-5"></i> 22 Oct 2016: The new adventure begins…
	',
	'FOOTER_HISTORY_TITLE'	=> 'History',
	'FOOTER_MANAGER_ROLE'	=> 'General Manager',
	'FOOTER_MANAGER_TEXT'	=> 'We <strong class="text-danger">are responsible</strong> for all content on <strong class="text-info">VinaBB.vn</strong> under the law of the Socialist Republic of Viet Nam.',
	'FOOTER_RULE_EXPLAIN'	=> 'We do not like to write rules too long. You should understand and do the right thing.',
	'FOOTER_RULE_TEXT'		=> '
		<li>No political, religious and sexual content.</li>
		<li>Use only Vietnamese or English in appropriate forums.</li>
		<li>Warez are not welcome.</li>
		<li>You do not spam and then we do not advertise.</li>
		<li>Never confuse the size of your wallet with the size of your phpBB forum.</li>
	',
	'FOOTER_RULE_TITLE'		=> 'Rules',

	'G_DEVELOPMENT_TEAM'	=> 'Developers',
	'G_MANAGEMENT_TEAM'		=> 'Managers',
	'G_MODERATOR_TEAM'		=> 'Moderators',
	'G_SUPPORT_TEAM'		=> 'Supporters',
	'GENDER'				=> 'Gender',
	'GENDER_FEMALE'			=> 'Female',
	'GENDER_MALE'			=> 'Male',
	'GITHUB'				=> 'GitHub',
	'GITHUB_PROFILE'		=> 'GitHub',

	'ITEM_EXT_ACP_STYLE'		=> 'ACP style changes',
	'ITEM_EXT_DB_DATA'			=> 'Database data changes',
	'ITEM_EXT_DB_SCHEMA'		=> 'Database schema changes',
	'ITEM_EXT_LANG'				=> 'Language changes',
	'ITEM_EXT_STYLE'			=> 'Style changes',
	'ITEM_STYLE_BOOTSTRAP'		=> 'Bootstrap',
	'ITEM_STYLE_PRESETS'		=> 'Presets',
	'ITEM_STYLE_PRESETS_AIO'	=> 'All-in-one presets',
	'ITEM_STYLE_RESPONSIVE'		=> 'Responsive',
	'ITEM_STYLE_SOURCE'			=> 'Source files',

	'LABEL_PERCENT'				=> '%s%%',
	'LABEL_YEAR'				=> 'Year %d',
	'LANG_DETAILS'				=> 'Language package information',
	'LANG_NAME'					=> 'Language package name',
	'LANG_SWITCH'				=> '%1$s » %2$s',
	'LANG_VERSION'				=> 'Language package version',
	'LANGUAGE'					=> 'Language',
	'LATEST_ACP_STYLES'			=> 'New ACP styles',
	'LATEST_EXTS'				=> 'New extensions',
	'LATEST_IVN'				=> 'Vietnamese',
	'LATEST_IVN_TITLE'			=> 'Download phpBB iVN',
	'LATEST_PHP'				=> 'PHP version',
	'LATEST_PHP_TITLE'			=> 'PHP Information',
	'LATEST_PHPBB'				=> 'Latest version',
	'LATEST_PHPBB_TITLE'		=> 'Download phpBB',
	'LATEST_POSTS'				=> 'New posts',
	'LATEST_STYLES'				=> 'New styles',
	'LATEST_TOOLS'				=> 'New tools',
	'LATEST_TOPICS'				=> 'New topics',
	'LATEST_USERS'				=> 'New members',
	'LATEST_VERSION'			=> 'Latest version',
	'LATEST_VERSIONS'			=> 'Latest versions',
	'LATEST_VERSIONS_INFO'		=> 'Latest version information',
	'LATITUDE'					=> 'Latitude',
	'LONGITUDE'					=> 'Longitude',

	'MAINTENANCE_TEXT'				=> 'Zzz… The administrator is now sleeping and dreaming about new features in phpBB 4.x. You can not wake up this lazy boy but his mother can. We are looking for her. Love you!',
	'MAINTENANCE_TEXT_TIME_END'		=> 'Estimated end time: <strong>%s</strong>.',
	'MAINTENANCE_TEXT_TIME_LONG'	=> 'Zzz… The administrator is now sleeping and dreaming about new features in phpBB 4.x. You can not wake up this lazy boy until <strong>%s</strong>. Thanks for participating. Love you!',
	'MAINTENANCE_TEXT_TIME_SHORT'	=> 'Zzz… The administrator is now sleeping and dreaming about new features in phpBB 4.x. Please come back at <strong>%s</strong> and this lazy boy can be woken. Love you!',
	'MAINTENANCE_TEXT_TIMEZONE'		=> 'Timezone: %1$s (%2$s).',
	'MAINTENANCE_TITLE'				=> 'Sorry, VinaBB is down for maintenance',
	'MAP'							=> 'Map',
	'MEMBERS'						=> 'Members',

	'NEWS'		=> 'News',
	'NO_POST'	=> 'The post does not exist.',

	'OS'		=> 'OS',
	'OS_LIST'	=> array(
		'ALL'		=> 'Cross-platform',
		'ANDROID'	=> 'Android',
		'BSD'		=> 'BSD',
		'IOS'		=> 'iOS',
		'LINUX'		=> 'Linux',
		'MAC'		=> 'Mac OS X',
		'WIN'		=> 'Windows',
		'WP'		=> 'Windows Phone',
	),

	'PAYPAL'					=> 'PayPal',
	'PHONE'						=> 'Phone',
	'PHP_VERSION_X'				=> 'PHP %s',
	'PHPBB_COM'					=> 'phpBB.com',
	'PHPBB_IVN_EXPLAIN'			=> 'Vietnamese language package for phpBB.',
	'PHPBB_IVN_VERSION_X'		=> 'phpBB iVN %s',
	'PHPBB_IVNPLUS_EXPLAIN'		=> 'Vietnamese language package for phpBB extensions.',
	'PHPBB_IVNPLUS_VERSION_X'	=> 'phpBB iVN+ %s',
	'PHPBB_VERSION'				=> 'phpBB version',
	'PHPBB_VERSION_X'			=> 'phpBB %s',
	'POWERED_BY'				=> 'Powered by <a href="https://www.phpbb.com/" data-toggle="tooltip" title="Forum software &copy; phpBB Limited">phpBB</a> with <i class="fa fa-heart animate-pulse text-danger"></i>',

	'RANK_TITLES'	=> array(
		'ADMINISTRATOR'		=> 'Administrator',
		'DEVELOPER'			=> 'Development Team',
		'FOUNDER'			=> 'Founder',
		'GLOBAL_MODERATOR'	=> 'Global Moderator',
		'MANAGER'			=> 'Management Team',
		'MEMBER'			=> 'Member',
		'MODERATOR'			=> 'Moderator',
		'SUPPORTER'			=> 'Support Team',
	),
	'RESOURCES'		=> 'Resources',

	'SELECT_CATEGORY'		=> 'Select a category',
	'SELECT_LANGUAGE'		=> 'Select a language',
	'SELECT_OS'				=> 'Select an OS',
	'SELECT_PHPBB_VERSION'	=> 'Select a phpBB version',
	'SELECT_VERSION'		=> 'Select a version',
	'STYLE_DETAILS'			=> 'Style information',
	'STYLE_NAME'			=> 'Style name',
	'STYLE_VERSION'			=> 'Style version',

	'TOOL_DETAILS'		=> 'Tool information',
	'TOOL_NAME'			=> 'Tool name',
	'TOOL_VERSION'		=> 'Tool version',
	'TOTAL_ACP_STYLES'	=> 'Total styles',
	'TOTAL_ARTICLES'	=> 'Total articles',
	'TOTAL_EXTS'		=> 'Total extensions',
	'TOTAL_LANGS'		=> 'Total language packages',
	'TOTAL_STYLES'		=> 'Total styles',
	'TOTAL_TOOLS'		=> 'Total tools',

	'UNKNOWN'	=> 'Unknown',
	'USER_ID'	=> 'User ID',

	'VERSION'		=> 'Version',
	'VIETNAMESE'	=> 'Vietnamese',
	'VIEW_DETAILS'	=> 'View details',
	'VINABB'		=> 'VinaBB',
	'VINABB_VN'		=> 'VinaBB.vn',

	'WEBSITE_FUND'	=> 'Website fund',
));
