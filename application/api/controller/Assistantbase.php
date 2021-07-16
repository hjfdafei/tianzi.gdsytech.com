<?php
namespace app\api\controller;
use think\Controller;
use think\facade\Request;
use think\Db;
use think\db\Query;
use think\facade\Session;
use app\api\service\UserService;
use app\api\service\AssistantService;
use app\api\service\GenericService;

class Assistantbase extends Controller{
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
        if($userinfo['roletype']!=2){
            exitdata('0019','你还不是名医助理');
        }
        $amap=[];
        $amap[]=['status','=',1];
        $amap[]=['id','=',$userinfo['roleid']];
        $amap[]=['isdel','=',2];
        $gservice=new GenericService();
        $assistantinfo=$gservice->assistantDetail($amap);
        if(empty($assistantinfo)){
            exitdata('0019','你还不是名医助理');
        }
        $userinfo['assistantinfo']=$assistantinfo;
        $this->base_userinfo=$userinfo;
        $this->token=$token;
        $this->weburl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
    }
}