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
	/** @var \phpbb\language\language */
	protected $language;

	/**
	* Constructor
	*
	* @param \phpbb\cache\driver\driver_interface $cache
	* @param string                               $cache_dir
	* @param string                               $key
	* @param factory                              $factory
	* @param \phpbb\event\dispatcher_interface    $dispatcher
	* @param \phpbb\language\language             $language
	*/
	public function __construct(\phpbb\cache\driver\driver_interface $cache, $cache_dir, $key, factory $factory, \phpbb\event\dispatcher_interface $dispatcher, \phpbb\language\language $language)
	{
		parent::__construct($cache, $cache_dir, $key, $factory, $dispatcher);
		$this->language = $language;
	}

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
			// L_FOO is set to $this->language->lang('FOO')
			if (strpos($param_name, 'L_') === 0)
			{
				$this->renderer->setParameter($param_name, $this->language->lang(substr($param_name, 2)));
			}
			// LE_FOO is set to $this->language->lang(['EMOTICON_TEXT', 'FOO'])
			else if (strpos($param_name, 'LE_') === 0)
			{
				$this->renderer->setParameter($param_name, $this->language->lang(['EMOTICON_TEXT', substr($param_name, 3)]));
			}
		}

		// Set this user's style id and other parameters
		$this->renderer->setParameters([
			'S_IS_BOT'          => $user->data['is_bot'],
			'S_REGISTERED_USER' => $user->data['is_registered'],
			'S_USER_LOGGED_IN'  => ($user->data['user_id'] != ANONYMOUS),
			'STYLE_ID'          => $user->style['style_id']
		]);
	}
}
