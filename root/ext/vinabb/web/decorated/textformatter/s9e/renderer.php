<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\decorated\textformatter\s9e;

/**
* s9e\TextFormatter\Renderer adapter
*/
class renderer extends \phpbb\textformatter\s9e\renderer
{
	/**
	* Configure this renderer as per the user's settings
	*
	* Should set the locale as well as the viewcensor/viewflash/viewimg/viewsmilies options.
	*
	* Copied from phpBB 3.2.0-RC1
	* Changes: Add smiley text parameters with the prefix LE_
	*
	* @param \phpbb\user $user
	* @param \phpbb\config\config $config
	* @param \phpbb\auth\auth $auth
	* @return null
	*/
	public function configure_user(\phpbb\user $user, \phpbb\config\config $config, \phpbb\auth\auth $auth)
	{
		$censor = $user->optionget('viewcensors') || !$config['allow_nocensors'] || !$auth->acl_get('u_chgcensors');

		$this->set_viewcensors($censor);
		$this->set_viewflash($user->optionget('viewflash'));
		$this->set_viewimg($user->optionget('viewimg'));
		$this->set_viewsmilies($user->optionget('viewsmilies'));

		// Set the stylesheet parameters
		foreach (array_keys($this->renderer->getParameters()) as $param_name)
		{
			if (strpos($param_name, 'L_') === 0)
			{
				// L_FOO is set to $user->lang('FOO')
				$this->renderer->setParameter($param_name, $user->lang(substr($param_name, 2)));
			}
			else if (strpos($param_name, 'LE_') === 0)
			{
				// LE_FOO is set to $user->lang('EMOTICON_TEXT', 'FOO')
				$this->renderer->setParameter($param_name, $user->lang(['EMOTICON_TEXT', substr($param_name, 3)]));
			}
		}

		// Set this user's style id and other parameters
		$this->renderer->setParameters(array(
			'S_IS_BOT'          => $user->data['is_bot'],
			'S_REGISTERED_USER' => $user->data['is_registered'],
			'S_USER_LOGGED_IN'  => ($user->data['user_id'] != ANONYMOUS),
			'STYLE_ID'          => $user->style['style_id'],
		));
	}
}
