<?php
namespace app\sytechadmin\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\sytechadmin\controller\Upload;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use app\sytechadmin\service\SchoolService;

//宽带账号管理
class BroadbandService extends Base{
    //field:查询字段 map:查询条件 search:搜索条件显示在分页链接 orderby:排序 pernum:每页多少条 type:获取数据类别 1获取分页 2获取全部
    //宽带账号列表
    public function getBroadbandList($type=1,$map=[],$field='*',$search=[],$pernum=20,$orderby=['id'=>'asc']){
        $list=array();
        $page='';
        $count=0;
        $statusname=['1'=>'启用','2'=>'禁用'];
        $usename=['1'=>'已使用','2'=>'未使用'];
        if($type==1){
            $schoolnamearr=[];
            $schoolservice=new SchoolService();
            $smap=[];
            $sfield='*';
            $sorderby=['sortby'=>'desc','id'=>'desc'];
            $school_list=$schoolservice->getSchoolList(2,$smap,$sfield,[],20,$sorderby)['list'];
            if(!empty($school_list)){
                foreach($school_list as $v){
                    $schoolnamearr[$v['id']]=$v['title'];
                }
            }
            $list=DB::name('broadband')->field($field)->where($map)->order($orderby)->paginate($pernum,false,['query'=>$search])->each(function($item,$key) use($statusname,$usename,$schoolnamearr){
                $schoolname='';
                if(isset($schoolnamearr[$item['school_id']])){
                    $schoolname=$schoolnamearr[$item['school_id']];
                }
                $item['schoolname']=$schoolname;
                $item['statusname']=$statusname[$item['status']];
                $item['usename']=$usename[$item['isuse']];
                return $item;
            });
            $page=$list->render();
            $count=$list->total();
        }else{
            $list=DB::name('broadband')->field($field)->where($map)->order($orderby)->select();
            $count=count($list);
        }
        $data['list']=$list;
        $data['page']=$page;
        $data['count']=$count;
        return $data;
    }

    //宽带账号数据校验
    public function broadband_verify($id,$admininfo){
        $id=intval($id);
        $keyaccount=input('post.keyaccount','','trim');
        $keypassword=input('post.keypassword','','trim');
        $status=input('post.status','1','intval');
        $school_id=input('post.school_id','0','intval');
        if($admininfo['school_id']>0){
            $school_id=$admininfo['school_id'];
        }
        if(!in_array($status,[1,2])){
            $status=1;
        }
        if($keyaccount==''){
            return jsondata('400','请输入宽带账号');
        }
        if($keypassword==''){
            return jsondata('400','请输入宽带密码');
        }
        if($school_id<=0){
            return jsondata('400','请选择宽带所在校区');
        }
        $schoolservice=new SchoolService();
        $smap=[];
        $smap[]=['id','=',$school_id];
        $school_info=$schoolservice->schoolDetail($smap);
        if(empty($school_info)){
            return jsondata('400','选择的校区不存在');
        }
        $info=[];
        if($id>0){
            $map=[];
            $map[]=['id','=',$id];
            $info=$this->broadbandDetail($map);
        }
        $map=[];
        $map[]=['keyaccount','=',$keyaccount];
        $hasbroadband=$this->broadbandDetail($map);
        if(!empty($hasbroadband)){
            if($hasbroadband['id']!=$id){
                return jsondata('400','宽带账号已存在');
            }
        }
        $data=[
            'keyaccount'=>$keyaccount,
            'keypassword'=>$keypassword,
            'status'=>$status,
            'school_id'=>$school_id,
        ];
        if(empty($info)){
            if($status==1){
                $data['enable_time']=date('Y-m-d H:i:s');
            }elseif($status==2){
                $data['unable_time']=date('Y-m-d H:i:s');
            }
            $data['create_time']=date('Y-m-d H:i:s');
            $res=DB::name('broadband')->insertGetId($data);
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
            $res=DB::name('broadband')->where([['id','=',$info['id']]])->update($data);
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

    //导入宽带账号
    public function broadband_importdata($filedata,$admininfo){
        set_time_limit(0);
        $fileext=substr(strrchr($filedata['name'],'.'),1);
        $objReader=IOFactory::createReader(ucfirst($fileext));
        $filename=$filedata['tmp_name'];
        $objPHPExcel=$objReader->load($filename);
        $sheet=$objPHPExcel->getSheet(0);
        $highestRow=$sheet->getHighestRow();
        $allnum=$highestRow-1;
        $highestColumn = $sheet->getHighestColumn();
        ++$highestColumn;
        $data=[];
        if($allnum>2000){
            return jsondata('400','一次最多只能导入2000条数据');
        }
        $school_id=input('post.school_id','0','intval');
        if($admininfo['school_id']>0){
            $school_id=$admininfo['school_id'];
        }
        if($school_id<=0){
            return jsondata('400','请选择宽带所在校区');
        }
        $schoolservice=new SchoolService();
        $smap=[];
        $smap[]=['id','=',$school_id];
        $school_info=$schoolservice->schoolDetail($smap);
        if(empty($school_info)){
            return jsondata('400','选择的校区不存在');
        }
        $num=0;
        $num2=0;
        $num3=0;
        DB::startTrans();
        for($i=2;$i<=$highestRow;$i++){
            $keyaccount=trim($objPHPExcel->getActiveSheet()->getCell('A'.$i)->getValue());
            $keypassword=trim($objPHPExcel->getActiveSheet()->getCell('B'.$i)->getValue());
            $map=[];
            $map[]=['keyaccount','=',$keyaccount];
            $hasbroadband=$this->broadbandDetail($map);
            if(empty($hasbroadband)){
                $data=[];
                $data['keyaccount']=$keyaccount;
                $data['keypassword']=$keypassword;
                $data['status']=1;
                $data['school_id']=$school_id;
                $data['create_time']=date('Y-m-d H:i:s');
                $res=DB::name('broadband')->insertGetId($data);
                if($res){
                    $num++;
                }else{
                    $num3++;
                }
            }else{
                $num2++;
            }
        }
        if($num>0){
            DB::commit();
            return jsondata('200','导入成功,其中导入成功'.$num.'条,导入失败'.$num3.'条,导入重复'.$num3.'条');
        }else{
            DB::rollback();
            return jsondata('400','导入失败,其中导入成功'.$num.'条,导入失败'.$num3.'条,导入重复'.$num3.'条');
        }
    }

    //宽带账号详情
    public function broadbandDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('broadband')->field($field)->where($map)->find();
    }

    //宽带账号启用/禁用
    public function broadband_showhide($id,$status=1,$admininfo){
        $map=[];
        $map[]=['id','=',$id];
        if($admininfo['school_id']>0){
            $map[]=['school_id','=',$admininfo['school_id']];
        }
        $info=$this->broadbandDetail($map);
        if(empty($info)){
            return jsondata('400','需要操作的宽带账号不存在');
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
        $res=DB::name('broadband')->where([['id','=',$info['id']]])->update($updateData);
        if($res){
            return jsondata('200',$statusname[$status]."成功");
        }else{
            return jsondata('400',$statusname[$status]."失败,请重试");
        }
    }

    //删除宽带账号
    public function broadband_delete($id,$admininfo){
        $delid=array();
        $delimg=[];
        foreach($id as $v){
            $map=[];
            $map[]=['id','=',intval($v)];
            if($admininfo['school_id']>0){
                $map[]=['school_id','=',$admininfo['school_id']];
            }
            $info=$this->broadbandDetail($map);
            if(!empty($info)){
                $omap=[];
                $omap[]=['broadband_id','=',$info['id']];
                $omap[]=['isdel','=',2];
                $hasorder=DB::name('orders')->where($omap)->find();
                if(empty($hasorder)){
                    $delid[]=$info['id'];
                }
            }
        }
        if(empty($delid)){
            return jsondata('400','请选择要删除的宽带账号,选中的宽带账号还有未删除的订单');
        }
        $map=array();
        $map[]=['id','in',$delid];
        $res=DB::name('broadband')->where($map)->delete();
        if($res){
            $code='200';
            $msg='删除宽带账号成功';
        }else{
            $code='400';
            $msg='删除宽带账号失败,请重试';
        }
        return jsondata($code,$msg);
    }

}
