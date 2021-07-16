<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\service\AdminroleService;
use app\sytechadmin\service\AdminuserService;
use app\sytechadmin\model\Rule;

class Adminrole extends Sytechadminbase{
    //角色列表
    public function adminrole_list(){
        $keyword=input('keyword','','trim');
        $role_status=input('status','','intval');
        $map=[];
        if($role_status>0){
            $map[]=['role_status','=',$role_status];
        }
        if($keyword!=''){
            $map[]=['role_title','like',"%$keyword%"];
        }
        $search['keyword']=$keyword;
        $search['status']=$role_status;
        $field='*';
        $adminroleservice=new AdminroleService();
        $data=$adminroleservice->getAdminroleList($field,$map,$search,20);
        $this->assign(['search'=>$search,'list'=>$data['list'],'count'=>$data['count'],'page'=>$data['page']]);
        return $this->fetch();
    }

    //添加角色
    public function adminrole_add(){
        if(request()->isPost() || request()->isAjax()){
            $roleservice=new AdminroleService();
            $roleservice->role_verify(0);
            return ;
        }
        return $this->fetch();
    }

    //修改角色
    public function adminrole_edit(){
        if(request()->isPost() || request()->isAjax()){
            $roleid=input('post.roleid','0','intval');
            $roleservice=new AdminroleService();
            $roleservice->role_verify($roleid);
            return ;
        }
        $roleid=input('roleid','0','intval');
        if($roleid<=0){
            return jsondata('400','请选择要编辑的角色');
        }
        $map[]=['id','=',$roleid];
        $info=DB::name('adminrole')->where($map)->find();
        if(empty($info)){
            return jsondata('400','选择编辑的角色信息不存在');
        }
        $this->assign('info',$info);
        return $this->fetch();
    }

    //删除角色
    public function adminrole_del(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $roleid=input('post.roleid','','trim');
            if($roleid==''){
                return jsondata('400','请选择要删除的角色');
            }
            $roleid=explode(',',trim($roleid,','));
            if(empty($roleid)){
                return jsondata('400','请选择要删除的角色');
            }
            $roleservice=new AdminroleService();
            return $roleservice->role_delete($roleid);
        }
        return jsondata('400','网络错误');
    }

    //禁用角色
    public function adminrole_close(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $status=2;
            $roleid=input('post.roleid','0','intval');
            $roleservice=new AdminroleService();
            return $roleservice->role_openorclose($roleid,$status);
        }
        return jsondata('400','网络错误');
    }

    //启用角色
    public function adminrole_open(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $status=1;
            $roleid=input('post.roleid','0','intval');
            $roleservice=new AdminroleService();
            return $roleservice->role_openorclose($roleid,$status);
        }
        return jsondata('400','网络错误');
    }

    //分配权限
    public function adminrole_assign(){
        if(request()->isPost() || request()->isAjax()){
            $roleid=input('post.roleid','0','intval');
            $ruleid=input('post.ids/a','','trim');
            if($roleid<=0){
                return jsondata('400','请选择要分配权限的角色');
            }
            if($ruleid==''){
                return jsondata('400','请选择权限');
            }
            if(empty($ruleid)){
                return jsondata('400','请选择权限');
            }
            $roleservice=new AdminroleService();
            $havepermission=explode(',',$this->base_admininfo['ruleid']);
            return $roleservice->role_assign($roleid,$ruleid,$havepermission,$this->base_admininfo['issuperadmin']);
        }
        $roleid=input('roleid','0','intval');
        if($roleid<=0){
            return jsondata('400','请选择要分配权限的角色');
        }
        $info=DB::name('adminrole')->where([['id','=',$roleid]])->find();
        if(empty($info)){
            return jsondata('400','请选择要分配权限的角色');
        }
        $info['role_ruleid']=explode(',',$info['role_ruleid']);
        $map=[];
        $adminuserservice=new AdminuserService();
        $roleids=$adminuserservice->admin_getruleid($this->base_admininfo['id']);
        $map[]=['id','in',$roleids];
        $rulemodel=new Rule();
        $list=$rulemodel->getRule($map);
        $this->assign(['list'=>$list,'info'=>$info]);
        return $this->fetch();
    }

    //取消权限
    public function adminrole_cancelassign(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $status=1;
            $roleid=input('post.roleid','0','intval');
            $roleservice=new AdminroleService();
            return $roleservice->role_cancelassign($roleid);
        }
        return jsondata('400','网络错误');
    }

}
