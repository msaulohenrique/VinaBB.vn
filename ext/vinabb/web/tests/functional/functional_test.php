<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\tests\functional;

class functional_test extends \phpbb_functional_test_case
{
	/**
	* Setup our extension
	*
	* @return array
	*/
	static protected function setup_extensions()
	{
		return array('vinabb/web');
	}

	/**
	* Just a test!
	*/
	public function test()
	{
		$this->login();
	}
}
