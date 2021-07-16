<?php
namespace app\api\model;
use think\Db;
use think\Model;
//商品基本信息
class Shopbaseinfo extends Model{
    protected $table="merchant_baseinfo";

    //通过shop_codeid获取店铺信息
    public function getinfo4shop_codeid($shop_codeid){
        $info=array();
        if($shop_codeid!=''){
            $map['shop_codeid']=$shop_codeid;
            $map['shop_auditstatus']=1;
            $info=DB::name($this->table)->where($map)->find();
            if(!empty($info)){
                $addrmap=array();
                if($info['shop_province']!=''){
                    $addrmap[]=$info['shop_province'];
                }
                if($info['shop_city']!=''){
                    $addrmap[]=$info['shop_city'];
                }
                if($info['shop_area']!=''){
                    $addrmap[]=$info['shop_area'];
                }
                if($info['shop_town']!=''){
                    $addrmap[]=$info['shop_town'];
                }
                $addrstr='';
                if(!empty($addrmap)){
                    $addrmap2[]=['code','in',$addrmap];
                    $addressinfo=DB::name('region')->field('name')->where($addrmap2)->select();
                    foreach($addressinfo as $av){
                        $addrstr.=$av['name'];
                    }
                }
                $info['shop_address']=$addrstr.$info['shop_address'];
            }
        }
        return $info;
    }

    //通过id获取店铺信息
    public function getinfo4id($id){
        $info=array();
        $id=intval($id);
        if($id>0){
            $map['id']=$id;
            $map['shop_auditstatus']=1;
            $info=DB::name($this->table)->where($map)->find();
            $addrmap=array();
            if($info['shop_province']!=''){
                $addrmap[]=$info['shop_province'];
            }
            if($info['shop_city']!=''){
                $addrmap[]=$info['shop_city'];
            }
            if($info['shop_area']!=''){
                $addrmap[]=$info['shop_area'];
            }
            if($info['shop_town']!=''){
                $addrmap[]=$info['shop_town'];
            }
            $addrstr='';
            if(!empty($addrmap)){
                $addrmap2[]=['code','in',$addrmap];
                $addressinfo=DB::name('region')->field('name')->where($addrmap2)->select();
                foreach($addressinfo as $av){
                    $addrstr.=$av['name'];
                }
            }
            $info['shop_address']=$addrstr.$info['shop_address'];
        }
        return $info;
    }


}
