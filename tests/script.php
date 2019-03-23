<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/31
 * Time: 下午3:37
 */
include_once __DIR__ . '/../vendor/autoload.php';

// 脚本测试

\BaAGee\Wtf\WtfError::register(new \BaAGee\Wtf\Handler\WtfHandler([
    'php_error_log_dir' => __DIR__ . '/log',//指定PHP错误log目录，为空 不记录
    'is_debug'          => true
]));

echo 'hello' . PHP_EOL;
include_once __DIR__ . '/error.php';
echo 'ok' . PHP_EOL;