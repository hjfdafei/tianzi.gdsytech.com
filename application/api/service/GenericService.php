<?php
namespace app\api\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
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

    //宽带套餐列表
    public function goodsList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('goods')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('goods')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('goods')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //宽带套餐详情
    public function goodsDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('goods')->field($field)->where($map)->find();
    }

    //校区列表
    public function schoolList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('school')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('school')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('school')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //年级列表
    public function gradeList($style,$map,$field,$start,$limit,$orderby){
        if($style==1){
            $list=DB::name('grade')->field($field)->where($map)->limit($start,$limit)->order($orderby)->select();
        }else{
            $list=DB::name('grade')->field($field)->where($map)->order($orderby)->select();
        }
        $count=DB::name('grade')->where($map)->count();
        return ['list'=>$list,'count'=>$count];
    }

    //校区详情
    public function schoolDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('school')->field($field)->where($map)->find();
    }

}