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
	// Maintenance mode
	const MAINTENANCE_MODE_NONE = 0;
	const MAINTENANCE_MODE_FOUNDER = 1;
	const MAINTENANCE_MODE_ADMIN = 2;
	const MAINTENANCE_MODE_MOD = 3;
	const MAINTENANCE_MODE_USER = 4;

	// Resource types
	const BB_TYPE_EXT = 1;
	const BB_TYPE_STYLE = 2;
	const BB_TYPE_LANG = 3;
	const BB_TYPE_TOOL = 4;
}
