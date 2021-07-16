/*!
 * common.js
 * gsystem common js by gawie 2016-04-09 09:49:42
 */


//加载框
$.loading = function (type)
{
    switch(type)
    {
        case 'show':
            $.toast({text:'loading...[<span aria-hidden="true">&times;</span>]',type:'danger',position:'center','timeout':'30000'}).show();
        break;

        case 'hide':
            $('.toast').remove();
        break;

        default:
            alert('$.loading.error!');
        break;

    }
};

 

//警告框
$.galert = function (text,type,url)
{
    switch(type)
    {
        default:
            $.alert({
                title:'提示：',
                body:text,
                transition:false,
                shown:function(e){
                    //回车执行
                    $('.modal-footer button').focus();
                },
            });
    }
};

/*确认*/
$.gconfirm = function (text,type)
{
    switch(type)
    {
        default:
            if(confirm(text) == true){
                return true;
            }else{
                return false;
            }
    }
};

/*连接*/
$.href = function (url,type)
{
    switch(type)
    {
        default:
            return window.location.href=url; 
    }
};



//ajax表单提交
$.gprompt = function (parme,data)
{
    var data = data || {};
    $.loading('show');
    $.ajax({
        type:"GET",
        url:parme.url,
        data:data,
        dataTyple:'html',
        success:function (html)
        {
            $.loading('hide');
            $.alert({
                'title':'提示：',
                'body' : html,
                'transition':false,
                'width' :'large',
                'hasfoot':false,
                 hidden:   function(e) {
                    if (parme.reload) {window.location.reload()};
                 }
            });

        }
    }); 
};

//ajax表单提交
$.gprompt_large  = function (parme,data)
{
    var data = data || {};
    $.loading('show');
    $.ajax({
        type:"GET",
        url:parme.url,
        data:data,
        dataTyple:'html',
        success:function (html)
        {
            $.loading('hide');
            $.alert({
                'transition':false,
                'title':'提示：',
                'body' : html,
                'hasfoot':false,
                'width' :'1100',
            });

        }
    }); 
};

//ajax提交
function ajax_request(url, params, type)
{
    if (!$.gconfirm('是否确认操作？')) {
        return false;
    };

    $.loading('show');
    if (typeof (params) != 'undefined')
    {
        $.post(url, params, function (result)
        {
            //$.hideIndicator();
            if (!result)
            {
                return false;
            }
            
            if (result.sts != 1)
            {
                alert(result.msg);
            }
            else if (result.sts == 1 && result.url)
            {
                window.location = decodeURIComponent(result.url);
            }
            else
            {
                window.location.reload();
            }
        }, 'json').error(function (error)
        {
            $.loading('hide');
            if ($.trim(error.responseText) != '')
            {
                $.alert('Error:' + ' ' + error.responseText);
            }
        });
    }
    else
    {
        $.get(url, function (result)
        {
            if (result.sts == 1) {
                $.alert({
                    'title' :'操作提示',
                    'body'  :result.msg,
                    transition:false,
                    backdrop: 'static',
                    okHidden: function(e) {
                        // styli 2017-01-09
                        // 解决删除按钮隐藏在TABLE的UL中
                        window.location = decodeURIComponent(result.url);
                    },
                    shown:function(e){
                        $('.modal-footer button').focus();
                    }
                });            
            }
            else
            {
                $.galert(result.msg);
            }
        }, 'json').error(function (error)
        {
            $.loading('hide');
            if ($.trim(error.responseText) != '')
            {
                $.alert('Error:' + ' ' + error.responseText);
            }
        });
    }
    $.loading('hide');
    return false;
}

/*=============================================
 *  
 *  jQuery validation extend
 *  
 =============================================*/

$.validator.addMethod( "phoneUS", function( phone_number, element ) {
    phone_number = phone_number.replace( /\s+/g, "" );
    return this.optional( element ) || phone_number.length > 17 &&
        phone_number.match( /^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}(\d|x|X)$/);
}, "Please specify a valid icard" );

$.validator.addMethod( "phoneCN", function( value, element ) {
    return this.optional( element ) || value.match(/^1[3456789][0-9]{9}$/) || value.match(/^0\d{2,3}-\d{7,8,9}(-\d{1,6})?$/);
}, "请输入正确的中国大陆手机号码" );

$.validator.addMethod( "phoneAU", function( value, element ) {
    return this.optional( element ) || value.match(/^0[0-9]{9}$/);
}, "请输入正确的澳洲手机号码" );

/*=============================================
 *  
 *  jQuery validation submit
 *  
 =============================================*/

//ajax_submit
function ajax_post(formEl,sys_alert){

    formEl.validate({     
        submitHandler: function(form){    
            $.loading('show');
            //提交按钮状态
            var obj =$(form).find(':submit');
            obj.addClass('disabled');
            var ajaxData = $(form).serialize();
            ajaxData = ajaxData.replace(/["']/g, "")
            //console.log(ajaxData);
            $.ajax({
                type: 'POST',
                url:$(form).attr('action'),
                data:ajaxData,
                dataType: 'json',
                success: function(data){
                    $.loading('hide');
                    if (sys_alert == 1) {
                        ajax_post_processer_sys_alert(data,obj);
                    }
                    else
                    {
                        ajax_post_processer(data,obj);
                    }
                   obj.removeClass('disabled');
                },
                error: function(xhr, type){
                    $.loading('hide');
                    $.galert('数据传输错误!');
                    obj.removeClass('disabled');
                },
                shown:function(e){
                    //回车执行
                    $('.modal-footer button').focus();
                }
            })
        }  
    }).form();
}

function ajax_post_processer(result,obj)
{
    if (typeof (result.sts) == 'undefined')
    {
        $.galert(result.msg);
    }
    else
    {
        if (result.sts  == '1'  && result.msg && result.url && result.msg != 'undefined')
        {
            $.alert({
                'title' :'标题：',
                'body'  :result.msg,
                transition:false,
                backdrop: 'static',
                okHidden: function(e) {
                    window.location = decodeURIComponent(result.url);
                },
                shown:function(e){
                    //回车执行
                    //obj.removeClass('disabled');
                    $('.modal-footer button').focus();
                }
            });            
        }
        else if(result.sts  == '1' && result.url){
            window.location = decodeURIComponent(result.url);
        }
        else
        {
            //$.toast(result.msg, "danger", "center");
            $.galert(result.msg);
        }
    }
    
    return false;
} 


function ajax_post_processer_sys_alert(result,obj)
{
    if (typeof (result.sts) == 'undefined')
    {
        alert(result.msg);
    }
    else
    {
        if (result.sts  == '1'  && result.msg && result.url && result.msg != 'undefined')
        {
            alert(result.msg);
            window.location = decodeURIComponent(result.url);     
        }
        else if(result.sts  == '1' && result.url){
            window.location = decodeURIComponent(result.url);
        }
        else
        {
            alert(result.msg);
        }
    }
    
    return false;
} 