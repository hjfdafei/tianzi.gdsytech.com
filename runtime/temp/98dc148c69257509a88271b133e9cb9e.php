<?php /*a:2:{s:110:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\adminrole\adminrole_assign.html";i:1586341680;s:95:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\layout\main.html";i:1626334813;}*/ ?>
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
        <title>管理后台--分配权限</title>
    </head>
    <body>
        
        
        
<body class="index">
    <div class="layui-card">
        <div class="layui-card-body">
            <div style='height:20px;'></div>
            <?php if(empty($list)): ?>
                <div class='nodata'>暂无数据</div>
            <?php else: ?>
                <form class="layui-form" id='goodsform'>
                    <div class="layui-form-item">
                        <?php foreach($list as $key=>$item): ?>
                        <dl class="rule_ul">
                            <dt>
                                <div class="layui-row layui-rowlevel1" style='padding-bottom:10px;'>
                                    <div class="layui-col-md">
                                      <input type="checkbox" name="ids[]" value="<?php echo htmlentities($item['id']); ?>" lay-filter="level-1" class="checkbox-ids" lay-skin="primary" title="<?php echo htmlentities($item['rule_title']); ?>" <?php if(in_array($item['id'],$info['role_ruleid'])): ?>checked='checked'<?php endif; ?> />
                                    </div>
                                </div>
                            </dt>
                            <dd style='background:#f2f2f2;padding-left:20px;'>
                            <?php if(!(empty($item['son']) || (($item['son'] instanceof \think\Collection || $item['son'] instanceof \think\Paginator ) && $item['son']->isEmpty()))): foreach($item['son'] as $key2=>$item2): ?>
                                <dl class="rule_ul menu-son">
                                    <dt>
                                        <div class="layui-row" style='padding-bottom:10px;'>
                                            <div class="layui-col-md">
                                                <input type="checkbox" name="ids[]" value="<?php echo htmlentities($item2['id']); ?>" lay-filter="level-2" class="checkbox-ids" lay-skin="primary" title="<?php echo htmlentities($item2['rule_title']); ?>" <?php if(in_array($item2['id'],$info['role_ruleid'])): ?>checked='checked'<?php endif; ?> />
                                            </div>
                                        </div>
                                    </dt>
                                </dl>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </dd>
                        </dl>
                        <?php endforeach; ?>
                    </div>
                    <div class="layui-input-block" style='text-align:center;margin:0;'>
                        <input type="hidden" name="roleid" value='<?php echo htmlentities($info['id']); ?>' />
                        <button class="layui-btn" lay-submit lay-filter="savedata_subbtn" id='savedata_subbtn'>提交</button>
                        <button type="reset" class="layui-btn layui-btn-primary">重置</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
<style type="text/css">
    .menu-son{min-width:120px;display:inline-block;}
</style>


        
        
<script type="text/javascript">
    layui.use(['form','jquery','layer'], function(){
        var $=layui.jquery;
        form = layui.form;
        layer= layui.layer;
        //隔行换色
        //$('.layui-form .layui-rowlevel1:odd').css('background-color','#f2f2f2');
        form.on('checkbox(level-1)', function(data){
            $(data.elem).parent().parent().parent().next().find('input').prop('checked',data.elem.checked);
            form.render('checkbox');
        });
        form.on('checkbox(level-2)', function(data){
            var aa=0;
            var bb=new Array();
            $(data.elem).parent().parent().parent().parent().parent().siblings().find('input').prop('checked',data.elem.checked);
            $($(data.elem).parent().parent().parent().parent().siblings()).each(function(){
                bb.push($(this).find('input').prop('checked'));
            });
            bb.push(data.elem.checked);
            for(var i=0;i<bb.length;i++){
                if(bb[i]==false){
                    aa+=0;
                }else{
                    aa+=1;
                }
            }
            if(aa==0){
                $(data.elem).parent().parent().parent().parent().parent().siblings().find('input').prop('checked',false);
            }else{
                $(data.elem).parent().parent().parent().parent().parent().siblings().find('input').prop('checked',true);
            }
            form.render('checkbox');
        });
        form.on('submit(savedata_subbtn)', function(data){
            savedata();
            return false;
        });
    });

    //保存数据
    function savedata(){
        var sindex=layer.load(1,{time:5*1000});
        $('#goodsform').ajaxSubmit({
            url:"<?php echo url('Adminrole/adminrole_assign'); ?>",
            type:'post',
            dataType:'json',
            beforeSubmit: function(){
                var dataid='';
                $('input[type=checkbox]:checked').each(function(){
                    dataid+=$(this).val()+',';
                });
                dataid=$.trim(dataid);
                if(dataid==''){
                    layer.msg('请选择权限');
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