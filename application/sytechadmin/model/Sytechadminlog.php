<?php
namespace app\sytechadmin\model;
use think\Db;
use think\Model;
class Sytechadminlog extends Model{
    protected $table="admin_log";

    //添加日志
    public function adminlog_add($admin_id,$content=''){
        $data['admin_id']=$admin_id;
        $data['ip']=request()->ip();
        $data['content']=$content;
        $data['create_time']=date('Y-m-d H:i:s');
        DB::name($this->table)->insert($data);
    }


}
