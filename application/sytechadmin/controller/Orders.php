<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\service\OrdersService;
use app\sytechadmin\service\SchoolService;
use app\sytechadmin\service\GoodsService;

//订单管理
class Orders extends Sytechadminbase{
    //订单列表
    public function orders_list(){
        $user_id=input('user_id','0','intval');
        $goods_id=input('goods_id','0','intval');
        $school_id=input('school_id','0','intval');
        $status=input('status','0','intval');
        $orderno=input('orderno','','trim');
        $keyword=input('keyword','','trim');
        $applytime_start=input('applytime_start','','trim');
        $applytime_end=input('applytime_end',date('Y-m-d 23:59:59'),'trim');
        $map=[];
        if($school_id>0){
            $map[]=['o.school_id','=',$school_id];
        }
        if($user_id>0){
            $map[]=['o.user_id','=',$user_id];
        }
        if($goods_id>0){
            $map[]=['o.goods_id','=',$goods_id];
        }
        if($status>0){
            $map[]=['status','=',$status];
        }
        if($keyword!=''){
            $map[]=['o.realname|o.mobile|o.idcardnum','like',"%$keyword%"];
        }
        if($orderno!=''){
            $map[]=['o.orderno','=',$orderno];
        }
        if($applytime_start!=''){
            $map[]=['o.create_time','>=',$applytime_start];
        }
        if($applytime_end!=''){
            $map[]=['o.create_time','<=',$applytime_end];
        }
        $search['user_id']=$user_id;
        $search['goods_id']=$goods_id;
        $search['school_id']=$school_id;
        $search['status']=$status;
        $search['keyword']=$keyword;
        $search['orderno']=$orderno;
        $search['applytime_start']=$applytime_start;
        $search['applytime_end']=$applytime_end;
        $field='o.*,g.goods_title,b.keyaccount,b.keypassword';
        $orderby=['o.id'=>'desc'];
        $service=new OrdersService();
        $data=$service->getOrdersList(1,$map,$field,$search,20,$orderby);
        $schoolservice=new SchoolService();
        $smap=[];
        $sfield='*';
        $sorderby=['sortby'=>'desc','id'=>'desc'];
        $school_list=$schoolservice->getSchoolList(2,$smap,$sfield,[],20,$sorderby)['list'];
        $goodsservice=new GoodsService();
        $gmap=[];
        $gfield='*';
        $gorderby=['goods_sortby'=>'desc','id'=>'desc'];
        $goods_list=$goodsservice->getGoodsList(2,$gmap,$gfield,[],20,$gorderby)['list'];
        $this->assign(['search'=>$search,'list'=>$data['list'],'count'=>$data['count'],'page'=>$data['page'],'school_list'=>$school_list,'goods_list'=>$goods_list]);
        return $this->fetch();
    }
    //订单详情
    public function orders_detail(){
        $ordersid=input('ordersid','0','intval');
        if($ordersid<=0){
            return jsondata('400','请选择预约订单');
        }
        $service=new OrdersService();
        $map=[];
        $map[]=['id','=',$ordersid];
        $info=$service->ordersDetail($map);
        if(empty($info)){
            return jsondata('400','预约订单信息不存在');
        }
        $info['money']=round($info['money']/100,2);
        $info['pay_money']=round($info['pay_money']/100,2);
        $info['refund_money']=round($info['refund_money']/100,2);
        $this->assign(['info'=>$info]);
        return $this->fetch();
    }

    //分配名医
    public function orders_assign(){
        $service=new OrdersService();
        if(request()->isPost() || request()->isAjax()){
            $ordersid=input('post.ordersid','0','intval');
            if($ordersid<=0){
                return jsondata('400','请选择需要分配名医的预约订单');
            }
            return $service->orders_assigning($ordersid);
        }
        $ordersid=input('ordersid','0','intval');
        if($ordersid<=0){
            return jsondata('400','请选择预约订单');
        }
        $map=[];
        $map[]=['id','=',$ordersid];
        $info=$service->ordersDetail($map);
        if(empty($info)){
            return jsondata('400','预约订单信息不存在');
        }
        $dservice=new DoctorService();
        $dmap=[];
        $dmap[]=['isdel','=',2];
        $dmap[]=['status','=',1];
        $doctor_list=$dservice->getDoctorList(2,$dmap,'id,realname',[],20)['list'];
        $this->assign(['info'=>$info,'doctor_list'=>$doctor_list]);
        return $this->fetch();
    }

    //设置费用
    public function orders_setfee(){
        $service=new OrdersService();
        if(request()->isPost() || request()->isAjax()){
            $ordersid=input('post.ordersid','0','intval');
            if($ordersid<=0){
                return jsondata('400','请选择需要设置费用的预约订单');
            }
            return $service->orders_setfeeing($ordersid);
        }
        $ordersid=input('ordersid','0','intval');
        if($ordersid<=0){
            return jsondata('400','请选择预约订单');
        }
        $map=[];
        $map[]=['id','=',$ordersid];
        $info=$service->ordersDetail($map);
        if(empty($info)){
            return jsondata('400','预约订单信息不存在');
        }
        $this->assign(['info'=>$info]);
        return $this->fetch();
    }

    //设置完成
    public function orders_setfinish(){
        $service=new OrdersService();
        if(request()->isPost() || request()->isAjax()){
            $ordersid=input('post.ordersid','','trim');
            $ordersid=trim(str_replace('，',',',$ordersid),',');
            if($ordersid==''){
                return jsondata('400','请选择需要设置完成的订单');
            }
            $ordersid=array_unique(explode(',',$ordersid));
            if(empty($ordersid)){
                return jsondata('400','请选择需要设置完成的订单');
            }
            return $service->orders_setfinishing($ordersid);
        }
        return jsondata('400','网络请求错误');
    }

    //设置积分
    public function orders_setintegral(){
        $service=new OrdersService();
        if(request()->isPost() || request()->isAjax()){
            $ordersid=input('post.ordersid','','trim');
            $ordersid=trim(str_replace('，',',',$ordersid),',');
            if($ordersid==''){
                return jsondata('400','请选择需要设置积分的订单');
            }
            $ordersid=array_unique(explode(',',$ordersid));
            if(empty($ordersid)){
                return jsondata('400','请选择需要设置积分的订单');
            }
            return $service->orders_setintegraling($ordersid);
        }
        $ordersid=input('ordersid','','trim');
        $ordersid=trim(str_replace('，',',',$ordersid),',');
        if($ordersid==''){
            return jsondata('400','请选择需要设置积分的订单');
        }
        $ordersid=array_unique(explode(',',$ordersid));
        if(empty($ordersid)){
            return jsondata('400','请选择需要设置积分的订单');
        }
        $map=[];
        $map[]=['id','=',$ordersid[0]];
        $info=$service->ordersDetail($map);
        if(empty($info)){
            return jsondata('400','预约订单信息不存在');
        }
        $this->assign(['info'=>$info,'ordersid'=>implode(',',$ordersid)]);
        return $this->fetch();
    }

    //设置结算
    public function orders_setsettle(){
        $service=new OrdersService();
        if(request()->isPost() || request()->isAjax()){
            $ordersid=input('post.ordersid','','trim');
            $ordersid=trim(str_replace('，',',',$ordersid),',');
            if($ordersid==''){
                return jsondata('400','请选择需要结算的订单');
            }
            $ordersid=array_unique(explode(',',$ordersid));
            if(empty($ordersid)){
                return jsondata('400','请选择需要结算的订单');
            }
            return $service->orders_setsettling($ordersid);
        }
        return jsondata('400','网络请求错误');
    }

    //关闭订单
    public function orders_close(){
        $service=new OrdersService();
        if(request()->isPost() || request()->isAjax()){
            $ordersid=input('post.ordersid','','trim');
            $ordersid=trim(str_replace('，',',',$ordersid),',');
            if($ordersid==''){
                return jsondata('400','请选择需要关闭的订单');
            }
            $ordersid=array_unique(explode(',',$ordersid));
            if(empty($ordersid)){
                return jsondata('400','请选择需要关闭的订单');
            }
            return $service->orders_closing($ordersid);
        }
        return jsondata('400','网络请求错误');
    }

    //申请退款列表
    public function orders_refundlist(){
        $status=input('status','0','intval');
        $orderno=input('orderno','','trim');
        $applytime_start=input('applytime_start','','trim');
        $applytime_end=input('applytime_end',date('Y-m-d 23:59:59'),'trim');
        $map=[];
        if($status>0){
            $map[]=['refund_status','=',1];
        }
        if($orderno!=''){
            $map[]=['orderno','=',$orderno];
        }
        if($applytime_start!=''){
            $map[]=['create_time','>=',$applytime_start];
        }
        if($applytime_end!=''){
            $map[]=['create_time','<=',$applytime_end];
        }
        $search['status']=$status;
        $search['orderno']=$orderno;
        $search['applytime_start']=$applytime_start;
        $search['applytime_end']=$applytime_end;
        $field='*';
        $orderby=['id'=>'desc'];
        $service=new OrdersService();
        $data=$service->getOrdersRefundList(1,$map,$field,$search,20,$orderby);
        $this->assign(['search'=>$search,'list'=>$data['list'],'count'=>$data['count'],'page'=>$data['page']]);
        return $this->fetch();
    }

    //同意退款
    public function orders_refund_agree(){
        return jsondata('400','暂无退款');
        $service=new OrdersService();
        if(request()->isPost() || request()->isAjax()){
            $refundid=input('post.refundid','0','intval');
            if($refundid<=0){
                return jsondata('400','请选择退款订单');
            }
            return $service->orders_refund_agreeing($refundid);
        }
        $refundid=input('refundid','0','intval');
        if($refundid<=0){
            return jsondata('400','请选择退款订单');
        }
        $map=[];
        $map[]=['id','=',$refundid];
        $info=$service->ordersRefundDetail($map);
        if(empty($info)){
            return jsondata('400','退款订单信息不存在');
        }
        $info['orders_money']=round($info['orders_money']/100,2);
        $info['refund_money']=round($info['refund_money']/100,2);
        $info['refund_applymoney']=round($info['refund_applymoney']/100,2);
        $this->assign(['info'=>$info]);
        return $this->fetch();
    }

    //拒绝退款
    public function orders_refund_refuse(){
        return jsondata('400','暂无退款');
        $service=new OrdersService();
        if(request()->isPost() || request()->isAjax()){
            $refundid=input('post.refundid','0','intval');
            if($refundid<=0){
                return jsondata('400','请选择退款订单');
            }
            return $service->orders_refund_refusing($refundid);
        }
        $refundid=input('refundid','0','intval');
        if($refundid<=0){
            return jsondata('400','请选择退款订单');
        }
        $map=[];
        $map[]=['id','=',$refundid];
        $info=$service->ordersRefundDetail($map);
        if(empty($info)){
            return jsondata('400','退款订单信息不存在');
        }
        $info['orders_money']=round($info['orders_money']/100,2);
        $info['refund_money']=round($info['refund_money']/100,2);
        $info['refund_applymoney']=round($info['refund_applymoney']/100,2);
        $this->assign(['info'=>$info]);
        return $this->fetch();
    }

    //导出订单
    public function orders_export(){
        $doctorid=input('doctorid','0','intval');
        $assistantsid=input('assistantsid','0','intval');
        $basedoctorsid=input('basedoctorsid','0','intval');
        $isassign=input('isassign','0','intval');
        $issetintegral=input('issetintegral','0','intval');
        $issettle=input('issettle','0','intval');
        $status=input('status','0','intval');
        $ispay=input('ispay','0','intval');
        $keyword=input('keyword','','trim');
        $orderno=input('orderno','','trim');
        $applytime_start=input('applytime_start','','trim');
        $applytime_end=input('applytime_end',date('Y-m-d 23:59:59'),'trim');
        $map=[];
        if($doctorid>0){
            $map[]=['doctor_id','=',$doctorid];
        }
        if($assistantsid>0){
            $map[]=['assistant_id','=',$assistantsid];
        }
        if($basedoctorsid>0){
            $map[]=['basedoctor_id','=',$basedoctorsid];
        }
        if($isassign==1){
            $map[]=['doctor_id','>',0];
        }elseif($isassign==2){
            $map[]=['doctor_id','<=',0];
        }
        if($issettle==1){
            $map[]=['issettle','=',1];
        }elseif($issettle==2){
            $map[]=['issettle','=',2];
        }
        if($issetintegral==1){
            $map[]=['doctor_integral','>',0];
        }elseif($issetintegral==2){
            $map[]=['doctor_integral','<=',0];
        }
        if($ispay==1){
            $map[]=['ispay','=',1];
        }elseif($ispay==2){
            $map[]=['ispay','=',2];
        }
        if($status>0){
            $map[]=['status','=',$status];
        }
        // if($status==1){
        //     $map[]=['status','=',1];
        // }elseif($status==2){
        //     $map[]=['status','=',2];
        // }elseif($status==3){
        //     $map[]=['status','=',3];
        // }
        if($keyword!=''){
            $map[]=['realname|mobile','like',"%$keyword%"];
        }
        if($orderno!=''){
            $map[]=['orderno','=',$orderno];
        }
        if($applytime_start!=''){
            $map[]=['create_time','>=',$applytime_start];
        }
        if($applytime_end!=''){
            $map[]=['create_time','<=',$applytime_end];
        }
        $field='*';
        $orderby=['id'=>'desc'];
        $service=new OrdersService();
        return $service->orders_exportdata($map);
    }

}
