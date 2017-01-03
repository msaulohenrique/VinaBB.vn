<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\controllers\portal\helper;

use vinabb\web\includes\constants;

/**
* Helper for the portal
*/
class helper extends helper_core implements helper_interface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \vinabb\web\controllers\cache\service_interface */
	protected $cache;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\content_visibility */
	protected $content_visibility;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

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

	/** @var \vinabb\web\controllers\helper_interface */
	protected $ext_helper;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $ext_root_path;

	/** @var array */
	protected $forum_data;

	/** @var array */
	protected $portal_cats;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth									$auth				Authentication object
	 * @param \vinabb\web\controllers\cache\service_interface	$cache				Cache service
	 * @param \phpbb\config\config								$config				Config object
	 * @param \phpbb\content_visibility							$content_visibility	Content visibility
	 * @param \phpbb\db\driver\driver_interface					$db					Database object
	 * @param \phpbb\extension\manager							$ext_manager		Extension manager
	 * @param \phpbb\language\language							$language			Language object
	 * @param \phpbb\notification\manager						$notification		Notification manager
	 * @param \phpbb\request\request							$request			Request object
	 * @param \phpbb\template\template							$template			Template object
	 * @param \phpbb\user										$user				User object
	 * @param \phpbb\controller\helper							$helper				Controller helper
	 * @param \vinabb\web\controllers\helper_interface			$ext_helper			Extension helper
	 * @param string											$root_path			phpBB root path
	 */
	public function __construct(
		\phpbb\auth\auth $auth,
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\config\config $config,
		\phpbb\content_visibility $content_visibility,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\extension\manager $ext_manager,
		\phpbb\language\language $language,
		\phpbb\notification\manager $notification,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\vinabb\web\controllers\helper_interface $ext_helper,
		$root_path
	)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
		$this->content_visibility = $content_visibility;
		$this->db = $db;
		$this->ext_manager = $ext_manager;
		$this->language = $language;
		$this->notification = $notification;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->helper = $helper;
		$this->ext_helper = $ext_helper;
		$this->root_path = $root_path;

		$this->ext_root_path = $this->ext_manager->get_extension_path('vinabb/web', true);
		$this->forum_data = $this->cache->get_forum_data();
		$this->portal_cats = $this->cache->get_portal_cats();
	}

	/**
	* Check all of new versions
	*/
	public function check_new_versions()
	{
		if (time() > $this->config['vinabb_web_check_gc'] + (constants::CHECK_VERSION_HOURS * 60 * 60))
		{
			$this->fetch_phpbb_version();
			$this->fetch_php_version();
			$this->fetch_vinabb_version();

			// Save this time
			$this->config->set('vinabb_web_check_gc', time(), true);
		}
	}

	/**
	* Get and set latest phpBB versions
	*/
	public function fetch_phpbb_version()
	{
		$raw = $this->ext_helper->fetch_url($this->config['vinabb_web_check_phpbb_url']);

		// Parse JSON
		$phpbb_data = json_decode($raw, true);

		// Latest version
		$this->update_version_value('phpbb_version', !empty($phpbb_data['stable'][$this->config['vinabb_web_check_phpbb_branch']]['current']) ? $phpbb_data['stable'][$this->config['vinabb_web_check_phpbb_branch']]['current'] : '');

		// Legacy version
		$this->update_version_value('phpbb_legacy_version', !empty($phpbb_data['stable'][$this->config['vinabb_web_check_phpbb_legacy_branch']]['current']) ? $phpbb_data['stable'][$this->config['vinabb_web_check_phpbb_legacy_branch']]['current'] : '');

		// Development version
		$this->update_version_value('phpbb_dev_version', !empty($phpbb_data['unstable'][$this->config['vinabb_web_check_phpbb_dev_branch']]['current']) ? $phpbb_data['unstable'][$this->config['vinabb_web_check_phpbb_dev_branch']]['current'] : '');
	}

	/**
	* Get and set latest PHP versions
	*/
	public function fetch_php_version()
	{
		$raw = $this->ext_helper->fetch_url($this->config['vinabb_web_check_php_url']);
		$raw = str_replace('php:version', 'php-version', $raw);

		// Parse XML
		$php_data = simplexml_load_string($raw);
		$latest_php_versions = [];

		// Find the latest version from feed data
		foreach ($php_data->entry as $entry)
		{
			$php_version = !empty($entry->{'php-version'}) ? $entry->{'php-version'} : '';
			$php_branch = substr($php_version, 0, strrpos($php_version, '.'));

			$latest_php_versions[$php_branch] = [
				'version'	=> $php_version,
				'url'		=> !empty($entry->id) ? $entry->id : ''
			];
		}

		// Latest version
		if (isset($latest_php_versions[$this->config['vinabb_web_check_php_branch']]))
		{
			$this->update_version_value('php_version', $latest_php_versions[$this->config['vinabb_web_check_php_branch']]['version'], $latest_php_versions[$this->config['vinabb_web_check_php_branch']]['url']);
		}

		// Legacy version
		if (isset($latest_php_versions[$this->config['vinabb_web_check_php_legacy_branch']]))
		{
			$this->update_version_value('php_legacy_version', $latest_php_versions[$this->config['vinabb_web_check_php_legacy_branch']]['version'], $latest_php_versions[$this->config['vinabb_web_check_php_legacy_branch']]['url']);
		}
	}

	/**
	* Get and set latest VinaBB.vn version
	*/
	public function fetch_vinabb_version()
	{
		$raw = file_get_contents("{$this->ext_root_path}composer.json");

		// Parse JSON
		$vinabb_data = json_decode($raw, true);

		$this->update_version_value('vinabb_version', !empty($vinabb_data['version']) ? $vinabb_data['version'] : '');
	}

	/**
	* Update newer version numbers found
	*
	* @param string	$type			Suffix of the config name, e.g. phpbb_version -> vinabb_web_check_phpbb_version
	* @param string	$version		Version number
	* @param string	$version_url	Release announcement URL of the new version (Optional)
	*/
	protected function update_version_value($type = 'phpbb_version', $version = '', $version_url = '')
	{
		$version = strtoupper($version);

		if (version_compare($version, $this->config['vinabb_web_check_' . $type], '>'))
		{
			$this->config->set('vinabb_web_check_' . $type, $version);

			if ($version_url != '')
			{
				$this->config->set('vinabb_web_check_' . $type . '_url', $version_url);
			}
		}
	}

	/**
	* Get all of news categories
	*
	* @param string $block_name Twig loop name
	*/
	public function get_portal_cats($block_name = 'portal_cats')
	{
		foreach ($this->portal_cats as $cat_id => $cat_data)
		{
			$this->template->assign_block_vars($block_name, [
				'ID'		=> $cat_id,
				'NAME'		=> ($this->user->lang_name == constants::LANG_VIETNAMESE) ? $cat_data['name_vi'] : $cat_data['name'],
				'VARNAME'	=> $cat_data['varname'],
				'ICON'		=> $cat_data['icon'],
				'URL'		=> $this->helper->route('vinabb_web_portal_cat_route', ['varname' => $cat_data['varname']])
			]);
		}
	}

	/**
	* Get all of headlines
	*
	* @param string $block_name Twig loop name
	*/
	public function get_headlines($block_name = 'headlines')
	{
		foreach ($this->cache->get_headlines($this->user->lang_name) as $row)
		{
			$this->template->assign_block_vars($block_name, [
				'NAME'	=> $row['name'],
				'DESC'	=> $row['desc'],
				'IMG'	=> $row['img'],
				'URL'	=> $row['url']
			]);
		}
	}

	/**
	* Get latest articles on index page
	*
	* @param string $block_name Twig loop name
	*/
	public function get_latest_articles($block_name = 'latest_articles')
	{
		$comment_counter = $this->cache->get_index_comment_counter($this->user->lang_name);

		foreach ($this->cache->get_index_articles($this->user->lang_name) as $article_data)
		{
			$this->template->assign_block_vars($block_name, [
				'CATEGORY'	=> ($this->user->lang_name == constants::LANG_VIETNAMESE) ? $this->portal_cats[$article_data['cat_id']]['name_vi'] : $this->portal_cats[$article_data['cat_id']]['name'],
				'CAT_URL'	=> $this->helper->route('vinabb_web_portal_cat_route', ['varname' => $this->portal_cats[$article_data['cat_id']]['varname']]),
				'NAME'		=> $article_data['name'],
				'IMG'		=> $article_data['img'],
				'DESC'		=> $article_data['desc'],
				'TIME'		=> $this->user->format_date($article_data['time']),
				'URL'		=> $this->helper->route('vinabb_web_portal_article_route', ['varname' => $this->portal_cats[$article_data['cat_id']]['varname'], 'article_id' => $article_data['id'], 'seo' => $article_data['name_seo'] . constants::REWRITE_URL_SEO]),
				'COMMENTS'	=> isset($comment_counter[$article_data['id']]) ? $comment_counter[$article_data['id']] : 0,

				'S_NEW'	=> ($article_data['time'] + (constants::FLAG_DAY_NEW_ARTICLE * 24 * 60 * 60)) > time()
			]);
		}
	}

	/**
	* Get latest phpBB resource items
	*
	* @param string $block_name_prefix	Prefix of Twig loop name
	* @param string $block_name_suffix	Suffix of Twig loop name
	*/
	public function get_latest_bb_items($block_name_prefix = 'bb_new_', $block_name_suffix = 's')
	{
		$bb_types = ['ext', 'style', 'acp_style', 'tool'];

		foreach ($bb_types as $bb_type)
		{
			$new_items = $this->cache->get_new_bb_items($bb_type);

			foreach ($new_items as $new_item)
			{
				$this->template->assign_block_vars($block_name_prefix . $bb_type . $block_name_suffix, [
					'NAME'		=> $new_item['name'],
					'VARNAME'	=> $new_item['varname'],
					'VERSION'	=> $new_item['version'],
					'PRICE'		=> $new_item['price'],
					'NEW'		=> $new_item['added'] + (24 * 60 * 60) > $new_item['updated']
				]);
			}
		}
	}

	/**
	* Generate template variables for latest version blocks
	*/
	public function get_version_tpl()
	{
		$this->template->assign_vars([
			'LATEST_PHPBB_VERSION'				=> $this->config['vinabb_web_check_phpbb_version'],
			'LATEST_PHPBB_DOWNLOAD_URL'			=> str_replace(['{branch}', '{version}'], [$this->config['vinabb_web_check_phpbb_branch'], $this->config['vinabb_web_check_phpbb_version']], htmlspecialchars_decode($this->config['vinabb_web_check_phpbb_download_url'])),
			'LATEST_PHPBB_GITHUB_URL'			=> str_replace(['{branch}', '{version}'], [$this->config['vinabb_web_check_phpbb_branch'], $this->config['vinabb_web_check_phpbb_version']], htmlspecialchars_decode($this->config['vinabb_web_check_phpbb_github_url'])),
			'LATEST_PHPBB_LEGACY_VERSION'		=> $this->config['vinabb_web_check_phpbb_legacy_version'],
			'LATEST_PHPBB_LEGACY_DOWNLOAD_URL'	=> str_replace(['{branch}', '{version}'], [$this->config['vinabb_web_check_phpbb_legacy_branch'], $this->config['vinabb_web_check_phpbb_legacy_version']], htmlspecialchars_decode($this->config['vinabb_web_check_phpbb_download_url'])),
			'LATEST_PHPBB_LEGACY_GITHUB_URL'	=> str_replace(['{branch}', '{version}'], [$this->config['vinabb_web_check_phpbb_legacy_branch'], $this->config['vinabb_web_check_phpbb_legacy_version']], htmlspecialchars_decode($this->config['vinabb_web_check_phpbb_github_url'])),
			'LATEST_PHPBB_DEV_VERSION'			=> $this->config['vinabb_web_check_phpbb_dev_version'],
			'LATEST_PHPBB_DEV_DOWNLOAD_URL'		=> str_replace(['{branch}', '{version}'], [$this->config['vinabb_web_check_phpbb_dev_branch'], $this->config['vinabb_web_check_phpbb_dev_version']], htmlspecialchars_decode($this->config['vinabb_web_check_phpbb_download_dev_url'])),
			'LATEST_PHPBB_DEV_GITHUB_URL'		=> str_replace(['{branch}', '{version}'], [$this->config['vinabb_web_check_phpbb_dev_branch'], $this->config['vinabb_web_check_phpbb_dev_version']], htmlspecialchars_decode($this->config['vinabb_web_check_phpbb_github_url'])),

			'LATEST_IVN_VERSION'		=> $this->config['vinabb_web_check_ivn_version'],
			'LATEST_IVN_LEGACY_VERSION'	=> $this->config['vinabb_web_check_ivn_legacy_version'],
			'LATEST_IVN_DEV_VERSION'	=> $this->config['vinabb_web_check_ivn_dev_version'],
			'LATEST_IVNPLUS_VERSION'	=> $this->config['vinabb_web_check_ivnplus_version'],

			'LATEST_PHP_VERSION'			=> $this->config['vinabb_web_check_php_version'],
			'LATEST_PHP_VERSION_URL'		=> htmlspecialchars_decode($this->config['vinabb_web_check_php_version_url']),
			'LATEST_PHP_LEGACY_VERSION'		=> $this->config['vinabb_web_check_php_legacy_version'],
			'LATEST_PHP_LEGACY_VERSION_URL'	=> htmlspecialchars_decode($this->config['vinabb_web_check_php_legacy_version_url']),
			'LATEST_VINABB_VERSION'			=> $this->config['vinabb_web_check_vinabb_version'],

			'LATEST_VINABB_GITHUB_PATH'			=> constants::VINABB_GITHUB_PATH,
			'LATEST_VINABB_GITHUB_URL'			=> constants::VINABB_GITHUB_URL,
			'LATEST_VINABB_GITHUB_DOWNLOAD_URL'	=> constants::VINABB_GITHUB_DOWNLOAD_URL,
			'LATEST_VINABB_GITHUB_FORK_URL'		=> constants::VINABB_GITHUB_FORK_URL,
			'LATEST_VINABB_TRAVIS_URL'			=> constants::VINABB_TRAVIS_URL,
			'LATEST_VINABB_TRAVIS_IMG_URL'		=> constants::VINABB_TRAVIS_IMG_URL,
			'LATEST_VINABB_INSIGHT_URL'			=> constants::VINABB_INSIGHT_URL,
			'LATEST_VINABB_INSIGHT_IMG_URL'		=> constants::VINABB_INSIGHT_IMG_URL,
			'LATEST_VINABB_SCRUTINIZER_URL'		=> constants::VINABB_SCRUTINIZER_URL,
			'LATEST_VINABB_SCRUTINIZER_IMG_URL'	=> constants::VINABB_SCRUTINIZER_IMG_URL,
			'LATEST_VINABB_CODECLIMATE_URL'		=> constants::VINABB_CODECLIMATE_URL,
			'LATEST_VINABB_CODECLIMATE_IMG_URL'	=> constants::VINABB_CODECLIMATE_IMG_URL
		]);
	}

	/**
	* Generate template variables for the donation block
	*/
	public function get_donate_tpl()
	{
		$this->template->assign_vars([
			'DONATE_LAST_YEAR'	=> max(0, $this->config['vinabb_web_donate_year'] - 1),
			'DONATE_PERCENT'	=> round($this->config['vinabb_web_donate_fund'] / max(1, $this->config['vinabb_web_donate_year_value']) * 100, 0),
			'DONATE_YEAR'		=> $this->config['vinabb_web_donate_year'],
			'DONATE_YEAR_VALUE'	=> $this->config['vinabb_web_donate_year_value'],
			'DONATE_FUND'		=> $this->config['vinabb_web_donate_fund'],
			'DONATE_CURRENCY'	=> $this->config['vinabb_web_donate_currency'],
			'DONATE_OWNER'		=> ($this->user->lang_name == constants::LANG_VIETNAMESE && $this->config['vinabb_web_donate_owner_vi'] != '') ? $this->config['vinabb_web_donate_owner_vi'] : $this->config['vinabb_web_donate_owner'],
			'DONATE_EMAIL'		=> $this->config['vinabb_web_donate_email'],
			'DONATE_BANK'		=> ($this->user->lang_name == constants::LANG_VIETNAMESE && $this->config['vinabb_web_donate_bank_vi'] != '') ? $this->config['vinabb_web_donate_bank_vi'] : $this->config['vinabb_web_donate_bank'],
			'DONATE_BANK_ACC'	=> $this->config['vinabb_web_donate_bank_acc'],
			'DONATE_BANK_SWIFT'	=> $this->config['vinabb_web_donate_bank_swift'],
			'DONATE_PAYPAL'		=> htmlspecialchars_decode($this->config['vinabb_web_donate_paypal'])
		]);
	}
}
