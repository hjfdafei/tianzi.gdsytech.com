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
use app\api\service\UserService;
class Payment extends Controller{
    public function _empty(){
        return json(['code'=>'0004','msg'=>'网络错误']);
    }

    //支付回调
    public function sypayment_notify(){
        $content=file_get_contents("php://input");
        file_put_contents('pay.log',$content."\r\n",FILE_APPEND);
        // $content="<xml><appid><![CDATA[wx7beff0c60556a44c]]></appid><attach><![CDATA[orderno=202101251607067645&pay_way=1]]></attach><bank_type><![CDATA[PAB_CREDIT]]></bank_type><cash_fee><![CDATA[1]]></cash_fee><fee_type><![CDATA[CNY]]></fee_type><is_subscribe><![CDATA[N]]></is_subscribe><mch_id><![CDATA[1605747426]]></mch_id><nonce_str><![CDATA[28jmle9cv0jzfuxa5p32nx5djk1jd6e8]]></nonce_str><openid><![CDATA[o8mUb5m1-iHpB5gQApxJV2jg_RT0]]></openid><out_trade_no><![CDATA[P202102040139437341]]></out_trade_no><result_code><![CDATA[SUCCESS]]></result_code><return_code><![CDATA[SUCCESS]]></return_code><sign><![CDATA[83BB75601A1B4CBD5C10DC9D888DD75ADA7CF5382B2E37E8CB53630E438E11D9]]></sign><time_end><![CDATA[20210204013951]]></time_end><total_fee>1</total_fee><trade_type><![CDATA[JSAPI]]></trade_type><transaction_id><![CDATA[4200000915202102046984890841]]></transaction_id></xml>";
        libxml_disable_entity_loader(true);
        $rescontent = json_decode(json_encode(simplexml_load_string($content,'SimpleXMLElement', LIBXML_NOCDATA)),true);
        if(strtolower($rescontent['result_code'])=='success' && strtolower($rescontent['return_code'])=='success'){
            require_once WXPAYPATH.'WxPay.JsApiPay.php';
            $transaction_id = $rescontent['transaction_id'];
            $input = new \WxPayOrderQuery();
            $input->SetTransaction_id($transaction_id);
            $config = new \WxPayConfig();
            $checkres=(\WxPayApi::orderQuery($config, $input));
            if(strtolower($checkres['result_code'])=='success' && strtolower($checkres['return_code'])=='success' && strtolower($checkres['trade_state'])=='success'){
                $attach=explode('&',$rescontent['attach']);
                $attachdata=[];
                foreach($attach as $v){
                    $tmpatach=explode('=',$v);
                    $attachdata[$tmpatach[0]]=$tmpatach[1];
                }
                $map=[];
                $map[]=['orderno','=',$attachdata['orderno']];
                $uservice=new UserService();
                $info=$uservice->ordersDetail($map);
                if(!empty($info)){
                    if($info['ispay']!=1){
                        $updateData['pay_money']=$rescontent['total_fee'];
                        $updateData['ispay']=1;
                        $updateData['pay_way']=$attachdata['pay_way'];
                        $updateData['pay_tradeno']=$rescontent['transaction_id'];
                        $updateData['pay_time']=date('Y-m-d H:i:s',strtotime($rescontent['time_end']));
                        DB::name('orders')->where([['id','=',$info['id']]])->update($updateData);
                        $data=[];
                        $data['user_id']=$info['user_id'];
                        $data['openid']=$rescontent['openid'];
                        $data['orders_id']=$info['id'];
                        $data['orderno']=$info['orderno'];
                        $data['transaction_id']=$rescontent['transaction_id'];
                        $data['content']=$content;
                        $data['type']=1;
                        $data['pay_path']=$attachdata['pay_way'];
                        $data['create_time']=date('Y-m-d H:i:s');
                        DB::name('orders_paynotice')->insert($data);

                        $bmap=[];
                        $bmap[]=['status','=',1];
                        $bmap[]=['isuse','=',2];
                        $broadbandinfo=DB::name('broadband')->field('id,keyaccount,keypassword')->where($bmap)->limit(1)->orderRand()->find();
                        if(!empty($broadbandinfo)){
                            $oumap=[];
                            $oumap[]=['id','=',$info['id']];
                            $oudata=[];
                            $oudata['broadband_id']=$broadbandinfo['id'];
                            $oudata['status']=3;
                            $oudata['finish_time']=date('Y-m-d H:i:s');
                            $oudata['update_time']=date('Y-m-d H:i:s');
                            $res=DB::name('orders')->where($oumap)->update($oudata);
                            $bumap=[];
                            $bumap[]=['id','=',$broadbandinfo['id']];
                            $budata=[];
                            $budata['isuse']=1;
                            $budata['use_time']=date('Y-m-d H:i:s');
                            $budata['update_time']=date('Y-m-d H:i:s');
                            $res2=DB::name('broadband')->where($bumap)->update($budata);
                            //send_broadbandtpl($info['openid'],$info['realname'],$info['orderno']);
                            send_mini_broadbandtpl($info['openid'],'宽带安装',$info['realname'],round($info['money']/100,2));
                        }
                    }
                }
            }
            echo 'SUCCESS';
        }else{
            echo 'FAIL';
        }
    }

    //退款回调
    //public function function syrefund_notify(){
        // $content=file_get_contents("php://input");
        // file_put_contents('refund.log',$content."\r\n",FILE_APPEND);
        // libxml_disable_entity_loader(true);
        // $rescontent=json_decode(json_encode(simplexml_load_string($content,'SimpleXMLElement', LIBXML_NOCDATA)),true);
        // if(strtolower($rescontent['return_code'])=='success'){
        //     $fmap=[];
        //     $fmap[]=['refund_orderno','=',$rescontent['out_refund_no']];
        //     $uservice=new UserService();
        //     $findinfo=$uservice->ordersRefundDetail($fmap);
        //     if(!empty($findinfo)){
        //         DB::name('orders_refund')->where([['id','=',$findinfo['id']]])->update(['refund_status'=>3,'refund_money'=>$rescontent['refund_fee'],'update_time'=>date('Y-m-d H:i:s')]);
        //         $sdata=[];
        //         $sdata['user_id']=$findinfo['user_id'];
        //         $sdata['openid']=$findinfo['openid'];
        //         $sdata['orders_id']=$findinfo['orders_id'];
        //         $sdata['orderno']=$rescontent['out_refund_no'];
        //         $sdata['transaction_id']=$rescontent['refund_id'];
        //         $sdata['content']=$content;
        //         $sdata['type']=2;
        //         $sdata['create_time']=date('Y-m-d H:i:s');
        //         DB::name('orders_paynotice')->insert($sdata);
        //     }
        //     echo 'SUCCESS';
        // }else{
        //     echo 'FAIL';
        // }
    //}

}
