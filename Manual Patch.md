### ./includes/functions_posting.php

FIND
```php
$pagination = $phpbb_container->get('pagination');
```
AFTER ADD
```php
$language = $phpbb_container->get('language');
```

FIND
```php
'SMILEY_DESC'	=> $row['emotion'])
```

REPLACE WITH
```php
'SMILEY_DESC'	=> ($language->is_set(['EMOTICON_TEXT', strtoupper($row['emotion'])])) ? $language->lang(['EMOTICON_TEXT', strtoupper($row['emotion'])]) : $row['emotion'])
```
