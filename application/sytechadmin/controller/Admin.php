<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\controller\Upload;
class Admin extends Sytechadminbase{
    //管理员列表
    public function index(){
        if($this->base_admininfo['roleid']!=1){
            exitdata('400','无权限操作');
        }
        $map=[];
        $map[]=['a.id','neq',1];
        $keyword=input('keyword','','trim');
        $search['keyword']=$keyword;
        if($keyword!=''){
            $map[]=['a.username','like',"%$keyword%"];
        }
        $field='a.*,ar.area_name';
        $list=DB::name('adminuser a')->field($field)->join('__AREA__ ar','ar.id=a.areaid','left')->where($map)->paginate(20,false,['query'=>$search]);
        $page=$list->render();
        $count=$list->total();
        $this->assign(['count'=>$count,'page'=>$page,'list'=>$list,'search'=>$search]);
        return $this->fetch();
    }

    //添加/修改管理员信息
    public function admin_add(){
        if($this->base_admininfo['roleid']!=1){
            exitdata('400','无权限操作');
        }
        if(request()->isPost() || request()->isAjax()){
            $areaid=input('post.areaid','0','intval');
            $username=input('post.username','','trim');
            $userpassword=input('post.userpassword','','trim');
            $status=input('post.status','1','intval');
            $adminid=input('post.adminid','0','intval');
            if($areaid<=0){
                return jsondata('400','请选择受理点');
            }
            if($username==''){
                return jsondata('400','请输入管理员用户名');
            }
            if($adminid<=0){
                if($userpassword==''){
                    return jsondata('400','请输入管理员登录密码');
                }
                $data['password']=md5(md5($userpassword));
            }
            if($userpassword!=''){
                $data['password']=md5(md5($userpassword));
            }
            $map[]=['id','=',$areaid];
            $map[]=['area_status','=',1];
            $areainfo=DB::name('area')->where($map)->find();
            if(empty($areainfo)){
                return jsondata('400','选择的受理点信息不存在,请联系管理员');
            }
            $map=[];
            $map[]=['username','=',$username];
            $hasadmin=DB::name('adminuser')->where($map)->find();
            if(!empty($hasadmin)){
                if($hasadmin['id']!=$adminid){
                    return jsondata('400','管理员用户名已存在');
                }
            }
            $data['username']=$username;
            $data['status']=$status;
            $data['areaid']=$areaid;
            if($adminid>0){
                $admininfo=DB::name('adminuser')->where('id','=',$adminid)->find();
                if(empty($admininfo)){
                    return jsondata('400','管理员信息不存在');
                }
                $data['update_time']=date('Y-m-d H:i:s');
                $res=DB::name('adminuser')->where('id','=',$admininfo['id'])->update($data);
            }else{
                $data['create_time']=date('Y-m-d H:i:s');
                $res=DB::name('adminuser')->insert($data);
            }
            if($res){
                return jsondata('200','数据保存成功');
            }else{
                return jsondata('400','数据保存失败,请重试');
            }
        }
        $adminid=input('adminid','0','intval');
        $info['id']=0;
        $info['status']=1;
        $info['username']='';
        $info['areaid']=0;
        if($adminid>0){
            $map=array();
            $map[]=['id','=',$adminid];
            $info=DB::name('adminuser')->where($map)->find();
        }
        $map=[];
        $map[]=['area_status','=',1];
        $arealist=DB::name('area')->field('id,area_name')->where($map)->select();
        $this->assign('arealist',$arealist);
        $this->assign('info',$info);
        return $this->fetch();
    }

    //修改管理员状态
    public function admin_open(){
        if($this->base_admininfo['roleid']!=1){
            exitdata('400','无权限操作');
        }
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $adminid=input('post.adminid','0','intval');
            $status=input('post.status','1','intval');
            if($adminid<=0){
                return jsondata('400','请选择管理员');
            }
            $map=[];
            $map[]=['id','=',$adminid];
            $admininfo=DB::name('adminuser')->where($map)->find();
            if(empty($admininfo)){
                return jsondata('400','选择管理员的信息不存在');
            }
            if($admininfo['status']==$status){
                return jsondata('400','管理员状态改变失败,请重试');
            }
            $data['status']=$status;
            $data['update_time']=date('Y-m-d H:i:s');
            $res=DB::name('adminuser')->where($map)->update($data);
            if($res){
                return jsondata('200','数据保存成功');
            }else{
                return jsondata('400','数据保存失败,请重试');
            }
        }
        return jsondata('400','网络错误');
    }

    //删除管理员信息
    public function admin_del(){
        if($this->base_admininfo['roleid']!=1){
            return jsondata('400','不允许操作');
        }
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $adminid=input('post.adminid','','trim');
            if($adminid==''){
                return jsondata('400','请选择要删除的管理员');
            }
            $adminid=explode(',',trim($adminid,','));
            $deladminid=array();
            foreach($adminid as $v){
                $map['id']=intval($v);
                $admininfo=DB::name('adminuser')->where($map)->find();
                if(!empty($admininfo)){
                    $deladminid[]=$admininfo['id'];
                }
            }
            if(empty($deladminid)){
                return jsondata('400','请选择要删除的管理员');
            }
            Db::startTrans();
            $map=array();
            $num=0;
            foreach($deladminid as $v){
                $map['id']=intval($v);
                $res=DB::name('adminuser')->where($map)->delete();
                if($res){
                    $num++;
                }
            }
            if($num>0){
                Db::commit();
                return jsondata('200','删除管理员成功');
            }else{
                Db::rollback();
                return jsondata('400','删除管理员失败,请重试');
            }
        }
        return jsondata('400','网络错误');
    }

    //资料更新
    public function admininfo_update(){
        if(request()->isPost() || request()->isAjax()){
            $username=input('post.username','','trim');
            $userpassword=input('post.userpassword','','trim');
            if($username==''){
                return jsondata('400','请输入管理员用户名');
            }
            if($userpassword!=''){
                $data['password']=md5(md5($userpassword));
            }
            $adminid=$this->base_admininfo['id'];
            $map=[];
            $map[]=['username','=',$username];
            $hasadmin=DB::name('adminuser')->where($map)->find();
            if(!empty($hasadmin)){
                if($hasadmin['id']!=$adminid){
                    return jsondata('400','管理员用户名已存在');
                }
            }
            $data['username']=$username;
            if($adminid>0){
                $admininfo=DB::name('adminuser')->where('id','=',$adminid)->find();
                if(empty($admininfo)){
                    return jsondata('400','管理员信息不存在');
                }
                $data['update_time']=date('Y-m-d H:i:s');
                $res=DB::name('adminuser')->where('id','=',$admininfo['id'])->update($data);
            }else{
                $data['create_time']=date('Y-m-d H:i:s');
                $res=DB::name('adminuser')->insert($data);
            }
            if($res){
                return jsondata('200','数据保存成功');
            }else{
                return jsondata('400','数据保存失败,请重试');
            }
        }
        $this->assign('info',$this->base_admininfo);
        return $this->fetch();
    }

}
