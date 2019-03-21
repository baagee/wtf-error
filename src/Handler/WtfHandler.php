<?php
/**
 * Desc: 具体的错误一场处理逻辑
 * User: baagee
 * Date: 2019/3/21
 * Time: 下午10:53
 */

namespace BaAGee\Wtf\Handler;

use BaAGee\Wtf\Base\WtfHandlerAbstract;

class WtfHandler extends WtfHandlerAbstract
{
    public static function catchError(int $err_no, string $err_msg, string $err_file, int $err_line)
    {
        var_dump($err_msg);
    }

    public static function catchThrowable(\Throwable $t)
    {
        var_dump($t);
    }
}