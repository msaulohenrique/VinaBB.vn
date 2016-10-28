<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controller;

class embed
{
	/** @var \phpbb\auth\auth */
	protected $auth;

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
	* Embed page of a forum
	*
	* @param $forum_id
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function forum($forum_id)
	{
		$error = false;
		$no_auth = false;

		if (!$forum_id)
		{
			$error = true;
		}
		else
		{
			$sql = 'SELECT *
				FROM ' . FORUMS_TABLE . "
				WHERE forum_id = $forum_id";
			$result = $this->db->sql_query($sql);
			$forum_data = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($forum_data)
			{
				// Ok forum exists, but check forum permissions
				if (!$this->auth->acl_gets('f_list', 'f_read', $forum_id) || ($forum_data['forum_type'] == FORUM_LINK && $forum_data['forum_link'] && !$this->auth->acl_get('f_read', $forum_id)))
				{
					$no_auth = true;
				}
				else
				{
					$this->template->assign_vars(array(
						'FORUM_NAME'	=> $forum_data['forum_name'],
						'FORUM_DESC'	=> truncate_string(strip_tags(generate_text_for_display($forum_data['forum_desc'], $forum_data['forum_desc_uid'], $forum_data['forum_desc_bitfield'], $forum_data['forum_desc_options'])), 200, 255, false, $this->language->lang('ELLIPSIS')),
						'FORUM_URL'		=> $this->helper->route('vinabb_web_board_forum_route', array('forum_id' => $forum_id)),
					));
				}
			}
			else
			{
				$error = true;
			}
		}

		if ($error)
		{
			$this->template->assign_vars(array(
				'ERROR'	=> ($no_auth) ? $this->language->lang('SORRY_AUTH_READ') : $this->language->lang('NO_FORUM'),
			));
		}

		return $this->helper->render(($error) ? 'embed_error.html' : 'embed_forum.html');
	}

	/**
	* Embed page of a post, rediect to the first post
	*
	* @param $topic_id
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function topic($topic_id)
	{
		$error = false;

		if (!$topic_id)
		{
			$error = true;
		}
		else
		{
			$sql = 'SELECT topic_first_post_id
				FROM ' . TOPICS_TABLE . "
				WHERE topic_id = $topic_id";
			$result = $this->db->sql_query($sql);
			$post_id = (int) $this->db->sql_fetchfield('topic_first_post_id');
			$this->db->sql_freeresult($result);

			if ($post_id)
			{
				redirect($this->helper->route('vinabb_web_embed_post_route', array('post_id' => $post_id)));
			}
			else
			{
				$error = true;
			}
		}

		if ($error)
		{
			$this->template->assign_vars(array(
				'ERROR'	=> $this->language->lang('NO_TOPIC'),
			));

			return $this->helper->render('embed_error.html');
		}
	}

	/**
	* Embed page of a post
	*
	* @param $post_id
	*
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function post($post_id)
	{
		$error = false;

		if (!$post_id)
		{
			$error = true;
		}
		else
		{
			$sql = 'SELECT *
				FROM ' . POSTS_TABLE . "
				WHERE post_id = $post_id";
			$result = $this->db->sql_query($sql);
			$post_data = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($post_data)
			{
				$enable_bbcode = ($post_data['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0;
				$enable_smilies = ($post_data['enable_smilies']) ? OPTION_FLAG_SMILIES : 0;
				$enable_magic_url = ($post_data['enable_magic_url']) ? OPTION_FLAG_LINKS : 0;
				$bbcode_options = $enable_bbcode ^ $enable_smilies ^ $enable_magic_url;

				if ($post_data['poster_id'])
				{
					$sql = 'SELECT username, user_avatar
						FROM ' . USERS_TABLE . '
						WHERE user_id = ' . $post_data['poster_id'];
					$result = $this->db->sql_query($sql);
					$poster_data = $this->db->sql_fetchrow($result);
					$this->db->sql_freeresult($result);
				}

				$this->template->assign_vars(array(
					'POST_SUBJECT'	=>truncate_string(generate_text_for_display($post_data['post_subject'], $post_data['bbcode_uid'], $post_data['bbcode_bitfield'], $bbcode_options), 40, 255, false, $this->language->lang('ELLIPSIS')),
					'POST_TEXT'		=> truncate_string(strip_tags(generate_text_for_display($post_data['post_text'], $post_data['bbcode_uid'], $post_data['bbcode_bitfield'], $bbcode_options)), 189, 255, false, $this->language->lang('ELLIPSIS')),
					'POST_URL'		=> $this->helper->route('vinabb_web_board_topic_route', array('topic_id' => $post_data['topic_id'], '#' => 'p' . $post_data['post_id'])),
					'POSTER'		=> ($post_data['poster_id']) ? $poster_data['username'] : ((!empty($post_data['post_username'])) ? $post_data['post_username'] : $this->language->lang('GUEST')),
					'POSTER_URL'	=> ($post_data['poster_id']) ? $this->helper->route('vinabb_web_user_profile_route', array('username' => $post_data['username'])) : '',
					'POST_TIME'		=> $this->user->format_date($post_data['post_time'], 'd/m/Y H:i')
				));
			}
			else
			{
				$error = true;
			}
		}

		if ($error)
		{
			$this->template->assign_vars(array(
				'ERROR'	=> $this->language->lang('NO_POST'),
			));
		}

		return $this->helper->render(($error) ? 'embed_error.html' : 'embed_post.html');
	}
}
