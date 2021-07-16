<?php
namespace app\api\controller;
use think\Controller;
use think\facade\Request;
use think\Db;
use think\db\Query;
use think\facade\Session;
use app\api\service\UserService;

class Userbase extends Controller{
    public $base_userinfo=array();
    public $weburl;
    public $language;
    public function initialize(){
        $request=request();
        $mod=strtolower($request->module());
        $con=strtolower($request->controller());
        $act=strtolower($request->action());
        $userinfo=[];
        $token=input('token','','trim');
        if($token==''){
            $token=Request::header('token');
        }
        if(!in_array($act,['getminiopenid','minilogin','updateinfo','checklogin','getwxcode'])){
            if($token==''){
                exitdata('0019','请登录');
            }
            $map=[];
            $map[]=['token','=',$token];
            $service=new UserService();
            $userinfo=$service->getUserInfo($map);
            if(empty($userinfo)){
                exitdata('0019','请登录');
            }
        }
        $this->base_userinfo=$userinfo;
        $this->token=$token;
        $this->weburl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
    }
}