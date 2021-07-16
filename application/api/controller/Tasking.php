<?php
namespace app\api\controller;
use think\facade\Request;
use think\Controller;
use think\Db;
use think\db\Query;
class Tasking extends Controller{
    public function _empty(){
        return json(['code'=>'404','msg'=>'网络错误']);
    }

    //处理过期的预约 每天凌晨12点处理
    public function taskovertime_meeting(){
        //exit;
        $map[]=['status','=',1];
        $map[]=['meetingymd','<',date('Ymd')];
        $map[]=['meetstyle','=','1'];
        $update_data['status']=4;
        $update_data['expiretime']=date('Y-m-d H:i:s');
        $update_data['update_time']=date('Y-m-d H:i:s');
        $update_data['upsource']='sys_2';
        DB::name('meeting_record')->where($map)->update($update_data);
        return ;
    }

    //加入黑名单 每天凌晨12点处理 移入黑名单
    public function taskblack_join(){
        $meetingconfig=DB::name('meeting_config')->where([['type','=',1],['id','=',1]])->find();
        $missnum=$meetingconfig['missnum'];
        $limitdaynum=$meetingconfig['limitdaynum'];
        // $missnum=1;
        // $limitdaynum=0.0001;
        if($missnum>0){
            $sql="select * from (select mr.id,mr.status,mr.create_time,u.idcardnum,u.openid,u.mobile,(select count(id) as num from ".config('database.prefix')."meeting_record where status=4 and openid=mr.openid) as missnum from ".config('database.prefix')."meeting_record mr left join ".config('database.prefix')."meeting_user u on u.openid=mr.openid where mr.status=4 group by openid) as recordres where missnum>=$missnum";
            $list=DB::query($sql);
            if(!empty($list)){
                foreach($list as $v){
                    $blackmap=[];
                    $blackmap[]=['idcardnum','=',$v['idcardnum']];
                    $blackmap[]=['type','=',1];
                    $hasback=DB::name('meeting_black')->where($blackmap)->find();
                    if(empty($hasback)){
                        $data=[];
                        $data=[
                            'type'=>1,
                            'openid'=>$v['openid'],
                            'idcardnum'=>$v['idcardnum'],
                            'mobile'=>$v['mobile'],
                            'isjoin'=>1,
                            'jointime'=>date('Y-m-d H:i:s'),
                            'outtime'=>date('Y-m-d H:i:s',time()+60*60*24*$limitdaynum),
                            'joinnum'=>1,
                            'create_time'=>date('Y-m-d H:i:s'),
                        ];
                        DB::name('meeting_black')->insert($data);
                    }else{
                        $map=[];
                        $map[]=['meetstyle','=','1'];
                        $map[]=['openid','=',$v['openid']];
                        $map[]=['status','=',4];
                        $map[]=['create_time','>',$hasback['outtime']];
                        $miss_meetingnum=DB::name('meeting_record')->where($map)->count();
                        if($miss_meetingnum>=$missnum){
                            $lastmeetinginfo=DB::name('meeting_record')->where($map)->order(['id'=>'desc'])->find();
                            $data=[];
                            $data=[
                                'isjoin'=>1,
                                'jointime'=>date('Y-m-d H:i:s'),
                                'joinnum'=>$blackinfo['joinnum']+1,
                                'outtime'=>date('Y-m-d H:i:s',time()+60*60*24*$limitdaynum),
                            ];
                            DB::name('meeting_black')->where([['id','=',$blackinfo['id']]])->update($data);
                        }
                    }
                }
            }
        }
        return ;
    }

    //移出黑名单
    public function taskblack_remove(){
        $map[]=['isjoin','=',1];
        $map[]=['outtime','<',date('Y-m-d H:i:s')];
        $map[]=['meetstyle','=',1];
        Db::name('meeting_black')->where($map)->update([
            'outnum'=>date('Y-m-d H:i:s'),
            'removetime'=>date('Y-m-d H:i:s'),
            'outnum'=>Db::raw('outnum+1'),
            'isjoin'=>2,
        ]);
        return ;
    }

    //疫苗预约提醒
    public function taskremind_meeting(){
        $map[]=['isnotice','=',1];
        $map[]=['stock','>',0];
        $map[]=['isshow','=',1];
        $field='id,title,vaccage';
        $list=DB::name('vaccine')->field($field)->where($map)->select();
        $indata=[];
        if(!empty($list)){
            foreach($list as $v){
                $days=json_decode($v['vaccage'],true);
                if(!empty($days)){
                    foreach($days as $dv){
                        $daynum=$dv['ages_year']*365+$dv['ages_month']*30;
                        $map2=[];
                        $map2[]=['u.type','=','2'];
                        $map2[]=['u.status','=',1];
                        $map2[]=['u.birthday','=',date('Y-m-d',time()-60*60*24*$daynum)];
                        $memberfield='p.realname,p.mobile,u.birthday,u.id';
                        $memberlist=DB::name('meeting_user u')->field($memberfield)->join('__MEETING_USER__ p','p.id=u.parentid','left')->where($map2)->select();
                        if(!empty($memberlist)){
                            foreach($memberlist as $mv){
                                $msgmap=[];
                                $msgmap[]=['memberid','=',$mv['id']];
                                $msgmap[]=['vaccineid','=',$v['id']];
                                $hasmsg=DB::name('meeting_sendmsg')->where($msgmap)->find();
                                if(empty($hasmsg)){
                                    $indata=['title'=>$v['title'],'memberid'=>$mv['id'],'realname'=>$mv['realname'],'mobile'=>$mv['mobile'],'vaccineid'=>$v['id'],'sendymd'=>date('Ymd'),'create_time'=>date('Y-m-d H:i:s')];
                                    DB::name('meeting_sendmsg')->insert($indata);
                                }
                            }
                        }
                    }
                }
            }
        }
        return ;
    }

    //发送短信提醒
    public function taskremind_sendmsg(){
        $map=[];
        $map[]=['issend','=',2];
        $map[]=['sendymd','=',date('Ymd')];
        $list=DB::name('meeting_sendmsg')->field('vaccineid')->where($map)->group('vaccineid')->select();
        $mobiledata=[];
        $realnamedata=[];
        $signname=[];
        $vaccinetitle=[];
        $msgconfig['aliaccesskeyid']=config('aliaccesskeyid');
        $msgconfig['aliaccesskeysecret']=config('aliaccesskeysecret');
        if(!empty($list)){
            foreach($list as $nv){
                $map=[];
                $map[]=['issend','=',2];
                $map[]=['sendymd','=',date('Ymd')];
                $map[]=['vaccineid','=',$nv['vaccineid']];
                $msglist=DB::name('meeting_sendmsg')->where($map)->select();
                $msgnum=count($msglist);
                $sendnum=ceil($msgnum/50);
                for($i=0;$i<$sendnum;$i++){
                    $mobiledata=[];
                    $signname=[];
                    $tmpparam=[];
                    for($j=$i*50;$j<($i+1)*50;$j++){
                        if(isset($msglist[$j]['mobile'])){
                            $mobiledata[]=$msglist[$j]['mobile'];
                            $signname[]=config('premsg');
                            $tmpparam[]=['name'=>$msglist[$j]['realname'],'title'=>$msglist[$j]['title']];
                            DB::name('meeting_sendmsg')->where([['id','=',$msglist[$j]['id']]])->update(['issend'=>1,'sendtime'=>date('Y-m-d H:i:s'),'update_time'=>date('Y-m-d H:i:s')]);
                        }
                    }
                    $senddata=['mobile'=>json_encode($mobiledata),'signname'=>json_encode($signname),'param'=>json_encode($tmpparam)];
                    $res=base_sendmsg($msgconfig,$senddata,3,1);
                }
            }
        }
        return ;
    }
}
