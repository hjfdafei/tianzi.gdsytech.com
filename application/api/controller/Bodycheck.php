<?php
namespace app\api\controller;
use think\facade\Request;
use think\Controller;
use think\Db;
use think\db\Query;
use app\api\controller\Bodycheckbase;
use app\api\service\BodycheckService;
use app\api\service\CommonmeetService;
use app\api\service\QrcodeService;
class Bodycheck extends Bodycheckbase{
    public function _empty(){
        return json(['code'=>'0004','msg'=>'网络错误']);
    }

    //获取时间
    public function meetingtime_list(){
        $commonservice=new CommonmeetService();
        $weekdata=$commonservice->getdatelist(7,2);
        $timedata=$commonservice->gettimenum(date('Y-m-d'),2);
        $data['weekdata']=$weekdata;
        $data['timedata']=$timedata;
        return jsondata('0001','获取成功',$data);
    }

    //获取更多日期
    public function meetingtime_list_more(){
        $commonservice=new CommonmeetService();
        $weekdata=$commonservice->getdatelist(31,2);
        $data['data']=$weekdata;
        return jsondata('0001','获取成功',$data);
    }

    //获取预约数量
    public function getmeeting_number(){
        $selectdate=input('selectdate',date('Y-m-d'),'trim');
        if($selectdate<date('Y-m-d')){
            return jsondata('0041','请选择日期');
        }
        $commonservice=new CommonmeetService();
        $timedata=$commonservice->gettimenum($selectdate,2);
        $data['data']=$timedata;
        return jsondata('0001','获取成功',$data);
    }

    //提交预约信息
    public function appointment_deal(){
        if(request()->isPost() || request()->isAjax()){
            $bodyservice=new BodycheckService();
            return $bodyservice->appoint_deal($this->base_userinfo);
        }
        return jsondata('0004','网络错误');
    }

    //预约信息列表
    public function appointment_list(){
        $start=input('start','0','intval');
        $limit=input('limit','10','intval');
        $list=[];
        $count=0;
        $map=[];
        $map[]=['mr.openid','=',$this->base_userinfo['open_id']];
        $map[]=['mr.meetstyle','=','2'];
        $field='mr.id,mr.meetingdate,mr.meetingtstarttime as starttime,mr.meetingendtime as endtime,mr.status,u.name as realname,u.phone as mobile';
        $bodyservice=new BodycheckService();
        $listdata=$bodyservice->getappoint_list($map,$field,1,$start,$limit,['mr.status'=>'asc','mr.id'=>'asc']);
        $count=$listdata['count'];
        $list=$listdata['list'];
        $data['count']=$count;
        $data['data']=$list;
        return jsondata('0001','获取成功',$data);
    }

    //预约信息详情
    public function appointment_detail(){
        $meetingid=input('meetingid','0','intval');
        if($meetingid<=0){
            return jsondata('0051','请选择预约信息');
        }
        $map[]=['mr.openid','=',$this->base_userinfo['open_id']];
        $map[]=['mr.id','=',$meetingid];
        $map[]=['mr.meetstyle','=','2'];
        $field='mr.id,mr.meetingdate,mr.meetingtstarttime as starttime,mr.meetingendtime as endtime,mr.status,u.name as realname,u.phone as mobile';
        $bodyservice=new BodycheckService();
        $info=$bodyservice->getappoint_detail($map,$field);
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //取消预约
    public function appointment_cancel(){
        if(request()->isPost() || request()->isAjax()){
            $meetingid=input('meetingid','0','intval');
            if($meetingid<=0){
                return jsondata('0051','请选择预约信息');
            }
            $bodyservice=new BodycheckService();
            return $bodyservice->appoint_cancel($this->base_userinfo,$meetingid);
        }
        return jsondata('0004','网络错误');
    }

    //删除预约信息
    public function appointment_del(){
        if(request()->isPost() || request()->isAjax()){
            $meetingid=input('meetingid','','trim');
            if($meetingid==''){
                return jsondata('0051','请选择预约信息');
            }
            $meetingid=explode(',',trim($meetingid,','));
            $bodyservice=new BodycheckService();
            return $bodyservice->appoint_delete($this->base_userinfo,$meetingid);
        }
        return jsondata('0004','网络错误');
    }

}
