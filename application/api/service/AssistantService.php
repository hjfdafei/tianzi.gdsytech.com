<?php
namespace app\api\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\api\service\QrcodeService;
use app\api\service\GenericService;
use app\api\service\UserService;
//名医助理
class AssistantService extends Base{
    //完善名医助理信息
    public function updateUser($param){
        $assistant_id=$param['assistant_id'];
        $realname=$param['realname'];
        $mobile=$param['mobile'];
        $avatar=$param['avatar'];
        $weburl=$param['weburl'];
        $useravatar=$param['useravatar'];
        $realavatar='';
        if($avatar!=''){
            $uploadres=base64_img_forfile($avatar,2,config('app.avatarpath'));
            if($uploadres['code']!=200){
                return ['code'=>'400','msg'=>$uploadres['msg']];
            }
            $realavatar=getabpath($uploadres['url'],'upload');
        }
        $map=[];
        $map[]=['id','=',$assistant_id];
        $map[]=['status','=',1];
        $map[]=['isdel','=',2];
        $gservice=new GenericService();
        $userInfo=$gservice->assistantDetail($map);
        if(empty($userInfo)){
            return ['code'=>'400','msg'=>'名医助理信息不存在'];
        }
        $hasmap=[];
        $hasmap[]=['mobile','=',$mobile];
        $hasmap[]=['isdel','=',2];
        $hasUser=$gservice->assistantDetail($hasmap);
        if(!empty($hasUser)){
            if($hasUser['id']!=$userInfo['id']){
                return ['code'=>'400','msg'=>'联系电话已存在'];
            }
        }
        $updateData=[
            'mobile'=>$mobile,
            'realname'=>$realname,
            'update_time'=>date('Y-m-d H:i:s'),
        ];
        if($realavatar!=''){
            $updateData['avatar']=$realavatar;
        }
        $upmap=[];
        $upmap[]=['id','=',$userInfo['id']];
        $res=DB::name('assistant')->where($upmap)->update($updateData);
        if($res){
            $info=[];
            $uavatar=$userInfo['avatar'];
            if($realavatar!=''){
                $uavatar=$weburl.getabpath($realavatar,'upload');
                @unlink($userInfo['avatar']);
            }else{
                if($uavatar==''){
                    $uavatar=$useravatar;
                }else{
                    $uavatar=$weburl.getabpath($uavatar,'upload');
                }
            }
            $info['avatar']=$uavatar;
            $info['realname']=$realname;
            $info['mobile']=$mobile;
            return ['code'=>'200','msg'=>'更新信息成功','data'=>$info];
        }else{
            return ['code'=>'400','msg'=>'更新信息失败,请重试','data'=>[]];
        }
    }

    //名医列表
    public function assistantDoctorList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('assistant_item at')->field($field)->join('__DOCTOR__ d','d.id=at.doctor_id','left')->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('assistant_item at')->field($field)->join('__DOCTOR__ d','d.id=at.doctor_id','left')->where($map)->order($orderby)->select();
        }
        $count=DB::name('assistant_item at')->join('__DOCTOR__ d','d.id=at.doctor_id','left')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //预约信息列表
    public function assistantMeetingList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('orders')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('orders')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('orders')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //预约信息详情
    public function assistantMeetingDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('orders')->field($field)->where($map)->find();
    }

    //获取上一条预约信息详情
    public function lastMeetingDetail($id,$map,$field='*'){
        $map2=[];
        $map2[]=['id','<',$id];
        return DB::name('orders')->field($field)->where($map)->where($map2)->order(['id'=>'desc'])->find();
    }

    //更改状态
    public function assistantMeetingChange($assistant_id,$meeting_id,$status){
        $map=[];
        $map[]=['assistant_id','=',$assistant_id];
        $map[]=['id','=',$meeting_id];
        $meetinginfo=$this->assistantMeetingDetail($map);
        if(empty($meetinginfo)){
            return ['code'=>'400','msg'=>'预约信息不存在'];
        }
        if($meetinginfo['status']!=1){
            return ['code'=>'400','msg'=>'预约信息已处理,无需再预约'];
        }
        $updateData['status']=$status;
        $updateData['deal_time']=date('Y-m-d H:i:s');
        $updateData['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('orders')->where([['id','=',$meetinginfo['id']]])->update($updateData);
        if($res){
            $code='200';
            $msg='设置成功';
        }else{
            $code='400';
            $msg='设置失败';
        }
        return ['code'=>$code,'msg'=>$msg];
    }

    //绑定基层医生
    public function assistantBindUser($assistant_id,$meeting_id,$basedoctor_id){
        $map=[];
        $map[]=['assistant_id','=',$assistant_id];
        $map[]=['id','=',$meeting_id];
        $meetinginfo=$this->assistantMeetingDetail($map);
        if(empty($meetinginfo)){
            return ['code'=>'400','msg'=>'预约信息不存在'];
        }
        if($meetinginfo['basedoctor_id']>0){
            return ['code'=>'400','msg'=>'已绑定基层医生'];
        }
        if($meetinginfo['status']==3){
            return ['code'=>'400','msg'=>'已完成,暂不支持绑定基层医生'];
        }
        $bmap=[];
        $bmap[]=['id','=',$basedoctor_id];
        $bmap[]=['status','=',1];
        $bmap[]=['isdel','=',2];
        $gservice=new GenericService();
        $basedoctorinfo=$gservice->basedoctorDetail($bmap);
        if(empty($basedoctorinfo)){
            return ['code'=>'400','msg'=>'基层医生信息不存在'];
        }

        $umap=[];
        $umap[]=['id','=',$meetinginfo['user_id']];
        $uservice=new UserService();
        $userinfo=$uservice->getUserInfo($umap);
        if(empty($userinfo)){
            return ['code'=>'400','msg'=>'用户信息不存在'];
        }
        if($userinfo['basedoctor_id']>0){
            $bmap2=[];
            $bmap2[]=['id','=',$userinfo['basedoctor_id']];
            $bmap2[]=['status','=',1];
            $bmap2[]=['isdel','=',2];
            $basedoctorinfo2=$gservice->basedoctorDetail($bmap2);
            if(!empty($basedoctorinfo2)){
                if($userinfo['basedoctor_id']!=$basedoctor_id){
                    return ['code'=>'400','msg'=>'已绑定基层医生'];
                }
            }else{
                $userinfo['basedoctor_id']=0;
            }
        }
        DB::startTrans();
        $upmap=[];
        $upmap[]=['assistant_id','=',$assistant_id];
        $upmap[]=['id','=',$meetinginfo['id']];
        $updateData=[];
        $updateData['basedoctor_id']=$basedoctor_id;
        $res=DB::name('orders')->where($upmap)->update($updateData);
        $res2=1;
        if($userinfo['basedoctor_id']<=0){
            $upmap=[];
            $upmap[]=['id','=',$meetinginfo['user_id']];
            $updateData=[];
            $updateData['basedoctor_id']=$basedoctor_id;
            $updateData['bind_time']=date('Y-m-d H:i:s');
            $updateData['bind_way']=1;
            $res2=DB::name('user')->where($upmap)->update($updateData);
        }
        if($res && $res2){
            DB::commit();
            return ['code'=>'200','msg'=>'绑定成功'];
        }else{
            DB::rollback();
            return ['code'=>'400','msg'=>'绑定失败'];
        }
    }

    //设置价钱
    public function assistantSetMoney($assistant_id,$meeting_id,$money){
        $map=[];
        $map[]=['assistant_id','=',$assistant_id];
        $map[]=['id','=',$meeting_id];
        $meetinginfo=$this->assistantMeetingDetail($map);
        if(empty($meetinginfo)){
            return ['code'=>'400','msg'=>'预约信息不存在'];
        }
        if($meetinginfo['ispay']==1){
            return ['code'=>'400','msg'=>'预约信息订单已支付,无需重复设置价格'];
        }
        if($meetinginfo['status']==3){
            return ['code'=>'400','msg'=>'预约信息订单已完成,无需重复设置价格'];
        }
        if($meetinginfo['status']==4){
            return ['code'=>'400','msg'=>'预约信息订单正在退款中'];
        }
        if($meetinginfo['status']==5){
            return ['code'=>'400','msg'=>'预约信息订单已完关闭'];
        }
        $upmap=[];
        $upmap[]=['assistant_id','=',$assistant_id];
        $upmap[]=['id','=',$meetinginfo['id']];
        $updateData=[];
        $updateData['money']=$money;
        $updateData['discount_money']=0;
        $updateData['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('orders')->where($upmap)->update($updateData);
        if($res){
            $openid='';
            $doctor_name='医生';
            $umap=[];
            $umap[]=['id','=',$meetinginfo['user_id']];
            $uservice=new UserService();
            $userinfo=$uservice->getUserInfo($umap);
            if(!empty($userinfo)){
                $openid=$userinfo['openid'];
            }
            $dmap=[];
            $dmap[]=['id','=',$meetinginfo['doctor_id']];
            $gservice=new GenericService();
            $doctorinfo=$gservice->doctorDetail($dmap);
            if(!empty($doctorinfo)){
                if($doctorinfo['realname']!=''){
                    $doctor_name=$doctorinfo['realname'];
                }
            }
            if($openid!=''){
                send_paytpl($openid,$meetinginfo['id'],$meetinginfo['realname'],$doctor_name,$meetinginfo['create_time'],$meetinginfo['orderno']);
            }
            return ['code'=>'200','msg'=>'设置价钱成功'];
        }else{
            return ['code'=>'400','msg'=>'设置价钱失败'];
        }
    }

    //名医申请列表
    public function assistantApplyDoctorList($style,$map,$field,$start,$limit,$orderby=['id'=>'desc']){
        if($style==1){
            $list=DB::name('doctor_apply')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('doctor_apply')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('doctor_apply')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //添加/修改名医申请
    public function assistantAddDoctor($id,$doctorid=0){
        $doctorid=intval($doctorid);
        $assistant_id=intval($id);
        $realname=input('post.realname','','trim');
        $mobile=input('post.mobile','','trim');
        $avatar=input('post.avatar','','trim');
        $hospital_id=input('post.hospital_id','0','intval');
        $department_id=input('post.department_id','0','intval');
        $content=input('post.content','','trim');
        $professional_titles=str_replace('、','|',input('post.professional_titles','','trim'));
        $labels=str_replace('、','|',input('post.labels','','trim'));
        $skill=str_replace('、','|',input('post.skill','','trim'));
        $honorlist=str_replace('、','|',input('post.honorlist','','trim'));
        $place=str_replace('、','|',input('post.place','','trim'));
        $data=[];
        if($realname==''){
            return ['code'=>'400','msg'=>'请输入名医姓名'];
        }
        $checkmobileres=checkformat_mobile($mobile);
        if($checkmobileres['code']!='0001'){
            return ['code'=>'400','msg'=>$checkmobileres['msg']];
        }
        if($content==''){
            return ['code'=>'400','msg'=>'请输入名医简介'];
        }
        if($doctorid<=0){
            if($avatar==''){
                return ['code'=>'400','msg'=>'请上传名医头像'];
            }
        }
        if($hospital_id<=0){
            return ['code'=>'400','msg'=>'请选择名医所属医院'];
        }
        if($department_id==''){
            return ['code'=>'400','msg'=>'请选择名医所在科室'];
        }
        $gservice=new GenericService();
        $hmap=[];
        $hmap[]=['typeid','=',1];
        $hmap[]=['isshow','=',1];
        $hmap[]=['id','=',$hospital_id];
        $hospitalinfo=$gservice->attributeDetail($hmap);
        if(empty($hospitalinfo)){
            return ['code'=>'400','msg'=>'请选择名医所属医院'];
        }
        $dmap=[];
        $dmap[]=['typeid','=',2];
        $dmap[]=['isshow','=',1];
        $dmap[]=['id','=',$department_id];
        $departmentinfo=$gservice->attributeDetail($dmap);
        if(empty($departmentinfo)){
            return ['code'=>'400','msg'=>'请选择名医所在科室'];
        }
        $hasmap=[];
        $hasmap[]=['realname','=',$realname];
        $hasmap[]=['mobile','=',$mobile];
        $hasmap[]=['isdel','=',2];
        $hasinfo=$gservice->doctorDetail($hasmap);
        if(!empty($hasinfo)){
            return ['code'=>'400','msg'=>'名医信息已存在'];
        }
        $hasmap2=[];
        $hasmap2[]=['realname','=',$realname];
        $hasmap2[]=['mobile','=',$mobile];
        $hasinfo2=$this->applyDoctorDetail($hasmap2);
        if(!empty($hasinfo2)){
            if($hasinfo2['id']!=$doctorid){
                return ['code'=>'400','msg'=>'名医信息已存在'];
            }
        }
        if($avatar!=''){
            $uploadres=base64_img_forfile($avatar,2,config('app.avatarpath'));
            if($uploadres['code']!=200){
                return ['code'=>'400','msg'=>$uploadres['msg']];
            }
            $avatar=getabpath($uploadres['url'],'upload');
            $data['avatar']=$avatar;
        }
        $search_word=$realname.$hospitalinfo['title'].$departmentinfo['title'].$skill.$labels.$professional_titles;
        $doctorinfo=[];
        if($doctorid>0){
            $dcmap=[];
            $dcmap[]=['id','=',$doctorid];
            $dcmap[]=['assistant_id','=',$assistant_id];
            $doctorinfo=$this->applyDoctorDetail($dcmap);
            if(empty($doctorinfo)){
                return ['code'=>'400','msg'=>'编辑的信息不存在'];
            }
            if($doctorinfo['status']==1){
                return ['code'=>'400','msg'=>'审核信息已通过,无需修改,如需修改,请联系管理员'];
            }
        }
        $data['assistant_id']=$assistant_id;
        $data['realname']=$realname;
        $data['mobile']=$mobile;
        $data['hospital_id']=$hospital_id;
        $data['department_id']=$department_id;
        $data['professional_titles']=$professional_titles;
        $data['labels']=$labels;
        $data['content']=$content;
        $data['skill']=$skill;
        $data['honorlist']=$honorlist;
        $data['place']=$place;
        $data['search_word']=$search_word;
        $data['status']=2;
        $opname='添加';
        if(empty($doctorinfo)){
            $data['create_time']=date('Y-m-d H:i:s');
            $res=DB::name('doctor_apply')->insertGetId($data);
        }else{
            $data['update_time']=date('Y-m-d H:i:s');
            $res=DB::name('doctor_apply')->where([['id','=',$doctorinfo['id']]])->update($data);
            $opname='修改';
        }
        if($res){
            return ['code'=>'200','msg'=>$opname.'成功'];
        }else{
            return ['code'=>'400','msg'=>$opname.'失败,请重试'];
        }
    }

    //申请的名医详情
    public function applyDoctorDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('doctor_apply')->field($field)->where($map)->find();
    }

    //删除名医申请
    public function assistantDeleteDoctor($assistant_id,$doctorid){
        $trueid=[];
        $delimg=[];
        foreach($doctorid as $v){
            $map=[];
            $map[]=['assistant_id','=',$assistant_id];
            $map[]=['status','<>',1];
            $map[]=['id','=',intval($v)];
            $info=$this->applyDoctorDetail($map);
            if(!empty($info)){
                $trueid[]=$info['id'];
                if($info['avatar']!=''){
                    $delimg[]=$info['avatar'];
                }
            }
        }
        if(empty($trueid)){
            return ['code'=>'400','msg'=>'没有符合条件的名医申请,删除失败'];
        }
        $map=[];
        $map[]=['assistant_id','=',$assistant_id];
        $map[]=['status','<>',1];
        $map[]=['id','in',$trueid];
        $res=DB::name('doctor_apply')->where($map)->delete();
        if($res){
            if(!empty($delimg)){
                foreach($delimg as $dv){
                    @unlink('.'.getabpath($dv,'upload'));
                }
            }
            return ['code'=>'200','msg'=>'删除成功'];
        }else{
            return ['code'=>'400','msg'=>'删除失败,请重试'];
        }
    }
}