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
    protected $errorType = '';
    /**
     * @var array 配置
     */
    protected $conf = [
        'is_debug'             => true,#是否为调试模式
        'php_error_log_dir'    => '',#php error log路径
        'product_error_hidden' => [E_WARNING, E_NOTICE, E_STRICT, E_DEPRECATED],# 非调试模式下隐藏那种错误类型
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

    final public function __construct(array $config = [])
    {
        ob_start();
        if (!empty($config)) {
            $this->conf = array_merge($this->conf, $config);
        }
    }

    final public function catchError(int $err_no, string $err_msg, string $err_file, int $err_line)
    {
        if ($this->checkAbleHidden($err_no)) {
            return true;
        }
        throw new \ErrorException($err_msg, $err_no, 1, $err_file, $err_line);
    }

    protected function checkAbleHidden($code)
    {
        if (!$this->conf['is_debug'] && in_array($code, $this->conf['product_error_hidden'])) {
            // echo '不显示' . $t->getMessage() . PHP_EOL;
            return true;
        } else {
            return false;
        }
    }

    private function getErrorType(\Throwable $t)
    {
        if (isset(self::ERROR_TYPE_ARRAY[$t->getCode()])) {
            return self::ERROR_TYPE_ARRAY[$t->getCode()];
        } else {
            $arr = preg_split("/(?=[A-Z])/", get_class($t));
            if (isset($arr[1])) {
                if ($arr[1] == \Error::class) {
                    $type = 'PHP Fatal error';
                } else if ($arr[1] == 'Parse') {
                    $type = 'PHP Parse error';
                }
            }
            if (!isset($type)) {
                $type = get_class($t);
            }
            // echo "DEBUG====== type=" . $type . PHP_EOL;
            return $type;
        }
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
            $log_str = sprintf('[%s] %s: %s ' . PHP_EOL . 'File:%s:%d' . PHP_EOL . '%s' . PHP_EOL, date('Y-m-d H:i:s'), $this->errorType, $t->getMessage(), $t->getFile(), $t->getLine(), $t->getTraceAsString());
            if (!is_dir($this->conf['php_error_log_dir'])) {
                exec('mkdir -p ' . $this->conf['php_error_log_dir']);
            }
            error_log($log_str, 3, $this->conf['php_error_log_dir'] . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log');
        }
        if ($this->checkAbleHidden($t->getCode())) {
            return true;
        }
        ob_clean();
        $this->throwableHandler($t);
        ob_end_flush();
        die;
    }

    abstract public function throwableHandler(\Throwable $t);
}
