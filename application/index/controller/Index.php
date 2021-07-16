<?php
namespace app\index\controller;
use think\Controller;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\index\model\Indexbase;
class Index extends Controller{
    //首页
    public function index(){
        return $this->fetch();
    }

    public function testpay(){
        return $this->fetch();
    }

    public function testshare(){
        return $this->fetch();
    }


}
