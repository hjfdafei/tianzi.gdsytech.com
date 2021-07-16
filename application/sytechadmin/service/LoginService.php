<?php
namespace app\sytechadmin\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\sytechadmin\model\AdminLoginLog;
use app\sytechadmin\model\Adminuser;
use app\sytechadmin\service\AdminuserService;

class LoginService extends Base{
    //登录验证
    public function login_verify(){
        $username=input('post.username','','trim');
        $userpassword=input('post.userpassword','','trim');
        if($username==''){
            return jsondata('400','请输入登录用户名');
        }
        if($userpassword==''){
            return jsondata('400','请输入登录密码');
        }
        $userpassword=md5($userpassword);
        $map['username']=$username;
        $map['password']=$userpassword;
        $admininfo=Db::name('adminuser')->where($map)->order('id','desc')->find();
        if(empty($admininfo)){
            return jsondata('400','账号或者密码错误');
        }
        if($admininfo['status']!=1){
            return jsondata('400','账号已被禁用,请联系管理员');
        }
        $adminuserservice=new AdminuserService();
        if($admininfo['issuperadmin']!=1){
            $ainfos=$adminuserservice->admin_gettypeandrole($admininfo['id']);
            if($ainfos['roleinfo']['role_status']!=1){
                return jsondata('400','你所属的角色'.$ainfos['roleinfo']['role_title'].'已被禁用,请联系管理员');
            }
            // if($ainfos['typeinfo']['status']!=1){
            //     return jsondata('400','你所属'.$ainfos['typeinfo']['title'].'已被关闭,请联系管理员');
            // }
        }
        session('admininfo',$admininfo);
        $adminuserservice->admin_getruleid($admininfo['id']);
        $adminloginlog=new AdminLoginLog();
        $adminloginlog->save();
        $adminuser=new Adminuser();
        $info=adminuser::get($admininfo['id']);
        $info->loginip=request()->ip();
        $info->logintime=date('Y-m-d H:i:s');
        $info->update_time=date('Y-m-d H:i:s');
        $info->save();
        $outdata['url']=url('Sytechadmin/Index/index');
        return jsondata('200','登录成功',$outdata);
    }
}
