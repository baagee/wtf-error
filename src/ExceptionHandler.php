<?php
/**
 * Desc: 异常
 * User: baagee
 * Date: 2018/12/31
 * Time: 下午2:54
 */

namespace WTF;
/**
 * Trait ExceptionHandler
 * @package WTF
 */
trait ExceptionHandler
{
    /**
     * @param \Throwable $exception
     */
    public static function catchException(\Throwable $exception)
    {
        if ($exception instanceof \ErrorException) {
        } else {
            self::$type = get_class($exception);
        }
        $backtrace      = $exception->getTrace();
        self::$err_line = $exception->getLine();
        array_unshift($backtrace, array('file' => $exception->getFile(), 'line' => self::$err_line, 'function' => 'break'));
        self::$backtrace = [];
        foreach ($backtrace as $error) {
            if (!empty($error['function'])) {
                $fun = '';
                if (!empty($error['class'])) {
                    $fun .= $error['class'] . $error['type'];
                }
                $fun               .= $error['function'] . '([args])';
                $error['function'] = $fun;
            }
            if (!isset($error['line'])) {
                continue;
            }
            if (!empty($error['file']) && !empty($error['line'])) {
                self::$backtrace[] = [
                    'file'     => $error['file'],
                    'line'     => $error['line'],
                    'function' => $error['function']];
            }
        }
        self::$err_msg = '[' . $exception->getCode() . '] ' . $exception->getMessage();
        self::getPhpCode();
        self::show();
    }
}