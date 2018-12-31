<?php
/**
 * Desc: 注册错误异常处理
 * User: baagee
 * Date: 2018/12/31
 * Time: 下午2:50
 */

namespace WTF;

trait WTFRegister
{
    public static function register(array $conf = [])
    {
        self::$conf = array_merge(self::$conf, $conf);
        set_error_handler(function ($err_no, $err_msg, $err_file, $err_line) {
            self::catchError($err_no, $err_msg, $err_file, $err_line);
        });
        set_exception_handler(function ($exception) {
            self::catchException($exception);
        });
    }
}