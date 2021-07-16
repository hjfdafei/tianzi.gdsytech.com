<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\service\BroadbandService;
//宽带账号管理
class Broadband extends Sytechadminbase{
    //宽带账号列表
    public function broadband_list(){
        $keyword=input('keyword','','trim');
        $usestatus=input('usestatus','0','intval');
        $status=input('status','0','intval');
        $map=[];
        if($status>0){
            $map[]=['status','=',$status];
        }
        if($usestatus>0){
            $map[]=['isuse','=',$usestatus];
        }
        if($keyword!=''){
            $map[]=['keyaccount','like',"%$keyword%"];
        }
        $search['keyword']=$keyword;
        $search['usestatus']=$usestatus;
        $search['status']=$status;
        $field='*';
        $orderby=['isuse'=>'desc','status'=>'asc'];
        $service=new BroadbandService();
        $data=$service->getBroadbandList(1,$map,$field,$search,20,$orderby);
        $this->assign(['search'=>$search,'list'=>$data['list'],'count'=>$data['count'],'page'=>$data['page']]);
        return $this->fetch();
    }

    //新增宽带账号
    public function broadband_add(){
        if(request()->isPost() || request()->isAjax()){
            $service=new BroadbandService();
            return $service->broadband_verify(0);
        }
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
            return $service->broadband_verify($broadbandid);
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
        $this->assign(['info'=>$info]);
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
            return $service->broadband_importdata($fileinfo);
        }
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
            return $service->broadband_showhide($broadbandid,1);
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
            return $service->broadband_showhide($broadbandid,2);
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
            return $service->broadband_delete($broadbandid);
        }
        return jsondata('400','网络错误');
    }

}
