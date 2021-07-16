<?php
namespace app\api\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\api\service\QrcodeService;
use app\api\service\GenericService;
//用户管理
class UserService extends Base{
    //获取小程序openid
    public function getOpenid($code){
        $url='https://api.weixin.qq.com/sns/jscode2session';
        $param['grant_type']='authorization_code';
        $param['appid']=config('app.miniappid');
        $param['secret']=config('app.minisecret');
        $param['js_code']=$code;
        $res=json_decode(http_send($url,$param,'','GET'),true);
        //$res['openid']='189521fgfgrtyud1';
        if(!isset($res['openid'])){
            return ['code'=>'400','msg'=>'获取小程序信息失败,请重试'];
        }
        if($res['openid']==''){
            return ['code'=>'400','msg'=>'获取小程序信息失败,请重试'];
        }
        return ['code'=>'200','openid'=>$res['openid']];
    }

    //会员注册登录
    public function verifyUser($param){
        $openid=$param['openid'];
        $nickname=preg_replace('/[\x{10000}-\x{10FFFF}]/u','', $param['nickname']);//$param['nick_name']
        $avatar=$param['avatar'];
        $invite_code=$param['invite_code'];
        $sex=$param['sex'];
        $city=$param['city'];
        $province=$param['province'];
        $country=$param['country'];
        $openid=string_authcode($openid,'decode',config('app.codekey'));
        if($openid==''){
            return ['code'=>'400','msg'=>'请重新登录'];
        }
        $map=[];
        $map[]=['openid','=',$openid];
        $userinfo=$this->getUserInfo($map);
        $token=md5(time().$openid.rand_string('',8,4));
        $data=[];
        $data=[
            'openid'=>$openid,
            'token'=>$token,
            'nickname'=>$nickname,
            'avatar'=>$avatar,
            'sex'=>$sex,
            'city'=>$city,
            'province'=>$province,
            'country'=>$country,
        ];
        if(empty($userinfo)){
            if($invite_code!=''){
                $bdmap=[];
                $bdmap[]=['invite_code','=',$invite_code];
                $bdmap[]=['status','=',1];
                $gservice=new GenericService();
                $basedoctorinfo=$gservice->basedoctorDetail($bdmap);
                if(!empty($basedoctorinfo)){
                    $data['basedoctor_id']=$basedoctorinfo['id'];
                    $data['bind_time']=date('Y-m-d H:i:s');
                    $data['bind_way']=1;
                }
            }
            $data['create_time']=date('Y-m-d H:i:s');
            DB::name('user')->insertGetId($data);
        }else{
            $data['update_time']=date('Y-m-d H:i:s');
            DB::name('user')->where([['id','=',$userinfo['id']]])->update($data);
        }
        $map=[];
        $map[]=['token','=',$token];
        $newUserInfo=$this->getUserInfo($map);
        if(empty($newUserInfo)){
            return ['code'=>'400','msg'=>'请重新登录','data'=>[]];
        }
        session('webtoken',$token);
        return ['code'=>'200','msg'=>'登录成功','data'=>$newUserInfo];
    }

    //用户详情
    public function getUserInfo($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('user')->field($field)->where($map)->find();
    }

    //完善会员信息
    public function updateUser($param){
        $token=$param['token'];
        $realname=$param['realname'];
        $mobile=$param['mobile'];
        $map=[];
        $map[]=['token','=',$token];
        $userInfo=$this->getUserInfo($map);
        if(empty($userInfo)){
            return ['code'=>'400','msg'=>'用户信息不存在'];
        }
        $hasmap=[];
        $hasmap[]=['mobile','=',$mobile];
        $hasUser=$this->getUserInfo($hasmap);
        if(!empty($hasUser)){
            if($hasUser['id']!=$userInfo['id']){
                return ['code'=>'400','msg'=>'联系电话已存在'];
            }
        }
        $updateData=[
            'mobile'=>$mobile,
            'realname'=>$realname,
            'update_time'=>date('Y-m-d H:i:s'),
        ];
        $upmap=[];
        $upmap[]=['id','=',$userInfo['id']];
        $res=DB::name('user')->where($upmap)->update($updateData);
        if($res){
            $info=[];
            $info['token']=$userInfo['token'];
            $info['nickname']=$userInfo['nickname'];
            $info['avatar']=$userInfo['avatar'];
            $info['realname']=$realname;
            $info['mobile']=$mobile;
            $info['status']=1;
            $info['roletype']=$userInfo['roletype'];
            return ['code'=>'200','msg'=>'更新信息成功','data'=>$info];
        }else{
            return ['code'=>'400','msg'=>'更新信息失败,请重试','data'=>[]];
        }
    }

    //收藏/取消收藏名医
    public function userDoctorFavor($user_id,$doctor_id){
        $gservice=new GenericService();
        $map=[];
        $map[]=['user_id','=',$user_id];
        $map[]=['doctor_id','=',$doctor_id];
        $favorinfo=DB::name('user_favor')->where($map)->find();
        $opname='收藏';
        $data=[];
        if(empty($favorinfo)){
            $dmap=[];
            $dmap[]=['isdel','=',2];
            $dmap[]=['status','=',1];
            $dmap[]=['id','=',$doctor_id];
            $doctorinfo=$gservice->doctorDetail($dmap);
            if(empty($doctorinfo)){
                return ['code'=>'400','msg'=>'名医信息不存在'];
            }
            $data['user_id']=$user_id;
            $data['doctor_id']=$doctor_id;
            $data['status']=1;
            $data['favor_time']=date('Y-m-d H:i:s');
            $data['create_time']=date('Y-m-d H:i:s');
            $res=DB::name('user_favor')->insertGetId($data);
        }else{
            if($favorinfo['status']==2){
                $dmap=[];
                $dmap[]=['isdel','=',2];
                $dmap[]=['status','=',1];
                $dmap[]=['id','=',$doctor_id];
                $doctorinfo=$gservice->doctorDetail($dmap);
                if(empty($doctorinfo)){
                    return ['code'=>'400','msg'=>'名医信息不存在'];
                }
                $data['status']=1;
                $data['favor_time']=date('Y-m-d H:i:s');
            }else{
                $data['status']=2;
                $data['update_time']=date('Y-m-d H:i:s');
                $opname='取消收藏';
            }
            $res=DB::name('user_favor')->where([['id','=',$favorinfo['id']]])->update($data);
        }
        if($res){
            return ['code'=>'200','msg'=>$opname.'成功'];
        }else{
            return ['code'=>'400','msg'=>$opname.'失败'];
        }
    }

    //名医收藏列表
    public function userDoctorFavorlist($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('user_favor f')->field($field)->join('__DOCTOR__ d','d.id=f.doctor_id','left')->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('user_favor f')->field($field)->join('__DOCTOR__ d','d.id=f.doctor_id','left')->where($map)->order($orderby)->select();
        }
        $count=DB::name('user_favor f')->join('__DOCTOR__ d','d.id=f.doctor_id','left')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //用户是否收藏名医
    public function userCheckFavor($user_id,$doctor_id){
        $map=[];
        $map[]=['user_id','=',$user_id];
        $map[]=['doctor_id','=',$doctor_id];
        $favorinfo=DB::name('user_favor')->where($map)->find();
        if(empty($favorinfo)){
            return false;
        }
        if($favorinfo['status']==2){
            return false;
        }
        return true;
    }

    //就诊人列表
    public function patientList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('user_patient')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('user_patient')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('user_patient')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //添加/修改就诊人
    public function patientVerify($user_id,$patient_id,$param=[]){
        if(empty($param)){
            return ['code'=>'400','msg'=>'请输入信息'];
        }
        $realname=$param['realname'];
        $mobile=$param['mobile'];
        $content=$param['content'];
        $patientinfo=[];
        if($patient_id>0){
            $map=[];
            $map[]=['user_id','=',$user_id];
            $map[]=['id','=',$patient_id];
            $patientinfo=$this->patientDetail($map);
        }
        $hasmap=[];
        $hasmap[]=['user_id','=',$user_id];
        $hasmap[]=['realname','=',$realname];
        $hasmap[]=['mobile','=',$mobile];
        $haspatientinfo=$this->patientDetail($hasmap);
        if(!empty($haspatientinfo)){
            if($haspatientinfo['id']!=$patient_id){
                return ['code'=>'400','msg'=>'就诊人信息已存在'];
            }
        }
        $data=[];
        $opname='新增';
        $data['realname']=$realname;
        $data['mobile']=$mobile;
        $data['content']=$content;
        if(empty($patientinfo)){
            $data['user_id']=$user_id;
            $data['create_time']=date('Y-m-d H:i:s');
            $res=DB::name('user_patient')->insertGetId($data);
            $patient_id=$res;
        }else{
            $data['update_time']=date('Y-m-d H:i:s');
            $res=DB::name('user_patient')->where([['id','=',$patientinfo['id']],['user_id','=',$user_id]])->update($data);
            $patient_id=$patientinfo['id'];
            $opname='修改';
        }
        if($res){
            $outdata=['patient_id'=>$patient_id,'realname'=>$realname,'mobile'=>$mobile];
            return ['code'=>'200','msg'=>$opname.'就诊人信息成功','data'=>$outdata];
        }else{
            return ['code'=>'400','msg'=>$opname.'就诊人信息失败'];
        }
    }

    //获取就诊人信息
    public function patientDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('user_patient')->field($field)->where($map)->find();
    }

    //删除就诊人信息
    public function patientDelete($user_id,$patient_id=[]){
        if(empty($patient_id)){
            return ['code'=>'400','msg'=>'删除失败'];
        }
        $realid=[];
        foreach($patient_id as $v){
            $map=[];
            $map[]=['user_id','=',$user_id];
            $map[]=['id','=',intval($v)];
            $info=$this->patientDetail($map);
            if(!empty($info)){
                $realid[]=$info['id'];
            }
        }
        if(empty($realid)){
            return ['code'=>'400','msg'=>'请选择要删除的信息'];
        }
        $map=[];
        $map[]=['user_id','=',$user_id];
        $map[]=['id','in',$realid];
        $res=DB::name('user_patient')->where($map)->delete();
        if($res){
            return ['code'=>'200','msg'=>'删除成功'];
        }else{
            return ['code'=>'400','msg'=>'删除失败,请重试'];
        }
    }

    //预约信息列表
    public function meetingList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('orders')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('orders')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('orders')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //新增预约信息
    public function meetingVerify($user_id,$param){
        $doctor_id=$param['doctor_id'];
        $realname=$param['realname'];
        $mobile=$param['mobile'];
        $content=$param['content'];
        $basedoctor_id=$param['basedoctor_id'];
        $verifycode=$param['verifycode'];
        $invitecode=$param['invitecode'];
        $doctorinfo=[];
        $assistant_id=0;
        if($doctor_id>0){
            $dmap=[];
            $dmap[]=['isdel','=',2];
            $dmap[]=['status','=',1];
            $dmap[]=['id','=',$doctor_id];
            $gservice=new GenericService();
            $doctorinfo=$gservice->doctorDetail($dmap);
            if(empty($doctorinfo)){
                return ['code'=>'400','msg'=>'选择预约的名医信息不存在,请刷新页面重试'];
            }
            if(isset($doctorinfo['assistantinfo']) && !empty($doctorinfo['assistantinfo'])){
                $assistant_id=$doctorinfo['assistantinfo']['id'];
            }
        }

        //校验验证码
        $coderes=$this->checkVerifyCode($mobile,$verifycode,1);
        if($coderes['code']!=200){
            return ['code'=>'400','msg'=>$coderes['msg']];
        }
        $basedoctor_id2=0;
        if($basedoctor_id>0){
            $bmap=[];
            $bmap[]=['isdel','=',2];
            $bmap[]=['status','=',1];
            $bmap[]=['id','=',$basedoctor_id];
            $gservice=new GenericService();
            $basedoctorinfo=$gservice->basedoctorDetail($bmap);
            if(empty($basedoctorinfo)){
                $basedoctor_id=0;
            }
        }else{
            if($invitecode!=''){
                $bmap=[];
                $bmap[]=['isdel','=',2];
                $bmap[]=['status','=',1];
                $bmap[]=['invite_code','=',$invitecode];
                $gservice=new GenericService();
                $basedoctorinfo=$gservice->basedoctorDetail($bmap);
                if(!empty($basedoctorinfo)){
                    $basedoctor_id=$basedoctorinfo['id'];
                    $basedoctor_id2=$basedoctorinfo['id'];
                }
            }
        }
        $data=[];
        $orderno=$this->orderno_create();
        $payno=$this->payno_create();
        $isfirst=1;
        $hasmap=[];
        $hasmap[]=['realname','=',$realname];
        $hasmap[]=['mobile','=',$mobile];
        $hasinfo=$this->meetingDetail($hasmap);
        if(!empty($hasinfo)){
            $isfirst=2;
        }
        $data=[
            'user_id'=>$user_id,
            'basedoctor_id'=>$basedoctor_id,
            'assistant_id'=>$assistant_id,
            'doctor_id'=>$doctor_id,
            'realname'=>$realname,
            'mobile'=>$mobile,
            'content'=>$content,
            'orderno'=>$orderno,
            'payno'=>$payno,
            'status'=>1,
            'meetingymd'=>date('Ymd'),
            'create_time'=>date('Y-m-d H:i:s'),
            'isfirst'=>$isfirst,
        ];
        $order_id=DB::name('orders')->insertGetId($data);
        if($order_id){
            $pmap=[];
            $pmap[]=['user_id','=',$user_id];
            $pmap[]=['realname','=',$realname];
            $pmap[]=['mobile','=',$mobile];
            $haspatientinfo=$this->patientDetail($pmap);
            if(empty($haspatientinfo)){
                $patient_id=0;
            }else{
                $patient_id=$haspatientinfo['id'];
            }
            $patientres=$this->patientVerify($user_id,$patient_id,['realname'=>$realname,'mobile'=>$mobile,'content'=>$content]);
            if(!empty($doctorinfo)){
                if(isset($doctorinfo['assistantinfo']) && !empty($doctorinfo['assistantinfo'])){
                    $openid=$doctorinfo['assistantinfo']['openid'];
                    if($openid!=''){
                        send_newtpl($openid,$doctorinfo['assistantinfo']['id'],$doctorinfo['assistantinfo']['realname'],$doctorinfo['realname']);
                    }
                }
            }
            $this->updateVerifyCode($mobile,$verifycode,1);
            if($basedoctor_id2>0){
                DB::name('user')->where([['id','=',$user_id]])->update(['basedoctor_id'=>$basedoctor_id2,'bind_time'=>date('Y-m-d H:i:s'),'bind_way'=>3]);
            }
            return ['code'=>'200','msg'=>'你的预约已成功提交，请保持通讯畅通，稍后将由名医助理为您安排就诊','data'=>$order_id];
        }else{
            return ['code'=>'400','msg'=>'预约失败'];
        }
    }

    //创建验证码
    public function createVerifyCode($mobile,$type){
        $code=rand_string('',6,1);
        $map=[];
        $map[]=['mobile','=',$mobile];
        $map[]=['type','=',$type];
        $map[]=['isread','=',2];
        $info=$this->verifycodeDetail($map);
        $data=[];
        $data['mobile']=$mobile;
        $data['type']=$type;
        $data['isread']=2;
        $data['code']=$code;
        if(!empty($info)){
            $leftseconds=strtotime($info['send_time'])+60*3-time();
            if($leftseconds>0){
                return ['code'=>'400','msg'=>'请在'.$leftseconds.'秒后重发'];
            }
            $upmap=[];
            $upmap[]=['type','=',$type];
            $upmap[]=['isread','=',2];
            $upmap[]=['id','=',$info['id']];
            $data['send_time']=date('Y-m-d H:i:s');
            $data['update_time']=date('Y-m-d H:i:s');
            $res=DB::name('verifycode')->where($upmap)->update($data);
        }else{
            $data['code']=$code;
            $data['send_time']=date('Y-m-d H:i:s');
            $data['create_time']=date('Y-m-d H:i:s');
            $res=DB::name('verifycode')->insert($data);
        }
        if($res){
            base_sendcontent($mobile,$code,$type);
            return ['code'=>'200','msg'=>'发送成功'];
        }else{
            return ['code'=>'400','msg'=>'发送失败,请重试'];
        }
    }

    //校验验证码
    public function checkVerifyCode($mobile,$verifycode,$type){
        $map=[];
        $map[]=['mobile','=',$mobile];
        $map[]=['code','=',$verifycode];
        $map[]=['type','=',$type];
        $info=$this->verifycodeDetail($map);
        if(empty($info)){
            return ['code'=>'400','msg'=>'验证码错误'];
        }
        if($info['isread']==1){
            return ['code'=>'400','msg'=>'验证码已被使用,请重新获取'];
        }
        $leftseconds=strtotime($info['send_time'])+60*13-time();
        if($leftseconds<0){
            return ['code'=>'400','msg'=>'验证码已过期,请重新获取'];
        }
        return ['code'=>'200','msg'=>'验证通过'];
    }

    //更新验证码
    public function updateVerifyCode($mobile,$verifycode,$type){
        $map=[];
        $map[]=['mobile','=',$mobile];
        $map[]=['code','=',$verifycode];
        $map[]=['type','=',$type];
        $info=$this->verifycodeDetail($map);
        if(!empty($info)){
            $updateData=[];
            $updateData['isread']=1;
            $updateData['use_time']=date('Y-m-d H:i:s');
            $updateData['update_time']=date('Y-m-d H:i:s');
            $upmap=[];
            $upmap[]=['id','=',$info['id']];
            $upmap[]=['isread','=',2];
            DB::name('verifycode')->where($upmap)->update($updateData);
        }
    }

    //获取验证码详情
    public function verifycodeDetail($map,$field='*',$orderby=['id'=>'desc']){
        if(empty($map)){
            return [];
        }
        return DB::name('verifycode')->where($map)->order($orderby)->find();
    }

    //预约信息详情
    public function meetingDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('orders')->field($field)->where($map)->find();
    }

    //获取上一条预约信息详情
    public function lastMeetingDetail($id,$map,$field='*'){
        $map2=[];
        $map2[]=['id','<',$id];
        return DB::name('orders')->field($field)->where($map)->where($map2)->order(['id'=>'desc'])->find();
    }

    //获取订单号
    public function orderno_create(){
        $no=date('YmdHis').mt_rand(1000,9999);
        $map=[];
        $map[]=['orderno','=',$no];
        $hasinfo=DB::name('orders')->where($map)->find();
        if(!empty($hasinfo)){
            $this->orderno_create();
        }
        return $no;
    }

    //获取自定义支付单号
    public function payno_create(){
        $no='P'.date('YmdHis').mt_rand(1000,9999);
        $map=[];
        $map[]=['payno','=',$no];
        $hasinfo=DB::name('orders')->where($map)->find();
        if(!empty($hasinfo)){
            $this->payno_create();
        }
        return $no;
    }

    //获取自定义退款单号
    public function refund_orderno_create(){
        $no='R'.date('YmdHis').mt_rand(1000,9999);
        $map=[];
        $map[]=['refund_orderno','=',$no];
        $hasinfo=DB::name('orders_refund')->where($map)->find();
        if(!empty($hasinfo)){
            $this->refund_orderno_create();
        }
        return $no;
    }

    //申请退款处理
    public function RefundFee($userinfo,$meeting_id){
        $money=input('post.money','0','trim')*100;
        // if($money<=0){
        //     return ['code'=>'400','msg'=>'请输入退款金额'];
        // }
        $remark=input('post.remark','','trim');
        $field='*';
        $map[]=['user_id','=',$userinfo['id']];
        $map[]=['id','=',$meeting_id];
        $info=$this->meetingDetail($map,$field);
        if(empty($info)){
            return ['code'=>'400','msg'=>'选择预约信息不存在'];
        }
        if($money<=0){
            $money=$info['pay_money'];
        }
        if($info['pay_money']<=0){
            return ['code'=>'400','msg'=>'无需申请退款'];
        }
        if($info['ispay']==0){
            return ['code'=>'400','msg'=>'订单尚未支付,无需申请退款'];
        }
        if($info['status']==3){
            return ['code'=>'400','msg'=>'订单已完成,不支持退款'];
        }
        if($info['status']==5){
            return ['code'=>'400','msg'=>'订单已关闭'];
        }
        if($info['isrefund']==1){
            return ['code'=>'400','msg'=>'退款申请已提交,正在退款中,请耐心等待'];
        }
        if($money>$info['pay_money']){
            return ['code'=>'400','msg'=>'最多只能申请'.round($info['pay_money']/100,2).'元'];
        }
        $all_refundmoney=DB::name('orders_refund')->where([['orders_id','=',$info['id']],['refund_status','=',3]])->sum('refund_money');
        $all_applyrefundmoney=DB::name('orders_refund')->where([['orders_id','=',$info['id']],['refund_status','=',1]])->sum('refund_applymoney');
        if($info['pay_money']<=($all_refundmoney+$all_applyrefundmoney)){
            return ['code'=>'400','msg'=>'最多只能申请'.round(($info['pay_money']-$all_refundmoney-$all_applyrefundmoney)/100,2).'元'];
        }
        DB::startTrans();
        $updateData['isrefund']=1;
        $updateData['status']=4;
        $updateData['refund_money']=$money+$info['refund_money'];
        $updateData['refund_time']=date('Y-m-d H:i:s');
        $res=DB::name('orders')->where([['id','=',$info['id']]])->update($updateData);
        $refund_orderno=$this->refund_orderno_create();
        $rdata=[];
        $rdata['user_id']=$userinfo['id'];
        $rdata['openid']=$userinfo['openid'];
        $rdata['orders_id']=$info['id'];
        $rdata['orderno']=$info['orderno'];
        $rdata['orders_money']=$info['pay_money'];
        $rdata['transaction_id']=$info['pay_tradeno'];
        $rdata['refund_orderno']=$refund_orderno;
        $rdata['refund_money']=0;
        $rdata['refund_applymoney']=$money;
        $rdata['apply_time']=date('Y-m-d H:i:s');
        $rdata['create_time']=date('Y-m-d H:i:s');
        $rdata['refund_status']=1;
        $res2=DB::name('orders_refund')->insert($rdata);
        if($res && $res2){
            DB::commit();
            return ['code'=>'200','msg'=>'申请退款成功'];
        }else{
            DB::rollback();
            return ['code'=>'400','msg'=>'申请退款失败,请重试'];
        }
    }

    //用户反馈处理
    public function suggestVerify($user_id,$param){
        $content=$param['content'];
        $data['user_id']=$user_id;
        $data['content']=$content;
        $data['type']=1;
        $data['status']=2;
        $data['create_time']=date('Y-m-d H:i:s');
        $res=DB::name('suggestion')->insertGetId($data);
        if($res){
            return ['code'=>'200','msg'=>'提交意见反馈成功'];
        }else{
            return ['code'=>'400','msg'=>'提交意见反馈失败'];
        }
    }

    //反馈列表
    public function suggestList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('suggestion')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('suggestion')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('suggestion')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //客服聊天记录
    public function serviceChatList($style,$map,$field,$start,$limit,$orderby=['id'=>'desc']){
        if($style==1){
            $list=DB::name('user_chat')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('user_chat')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('user_chat')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //客服聊天
    public function serviceChatVerify($user_id,$param){
        $content=$param['content'];
        $img=$param['img'];
        $msgtype=1;
        if($img!=''){
            $msgtype=2;
        }
        if($content!=''){
            $data['content']=$content;
        }
        if($img!=''){
            $uploadres=base64_img_forfile($img,2,config('app.avatarpath'));
            if($uploadres['code']!=200){
                return ['code'=>'400','msg'=>$uploadres['msg']];
            }
            $realimg=getabpath($uploadres['url'],'upload');
            $data['content']=$realimg;
        }
        $data['user_id']=$user_id;
        $data['type']=1;
        $data['msgtype']=$msgtype;
        $data['create_time']=date('Y-m-d H:i:s');
        $res=DB::name('user_chat')->insertGetId($data);
        if($res){
            return ['code'=>'200','msg'=>'消息发送成功'];
        }else{
            return ['code'=>'400','msg'=>'消息发送失败'];
        }
    }
}