<?php
namespace app\sytechadmin\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\sytechadmin\controller\Upload;

//用户管理
class UserService extends Base{
    //field:查询字段 map:查询条件 search:搜索条件显示在分页链接 orderby:排序 pernum:每页多少条 type:获取数据类别 1获取分页 2获取全部
    //用户列表
    public function getUserList($type=1,$map=[],$field='*',$search=[],$pernum=20,$orderby=['id'=>'asc']){
        $list=array();
        $page='';
        $count=0;
        if($type==1){
            $list=DB::name('user')->field($field)->where($map)->order($orderby)->paginate($pernum,false,['query'=>$search])->each(function($item,$key){
                return $item;
            });
            $page=$list->render();
            $count=$list->total();
        }else{
            $list=DB::name('user')->field($field)->where($map)->order($orderby)->select();
            $count=count($list);
        }
        $data['list']=$list;
        $data['page']=$page;
        $data['count']=$count;
        return $data;
    }

    //用户数据校验
    public function user_verify($id=0){
        $id=intval($id);
        $realname=input('post.realname','','trim');
        $mobile=input('post.mobile','','trim');
        $idcardnum=input('post.idcardnum','','trim');
        $department=input('post.department','','trim');
        $studentnumber=input('post.studentnumber','','trim');
        $address=input('post.address','','trim');
        if($realname==''){
            return jsondata('400','请输入用户姓名');
        }
        $checkmobileres=checkformat_mobile($mobile);
        if($checkmobileres['code']!='0001'){
            return jsondata('400',$checkmobileres['msg']);
        }
        $hasmap=[];
        $hasmap[]=['realname','=',$realname];
        $hasmap[]=['mobile','=',$mobile];
        $hasinfo=$this->userDetail($hasmap);
        if(!empty($hasinfo)){
            if($hasinfo['id']!=$id){
                return jsondata('400','已存在相同姓名和联系电话的用户');
            }
        }
        $info=[];
        if($id>0){
            $map=[];
            $map[]=['id','=',$id];
            $info=$this->userDetail($map);
        }
        $data=[
            'realname'=>$realname,
            'mobile'=>$mobile,
            'idcardnum'=>$idcardnum,
            'department'=>$department,
            'studentnumber'=>$studentnumber,
            'address'=>$address,
        ];
        $data['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('user')->where([['id','=',$info['id']]])->update($data);
        $opname='修改';
        if($res){
            $code='200';
            $msg=$opname.'成功';
        }else{
            $code='400';
            $msg=$opname.'失败,请重试';
        }
        return jsondata($code,$msg);
    }

    //用户详情
    public function userDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('user')->field($field)->where($map)->find();
    }

}
