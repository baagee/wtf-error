<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo $err_type; ?></title>
    <meta name="robots" content="noindex,nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <style>
        /* Base */
        body {
            color: #333;
            margin: 0;
            padding: 0 20px 20px;
            word-break: break-word;
            font-family: sans-serif;
        }

        h1 {
            margin: 10px 0 0;
            font-size: 28px;
            line-height: 32px;
            color: red;
            font-weight: bold;
        }

        h2 {
            color: #4288ce;
            padding: 6px 0;
            margin: 6px 0 0;
            font-size: 19px;
        }

        h3 {
            margin: 12px;
            font-size: 16px;
            font-weight: bold;
        }

        a {
            color: #868686;
            cursor: pointer;
        }

        a:hover {
            text-decoration: underline;
        }

        .not-user-select{
            moz-user-select: -moz-none;
            -moz-user-select: none;
            -o-user-select:none;
            -khtml-user-select:none;
            -webkit-user-select:none;
            -ms-user-select:none;
            user-select:none;
        }

        .line-error {
            background: #f8cbcb;
        }

        .line-error-secound {
            background: #fde0e1;
        }
        .error_sql{
            border:1px solid #ddd;
            border-top:none;
            padding-left: 12px;
            max-width: 99%;
            overflow-x: scroll;
            background-color: #f8cbcb
        }

        /* Exception Info */
        .exception {
            margin-top: 20px;
        }

        .exception .message {
            padding: 12px;
            border: 1px solid #ddd;
            line-height: 18px;
            font-size: 16px;
            border-top-left-radius: 4px;
            border-top-right-radius: 4px;
        }

        .exception .source-code {
            padding: 6px;
            border: 1px solid #ddd;
            background: #f9f9f9;
            overflow-x: auto;
            border-top: 0 none;
        }

        .exception .source-code pre {
            margin: 0;
        }

        .exception .source-code pre ol {
            margin: 0;
            color: #4288ce;
            display: inline-block;
            min-width: 100%;
            box-sizing: border-box;
            font-size: 14px;
            padding-left: 48px;
        }

        .exception .source-code pre li {
            border-left: 1px solid #ddd;
            height: 18px;
            line-height: 18px;
        }

        .exception .source-code pre code {
            color: #333;
            height: 100%;
            display: inline-block;
            border-left: 1px solid #fff;
            font-size: 14px;
        }

        .exception .trace {
            padding: 6px;
            border: 1px solid #ddd;
            border-top: 0 none;
            line-height: 16px;
            font-size: 14px;
            font-family: Consolas, "Liberation Mono", Courier, Verdana, "微软雅黑";
            word-break: break-all;
        }

        .exception .trace ol {
            padding-left: 48px;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }

        .exception .trace ol li {
            padding: 2px 4px;
            border-left: 1px solid #eeeeee;
        }

        .exception div:last-child {
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        /* prettyprint */
        pre.prettyprint .pln {
            color: #6900ff
        }

        pre.prettyprint .str {
            color: #de1c7e
        }

        pre.prettyprint .kwd {
            color: #1aa725
        }

        pre.prettyprint .com {
            color: #8e7b7b
        }

        pre.prettyprint .typ {
            color: #606
        }

        pre.prettyprint .lit {
            color: #066
        }

        pre.prettyprint .pun, pre.prettyprint .opn, pre.prettyprint .clo {
            color: #cc6311
        }

        pre.prettyprint .tag {
            color: #008
        }

        pre.prettyprint .atn {
            color: #606
        }

        pre.prettyprint .atv {
            color: #080
        }

        pre.prettyprint .dec, pre.prettyprint .var {
            color: #606
        }

        pre.prettyprint .fun {
            color: red
        }

        .sdgdfhdfghdgr{
            text-align: center;color: #666;font-size: 16px;margin-top: 15%;
        }
        .sdgsdfyrtgfsfge{
            font-size: 22px;display: inline-block;margin-right: 30px;color: #333;
        }
        ol li:hover{
            background-color: #e7f4ff;
        }
    </style>
</head>
<body>
<div class="exception">
    <div class="message">
        <div class="info">
            <div style="border-bottom: 1px solid #eee;">
                <img height="65px" style="vertical-align: middle"
                     src="data:image/png;base64,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">
                <h2 style="display: inline;"> <?php echo $err_type; ?>
                    <?php if($is_debug){;?>in <a class="not-user-select"
                                                 title="<?php echo $err_file; ?>" onclick="display('source-code')"><?php echo $err_file; ?>
                        at line <?php echo $err_line; ?></a>
                    <?php };?>
                </h2>
            </div>
            <div>
                <h1>
                    <?php
                    echo '['.$err_code.'] ';
                    echo $err_msg;
                    ?>
                </h1>
            </div>
        </div>

    </div>
    <?php if($is_debug){;?>
        <div class="source-code" style="display: block;">
            <pre class="prettyprint lang-php"><ol start="<?php echo array_keys($err_php_code_arr)[0];?>"><?php foreach ($err_php_code_arr as $line=>$code){;?><li class="line-<?php echo $line;?> <?php if($err_line==$line){echo 'line-error';}elseif ($line+1==$err_line || $line-1==$err_line){echo 'line-error-secound';};?>"><code><?php echo $code;?></code></li><?php };?></ol></pre>
        </div>
        <div class="trace">
            <div>
                <h2 style="font-family: sans-serif">Backtrace</h2>
            </div>
            <ol>
                <?php foreach ($backtrace as $item) {
                    ; ?>
                    <li>
                        <?php
                        echo $item;
                        ?>
                    </li>
                <?php }; ?>
            </ol>
        </div>
    <?php }else{;?>
        <div class="sdgdfhdfghdgr">
            <p>
                页面即将返回，等待时间： <b id="wait" style="color: red;font-size: 24px;font-weight: normal;">3</b>s
            </p>
            <a class="sdgsdfyrtgfsfge" onclick="window.history.go(-1)">立即返回</a>
            <a style="font-size: 18px" onclick="stop()">停止返回</a>
        </div>
    <?php };?>
</div>
<?php if($is_debug){;?>
    <script>
        <?php echo file_get_contents(implode(DIRECTORY_SEPARATOR,[__DIR__,'prettify.min.js']));?>
        prettyPrint();
        var ol=document.getElementsByClassName('source-code')[0].children[0].children
        if(window.navigator.userAgent.indexOf('Firefox') >= 0){
            ol[0].innerHTML = ol[0].innerHTML;
        }
        function display(className) {
            var ele=document.getElementsByClassName(className)[0];
            if (ele.style.display == 'block') {
                ele.style.display = 'none';
            } else {
                ele.style.display = 'block';
            }
        }
    </script>
<?php }else{;?>
    <script type="text/javascript">
        var wait = document.getElementById('wait');
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                window.history.go(-1);
                clearInterval(interval);
            };
        }, 1000);
        function stop() {
            clearInterval(interval);
        }
    </script>
<?php };?>
</body>
</html>