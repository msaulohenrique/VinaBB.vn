<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\user;

/**
* Sending a message via Jabber to an user
*/
class messenger implements messenger_interface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

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
	* @param \phpbb\config\config $config
	* @param \phpbb\db\driver\driver_interface $db
	* @param \phpbb\language\language $language
	* @param \phpbb\request\request $request
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\controller\helper $helper
	* @param string $root_path
	* @param string $php_ext
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		$root_path,
		$php_ext
	)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Main method
	*
	* @param string	$action		Service type
	* @param int	$user_id	User ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main($action, $user_id)
	{
		// Language
		$this->language->add_lang('memberlist');

		if (!$this->auth->acl_get('u_sendim'))
		{
			trigger_error('NOT_AUTHORISED');
		}

		$presence_img = '';

		switch ($action)
		{
			case 'jabber':
				$lang = 'JABBER';
				$sql_field = 'user_jabber';
				$s_select = (extension_loaded('xml') && $this->config['jab_enable']) ? 'S_SEND_JABBER' : 'S_NO_SEND_JABBER';
				$s_action = $this->helper->route('vinabb_web_user_messenger_route', ['action' => $action, 'user_id' => $user_id]);
			break;

			default:
				trigger_error('NO_MODE');
			break;
		}

		// Grab relevant data
		$sql = "SELECT user_id, username, user_email, user_lang, $sql_field
			FROM " . USERS_TABLE . "
			WHERE user_id = $user_id
				AND " . $this->db->sql_in_set('user_type', [USER_NORMAL, USER_FOUNDER]);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			trigger_error('NO_USER');
		}
		else if (empty($row[$sql_field]))
		{
			trigger_error('IM_NO_DATA');
		}

		// Post data grab actions
		switch ($action)
		{
			case 'jabber':
				add_form_key('memberlist_messaging');

				if ($this->request->is_set_post('submit') && extension_loaded('xml') && $this->config['jab_enable'])
				{
					if (check_form_key('memberlist_messaging'))
					{
						if (!class_exists('messenger'))
						{
							include "{$this->root_path}includes/functions_messenger.{$this->php_ext}";
						}

						$subject = $this->language->lang('IM_JABBER_SUBJECT', $this->user->data['username'], $this->config['server_name']);
						$message = $this->request->variable('message', '', true);

						if (empty($message))
						{
							trigger_error('EMPTY_MESSAGE_IM');
						}

						$messenger = new \messenger(false);
						$messenger->template('profile_send_im', $row['user_lang']);
						$messenger->subject(htmlspecialchars_decode($subject));
						$messenger->replyto($this->user->data['user_email']);
						$messenger->set_addresses($row);

						$messenger->assign_vars([
							'BOARD_CONTACT'	=> phpbb_get_board_contact($this->config, $this->php_ext),
							'FROM_USERNAME'	=> htmlspecialchars_decode($this->user->data['username']),
							'TO_USERNAME'	=> htmlspecialchars_decode($row['username']),
							'MESSAGE'		=> htmlspecialchars_decode($message)
						]);

						$messenger->send(NOTIFY_IM);

						$s_select = 'S_SENT_JABBER';
					}
					else
					{
						trigger_error('FORM_INVALID');
					}
				}
			break;
		}

		// Send vars to the template
		$this->template->assign_vars([
			'IM_CONTACT'	=> $row[$sql_field],
			'A_IM_CONTACT'	=> addslashes($row[$sql_field]),

			'USERNAME'		=> $row['username'],
			'CONTACT_NAME'	=> $row[$sql_field],
			'SITENAME'		=> $this->config['sitename'],

			'PRESENCE_IMG'	=> $presence_img,

			'L_SEND_IM_EXPLAIN'	=> $this->language->lang('IM_' . $lang),
			'L_IM_SENT_JABBER'	=> $this->language->lang('IM_SENT_JABBER', $row['username']),

			$s_select		=> true,
			'S_IM_ACTION'	=> $s_action
		]);

		return $this->helper->render('memberlist_im.html', $this->language->lang('IM_USER'));
	}
}
