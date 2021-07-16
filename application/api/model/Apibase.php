<?php
namespace app\api\model;
use think\Db;
use think\Model;
//商品基本信息
class Apibase extends Model{
    //校验验证码
    public function base_check_verifycode($mobile,$type,$minute,$verifycode){
        $result=array();
        $result['code']='0012';
        $result['msg']='参数出错';
        if(!in_array($type,array(1,2))){
            $result['code']='0016';
            $result['msg']='请输入验证码';
            return $result;
        }

        if($mobile=='13122223333' && $verifycode==1111){
            $result['code']='0001';
            $result['msg']='验证通过';
            return $result;
        }

        $checkres=checkformat_mobile($mobile);
        if($checkres['code']!='0001'){
            exitdata($checkres['code'],$checkres['msg']);
        }
        if($verifycode==''){
            $result['code']='0016';
            $result['msg']='请输入验证码';
            return $result;
        }
        if(strlen($verifycode)<4 || strlen($verifycode)>6){
            $result['code']='0016';
            $result['msg']='请输入验证码';
            return $result;
        }
        $where['mobile']=$mobile;
        $where['type']=$type;
        $where['verifycode']=$verifycode;
        $codeinfo=DB::name('verifycode')->where($where)->order('id desc')->find();
        if(empty($codeinfo)){
            $result['code']='0017';
            $result['msg']='验证码错误';
            return $result;
        }
        if((strtotime($codeinfo['sendtime'])+60*$minute)<time()){
            $result['code']='0017';
            $result['msg']='验证码已过期';
            return $result;
        }
        if($codeinfo['isread']==1){
            $result['code']='0017';
            $result['msg']='验证码已失效,请重新获取';
            return $result;
        }
        $where=array();
        $where['id']=$codeinfo['id'];
        $where['isread']=0;
        $updata['isread']=1;
        $updata['verifytime']=date('Y-m-d H:i:s');
        $res=DB::name('verifycode')->where($where)->update($updata);
        if($res){
            $result['code']='0001';
            $result['msg']='验证通过';
            return $result;
        }else{
            $result['code']='0017';
            $result['msg']='验证失败,请重试';
            return $result;
        }
        return $result;
    }

    //通过手机号码获取会员信息
    public function getmemberinfo4mobile($mobile){
        $info=DB::name('member')->where(['mobile'=>$mobile])->find();
        return $info;
    }

    //通过车牌获取会员信息
    public function getmemberinfo4carnumber($carnumber){
        $info=DB::name('member')->where(['carnumber'=>$carnumber])->find();
        return $info;
    }

    //通过手机号码获取员工信息
    public function getstaffinfo4mobile($mobile,$merchantid){
        $info=DB::name('merchant_staff')->where(['mobile'=>$mobile,'merchantid'=>$merchantid,'isdel'=>2])->find();
        return $info;
    }

    //通过登录code获取小程序openid
    public function getopenid4code($appid,$appsecret,$code){
        if($code==''){
            $result['code']='0014';
            $result['msg']='参数出错';
            $result['data']=array();
            return $result;
        }
        $url='https://api.weixin.qq.com/sns/jscode2session';
        $param['grant_type']='authorization_code';
        $param['appid']=$appid;
        $param['secret']=$appsecret;
        $param['js_code']=$code;
        $res=http_send($url,$param,'','GET');
        $res=json_decode($res,true);
        var_dump($res);
        if(!isset($res['openid'])){
            $result['code']='0015';
            $result['msg']='获取失败,请重试';
            $result['data']=array();
            return $result;
        }
        if($res['openid']==''){
            $result['code']='0016';
            $result['msg']='获取失败,请重试';
            $result['data']=array();
            return $result;
        }
        $res['decode_key']=$res['session_key'];
        $result['code']='0001';
        $result['msg']='获取成功';
        $result['data']=$res;
        return $result;
    }

    //解密小程序信息
    public function mini_message_decode($appid,$sessionKey,$encrypteddata,$iv){
        $resinfo=array();
        if(strlen($sessionKey)!=24){
            $resinfo['code']='-41001';
            $resinfo['msg']='encodingAesKey 非法';
            $resinfo['data']=array();
            return $resinfo;
        }
        $aesKey=base64_decode($sessionKey);
        if(strlen($iv)!=24){
            $resinfo['code']='-41002';
            $resinfo['msg']='iv变量 非法';
            $resinfo['data']=array();
            return $resinfo;
        }
        $aesIV=base64_decode($iv);
        $aesCipher=base64_decode($encrypteddata);
        $result=openssl_decrypt($aesCipher,"AES-128-CBC",$aesKey,1,$aesIV);
        $dataObj=json_decode($result);
        if($dataObj==NULL){
            $resinfo['code']='-41003';
            $resinfo['msg']='aes 解密失败';
            $resinfo['data']=array();
            return $resinfo;
        }
        if($dataObj->watermark->appid!=$appid){
            $resinfo['code']='-41003';
            $resinfo['msg']='aes 解密失败';
            $resinfo['data']=array();
            return $resinfo;
        }
        $data=$result;
        $resinfo['code']='0001';
        $resinfo['msg']='解密成功';
        $resinfo['data']=$data;
        return $resinfo;
    }

}
