<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\events;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use s9e\TextFormatter\Bundles\MediaPack;

/**
* PHP events
*/
class text_formatter implements EventSubscriberInterface
{
	/** @var \vinabb\web\controllers\cache\service_interface $cache */
	protected $cache;

	/** @var \phpbb\language\language $language */
	protected $language;

	/** @var \phpbb\template\template $template */
	protected $template;

	/**
	* Constructor
	*
	* @param \vinabb\web\controllers\cache\service_interface	$cache			Cache service
	* @param \phpbb\language\language							$language		Language object
	* @param \phpbb\template\template							$template		Template object
	*/
	public function __construct(
		\vinabb\web\controllers\cache\service_interface $cache,
		\phpbb\language\language $language,
		\phpbb\template\template $template
	)
	{
		$this->cache = $cache;
		$this->language = $language;
		$this->template = $template;
	}

	/**
	* List of phpBB's PHP events to be used
	*
	* @return array
	*/
	static public function getSubscribedEvents()
	{
		return [
			'core.modify_format_display_text_after'		=> 'modify_format_display_text_after',
			'core.modify_submit_post_data'				=> 'modify_submit_post_data',
			'core.modify_text_for_display_after'		=> 'modify_text_for_display_after',
			'core.modify_text_for_edit_before'			=> 'modify_text_for_edit_before',
			'core.modify_text_for_storage_after'		=> 'modify_text_for_storage_after',
			'core.submit_pm_before'						=> 'submit_pm_before',
			'core.text_formatter_s9e_configure_before'	=> 'text_formatter_s9e_configure_before',
			'core.text_formatter_s9e_configure_after'	=> 'text_formatter_s9e_configure_after'
		];
	}

	/**
	* core.modify_format_display_text_after
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_format_display_text_after($event)
	{
		$event['text'] = $this->render($this->parse($event['text']));
	}

	/**
	* core.modify_submit_post_data
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_submit_post_data($event)
	{
		$data = $event['data'];
		$data['message'] = $this->parse($data['message']);
		$event['data'] = $data;
	}

	/**
	* core.modify_text_for_display_after
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_text_for_display_after($event)
	{
		$event['text'] = $this->render($event['text']);

		// Load highlight.js
		$this->template->assign_var('S_LOAD_HIGHLIGHT', true);
	}

	/**
	* core.modify_text_for_edit_before
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_text_for_edit_before($event)
	{
		$event['text'] = $this->unparse($event['text']);
	}

	/**
	* core.modify_text_for_storage_after
	*
	* @param array $event Data from the PHP event
	*/
	public function modify_text_for_storage_after($event)
	{
		$event['text'] = $this->parse($event['text']);
	}

	/**
	* core.submit_pm_before
	*
	* @param array $event Data from the PHP event
	*/
	public function submit_pm_before($event)
	{
		$data = $event['data'];
		$data['message'] = $this->parse($data['message']);
		$event['data'] = $data;
	}

	/**
	* core.text_formatter_s9e_configure_before
	*
	* @param array $event Data from the PHP event
	*/
	public function text_formatter_s9e_configure_before($event)
	{
		$configurator = $event['configurator'];

		// Add social sites
		$configurator->MediaEmbed->add('facebook');
		$configurator->MediaEmbed->add('twitter');
		$configurator->MediaEmbed->add('googleplus');
		$configurator->MediaEmbed->add('youtube');
		$configurator->MediaEmbed->add('flickr');
		$configurator->MediaEmbed->add('instagram');
		$configurator->MediaEmbed->add('gist');

		// Add our site
		$configurator->MediaEmbed->add('vinabb', [
			'host'		=> 'vinabb.vn',
			'extract'	=> [
				"!vinabb\\.vn/viewtopic\\.php\\?f=(?'f'[0-9]+)\\&t=(?'t'[0-9]+)!",
				"!vinabb\\.vn/viewtopic\\.php\\?t=(?'t'[0-9]+)!"
			],
			'iframe'	=> [
				'width'		=> 560,
				'height'	=> 260,
				'src'		=> 'http://localhost/vinabb/embed/topic/{@t}'
			]
		]);
	}

	/**
	* core.text_formatter_s9e_configure_after
	*
	* @param array $event Data from the PHP event
	*/
	public function text_formatter_s9e_configure_after($event)
	{
		$configurator = $event['configurator'];

		/**
		* Use backticks to post inline code: `$phpBB`
		*
		* https://github.com/s9e/phpbb-ext-incode
		* @copyright Copyright (c) 2015 The s9e Authors
		*/
		$action = $configurator->tags->onDuplicate('ignore');

		$configurator->Preg->replace('/`(.*?)`/', '<code class="inline">$1</code>', 'C');
		$configurator->tags->onDuplicate($action);

		// Remove old smiley data
		unset($configurator->Emoticons);

		// Set new smiley data with emoticon based on user language
		foreach ($this->cache->get_smilies() as $smiley_code => $smiley_data)
		{
			if ($this->language->is_set(['EMOTICON_TEXT', strtoupper($smiley_data['emotion'])]))
			{
				$emotion_text = '{$LE_' . strtoupper($smiley_data['emotion']) . '}';
			}
			else
			{
				$emotion_text = $smiley_data['emotion'];
			}

			$configurator->Emoticons->add($smiley_code, '<img class="smilies" src="{$T_SMILIES_PATH}/' . $smiley_data['url'] . '" width="' . $smiley_data['width'] . '" height="' . $smiley_data['height'] . '" alt="{.}" data-tooltip="true" title="' . $emotion_text . '"/>');
		}

		if (isset($configurator->Emoticons))
		{
			// Force emoticons to be rendered as text if $S_VIEWSMILIES is not set
			$configurator->Emoticons->notIfCondition = 'not($S_VIEWSMILIES)';

			// Only parse emoticons at the beginning of the text or if they're preceded by any
			// one of: a new line, a space, a dot, or a right square bracket
			$configurator->Emoticons->notAfter = '[^\\n .\\]]';
		}

		// Use EmojiOne
		$configurator->Emoji->useEmojiOne();
		$configurator->Emoji->setImageSize(16);
	}

	/**
	* Render MediaEmbed markup tags when displaying text
	*
	* https://github.com/s9e/phpbb-ext-mediaembed
	* @copyright Copyright (c) 2014-2016 The s9e Authors
	*
	* @param $text
	* @return mixed
	*/
	protected function render($text)
	{
		if (strpos($text, '<!-- s9e:mediaembed') === false)
		{
			return $text;
		}

		return preg_replace_callback(
			'(<!-- s9e:mediaembed:([^ ]+) --><!-- m -->.*?<!-- m -->)',
			function($m)
			{
				return MediaPack::render(base64_decode($m[1]));
			},
			$text
		);
	}

	/**
	* Insert MediaEmbed markup tags when saving text
	*
	* https://github.com/s9e/phpbb-ext-mediaembed
	* @copyright Copyright (c) 2014-2016 The s9e Authors
	*
	* @param $text
	* @return mixed
	*/
	protected function parse($text)
	{
		if (strpos($text, '<!-- m -->') === false)
		{
			return $text;
		}

		return preg_replace_callback(
			'(<!-- m -->.*?href="([^"]+).*?<!-- m -->)',
			function($m)
			{
				$xml = MediaPack::parse(htmlspecialchars_decode($m[1]));

				return ($xml[1] === 'r') ? '<!-- s9e:mediaembed:' . base64_encode($xml) . ' -->' . $m[0] : $m[0];
			},
			$text
		);
	}

	/**
	* Remove MediaEmbed markup tags when editing text
	*
	* https://github.com/s9e/phpbb-ext-mediaembed
	* @copyright Copyright (c) 2014-2016 The s9e Authors
	*
	* @param $text
	* @return mixed
	*/
	protected function unparse($text)
	{
		if (strpos($text, '<!-- s9e:mediaembed') === false)
		{
			return $text;
		}

		return preg_replace('(<!-- s9e:mediaembed:([^ ]+) -->)', '', $text);
	}
}
