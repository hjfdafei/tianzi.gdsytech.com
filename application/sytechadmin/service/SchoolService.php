<?php
namespace app\sytechadmin\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\sytechadmin\controller\Upload;
//use app\sytechadmin\model\Banner;
//校区管理
class SchoolService extends Base{
    //field:查询字段 map:查询条件 search:搜索条件显示在分页链接 orderby:排序 pernum:每页多少条 type:获取数据类别 1获取分页 2获取全部
    //校区列表
    public function getSchoolList($type=1,$map=[],$field='*',$search=[],$pernum=20,$orderby=['id'=>'asc']){
        $list=array();
        $page='';
        $count=0;
        $statusname=['1'=>'启用','2'=>'禁用'];
        if($type==1){
            $list=DB::name('school')->field($field)->where($map)->order($orderby)->paginate($pernum,false,['query'=>$search])->each(function($item,$key) use($statusname){
                $item['statusname']=$statusname[$item['status']];
                return $item;
            });
            $page=$list->render();
            $count=$list->total();
        }else{
            $list=DB::name('school')->field($field)->where($map)->order($orderby)->select();
            $count=count($list);
        }
        $data['list']=$list;
        $data['page']=$page;
        $data['count']=$count;
        return $data;
    }

    //校区数据校验
    public function school_verify($id){
        $id=intval($id);
        $title=input('post.title','','trim');
        $address=input('post.address','','trim');
        $status=input('post.status','1','intval');
        $sortby=input('post.sortby','0','intval');
        if(!in_array($status,[1,2])){
            $status=1;
        }
        $info=[];
        if($id>0){
            $map=[];
            $map[]=['id','=',$id];
            $info=$this->schoolDetail($map);
        }
        $map=[];
        $map[]=['title','=',$title];
        $hasschool=$this->schoolDetail($map);
        if(!empty($hasschool)){
            if($hasschool['id']!=$id){
                return jsondata('400','校区名称已存在');
            }
        }
        $data=[
            'title'=>$title,
            'address'=>$address,
            'status'=>$status,
            'sortby'=>$sortby
        ];
        if(empty($info)){
            if($status==1){
                $data['enable_time']=date('Y-m-d H:i:s');
            }elseif($status==2){
                $data['unable_time']=date('Y-m-d H:i:s');
            }
            $data['create_time']=date('Y-m-d H:i:s');
            $res=DB::name('school')->insertGetId($data);
            $opname='新增';
        }else{
            if($status!=$info['status']){
                if($status==1){
                    $data['enable_time']=date('Y-m-d H:i:s');
                }elseif($status==2){
                    $data['unable_time']=date('Y-m-d H:i:s');
                }
            }
            $data['update_time']=date('Y-m-d H:i:s');
            $res=DB::name('school')->where([['id','=',$info['id']]])->update($data);
            $opname='修改';
        }
        if($res){
            $code='200';
            $msg=$opname.'成功';
        }else{
            $code='400';
            $msg=$opname.'失败,请重试';
        }
        return jsondata($code,$msg);
    }

    //校区详情
    public function schoolDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('school')->field($field)->where($map)->find();
    }

    //校区启用/禁用
    public function school_showhide($id,$status=1){
        $map=[];
        $map[]=['id','=',$id];
        $info=$this->schoolDetail($map);
        if(empty($info)){
            return jsondata('400','需要操作的校区不存在');
        }
        $statusname=['1'=>'启用','2'=>'禁用'];
        if($status==$info['status']){
            return jsondata('400',"已是".$statusname[$status]."状态,无需重复操作");
        }
        $updateData['status']=$status;
        if($status==1){
            $updateData['enable_time']=date('Y-m-d H:i:s');
        }elseif($status==2){
            $updateData['unable_time']=date('Y-m-d H:i:s');
        }
        $updateData['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('school')->where([['id','=',$info['id']]])->update($updateData);
        if($res){
            return jsondata('200',$statusname[$status]."成功");
        }else{
            return jsondata('400',$statusname[$status]."失败,请重试");
        }
    }

    //删除校区
    public function school_delete($id){
        $delid=array();
        $delimg=[];
        foreach($id as $v){
            $map=[];
            $map[]=['id','=',intval($v)];
            $info=$this->schoolDetail($map);
            if(!empty($info)){
                $omap=[];
                $omap[]=['school_id','=',$info['id']];
                $omap[]=['isdel','=',2];
                $hasorder=DB::name('orders')->where($omap)->find();
                if(empty($hasorder)){
                    $delid[]=$info['id'];
                }
            }
        }
        if(empty($delid)){
            return jsondata('400','请选择要删除的校区,选中的校区还有未删除的订单');
        }
        $map=array();
        $map[]=['id','in',$delid];
        $res=DB::name('school')->where($map)->delete();
        if($res){
            $code='200';
            $msg='删除校区成功';
        }else{
            $code='400';
            $msg='删除校区失败,请重试';
        }
        return jsondata($code,$msg);
    }

}
