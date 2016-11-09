<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\decorates\message;

class topic_form extends \phpbb\message\topic_form
{
	/**
	* {inheritDoc}
	*/
	public function bind_with_id(\phpbb\request\request_interface $request, $topic_id)
	{
		parent::bind($request);

		$this->topic_id = $topic_id;
		$this->recipient_address = $request->variable('email', '');
		$this->recipient_name = $request->variable('name', '', true);
		$this->recipient_lang = $request->variable('lang', $this->config['default_lang']);

		$this->topic_row = $this->get_topic_row($this->topic_id);
	}
}
