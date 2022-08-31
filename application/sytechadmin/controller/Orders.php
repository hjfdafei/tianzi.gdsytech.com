<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\service\OrdersService;
use app\sytechadmin\service\SchoolService;
use app\sytechadmin\service\GradeService;
use app\sytechadmin\service\GoodsService;
use app\sytechadmin\service\BroadbandService;

//订单管理
class Orders extends Sytechadminbase{
    //订单列表
    public function orders_list(){
        $user_id=input('user_id','0','intval');
        $goods_id=input('goods_id','0','intval');
        $school_id=input('school_id','0','intval');
        $grade_id=input('grade_id','0','intval');
        $status=input('status','0','intval');
        $orderno=input('orderno','','trim');
        $keyword=input('keyword','','trim');
        $promoter=input('promoter','','trim');
        $applytime_start=input('applytime_start','','trim');
        $applytime_end=input('applytime_end',date('Y-m-d 23:59:59'),'trim');
        $orders_style=input('orders_style','0','intval');
        if($this->base_admininfo['school_id']>0){
            $school_id=$this->base_admininfo['school_id'];
        }
        $map=[];
        $map[]=['o.isdel','=',2];
        if($school_id>0){
            $map[]=['o.school_id','=',$school_id];
        }
        if($user_id>0){
            $map[]=['o.user_id','=',$user_id];
        }
        if($goods_id>0){
            $map[]=['o.goods_id','=',$goods_id];
        }
        if($grade_id>0){
            $map[]=['o.grade_id','=',$grade_id];
        }
        if($status>0){
            if($status==2){
                $map[]=['o.ispay','=',1];
            }elseif($status==6){
                $map[]=['o.ispay','=',1];
                $map[]=['o.broadband_id','=',0];
            }else{
                $map[]=['o.status','=',$status];
            }
        }
        if($keyword!=''){
            $map[]=['o.realname|o.mobile|o.idcardnum','like',"%$keyword%"];
        }
        if($promoter!=''){
            $map[]=['o.promoter','like',"%$promoter%"];
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
        if($orders_style>0){
            $map[]=['o.orders_style','=',$orders_style];
        }
        $search['user_id']=$user_id;
        $search['goods_id']=$goods_id;
        $search['school_id']=$school_id;
        $search['grade_id']=$grade_id;
        $search['status']=$status;
        $search['keyword']=$keyword;
        $search['promoter']=$promoter;
        $search['orderno']=$orderno;
        $search['applytime_start']=$applytime_start;
        $search['applytime_end']=$applytime_end;
        $search['orders_style']=$orders_style;
        $field='o.*,g.goods_title,b.keyaccount,b.keypassword,b.start_time,b.end_time';
        $orderby=['o.id'=>'desc'];
        $service=new OrdersService();
        $data=$service->getOrdersList(1,$map,$field,$search,20,$orderby);
        $schoolservice=new SchoolService();
        $smap=[];
        if($this->base_admininfo['school_id']>0){
            $smap[]=['id','=',$school_id];
        }
        $sfield='*';
        $sorderby=['sortby'=>'desc','id'=>'desc'];
        $school_list=$schoolservice->getSchoolList(2,$smap,$sfield,[],20,$sorderby)['list'];
        $goodsservice=new GoodsService();
        $gmap=[];
        $gfield='*';
        $gorderby=['goods_sortby'=>'desc','id'=>'desc'];
        $goods_list=$goodsservice->getGoodsList(2,$gmap,$gfield,[],20,$gorderby)['list'];
        $grmap=[];
        if($school_id>0){
            $grmap[]=['school_id','=',$school_id];
        }else{
            $grmap[]=['id','=',0];
        }
        $grfield='*';
        $grorderby=['sortby'=>'desc','id'=>'desc'];
        $grservice=new GradeService();
        $grade_list=$grservice->getGradeList(2,$grmap,$grfield,[],20,$grorderby)['list'];
        $this->assign(['search'=>$search,'list'=>$data['list'],'count'=>$data['count'],'page'=>$data['page'],'school_list'=>$school_list,'grade_list'=>$grade_list,'goods_list'=>$goods_list]);
        return $this->fetch();
    }

    //订单详情
    public function orders_detail(){
        $ordersid=input('ordersid','0','intval');
        if($ordersid<=0){
            return jsondata('400','请选择宽带订单');
        }
        $service=new OrdersService();
        $map=[];
        if($this->base_admininfo['school_id']>0){
            $map[]=['school_id','=',$this->base_admininfo['school_id']];
        }
        $map[]=['isdel','=',2];
        $map[]=['id','=',$ordersid];
        $info=$service->ordersDetail($map);
        if(empty($info)){
            return jsondata('400','宽带订单信息不存在');
        }
        $goodsservice=new GoodsService();
        $broadbandservice=new BroadbandService();
        $schoolservice=new SchoolService();
        $gradeservice=new GradeService();
        $gmap=[];
        $gmap[]=['id','=',$info['goods_id']];
        $goodsinfo=$goodsservice->goodsDetail($gmap);
        $goods_title='';
        if(!empty($goodsinfo)){
            $goods_title=$goodsinfo['goods_title'];
        }
        $bmap=[];
        $bmap[]=['id','=',$info['broadband_id']];
        $broadbandinfo=$broadbandservice->broadbandDetail($bmap);
        $keyaccount='';
        $keypassword='';
        $start_time='';
        $end_time='';
        if(!empty($broadbandinfo)){
            $keyaccount=$broadbandinfo['keyaccount'];
            $keypassword=$broadbandinfo['keypassword'];
            $start_time=$broadbandinfo['start_time'];
            $end_time=$broadbandinfo['end_time'];
        }
        $smap=[];
        $smap[]=['id','=',$info['school_id']];
        $school_info=$schoolservice->schoolDetail($smap);
        $school_name='';
        if(!empty($school_info)){
            $school_name=$school_info['title'];
        }
        $grade_name='';
        if($info['grade_id']>0){
            $gmap=[];
            $gmap[]=['id','=',$info['grade_id']];
            $gmap[]=['school_id','=',$info['school_id']];
            $grade_info=$gradeservice->gradeDetail($gmap);
            if(!empty($grade_info)){
                $grade_name=$grade_info['title'];
            }
        }
        if($school_name!='' && $grade_name!=''){
            $school_name.='-'.$grade_name;
        }
        $orders_stylelist=config('app.orders_style');
        $orders_stylearr=[];
        if(!empty($orders_stylelist)){
            foreach($orders_stylelist as $ov){
                $orders_stylearr[$ov['id']]=$ov['title'];
            }
        }
        $stylename='';
        if(isset($orders_stylearr[$info['orders_style']])){
            $stylename=$orders_stylearr[$info['orders_style']];
        }
        $info['stylename']=$stylename;
        $info['school_name']=$school_name;
        $info['goods_title']=$goods_title;
        $info['keyaccount']=$keyaccount;
        $info['keypassword']=$keypassword;
        $info['start_time']=$start_time;
        $info['end_time']=$end_time;
        $this->assign(['info'=>$info]);
        return $this->fetch();
    }

    //编辑订单
    public function orders_edit(){
        $service=new OrdersService();
        if(request()->isPost() || request()->isAjax()){
            $ordersid=input('post.ordersid','0','intval');
            if($ordersid<=0){
                return jsondata('400','请选择宽带订单');
            }
            return $service->orders_verify($ordersid,$this->base_admininfo);
        }
        $ordersid=input('ordersid','0','intval');
        if($ordersid<=0){
            return jsondata('400','请选择宽带订单');
        }
        $map=[];
        if($this->base_admininfo['school_id']>0){
            $map[]=['school_id','=',$this->base_admininfo['school_id']];
        }
        $map[]=['id','=',$ordersid];
        $map[]=['isdel','=',2];
        $info=$service->ordersDetail($map);
        if(empty($info)){
            return jsondata('400','宽带订单信息不存在');
        }
        $info['money']=round($info['money']/100,2);
        $schoolservice=new SchoolService();
        $smap=[];
        if($this->base_admininfo['school_id']>0){
            $smap[]=['id','=',$this->base_admininfo['school_id']];
        }
        $sfield='*';
        $sorderby=['sortby'=>'desc','id'=>'desc'];
        $school_list=$schoolservice->getSchoolList(2,$smap,$sfield,[],20,$sorderby)['list'];
        $grmap=[];
        $grmap[]=['school_id','=',$info['school_id']];
        $grfield='*';
        $grorderby=['sortby'=>'desc','id'=>'desc'];
        $grservice=new GradeService();
        $grade_list=$grservice->getGradeList(2,$grmap,$grfield,[],20,$grorderby)['list'];
        $this->assign(['info'=>$info,'school_list'=>$school_list,'grade_list'=>$grade_list]);
        return $this->fetch();
    }

    //清空订单的宽带信息
    public function orders_clearbroadband(){
        $service=new OrdersService();
        if(request()->isPost() || request()->isAjax()){
            $ordersid=input('post.ordersid','','trim');
            $ordersid=trim(str_replace('，',',',$ordersid),',');
            if($ordersid==''){
                return jsondata('400','请选择需要清空宽带信息的订单');
            }
            $ordersid=array_unique(explode(',',$ordersid));
            if(empty($ordersid)){
                return jsondata('400','请选择需要清空宽带信息的订单');
            }
            return $service->orders_clearing($ordersid,$this->base_admininfo);
        }
        return jsondata('400','网络请求错误');
    }

    //分配宽带账号
    public function orders_setbroadband(){
        $service=new OrdersService();
        if(request()->isPost() || request()->isAjax()){
            $ordersid=input('post.ordersid','0','intval');
            if($ordersid<=0){
                return jsondata('400','请选择宽带订单');
            }
            return $service->orders_settingbroadband($ordersid,$this->base_admininfo);
        }
        $ordersid=input('ordersid','0','intval');
        if($ordersid<=0){
            return jsondata('400','请选择宽带订单');
        }
        $service=new OrdersService();
        $map=[];
        $map[]=['id','=',$ordersid];
        $map[]=['isdel','=',2];
        $info=$service->ordersDetail($map);
        if(empty($info)){
            return jsondata('400','宽带订单信息不存在');
        }
        $broadbandservice=new BroadbandService();
        $bmap=[];
        $bmap[]=['id','=',$info['broadband_id']];
        $broadbandinfo=$broadbandservice->broadbandDetail($bmap);
        $keyaccount='';
        $keypassword='';
        $start_time='';
        $end_time='';
        if(!empty($broadbandinfo)){
            $keyaccount=$broadbandinfo['keyaccount'];
            $keypassword=$broadbandinfo['keypassword'];
            $start_time=$broadbandinfo['start_time'];
            $end_time=$broadbandinfo['end_time'];
        }
        $info['keyaccount']=$keyaccount;
        $info['keypassword']=$keypassword;
        $info['start_time']=$start_time;
        $info['end_time']=$end_time;
        $this->assign(['info'=>$info]);
        return $this->fetch();
    }

    //设置宽带时间
    public function orders_settime(){
        $service=new OrdersService();
        if(request()->isPost() || request()->isAjax()){
            $ordersid=input('post.ordersid','0','intval');
            if($ordersid<=0){
                return jsondata('400','请选择宽带订单');
            }
            return $service->orders_settingtime($ordersid,$this->base_admininfo);
        }
        $ordersid=input('ordersid','0','intval');
        if($ordersid<=0){
            return jsondata('400','请选择宽带订单');
        }
        $service=new OrdersService();
        $map=[];
        $map[]=['id','=',$ordersid];
        $map[]=['isdel','=',2];
        $info=$service->ordersDetail($map);
        if(empty($info)){
            return jsondata('400','宽带订单信息不存在');
        }
        $broadbandservice=new BroadbandService();
        $bmap=[];
        $bmap[]=['id','=',$info['broadband_id']];
        $broadbandinfo=$broadbandservice->broadbandDetail($bmap);
        $keyaccount='';
        $keypassword='';
        $start_time='';
        $end_time='';
        if(!empty($broadbandinfo)){
            $keyaccount=$broadbandinfo['keyaccount'];
            $keypassword=$broadbandinfo['keypassword'];
            $start_time=$broadbandinfo['start_time'];
            $end_time=$broadbandinfo['end_time'];
        }
        $info['keyaccount']=$keyaccount;
        $info['keypassword']=$keypassword;
        $info['start_time']=$start_time;
        $info['end_time']=$end_time;
        $this->assign(['info'=>$info]);
        return $this->fetch();
    }

    //导出订单
    public function orders_export(){
        $user_id=input('user_id','0','intval');
        $goods_id=input('goods_id','0','intval');
        $school_id=input('school_id','0','intval');
        $grade_id=input('grade_id','0','intval');
        $status=input('status','0','intval');
        $orderno=input('orderno','','trim');
        $keyword=input('keyword','','trim');
        $applytime_start=input('applytime_start','','trim');
        $applytime_end=input('applytime_end',date('Y-m-d 23:59:59'),'trim');
        $orders_style=input('orders_style','0','intval');
        if($this->base_admininfo['school_id']>0){
            $school_id=$this->base_admininfo['school_id'];
        }
        $map=[];
        $map[]=['o.isdel','=',2];
        if($school_id>0){
            $map[]=['o.school_id','=',$school_id];
        }
        if($grade_id>0){
            $map[]=['o.grade_id','=',$grade_id];
        }
        if($user_id>0){
            $map[]=['o.user_id','=',$user_id];
        }
        if($goods_id>0){
            $map[]=['o.goods_id','=',$goods_id];
        }
        if($status>0){
            if($status==2){
                $map[]=['o.ispay','=',1];
            }elseif($status==6){
                $map[]=['o.ispay','=',1];
                $map[]=['o.broadband_id','=',0];
            }else{
                $map[]=['o.status','=',$status];
            }
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
        if($orders_style>0){
            $map[]=['o.orders_style','=',$orders_style];
        }
        $search['user_id']=$user_id;
        $search['goods_id']=$goods_id;
        $search['school_id']=$school_id;
        $search['grade_id']=$grade_id;
        $search['status']=$status;
        $search['keyword']=$keyword;
        $search['orderno']=$orderno;
        $search['applytime_start']=$applytime_start;
        $search['applytime_end']=$applytime_end;
        $search['orders_style']=$orders_style;
        $field='o.*,g.goods_title,b.keyaccount,b.keypassword,b.start_time,b.end_time';
        $orderby=['o.id'=>'desc'];
        $service=new OrdersService();
        return $service->orders_exportdata($map,$field);
    }

    //删除订单
    public function orders_del(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $ordersid=input('post.ordersid','','trim');
            if($ordersid==''){
                return jsondata('400','请选择要删除的订单信息');
            }
            $ordersid=explode(',',trim($ordersid,','));
            if(empty($ordersid)){
                return jsondata('400','请选择要删除的订单信息');
            }
            $ordersid=array_unique($ordersid);
            $service=new OrdersService();
            return $service->orders_delete($ordersid,$this->base_admininfo);
        }
        return jsondata('400','网络错误');
    }

}
