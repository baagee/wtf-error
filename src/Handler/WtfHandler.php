<?php
/**
 * Desc: 具体的错误异常处理逻辑
 * User: baagee
 * Date: 2019/3/21
 * Time: 下午10:53
 */

namespace BaAGee\Wtf\Handler;

use BaAGee\Wtf\Base\WtfHandlerAbstract;

/**
 * Class WtfHandler
 * @package BaAGee\Wtf\Handler
 */
class WtfHandler extends WtfHandlerAbstract
{
    /**
     * @param \Throwable $t
     */
    public function throwableHandler(\Throwable $t)
    {
        if (strtolower(PHP_SAPI) !== 'cli') {
            $this->pageShow($t);
        } else {
            $this->terminalShow($t);
        }
    }

    /**
     * @param \Throwable $t
     */
    protected function pageShow(\Throwable $t)
    {
        header('Content-Type: text/html; charset=utf-8');
        http_response_code(500);
        if ($this->conf['is_debug']) {
            $err_msg          = $t->getMessage();
            $err_type         = $this->errorType;
            $err_file         = $t->getFile();
            $err_line         = $t->getLine();
            $err_php_code_arr = self::getPhpCode($err_file, $err_line);
            $is_debug         = $this->conf['is_debug'];
            $backtrace        = explode(PHP_EOL, $t->getTraceAsString());
            foreach ($backtrace as &$item) {
                $item = trim(substr($item, 3));
            }
        } else {
            if (!empty($this->conf['product_error_message'])) {
                $err_msg = $this->conf['product_error_message'];
            } else {
                $err_msg = '未知错误';
            }
            $err_type = 'What The Fuck!';
        }
        $err_code = $t->getCode();
        include_once __DIR__ . DIRECTORY_SEPARATOR . 'WTFTemplate.php';
    }

    /**
     * 终端脚本出错展示
     * @param \Throwable $t
     */
    protected function terminalShow(\Throwable $t)
    {
        // 脚本出错 不管是否开发模式不隐藏错误信息
        $code       = $t->getCode();
        $msg        = $t->getMessage();
        $file       = $t->getFile();
        $line       = $t->getLine();
        $sourceCode = self::getPhpCode($file, $line);//获取脚本出错位置代码
        echo sprintf(PHP_EOL . "\033[1;37;41m[%d] %s\033[0m: \033[1;31m%s\033[0m" . PHP_EOL . PHP_EOL, $code, $this->errorType, $msg);
        echo sprintf("at \033[1;37;42m%s : %d\033[0m " . PHP_EOL, $file, $line);
        $max_len = strlen(max(array_keys($sourceCode)));
        foreach ($sourceCode as $_line => $codeStr) {
            if (!empty($codeStr)) {
                $codeStr  = htmlspecialchars_decode($codeStr);
                $thisLine = $_line;
                $_line    = str_pad($_line, $max_len, '0', STR_PAD_LEFT);
                if ($line == $thisLine) {
                    echo sprintf("%s: \033[1;37;41m%s\033[0m" . PHP_EOL, $_line, rtrim($codeStr, PHP_EOL));
                } else {
                    echo sprintf("%s: %s", $_line, $codeStr);
                }
            }
        }
        echo PHP_EOL;
    }

    /**
     * @param $file
     * @param $line
     * @return array
     */
    protected static function getPhpCode($file, $line)
    {
        $fh         = new \SplFileObject($file);
        $start_line = $line - 9 < 0 ? 0 : $line - 9;
        $fh->seek($start_line);
        $content = [];
        for ($i = 0; $i <= 16; ++$i) {
            $content[$start_line + $i + 1] = htmlspecialchars($fh->current());
            $fh->next();
        }
        return $content;
    }

    /**
     * 写入php error log
     * @param \Throwable $t
     */
    public function writePhpErrorLog(\Throwable $t)
    {
        $log_str = sprintf('[%s] [%d] %s: %s ' . PHP_EOL . 'File:%s:%d' . PHP_EOL . '%s' . PHP_EOL,
            date('Y-m-d H:i:s'), $t->getCode(), $this->errorType, $t->getMessage(), $t->getFile(), $t->getLine(), $t->getTraceAsString());
        if (!is_dir($this->conf['php_error_log_dir'])) {
            mkdir($this->conf['php_error_log_dir'],0755,true);
            // exec('mkdir -p ' . $this->conf['php_error_log_dir']);
        }
        error_log($log_str, 3, $this->conf['php_error_log_dir'] . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log');
    }
}
