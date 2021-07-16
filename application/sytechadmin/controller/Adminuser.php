<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\service\AdminuserService;
use app\sytechadmin\service\MeetingsiteService;
use app\sytechadmin\model\Rule;

class Adminuser extends Sytechadminbase{
    //管理员列表
    public function adminuser_list(){
        $keyword=input('keyword','','trim');
        $status=input('status','','intval');
        $meetingsiteid=input('meetingsiteid','0','intval');
        $map=[];
        $map[]=['id','<>',1];
        if($status>0){
            $map[]=['status','=',$status];
        }
        if($meetingsiteid>0){
            $map[]=['meetingsiteid','=',$meetingsiteid];
        }
        if($keyword!=''){
            $map[]=['username|mobile','like',"%$keyword%"];
        }
        $search['keyword']=$keyword;
        $search['status']=$status;
        $search['meetingsiteid']=$meetingsiteid;
        $field='*';
        $adminuserservice=new AdminuserService();
        $data=$adminuserservice->getAdminuserList($field,$map,$search,20);
        $meetingsiteservice=new MeetingsiteService();
        $meetmap=[];
        if($this->base_admininfo['meetingsiteid']>0){
            $meetmap[]=['id','=',$this->base_admininfo['meetingsiteid']];
        }
        $meetingsitelist=$meetingsiteservice->getMeetingsiteList(2,$meetmap);
        $this->assign(['search'=>$search,'list'=>$data['list'],'count'=>$data['count'],'page'=>$data['page'],'meetingsitelist'=>$meetingsitelist['list']]);
        return $this->fetch();
    }

    //添加管理员
    public function adminuser_add(){
        $adminuserservice=new AdminuserService();
        if(request()->isPost() || request()->isAjax()){
            $adminuserservice->admin_verify(0);
            return ;
        }
        $meetingsiteservice=new MeetingsiteService();
        $meetmap=[];
        if($this->base_admininfo['meetingsiteid']>0){
            $meetmap[]=['id','=',$this->base_admininfo['meetingsiteid']];
        }
        $meetingsitelist=$meetingsiteservice->getMeetingsiteList(2,$meetmap);
        $rolist=$adminuserservice->admin_rolelist();
        $this->assign(['rolist'=>$rolist,'meetingsitelist'=>$meetingsitelist['list']]);
        return $this->fetch();
    }

    //修改管理员
    public function adminuser_edit(){
        $adminuserservice=new AdminuserService();
        if(request()->isPost() || request()->isAjax()){
            $adminuserid=input('post.adminuserid','0','intval');
            $adminuserservice->admin_verify($adminuserid);
            return ;
        }
        $adminuserid=input('adminuserid','0','intval');
        if($adminuserid<=0){
            return jsondata('400','请选择要编辑的管理员');
        }
        $map[]=['id','=',$adminuserid];
        $info=DB::name('adminuser')->where($map)->find();
        if(empty($info)){
            return jsondata('400','选择编辑的管理员信息不存在');
        }
        //$info['admintype_list']=config('admintypelist');
        //$info['admintypeid_list']=$adminuserservice->admin_gettypelist($info['admintype']);
        $info['role_list']=$adminuserservice->admin_rolelist();
        $meetingsiteservice=new MeetingsiteService();
        $meetmap=[];
        if($this->base_admininfo['meetingsiteid']>0){
            $meetmap[]=['id','=',$this->base_admininfo['meetingsiteid']];
        }
        $meetingsitelist=$meetingsiteservice->getMeetingsiteList(2,$meetmap);
        $this->assign(['info'=>$info,'meetingsitelist'=>$meetingsitelist['list']]);
        return $this->fetch();
    }

    //删除角色
    public function adminuser_del(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $adminuserid=input('post.adminuserid','','trim');
            if($adminuserid==''){
                return jsondata('400','请选择要删除的角色');
            }
            $adminuserid=explode(',',trim($adminuserid,','));
            if(empty($adminuserid)){
                return jsondata('400','请选择要删除的角色');
            }
            $adminuserservice=new AdminuserService();
            return $adminuserservice->admin_delete($adminuserid);
        }
        return jsondata('400','网络错误');
    }

    //禁用管理员
    public function adminuser_close(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $status=2;
            $adminuserid=input('post.adminuserid','0','intval');
            $adminiuserservice=new AdminuserService();
            return $adminiuserservice->admin_openorclose($adminuserid,$status);
        }
        return jsondata('400','网络错误');
    }

    //启用管理员
    public function adminuser_open(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $status=1;
            $adminuserid=input('post.adminuserid','0','intval');
            $adminiuserservice=new AdminuserService();
            return $adminiuserservice->admin_openorclose($adminuserid,$status);
        }
        return jsondata('400','网络错误');
    }

    //分配权限
    public function adminuser_assign(){
        if(request()->isPost() || request()->isAjax()){
            $adminuserid=input('post.adminuserid','0','intval');
            $ruleid=input('post.ids/a','','trim');

            if($adminuserid<=0){
                return jsondata('400','请选择要分配权限的管理员');
            }
            if($ruleid==''){
                return jsondata('400','请选择权限');
            }
            if(empty($ruleid)){
                return jsondata('400','请选择权限');
            }
            $info=DB::name('adminuser')->where([['id','=',$adminuserid]])->find();
            if(empty($info)){
                return jsondata('400','请选择要分配权限的管理员');
            }
            $adminuserservice=new AdminuserService();
            $role_ruleid=array();
            $ownroleinfo=DB::name('adminrole')->where([['id','=',$info['roleid']]])->find();
            $role_ruleid=explode(',',$ownroleinfo['role_ruleid']);
            if($this->base_admininfo['issuperadmin']!=1){
                $tmpruleid2=explode(',',$this->base_admininfo['ruleid']);
                $havepermission=array_merge($role_ruleid,$tmpruleid2);
                $havepermission=array_unique($havepermission);
            }
            $havepermission=explode(',',$this->base_admininfo['ruleid']);
            return $adminuserservice->admin_assign($adminuserid,$ruleid,$havepermission,$role_ruleid,$this->base_admininfo['issuperadmin']);
        }
        $adminuserid=input('adminuserid','0','intval');
        if($adminuserid<=0){
            return jsondata('400','请选择要分配权限的管理员');
        }
        $info=DB::name('adminuser')->where([['id','=',$adminuserid]])->find();
        if(empty($info)){
            return jsondata('400','请选择要分配权限的管理员');
        }
        $roleinfo=DB::name('adminrole')->where([['id','=',$info['roleid']]])->find();
        $info['ruleid']=explode(',',$info['ruleid']);
        $info['role_ruleid']=explode(',',$roleinfo['role_ruleid']);
        $map=[];
        if($this->base_admininfo['issuperadmin']!=1){
            $ownroleinfo=DB::name('adminrole')->where([['id','=',$info['roleid']]])->find();
            $tmpruleid=explode(',',$ownroleinfo['role_ruleid']);
            $tmpruleid2=explode(',',$this->base_admininfo['ruleid']);
            $tmpruleid=array_merge($tmpruleid2);
            $map[]=['id','in',$tmpruleid];
        }
        $rulemodel=new Rule();
        $list=$rulemodel->getRule($map);
        $this->assign(['list'=>$list,'info'=>$info]);
        return $this->fetch();
    }

    //取消权限
    public function adminuser_cancelassign(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $adminuserid=input('post.adminuserid','0','intval');
            $adminuserservice=new AdminuserService();
            return $adminuserservice->admin_cancelassign($adminuserid);
        }
        return jsondata('400','网络错误');
    }

}
