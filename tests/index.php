<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/31
 * Time: 下午3:21
 */
include_once __DIR__ . '/../vendor/autoload.php';

\BaAGee\Wtf\WtfError::register(new \BaAGee\Wtf\Handler\WtfHandler([
    'php_error_log_dir'     => __DIR__ . '/loga',
    'is_debug'              => false,
    'product_error_hidden'  => [E_WARNING, E_NOTICE, E_STRICT, E_DEPRECATED],# 非调试模式下隐藏那种错误类型
    'product_error_message' => 'What the fuck! Looks like something went wrong',# 默认非调试模式下错误提示
]));

echo 'hello';
include_once __DIR__ . '/error.php';
echo 'ok';