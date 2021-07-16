<?php
namespace app\api\controller;
use think\Controller;
use think\facade\Request;
use think\Db;
use think\db\Query;
use think\facade\Session;
use app\api\service\BodycheckService;

class Bodycheckbase extends Controller{
    public $base_userinfo=array();
    public $base_meetinguser=[];
    public $weburl;
    public function initialize(){
        $request=request();
        $mod=strtolower($request->module());
        $con=strtolower($request->controller());
        $act=strtolower($request->action());

        $token=input('token','','trim');
        if($token==''){
            exitdata('0808','请登录');
        }
        $map[]=['logged','=',$token];
        $map[]=['deleted_at','=',NULL];
        $field='*';
        $userinfo=DB::name('wechat_user')->field($field)->where($map)->find();
        if(empty($userinfo)){
            exitdata('0909','用户不存在');
        }
        $meetinguserinfo=[];
        $bodyservice=new BodycheckService();
        $meetinguserinfo=$bodyservice->info4openid($userinfo['openid']);
        if(empty($meetinguserinfo)){
            exitdata('0707','请先完善个人信息');
        }
        if($meetinguserinfo['name']=='' || $meetinguserinfo['phone']==''){
            exitdata('0707','请先完善个人信息');
        }

        $this->base_userinfo=$meetinguserinfo;
        //$this->base_meetinguser=$meetinguserinfo;
        $this->token=$token;
        $this->weburl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
    }
}