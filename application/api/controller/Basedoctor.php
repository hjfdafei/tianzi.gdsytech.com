<?php
namespace app\api\controller;
use think\facade\Request;
use think\Controller;
use think\Db;
use think\db\Query;
use app\api\controller\Basedoctorbase;
use app\api\service\UserService;
use app\api\service\AssistantService;
use app\api\service\GenericService;
use app\api\service\BasedoctorService;
class Basedoctor extends Basedoctorbase{
    public function _empty(){
        return json(['code'=>'0004','msg'=>'error']);
    }

    //基层医生详情
    public function basedoctorDetail(){
        $avatar=$this->base_userinfo['basedoctorinfo']['avatar'];
        if($avatar!=''){
            $avatar=$this->weburl.getabpath($avatar,'upload');
        }else{
            $avatar=$this->base_userinfo['avatar'];
        }
        $qrcode=$this->base_userinfo['basedoctorinfo']['qrcode'];
        if($qrcode!=''){
            $qrcode=$this->weburl.getabpath($qrcode,'upload');
        }
        $professional_titles=$this->base_userinfo['basedoctorinfo']['professional_titles'];
        if($professional_titles!=''){
            $professional_titles=explode('|',$professional_titles);
        }
        $labels=$this->base_userinfo['basedoctorinfo']['labels'];
        if($labels!=''){
            $labels=explode('|',$labels);
        }
        $service=new BasedoctorService();
        $order_num=$service->getOrderNum($this->base_userinfo['basedoctorinfo']['id'],1);
        $finish_num=$service->getOrderNum($this->base_userinfo['basedoctorinfo']['id'],2);
        $fans_num=$service->getFansNum($this->base_userinfo['basedoctorinfo']['id']);
        $info['realname']=$this->base_userinfo['basedoctorinfo']['realname'];
        $info['mobile']=$this->base_userinfo['basedoctorinfo']['mobile'];
        $info['avatar']=$avatar;
        $info['qrcode']=$qrcode;
        $info['order_num']=$order_num;
        $info['finish_num']=$finish_num;
        $info['fans_num']=$fans_num;
        $info['professional_titles']=$professional_titles;
        $info['labels']=$labels;
        $info['content']=htmlspecialchars_decode($this->base_userinfo['basedoctorinfo']['content']);
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //更新基层医生信息
    public function updateInfo(){
        if(request()->isPost() || request()->isAjax()){
            $realname=input('post.realname','','trim');
            $mobile=input('post.mobile','','trim');
            $avatar=input('post.avatar','','trim');
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
                'basedoctor_id'=>$this->base_userinfo['basedoctorinfo']['id'],
                'realname'=>$realname,
                'mobile'=>$mobile,
                'avatar'=>$avatar,
                'weburl'=>$this->weburl,
                'useravatar'=>$this->base_userinfo['avatar'],
            ];
            $userservice=new BasedoctorService();
            $res=$userservice->updateUser($param);
            if($res['code']!=200){
                return jsondata('0027',$res['msg']);
            }
            $outdata['data']=$res['data'];
            return jsondata('0001',$res['msg'],$outdata);
        }
        return jsondata('0028','网络请求错误');
    }

    //粉丝列表
    public function basedoctor_fanslist(){
        $keyword=input('keyword','','trim');
        $status=input('status','0','intval');
        $doctor_id=input('doctor_id','0','intval');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='id,nickname,realname,mobile,bind_time';
        $orderby=['id'=>'desc'];
        $map=[];
        $map[]=['basedoctor_id','=',$this->base_userinfo['basedoctorinfo']['id']];
        if($keyword!=''){
            $map[]=['nickname|realname|mobile','like',"%$keyword%"];
        }
        $service=new BasedoctorService();
        $list=$service->basedoctorFansList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $count=$list['count'];
        if(!empty($listdata)){
            foreach($listdata as &$v){
                $realname=$v['realname'];
                if($realname==''){
                    $realname=$v['nickname'];
                }
                $v['realname']=$realname;
                unset($v['nickname']);
            }
        }
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //预约列表
    public function basedoctor_meetinglist(){
        $keyword=input('keyword','','trim');
        $status=input('status','0','intval');
        $user_id=input('user_id','0','intval');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='id,assistant_id,doctor_id,orderno,realname,mobile,money,ispay,create_time,status';
        $orderby=['id'=>'desc'];
        $map=[];
        $map[]=['basedoctor_id','=',$this->base_userinfo['basedoctorinfo']['id']];
        if($user_id>0){
            $map[]=['user_id','=',$user_id];
        }
        if($status>0){
            if(in_array($status,[1,2])){
                $map[]=['status','<',3];
            }else{
                $map[]=['status','=',$status];
            }
        }

        // if($status==1){
        //     $map[]=['status','<',3];
        // }elseif($status==3){
        //     $map[]=['status','=',3];
        // }
        $service=new BasedoctorService();
        $list=$service->basedoctorMeetingList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        if(!empty($listdata)){
            $gservice=new GenericService();
            $doctorarr=[];
            $assistantarr=[];
            foreach($listdata as &$v){
                if(!isset($doctorarr[$v['doctor_id']])){
                    $dmap=[];
                    $dmap[]=['id','=',$v['doctor_id']];
                    $doctorinfo=$gservice->doctorDetail($dmap);
                    $doctorarr[$v['doctor_id']]=$doctorinfo;
                }else{
                    $doctorinfo=$doctorarr[$v['doctor_id']];
                }
                if(!isset($assistantarr[$v['assistant_id']])){
                    $amap=[];
                    $amap[]=['id','=',$v['assistant_id']];
                    $assistantinfo=$gservice->assistantDetail($amap);
                    $assistantarr[$v['assistant_id']]=$assistantinfo;
                }else{
                    $assistantinfo=$assistantarr[$v['assistant_id']];
                }
                $doctor_name='';
                $assistant_mobile='';
                $assistant_name='';
                if(!empty($doctorinfo)){
                    $doctor_name=$doctorinfo['realname'];
                }
                if(!empty($assistantinfo)){
                    $assistant_mobile=$assistantinfo['mobile'];
                    $assistant_name=$assistantinfo['realname'];
                }
                $v['doctor_name']=$doctor_name;
                $v['assistant_mobile']=$assistant_mobile;
                $v['assistant_name']=$assistant_name;
                $v['meeting_time']=date('Y-m-d',strtotime($v['create_time']));
                $v['money']=round($v['money']/100,2);
                if(in_array($v['status'],[1,2])){
                    $v['status']=1;
                }elseif($v['status']==3){
                    $v['status']=2;
                }
                // if($v['status']<3){
                //     $v['status']=1;
                // }
                unset($v['assistant_id']);
                unset($v['create_time']);
            }
        }
        $count=$list['count'];
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //预约详情
    public function basedoctor_meetingdetail(){
        $meeting_id=input('meeting_id','0','intval');
        if($meeting_id<=0){
            return jsondata('0021','请选择预约信息');
        }
        $field='id,user_id,assistant_id,doctor_id,orderno,realname,mobile,content,status,money,pay_money,ispay,pay_time,create_time,finish_time';
        $map=[];
        $map[]=['basedoctor_id','=',$this->base_userinfo['basedoctorinfo']['id']];
        $map[]=['id','=',$meeting_id];
        $service=new BasedoctorService();
        $info=$service->basodoctorMeetingDetail($map,$field);
        if(empty($info)){
            return jsondata('0021','选择预约信息不存在');
        }
        $gservice=new GenericService();
        $dmap=[];
        $dmap[]=['id','=',$info['doctor_id']];
        $doctorinfo=$gservice->doctorDetail($dmap);
        $amap=[];
        $amap[]=['id','=',$info['assistant_id']];
        $assistantinfo=$gservice->assistantDetail($amap);
        $doctor_name='';
        $assistant_mobile='';
        $assistant_name='';
        if(!empty($doctorinfo)){
            $doctor_name=$doctorinfo['realname'];
        }
        if(!empty($assistantinfo)){
            $assistant_mobile=$assistantinfo['mobile'];
            $assistant_name=$assistantinfo['realname'];
        }

        //$info['meeting_time']=date('Y-m-d',strtotime($info['create_time']));
        $meeting_time=date('Y-m-d',strtotime($info['create_time']));
        $last_meeting_time='';
        if($info['status']==3){
            $meeting_time=date('Y-m-d',strtotime($info['finish_time']));
        }
        $map2=[];
        $map2[]=['user_id','=',$info['user_id']];
        $map2[]=['realname','=',$info['realname']];
        $map2[]=['mobile','=',$info['mobile']];
        $map2[]=['basedoctor_id','=',$this->base_userinfo['basedoctorinfo']['id']];
        $lastinfo=$service->lastMeetingDetail($info['id'],$map2);
        if(!empty($lastinfo)){
            $last_meeting_time=date('Y-m-d',strtotime($lastinfo['create_time']));
        }
        if(in_array($info['status'],[1,2])){
            $info['status']=1;
        }elseif($info['status']==3){
            $info['status']=2;
        }
        // if($info['status']<3){
        //     $info['status']=1;
        // }
        $info['doctor_name']=$doctor_name;
        $info['assistant_mobile']=$assistant_mobile;
        $info['assistant_name']=$assistant_name;
        $info['meeting_time']=$meeting_time;
        $info['last_meeting_time']=$last_meeting_time;
        $info['money']=round($info['money']/100,2);
        $info['pay_money']=round($info['pay_money']/100,2);
        unset($info['assistant_id']);
        unset($info['user_id']);
        unset($info['create_time']);
        unset($info['finish_time']);
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

}
