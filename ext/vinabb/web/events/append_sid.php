<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use vinabb\web\includes\constants;

/**
* PHP events
*/
class append_sid implements EventSubscriberInterface
{
	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface $cache
	* @param \phpbb\extension\manager $ext_manager
	* @param \phpbb\controller\helper $helper
	* @param string $php_ext
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\extension\manager $ext_manager,
		\phpbb\controller\helper $helper,
		$php_ext
	)
	{
		$this->cache = $cache;
		$this->ext_manager = $ext_manager;
		$this->helper = $helper;
		$this->php_ext = $php_ext;
	}

	/**
	* List of phpBB's PHP events to be used
	*
	* @return array
	*/
	static public function getSubscribedEvents()
	{
		return [
			'core.append_sid'	=> 'append_sid'
		];
	}

	/**
	* core.append_sid
	*
	* @param array $event Data from the PHP event
	*/
	public function append_sid($event)
	{
		// Add checking our extension, unless it causes errors when disabling the extension
		if (!$event['is_route'] && $this->ext_manager->is_enabled('vinabb/web'))
		{
			// Get parameters
			$params_ary = [];

			if ($event['params'] !== false || strpos($event['url'], "ucp.{$this->php_ext}") !== false || strpos($event['url'], "mcp.{$this->php_ext}") !== false)
			{
				$event_params = ($event['params'] !== false) ? $event['params'] : substr(strrchr($event['url'], '?'), 1);
				$event_params = str_replace(['&amp;', '?'], ['&', ''], $event_params);

				// Some cases: abc.php?&x=1
				$event_params = (substr($event_params, 0, 1) == '&') ? substr($event_params, 1) : $event_params;

				if (!empty($event_params))
				{
					$params = explode('&', $event_params);

					foreach ($params as $param)
					{
						list($param_key, $param_value) = explode('=', $param);
						$params_ary[$param_key] = $param_value;
					}
				}
			}

			// Detect URLs
			$route_name = '';

			if (strpos($event['url'], "viewforum.{$this->php_ext}") !== false)
			{
				// Get forum SEO names from cache
				$forum_data = $this->cache->get_forum_data();

				if (!sizeof($params_ary))
				{
					$params_ary['f'] = '';
				}

				if (isset($params_ary['f']))
				{
					$params_ary['forum_id'] = $params_ary['f'];
					unset($params_ary['f']);

					if ($params_ary['forum_id'])
					{
						$params_ary['seo'] = $forum_data[$params_ary['forum_id']]['name_seo'] . constants::REWRITE_URL_SEO;
					}
				}

				$route_name = 'vinabb_web_board_forum_route';
			}
			else if (strpos($event['url'], "viewonline.{$this->php_ext}") !== false)
			{
				$route_name = 'vinabb_web_user_online_route';
			}
			else if (strpos($event['url'], "mcp.{$this->php_ext}") !== false)
			{
				if (isset($params_ary['i']))
				{
					$params_ary['id'] = (substr($params_ary['i'], 0, 4) == 'mcp_') ? substr($params_ary['i'], 4) : $params_ary['i'];
					unset($params_ary['i']);
				}

				$route_name = 'vinabb_web_mcp_route';
			}
			else if (strpos($event['url'], "ucp.{$this->php_ext}") !== false)
			{
				if (isset($params_ary['i']))
				{
					$params_ary['id'] = (substr($params_ary['i'], 0, 4) == 'ucp_') ? substr($params_ary['i'], 4) : $params_ary['i'];
					unset($params_ary['i']);
				}
				else if (isset($params_ary['mode']) && in_array($params_ary['mode'], ['activate', 'resend_act', 'sendpassword', 'register', 'confirm', 'login', 'login_link', 'logout', 'terms', 'privacy', 'delete_cookies', 'switch_perm', 'restore_perm']))
				{
					$params_ary['id'] = 'front';
				}

				$route_name = 'vinabb_web_ucp_route';
			}
			else if (strpos($event['url'], "memberlist.{$this->php_ext}") !== false)
			{
				if (isset($params_ary['mode']))
				{
					switch ($params_ary['mode'])
					{
						case 'contactadmin':
							$route_name = 'vinabb_web_user_contact_route';
						break;

						case 'email':
							if (isset($params_ary['t']))
							{
								$params_ary['type'] = 'topic';
								$params_ary['id'] = $params_ary['t'];
								unset($params_ary['t']);
							}
							else if (isset($params_ary['u']))
							{
								$params_ary['type'] = 'user';
								$params_ary['id'] = $params_ary['u'];
								unset($params_ary['u']);
							}

							$route_name = 'vinabb_web_user_email_route';
						break;

						case 'contact':
							if (isset($params_ary['u']))
							{
								$params_ary['user_id'] = $params_ary['u'];
								unset($params_ary['u']);
							}

							$route_name = 'vinabb_web_user_messenger_route';
						break;

						case 'team':
							$route_name = 'vinabb_web_user_team_route';
						break;
					}

					unset($params_ary['mode']);
				}
				else
				{
					$route_name = 'vinabb_web_user_list_route';
				}
			}

			// Replace by routes
			if (!empty($route_name))
			{
				$event['append_sid_overwrite'] = $this->helper->route($route_name, $params_ary, false, $event['session_id']);
			}
		}
	}
}
