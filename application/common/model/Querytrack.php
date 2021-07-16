<?php
namespace app\common\model;
use think\Db;
use think\Model;
//商户后台操作日志
class Querytrack extends Model{
    //通过快递单号获取快递公司编码
    public function get_expresscode($tracknumber,$querykey){
        $code='';
        if($tracknumber==''){
            return $code;
        }
        $param['num']=$tracknumber;
        $param['key']=$querykey;
        $url="http://www.kuaidi100.com/autonumber/auto";
        $res=http_send($url,$param);
        $res=json_decode($res,true);
        if(isset($res['comCode'])){
            $code=$res['comCode'];
        }
        return $code;
    }

    //调用快递100接口查询物流
    public function get_trackdetail($customer,$key,$expresscode,$tracknumber){
        $detail=array();
        if($customer=='' || $key=='' || $expresscode=='' || $tracknumber==''){
            $detail['code']='4001';
            $detail['msg']='暂无数据';
        }
        $param=array (
            'com'=>$expresscode,           //快递公司编码
            'num'=>$tracknumber,   //快递单号
            'phone' => '',              //手机号
            'from' => '',               //出发地城市
            'to' => '',                 //目的地城市
            'resultv2' => '1'           //开启行政区域解析
        );
        $post_data=array();
        $post_data["customer"]=$customer;
        $post_data["param"]=json_encode($param);
        $sign=md5($post_data["param"].$key.$post_data["customer"]);
        $post_data["sign"]=strtoupper($sign);
        $url='http://poll.kuaidi100.com/poll/query.do';
        $params="";
        foreach($post_data as $k=>$v){
            $params.="$k=".urlencode($v)."&";
        }
        $post_data=substr($params,0,-1);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $res=curl_exec($ch);
        $res=str_replace("\"", '"', $res);
        //file_put_contents('ccccccc.txt',$res);
        $res=json_decode($res,true);
        if(isset($res['result'])){
            $detail['code']='4002';
            $detail['msg']=$res['message'];
        }else{
            $detail['code']='200';
            $detail['msg']='获取成功';
            $detail['data']=$res;
        }
        return $detail;
    }
}
