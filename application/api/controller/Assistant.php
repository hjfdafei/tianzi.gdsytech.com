<?php
namespace app\api\controller;
use think\facade\Request;
use think\Controller;
use think\Db;
use think\db\Query;
use app\api\controller\Assistantbase;
use app\api\service\UserService;
use app\api\service\AssistantService;
use app\api\service\GenericService;
class Assistant extends Assistantbase{
    public function _empty(){
        return json(['code'=>'0004','msg'=>'error']);
    }

    //助理详情
    public function assistantDetail(){
        $avatar=$this->base_userinfo['assistantinfo']['avatar'];
        if($avatar!=''){
            $avatar=$this->weburl.getabpath($avatar,'upload');
        }else{
            $avatar=$this->base_userinfo['avatar'];
        }
        $info['realname']=$this->base_userinfo['assistantinfo']['realname'];
        $info['mobile']=$this->base_userinfo['assistantinfo']['mobile'];
        $info['avatar']=$avatar;

        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //更新助理信息
    public function updateInfo(){
        return jsondata('0028','不支持修改,请联系管理员');
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
                'assistant_id'=>$this->base_userinfo['assistantinfo']['id'],
                'realname'=>$realname,
                'mobile'=>$mobile,
                'avatar'=>$avatar,
                'weburl'=>$this->weburl,
                'useravatar'=>$this->base_userinfo['avatar'],
            ];
            $userservice=new AssistantService();
            $res=$userservice->updateUser($param);
            if($res['code']!=200){
                return jsondata('0027',$res['msg']);
            }
            $outdata['data']=$res['data'];
            return jsondata('0001',$res['msg'],$outdata);
        }
        return jsondata('0028','网络请求错误');
    }

    //名医列表
    public function assistant_doctorlist(){
        $keyword=input('keyword','','trim');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='d.id,d.realname,d.avatar,d.hospital_id,d.department_id,d.professional_titles';
        $orderby=['at.create_time'=>'desc','d.sortby'=>'desc'];
        $map=[];
        $map[]=['d.status','=',1];
        $map[]=['at.assistant_id','=',$this->base_userinfo['assistantinfo']['id']];
        $service=new AssistantService();
        $list=$service->assistantDoctorList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $count=$list['count'];
        $gservice=new GenericService();
        $hospital_namearr=[];
        $department_namearr=[];
        if(!empty($listdata)){
            foreach($listdata as &$v){
                if($v['avatar']!=''){
                    $v['avatar']=$this->weburl.getabpath($v['avatar'],'upload');
                }
                $v['professional_titles']=explode('|',$v['professional_titles']);
                $hospital_name='';
                $department_name='';
                if(isset($hospital_namearr[$v['hospital_id']])){
                    $hospital_name=$hospital_namearr[$v['hospital_id']];
                }else{
                    $hospital_namearr[$v['hospital_id']]=$gservice->attributeName($v['hospital_id']);
                    $hospital_name=$hospital_namearr[$v['hospital_id']];
                }
                if(isset($department_namearr[$v['department_id']])){
                    $department_name=$department_namearr[$v['department_id']];
                }else{
                    $department_namearr[$v['department_id']]=$gservice->attributeName($v['department_id']);
                    $department_name=$department_namearr[$v['department_id']];
                }
                $v['hospital_name']=$hospital_name;
                $v['department_name']=$department_name;
            }
        }
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //预约列表
    public function assistant_meetinglist(){
        $keyword=input('keyword','','trim');
        $status=input('status','0','intval');
        $doctor_id=input('doctor_id','0','intval');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='id,assistant_id,basedoctor_id,doctor_id,orderno,realname,mobile,money,ispay,create_time,status';
        $orderby=['id'=>'desc'];
        $map=[];
        $map[]=['assistant_id','=',$this->base_userinfo['assistantinfo']['id']];
        if($status>0){
            $map[]=['status','=',1];
        }
        // if($status==1){
        //     $map[]=['status','=',1];
        // }elseif($status==2){
        //     $map[]=['status','=',2];
        // }elseif($status==3){
        //     $map[]=['status','=',3];
        // }
        if($doctor_id>0){
            $atmap=[];
            $atmap[]=['assistant_id','=',$this->base_userinfo['assistantinfo']['id']];
            $atmap[]=['doctor_id','=',$doctor_id];
            $isbelong=DB::name('assistant_item')->where($atmap)->find();
            if(!empty($isbelong)){
                $map[]=['doctor_id','=',$doctor_id];
            }
        }
        $service=new AssistantService();
        $list=$service->assistantMeetingList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        if(!empty($listdata)){
            $gservice=new GenericService();
            $doctorarr=[];
            $basedoctorarr=[];
            foreach($listdata as &$v){
                if(!isset($doctorarr[$v['doctor_id']])){
                    $dmap=[];
                    $dmap[]=['id','=',$v['doctor_id']];
                    $doctorinfo=$gservice->doctorDetail($dmap);
                    $doctorarr[$v['doctor_id']]=$doctorinfo;
                }else{
                    $doctorinfo=$doctorarr[$v['doctor_id']];
                }
                if(!isset($basedoctorarr[$v['basedoctor_id']])){
                    $dmap=[];
                    $dmap[]=['id','=',$v['basedoctor_id']];
                    $basedoctorinfo=$gservice->basedoctorDetail($dmap);
                    $basedoctorarr[$v['basedoctor_id']]=$basedoctorinfo;
                }else{
                    $basedoctorinfo=$basedoctorarr[$v['basedoctor_id']];
                }
                $doctor_name='';
                $basedoctor_name='';
                if(!empty($doctorinfo)){
                    $doctor_name=$doctorinfo['realname'];
                }
                if(!empty($basedoctorinfo)){
                    $basedoctor_name=$basedoctorinfo['realname'];
                }
                $v['doctor_name']=$doctor_name;
                $v['basedoctor_name']=$basedoctor_name;
                $v['meeting_time']=date('Y-m-d',strtotime($v['create_time']));
                $v['money']=round($v['money']/100,2);
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
    public function assistant_meetingdetail(){
        $meeting_id=input('meeting_id','0','intval');
        if($meeting_id<=0){
            return jsondata('0021','请选择预约信息');
        }
        $field='id,user_id,assistant_id,doctor_id,basedoctor_id,orderno,realname,mobile,content,status,money,pay_money,ispay,pay_time,create_time,finish_time';
        $map=[];
        $map[]=['assistant_id','=',$this->base_userinfo['assistantinfo']['id']];
        $map[]=['id','=',$meeting_id];
        $service=new AssistantService();
        $info=$service->assistantMeetingDetail($map,$field);
        if(empty($info)){
            return jsondata('0021','选择预约信息不存在');
        }
        $gservice=new GenericService();
        $dmap=[];
        $dmap[]=['id','=',$info['doctor_id']];
        $doctorinfo=$gservice->doctorDetail($dmap);
        $bmap=[];
        $bmap[]=['id','=',$info['basedoctor_id']];
        $basedoctorinfo=$gservice->basedoctorDetail($bmap);
        $doctor_name='';
        $assistant_mobile='';
        $basedoctor_name='';
        if(!empty($doctorinfo)){
            $doctor_name=$doctorinfo['realname'];
        }
        if(!empty($basedoctorinfo)){
            $basedoctor_name=$basedoctorinfo['realname'];
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
        $map2[]=['assistant_id','=',$this->base_userinfo['assistantinfo']['id']];
        $lastinfo=$service->lastMeetingDetail($info['id'],$map2);
        if(!empty($lastinfo)){
            $last_meeting_time=date('Y-m-d',strtotime($lastinfo['create_time']));
        }

        // if($info['status']>1){
        //     $info['status']=2;
        // }
        $info['doctor_name']=$doctor_name;
        $info['basedoctor_name']=$basedoctor_name;
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

    //更改预约状态
    public function assistant_meeting_change(){
        if(request()->isPost() || request()->isAjax()){
            $meeting_id=input('post.meeting_id','0','intval');
            $status=input('post.status','2','intval');
            $status=2;
            if($meeting_id<=0){
                return jsondata('0021','请选择预约信息');
            }
            $service=new AssistantService();
            $res=$service->assistantMeetingChange($this->base_userinfo['assistantinfo']['id'],$meeting_id,$status);
            $code='0028';
            if($res['code']==200){
                $code='0001';
            }
            return jsondata($code,$res['msg']);
        }
        return jsondata('0029','网络请求错误');
    }

    //绑定用户
    public function assistant_binduser(){
        if(request()->isPost() || request()->isAjax()){
            $meeting_id=input('post.meeting_id','0','intval');
            $basedoctor_id=input('post.basedoctor_id','0','intval');
            if($meeting_id<=0){
                return jsondata('0021','请选择预约信息');
            }
            if($basedoctor_id<=0){
                return jsondata('0021','请选择预约信息');
            }
            $service=new AssistantService();
            $res=$service->assistantBindUser($this->base_userinfo['assistantinfo']['id'],$meeting_id,$basedoctor_id);
            $code='0028';
            if($res['code']==200){
                $code='0001';
            }
            return jsondata($code,$res['msg']);
        }
        return jsondata('0029','网络请求错误');
    }

    //设置价钱
    public function assistant_setmoney(){
        return jsondata('0021','暂不支持设置价格');
        if(request()->isPost() || request()->isAjax()){
            $meeting_id=input('post.meeting_id','0','intval');
            $money=input('post.money','0','trim');
            if($meeting_id<=0){
                return jsondata('0021','请选择预约信息');
            }
            if($money<=0){
                return jsondata('0021','请输入价钱');
            }
            $service=new AssistantService();
            $res=$service->assistantSetMoney($this->base_userinfo['assistantinfo']['id'],$meeting_id,round($money,2)*100);
            $code='0028';
            if($res['code']==200){
                $code='0001';
            }
            return jsondata($code,$res['msg']);
        }
        return jsondata('0029','网络请求错误');
    }

    //申请名医列表
    public function assistant_applydoctor_list(){
        $keyword=input('keyword','','trim');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='id,realname,avatar,hospital_id,department_id,professional_titles,status';
        $orderby=['id'=>'desc'];
        $map=[];
        $map[]=['status','<>',1];
        $map[]=['assistant_id','=',$this->base_userinfo['assistantinfo']['id']];
        $service=new AssistantService();
        $list=$service->assistantApplyDoctorList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $count=$list['count'];
        $gservice=new GenericService();
        $hospital_namearr=[];
        $department_namearr=[];
        if(!empty($listdata)){
            foreach($listdata as &$v){
                if($v['avatar']!=''){
                    $v['avatar']=$this->weburl.getabpath($v['avatar'],'upload');
                }
                $v['professional_titles']=explode('|',$v['professional_titles']);
                $hospital_name='';
                $department_name='';
                if(isset($hospital_namearr[$v['hospital_id']])){
                    $hospital_name=$hospital_namearr[$v['hospital_id']];
                }else{
                    $hospital_namearr[$v['hospital_id']]=$gservice->attributeName($v['hospital_id']);
                    $hospital_name=$hospital_namearr[$v['hospital_id']];
                }
                if(isset($department_namearr[$v['department_id']])){
                    $department_name=$department_namearr[$v['department_id']];
                }else{
                    $department_namearr[$v['department_id']]=$gservice->attributeName($v['department_id']);
                    $department_name=$department_namearr[$v['department_id']];
                }
                $v['hospital_name']=$hospital_name;
                $v['department_name']=$department_name;
            }
        }
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //添加名医
    public function assistant_applydoctor(){
        if(request()->isPost() || request()->isAjax()){
            $service=new AssistantService();
            $res=$service->assistantAddDoctor($this->base_userinfo['assistantinfo']['id'],0);
            $code='0026';
            if($res['code']=='200'){
                $code='0001';
            }
            return jsondata($code,$res['msg']);
        }
        return jsondata('0028','网络请求错误');
    }

    //修改名医
    public function assistant_editdoctor(){
        if(request()->isPost() || request()->isAjax()){
            $service=new AssistantService();
            $doctorid=input('post.doctorid','0','intval');
            if($doctorid<=0){
                return jsondata('0028','请选择编辑信息');
            }
            $res=$service->assistantAddDoctor($this->base_userinfo['assistantinfo']['id'],$doctorid);
            $code='0026';
            if($res['code']=='200'){
                $code='0001';
            }
            return jsondata($code,$res['msg']);
        }
        return jsondata('0028','网络请求错误');
    }

    //申请的名医详情
    public function assistant_applydoctor_detail(){
        $doctorid=input('doctorid','0','intval');
        if($doctorid<=0){
            return jsondata('400','请选择申请名医的信息');
        }
        $map=[];
        $map[]=['assistant_id','=',$this->base_userinfo['assistantinfo']['id']];
        $map[]=['id','=',$doctorid];
        $map[]=['status','<>',1];
        $field='id,realname,mobile,avatar,hospital_id,department_id,professional_titles,labels,content,skill,honorlist,place,status';
        $service=new AssistantService();
        $info=$service->applyDoctorDetail($map,$field);
        if(empty($info)){
            return jsondata('0021','查看信息不存在');
        }
        if($info['avatar']!=''){
            $info['avatar']=$this->weburl.getabpath($info['avatar'],'upload');
        }
        $gservice=new GenericService();
        $info['professional_titles']=$info['professional_titles']==''?[]:explode('|',$info['professional_titles']);
        $info['labels']=$info['labels']==''?[]:explode('|',$info['labels']);
        $info['skill']=$info['skill']==''?[]:explode('|',$info['skill']);
        $info['honorlist']=$info['honorlist']==''?[]:explode('|',$info['honorlist']);
        $info['place']=$info['place']==''?[]:explode('|',$info['place']);
        $info['content']=htmlspecialchars_decode(html_entity_decode($info['content']));
        $info['hospital_name']=$gservice->attributeName($info['hospital_id']);
        $info['department_name']=$gservice->attributeName($info['department_id']);
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //删除名医申请
    public function assistant_applydoctor_del(){
        if(request()->isPost() || request()->isAjax()){
            $doctorid=input('doctorid','','trim');
            if($doctorid==''){
                return jsondata('400','请选择要删除的名医申请');
            }
            $doctorid=array_unique(explode(',',trim(str_replace('，',',',$doctorid),',')));
            if(empty($doctorid)){
                return jsondata('400','请选择要删除的名医申请');
            }
            $service=new AssistantService();
            $res=$service->assistantDeleteDoctor($this->base_userinfo['assistantinfo']['id'],$doctorid);
            $code='0026';
            if($res['code']==200){
                $code='0001';
            }
            return jsondata($code,$res['msg']);
        }
        return jsondata('0028','网络请求错误');
    }

}
