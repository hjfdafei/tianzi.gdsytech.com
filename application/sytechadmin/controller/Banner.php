<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\service\BannerService;
use app\sytechadmin\service\SchoolService;
//幻灯片管理
class Banner extends Sytechadminbase{
    //幻灯片列表
    public function banner_list(){
        $keyword=input('keyword','','trim');
        $school_id=input('school_id','0','intval');
        if($this->base_admininfo['school_id']>0){
            $school_id=$this->base_admininfo['school_id'];
        }
        $map=[];
        if($school_id>0){
            $map[]=['school_id','=',$school_id];
        }
        if($keyword!=''){
            $map[]=['title','like',"%$keyword%"];
        }
        $search['keyword']=$keyword;
        $search['school_id']=$school_id;
        $field='*';
        $orderby=['sortby'=>'desc','id'=>'desc'];
        $service=new BannerService();
        $data=$service->getBannerList(1,$map,$field,$search,20,$orderby);
        $smap=[];
        if($this->base_admininfo['school_id']>0){
            $smap[]=['id','=',$this->base_admininfo['school_id']];
        }
        $sfield='*';
        $sorderby=['sortby'=>'desc','id'=>'desc'];
        $sservice=new SchoolService();
        $sdata=$sservice->getSchoolList(2,$smap,$sfield,[],20,$sorderby);
        $this->assign(['search'=>$search,'list'=>$data['list'],'count'=>$data['count'],'page'=>$data['page'],'school_list'=>$sdata['list']]);
        return $this->fetch();
    }

    //新增banner
    public function banner_add(){
        if(request()->isPost() || request()->isAjax()){
            $service=new BannerService();
            return $service->banner_verify(0,$this->base_admininfo);
        }
        $map=[];
        if($this->base_admininfo['school_id']>0){
            $map[]=['id','=',$this->base_admininfo['school_id']];
        }
        $field='*';
        $orderby=['sortby'=>'desc','id'=>'desc'];
        $service=new SchoolService();
        $data=$service->getSchoolList(2,$map,$field,[],20,$orderby);
        $position=config('app.bannerposition');
        $this->assign(['position'=>$position,'school_list'=>$data['list']]);
        return $this->fetch();
    }

    //编辑banner
    public function banner_edit(){
        $service=new BannerService();
        if(request()->isPost() || request()->isAjax()){
            $bannerid=input('post.bannerid','0','intval');
            if($bannerid<=0){
                return jsondata('400','请选择banner信息');
            }
            return $service->banner_verify($bannerid,$this->base_admininfo);
        }
        $bannerid=input('bannerid','0','intval');
        if($bannerid<=0){
            return jsondata('400','请选择banner信息');
        }

        $map[]=['id','=',$bannerid];
        if($this->base_admininfo['school_id']>0){
            $map[]=['school_id','=',$this->base_admininfo['school_id']];
        }
        $info=$service->bannerDetail($map);
        if(empty($info)){
            return jsondata('400','请选择banner信息');
        }
        $map=[];
        if($this->base_admininfo['school_id']>0){
            $map[]=['id','=',$this->base_admininfo['school_id']];
        }
        $field='*';
        $orderby=['sortby'=>'desc','id'=>'desc'];
        $service=new SchoolService();
        $data=$service->getSchoolList(2,$map,$field,[],20,$orderby);
        $position=config('app.bannerposition');
        $this->assign(['info'=>$info,'position'=>$position,'school_list'=>$data['list']]);
        return $this->fetch();
    }

    //显示banner
    public function banner_show(){
        if(request()->isPost() || request()->isAjax()){
            $bannerid=input('bannerid','0','intval');
            if($bannerid<=0){
                return jsondata('400','请选择需要显示的banner');
            }
            $service=new BannerService();
            return $service->banner_showhide($bannerid,1,$this->base_admininfo);
        }
        return jsondata('400','网络错误');
    }

    //隐藏banner
    public function banner_hide(){
        if(request()->isPost() || request()->isAjax()){
            $bannerid=input('bannerid','0','intval');
            if($bannerid<=0){
                return jsondata('400','请选择需要隐藏的banner');
            }
            $service=new BannerService();
            return $service->banner_showhide($bannerid,2,$this->base_admininfo);
        }
        return jsondata('400','网络错误');
    }

    //删除banner
    public function banner_del(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $bannerid=input('post.bannerid','','trim');
            if($bannerid==''){
                return jsondata('400','请选择要删除的banner');
            }
            $bannerid=explode(',',trim($bannerid,','));
            if(empty($bannerid)){
                return jsondata('400','请选择要删除的banner');
            }
            $service=new BannerService();
            return $service->banner_delete($bannerid,$this->base_admininfo);
        }
        return jsondata('400','网络错误');
    }

}
