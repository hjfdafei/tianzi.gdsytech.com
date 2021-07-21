<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\service\BroadbandService;
use app\sytechadmin\service\SchoolService;
//宽带账号管理
class Broadband extends Sytechadminbase{
    //宽带账号列表
    public function broadband_list(){
        $keyword=input('keyword','','trim');
        $usestatus=input('usestatus','0','intval');
        $status=input('status','0','intval');
        $school_id=input('school_id','0','intval');
        if($this->base_admininfo['school_id']>0){
            $school_id=$this->base_admininfo['school_id'];
        }
        $map=[];
        if($status>0){
            $map[]=['status','=',$status];
        }
        if($usestatus>0){
            $map[]=['isuse','=',$usestatus];
        }
        if($school_id>0){
            $map[]=['school_id','=',$school_id];
        }
        if($keyword!=''){
            $map[]=['keyaccount','like',"%$keyword%"];
        }
        $search['keyword']=$keyword;
        $search['usestatus']=$usestatus;
        $search['status']=$status;
        $search['school_id']=$school_id;
        $field='*';
        $orderby=['isuse'=>'desc','status'=>'asc'];
        $service=new BroadbandService();
        $data=$service->getBroadbandList(1,$map,$field,$search,20,$orderby);
        $schoolservice=new SchoolService();
        $smap=[];
        if($school_id>0){
            $smap[]=['id','=',$school_id];
        }
        $sfield='*';
        $sorderby=['sortby'=>'desc','id'=>'desc'];
        $school_list=$schoolservice->getSchoolList(2,$smap,$sfield,[],20,$sorderby)['list'];
        $this->assign(['search'=>$search,'list'=>$data['list'],'count'=>$data['count'],'page'=>$data['page'],'school_list'=>$school_list]);
        return $this->fetch();
    }

    //新增宽带账号
    public function broadband_add(){
        if(request()->isPost() || request()->isAjax()){
            $service=new BroadbandService();
            return $service->broadband_verify(0,$this->base_admininfo);
        }
        $schoolservice=new SchoolService();
        $smap=[];
        if($this->base_admininfo['school_id']>0){
            $smap[]=['id','=',$this->base_admininfo['school_id']];
        }
        $sfield='*';
        $sorderby=['sortby'=>'desc','id'=>'desc'];
        $school_list=$schoolservice->getSchoolList(2,$smap,$sfield,[],20,$sorderby)['list'];
        $this->assign(['school_list'=>$school_list]);
        return $this->fetch();
    }

    //编辑宽带账号
    public function broadband_edit(){
        $service=new BroadbandService();
        if(request()->isPost() || request()->isAjax()){
            $broadbandid=input('post.broadbandid','0','intval');
            if($broadbandid<=0){
                return jsondata('400','请选择宽带账号信息');
            }
            return $service->broadband_verify($broadbandid,$this->base_admininfo);
        }
        $broadbandid=input('broadbandid','0','intval');
        if($broadbandid<=0){
            return jsondata('400','请选择宽带账号信息');
        }

        $map[]=['id','=',$broadbandid];
        $info=$service->broadbandDetail($map);
        if(empty($info)){
            return jsondata('400','请选择宽带账号信息');
        }
        $schoolservice=new SchoolService();
        $smap=[];
        if($this->base_admininfo['school_id']>0){
            $smap[]=['id','=',$this->base_admininfo['school_id']];
        }
        $sfield='*';
        $sorderby=['sortby'=>'desc','id'=>'desc'];
        $school_list=$schoolservice->getSchoolList(2,$smap,$sfield,[],20,$sorderby)['list'];
        $this->assign(['info'=>$info,'school_list'=>$school_list]);
        return $this->fetch();
    }

    //导入宽带账号
    public function broadband_import(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $uploadfiles=request()->file('uploadfiles');
            if(empty($uploadfiles)){
                return jsondata('400','请选择上传文件');
            }
            $fileinfo=$uploadfiles->getInfo();
            $allow_exe=array('application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/kset','application/octet-stream');
            if(!in_array($fileinfo['type'],$allow_exe)){
                return jsondata('400','您导入的【'.$fileinfo['type'].'】格式不正确');
            }
            if($fileinfo['size']>20*1024*1024){
                return jsondata('400','上传文件大小不能超过20M');
            }
            if($fileinfo['tmp_name']==''){
                return jsondata('400','请选择上传文件');
            }
            $service=new BroadbandService();
            return $service->broadband_importdata($fileinfo,$this->base_admininfo);
        }
        $schoolservice=new SchoolService();
        $smap=[];
        if($this->base_admininfo['school_id']>0){
            $smap[]=['id','=',$this->base_admininfo['school_id']];
        }
        $sfield='*';
        $sorderby=['sortby'=>'desc','id'=>'desc'];
        $school_list=$schoolservice->getSchoolList(2,$smap,$sfield,[],20,$sorderby)['list'];
        $this->assign(['school_list'=>$school_list]);
        return $this->fetch();
    }

    //启用宽带账号
    public function broadband_show(){
        if(request()->isPost() || request()->isAjax()){
            $broadbandid=input('broadbandid','0','intval');
            if($broadbandid<=0){
                return jsondata('400','请选择需要启用的宽带账号');
            }
            $service=new BroadbandService();
            return $service->broadband_showhide($broadbandid,1,$this->base_admininfo);
        }
        return jsondata('400','网络错误');
    }

    //禁用宽带账号
    public function broadband_hide(){
        if(request()->isPost() || request()->isAjax()){
            $broadbandid=input('broadbandid','0','intval');
            if($broadbandid<=0){
                return jsondata('400','请选择需要禁用的宽带账号');
            }
            $service=new BroadbandService();
            return $service->broadband_showhide($broadbandid,2,$this->base_admininfo);
        }
        return jsondata('400','网络错误');
    }

    //删除宽带账号
    public function broadband_del(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $broadbandid=input('post.broadbandid','','trim');
            if($broadbandid==''){
                return jsondata('400','请选择要删除的宽带账号');
            }
            $broadbandid=explode(',',trim($broadbandid,','));
            if(empty($broadbandid)){
                return jsondata('400','请选择要删除的宽带账号');
            }
            $service=new BroadbandService();
            return $service->broadband_delete($broadbandid,$this->base_admininfo);
        }
        return jsondata('400','网络错误');
    }

}
