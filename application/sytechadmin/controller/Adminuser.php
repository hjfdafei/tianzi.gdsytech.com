<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\service\AdminuserService;
use app\sytechadmin\service\SchoolService;
use app\sytechadmin\model\Rule;

class Adminuser extends Sytechadminbase{
    //管理员列表
    public function adminuser_list(){
        $keyword=input('keyword','','trim');
        $status=input('status','','intval');
        $school_id=input('school_id','0','intval');
        if($this->base_admininfo['school_id']>0){
            $school_id=$this->base_admininfo['school_id'];
        }
        $map=[];
        $map[]=['id','<>',1];
        if($status>0){
            $map[]=['status','=',$status];
        }
        if($school_id>0){
            $map[]=['school_id','=',$school_id];
        }
        if($keyword!=''){
            $map[]=['username|mobile','like',"%$keyword%"];
        }
        $search['keyword']=$keyword;
        $search['status']=$status;
        $search['school_id']=$school_id;
        $field='*';
        $adminuserservice=new AdminuserService();
        $data=$adminuserservice->getAdminuserList($field,$map,$search,20);
        $schoolservice=new SchoolService();
        $smap=[];
        if($this->base_admininfo['school_id']>0){
            $smap[]=['id','=',$this->base_admininfo['school_id']];
        }
        $school_list=$schoolservice->getSchoolList(2,$smap)['list'];
        $this->assign(['search'=>$search,'list'=>$data['list'],'count'=>$data['count'],'page'=>$data['page'],'school_list'=>$school_list]);
        return $this->fetch();
    }

    //添加管理员
    public function adminuser_add(){
        $adminuserservice=new AdminuserService();
        if(request()->isPost() || request()->isAjax()){
            $adminuserservice->admin_verify(0,$this->base_admininfo);
            return ;
        }
        $schoolservice=new SchoolService();
        $smap=[];
        if($this->base_admininfo['school_id']>0){
            $smap[]=['id','=',$this->base_admininfo['school_id']];
        }
        $school_list=$schoolservice->getSchoolList(2,$smap)['list'];
        $rolist=$adminuserservice->admin_rolelist();
        $this->assign(['rolist'=>$rolist,'school_list'=>$school_list]);
        return $this->fetch();
    }

    //修改管理员
    public function adminuser_edit(){
        $adminuserservice=new AdminuserService();
        if(request()->isPost() || request()->isAjax()){
            $adminuserid=input('post.adminuserid','0','intval');
            $adminuserservice->admin_verify($adminuserid,$this->base_admininfo);
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
        $info['role_list']=$adminuserservice->admin_rolelist();
        $schoolservice=new SchoolService();
        $smap=[];
        if($this->base_admininfo['school_id']>0){
            $smap[]=['id','=',$this->base_admininfo['school_id']];
        }
        $school_list=$schoolservice->getSchoolList(2,$smap)['list'];
        $this->assign(['info'=>$info,'school_list'=>$school_list]);
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
            return $adminuserservice->admin_delete($adminuserid,$this->base_admininfo);
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
            return $adminiuserservice->admin_openorclose($adminuserid,$status,$this->base_admininfo);
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
            return $adminiuserservice->admin_openorclose($adminuserid,$status,$this->base_admininfo);
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
            return $adminuserservice->admin_cancelassign($adminuserid,$this->base_admininfo);
        }
        return jsondata('400','网络错误');
    }

}
