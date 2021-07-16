<?php
namespace app\api\controller;
use think\facade\Request;
use think\Controller;
use think\Db;
use think\db\Query;
use app\api\controller\Indexbase;
use app\api\service\IndexService;
use app\api\service\CommonmeetService;
use app\api\service\QrcodeService;
class Index extends Indexbase{
    public function _empty(){
        return json(['code'=>'0004','msg'=>'网络错误']);
    }

    //完善家长信息
    public function improve_parentinfo(){
        if(request()->isPost() || request()->isAjax()){
            $indexservice=new IndexService();
            return $indexservice->parentinfo_verify($this->base_userinfo['openid']);
        }
        return jsondata('0004','网络错误');
    }

    //获取家长信息
    public function getparentinfo(){
        $indexservice=new IndexService();
        $field='realname,idcardnum,mobile,address,status';
        $info=$indexservice->parentinfo4openid($this->base_userinfo['openid'],$field);
        // $remark='';
        // if($info['status']==3){
        //     if($info['remark']!=''){
        //         $remark=str_replace("+-??-+", ',',$info['remark']);
        //     }
        // }
        // $info['remark']=$remark;
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //宝宝信息列表
    public function babyinfo_list(){
        $start=input('start','0','intval');
        $limit=input('limit','10','intval');
        $keyword=input('keyword','','trim');
        $list=[];
        $count=0;
        $indexservice=new IndexService();
        $map=[];
        $map[]=['parentid','=',$this->base_meetinguser['id']];
        if($keyword!=''){
            $map[]=['realname','like',"%$keyword%"];
        }
        $field='id,realname,sex,birthday,status';
        $listdata=$indexservice->getbaby_list($map,$field,1,$start,$limit);
        $data['count']=$listdata['count'];
        $data['data']=$listdata['list'];
        return jsondata('0001','获取成功',$data);
    }

    //添加宝宝信息
    public function babyinfo_add(){
        if(request()->isPost() || request()->isAjax()){
            $indexservice=new IndexService();
            return $indexservice->baby_verify($this->base_meetinguser['id'],0);
        }
        return jsondata('0004','网络错误');
    }

    //获取宝宝信息
    public function babyinfo_detail(){
        $babyid=input('babyid','0','intval');
        if($babyid<=0){
            return jsondata('0021','请选择需要查看的宝宝信息');
        }
        $indexservice=new IndexService();
        $field='id,realname,sex,birthday,status,proveimg_front,proveimg_back,remark';
        $info=$indexservice->babyinfo4id($this->base_meetinguser['id'],$babyid,$field);
        if($info['status']==1){
            $info['proveimg_front']='';
            $info['proveimg_back']='';
        }
        $remark='';
        if($info['status']==3){
            if($info['remark']!=''){
                $remark=str_replace("+-??-+", ',',$info['remark']);
            }
        }
        $info['remark']=$remark;
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //修改宝宝信息
    public function babyinfo_edit(){
        if(request()->isPost() || request()->isAjax()){
            $babyid=input('babyid','0','intval');
            if($babyid<=0){
                return jsondata('0021','请选择需要修改的宝宝信息');
            }
            $indexservice=new IndexService();
            return $indexservice->baby_verify($this->base_meetinguser['id'],$babyid);
        }
        return jsondata('0004','网络错误');
    }

    //删除宝宝信息
    public function babyinfo_del(){
        if(request()->isPost() || request()->isAjax()){
            $babyid=input('babyid','','trim');
            if($babyid==''){
                return jsondata('0031','请选择需要删除的宝宝信息');
            }
            $babyid=explode(',',trim($babyid,','));
            $indexservice=new IndexService();
            return $indexservice->baby_delete($this->base_meetinguser,$babyid);
        }
        return jsondata('0004','网络错误');
    }

    //获取时间
    public function meetingtime_list(){
        $meetingsiteid=input('meetingsiteid','1','intval');
        $commonservice=new CommonmeetService();
        $weekdata=$commonservice->getdatelist(7,1,$meetingsiteid);
        $timedata=$commonservice->gettimenum(date('Y-m-d'),1,$meetingsiteid);
        $data['weekdata']=$weekdata;
        $data['timedata']=$timedata;
        return jsondata('0001','获取成功',$data);
    }

    //获取更多日期
    public function meetingtime_list_more(){
        $meetingsiteid=input('meetingsiteid','1','intval');
        $commonservice=new CommonmeetService();
        $weekdata=$commonservice->getdatelist(31,1,$meetingsiteid);
        $data['data']=$weekdata;
        return jsondata('0001','获取成功',$data);
    }

    //获取预约数量
    public function getmeeting_number(){
        $meetingsiteid=input('meetingsiteid','1','intval');
        $selectdate=input('selectdate',date('Y-m-d'),'trim');
        if($selectdate<date('Y-m-d')){
            return jsondata('0041','请选择日期');
        }
        $commonservice=new CommonmeetService();
        $timedata=$commonservice->gettimenum($selectdate,1,$meetingsiteid);
        $data['data']=$timedata;
        return jsondata('0001','获取成功',$data);
    }

    //获取疫苗列表
    public function vaccine_list(){
        $meetingsiteid=input('meetingsiteid','1','intval');
        $field='v.id,v.title,v.content,v.remark,v.vaccage';
        $map[]=['vi.meetingsiteid','=',$meetingsiteid];
        $map[]=['vi.isshow','=','1'];
        $map[]=['vi.stock','>','0'];
        //$list=DB::name('vaccine')->field($field)->where($map)->order(['orderby'=>'desc','id'=>'asc'])->select();
        $list=DB::name('vaccine_item vi')->field($field)->join('__VACCINE__ v','v.id=vi.vaccineid')->where($map)->order(['vi.orderby'=>'desc','vi.vaccineid'=>'asc'])->select();
        foreach($list as &$v){
            $tmpages=json_decode($v['vaccage'],true);
            if($v['remark']==''){
                //$remark='接种年龄在';
                $remark='';
                if(!empty($tmpages)){
                    foreach($tmpages as $agv){
                        if($agv['ages_year']>0 && $agv['ages_month']>0){
                            $remark.=$agv['ages_year'].'岁'.$agv['ages_month'].'月;';
                        }elseif($agv['ages_year']>0 && $agv['ages_month']<=0){
                             $remark.=$agv['ages_year'].'岁;';
                        }elseif($agv['ages_year']<=0 && $agv['ages_month']>0){
                            $remark.=$agv['ages_month'].'月;';
                        }
                        // if($agv['ages_year']>0){
                        //     $remark.=';'.$agv['ages_year'].'岁';
                        // }
                        // if($agv['ages_month']>0){
                        //     $remark.=$agv['ages_month'].'月;';
                        // }
                    }
                }
                $v['remark']=trim($remark,';');
                unset($v['vaccage']);
                //$v['remark']="接种年龄在".$v['minyear']."年".$v['minmonth']."月到".$v['maxyear']."年".$v['maxmonth']."月";
            }

            // $tmpminage=explode('_',$v['minage']);
            // $tmpmaxage=explode('_',$v['maxage']);
            // if(!isset($tmpminage[0]) || $tmpminage[0]==''){
            //     $tmpminage[0]=0;
            // }
            // if(!isset($tmpminage[1]) || $tmpminage[1]==''){
            //     $tmpminage[1]=0;
            // }
            // if(!isset($tmpmaxage[0]) || $tmpmaxage[0]==''){
            //     $tmpmaxage[0]=0;
            // }
            // if(!isset($tmpmaxage[1]) || $tmpmaxage[1]==''){
            //     $tmpmaxage[1]=0;
            // }
            // $v['minyear']=$tmpminage[0];
            // $v['minmonth']=$tmpminage[1];
            // $v['maxyear']=$tmpmaxage[0];
            // $v['maxmonth']=$tmpmaxage[1];
            // if($v['remark']==''){
            //     //$remark='接种年龄在';
            //     $remark='';
            //     if($v['minage']==$v['maxage']){
            //         if($v['minyear']>0){
            //             $remark.=$v['minyear'].'岁';
            //         }
            //         if($v['minmonth']>0){
            //             $remark.=$v['minmonth'].'月';
            //         }
            //     }else{
            //         if($v['minyear']>0){
            //             $remark.=$v['minyear'].'岁';
            //         }
            //         if($v['minmonth']>0){
            //             $remark.=$v['minmonth'].'月';
            //         }
            //         if($v['maxyear']>0 || $v['maxmonth']>0){
            //             $remark.='-';
            //         }
            //         if($v['maxyear']>0){
            //             $remark.=$v['maxyear'].'岁';
            //         }
            //         if($v['maxmonth']>0){
            //             $remark.=$v['maxmonth'].'月';
            //         }
            //     }
            //     $v['remark']=$remark;
            //     //$v['remark']="接种年龄在".$v['minyear']."年".$v['minmonth']."月到".$v['maxyear']."年".$v['maxmonth']."月";
            // }
            // unset($v['minage'],$v['maxage']);
            //$v['content']=htmlspecialchars_decode($v['content']);
        }
        $data['data']=$list;
        return jsondata('0001','获取成功',$data);
    }

    //获取疫苗详情
    public function vaccine_detail(){
        $meetingsiteid=input('meetingsiteid','1','intval');
        $vaccineid=input('vaccineid','0','intval');
        if($vaccineid<=0){
            return jsondata('0041','请选择疫苗');
        }
        //$field='id,title,remark,content,vaccage';
        //$map[]=['isshow','=','1'];
        //$map[]=['id','=',$vaccineid];
        //$info=DB::name('vaccine')->field($field)->where($map)->find();
        $map[]=['vi.vaccineid','=',$vaccineid];
        $map[]=['vi.meetingsiteid','=',$meetingsiteid];
        $map[]=['vi.isshow','=','1'];
        $field='v.id,v.title,v.content,v.remark,v.vaccage';
        $info=DB::name('vaccine_item vi')->field($field)->join('__VACCINE__ v','v.id=vi.vaccineid')->where($map)->find();
        if(empty($info)){
            return jsondata('0041','请选择疫苗');
        }
        $tmpages=json_decode($info['vaccage'],true);
        if($info['remark']==''){
            $remark='接种年龄：';
            if(!empty($tmpages)){
                foreach($tmpages as $agv){
                    if($agv['ages_year']>0 && $agv['ages_month']>0){
                            $remark.=$agv['ages_year'].'岁'.$agv['ages_month'].'月;';
                        }elseif($agv['ages_year']>0 && $agv['ages_month']<=0){
                             $remark.=$agv['ages_year'].'岁;';
                        }elseif($agv['ages_year']<=0 && $agv['ages_month']>0){
                            $remark.=$agv['ages_month'].'月;';
                        }
                    // if($agv['ages_year']>0){
                    //     $remark.=$agv['ages_year'].'岁';
                    // }
                    // if($agv['ages_month']>0){
                    //     $remark.=$agv['ages_month'].'月;';
                    // }
                }
            }
            $info['remark']=trim($remark,';');
            //$v['remark']="接种年龄在".$v['minyear']."年".$v['minmonth']."月到".$v['maxyear']."年".$v['maxmonth']."月";
        }
        unset($info['vaccage']);
        // $tmpminage=explode('_',$info['minage']);
        // $tmpmaxage=explode('_',$info['maxage']);
        // if(!isset($tmpminage[0]) || $tmpminage[0]==''){
        //     $tmpminage[0]=0;
        // }
        // if(!isset($tmpminage[1]) || $tmpminage[1]==''){
        //     $tmpminage[1]=0;
        // }
        // if(!isset($tmpmaxage[0]) || $tmpmaxage[0]==''){
        //     $tmpmaxage[0]=0;
        // }
        // if(!isset($tmpmaxage[1]) || $tmpmaxage[1]==''){
        //     $tmpmaxage[1]=0;
        // }
        // $info['minyear']=$tmpminage[0];
        // $info['minmonth']=$tmpminage[1];
        // $info['maxyear']=$tmpmaxage[0];
        // $info['maxmonth']=$tmpmaxage[1];
        // if($info['remark']==''){
        //     $info['remark']="接种年龄在".$info['minyear']."年".$info['minmonth']."月到".$info['maxyear']."年".$info['maxmonth']."月";
        // }
        // unset($info['minage'],$info['maxage']);
        $info['content']=htmlspecialchars_decode($info['content']);
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //提交预约信息
    public function appointment_deal(){
        if(request()->isPost() || request()->isAjax()){
            $indexservice=new IndexService();
            return $indexservice->appoint_deal($this->base_meetinguser);
        }
        return jsondata('0004','网络错误');
    }

    //预约信息列表
    public function appointment_list(){
        // $msgconfig['aliaccesskeyid']=config('aliaccesskeyid');
        // $msgconfig['aliaccesskeysecret']=config('aliaccesskeysecret');
        // $msgdata['mobile']='18825955333';
        // $msgdata['name']='植哥哥';
        // base_sendmsg($msgconfig,$msgdata,1);
        $start=input('start','0','intval');
        $limit=input('limit','10','intval');
        $keyword=input('keyword','','trim');
        $list=[];
        $count=0;
        $map=[];
        $map[]=['mr.meetstyle','=',1];
        $map[]=['mr.openid','=',$this->base_meetinguser['openid']];
        if($keyword!=''){
            $map[]=['u.realname|mr.bookcode','=',"%$keyword%"];
        }
        $field='mr.id,mr.meetingsiteid,mr.vaccinename,mr.bookcode,mr.codeimg,mr.meetingdate,mr.meetingtstarttime as starttime,mr.meetingendtime as endtime,mr.status,mr.signtime,mr.canceltime,mr.expiretime,u.realname';
        $indexservice=new IndexService();
        $listdata=$indexservice->getappoint_list($map,$field,1,$start,$limit,['mr.status'=>'asc','mr.id'=>'asc']);
        $count=$listdata['count'];
        $list=$listdata['list'];
        if(!empty($list)){
            foreach($list as &$v){
                if($v['status']==1){
                    $extime=strtotime($v['meetingdate'].' '.$v['endtime'])+59+60*15;
                    if(time()>$extime){
                        $v['status']=4;
                        $v['expiretime']=date('Y-m-d H:i:s');
                        DB::name('meeting_record')->where([['id','=',$v['id']],['status','=','1']])->update(['status'=>4,'expiretime'=>date('Y-m-d H:i:s'),'upsource'=>'sys_1']);
                    }
                }
                if($v['codeimg']!=''){
                    $v['codeimg']=$this->weburl.$v['codeimg'];
                }
            }
        }
        $data['count']=$count;
        $data['data']=$list;
        return jsondata('0001','获取成功',$data);
    }

    //预约信息详情
    public function appointment_detail(){
        $meetingid=input('meetingid','0','intval');
        $bookcode=input('bookecode','','trim');
        if($meetingid<=0 && $bookcode==''){
            return jsondata('0051','请选择预约信息');
        }
        $map[]=['mr.meetstyle','=',1];
        $map[]=['mr.openid','=',$this->base_meetinguser['openid']];
        if($meetingid>0 && $bookcode==''){
            $map[]=['mr.id','=',$meetingid];
        }elseif($meetingid<=0 && $bookcode!=''){
            $map[]=['bookcode','=',$bookcode];
        }elseif($meetingid>0 && $bookcode!=''){
            $map[]=['mr.id','=',$meetingid];
            $map[]=['mr.bookcode','=',$bookcode];
        }
        $field='mr.id,mr.meetingsiteid,mr.vaccinename,mr.bookcode,mr.codeimg,mr.meetingdate,mr.meetingtstarttime as starttime,mr.meetingendtime as endtime,mr.status,mr.signtime,mr.canceltime,mr.expiretime,u.realname';
        $indexservice=new IndexService();
        $info=$indexservice->getappoint_detail($map,$field);
        if($info['codeimg']!=''){
            $info['codeimg']=$this->weburl.$info['codeimg'];
        }
        $data['data']=$info;
        return jsondata('0001','获取成功',$data);
    }

    //取消预约
    public function appointment_cancel(){
        if(request()->isPost() || request()->isAjax()){
            $meetingid=input('meetingid','0','intval');
            if($meetingid<=0){
                return jsondata('0051','请选择预约信息');
            }
            $indexservice=new IndexService();
            return $indexservice->appoint_cancel($this->base_meetinguser,$meetingid,3);
        }
        return jsondata('0004','网络错误');
    }

    //删除预约信息
    public function appointment_del(){
        if(request()->isPost() || request()->isAjax()){
            $meetingid=input('meetingid','','trim');
            if($meetingid==''){
                return jsondata('0051','请选择预约信息');
            }
            $meetingid=explode(',',trim($meetingid,','));
            $indexservice=new IndexService();
            return $indexservice->appoint_delete($this->base_meetinguser,$meetingid);
        }
        return jsondata('0004','网络错误');
    }

    //获取预约站点列表
    public function meetingsite_list(){
        $map=[];
        $map[]=['isshow','=',1];
        $field='id,title,mobile,address,lng,lat';
        $indexservice=new IndexService();
        $list=$indexservice->getMeetingsiteList(2,$map,$field,20,[],['orderby'=>'desc','id'=>'asc']);
        $data['data']=$list['list'];
        return jsondata('0001','获取成功',$data);
    }

    //获取预约站点详情
    public function meetingsite_detail(){
        $meetingsiteid=input('meetingsiteid','0','intval');
        if($meetingsiteid<=0){
            return jsondata('0012','请选择预约站点');
        }
        $indexservice=new IndexService();
        $map=[];
        $map[]=['id','=',$meetingsiteid];
        $map[]=['isshow','=',1];
        $field='id,title,mobile,address,lng,lat';
        $info=$indexservice->getMeetingsiteDetail($map,$field);
        if(empty($info)){
            return jsondata('0013','选择的预约站点不存在');
        }
        $data['data']=$info;
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
