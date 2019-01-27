<?php
/**
 * Desc: 异常
 * User: baagee
 * Date: 2018/12/31
 * Time: 下午2:54
 */

namespace WTF;
/**
 * Trait ThrowableHandler
 * @package WTF
 */
trait ThrowableHandler
{
    /**
     * @param \Throwable $t
     */
    protected static function catchThrowable(\Throwable $t)
    {
        self::$error_msg  = $t->getMessage();
        self::$error_code = $t->getCode();
        if (self::$error_line == '' || self::$error_line == 0) {
            //处理  存在没有设置出错文件的情况
            self::$error_file = $t->getFile();
            self::$error_line = $t->getLine();
        }
        if ($t instanceof \Error) {
            // php代码错误 比如notice warning,error，语法错误等
            if (empty(self::$error_type)) {
                // 获取其他的错误类型 比如调用不存在的方法 或者语法错误  不经过ErrorHandler，直接进入这里 没有type，需要通过class判断
                if (strtolower(get_class($t)) == 'parseerror') {
                    self::$error_type = self::ERROR_TYPE_ARRAY[E_PARSE];
                    self::$error_code = E_PARSE;
                } else {
                    self::$error_type = self::ERROR_TYPE_ARRAY[E_ERROR];
                    self::$error_code = E_ERROR;
                }
            }
            // 记录log
            self::recordError();
            if (self::$conf['is_debug'] == false) {
                if (in_array(self::$error_code, self::$conf['product_error_hidden'])) {
                    return true;
                }
            }
        }
        if (empty(self::$error_type)) {
            self::$error_type = get_class($t);
        }
        $backtrace       = $t->getTrace();
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
        self::getPhpCode();
        self::show();
    }
}