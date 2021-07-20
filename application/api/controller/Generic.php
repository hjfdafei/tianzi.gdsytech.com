<?php
namespace app\api\controller;
use think\facade\Request;
use think\Controller;
use think\Db;
use think\db\Query;
use app\api\controller\Base;
use app\api\controller\Upload;
use app\api\service\GenericService;
class Generic extends Base{
    public function _empty(){
        return json(['code'=>'0004','msg'=>'error']);
    }

    //获取banner
    public function getBanner(){
        $position=input('position','1','intval');
        $style=2;
        $start=1;
        $limit=10;
        $orderby=['sortby'=>'desc','id'=>'desc'];
        $field='id,title,img,linkurl,type';
        $map=[];
        $map[]=['position','=',$position];
        $map[]=['isshow','=',1];
        $service=new GenericService();
        $list=$service->bannerList($style,$map,$field,$start,$limit,$orderby);
        if(!empty($list['list'])){
            foreach($list['list'] as &$v){
                if($v['img']!=''){
                    $v['img']=$this->weburl.getabpath($v['img'],'upload');
                }
            }
        }
        $data['list']=$list['list'];
        $data['count']=$list['count'];
        return jsondata('0001','获取成功',$data);
    }

    //获取宽带套餐办理须知
    public function getNotice(){
        $path='../config/webconfig.json';
        $webconfig=json_decode(file_get_contents($path),true);
        $data['content']=htmlspecialchars_decode($webconfig['content']);
        return jsondata('0001','获取成功',$data);
    }

    //获取宽带套餐列表
    public function getGoodsList(){
        $pagenum=input('pagenum',1,'intval');
        $keyword=input('keyword','','trim');
        if($pagenum<=0) $pagenum=1;
        $map=[];
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $map[]=['goods_status','=',1];
        if($keyword!=''){
            $map[]=['goods_title','like',"%$keyword%"];
        }
        $field='id,goods_title,goods_img,goods_price';
        $orderby=['goods_sortby'=>'desc','id'=>'asc'];
        $style=1;
        $service=new GenericService();
        $list=$service->goodsList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $count=$list['count'];
        if(!empty($listdata)){
            foreach($listdata as &$v){
                if($v['goods_img']!=''){
                    $v['goods_img']=$this->weburl.getabpath($v['goods_img'],'upload');
                }
            }
        }
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //获取宽带套餐详情
    public function getGoodsDetail(){
        $goods_id=input('goods_id','0','intval');
        if($goods_id<=0){
            return jsondata('0019','请选择宽带套餐');
        }
        $map=[];
        $map[]=['goods_status','=',1];
        $map[]=['id','=',$goods_id];
        $field='id,goods_title,goods_img,goods_price,goods_content';
        $service=new GenericService();
        $info=$service->goodsDetail($map,$field);
        if(empty($info)){
            return jsondata('0019','宽带套餐信息不存在');
        }
        if($info['goods_img']!=''){
            $info['goods_img']=$this->weburl.getabpath($info['goods_img'],'upload');
        }
        $info['goods_content']=htmlspecialchars_decode($info['goods_content']);
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //获取校区列表
    public function getSchoolList(){
        $pagenum=input('pagenum',1,'intval');
        $keyword=input('keyword','','trim');
        if($pagenum<=0) $pagenum=1;
        $map=[];
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $map[]=['status','=',1];
        if($keyword!=''){
            $map[]=['title','like',"%$keyword%"];
        }
        $field='id,title,address,logo';
        $orderby=['sortby'=>'desc','id'=>'asc'];
        $style=1;
        $service=new GenericService();
        $list=$service->schoolList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $count=$list['count'];
        if(!empty($listdata)){
            foreach($listdata as &$v){
                if($v['logo']!=''){
                    $v['logo']=$this->weburl.getabpath($v['logo'],'upload');
                }else{
                    $v['logo']=$this->weburl.'/static/images/school_logo.png';
                }
            }
        }
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //校区详情
    public function getSchoolDetail(){
        $school_id=input('school_id','0','intval');
        if($school_id<=0){
            return jsondata('0019','请选择校区');
        }
        $map=[];
        $map[]=['id','=',$school_id];
        $field='id,title,address,logo';
        $service=new GenericService();
        $info=$service->schoolDetail($map,$field);
        if(empty($info)){
            return jsondata('0018','校区信息不存在');
        }
        if($info['logo']!=''){
            $info['logo']=$this->weburl.getabpath($info['logo'],'upload');
        }else{
            $info['logo']=$this->weburl.'/static/images/school_logo.png';
        }
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //小程序订阅消息模板
    public function getSubTemplate(){
        $list=[
            [
                'template_id'=>'grFWUPWUQvG1D6cr3dqjhjr1S2BxndHc_5DaLg-RI4w',
            ],
        ];
        $data['list']=$list;
        return jsondata('0001','获取成功',$data);
    }

    public function testpush(){
        //send_broadbandtpl('oLzrE4u1Vhfnylyjv9BKpE3CvJbE','大飞','202107201601561929');
        send_mini_broadbandtpl('oLzrE4u1Vhfnylyjv9BKpE3CvJbE','宽带套餐','dafei ','30');
    }

    //上传证件
    public function attach_upload(){
        exit;
        set_time_limit(0);
        $file_field=input('param.uploadfile','uploadfile','trim');
        $upload=new Upload;
        $uploadres=$upload->file_upload_param('attach',$file_field,'2',1);
        if($uploadres['code']!='1'){
            return jsondata('400',$uploadres['msg']);
        }
        $data['file_path']=$this->weburl.$uploadres['url'];
        return jsondata('0001','上传成功',$data);

    }
}
