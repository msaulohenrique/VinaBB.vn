<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\includes;

class constants
{
	// Project URLs
	const VINABB_GITHUB_PATH = 'VinaBB/VinaBB.vn';
	const VINABB_GITHUB_URL = 'https://github.com/VinaBB/VinaBB.vn';
	const VINABB_GITHUB_DOWNLOAD_URL = '';
	const VINABB_GITHUB_FORK_URL = 'https://github.com/VinaBB/VinaBB.vn/fork';
	const VINABB_TRAVIS_URL = 'http://tv.vinabb.vn/';
	const VINABB_TRAVIS_IMG_URL = 'https://travis-ci.org/VinaBB/VinaBB.vn.svg?branch=master';
	const VINABB_INSIGHT_URL = 'http://is.vinabb.vn/';
	const VINABB_INSIGHT_IMG_URL = 'https://insight.sensiolabs.com/projects/791226a3-5228-429d-9f3a-20f9a9404b7b/mini.png';
	const VINABB_SCRUTINIZER_URL = 'http://sc.vinabb.vn/';
	const VINABB_SCRUTINIZER_IMG_URL = 'https://scrutinizer-ci.com/g/VinaBB/VinaBB.vn/badges/quality-score.png?b=master';
	const VINABB_CODECLIMATE_URL = 'http://cc.vinabb.vn/';
	const VINABB_CODECLIMATE_IMG_URL = 'https://codeclimate.com/github/VinaBB/VinaBB.vn/badges/gpa.svg';

	// Languages
	const LANG_VIETNAMESE = 'vi';
	const LANG_ENGLISH = 'en';

	// Maintenance mode
	const MAINTENANCE_MODE_NONE = 0;
	const MAINTENANCE_MODE_FOUNDER = 1;
	const MAINTENANCE_MODE_ADMIN = 2;
	const MAINTENANCE_MODE_MOD = 3;
	const MAINTENANCE_MODE_USER = 4;

	// Resource types
	const BB_TYPE_EXT = 1;
	const BB_TYPE_STYLE = 2;
	const BB_TYPE_ACP_STYLE = 3;
	const BB_TYPE_LANG = 4;
	const BB_TYPE_TOOL = 5;

	// Resource type varnames
	const BB_TYPE_VARNAME_EXT = 'extensions';
	const BB_TYPE_VARNAME_STYLE = 'styles';
	const BB_TYPE_VARNAME_ACP_STYLE = 'acp-styles';
	const BB_TYPE_VARNAME_LANG = 'languages';
	const BB_TYPE_VARNAME_TOOL = 'tools';

	// Menu types
	const MENU_TYPE_URL = 1;
	const MENU_TYPE_ROUTE = 2;
	const MENU_TYPE_PAGE = 3;
	const MENU_TYPE_FORUM = 4;
	const MENU_TYPE_USER = 5;
	const MENU_TYPE_GROUP = 6;
	const MENU_TYPE_BOARD = 7;
	const MENU_TYPE_PORTAL = 8;
	const MENU_TYPE_BB = 9;

	// OS
	const OS_ALL = 0;
	const OS_WIN = 1;
	const OS_MAC = 2;
	const OS_LINUX = 3;
	const OS_BSD = 4;
	const OS_ANDROID = 5;
	const OS_IOS = 6;
	const OS_WP = 7;

	// OS names
	const OS_NAME_WIN = 'Windows';
	const OS_NAME_MAC = 'macOS';
	const OS_NAME_LINUX = 'Linux';
	const OS_NAME_BSD = 'BSD';
	const OS_NAME_ANDROID = 'Android';
	const OS_NAME_IOS = 'iOS';
	const OS_NAME_WP = 'Windows Phone';

	// Rewrite URLs
	const REWRITE_URL_PAGE = 'page-';
	const REWRITE_URL_SEO = '.';
	const REWRITE_URL_FORUM_CAT = '-';
	const REWRITE_URL_FORUM_ZERO = 'x';

	// Article comment modes
	const ARTICLE_COMMENT_MODE_HIDE = 0;
	const ARTICLE_COMMENT_MODE_SHOW = 1;
	const ARTICLE_COMMENT_MODE_PENDING = 2;

	// Number of display items
	const NUM_NEW_ITEMS_ON_INDEX = 10;
	const NUM_ARTICLES_ON_INDEX = 5;

	// Pagination
	const USERS_PER_PAGE = 20;
	const PORTAL_ARTICLES_PER_PAGE = 20;
	const BB_ITEMS_PER_PAGE = 10;

	// Maximum length
	const MAX_CONFIG_NAME = 255;
	const MAX_PORTAL_CAT_NAME = 32;
	const MAX_PORTAL_CAT_VARNAME = 16;
	const MAX_PORTAL_ARTICLE_NAME = 64;
	const MAX_PORTAL_ARTICLE_DESC = 200;
	const MAX_BB_CAT_NAME = 48;
	const MAX_BB_CAT_VARNAME = 24;
	const MAX_BB_CAT_DESC = 255;
	const MAX_BB_ITEM_NAME = 64;
	const MAX_BB_ITEM_VARNAME = 64;
	const MAX_BB_AUTHOR_NAME = 32;
	const MAX_PAGE_NAME = 48;
	const MAX_PAGE_VARNAME = 24;
	const MAX_MENU_NAME = 24;
	const MAX_HEADLINE_NAME = 64;
	const MAX_HEADLINE_DESC = 128;

	// Flag as new (in time: days, hours...)
	const FLAG_DAY_NEW_ARTICLE = 3;
	const FLAG_DAY_NEW_BB_ITEM = 1;

	// Number of hours to check new versions again
	const CHECK_VERSION_HOURS = 8;
}
