<?php
namespace app\index\model;
use think\Db;
use think\Model;

class Indexbase extends Model{
    protected $table="merchant_log";

    public function getdatelist($areaid,$startdate,$enddate){
    	$outdata=array();
        $areaid=input('areaid','0','intval');
        $startdate=input('startdate','2020-02-01','trim');
        $enddate=input('enddate','2020-07-31','trim');
        if($areaid<=0){
        	return $outdata;
            //return jsondata('400','请选择受理点');
        }
        $map[]=['id','=',$areaid];
        $map[]=['area_status','=',1];
        $areainfo=DB::name('area')->where($map)->find();
        if(empty($areainfo)){
        	return $outdata;
            //return jsondata('400','受理点不存在');
        }
        if($areainfo['area_startdate']!=''){
            $startdate=$areainfo['area_startdate'];
        }
        if($areainfo['area_enddate']!=''){
            $enddate=$areainfo['area_enddate'];
        }
        $firstday=date('Y-m-01',strtotime($startdate));
        $firstdaystr=strtotime($firstday);
        $lastday=date('Y-m-'.date('t',strtotime($enddate)),strtotime($enddate));
        $daynumber=(strtotime($lastday)-strtotime($firstday))/(60*60*24);
        $dayitems=[];
        $cnweek=array('周日','周一','周二','周三','周四','周五','周六');
        //是否可预约
        for($i=0;$i<=$daynumber;$i++){
            if(date('Y-m-d',$firstdaystr+60*60*24*$i)>=$firstday && date('Y-m-d',$firstdaystr+60*60*24*$i)<=$lastday){
                $tmptimes=$firstdaystr+60*60*24*$i;
                $month=date('n',$tmptimes);
                $day=date('d',$tmptimes);
                $cnweekname=$cnweek[date('w',$tmptimes)];
                $iscanmeet=1;
                $isnow=0;
                if(date('Y-m-d',$tmptimes)<$areainfo['area_startdate'] || date('Y-m-d',$tmptimes)>$areainfo['area_enddate']){
                    $iscanmeet=0;
                }
                if(date('w',$tmptimes)==0){
                    $iscanmeet=0;
                    if($areainfo['area_sundayopen']==1){
                        $iscanmeet=1;
                    }
                }
                if(date('Ymd')==date('Ymd',$tmptimes)){
                    $isnow=1;
                }
                $dayitems[$month][]=array('areaid'=>$areaid,'datestr'=>$day,'weeknum'=>date('w',$tmptimes),'weekname'=>$cnweekname,'iscanmeet'=>$iscanmeet,'isnow'=>$isnow,'datestr2'=>date('Y-m-d',$tmptimes));
            }
        }
        $outdata=$dayitems;
        return $outdata;
    }


}
