<?php
namespace app\api\controller;
use think\Controller;
use think\facade\Request;
use think\Db;
use think\db\Query;
use think\facade\Session;

class Base extends Controller{
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
        $language=input('language','1','intval');
        $this->language=$language;
        $this->weburl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
    }
}