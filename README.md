# php错误异常展示类
默认的php异常/错误在页面上展示的很难看，通过set_error_handler和set_exception_handler可以捕获到错误和异常，按照自己喜欢的格式去输出或者隐藏

此类稍微封装了一下两种模式下的错误输出
页面上出错时：

开启调试模式 会报所有异常，包括notice,warning等
![](https://meinv-1256151484.cos.ap-beijing.myqcloud.com/tuchuang/page.png)

关闭开发模式

`E_WARNING, E_NOTICE, E_STRICT, E_DEPRECATED`将不会提示，
同时也会隐藏具体的储物代码等信息
![](https://meinv-1256151484.cos.ap-beijing.myqcloud.com/tuchuang/page2.png)

命令行脚本出错时：
![](https://meinv-1256151484.cos.ap-beijing.myqcloud.com/tuchuang/QQ20181231-162107%402x.png)

具体使用：

`composer require baagee/wtf-error`

code:
```php
<?php

include_once __DIR__ . '/../vendor/autoload.php';

\WTF\WTF::register([
    'php_error_log_dir' => __DIR__ . '/log',# php错误log输出目录
    'is_debug'          => true #是否为调试模式
]);

echo 'hello';
include_once __DIR__ . '/error.php';
echo 'ok';
```

详细见tests目录

其他待扩展...
