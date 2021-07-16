<?php
namespace app\index\controller;
use think\facade\Request;
use think\Controller;
use think\Db;
use think\db\Query;
use app\common\model\Querytrack;
class Error extends Controller{
    public function _empty(){
        return json(['code'=>'400','msg'=>'网络错误']);
    }
}
