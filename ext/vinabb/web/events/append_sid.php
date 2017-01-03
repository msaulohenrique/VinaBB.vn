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
	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var \phpbb\extension\manager $ext_manager */
	protected $ext_manager;

	/** @var \phpbb\controller\helper $helper */
	protected $helper;

	/** @var string $php_ext */
	protected $php_ext;

	/** @var string $route_name */
	private $route_name;

	/** @var array $route_data */
	private $route_data;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface	$cache			Cache service
	* @param \phpbb\extension\manager							$ext_manager	Extension manager
	* @param \phpbb\controller\helper							$helper			Controller helper
	* @param string												$php_ext		PHP file extension
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

			// Detect PHP filename
			$php_filename = substr(basename($event['url']), 0, strpos(basename($event['url']), ".{$this->php_ext}"));

			// Get parameters
			if ($event['params'] !== false || in_array($php_filename, ['mcp', 'ucp']))
			{
				$this->set_route_data($event['params'], $event['url']);
			}

			// Detect URLs
			if (in_array($php_filename, ['viewforum', 'viewonline', 'mcp', 'ucp', 'memberlist']))
			{
				$this->{'convert_' . $php_filename}();
			}

			// Replace by routes
			if ($this->route_name != '')
			{
				$event['append_sid_overwrite'] = $this->helper->route($this->route_name, $this->route_data, false, $event['session_id']);
			}
		}
	}

	/**
	* Set URL parameters to the route data
	*
	* @param string	$event_params	The value of $event['params']
	* @param string	$event_url		The value of $event['url']
	*/
	protected function set_route_data($event_params, $event_url)
	{
		$event_params = ($event_params !== false) ? $event_params : substr(strrchr($event_url, '?'), 1);
		$event_params = str_replace(['&amp;', '?'], ['&', ''], $event_params);

		// Some cases: abc.php?&x=1
		$event_params = (substr($event_params, 0, 1) == '&') ? substr($event_params, 1) : $event_params;

		if ($event_params != '')
		{
			$params = explode('&', $event_params);

			foreach ($params as $param)
			{
				list($param_key, $param_value) = explode('=', $param);
				$this->route_data[$param_key] = $param_value;
			}
		}
	}

	/**
	* Conversion rules for URLs from viewforum.php
	*/
	protected function convert_viewforum()
	{
		static $forum_data;

		// Get forum SEO names from cache
		if (!isset($forum_data))
		{
			$forum_data = $this->cache->get_forum_data();
		}

		if (isset($this->route_data['f']))
		{
			$this->route_data['forum_id'] = $this->route_data['f'];

			unset($this->route_data['f']);

			if ($this->route_data['forum_id'])
			{
				$this->route_data['seo'] = $forum_data[$this->route_data['forum_id']]['name_seo'] . constants::REWRITE_URL_SEO;
			}

			$this->route_name = 'vinabb_web_board_forum_route';
		}
		// Some URLs of viewforum.php without f=...
		else
		{
			$this->route_name = '';
		}
	}

	/**
	* Conversion rules for URLs from viewonline.php
	*/
	protected function convert_viewonline()
	{
		$this->route_name = 'vinabb_web_user_online_route';
	}

	/**
	* Conversion rules for URLs from mcp.php
	*/
	protected function convert_mcp()
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
	protected function convert_ucp()
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
	protected function convert_memberlist()
	{
		if (isset($this->route_data['mode']))
		{
			if (in_array($this->route_data['mode'], ['contactadmin', 'email', 'contact', 'team']))
			{
				$this->{'convert_memberlist_' . $this->route_data['mode']}();
			}

			unset($this->route_data['mode']);
		}
		else
		{
			$this->route_name = 'vinabb_web_user_list_route';
		}
	}

	/**
	* Sub-method for $this->convert_memberlist() with mode = 'contactadmin'
	*/
	protected function convert_memberlist_contactadmin()
	{
		$this->route_name = 'vinabb_web_user_contact_route';
	}

	/**
	* Sub-method for $this->convert_memberlist() with mode = 'email'
	*/
	protected function convert_memberlist_email()
	{
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
	}

	/**
	* Sub-method for $this->convert_memberlist() with mode = 'contact'
	*/
	protected function convert_memberlist_contact()
	{
		if (isset($this->route_data['u']))
		{
			$this->route_data['user_id'] = $this->route_data['u'];

			unset($this->route_data['u']);
		}

		$this->route_name = 'vinabb_web_user_messenger_route';
	}

	/**
	* Sub-method for $this->convert_memberlist() with mode = 'team'
	*/
	protected function convert_memberlist_team()
	{
		$this->route_name = 'vinabb_web_user_team_route';
	}
}
