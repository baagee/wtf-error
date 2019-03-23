# php错误异常展示类
默认的php异常/错误在页面上展示的很难看，通过set_error_handler和set_exception_handler可以捕获到错误和异常，按照自己喜欢的格式去输出或者隐藏

此类稍微封装了一下两种模式下的错误输出

具体使用：

`composer require baagee/wtf-error`

详细见tests目录