<?php /*a:3:{s:110:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\orders\orders_setbroadband.html";i:1626858329;s:95:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\layout\main.html";i:1626334813;s:104:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\orders\orders_footer.html";i:1626858312;}*/ ?>
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
        <title>管理后台--设置订单宽带信息</title>
    </head>
    <body>
        
        
        
<body class="index">
    <div class="setting_form">
        <form class='dataform layui-form' enctype="multipart/form-data" method="post" id='goodsform'>
            <table class="layui-table">
                <tr>
                    <td class='td_right'><label class="layui-form-label">宽带账号<span class='musttip'>*</span></label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='keyaccount' id="keyaccount" placeholder="宽带账号" autocomplete="off" class="layui-input keyaccount" value='<?php echo htmlentities($info['keyaccount']); ?>' /><span class="layui-btn layui-btn-sm" onclick="orders_getrandaccount(<?php echo htmlentities($info['school_id']); ?>);">随机获取账号</span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">宽带密码<span class='musttip'>*</span></label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='keypassword' id="keypassword" placeholder="宽带密码" autocomplete="off" class="layui-input keypassword" value='<?php echo htmlentities($info['keypassword']); ?>' />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">开始时间</label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='applytime_start' id="applytime_start" placeholder="开始时间" autocomplete="off" class="layui-input applytime_start" value='<?php echo htmlentities($info['start_time']); ?>' />
                        </div>
                        <span class='inputnote_span'>(宽带有效期开始时间)</span>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">结束时间</label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='applytime_end' id="applytime_end" placeholder="结束时间" autocomplete="off" class="layui-input applytime_end" value='<?php echo htmlentities($info['end_time']); ?>' />
                        </div>
                        <span class='inputnote_span'>(宽带有效期结束时间)</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center">
                        <div class="layui-input-block">
                            <input type="hidden" name="ordersid" value='<?php echo htmlentities($info['id']); ?>' />
                            <button class="layui-btn" lay-submit lay-filter="savedata_setbtn" id='savedata_setbtn'>提交</button>
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
        var uploadInst = upload.render({
            elem: '#img',
            auto:false,
            //bindAction:'#savedata_subbtn',
            choose: function(obj){
                obj.preview(function(index, file, result){
                    $('#preimg_view').attr('src', result);
                });
            }
        });
        form.on('submit(savedata_editbtn)', function(data){
            var url="<?php echo url('Orders/orders_edit'); ?>";
            savedata_edit(url);
            return false;
        });
        form.on('submit(savedata_setbtn)', function(data){
            var url="<?php echo url('Orders/orders_setbroadband'); ?>";
            savedata_setting(url);
            return false;
        });
        form.on('submit(savedata_settimebtn)', function(data){
            var url="<?php echo url('Orders/orders_settime'); ?>";
            savedata_settingtime(url);
            return false;
        });
        laydate.render({
            'elem':'#applytime_start',
            'trigger':'click',
            'type':'datetime',
            'value':''
        });
        laydate.render({
            'elem':'#applytime_end',
            'trigger':'click',
            'type':'datetime',
            'value':''
        });

    })

    function orders_detail(dataid){
        var url='<?php echo url("Orders/orders_detail"); ?>?ordersid='+dataid;
        var title='订单详情';
        layer.open({
            type: 2,
            title:title,
            shadeClose: false,
            shade: 0.8,
            area: ['95%', '90%'],
            content: url
        });
    }

    function orders_edit(dataid){
        var url='<?php echo url("Orders/orders_edit"); ?>?ordersid='+dataid;
        var title='修改宽带订单登记信息';
        layer.open({
            type: 2,
            title:title,
            shadeClose: false,
            shade: 0.8,
            area: ['95%', '90%'],
            content: url
        });
    }

    function savedata_edit(url){
        var sindex=layer.load(1,{time:5*1000});
        $('#goodsform').ajaxSubmit({
            url:url,
            type:'post',
            dataType:'json',
            beforeSubmit: function(){
                var realname=$.trim($('.realname').val());
                if(realname==''){
                    layer.close(sindex);
                    layer.msg('请输入姓名');
                    return false;
                }
                var mobile=$.trim($('.mobile').val());
                if(mobile==''){
                    layer.close(sindex);
                    layer.msg('请输入联系电话');
                    return false;
                }
                var idcardnum=$.trim($('.idcardnum').val());
                if(idcardnum==''){
                    layer.close(sindex);
                    layer.msg('请输入身份证号码');
                    return false;
                }
                var department=$.trim($('.department').val());
                if(department==''){
                    layer.close(sindex);
                    layer.msg('请输入院系');
                    return false;
                }
                var studentnumber=$.trim($('.studentnumber').val());
                if(studentnumber==''){
                    layer.close(sindex);
                    layer.msg('请输入学号');
                    return false;
                }
                var address=$.trim($('.address').val());
                if(address==''){
                    layer.close(sindex);
                    layer.msg('请输入宿舍地址');
                    return false;
                }
                var money=$.trim($('.money').val());
                if(money<=0){
                    layer.close(sindex);
                    layer.msg('请输入正确金额');
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

    function orders_settime(dataid){
        var url='<?php echo url("Orders/orders_settime"); ?>?ordersid='+dataid;
        var title='设置宽带时间';
        layer.open({
            type: 2,
            title:title,
            shadeClose: false,
            shade: 0.8,
            area: ['95%', '90%'],
            content: url
        });
    }

    function savedata_settingtime(url){
        var sindex=layer.load(1,{time:5*1000});
        $('#goodsform').ajaxSubmit({
            url:url,
            type:'post',
            dataType:'json',
            beforeSubmit: function(){
                var applytime_start=$.trim($('.applytime_start').val());
                if(applytime_start==''){
                    layer.close(sindex);
                    layer.msg('请输入设置宽带生效开始时间');
                    return false;
                }
                var applytime_end=$.trim($('.applytime_end').val());
                if(applytime_end==''){
                    layer.close(sindex);
                    layer.msg('请输入设置宽带生效结束时间');
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

    function orders_setbroadband(dataid){
        var url='<?php echo url("Orders/orders_setbroadband"); ?>?ordersid='+dataid;
        var title='设置宽带账号';
        layer.open({
            type: 2,
            title:title,
            shadeClose: false,
            shade: 0.8,
            area: ['95%', '90%'],
            content: url
        });
    }

    function savedata_setting(url){
        var sindex=layer.load(1,{time:5*1000});
        $('#goodsform').ajaxSubmit({
            url:url,
            type:'post',
            dataType:'json',
            beforeSubmit: function(){
                var keyaccount=$.trim($('.keyaccount').val());
                if(keyaccount==''){
                    layer.close(sindex);
                    layer.msg('请输入宽带账号');
                    return false;
                }
                var keypassword=$.trim($('.keypassword').val());
                if(keypassword==''){
                    layer.close(sindex);
                    layer.msg('请输入宽带密码');
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

    function orders_getrandaccount(school_id){

        $.post("<?php echo url('Index/getRandAccount'); ?>",{'school_id':school_id},function(data){
            if(data.code==200){
                $('.keyaccount').val(data.data.data.keyaccount)
                $('.keypassword').val(data.data.data.keypassword);
            }else{
                layer.msg(data.msg);
            }
        },'json')
    }

    function orders_clearbroadband(dataid){
        if(dataid!=''){
            layer.confirm('确定清空订单的宽带信息吗?',{icon:3,title:'操作提示'},function(index){
                var sindex=layer.load(1,{'time':3*1000});
                $.post("<?php echo url('Orders/orders_clearbroadband'); ?>",{'ordersid':dataid},function(data){
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

    function orders_search(){
        var url="<?php echo url('Orders/orders_list'); ?>?a=1";
        var school_id=$('.school_id').val();
        if(school_id!=''){
            url+='&school_id='+school_id;
        }
        var goods_id=$('.goods_id').val();
        if(goods_id!=''){
            url+='&goods_id='+goods_id;
        }
        var status=$('.status').val();
        if(status!=''){
            url+='&status='+status;
        }
        var orderno=$('.orderno').val();
        if(orderno!=''){
            url+='&orderno='+orderno;
        }
        var keyword=$('.keyword').val();
        if(keyword!=''){
            url+='&keyword='+keyword;
        }
        var applytime_start=$('.applytime_start').val();
        if(applytime_start!=''){
            url+='&applytime_start='+applytime_start;
        }
        var applytime_end=$('.applytime_end').val();
        if(applytime_end!=''){
            url+='&applytime_end='+applytime_end;
        }
        window.location.href=url;
    }

    function orders_export(){
        var url="<?php echo url('Orders/orders_export'); ?>?a=1";
        var school_id=$('.school_id').val();
        if(school_id!=''){
            url+='&school_id='+school_id;
        }
        var goods_id=$('.goods_id').val();
        if(goods_id!=''){
            url+='&goods_id='+goods_id;
        }
        var status=$('.status').val();
        if(status!=''){
            url+='&status='+status;
        }
        var orderno=$('.orderno').val();
        if(orderno!=''){
            url+='&orderno='+orderno;
        }
        var keyword=$('.keyword').val();
        if(keyword!=''){
            url+='&keyword='+keyword;
        }
        var applytime_start=$('.applytime_start').val();
        if(applytime_start!=''){
            url+='&applytime_start='+applytime_start;
        }
        var applytime_end=$('.applytime_end').val();
        if(applytime_end!=''){
            url+='&applytime_end='+applytime_end;
        }
        window.location.href=url;
    }

    function orders_del(dataid=''){
        if(dataid==''){
            $("[name='checkgoods[]']:checked").each(function(){
                dataid+=$(this).val()+',';
            })
        }
        dataid=$.trim(dataid);
        if(dataid==''){
            layer.msg('请选择需要删除的订单');
            return false;
        }
        if(dataid!=''){
            layer.confirm('确定删除选中的订单信息吗?',{icon:3,title:'操作提示'},function(index){
                var sindex=layer.load(1,{'time':3*1000});
                $.post("<?php echo url('Orders/orders_del'); ?>",{'ordersid':dataid},function(data){
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