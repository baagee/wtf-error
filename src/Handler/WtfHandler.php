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
    const RESPONSE_HTML     = 'html';
    const RESPONSE_XML      = 'xml';
    const RESPONSE_JSON     = 'json';
    const RESPONSE_TERMINAL = 'terminal';
    const RESPONSE_PLAIN    = 'plain';

    /**
     * @param \Throwable $t
     */
    public function throwableHandler(\Throwable $t)
    {
        $responseType = $this->getResponseType();
        switch ($responseType) {
            case self::RESPONSE_HTML:
                $this->responseHtml($t);
                break;
            case self::RESPONSE_TERMINAL:
                $this->responseTerminal($t);
                break;
            case self::RESPONSE_XML:
                $this->responseXml($t);
                break;
            case self::RESPONSE_PLAIN:
                $this->responsePlain($t);
                break;
            case self::RESPONSE_JSON:
            default:
                $this->responseJson($t);
        }
    }

    /**
     * 获取输出类型
     * @return string
     */
    protected function getResponseType()
    {
        $responseType = self::RESPONSE_JSON;//默认json
        if (strtolower(PHP_SAPI) == 'cli') {
            // 命令行终端
            $responseType = self::RESPONSE_TERMINAL;
        } else {
            $headers = headers_list();
            /*
             * text/html
             * text/plain
             * text/xml
             * application/json
             * application/xml
             *
             * content-type: text/html;charset=utf-8
             * */
            foreach ($headers as $header) {
                $header = str_replace(array(" ", "　", "\t", "\n", "\r"), array("", "", "", "", ""), $header);
                if (stripos($header, 'Content-Type:application/json') !== false) {
                    $responseType = self::RESPONSE_JSON;
                    break;
                } elseif (stripos($header, 'Content-Type:application/xml') !== false ||
                    stripos($header, 'Content-Type:text/xml') !== false) {
                    $responseType = self::RESPONSE_XML;
                    break;
                } elseif (stripos($header, 'Content-Type:text/plain') !== false) {
                    $responseType = self::RESPONSE_PLAIN;
                    break;
                } elseif (stripos($header, 'Content-Type:text/html') !== false) {
                    $responseType = self::RESPONSE_HTML;
                    break;
                }
            }
        }
        return $responseType;
    }

    /**
     * 获取错误信息数据
     * @param \Throwable $t
     * @return array
     */
    protected function getErrorData(\Throwable $t)
    {
        $is_debug = $this->conf['is_debug'] ? 'true' : 'false';
        if ($this->conf['is_debug']) {
            $err_msg = $t->getMessage();
            $err_type = $this->errorType;
            $err_file = $t->getFile();
            $err_line = $t->getLine();
            $err_php_code_arr = self::getPhpCode($err_file, $err_line);
            $backtrace_array = $t->getTrace();
            $backtrace = explode(PHP_EOL, $t->getTraceAsString());
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
        return compact('err_code', 'err_msg', 'err_type', 'err_file', 'err_line', 'is_debug', 'err_php_code_arr', 'backtrace', 'backtrace_array');
    }

    /**
     * 响应xml
     * @param \Throwable $t
     */
    protected function responseXml(\Throwable $t)
    {
        header('Content-Type: application/xml; charset=utf-8', true, 500);
        $data = $this->getErrorData($t);
        array_walk($data['err_php_code_arr'], function (&$v) {
            $v = str_replace(PHP_EOL, '', $v);
        });
        $data = array_filter($data, function ($v) {
            return $v !== null;
        });
        $string = '<?xml version="1.0" encoding="utf-8"?><error/>';
        $xml = new \SimpleXMLElement($string);
        $this->arrayToXML($xml, $data);
        echo $xml->asXML();
    }

    /**
     * 数组转xml
     * @param \SimpleXMLElement $obj
     * @param array             $array
     * @param string            $child
     */
    protected function arrayToXML(\SimpleXMLElement $obj, array $array, $child = "items")
    {
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                $key = $child . $key;
            }
            if (is_array($value)) {
                $node = $obj->addChild($key);
                $this->arrayToXML($node, $value);
            } else {
                $obj->addChild($key, htmlspecialchars($value));
            }
        }
    }

    /**
     * 响应json
     * @param \Throwable $t
     */
    protected function responseJson(\Throwable $t)
    {
        header('Content-Type: application/json; charset=utf-8', true, 500);
        $data = $this->getErrorData($t);
        array_walk($data['err_php_code_arr'], function (&$v) {
            $v = str_replace(PHP_EOL, '', $v);
        });
        $data = array_filter($data, function ($v) {
            return $v !== null;
        });
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 输出文本错误信息
     * @param \Throwable $t
     */
    protected function responsePlain(\Throwable $t)
    {
        header('Content-Type: text/plain; charset=utf-8', true, 500);
        extract($this->getErrorData($t));
        echo sprintf('Debug: %s' . PHP_EOL, $is_debug);
        echo sprintf('[%d] %s: %s' . PHP_EOL . PHP_EOL, $err_code, $err_type, $err_msg);
        if ($is_debug == 'true') {
            echo sprintf('at %s : %d' . PHP_EOL, $err_file, $err_line);
            $max_len = strlen(max(array_keys($err_php_code_arr)));
            foreach ($err_php_code_arr as $_line => $codeStr) {
                if (!empty($codeStr)) {
                    $codeStr = htmlspecialchars_decode($codeStr);
                    $_line = str_pad($_line, $max_len, '0', STR_PAD_LEFT);
                    echo sprintf("%s: %s", $_line, $codeStr);
                }
            }
        }
    }

    /**
     * 响应html
     * @param \Throwable $t
     */
    protected function responseHtml(\Throwable $t)
    {
        header('Content-Type: text/html; charset=utf-8', true, 500);
        extract($this->getErrorData($t));
        include_once __DIR__ . DIRECTORY_SEPARATOR . 'WTFTemplate.php';
    }

    /**
     * 终端脚本出错展示
     * @param \Throwable $t
     */
    protected function responseTerminal(\Throwable $t)
    {
        // 脚本出错 不管是否开发模式不隐藏错误信息
        extract($this->getErrorData($t));
        echo sprintf(PHP_EOL . 'Debug: %s' . PHP_EOL, $is_debug == 'true' ? "\033[1;32mtrue[0m" : "\033[1;31mfalse[0m");
        echo sprintf("\033[1;37;41m[%d] %s\033[0m: \033[1;31m%s\033[0m" . PHP_EOL . PHP_EOL, $err_code, $err_type, $err_msg);
        if ($is_debug == 'true') {
            echo sprintf("at \033[1;37;42m%s : %d\033[0m " . PHP_EOL, $err_file, $err_line);
            $max_len = strlen(max(array_keys($err_php_code_arr)));
            foreach ($err_php_code_arr as $_line => $codeStr) {
                if (!empty($codeStr)) {
                    $codeStr = htmlspecialchars_decode($codeStr);
                    $thisLine = $_line;
                    $_line = str_pad($_line, $max_len, '0', STR_PAD_LEFT);
                    if ($err_line == $thisLine) {
                        echo sprintf("%s: \033[1;37;41m%s\033[0m" . PHP_EOL, $_line, rtrim($codeStr, PHP_EOL));
                    } else {
                        echo sprintf("%s: %s", $_line, $codeStr);
                    }
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
        $fh = new \SplFileObject($file);
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
            mkdir($this->conf['php_error_log_dir'], 0755, true);
            // exec('mkdir -p ' . $this->conf['php_error_log_dir']);
        }
        error_log($log_str, 3, $this->conf['php_error_log_dir'] . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log');
    }
}
