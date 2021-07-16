<?php
namespace app\sytechadmin\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\sytechadmin\model\Adminuser;
use app\sytechadmin\service\MeetingsiteService;
//管理员
class AdminuserService extends Base{
    //field:查询字段 map:查询条件 search:搜索条件显示在分页链接 orderby:排序 pernum:每页多少条 type:获取数据类别 1获取分页 2获取全部
    //角色列表
    public function getAdminuserList($field='*',$map=[],$search=[],$pernum=20,$type=1,$orderby=['id'=>'asc']){
        $list=array();
        $page='';
        $count=0;
        $meetingsiteservice=new MeetingsiteService();
        $meetingsitelist=$meetingsiteservice->getMeetingsiteList(2);
        $meetingsitename=[];
        foreach($meetingsitelist['list'] as $v){
            $meetingsitename[$v['id']]=$v['title'];
        }
        if($type==1){
            $list=DB::name('adminuser')->field($field)->where($map)->order($orderby)->paginate($pernum,false,['query'=>$search])->each(function($item,$key) use($meetingsitename){
                    //$item['admin_belong']=$this->admin_getdetailname($item['admintype'],$item['admintypeid']);
                    //$item['admin_typename']=$this->admin_gettypename($item['admintype']);
                    $tmpname='平台';
                    if(isset($meetingsitename[$item['meetingsiteid']])){
                        $tmpname=$meetingsitename[$item['meetingsiteid']];
                    }
                    $item['admin_belong']=$tmpname;
                    $item['admin_rolename']=$this->admin_getrolename($item['roleid']);
                    return $item;
                });
            $page=$list->render();
            $count=$list->total();
        }else{
            $list=DB::name('adminuser')->field($field)->where($map)->order($orderby)->select();
            $count=count($list);
        }
        $data['list']=$list;
        $data['page']=$page;
        $data['count']=$count;
        return $data;
    }

    //添加/修改管理员
    public function admin_verify($adminuserid=0){
        $username=input('post.username','','trim');
        $userpassword=input('post.userpassword','','trim');
        $mobile=input('post.mobile','','trim');
        $status=input('post.status','','intval');
        $roleid=input('post.roleid','','intval');
        $meetingsiteid=input('post.meetingsiteid','0','intval');
        $admintype=input('post.admintype','0','intval');
        $admintypeid=input('post.admintypeid','0','intval');
        $adminuserid=intval($adminuserid);
        if($username==''){
            return jsondata('400','请输入管理员账号');
        }
        if(in_array($username,array('admin','root','user','www'))){
            return jsondata('400','输入的管理员账号不合法');
        }
        if($roleid<=0){
            return jsondata('400','请选择管理员角色');
        }
        if($admintype>0){
            if($admintypeid<=0){
                return jsondata('400','请选择管理员类型');
            }
        }
        if($meetingsiteid>0){
            $meetingsiteservice=new MeetingsiteService();
            $meetmap=[];
            $meetmap[]=['id','=',$meetingsiteid];
            $meetingsiteinfo=$meetingsiteservice->getMeetingsiteDetail($meetmap);
            if(empty($meetingsiteinfo)){
                return jsondata('400','选择的站点不存在');
            }
        }
        $map=[];
        $map[]=['username','=',$username];
        $hasadmin=DB::name('adminuser')->where($map)->find();
        if(!empty($hasadmin)){
            if($hasadmin['id']!=$adminuserid){
                return jsondata('400','管理员账号已存在');
            }
        }
        $info=array();
        if($adminuserid>0){
            $info=DB::name('adminuser')->where([['id','=',$adminuserid]])->find();
        }
        $data['username']=$username;
        $data['mobile']=$mobile;
        $data['status']=$status;
        $data['roleid']=$roleid;
        $data['meetingsiteid']=$meetingsiteid;
        //$data['admintype']=$admintype;
        //$data['admintypeid']=$admintypeid;
        if(empty($info)){
            if($userpassword==''){
                return jsondata('400','请输入密码');
            }
            $userpassword=md5(md5($userpassword));
            $data['password']=$userpassword;
            if(session('admininfo.preadminids')!=''){
                $tmppreadminids=explode(',',session('admininfo.preadminids'));
            }
            $tmppreadminids[]=session('admininfo.id');
            $data['preadminids']=implode(',',array_unique($tmppreadminids));
            $data['parentid']=session('admininfo.id');
            $data['create_time']=date('Y-m-d H:i:s');
            $data['adminid']=session('admininfo.id');
            $res=DB::name('adminuser')->insert($data);
        }else{
            if($userpassword!=''){
                $userpassword=md5(md5($userpassword));
                $data['password']=$userpassword;
            }
            $data['update_time']=date('Y-m-d H:i:s');
            $res=DB::name('adminuser')->where([['id','=',$info['id']]])->update($data);
        }
        if($res){
            return jsondata('200','数据保存成功');
        }else{
            return jsondata('400','数据保存失败,请重试');
        }
    }

    //删除管理员
    public function admin_delete($adminuserid){
        $deladminuserid=array();
        foreach($adminuserid as $v){
            $map=[];
            $map[]=['id','=',intval($v)];
            $info=DB::name('adminuser')->where($map)->find();
            if(!empty($info)){
                $deladminuserid[]=$info['id'];
            }
        }
        if(empty($deladminuserid)){
            return jsondata('400','请选择要删除的管理员');
        }
        $map=array();
        $num=0;
        $map[]=['id','in',$deladminuserid];
        $res=DB::name('adminuser')->where($map)->delete();
        if($res){
            return jsondata('200','删除管理员成功');
        }else{
            return jsondata('400','删除管理员失败,请重试');
        }
    }

    //启用/禁用管理员
    public function admin_openorclose($adminuserid,$status){
        $statusname=['1'=>'启用','2'=>'禁用'];
        $adminuserid=intval($adminuserid);
        if(!in_array($status,array(1,2))){
            return jsondata('400','请选择需要启用或者禁用的管理员');
        }
        if($adminuserid<=0){
            return jsondata('400','请选择需要'.$statusname[$status].'的管理员');
        }
        $map[]=['id','=',$adminuserid];
        $info=DB::name('adminuser')->where($map)->find();
        if(empty($info)){
            return jsondata('400','请选择需要'.$statusname[$status].'的管理员');
        }
        if($info['status']==$status){
            return jsondata('400','当前管理员已是'.$statusname[$status].'状态无需重复'.$statusname[$status]);
        }
        $update_data['status']=$status;
        $update_data['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('adminuser')->where($map)->update($update_data);
        if($res){
            return jsondata('200',$statusname[$status].'管理员成功');
        }else{
            return jsondata('400',$statusname[$status].'管理员失败,请重试');
        }
    }

    //分配权限 adminuserid管理员id  ruleid权限id,数组格式 role_ruleids角色权限本身拥有的权限
    public function admin_assign($adminuserid,$ruleid,$havepermission,$role_ruleids,$issuperadmin=0){
        $adminuserid=intval($adminuserid);
        if($adminuserid<=0){
            return jsondata('400','请选择分配权限的管理员');
        }
        if(empty($ruleid)){
            return jsondata('400','请选择权限,刷新页面再试');
        }
        $real_ruleid=[];
        foreach($ruleid as $v){
            $map=[];
            $map[]=['id','=',intval($v)];
            if($issuperadmin!=1){
                $map[]=['id','in',$havepermission];
            }
            $map[]=['id','not in',$role_ruleids];
            $hasrule=DB::name('rule')->where($map)->find();
            if(!empty($hasrule)){
                $real_ruleid[]=$hasrule['id'];
            }
        }
        if(empty($real_ruleid)){
            return jsondata('400','请选择权限,刷新页面再试');
        }
        $map=[];
        $map[]=['id','=',$adminuserid];
        $update_data['ruleid']=implode(',',$real_ruleid);
        $update_data['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('adminuser')->where($map)->update($update_data);
        if($res){
            return jsondata('200','分配管理员权限成功');
        }else{
            return jsondata('400','分配管理员权限失败,请重试');
        }
    }

    //取消权限
    public function admin_cancelassign($adminuserid){
        $adminuserid=intval($adminuserid);
        if($adminuserid<=0){
            return jsondata('400','请选择需要取消权限的管理员');
        }
        $map=[];
        $map[]=['id','=',$adminuserid];
        $info=DB::name('adminuser')->where($map)->find();
        if(empty($info)){
            return jsondata('400','选择的角色不存在');
        }
        $update_data['ruleid']='';
        $update_data['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('adminuser')->where($map)->update($update_data);
        if($res){
            return jsondata('200','取消管理员权限成功');
        }else{
            return jsondata('400','取消管理员权限失败,请重试');
        }
    }

    //根据类型列表获取不同的表数据
    public function admin_gettypelist($type){
        $listdata=[];
        if($type==0){
            $listdata=array(array('id'=>0,'title'=>'平台'));
        }elseif($type==1){
            $map=[];
            $map[]=['collect_status','=',1];
            $list=DB::name('collectpoint')->where($map)->select();
            if(!empty($list)){
                foreach($list as $v){
                    $listdata[]=array('id'=>$v['id'],'title'=>$v['collect_title']);
                }
            }
        }elseif($type==2){
            $map=[];
            $map[]=['trans_status','=',1];
            $list=DB::name('transportcom')->where($map)->select();
            if(!empty($list)){
                foreach($list as $v){
                    $listdata[]=array('id'=>$v['id'],'title'=>$v['trans_title']);
                }
            }
            $data['list']=$listdata;
        }elseif($type==3){
            $map=[];
            $map[]=['declare_status','=',1];
            $list=DB::name('declarecom')->where($map)->select();
            if(!empty($list)){
                foreach($list as $v){
                    $listdata[]=array('id'=>$v['id'],'title'=>$v['declare_title']);
                }
            }
        }elseif($type==4){
            $map=[];
            $map[]=['port_status','=',1];
            $list=DB::name('seaport')->where($map)->select();
            if(!empty($list)){
                foreach($list as $v){
                    $listdata[]=array('id'=>$v['id'],'title'=>$v['port_title']);
                }
            }
        }
        return $listdata;
    }

    //获取角色列表
    public function admin_rolelist($type=0,$typeid=0){
        $listdata=[];
        if($type>=0 && $typeid>=0){
            $map=[];
            $map[]=['role_status','=',1];
            $map[]=['role_type','=',$type];
            $map[]=['role_typeid','=',$typeid];
            $list=DB::name('adminrole')->where($map)->select();
            if(!empty($list)){
                foreach($list as $v){
                    $listdata[]=array('id'=>$v['id'],'title'=>$v['role_title']);
                }
            }
        }
        return $listdata;
    }

    //获取类型名称
    public function admin_gettypename($type){
        return isset(config('admintype')[$type])?config('admintype')[$type]:'';
    }

    //获取对应类型的名称
    public function admin_getdetailname($type,$typeid){
        $detailname='';
        if($type==0){
            $detailname=config('admintype')[$type];
        }elseif($type==1){
            $map=[];
            $map[]=['id','=',$typeid];
            $info=DB::name('collectpoint')->where($map)->find();
            if(!empty($info)){
                $detailname=$info['collect_title'];
            }
        }elseif($type==2){
            $map=[];
            $map[]=['id','=',$typeid];
            $info=DB::name('transportcom')->where($map)->find();
            if(!empty($info)){
                $detailname=$info['trans_title'];
            }
        }elseif($type==3){
            $map=[];
            $map[]=['id','=',$typeid];
            $info=DB::name('declarecom')->where($map)->find();
            if(!empty($info)){
                $detailname=$info['declare_title'];
            }
        }elseif($type==4){
            $map=[];
            $map[]=['id','=',$typeid];
            $info=DB::name('seaport')->where($map)->find();
            if(!empty($info)){
                $detailname=$info['port_title'];
            }
        }
        return $detailname;
    }

    //获取角色名称
    public function admin_getrolename($roleid){
        $rolename='';
        $map=[];
        $map[]=['id','=',$roleid];
        $info=DB::name('adminrole')->where($map)->find();
        if(!empty($info)){
            $rolename=$info['role_title'];
        }
        return $rolename;
    }

    //获取管理员权限
    public function admin_getruleid($adminuserid){
        $ruleids=[];
        $map=[];
        $map[]=['id','=',$adminuserid];
        $admininfo=DB::name('adminuser')->where($map)->find();
        if(empty($admininfo)){
            return $ruleids;
        }
        if($admininfo['issuperadmin']==1){
            $tmprule=DB::name('rule')->select();
            foreach($tmprule as $v){
                $ruleids[]=$v['id'];
            }
        }else{
            $map=[];
            $map[]=['id','=',$admininfo['roleid']];
            $roleinfo=DB::name('adminrole')->where($map)->find();
            if(empty($roleinfo)){
                return $ruleids;
            }
            $tmpruleid=explode(',',$roleinfo['role_ruleid']);
            $adminruleid=explode(',',$admininfo['ruleid']);
            $ruleids=array_unique(array_merge($tmpruleid,$adminruleid));
        }
        return $ruleids;
    }

    //获取左边菜单
    public function admin_getleftmenu($adminuserid){
        $ruleids=$this->admin_getruleid($adminuserid);
        $map=[];
        $map[]=['id','in',$ruleids];
        $map[]=['parentid','=',0];
        $map[]=['rule_ismenu','=',1];
        $map[]=['rule_isshow','=',1];
        $field='id,rule_module,rule_controller,rule_action,rule_title,rule_class';
        $menu=DB::name('rule')->field($field)->where($map)->order(['rule_sort'=>'desc','id'=>'asc'])->select();
        if(!empty($menu)){
            foreach($menu as &$v){
                $map=[];
                $map[]=['id','in',$ruleids];
                $map[]=['parentid','=',$v['id']];
                $map[]=['rule_ismenu','=',1];
                $map[]=['rule_isshow','=',1];
                if($v['rule_controller']=='#' || $v['rule_action']=='#'){
                    $v['rule_url']='javascript:void(0);';
                }else{
                    $v['rule_url']=url($v['rule_module'].'/'.$v['rule_controller'].'/'.$v['rule_action']);
                }
                $v['item']=DB::name('rule')->field($field)->where($map)->order(['rule_sort'=>'desc','id'=>'asc'])->select();
                foreach($v['item'] as &$v2){
                    if($v2['rule_controller']=='#' && $v2['rule_action']=='#'){
                        $v2['rule_url']='javascript:void(0);';
                    }else{
                        $v2['rule_url']=url($v2['rule_module'].'/'.$v2['rule_controller'].'/'.$v2['rule_action']);
                    }
                }
            }
        }
        return $menu;
    }

    //通过控制器和方法获取权限id
    public function admin_getrulefromaction($module,$controller,$action){
        $ruleid=0;
        $map=[];
        $map[]=['rule_module','=',$module];
        $map[]=['rule_controller','=',$controller];
        $map[]=['rule_action','=',$action];
        $ruleinfo=DB::name('rule')->field('id')->where($map)->find();
        if(!empty($ruleinfo)){
            $ruleid=$ruleinfo['id'];
        }
        return $ruleid;
    }

    //根据管理员id获取对应角色和类型信息
    public function admin_gettypeandrole($adminuserid){
        $info=[];
        $map=[];
        $map[]=['id','=',$adminuserid];
        $admininfo=DB::name('adminuser')->where($map)->find();
        if(empty($admininfo)){
            return $info;
        }
        $map=[];
        $map[]=['id','=',$admininfo['roleid']];
        $roleinfo=DB::name('adminrole')->where($map)->find();
        if(!empty($roleinfo)){
            $info['roleinfo']=$roleinfo;
        }
        // if($admininfo['admintype']==0){
        //     $typeinfo['status']=1;
        //     $typeinfo['id']=0;
        //     $typeinfo['title']=config('admintype')[$admininfo['admintype']];
        // }else{
        //     if($admininfo['admintype']==1){
        //         $tablename='collectpoint';
        //         $field='id,collect_status as status,collect_title as title';
        //     }elseif($type==2){
        //         $tablename='transportcom';
        //         $field='id,trans_status as status,trans_title as title';
        //     }elseif($type==3){
        //         $tablename='declarecom';
        //         $field='id,declare_status as status,declare_title as title';
        //     }elseif($type==4){
        //         $tablename='seaport';
        //         $field='id,port_status as status,port_title as title';
        //     }
        //     $map=[];
        //     $map[]=['id','=',$admininfo['admintypeid']];
        //     $typeinfo=DB::name($tablename)->where($map)->find();
        // }
        //$info['typeinfo']=$typeinfo;
        return $info;
    }

}
