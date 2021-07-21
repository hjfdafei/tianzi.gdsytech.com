<?php /*a:2:{s:98:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\rule\rule_list.html";i:1602572576;s:95:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\layout\main.html";i:1626334813;}*/ ?>
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
        <title>管理后台--权限列表</title>
    </head>
    <body>
        
        
        
<body class="index">
    <div class="layui-card">
        <div class="layui-card-body">
            <div style='height:10px;'></div>
            <div class="layui-row">
                <div class="layui-col-md12">
                    <button class="layui-btn layui-btn" onclick="rule_add()">新增权限</button>
                    <button class="layui-btn layui-btn-danger" onclick="rule_del();">删除权限</button>
                </div>
            </div>
            <div style='height:20px;'></div>
            <?php if(empty($list)): ?>
                <div class='nodata'>暂无数据</div>
            <?php else: ?>
                <form class="layui-form" action="">
                    <div class="layui-form-item">
                        <?php foreach($list as $key=>$item): ?>
                        <dl class="rule_ul">
                            <dt>
                                <div class="layui-row" style='padding-bottom:10px;'>
                                    <div class="layui-col-md9">
                                      <input type="checkbox" name="ids[]" value="<?php echo htmlentities($item['id']); ?>" lay-filter="level-1" class="checkbox-ids" lay-skin="primary" title="<?php echo htmlentities($item['rule_title']); ?>">
                                    </div>
                                    <div class="layui-col-md3">
                                        <a class="layui-btn layui-btn-warm layui-btn-sm rule_ul_edit" href="javascript:void(0);" onclick="rule_edit(<?php echo htmlentities($item['id']); ?>)">修改</a>
                                    </div>
                                </div>
                            </dt>
                            <dd>
                            <?php if(!(empty($item['son']) || (($item['son'] instanceof \think\Collection || $item['son'] instanceof \think\Paginator ) && $item['son']->isEmpty()))): foreach($item['son'] as $key2=>$item2): ?>
                                <dl class="rule_ul menu-son">
                                    <dt>
                                        <div class="layui-row" style='padding-bottom:10px;'>
                                            <div class="layui-col-md9" style="padding-left: 20px;">
                                                <input type="checkbox" name="ids[]" value="<?php echo htmlentities($item2['id']); ?>" lay-filter="level-2" class="checkbox-ids" lay-skin="primary" title="<?php echo htmlentities($item2['rule_title']); ?>">
                                            </div>
                                            <div class="layui-col-md3">
                                                <a class="layui-btn layui-btn-normal layui-btn-sm rule_ul_edit" href="javascript:void(0);" onclick="rule_edit(<?php echo htmlentities($item2['id']); ?>)">修改</a>
                                            </div>
                                        </div>
                                    </dt>
                                    <?php if(!(empty($item2['son']) || (($item2['son'] instanceof \think\Collection || $item2['son'] instanceof \think\Paginator ) && $item2['son']->isEmpty()))): foreach($item2['son'] as $key3=>$item3): ?>
                                    <dd>
                                        <div class="layui-row" style='padding-bottom:10px;'>
                                            <div class="layui-col-md9"  style="padding-left: 40px;">
                                                <input type="checkbox" name="ids[]" value="<?php echo htmlentities($item3['id']); ?>" class="checkbox-ids" lay-skin="primary" title="<?php echo htmlentities($item3['rule_desc']); ?>">
                                            </div>
                                            <div class="layui-col-md3">
                                                <a class="layui-btn layui-btn-normal layui-btn-sm rule_ul_edit" href="javascript:void(0);" onclick="rule_edit(<?php echo htmlentities($item3['id']); ?>)">修改</a>
                                            </div>
                                        </div>
                                    </dd>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </dl>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </dd>
                        </dl>
                        <?php endforeach; ?>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>

        
        
<script type="text/javascript">
    layui.use(['form','jquery','layer'], function(){
        var $    = layui.jquery;
            form = layui.form;
            layer= layui.layer;
        //隔行换色
        $('.layui-form .layui-row:odd').css('background-color','#f2f2f2');

        form.on('checkbox(level-1)', function(data){
            $(data.elem).parent().parent().parent().next().find('input').prop('checked',data.elem.checked);
            form.render('checkbox');
        });

        form.on('checkbox(level-2)', function(data){
            $(data.elem).parent().parent().parent().siblings().find('input').prop('checked',data.elem.checked);
            form.render('checkbox');
        });
    });

    function rule_add(){
        var url='<?php echo url("rule/rule_add"); ?>';
        var title='添加权限';
        layer.open({
            type: 2,
            title:title,
            shadeClose: false,
            shade: 0.8,
            area: ['95%', '90%'],
            content: url, //iframe的url
            end:function () {
                location.reload()
            }
        });
    }

    function rule_edit(dataid){
        if(dataid>0){
            var url='<?php echo url("rule/rule_edit"); ?>?ruleid='+dataid;
            var title='修改权限';
            layer.open({
                type: 2,
                title:title,
                shadeClose: false,
                shade: 0.8,
                area: ['95%', '90%'],
                content: url, //iframe的url
                end:function () {
                    location.reload()
                }
            });
        }
    }

    function rule_del(){
        var dataid='';
        $("[name='ids[]']:checked").each(function(){
            dataid+=$(this).val()+',';
        })
        dataid=$.trim(dataid);
        if(dataid!=''){
            layer.confirm('确定删除吗?',{icon:3,title:'操作提示'},function(index){
                var sindex=layer.load(1,{'time':3*1000});
                $.post("<?php echo url('rule/rule_del'); ?>",{'ruleid':dataid},function(data){
                    layer.msg(data.msg);
                    layer.close(sindex);
                    if(data.code==200){
                        setTimeout("window.location.reload();",2000);
                    }
                },'json')
                layer.close(index);
            })
        }
    }
</script>

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