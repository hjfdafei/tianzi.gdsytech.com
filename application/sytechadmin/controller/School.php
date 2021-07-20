<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\service\SchoolService;
//校区管理
class School extends Sytechadminbase{
    //校区列表
    public function school_list(){
        $keyword=input('keyword','','trim');
        $status=input('status','0','intval');
        $map=[];
        if($status>0){
            $map[]=['status','=',$status];
        }
        if($keyword!=''){
            $map[]=['title','like',"%$keyword%"];
        }
        $search['keyword']=$keyword;
        $search['status']=$status;
        $field='*';
        $orderby=['sortby'=>'desc','id'=>'desc'];
        $service=new SchoolService();
        $data=$service->getSchoolList(1,$map,$field,$search,20,$orderby);
        $this->assign(['search'=>$search,'list'=>$data['list'],'count'=>$data['count'],'page'=>$data['page']]);
        return $this->fetch();
    }

    //新增校区
    public function school_add(){
        if(request()->isPost() || request()->isAjax()){
            $service=new SchoolService();
            return $service->school_verify(0);
        }
        return $this->fetch();
    }

    //编辑校区
    public function school_edit(){
        $service=new SchoolService();
        if(request()->isPost() || request()->isAjax()){
            $schoolid=input('post.schoolid','0','intval');
            if($schoolid<=0){
                return jsondata('400','请选择校区信息');
            }
            return $service->school_verify($schoolid);
        }
        $schoolid=input('schoolid','0','intval');
        if($schoolid<=0){
            return jsondata('400','请选择校区信息');
        }

        $map[]=['id','=',$schoolid];
        $info=$service->schoolDetail($map);
        if(empty($info)){
            return jsondata('400','请选择校区信息');
        }
        $this->assign(['info'=>$info]);
        return $this->fetch();
    }

    //启用校区
    public function school_show(){
        if(request()->isPost() || request()->isAjax()){
            $schoolid=input('schoolid','0','intval');
            if($schoolid<=0){
                return jsondata('400','请选择需要启用的校区');
            }
            $service=new SchoolService();
            return $service->school_showhide($schoolid,1);
        }
        return jsondata('400','网络错误');
    }

    //禁用校区
    public function school_hide(){
        if(request()->isPost() || request()->isAjax()){
            $schoolid=input('schoolid','0','intval');
            if($schoolid<=0){
                return jsondata('400','请选择需要禁用的校区');
            }
            $service=new SchoolService();
            return $service->school_showhide($schoolid,2);
        }
        return jsondata('400','网络错误');
    }

    //删除校区
    public function school_del(){
        return jsondata('400','网络错误');
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $schoolid=input('post.schoolid','','trim');
            if($schoolid==''){
                return jsondata('400','请选择要删除的校区');
            }
            $schoolid=explode(',',trim($schoolid,','));
            if(empty($schoolid)){
                return jsondata('400','请选择要删除的校区');
            }
            $service=new SchoolService();
            return $service->school_delete($schoolid);
        }
        return jsondata('400','网络错误');
    }

}
