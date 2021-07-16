<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\service\BannerService;
//幻灯片管理
class Banner extends Sytechadminbase{
    //幻灯片列表
    public function banner_list(){
        $keyword=input('keyword','','trim');
        $map=[];
        if($keyword!=''){
            $map[]=['title','like',"%$keyword%"];
        }
        $search['keyword']=$keyword;
        $field='*';
        $orderby=['sortby'=>'desc','id'=>'desc'];
        $service=new BannerService();
        $data=$service->getBannerList(1,$map,$field,$search,20,$orderby);
        $this->assign(['search'=>$search,'list'=>$data['list'],'count'=>$data['count'],'page'=>$data['page']]);
        return $this->fetch();
    }

    //新增banner
    public function banner_add(){
        if(request()->isPost() || request()->isAjax()){
            $service=new BannerService();
            return $service->banner_verify(0);
        }
        $position=config('app.bannerposition');
        $this->assign(['position'=>$position]);
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
            return $service->banner_verify($bannerid);
        }
        $bannerid=input('bannerid','0','intval');
        if($bannerid<=0){
            return jsondata('400','请选择banner信息');
        }

        $map[]=['id','=',$bannerid];
        $info=$service->bannerDetail($map);
        if(empty($info)){
            return jsondata('400','请选择banner信息');
        }
        $position=config('app.bannerposition');
        $this->assign(['info'=>$info,'position'=>$position]);
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
            return $service->banner_showhide($bannerid,1);
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
            return $service->banner_showhide($bannerid,2);
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
            return $service->banner_delete($bannerid);
        }
        return jsondata('400','网络错误');
    }

}
