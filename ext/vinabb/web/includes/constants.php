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

	// Rewrite URLs
	const REWRITE_URL_PAGE = 'page-';
	const REWRITE_URL_SEO = '.';
	const REWRITE_URL_FORUM_CAT = '-';
	const REWRITE_URL_FORUM_ZERO = 'x';

	// Pagination
	const BB_CATS_PER_PAGE = 20;
	const BB_ITEMS_PER_PAGE = 10;

	// Table names
	const BB_CATEGORIES_TABLE = 'bb_categories';
	const BB_ITEMS_TABLE = 'bb_items';
}
