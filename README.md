# VinaBB.vn
Source code on VinaBB.vn without private bits.

[![Build Status](https://travis-ci.org/VinaBB/VinaBB.vn.svg?branch=master)](https://travis-ci.org/VinaBB/VinaBB.vn)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/VinaBB/VinaBB.vn/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/VinaBB/VinaBB.vn/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/791226a3-5228-429d-9f3a-20f9a9404b7b/mini.png)](https://insight.sensiolabs.com/projects/791226a3-5228-429d-9f3a-20f9a9404b7b)

## Quick Note
* Install the extension "vinabb/web".
* Copy and replace front files:
```
faq.php
feed.php
index.php
mcp.php
memberlist.php
posting.php
report.php
search.php
ucp.php
viewforum.php
viewonline.php
viewtopic.php
```
* Install Vietnamese language pack from [phpBB iVN](https://github.com/VinaBB/phpBB.iVN).
* `style.min.css`
  * Purchase a copy of the template [Float](https://themeforest.net/item/float/17838778).
  * Copy `styles/vinabb/theme/contrib/scss/_custon.scss` to the directory `scss` of Float.
  * Open `style.scss`, add at the end `@import '_custom.scss';`.
  * Compile `style.scss` to `style.css`, then minify it: `style.min.css`.
  * Copy `style.min.css` to `styles/vinabb/theme/css/`.
* Install the style "VinaBB".
* Have done 😊
