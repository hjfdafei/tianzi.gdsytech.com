<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\service\GradeService;
use app\sytechadmin\service\SchoolService;
//年级管理
class Grade extends Sytechadminbase{
    //年级列表
    public function grade_list(){
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
        $service=new GradeService();
        $data=$service->getGradeList(1,$map,$field,$search,20,$orderby);
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

    //新增年级
    public function grade_add(){
        if(request()->isPost() || request()->isAjax()){
            $service=new GradeService();
            return $service->grade_verify(0,$this->base_admininfo);
        }
        $map=[];
        if($this->base_admininfo['school_id']>0){
            $map[]=['id','=',$this->base_admininfo['school_id']];
        }
        $field='*';
        $orderby=['sortby'=>'desc','id'=>'desc'];
        $service=new SchoolService();
        $data=$service->getSchoolList(2,$map,$field,[],20,$orderby);
        $this->assign(['school_list'=>$data['list']]);
        return $this->fetch();
    }

    //编辑年级
    public function grade_edit(){
        $service=new GradeService();
        if(request()->isPost() || request()->isAjax()){
            $grade_id=input('post.grade_id','0','intval');
            if($grade_id<=0){
                return jsondata('400','请选择年级信息');
            }
            return $service->grade_verify($grade_id,$this->base_admininfo);
        }
        $grade_id=input('grade_id','0','intval');
        if($grade_id<=0){
            return jsondata('400','请选择年级信息');
        }

        $map[]=['id','=',$grade_id];
        if($this->base_admininfo['school_id']>0){
            $map[]=['school_id','=',$this->base_admininfo['school_id']];
        }
        $info=$service->gradeDetail($map);
        if(empty($info)){
            return jsondata('400','请选择年级信息');
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

    //显示年级
    public function grade_show(){
        if(request()->isPost() || request()->isAjax()){
            $grade_id=input('grade_id','0','intval');
            if($grade_id<=0){
                return jsondata('400','请选择需要显示的年级');
            }
            $service=new GradeService();
            return $service->grade_showhide($grade_id,1,$this->base_admininfo);
        }
        return jsondata('400','网络错误');
    }

    //隐藏年级
    public function grade_hide(){
        if(request()->isPost() || request()->isAjax()){
            $grade_id=input('grade_id','0','intval');
            if($grade_id<=0){
                return jsondata('400','请选择需要隐藏的年级');
            }
            $service=new GradeService();
            return $service->grade_showhide($grade_id,2,$this->base_admininfo);
        }
        return jsondata('400','网络错误');
    }

    //删除年级
    public function grade_del(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $grade_id=input('post.grade_id','','trim');
            if($grade_id==''){
                return jsondata('400','请选择要删除的年级');
            }
            $grade_id=explode(',',trim($grade_id,','));
            if(empty($grade_id)){
                return jsondata('400','请选择要删除的年级');
            }
            $service=new GradeService();
            return $service->grade_delete($grade_id,$this->base_admininfo);
        }
        return jsondata('400','网络错误');
    }

}
