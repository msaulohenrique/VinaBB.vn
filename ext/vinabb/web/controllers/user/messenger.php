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
	/** @var \phpbb\auth\auth $auth */
	protected $auth;

	/** @var \phpbb\config\config $config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface $db */
	protected $db;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\request\request $request */
	protected $request;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\user $user */
	protected $user;

	/** @var \phpbb\controller\helper $helper */
	protected $helper;

	/** @var string $root_path */
	protected $root_path;

	/** @var string $php_ext */
	protected $php_ext;

	/** @var array $recipient_data */
	protected $recipient_data;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth					$auth		Authentication object
	* @param \phpbb\config\config				$config		Config object
	* @param \phpbb\db\driver\driver_interface	$db			Database object
	* @param \phpbb\language\language			$language	Language object
	* @param \phpbb\request\request				$request	Request object
	* @param \phpbb\template\template			$template	Template object
	* @param \phpbb\user						$user		User object
	* @param \phpbb\controller\helper			$helper		Controller helper
	* @param string								$root_path	phpBB root path
	* @param string								$php_ext	PHP file extension
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
			send_status_line(403, 'Forbidden');
			trigger_error('NOT_AUTHORISED');
		}

		$s_select = (extension_loaded('xml') && $this->config['jab_enable']) ? 'S_SEND_JABBER' : 'S_NO_SEND_JABBER';
		$s_action = $this->helper->route('vinabb_web_user_messenger_route', ['action' => $action, 'user_id' => $user_id]);

		$this->get_recipient_data($user_id);

		// Post data grab actions
		add_form_key('memberlist_messaging');

		if ($this->request->is_set_post('submit') && extension_loaded('xml') && $this->config['jab_enable'])
		{
			if (check_form_key('memberlist_messaging'))
			{
				$this->send_message();

				$s_select = 'S_SENT_JABBER';
			}
			else
			{
				trigger_error('FORM_INVALID');
			}
		}

		// Send vars to the template
		$this->template->assign_vars([
			'IM_CONTACT'	=> $this->recipient_data['user_jabber'],
			'A_IM_CONTACT'	=> addslashes($this->recipient_data['user_jabber']),

			'USERNAME'		=> $this->recipient_data['username'],
			'CONTACT_NAME'	=> $this->recipient_data['user_jabber'],
			'SITENAME'		=> $this->config['sitename'],

			'L_SEND_IM_EXPLAIN'	=> $this->language->lang('IM_JABBER'),
			'L_IM_SENT_JABBER'	=> $this->language->lang('IM_SENT_JABBER', $this->recipient_data['username']),

			$s_select		=> true,
			'S_IM_ACTION'	=> $s_action
		]);

		return $this->helper->render('memberlist_im.html', $this->language->lang('IM_USER'));
	}

	/**
	* Get the recipient's user data
	*
	* @param int $user_id Recipient user ID
	*/
	protected function get_recipient_data($user_id)
	{
		// Grab relevant data
		$sql = "SELECT user_id, username, user_email, user_lang, user_jabber
			FROM " . USERS_TABLE . '
			WHERE user_id = ' . (int) $user_id . '
				AND ' . $this->db->sql_in_set('user_type', [USER_NORMAL, USER_FOUNDER]);
		$result = $this->db->sql_query($sql);
		$this->recipient_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($this->recipient_data === false)
		{
			trigger_error('NO_USER');
		}
		else if (empty($this->recipient_data['user_jabber']))
		{
			trigger_error('IM_NO_DATA');
		}
	}

	/**
	* Send message to the recipient
	*/
	protected function send_message()
	{
		if (!class_exists('messenger'))
		{
			include "{$this->root_path}includes/functions_messenger.{$this->php_ext}";
		}

		$subject = $this->language->lang('IM_JABBER_SUBJECT', $this->user->data['username'], $this->config['server_name']);
		$message = $this->request->variable('message', '', true);

		if ($message == '')
		{
			trigger_error('EMPTY_MESSAGE_IM');
		}

		$messenger = new \messenger(false);
		$messenger->template('profile_send_im', $this->recipient_data['user_lang']);
		$messenger->subject(htmlspecialchars_decode($subject));
		$messenger->replyto($this->user->data['user_email']);
		$messenger->set_addresses($this->recipient_data);

		$messenger->assign_vars([
			'BOARD_CONTACT'	=> phpbb_get_board_contact($this->config, $this->php_ext),
			'FROM_USERNAME'	=> htmlspecialchars_decode($this->user->data['username']),
			'TO_USERNAME'	=> htmlspecialchars_decode($this->recipient_data['username']),
			'MESSAGE'		=> htmlspecialchars_decode($message)
		]);

		$messenger->send(NOTIFY_IM);
	}
}
