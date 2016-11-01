<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controller;

class board
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\notification\manager */
	protected $notification;

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
	* @param \phpbb\notification\manager $notification
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
		\phpbb\notification\manager $notification,
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
		$this->notification = $notification;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	* Board index page
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function main()
	{
		// Common functions
		include "{$this->root_path}includes/functions_display.{$this->php_ext}";

		// Language
		$this->language->add_lang('viewforum');

		// Mark notifications read
		if (($mark_notification = $this->request->variable('mark_notification', 0)))
		{
			if ($this->user->data['user_id'] == ANONYMOUS)
			{
				if ($this->request->is_ajax())
				{
					trigger_error('LOGIN_REQUIRED');
				}

				login_box('', $this->language->lang('LOGIN_REQUIRED'));
			}

			if (check_link_hash($this->request->variable('hash', ''), 'mark_notification_read'))
			{
				$notification = $this->notification->load_notifications('notification.method.board', array(
					'notification_id'	=> $mark_notification,
				));

				if (isset($notification['notifications'][$mark_notification]))
				{
					$notification = $notification['notifications'][$mark_notification];
					$notification->mark_read();

					if ($this->request->is_ajax())
					{
						$json_response = new \phpbb\json_response();
						$json_response->send(array(
							'success'	=> true,
						));
					}

					if (($redirect = $this->request->variable('redirect', '')))
					{
						redirect(append_sid($this->root_path . $redirect));
					}

					redirect($notification->get_redirect_url());
				}
			}
		}

		// Display forums
		display_forums('', $this->config['load_moderators']);

		// Breadcrumb
		$this->template->assign_block_vars('breadcrumb', array(
			'NAME'	=> $this->language->lang('BOARD')
		));

		// Assign index specific vars
		$this->template->assign_vars(array(
			'FORUM_IMG'					=> $this->user->img('forum_read', 'NO_UNREAD_POSTS'),
			'FORUM_UNREAD_IMG'			=> $this->user->img('forum_unread', 'UNREAD_POSTS'),
			'FORUM_LOCKED_IMG'			=> $this->user->img('forum_read_locked', 'NO_UNREAD_POSTS_LOCKED'),
			'FORUM_UNREAD_LOCKED_IMG'	=> $this->user->img('forum_unread_locked', 'UNREAD_POSTS_LOCKED'),

			'S_BOARD'	=> true,

			'U_MARK_FORUMS'	=> ($this->user->data['is_registered'] || $this->config['load_anon_lastread']) ? $this->helper->route('vinabb_web_board_route', array('hash' => generate_link_hash('global'), 'mark' => 'forums', 'mark_time' => time())) : '',
		));

		return $this->helper->render('index_body.html', $this->language->lang('BOARD'));
	}
}
