<?php
/**
 * Desc: 错误
 * User: baagee
 * Date: 2018/9/6
 * Time: 下午3:24
 */

namespace WTF;

/**
 * Trait ErrorHandler
 * @package WTF
 */
trait ErrorHandler
{
    /**
     * 错误处理函数
     * @param int    $err_no   错误码
     * @param string $err_msg  错误信息
     * @param string $err_file 错误文件
     * @param int    $err_line 错误行
     */
    protected static function catchError($err_no, $err_msg, $err_file, $err_line)
    {
        self::$error_type = self::ERROR_TYPE_ARRAY[$err_no];
        self::$error_file = $err_file;
        self::$error_line = $err_line;
        //抛出错误 进入ThrowableHandler处理
        throw new \Error($err_msg, $err_no);
    }
}
