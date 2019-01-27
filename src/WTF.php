<?php
/**
 * Desc: WTF Error & Exception
 * User: baagee
 * Date: 2018/12/31
 * Time: 下午2:56
 */

namespace WTF;

/**
 * Class WTF
 * @package WTF
 */
class WTF
{
    /**
     * @var array 存放代码回溯信息
     */
    protected static $backtrace = [];

    /**
     * @var string 错误类型
     */
    protected static $error_type = '';

    /**
     * @var string 错误信息
     */
    protected static $error_msg = '';

    /**
     * @var int 错误码
     */
    protected static $error_code = 0;

    /**
     * @var int 错误行
     */
    protected static $error_line = 0;

    /**
     * @var string 错误文件
     */
    protected static $error_file = '';

    /**
     * @var array 保存错误的php代码
     */
    protected static $error_php_source_code = [];

    /**
     * @var array 配置
     */
    protected static $conf = [
        'is_debug'              => true,#是否为调试模式
        'php_error_log_dir'     => '',#php error log路径
        'product_error_hidden'  => [E_WARNING, E_NOTICE, E_STRICT, E_DEPRECATED],# 非调试模式下隐藏那种错误类型
        'product_error_message' => 'What the fuck! Looks like something went wrong',# 默认非调试模式下错误提示
    ];

    /**
     * 错误码
     */
    private const ERROR_TYPE_ARRAY = array(
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

    use ThrowableHandler, ErrorHandler;

    /**
     * 注册函数
     * @param array $conf
     */
    public static function register(array $conf = [])
    {
        error_reporting(0);
        self::$conf = array_merge(self::$conf, $conf);
        set_error_handler(function ($err_no, $err_msg, $err_file, $err_line) {
            self::catchError($err_no, $err_msg, $err_file, $err_line);
        });
        set_exception_handler(function ($exception) {
            self::catchThrowable($exception);
        });
    }

    /**
     * 展示错误信息界面
     */
    protected static function show()
    {
        $error_msg             = self::$error_msg;
        $error_type            = self::$error_type;
        $backtrace             = self::$backtrace;
        $error_php_source_code = self::$error_php_source_code;
        $error_file            = self::$error_file;
        $error_line            = self::$error_line;
        $error_code            = self::$error_code;
        if (strtolower(php_sapi_name()) !== 'cli') {
            // 页面模式
            $is_debug    = self::$conf['is_debug'];
            $environment = array_merge([
                'GET'   => json_encode($_GET, JSON_UNESCAPED_UNICODE),
                'POST'  => json_encode($_POST, JSON_UNESCAPED_UNICODE),
                'FILES' => json_encode($_FILES, JSON_UNESCAPED_UNICODE),
            ], $_SERVER);
            $host        = $_SERVER['HTTP_HOST'];
            if (self::$conf['is_debug'] == false) {
                $error_msg = self::$conf['product_error_message'];
            }
            ob_end_clean();
            header("Content-type: text/html; charset=utf-8");
            include_once __DIR__ . '/WTFTemplate.php';
        } else {
            // cli脚本模式
            echo sprintf(PHP_EOL . "\033[1;37;41m[%d]%s\033[0m : \033[1;31m%s\033[0m" . PHP_EOL . PHP_EOL, $error_code, $error_type, $error_msg);
            echo sprintf("at \033[1;37;42m%s : %d\033[0m " . PHP_EOL, $error_file, $error_line);
            $max_len = strlen(max(array_keys($error_php_source_code)));
            foreach ($error_php_source_code as $line => $code) {
                $line = str_pad($line, $max_len, '0', STR_PAD_LEFT);
                if (!empty($code)) {
                    $code = htmlspecialchars_decode($code);
                    if ($error_line == $line) {
                        echo sprintf("%s: \033[1;37;41m%s\033[0m" . PHP_EOL, $line, rtrim($code, PHP_EOL));
                    } else {
                        echo sprintf("%s: %s", $line, $code);
                    }
                }
            }
            echo PHP_EOL;
        }
        exit();
    }

    /**
     * 获取php出错代码附近的代码
     */
    protected static function getPhpCode()
    {
        $fh         = new \SplFileObject(self::$error_file);
        $start_line = self::$error_line - 9 < 0 ? 0 : self::$error_line - 9;
        $fh->seek($start_line);
        $content = [];
        for ($i = 0; $i <= 16; ++$i) {
            $content[$start_line + $i + 1] = htmlspecialchars($fh->current());
            $fh->next();
        }
        self::$error_php_source_code = $content;
    }

    /**
     * 记录php错误信息
     */
    protected static function recordError()
    {
        if (!empty(self::$conf['php_error_log_dir'])) {
            $path             = self::$conf['php_error_log_dir'];
            $php_err_log_file = $path . DIRECTORY_SEPARATOR . date('Y-m-d-H') . '.log';
            if (!is_dir($path)) {
                exec('mkdir -p ' . $path);
            }
            // [time] [type] [file:line] [error_message]
            $log_str = sprintf('[%s] [%s] [%s:%d] %s' . PHP_EOL,
                date('Y-m-d H:i:s'), self::$error_type, self::$error_file, self::$error_line, self::$error_msg);
            error_log($log_str, 3, $php_err_log_file);
        }
    }
}