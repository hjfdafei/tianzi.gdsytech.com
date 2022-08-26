<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\service\GoodsService;
use app\sytechadmin\service\SchoolService;
use app\sytechadmin\service\GradeService;
//宽带套餐管理
class Goods extends Sytechadminbase{
    //宽带套餐列表
    public function goods_list(){
        $keyword=input('keyword','','trim');
        $status=input('status','0','intval');
        $school_id=input('school_id','0','intval');
        $grade_id=input('grade_id','0','intval');
        if($this->base_admininfo['school_id']>0){
            $school_id=$this->base_admininfo['school_id'];
        }
        $map=[];
        if($school_id>0){
            $map[]=['school_id','=',$school_id];
        }
        if($grade_id>0){
            $map[]=['grade_id','=',$grade_id];
        }
        if($status>0){
            $map[]=['goods_status','=',$status];
        }
        if($keyword!=''){
            $map[]=['goods_title','like',"%$keyword%"];
        }
        $search['keyword']=$keyword;
        $search['status']=$status;
        $search['school_id']=$school_id;
        $search['grade_id']=$grade_id;
        $field='*';
        $orderby=['goods_sortby'=>'desc','id'=>'desc'];
        $service=new GoodsService();
        $data=$service->getGoodsList(1,$map,$field,$search,20,$orderby);
        $smap=[];
        if($this->base_admininfo['school_id']>0){
            $smap[]=['id','=',$this->base_admininfo['school_id']];
        }
        $sfield='*';
        $sorderby=['sortby'=>'desc','id'=>'desc'];
        $sservice=new SchoolService();
        $sdata=$sservice->getSchoolList(2,$smap,$sfield,[],20,$sorderby);
        $gmap=[];
        if($this->base_admininfo['school_id']>0){
            $gmap[]=['school_id','=',$this->base_admininfo['school_id']];
        }
        $gfield='*';
        $gorderby=['sortby'=>'desc','id'=>'desc'];
        $gservice=new GradeService();
        $gdata=$gservice->getGradeList(2,$gmap,$gfield,[],20,$gorderby);
        $this->assign(['search'=>$search,'list'=>$data['list'],'count'=>$data['count'],'page'=>$data['page'],'school_list'=>$sdata['list'],'grade_list'=>$gdata['list']]);
        return $this->fetch();
    }

    //新增宽带套餐
    public function goods_add(){
        if(request()->isPost() || request()->isAjax()){
            $service=new GoodsService();
            return $service->goods_verify(0,$this->base_admininfo);
        }
        $map=[];
        if($this->base_admininfo['school_id']>0){
            $map[]=['id','=',$this->base_admininfo['school_id']];
        }
        $field='*';
        $orderby=['sortby'=>'desc','id'=>'desc'];
        $service=new SchoolService();
        $data=$service->getSchoolList(2,$map,$field,[],20,$orderby);
        $gmap=[];
        if($this->base_admininfo['school_id']>0){
            $gmap[]=['school_id','=',$this->base_admininfo['school_id']];
        }
        $gfield='*';
        $gorderby=['sortby'=>'desc','id'=>'desc'];
        $gservice=new GradeService();
        $gdata=$gservice->getGradeList(2,$gmap,$gfield,[],20,$gorderby);
        $this->assign(['school_list'=>$data['list'],'grade_list'=>$gdata['list']]);
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
            return $service->goods_verify($goodsid,$this->base_admininfo);
        }
        $goodsid=input('goodsid','0','intval');
        if($goodsid<=0){
            return jsondata('400','请选择宽带套餐信息');
        }

        $map[]=['id','=',$goodsid];
        if($this->base_admininfo['school_id']>0){
            $map[]=['school_id','=',$this->base_admininfo['school_id']];
        }
        $info=$service->goodsDetail($map);
        if(empty($info)){
            return jsondata('400','请选择宽带套餐信息');
        }
        $map=[];
        if($this->base_admininfo['school_id']>0){
            $map[]=['id','=',$this->base_admininfo['school_id']];
        }
        $field='*';
        $orderby=['sortby'=>'desc','id'=>'desc'];
        $service=new SchoolService();
        $data=$service->getSchoolList(2,$map,$field,[],20,$orderby);

        $gmap=[];
        $gmap[]=['school_id','=',$info['school_id']];
        $gfield='*';
        $gorderby=['sortby'=>'desc','id'=>'desc'];
        $gservice=new GradeService();
        $gdata=$gservice->getGradeList(2,$gmap,$gfield,[],20,$gorderby);
        $this->assign(['info'=>$info,'school_list'=>$data['list'],'grade_list'=>$gdata['list']]);
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
            return $service->goods_showhide($goodsid,1,$this->base_admininfo);
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
            return $service->goods_showhide($goodsid,2,$this->base_admininfo);
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
            return $service->goods_delete($goodsid,$this->base_admininfo);
        }
        return jsondata('400','网络错误');
    }

}
