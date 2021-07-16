<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\service\UserService;
use app\sytechadmin\service\AssistantsService;
use app\sytechadmin\service\BasedoctorsService;

//用户管理
class User extends Sytechadminbase{
    //用户列表
    public function user_list(){
        $keyword=input('keyword','','trim');
        $basedoctorsid=input('basedoctorsid','0','intval');
        $roletype=input('roletype','0','intval');
        $map=[];
        if($basedoctorsid>0){
            $map[]=['basedoctor_id','=',$basedoctorsid];
        }
        if($roletype>0){
            $map[]=['roletype','=',$roletype];
        }
        if($keyword!=''){
            $map[]=['nickname|realname|mobile|openid','like',"%$keyword%"];
        }
        $search['basedoctorsid']=$basedoctorsid;
        $search['roletype']=$roletype;
        $search['keyword']=$keyword;
        $field='*';
        $orderby=['id'=>'desc'];
        $service=new UserService();
        $data=$service->getUserList(1,$map,$field,$search,20,$orderby);
        $this->assign(['search'=>$search,'list'=>$data['list'],'count'=>$data['count'],'page'=>$data['page']]);
        return $this->fetch();
    }

    //编辑用户信息
    public function user_edit(){
        $service=new UserService();
        if(request()->isPost() || request()->isAjax()){
            $userid=input('post.userid','0','intval');
            if($userid<=0){
                return jsondata('400','请选择用户信息');
            }
            return $service->user_verify($userid);
        }
        $userid=input('userid','0','intval');
        if($userid<=0){
            return jsondata('400','请选择用户信息');
        }

        $map[]=['id','=',$userid];
        $info=$service->userDetail($map);
        if(empty($info)){
            return jsondata('400','请选择用户信息');
        }
        $this->assign(['info'=>$info]);
        return $this->fetch();
    }

}
