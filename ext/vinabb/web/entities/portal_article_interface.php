<?php
/**
* This file is part of the VinaBB.vn package.
*
* @copyright (c) VinaBB <vinabb.vn>
* @license GNU General Public License, version 2 (GPL-2.0)
*/

namespace vinabb\web\entities;

/**
* Interface for a single article
*/
interface portal_article_interface
{
	/**
	* Load the data from the database for an entity
	*
	* @param int						$id		Article ID
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function load($id);

	/**
	* Import data for an entity
	*
	* Used when the data is already loaded externally.
	* Any existing data on this entity is over-written.
	* All data is validated and an exception is thrown if any data is invalid.
	*
	* @param array						$data	Data array from the database
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\base
	*/
	public function import($data);

	/**
	* Insert the entity for the first time
	*
	* Will throw an exception if the entity was already inserted (call save() instead)
	*
	* @return portal_article_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function insert();

	/**
	* Save the current settings to the database
	*
	* This must be called before closing or any changes will not be saved!
	* If adding an entity (saving for the first time), you must call insert() or an exception will be thrown
	*
	* @return portal_article_interface $this Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\out_of_bounds
	*/
	public function save();

	/**
	* Get the article_id
	*
	* @return int
	*/
	public function get_id();

	/**
	* Get the category ID
	*
	* @return int
	*/
	public function get_cat_id();

	/**
	* Set the category ID
	*
	* @param int						$id		Category ID
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_cat_id($id);

	/**
	* Get the article name
	*
	* @return string
	*/
	public function get_name();

	/**
	* Set the article name
	*
	* @param string						$text	Article name
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name($text);

	/**
	* Get the article SEO name
	*
	* @return string
	*/
	public function get_name_seo();

	/**
	* Set the article SEO name
	*
	* @param string						$text	Article SEO name
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_name_seo($text);

	/**
	* Get the article language
	*
	* @return string
	*/
	public function get_lang();

	/**
	* Set the article language
	*
	* @param string						$text	2-letter language ISO code
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_lang($text);

	/**
	* Get the article main image
	*
	* @return string
	*/
	public function get_img();

	/**
	* Set the article main image
	*
	* @param string						$text	Article image
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_img($text);

	/**
	* Get the article description
	*
	* @return string
	*/
	public function get_desc();

	/**
	* Set the article description
	*
	* @param string						$text	Article description
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	* @throws \vinabb\web\exceptions\unexpected_value
	*/
	public function set_desc($text);

	/**
	* Get article content for edit
	*
	* @return string
	*/
	public function get_text_for_edit();

	/**
	* Get article content for display
	*
	* @param bool $censor True to censor the text
	* @return string
	*/
	public function get_text_for_display($censor = true);

	/**
	* Set article content
	*
	* @param string						$text	Article content
	* @return portal_article_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_text($text);

	/**
	* Check if BBCode is enabled on the article content
	*
	* @return bool
	*/
	public function text_bbcode_enabled();

	/**
	* Enable BBCode on the article content
	* This should be called before set_text(); text_enable_bbcode()->set_text()
	*
	* @return portal_article_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_bbcode();

	/**
	* Disable BBCode on the article content
	* This should be called before set_text(); text_disable_bbcode()->set_text()
	*
	* @return portal_article_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_disable_bbcode();

	/**
	* Check if URLs is enabled on the article content
	*
	* @return bool
	*/
	public function text_urls_enabled();

	/**
	* Enable URLs on the article content
	* This should be called before set_text(); text_enable_urls()->set_text()
	*
	* @return portal_article_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_urls();

	/**
	* Disable URLs on the article content
	* This should be called before set_text(); text_disable_urls()->set_text()
	*
	* @return portal_article_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_disable_urls();

	/**
	* Check if smilies are enabled on the article content
	*
	* @return bool
	*/
	public function text_smilies_enabled();

	/**
	* Enable smilies on the article content
	* This should be called before set_text(); text_enable_smilies()->set_text()
	*
	* @return portal_article_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_enable_smilies();

	/**
	* Disable smilies on the article content
	* This should be called before set_text(); text_disable_smilies()->set_text()
	*
	* @return portal_article_interface $this Object for chaining calls: load()->set()->save()
	*/
	public function text_disable_smilies();

	/**
	* Get article display setting
	*
	* @return bool
	*/
	public function get_enable();

	/**
	* Set article display setting
	*
	* @param bool				$value	Article display setting
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_enable($value);

	/**
	* Get the article views
	*
	* @return bool
	*/
	public function get_views();

	/**
	* Get the article time
	*
	* @return bool
	*/
	public function get_time();

	/**
	* Set the article time
	*
	* @return page_interface	$this	Object for chaining calls: load()->set()->save()
	*/
	public function set_time();
}
