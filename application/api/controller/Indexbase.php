<?php
namespace app\api\controller;
use think\Controller;
use think\facade\Request;
use think\Db;
use think\db\Query;
use think\facade\Session;
use app\api\service\IndexService;

class Indexbase extends Controller{
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
        if(!in_array($act,['improve_parentinfo','attach_upload'])){
            $indexservice=new IndexService();
            $meetinguserinfo=$indexservice->parentinfo4openid($userinfo['openid']);
            if(empty($meetinguserinfo)){
                exitdata('0707','请先完善家长信息');
            }
            if($meetinguserinfo['realname']=='' || $meetinguserinfo['idcardnum']=='' || $meetinguserinfo['mobile']=='' || $meetinguserinfo['address']==''){
                exitdata('0707','请先完善家长信息');
            }
            // if($meetinguserinfo['realname']=='' || $meetinguserinfo['idcardnum']=='' || $meetinguserinfo['mobile']=='' || $meetinguserinfo['address']=='' || $meetinguserinfo['proveimg_front']=='' || $meetinguserinfo['proveimg_back']=='' ){
            //     exitdata('0707','请先完善家长信息');
            // }
        }

        $this->base_userinfo=$userinfo;
        $this->base_meetinguser=$meetinguserinfo;
        $this->token=$token;
        $this->weburl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
    }
}