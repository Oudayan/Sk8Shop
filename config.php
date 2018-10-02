<?php
// HTTP
define('HTTP_SERVER', 'http://e9134268.webdev.cmaisonneuve.qc.ca/Sk8Shop/');

// HTTPS
define('HTTPS_SERVER', 'http://e9134268.webdev.cmaisonneuve.qc.ca/Sk8Shop/');

// DIR
define('DIR_APPLICATION', '/var/www/vhosts/e9134268/www/Sk8Shop/catalog/');
define('DIR_SYSTEM', '/var/www/vhosts/e9134268/www/Sk8Shop/system/');
define('DIR_IMAGE', '/var/www/vhosts/e9134268/www/Sk8Shop/image/');
define('DIR_STORAGE', '/var/www/vhosts/e9134268/www/SWCE-TP2/storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mpdo');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'e9134268');
define('DB_PASSWORD', 'Sk80rd1e');
define('DB_DATABASE', 'e9134268');
define('DB_PORT', '3306');
define('DB_PREFIX', 'sk8_');