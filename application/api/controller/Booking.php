<?php
namespace app\api\controller;
use think\facade\Request;
use think\Controller;
use think\Db;
use think\db\Query;
use app\api\service\QrcodeService;
class Booking extends Controller{
    public function _empty(){
        return json(['code'=>'404','msg'=>'网络错误']);
    }

    //预约信息列表
    public function bookingList(){
        $companyCode=input('companyCode','','trim');
        $startBookDate=input('startBookDate',date('Y-m-d'),'trim');
        $endBookDate=input('endBookDate',date('Y-m-d'),'trim');
        $cardType=input('cardType','','trim');
        $idCard=input('idCard','','trim');
        $status=input('status','1','intval');
        $bookCode=input('bookCode','','trim');
        $cardType=1;

        if($companyCode==''){
            return jsondata('400','请输入机构编码');
        }
        if($companyCode!='DQWSZ'){
            return jsondata('400','请输入机构编码');
        }
        $code='400';
        $body='';
        $map=[];
        if($idCard!=''){
            $map2[]=['idcardnum','=',$idCard];
            $userinfo=DB::name('meeting_user')->where($map2)->find();
            if(!empty($userinfo)){
                $map[]=['mr.openid','=',$userinfo['openid']];
            }
        }
        if($bookCode!=''){
            $map[]=['mr.bookcode','=',$bookCode];
        }
        if($startBookDate!=''){
            $map[]=['mr.meetingymd','>=',date('Ymd',strtotime($startBookDate))];
        }
        if($endBookDate!=''){
            $map[]=['mr.meetingymd','<=',date('Ymd',strtotime($endBookDate))];
        }
        if(!in_array($status,[1,2,3,6])){
            $status=1;
        }
        if($status==1){
            $map[]=['mr.status','=','1'];
        }elseif($status==2){
            $map[]=['mr.status','=','3'];
        }elseif($status==3){
            $map[]=['mr.status','=','2'];
        }elseif($status==6){
            $map[]=['mr.status','=','4'];
        }
        $field='mr.*,u.realname,u.birthday,u.sex,u2.realname as parentname,u2.mobile,u2.idcardnum,u2.address';
        $orderby=['mr.status'=>'asc','mr.id'=>'desc'];
        $count=DB::name('meeting_record mr')->field($field)->join('__MEETING_USER__ u','u.id=mr.memberid','left')->join('__MEETING_USER__ u2','u2.openid=mr.openid','left')->where($map)->count();
        $list=DB::name('meeting_record mr')->field($field)->join('__MEETING_USER__ u','u.id=mr.memberid','left')->join('__MEETING_USER__ u2','u2.openid=mr.openid','left')->where($map)->order($orderby)->select();
        if(empty($list)){
            $outdata['code']=$code;
            $outdata['body']=$body;
            echo json_encode($outdata,256);
            return ;
        }
        $bodydata=[];
        foreach($list as $k=>$v){
            $sex=1;
            $age='';
            $agedate=ymd4date($v['birthday']);
            if($agedate['year']>0){
                $age.=$agedate['year'].'岁';
            }
            if($agedate['month']>0){
                $age.=$agedate['year'].'个月';
            }
            if($v['sex']=='女'){
                $sex=2;
            }
            $extime=strtotime($v['meetingdate'].' '.$v['meetingendtime'])+59+60*15;
            if(time()>$extime){
                $v['status']=4;
                $v['expiretime']=date('Y-m-d H:i:s');
                DB::name('meeting_record')->where([['id','=',$v['id']],['status','=','1']])->update(['status'=>4,'expiretime'=>date('Y-m-d H:i:s'),'upsource'=>'DQWSZ_1']);
            }

            $status=$v['status'];
            if($v['status']==2){
                $status=3;
            }elseif($v['status']==3){
                $status=2;
            }elseif($v['status']==4){
                $status=6;
            }
            $bodydata[]=[
                'name'=>$v['realname'],
                'cardType'=>1,
                'idCard'=>$v['idcardnum'],
                'sex'=>$sex,
                'age'=>$age,
                'mobile'=>$v['mobile'],
                'companyCode'=>$companyCode,
                'companyName'=>'东区卫生服务中心',
                'bookDate'=>$v['meetingdate'],
                'timeSlice'=>$v['meetingtstarttime'].'-'.date('H:i',$extime),//$v['meetingendtime'],
                'status'=>$status,
                'bookCode'=>$v['bookcode'],
                'amount'=>$count,
                'count'=>$k+1,
            ];
        }
        $code=200;
        $body=$bodydata;
        $outdata['code']=$code;
        $outdata['body']=$body;
        echo json_encode($outdata,256);
        return ;
    }

    //预约信息详情
    public function bookingUpdate(){
        $companyCode=input('companyCode','0','trim');
        $list=input('list','','strip_tags');
        if($companyCode==''){
            return jsondata('400','请输入机构编码');
        }
        if($companyCode!='DQWSZ'){
            return jsondata('400','请输入机构编码');
        }
        if($list==''){
            return jsondata('400','请输入修改数据');
        }
        $list=json_decode($list,true);
        if(empty($list)){
            return jsondata('4000','请输入修改数据');
        }
        DB::startTrans();
        $num=0;
        foreach($list as $v){
            if($v['bookCode']==''){
                return jsondata('400','请选择预约信息');
            }
            if(!in_array($v['status'],[2,3,6])){
                return jsondata('400','请选择预约信息状态');
            }
            $map=[];
            $map[]=['status','=',1];
            $map[]=['bookcode','=',$v['bookCode']];
            $signtime=$v['signTime'];
            if($v['signTime']<date('Y-m-d H:i:s',(time()-300)) || $v['signTime']>date('Y-m-d')){
                $signtime=date('Y-m-d H:i:s');
            }
            $update_data=[];
            if($v['status']==2){
                $update_data['status']=3;
                $update_data['canceltime']=$signtime;
            }elseif($v['status']==3){
                $update_data['status']=2;
                $update_data['signtime']=$signtime;
            }elseif($v['status']==6){
                $update_data['status']=4;
                $update_data['expiretime']=$signtime;
            }
            $update_data['update_time']=date('Y-m-d H:i:s');
            $update_data['upsource']='DQWSZ_2';
            $res=DB::name('meeting_record')->where($map)->update($update_data);
            if($res){
                $num++;
            }
        }
        if($num==count($list) && $num>0){
            DB::commit();
            return jsondata('200','更新成功');
        }else{
            DB::rollback();
            return jsondata('400','更新失败');
        }
    }

    //获取banner
    public function getBanner(){
        $data['data']=array();
        $map[]=['pid','=',0];
        $map[]=['level','=',0];
        $map[]=['type','=',0];
        $field='id,pid,banner,img,type,title';
        $banner=DB::name('menu')->field($field)->where($map)->whereNull('deleted_at')->find();
        if(!empty($banner)){
            if($banner['banner']!=''){
                $banner['banner']='https://zsjkdq.gdsytech.com/admin/static'.$banner['banner'];
            }
            if($banner['img']!=''){
                $banner['img']='https://zsjkdq.gdsytech.com/admin/static'.$banner['img'];
            }
            $data['data']=$banner;
        }
        return jsondata('0001','获取成功',$data);
    }

    //获取菜单
    public function getMenu(){
        $parentid=input('parentid','0','intval');
        $data['data']=array();
        //$map['pid']=$perentid;
        // if($perentid==18){
        //     $map['id']=array('not in','19,21');
        // }
        // if($perentid==23){
        //     $map['id']=array('not in','24,25');
        // }
        // if($perentid==17){
        //     $map['id']=array('not in','20');
        // }
        // $map['level']=array('gt',0);
        // $map['type']=array('gt',0);
        // $map['deleted_at']=array('exp','is null');
        $map[]=['pid','=',$parentid];
        $map[]=['level','>',0];
        $map[]=['type','>',0];
        $field='id,pid,banner,img,type,title';
        $list=DB::name('menu')->field($field)->where($map)->whereNull('deleted_at')->order(['sort'=>'asc'])->select();
        if(!empty($list)){
            foreach($list as &$v){
                $v['is_child']=0;
                $map=array();
                $map[]=['pid','=',$v['id']];
                $hasson=DB::name('menu')->where($map)->whereNull('deleted_at')->find();
                if(!empty($hasson)){
                    $v['is_child']=1;
                }
                if($v['banner']!=''){
                    $v['banner']='https://zsjkdq.gdsytech.com/admin/static'.$v['banner'];
                }
                if($v['img']!=''){
                    $v['img']='https://zsjkdq.gdsytech.com/admin/static'.$v['img'];
                }
            }
            $data['data']=$list;
        }
        return jsondata('0001','获取成功',$data);
    }

}
