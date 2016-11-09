<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\decorates\message;

class user_form extends \phpbb\message\user_form
{
	/**
	* {inheritDoc}
	*/
	public function bind_with_id(\phpbb\request\request_interface $request, $user_id)
	{
		parent::bind($request);

		$this->recipient_id = $user_id;
		$this->subject = $request->variable('subject', '', true);

		$this->recipient_row = $this->get_user_row($this->recipient_id);
	}
}
