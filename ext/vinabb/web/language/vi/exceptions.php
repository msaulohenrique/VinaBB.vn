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

/**
* These are errors which can be triggered by sending invalid data to the extension API.
*
* These errors will never show to a user unless they are either
* modifying the extension code OR unless they are writing an extension
* which makes calls to this extension.
*
* Translators: Do not need to translate these language strings ;)
*/
$lang = array_merge($lang, [
	'EXCEPTION_INVALID_ARGUMENT'	=> 'Invalid argument specified for “%1$s”. Reason: %2$s.',
	'EXCEPTION_OUT_OF_BOUNDS'		=> 'The field “%1$s” received data beyond its bounds.',
	'EXCEPTION_UNEXPECTED_VALUE'	=> 'The field “%1$s” received unexpected data. Reason: %2$s.'
]);
