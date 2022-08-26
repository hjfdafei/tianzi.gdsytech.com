<?php /*a:2:{s:97:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\index\setting.html";i:1630036323;s:95:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\layout\main.html";i:1626942442;}*/ ?>
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
        <title>系统管理后台</title>
    </head>
    <body>
        
        
        
<body class="index">
    <div class="setting_form">
        <form class='layui-form dataform' enctype="multipart/form-data" method="post" id='settingform'>
            <table class="layui-table">
                <tr>
                    <td class='td_right'><label class="layui-form-label">办理指引<span class='musttip'>*</span></label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <textarea name="guide" id='guide' placeholder="办理指引" class="layui-textarea guide"><?php echo htmlentities($webconfig['guide']); ?></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class='td_right'><label class="layui-form-label">办理条款<span class='musttip'>*</span></label></td>
                    <td class='td_left'>
                        <div class="layui-input-block">
                            <textarea name="content" id='content' placeholder="办理条款" class="layui-textarea content"><?php echo htmlentities($webconfig['content']); ?></textarea>
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

        
        
    <style>
        .mapsearch{padding-top:10px;}
        .amap-icon img, .amap-marker-content img {width:19px;height:33px;}
    </style>
    <script type="text/javascript">
        tinymce.init({
            'selector':'#content,#guide',
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
            'convert_urls':false,
            'upload_image_url':"<?php echo url('sytechadmin/upload/file_upload',array('type'=>'attach')); ?>"
        });
        $('#settingform').bind('form-pre-serialize', function(event, form, options, veto) { tinyMCE.triggerSave(); });
    </script>
    <script type="text/javascript">
        layui.use(['laydate','form','table','upload'], function(){
            var laydate = layui.laydate;
            var table = layui.table;
            var form = layui.form;
            var upload=layui.upload;
            form.on('submit(savedata_subbtn)', function(data){
                savedata();
                return false;
            });
        })

        //保存数据
        function savedata(){
            var sindex=layer.load(1,{time:5*1000});
            $('#settingform').ajaxSubmit({
                url:"<?php echo url('Index/setting'); ?>",
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
                        setTimeout("window.location.reload()",2000)
                    }
                }
            });
            return false;
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