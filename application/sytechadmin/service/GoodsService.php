<?php
namespace app\sytechadmin\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\sytechadmin\controller\Upload;

//宽带套餐管理
class GoodsService extends Base{
    //field:查询字段 map:查询条件 search:搜索条件显示在分页链接 orderby:排序 pernum:每页多少条 type:获取数据类别 1获取分页 2获取全部
    //宽带套餐列表
    public function getGoodsList($type=1,$map=[],$field='*',$search=[],$pernum=20,$orderby=['id'=>'asc']){
        $list=array();
        $page='';
        $count=0;
        $statusname=['1'=>'上架','2'=>'下架'];
        if($type==1){
            $list=DB::name('goods')->field($field)->where($map)->order($orderby)->paginate($pernum,false,['query'=>$search])->each(function($item,$key) use($statusname){
                $item['statusname']=$statusname[$item['goods_status']];
                $omap=[];
                $omap[]=['goods_id','=',$item['id']];
                $item['sale_num']=DB::name('orders')->where($omap)->count();
                return $item;
            });
            $page=$list->render();
            $count=$list->total();
        }else{
            $list=DB::name('goods')->field($field)->where($map)->order($orderby)->select();
            $count=count($list);
        }
        $data['list']=$list;
        $data['page']=$page;
        $data['count']=$count;
        return $data;
    }

    //宽带套餐数据校验
    public function goods_verify($id){
        $id=intval($id);
        $goods_title=input('post.goods_title','','trim');
        $goods_price=input('post.goods_price','','trim');
        $goods_status=input('post.goods_status','1','intval');
        $goods_sortby=input('post.goods_sortby','0','intval');
        $goods_content=input('post.goods_content','','trim');
        if(!in_array($goods_status,[1,2])){
            $goods_status=1;
        }
        $info=[];
        if($id>0){
            $map=[];
            $map[]=['id','=',$id];
            $info=$this->goodsDetail($map);
        }
        if(empty($info)){
            if(empty(request()->file('goods_img'))){
                return jsondata('400','请上传宽带套餐封面图');
            }
        }
        $data=[
            'goods_title'=>$goods_title,
            'goods_price'=>$goods_price,
            'goods_status'=>$goods_status,
            'goods_sortby'=>$goods_sortby,
            'goods_content'=>$goods_content,
        ];
        if(!empty(request()->file('goods_img'))){
            $upload=new Upload;
            $uploadres=$upload->file_upload_param('attach','goods_img','2');
            if($uploadres['code']!='1'){
                return jsondata('400',$uploadres['msg']);
            }
            $data['goods_img']=$uploadres['url'];
        }
        if(empty($info)){
            if($goods_status==1){
                $data['goods_onshelf_time']=date('Y-m-d H:i:s');
            }elseif($goods_status==2){
                $data['goods_offshelf_time']=date('Y-m-d H:i:s');
            }
            $data['create_time']=date('Y-m-d H:i:s');
            $res=DB::name('goods')->insertGetId($data);
            $opname='新增';
        }else{
            if($goods_status!=$info['goods_status']){
                if($goods_status==1){
                    $data['goods_onshelf_time']=date('Y-m-d H:i:s');
                }elseif($goods_status==2){
                    $data['goods_offshelf_time']=date('Y-m-d H:i:s');
                }
            }
            $data['update_time']=date('Y-m-d H:i:s');
            $res=DB::name('goods')->where([['id','=',$info['id']]])->update($data);
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

    //宽带套餐详情
    public function goodsDetail($map,$field='*'){
        if(empty($map)){
            return [];
        }
        return DB::name('goods')->field($field)->where($map)->find();
    }

    //宽带套餐上架/下架
    public function goods_showhide($id,$status=1){
        $map=[];
        $map[]=['id','=',$id];
        $info=$this->goodsDetail($map);
        if(empty($info)){
            return jsondata('400','需要操作的宽带套餐不存在');
        }
        $statusname=['1'=>'上架','2'=>'下架'];
        if($status==$info['goods_status']){
            return jsondata('400',"已是".$statusname[$status]."状态,无需重复操作");
        }
        $updateData['goods_status']=$status;
        if($status==1){
            $updateData['goods_onshelf_time']=date('Y-m-d H:i:s');
        }elseif($status==2){
            $updateData['goods_offshelf_time']=date('Y-m-d H:i:s');
        }
        $updateData['update_time']=date('Y-m-d H:i:s');
        $res=DB::name('goods')->where([['id','=',$info['id']]])->update($updateData);
        if($res){
            return jsondata('200',$statusname[$status]."成功");
        }else{
            return jsondata('400',$statusname[$status]."失败,请重试");
        }
    }

    //删除宽带套餐
    public function goods_delete($id){
        $delid=array();
        $delimg=[];
        foreach($id as $v){
            $map=[];
            $map[]=['id','=',intval($v)];
            $info=$this->goodsDetail($map);
            if(!empty($info)){
                $omap=[];
                $omap[]=['goods_id','=',$info['id']];
                $omap[]=['isdel','=',2];
                $hasorder=DB::name('orders')->where($omap)->find();
                if(empty($hasorder)){
                    $delid[]=$info['id'];
                }
            }
        }
        if(empty($delid)){
            return jsondata('400','请选择要删除的宽带套餐,选中的宽带套餐还有未删除的订单');
        }
        $map=array();
        $map[]=['id','in',$delid];
        $res=DB::name('goods')->where($map)->delete();
        if($res){
            $code='200';
            $msg='删除宽带套餐成功';
        }else{
            $code='400';
            $msg='删除宽带套餐失败,请重试';
        }
        return jsondata($code,$msg);
    }

}
