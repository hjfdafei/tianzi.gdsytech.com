<?php /*a:1:{s:85:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\/common/tips/index.html";i:1570845574;}*/ ?>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="format-detection" content="telephone=no">
<script type="text/javascript" src="/static/js/jquery.min.js"></script>
<script type="text/javascript" src="/static/js/layer-v3.1.1/layer/layer.js"></script>
<title>信息提示</title>
</head>
<body>
    <script type="text/javascript">
        (function(){
            var msg = '<?php echo(strip_tags($msg));?>';
            var iurl = '<?php echo($url);?>';
            var wait = '<?php echo($wait);?>';
            var code='<?php echo $code; ?>';
            if(code>0){
                layer.msg(msg,{icon:"6",time:wait*1000});
            }else{
                layer.msg(msg,{icon:"5",time:wait*1000});
            }
            setTimeout(function(){
                location.href=iurl;
            },wait*1000)
        })();
    </script>
</body>
</html>