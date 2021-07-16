<?php
namespace app\api\model;
use think\Db;
use think\Model;
//微信支付的方法
class Wechatpay extends Model{
    public $create_baseurl="https://api.mch.weixin.qq.com/pay/unifiedorder";
    public $check_baseurl="https://api.mch.weixin.qq.com/pay/orderquery";
    //生成签名 $arr为数组
    public function makesign($arr,$key){
        if(!is_array($arr)){
            return '';
        }
        if(empty($arr)){
            return '';
        }
        if($key==''){
            return '';
        }
        ksort($arr);
        $str='';
        foreach($arr as $k=>$v){
            $str.=$k.'='.$v.'&';
        }
        $str.='key='.$key;
        return strtoupper(md5($str));
    }

    //数组转xml $arr为数组
    public function array2xml($arr){
        if(!is_array($arr)){
            return '';
        }
        if(empty($arr)){
            return '';
        }
        $xml = "<xml>";
        foreach ($arr as $key => $val){
            if(is_numeric($val)){
                $xml.= "<$key>$val</$key>";
            }else{
                $xml.= "<$key><![CDATA[$val]]></$key>";
            }
        }
        $xml.= "</xml>";
        return $xml;
    }

    //统一下单
    public function create_wechatorder($postdata){
        $url=$this->create_baseurl;
        $res=$this->xml2array($this->postXmlCurl($url,$postdata,30));
        return $res;
    }

    //查询订单
    public function check_wechatorder($postdata){
        $url=$this->check_baseurl;
        $res=$this->xml2array($this->postXmlCurl($url,$postdata,30));
        return $res;
    }

    public function xml2array($xml){
        $arr=json_decode(json_encode(simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $arr;
    }

    private static function postXmlCurl($url,$xml,$second = 30){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 40);
        set_time_limit(0);
        //运行curl
        $data = curl_exec($ch);
        file_put_contents('pay.log',date('Y-m-d H:i:s').'----'.$data."\r\n",FILE_APPEND);
        //var_dump($data);
        //返回结果
        if($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
        }
    }

}
