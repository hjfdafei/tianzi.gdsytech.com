<?php
namespace app\sytechadmin\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\sytechadmin\controller\Upload;
use app\sytechadmin\service\SchoolService;
//use app\sytechadmin\model\Banner;
//年级管理
class GradeService extends Base{
    //field:查询字段 map:查询条件 search:搜索条件显示在分页链接 orderby:排序 pernum:每页多少条 type:获取数据类别 1获取分页 2获取全部
    //年级列表
    public function getGradeList($type=1,$map=[],$field='*',$search=[],$pernum=20,$orderby=['id'=>'asc']){
        $list=array();
        $page='';
        $count=0;
        $isshowname=['1'=>'显示','2'=>'隐藏'];
        $schoolnamearr=[];
        $schoolservice=new SchoolService();
        if($type==1){
            $smap=[];
            $sfield='*';
            $sorderby=['sortby'=>'desc','id'=>'desc'];
            $school_list=$schoolservice->getSchoolList(2,$smap,$sfield,[],20,$sorderby)['list'];
            if(!empty($school_list)){
                foreach($school_list as $v){
                    $schoolnamearr[$v['id']]=$v['title'];
                }
            }
            $list=DB::name('grade')->field($field)->where($map)->order($orderby)->paginate($pernum,false,['query'=>$search])->each(function($item,$key) use($isshowname,$schoolnamearr){
                $item['isshowname']=$isshowname[$item['isshow']];
                $schoolname='';
                if(isset($schoolnamearr[$item['school_id']])){
                    $schoolname=$schoolnamearr[$item['school_id']];
                }
                $item['schoolname']=$schoolname;
                return $item;
            });
            $page=$list->render();
            $count=$list->total();
        }else{
            $list=DB::name('grade')->field($field)->where($map)->order($orderby)->select();
            $count=count($list);
        }
        $data['list']=$list;
        $data['page']=$page;
        $data['count']=$count;
        return $data;
    }

    //年级数据校验
    public function grade_verify($id,$admininfo){
        $id=intval($id);
        $title=input('post.title','','trim');
        $isshow=input('post.isshow','1','intval');
        $sortby=input('post.sortby','0','intval');
        $school_id=input('post.school_id','0','intval');
        if($admininfo['school_id']>0){
            $school_id=$admininfo['school_id'];
        }
        if(!in_array($isshow,[1,2])){
            $isshow=1;
        }
        $info=[];
        if($id>0){
            $map=[];
            $map[]=['id','=',$id];
            if($admininfo['school_id']>0){
                $map[]=['school_id','=',$admininfo['school_id']];
            }
            $info=$this->gradeDetail($map);
        }
        if($school_id>0){
            $school_service=new SchoolService();
            $school_map[]=['id','=',$school_id];
            $school_info=$school_service->schoolDetail($school_map);
            if(empty($school_info)){
                return jsondata('400','选择的校区不存在');
            }
        }
        $data=[
            'title'=>$title,
            'isshow'=>$isshow,
            'sortby'=>$sortby,
            'school_id'=>$school_id,
        ];
        if(empty($info)){
            $data['create_time']=date('Y-m-d H:i:s');
            $res=DB::name('grade')->insertGetId($data);
            $opname='新增';
        }else{
            $data['update_time']=date('Y-m-d H:i:s');
            $res=DB::name('grade')->where([['id','=',$info['id']]])->update($data);
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

    //年级详情
    public function gradeDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('grade')->field($field)->where($map)->find();
    }

    //年级隐藏/显示
    public function grade_showhide($id,$status=1,$admininfo){
        $map=[];
        $map[]=['id','=',$id];
        if($admininfo['school_id']>0){
            $map[]=['school_id','=',$admininfo['school_id']];
        }
        $info=$this->gradeDetail($map);
        if(empty($info)){
            return jsondata('400','需要操作的年级不存在');
        }
        $statusname=['1'=>'显示','2'=>'隐藏'];
        if($status==$info['isshow']){
            return jsondata('400',"已是".$statusname[$status]."状态,无需重复操作");
        }
        $updateData['isshow']=$status;
        $updateData['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('grade')->where([['id','=',$info['id']]])->update($updateData);
        if($res){
            return jsondata('200',$statusname[$status]."成功");
        }else{
            return jsondata('400',$statusname[$status]."失败,请重试");
        }
    }

    //删除年级
    public function grade_delete($id,$admininfo){
        $delid=array();
        $delimg=[];
        foreach($id as $v){
            $map=[];
            $map[]=['id','=',intval($v)];
            if($admininfo['school_id']>0){
                $map[]=['school_id','=',$admininfo['school_id']];
            }
            $info=$this->gradeDetail($map);
            if(!empty($info)){
                $delid[]=$info['id'];
            }
        }
        if(empty($delid)){
            return jsondata('400','请选择要删除的年级');
        }
        $map=array();
        $map[]=['id','in',$delid];
        $res=DB::name('grade')->where($map)->delete();
        if($res){
            $code='200';
            $msg='删除年级成功';
        }else{
            $code='400';
            $msg='删除年级失败,请重试';
        }
        return jsondata($code,$msg);
    }

}
