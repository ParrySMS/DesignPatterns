<?php
/**
 * Created by PhpStorm.
 * User: L
 * Date: 2019-8-24
 * Time: 16:11
 */
define('FILE_PATH', './');
define('FILE_NAME_ORI', 'README.md');
define('FILE_NAME_NEW', 'README_NO_CODE.md');
define('RM_START_TAG1', '```php');
define('RM_START_TAG2', '```PHP');
define('RM_END_TAG', '```');

$original_file = fopen(FILE_PATH . FILE_NAME_ORI, 'r+');
$new_file = fopen(FILE_PATH . FILE_NAME_NEW, 'w');

for ($item_line = fgets($original_file), $need_write = true; !feof($original_file); $item_line = fgets($original_file)) {

    if (strstr($item_line, RM_START_TAG1)
        || strstr($item_line, RM_START_TAG2)) {
        $need_write = false;
        continue;
    }

    if (strstr($item_line, RM_END_TAG)) {
        if (!$need_write) {
            $need_write = true;
            continue;
        } else {
            $need_write = true;
        }
    }

    if ($need_write) fwrite($new_file, $item_line);
}

fclose($original_file);
fclose($new_file);
echo 'done!';