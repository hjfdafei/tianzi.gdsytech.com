<?php
namespace app\api\controller;
use think\facade\Request;
use think\Controller;
use think\Db;
use think\db\Query;
use app\api\controller\Userbase;
use app\api\service\UserService;
use app\api\service\GenericService;
class User extends Userbase{
    public function _empty(){
        return json(['code'=>'0004','msg'=>'error']);
    }

    //获取小程序openid
    public function getMiniOpenid(){
        $code=input('code','','trim');
        if($code==''){
            return jsondata('0018','请选择允许小程序获取信息');
        }
        $userservice=new UserService();
        $res=$userservice->getOpenid($code);
        if($res['code']!=200){
            return jsondata('0018',$res['msg']);
        }
        $openid=string_authcode($res['openid'],'encode',config('app.codekey'));
        $info['openid']=$openid;
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //小程序登录
    public function miniLogin(){
        $openid=input('post.openid','','trim');
        $nickname=input('post.nickname','','trim');
        $avatar=input('post.avatar','','trim');
        $invite_code=input('invite_code','','trim');
        if($openid==''){
            return jsondata('0011','请选择允许小程序获取信息');
        }
        if($nickname==''){
            return jsondata('0011','请输入昵称');
        }
        if($avatar==''){
            return jsondata('0011','请选择微信头像');
        }
        $param=[];
        $param['openid']=$openid;
        $param['nickname']=$nickname;
        $param['avatar']=$avatar;
        $param['invite_code']=$invite_code;
        $userservice=new UserService();
        $res=$userservice->verifyUser($param);
        if($res['code']!=200){
            return jsondata('0019',$res['msg']);
        }
        $resdata=$res['data'];
        $status=1;
        if($resdata['realname']=='' || $resdata['mobile']==''){
            $status=2;
        }
        $info['token']=$resdata['token'];
        $info['nickname']=$resdata['nickname'];
        $info['avatar']=$resdata['avatar'];
        $info['realname']=$resdata['realname'];
        $info['mobile']=$resdata['mobile'];
        $info['status']=$status;
        $info['roletype']=$resdata['roletype'];
        $outdata['data']=$info;
        return jsondata('0001','获取成功',$outdata);
    }

    //检测是否登录
    public function checkLogin(){
        $token=session('webtoken');
        $url=input('url','','trim');
        if($url==''){
            return jsondata('0021','请输入跳转链接');
        }
        if((strpos($url,'?') === false)){
            $url.="?a=1";
        }
        $userinfo=[];
        session('fronturl',$url);
        if($token==''){
            $codeurl='https://open.weixin.qq.com/connect/oauth2/authorize';
            $query = array(
                'appid'         => config('app.miniappid'),
                'redirect_uri'  => config('app_host').'/APi/User/getWxCode',
                'response_type' => 'code',
                'scope'         => 'snsapi_userinfo',//snsapi_userinfo(读取信息) snsapi_base(静默授权)
                'state'=>mt_rand(1000,9999),
            );
            $query = http_build_query($query);
            $linkurl=$codeurl.'?'.$query.'#wechat_redirect';
            $info['linkurl']=$linkurl;
            $data['data']=$info;
            return jsondata('0019','未登录',$data);
        }
        $map=[];
        $map[]=['token','=',$token];
        $service=new UserService();
        $userinfo=$service->getUserInfo($map);
        if(empty($userinfo)){
            $codeurl='https://open.weixin.qq.com/connect/oauth2/authorize';
            $query = array(
                'appid'         => config('app.miniappid'),
                'redirect_uri'  => config('app_host').'/APi/User/getWxCode',
                'response_type' => 'code',
                'scope'         => 'snsapi_userinfo',//snsapi_userinfo(读取信息) snsapi_base(静默授权)
                'state'=>mt_rand(1000,9999),
            );
            $query = http_build_query($query);
            $linkurl=$codeurl.'?'.$query.'#wechat_redirect';
            $info['linkurl']=$linkurl;
            $data['data']=$info;
            return jsondata('0019','未登录',$data);
        }
        $info=[];
        $status=1;
        if($userinfo['realname']=='' || $userinfo['mobile']==''){
            $status=2;
        }
        $info['token']=$userinfo['token'];
        $info['nickname']=$userinfo['nickname'];
        $info['avatar']=$userinfo['avatar'];
        $info['realname']=$userinfo['realname'];
        $info['mobile']=$userinfo['mobile'];
        $info['status']=$status;
        $info['roletype']=$userinfo['roletype'];
        $info['linkurl']=session('fronturl');
        $outdata['data']=$info;
        return jsondata('0001','获取成功',$outdata);
    }

    //获取公众号授权链接返回的code
    public function getWxCode(){
        $url2=session('fronturl')==''?config('app.app_host')."/h5":session('fronturl');
        if((strpos($url2,'?') === false)){
            $url2.="?a=1";
        }
        session('fronturl',$url2);
        $urlparam=parse_url(session('fronturl'));
        $urldata=[];
        if(isset($urlparam['query'])){
            $urlquery=explode('&',trim($urlparam['query'],'&'));
            if(!empty($urlquery)){
                foreach($urlquery as $uv){
                    $uvarr=explode('=',$uv);
                    if(isset($uvarr[0]) && isset($uvarr[1])){
                        $urldata[$uvarr[0]]=$uvarr[1];
                    }
                }
            }
        }
        $invite_code=isset($urldata['invite_code'])?strtoupper($urldata['invite_code']):'';
        if(session('webtoken')!=''){
            $map=[];
            $map[]=['token','=',session('webtoken')];
            $service=new UserService();
            $userinfo=$service->getUserInfo($map);
            if(!empty($userinfo)){
                //header('Location: '.session('fronturl').'&token='.session('webtoken').'&roletype='.$userinfo['roletype']);
                $this->redirect(session('fronturl').'&token='.session('webtoken').'&roletype='.$userinfo['roletype'].'&invite_code='.$invite_code);
                exit;
            }
        }
        $code=input('code','','trim');
        if($code==''){
            return jsondata('0029','请选择允许获取信息');
        }
        $param=array(
            'appid'=>config('app.miniappid'),
            'secret'=>config('app.minisecret'),
            'code'=>$code,
            'grant_type'=>'authorization_code',
        );
        $url="https://api.weixin.qq.com/sns/oauth2/access_token";
        $tokeninfo=http_send($url,$param);
        $tokeninfo=json_decode($tokeninfo,true);
        if(empty($tokeninfo)){
            return jsondata('0029','获取信息失败,请重新授权登录');
        }
        if(isset($tokeninfo['errcode'])){
            return jsondata('0029','获取信息失败,请5分钟后再重新授权登录');
        }
        $openid=$tokeninfo['openid'];
        $access_token=$tokeninfo['access_token'];
        $query=array(
            'access_token'=>$access_token,
            'openid'=>$openid,
            'lang'=>'zh_CN',
        );
        $info=http_send("https://api.weixin.qq.com/sns/userinfo", $query);
        $info=json_decode($info,true);
        $param=[];
        $nickname='';
        $avatar='';
        $sex='';
        $city='';
        $province='';
        $country='';
        $param['openid']=string_authcode($openid,'encode',config('app.codekey'));
        if(!empty($info)){
            $nickname=$info['nickname'];
            $avatar=$info['headimgurl'];
            $sex=$info['sex'];
            $city=$info['city'];
            $province=$info['province'];
            $country=$info['country'];
        }
        $param['nickname']=$nickname;
        $param['avatar']=$avatar;
        $param['invite_code']=$invite_code;
        $param['sex']=$sex;
        $param['city']=$city;
        $param['province']=$province;
        $param['country']=$country;
        $userservice=new UserService();
        $res=$userservice->verifyUser($param);
        if($res['code']!=200){
            //header('Location: '.session('fronturl').'&token=&roletype=1');
            $this->redirect(session('fronturl').'&token=&roletype=1');
            exit;
            //return jsondata('0019',$res['msg']);
        }
        //header('Location: '.session('fronturl').'&token='.session('webtoken').'&roletype='.$userinfo['roletype']);
        $this->redirect(session('fronturl').'&token='.session('webtoken').'&roletype='.$res['data']['roletype'].'&invite_code='.$invite_code);
    }

    //用户信息详情
    public function userDetail(){
        $userInfo=$this->base_userinfo;
        if(empty($userInfo)){
            return jsondata('0011','用户信息不存在',[],$this->language);
        }
        $status=1;
        if($userInfo['realname']=='' || $userInfo['mobile']==''){
            $status=2;
        }
        $info['token']=$userInfo['token'];
        $info['nickname']=$userInfo['nickname'];
        $info['avatar']=$userInfo['avatar'];
        $info['realname']=$userInfo['realname'];
        $info['mobile']=$userInfo['mobile'];
        $info['status']=$status;
        $info['roletype']=$userInfo['roletype'];
        $outdata['data']=$info;
        return jsondata('0001','获取成功',$outdata);
    }

    //更新用户信息
    public function updateInfo(){
        if(request()->isPost() || request()->isAjax()){
            $realname=input('post.realname','','trim');
            $mobile=input('post.mobile','','trim');
            if($realname==''){
                return jsondata('0011','请输入姓名');
            }
            if($mobile==''){
                return jsondata('0011','请输入联系电话');
            }
            $checkmobile_res=checkformat_mobile($mobile);
            if($checkmobile_res['code']!='0001'){
                return jsondata('0012',$checkmobile_res['msg']);
            }
            $param=[];
            $param=[
                'token'=>$this->token,
                'realname'=>$realname,
                'mobile'=>$mobile
            ];
            $userservice=new UserService();
            $res=$userservice->updateUser($param);
            if($res['code']!=200){
                return jsondata('0027',$res['msg']);
            }
            $outdata['data']=$res['data'];
            return jsondata('0001',$res['msg'],$outdata);
        }
        return jsondata('0028','网络请求错误');
    }

    //用户收藏(取消收藏)名医
    public function user_doctor_favor(){
        if(request()->isPost() || request()->isAjax()){
            $doctor_id=input('post.doctor_id','0','intval');
            if($doctor_id<=0){
                return jsondata('0021','请选择名医');
            }
            $service=new UserService();
            $res=$service->userDoctorFavor($this->base_userinfo['id'],$doctor_id);
            if($res['code']==200){
                return jsondata('0001',$res['msg']);
            }else{
                return jsondata('0029',$res['msg']);
            }
        }
        return jsondata('0028','网络请求错误');
    }

    //用户收藏名医列表
    public function user_doctor_favorlist(){
        $keyword=input('keyword','','trim');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='d.id,d.realname,d.avatar,d.hospital_id,d.department_id,d.professional_titles,d.labels,d.skill,d.meetnum';
        $orderby=['f.favor_time'=>'desc','d.sortby'=>'desc'];
        $map=[];
        $map[]=['f.status','=',1];
        $map[]=['d.status','=',1];
        $map[]=['f.user_id','=',$this->base_userinfo['id']];
        $service=new UserService();
        $list=$service->userDoctorFavorlist($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $count=$list['count'];
        $gservice=new GenericService();
        $hospital_namearr=[];
        $department_namearr=[];
        if(!empty($listdata)){
            foreach($listdata as &$v){
                if($v['avatar']!=''){
                    $v['avatar']=$this->weburl.getabpath($v['avatar'],'upload');
                }
                $v['professional_titles']=explode('|',$v['professional_titles']);
                $v['labels']=explode('|',$v['labels']);
                $v['skill']=explode('|',$v['skill']);
                if($v['meetnum']<=0){
                    $v['meetnum']=$gservice->getDoctorMeetnum($v['id']);
                }
                $hospital_name='';
                $department_name='';
                if(isset($hospital_namearr[$v['hospital_id']])){
                    $hospital_name=$hospital_namearr[$v['hospital_id']];
                }else{
                    $hospital_namearr[$v['hospital_id']]=$gservice->attributeName($v['hospital_id']);
                    $hospital_name=$hospital_namearr[$v['hospital_id']];
                }
                if(isset($department_namearr[$v['department_id']])){
                    $department_name=$department_namearr[$v['department_id']];
                }else{
                    $department_namearr[$v['department_id']]=$gservice->attributeName($v['department_id']);
                    $department_name=$department_namearr[$v['department_id']];
                }
                $v['hospital_name']=$hospital_name;
                $v['department_name']=$department_name;
            }
        }
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //就诊人列表
    public function patient_list(){
        $keyword=input('keyword','','trim');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='id as patient_id,realname,mobile,content';
        $orderby=['id'=>'desc'];
        $map=[];
        $map[]=['user_id','=',$this->base_userinfo['id']];
        $service=new UserService();
        $list=$service->patientList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $count=$list['count'];
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //就诊人详情
    public function patient_detail(){
        $patient_id=input('patient_id','0','intval');
        if($patient_id<=0){
            return jsondata('0021','请选择就诊人');
        }
        $map=[];
        $map[]=['user_id','=',$this->base_userinfo['id']];
        $map[]=['id','=',$patient_id];
        $field='id as patient_id,realname,mobile,content';
        $service=new UserService();
        $info=$service->patientDetail($map,$field);
        if(empty($info)){
            return jsondata('400','就诊人信息不存在');
        }
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //添加就诊人
    public function patient_add(){
        if(request()->isPost() || request()->isAjax()){
            $realname=input('post.realname','','trim');
            $mobile=input('post.mobile','','trim');
            $content=input('post.content','','trim');
            if($realname==''){
                return jsondata('0021','请输入姓名');
            }
            if($mobile==''){
                return jsondata('0021','请输入联系电话');
            }
            $checkmobile_res=checkformat_mobile($mobile);
            if($checkmobile_res['code']!='0001'){
                return jsondata('0021',$checkmobile_res['msg']);
            }
            $param['realname']=$realname;
            $param['mobile']=$mobile;
            $param['content']=$content;
            $service=new UserService();
            $res=$service->patientVerify($this->base_userinfo['id'],0,$param);
            $data=[];
            if($res['code']==200){
                $data['list']=$res['data'];
                $code='0001';
            }else{
                $code='0028';
            }
            return jsondata($code,$res['msg'],$data);
        }
        return jsondata('0029','网络请求错误');
    }

    //修改就诊人
    public function patient_edit(){
        if(request()->isPost() || request()->isAjax()){
            $patient_id=input('post.patient_id','0','intval');
            $realname=input('post.realname','','trim');
            $mobile=input('post.mobile','','trim');
            $content=input('post.content','','trim');
            if($patient_id<=0){
                return jsondata('0021','请选择编辑信息');
            }
            if($realname==''){
                return jsondata('0021','请输入姓名');
            }
            if($mobile==''){
                return jsondata('0021','请输入联系电话');
            }
            $checkmobile_res=checkformat_mobile($mobile);
            if($checkmobile_res['code']!='0001'){
                return jsondata('0021',$checkmobile_res['msg']);
            }
            $param['realname']=$realname;
            $param['mobile']=$mobile;
            $param['content']=$content;
            $service=new UserService();
            $res=$service->patientVerify($this->base_userinfo['id'],$patient_id,$param);
            $data=[];
            if($res['code']==200){
                $data['list']=$res['data'];
                $code='0001';
            }else{
                $code='0028';
            }
            return jsondata($code,$res['msg'],$data);
        }
        return jsondata('0029','网络请求错误');
    }

    //删除就诊人
    public function patient_del(){
        if(request()->isPost() || request()->isAjax()){
            $patient_id=input('post.patient_id','','trim');
            $patient_id=trim(str_replace('，',',',$patient_id),',');
            if($patient_id==''){
                return jsondata('0021','请选择要删除的信息');
            }
            $patient_id=array_unique(explode(',',$patient_id));
            $service=new UserService();
            $res=$service->patientDelete($this->base_userinfo['id'],$patient_id);
            $code='0028';
            if($res['code']==200){
                $code='0001';
            }
            return jsondata($code,$res['msg']);
        }
        return jsondata('0029','网络请求错误');
    }

    //发送短信
    public function meeting_sendsms(){
        if(request()->isPost() || request()->isAjax()){
            $mobile=input('post.mobile','','trim');
            $checkmobile_res=checkformat_mobile($mobile);
            if($checkmobile_res['code']!='0001'){
                return jsondata('0021',$checkmobile_res['msg']);
            }
            $service=new UserService();
            $res=$service->createVerifyCode($mobile,1);
            $code='0023';
            if($res['code']==200){
                $code='0001';
            }
            return jsondata($code,$res['msg']);
        }
        return jsondata('0029','网络请求错误');
    }

    //预约名医
    public function meeting_add(){
        if(request()->isPost() || request()->isAjax()){
            $doctor_id=input('post.doctor_id','0','intval');
            $realname=input('post.realname','','trim');
            $mobile=input('post.mobile','','trim');
            $content=input('post.content','','trim');
            $verifycode=input('post.verifycode','','trim');
            $invitecode=input('post.invitecode','','trim');
            if($realname=='' || $mobile==''){
                return jsondata('0021','请填写预约就诊信息');
            }
            $checkmobile_res=checkformat_mobile($mobile);
            if($checkmobile_res['code']!='0001'){
                return jsondata('0021',$checkmobile_res['msg']);
            }
            if($verifycode==''){
                return jsondata('0022','请输入验证码');
            }
            if(strlen($verifycode)!=6){
                return jsondata('0022','验证码错误');
            }
            $param=[
                'doctor_id'=>$doctor_id,
                'realname'=>$realname,
                'mobile'=>$mobile,
                'content'=>$content,
                'basedoctor_id'=>$this->base_userinfo['basedoctor_id'],
                'verifycode'=>$verifycode,
                'invitecode'=>$invitecode,
            ];
            $service=new UserService();
            $res=$service->meetingVerify($this->base_userinfo['id'],$param);
            $code='0028';
            $data=[];
            if($res['code']==200){
                $code='0001';
                $data['data']=$res['data'];
            }
            return jsondata($code,$res['msg'],$data);
        }
        return jsondata('0029','网络请求错误');
    }

    //名医预约列表
    public function meeting_list(){
        $keyword=input('keyword','','trim');
        $status=input('status','0','intval');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='id,assistant_id,doctor_id,orderno,realname,mobile,money,ispay,create_time,status,content';
        $orderby=['id'=>'desc'];
        $map=[];
        $map[]=['user_id','=',$this->base_userinfo['id']];
        if($status==1){
            $map[]=['status','=',1];
        }elseif($status==2){
            $map[]=['status','in',[2,3]];
        }elseif($status==4){
            $map[]=['status','=',1];
        }elseif($status==5){
            $map[]=['status','=',1];
        }
        $service=new UserService();
        $list=$service->meetingList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        if(!empty($listdata)){
            $gservice=new GenericService();
            $doctorarr=[];
            $assistantarr=[];
            foreach($listdata as &$v){
                if(!isset($doctorarr[$v['doctor_id']])){
                    $dmap=[];
                    $dmap[]=['id','=',$v['doctor_id']];
                    $doctorinfo=$gservice->doctorDetail($dmap);
                }else{
                    $doctorinfo=$doctorarr[$v['doctor_id']];
                }
                if(!isset($assistantarr[$v['assistant_id']])){
                    $amap=[];
                    $amap[]=['id','=',$v['assistant_id']];
                    $assistantinfo=$gservice->assistantDetail($amap);
                }else{
                    $assistantinfo=$doctorarr[$v['assistant_id']];
                }
                $doctor_name='';
                $assistant_mobile='';
                $assistant_name='';
                if(!empty($doctorinfo)){
                    $doctor_name=$doctorinfo['realname'];
                }
                if(!empty($assistantinfo)){
                    $assistant_mobile=$assistantinfo['mobile'];
                    $assistant_name=$assistantinfo['realname'];
                }
                $v['doctor_name']=$doctor_name;
                $v['assistant_mobile']=$assistant_mobile;
                $v['assistant_name']=$assistant_name;
                $v['meeting_time']=date('Y-m-d',strtotime($v['create_time']));
                $v['money']=round($v['money']/100,2);
                // if($v['status']>1){
                //     if(in_array($v['status'],[2,3])){
                //         $v['status']=2;
                //     }
                // }
                if(in_array($v['status'],[2,3])){
                    $v['status']=2;
                }
                unset($v['assistant_id']);
                unset($v['create_time']);
            }
        }
        $count=$list['count'];
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //预约详情
    public function meeting_detail(){
        $meeting_id=input('meeting_id','0','intval');
        if($meeting_id<=0){
            return jsondata('0021','请选择预约信息');
        }
        $field='id,assistant_id,doctor_id,orderno,realname,mobile,content,status,money,pay_money,ispay,pay_time,create_time,finish_time';
        $map=[];
        $map[]=['user_id','=',$this->base_userinfo['id']];
        $map[]=['id','=',$meeting_id];
        $service=new UserService();
        $info=$service->meetingDetail($map,$field);
        if(empty($info)){
            return jsondata('0021','选择预约信息不存在');
        }
        $gservice=new GenericService();
        $dmap=[];
        $dmap[]=['id','=',$info['doctor_id']];
        $doctorinfo=$gservice->doctorDetail($dmap);
        $amap=[];
        $amap[]=['id','=',$info['assistant_id']];
        $assistantinfo=$gservice->assistantDetail($amap);
        $doctor_name='';
        $assistant_mobile='';
        $assistant_name='';
        if(!empty($doctorinfo)){
            $doctor_name=$doctorinfo['realname'];
        }
        if(!empty($assistantinfo)){
            $assistant_mobile=$assistantinfo['mobile'];
            $assistant_name=$assistantinfo['realname'];
        }
        // if($info['status']>1){
        //     $info['status']=2;
        // }

        //$info['meeting_time']=date('Y-m-d',strtotime($info['create_time']));
        $meeting_time=date('Y-m-d',strtotime($info['create_time']));
        $last_meeting_time='';
        if($info['status']==3){
            $meeting_time=date('Y-m-d',strtotime($info['finish_time']));
        }
        $map2=[];
        $map2[]=['user_id','=',$this->base_userinfo['id']];
        $map2[]=['realname','=',$info['realname']];
        $map2[]=['mobile','=',$info['mobile']];
        $lastinfo=$service->lastMeetingDetail($info['id'],$map2);
        if(!empty($lastinfo)){
            $last_meeting_time=date('Y-m-d',strtotime($lastinfo['create_time']));
        }
        if(in_array($info['status'],[2,3])){
            $info['status']=2;
        }
        $info['doctor_name']=$doctor_name;
        $info['assistant_mobile']=$assistant_mobile;
        $info['assistant_name']=$assistant_name;
        $info['meeting_time']=$meeting_time;
        $info['last_meeting_time']=$last_meeting_time;
        $info['money']=round($info['money']/100,2);
        $info['pay_money']=round($info['pay_money']/100,2);
        unset($info['assistant_id']);
        unset($info['create_time']);
        unset($info['finish_time']);
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //预约费用支付(返回jsapi支付参数)
    public function meeting_payfee(){
        return jsondata('0021','暂不支持线上支付');

        $meeting_id=input('meeting_id','0','intval');
        if($meeting_id<=0){
            return jsondata('0021','请选择预约信息');
        }
        $field='*';
        $map[]=['user_id','=',$this->base_userinfo['id']];
        $map[]=['id','=',$meeting_id];
        $service=new UserService();
        $info=$service->meetingDetail($map,$field);
        if(empty($info)){
            return jsondata('0021','选择预约信息不存在');
        }
        if($info['money']<=0){
            return jsondata('0021','订单还没设置费用');
        }
        if($info['ispay']==1){
            return jsondata('0021','订单已支付,无需重复支付');
        }
        if($info['status']==3){
            return jsondata('0021','订单已完成,无需重复支付');
        }
        if($info['status']==4){
            return jsondata('0021','订单正在退款中');
        }
        if($info['status']==5){
            return jsondata('0021','订单已关闭');
        }
        $payno=$service->payno_create();
        require_once WXPAYPATH.'WxPay.JsApiPay.php';
        $tools = new \JsApiPay();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("预约费用");
        $input->SetAttach('orderno='.$info['orderno'].'&pay_way=1');
        $input->SetOut_trade_no($payno);
        $input->SetTotal_fee($info['money']);
        $input->SetNotify_url(config('app_host').'/Api/Payment/sypayment_notify');
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($this->base_userinfo['openid']);
        $config = new \WxPayConfig();
        $order = \WxPayApi::unifiedOrder($config, $input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        $jsApiParameters=json_decode($jsApiParameters,true);
        $data['data']=$jsApiParameters;
        DB::name('orders')->where([['id','=',$info['id']],['user_id','=',$this->base_userinfo['id']]])->update(['payno'=>$payno,'update_time'=>date('Y-m-d H:i:s')]);
        return jsondata('0001','获取成功',$data);
    }

    //申请退款
    public function meeting_refundfee(){
        return jsondata('0021','暂不支持退款');

        if(request()->isPost() || request()->isAjax()){
            $meeting_id=input('post.meeting_id','0','intval');
            if($meeting_id<=0){
                return jsondata('0021','请选择预约信息');
            }
            $service=new UserService();
            $res=$service->RefundFee($this->base_userinfo,$meeting_id);
            if($res['code']==200){
                return jsondata('0001',$res['msg']);
            }else{
                return jsondata('0025',$res['msg']);
            }
        }
        return jsondata('0028','网络请求错误');
    }

    //意见反馈
    public function user_suggest(){
        if(request()->isPost() || request()->isAjax()){
            $content=input('post.content','','trim');
            if($content==''){
                return jsondata('400','请填写反馈内容');
            }
            $param=[
                'content'=>$content,
            ];
            $service=new UserService();
            $res=$service->suggestVerify($this->base_userinfo['id'],$param);
            $code='0028';
            if($res['code']==200){
                $code='0001';
            }
            return jsondata($code,$res['msg']);
        }
        return jsondata('0029','网络请求错误');
    }

    //意见反馈列表
    public function user_suggest_list(){
        $keyword=input('keyword','','trim');
        $status=input('status','0','intval');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='id,content,status,reply_time,reply_content,create_time';
        $orderby=['id'=>'desc'];
        $map=[];
        $map[]=['user_id','=',$this->base_userinfo['id']];
        $map[]=['type','=',1];
        $service=new UserService();
        $list=$service->suggestList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $count=$list['count'];
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //客服聊天
    public function service_chat(){
        if(request()->isPost() || request()->isAjax()){
            $content=input('post.content','','trim');
            $img=input('post.img','','trim');
            if($content=='' && $img==''){
                return jsondata('400','请填写内容或者上传图片');
            }
            $param=[
                'content'=>$content,
                'img'=>$img,
            ];
            $service=new UserService();
            $res=$service->serviceChatVerify($this->base_userinfo['id'],$param);
            $code='0028';
            if($res['code']==200){
                $code='0001';
            }
            return jsondata($code,$res['msg']);
        }
        return jsondata('0029','网络请求错误');
    }

    //客服聊天列表
    public function service_chatlist(){
        $keyword=input('keyword','','trim');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=30;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='id,type,msgtype,content,create_time';
        $orderby=['id'=>'desc'];
        $map=[];
        $map[]=['user_id','=',$this->base_userinfo['id']];
        $service=new UserService();
        $list=$service->serviceChatList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $tmpdata=[];
        for($i=count($listdata);$i>0;$i--){
            $tmpdata[]=$listdata[($i-1)];
        }
        if(!empty($tmpdata)){
            foreach($tmpdata as &$v){
                if($v['msgtype']==2){
                    if($v['content']!=''){
                        $v['content']=$this->weburl.getabpath($v['content'],'upload');
                    }
                }
            }
        }
        $count=$list['count'];
        $data['list']=$tmpdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }
}
