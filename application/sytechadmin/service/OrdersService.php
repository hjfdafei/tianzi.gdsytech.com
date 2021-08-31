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
    public function getOrdersList($type=1,$map=[],$field='o.*',$search=[],$pernum=20,$orderby=['o.id'=>'asc']){
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
            $orders_stylelist=config('app.orders_style');
            $orders_stylearr=[];
            if(!empty($orders_stylelist)){
                foreach($orders_stylelist as $ov){
                    $orders_stylearr[$ov['id']]=$ov['title'];
                }
            }
            $list=DB::name('orders o')->field($field)->join('__GOODS__ g','g.id=o.goods_id','left')->join('__BROADBAND__ b','b.id=o.broadband_id','left')->where($map)->order($orderby)->paginate($pernum,false,['query'=>$search])->each(function($item,$key) use($statusname,$ispayname,$isrefundname,$schoolnamearr,$orders_stylearr){
                $schoolname='';
                if(isset($schoolnamearr[$item['school_id']])){
                    $schoolname=$schoolnamearr[$item['school_id']];
                }
                if($item['start_time']!=''){
                    $item['start_time']=date('Y.m.d',strtotime($item['start_time']));
                }
                if($item['end_time']!=''){
                    $item['end_time']=date('Y.m.d',strtotime($item['end_time']));
                }
                $stylename='';
                if(isset($orders_stylearr[$item['orders_style']])){
                    $stylename=$orders_stylearr[$item['orders_style']];
                }
                $item['stylename']=$stylename;
                $item['money']=round($item['money']/100,2);
                $item['discount_money']=round($item['discount_money']/100,2);
                $item['pay_money']=round($item['pay_money']/100,2);
                $item['statusname']=$statusname[$item['status']];
                $item['ispayname']=$ispayname[$item['ispay']];
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
        return $info;
    }

    //编辑订单
    public function orders_verify($id,$admininfo){
        $id=intval($id);
        $school_id=input('post.school_id','0','intval');
        $realname=input('post.realname','','trim');
        $mobile=input('post.mobile','','trim');
        $idcardnum=input('post.idcardnum','','trim');
        $department=input('post.department','','trim');
        $studentnumber=input('post.studentnumber','','trim');
        $address=input('post.address','','trim');
        $money=input('post.money','','trim');
        $money=$money*100;
        if($admininfo['school_id']>0){
            $school_id=$admininfo['school_id'];
        }
        if($school_id<=0){
            return jsondata('400','请选择所在校区');
        }
        if($realname==''){
            return jsondata('400','请输入姓名');
        }
        if($mobile==''){
            return jsondata('400','请输入联系电话');
        }
        if($idcardnum==''){
            return jsondata('400','请输入身份证号码');
        }
        if($department==''){
            return jsondata('400','请输入院系');
        }
        if($studentnumber==''){
            return jsondata('400','请输入学号');
        }
        if($address==''){
            return jsondata('400','请输入宿舍地址');
        }
        if(round($money,2)<=0){
            return jsondata('400','请输入正确金额');
        }
        $schoolservice=new SchoolService();
        $smap=[];
        $smap[]=['id','=',$school_id];
        $school_info=$schoolservice->schoolDetail($smap);
        if(empty($school_info)){
            return jsondata('400','选择的校区不存在');
        }
        $map=[];
        if($admininfo['school_id']>0){
            $map[]=['school_id','=',$admininfo['school_id']];
        }
        $map[]=['id','=',$id];
        $map[]=['isdel','=',2];
        $info=$this->ordersDetail($map);
        if(empty($info)){
            return jsondata('400','宽带订单信息不存在');
        }
        $data=[
            'school_id'=>$school_id,
            'realname'=>$realname,
            'mobile'=>$mobile,
            'idcardnum'=>$idcardnum,
            'department'=>$department,
            'studentnumber'=>$studentnumber,
            'address'=>$address,
            'update_time'=>date('Y-m-d H:i:s'),
        ];
        if($info['ispay']!=1){
            $data['money']=$money;
        }
        $map=[];
        $map[]=['id','=',$info['id']];
        $res=DB::name('orders')->where($map)->update($data);
        if($res){
            return jsondata('200','更新订单信息成功');
        }else{
            return jsondata('400','更新订单信息失败,请重试');
        }
    }

    //清空订单宽带信息
    public function orders_clearing($id,$admininfo){
        $num=0;
        DB::startTrans();
        foreach($id as $v){
            $map=[];
            if($admininfo['school_id']>0){
                $map[]=['school_id','=',$admininfo['school_id']];
            }
            $map[]=['id','=',intval($v)];
            $map[]=['isdel','=',2];
            $info=$this->ordersDetail($map);
            if($info['broadband_id']>0){
                $oudata=[];
                $oudata['broadband_id']=0;
                $oudata['update_time']=date('Y-m-d H:i:s');
                $oumap=[];
                $oumap[]=['id','=',$info['id']];
                $res=DB::name('orders')->where($oumap)->update($oudata);
                if($res){
                    $num++;
                }
                $budata=[];
                $budata['isuse']=2;
                $budata['use_time']='';
                $budata['start_time']='';
                $budata['end_time']='';
                $budata['update_time']=date('Y-m-d H:i:s');
                $bumap=[];
                $bumap[]=['id','=',$info['broadband_id']];
                $res2=DB::name('broadband')->where($bumap)->update($budata);
            }
        }
        if($num>0){
            DB::commit();
            return jsondata('200','清空订单宽带信息成功');
        }else{
            DB::rollback();
            return jsondata('400','清空订单宽带信息失败,请重试');
        }
    }

    //设置宽带账号
    public function orders_settingbroadband($id,$admininfo){
        $id=intval($id);
        $keyaccount=input('post.keyaccount','','trim');
        $keypassword=input('post.keypassword','','trim');
        $applytime_start=input('post.applytime_start','','trim');
        $applytime_end=input('post.applytime_end','','trim');

        if($keyaccount==''){
            return jsondata('400','请输入宽带账号');
        }
        if($keypassword==''){
            return jsondata('400','请输入宽带密码');
        }
        $map=[];
        if($admininfo['school_id']>0){
            $map[]=['school_id','=',$admininfo['school_id']];
        }
        $map[]=['id','=',$id];
        $map[]=['isdel','=',2];
        $info=$this->ordersDetail($map);
        if(empty($info)){
            return jsondata('400','宽带订单信息不存在');
        }
        if($info['ispay']!=1){
            return jsondata('400','订单还没支付,暂不支持分配宽带信息');
        }
        if(in_array($info['status'],[4,5])){
            return jsondata('400','订单已取消,暂不支持分配宽带信息');
        }
        $bservice=new BroadbandService();
        $bmap=[];
        if($admininfo['school_id']>0){
            $bmap[]=['school_id','=',$admininfo['school_id']];
        }
        $bmap[]=['keyaccount','=',$keyaccount];
        $bmap[]=['keypassword','=',$keypassword];
        $bmap[]=['isuse','=',2];
        $bmap[]=['status','=',1];
        $broadbandinfo=$bservice->broadbandDetail($bmap);
        if(empty($broadbandinfo)){
            return jsondata('400','宽带账号密码信息不存在,或者宽带账号密码已被使用');
        }
        if($info['broadband_id']>0){
            return jsondata('400','订单已绑定宽带信息，如要重新换绑请先清空之前绑定的宽带信息');
        }
        DB::startTrans();
        $oumap=[];
        $oumap[]=['id','=',$info['id']];
        $oudata=[];
        $oudata['broadband_id']=$broadbandinfo['id'];
        $oudata['status']=3;
        $oudata['finish_time']=date('Y-m-d H:i:s');
        $oudata['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('orders')->where($oumap)->update($oudata);
        $bumap=[];
        $bumap[]=['id','=',$broadbandinfo['id']];
        $budata=[];
        $budata['isuse']=1;
        $budata['use_time']=date('Y-m-d H:i:s');
        $budata['start_time']=$applytime_start;
        $budata['end_time']=$applytime_end;
        $budata['update_time']=date('Y-m-d H:i:s');
        $res2=DB::name('broadband')->where($bumap)->update($budata);
        if($res && $res2){
            DB::commit();
            //send_broadbandtpl($info['openid'],$info['realname'],$info['orderno']);
            send_mini_broadbandtpl($info['openid'],'宽带安装',$info['realname'],round($info['money']/100,2),$info['id']);
            return jsondata('200','分配宽带账号成功');
        }else{
            DB::rollback();
            return jsondata('400','分配宽带账号失败,请重试');
        }
    }

    //设置宽带时间
    public function orders_settingtime($id,$admininfo){
        $id=intval($id);
        $applytime_start=input('post.applytime_start','','trim');
        $applytime_end=input('post.applytime_end','','trim');
        if($applytime_start==''){
            return jsondata('400','请输入宽带生效开始时间');
        }
        if($applytime_end==''){
            return jsondata('400','请输入宽带生效结束时间');
        }
        if($applytime_end<$applytime_start){
            return jsondata('400','请选择正确时间段');
        }
        $map=[];
        $map[]=['id','=',$id];
        $map[]=['isdel','=',2];
        $info=$this->ordersDetail($map);
        if(empty($info)){
            return jsondata('400','宽带订单信息不存在');
        }
        if($info['broadband_id']<=0){
            return jsondata('400','订单暂未绑定宽带信息，无需设置时间');
        }
        $bservice=new BroadbandService();
        $bmap=[];
        if($admininfo['school_id']>0){
            $bmap[]=['school_id','=',$admininfo['school_id']];
        }
        $bmap[]=['id','=',$info['broadband_id']];
        $bmap[]=['isuse','=',1];
        $broadbandinfo=$bservice->broadbandDetail($bmap);
        if(empty($broadbandinfo)){
            return jsondata('400','宽带账号密码信息不存在,请检查');
        }
        $bumap=[];
        $bumap[]=['id','=',$broadbandinfo['id']];
        $budata=[];
        $budata['start_time']=$applytime_start;
        $budata['end_time']=$applytime_end;
        $budata['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('broadband')->where($bumap)->update($budata);
        if($res){
            return jsondata('200','宽带生效时间成功');
        }else{
            return jsondata('400','宽带生效时间失败,请重试');
        }
    }

    //导出订单数据
    public function orders_exportdata($map,$field='o.*'){
        if(empty($map)){
            echo '<script>alert("暂无数据");window.history.go(-1)</script>';
            return ;
        }
        $list=$this->getOrdersList(2,$map,$field);
        if(empty($list['list'])){
            echo '<script>alert("暂无数据");window.history.go(-1)</script>';
            return ;
        }
        $listdata=$list['list'];
        $filename='订单信息表';
        $head=['所在校区','订单类型','订单号','姓名','联系电话','身份证号码','院系','学号','宿舍地址','推荐人','续费宽带账号','宽带套餐','宽带账号','宽带密码','宽带有效期','应付金额','优惠金额','实付金额','支付时间','订单状态','下单时间'];
        $data=[];
        $statusnamearr=['1'=>'待支付','2'=>'已支付','3'=>'已发放','4'=>'已取消','5'=>'取消中'];
        $schoolservice=new SchoolService();
        $smap=[];
        $sfield='*';
        $sorderby=['sortby'=>'desc','id'=>'desc'];
        $school_list=$schoolservice->getSchoolList(2,$smap,$sfield,[],20,$sorderby)['list'];
        if(!empty($school_list)){
            foreach($school_list as $v){
                $schoolnamearr[$v['id']]=$v['title'];
                unset($v);
            }
        }
        $orders_stylelist=config('app.orders_style');
        $orders_stylearr=[];
        if(!empty($orders_stylelist)){
            foreach($orders_stylelist as $ov){
                $orders_stylearr[$ov['id']]=$ov['title'];
            }
        }
        foreach($listdata as $v){
            $schoolname='';
            if(isset($schoolnamearr[$v['school_id']])){
                $schoolname=$schoolnamearr[$v['school_id']];
            }
            $stylename='';
            if(isset($orders_stylearr[$v['orders_style']])){
                $stylename=$orders_stylearr[$v['orders_style']];
            }
            $start_time='';
            $end_time='';
            if($v['start_time']!=''){
                $start_time=date('Y.m.d',strtotime($v['start_time']));
            }
            if($v['end_time']!=''){
                $end_time=date('Y.m.d',strtotime($v['end_time']));
            }
            $money=round($v['money']/100,2);
            $discount_money=round($v['discount_money']/100,2);
            $pay_money=round($v['pay_money']/100,2);
            $statusname=$statusnamearr[$v['status']];
            $data[]=[$schoolname,$stylename,"\t".$v['orderno'],$v['realname'],"\t".$v['mobile'],"\t".$v['idcardnum'],$v['department'],$v['studentnumber'],$v['address'],$v['promoter'],$v['broadband_account'],$v['goods_title'],$v['keyaccount'],$v['keypassword'],"\t".$start_time.'--'."\t".$end_time,$money,$discount_money,$pay_money,"\t".$v['pay_time'],$statusname,"\t".$v['create_time']];
        }
        exportdatas($filename,$head,$data);
        return ;
    }

    //删除订单
    public function orders_delete($id,$admininfo){
        $num=0;
        $truedelid=[];
        foreach($id as $v){
            $map=[];
            if($admininfo['school_id']>0){
                $map[]=['school_id','=',$admininfo['school_id']];
            }
            $map[]=['id','=',intval($v)];
            $map[]=['isdel','=',2];
            $info=$this->ordersDetail($map);
            if(!empty($info)){
                $truedelid[]=$info['id'];
            }
        }
        if(empty($truedelid)){
            return jsondata('400','请选择要删除的订单');
        }
        $oumap=[];
        $oumap[]=['id','in',$truedelid];
        $oumap[]=['isdel','=',2];
        $oudata=[];
        $oudata['isdel']=1;
        $oudata['del_time']=date('Y-m-d H:i:s');
        $oudata['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('orders')->where($oumap)->update($oudata);
        if($res){
            return jsondata('200','删除订单成功');
        }else{
            return jsondata('400','删除订单失败,请重试');
        }
    }

}
