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
        $userservice=new UserService();
        $res=$userservice->verifyUser($param);
        if($res['code']!=200){
            return jsondata('0019',$res['msg']);
        }
        $resdata=$res['data'];
        $status=1;
        // if($resdata['realname']=='' || $resdata['mobile']==''){
        //     $status=2;
        // }
        $info['token']=$resdata['token'];
        $info['nickname']=$resdata['nickname'];
        $info['avatar']=$resdata['avatar'];
        $info['realname']=$resdata['realname'];
        $info['mobile']=$resdata['mobile'];
        $info['status']=$status;
        // $info['roletype']=$resdata['roletype'];
        $outdata['data']=$info;
        return jsondata('0001','获取成功',$outdata);
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
        $outdata['data']=$info;
        return jsondata('0001','获取成功',$outdata);
    }

    //更新用户信息
    public function updateInfo(){
        return jsondata('0001','更新成功');
        exit;
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

    //订单列表
    public function getOrdersList(){
        $pagenum=input('pagenum',1,'intval');
        $status=input('status','0','intval');
        if($pagenum<=0) $pagenum=1;
        $map=[];
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $map[]=['o.user_id','=',$this->base_userinfo['id']];
        $map[]=['o.isdel','=',2];
        if($status>0){
            $map[]=['o.status','=',$status];
        }
        $field='o.id,o.orderno,o.realname,o.mobile,o.money,o.status,o.create_time,g.goods_title,b.start_time,b.end_time';
        $orderby=['o.id'=>'desc'];
        $style=1;
        $service=new UserService();
        $list=$service->ordersList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $count=$list['count'];
        if(!empty($listdata)){
            foreach($listdata as &$v){
                $v['money']=round($v['money']/100,2);
            }
        }
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //下单
    public function createOrders(){
        if(request()->isPost() || request()->isAjax()){
            $school_id=input('post.school_id','0','intval');
            $goods_id=input('post.goods_id','0','intval');
            $realname=input('post.realname','','trim');
            $mobile=input('post.mobile','','trim');
            $idcardnum=input('post.idcardnum','','trim');
            $department=input('post.department','','trim');
            $studentnumber=input('post.studentnumber','','trim');
            $address=input('post.address','','trim');
            $param=[
                'school_id'=>$school_id,
                'goods_id'=>$goods_id,
                'realname'=>$realname,
                'mobile'=>$mobile,
                'idcardnum'=>$idcardnum,
                'department'=>$department,
                'studentnumber'=>$studentnumber,
                'address'=>$address,
            ];
            $service=new UserService();
            $res=$service->ordersVerify($this->base_userinfo['id'],$param);
            $code='0028';
            $data=[];
            if($res['code']==200){
                $code='0001';
                $data['data']=$res['data'];
            }
            return jsondata($code,$res['msg'],$data);
        }
        return jsondata('0028','网络请求错误');
    }

    //订单详情
    public function getOrdersDetail(){
        $orders_id=input('orders_id','0','intval');
        if($orders_id<=0){
            return jsondata('0029','请选择订单');
        }
        $field='o.id,o.orderno,o.payno,o.realname,o.mobile,o.money,o.idcardnum,o.department,o.studentnumber,o.address,o.status,o.create_time,o.finish_time,o.pay_time,g.goods_title,b.keyaccount,b.keypassword,b.start_time,b.end_time';
        $map=[];
        $map[]=['o.user_id','=',$this->base_userinfo['id']];
        $map[]=['o.id','=',$orders_id];
        $map[]=['o.isdel','=',2];
        $service=new UserService();
        $info=$service->ordersInfo($map,$field);
        if(empty($info)){
            return jsondata('0023','订单信息不存在');
        }
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);

    }

    //预约费用支付(返回jsapi支付参数)
    public function ordersPay(){
        $orders_id=input('orders_id','0','intval');
        if($orders_id<=0){
            return jsondata('0021','请选择预约信息');
        }
        $field='*';
        $map=[];
        $map[]=['user_id','=',$this->base_userinfo['id']];
        $map[]=['id','=',$orders_id];
        $service=new UserService();
        $info=$service->ordersDetail($map,$field);
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
            return jsondata('0021','订单已关闭');
        }
        if($info['status']==5){
            return jsondata('0021','订单正在退款中');
        }
        $payno=$service->payno_create();
        require_once WXPAYPATH.'WxPay.JsApiPay.php';
        $tools = new \JsApiPay();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("宽带套餐费用");
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

}
