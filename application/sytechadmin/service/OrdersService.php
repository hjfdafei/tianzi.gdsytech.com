<?php
namespace app\sytechadmin\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\sytechadmin\controller\Upload;
use app\sytechadmin\service\UserService;
use app\sytechadmin\service\SchoolService;
use app\sytechadmin\service\GoodsService;
use app\sytechadmin\service\BroadbandService;
//订单管理
class OrdersService extends Base{
    //field:查询字段 map:查询条件 search:搜索条件显示在分页链接 orderby:排序 pernum:每页多少条 type:获取数据类别 1获取分页 2获取全部
    //订单列表
    public function getOrdersList($type=1,$map=[],$field='*',$search=[],$pernum=20,$orderby=['id'=>'asc']){
        $list=array();
        $page='';
        $count=0;
        $statusname=['1'=>'待支付','2'=>'已支付','3'=>'已发放','4'=>'已取消','5'=>'取消中'];
        $ispayname=['1'=>'已支付','2'=>'待支付','3'=>'支付失败'];
        $isrefundname=['1'=>'有退款','2'=>'无退款'];
        $schoolnamearr=[];
        $schoolservice=new SchoolService();
        if($type==1){
            $smap=[];
            $sfield='*';
            $sorderby=['sortby'=>'desc','id'=>'desc'];
            $school_list=$schoolservice->getSchoolList(2,$smap,$sfield,[],20,$sorderby)['list'];
            if(!empty($school_list)){
                foreach($school_list as $v){
                    $schoolnamearr[$v['id']]=$v['title'];
                }
            }
            $list=DB::name('orders o')->field($field)->join('__GOODS__ g','g.id=o.goods_id','left')->join('__BROADBAND__ b','b.id=o.broadband_id','left')->where($map)->order($orderby)->paginate($pernum,false,['query'=>$search])->each(function($item,$key) use($statusname,$ispayname,$isrefundname,$schoolnamearr){
                $schoolname='';
                if(isset($schoolnamearr[$item['school_id']])){
                    $schoolname=$schoolnamearr[$item['school_id']];
                }
                $item['money']=round($item['money']/100,2);
                $item['pay_money']=round($item['pay_money']/100,2);
                $item['refund_money']=round($item['refund_money']/100,2);
                $item['statusname']=$statusname[$item['status']];
                $item['ispayname']=$ispayname[$item['ispay']];
                $item['isrefundname']=$isrefundname[$item['isrefund']];
                $item['schoolname']=$schoolname;
                return $item;
            });
            $page=$list->render();
            $count=$list->total();
        }else{
            $list=DB::name('orders o')->field($field)->join('__GOODS__ g','g.id=o.goods_id','left')->join('__BROADBAND__ b','b.id=o.broadband_id','left')->where($map)->order($orderby)->select();
            $count=count($list);
        }
        $data['list']=$list;
        $data['page']=$page;
        $data['count']=$count;
        return $data;
    }

    //订单详情
    public function ordersDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        $info=DB::name('orders')->where($map)->find();
        if(empty($info)){
            return [];
        }
        $aservice=new AssistantsService();
        $bservice=new BasedoctorsService();
        $dservice=new DoctorService();
        $statusname=['1'=>'待处理','2'=>'已预约','3'=>'已结算','4'=>'退款中','5'=>'已关闭'];
        $ispayname=['1'=>'已支付','2'=>'待支付','3'=>'支付失败'];
        $isrefundname=['1'=>'有退款','2'=>'无退款'];
        $doctor_name='';
        $assistant_name='';
        $basedoctor_name='';
        $doctorinfo=[];
        $assistantinfo=[];
        $basedoctorinfo=[];
        if($info['doctor_id']>0){
            if(isset($doctorarr[$info['doctor_id']])){
                $doctorinfo=$doctorarr[$info['doctor_id']];
            }else{
                $dmap=[];
                $dmap[]=['id','=',$info['doctor_id']];
                $doctorinfo=$dservice->doctorDetail($dmap);
                $doctorarr[$info['doctor_id']]=$doctorinfo;
            }
        }
        if(!empty($doctorinfo)){
            $doctor_name=$doctorinfo['realname'];
        }
        if($info['assistant_id']>0){
            if(isset($assistantarr[$info['assistant_id']])){
                $assistantinfo=$assistantarr[$info['assistant_id']];
            }else{
                $amap=[];
                $amap[]=['id','=',$info['assistant_id']];
                $assistantinfo=$aservice->assistantsDetail($amap);
                $assistantarr[$info['assistant_id']]=$assistantinfo;
            }
        }
        if(!empty($assistantinfo)){
            $assistant_name=$assistantinfo['realname'];
        }

        if($info['basedoctor_id']>0){
            if(isset($basedoctorarr[$info['basedoctor_id']])){
                $basedoctorinfo=$basedoctorarr[$info['basedoctor_id']];
            }else{
                $bmap=[];
                $bmap[]=['id','=',$info['basedoctor_id']];
                $basedoctorinfo=$bservice->basedoctorsDetail($bmap);
                $basedoctorarr[$info['basedoctor_id']]=$basedoctorinfo;
            }
        }
        if(!empty($basedoctorinfo)){
            $basedoctor_name=$basedoctorinfo['realname'];
        }
        // $info['money']=round($info['money']/100,2);
        // $info['pay_money']=round($info['pay_money']/100,2);
        // $info['refund_money']=round($info['refund_money']/100,2);
        $info['doctor_name']=$doctor_name;
        $info['assistant_name']=$assistant_name;
        $info['basedoctor_name']=$basedoctor_name;
        $info['statusname']=$statusname[$info['status']];
        $info['ispayname']=$ispayname[$info['ispay']];
        $info['isrefundname']=$isrefundname[$info['isrefund']];
        return $info;
    }

    //分配名医 名医和名医助理正常才能用
    public function orders_assigning($id){
        $id=intval($id);
        $doctor_id=input('post.doctor_id','0','intval');
        if($doctor_id<=0){
            return jsondata('400','请选择名医');
        }
        $map=[];
        $map[]=['id','=',$id];
        $info=$this->ordersDetail($map);
        if(empty($info)){
            return jsondata('400','预约订单信息不存在');
        }
        if($info['doctor_id']>0){
            return jsondata('400','预约订单已分配名医,不支持重新分配');
        }
        $dservice=new DoctorService();
        $dmap=[];
        $dmap[]=['id','=',$doctor_id];
        $dmap[]=['isdel','=',2];
        $dmap[]=['status','=',1];
        $doctorinfo=$dservice->doctorDetail($dmap);
        if(empty($doctorinfo)){
            return jsondata('400','名医信息不存在');
        }
        $hmap=[];
        $hmap[]=['doctor_id','=',$doctor_id];
        $hasassistant=DB::name('assistant_item')->where($hmap)->find();
        if(empty($hasassistant)){
            return jsondata('400','名医还没绑定助理,暂时不能派单');
        }
        $aservice=new AssistantsService();
        $amap=[];
        $amap[]=['id','=',$hasassistant['assistant_id']];
        $amap[]=['isdel','=',2];
        $amap[]=['status','=',1];
        $assistantinfo=$aservice->assistantsDetail($amap);
        if(empty($assistantinfo)){
            return jsondata('400','名医助理信息不存在');
        }
        $upmap=[];
        $upmap[]=['id','=',$info['id']];
        $updateData['doctor_id']=$doctorinfo['id'];
        $updateData['assistant_id']=$assistantinfo['id'];
        $updateData['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('orders')->where($upmap)->update($updateData);
        if($res){
            send_newtpl($assistantinfo['openid'],$assistantinfo['id'],$assistantinfo['realname'],$doctorinfo['realname']);
            return jsondata('200','分配名医成功');
        }else{
            return jsondata('400','分配名医失败,请重试');
        }
    }

    //设置费用
    public function orders_setfeeing($id){
        $id=intval($id);
        $money=input('post.money','0','trim');
        if($money<=0){
            return jsondata('400','请设置费用');
        }
        $map=[];
        $map[]=['id','=',$id];
        $info=$this->ordersDetail($map);
        if(empty($info)){
            return jsondata('400','预约订单信息不存在');
        }
        if($info['ispay']==1){
            return jsondata('400','订单已被支付,不能再设置费用');
        }
        $updateData['money']=$money*100;
        $updateData['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('orders')->where([['id','=',$info['id']]])->update($updateData);
        if($res){
            return jsondata('200','设置费用成功');
        }else{
            return jsondata('400','设置费用失败,请重试');
        }
    }

    //设置积分 $id array
    public function orders_setintegraling($id){
        $trueid=[];
        $integral=input('post.integral','','trim')*1;
        if($integral==''){
            return jsondata('400','请设置积分');
        }
        if($integral<0){
            return jsondata('400','请设置积分,积分值应大于等于0');
        }
        foreach($id as $v){
            $map=[];
            $map[]=['id','=',intval($v)];
            $map[]=['issettle','<>',1];
            $info=$this->ordersDetail($map);
            if(!empty($info)){
                $trueid[]=$info['id'];
            }
        }
        if(empty($trueid)){
            return jsondata('400','选择的订单已结算,无需设置积分,请重试。');
        }
        $updateData['doctor_integral']=$integral;
        $updateData['update_time']=date('Y-m-d H:i:s');
        $upmap=[];
        $upmap[]=['id','in',$trueid];
        $upmap[]=['issettle','<>',1];
        $res=DB::name('orders')->where($upmap)->update($updateData);
        if($res){
            return jsondata('200','设置积分成功');
        }else{
            return jsondata('400','设置积分失败,请重试');
        }
    }

    //设置完成 $id array
    public function orders_setfinishing($id){
        $trueid=[];
        foreach($id as $v){
            $map=[];
            $map[]=['id','=',intval($v)];
            $map[]=['doctor_id','>',0];
            $map[]=['status','<>',3];
            $map[]=['issettle','<>',1];
            $info=$this->ordersDetail($map);
            if(!empty($info)){
                $trueid[]=$info['id'];
            }
        }
        if(empty($trueid)){
            return jsondata('400','选择的订单不符合,设置订单完成失败,请重试。');
        }
        $upmap=[];
        $upmap[]=['id','in',$trueid];
        $updateData['status']=3;
        $updateData['finish_time']=date('Y-m-d H:i:s');
        $updateData['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('orders')->where($upmap)->update($updateData);
        if($res){
            return jsondata('200','设置订单完成成功');
        }else{
            return jsondata('400','设置订单完成失败,请重试');
        }
    }

    //结算订单 $id array
    public function orders_setsettling($id){
        $trueid=[];
        $dservice=new DoctorService();
        DB::startTrans();
        $num=0;
        foreach($id as $v){
            $map=[];
            $map[]=['id','=',intval($v)];
            $map[]=['status','=',3];
            $map[]=['issettle','<>',1];
            $info=$this->ordersDetail($map);
            if(!empty($info)){
                $dmap=[];
                $dmap[]=['id','=',$info['doctor_id']];
                $doctorinfo=$dservice->doctorDetail($dmap);
                if(!empty($doctorinfo)){
                    $omap=[];
                    $omap[]=['id','=',$info['id']];
                    $omap[]=['issettle','<>',1];
                    $oupdata=[];
                    $oupdata['issettle']=1;
                    $oupdata['settle_time']=date('Y-m-d H:i:s');
                    $oupdata['update_time']=date('Y-m-d H:i:s');
                    $res=DB::name('orders')->where($omap)->update($oupdata);
                    $hasmap=[];
                    $hasmap[]=['orders_id','=',$info['id']];
                    $hassettle=$this->integralDetail($hasmap);
                    if(empty($hassettle)){
                        $dmap=[];
                        $dmap[]=['id','=',$doctorinfo['id']];
                        $dupdata=[];
                        $dupdata['integral_all']=$doctorinfo['integral_all']+$info['doctor_integral'];
                        $dupdata['integral_able']=$doctorinfo['integral_able']+$info['doctor_integral'];
                        $dupdata['update_time']=date('Y-m-d H:i:s');
                        $res2=DB::name('doctor')->where($dmap)->update($dupdata);
                        $ordernum=$this->integralorderno_create();
                        $integral_start=0;
                        $hasmap2=[];
                        $hasmap2[]=['doctor_id','=',$doctorinfo['id']];
                        $hassettle2=$this->integralDetail($hasmap2);
                        if(!empty($hassettle2)){
                            $integral_start=$hassettle2['integral_end'];
                        }
                        $integral=$info['doctor_integral'];
                        $integral_end=$integral_start+$integral;
                        $idata=[];
                        $idata['doctor_id']=$doctorinfo['id'];
                        $idata['user_id']=$info['user_id'];
                        $idata['orders_id']=$info['id'];
                        $idata['orders_no']=$info['orderno'];
                        $idata['ordernum']=$ordernum;
                        $idata['type']=1;
                        $idata['integral']=$integral;
                        $idata['integral_start']=$integral_start;
                        $idata['integral_end']=$integral_end;
                        $idata['remark']="订单:".$info['orderno']."结算,返还积分";
                        $idata['create_time']=date('Y-m-d H:i:s');
                        $res3=DB::name('doctor_integral')->insert($idata);
                    }else{
                        $res2=0;
                        $res3=0;
                    }
                    if($res && $res2 && $res3){
                        $num++;
                    }
                }
            }
        }
        if($num==count($id)){
            DB::commit();
            return jsondata('200','结算订单成功,共结算'.$num.'单');
        }else{
            DB::rollback();
            return jsondata('400','结算订单失败,请重试');
        }
    }

    //关闭订单
    public function orders_closing($id){
        $trueid=[];
        foreach($id as $v){
            $map=[];
            $map[]=['id','=',intval($v)];
            $map[]=['status','<>',5];
            //$map[]=['issettle','<>',1];
            $info=$this->ordersDetail($map);
            if(!empty($info)){
                $trueid[]=$info['id'];
            }
        }
        if(empty($trueid)){
            return jsondata('400','请选择需要关闭的订单');
        }
        $omap=[];
        $omap[]=['id','in',$trueid];
        $omap[]=['status','<>',5];
        $oupdata=[];
        $oupdata['close_time']=date('Y-m-d H:i:s');
        $oupdata['update_time']=date('Y-m-d H:i:s');
        $oupdata['status']=5;
        $res=DB::name('orders')->where($omap)->update($oupdata);
        if($res){
            return jsondata('200','关闭订单成功');
        }else{
            return jsondata('400','关闭订单失败,请重试');
        }
    }

    //生成自定义积分明细订单号
    public function integralorderno_create(){
        $no='D'.date('YmdHis').mt_rand(1000,9999);
        $map=[];
        $map[]=['ordernum','=',$no];
        $hasinfo=DB::name('doctor_integral')->where($map)->find();
        if(!empty($hasinfo)){
            $this->integralorderno_create();
        }
        return $no;
    }

    //积分明细详情
    public function integralDetail($map,$orderby=['id'=>'desc']){
        if(empty($map)){
            return [];
        }
        return DB::name('doctor_integral')->where($map)->order($orderby)->find();
    }

    //申请退款列表
    public function getOrdersRefundList($type=1,$map=[],$field='*',$search=[],$pernum=20,$orderby=['id'=>'asc']){
        $list=array();
        $page='';
        $count=0;
        $statusname=['1'=>'待处理','2'=>'已拒绝','3'=>'已退款'];
        if($type==1){
            $list=DB::name('orders_refund')->field($field)->where($map)->order($orderby)->paginate($pernum,false,['query'=>$search])->each(function($item,$key) use($statusname){
                $item['statusname']=$statusname[$item['refund_status']];
                $item['orders_money']=round($item['orders_money']/100,2);
                $item['refund_money']=round($item['refund_money']/100,2);
                $item['refund_applymoney']=round($item['refund_applymoney']/100,2);
                return $item;
            });
            $page=$list->render();
            $count=$list->total();
        }else{
            $list=DB::name('orders_refund')->field($field)->where($map)->order($orderby)->select();
            $count=count($list);
        }
        $data['list']=$list;
        $data['page']=$page;
        $data['count']=$count;
        return $data;
    }

    //退款详情
    public function ordersRefundDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('orders_refund')->field($field)->where($map)->find();
    }

    //同意退款
    public function orders_refund_agreeing($id){
        $id=intval($id);
        $remark=input('post.remark','','trim');
        $money=input('post.money','0','trim')*100;
        $map=[];
        $map[]=['id','=',$id];
        $info=$this->ordersRefundDetail($map);
        if(empty($info)){
            return jsondata('400','申请退款信息不存在');
        }
        if($info['refund_status']==2){
            return jsondata('400','申请退款信息已被拒绝,不能通过');
        }elseif($info['refund_status']==3){
            return jsondata('400','申请退款信息已通过');
        }elseif($info['refund_status']==4){
            return jsondata('400','申请退款信息正在处理中.');
        }
        $omap=[];
        $omap[]=['id','=',$info['orders_id']];
        $ordersinfo=$this->ordersDetail($omap);
        if(!empty($ordersinfo)){
            if($money>$ordersinfo['pay_money']){
                return jsondata('400','最多只能申请'.round($ordersinfo['pay_money']/100,2).'元');
                //return ['code'=>'400','msg'=>'最多只能申请'.round($ordersinfo['pay_money']/100,2).'元'];
            }
            $all_refundmoney=DB::name('orders_refund')->where([['orders_id','=',$ordersinfo['id']],['refund_status','=',3]])->sum('refund_money');
            $all_applyrefundmoney=DB::name('orders_refund')->where([['orders_id','=',$ordersinfo['id']],['refund_status','=',1]])->sum('refund_applymoney');
            if($ordersinfo['pay_money']<=($all_refundmoney+$all_applyrefundmoney)){
                return jsondata('400','最多只能申请'.round(($ordersinfo['pay_money']-$all_refundmoney-$all_applyrefundmoney)/100,2).'元');
                //return ['code'=>'400','msg'=>'最多只能申请'.round(($ordersinfo['pay_money']-$all_refundmoney-$all_applyrefundmoney)/100,2).'元'];
            }
            if($ordersinfo['status']==5){
                return jsondata('400','订单已关闭');
            }
            if($ordersinfo['status']==3){
                return jsondata('400','订单已完成');
            }
            DB::name('orders_refund')->where([['id','=',$info['id']]])->update(['deal_time'=>date('Y-m-d H:i:s'),'refund_status'=>4]);
            $param=[
                'appid' => config('app.miniappid'),
                'mch_id' => config('app.mchid'),
                'nonce_str' => md5(time()),
                'sign_type' => 'MD5',
                'transaction_id' => $info['transaction_id'],
                'out_refund_no' => $info['refund_orderno'],
                'total_fee' => $info['orders_money'],
                'refund_fee' => $money,
                'refund_desc' =>'用户申请',
                'notify_url' => config('app_host').'/Api/Payment/syrefund_notify',//通知地址
            ];
            $sign=wechatmakesign($param,config('app.mchkey'));
            $param['sign']=$sign;
            $xmldata=wechatarr2xml($param);
            $content=curl_post_ssl('https://api.mch.weixin.qq.com/secapi/pay/refund',$xmldata);
            libxml_disable_entity_loader(true);
            $rescontent=json_decode(json_encode(simplexml_load_string($content,'SimpleXMLElement', LIBXML_NOCDATA)),true);
            if(strtolower($rescontent['return_code'])=='success'){
                $fmap=[];
                $fmap[]=['refund_orderno','=',$rescontent['out_refund_no']];
                $findinfo=$this->ordersRefundDetail($fmap);
                if(!empty($findinfo)){
                    DB::name('orders_refund')->where([['id','=',$findinfo['id']]])->update(['refund_status'=>3,'refund_money'=>$rescontent['refund_fee'],'update_time'=>date('Y-m-d H:i:s')]);
                    $sdata=[];
                    $sdata['user_id']=$findinfo['user_id'];
                    $sdata['openid']=$findinfo['openid'];
                    $sdata['orders_id']=$findinfo['orders_id'];
                    $sdata['orderno']=$rescontent['out_refund_no'];
                    $sdata['transaction_id']=$rescontent['refund_id'];
                    $sdata['content']=$content;
                    $sdata['type']=2;
                    $sdata['create_time']=date('Y-m-d H:i:s');
                    DB::name('orders_paynotice')->insert($sdata);
                }
                return jsondata('200','同意退款申请成功');
            }
        }
        return jsondata('400','同意退款申请失败,请重试');
    }

    //拒绝退款
    public function orders_refund_refusing($id){
        $id=intval($id);
        $remark=input('post.remark','','trim');
        $map=[];
        $map[]=['id','=',$id];
        $info=$this->ordersRefundDetail($map);
        if(empty($info)){
            return jsondata('400','申请退款信息不存在');
        }
        if($info['refund_status']==2){
            return jsondata('400','申请退款信息已被拒绝');
        }elseif($info['refund_status']==3){
            return jsondata('400','申请退款信息已通过,不能再拒绝');
        }elseif($info['refund_status']==4){
            return jsondata('400','申请退款信息正在处理中.');
        }
        DB::startTrans();
        if($remark!=''){
            $updateData['remark']=$remark;
        }
        $updateData['refund_status']=2;
        $updateData['deal_time']=date('Y-m-d H:i:s');
        $updateData['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('orders_refund')->where([['id','=',$info['id']]])->update($updateData);
        $res2=1;
        $omap=[];
        $omap[]=['id','=',$info['orders_id']];
        $ordersinfo=$this->ordersDetail($omap);
        $needrefund_money=round($ordersinfo['refund_money']-$info['refund_applymoney'],2);
        if(!empty($ordersinfo)){
            $needrefund_money=$ordersinfo['refund_money']-$info['refund_applymoney'];
            $oupdata['refund_money']=$needrefund_money;
            $oupdata['status']=2;
            $oupdata['update_time']=date('Y-m-d H:i:s');
            if($needrefund_money==0){
                $oupdata['isrefund']=2;
                $oupdata['refund_time']=NULL;
            }
            $res2=DB::name('orders')->where([['id','=',$ordersinfo['id']]])->update($oupdata);
        }
        if($res && $res2){
            DB::commit();
            return jsondata('200','拒绝退款成功');
        }else{
            DB::rollback();
            return jsondata('400','拒绝退款失败,请重试');
        }
    }

    //导出订单数据
    public function orders_exportdata($map){
        if(empty($map)){
            echo '<script>alert("暂无数据");window.history.go(-1)</script>';
            return ;
        }
        $list=$this->getOrdersList(2,$map);
        if(empty($list['list'])){
            echo '<script>alert("暂无数据");window.history.go(-1)</script>';
            return ;
        }
        $listdata=$list['list'];
        $filename='预约订单信息表';
        $head=['下单会员昵称','就诊人姓名','就诊人联系电话','就诊人确诊信息','名医姓名','名医助理姓名','基层医生姓名','名医所得积分','业务状态','结算状态','助理处理订单时间','完成订单时间','结算订单时间','下单时间'];
        $data=[];
        $aservice=new AssistantsService();
        $bservice=new BasedoctorsService();
        $dservice=new DoctorService();
        $uservice=new UserService();
        $statusnamearr=['1'=>'待处理','2'=>'已预约','3'=>'已完成','4'=>'退款中','5'=>'已关闭'];
        $ispaynamearr=['1'=>'已支付','2'=>'待支付','3'=>'支付失败'];
        $isrefundnamearr=['1'=>'有退款','2'=>'无退款'];
        $issettlenamearr=['1'=>'已结算','2'=>'待结算'];
        foreach($listdata as $v){
            $doctor_name='';
            $assistant_name='';
            $basedoctor_name='';
            $user_nickname='';
            $userinfo=[];
            $doctorinfo=[];
            $assistantinfo=[];
            $basedoctorinfo=[];
            if($v['doctor_id']>0){
                if(isset($doctorarr[$v['doctor_id']])){
                    $doctorinfo=$doctorarr[$v['doctor_id']];
                }else{
                    $dmap=[];
                    $dmap[]=['id','=',$v['doctor_id']];
                    $doctorinfo=$dservice->doctorDetail($dmap);
                    $doctorarr[$v['doctor_id']]=$doctorinfo;
                }
            }
            if(!empty($doctorinfo)){
                $doctor_name=$doctorinfo['realname'];
            }
            if($v['assistant_id']>0){
                if(isset($assistantarr[$v['assistant_id']])){
                    $assistantinfo=$assistantarr[$v['assistant_id']];
                }else{
                    $amap=[];
                    $amap[]=['id','=',$v['assistant_id']];
                    $assistantinfo=$aservice->assistantsDetail($amap);
                    $assistantarr[$v['assistant_id']]=$assistantinfo;
                }
            }
            if(!empty($assistantinfo)){
                $assistant_name=$assistantinfo['realname'];
            }

            if($v['basedoctor_id']>0){
                if(isset($basedoctorarr[$v['basedoctor_id']])){
                    $basedoctorinfo=$basedoctorarr[$v['basedoctor_id']];
                }else{
                    $bmap=[];
                    $bmap[]=['id','=',$v['basedoctor_id']];
                    $basedoctorinfo=$bservice->basedoctorsDetail($bmap);
                    $basedoctorarr[$v['basedoctor_id']]=$basedoctorinfo;
                }
            }
            if(!empty($basedoctorinfo)){
                $basedoctor_name=$basedoctorinfo['realname'];
            }
            if($v['user_id']>0){
                if(isset($userinfoarr[$v['user_id']])){
                    $userinfo=$userinfoarr[$v['user_id']];
                }else{
                    $bmap=[];
                    $bmap[]=['id','=',$v['user_id']];
                    $userinfo=$uservice->userDetail($bmap);
                    $userinfoarr[$v['user_id']]=$userinfo;
                }
            }
            if(!empty($userinfo)){
                $user_nickname=$userinfo['nickname'];
            }
            $statusname=$statusnamearr[$v['status']];
            $issettlename=$issettlenamearr[$v['issettle']];
            $data[]=[$user_nickname,$v['realname'],"\t".$v['mobile'],$v['content'],$doctor_name,$assistant_name,$basedoctor_name,$v['doctor_integral'],$statusname,$issettlename,$v['deal_time'],$v['finish_time'],$v['settle_time'],$v['create_time']];
        }
        exportdatas($filename,$head,$data);
        return ;

    }

}
