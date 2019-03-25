<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/31
 * Time: 下午3:21
 */
include_once __DIR__ . '/../vendor/autoload.php';

\BaAGee\Wtf\WtfError::register(new \BaAGee\Wtf\Handler\WtfHandler([
    // 以下三个参数内置 \BaAGee\Wtf\Handler\WtfHandler
    'php_error_log_dir'     => __DIR__ . '/log',
    'is_debug'              => true,
    'product_error_hidden'  => [E_WARNING, E_NOTICE, E_STRICT, E_DEPRECATED],# 非调试模式下隐藏那种错误类型
    // 其他的参数名在使用自定义WtfHandler时自己随便定义
    'product_error_message' => 'What the fuck! Looks like something went wrong',# 内置WtfHandler默认非调试模式下错误提示
]));

echo 'hello';
include_once __DIR__ . '/error.php';
echo 'ok';