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
    /**
     * @var string
     */
    protected $errorType = '';
    /**
     * @var array 配置
     */
    protected $conf = [
        'is_debug'             => true,#是否为调试模式
        'php_error_log_dir'    => '',#php error log路径不为空就调用写Log方法
        'product_error_hidden' => [E_WARNING, E_NOTICE, E_STRICT, E_DEPRECATED],# 非调试模式下隐藏哪种PHP错误类型
        'dev_error_hidden'     => [E_WARNING, E_NOTICE, E_STRICT, E_DEPRECATED],# 调试开发模式下隐藏哪种PHP错误类型
    ];
    /**
     * 错误码
     */
    protected const ERROR_TYPE_ARRAY = [
        E_ERROR             => 'PHP Fatal Error',
        E_WARNING           => 'PHP Warning',//
        E_PARSE             => 'PHP Parse Error',
        E_NOTICE            => 'PHP Notice',//
        E_CORE_ERROR        => 'PHP Core Error',
        E_CORE_WARNING      => 'PHP Core Warning',
        E_COMPILE_ERROR     => 'PHP Compile Error',
        E_COMPILE_WARNING   => 'PHP Compile Warning',
        E_USER_ERROR        => 'PHP User Error',
        E_USER_WARNING      => 'PHP User Warning',
        E_USER_NOTICE       => 'PHP User Notice',
        E_STRICT            => 'PHP Strict',//
        E_RECOVERABLE_ERROR => 'PHP Recoverable Error',//
        E_DEPRECATED        => 'PHP Deprecated',//
        E_USER_DEPRECATED   => 'PHP User Deprecated'
    ];

    /**
     * WtfHandlerAbstract constructor.
     * @param array $config
     */
    final public function __construct(array $config = [])
    {
        ob_start();
        if (!empty($config)) {
            $this->conf = array_merge($this->conf, $config);
        }
    }

    /**
     * @param int    $err_no
     * @param string $err_msg
     * @param string $err_file
     * @param int    $err_line
     * @return bool
     * @throws \ErrorException
     */
    final public function catchError(int $err_no, string $err_msg, string $err_file, int $err_line)
    {
        if ($this->checkAbleHidden($err_no)) {
            return true;
        }
        throw new \ErrorException($err_msg, $err_no, 1, $err_file, $err_line, new \Error($err_msg, $err_no));
    }

    /**
     * @param $code
     * @return bool
     */
    protected function checkAbleHidden($code)
    {
        if ($this->conf['is_debug']) {
            return in_array($code, $this->conf['dev_error_hidden']);
        } else {
            return in_array($code, $this->conf['product_error_hidden']);
        }
    }

    /**
     * @param \Throwable $t
     * @return mixed|string
     */
    private function getErrorType(\Throwable &$t)
    {
        if (($t instanceof \Error) || ($t instanceof \ErrorException)) {
            if ($t instanceof \Error) {
                if (isset(self::ERROR_TYPE_ARRAY[$t->getCode()])) {
                    $type = self::ERROR_TYPE_ARRAY[$t->getCode()];
                }
            } else {
                if ($prev = $t->getPrevious()) {
                    if ($prev instanceof \Error) {
                        $type = self::ERROR_TYPE_ARRAY[$prev->getCode()];
                    }
                }
            }
        } else {
            $arr = preg_split("/(?=[A-Z])/", get_class($t));
            if (isset($arr[1])) {
                if ($arr[1] == \Error::class) {
                    $type = 'PHP Fatal error';
                } else if ($arr[1] == 'Parse') {
                    $type = 'PHP Parse error';
                }
            }
        }
        if (!isset($type)) {
            $type = get_class($t);
        }
        return $type;
    }

    /**
     * 异常/错误处理
     * @param \Throwable $t
     * @return bool
     */
    final public function catchThrowable(\Throwable $t)
    {
        $this->errorType = $this->getErrorType($t);
        if (!empty($this->conf['php_error_log_dir'])) {
            $this->writePhpErrorLog($t);
        }
        if ($t instanceof \ErrorException) {
            if ($prev = $t->getPrevious()) {
                if ($prev instanceof \Error) {
                    if ($this->checkAbleHidden($t->getCode())) {
                        // 来自catchError的错误
                        return true;
                    }
                }
            }
        }
        ob_clean();
        $this->throwableHandler($t);
        ob_end_flush();
        die;
    }

    public function writePhpErrorLog(\Throwable $t)
    {

    }

    /**
     * @param \Throwable $t
     * @return mixed
     */
    abstract public function throwableHandler(\Throwable $t);
}
