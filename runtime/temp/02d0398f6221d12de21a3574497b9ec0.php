<?php /*a:3:{s:99:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\grade\grade_add.html";i:1661507295;s:95:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\layout\main.html";i:1626942442;s:102:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\grade\grade_footer.html";i:1661507231;}*/ ?>
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
        <script type="text/javascript" src='/static/js/md5.min.js'></script>
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
        <title>管理后台--添加年级</title>
    </head>
    <body>
        
        
        
<body class="index">
    <div class="setting_form">
        <form class='dataform layui-form' enctype="multipart/form-data" method="post" id='goodsform'>
            <table class="layui-table">
                <tr>
                    <td class='td_right'><label class="layui-form-label">选择校区<span class='musttip'>*</span></label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <select name="school_id" class='school_id'>
                                <option value='0'>请选择校区</option>
                                <?php if(is_array($school_list) || $school_list instanceof \think\Collection || $school_list instanceof \think\Paginator): $i = 0; $__LIST__ = $school_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                <option value='<?php echo htmlentities($vo['id']); ?>'><?php echo htmlentities($vo['title']); ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">年级名称<span class='musttip'>*</span></label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='title' id="title" placeholder="年级名称" autocomplete="off" class="layui-input title" value='' />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">是否显示<span class='musttip'>*</span></label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="radio" name="isshow" value="1" title="是" checked>
                            <input type="radio" name="isshow" value="2" title="否" >
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">排序</label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="number" name='sortby' id="sortby" placeholder="年级排序" autocomplete="off" class="layui-input sortby" value='0' />
                        </div>
                        <span class='inputnote_span'>(排序值越大排在越前)</span>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="text-align: center">
                        <div class="layui-input-block">
                            <button class="layui-btn" lay-submit lay-filter="savedata_addbtn" id='savedata_addbtn'>提交</button>
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
        var laydate=layui.laydate;
        var table=layui.table;
        var form=layui.form;
        var upload=layui.upload;
        form.on('submit(savedata_addbtn)', function(data){
            var url="<?php echo url('Grade/grade_add'); ?>";
            savedata(url);
            return false;
        });
        form.on('submit(savedata_editbtn)', function(data){
            var url="<?php echo url('Grade/grade_edit'); ?>";
            savedata(url);
            return false;
        });
    })

    function grade_add(){
        var url='<?php echo url("Grade/grade_add"); ?>';
        var title='添加年级';
        layer.open({
            type: 2,
            title:title,
            shadeClose: false,
            shade: 0.8,
            area: ['95%', '90%'],
            content: url
        });
    }

    function grade_edit(dataid){
        if(dataid>0){
            var url='<?php echo url("Grade/grade_edit"); ?>?grade_id='+dataid;
            var title='修改年级';
            layer.open({
                type: 2,
                title:title,
                shadeClose: false,
                shade: 0.8,
                area: ['95%', '90%'],
                content: url
            });
        }
    }

    function savedata(url){
        var sindex=layer.load(1,{time:5*1000});
        $('#goodsform').ajaxSubmit({
            url:url,
            type:'post',
            dataType:'json',
            beforeSubmit: function(){
                var title=$.trim($('.title').val());
                if(title==''){
                    layer.msg('请输入年级标题');
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
                    setTimeout("parent.closealllayer()",2000)
                }
            }
        });
        return false;
    }

    function grade_del(){
        var dataid='';
        $("[name='checkgoods[]']:checked").each(function(){
            dataid+=$(this).val()+',';
        })
        dataid=$.trim(dataid);
        if(dataid!=''){
            layer.confirm('确定删除选中的年级吗?',{icon:3,title:'操作提示'},function(index){
                var sindex=layer.load(1,{'time':3*1000});
                $.post("<?php echo url('Grade/grade_del'); ?>",{'grade_id':dataid},function(data){
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

    function grade_hide(dataid){
        if(dataid!=''){
            layer.confirm('隐藏后前端将不再显示，确定隐藏吗?',{icon:3,title:'操作提示'},function(index){
                var sindex=layer.load(1,{'time':3*1000});
                $.post("<?php echo url('Grade/grade_hide'); ?>",{'grade_id':dataid},function(data){
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

    function grade_show(dataid){
        if(dataid!=''){
            var sindex=layer.load(1,{'time':3*1000});
            $.post("<?php echo url('Grade/grade_show'); ?>",{'grade_id':dataid},function(data){
                layer.msg(data.msg);
                layer.close(sindex);
                if(data.code==200){
                    setTimeout("window.location.reload();",2000);
                }
            },'json')
            layer.close(index);
        }
    }

    function grade_search(){
        var url="<?php echo url('Grade/grade_list'); ?>?a=1";
        var keyword=$('.keyword').val();
        var school_id=$('.school_id').val();
        if(school_id!=''){
            url+='&school_id='+school_id;
        }
        if(keyword!=''){
            url+='&keyword='+keyword;
        }
        window.location.href=url;
    }

</script>

        <!-- <div class='mainfoot'>
            <div class='hasneworder' onclick="parent.xadmin.add_tab('预约订单列表','<?php echo url("Orders/orders_list"); ?>')">你有新的订单需要处理</div>
            <div class='hasnewchat' onclick="parent.xadmin.add_tab('客服消息列表','<?php echo url("Servicechat/servicechat_list"); ?>')">你有新的消息需要回复</div>
        </div> -->
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