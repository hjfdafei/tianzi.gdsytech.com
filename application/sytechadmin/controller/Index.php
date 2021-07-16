<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\controller\Upload;
use app\sytechadmin\service\AdminuserService;
use app\sytechadmin\model\Rule;
use app\sytechadmin\service\AssistantsService;
use app\sytechadmin\service\BasedoctorsService;
use app\sytechadmin\service\UserService;
class Index extends Sytechadminbase{
    public function index(){
        $adminuserservice=new AdminuserService();
        $menu=$adminuserservice->admin_getleftmenu($this->base_admininfo['id']);
        //ptr($menu);
        $page_title=config('app_name');
        $this->assign(['menu'=>$menu,'page_title'=>$page_title]);
        return $this->fetch();
    }

    //欢迎页
    public function welcome(){
        $this->assign('info',$this->base_admininfo);
        $week=array('日','一','二','三','四','五','六');
        $weektime='星期'.$week[date('w')];
        $currenttime=date('Y/n/d H:i:s');
        $this->assign('currenttime',$currenttime);
        $this->assign('weektime',$weektime);
        return $this->fetch();
    }

    //清除缓存
    public function clear(){
        if (delete_dir_file(CACHE_PATH) || delete_dir_file(TEMP_PATH)) {
            return jsondata('200','清除缓存成功');
        } else {
            return jsondata('400','清除缓存失败');
        }
    }

    //系统设置
    public function setting(){
        $path='../config/webconfig.json';
        if(!file_exists($path)){
            $data['content']='';
            file_put_contents($path,json_encode($data,256));
        }
        $webconfig=json_decode(file_get_contents($path),true);
        if(request()->isPost() || request()->isAjax()){
            $content=input('post.content','','trim');
            if($content==''){
                return jsondata('400','请填写办理条款');
            }
            $data['content']=$content;
            file_put_contents($path,json_encode($data,256));
            return jsondata('200','设置成功');
        }
        $this->assign(['webconfig'=>$webconfig]);
        return $this->fetch();

    }

    //获取地区
    public function getarea(){
        $parentid=input('parentid')==''?'000000':input('parentid');
        $map['parent_code']=$parentid;
        $field='code,name';
        $list=DB::name('region')->field($field)->where($map)->select();
        if(empty($list)){
            return jsondata('400','暂无数据');
        }else{
            $data['data']=$list;
            return jsondata('200','获取成功',$data);
        }
    }

    //修改个人信息
    public function admininfo_update(){
        if(request()->isPost() || request()->isAjax()){
            $username=input('post.username','','trim');
            $userpassword=input('post.userpassword','','trim');
            $mobile=input('post.mobile','','trim');
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
            $data['mobile']=$mobile;
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

    //ajax获取类型
    public function gettype(){
        $type=input('post.type','','intval');
        $adminuserservice=new AdminuserService();
        $listdata=$adminuserservice->admin_gettypelist($type);
        if(empty($listdata)){
            return jsondata('400','暂无数据');
        }
        $data['list']=$listdata;
        return jsondata('200','获取成功',$data);
    }

    //ajax获取角色
    public function getrole(){
        $type=input('post.type','0','intval');
        $typeid=input('post.typeid','0','intval');
        $adminuserservice=new AdminuserService();
        $listdata=$adminuserservice->admin_rolelist($type,$typeid);
        if(empty($listdata)){
            return jsondata('400','暂无数据');
        }
        $data['list']=$listdata;
        return jsondata('200','获取成功',$data);
    }

    //获取类型角色列表
    public function getUserRoleList(){
        $type=input('post.type','1','intval');
        $aservice=new AssistantsService();
        $bservice=new BasedoctorsService();
        $uservice=new UserService();
        $rolelist=[['id'=>0,'title'=>'无']];
        if($type==2){
            $rolelist=[];
            $userid=[];
            $umap=[];
            $umap[]=['roletype','=',2];
            $userlist=$uservice->getUserList(2,$umap,'*',[],20)['list'];
            if(!empty($userlist)){
                foreach($userlist as $v){
                    $userid[]=$v['roleid'];
                }
                $userid=array_unique($userid);
            }
            $amap=[];
            $amap[]=['isdel','=',2];
            if(!empty($userid)){
                $amap[]=['id','not in',$userid];
            }
            $rolelist=$aservice->getAssistantsList(2,$amap,'id,realname as title',[],20)['list'];
        }elseif($type==3){
            $rolelist=[];
            $userid=[];
            $umap=[];
            $umap[]=['roletype','=',3];
            $userlist=$uservice->getUserList(2,$umap,'*',[],20)['list'];
            if(!empty($userlist)){
                foreach($userlist as $v){
                    $userid[]=$v['roleid'];
                }
                $userid=array_unique($userid);
            }
            $bmap=[];
            $bmap[]=['isdel','=',2];
            if(!empty($userid)){
                $bmap[]=['id','not in',$userid];
            }
            $rolelist=$bservice->getBasedoctorsList(2,$bmap,'id,realname as title',[],20)['list'];
        }
        $data['list']=$rolelist;
        return jsondata('200','获取成功',$data);
    }

    //查询新订单和新客服消息
    public function chekcnums(){
        if(session('allnum')==NULL){
            session('allnum',0);
        }
        if(session('ordernum')==NULL){
            session('ordernum',0);
        }
        if(session('chatnum')==NULL){
            session('chatnum',0);
        }
        if(session('old_allnum')==NULL){
            session('old_allnum',0);
        }
        if(session('old_ordernum')==NULL){
            session('old_ordernum',0);
        }
        if(session('old_chatnum')==NULL){
            session('old_chatnum',0);
        }
        $ordernum=DB::name('orders')->count();
        $chatnum=DB::name('user_chat')->where([['type','=',1]])->count();
        if($ordernum>session('ordernum')){
            session('old_ordernum',session('ordernum'));
            session('ordernum',$ordernum);
        }
        if($chatnum>session('chatnum')){
            session('old_chatnum',session('chatnum'));
            session('chatnum',$chatnum);
        }
        // echo session('ordernum').'<br/>';
        // echo session('chatnum').'<br/>';
        // echo session('old_ordernum').'<br/>';
        // echo session('old_chatnum').'<br/>';
        $allnum=session('ordernum')+session('chatnum')-session('old_ordernum')-session('old_chatnum');
        session('allnum',$allnum);
        $data['data']=['allnum'=>session('allnum'),'ordernum'=>session('ordernum')-session('old_ordernum'),'chatnum'=>session('chatnum')-session('old_chatnum')];
        return jsondata('200','获取成功',$data);
    }

}
