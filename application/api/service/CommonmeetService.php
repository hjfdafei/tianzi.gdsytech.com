<?php
namespace app\api\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
//预约通用信息管理
class CommonmeetService extends Base{
    //获取日期 $days_num天数 7为一个星期 31为一个月 $meetstyle类型 1疫苗预约 2体检预约 3两癌筛查
    public function getdatelist($days_num,$meetstyle=1,$meetingsiteid=1){
        $cnweek=array('周日','周一','周二','周三','周四','周五','周六');
        $oneweek=array();
        $allnum=0;
        $ablenum=0;
        foreach(config('fixtime') as $tv){
            $allnum+=$tv['num'];
        }
        for($i=0;$i<=$days_num;$i++){
            //已预约人数 当天可预约总人数
            $map=[];
            if($meetstyle==1){
                $map[]=['meetingsiteid','=',$meetingsiteid];
            }
            $map[]=['meetstyle','=',$meetstyle];
            $map[]=['noymd','=',date('Ymd',time()+60*60*24*$i)];
            $hasnodate=DB::name('meeting_nodates')->where($map)->find();
            if(!empty($hasnodate)){
                $hasnumber=0;
            }else{
                // $map=[];
                // $map[]=['meetstyle','=',$meetstyle];
                // $map[]=['type','=',1];
                // $commonsetting=DB::name('meeting_timesetting')->where($map)->find();
                // if(!empty($commonsetting)){
                //     $allnum=DB::name('meeting_timesetting')->where($map)->sum('meetnum');
                // }
                // $map=[];
                // $map[]=['meetstyle','=',$meetstyle];
                // $map[]=['type','=',2];
                // $map[]=['typeid','=',date('l',time()+60*60*24*$i)];
                // $singlesetting=DB::name('meeting_timesetting')->where($map)->find();
                // if(!empty($singlesetting)){
                //     $allnum=DB::name('meeting_timesetting')->where($map)->sum('meetnum');
                //     $allnum=DB::name('meeting_timesetting')->where($map)->sum('meetnum');
                // }
                // $map2=[];
                // $map2[]=['meetstyle','=',$meetstyle];
                // $map2[]=['type','=',3];
                // $map2[]=['typeid','=',date('Y-m-d',time()+60*60*24*$i)];
                // $singlesetting2=DB::name('meeting_timesetting')->where($map2)->find();
                // if(!empty($singlesetting2)){
                //     $allnum=DB::name('meeting_timesetting')->where($map2)->sum('meetnum');
                // }
                // $map=[];
                // $map[]=['meetstyle','=',$meetstyle];
                // $map[]=['meetingymd','=',date('Ymd',time()+60*60*24*$i)];
                // $map[]=['status','in',[1,2,4]];
                // $meetnum=DB::name('meeting_record')->where($map)->sum('meetingnum');
                // $hasnumber=0;
                // if($allnum>$meetnum){
                //     $hasnumber=1;
                // }
                $hasnumber=0;
                $ablenum=$this->getbookablenum(date('Y-m-d',time()+60*60*24*$i),0,$meetstyle,$meetingsiteid);
                if($ablenum>0){
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

    //获取某天每个时段可预约数量 date_detail为具体日期 2020-05-20 $meetstyle类型 1疫苗预约 2体检预约 3两癌筛查
    public function gettimenum($date_detail,$meetstyle=1,$meetingsiteid=1){
        //当天各时段剩余预约数量 $timenum当天最大预约数量 $datemeetnum当天已预约数量
        $timedata=[];
        $map=[];
        if($meetstyle==1){
            $map[]=['meetingsiteid','=',$meetingsiteid];
        }
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
                // $timenum=$tv['num'];
                // $map=[];
                // $map[]=['meetstyle','=',$meetstyle];
                // $map[]=['type','=',2];
                // $map[]=['timeid','=',$tv['id']];
                // $map[]=['typeid','=',date('l',strtotime($date_detail))];
                // $singlesettinginfo=DB::name('meeting_timesetting')->where($map)->find();
                // if($singlesettinginfo){
                //     $timenum=$singlesettinginfo['meetnum'];
                // }
                // $map2=[];
                // $map2[]=['meetstyle','=',$meetstyle];
                // $map2[]=['type','=',3];
                // $map2[]=['timeid','=',$tv['id']];
                // $map2[]=['typeid','=',date('Y-m-d',strtotime($date_detail))];
                // $singlesettinginfo2=DB::name('meeting_timesetting')->where($map2)->find();
                // if($singlesettinginfo2){
                //     $timenum=$singlesettinginfo2['meetnum'];
                // }
                // if(empty($singlesettinginfo) && empty($singlesettinginfo2)){
                //     $map=[];
                //     $map[]=['meetstyle','=',$meetstyle];
                //     $map[]=['type','=',1];
                //     $map[]=['timeid','=',$tv['id']];
                //     $settinginfo=DB::name('meeting_timesetting')->where($map)->find();
                //     if(!empty($settinginfo)){
                //         $timenum=$settinginfo['meetnum'];
                //     }
                // }
                // $map=[];
                // $map[]=['meetingymd','=',date('Ymd',strtotime($date_detail))];
                // $map[]=['status','in',[1,2,4]];
                // $map[]=['meetingtimeid','=',$tv['id']];
                // $map[]=['meetstyle','=',$meetstyle];
                // $datemeetnum=DB::name('meeting_record')->where($map)->sum('meetingnum');

                $ablenum=$this->getbookablenum($date_detail,$tv['id'],$meetstyle,$meetingsiteid);
                if($ablenum<=0){
                    $ablenum=0;
                }
                $timedata[]=[
                    'id'=>$tv['id'],
                    'starttime'=>$tv['start'],
                    'endtime'=>$tv['end'],
                    //'num'=>$timenum-$datemeetnum,
                    'num'=>$ablenum,
                ];
            }
        }
        return $timedata;
    }

    //获取某天的具体可预约数量 $meetstyle类型 1疫苗预约 2体检预约 3两癌筛查
    public function getbookablenum($datestr,$timeid=0,$meetstyle=1,$meetingsiteid=1){
        $ablenum=0;
        $num=0;
        $map=[];
        if($meetstyle==1){
            $map[]=['meetingsiteid','=',$meetingsiteid];
        }
        $map[]=['meetstyle','=',$meetstyle];
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
            if($meetstyle==1){
                $map[]=['meetingsiteid','=',$meetingsiteid];
            }
            $map[]=['meetstyle','=',$meetstyle];
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
            if($meetstyle==1){
                $map2[]=['meetingsiteid','=',$meetingsiteid];
            }
            $map2[]=['meetstyle','=',$meetstyle];
            $map2[]=['type','=',3];
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
                if($meetstyle==1){
                    $map[]=['meetingsiteid','=',$meetingsiteid];
                }
                $map[]=['meetstyle','=',$meetstyle];
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
            if($meetstyle==1){
                $map[]=['meetingsiteid','=',$meetingsiteid];
            }
            $map[]=['meetstyle','=',$meetstyle];
            $map[]=['meetingymd','=',date('Ymd',strtotime($datestr))];
            $map[]=['status','in',[1,2,4]];
            if($timeid>0){
                $map[]=['meetingtimeid','=',$timeid];
            }
            $datemeetnum=DB::name('meeting_record')->where($map)->sum('meetingnum');
            $ablenum=$num-$datemeetnum;
        }
        return $ablenum;
    }

}