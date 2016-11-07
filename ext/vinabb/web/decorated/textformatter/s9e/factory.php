<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\decorated\textformatter\s9e;

use s9e\TextFormatter\Configurator;
use s9e\TextFormatter\Configurator\Items\AttributeFilters\RegexpFilter;
use s9e\TextFormatter\Configurator\Items\UnsafeTemplate;

/**
* Creates s9e\TextFormatter objects
*/
class factory extends \phpbb\textformatter\s9e\factory
{
	/** @var \phpbb\language\language */
	protected $language;

	/**
	* Constructor
	*
	* Copied from phpBB 3.2.0-RC1
	* Changes: Add the $language
	*
	* @param \phpbb\textformatter\data_access $data_access
	* @param \phpbb\cache\driver\driver_interface $cache
	* @param \phpbb\event\dispatcher_interface $dispatcher
	* @param \phpbb\config\config $config
	* @param \phpbb\language\language $language
	* @param \phpbb\textformatter\s9e\link_helper $link_helper
	* @param string $cache_dir          Path to the cache dir
	* @param string $cache_key_parser   Cache key used for the parser
	* @param string $cache_key_renderer Cache key used for the renderer
	*/
	public function __construct(
		\phpbb\textformatter\data_access $data_access,
		\phpbb\cache\driver\driver_interface $cache,
		\phpbb\event\dispatcher_interface $dispatcher,
		\phpbb\config\config $config,
		\phpbb\language\language $language,
		\phpbb\textformatter\s9e\link_helper $link_helper,
		$cache_dir,
		$cache_key_parser,
		$cache_key_renderer
	)
	{
		$this->data_access = $data_access;
		$this->cache = $cache;
		$this->dispatcher = $dispatcher;
		$this->config = $config;
		$this->language = $language;
		$this->link_helper = $link_helper;
		$this->cache_dir = $cache_dir;
		$this->cache_key_parser = $cache_key_parser;
		$this->cache_key_renderer = $cache_key_renderer;
	}

	/**
	* Generate and return a new configured instance of s9e\TextFormatter\Configurator
	*
	* Copied from phpBB 3.2.0-RC1
	* Changes: Add smiley width/height and set emoticon by language variables
	*
	* @return Configurator
	*/
	public function get_configurator()
	{
		// Create a new Configurator
		$configurator = new Configurator;

		/**
		* Modify the s9e\TextFormatter configurator before the default settings are set
		*
		* @event core.text_formatter_s9e_configure_before
		* @var \s9e\TextFormatter\Configurator configurator Configurator instance
		* @since 3.2.0-a1
		*/
		$vars = ['configurator'];
		extract($this->dispatcher->trigger_event('core.text_formatter_s9e_configure_before', compact($vars)));

		// Reset the list of allowed schemes
		foreach ($configurator->urlConfig->getAllowedSchemes() as $scheme)
		{
			$configurator->urlConfig->disallowScheme($scheme);
		}
		foreach (explode(',', $this->config['allowed_schemes_links']) as $scheme)
		{
			$configurator->urlConfig->allowScheme(trim($scheme));
		}

		// Convert newlines to br elements by default
		$configurator->rootRules->enableAutoLineBreaks();

		// Don't automatically ignore text in places where text is not allowed
		$configurator->rulesGenerator->remove('IgnoreTextIfDisallowed');

		// Don't remove comments and instead convert them to xsl:comment elements
		$configurator->templateNormalizer->remove('RemoveComments');
		$configurator->templateNormalizer->add('TransposeComments');

		// Set the rendering engine and configure it to save to the cache dir
		$configurator->rendering->engine = 'PHP';
		$configurator->rendering->engine->cacheDir = $this->cache_dir;
		$configurator->rendering->engine->defaultClassPrefix = 's9e_renderer_';
		$configurator->rendering->engine->enableQuickRenderer = true;

		// Create custom filters for BBCode tokens that are supported in phpBB but not in
		// s9e\TextFormatter
		$filter = new RegexpFilter('#^' . get_preg_expression('relative_url') . '$#Du');
		$configurator->attributeFilters->add('#local_url', $filter);
		$configurator->attributeFilters->add('#relative_url', $filter);

		// INTTEXT regexp from acp_bbcodes
		$filter = new RegexpFilter('!^([\p{L}\p{N}\-+,_. ]+)$!Du');
		$configurator->attributeFilters->add('#inttext', $filter);

		// Create custom filters for Flash restrictions, which use the same values as the image
		// restrictions but have their own error message
		$configurator->attributeFilters
			->add('#flashheight', 'phpbb\\textformatter\\s9e\\parser::filter_flash_height')
			->addParameterByName('max_img_height')
			->addParameterByName('logger');

		$configurator->attributeFilters
			->add('#flashwidth', 'phpbb\\textformatter\\s9e\\parser::filter_flash_width')
			->addParameterByName('max_img_width')
			->addParameterByName('logger');

		// Create a custom filter for phpBB's per-mode font size limits
		$configurator->attributeFilters
			->add('#fontsize', 'phpbb\\textformatter\\s9e\\parser::filter_font_size')
			->addParameterByName('max_font_size')
			->addParameterByName('logger')
			->markAsSafeInCSS();

		// Create a custom filter for image URLs
		$configurator->attributeFilters
			->add('#imageurl', 'phpbb\\textformatter\\s9e\\parser::filter_img_url')
			->addParameterByName('urlConfig')
			->addParameterByName('logger')
			->addParameterByName('max_img_height')
			->addParameterByName('max_img_width')
			->markAsSafeAsURL();

		// Add default BBCodes
		foreach ($this->get_default_bbcodes($configurator) as $bbcode)
		{
			$configurator->BBCodes->addCustom($bbcode['usage'], $bbcode['template']);
		}

		// Modify the template to disable images/flash depending on user's settings
		foreach (['FLASH', 'IMG'] as $name)
		{
			$tag = $configurator->tags[$name];
			$tag->template = '<xsl:choose><xsl:when test="$S_VIEW' . $name . '">' . $tag->template . '</xsl:when><xsl:otherwise><xsl:apply-templates/></xsl:otherwise></xsl:choose>';
		}

		// Load custom BBCodes
		foreach ($this->data_access->get_bbcodes() as $row)
		{
			// Insert the board's URL before {LOCAL_URL} tokens
			$tpl = preg_replace_callback(
				'#\\{LOCAL_URL\\d*\\}#',
				function($m)
				{
					return generate_board_url() . '/' . $m[0];
				},
				$row['bbcode_tpl']
			);

			try
			{
				$configurator->BBCodes->addCustom($row['bbcode_match'], new UnsafeTemplate($tpl));
			}
			catch (\Exception $e)
			{
			}
		}

		// Load smilies
		foreach ($this->data_access->get_smilies() as $row)
		{
			if ($this->language->is_set(['EMOTICON_TEXT', strtoupper($row['emotion'])]))
			{
				$emotion_text = '{$LE_' . strtoupper($row['emotion']) . '}';
			}
			else
			{
				$emotion_text = $row['emotion'];
			}

			$configurator->Emoticons->add(
				$row['code'],
				'<img class="smilies" src="{$T_SMILIES_PATH}/' . $row['smiley_url'] . '" width="' . $row['smiley_width'] . '" height="' . $row['smiley_height'] . '" alt="{.}" title="' . $emotion_text . '"/>'
			);
		}

		if (isset($configurator->Emoticons))
		{
			// Force emoticons to be rendered as text if $S_VIEWSMILIES is not set
			$configurator->Emoticons->notIfCondition = 'not($S_VIEWSMILIES)';

			// Only parse emoticons at the beginning of the text or if they're preceded by any
			// one of: a new line, a space, a dot, or a right square bracket
			$configurator->Emoticons->notAfter = '[^\\n .\\]]';
		}

		// Load the censored words
		$censor = $this->data_access->get_censored_words();
		if (!empty($censor))
		{
			// Use a namespaced tag to avoid collisions
			$configurator->plugins->load('Censor', ['tagName' => 'censor:tag']);
			foreach ($censor as $row)
			{
				// NOTE: words are stored as HTML, we need to decode them to plain text
				$configurator->Censor->add(htmlspecialchars_decode($row['word']), htmlspecialchars_decode($row['replacement']));
			}
		}

		// Load the magic links plugins. We do that after BBCodes so that they use the same tags
		$this->configure_autolink($configurator);

		// Register some vars with a default value. Those should be set at runtime by whatever calls
		// the parser
		$configurator->registeredVars['max_font_size'] = 0;
		$configurator->registeredVars['max_img_height'] = 0;
		$configurator->registeredVars['max_img_width'] = 0;

		// Load the Emoji plugin and modify its tag's template to obey viewsmilies
		$configurator->Emoji->setImageSize(18);
		$tag = $configurator->Emoji->getTag();
		$tag->template = '<xsl:choose><xsl:when test="$S_VIEWSMILIES">' . str_replace('class="emoji"', 'class="smilies"', $tag->template) . '</xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose>';

		/**
		* Modify the s9e\TextFormatter configurator after the default settings are set
		*
		* @event core.text_formatter_s9e_configure_after
		* @var \s9e\TextFormatter\Configurator configurator Configurator instance
		* @since 3.2.0-a1
		*/
		$vars = ['configurator'];
		extract($this->dispatcher->trigger_event('core.text_formatter_s9e_configure_after', compact($vars)));

		return $configurator;
	}
}
