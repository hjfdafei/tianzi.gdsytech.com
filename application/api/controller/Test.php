<?php
namespace app\api\controller;
use think\Controller;
use think\facade\Request;
use think\Db;
use think\db\Query;
use think\facade\Session;
use app\api\service\UserService;

class Test extends Controller{
    public function index(){
        $openid='o8mUb5m1-iHpB5gQApxJV2jg_RT0';
        $orders_id='10';
        $patient_name='大飞';
        $doctor_name='张医生';
        $orders_time='2021-02-05 16:01:38';
        $orderno='202101251607067645';
        send_paytpl($openid,$orders_id,$patient_name,$doctor_name,$orders_time,$orderno);
        // session('fronturl','http://web.whyl.gdsytech.com');
        // session('webtoken','fbdeebf520e60e3044d722611d61e29d');
        // $url="http://web.whyl.gdsytech.com/Api/Test/test?a=1&invite_code=123456&gg=";
        // ptr(parse_url($url));
        // $urlparam=parse_url($url);
        // $urldata=[];
        // if(isset($urlparam['query'])){
        //     $urlquery=explode('&',trim($urlparam['query'],'&'));
        //     if(!empty($urlquery)){
        //         foreach($urlquery as $uv){
        //             $uvarr=explode('=',$uv);
        //             if(isset($uvarr[0]) && isset($uvarr[1])){
        //                 $urldata[$uvarr[0]]=$uvarr[1];
        //             }
        //         }
        //     }
        // }
        // header("Location: http://web.whyl.gdsytech.com/Api/Test/test?a=1");
        // $this->redirect("http://web.whyl.gdsytech.com/Api/Test/test?a=1");
    }

    public function test(){
        $a=input('a','ff','trim');
        echo $a;
        echo 'ghg';
    }
}