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
	'BBCODE_C_HELP'	=> 'Insert code: [code]block code[/code] or `inline code`',
	'BOARD'			=> 'Board',

	'COMMUNITY'	=> 'Community',

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

	'FORUM_WAR'	=> 'Forum War',

	'G_MANAGEMENT_TEAM'	=> 'Managers',
	'G_MODERATOR_TEAM'	=> 'Moderators',
	'G_SUPPORT_TEAM'	=> 'Supporters',
	'GENDER'			=> 'Gender',
	'GENDER_FEMALE'		=> 'Female',
	'GENDER_MALE'		=> 'Male',
	'GITHUB'			=> 'GitHub',
	'GITHUB_PROFILE'	=> 'GitHub',

	'LANG_SWITCH'	=> '%1$s » %2$s',

	'MAINTENANCE_TEXT'				=> 'Zzz… The administrator is now sleeping and dreaming about new features in phpBB 4.x. You can not wake up this lazy boy but his mother can. We are looking for her. Love you!',
	'MAINTENANCE_TEXT_TIME_END'		=> 'Estimated end time: <strong>%s</strong>.',
	'MAINTENANCE_TEXT_TIME_LONG'	=> 'Zzz… The administrator is now sleeping and dreaming about new features in phpBB 4.x. You can not wake up this lazy boy until <strong>%s</strong>. Thanks for participating. Love you!',
	'MAINTENANCE_TEXT_TIME_SHORT'	=> 'Zzz… The administrator is now sleeping and dreaming about new features in phpBB 4.x. Please come back at <strong>%s</strong> and this lazy boy can be woken. Love you!',
	'MAINTENANCE_TEXT_TIMEZONE'		=> 'Timezone: %1$s (%2$s).',
	'MAINTENANCE_TITLE'				=> 'Sorry, VinaBB is down for maintenance',
	'MEMBERS'						=> 'Members',

	'NEWS'		=> 'News',
	'NO_POST'	=> 'The post does not exist.',

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

	'USER_ID'	=> 'User ID',

	'VINABB'	=> 'VinaBB',
));
