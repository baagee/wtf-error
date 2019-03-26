# php错误异常展示类
默认的php异常/错误在页面上展示的很难看，通过set_error_handler和set_exception_handler可以捕获到错误和异常，按照自己喜欢的格式去输出或者隐藏

此类稍微封装了一下两种模式下的错误输出

具体使用：

`composer require baagee/wtf-error`

### 只需要在项目开头注册一下就行了
```php
// 使用内置的错误处理展示
\BaAGee\Wtf\WtfError::register(new \BaAGee\Wtf\Handler\WtfHandler([
    'php_error_log_dir' => __DIR__ . '/log',//指定PHP错误log目录，为空 不记录
    // 是否是开发调试模式，会显示详细错误。生产环境下很不安全，建议为false
    'is_debug'          => true
]));
```
截图示例：

访问页面出错时：

![示例](https://meinv-1256151484.cos.ap-beijing.myqcloud.com/tuchuang/%E4%BC%81%E4%B8%9A%E5%BE%AE%E4%BF%A1%E6%88%AA%E5%9B%BE_6fdddaaf-bba5-418f-9581-35a93afa2515.png)

执行脚本出错时：

![示例](https://meinv-1256151484.cos.ap-beijing.myqcloud.com/tuchuang/%E4%BC%81%E4%B8%9A%E5%BE%AE%E4%BF%A1%E6%88%AA%E5%9B%BE_00cab1f8-dacb-4e85-bd55-475b019bfa82.png)
### 使用自定义的错误处理

```php
/**
 * Class MyHandler
 */
class MyHandler extends \BaAGee\Wtf\Base\WtfHandlerAbstract
{
    /**
     * 自定义错误异常显示 必须实现
     * @param Throwable $t
     */
    public function throwableHandler(\Throwable $t)
    {
        header('content-type: application/json; charset=utf-8');
        // 输出json
        echo json_encode([
            'code'    => $t->getCode(),
            'message' => $t->getMessage(),
            'file'    => $t->getFile(),
            'line'    => $t->getLine(),
        ], JSON_UNESCAPED_UNICODE);
    }

    // 自定义写入Log 不是必须的，不用自己调用，定义好方法就会自动调用
    public function writePhpErrorLog(\Throwable $t)
    {
        file_put_contents('./log.log', $t->getMessage() . PHP_EOL, FILE_APPEND);
    }
}

\BaAGee\Wtf\WtfError::register(new MyHandler([
    'php_error_log_dir'    => __DIR__ . '/log',
    'is_debug'             => true,
    'product_error_hidden' => [E_WARNING, E_NOTICE, E_STRICT, E_DEPRECATED]
]));
```
输出结果:
```json
{"code":0,"message":"Call to undefined function abc()","file":"\/Users\/baagee\/PhpstormProjects\/github\/wtf-error\/tests\/error.php","line":26}
```

### 详细见tests目录