<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/31
 * Time: 下午3:21
 */
include_once __DIR__ . '/../vendor/autoload.php';

// 自定义错误处理

/**
 * Class MyHandler
 */
class MyHandler extends \BaAGee\Wtf\Base\WtfHandlerAbstract
{
    /**
     * @param Throwable $t
     */
    public function throwableHandler(\Throwable $t)
    {
        header('content-type: application/json; charset=utf-8');
        echo json_encode([
            'code'    => $t->getCode(),
            'message' => $t->getMessage(),
            'file'    => $t->getFile(),
            'line'    => $t->getLine(),
        ], JSON_UNESCAPED_UNICODE);
    }
}

\BaAGee\Wtf\WtfError::register(new MyHandler([
    'php_error_log_dir'    => __DIR__ . '/log',
    'is_debug'             => true,
    'product_error_hidden' => [E_WARNING, E_NOTICE, E_STRICT, E_DEPRECATED]
]));

echo 'hello';
include_once __DIR__ . '/error.php';
echo 'ok';