<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers;

use Symfony\Component\DependencyInjection\ContainerInterface;
use vinabb\web\includes\constants;

class embed
{
	/** @var \phpbb\auth\auth $auth */
	protected $auth;

	/** @var ContainerInterface $container */
	protected $container;

	/** @var \vinabb\web\controllers\board\post_interface $post */
	protected $post;

	/** @var \phpbb\template\template $template */
	protected $template;

	/** @var \phpbb\controller\helper $helper */
	protected $helper;

	/**
	* Constructor
	*
	* @param \phpbb\auth\auth								$auth		Authentication object
	* @param ContainerInterface								$container	Container object
	* @param \vinabb\web\controllers\board\post_interface	$post		Post controller
	* @param \phpbb\template\template						$template	Template object
	* @param \phpbb\controller\helper						$helper		Controller helper
	*/
	public function __construct(
		\phpbb\auth\auth $auth,
		ContainerInterface $container,
		\vinabb\web\controllers\board\post_interface $post,
		\phpbb\template\template $template,
		\phpbb\controller\helper $helper
	)
	{
		$this->auth = $auth;
		$this->container = $container;
		$this->post = $post;
		$this->template = $template;
		$this->helper = $helper;
	}

	/**
	* Embed page of a forum
	*
	* @param int $forum_id Forum ID
	* @return \Symfony\Component\HttpFoundation\Response
	* @throws \phpbb\exception\http_exception
	*/
	public function forum($forum_id)
	{
		try
		{
			/** @var \vinabb\web\entities\forum_interface $entity */
			$entity = $this->container->get('vinabb.web.entities.forum')->load($forum_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			throw new \phpbb\exception\http_exception(404, 'NO_FORUM');
		}

		// Ok forum exists, but check forum permissions
		if (!$this->auth->acl_gets('f_list', 'f_read', $forum_id) || ($entity->get_type() == FORUM_LINK && $entity->get_link() && !$this->auth->acl_get('f_read', $forum_id)))
		{
			send_status_line(403, 'Forbidden');
			trigger_error('SORRY_AUTH_READ');
		}

		$this->template->assign_vars([
			'FORUM_ID'		=> $forum_id,
			'FORUM_NAME'	=> $entity->get_name(),
			'FORUM_DESC'	=> $entity->get_desc_for_display(),

			'U_FORUM'	=> $this->helper->route('vinabb_web_board_forum_route', ['forum_id' => $forum_id, 'seo' => $entity->get_name_seo() . constants::REWRITE_URL_SEO])
		]);

		return $this->helper->render('embed_forum.html');
	}

	/**
	* Embed page of a post, rediect to the first post
	*
	* @param int $topic_id Topic ID
	* @throws \phpbb\exception\http_exception
	*/
	public function topic($topic_id)
	{
		try
		{
			/** @var \vinabb\web\entities\topic_interface $entity */
			$entity = $this->container->get('vinabb.web.entities.topic')->load($topic_id);
		}
		catch (\vinabb\web\exceptions\base $e)
		{
			throw new \phpbb\exception\http_exception(404, 'NO_TOPIC');
		}

		redirect($this->helper->route('vinabb_web_embed_post_route', ['post_id' => $entity->get_first_post_id()]));
	}

	/**
	* Embed page of a post
	*
	* @param int $post_id Post ID
	* @return \Symfony\Component\HttpFoundation\Response
	*/
	public function post($post_id)
	{
		// Load the post controller
		$this->post->main($post_id, 'embed_post.html');
	}
}
