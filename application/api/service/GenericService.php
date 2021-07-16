<?php
namespace app\api\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\api\service\QrcodeService;
use app\api\service\OrdersService;
class GenericService extends Base{
    //获取省市区
    public function getRegionList($map){
        $list=[];
        if(empty($map)){
            return $list;
        }
        $field='id,parent_code,area_code,name';
        $list=DB::name('cn_area')->field($field)->where($map)->select();
        return $list;
    }

    //banner列表
    public function bannerList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('banner')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('banner')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('banner')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //banner详情
    public function bannerDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('banner')->field($field)->where($map)->find();
    }

    //名医列表
    public function doctorList($style,$map,$map2,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('doctor')->field($field)->where($map)->where(function($query) use($map2){$query->whereOr($map2);})->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('doctor')->field($field)->where($map)->where(function($query) use($map2){$query->whereOr($map2);})->order($orderby)->select();
        }
        $count=DB::name('doctor')->where($map)->where(function($query) use($map2){$query->whereOr($map2);})->count();
        return ['list'=>$list,'count'=>$count];
    }

    //名医详情
    public function doctorDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        $info=DB::name('doctor')->field($field)->where($map)->find();
        if(!empty($info)){
            $dmap=[];
            $dmap[]=['doctor_id','=',$info['id']];
            $hasassistant=DB::name('assistant_item')->where($dmap)->find();
            $assistantinfo=[];
            if(!empty($hasassistant)){
                $amap=[];
                $amap[]=['isdel','=',2];
                $amap[]=['status','=',1];
                $amap[]=['id','=',$hasassistant['assistant_id']];
                $assistantinfo=$this->assistantDetail($amap);
            }
            $info['assistantinfo']=$assistantinfo;
        }
        return $info;
    }

    //名医信息
    public function getDoctorInfo($id){
        $id=intval($id);
    }

    //名医助理详情
    public function assistantDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        $info=DB::name('assistant')->field($field)->where($map)->find();
        return $info;
    }

    //基层医生详情
    public function basedoctorDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        $info=DB::name('basedoctor')->field($field)->where($map)->find();
        return $info;
    }

    //资讯列表
    public function newsList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('news')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('news')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('news')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //资讯详情
    public function newsDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('news')->field($field)->where($map)->find();
    }

    //基层医生列表
    public function basedoctorList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('basedoctor')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('basedoctor')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('basedoctor')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //属性列表(医院/科室)
    public function attributeList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('attribute')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('attribute')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('attribute')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //获取属性详情
    public function attributeDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('attribute')->field($field)->where($map)->find();
    }

    //获取属性名称
    public function attributeName($attrid){
        if($attrid<=0){
            return '';
        }
        $map=[];
        $map[]=['id','=',$attrid];
        $attrname='';
        $info=$this->attributeDetail($map);
        if(!empty($info)){
            $attrname=$info['title'];
        }
        return $attrname;
    }

    //名医预约人数
    public function getDoctorMeetnum($id){
        $num=0;
        $id=intval($id);
        if($id<=0){
            return $num;
        }
        $map=[];
        $map[]=['doctor_id','=',$id];
        return DB::name('orders')->where($map)->count();
    }

    //名医复诊率
    public function getDoctorVisitRate($id){
        $rate=0;
        $id=intval($id);
        if($id<=0){
            return $rate;
        }
        $map=[];
        $map[]=['doctor_id','=',$id];
        $allnum=DB::name('orders')->where($map)->count();
        $map[]=['isfirst','=',2];
        $visitnum=DB::name('orders')->where($map)->count();
        if($allnum>0){
            return round($visitnum/$allnum,2);
        }
        return $rate;
    }

}