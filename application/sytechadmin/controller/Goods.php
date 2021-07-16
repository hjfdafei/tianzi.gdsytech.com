<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\service\GoodsService;
//宽带套餐管理
class Goods extends Sytechadminbase{
    //宽带套餐列表
    public function goods_list(){
        $keyword=input('keyword','','trim');
        $status=input('status','0','intval');
        $map=[];
        if($status>0){
            $map[]=['goods_status','=',$status];
        }
        if($keyword!=''){
            $map[]=['goods_title','like',"%$keyword%"];
        }
        $search['keyword']=$keyword;
        $search['status']=$status;
        $field='*';
        $orderby=['goods_sortby'=>'desc','id'=>'desc'];
        $service=new GoodsService();
        $data=$service->getGoodsList(1,$map,$field,$search,20,$orderby);
        $this->assign(['search'=>$search,'list'=>$data['list'],'count'=>$data['count'],'page'=>$data['page']]);
        return $this->fetch();
    }

    //新增宽带套餐
    public function goods_add(){
        if(request()->isPost() || request()->isAjax()){
            $service=new GoodsService();
            return $service->goods_verify(0);
        }
        return $this->fetch();
    }

    //编辑宽带套餐
    public function goods_edit(){
        $service=new GoodsService();
        if(request()->isPost() || request()->isAjax()){
            $goodsid=input('post.goodsid','0','intval');
            if($goodsid<=0){
                return jsondata('400','请选择宽带套餐信息');
            }
            return $service->goods_verify($goodsid);
        }
        $goodsid=input('goodsid','0','intval');
        if($goodsid<=0){
            return jsondata('400','请选择宽带套餐信息');
        }

        $map[]=['id','=',$goodsid];
        $info=$service->goodsDetail($map);
        if(empty($info)){
            return jsondata('400','请选择宽带套餐信息');
        }
        $this->assign(['info'=>$info]);
        return $this->fetch();
    }

    //上架宽带套餐
    public function goods_show(){
        if(request()->isPost() || request()->isAjax()){
            $goodsid=input('goodsid','0','intval');
            if($goodsid<=0){
                return jsondata('400','请选择需要启用的宽带套餐');
            }
            $service=new GoodsService();
            return $service->goods_showhide($goodsid,1);
        }
        return jsondata('400','网络错误');
    }

    //下架宽带套餐
    public function goods_hide(){
        if(request()->isPost() || request()->isAjax()){
            $goodsid=input('goodsid','0','intval');
            if($goodsid<=0){
                return jsondata('400','请选择需要禁用的宽带套餐');
            }
            $service=new GoodsService();
            return $service->goods_showhide($goodsid,2);
        }
        return jsondata('400','网络错误');
    }

    //删除宽带套餐
    public function goods_del(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $goodsid=input('post.goodsid','','trim');
            if($goodsid==''){
                return jsondata('400','请选择要删除的宽带套餐');
            }
            $goodsid=explode(',',trim($goodsid,','));
            if(empty($goodsid)){
                return jsondata('400','请选择要删除的宽带套餐');
            }
            $service=new GoodsService();
            return $service->goods_delete($goodsid);
        }
        return jsondata('400','网络错误');
    }

}
