<?php /*a:3:{s:95:"D:\webenv\Apache2.4.33\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\index\index.html";i:1626442045;s:95:"D:\webenv\Apache2.4.33\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\layout\main.html";i:1626442045;s:94:"D:\webenv\Apache2.4.33\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\index\menu.html";i:1626442045;}*/ ?>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="format-detection" content="telephone=no">
        <link rel="stylesheet" href="/static/plugins/layui/css/layui.css" media="all" />
        <link rel="stylesheet" href="/static/plugins/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="/static/css/global.css" media="all">
        <script type="text/javascript" src="/static/js/jquery.min.js"></script>
        <script type="text/javascript" src="/static/js/jquery.form.min.js"></script>
        <script type="text/javascript" src="/static/js/jquery.particleground.min.js"></script>
        <script type="text/javascript" src="/static/plugins/layui/layui.js"></script>
        <script type="text/javascript" src="/static/plugins/tinymce4.9.2/tinymce.min.js"></script>
        <script type="text/javascript" src='https://cdn.bootcss.com/blueimp-md5/2.10.0/js/md5.min.js'></script>
        <link rel="stylesheet" href="/static/xadmin/css/font.css">
        <link rel="stylesheet" href="/static/css/weui.min.css">
        <link rel="stylesheet" href="/static/xadmin/css/xadmin.css">
        <script type="text/javascript" src="/static/xadmin/js/xadmin.js"></script>
        <link rel="stylesheet" href="/static/font-awesome-4.7.0/css/font-awesome.css" media="all">
        <link rel="stylesheet" href="/static/css/ownstyle.css" media="all">
        <!--[if lt IE 9]>
            <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
            <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <title><?php echo htmlentities($page_title); ?>系统管理后台</title>
    </head>
    <body>
        
        
        
<body class="index">
        <!-- 顶部开始 -->
        <div class="container">
            <div class="logo"><a href="<?php echo url('index/index'); ?>">管理后台</a></div>
            <div class="left_open">
                <a><i title="展开左侧栏" class="layui-icon layui-icon-shrink-right"></i></a>
            </div>
            <ul class="layui-nav right" lay-filter="">
                <li class="layui-nav-item">
                    <a href="javascript:;"><?php echo htmlentities($admininfo['username']); ?></a>
                    <dl class="layui-nav-child">
                        <!-- 二级菜单 -->
                        <dd>
                            <a onclick="xadmin.open('修改信息','<?php echo url("index/admininfo_update"); ?>')">修改信息</a>
                        </dd>
                        <dd>
                            <a href="javascript:void(0);" onclick="logout();">退出</a>
                        </dd>

                    </dl>
                </li>
                <li class="layui-nav-item">
                    <a href="javascript:void(0);" onclick="clears();" class="layui-btn layui-btn-normal">清除缓存</a>
                </li>
            </ul>
        </div>
        <!-- 顶部结束 -->
        <!-- 中部开始 -->
        <!-- 左侧菜单开始 -->
        <div class="left-nav">
            <div id="side-nav" class='layui-side-scroll'>
                <ul id="nav">
    <?php if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
        <li>
            <a href="<?php echo htmlentities($vo['rule_url']); ?>">
                <i class="iconfont left-nav-li" lay-tips="<?php echo htmlentities($vo['rule_title']); ?>"> <i class="fa <?php echo htmlentities($vo['rule_class']); ?>"></i></i>
                <cite><?php echo htmlentities($vo['rule_title']); ?></cite>
                <i class="iconfont nav_right"></i>
            </a>
            <?php if(!empty($vo['item'])): ?>
            <ul class="sub-menu">
                <?php if(is_array($vo['item']) || $vo['item'] instanceof \think\Collection || $vo['item'] instanceof \think\Paginator): $i = 0; $__LIST__ = $vo['item'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($i % 2 );++$i;?>
                <li>
                    <a onclick="xadmin.add_tab('<?php echo htmlentities($vo2['rule_title']); ?>','<?php echo htmlentities($vo2['rule_url']); ?>')">
                        <i class="layui-icon layui-icon-right"></i>
                        <cite><?php echo htmlentities($vo2['rule_title']); ?></cite>
                    </a>
                </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
            <?php endif; ?>
        </li>
    <?php endforeach; endif; else: echo "" ;endif; ?>
    <li>
        <a onclick="xadmin.add_tab('系统设置','<?php echo url("Index/setting"); ?>')">
            <i class="iconfont left-nav-li" lay-tips="系统设置"> <i class="fa fa-cog"></i></i>
            <cite>系统设置</cite>
            <i class="iconfont nav_right"></i>
        </a>
    </li>
    <li>
        <a href="javascript:void(0);">
            <i class="iconfont left-nav-li" lay-tips="Banner图片管理"> <i class="fa fa-picture-o"></i></i>
            <cite>Banner图片管理</cite>
            <i class="iconfont nav_right"></i>
        </a>
        <ul class="sub-menu">
            <li>
                <a onclick="xadmin.add_tab('Banner列表','<?php echo url("Banner/banner_list"); ?>')">
                    <i class="layui-icon layui-icon-right"></i>
                    <cite>Banner列表</cite>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="javascript:void(0);">
            <i class="iconfont left-nav-li" lay-tips="校区管理"> <i class="fa fa-life-ring"></i></i>
            <cite>校区管理</cite>
            <i class="iconfont nav_right"></i>
        </a>
        <ul class="sub-menu">
            <li>
                <a onclick="xadmin.add_tab('校区列表','<?php echo url("School/school_list"); ?>')">
                    <i class="layui-icon layui-icon-right"></i>
                    <cite>校区列表</cite>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="javascript:void(0);">
            <i class="iconfont left-nav-li" lay-tips="用户管理"> <i class="fa fa-user-circle"></i></i>
            <cite>用户管理</cite>
            <i class="iconfont nav_right"></i>
        </a>
        <ul class="sub-menu">
            <li>
                <a onclick="xadmin.add_tab('用户列表','<?php echo url("User/user_list"); ?>')">
                    <i class="layui-icon layui-icon-right"></i>
                    <cite>用户列表</cite>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="javascript:void(0);">
            <i class="iconfont left-nav-li" lay-tips="宽带套餐管理"> <i class="fa fa-asterisk"></i></i>
            <cite>宽带套餐管理</cite>
            <i class="iconfont nav_right"></i>
        </a>
        <ul class="sub-menu">
            <li>
                <a onclick="xadmin.add_tab('宽带套餐列表','<?php echo url("Goods/goods_list"); ?>')">
                    <i class="layui-icon layui-icon-right"></i>
                    <cite>宽带套餐列表</cite>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="javascript:void(0);">
            <i class="iconfont left-nav-li" lay-tips="宽带账号管理"> <i class="fa fa-futbol-o"></i></i>
            <cite>宽带账号管理</cite>
            <i class="iconfont nav_right"></i>
        </a>
        <ul class="sub-menu">
            <li>
                <a onclick="xadmin.add_tab('宽带账号列表','<?php echo url("Broadband/broadband_list"); ?>')">
                    <i class="layui-icon layui-icon-right"></i>
                    <cite>宽带账号列表</cite>
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="javascript:void(0);">
            <i class="iconfont left-nav-li" lay-tips="订单管理"> <i class="fa fa-first-order"></i></i>
            <cite>订单管理</cite>
            <i class="iconfont nav_right"></i>
        </a>
        <ul class="sub-menu">
            <li>
                <a onclick="xadmin.add_tab('订单列表','<?php echo url("Orders/orders_list"); ?>')">
                    <i class="layui-icon layui-icon-right"></i>
                    <cite>订单列表</cite>
                </a>
            </li>
        </ul>
    </li>
    <!-- <li>
        <a href="javascript:void(0);">
            <i class="iconfont left-nav-li" lay-tips="权限管理"> <i class="fa fa-sitemap"></i></i>
            <cite>权限管理</cite>
            <i class="iconfont nav_right"></i>
        </a>
        <ul class="sub-menu">
            <li>
                <a onclick="xadmin.add_tab('权限列表','<?php echo url("Rule/rule_list"); ?>')">
                    <i class="layui-icon layui-icon-right"></i>
                    <cite>权限列表</cite>
                </a>
            </li>
        </ul>
    </li> -->
</ul>
            </div>
        </div>
        <!-- <div class="x-slide_left"></div> -->
        <!-- 左侧菜单结束 -->
        <!-- 右侧主体开始 -->
        <div class="page-content">
            <div class="layui-tab tab" lay-filter="xbs_tab" lay-allowclose="false">
                <ul class="layui-tab-title">
                    <li class="home"><i class="layui-icon">&#xe68e;</i>我的桌面</li>
                </ul>
                    <div class="layui-unselect layui-form-select layui-form-selected" id="tab_right" >
                        <dl>
                            <dd data-type="this">关闭当前</dd>
                            <dd data-type="other">关闭其它</dd>
                            <dd data-type="all">关闭全部</dd>
                        </dl>
                    </div>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <iframe src="<?php echo url('index/welcome'); ?>" frameborder="0" class="x-iframe"></iframe>
                    </div>
                </div>
                <div id="tab_show"></div>
            </div>
        </div>
        <div class="page-content-bg"></div>
        <style id="theme_style"></style>
    </body>

        
        

        <div class='mainfoot'>
            <div class='hasneworder' onclick="parent.xadmin.add_tab('预约订单列表','<?php echo url("Orders/orders_list"); ?>')">你有新的订单需要处理</div>
            <div class='hasnewchat' onclick="parent.xadmin.add_tab('客服消息列表','<?php echo url("Servicechat/servicechat_list"); ?>')">你有新的消息需要回复</div>
        </div>
        <style type="text/css">
            .mainfoot{position:fixed;right:0;bottom:0;background:#333;height:70px;padding:8px;display:none;}
            .hasneworder{display:block;border:1px solid #1E9FFF;border-radius:5px;padding:5px;cursor:pointer;color:#fff;display:none;}
            .hasnewchat{display:block;border:1px solid #F00;border-radius:5px;margin-top:10px;padding:5px;cursor:pointer;color:#fff;display:none;}
        </style>
        <script type="text/javascript">
            layui.use(['form'], function(){
                var form = layui.form;
                form.on('checkbox(choose_all)', function (data) {
                    $("input[name='checkgoods[]']").each(function () {
                        this.checked = data.elem.checked;
                    });
                    form.render('checkbox');
                });
                form.on('checkbox(choose_single)', function (data) {
                    var i = 0;
                    var j = 0;
                    $("input[name='check[]']").each(function () {
                        if(this.checked === true){
                            i++;
                        }
                        j++;
                    });
                    if(i == j){
                        $(".choose_all").prop("checked",true);
                        form.render('checkbox');
                    }else{
                        $(".choose_all").removeAttr("checked");
                        form.render('checkbox');
                    }
                });
            });

            function clears(){
                layer.confirm('你确定要清除缓存吗？', {
                    btn: ['确定', '取消']
                }, function(index, layero){
                    $.post('<?php echo url("sytechadmin/index/clear"); ?>',{},function(data){
                        layer.msg(data.msg);
                        if(data.code==200){
                            setTimeout(function(){location.reload();},1500);
                        }
                    },'json');
                }, function(index){
                });
            }

            function logout(){
                layer.confirm('你确定要退出吗？', {
                    btn: ['确定', '取消']
                }, function(index, layero){
                    $.post("<?php echo url('sytechadmin/login/logout'); ?>",{},function(data){
                        layer.msg(data.msg);
                        if(data.code==200){
                            setTimeout(function(){window.location.href="<?php echo url('Sytechadmin/Login/login'); ?>";},1500);
                        }
                    },'json');
                }, function(index){
                });
            }

            // function checknums(){
            //     $.post('<?php echo url("sytechadmin/index/chekcnums"); ?>',{},function(data){
            //         if(data.code==200){
            //             var resdata=data.data.data;
            //             if(resdata.allnum>0){
            //                 $('.mainfoot').show();
            //                 if(resdata.ordernum>0){
            //                     $('.hasneworder').show();
            //                 }else{
            //                     $('.hasneworder').hide();
            //                 }
            //                 if(resdata.chatnum>0){
            //                     $('.hasnewchat').show();
            //                 }else{
            //                     $('.hasnewchat').show();
            //                 }
            //             }else{
            //                 $('.mainfoot').hide();
            //             }
            //         }
            //     },'json');
            // }
            // checknums();
            // setInterval(checknums,1000*60);
        </script>
    </body>
</html>