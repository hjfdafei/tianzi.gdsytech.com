<?php /*a:3:{s:108:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\broadband\broadband_list.html";i:1661415441;s:95:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\layout\main.html";i:1626942442;s:110:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\broadband\broadband_footer.html";i:1629281566;}*/ ?>
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
        <title>管理后台--宽带账号列表</title>
    </head>
    <body>
        
        
        
<body class="index">
    <div class="layui-card">
        <div class="layui-card-body">
            <div style='height:10px;'></div>
            <div class="layui-row">
                <div class="layui-col-md12">
                    <button class="layui-btn layui-btn" onclick="broadband_add()">新增宽带账号</button>
                    <button class="layui-btn layui-btn-warm" onclick="broadband_import();">导入宽带账号</button>
                    <button class="layui-btn layui-btn-danger" onclick="broadband_del();">删除宽带账号</button>
                </div>
            </div>
            <div style='height:20px;'></div>
                <form class="layui-form" enctype="multipart/form-data" method="post" id='searchform'>
                    <div class="layui-row">
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">关键词</label>
                                <div class="layui-input-block">
                                    <input type="text" id="keyword" name="keyword" placeholder="宽带账号" autocomplete="off" class="layui-input keyword" value='<?php echo htmlentities($search['keyword']); ?>'>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">所属校区</label>
                                <div class="layui-input-block">
                                    <select name="school_id" class='school_id' lay-search>
                                        <option value='0'>全部</option>
                                        <?php if(is_array($school_list) || $school_list instanceof \think\Collection || $school_list instanceof \think\Paginator): $i = 0; $__LIST__ = $school_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                        <option value='<?php echo htmlentities($vo['id']); ?>' <?php if($search['school_id']==$vo['id']): ?>selected='selected'<?php endif; ?>><?php echo htmlentities($vo['title']); ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">使用状态</label>
                                <div class="layui-input-block">
                                    <select name='usestatus' class='usestatus'>
                                        <option value='0'>全部</option>
                                        <option value='1' <?php if($search['usestatus']==1): ?>selected<?php endif; ?>>已使用</option>
                                        <option value='2' <?php if($search['usestatus']==2): ?>selected<?php endif; ?>>未使用</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">状态</label>
                                <div class="layui-input-block">
                                    <select name='status' class='status'>
                                        <option value='0'>全部</option>
                                        <option value='1' <?php if($search['status']==1): ?>selected<?php endif; ?>>正常</option>
                                        <option value='2' <?php if($search['status']==2): ?>selected<?php endif; ?>>禁用</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-row">
                        <label class="layui-form-label"></label>
                        <span class="layui-btn" onclick="broadband_search()">搜索</span>
                        <a style="margin-left:20px;" class="layui-btn layui-btn-normal" href="<?php echo url('Broadband/broadband_list'); ?>">刷新</a>
                    </div>
                </form>
                <?php if(count($list)<=0): ?>
                    <table class="layui-table layui-form" id="goods_table">
                        <tr>
                            <td style='text-align:center;'>暂无数据</td>
                        </tr>
                    </table>
                <?php else: ?>
                <div style='height:10px;'></div>
                <div class='layui-row'>
                    <table class="layui-table layui-form" id="goods_table">
                        <tr class='table_tr'>
                            <th style='min-width:20px;'><input type="checkbox" class="checkbox_all" lay-filter="choose_all" lay-skin="primary" class='goods_checkbox'></th>
                            <th>所属校区</th>
                            <th>宽带账号</th>
                            <th>宽带密码</th>
                            <th>使用状态</th>
                            <th>状态</th>
                            <th>添加时间</th>
                            <th>操作</th>
                        </tr>
                        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr class='table_tr'>
                            <td style='min-width:20px;'><input type="checkbox" name="checkgoods[]" lay-filter="choose_single" lay-skin="primary" class='goods_checkbox' value='<?php echo htmlentities($vo['id']); ?>'></td>
                            <td><?php echo htmlentities($vo['schoolname']); ?></td>
                            <td><?php echo htmlentities($vo['keyaccount']); ?></td>
                            <td><?php echo htmlentities($vo['keypassword']); ?></td>
                            <td><?php echo htmlentities($vo['usename']); ?></td>
                            <td><?php echo htmlentities($vo['statusname']); ?></td>
                            <td><?php echo htmlentities($vo['create_time']); ?></td>
                            <td>
                                <?php if($vo['status']==1): ?>
                                    <a class="layui-btn layui-btn-normal layui-btn-sm" onclick="broadband_hide(<?php echo htmlentities($vo['id']); ?>)">禁用</a>
                                <?php else: ?>
                                    <a class="layui-btn layui-btn-normal layui-btn-sm" onclick="broadband_show(<?php echo htmlentities($vo['id']); ?>)">启用</a>
                                <?php endif; ?>
                                <a class="layui-btn layui-btn-warm layui-btn-sm" onclick="broadband_edit(<?php echo htmlentities($vo['id']); ?>)">修改</a>
                            </td>
                        </tr>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                        <tr>
                            <td colspan="10" class='page_wrap'><span class='page_count'>共<font><?php echo htmlentities($count); ?></font>条记录</span><?php echo $page; ?></td>
                        </tr>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

        
        
<script type="text/javascript">
    function closealllayer(){
        layer.closeAll();
        window.location.reload();
    }
</script>
<script type="text/javascript">
    layui.use(['laydate','form','table','upload'], function(){
        var laydate=layui.laydate;
        var table=layui.table;
        var form=layui.form;
        var upload=layui.upload;
        form.on('submit(savedata_addbtn)', function(data){
            var url="<?php echo url('Broadband/broadband_add'); ?>";
            savedata(url);
            return false;
        });
        form.on('submit(savedata_editbtn)', function(data){
            var url="<?php echo url('Broadband/broadband_edit'); ?>";
            savedata(url);
            return false;
        });
        var uploadInst = upload.render({
            elem: '#uploadfiles',
            auto:false,
            exts: 'xls|xlsx|csv',
            choose: function(obj){
                obj.preview(function(index, file, result){
                    $('.prefile_content').html(file.name);
                });
            }
        });

        form.on('submit(savedata_uploadbtn)',function(data){
            savedata_upload();
            return false;
        })
    })

    function broadband_add(){
        var url='<?php echo url("Broadband/broadband_add"); ?>';
        var title='添加宽带账号';
        layer.open({
            type: 2,
            title:title,
            shadeClose: false,
            shade: 0.8,
            area: ['95%', '90%'],
            content: url
        });
    }

    function broadband_edit(dataid){
        if(dataid>0){
            var url='<?php echo url("Broadband/broadband_edit"); ?>?broadbandid='+dataid;
            var title='修改宽带账号';
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
                var keyaccount=$.trim($('.keyaccount').val());
                if(keyaccount==''){
                    layer.msg('请输入宽带账号');
                    layer.close(sindex);
                    return false;
                }
                var keypassword=$.trim($('.keypassword').val());
                if(keypassword==''){
                    layer.msg('请输入宽带密码');
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

    //导入宽带账号
    function broadband_import(){
        var url='<?php echo url("Broadband/broadband_import"); ?>';
        layer.open({
            type: 2,
            title:'导入宽带账号',
            shadeClose: false,
            shade: 0.8,
            area: ['95%', '90%'],
            content: url
        });
    }

    function savedata_upload(){
        var sindex=layer.load(1,{time:5*1000});
        $('#goodsform').ajaxSubmit({
            url:"<?php echo url('Broadband/broadband_import'); ?>",
            type:'post',
            dataType:'json',
            beforeSubmit: function(){
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

    function broadband_del(){
        var dataid='';
        $("[name='checkgoods[]']:checked").each(function(){
            dataid+=$(this).val()+',';
        })
        dataid=$.trim(dataid);
        if(dataid!=''){
            layer.confirm('确定删除选中的宽带账号吗?',{icon:3,title:'操作提示'},function(index){
                var sindex=layer.load(1,{'time':3*1000});
                $.post("<?php echo url('Broadband/broadband_del'); ?>",{'broadbandid':dataid},function(data){
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

    function broadband_hide(dataid){
        if(dataid!=''){
            layer.confirm('禁用后宽带账号将不能分配，确定禁用吗?',{icon:3,title:'操作提示'},function(index){
                var sindex=layer.load(1,{'time':3*1000});
                $.post("<?php echo url('Broadband/broadband_hide'); ?>",{'broadbandid':dataid},function(data){
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

    function broadband_show(dataid){
        if(dataid!=''){
            var sindex=layer.load(1,{'time':3*1000});
            $.post("<?php echo url('Broadband/broadband_show'); ?>",{'broadbandid':dataid},function(data){
                layer.msg(data.msg);
                layer.close(sindex);
                if(data.code==200){
                    setTimeout("window.location.reload();",2000);
                }
            },'json')
            layer.close(index);
        }
    }

    function broadband_search(){
        var url="<?php echo url('Broadband/broadband_list'); ?>?a=1";
        var keyword=$('.keyword').val();
        var school_id=$('.school_id').val();
        var usestatus=$('.usestatus').val();
        var status=$('.status').val();
        if(keyword!=''){
            url+='&keyword='+keyword;
        }
        if(school_id!=''){
            url+='&school_id='+school_id;
        }
        if(usestatus!=''){
            url+='&usestatus='+usestatus;
        }
        if(status!=''){
            url+='&status='+status;
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