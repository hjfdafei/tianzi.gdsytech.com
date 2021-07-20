<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
use think\Db;
use think\db\Query;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
//公共函数
/**
 * [pswCrypt description]密码加密
 * @param  [type] $psw [description]
 * @return [type]      [description]
 */
function pswCrypt($psw){
    $psw = md5($psw);
    $salt = substr($psw,0,4);
    $psw = crypt($psw,$salt);
    return $psw;
}


/**
 * [getActionUrl description]获取当前url
 * @return [type] [description]
 */
function getActionUrl(){
    $module = strtolower(request()->module());
    $controller =strtolower(request()->controller());
    $action = strtolower(request()->action());
    return $module.'/'.$controller.'/'.$action;
}

/**
 * 数组层级缩进转换
 * @param array $array
 * @param int   $pid
 * @param int   $level
 * @return array
 */
function tree($array, $pid = 0, $level = 1) {
    static $list = [];
    foreach ($array as $v) {
        if ($v['parent_id'] == $pid) {
            $v['level'] = $level;
            $list[]     = $v;
            $this->tree($array, $v['id'], $level + 1);
        }
    }
    return $list;
}

/**
 * 构建层级（树状）数组
 * @param array  $array 要进行处理的一维数组，经过该函数处理后，该数组自动转为树状数组
 * @param string $pid 父级ID的字段名
 * @param string $child_key_name 子元素键名
 * @return array|bool
 */
function array2tree(&$array, $pid = 'pid', $child_key_name = 'children') {
    $counter = $this->array_children_count($array, $pid);
    if ($counter[0] == 0){
        return false;
    }
    $tree = [];
    while (isset($counter[0]) && $counter[0] > 0) {
        $temp = array_shift($array);
        if (isset($counter[$temp['id']]) && $counter[$temp['id']] > 0) {
            array_push($array, $temp);
        } else {
            if ($temp[$pid] == 0) {
                $tree[] = $temp;
            } else {
                $array = $this->array_child_append($array, $temp[$pid], $temp, $child_key_name);
            }
        }
        $counter = $this->array_children_count($array, $pid);
    }

    return $tree;
}

/**
 * 子元素计数器
 * @param $array
 * @param $pid
 * @return array
 */
function array_children_count($array, $pid) {
    $counter = [];
    foreach ($array as $item) {
        $count = isset($counter[$item[$pid]]) ? $counter[$item[$pid]] : 0;
        $count++;
        $counter[$item[$pid]] = $count;
    }

    return $counter;
}

/**
 * 把元素插入到对应的父元素$child_key_name字段
 * @param        $parent
 * @param        $pid
 * @param        $child
 * @param string $child_key_name 子元素键名
 * @return mixed
 */
function array_child_append($parent, $pid, $child, $child_key_name) {
    foreach ($parent as &$item) {
        if ($item['id'] == $pid) {
            if (!isset($item[$child_key_name]))
                $item[$child_key_name] = [];
            $item[$child_key_name][] = $child;
        }
    }

    return $parent;
}


/**
 * [log description]打印日志
 * @param  [type] $name  [description]
 * @param  [type] $value [description]
 * @param  [type] $file  [description]
 * @param  [type] $line  [description]
 * @return [type]        [description]
 */
function logs($name, $value, $file = __FILE__, $line = __LINE__) {
    $value = date('Y-m-d H:i:s') . " " . $value;
    return app_log(date('Ymd') . $name, $value, "", $line);
}

/**
 * [app_log description]日志
 * @param  [type] $name  [description]
 * @param  [type] $value [description]
 * @param  [type] $file  [description]
 * @param  [type] $line  [description]
 * @return [type]        [description]
 */
function app_log($name,$value,$file=__FILE__,$line=__LINE__){
    $value="<?exit;?".">$file\t$line\t".$value."\n";
    if (!is_dir(ROOT_PATH.'cache')){//当路径不穿在
        mkdir(ROOT_PATH.'cache', 0777);
        chmod(ROOT_PATH.'cache', 0777);
    }
    file_put_contents(ROOT_PATH.'cache/log.'.$name.'.php',$value,FILE_APPEND);
}

/**
 * 循环删除目录和文件
 * @param string $dir_name
 * @return bool
 */
function delete_dir_file($dir_name) {
    $result = false;
    if(is_dir($dir_name)){
        if ($handle = opendir($dir_name)) {
            while (false !== ($item = readdir($handle))) {
                if ($item != '.' && $item != '..') {
                    if (is_dir($dir_name . DS . $item)) {
                        delete_dir_file($dir_name . DS . $item);
                    } else {
                        unlink($dir_name . DS . $item);
                    }
                }
            }
            closedir($handle);
            if (rmdir($dir_name)) {
                $result = true;
            }
        }
    }

    return $result;
}

function ptr($data){
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

function exitdata($code='0012',$msg='参数不能为空',$data=array()){
    $json['code']=$code;
    $json['msg']=$msg;
    $json['data']=$data;
    exit(json_encode($json,256));
}

function jsondata($code='0012',$msg='参数不能为空',$data=array()){
    $json['code']=$code;
    $json['msg']=$msg;
    $json['data']=$data;
    echo json_encode($json,256);
    return;
}

//生成随机字符串
function rand_string($ukey = "", $len = 6, $type = "1", $utype = "1", $addChars = "",$temail='') {
    $str = "";
    switch ($type) {
        case 0 :
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz" . $addChars;
            break;
        case 1 :
            $chars = str_repeat("123456789", 3);
            break;
        case 2 :
            $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ" . $addChars;
            break;
        case 3 :
            $chars = "abcdefghijklmnopqrstuvwxyz" . $addChars;
            break;
        default :
            $chars = "ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789" . $addChars;
            break;
    }
    if (10 < $len) {
        $chars = $type == 1 ? str_repeat($chars, $len) : str_repeat($chars, 5);
    }
    $chars = str_shuffle($chars);
    $str = substr($chars, 0, $len);
    return $str;
}

//生成订单号
function build_order_no() {
    return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

//截取字符串
function cnsubstr($str, $length, $start = 0, $charset = "utf-8", $suffix = true) {
    $str = strip_tags($str);
    if (function_exists("mb_substr")) {
        if (mb_strlen($str, $charset) <= $length) {
            return $str;
        }
        $slice = mb_substr($str, $start, $length, $charset);
    } else {
        $re['utf-8'] = "/[\x01-]|[-][-]|[-][-]{2}|[-][-]{3}/";
        $re['gb2312'] = "/[\x01-]|[-][-]/";
        $re['gbk'] = "/[\x01-]|[-][@-]/";
        $re['big5'] = "/[\x01-]|[-]([@-~]|-])/";
        preg_match_all($re[$charset], $str, $match);
        if (count($match[0]) <= $length) {
            return $str;
        }
        $slice = join("", array_slice($match[0], $start, $length));
    }
    if ($suffix) {
        return $slice . "…";
    }
    return $slice;
}

//字符串加密
function string_authcode($string,$operation='decode',$key='',$expiry=86400){
    $ckey_length=4;
    // 随机密钥长度 取值 0-32;
    // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
    // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
    // 当此值为 0 时，则不产生随机密钥
    $key=md5($key?$key:'zssytech.com');//这里可以填写默认key值
    $keya=md5(substr($key,0,16));
    $keyb=md5(substr($key,16,16));
    $keyc=$ckey_length?(strtolower($operation)=='decode'?substr($string,0,$ckey_length):substr(md5(microtime()),-$ckey_length)):'';
    $cryptkey=$keya.md5($keya.$keyc);
    $key_length=strlen($cryptkey);
    $string=strtolower($operation)=='decode'?base64_decode(substr($string, $ckey_length)):sprintf('%010d',$expiry?$expiry+time():0).substr(md5($string.$keyb),0,16).$string;
    $string_length=strlen($string);
    $result='';
    $box=range(0,255);
    $rndkey=array();
    for($i=0;$i<=255;$i++) {
        $rndkey[$i]=ord($cryptkey[$i%$key_length]);
    }
    for($j=$i=0;$i<256;$i++){
        $j=($j+$box[$i]+$rndkey[$i])%256;
        $tmp=$box[$i];
        $box[$i]=$box[$j];
        $box[$j]=$tmp;
    }
    for($a=$j=$i=0; $i<$string_length;$i++){
        $a=($a+1)%256;
        $j=($j+$box[$a])%256;
        $tmp=$box[$a];
        $box[$a]=$box[$j];
        $box[$j]=$tmp;
        $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
    }
    if(strtolower($operation)=='decode'){
        if((substr($result,0,10)==0 || substr($result,0,10)-time()>0) && substr($result,10,16)==substr(md5(substr($result,26).$keyb),0,16)){
            return substr($result,26);
        }else{
            return '';
        }
    }else{
        return $keyc.str_replace('=','',base64_encode($result));
    }
}

function http_send($url, $param, $data = '', $method = 'GET'){
    $opts = array(
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    );
    /* 根据请求类型设置特定参数 */
    $opts[CURLOPT_URL] = $url . '?' . http_build_query($param);
    if(strtoupper($method) == 'POST'){
        $opts[CURLOPT_POST] = 1;
        $opts[CURLOPT_POSTFIELDS] = $data;
        if(is_string($data)){ //发送JSON数据
            $opts[CURLOPT_HTTPHEADER] = array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data),
            );
        }
    }
    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $data  = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    return  $data;
}

function http_postsend($url,$data,$second = 30){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
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

//检查手机号
function checkformat_mobile($mobile){
    $result['code']='0012';
    $result['msg']='电话号码不能为空';
    if($mobile==''){
        return $result;
    }
    if(!preg_match("/^1[2-9]\d{9,10}$/",$mobile)){
        $result['code']='0012';
        $result['msg']='请输入正确的手机号码';
        return $result;
    }
    $result['code']='0001';
    $result['msg']='验证成功';
    return $result;
}

//获取距离
function getdistance($lat1, $lon1, $lat2,$lon2,$radius = 6378.137){
    $rad = floatval(M_PI / 180.0);
    $lat1 = floatval($lat1) * $rad;
    $lon1 = floatval($lon1) * $rad;
    $lat2 = floatval($lat2) * $rad;
    $lon2 = floatval($lon2) * $rad;

    $theta = $lon2 - $lon1;

    $dist = acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($theta));

    if ($dist < 0 ) {
        $dist += M_PI;
    }
    return $dist = $dist * $radius;
}

//数据转xml格式
function wechatarr2xml($data){
    $xml = "<xml>";
    foreach ($data as $key => $val){
        if(is_numeric($val)){
            $xml.= "<$key>$val</$key>";
        }else{
            $xml.= "<$key><![CDATA[$val]]></$key>";
        }
    }
    $xml.= "</xml>";
    return $xml;
}

//微信签名
function wechatmakesign($data,$key){
    $str='';
    if(is_array($data)){
        ksort($data);
        foreach($data as $k=>$v){
            if($key!='sign'){
                $str.="$k=$v&";
            }
        }
        $str.="key=$key";
        return strtoupper(md5($str));
    }else{
        return $data;
    }
}

//零钱打款请求发送
function curl_post_ssl($url,$postxml,$second=60){
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_TIMEOUT,$second);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
    //以下两种方式需选择一种
    // curl_setopt($ch,CURLOPT_SSLCERT,trim(getcwd(),'\public')."/wechatcerts/apiclient_cert.pem");
    // curl_setopt($ch,CURLOPT_SSLKEY,trim(getcwd(),'\public')."/wechatcerts/truecert/apiclient_key.pem");
    curl_setopt($ch,CURLOPT_SSLCERT,trim(getcwd(),'\public')."/vendor/payment/wxpay/cert/apiclient_cert.pem");
    curl_setopt($ch,CURLOPT_SSLKEY,trim(getcwd(),'\public')."/vendor/payment/wxpay/cert/apiclient_key.pem");
    curl_setopt($ch,CURLOPT_POST, 1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$postxml);
    $data = curl_exec($ch);
    //file_put_contents('tttt.txt',date('Y-m-d H:i:s').'----'.$data."\r\n",FILE_APPEND);
    if($data){
        curl_close($ch);
        return $data;
    }else{
        $error = curl_errno($ch);
        echo "call faild, errorCode:$error\n";
        curl_close($ch);
        return false;
    }
}

//获取发送模板的access_token
function getaccess_token($appid,$appsecret){
    $path='./'.$appid.'_access_token.json';
    if(!file_exists($path)){
        $data['expire_time']=0;
        $data['access_token']='';
        file_put_contents($path,json_encode($data));
    }
    $data=json_decode(file_get_contents($path),true);
    $access_token='';
    if($data['expire_time']<time()){
        $url="https://api.weixin.qq.com/cgi-bin/token";
        $param['grant_type']='client_credential';
        $param['appid']=$appid;
        $param['secret']=$appsecret;
        $res=json_decode(http_send($url,$param),true);
        if(isset($res['access_token']) && $res['access_token']!=''){
            $data['expire_time']=time() + 7000;
            $data['access_token']=$res['access_token'];
            file_put_contents($path,json_encode($data));
            $access_token=$res['access_token'];
        }
    }else{
        $access_token=$data['access_token'];
    }
    return $access_token;
}

//获取jsapiticket
function getJsApiTicket($appid,$appsecret){
    $data=json_decode(file_get_contents("./jsapi_ticket.json"),true);
    $jsapi_ticket='';
    if($data['expire_time']<time()){
        $access_token=getaccess_token($appid,$appsecret);
        $url="https://api.weixin.qq.com/cgi-bin/ticket/getticket";
        $param['type']='jsapi';
        $param['access_token']=$access_token;
        $res=json_decode(http_send($url,$param),true);
        if(isset($res['ticket']) && $res['ticket']!=''){
            $data['expire_time']=time() + 7000;
            $data['jsapi_ticket']=$res['ticket'];
            file_put_contents('./jsapi_ticket.json',json_encode($data));
            $jsapi_ticket=$res['ticket'];
        }
    }else{
        $jsapi_ticket=$data['jsapi_ticket'];
    }
    return $jsapi_ticket;
}

//发送缴费通知
function send_paytpl($openid,$orders_id,$patient_name,$doctor_name,$orders_time,$orderno){
    $postdata['touser']=$openid;
    $postdata['template_id']='5EsQbWosoNAcyBbxQXTqz9fui27pfa7L_AfpO8wYUgI';
    $postdata['url']='http://vipmedcare.gdsytech.com/h5?orders_id='.$orders_id;
    $data=[
        'first'=>['value'=>'您好,'.$patient_name.',您有一笔代缴费订单,请及时支付'],
        'keyword1'=>['value'=>$patient_name],
        'keyword2'=>['value'=>$doctor_name],
        'keyword3'=>['value'=>'预约费用缴费'],
        'keyword4'=>['value'=>$orders_time],
        'keyword5'=>['value'=>$orderno],
        'remark'=>['value'=>'如有疑问，请联系万合医疗客服'],
    ];
    $postdata['data']=$data;
    $postdata=json_encode($postdata);
    $access_token=getaccess_token(config('app.miniappid'),config('app.minisecret'));
    $url="https://api.weixin.qq.com/cgi-bin/message/template/send";
    //$param['access_token']='41_Zg-NQvbIJzxYp0reEG421BSEW7ZFuDFErXBX99AKFdrEaglUCdoRizmpf0_dtcrySgNahHsWqBicpoIPAZpycUsGg3vMjktu6_K5nYmuIRNGxlh9buJsx5r3Z1Sb3_B34IrCTck7Ho2R-TjCMPWbAHABEL';
    if($access_token!=''){
        $param['access_token']=$access_token;
        $res=http_send($url,$param,$postdata,'POST');
    }
}

//发送新订单通知
function send_newtpl($openid,$assistant_id,$assistant_name,$doctor_name){
    $postdata['touser']=$openid;
    $postdata['template_id']='efTCAkgKJ_JETy-9K4iP2Rd1l1pmhyDxSQricJwtcqg';
    $postdata['url']='http://vipmedcare.gdsytech.com/h5?assistant_id='.$assistant_id;
    $data=[
        'first'=>['value'=>'您好,'.$assistant_name.',您有一笔新的订单,请及时处理'],
        'keyword1'=>['value'=>'预约面诊'],
        'keyword2'=>['value'=>$doctor_name],
        'keyword3'=>['value'=>'请及时处理'],
        'remark'=>['value'=>'如有疑问，请联系万合医疗客服'],
    ];
    $postdata['data']=$data;
    $postdata=json_encode($postdata);
    $access_token=getaccess_token(config('app.miniappid'),config('app.minisecret'));
    $url="https://api.weixin.qq.com/cgi-bin/message/template/send";
    //$param['access_token']='41_Zg-NQvbIJzxYp0reEG421BSEW7ZFuDFErXBX99AKFdrEaglUCdoRizmpf0_dtcrySgNahHsWqBicpoIPAZpycUsGg3vMjktu6_K5nYmuIRNGxlh9buJsx5r3Z1Sb3_B34IrCTck7Ho2R-TjCMPWbAHABEL';
    if($access_token!=''){
        $param['access_token']=$access_token;
        $res=http_send($url,$param,$postdata,'POST');
    }
}

//发送宽带账号(服务号模板消息通知小程序)
function send_broadbandtpl($openid,$realname,$orderno){
    $miniappid=config('app.miniappid');
    $minisecret=config('app.minisecret');
    $appid=config('app.appid');
    $access_token=getaccess_token($miniappid,$minisecret);
    $url="https://api.weixin.qq.com/cgi-bin/message/wxopen/template/uniform_send";
    $postdata=[
        'access_token'=>$access_token,
        'touser'=>$openid,
        'mp_template_msg'=>[
            'appid'=>$appid,
            'template_id'=>'4xxImIkdfAonHVbffLbrNPhC27dMFVi4mvcGY8c_Smc',
            'url'=>'11',
            'miniprogram'=>[
                'appid'=>config('app.miniappid'),
                'path'=>'pages/index',
            ],
            'data'=>[
                'first'=>['value'=>'尊敬的客户，您的宽带订单已包装成功'],
                'keyword1'=>['value'=>$realname],
                'keyword2'=>['value'=>$orderno],
                'remark'=>['value'=>'你已成功办理佛大校园宽带报装，请前往《佛大校园服务》小程序个人中心查看办理账号和密码'],
            ]
        ],
    ];
    $postdata=json_encode($postdata);
    if($access_token!=''){
        $param['access_token']=$access_token;
        $res=http_send($url,$param,$postdata,'POST');
    }
}

//发送小程序订阅通知
function send_mini_broadbandtpl($openid,$goods_title,$realname,$money){
    $url="https://api.weixin.qq.com/cgi-bin/message/subscribe/send";
    $miniappid=config('app.miniappid');
    $minisecret=config('app.minisecret');
    $appid=config('app.appid');
    $access_token=getaccess_token($miniappid,$minisecret);
    $postdata=[
        'access_token'=>$access_token,
        'touser'=>$openid,
        'template_id'=>'grFWUPWUQvG1D6cr3dqjhjr1S2BxndHc_5DaLg-RI4w',
        'page'=>'pages/index',
        'data'=>[
            'thing1'=>$goods_title,
            'name3'=>$realname,
            'amount16'=>$money,
        ],
    ];
    $postdata=json_encode($postdata);
    if($access_token!=''){
        $param['access_token']=$access_token;
        $res=http_send($url,$param,$postdata,'POST');
        ptr($res);
    }

}

//发送模板
function sendtmpl($data){
    if(!empty($data)){
        $access_token=getaccess_token(config('minipro.appid'),config('minipro.appsecret'));
        $url=' https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send';
        $param['access_token']=$access_token;
        $postdata=json_encode($data);
        $res=http_send($url,$param,$postdata,'POST');
        file_put_contents('msg.log',date('Y-m-d H:i:s').'----'.$res."\r\n",FILE_APPEND);
    }
}

//发送短信
function base_sendcontent($mobile,$code,$type=1,$msg=''){
    AlibabaCloud::accessKeyClient(config('app.aliaccesskeyid'),config('app.aliaccesskeysecret'))->regionId('不填写')->asDefaultClient();
    if(in_array($type,array(1,2))){
        $result=AlibabaCloud::rpc()->product('Dysmsapi')->version('2017-05-25')->action('SendSms')->method('POST')->options([
            'query'=>[
                'PhoneNumbers'=>$mobile,
                'SignName'=>"万合医疗",
                'TemplateCode'=>"SMS_212055968",
                'TemplateParam'=>json_encode(['code'=>$code]),
            ],
        ])->request();
    }elseif(in_array($type,array(3,4))){
        $result=AlibabaCloud::rpc()->product('Dysmsapi')->version('2017-05-25')->action('SendSms')->method('POST')->options([
            'query'=>[
                'PhoneNumbers'=>$mobile,
                'SignName'=>"万合医疗",
                'TemplateCode'=>"SMS_170836571",
                'TemplateParam'=>json_encode(['code'=>$code]),
            ],
        ])->request();
    }

    //file_put_contents('lll.txt',$result."\r\n",FILE_APPEND);
    $res=0;
    $result=json_decode($result,true);
    if(strtolower($result['Code'])=='ok'){
        $res=1;
    }
    return $res;
    // $res=0;
    // $smsapi=new \ChuanglanSmsApi();
    // $result=$smsapi->queryBalance();
    // $result=json_decode($result,true);
    // if($result['code']==0){
    //     if(round($result['balance'],2)>2.00){//余额大于2元，才能发送
    //         $result=$smsapi->sendSMS($mobile,$msg);
    //         $result=json_decode($result,true);
    //         if($result['code']==0){
    //             $res=1;
    //         }
    //     }
    // }
    // return $res;
}

function export_datacsv($filename='',$headarr=[],$data=[]){
    @ini_set('memory_limit','2048M');
    @set_time_limit(0);
    if (!$headarr || !$data || !is_array($data)) {
        return false;
    }
    setlocale(LC_ALL, 'en_US.UTF-8');
    // 输出 Excel 文件头
    $filename=empty($filename) ? date('Y-m-d') : $filename;
    $filename=$filename.".csv";
    $string="";
    $string.=implode(',', $headarr)."\n";//首先写入表格标题栏
    foreach($data as $key=>$value){
        foreach($value as $k=>$val){
            $value[$k]=$val;
        }
        $string .= implode(",", $value) . "\n"; //用英文逗号分开
    }
    ob_end_clean();
    header("Content-type:text/csv");
    header("Content-Disposition:attachment;filename=" . $filename);
    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
    header('Expires:0');
    header('Pragma:public');
    echo iconv('UTF-8', 'GBK//IGNORE', $string);
    exit;
}

//通过ip获取经纬度
function ip2lnglat($ip=''){
    $url="https://restapi.amap.com/v3/ip";
    $parameters['key']='f18a5dc1984078577bba97f880f4097b';
    if($ip!=''){
        $parameters['ip']=$ip;
    }
    $res=http_send($url,$parameters);
    return json_decode($res,true);
}

//获取百度云access_token
function getbaidu_access_token($appid,$appsecret){
    $data=json_decode(file_get_contents("./baiduaccess_token.json"),true);
    if($data['expire_time']<time()){
        $url="https://aip.baidubce.com/oauth/2.0/token";
        $param['grant_type']='client_credentials';
        $senddata['client_id']=$appid;
        $senddata['client_secret']=$appsecret;
        $res=json_decode(http_send($url,$param,$senddata,'post'),true);
        if($res['access_token']){
            $data['expire_time']=time() + 60*60*24*29;
            $data['access_token']=$res['access_token'];
            file_put_contents('./baiduaccess_token.json',json_encode($data));
        }
        $access_token=$res['access_token'];
    }else{
        $access_token=$data['access_token'];
    }
    return $access_token;
}

//tcpdf
function tcpdf_createpdt($html,$filename='info',$mode='I'){
    $filename.='.pdf';
    $pdf=new \TCPDF('P','mm','A4',true,'UTF-8');
    $pdf->setPrintHeader(false);    //页面头部横线取消
    $pdf->setPrintFooter(false); //页面底部更显取消
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);//自动分页
    $pdf->SetMargins(5, 10,5);//设置页面margin
    $pdf->AddPage();//增加一个页面
    $pdf->setCellPaddings(0, 0, 0, 0);//设置一个单元格的padding
    $pdf->setFont('msyh','B',12);
    $pdf->writeHTML($html,true, false, true, false, '');
    ob_clean();
    $pdf->Output($filename,$mode);
}

/** @param string $filename 文件名称
 * @param array $headArr 表头名称
 * @param array $data 要导出的数据*/
function exportdatas_old($filename='数据表',$headArr=[],$data=[]){
    $filename.= "_" .date("Y_m_d",time()).".xls";
    $spreadsheet=new Spreadsheet();
    $objPHPExcel=$spreadsheet->getActiveSheet();
    $key = ord("A");// 设置表头
    foreach ($headArr as $v) {
        $colum = chr($key);
        $objPHPExcel->setCellValue($colum . '1', $v);
        $key += 1;
    }
    $column=2;
    foreach($data as $key=>$rows){
        $span=ord("A");
        foreach($rows as $keyName=>$value){ // 列写入
            $objPHPExcel->setCellValue(chr($span) . $column, $value);
            $span++;
        }
        $column++;
    }
    $filename=iconv("utf-8", "gbk//IGNORE", $filename); // 重命名表（UTF8编码不需要这一步）
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
    exit;
}

/** @param string $filename 文件名称
 * @param array $headArr 表头名称
 * @param array $data 要导出的数据*/
function exportdatas($filename='数据表',$headArr=[],$data=[]){
    $sheettitle=$filename;
    $filename.= "_" .date("Y_m_d",time()).".xls";
    $spreadsheet=new Spreadsheet();
    $objPHPExcel=$spreadsheet->getActiveSheet();
    $objPHPExcel->setTitle($sheettitle);//设置sheet标题
    $key=1;
    foreach($headArr as $v){
        $colum=Coordinate::stringFromColumnIndex($key);
        $objPHPExcel->setCellValue($colum.'1', $v);
        $objPHPExcel->getStyle($colum.'1')->getAlignment()->setWrapText(true);
        $objPHPExcel->getColumnDimension($colum)->setWidth(20);
        $objPHPExcel->getStyle($colum.'1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $objPHPExcel->getStyle($colum.'1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $key+=1;
    }
    $column=2;
    foreach($data as $key => $rows){ //行写入
        $span = 1;
        foreach($rows as $keyName=>$value){// 列写入
            $j = Coordinate::stringFromColumnIndex($span);
            $objPHPExcel->setCellValue($j.$column, $value);
            $objPHPExcel->getStyle($j.$column)->getAlignment()->setWrapText(true);
            $objPHPExcel->getStyle($j.$column)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getStyle($j.$column)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $span++;
        }
        $column++;
    }
    $filename=iconv("utf-8", "gbk//IGNORE", $filename); // 重命名表（UTF8编码不需要这一步）
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
    exit;
}

//导出多个excel，并打包zip
function exportdatamore($filename='数据表',$headArr=[],$data=[]){
    $spreadsheet=new Spreadsheet();
    $objPHPExcel=$spreadsheet->getActiveSheet();
    $key = ord("A");// 设置表头
    foreach ($headArr as $v) {
        $colum = chr($key);
        $objPHPExcel->setCellValue($colum . '1', $v);
        $key += 1;
    }
    $column=2;
    foreach($data as $key=>$rows){
        $span=ord("A");
        foreach($rows as $keyName=>$value){ // 列写入
            $objPHPExcel->setCellValue(chr($span) . $column, $value);
            $span++;
        }
        $column++;
    }
    //$filename=iconv("utf-8", "gbk//IGNORE", $filename); // 重命名表（UTF8编码不需要这一步）
    $writer = new Xlsx($spreadsheet);
    $writer->save($filename);
}

function exportzip($filename='打包数据',$filenamearr=[]){
    if(empty($filenamearr)){
        return jsondata('400','暂无数据');
    }
    $zip=new \ZipArchive();
    $filename.="_" .date("Y_m_d",time()).".zip";
    //$filename=iconv('utf-8','gbk//ignore',$filename);
    $zip->open($filename,ZipArchive::CREATE);
    foreach ($filenamearr as $file) {
        $a=$zip->addFromString(iconv('utf-8', 'gbk//ignore', $file),file_get_contents(iconv('utf-8', 'gbk//ignore', $file)));
    }
    $zip->close();
    foreach($filenamearr as $file) {
        @unlink($file);
    }
    header("Cache-Control: max-age=0");
    header("Content-Description: File Transfer");
    header('Content-disposition: attachment; filename='.$filename); // 文件名
    header("Content-Type: application/zip"); // zip格式的
    header("Content-Transfer-Encoding: binary"); //
    header('Content-Length: '.filesize($filename)); //
    ob_clean();
    flush();
    readfile($filename);
    unlink($filename);
    exit;
}

//通过传入日期获取距离当天日期的天数，并转换为几岁几个月
function ymd4date($date){
    //获取天数
    if($date==''){
        $date=date('Y-m-d');
    }
    $daynum=ceil((time()-strtotime($date))/(60*60*24));
    $year=intval($daynum/365);
    $month=intval(($daynum-$year*365)/30);
    $day=$daynum-$year*365-$month*30;
    return ['year'=>$year,'month'=>$month,'day'=>$day,'daynum'=>$daynum];
}

//$code要生成的码 $num多少位数的码，不够往左补0
function createnum4rand($code='',$num=8){
    if(strlen($code)==$num){
        return $code;
    }
    $maxcode=9;
    if($num>1){
        for($i=0;$i<$num;$i++){
            $maxcode.=9;
        }
    }
    if($code==''){
        $code=mt_rand(0,$maxcode);
    }
    if(strlen($code)<$num){
        $code=str_pad($code,$num,'0',STR_PAD_LEFT);
    }
    return $code;
}
//base64图片转换成文件 size单位 M
function base64_img_forfile($base64_image,$size='2',$path='./upload/attach'){
    //匹配出图片的格式
    if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image,$result)){
        $type=$result[2];
        $basedir=$path."/".date('Ymd',time())."/";
        if(!file_exists($basedir)){
            mkdir($basedir, 0777,true);
        }
        $allowext=['jpg','jpeg','png'];
        if(!in_array($type,$allowext)){
            return ['code'=>'400','msg'=>'请上传以'.implode(',',$allowext).'后缀结尾的图片','url'=>''];
        }
        $filename=$basedir.md5(microtime(true)).".{$type}";
        if (file_put_contents($filename,base64_decode(str_replace($result[1],'',$base64_image)))){
            //file_put_contents('imglog.log',date('Y-m-d H:i:s').'----'.$filename.':'.filesize($filename),FILE_APPEND);
            if(round(filesize($filename)/1024/1024,2)>2){
                @unlink($filename);
                return ['code'=>'400','msg'=>'图片上传失败,上传不大于'.$size.'M的图片','url'=>''];
            }
            return ['code'=>'200','msg'=>'图片上传成功','url'=>$filename];
        }else{
            return ['code'=>'400','msg'=>'图片上传失败','url'=>''];
        }
    }else{
        return ['code'=>'400','msg'=>'图片格式错误','url'=>''];
    }
}

//查找路径
function getabpath($url,$str='upload'){
    return substr($url,stripos($url,$str)-1);
}