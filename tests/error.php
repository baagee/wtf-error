<?php
/**
 * Desc:
 * User: baagee
 * Date: 2018/12/31
 * Time: 下午4:07
 */

/**
 * Class Test
 */
class Test
{
    /**
     * @throws Exception
     */
    public function testa()
    {
        $i = mt_rand(1, 12);
        switch ($i) {
            case 1:
                // E_ERROR
                // 这种错误是致命错误，会在页面显示Fatal Error， 当出现这种错误的时候，程序就无法继续执行下去了
                // 注意，如果有未被捕获的异常，也是会触发这个级别的。
                abc();
                break;
            case 2:
                // E_WARNING
                // 这种错误只是警告，不会终止脚本，程序还会继续进行，显示的错误信息是Warning。比如include一个不存在的文件
                include 'abc.php';
                break;
            case 3:
                // E_NOTICE
                // 这种错误程度更为轻微一些，提示你这个地方不应该这么写。这个也是运行时错误，这个错误的代码可能在其他地方没有问题，只是在当前上下文情况下出现了问题。这种错误程度更为轻微一些，提示你这个地方不应该这么写。这个也是运行时错误，这个错误的代码可能在其他地方没有问题，只是在当前上下文情况下出现了问题。这种错误程度更为轻微一些，提示你这个地方不应该这么写。这个也是运行时错误，这个错误的代码可能在其他地方没有问题，只是在当前上下文情况下出现了问题。
                echo $abc;
                break;
            case 4:
                //E_PARSE
                //这个错误是编译时候发生的，在编译期发现语法错误，不能进行语法分析。这个错误是编译时候发生的，在编译期发现语法错误，不能进行语法分析。这个错误是编译时候发生的，在编译期发现语法错误，不能进行语法分析。这个错误是编译时候发生的，在编译期发现语法错误，不能进行语法分析。这个错误是编译时候发生的，在编译期发现语法错误，不能进行语法分析。
                //比如下面的没有设置为变量
                $aa = 90;// 去掉$
                fss('这个错误是编译时候发生的，在编译期发现语法错误，不能进行语法分析。这个错误是编译时候发生的，在编译期发现语法错误，不能进行语法分析。这个错误是编译时候发生的，在编译期发现语法错误，不能进行语法分析。这个错误是编译时候发生的，在编译期发现语法错误，不能进行语法分析。这个错误是编译时候发生的，在编译期发现语法错误，不能进行语法分析。');
                break;
            case 5:
                //E_STRICT
                //这个错误是PHP5之后引入的，你的代码可以运行，但是不是PHP建议的写法。
                //比如在函数形参传递++符号
                function change(&$var)
                {
                    $var += 10;
                }

                $var = 1;
                change(++$var);
                break;
            case 6:
                //E_RECOVERABLE_ERROR
                //这个级别其实是ERROR级别的，但是它是期望被捕获的，如果没有被错误处理捕获，表现和E_ERROR是一样的。
                //经常出现在形参定义了类型，但调用的时候传入了错误类型。它的错误提醒也比E_ERROR的fatal error前面多了一个Catachable的字样。
                function testCall(A $a)
                {
                }

                $b = new B();
                testCall($b);
                break;
            case 7:
                //E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE, E_USER_DEPRECATED,
                //这些错误都是用户制造的，使用trigger_error，这里就相当于一个口子给用户触发出各种错误类型。这个是一个很好逃避try catch异常的方式。
                trigger_error("E_USER_WARNING trigger_error", E_USER_WARNING);
                break;
            case 8:
                //E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE, E_USER_DEPRECATED,
                //这些错误都是用户制造的，使用trigger_error，这里就相当于一个口子给用户触发出各种错误类型。这个是一个很好逃避try catch异常的方式。
                trigger_error("E_USER_ERROR trigger_error", E_USER_ERROR);
                break;
            case 9:
                //E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE, E_USER_DEPRECATED,
                //这些错误都是用户制造的，使用trigger_error，这里就相当于一个口子给用户触发出各种错误类型。这个是一个很好逃避try catch异常的方式。
                trigger_error("E_USER_NOTICE trigger_error", E_USER_NOTICE);
                break;
            case 10:
                //E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE, E_USER_DEPRECATED,
                //这些错误都是用户制造的，使用trigger_error，这里就相当于一个口子给用户触发出各种错误类型。这个是一个很好逃避try catch异常的方式。
                trigger_error("E_USER_DEPRECATED trigger_error", E_USER_DEPRECATED);
                break;
            case 11:
                throw new PDOException('pdo Exception');
                break;
            default:
                throw new \Exception(__METHOD__ . ' 自定义异常', 1345678);
        }
    }
}

/**
 * Class A
 */
class A
{

}

/**
 * Class B
 */
class B
{

}

(new Test())->testa();