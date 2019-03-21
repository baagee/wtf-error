<?php
/**
 * Desc: 抽象错误异常处理
 * User: baagee
 * Date: 2019/3/21
 * Time: 下午10:52
 */

namespace BaAGee\Wtf\Base;

/**
 * Class WtfHandlerAbstract
 * @package BaAGee\Wtf\Base
 */
abstract class WtfHandlerAbstract
{
    use ProhibitNewClone;
    /**
     * 错误码
     */
    protected const ERROR_TYPE_ARRAY = array(
        E_ERROR             => 'Fatal Error',
        E_WARNING           => 'Warning',//
        E_PARSE             => 'Parse Error',
        E_NOTICE            => 'Notice',//
        E_CORE_ERROR        => 'Core Error',
        E_CORE_WARNING      => 'Core Warning',
        E_COMPILE_ERROR     => 'Compile Error',
        E_COMPILE_WARNING   => 'Compile Warning',
        E_USER_ERROR        => 'User Error',
        E_USER_WARNING      => 'User Warning',
        E_USER_NOTICE       => 'User Notice',
        E_STRICT            => 'Strict',//
        E_RECOVERABLE_ERROR => 'Recoverable Error',//
        E_DEPRECATED        => 'Deprecated',//
        E_USER_DEPRECATED   => 'User Deprecated'
    );

    /**
     * 错误处理
     * @param int    $err_no
     * @param string $err_msg
     * @param string $err_file
     * @param int    $err_line
     * @return mixed
     */
    abstract public static function catchError(int $err_no, string $err_msg, string $err_file, int $err_line);

    /**
     * 异常/错误处理
     * @param \Throwable $t
     * @return mixed
     */
    abstract public static function catchThrowable(\Throwable $t);
}
