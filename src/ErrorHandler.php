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
     * @return bool
     * @throws \ErrorException
     */
    public static function catchError($err_no, $err_msg, $err_file, $err_line)
    {
        self::$type = self::getErrorLevel($err_no);
        // 记录错误信息
        self::recordError($err_file, $err_line, $err_msg);
        if (self::$conf['is_debug'] == false) {
            if (in_array($err_no, [E_WARNING, E_NOTICE, E_STRICT, E_DEPRECATED])) {
                return true;
            }
        }
        throw new \ErrorException($err_msg, $err_no, 0, $err_file, $err_line);
    }

    /**
     * 获取错误类型
     * @param $err_no
     * @return string
     */
    protected static function getErrorLevel($err_no)
    {
        switch ($err_no) {
            case E_WARNING:
                $level_tips = 'PHP Warning';
                break;
            case E_NOTICE:
                $level_tips = 'PHP Notice';
                break;
            case E_DEPRECATED:
                $level_tips = 'PHP Deprecated';
                break;
            case E_USER_ERROR:
                $level_tips = 'User Error';
                break;
            case E_USER_WARNING:
                $level_tips = 'User Warning';
                break;
            case E_USER_NOTICE:
                $level_tips = 'User Notice';
                break;
            case E_USER_DEPRECATED:
                $level_tips = 'User Deprecated';
                break;
            case E_STRICT:
                $level_tips = 'PHP Strict';
                break;
            default:
                $level_tips = 'Unknow Type Error';
                break;
        }
        return $level_tips;
    }

    /**
     * 记录php错误信息
     * @param string $err_file 出错文件
     * @param int    $err_line 出错行数
     * @param string $err_msg  出错信息
     */
    protected static function recordError($err_file, $err_line, $err_msg)
    {
        if (!empty(self::$conf['php_error_log_dir'])) {
            $path             = self::$conf['php_error_log_dir'];
            $php_err_log_file = $path . '/' . date('Y-m-d-H') . '.log';
            if (!is_dir($path)) {
                exec('mkdir -p ' . $path);
            }
            // [time] [type] [file:line] [errpr_message]
            $log_file = sprintf('[%s] [%s] [%s:%d] %s' . PHP_EOL,
                date('Y-m-d H:i:s'), self::$type, $err_file, $err_line, $err_msg);
            error_log($log_file, 3, $php_err_log_file);
        }
    }
}
