<?php
namespace app\sytechadmin\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
//use app\sytechadmin\model\Audit;
//信息审核管理
class AuditService extends Base{
    //field:查询字段 map:查询条件 search:搜索条件显示在分页链接 orderby:排序 pernum:每页多少条 type:获取数据类别 1获取分页 2获取全部
    //角色列表
    public function getAuditList($field='c.*',$map=[],$search=[],$pernum=20,$type=1,$orderby=['c.id'=>'asc']){
        $list=array();
        $page='';
        $count=0;
        if($type==1){
            $list=DB::name('meeting_user c')->field($field)->join('__MEETING_USER__ p','p.id=c.parentid','left')->where($map)->order($orderby)->paginate($pernum,false,['query'=>$search])->each(function($item,$key){
                    if($item['status']==3){
                        if($item['remark']!=''){
                            $item['remark']=explode("+-??-+",$item['remark']);
                        }
                    }else{
                        $item['remark']='';
                    }
                    // $childnum=DB::name('meeting_user')->where([['parentid','=',$item['id']],['type','=','2']])->count();
                    // $item['childnum']=$childnum;
                    // $item['role_belong']='平台';
                    return $item;
                });
            $page=$list->render();
            $count=$list->total();
        }else{
            $list=DB::name('meeting_user c')->field($field)->join('__MEETING_USER__ p','p.id=c.parentid','left')->where($map)->order($orderby)->select();
            $count=count($list);
        }
        $data['list']=$list;
        $data['page']=$page;
        $data['count']=$count;
        return $data;
    }

    //处理通过和拒绝
    public function auditinfo_deal($infoid,$status,$remark=''){
        $statusname=['1'=>'通过','2'=>'待审核','3'=>'拒绝'];
        $infoid=intval($infoid);
        if(!in_array($status,array(1,2,3))){
            return jsondata('400','请选择需要审核的信息');
        }
        if($infoid<=0){
            return jsondata('400','请选择需要'.$statusname[$status].'的信息');
        }
        $map[]=['id','=',$infoid];
        $map[]=['type','=',2];
        $info=DB::name('meeting_user')->where($map)->find();
        if(empty($info)){
            return jsondata('400','请选择需要'.$statusname[$status].'的信息');
        }
        if($info['status']==$status){
            return jsondata('400','当前信息已是'.$statusname[$status].'状态无需重复操作'.$statusname[$status]);
        }
        $parentinfo=DB::name('meeting_user')->where([['type','=','1'],['id','=',$info['parentid']]])->find();
        if($remark!=''){
            $remark=preg_replace("/[\r\n]+/", '+-??-+',$remark);
            $update_data['remark']=$remark;
        }
        $update_data['status']=$status;
        $update_data['audit_time']=date('Y-m-d H:i:s');
        $update_data['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('meeting_user')->where($map)->update($update_data);
        if($res){
            $msgconfig['aliaccesskeyid']=config('aliaccesskeyid');
            $msgconfig['aliaccesskeysecret']=config('aliaccesskeysecret');
            $msgdata['mobile']=$parentinfo['mobile'];
            $msgdata['name']=$parentinfo['realname'];
            if($status==1){
                base_sendmsg($msgconfig,$msgdata,1);//发短信通知
            }elseif($status==3){
                base_sendmsg($msgconfig,$msgdata,2);//发短信通知
            }
            return jsondata('200',$statusname[$status].'信息成功');
        }else{
            return jsondata('400',$statusname[$status].'信息失败,请重试');
        }
    }

    //获取信息详情
    public function auditinfo_detail($field='c.*',$map=[]){
        $info=DB::name('meeting_user c')->field($field)->join('__MEETING_USER__ p','p.id=c.parentid','left')->where($map)->find();
        return $info;
    }

    //删除宝宝信息
    public function audit_delete($babyid){
        $delbabyid=array();
        $hasadminbabyid=array();
        $delimgs=[];
        foreach($babyid as $v){
            $info=$this->auditinfo_detail('c.*',['c.id'=>intval($v)]);
            if(!empty($info)){
                $delbabyid[]=$info['id'];
                $delimgs[]=['proveimg_front'=>$info['proveimg_front'],'proveimg_back'=>$info['proveimg_back']];
            }
        }
        if(empty($delbabyid)){
            return jsondata('400','请选择要删除的宝宝信息000');
        }
        $map=array();
        $map[]=['id','in',$delbabyid];
        $map[]=['type','=','2'];
        $res=DB::name('meeting_user')->where($map)->delete();
        if($res){
            $map=[];
            $map[]=['memberid','in',$delbabyid];
            DB::name('meeting_record')->where($map)->delete();
            if(!empty($delimgs)){
                foreach($delimgs as $imgv){
                    $urlhead=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
                    if(substr($imgv['proveimg_front'],strlen($urlhead))!=''){
                        @unlink('.'.substr($imgv['proveimg_front'],strlen($urlhead)));
                    }
                    if(substr($imgv['proveimg_back'],strlen($urlhead))!=''){
                        @unlink('.'.substr($imgv['proveimg_back'],strlen($urlhead)));
                    }
                }
            }
            return jsondata('200','删除宝宝信息成功');
        }else{
            return jsondata('400','删除宝宝信息失败,请重试');
        }
    }


}
