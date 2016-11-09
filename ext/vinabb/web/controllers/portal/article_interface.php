<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\portal;

interface article_interface
{
	/**
	* View details an article
	*
	* @param $article_id Article ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function article($article_id);
}
