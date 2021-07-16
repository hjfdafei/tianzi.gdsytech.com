<?php
namespace app\api\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\api\service\QrcodeService;
//预约管理
class IndexService extends Base{
    //完善家长信息
    public function parentinfo_verify($openid){
        $realname=input('post.realname','','trim');
        $idcardnum=input('post.idcardnum','','trim');
        $mobile=input('post.mobile','','trim');
        $address=input('post.address','','trim');
        // $proveimg_front=input('post.proveimg_front','','trim');
        // $proveimg_back=input('post.proveimg_back','','trim');
        if($realname==''){
            return jsondata('0011','请输入家长姓名');
        }
        if($idcardnum==''){
            return jsondata('0012','请输入家长身份证号码');
        }
        if($mobile==''){
            return jsondata('0013','请输入家长联系电话');
        }
        $ckmobileres=checkformat_mobile($mobile);
        if($ckmobileres['code']!='0001'){
            return jsondata('0013','请输入家长联系电话');
        }
        if($address==''){
            return jsondata('0014','请输入家长居住地址');
        }
        $hasinfo=$this->parentinfo4idcardnum($idcardnum);
        if(!empty($hasinfo)){
            if($hasinfo['openid']!=$openid){
                return jsondata('0012','该家长身份证号码已被绑定');
            }
        }
        $hasinfo=$this->parentinfo4mobile($mobile);
        if(!empty($hasinfo)){
            if($hasinfo['openid']!=$openid){
                return jsondata('0013','该联系号码已被绑定');
            }
        }
        $data['realname']=$realname;
        $data['idcardnum']=$idcardnum;
        $data['mobile']=$mobile;
        $data['address']=$address;
        $info=$this->parentinfo4openid($openid);
        if(empty($info)){
            // if($proveimg_front==''){
            //     return jsondata('0015','请上传户口本户主页或者居住证正面');
            // }
            // $urlhead=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
            // $fpath='';
            // if(substr($proveimg_front,strlen($urlhead))!=''){
            //     $fpath='.'.substr($proveimg_front,strlen($urlhead));
            // }
            // if(!file_exists($fpath) || $fpath==''){
            //     return jsondata('0015','请上传户口本本人页或者居住证反面');
            // }
            // if($proveimg_back==''){
            //     return jsondata('0016','请上传户口本本人页或者居住证反面');
            // }
            // $urlhead=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
            // $bpath='';
            // if(substr($proveimg_back,strlen($urlhead))!=''){
            //     $bpath='.'.substr($proveimg_back,strlen($urlhead));
            // }
            // if(!file_exists($bpath) || $bpath==''){
            //     return jsondata('0016','请上传户口本本人页或者居住证反面');
            // }
            $data['openid']=$openid;
            $data['parentid']=0;
            //$data['proveimg_front']=$proveimg_front;
            //$data['proveimg_back']=$proveimg_back;
            $data['status']=1;
            $data['type']=1;
            $data['create_time']=date('Y-m-d H:i:s');
            $res=DB::name('meeting_user')->insertGetId($data);
        }else{
            // if($proveimg_front!=''){
            //     $urlhead=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
            //     $fpath='';
            //     if(substr($proveimg_front,strlen($urlhead))!=''){
            //         $fpath='.'.substr($proveimg_front,strlen($urlhead));
            //     }
            //     if(!file_exists($fpath) || $fpath==''){
            //         return jsondata('0015','请上传户口本户主页或者居住证正面');
            //     }
            //     $data['proveimg_front']=$proveimg_front;
            //     if($info['proveimg_front']!=$proveimg_front){
            //         $data['status']=2;
            //     }
            // }
            // if($proveimg_back!=''){
            //     $urlhead=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
            //     $bpath='';
            //     if(substr($proveimg_back,strlen($urlhead))!=''){
            //         $bpath='.'.substr($proveimg_back,strlen($urlhead));
            //     }
            //     if(!file_exists($bpath) || $bpath==''){
            //         return jsondata('0016','请上传户口本本人页或者居住证反面');
            //     }
            //     $data['proveimg_back']=$proveimg_back;
            //     if($info['proveimg_back']!=$proveimg_back){
            //         $data['status']=2;
            //     }
            // }
            // if($info['realname']!=$realname || $info['idcardnum']!=$idcardnum || $info['mobile']!=$mobile || $info['address']!=$address){
            //     $data['status']=2;
            // }
            $data['update_time']=date('Y-m-d H:i:s');
            $res=DB::name('meeting_user')->where([['id','=',$info['id']]])->update($data);
        }
        if($res){
            return jsondata('0001','保存信息成功');
        }else{
            return jsondata('0019','保存信息失败,请重试');
        }
    }

    //宝宝信息列表
    public function getbaby_list($map=[],$field='*',$type=1,$start=0,$limit=10,$orderby=['id'=>'asc']){
        $list=array();
        $count=0;
        $map2[]=['type','=','2'];
        $count=DB::name('meeting_user')->where($map)->where($map2)->count();
        if($type==1){
            $list=DB::name('meeting_user')->field($field)->where($map)->where($map2)->order($orderby)->limit($start,$limit)->select();
        }else{
            $list=DB::name('meeting_user')->field($field)->where($map)->where($map2)->order($orderby)->select();
        }
        $data['list']=$list;
        $data['count']=$count;
        return $data;
    }

    //添加/修改宝宝信息
    public function baby_verify($parentid,$babyid=0){
        $realname=input('post.realname','','trim');
        $sex=input('post.sex','','trim');
        $birthday=input('post.birthday','','trim');
        $proveimg_front=input('post.proveimg_front','','trim');
        $proveimg_back=input('post.proveimg_back','','trim');
        if($realname==''){
            return jsondata('0021','请输入宝宝姓名');
        }
        $data['realname']=$realname;
        $info=[];
        if($babyid>0){
            $info=$this->babyinfo4id($parentid,$babyid);
        }
        if(empty($info)){
            if(!in_array($sex,['男','女'])){
                return jsondata('0022','请选择宝宝性别');
            }
            if($birthday==''){
                return jsondata('0023','请选择宝宝出生日期');
            }
            if($birthday>date('Y-m-d')){
                return jsondata('0023','请选择宝宝出生日期');
            }

            if($proveimg_front==''){
                return jsondata('0024','请上传家长信息证明');
            }
            $urlhead=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
            $fpath='';
            if(substr($proveimg_front,strlen($urlhead))!=''){
                $fpath='.'.substr($proveimg_front,strlen($urlhead));
            }
            if(!file_exists($fpath) || $fpath==''){
                return jsondata('0024','请上传家长信息证明');
            }
            if($proveimg_back==''){
                return jsondata('0025','请上传宝宝信息证明');
            }
            $urlhead=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
            $bpath='';
            if(substr($proveimg_back,strlen($urlhead))!=''){
                $bpath='.'.substr($proveimg_back,strlen($urlhead));
            }
            if(!file_exists($bpath) || $bpath==''){
                return jsondata('0025','请上传宝宝信息证明');
            }

            $data['sex']=$sex;
            $data['birthday']=$birthday;
            $data['parentid']=$parentid;
            $data['status']=2;
            $data['type']=2;
            $data['proveimg_front']=$proveimg_front;
            $data['proveimg_back']=$proveimg_back;
            $data['create_time']=date('Y-m-d H:i:s');
            $res=DB::name('meeting_user')->insertGetId($data);
        }else{
            if($proveimg_front!=''){
                $urlhead=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
                $fpath='';
                if(substr($proveimg_front,strlen($urlhead))!=''){
                    $fpath='.'.substr($proveimg_front,strlen($urlhead));
                }
                if(!file_exists($fpath) || $fpath==''){
                    return jsondata('0024','请上传家长信息证明');
                }
                $data['proveimg_front']=$proveimg_front;
                if($info['proveimg_front']!=$proveimg_front){
                    $data['status']=2;
                }
            }
            if($proveimg_back!=''){
                $urlhead=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'];
                $bpath='';
                if(substr($proveimg_back,strlen($urlhead))!=''){
                    $bpath='.'.substr($proveimg_back,strlen($urlhead));
                }
                if(!file_exists($bpath) || $bpath==''){
                    return jsondata('0025','请上传宝宝信息证明');
                }
                $data['proveimg_back']=$proveimg_back;
                if($info['proveimg_back']!=$proveimg_back){
                    $data['status']=2;
                }
            }
            if($info['realname']!=$realname){
                $data['status']=2;
            }
            $data['update_time']=date('Y-m-d H:i:s');
            $res=DB::name('meeting_user')->where([['id','=',$info['id']]])->update($data);
        }
        if($res){
            return jsondata('0001','保存信息成功');
        }else{
            return jsondata('0029','保存信息失败,请重试');
        }
    }

    //删除宝宝信息
    public function baby_delete($parentinfo,$babyid){
        $delbabyid=array();
        $hasadminbabyid=array();
        $delimgs=[];
        foreach($babyid as $v){
            $info=$this->babyinfo4id($parentinfo['id'],intval($v));
            if(!empty($info)){
                $delbabyid[]=$info['id'];
                $delimgs[]=['proveimg_front'=>$info['proveimg_front'],'proveimg_back'=>$info['proveimg_back']];
            }
        }
        if(empty($delbabyid)){
            return jsondata('0038','请选择要删除的宝宝信息');
        }
        $map=array();
        $map[]=['id','in',$delbabyid];
        $map[]=['parentid','=',$parentinfo['id']];
        $map[]=['type','=','2'];
        $res=DB::name('meeting_user')->where($map)->delete();
        if($res){
            $map=[];
            $map[]=['memberid','in',$delbabyid];
            $map[]=['openid','=',$parentinfo['openid']];
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
            return jsondata('0001','删除宝宝信息成功');
        }else{
            return jsondata('0039','删除宝宝信息失败,请重试');
        }
    }

    //获取家长信息 通过openid获取家长信息
    public function parentinfo4openid($openid,$field='*'){
        $info=[];
        if($openid!=''){
            $map=[];
            $map[]=['openid','=',$openid];
            $map[]=['parentid','=',0];
            $info=DB::name('meeting_user')->field($field)->where($map)->find();
        }
        return $info;
    }

    //获取家长信息 通过身份证号获取家长信息
    public function parentinfo4idcardnum($idcardnum,$field='*'){
        $info=[];
        if($idcardnum!=''){
            $map=[];
            $map[]=['idcardnum','=',$idcardnum];
            $map[]=['parentid','=',0];
            $info=DB::name('meeting_user')->field($field)->where($map)->find();
        }
        return $info;
    }

    //获取家长信息 通过手机号码获取家长信息
    public function parentinfo4mobile($mobile,$field='*'){
        $info=[];
        if($mobile!=''){
            $map=[];
            $map[]=['mobile','=',$mobile];
            $map[]=['parentid','=',0];
            $info=DB::name('meeting_user')->field($field)->where($map)->find();
        }
        return $info;
    }

    //获取宝宝信息
    public function babyinfo4id($parentid,$babyid,$field='*'){
        $info=[];
        $map[]=['id','=',$babyid];
        $map[]=['parentid','=',$parentid];
        $map[]=['type','=','2'];
        $info=DB::name('meeting_user')->field($field)->where($map)->find();
        return $info;
    }

    //获取日期 $days_num天数 7为一个星期 31为一个月
    public function getdatelist($days_num){
        $cnweek=array('周日','周一','周二','周三','周四','周五','周六');
        $oneweek=array();
        $allnum=0;
        foreach(config('fixtime') as $tv){
            $allnum+=$tv['num'];
        }
        for($i=0;$i<=$days_num;$i++){
            //已预约人数 当天可预约总人数
            $map=[];
            $map[]=['meetstyle','=',1];
            $map[]=['noymd','=',date('Ymd',time()+60*60*24*$i)];
            $hasnodate=DB::name('meeting_nodates')->where($map)->find();
            if(!empty($hasnodate)){
                $hasnumber=0;
            }else{
                $map=[];
                $map[]=['meetstyle','=',1];
                $map[]=['type','=',1];
                $commonsetting=DB::name('meeting_timesetting')->where($map)->find();
                if(!empty($commonsetting)){
                    $allnum=DB::name('meeting_timesetting')->where($map)->sum('meetnum');
                }
                $map=[];
                $map[]=['meetstyle','=',1];
                $map[]=['type','=',2];
                $map[]=['typeid','=',date('l',time()+60*60*24*$i)];
                $singlesetting=DB::name('meeting_timesetting')->where($map)->find();
                if(!empty($singlesetting)){
                    $allnum=DB::name('meeting_timesetting')->where($map)->sum('meetnum');
                    $allnum=DB::name('meeting_timesetting')->where($map)->sum('meetnum');
                }
                $map2=[];
                $map2[]=['meetstyle','=',1];
                $map2[]=['type','=',3];
                $map2[]=['typeid','=',date('Y-m-d',time()+60*60*24*$i)];
                $singlesetting2=DB::name('meeting_timesetting')->where($map2)->find();
                if(!empty($singlesetting2)){
                    $allnum=DB::name('meeting_timesetting')->where($map2)->sum('meetnum');
                }
                $map=[];
                $map[]=['meetingymd','=',date('Ymd',time()+60*60*24*$i)];
                $map[]=['status','in',[1,3,4]];
                $meetnum=DB::name('meeting_record')->where($map)->sum('meetingnum');
                $hasnumber=0;
                if($allnum>$meetnum){
                    $hasnumber=1;
                }
            }
            $isnow=0;
            if(date('Ymd',time()+60*60*24*$i)==date('Ymd')){
                $isnow=1;
            }
            $oneweek[]=array('weekdate'=>date('d',time()+60*60*24*$i),'weekname'=>$cnweek[date('w',time()+60*60*24*$i)],'datestr'=>date('Ymd',time()+60*60*24*$i),'datestr2'=>date('Y-m-d',time()+60*60*24*$i),'hasnumber'=>$hasnumber,'isnow'=>$isnow);
        }
        return $oneweek;
    }

    //获取某天每个时段可预约数量 date_detail为具体日期 2020-05-20
    public function gettimenum($date_detail){
        //当天各时段剩余预约数量 $timenum当天最大预约数量 $datemeetnum当天已预约数量
        $timedata=[];
        $map=[];
        $map[]=['meetstyle','=',1];
        $map[]=['noymd','=',date('Ymd',strtotime($date_detail))];
        $hasnodate=DB::name('meeting_nodates')->where($map)->find();
        if(!empty($hasnodate)){
            foreach(config('fixtime') as $tv){
                $timedata[]=[
                    'id'=>$tv['id'],
                    'starttime'=>$tv['start'],
                    'endtime'=>$tv['end'],
                    'num'=>0,
                ];
            }
        }else{
            foreach(config('fixtime') as $tv){
                $timenum=$tv['num'];
                $map=[];
                $map[]=['meetstyle','=',1];
                $map[]=['type','=',2];
                $map[]=['timeid','=',$tv['id']];
                $map[]=['typeid','=',date('l',strtotime($date_detail))];
                $singlesettinginfo=DB::name('meeting_timesetting')->where($map)->find();
                if($singlesettinginfo){
                    $timenum=$singlesettinginfo['meetnum'];
                }
                $map2=[];
                $map2[]=['meetstyle','=',1];
                $map2[]=['type','=',3];
                $map2[]=['timeid','=',$tv['id']];
                $map2[]=['typeid','=',date('Y-m-d',strtotime($date_detail))];
                $singlesettinginfo2=DB::name('meeting_timesetting')->where($map2)->find();
                if($singlesettinginfo2){
                    $timenum=$singlesettinginfo2['meetnum'];
                }
                if(empty($singlesettinginfo) && empty($singlesettinginfo2)){
                    $map=[];
                    $map[]=['meetstyle','=',1];
                    $map[]=['type','=',1];
                    $map[]=['timeid','=',$tv['id']];
                    $settinginfo=DB::name('meeting_timesetting')->where($map)->find();
                    if(!empty($settinginfo)){
                        $timenum=$settinginfo['meetnum'];
                    }
                }
                $map=[];
                $map[]=['meetingymd','=',date('Ymd',strtotime($date_detail))];
                $map[]=['status','in',[1,3,4]];
                $map[]=['meetingtimeid','=',$tv['id']];
                $datemeetnum=DB::name('meeting_record')->where($map)->sum('meetingnum');
                $timedata[]=[
                    'id'=>$tv['id'],
                    'starttime'=>$tv['start'],
                    'endtime'=>$tv['end'],
                    'num'=>$timenum-$datemeetnum,
                ];
            }
        }
        return $timedata;
    }

    //获取某天的具体可预约数量
    public function getbookablenum($datestr,$timeid=0){
        $ablenum=0;
        $num=0;
        $map=[];
        $map[]=['meetstyle','=','1'];
        $map[]=['noymd','=',date('Ymd',strtotime($datestr))];
        $hasnodate=DB::name('meeting_nodates')->where($map)->find();
        if(empty($hasnodate)){
            if($timeid>0){
                foreach(config('fixtime') as $tv){
                    if($tv['id']==$timeid){
                        $num=$tv['num'];
                    }
                }
            }else{
                foreach(config('fixtime') as $tv){
                    $num+=$tv['num'];
                }
            }
            $map=[];
            $map[]=['meetstyle','=',1];
            $map[]=['type','=',2];
            $map[]=['typeid','=',date('l',strtotime($datestr))];
            if($timeid>0){
                $map[]=['timeid','=',$timeid];
            }
            $singlesettinginfo=DB::name('meeting_timesetting')->where($map)->find();
            if(!empty($singlesettinginfo)){
                $num=DB::name('meeting_timesetting')->where($map)->sum('meetnum');
            }
            $map2=[];
            $map2[]=['meetstyle','=',1];
            $map2[]=['type','=',2];
            $map2[]=['typeid','=',date('Y-m-d',strtotime($datestr))];
            if($timeid>0){
                $map2[]=['timeid','=',$timeid];
            }
            $singlesettinginfo2=DB::name('meeting_timesetting')->where($map2)->find();
            if(!empty($singlesettinginfo2)){
                $num=DB::name('meeting_timesetting')->where($map2)->sum('meetnum');
            }
            if(empty($singlesettinginfo) && empty($singlesettinginfo2)){
                $map=[];
                $map[]=['meetstyle','=',1];
                $map[]=['type','=',1];
                if($timeid>0){
                    $map[]=['timeid','=',$timeid];
                }
                $settinginfo=DB::name('meeting_timesetting')->where($map)->find();
                if(!empty($settinginfo)){
                    $num=DB::name('meeting_timesetting')->where($map)->sum('meetnum');
                }
            }
            $map=[];
            $map[]=['meetingymd','=',date('Ymd',strtotime($datestr))];
            $map[]=['status','in',[1,3,4]];
            if($timeid>0){
                $map[]=['meetingtimeid','=',$timeid];
            }
            $datemeetnum=DB::name('meeting_record')->where($map)->sum('meetingnum');
            $ablenum=$num-$datemeetnum;
        }
        return $ablenum;
    }

    //提交预约数据处理
    public function appoint_deal($parentinfo){
        $timenamearr=[];
        $timeidarr=[];
        foreach(config('fixtime') as $v){
            $timeidarr[]=$v['id'];
            $timenamearr[$v['id']]=array($v['start'],$v['end']);
        }
        $meetingdate=input('post.meetingdate',date('Y-m-d'),'trim');
        $meetingtimeid=input('post.meetingtimeid','0','intval');
        $babyid=input('post.babyid','','trim');
        $vaccineid=input('post.vaccineid','','trim');
        // if($parentinfo['status']!=1){
        //     return jsondata('0057','抱歉,你提交的信息正在审核中,暂时不能进行线上预约,感谢你的关注');
        // }
        if($meetingdate==''){
            return jsondata('0051','请选择预约日期');
        }
        if($meetingdate<date('Y-m-d')){
            return jsondata('0051','请选择预约日期');
        }
        if(strtotime($meetingdate)>time()+60*60*24*31){
            return jsondata('0051','只能预约一个月的日期,请知悉');
        }
        if($meetingtimeid<=0){
            return jsondata('0052','请选择预约时间段');
        }
        if(!in_array($meetingtimeid,$timeidarr)){
            return jsondata('0052','请选择预约时间段');
        }
        if(strtotime($meetingdate.' '.$timenamearr[$meetingtimeid][1])<time()){
            return jsondata('0052','你选择预约时间段'.$timenamearr[$meetingtimeid][0].'-'.$timenamearr[$meetingtimeid][1].'已过');
        }
        if($babyid==''){
            return jsondata('0053','请选择预约的宝宝');
        }
        if($vaccineid==''){
            return jsondata('0054','请选择预约的疫苗');
        }
        $babyid=explode(',',trim($babyid,','));
        $vaccineid=explode(',',trim($vaccineid,','));
        if(empty($babyid)){
            return jsondata('0053','请选择预约的宝宝');
        }
        if(empty($vaccineid)){
            return jsondata('0054','请选择预约的疫苗');
        }
        if(count($babyid)!=count($vaccineid)){
            return jsondata('0055','请为宝宝选择预约的疫苗');
        }
        //先查一次黑名单，把条件符合的加进黑名单，直接返回预约不成功
        $meetingconfig=DB::name('meeting_config')->where([['type','=',1],['id','=',1]])->find();
        $map=[];
        $map[]=['type','=','1'];
        $map[]=['openid','=',$parentinfo['openid']];
        $blackinfo=DB::name('meeting_black')->where($map)->find();
        if(!empty($blackinfo)){
            if($blackinfo['isjoin']==1){
                if($blackinfo['outtime']>date('Y-m-d H:i:s')){
                    return jsondata('0760','号源紧缺,暂无法预约,如多次无法预约不上请联系工作人员');
                }else{
                    DB::name('meeting_black')->where([['id','=',$blackinfo['id']]])->update(['isjoin'=>'2','outnum'=>$blackinfo['outnum']+1,'outtime'=>date('Y-m-d H:i:s'),'removetime'=>date('Y-m-d H:i:s')]);
                }
            }else{
                if($meetingconfig['missnum']>0){
                    $map=[];
                    $map[]=['openid','=',$parentinfo['openid']];
                    $map[]=['status','=',4];
                    $map[]=['create_time','>',$blackinfo['outtime']];
                    $miss_meetingnum=DB::name('meeting_record')->where($map)->count();
                    if($miss_meetingnum>=$meetingconfig['missnum']){
                        $lastmeetinginfo=DB::name('meeting_record')->where($map)->order(['id'=>'desc'])->find();
                        DB::name('meeting_black')->where([['id','=',$blackinfo['id']]])->update(['isjoin'=>1,'jointime'=>date('Y-m-d H:i:s',strtotime($lastmeetinginfo['meetingdate'])+60*60*24),'joinnum'=>$blackinfo['joinnum']+1,'outtime'=>date('Y-m-d H:i:s',strtotime($lastmeetinginfo['meetingdate'])+60*60*24+60*60*24*$meetingconfig['limitdaynum'])]);
                        return jsondata('0760','号源紧缺,暂无法预约,如多次无法预约不上请联系工作人员');
                    }
                }
            }
        }else{
            if($meetingconfig['missnum']>0){
                $map=[];
                $map[]=['openid','=',$parentinfo['openid']];
                $map[]=['status','=',4];
                $miss_meetingnum=DB::name('meeting_record')->where($map)->count();
                if($miss_meetingnum>=$meetingconfig['missnum']){
                    $lastmeetinginfo=DB::name('meeting_record')->where($map)->order(['id'=>'desc'])->find();
                    DB::name('meeting_black')->insert(['type'=>1,'openid'=>$parentinfo['openid'],'idcardnum'=>$parentinfo['idcardnum'],'mobile'=>$parentinfo['mobile'],'isjoin'=>1,'jointime'=>date('Y-m-d H:i:s',strtotime($lastmeetinginfo['meetingdate'])+60*60*24),'outtime'=>date('Y-m-d H:i:s',strtotime($lastmeetinginfo['meetingdate'])+60*60*24+60*60*24*$meetingconfig['limitdaynum']),'joinnum'=>1,'create_time'=>date('Y-m-d H:i:s',strtotime($lastmeetinginfo['meetingdate'])+60*60*24)]);
                    return jsondata('0760','号源紧缺,暂无法预约,如多次无法预约不上请联系工作人员');
                }
            }
        }

        if($meetingconfig['isopen']==2){
            if(date('Y-m-d H:i:s')<$meetingconfig['close_endtime'] && date('Y-m-d H:i:s')>$meetingconfig['close_starttime']){
                return jsondata('0056',date('Y-m-d H:i',strtotime($meetingconfig['close_starttime'])).'至'.date('Y-m-d H:i',strtotime($meetingconfig['close_endtime'])).'暂不开放预约');
            }
        }
        $map=[];
        $map[]=['meetstyle','=','1'];
        $map[]=['noymd','=',date('Ymd',strtotime($meetingdate))];
        $hasnodate=DB::name('meeting_nodates')->where($map)->find();
        if(!empty($hasnodate)){
            return jsondata('0056','今天暂不开放预约,请选择别的日期');
        }
        $today_meetingnum=$this->getbookablenum($meetingdate,0);
        if($today_meetingnum<=0){
            return jsondata('0056','今天预约已满,请选择别的日期');
        }
        $today_meetingtimenum=$this->getbookablenum($meetingdate,$meetingtimeid);
        if($today_meetingtimenum<=0){
            return jsondata('0056','选择的时段'.implode('-',$timenamearr[$meetingtimeid]).'预约已满,请选择别的时段');
        }
        DB::startTrans();
        $jnum=0;
        for($i=0;$i<count($babyid);$i++){
            $map=[];
            $map[]=['id','=',$babyid[$i]];
            $map[]=['parentid','=',$parentinfo['id']];
            $map[]=['type','=',2];
            $babyinfo=DB::name('meeting_user')->where($map)->find();
            if(empty($babyinfo)){
                exitdata('0058','宝宝信息不存在');
            }
            if($babyinfo['status']!=1){
                return jsondata('0058','抱歉,宝宝信息正在审核中,暂时不能进行线上预约,感谢你的关注');
            }
            //$babymonthnum=ceil((strtotime(date('Y-m-d 00:00:00'))-strtotime($babyinfo['birthday']))/(60*60*24));
            $birthday2num=ymd4date($babyinfo['birthday']);
            //echo ptr($birthday2num);daynum
            //exit;
            $map=[];
            $map[]=['id','=',$vaccineid[$i]];
            $map[]=['isshow','=','1'];
            $vaccineinfo=DB::name('vaccine')->where($map)->find();
            if(empty($vaccineinfo)){
                exitdata('0059','选择预约的疫苗不存在,请刷新页面重试');
            }
            if($vaccineinfo['stock']<=0){
                exitdata('0059','选择预约的疫苗库存不足,请继续关注');
            }
            // $vaccage=json_decode($vaccineinfo['vaccage'],true);
            // if(empty($vaccage)){
            //     exitdata('0059','选择预约的疫苗不存在,请继续关注');
            // }
            // $vaccagearr=[];
            // foreach($vaccage as $vav){
            //     $vaccagearr[]=$vav['ages_year']*365+$vav['ages_month']*30;
            // }
            // sort($vaccagearr);
            // $rightage_min=$vaccagearr[0];
            // $rightage_max=$vaccagearr[(count($vaccagearr)-1)];
            // if($birthday2num['daynum']+30*2<$rightage_min || $birthday2num['daynum']>$rightage_max+30*2){
            //     exitdata('0059','宝宝'.$babyinfo['realname'].'不能预约疫苗'.$vaccineinfo['title']);
            // }

            // $map=[];
            // $map[]=['openid','=',$parentinfo['openid']];
            // $map[]=['memberid','=',$babyid[$i]];
            // $map[]=['create_time','>=',date('Y-m-d 00:00:00',time())];
            // $map[]=['create_time','<=',date('Y-m-d 23:59:59',time())];
            // $map[]=['status','in',[1,2]];
            // $meetinginfo=DB::name('meeting_record')->where($map)->find();
            // if(!empty($meetinginfo)){
            //     exitdata('0059','家长'.$parentinfo['realname'].'你今天已经为宝宝'.$babyinfo['realname'].'预约过了,家长同一天内能为多名宝宝预约,但是同一名宝宝一天只能预约一次');
            // }
            $map=[];
            $map[]=['openid','=',$parentinfo['openid']];
            $map[]=['memberid','=',$babyid[$i]];
            $map[]=['meetingymd','=',date('Ymd',strtotime($meetingdate))];
            $map[]=['status','in',[1,2]];
            $meetinginfo=DB::name('meeting_record')->where($map)->find();
            if(!empty($meetinginfo)){
                exitdata('0059','宝宝'.$babyinfo['realname'].'已经预约过'.date('Y-m-d',strtotime($meetingdate)).'了');
            }
            $map=[];
            $map[]=['openid','=',$parentinfo['openid']];
            $map[]=['memberid','=',$babyid[$i]];
            $map[]=['vaccineid','=',$vaccineid[$i]];
            $map[]=['status','in',[1,2]];
            $meetinginfo=DB::name('meeting_record')->where($map)->find();
            if(!empty($meetinginfo)){
                exitdata('0059','宝宝'.$babyinfo['realname'].'已经预约过疫苗'.$vaccineinfo['title'].'了');
            }
            $bookcode=$this->getbookcode();
            $data=[
                'openid'=>$parentinfo['openid'],
                'memberid'=>$babyid[$i],
                'vaccineid'=>$vaccineid[$i],
                'vaccinename'=>$vaccineinfo['title'],
                'bookcode'=>$bookcode,
                'meetingdate'=>date('Y-m-d',strtotime($meetingdate)),
                'meetingtstarttime'=>$timenamearr[$meetingtimeid][0],
                'meetingendtime'=>$timenamearr[$meetingtimeid][1],
                'meetingtimeid'=>$meetingtimeid,
                'meetingnum'=>1,
                'meetingymd'=>date('Ymd',strtotime($meetingdate)),
                'meetingymdh'=>date('YmdH',strtotime($meetingdate.' '.$timenamearr[$meetingtimeid][0])),
                'status'=>1,
                'create_time'=>date('Y-m-d H:i:s'),
            ];
            $res=DB::name('meeting_record')->insertGetId($data);
            if($res){
                //生成二维码然后保存
                $qr_url =$bookcode;
                $file_name = './upload/qrcode/'.date('Ymd');
                if(!is_dir($file_name)){
                    mkdir($file_name,0777,true);
                }
                $codeconfig['file_name'] = $file_name;
                $codeconfig['generate']  = 'writefile';
                $codeconfig['size']=640;

                $qr_code = new QrcodeService($codeconfig);
                $rs = $qr_code->createServer($qr_url);
                if($rs['success']==1){
                    $codeurl=trim(str_replace("\\",'/',$rs['data']['url']),'.');
                    DB::name('meeting_record')->where([['id','=',$res]])->update(['codeimg'=>$codeurl]);
                }
                $res2=DB::name('vaccine')->where([['id','=',$vaccineinfo['id']]])->update(['stock'=>$vaccineinfo['stock']-1]);
                if($res2){
                    $jnum++;
                }
            }
        }
        if($jnum>0 && $jnum==count($babyid)){
            DB::commit();
            $msgconfig['aliaccesskeyid']=config('aliaccesskeyid');
            $msgconfig['aliaccesskeysecret']=config('aliaccesskeysecret');
            $msgdata['mobile']=$parentinfo['mobile'];
            $msgdata['name']=$parentinfo['realname'];
            $msgdata['datetime']=date('n月d日H时i分',strtotime($meetingdate.' '.$timenamearr[$meetingtimeid][0]));
            $msgdata['overnum']=$meetingconfig['missnum'];
            $msgdata['limitday']=$meetingconfig['limitdaynum'];
            base_sendmsg($msgconfig,$msgdata,4);//发短信通知
            return jsondata('0001','预约成功');
        }else{
            DB::rollback();
            return jsondata('0059','预约失败,请重试');
        }
    }

    //生成预约码
    public function getbookcode(){
        //$bookcode=mt_rand(0,99999999999);
        // $bookcode=$bookcode>=10?($bookcode>=100?($bookcode>=1000?($bookcode>=10000?($bookcode>=100000?($bookcode>=1000000?($bookcode>=10000000?$bookcode.'':''.$bookcode):'0'.$bookcode):'00'.$bookcode):'000'.$bookcode):'0000000'.$bookcode):'000000000'.$bookcode):'0000000000'.$bookcode;
        // $bookcode=$bookcode>=10?($bookcode>=100?($bookcode>=1000?($bookcode>=10000?($bookcode>=100000?($bookcode>=1000000?($bookcode>=10000000?($bookcode>=100000000?($bookcode>=1000000000?($bookcode>=10000000000?$bookcode:'0'.$bookcode):'00'.$bookcode):'000'.$bookcode):'0000'.$bookcode):'00000'.$bookcode):'000000'.$bookcode):'0000000'.$bookcode):'00000000'.$bookcode):'000000000'.$bookcode):'0000000000'.$bookcode;
        $bookcode=createnum4rand(mt_rand(0,99999999999),11);
        $map=[];
        $map[]=['bookcode','=',$bookcode];
        $meetinginfo=DB::name('meeting_record')->where($map)->find();
        if(!empty($meetinginfo)){
            $this->getbookcode();
        }
        return $bookcode;
    }

    //宝宝疫苗预约列表
    public function getappoint_list($map=[],$field='*',$type=1,$start=0,$limit=10,$orderby=['mr.id'=>'asc']){
        $list=array();
        $count=0;
        $count=DB::name('meeting_record mr')->join('__MEETING_USER__ u','u.id=mr.memberid','left')->where($map)->count();
        if($type==1){
            $list=DB::name('meeting_record mr')->field($field)->join('__MEETING_USER__ u','u.id=mr.memberid','left')->where($map)->order($orderby)->limit($start,$limit)->select();
        }else{
            $list=DB::name('meeting_record mr')->field($field)->join('__MEETING_USER__ u','u.id=mr.memberid','left')->where($map)->order($orderby)->select();
        }
        $data['list']=$list;
        $data['count']=$count;
        return $data;
    }

    //宝宝预约详情
    public function getappoint_detail($map=[],$field='*'){
        $info=[];
        $info=DB::name('meeting_record mr')->field($field)->join('__MEETING_USER__ u','u.id=mr.memberid','left')->where($map)->find();
        return $info;
    }

    //取消预约
    public function appoint_cancel($parentinfo,$meetingid,$ststus){
        $map[]=['mr.openid','=',$parentinfo['openid']];
        $map[]=['mr.id','=',$meetingid];
        $map[]=['mr.status','=','1'];
        $info=$this->getappoint_detail($map,'mr.*');
        if(empty($info)){
            return jsondata('0051','预约信息不存在');
        }
        if(strtotime($info['meetingdate'].' '.$info['meetingendtime'])<time()+60*60*24){
            return jsondata('0052','预约信息暂不能取消');
        }
        DB::startTrans();
        $update_data['status']=3;
        $update_data['canceltime']=date('Y-m-d H:i:s');
        $res=DB::name('meeting_record mr')->where($map)->update($update_data);
        $res2=1;
        $vaccineinfo=DB::name('vaccine')->where([['id','=',$info['vaccineid']]])->find();
        if(!empty($vaccineinfo)){
            $res2=DB::name('vaccine')->where([['id','=',$info['vaccineid']]])->update(['stock'=>$vaccineinfo['stock']+1]);
        }
        if($res && $res2){
            DB::commit();
            return jsondata('0001','取消预约成功');
        }else{
            DB::rollback();
            return jsondata('0059','取消预约失败,请重试');
        }
    }

    //删除预约信息
    public function appoint_delete($parentinfo,$meetingid){
        $delmeetingid=array();
        $hasadminmeetingid=array();
        foreach($meetingid as $v){
            $map=[];
            $map[]=['mr.openid','=',$parentinfo['openid']];
            $map[]=['mr.id','=',$v];
            $map[]=['mr.status','in',[2,3,4]];
            $info=$this->getappoint_detail($map,'mr.*');
            if(!empty($info)){
                $delmeetingid[]=$info['id'];
            }
        }
        if(empty($delmeetingid)){
            return jsondata('0051','请选择要预约信息');
        }
        $map=array();
        $map[]=['openid','=',$parentinfo['openid']];
        $map[]=['id','in',$delmeetingid];
        $map[]=['status','in',[2,3,4]];
        $res=DB::name('meeting_record')->where($map)->delete();
        if($res){
            return jsondata('0001','删除预约信息成功');
        }else{
            return jsondata('0059','删除预约信息失败,请重试');
        }
    }
}