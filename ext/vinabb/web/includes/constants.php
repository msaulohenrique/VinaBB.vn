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
	const BB_TYPE_VARNAME_ACP_STYLE = 'acp';
	const BB_TYPE_VARNAME_LANG = 'ivn';
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
	const PORTAL_CATS_PER_PAGE = 20;
	const PORTAL_ARTICLES_PER_PAGE = 10;

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
