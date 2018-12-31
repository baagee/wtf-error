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
    protected static $type = 'Error';

    /**
     * @var string 错误信息
     */
    protected static $err_msg = '';

    /**
     * @var int 错误行
     */
    protected static $err_line = 0;

    /**
     * @var array 保存错误的php代码
     */
    protected static $err_php_code_arr = [];

    use WTFRegister, ExceptionHandler, ErrorHandler;

    /**
     * @var array 配置
     */
    protected static $conf = [
        'is_debug'          => true,#是否为调试模式
        'php_error_log_dir' => ''#php error log路径
    ];

    /**
     * 展示错误信息界面
     */
    protected static function show()
    {
        $errorMsg         = self::$err_msg;
        $title_type       = self::$type;
        $backtrace        = self::$backtrace;
        $err_php_code_arr = self::$err_php_code_arr;
        $err_line         = self::$err_line;
        if (strtolower(php_sapi_name()) !== 'cli') {
            // 页面模式
            $is_debug    = self::$conf['is_debug'];
            $environment = array_merge([
                'GET'   => json_encode($_GET, JSON_UNESCAPED_UNICODE),
                'POST'  => json_encode($_POST, JSON_UNESCAPED_UNICODE),
                'FILES' => json_encode($_FILES, JSON_UNESCAPED_UNICODE),
            ], $_SERVER);
            $host        = $_SERVER['HTTP_HOST'];
            ob_end_clean();
            header("Content-type: text/html; charset=utf-8");
            include_once __DIR__ . '/WTFTemplate.php';
        } else {
            // cli脚本模式
            echo sprintf(PHP_EOL . "\033[1;37;41m%s\033[0m : \033[1;31m%s\033[0m" . PHP_EOL . PHP_EOL, $title_type, $errorMsg);
            $end_trace = $backtrace[0];
            echo sprintf("at \033[1;37;42m%s : %d\033[0m " . PHP_EOL, $end_trace['file'], $end_trace['line']);
            $max_len = strlen(max(array_keys($err_php_code_arr)));
            foreach ($err_php_code_arr as $line => $code) {
                $line = str_pad($line, $max_len, '0', STR_PAD_LEFT);
                if (!empty($code)) {
                    $code = htmlspecialchars_decode($code);
                    if ($err_line == $line) {
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
        $error_file = self::$backtrace[0]['file'];
        $err_line   = self::$backtrace[0]['line'];
        $fh         = new \SplFileObject($error_file);
        $start_line = $err_line - 9 < 0 ? 0 : $err_line - 9;
        $fh->seek($start_line);
        $content = [];
        for ($i = 0; $i <= 16; ++$i) {
            $content[$start_line + $i + 1] = htmlspecialchars($fh->current());
            $fh->next();
        }
        self::$err_php_code_arr = $content;
    }
}