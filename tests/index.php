<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/31
 * Time: 下午3:21
 */
include_once __DIR__ . '/../vendor/autoload.php';

\WTF\WTF::register([
    'php_error_log_dir' => __DIR__ . '/log',
    'is_debug'          => false
]);

echo 'hello';
include_once __DIR__ . '/error.php';
echo 'ok';