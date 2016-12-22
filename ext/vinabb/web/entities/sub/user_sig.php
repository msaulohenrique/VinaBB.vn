<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities\sub;

/**
* Sub-entity for user/user_sig
*/
class user_sig
{
	/**
	* Data for this abstract entity
	*
	*	...
	*		user_sig
	*		user_sig_bbcode_uid
	*		user_sig_bbcode_bitfield
	*		user_options
	*	...
	*
	* @var array $data
	*/
	protected $data;

	/** @var \phpbb\user $user */
	protected $user;

	/**
	* Constructor
	*
	* @param \phpbb\user $user User object
	*/
	public function __construct(\phpbb\user $user)
	{
		$this->user = $user;
	}

	/**
	* Get user signature for edit
	*
	* @return string
	*/
	public function get_sig_for_edit()
	{
		// Use defaults if these haven't been set yet
		$text = isset($this->data['user_sig']) ? $this->data['user_sig'] : '';
		$uid = isset($this->data['user_sig_bbcode_uid']) ? $this->data['user_sig_bbcode_uid'] : '';
		$options = $this->user->optionget('sig_bbcode') ? OPTION_FLAG_BBCODE : 0;
		$options |= $this->user->optionget('sig_links') ? OPTION_FLAG_LINKS : 0;
		$options |= $this->user->optionget('sig_smilies') ? OPTION_FLAG_SMILIES : 0;

		$text_data = generate_text_for_edit($text, $uid, $options);

		return $text_data['text'];
	}

	/**
	* Get user signature for display
	*
	* @param bool $censor True to censor the content
	* @return string
	*/
	public function get_sig_for_display($censor = true)
	{
		// If these haven't been set yet; use defaults
		$text = isset($this->data['user_sig']) ? $this->data['user_sig'] : '';
		$uid = isset($this->data['user_sig_bbcode_uid']) ? $this->data['user_sig_bbcode_uid'] : '';
		$bitfield = isset($this->data['user_sig_bbcode_bitfield']) ? $this->data['user_sig_bbcode_bitfield'] : '';
		$options = $this->user->optionget('sig_bbcode') ? OPTION_FLAG_BBCODE : 0;
		$options |= $this->user->optionget('sig_links') ? OPTION_FLAG_LINKS : 0;
		$options |= $this->user->optionget('sig_smilies') ? OPTION_FLAG_SMILIES : 0;

		return generate_text_for_display($text, $uid, $bitfield, $options, $censor);
	}
}
