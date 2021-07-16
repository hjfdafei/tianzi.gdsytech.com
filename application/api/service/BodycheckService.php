<?php
namespace app\api\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\api\service\QrcodeService;
use app\api\service\CommonmeetService;
//体检预约管理
class BodycheckService extends Base{
    //获取个人信息 通过openid获取个人信息
    public function info4openid($openid,$field='*'){
        $info=[];
        if($openid!=''){
            $map=[];
            $map[]=['open_id','=',$openid];
            $map[]=['deleted_at','=',NULL];
            $info=DB::name('user_basic')->field($field)->where($map)->find();
        }
        return $info;
    }

    //获取个人信息 通过手机号码获取个人信息
    public function info4mobile($mobile,$field='*'){
        $info=[];
        if($mobile!=''){
            $map=[];
            $map[]=['phone','=',$mobile];
            $map[]=['deleted_at','=',NULL];
            $info=DB::name('user_basic')->field($field)->where($map)->find();
        }
        return $info;
    }

    //提交预约数据处理
    public function appoint_deal($baseuserinfo){
        $timenamearr=[];
        $timeidarr=[];
        foreach(config('fixtime') as $v){
            $timeidarr[]=$v['id'];
            $timenamearr[$v['id']]=array($v['start'],$v['end']);
        }
        $meetingdate=input('post.meetingdate',date('Y-m-d'),'trim');
        $meetingtimeid=input('post.meetingtimeid','0','intval');
        if($meetingdate==''){
            return jsondata('0051','请选择预约日期');
        }
        if($meetingdate<date('Y-m-d')){
            return jsondata('0051','请选择预约日期');
        }
        if(strtotime($meetingdate)>time()+60*60*24*31){
            return jsondata('0051','只能预约一个月的日期,请知悉');
        }
        if($meetingtimeid<=0){
            return jsondata('0052','请选择预约时间段');
        }
        if(!in_array($meetingtimeid,$timeidarr)){
            return jsondata('0052','请选择预约时间段');
        }
        if(strtotime($meetingdate.' '.$timenamearr[$meetingtimeid][1])<time()){
            return jsondata('0052','你选择预约时间段'.$timenamearr[$meetingtimeid][0].'-'.$timenamearr[$meetingtimeid][1].'已过');
        }
        $meetingconfig=DB::name('meeting_config')->where([['type','=',2],['id','=',2]])->find();
        if($meetingconfig['isopen']==2){
            if(date('Y-m-d H:i:s')<$meetingconfig['close_endtime'] && date('Y-m-d H:i:s')>$meetingconfig['close_starttime']){
                return jsondata('0056',date('Y-m-d H:i',strtotime($meetingconfig['close_starttime'])).'至'.date('Y-m-d H:i',strtotime($meetingconfig['close_endtime'])).'暂不开放预约');
            }
        }
        $map=[];
        $map[]=['meetstyle','=','2'];
        $map[]=['noymd','=',date('Ymd',strtotime($meetingdate))];
        $hasnodate=DB::name('meeting_nodates')->where($map)->find();
        if(!empty($hasnodate)){
            return jsondata('0056','今天暂不开放预约,请选择别的日期');
        }
        $commonservice=new CommonmeetService();
        // $today_meetingnum=$commonservice->getbookablenum($meetingdate,0,2);
        // if($today_meetingnum<=0){
        //     return jsondata('0056','今天预约已满,请选择别的日期');
        // }
        $today_meetingtimenum=$commonservice->getbookablenum($meetingdate,$meetingtimeid,2);
        if($today_meetingtimenum<=0){
            return jsondata('0056','选择的时段'.implode('-',$timenamearr[$meetingtimeid]).'预约已满,请选择别的时段');
        }
        $map=[];
        $map[]=['openid','=',$baseuserinfo['open_id']];
        $map[]=['meetingymd','=',date('Ymd',strtotime($meetingdate))];
        $map[]=['status','in',[1,2]];
        $meetinginfo=DB::name('meeting_record')->where($map)->find();
        if(!empty($meetinginfo)){
            return jsondata('0059','你已经预约过'.date('Y-m-d',strtotime($meetingdate)).'了');
        }
        $bookcode=$this->getbookcode();
        $data=[
            'openid'=>$baseuserinfo['open_id'],
            'meetstyle'=>2,
            'memberid'=>0,
            'vaccineid'=>0,
            'vaccinename'=>'',
            'bookcode'=>$bookcode,
            'meetingdate'=>date('Y-m-d',strtotime($meetingdate)),
            'meetingtstarttime'=>$timenamearr[$meetingtimeid][0],
            'meetingendtime'=>$timenamearr[$meetingtimeid][1],
            'meetingtimeid'=>$meetingtimeid,
            'meetingnum'=>1,
            'meetingymd'=>date('Ymd',strtotime($meetingdate)),
            'meetingymdh'=>date('YmdH',strtotime($meetingdate.' '.$timenamearr[$meetingtimeid][0])),
            'status'=>1,
            'create_time'=>date('Y-m-d H:i:s'),
        ];
        $res=DB::name('meeting_record')->insertGetId($data);
        if($res){
            return jsondata('0001','预约成功');
        }else{
            return jsondata('0059','预约失败,请重试');
        }
    }

    //生成预约码
    public function getbookcode(){
        $bookcode=createnum4rand(mt_rand(0,99999999999),11);
        $map=[];
        $map[]=['bookcode','=',$bookcode];
        $meetinginfo=DB::name('meeting_record')->where($map)->find();
        if(!empty($meetinginfo)){
            $this->getbookcode();
        }
        return $bookcode;
    }

    //体检预约列表
    public function getappoint_list($map=[],$field='*',$type=1,$start=0,$limit=10,$orderby=['mr.id'=>'asc']){
        $list=array();
        $count=0;
        $count=DB::name('meeting_record mr')->join('__USER_BASIC__ u','u.open_id=mr.openid','left')->where($map)->count();
        if($type==1){
            $list=DB::name('meeting_record mr')->field($field)->join('__USER_BASIC__ u','u.open_id=mr.openid','left')->where($map)->order($orderby)->limit($start,$limit)->select();
        }else{
            $list=DB::name('meeting_record mr')->field($field)->join('__USER_BASIC__ u','u.open_id=mr.openid','left')->where($map)->order($orderby)->select();
        }
        $data['list']=$list;
        $data['count']=$count;
        return $data;
    }

    //体检预约详情
    public function getappoint_detail($map=[],$field='*'){
        $info=[];
        $info=DB::name('meeting_record mr')->field($field)->join('__USER_BASIC__ u','u.open_id=mr.openid','left')->where($map)->find();
        return $info;
    }

    //取消预约
    public function appoint_cancel($baseuserinfo,$meetingid){
        $map[]=['mr.openid','=',$baseuserinfo['open_id']];
        $map[]=['mr.id','=',$meetingid];
        $map[]=['mr.status','=','1'];
        $map[]=['mr.meetstyle','=','2'];
        $info=$this->getappoint_detail($map,'mr.*');
        if(empty($info)){
            return jsondata('0051','预约信息不存在');
        }
        if(strtotime($info['meetingdate'].' '.$info['meetingendtime'])<time()+60*60*24){
            return jsondata('0052','预约信息暂不能取消');
        }
        $update_data['status']=3;
        $update_data['canceltime']=date('Y-m-d H:i:s');
        $res=DB::name('meeting_record mr')->where($map)->update($update_data);
        if($res){
            return jsondata('0001','取消预约成功');
        }else{
            return jsondata('0059','取消预约失败,请重试');
        }
    }

    //删除预约信息
    public function appoint_delete($baseuserinfo,$meetingid){
        $delmeetingid=array();
        $hasadminmeetingid=array();
        foreach($meetingid as $v){
            $map=[];
            $map[]=['mr.openid','=',$baseuserinfo['open_id']];
            $map[]=['mr.id','=',intval($v)];
            $map[]=['mr.status','in',[2,3,4]];
            $map[]=['mr.meetstyle','=','2'];
            $info=$this->getappoint_detail($map,'mr.*');
            if(!empty($info)){
                $delmeetingid[]=$info['id'];
            }
        }
        if(empty($delmeetingid)){
            return jsondata('0051','请选择预约信息');
        }
        $map=array();
        $map[]=['openid','=',$baseuserinfo['open_id']];
        $map[]=['id','in',$delmeetingid];
        $map[]=['status','in',[2,3,4]];
        $map[]=['meetstyle','=','2'];
        $res=DB::name('meeting_record')->where($map)->delete();
        if($res){
            return jsondata('0001','删除预约信息成功');
        }else{
            return jsondata('0059','删除预约信息失败,请重试');
        }
    }
}