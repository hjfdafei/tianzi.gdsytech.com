<?php
namespace app\api\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\api\service\QrcodeService;
use app\api\service\GenericService;
//基层医生
class BasedoctorService extends Base{
    //完善基层医生信息
    public function updateUser($param){
        $basedoctor_id=$param['basedoctor_id'];
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
        $map[]=['id','=',$basedoctor_id];
        $gservice=new GenericService();
        $userInfo=$gservice->basedoctorDetail($map);
        if(empty($userInfo)){
            return ['code'=>'400','msg'=>'基层医生信息不存在'];
        }
        $hasmap=[];
        $hasmap[]=['mobile','=',$mobile];
        $hasUser=$gservice->basedoctorDetail($hasmap);
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
        $res=DB::name('basedoctor')->where($upmap)->update($updateData);
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

    //获取订单数量 基层医生id type 1总数量 2已完成数量
    public function getOrderNum($basedoctor_id,$type=1){
        if($basedoctor_id<=0){
            return 0;
        }
        $map=[];
        $map[]=['basedoctor_id','=',$basedoctor_id];
        if($type==2){
            $map[]=['status','=',3];
        }
        return DB::name('orders')->where($map)->count();
    }

    //获取粉丝数量
    public function getFansNum($basedoctor_id){
        if($basedoctor_id<=0){
            return 0;
        }
        $map=[];
        $map[]=['basedoctor_id','=',$basedoctor_id];
        return DB::name('user')->where($map)->count();
    }

    //粉丝列表
    public function basedoctorFansList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('user')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('user')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('user')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //预约列表
    public function basedoctorMeetingList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('orders')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('orders')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('orders')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

     //预约信息详情
    public function basodoctorMeetingDetail($map,$field='*'){
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

}