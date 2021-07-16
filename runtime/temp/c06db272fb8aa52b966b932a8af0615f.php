<?php /*a:3:{s:102:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\orders\orders_list.html";i:1626434584;s:95:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\layout\main.html";i:1626334813;s:104:"E:\webenv\apache2.4.39\htdocs\tianzi.gdsytech.com\application\sytechadmin\view\orders\orders_footer.html";i:1626430125;}*/ ?>
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
        <title>管理后台--订单列表</title>
    </head>
    <body>
        
        
        
<body class="index">
    <div class="layui-card">
        <div class="layui-card-body">
            <div style='height:10px;'></div>
            <div class="layui-row">
                <div class="layui-col-md12">
                    <button class="layui-btn layui-btn-danger" onclick="orders_del();">删除订单</button>
                </div>
            </div>
            <div style='height:20px;'></div>
                <form class="layui-form" enctype="multipart/form-data" method="post" id='searchform'>
                    <div class="layui-row">
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">校区</label>
                                <div class="layui-input-block">
                                    <select name="school_id" class='school_id'>
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
                                <label class="layui-form-label">宽带套餐</label>
                                <div class="layui-input-block">
                                    <select name="goods_id" class='goods_id'>
                                        <option value='0'>全部</option>
                                        <?php if(is_array($goods_list) || $goods_list instanceof \think\Collection || $goods_list instanceof \think\Paginator): $i = 0; $__LIST__ = $goods_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                        <option value='<?php echo htmlentities($vo['id']); ?>' <?php if($search['goods_id']==$vo['id']): ?>selected='selected'<?php endif; ?>><?php echo htmlentities($vo['goods_title']); ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">业务状态</label>
                                <div class="layui-input-block">
                                    <select name="status" class='status'>
                                        <option value='0' <?php if($search['status']==0): ?>selected='selected'<?php endif; ?>>全部</option>
                                        <option value='2' <?php if($search['status']==2): ?>selected='selected'<?php endif; ?>>已支付</option>
                                        <option value='1' <?php if($search['status']==1): ?>selected='selected'<?php endif; ?>>未支付</option>
                                        <option value='3' <?php if($search['status']==3): ?>selected='selected'<?php endif; ?>>已发放</option>
                                        <option value='4' <?php if($search['status']==4): ?>selected='selected'<?php endif; ?>>已取消</option>
                                        <option value='5' <?php if($search['status']==5): ?>selected='selected'<?php endif; ?>>取消中</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">订单号</label>
                                <div class="layui-input-block">
                                    <input type="text" id="orderno" name="orderno" placeholder="订单号" autocomplete="off" class="layui-input orderno" value='<?php echo htmlentities($search['orderno']); ?>'>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-row">
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">关键词</label>
                                <div class="layui-input-block">
                                    <input type="text" id="keyword" name="keyword" placeholder="姓名|电话|身份证号码" autocomplete="off" class="layui-input keyword" value='<?php echo htmlentities($search['keyword']); ?>'>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">下单日期</label>
                                <div class="layui-input-block">
                                    <input type="text" id="applytime_start" name="applytime_start" placeholder="开始下单日期" autocomplete="off" class="layui-input applytime_start" readonly="readonly" value='<?php echo htmlentities($search['applytime_start']); ?>'>
                                </div>
                            </div>
                        </div>
                        <div class="layui-col-md3">
                            <div class="layui-form-item">
                                <label class="layui-form-label">下单日期</label>
                                <div class="layui-input-block">
                                    <input type="text" id="applytime_end" name="applytime_end" placeholder="结束下单日期" autocomplete="off" class="layui-input applytime_end" readonly="readonly" value='<?php echo htmlentities($search['applytime_end']); ?>'>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="layui-row">
                        <label class="layui-form-label"></label>
                        <span class="layui-btn" onclick="orders_search()">搜索</span>
                        <span class="layui-btn layui-btn-warm" onclick="orders_export()">导出订单</span>
                        <a style="margin-left:20px;" class="layui-btn layui-btn-normal" href="<?php echo url('Orders/orders_list'); ?>">刷新</a>
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
                <div class='layui-row' style='overflow: auto;'>
                    <table class="layui-table layui-form" id="goods_table">
                        <tr class='table_tr'>
                            <th style='min-width:20px;'><input type="checkbox" class="checkbox_all" lay-filter="choose_all" lay-skin="primary" class='goods_checkbox'></th>
                            <th>所在校区</th>
                            <th>订单号</th>
                            <th>报装信息</th>
                            <th>宽带套餐</th>
                            <th>宽带信息</th>
                            <th>业务状态</th>
                            <th>支付状态</th>
                            <th>支付金额</th>
                            <th>下单时间</th>
                            <th>操作</th>
                        </tr>
                        <?php if(is_array($list) || $list instanceof \think\Collection || $list instanceof \think\Paginator): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                        <tr class='table_tr'>
                            <td style='min-width:20px;'><input type="checkbox" name="checkgoods[]" lay-filter="choose_single" lay-skin="primary" class='goods_checkbox' value='<?php echo htmlentities($vo['id']); ?>'></td>
                            <td><?php echo htmlentities($vo['schoolname']); ?></td>
                            <td><?php echo htmlentities($vo['orderno']); ?></td>
                            <td>
                                <span style='color:#337ab7;display:block;'>姓名:<?php echo htmlentities($vo['realname']); ?></span>
                                <span style='color:#337ab7;display:block;'>电话:<?php echo htmlentities($vo['mobile']); ?></span>
                                <span style='color:#337ab7;display:block;'>身份证号码:<?php echo htmlentities($vo['idcardnum']); ?></span>
                                <span style='color:#337ab7;display:block;'>院系:<?php echo htmlentities($vo['department']); ?></span>
                                <span style='color:#337ab7;display:block;'>学号:<?php echo htmlentities($vo['studentnumber']); ?></span>
                                <span style='color:#337ab7;display:block;'>宿舍地址:<?php echo htmlentities($vo['address']); ?></span>
                            </td>
                            <td>
                                <span style='color:#337ab7;display:block;'>套餐名称:<?php echo htmlentities($vo['goods_title']); ?></span>
                                <span style='color:#337ab7;display:block;'>套餐金额:<?php echo htmlentities($vo['money']); ?></span>
                            </td>
                            <td>
                                <span style='color:#337ab7;display:block;'>宽带账号:<?php echo htmlentities($vo['keyaccount']); ?></span>
                                <span style='color:#337ab7;display:block;'>宽带密码:<?php echo htmlentities($vo['keypassword']); ?></span>
                            </td>
                            <td><?php echo htmlentities($vo['statusname']); ?></td>
                            <td>
                                <?php echo htmlentities($vo['ispayname']); if($vo['ispay']!=2): ?><span style='color:#337ab7;display:block;'>支付时间:<?php echo htmlentities($vo['pay_time']); ?></span><?php endif; ?>
                            </td>
                            <td>
                                <span style='color:#337ab7;display:block;'>应付金额:<?php echo htmlentities($vo['money']); ?></span>
                                <span style='color:#337ab7;display:block;'>折扣金额:<?php echo htmlentities($vo['discount_money']); ?></span>
                                <span style='color:#337ab7;display:block;'>实付金额:<?php echo htmlentities($vo['pay_money']); ?></span>
                            </td>
                            <td><?php echo htmlentities($vo['create_time']); ?></td>
                            <td>
                                <a class="layui-btn layui-btn-sm" onclick="orders_detail(<?php echo htmlentities($vo['id']); ?>)">详情</a>
                                <a class="layui-btn layui-btn-warm layui-btn-sm" onclick="orders_setsettle(<?php echo htmlentities($vo['id']); ?>)">修改</a>
                                <a class="layui-btn layui-btn-normal layui-btn-sm" onclick="orders_setintegral(<?php echo htmlentities($vo['id']); ?>)">分配宽带账号</a>
                                <a class="layui-btn layui-btn-danger layui-btn-sm" onclick="orders_setintegral(<?php echo htmlentities($vo['id']); ?>)">清空宽带账号</a>
                            </td>
                        </tr>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                        <tr>
                            <td colspan="14" class='page_wrap'><span class='page_count'>共<font><?php echo htmlentities($count); ?></font>条记录</span><?php echo $page; ?></td>
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
        form.on('submit(savedata_assignbtn)', function(data){
            var url="<?php echo url('Orders/orders_assign'); ?>";
            savedata_assiging(url);
            return false;
        });
        form.on('submit(savedata_feebtn)', function(data){
            var url="<?php echo url('Orders/orders_setfee'); ?>";
            savedata_setfeeing(url);
            return false;
        });
        form.on('submit(savedata_integralbtn)', function(data){
            var url="<?php echo url('Orders/orders_setintegral'); ?>";
            savedata_setintegraling(url);
            return false;
        });

        form.on('submit(savedata_refusebtn)', function(data){
            var url="<?php echo url('Orders/orders_refund_refuse'); ?>";
            savedata_refusing(url);
            return false;
        });
        form.on('submit(savedata_agreebtn)', function(data){
            var url="<?php echo url('Orders/orders_refund_agree'); ?>";
            savedata_agreeing(url);
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

    function orders_assign(dataid){
        var url='<?php echo url("Orders/orders_assign"); ?>?ordersid='+dataid;
        var title='分配名医';
        layer.open({
            type: 2,
            title:title,
            shadeClose: false,
            shade: 0.8,
            area: ['95%', '90%'],
            content: url
        });
    }

    function savedata_assiging(url){
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

    function orders_setfee(dataid){
        var url='<?php echo url("Orders/orders_setfee"); ?>?ordersid='+dataid;
        var title='设置费用';
        layer.open({
            type: 2,
            title:title,
            shadeClose: false,
            shade: 0.8,
            area: ['95%', '90%'],
            content: url
        });
    }

    function savedata_setfeeing(url){
        var sindex=layer.load(1,{time:5*1000});
        $('#goodsform').ajaxSubmit({
            url:url,
            type:'post',
            dataType:'json',
            beforeSubmit: function(){
                var money=$.trim($('.money').val());
                if(money<=0 || money==''){
                    layer.close(sindex);
                    layer.msg('请设置费用');
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

    function orders_close(dataid){
        if(dataid==''){
            $("[name='checkgoods[]']:checked").each(function(){
                dataid+=$(this).val()+',';
            })
        }
        dataid=$.trim(dataid);
        if(dataid==''){
            layer.msg('请选择订单');
            return false;
        }
        if(dataid!=''){
            layer.confirm('确定关闭选择的订单吗?',{icon:3,title:'操作提示'},function(index){
                var sindex=layer.load(1,{'time':3*1000});
                $.post("<?php echo url('Orders/orders_close'); ?>",{'ordersid':dataid},function(data){
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

    function orders_setfinish(dataid=''){
        if(dataid==''){
            $("[name='checkgoods[]']:checked").each(function(){
                dataid+=$(this).val()+',';
            })
        }
        if(dataid==''){
            layer.msg('请选择订单');
            return false;
        }
        dataid=$.trim(dataid);
        if(dataid!=''){
            layer.confirm('确定完成选择的订单吗?',{icon:3,title:'操作提示'},function(index){
                var sindex=layer.load(1,{'time':3*1000});
                $.post("<?php echo url('Orders/orders_setfinish'); ?>",{'ordersid':dataid},function(data){
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

    function orders_setintegral(dataid=''){
        if(dataid==''){
            $("[name='checkgoods[]']:checked").each(function(){
                dataid+=$(this).val()+',';
            })
        }
        dataid=$.trim(dataid);
        if(dataid==''){
            layer.msg('请选择订单');
            return false;
        }
        var url='<?php echo url("Orders/orders_setintegral"); ?>?ordersid='+dataid;
        var title='设置积分';
        layer.open({
            type: 2,
            title:title,
            shadeClose: false,
            shade: 0.8,
            area: ['95%', '90%'],
            content: url
        });
    }

    function savedata_setintegraling(url){
        var sindex=layer.load(1,{time:5*1000});
        $('#goodsform').ajaxSubmit({
            url:url,
            type:'post',
            dataType:'json',
            beforeSubmit: function(){
                var integral=$.trim($('.integral').val());
                if(integral<0 || integral==''){
                    layer.close(sindex);
                    layer.msg('请设置积分');
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

    function orders_setsettle(dataid=''){
        if(dataid==''){
            $("[name='checkgoods[]']:checked").each(function(){
                dataid+=$(this).val()+',';
            })
        }
        dataid=$.trim(dataid);
        if(dataid==''){
            layer.msg('请选择订单');
            return false;
        }
        if(dataid!=''){
            layer.confirm('确定结算选择的订单吗?',{icon:3,title:'操作提示'},function(index){
                var sindex=layer.load(1,{'time':3*1000});
                $.post("<?php echo url('Orders/orders_setsettle'); ?>",{'ordersid':dataid},function(data){
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
        var doctorid=$('.doctorid').val();
        if(doctorid!=''){
            url+='&doctorid='+doctorid;
        }
        var assistantsid=$('.assistantsid').val();
        if(assistantsid!=''){
            url+='&assistantsid='+assistantsid;
        }
        var basedoctorsid=$('.basedoctorsid').val();
        if(basedoctorsid!=''){
            url+='&basedoctorsid='+basedoctorsid;
        }
        var isassign=$('.isassign').val();
        if(isassign!=''){
            url+='&isassign='+isassign;
        }
        var issettle=$('.issettle').val();
        if(issettle!=''){
            url+='&issettle='+issettle;
        }
        var issetintegral=$('.issetintegral').val();
        if(issetintegral!=''){
            url+='&issetintegral='+issetintegral;
        }
        var ispay=$('.ispay').val();
        if(ispay!=''){
            url+='&ispay='+ispay;
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

    function orders_refund_search(){
        var url="<?php echo url('Orders/orders_refundlist'); ?>?a=1";
        var status=$('.status').val();
        if(status!=''){
            url+='&status='+status;
        }
        var orderno=$('.orderno').val();
        if(orderno!=''){
            url+='&orderno='+orderno;
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

    function orders_refund_agree(dataid){
        var url='<?php echo url("Orders/orders_refund_agree"); ?>?refundid='+dataid;
        var title='同意退款';
        layer.open({
            type: 2,
            title:title,
            shadeClose: false,
            shade: 0.8,
            area: ['95%', '90%'],
            content: url
        });
    }

    function savedata_agreeing(url){
        var sindex=layer.load(1,{time:5*1000});
        $('#goodsform').ajaxSubmit({
            url:url,
            type:'post',
            dataType:'json',
            beforeSubmit: function(){
                var money=$.trim($('.money').val());
                if(money<=0 || money==''){
                    layer.close(sindex);
                    layer.msg('请输入退款金额');
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

    function orders_refund_refuse(dataid){
        var url='<?php echo url("Orders/orders_refund_refuse"); ?>?refundid='+dataid;
        var title='拒绝退款';
        layer.open({
            type: 2,
            title:title,
            shadeClose: false,
            shade: 0.8,
            area: ['95%', '90%'],
            content: url
        });
    }

    function savedata_refusing(url){
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