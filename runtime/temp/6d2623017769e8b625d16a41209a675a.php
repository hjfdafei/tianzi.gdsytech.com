<?php /*a:3:{s:98:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\user\user_edit.html";i:1626335889;s:95:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\layout\main.html";i:1626334813;s:100:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\user\user_footer.html";i:1616135144;}*/ ?>
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
        <title>管理后台--修改用户信息</title>
    </head>
    <body>
        
        
        
<body class="index">
    <div class="setting_form">
        <form class='dataform layui-form' enctype="multipart/form-data" method="post" id='goodsform'>
            <table class="layui-table">
                <tr>
                    <td class='td_right'><label class="layui-form-label">姓名<span class='musttip'>*</span></label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='realname' id="realname" placeholder="姓名" autocomplete="off" class="layui-input realname" value='<?php echo htmlentities($info['realname']); ?>' />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">联系电话<span class='musttip'>*</span></label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='mobile' id="mobile" placeholder="联系电话" autocomplete="off" class="layui-input mobile" value='<?php echo htmlentities($info['mobile']); ?>' />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">身份证号码</label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='idcardnum' id="idcardnum" placeholder="身份证号码" autocomplete="off" class="layui-input idcardnum" value='<?php echo htmlentities($info['idcardnum']); ?>' />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">院系</label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='department' id="department" placeholder="院系" autocomplete="off" class="layui-input department" value='<?php echo htmlentities($info['department']); ?>' />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">学号</label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='studentnumber' id="studentnumber" placeholder="学号" autocomplete="off" class="layui-input studentnumber" value='<?php echo htmlentities($info['studentnumber']); ?>' />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">宿舍地址</label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <input type="text" name='address' id="address" placeholder="宿舍地址" autocomplete="off" class="layui-input address" value='<?php echo htmlentities($info['address']); ?>' />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: center">
                        <div class="layui-input-block">
                            <input type="hidden" name="userid" value='<?php echo htmlentities($info['id']); ?>' />
                            <button class="layui-btn" lay-submit lay-filter="savedata_editbtn" id='savedata_editbtn'>提交</button>
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
            var url="<?php echo url('User/user_edit'); ?>";
            savedata(url);
            return false;
        });
        form.on('select(getrolelist)', function(data){
            getrolelist(data.value,form);
            return false;
        });
        form.on('submit(savedata_setbtn)', function(data){
            var url="<?php echo url('User/user_setrole'); ?>";
            user_setingrole(url);
            return false;
        });
        form.on('submit(savedata_bindbtn)', function(data){
            var url="<?php echo url('User/user_bind'); ?>";
            user_binding(url);
            return false;
        });

    })

    function user_edit(dataid){
        if(dataid>0){
            var url='<?php echo url("User/user_edit"); ?>?userid='+dataid;
            var title='修改用户信息';
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
                var realname=$.trim($('.realname').val());
                if(realname==''){
                    layer.msg('请输入用户姓名');
                    layer.close(sindex);
                    return false;
                }
                var mobile=$.trim($('.mobile').val());
                if(mobile==''){
                    layer.msg('请用户联系电话');
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

    function user_setrole(dataid){
        if(dataid>0){
            var url='<?php echo url("User/user_setrole"); ?>?userid='+dataid;
            var title='设置用户角色';
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

    function getrolelist(dataid,obj){
        if(dataid>=0){
            $.post("<?php echo url('Index/getUserRoleList'); ?>",{'type':dataid},function(res){
                if(res.code==200){
                    var str="";
                    var resdata=res.data.list;
                    for(var i=0;i<resdata.length;i++){
                        str+="<option value='"+resdata[i]['id']+"' title='"+dataid+"'>"+resdata[i]['title']+"</option>";
                    }
                    $('.roleid').empty().append(str);
                    obj.render('select');
                }else{
                    layer.msg(res.msg);
                }
            },'json')
        }
    }

    function user_setingrole(url){
        var sindex=layer.load(1,{time:5*1000});
        $('#goodsform').ajaxSubmit({
            url:url,
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

    function user_bind(dataid){
        if(dataid>0){
            var url='<?php echo url("User/user_bind"); ?>?userid='+dataid;
            var title='绑定基层医生/推广员';
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

    function user_binding(url){
        var sindex=layer.load(1,{time:5*1000});
        $('#goodsform').ajaxSubmit({
            url:url,
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

    function user_patient(dataid){
        if(dataid>0){
            var url='<?php echo url("User/user_patient"); ?>?userid='+dataid;
            var title='用户就诊人列表';
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

    function user_patient_search(dataid){
        var url="<?php echo url('User/user_patient'); ?>?userid="+dataid;
        var keyword=$('.keyword').val();
        if(keyword!=''){
            url+='&keyword='+keyword;
        }
        window.location.href=url;
    }

    function user_search(){
        var url="<?php echo url('User/user_list'); ?>?a=1";
        var keyword=$('.keyword').val();
        var basedoctorsid=$('.basedoctorsid').val();
        var roletype=$('.roletype').val();
        if(basedoctorsid!=''){
            url+='&basedoctorsid='+basedoctorsid;
        }
        if(roletype!=''){
            url+='&roletype='+roletype;
        }
        if(keyword!=''){
            url+='&keyword='+keyword;
        }
        window.location.href=url;
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