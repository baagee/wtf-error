<?php
/**
 * Desc: Wtf错误异常处理
 * User: baagee
 * Date: 2019/3/21
 * Time: 下午10:45
 */

namespace BaAGee\Wtf;

use BaAGee\Wtf\Base\ProhibitNewClone;
use BaAGee\Wtf\Base\WtfHandlerAbstract;
use BaAGee\Wtf\Handler\WtfHandler;

/**
 * Class WtfError
 * @package BaAGee\Wtf
 */
class WtfError
{
    use ProhibitNewClone;

    /**
     * @var array 配置
     */
    protected static $conf = [
        'is_debug'              => true,#是否为调试模式
        'php_error_log_dir'     => '',#php error log路径
        'product_error_hidden'  => [E_WARNING, E_NOTICE, E_STRICT, E_DEPRECATED],# 非调试模式下隐藏那种错误类型
        'product_error_message' => 'What the fuck! Looks like something went wrong',# 默认非调试模式下错误提示
        'wtf_handler'           => WtfHandler::class,// 错误异常具体处理
    ];

    /**
     * 注册
     * @param array $conf
     * @throws \Exception
     */
    final public static function register(array $conf = [])
    {
        error_reporting(0);
        self::$conf = array_merge(self::$conf, $conf);
        if (!is_subclass_of(self::$conf['wtf_handler'], WtfHandlerAbstract::class)) {
            throw new \Exception(sprintf('[%s]没有继承[%s]', self::$conf['wtf_handler'], WtfHandlerAbstract::class));
        } else {
            set_error_handler(function ($err_no, $err_msg, $err_file, $err_line) {
                self::$conf['wtf_handler']::catchError($err_no, $err_msg, $err_file, $err_line);
            });
            set_exception_handler(function ($t) {
                self::$conf['wtf_handler']::catchThrowable($t);
            });
        }
    }
}
