<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

/**
* User Whois tool for only administrators
*/
class whois implements whois_interface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\language\language $language
	* @param \phpbb\template\template $template
	* @param \phpbb\controller\helper $helper
	* @param string $root_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\controller\helper $helper,
		$root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->language = $language;
		$this->template = $template;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Main method
	*
	* @param string $session_id Session ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main($session_id)
	{
		if ($this->auth->acl_get('a_'))
		{
			include "{$this->root_path}includes/functions_user.{$this->php_ext}";

			$sql = 'SELECT u.user_id, u.username, u.user_type, s.session_ip
				FROM ' . USERS_TABLE . ' u, ' . SESSIONS_TABLE . " s
				WHERE s.session_id = '" . $this->db->sql_escape($session_id) . "'
			AND	u.user_id = s.session_user_id";
			$result = $this->db->sql_query($sql);

			if ($row = $this->db->sql_fetchrow($result))
			{
				$this->template->assign_var('WHOIS', user_ipwhois($row['session_ip']));
			}
			$this->db->sql_freeresult($result);

			return $this->helper->render('viewonline_whois.html', $this->language->lang('WHO_IS_ONLINE'));
		}
		else
		{
			send_status_line(401, 'Unauthorized');
			trigger_error('NOT_AUTHORISED');
		}
	}
}
