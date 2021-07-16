<?php
namespace app\api\controller;
use think\facade\Request;
use think\Controller;
use think\Db;
use think\db\Query;
use app\api\controller\Base;
use app\api\service\GenericService;
use app\api\service\UserService;
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

    //名医搜索
    public function doctor_list(){
        $keyword=input('keyword','','trim');
        $isrecommend=input('isrecommend','0','intval');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='id,realname,avatar,hospital_id,department_id,professional_titles,labels,skill,meetnum,visit_rate';
        $orderby=['sortby'=>'desc','id'=>'asc'];
        $map=[];
        $map[]=['status','=',1];
        $map[]=['isdel','=',2];
        if($isrecommend==1){
            $map[]=['isindex','=',1];
        }
        $map2=[];
        if($keyword!=''){
            $keywords=[];
            $search_keyword=explode(',',str_replace([" ",'，'],[',',','],$keyword));
            if(!empty($search_keyword)){
                foreach($search_keyword as $v){
                    if($v!=''){
                        $keywords[]=$v;
                    }
                }
            }
            $keywords=array_unique($keywords);
            if(!empty($keywords)){
                foreach($keywords as $v){
                    $map2[]=['realname|professional_titles|labels|search_word|skill','like',"%$v%"];
                }
            }
        }
        $service=new GenericService();
        $list=$service->doctorList($style,$map,$map2,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $count=$list['count'];
        $hospital_namearr=[];
        $department_namearr=[];
        if(!empty($listdata)){
            foreach($listdata as &$v){
                if($v['avatar']!=''){
                    $v['avatar']=$this->weburl.getabpath($v['avatar'],'upload');
                }
                $v['professional_titles']=explode('|',$v['professional_titles']);
                $v['labels']=explode('|',$v['labels']);
                $v['skill']=explode('|',$v['skill']);
                if($v['visit_rate']<=0){
                    $v['visit_rate']=$service->getDoctorVisitRate($v['id']);
                }
                $v['visit_rate']=$v['visit_rate'].'%';
                if($v['meetnum']<=0){
                    $v['meetnum']=$service->getDoctorMeetnum($v['id']);
                }
                $hospital_name='';
                $department_name='';
                if(isset($hospital_namearr[$v['hospital_id']])){
                    $hospital_name=$hospital_namearr[$v['hospital_id']];
                }else{
                    $hospital_namearr[$v['hospital_id']]=$service->attributeName($v['hospital_id']);
                    $hospital_name=$hospital_namearr[$v['hospital_id']];
                }
                if(isset($department_namearr[$v['department_id']])){
                    $department_name=$department_namearr[$v['department_id']];
                }else{
                    $department_namearr[$v['department_id']]=$service->attributeName($v['department_id']);
                    $department_name=$department_namearr[$v['department_id']];
                }
                $v['hospital_name']=$hospital_name;
                $v['department_name']=$department_name;
            }
        }
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //名医详情
    public function doctor_detail(){
        $doctor_id=input('doctor_id','0','intval');
        $token=input('token','','trim');
        if($doctor_id<=0){
            return jsondata('0021','请选择查看信息');
        }
        $map=[];
        $map[]=['status','=',1];
        $map[]=['isdel','=',2];
        $map[]=['id','=',$doctor_id];
        $field='id,realname,avatar,hospital_id,department_id,professional_titles,labels,content,skill,honorlist,place,meetnum,visit_rate';
        $service=new GenericService();
        $info=$service->doctorDetail($map,$field);
        if(empty($info)){
            return jsondata('0021','查看信息不存在');
        }
        if($info['avatar']!=''){
            $info['avatar']=$this->weburl.getabpath($info['avatar'],'upload');
        }
        $info['professional_titles']=$info['professional_titles']==''?[]:explode('|',$info['professional_titles']);
        $info['labels']=$info['labels']==''?[]:explode('|',$info['labels']);
        $info['skill']=$info['skill']==''?[]:explode('|',$info['skill']);
        $info['honorlist']=$info['honorlist']==''?[]:explode('|',$info['honorlist']);
        $info['place']=$info['place']==''?[]:explode('|',$info['place']);
        $info['content']=htmlspecialchars_decode(html_entity_decode($info['content']));
        if($info['visit_rate']<=0){
            $info['visit_rate']=$service->getDoctorVisitRate($info['id']);
        }
        $info['visit_rate']=$info['visit_rate'].'%';
        if($info['meetnum']<=0){
            $info['meetnum']=$service->getDoctorMeetnum($info['id']);
        }
        $info['hospital_name']=$service->attributeName($info['hospital_id']);
        $info['department_name']=$service->attributeName($info['department_id']);
        $assistant_mobile='';
        if(isset($info['assistantinfo']) && !empty($info['assistantinfo'])){
            $assistant_mobile=$info['assistantinfo']['mobile'];
        }
        $isfavor=0;
        if($token!=''){
            $umap=[];
            $umap[]=['token','=',$token];
            $userservice=new UserService();
            $userinfo=$userservice->getUserInfo($umap);
            if(!empty($userinfo)){
                $favorres=$userservice->userCheckFavor($userinfo['id'],$doctor_id);
                if($favorres){
                    $isfavor=1;
                }
            }
        }
        $info['isfavor']=$isfavor;
        $info['assistant_mobile']=$assistant_mobile;
        unset($info['assistantinfo']);
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //资讯类型
    public function news_type(){
        $list=config('app.newstype');
        $data['list']=$list;
        return jsondata('0001','获取成功',$data);
    }

    //资讯列表
    public function news_list(){
        $position=input('position','1','intval');
        $typeid=input('typeid','1','intval');
        $keyword=input('keyword','','trim');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='id,title,img,linkurl,subtitle,brief,place,content';
        $orderby=['sortby'=>'desc','id'=>'asc'];
        $map=[];
        $map[]=['ispublic','=',1];
        $map[]=['typeid','=',$typeid];
        if($position==1){
            $map[]=['isindex','=',1];
            $start=0;
            $limit=2;
        }
        $service=new GenericService();
        $list=$service->newsList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $count=$list['count'];
        if(!empty($listdata)){
            foreach($listdata as &$v){
                if($v['img']!=''){
                    $v['img']=$this->weburl.getabpath($v['img'],'upload');
                }
                if($v['brief']==''){
                    $v['brief']=cnsubstr(preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/",'',strip_tags(htmlspecialchars_decode(html_entity_decode($v['content'])))),40);
                }
                unset($v['content']);
            }
        }
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //资讯详情
    public function news_detail(){
        $news_id=input('news_id','0','intval');
        if($news_id<=0){
            return jsondata('0021','请选择资讯');
        }
        $field='id,title,img,linkurl,subtitle,brief,place,content';
        $map=[];
        $map[]=['ispublic','=',1];
        $map[]=['id','=',$news_id];
        $service=new GenericService();
        $info=$service->newsDetail($map,$field);
        if(empty($info)){
            return jsondata('0022','查看的资讯不存在');
        }
        if($info['img']!=''){
            $info['img']=$this->weburl.getabpath($info['img'],'upload');
        }
        if($info['brief']==''){
            $info['brief']=cnsubstr(preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/",'',strip_tags(htmlspecialchars_decode(html_entity_decode($info['content'])))),40);
        }
        //$info['content']=htmlspecialchars_decode($info['content']);
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //基层医生列表
    public function basedoctor_list(){
        $keyword=input('keyword','','trim');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='id,realname';
        $orderby=['sortby'=>'desc','id'=>'asc'];
        $map=[];
        $map[]=['status','=',1];
        $map[]=['isdel','=',2];
        if($keyword!=''){
            $map[]=['realname|mobile','like',"%$keyword%"];
        }
        $service=new GenericService();
        $list=$service->basedoctorList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $count=$list['count'];
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //医院列表
    public function hospital_list(){
        $keyword=input('keyword','','trim');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='id,title';
        $orderby=['sortby'=>'desc','id'=>'asc'];
        $map=[];
        $map[]=['typeid','=',1];
        $map[]=['isshow','=',1];
        if($keyword!=''){
            $map[]=['title','like',"%$keyword%"];
        }
        $service=new GenericService();
        $list=$service->attributeList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $count=$list['count'];
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //科室列表
    public function department_list(){
        $keyword=input('keyword','','trim');
        $style=input('style','1','intval');
        $pagenum=input('pagenum',1,'intval');
        $pernum=10;
        $start=($pagenum-1)*$pernum;
        $limit=$pernum;
        $field='id,title';
        $orderby=['sortby'=>'desc','id'=>'asc'];
        $map=[];
        $map[]=['typeid','=',2];
        $map[]=['isshow','=',1];
        if($keyword!=''){
            $map[]=['title','like',"%$keyword%"];
        }
        $service=new GenericService();
        $list=$service->attributeList($style,$map,$field,$start,$limit,$orderby);
        $listdata=$list['list'];
        $count=$list['count'];
        $data['list']=$listdata;
        $data['count']=$count;
        return jsondata('0001','获取成功',$data);
    }

    //获取分享参数
    public function getShareParam(){
        $url=input('url','','trim');
        if($url==''){
            return jsondata('0029','请输入页面链接');
        }
        $appid=config('app.miniappid');
        $appsecret=config('app.minisecret');
        $jsapiTicket=getJsApiTicket($appid,$appsecret);
        $timestamp = time();
        $nonceStr=rand_string('',32,4);
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string="jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = array(
            "appId"     => $appid,
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            'sharelogo'=>$this->weburl.'/upload/sharelogo.png',
            //"rawString" => $string
        );
        $data['data']=$signPackage;
        return jsondata('0001','获取成功',$data);
    }
    //上传证件
    public function attach_upload(){
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
