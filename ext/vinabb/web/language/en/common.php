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
	'ACP_CAT_BB'				=> 'phpBB Resource',
	'ACP_CAT_PORTAL'			=> 'Portal',
	'ACP_CAT_VINABB'			=> 'VinaBB',
	'ACP_CAT_VINABB_SETTINGS'	=> 'Settings',
	'ACP_STYLE_DETAILS'			=> 'Style information',
	'ACP_STYLE_NAME'			=> 'Style name',
	'ACP_STYLE_VERSION'			=> 'Style version',
	'ALL_TIMES'					=> 'All times are %1$s',
	'ANALYTICS'					=> 'Analytics',
	'AUTHOR_NAME'				=> 'Developer name',

	'BB'				=> 'Resources',
	'BB_ACP_STYLES'		=> 'ACP styles',
	'BB_AUTHORS'		=> 'Developers',
	'BB_EXTS'			=> 'Extensions',
	'BB_LANGS'			=> 'Language packages',
	'BB_LANGS_ALT'		=> 'Languages',
	'BB_STYLES'			=> 'Styles',
	'BB_SUBSCRIBERS'	=> 'Subscribers',
	'BB_TOOLS'			=> 'Tools',
	'BOARD'				=> 'Board',

	'CATEGORY'				=> 'Category',
	'CLOSE'					=> 'Close',
	'CODE'					=> 'Code',
	'CODECLIMATE'			=> 'CodeClimate',
	'COMMENT'				=> 'Comment',
	'COMMENTS'				=> 'Comments',
	'COMMUNITY'				=> 'Community',
	'CONFIRM_LOGOUT'		=> 'Are you sure you want to logout?',
	'COORDINATES'			=> 'Coordinates',
	'COPY'					=> 'Copy',
	'COPY_ERROR_APPEND'		=> 'C to copy',
	'COPY_ERROR_PREPEND'	=> 'Press',
	'COPY_EXPLAIN'			=> 'Copy to clipboard',
	'COPY_SUCCESS'			=> 'Copied!',
	'COUNTER_ACP_STYLE'		=> [
		0	=> 'No styles',
		1	=> '%d style',
		2	=> '%d styles'
	],
	'COUNTER_ARTICLE'		=> [
		0	=> 'No articles',
		1	=> '%d article',
		2	=> '%d articles'
	],
	'COUNTER_COMMENT'		=> [
		0	=> 'No comments',
		1	=> '%d comment',
		2	=> '%d comments'
	],
	'COUNTER_EXT'			=> [
		0	=> 'No extensions',
		1	=> '%d extension',
		2	=> '%d extensions'
	],
	'COUNTER_LANG'			=> [
		0	=> 'No language packages',
		1	=> '%d language package',
		2	=> '%d language packages'
	],
	'COUNTER_STYLE'			=> [
		0	=> 'No styles',
		1	=> '%d style',
		2	=> '%d styles'
	],
	'COUNTER_TOOL'			=> [
		0	=> 'No tools',
		1	=> '%d tool',
		2	=> '%d tools'
	],
	'CURRENCY'				=> 'Currency',
	'CURRENT_IMAGE'			=> 'Current image',

	'DEBUG'				=> 'Debug',
	'DEBUG_INFO'		=> 'Debug information',
	'DESCRIPTION'		=> 'Description',
	'DONATE'			=> 'Donate',
	'DONATE_BANK'		=> 'Bank name',
	'DONATE_BANK_ACC'	=> 'Bank account',
	'DONATE_BANK_SWIFT'	=> 'SWIFT code',
	'DONATE_BUTTON'		=> 'Donate',
	'DONATE_CURRENCY'	=> 'Currency code',
	'DONATE_DONE'		=> 'Paid',
	'DONATE_OWNER'		=> 'Account owner',
	'DOWNLOADS'			=> 'Downloads',

	'EMOTICON_TEXT'				=> [
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
		'IN_LOVE'			=> 'I love phpBB',
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
		'WEEPING'			=> 'Weeping',
		'WHO_AM_I'			=> 'Who am I?',
		'WINKING'			=> 'Winking',
		'WORLD_WEARY'		=> 'World weary',
		'WORRIED'			=> 'Worried',
		'WTF'				=> 'WTF'
	],
	'ENGLISH'					=> 'English',
	'ERROR_USER_ID_EMPTY'		=> 'No users specified.',
	'ERROR_USER_ID_NOT_EXISTS'	=> 'The user does not exist.',
	'EXT_DETAILS'				=> 'Extension information',
	'EXT_NAME'					=> 'Extension name',
	'EXT_VERSION'				=> 'Extension version',

	'FIRST_NAME'			=> 'First name',
	'FOOTER_FLAG_TEXT'		=> 'VinaBB - The Vietnamese phpBB Community since 2006',
	'FOOTER_FLAG_TITLE'		=> 'Code in Viet Nam',
	'FOOTER_HISTORY_TEXT'	=> '
		<i class="icon-heart padding-5"></i> 17 Jul 2004: Found phpBB 2.0.10 on the Internet.<br>
		<i class="icon-emotsmile padding-5"></i> 22 Oct 2006: Born with a smile.<br>
		<i class="icon-rocket padding-5"></i> 11 Jun 2007: Set data on Olympus, Mars.<br>
		<i class="icon-feed padding-5"></i> 11 Jun 2009: Lost connection to Earth. [ <a href="http://2007.vinabb.vn/">Time Machine</a> ]<br>
		<i class="icon-graph padding-5"></i> 28 Jul 2016: Set new base on the Rhea moon, Saturn.<br>
		<i class="icon-star padding-5"></i> 12 Dec 2016: The new adventure begins…
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
	'G_FOUNDERS'			=> 'Founders',
	'G_MANAGEMENT_TEAM'		=> 'Managers',
	'G_MODERATOR_TEAM'		=> 'Moderators',
	'G_SUPPORT_TEAM'		=> 'Supporters',
	'GITHUB'				=> 'GitHub',

	'INSIGHT'					=> 'SensioLabs Insight',
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
	'LAST_ACTIVE'				=> 'Last active',
	'LAST_NAME'					=> 'Last name',
	'LATEST_ACP_STYLES'			=> 'New ACP styles',
	'LATEST_EXTS'				=> 'New extensions',
	'LATEST_IVN'				=> 'Vietnamese',
	'LATEST_IVN_TITLE'			=> 'Download phpBB iVN',
	'LATEST_PHP'				=> 'PHP version',
	'LATEST_PHP_TITLE'			=> 'PHP Information',
	'LATEST_PHPBB'				=> 'phpBB version',
	'LATEST_PHPBB_TITLE'		=> 'Download phpBB',
	'LATEST_POSTS'				=> 'New posts',
	'LATEST_STYLES'				=> 'New styles',
	'LATEST_TOOLS'				=> 'New tools',
	'LATEST_TOPICS'				=> 'New topics',
	'LATEST_USERS'				=> 'New members',
	'LATEST_VERSION'			=> 'Latest version',
	'LATEST_VERSIONS'			=> 'Latest versions',
	'LATEST_VERSIONS_INFO'		=> 'Latest version information',
	'LATEST_VINABB'				=> 'VinaBB version',
	'LATEST_VINABB_TITLE'		=> 'VinaBB.vn source code',
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

	'NEW'					=> 'New',
	'NEWS'					=> 'News',
	'NO_BB_ACP_STYLE'		=> 'The ACP style does not exist.',
	'NO_BB_ACP_STYLE_ID'	=> 'No ACP styles specified.',
	'NO_BB_ACP_STYLES'		=> 'No ACP styles.',
	'NO_BB_CAT'				=> 'The category does not exist.',
	'NO_BB_CAT_ID'			=> 'No categories specified.',
	'NO_BB_EXT'				=> 'The extension does not exist.',
	'NO_BB_EXT_ID'			=> 'No extensions specified.',
	'NO_BB_EXTS'			=> 'No extensions.',
	'NO_BB_LANG'			=> 'The language package does not exist.',
	'NO_BB_LANG_ID'			=> 'No language packages specified.',
	'NO_BB_LANGS'			=> 'No language packages.',
	'NO_BB_STYLE'			=> 'The style does not exist.',
	'NO_BB_STYLE_ID'		=> 'No styles specified.',
	'NO_BB_STYLES'			=> 'No styles.',
	'NO_BB_TOOL'			=> 'The tool does not exist.',
	'NO_BB_TOOL_ID'			=> 'No tools specified.',
	'NO_BB_TOOLS'			=> 'No tools.',
	'NO_PORTAL_ARTICLE'		=> 'The article does not exist.',
	'NO_PORTAL_ARTICLE_ID'	=> 'No articles specified.',
	'NO_PORTAL_ARTICLES'	=> 'No articles.',
	'NO_POST'				=> 'The post does not exist.',
	'NONE'					=> 'None',

	'ORIGINAL_URL'	=> 'Original URL',
	'OS'			=> 'OS',
	'OS_ALL'		=> 'Cross-platform',

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
	'PORTAL_ARTICLE'			=> 'Article',
	'POWERED_BY'				=> 'Powered by <a href="https://www.phpbb.com/" data-tooltip="true" title="Forum software &copy; phpBB Limited">phpBB</a> with <i class="fa fa-heart animate-pulse text-danger"></i>',
	'PRICE'						=> 'Price',
	'PRINT'						=> 'Print',

	'RANK_TITLES'	=> [
		'ADMINISTRATOR'		=> 'Administrator',
		'DEVELOPER'			=> 'Development Team',
		'FOUNDER'			=> 'Founder',
		'GLOBAL_MODERATOR'	=> 'Global Moderator',
		'MANAGER'			=> 'Management Team',
		'MEMBER'			=> 'Member',
		'MODERATOR'			=> 'Moderator',
		'SUPPORTER'			=> 'Support Team'
	],
	'REAL_NAME'		=> 'Real name',
	'RESOURCES'		=> 'Resources',

	'SAVE'					=> 'Save',
	'SCRUTINIZER'			=> 'Scrutinizer',
	'SELECT_CATEGORY'		=> 'Select a category',
	'SELECT_ICON'			=> 'Select a icon',
	'SELECT_LANGUAGE'		=> 'Select a language',
	'SELECT_OS'				=> 'Select an OS',
	'SELECT_PHPBB_VERSION'	=> 'Select a phpBB version',
	'SELECT_VERSION'		=> 'Select a version',
	'SHARE'					=> 'Share',
	'SHARE_ON'				=> 'Share on %s',
	'STYLE_DETAILS'			=> 'Style information',
	'STYLE_NAME'			=> 'Style name',
	'STYLE_VERSION'			=> 'Style version',

	'TAGS'				=> 'Tags',
	'TOGGLE'			=> 'Toggle',
	'TOOL_DETAILS'		=> 'Tool information',
	'TOOL_NAME'			=> 'Tool name',
	'TOOL_VERSION'		=> 'Tool version',
	'TOTAL_ACP_STYLES'	=> 'Total styles',
	'TOTAL_ARTICLES'	=> 'Total articles',
	'TOTAL_EXTS'		=> 'Total extensions',
	'TOTAL_LANGS'		=> 'Total language packages',
	'TOTAL_STYLES'		=> 'Total styles',
	'TOTAL_TOOLS'		=> 'Total tools',
	'TRAVIS'			=> 'Travis',

	'UNKNOWN'			=> 'Unknown',
	'UPLOAD_NEW_FILE'	=> 'Upload new file',
	'UPLOAD_NEW_IMAGE'	=> 'Upload new image',
	'USER_ID'			=> 'User ID',

	'VERSION'			=> 'Version',
	'VIETNAMESE'		=> 'Vietnamese',
	'VIEW_DETAILS'		=> 'View details',
	'VINABB'			=> 'VinaBB',
	'VINABB_VERSION'	=> 'VinaBB version',
	'VINABB_VERSION_X'	=> 'VinaBB %s',
	'VINABB_VN'			=> 'VinaBB.vn',

	'WEBSITE_FUND'	=> 'Website fund'
]);
