<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\facade\Request;
use think\Db;
use think\db\Query;
use think\facade\Session;
use app\sytechadmin\model\Rule;
use app\sytechadmin\service\AdminuserService;

class Sytechadminbase extends Controller{
    public $base_admininfo=array();
    public $weburl;
    public function initialize(){
        $this->islogin();
        $admininfo=DB::name('adminuser')->where(['id'=>session('admininfo')['id']])->find();
        $this->assign('admininfo',$admininfo);
        $this->base_admininfo=$admininfo;
        $request=request();
        $mod=strtolower($request->module());
        $con=strtolower($request->controller());
        $act=strtolower($request->action());
        if(!config('isopen_auth')) {
            return;
        }
        if(in_array($con,['index','upload'])){
            return ;
        }
        // if($con=='index') {
        //     return;
        // }
        if($admininfo['issuperadmin']!=1){
            $adminuserservice=new AdminuserService();
            $ainfos=$adminuserservice->admin_gettypeandrole($admininfo['id']);
            if($ainfos['roleinfo']['role_status']!=1){
                exitdata('400','你所属的角色'.$ainfos['roleinfo']['role_title'].'已被禁用,请联系管理员');
            }
            if(!in_array($adminuserservice->admin_getrulefromaction($mod,$con,$act),$adminuserservice->admin_getruleid($admininfo['id']))){
                exitdata('403','暂无权限操作');
            }
        }
        $this->weburl=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
    }

    //判断商户是否登录
    public function islogin(){
        if(is_null(session('admininfo'))){
            $this->redirect('Sytechadmin/Login/login');
            //$this->error('您尚未登陆！',url('Sytechadmin/Login/login'),'',3);
            return ;
        }else{
            $info=DB::name('adminuser')->where(['id'=>session('admininfo')['id']])->find();
            if(empty($info)){
                $this->redirect('Sytechadmin/Login/login');
                //$this->error('您尚未登陆！',url('Sytechadmin/Login/login'),'',3);
            }
            if($info['status']!=1){
                $this->error('账号已被禁用,请联系管理员!',url('Sytechadmin/Login/login'),'',3);
            }
        }
    }
}
