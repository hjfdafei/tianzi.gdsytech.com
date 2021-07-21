<?php /*a:2:{s:97:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\rule\rule_add.html";i:1583834612;s:95:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\layout\main.html";i:1626334813;}*/ ?>
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
        <title>管理后台--添加权限</title>
    </head>
    <body>
        
        
        
<body class="index">
    <div class="setting_form">
        <form class='layui-form dataform' enctype="multipart/form-data" method="post" id='goodsform'>
            <table class="layui-table">
                <tr>
                    <td class='td_right'><label class="layui-form-label">选择层级</label></td>
                    <td class='td_left'>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <select name="parentid">
                                    <option value="0">顶级</option>
                                    <?php if(is_array($parentlist) || $parentlist instanceof \think\Collection || $parentlist instanceof \think\Paginator): $i = 0; $__LIST__ = $parentlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                    <option value="<?php echo htmlentities($vo['id']); ?>"><?php echo htmlentities($vo['rule_title']); ?></option>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                          </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">权限名称</label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='rule_title' id="rule_title" placeholder="权限名称" autocomplete="off" class="layui-input rule_title" value='' />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">模块名称</label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='rule_module' id="rule_module" placeholder="模块名称" autocomplete="off" class="layui-input rule_module" value='sytechadmin' />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">控制器名称</label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='rule_controller' id="rule_controller" placeholder="控制器名称" autocomplete="off" class="layui-input rule_controller" value='' />
                            <span class='inputnote_span'>(如果没有，请填写#)</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">方法名称</label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='rule_action' id="rule_action" placeholder="方法名称" autocomplete="off" class="layui-input rule_action" value='' />
                            <span class='inputnote_span'>(如果没有，请填写#)</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">权限css样式类</label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='rule_class' id="rule_class" placeholder="权限css样式类" autocomplete="off" class="layui-input rule_class" value='' />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">排序</label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="number" name='rule_sort' id="rule_sort" placeholder="排序" autocomplete="off" class="layui-input rule_sort" value='0' />
                            <span class='inputnote_span'>(值越大排在越前)</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">是否菜单</label></td>
                    <td class='td_left'>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <input type="radio" name="rule_ismenu" class='rule_ismenu' value="1" title="是" />
                                <input type="radio" name="rule_ismenu" class='rule_ismenu' value="2" title="否" checked='checked' />
                            </div>
                            <span class='inputnote_span'>(设置为菜单的权限，才会在分配权限后在左侧栏显示)</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="savedata_subbtn" id='savedata_subbtn'>提交</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                        </div>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</body>

        
        
    <script type="text/javascript">
        layui.use(['laydate','form','table','upload'], function(){
            var laydate = layui.laydate;
            var table = layui.table;
            var form = layui.form;
            form.on('submit(savedata_subbtn)', function(data){
                savedata();
                return false;
            });

        })

        //保存数据
        function savedata(){
            var sindex=layer.load(1,{time:5*1000});
            $('#goodsform').ajaxSubmit({
                url:"<?php echo url('rule/rule_add'); ?>",
                type:'post',
                dataType:'json',
                beforeSubmit: function(){
                    var rule_title=$.trim($('.rule_title').val());
                    var rule_module=$.trim($('.rule_module').val());
                    var rule_controller=$.trim($('.rule_controller').val());
                    var rule_action=$.trim($('.rule_action').val());
                    if(rule_title==''){
                        layer.msg('请输入权限名称');
                        layer.close(sindex);
                        return false;
                    }
                    if(rule_module==''){
                        layer.msg('请输入模块名称');
                        layer.close(sindex);
                        return false;
                    }
                    if(rule_controller==''){
                        layer.msg('请输入控制器名称');
                        layer.close(sindex);
                        return false;
                    }
                    if(rule_action==''){
                        layer.msg('请输入方法名称');
                        layer.close(sindex);
                        return false;
                    }
                },
                success: function(data){
                    layer.close(sindex);
                    layer.msg(data.msg);
                    if(data.code==400){
                        return false;
                    }else if(data.code==200){
                        setTimeout("window.location.reload()",2000)
                    }
                }
            });
            return false;
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