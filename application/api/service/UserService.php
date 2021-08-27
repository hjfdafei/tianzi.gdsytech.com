<?php
namespace app\api\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
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

    //用户注册登录
    public function verifyUser($param){
        $openid=$param['openid'];
        $nickname=preg_replace('/[\x{10000}-\x{10FFFF}]/u','', $param['nickname']);//$param['nick_name']
        $avatar=$param['avatar'];
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
        ];
        if(empty($userinfo)){
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

    //订单列表
    public function ordersList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('orders o')->field($field)->join('__GOODS__ g','g.id=o.goods_id','left')->join('__BROADBAND__ b','b.id=o.broadband_id','left')->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('orders o')->field($field)->join('__GOODS__ g','g.id=o.goods_id','left')->join('__BROADBAND__ b','b.id=o.broadband_id','left')->where($map)->order($orderby)->select();
        }
        $count=DB::name('orders o')->field($field)->join('__GOODS__ g','g.id=o.goods_id','left')->join('__BROADBAND__ b','b.id=o.broadband_id','left')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //新增订单
    public function ordersVerify($userinfo,$param){
        $user_id=$userinfo['id'];
        $openid=$userinfo['openid'];
        $school_id=$param['school_id'];
        $goods_id=$param['goods_id'];
        $realname=$param['realname'];
        $mobile=$param['mobile'];
        $idcardnum=$param['idcardnum'];
        $department=$param['department'];
        $studentnumber=$param['studentnumber'];
        $address=$param['address'];
        $promoter=$param['promoter'];
        if($school_id<=0){
            return ['code'=>'400','msg'=>'请选择校区'];
        }
        if($goods_id<=0){
            return ['code'=>'400','msg'=>'请选择宽带套餐'];
        }
        if($realname==''){
            return ['code'=>'400','msg'=>'请输入姓名'];
        }
        if($mobile==''){
            return ['code'=>'400','msg'=>'请输入联系电话'];
        }
        if($idcardnum==''){
            return ['code'=>'400','msg'=>'请输入身份证号码'];
        }
        if($department==''){
            return ['code'=>'400','msg'=>'请输入院系'];
        }
        if($studentnumber==''){
            return ['code'=>'400','msg'=>'请输入学号'];
        }
        if($address==''){
            return ['code'=>'400','msg'=>'请输入宿舍地址'];
        }
        $checkmobile_res=checkformat_mobile($mobile);
        if($checkmobile_res['code']!='0001'){
            return ['code'=>'400','msg'=>$checkmobile_res['msg']];
        }
        if(!validation_idcard($idcardnum)){
            return ['code'=>'400','msg'=>'请输入正确的身份证号码'];
        }
        $gservice=new GenericService();
        $smap=[];
        $smap[]=['id','=',$school_id];
        $smap[]=['status','=',1];
        $school_info=$gservice->schoolDetail($smap);
        if(empty($school_info)){
            return ['code'=>'400','msg'=>'选择的校区暂未开通服务'];
        }
        $gmap=[];
        $gmap[]=['id','=',$goods_id];
        $gmap[]=['goods_status','=',1];
        $goods_info=$gservice->goodsDetail($gmap);
        if(empty($goods_info)){
            return ['code'=>'400','msg'=>'宽带套餐已下架'];
        }
        $data=[];
        $orderno=$this->orderno_create();
        $payno=$this->payno_create();
        $isfirst=1;
        $hasmap=[];
        $hasmap[]=['user_id','=',$user_id];
        $hasinfo=$this->ordersDetail($hasmap);
        if(!empty($hasinfo)){
            $isfirst=2;
        }
        $data=[
            'user_id'=>$user_id,
            'openid'=>$openid,
            'school_id'=>$school_id,
            'goods_id'=>$goods_id,
            'orderno'=>$orderno,
            'realname'=>$realname,
            'mobile'=>$mobile,
            'idcardnum'=>$idcardnum,
            'department'=>$department,
            'studentnumber'=>$studentnumber,
            'address'=>$address,
            'promoter'=>$promoter,
            'payno'=>$payno,
            'status'=>1,
            'money'=>$goods_info['goods_price']*100,
            'create_time'=>date('Y-m-d H:i:s'),
            'isfirst'=>$isfirst,
        ];
        $order_id=DB::name('orders')->insertGetId($data);
        if($order_id){
            $uudata=[];
            $uudata['realname']=$realname;
            $uudata['mobile']=$mobile;
            $uudata['idcardnum']=$idcardnum;
            $uudata['department']=$department;
            $uudata['studentnumber']=$studentnumber;
            $uudata['address']=$address;
            $uudata['update_time']=date('Y-m-d H:i:s');
            $uumap=[];
            $uumap[]=['id','=',$user_id];
            DB::name('user')->where($uumap)->update($uudata);
            return ['code'=>'200','msg'=>'下单成功','data'=>$order_id];
        }else{
            return ['code'=>'400','msg'=>'下单失败,请重试'];
        }
    }

    //订单详情
    public function ordersDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('orders')->field($field)->where($map)->find();
    }

    //订单信息
    public function ordersInfo($map,$field='o.*'){
        if(empty($map)){
            return [];
        }
        return DB::name('orders o')->field($field)->join('__GOODS__ g','g.id=o.goods_id','left')->join('__BROADBAND__ b','b.id=o.broadband_id','left')->where($map)->find();
    }

    //获取订单号
    public function orderno_create(){
        $no=date('YmdHis').mt_rand(1000,9999);
        $map=[];
        $map[]=['orderno','=',$no];
        $hasinfo=$this->ordersDetail($map);
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
        $hasinfo=$this->ordersDetail($map);
        if(!empty($hasinfo)){
            $this->payno_create();
        }
        return $no;
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

    //获取上一条预约信息详情
    public function lastMeetingDetail($id,$map,$field='*'){
        $map2=[];
        $map2[]=['id','<',$id];
        return DB::name('orders')->field($field)->where($map)->where($map2)->order(['id'=>'desc'])->find();
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