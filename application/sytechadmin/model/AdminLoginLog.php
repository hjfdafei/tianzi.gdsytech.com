<?php
namespace app\sytechadmin\model;
use think\Model;

class AdminLoginLog extends Model{
    protected $pk='admin_id';
    protected $insert=['create_time','ip','admin_id'];
    protected function setCreateTimeAttr(){
        return date('Y-m-d H:i:s');
    }

    protected function setIpAttr(){
        return request()->ip(1);
    }

    protected function setAdminIdAttr(){
        return session('admininfo.id');
    }
}