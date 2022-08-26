<?php
namespace app\sytechadmin\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\sytechadmin\controller\Upload;
use app\sytechadmin\service\SchoolService;
//use app\sytechadmin\model\Banner;
//幻灯片管理
class BannerService extends Base{
    //field:查询字段 map:查询条件 search:搜索条件显示在分页链接 orderby:排序 pernum:每页多少条 type:获取数据类别 1获取分页 2获取全部
    //幻灯片列表
    public function getBannerList($type=1,$map=[],$field='*',$search=[],$pernum=20,$orderby=['id'=>'asc']){
        $list=array();
        $page='';
        $count=0;
        $position=config('app.bannerposition');
        $positionname=[];
        foreach($position as $v){
            $positionname[$v['id']]=$v['title'];
        }
        $typename=['1'=>'无跳转','2'=>'小程序','3'=>'网页'];
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
            $list=DB::name('banner')->field($field)->where($map)->order($orderby)->paginate($pernum,false,['query'=>$search])->each(function($item,$key) use($positionname,$typename,$isshowname,$schoolnamearr){
                $item['typename']=$typename[$item['type']];
                $item['isshowname']=$isshowname[$item['isshow']];
                $item['positionname']=$positionname[$item['position']];
                if($item['img']!=''){
                    $item['img']=config('app.app_host').getabpath($item['img'],'upload');
                }
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
            $list=DB::name('banner')->field($field)->where($map)->order($orderby)->select();
            $count=count($list);
        }
        $data['list']=$list;
        $data['page']=$page;
        $data['count']=$count;
        return $data;
    }

    //benner数据校验
    public function banner_verify($id,$admininfo){
        $id=intval($id);
        $position=input('post.position','','intval');
        $title=input('post.title','','trim');
        $type=input('post.type','1','intval');
        $linkurl=input('post.linkurl','','trim');
        $isshow=input('post.isshow','1','intval');
        $sortby=input('post.sortby','0','intval');
        $school_id=input('post.school_id','0','intval');
        if($admininfo['school_id']>0){
            $school_id=$admininfo['school_id'];
        }
        $positionids=[];
        foreach(config('app.bannerposition') as $pv){
            $positionids[]=$pv['id'];
        }
        if(!in_array($position,$positionids)){
            return jsondata('400','请选择banner图位置');
        }
        if(!in_array($type,[1,2,3])){
            return jsondata('400','请选择跳转类型');
        }
        if(!in_array($isshow,[1,2])){
            $isshow=1;
        }
        if($type!=1){
            if($linkurl==''){
                return jsondata('400','请输入跳转链接');
            }
        }else{
            $linkurl='';
        }
        $info=[];
        if($id>0){
            $map=[];
            $map[]=['id','=',$id];
            if($admininfo['school_id']>0){
                $map[]=['school_id','=',$admininfo['school_id']];
            }
            $info=$this->bannerDetail($map);
        }
        if(empty($info)){
            if(empty(request()->file('img'))){
                return jsondata('400','请上传banner图片');
            }
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
            'linkurl'=>$linkurl,
            'type'=>$type,
            'isshow'=>$isshow,
            'position'=>$position,
            'sortby'=>$sortby,
            'school_id'=>$school_id,
        ];
        if(!empty(request()->file('img'))){
            $upload=new Upload;
            $uploadres=$upload->file_upload_param('attach','img','2');
            if($uploadres['code']!='1'){
                return jsondata('400',$uploadres['msg']);
            }
            $data['img']=$uploadres['url'];
        }
        if(empty($info)){
            $data['create_time']=date('Y-m-d H:i:s');
            $res=DB::name('banner')->insertGetId($data);
            $opname='新增';
        }else{
            $data['update_time']=date('Y-m-d H:i:s');
            $res=DB::name('banner')->where([['id','=',$info['id']]])->update($data);
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

    //banner详情
    public function bannerDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('banner')->field($field)->where($map)->find();
    }

    //banner隐藏/显示
    public function banner_showhide($id,$status=1,$admininfo){
        $map=[];
        $map[]=['id','=',$id];
        if($admininfo['school_id']>0){
            $map[]=['school_id','=',$admininfo['school_id']];
        }
        $info=$this->bannerDetail($map);
        if(empty($info)){
            return jsondata('400','需要操作的banner不存在');
        }
        $statusname=['1'=>'显示','2'=>'隐藏'];
        if($status==$info['isshow']){
            return jsondata('400',"已是".$statusname[$status]."状态,无需重复操作");
        }
        $updateData['isshow']=$status;
        $updateData['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('banner')->where([['id','=',$info['id']]])->update($updateData);
        if($res){
            return jsondata('200',$statusname[$status]."成功");
        }else{
            return jsondata('400',$statusname[$status]."失败,请重试");
        }
    }

    //删除banner
    public function banner_delete($id,$admininfo){
        $delid=array();
        $delimg=[];
        foreach($id as $v){
            $map=[];
            $map[]=['id','=',intval($v)];
            if($admininfo['school_id']>0){
                $map[]=['school_id','=',$admininfo['school_id']];
            }
            $info=$this->bannerDetail($map);
            if(!empty($info)){
                $delid[]=$info['id'];
                if($info['img']!=''){
                    $delimg[]=$info['img'];
                }
            }
        }
        if(empty($delid)){
            return jsondata('400','请选择要删除的banner');
        }
        $map=array();
        $map[]=['id','in',$delid];
        $res=DB::name('banner')->where($map)->delete();
        if($res){
            $code='200';
            $msg='删除banner成功';
            if(!empty($delimg)){
                foreach($delimg as $dv){
                    @unlink('.'.$dv);
                }
            }
        }else{
            $code='400';
            $msg='删除banner失败,请重试';
        }
        return jsondata($code,$msg);
    }

}
