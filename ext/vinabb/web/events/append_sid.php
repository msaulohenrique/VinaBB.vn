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

	/** @var string */
	private $route_name;

	/** @var array */
	private $route_data;

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
			// Reset values
			$this->route_name = '';
			$this->route_data = [];

			// Get parameters
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
						$this->route_data[$param_key] = $param_value;
					}
				}
			}

			// Detect URLs
			if (strpos($event['url'], "viewforum.{$this->php_ext}") !== false)
			{
				$this->convert_viewforum();
			}
			else if (strpos($event['url'], "viewonline.{$this->php_ext}") !== false)
			{
				$this->route_name = 'vinabb_web_user_online_route';
			}
			else if (strpos($event['url'], "mcp.{$this->php_ext}") !== false)
			{
				$this->convert_mcp();
			}
			else if (strpos($event['url'], "ucp.{$this->php_ext}") !== false)
			{
				$this->convert_ucp();
			}
			else if (strpos($event['url'], "memberlist.{$this->php_ext}") !== false)
			{
				$this->convert_memberlist();
			}

			// Replace by routes
			if ($this->route_name != '')
			{
				$event['append_sid_overwrite'] = $this->helper->route($this->route_name, $this->route_data, false, $event['session_id']);
			}
		}
	}

	/**
	* Conversion rules for URLs from viewforum.php
	*/
	private function convert_viewforum()
	{
		// Get forum SEO names from cache
		$forum_data = $this->cache->get_forum_data();

		if (!sizeof($this->route_data))
		{
			$this->route_data['f'] = '';
		}

		if (isset($this->route_data['f']))
		{
			$this->route_data['forum_id'] = $this->route_data['f'];

			unset($this->route_data['f']);

			if ($this->route_data['forum_id'])
			{
				$this->route_data['seo'] = $forum_data[$this->route_data['forum_id']]['name_seo'] . constants::REWRITE_URL_SEO;
			}
		}

		$this->route_name = 'vinabb_web_board_forum_route';
	}

	/**
	* Conversion rules for URLs from mcp.php
	*/
	private function convert_mcp()
	{
		if (isset($this->route_data['i']))
		{
			$this->route_data['id'] = (substr($this->route_data['i'], 0, 4) == 'mcp_') ? substr($this->route_data['i'], 4) : $this->route_data['i'];

			unset($this->route_data['i']);
		}

		$this->route_name = 'vinabb_web_mcp_route';
	}

	/**
	* Conversion rules for URLs from ucp.php
	*/
	private function convert_ucp()
	{
		if (isset($this->route_data['i']))
		{
			$this->route_data['id'] = (substr($this->route_data['i'], 0, 4) == 'ucp_') ? substr($this->route_data['i'], 4) : $this->route_data['i'];

			unset($this->route_data['i']);
		}
		else if (isset($this->route_data['mode']) && in_array($this->route_data['mode'], ['activate', 'resend_act', 'sendpassword', 'register', 'confirm', 'login', 'login_link', 'logout', 'terms', 'privacy', 'delete_cookies', 'switch_perm', 'restore_perm']))
		{
			$this->route_data['id'] = 'front';
		}

		$this->route_name = 'vinabb_web_ucp_route';
	}

	/**
	* Conversion rules for URLs from memberlist.php
	*/
	private function convert_memberlist()
	{
		if (isset($this->route_data['mode']))
		{
			switch ($this->route_data['mode'])
			{
				case 'contactadmin':
					$this->route_name = 'vinabb_web_user_contact_route';
				break;

				case 'email':
					if (isset($this->route_data['t']))
					{
						$this->route_data['type'] = 'topic';
						$this->route_data['id'] = $this->route_data['t'];

						unset($this->route_data['t']);
					}
					else if (isset($this->route_data['u']))
					{
						$this->route_data['type'] = 'user';
						$this->route_data['id'] = $this->route_data['u'];

						unset($this->route_data['u']);
					}

					$this->route_name = 'vinabb_web_user_email_route';
				break;

				case 'contact':
					if (isset($this->route_data['u']))
					{
						$this->route_data['user_id'] = $this->route_data['u'];

						unset($this->route_data['u']);
					}

					$this->route_name = 'vinabb_web_user_messenger_route';
				break;

				case 'team':
					$this->route_name = 'vinabb_web_user_team_route';
				break;
			}

			unset($this->route_data['mode']);
		}
		else
		{
			$this->route_name = 'vinabb_web_user_list_route';
		}
	}
}
