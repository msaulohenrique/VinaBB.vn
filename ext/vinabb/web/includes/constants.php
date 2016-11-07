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
	const VINABB_GITHUB_DOWNLOAD_URL = 'https://github.com/VinaBB/VinaBB.vn/archive/master.zip';
	const VINABB_GITHUB_FORK_URL = 'https://github.com/VinaBB/VinaBB.vn/fork';
	const VINABB_TRAVIS_URL = 'http://tv.vinabb.vn/';
	const VINABB_TRAVIS_IMG_URL = 'https://travis-ci.org/VinaBB/VinaBB.vn.svg?branch=master';
	const VINABB_INSIGHT_URL = 'http://is.vinabb.vn/';
	const VINABB_INSIGHT_IMG_URL = 'https://insight.sensiolabs.com/projects/791226a3-5228-429d-9f3a-20f9a9404b7b/mini.png';
	const VINABB_SCRUTINIZER_URL = 'http://sc.vinabb.vn/';
	const VINABB_SCRUTINIZER_IMG_URL = 'https://scrutinizer-ci.com/g/VinaBB/VinaBB.vn/badges/quality-score.png?b=master';

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
	const PORTAL_ARTICLES_PER_PAGE = 20;

	// Maximum length
	const MAX_PORTAL_CAT_NAME = 32;
	const MAX_BB_CAT_NAME = 48;

	// Pagination
	const BB_CATS_PER_PAGE = 20;
	const BB_ITEMS_PER_PAGE = 10;

	// Number of hours to check new versions again
	const CHECK_VERSION_HOURS = 8;

	// Table names
	const BB_CATEGORIES_TABLE = 'bb_categories';
	const BB_ITEMS_TABLE = 'bb_items';
	const BB_AUTHORS_TABLE = 'bb_authors';
	const PORTAL_CATEGORIES_TABLE = 'portal_categories';
	const PORTAL_ARTICLES_TABLE = 'portal_articles';
	const PORTAL_COMMENTS_TABLE = 'portal_comments';
}
