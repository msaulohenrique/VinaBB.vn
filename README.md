# VinaBB.vn
Source code on VinaBB.vn without private bits.

[![Build Status](https://travis-ci.org/VinaBB/VinaBB.vn.svg?branch=master)](https://travis-ci.org/VinaBB/VinaBB.vn)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/VinaBB/VinaBB.vn/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/VinaBB/VinaBB.vn/?branch=master)
[![Code Climate](https://codeclimate.com/github/VinaBB/VinaBB.vn/badges/gpa.svg)](https://codeclimate.com/github/VinaBB/VinaBB.vn)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/791226a3-5228-429d-9f3a-20f9a9404b7b/mini.png)](https://insight.sensiolabs.com/projects/791226a3-5228-429d-9f3a-20f9a9404b7b)

## Quick Note
* Install the extension "vinabb/web".
* Copy and replace front files:
  * Required: `index.php`
  * Optional (For cached results from Google Search):
`viewforum.php` `viewtopic.php`
* Install Vietnamese language pack from [phpBB iVN](https://github.com/VinaBB/phpBB.iVN).
* `style.min.css`
  * Purchase a copy of the template [Float](https://themeforest.net/item/float/17838778).
  * Copy all files from `styles/vinabb/theme/contrib/scss/*.scss` to the directory `scss` of Float.
  * Open `style.scss`, add at the end:
```css
@import '_plugins.scss';
@import '_ribbon.scss';
@import '_custom.scss';
```
  * Compile `style.scss` to `style.css`, then minify it: `style.min.css`.
  * Copy `style.min.css` to `styles/vinabb/theme/css/`.
* Install the style "VinaBB".
* Have done 😊

## Patch minified JS files (When update a newer version)
* `highlight.min.js`
  * Find `template_from_string`, add to range `lang`.
    * `{{ lang('ABC') }}`
  * Find `number_format`, add to range `e`.
    * `{{ ABC|e('js') }}`
  * Find `verbatim`, add to range `EVENT`.
    * `{% EVENT abc %}`
* `jquery.sceditor.bbcode.min.js`
  * Allow to override the English translation.
    * Find and remove `&&"en"!==c.locale`.
  * Set emoticon width/height to 16x16 pixels.
    * Find `data-sceditor-emoticon="{key}"`, in-line add after ` width="16" height="16"`.
    * Find `src:d.url||d,`, in-line add after `width:16,height:16,`.
