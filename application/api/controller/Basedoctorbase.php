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
use app\api\service\BasedoctorService;

class Basedoctorbase extends Controller{
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
        if($userinfo['roletype']!=3){
            exitdata('0019','你还不是基层医生');
        }
        $bmap=[];
        $bmap[]=['isdel','=',2];
        $bmap[]=['status','=',1];
        $bmap[]=['id','=',$userinfo['roleid']];
        $gservice=new GenericService();
        $basedoctorinfo=$gservice->basedoctorDetail($bmap);
        if(empty($basedoctorinfo)){
            exitdata('0019','你还不是基层医生');
        }
        $userinfo['basedoctorinfo']=$basedoctorinfo;
        $this->base_userinfo=$userinfo;
        $this->token=$token;
        $this->weburl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
    }
}