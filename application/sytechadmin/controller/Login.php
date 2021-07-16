<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\sytechadmin\service\LoginService;
use app\sytechadmin\model\Sytechadminlog;
class Login extends Controller{
    //登录
    public function login(){
        if(request()->isAjax() || request()->isPost()){
            $loginservice=new LoginService();
            $res=$loginservice->login_verify();
        }else{
            $page_title=config('app_name');
            $this->assign(['page_title'=>$page_title]);
            return view('login');
        }
    }

    //退出
    public function logout(){
        session('admininfos',null);
        session('adminmenus',null);
        if(empty(session('admininfos'))){
            return jsondata('200','退出成功');
        }else{
            return jsondata('400','退出失败');
        }
    }
}
