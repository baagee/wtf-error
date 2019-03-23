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

/**
 * Class WtfError
 * @package BaAGee\Wtf
 */
class WtfError
{
    use ProhibitNewClone;

    /*
     * 注册
     */
    final public static function register(WtfHandlerAbstract $wtf)
    {
        error_reporting(0);
        if (!is_subclass_of($wtf, WtfHandlerAbstract::class)) {
            throw new \Exception(sprintf('[%s]没有继承[%s]', get_class($wtf), WtfHandlerAbstract::class));
        } else {
            set_error_handler(function ($err_no, $err_msg, $err_file, $err_line) use ($wtf) {
                return call_user_func_array([$wtf, 'catchError'], [$err_no, $err_msg, $err_file, $err_line]);
            });
            set_exception_handler(function ($t) use ($wtf) {
                return call_user_func([$wtf, 'catchThrowable'], $t);
            });
        }
    }
}
