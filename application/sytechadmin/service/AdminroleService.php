<?php
namespace app\sytechadmin\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\sytechadmin\model\Adminrole;
//角色
class AdminroleService extends Base{
    //field:查询字段 map:查询条件 search:搜索条件显示在分页链接 orderby:排序 pernum:每页多少条 type:获取数据类别 1获取分页 2获取全部
    //角色列表
    public function getAdminroleList($field='*',$map=[],$search=[],$pernum=20,$type=1,$orderby=['id'=>'asc']){
        $list=array();
        $page='';
        $count=0;
        if($type==1){
            $list=DB::name('adminrole')->field($field)->where($map)->order($orderby)->paginate($pernum,false,['query'=>$search])->each(function($item,$key){
                    $item['role_belong']='平台';
                    return $item;
                });
            $page=$list->render();
            $count=$list->total();
        }else{
            $list=DB::name('adminrole')->field($field)->where($map)->order($orderby)->select();
            $count=count($list);
        }
        $data['list']=$list;
        $data['page']=$page;
        $data['count']=$count;
        return $data;
    }

    //添加/修改角色
    public function role_verify($roleid=0){
        $role_title=input('post.role_title','','trim');
        $role_type=input('post.role_type','0','intval');
        $role_typeid=input('post.role_typeid','0','intval');
        $role_status=input('post.role_status','','intval');
        $roleid=intval($roleid);
        if($role_title==''){
            return jsondata('400','请输入角色名称');
        }
        if($role_type>0){
            if($role_typeid<=0){
                $typename=isset(config('admintype')[$role_type])?config('admintype')[$role_type]:'数据';
                return jsondata('400','请选择所属'.$typename);
            }
        }
        $map=[];
        $map[]=['role_title','=',$role_title];
        $map[]=['role_type','=',$role_type];
        $map[]=['role_typeid','=',$role_typeid];
        $hasrole=DB::name('adminrole')->where($map)->find();
        if(!empty($hasrole)){
            if($hasrole['id']!=$roleid){
                return jsondata('400','角色名称已存在');
            }
        }
        $info=array();
        if($roleid>0){
            $info=DB::name('adminrole')->where([['id','=',$roleid]])->find();
        }
        $data['role_title']=$role_title;
        $data['role_type']=$role_type;
        $data['role_typeid']=$role_typeid;
        $data['role_status']=$role_status;
        if(empty($info)){
            $data['create_time']=date('Y-m-d H:i:s');
            $data['adminid']=session('admininfo.id');
            $res=DB::name('adminrole')->insert($data);
        }else{
            $data['update_time']=date('Y-m-d H:i:s');
            $res=DB::name('adminrole')->where([['id','=',$info['id']]])->update($data);
        }
        if($res){
            return jsondata('200','数据保存成功');
        }else{
            return jsondata('400','数据保存失败,请重试');
        }
    }

    //删除角色
    public function role_delete($roleid){
        $delroleid=array();
        $hasadminroleid=array();
        foreach($roleid as $v){
            $map=[];
            $map[]=['id','=',intval($v)];
            $info=DB::name('adminrole')->where($map)->find();
            if(!empty($info)){
                $hasadmin=DB::name('adminuser')->where([['roleid','=',intval($v)]])->find();
                if(empty($hasadmin)){
                    $delroleid[]=$info['id'];
                }else{
                    $hasadminroleid[]=$hasadmin['id'];
                }
            }
        }
        if(!empty($hasadminroleid)){
            return jsondata('400','选择的角色还有管理员没删除,暂不能删除选中的角色,请先删除管理员,再删除');
        }
        if(empty($delroleid)){
            return jsondata('400','请选择要删除的角色');
        }
        $map=array();
        $num=0;
        $map[]=['id','in',$delroleid];
        $res=DB::name('adminrole')->where($map)->delete();
        if($res){
            return jsondata('200','删除角色成功');
        }else{
            return jsondata('400','删除角色失败,请重试');
        }
    }

    //启用/禁用角色
    public function role_openorclose($roleid,$status){
        $statusname=['1'=>'启用','2'=>'禁用'];
        $roleid=intval($roleid);
        if(!in_array($status,array(1,2))){
            return jsondata('400','请选择需要启用或者禁用的角色');
        }
        if($roleid<=0){
            return jsondata('400','请选择需要'.$statusname[$status].'的角色');
        }
        $map[]=['id','=',$roleid];
        $info=DB::name('adminrole')->where($map)->find();
        if(empty($info)){
            return jsondata('400','请选择需要'.$statusname[$status].'的角色');
        }
        if($info['role_status']==$status){
            return jsondata('400','当前角色已是'.$statusname[$status].'状态无需重复'.$statusname[$status]);
        }
        $update_data['role_status']=$status;
        $update_data['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('adminrole')->where($map)->update($update_data);
        if($res){
            return jsondata('200',$statusname[$status].'角色成功');
        }else{
            return jsondata('400',$statusname[$status].'角色失败,请重试');
        }
    }

    //分配权限 roleid角色id  ruleid权限id,数组格式
    public function role_assign($roleid,$ruleid,$havepermission,$issuperadmin=0){
        $roleid=intval($roleid);
        if($roleid<=0){
            return jsondata('400','请选择分配权限的角色');
        }
        if(empty($ruleid)){
            return jsondata('400','请选择权限');
        }
        $real_ruleid=[];
        foreach($ruleid as $v){
            $map=[];
            $map[]=['id','=',intval($v)];
            if($issuperadmin!=1){
                $map[]=['id','in',$havepermission];
            }
            $hasrule=DB::name('rule')->where($map)->find();
            if(!empty($hasrule)){
                $real_ruleid[]=$hasrule['id'];
            }
        }
        if(empty($real_ruleid)){
            return jsondata('400','请选择权限');
        }
        $map=[];
        $map[]=['id','=',$roleid];
        $update_data['role_ruleid']=implode(',',$real_ruleid);
        $update_data['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('adminrole')->where($map)->update($update_data);
        if($res){
            return jsondata('200','分配角色权限成功');
        }else{
            return jsondata('400','分配角色权限失败,请重试');
        }
    }

    //取消权限
    public function role_cancelassign($roleid){
        $roleid=intval($roleid);
        if($roleid<=0){
            return jsondata('400','请选择需要取消权限的角色');
        }
        $map=[];
        $map[]=['id','=',$roleid];
        $info=DB::name('adminrole')->where($map)->find();
        if(empty($info)){
            return jsondata('400','选择的角色不存在');
        }
        $update_data['role_ruleid']='';
        $update_data['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('adminrole')->where($map)->update($update_data);
        if($res){
            return jsondata('200','取消角色权限成功');
        }else{
            return jsondata('400','取消角色权限失败,请重试');
        }
    }
}
