<?php /*a:3:{s:100:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\goods\goods_list.html";i:1661510055;s:95:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\layout\main.html";i:1626942442;s:102:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\goods\goods_footer.html";i:1661509710;}*/ ?>
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
        <title>管理后台--宽带套餐列表</title>
    </head>
    <body>
        
        
        
<body class="index">
    <div class="layui-card">
        <div class="layui-card-body">
            <div style='height:10px;'></div>
            <div class="layui-row">
                <div class="layui-col-md12">
                    <button class="layui-btn layui-btn" onclick="goods_add()">新增宽带套餐</button>
                    <button class="layui-btn layui-btn-danger" onclick="goods_del();">删除宽带套餐</button>
                </div>
            </div>
            <div style='height:20px;'></div>
                <form class="layui-form" enctype="multipart/form-data" method="post" id='searchform'>
                    <div class="layui-row">
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">校区</label>
                                <div class="layui-input-block">
                                    <select name="school_id" class='school_id' lay-filter="getgrade" lay-search>
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
                                <label class="layui-form-label">年级</label>
                                <div class="layui-input-block">
                                    <select name="grade_id" class='grade_id' lay-search>
                                        <option value='0'>全部年级</option>
                                        <?php if(is_array($grade_list) || $grade_list instanceof \think\Collection || $grade_list instanceof \think\Paginator): $i = 0; $__LIST__ = $grade_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                        <option value='<?php echo htmlentities($vo['id']); ?>' <?php if($search['grade_id']==$vo['id']): ?>selected='selected'<?php endif; ?>><?php echo htmlentities($vo['title']); ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">关键词</label>
                                <div class="layui-input-block">
                                    <input type="text" id="keyword" name="keyword" placeholder="宽带套餐标题" autocomplete="off" class="layui-input keyword" value='<?php echo htmlentities($search['keyword']); ?>'>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">状态</label>
                                <div class="layui-input-block">
                                    <select name='status' class='status'>
                                        <option value='0'>全部</option>
                                        <option value='1' <?php if($search['status']==1): ?>selected<?php endif; ?>>上架</option>
                                        <option value='2' <?php if($search['status']==2): ?>selected<?php endif; ?>>下架</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-row">
                        <label class="layui-form-label"></label>
                        <span class="layui-btn" onclick="goods_search()">搜索</span>
                        <a style="margin-left:20px;" class="layui-btn layui-btn-normal" href="<?php echo url('Goods/goods_list'); ?>">刷新</a>
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
                            <th>标题</th>
                            <th>封面图</th>
                            <th>价格</th>
                            <th>已售数量</th>
                            <th>状态</th>
                            <th>添加时间</th>
                            <th>操作</th>
                        </tr>
                        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr class='table_tr'>
                            <td style='min-width:20px;'><input type="checkbox" name="checkgoods[]" lay-filter="choose_single" lay-skin="primary" class='goods_checkbox' value='<?php echo htmlentities($vo['id']); ?>'></td>
                            <td><?php echo htmlentities($vo['schoolname']); if($vo['gradename']!=''): ?>-<?php echo htmlentities($vo['gradename']); ?><?php endif; ?></td>
                            <td><?php echo htmlentities($vo['goods_title']); ?></td>
                            <td><?php if($vo['goods_img']!=''): ?><a href='<?php echo htmlentities($vo['goods_img']); ?>' target="_blank"><img src='<?php echo htmlentities($vo['goods_img']); ?>' height='50'/></a><?php else: ?>暂未上传<?php endif; ?></td>
                            <td><?php echo htmlentities($vo['goods_price']); ?></td>
                            <td><?php echo htmlentities($vo['sale_num']); ?></td>
                            <td><?php echo htmlentities($vo['statusname']); ?></td>
                            <td><?php echo htmlentities($vo['create_time']); ?></td>
                            <td>
                                <?php if($vo['goods_status']==1): ?>
                                    <a class="layui-btn layui-btn-normal layui-btn-sm" onclick="goods_hide(<?php echo htmlentities($vo['id']); ?>)">下架</a>
                                <?php else: ?>
                                    <a class="layui-btn layui-btn-normal layui-btn-sm" onclick="goods_show(<?php echo htmlentities($vo['id']); ?>)">上架</a>
                                <?php endif; ?>
                                <a class="layui-btn layui-btn-warm layui-btn-sm" onclick="goods_edit(<?php echo htmlentities($vo['id']); ?>)">修改</a>
                                <a class="layui-btn layui-btn-sm" onclick="goods_orders_list(<?php echo htmlentities($vo['id']); ?>)">订单列表</a>
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
    tinymce.init({
        'selector':'#goods_content',
        'language':'zh_CN',
        'width':'100%',
        'height':'500px',
        'resize':false,
        'plugins': 'print preview searchreplace autolink directionality visualblocks visualchars fullscreen uploadimage link media template code codesample table charmap hr pagebreak nonbreaking anchor insertdatetime advlist lists wordcount imagetools textpattern help powerpaste emoticons autosave',
        'toolbar':
            'code undo redo restoredraft | cut copy paste pastetext | forecolor backcolor bold italic underline strikethrough link anchor | alignleft aligncenter alignright alignjustify outdent indent | \
            styleselect formatselect fontselect fontsizeselect | bullist numlist | blockquote subscript superscript removeformat | \
            table uploadimage media charmap emoticons hr pagebreak insertdatetime print preview | fullscreen',
        'fontsize_formats': '12px 14px 16px 18px 24px 36px 48px 56px 72px',
        'font_formats': '微软雅黑=Microsoft YaHei,Helvetica Neue,PingFang SC,sans-serif;苹果苹方=PingFang SC,Microsoft YaHei,sans-serif;宋体=simsun,serif;仿宋体=FangSong,serif;黑体=SimHei,sans-serif;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats;知乎配置=BlinkMacSystemFont, Helvetica Neue, PingFang SC, Microsoft YaHei, Source Han Sans SC, Noto Sans CJK SC, WenQuanYi Micro Hei, sans-serif;小米配置=Helvetica Neue,Helvetica,Arial,Microsoft Yahei,Hiragino Sans GB,Heiti SC,WenQuanYi Micro Hei,sans-serif',
        'template_cdate_format':'[CDATE: %m/%d/%Y : %H:%M:%S]',
        'template_mdate_format':'[MDATE: %m/%d/%Y : %H:%M:%S]',
        'image_caption': true,
        'upload_image_url':"<?php echo url('sytechadmin/upload/file_upload',array('type'=>'attach')); ?>"
    });
    $('#goodsform').bind('form-pre-serialize', function(event, form, options, veto) { tinyMCE.triggerSave(); });
</script>
<script type="text/javascript">
    layui.use(['laydate','form','table','upload'], function(){
        var laydate=layui.laydate;
        var table=layui.table;
        var form=layui.form;
        var upload=layui.upload;
        var uploadInst = upload.render({
            elem: '#goods_img',
            auto:false,
            //bindAction:'#savedata_subbtn',
            choose: function(obj){
                obj.preview(function(index, file, result){
                    $('#preimg_view').attr('src', result);
                });
            }
        });
        form.on('select(getgrade)',function(data){
            getgrade(data.value,form);
        })
        form.on('submit(savedata_addbtn)', function(data){
            var url="<?php echo url('Goods/goods_add'); ?>";
            savedata(url);
            return false;
        });
        form.on('submit(savedata_editbtn)', function(data){
            var url="<?php echo url('Goods/goods_edit'); ?>";
            savedata(url);
            return false;
        });
    })

    function getgrade(dataid,obj){
        if(dataid>0){
            $.post("<?php echo url('Index/getgrade'); ?>",{'dataid':dataid},function(res){
                if(res.code==200){
                    var str='';
                    var resdata=res.data.data;
                    for(var i=0;i<resdata.length;i++){
                        clistr+="<option value='"+resdata[i]['id']+"' title='"+dataid+"'>"+resdata[i]['title']+"</option>";
                    }
                    $('.grade_id').empty().append(asrstr);
                    obj.render('select');
                }else{
                    var str="<option value='0'>全部年级</option>";
                    $('.grade_id').empty().append(str);
                    obj.render('select');
                }
            },'json')
        }else{
            var str="<option value='0'>全部年级</option>";
            $('.grade_id').empty().append(str);
            obj.render('select');
        }
    }

    function goods_add(){
        var url='<?php echo url("Goods/goods_add"); ?>';
        var title='添加宽带套餐';
        layer.open({
            type: 2,
            title:title,
            shadeClose: false,
            shade: 0.8,
            area: ['95%', '90%'],
            content: url
        });
    }

    function goods_edit(dataid){
        if(dataid>0){
            var url='<?php echo url("Goods/goods_edit"); ?>?goodsid='+dataid;
            var title='修改宽带套餐';
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
                var goods_title=$.trim($('.goods_title').val());
                if(goods_title==''){
                    layer.msg('请输入宽带套餐标题');
                    layer.close(sindex);
                    return false;
                }
                var goods_price=$.trim($('.goods_price').val());
                if(goods_price==''){
                    layer.msg('请输入宽带套餐价格');
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

    function goods_orders_list(dataid){
        if(dataid>0){
            var url='<?php echo url("Orders/orders_list"); ?>?goods_id='+dataid;
            var title='宽带套餐订单列表';
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

    function goods_del(){
        var dataid='';
        $("[name='checkgoods[]']:checked").each(function(){
            dataid+=$(this).val()+',';
        })
        dataid=$.trim(dataid);
        if(dataid!=''){
            layer.confirm('确定删除选中的宽带套餐吗?',{icon:3,title:'操作提示'},function(index){
                var sindex=layer.load(1,{'time':3*1000});
                $.post("<?php echo url('Goods/goods_del'); ?>",{'goodsid':dataid},function(data){
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

    function goods_hide(dataid){
        if(dataid!=''){
            layer.confirm('下架后前端将不再显示，确定下架吗?',{icon:3,title:'操作提示'},function(index){
                var sindex=layer.load(1,{'time':3*1000});
                $.post("<?php echo url('Goods/goods_hide'); ?>",{'goodsid':dataid},function(data){
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

    function goods_show(dataid){
        if(dataid!=''){
            var sindex=layer.load(1,{'time':3*1000});
            $.post("<?php echo url('Goods/goods_show'); ?>",{'goodsid':dataid},function(data){
                layer.msg(data.msg);
                layer.close(sindex);
                if(data.code==200){
                    setTimeout("window.location.reload();",2000);
                }
            },'json')
            layer.close(index);
        }
    }

    function goods_search(){
        var url="<?php echo url('Goods/goods_list'); ?>?a=1";
        var keyword=$('.keyword').val();
        var status=$('.status').val();
        var school_id=$('.school_id').val();
        if(status!=''){
            url+='&status='+status;
        }
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